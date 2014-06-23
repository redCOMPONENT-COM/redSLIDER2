<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('redcore.bootstrap');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * Plugins RedSLIDER section article
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Article extends JPlugin
{
	private $sectionId;

	private $sectionName;

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->sectionId = "SECTION_ARTICLE";
		$this->sectionName = JText::_("PLG_SECTION_ARTICLE_NAME");
	}

	/**
	 * Get section name
	 *
	 * @return  array
	 */
	public function getSectionName()
	{
		$section = new stdClass;
		$section->id = $this->sectionId;
		$section->name = $this->sectionName;

		return $section;
	}

	/**
	 * Get section name by section Id
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  string
	 */
	public function getSectionNameById($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			return $this->sectionName;
		}
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  void/array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId === $this->sectionId)
		{
			$tags = array(
					"{article_title}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_TITLE_DESC"),
					"{article_introtext|<em>limit</em>}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_INTROTEXT_DESC"),
					"{article_fulltext|<em>limit</em>}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_FULLTEXT_DESC"),
					"{article_date}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_DATE_DESC"),
					"{article_link}" => JText::_("COM_REDSLIDER_TAG_ARTICLE_LINK_DESC"),
				);

			return $tags;
		}
	}

	/**
	 * Add forms fields of section to slide view
	 *
	 * @param   mixed   $form       joomla form object
	 * @param   string  $sectionId  section's id
	 *
	 * @return  boolean
	 */
	public function onSlidePrepareForm($form, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				JForm::addFormPath(__DIR__ . '/forms/');
				$return = $form->loadFile('fields_article', false);
			}
		}

		return $return;
	}

	/**
	 * Add template of section to template slide
	 *
	 * @param   object  $view       JView object
	 * @param   string  $sectionId  section's id
	 *
	 * @return boolean
	 */
	public function onSlidePrepareTemplate($view, $sectionId)
	{
		$return = false;

		if ($sectionId === $this->sectionId)
		{
			$app = JFactory::getApplication();

			if ($app->isAdmin())
			{
				$view->addTemplatePath(__DIR__ . '/tmpl/');
				$return = $view->loadTemplate('article');
			}
		}

		return $return;
	}

	/**
	 * Event on store a slide
	 *
	 * @param   object  $jtable  JTable object
	 * @param   object  $jinput  JForm data
	 *
	 * @return boolean
	 */
	public function onSlideStore($jtable, $jinput)
	{
		return true;
	}

	/**
	 * Prepare content for slide show in module
	 *
	 * @param   string  $content  Template Content
	 * @param   object  $slide    Slide result object
	 *
	 * @return  string  $content  repaced content
	 */
	public function onPrepareTemplateContent($content, $slide)
	{
		// Load stylesheet for each section
		$css = 'redslider.' . strtolower($this->sectionId) . '.css';
		RHelperAsset::load($css, 'mod_redslider');

		if ($slide->section === $this->sectionId)
		{
			$params = new JRegistry($slide->params);
			$article = new stdClass;

			$article->id = (int) $params->get('article_id', '0');
			$article->image = JString::trim($params->get('background_image', ''));
			$article->slideClass = JString::trim($params->get('article_slide_class', 'article_slide'));

			$articleModel = RModel::getFrontInstance('Article', array('ignore_request' => false), 'com_content');
			$article->instance = $articleModel->getItem($article->id);

			$matches = array();

			if (preg_match_all('/{article_title[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $article->instance->title, $content);
					}
				}
			}

			if (preg_match_all('/{article_introtext[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = RedsliderHelperHelper::replaceTagsHTML($match[0], $article->instance->introtext, $content);
					}
				}
			}

			if (preg_match_all('/{article_fulltext[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = RedsliderHelperHelper::replaceTagsHTML($match[0], $article->instance->fulltext, $content);
					}
				}
			}

			if (preg_match_all('/{article_date[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], $article->instance->created, $content);
					}
				}
			}

			if (preg_match_all('/{article_link[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = JString::str_ireplace($match[0], ContentHelperRoute::getArticleRoute($article->id), $content);
					}
				}
			}

			// Adding background image to article slide
			$html  = '<div class=\'' . $article->slideClass . '\' style=\'background-image:url("' . JURI::base() . $article->image . '")\';>';
			$html .= $content;
			$html .= '</div>';

			$content = $html;

			return $content;
		}
	}
}
