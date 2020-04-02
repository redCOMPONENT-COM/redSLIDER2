<?php
/**
 * @package     RedSlider
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2013 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Redslider\Plugin\AbstractRedsliderSection;

defined('_JEXEC') or die;

JLoader::import('redslider.library');

/**
 * Plugins RedSLIDER section article
 *
 * @since  1.0
 */
class PlgRedslider_SectionsSection_Article extends AbstractRedsliderSection
{
	/**
	 * Section ID
	 *
	 * @var  string
	 */
	protected $sectionId = 'SECTION_ARTICLE';

	protected $formName = 'fields_article';

	protected $templateName = 'article';

	/**
	 * Constructor - note in Joomla 2.5 PHP4.x is no longer supported so we can use this.
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->sectionName = Text::_('PLG_SECTION_ARTICLE_NAME');
	}

	/**
	 * Get section's tags name
	 *
	 * @param   string  $sectionId  Section's ID
	 *
	 * @return  array
	 */
	public function getTagNames($sectionId)
	{
		if ($sectionId != $this->sectionId)
		{
			return array();
		}

		return array(
			'{article_title}'                    => Text::_('COM_REDSLIDER_TAG_ARTICLE_TITLE_DESC'),
			'{article_introtext|<em>limit</em>}' => Text::_('COM_REDSLIDER_TAG_ARTICLE_INTROTEXT_DESC'),
			'{article_fulltext|<em>limit</em>}'  => Text::_('COM_REDSLIDER_TAG_ARTICLE_FULLTEXT_DESC'),
			'{article_date}'                     => Text::_('COM_REDSLIDER_TAG_ARTICLE_DATE_DESC'),
			'{article_link}'                     => Text::_('COM_REDSLIDER_TAG_ARTICLE_LINK_DESC'),
		);
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
		if ($slide->section != $this->sectionId)
		{
			return '';
		}

		$params = new Registry($slide->params);

		$id = (int) $params->get('article_id', 0);

		if (!$id)
		{
			return '';
		}

		// Check if we need to load component's CSS or not
		$useOwnCSS = ComponentHelper::getParams('com_redslider')->get('use_own_css', '0');

		// Load stylesheet for each section
		$css = 'redslider.' . StringHelper::strtolower($this->sectionId) . '.min.css';

		if (!$useOwnCSS)
		{
			RHelperAsset::load($css, 'redslider_sections/' . StringHelper::strtolower($this->sectionId));
		}

		$articleModel = RModel::getFrontInstance('Article', array('ignore_request' => false), 'com_content');

		if ($this->canAccessArticle($id))
		{
			$article = $articleModel->getItem($id);
			$matches = array();

			if (preg_match_all('/{article_title[^}]*}/i', $content, $matches) > 0)
			{
				$match   = $matches[0];
				$content = StringHelper::str_ireplace($match[0], $article->title, $content);
			}

			if (preg_match_all('/{article_introtext[^}]*}/i', $content, $matches) > 0)
			{
				$match   = $matches[0];
				$content = RedsliderHelper::replaceTagsHTML($match[0], $article->introtext, $content);
			}

			if (preg_match_all('/{article_fulltext[^}]*}/i', $content, $matches) > 0)
			{
				$match   = $matches[0];
				$content = RedsliderHelper::replaceTagsHTML($match[0], $article->fulltext, $content);
			}

			if (preg_match_all('/{article_date[^}]*}/i', $content, $matches) > 0)
			{
				$match   = $matches[0];
				$content = RedsliderHelper::replaceTagsHTML($match[0], $article->created, $content);
			}

			if (preg_match_all('/{article_link[^}]*}/i', $content, $matches) > 0)
			{
				$match   = $matches[0];
				$content = RedsliderHelper::replaceTagsHTML(
					$match[0],
					Route::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid, $article->language)),
					$content
				);
			}
		}
		else
		{
			$matches = array();

			if (preg_match_all('/{article_title[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = StringHelper::str_ireplace($match[0], '', $content);
					}
				}
			}

			if (preg_match_all('/{article_introtext[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = RedsliderHelper::replaceTagsHTML($match[0], '', $content);
					}
				}
			}

			if (preg_match_all('/{article_fulltext[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = RedsliderHelper::replaceTagsHTML($match[0], '', $content);
					}
				}
			}

			if (preg_match_all('/{article_date[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = StringHelper::str_ireplace($match[0], '', $content);
					}
				}
			}

			if (preg_match_all('/{article_link[^}]*}/i', $content, $matches) > 0)
			{
				foreach ($matches as $match)
				{
					if (count($match))
					{
						$content = StringHelper::str_ireplace($match[0], '', $content);
					}
				}
			}
		}

		return $content;
	}

	/**
	 * Count article
	 *
	 * @param   int  $articleId  ID of article
	 *
	 * @return  boolean
	 */
	public function canAccessArticle($articleId)
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->qn('#__content'))
			->where($db->qn('state') . ' = 1')
			->where($db->qn('id') . ' = ' . (int) $articleId);

		if (Multilanguage::isEnabled())
		{
			$query->where('language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		return (boolean) $db->setQuery($query)->loadResult();
	}
}
