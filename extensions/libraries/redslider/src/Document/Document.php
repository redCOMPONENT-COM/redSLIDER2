<?php
/**
 * RedSLIDER Library file.
 * Including this file into your application will make redSLIDER available to use.
 *
 * @package    RedSLIDER.Library
 * @copyright  Copyright (C) 2020 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

namespace Redslider\Document;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Class Document
 * @package Redslider\Document
 * @since __DEPLOY_VERSION__
 */
class Document
{
	/**
	 * The instance.
	 *
	 * @var  static
	 */
	protected static $instance;

	/**
	 * Scripts marked as disabled
	 *
	 * @var  array
	 */
	protected static $disabledScripts = array();

	/**
	 * Stylesheets marked as disabled
	 *
	 * @var  array
	 */
	protected static $disabledStylesheets = array();

	/**
	 * Scripts that will be injected on top
	 *
	 * @var  array
	 */
	protected static $topScripts = array();

	/**
	 * Stylesheets that will be injected on top
	 *
	 * @var  array
	 */
	protected static $topStylesheets = array();

	/**
	 * Gets an instance or create it.
	 *
	 * @return  static
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getInstance()
	{
		if (null === self::$instance)
		{
			self::$instance = new static;

			Factory::getApplication()->registerEvent(
				'onBeforeCompileHead',
				function () {
					self::$instance
						->cleanHeader();
				}
			);
		}

		return self::$instance;
	}

	/**
	 * Add a script to the top of the document scripts
	 *
	 * @param   string   $url    URL to the linked script
	 * @param   string   $type   Type of script. Defaults to 'text/javascript'
	 * @param   boolean  $defer  Adds the defer attribute.
	 * @param   boolean  $async  Adds the async attribute.
	 *
	 * @return  $this
	 * @since   __DEPLOY_VERSION__
	 */
	public function addTopScript($url, $type = "text/javascript", $defer = false, $async = false)
	{
		$script = array(
			'mime' => $type,
			'defer' => $defer,
			'async' => $async
		);

		static::$topScripts[$url] = $script;

		return $this;
	}

	/**
	 * Add a script to the top of the document scripts
	 *
	 * @param   string  $url      URL to the linked style sheet
	 * @param   string  $type     Mime encoding type
	 * @param   string  $media    Media type that this stylesheet applies to
	 * @param   array   $attribs  Array of attributes
	 *
	 * @return  $this
	 * @since   __DEPLOY_VERSION__
	 */
	public function addTopStylesheet($url, $type = 'text/css', $media = null, $attribs = array())
	{
		$stylesheet = array(
			'mime'    => $type,
			'media'   => $media,
			'attribs' => $attribs
		);

		static::$topStylesheets[$url] = $stylesheet;

		return $this;
	}

	/**
	 * Clean header assets
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	public function cleanHeader()
	{
		$this->cleanHeaderScripts();
		$this->cleanHeaderStylesheets();
		$this->injectTopScripts();
		$this->injectTopStylesheets();
	}

	/**
	 * Injects the pending scripts on the top of the scripts
	 *
	 * @return  $this
	 * @since   __DEPLOY_VERSION__
	 */
	protected function injectTopScripts()
	{
		if (empty(static::$topScripts))
		{
			return true;
		}

		$doc = Factory::getDocument();

		$doc->_scripts = array_merge(static::$topScripts, $doc->_scripts);

		return $this;
	}

	/**
	 * Injects the top stylesheets on the top of the document stylesheets
	 *
	 * @return  $this
	 * @since   __DEPLOY_VERSION__
	 */
	protected function injectTopStylesheets()
	{
		if (empty(static::$topStylesheets))
		{
			return true;
		}

		$doc = Factory::getDocument();

		$doc->_styleSheets = array_merge(static::$topStylesheets, $doc->_styleSheets);

		return $this;
	}

	/**
	 * Clear all the scripts marked as disabled
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	protected function cleanHeaderScripts()
	{
		if (!empty(static::$disabledScripts))
		{
			foreach (static::$disabledScripts as $script)
			{
				$this->removeScript($script);
			}
		}
	}

	/**
	 * Clear all the stylesheets marked as disabled
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	protected function cleanHeaderStylesheets()
	{
		if (!empty(static::$disabledStylesheets))
		{
			foreach (static::$disabledStylesheets as $stylesheet)
			{
				$this->removeStylesheet($stylesheet);
			}
		}
	}

	/**
	 * Mark a script as disabled
	 *
	 * @param   string   $script          Script to disable
	 * @param   boolean  $disableOnDebug  Disable also uncompressed version
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	public function disableScript($script, $disableOnDebug = true)
	{
		$script = trim($script);

		if ($script && !in_array($script, static::$disabledScripts))
		{
			array_push(static::$disabledScripts, $script);

			if ($disableOnDebug)
			{
				array_push(static::$disabledScripts, $this->getUncompressedPath($script));
			}
		}
	}

	/**
	 * Mark a stylesheet as disabled
	 *
	 * @param   string   $stylesheet      Stylesheets to disable
	 * @param   boolean  $disableOnDebug  Disable also uncompressed version
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	public function disableStylesheet($stylesheet, $disableOnDebug = true)
	{
		$stylesheet = trim($stylesheet);

		if ($stylesheet && !in_array($stylesheet, static::$disabledStylesheets))
		{
			array_push(static::$disabledStylesheets, $stylesheet);

			if ($disableOnDebug)
			{
				array_push(static::$disabledScripts, $this->getUncompressedPath($stylesheet));
			}
		}
	}

	/**
	 * Filter array values with fnmatch.
	 *
	 * @param   string  $filter  Filter to apply
	 * @param   array   $array   Values to filter
	 *
	 * @return  array|false
	 * @since   __DEPLOY_VERSION__
	 */
	protected function fnmatchFilter($filter, $array)
	{
		$callback = function ($value) use ($filter) {
			if (!fnmatch($filter, $value))
			{
				return false;
			}

			return $value;
		};

		return array_filter(array_map($callback, $array));
	}

	/**
	 * Get the route to an uncompressed asset bassed on the compressed path
	 *
	 * @param   string  $assetPath  Path to the asset
	 *
	 * @return  string
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getUncompressedPath($assetPath)
	{
		$fileName      = basename($assetPath);
		$fileNameOnly  = pathinfo($fileName, PATHINFO_FILENAME);
		$fileExtension = pathinfo($assetPath, PATHINFO_EXTENSION);

		if (strlen($fileNameOnly) > 4 && strrpos($fileNameOnly, '.min', '-4'))
		{
			$position             = strrpos($fileNameOnly, '.min', '-4');
			$uncompressedFileName = str_replace('.min', '.', $fileNameOnly, $position);
			$uncompressedFileName = $uncompressedFileName . $fileExtension;
		}
		else
		{
			$uncompressedFileName = $fileNameOnly . '-uncompressed.' . $fileExtension;
		}

		return str_replace($fileName, $uncompressedFileName, $assetPath);
	}

	/**
	 * Remove a script from the JDocument header
	 *
	 * @param   string  $script  Script path
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeScript($script)
	{
		$doc = Factory::getDocument();

		$script = trim($script);

		if (empty($script))
		{
			return;
		}

		// Try to apply fnmatch filter
		$matches = $this->fnmatchFilter($script, array_keys($doc->_scripts));

		if ($matches)
		{
			$doc->_scripts = array_diff_key($doc->_scripts, array_flip($matches));

			return;
		}

		$uri = Uri::getInstance();

		$relativePath   = trim(str_replace($uri->getPath(), '', Uri::root()), '/');
		$relativeScript = trim(str_replace($uri->getPath(), '', $script), '/');
		$relativeUrl    = str_replace($relativePath, '', $script);

		$mediaVersion = $doc->getMediaVersion();

		// Try to disable relative and full URLs
		unset($doc->_scripts[$script]);
		unset($doc->_scripts[$script . '?' . $mediaVersion]);

		unset($doc->_scripts[$relativeUrl]);
		unset($doc->_scripts[$relativeUrl . '?' . $mediaVersion]);

		unset($doc->_scripts[Uri::root(true) . $script]);
		unset($doc->_scripts[Uri::root(true) . $script . '?' . $mediaVersion]);

		unset($doc->_scripts[Uri::root(true) . '/' . $script]);
		unset($doc->_scripts[Uri::root(true) . '/' . $script . '?' . $mediaVersion]);

		unset($doc->_scripts[$relativeScript]);
		unset($doc->_scripts[$relativeScript . '?' . $mediaVersion]);
	}

	/**
	 * Remove a stylesheet from the JDocument header
	 *
	 * @param   string  $stylesheet  URL to the stylesheet (both global/relative should work)
	 *
	 * @return  void
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeStylesheet($stylesheet)
	{
		$stylesheet = trim($stylesheet);

		if (empty($stylesheet))
		{
			return;
		}

		$doc = Factory::getDocument();

		// Try to apply fnmatch filter
		$matches = $this->fnmatchFilter($stylesheet, array_keys($doc->_styleSheets));

		if ($matches)
		{
			$doc->_styleSheets = array_diff_key($doc->_styleSheets, array_flip($matches));

			return;
		}

		$uri = Uri::getInstance();

		$relativePath       = trim(str_replace($uri->getPath(), '', Uri::root()), '/');
		$relativeStylesheet = trim(str_replace($uri->getPath(), '', $stylesheet), '/');
		$relativeUrl        = str_replace($relativePath, '', $stylesheet);

		$mediaVersion = $doc->getMediaVersion();

		// Try to disable relative and full URLs
		unset($doc->_styleSheets[$stylesheet]);
		unset($doc->_styleSheets[$stylesheet . '?' . $mediaVersion]);

		unset($doc->_styleSheets[$relativeUrl]);
		unset($doc->_styleSheets[$relativeUrl . '?' . $mediaVersion]);

		unset($doc->_styleSheets[Uri::root(true) . $stylesheet]);
		unset($doc->_styleSheets[Uri::root(true) . $stylesheet . '?' . $mediaVersion]);

		unset($doc->_styleSheets[Uri::root(true) . '/' . $stylesheet]);
		unset($doc->_styleSheets[Uri::root(true) . '/' . $stylesheet . '?' . $mediaVersion]);

		unset($doc->_styleSheets[$relativeStylesheet]);
		unset($doc->_styleSheets[$relativeStylesheet . '?' . $mediaVersion]);
	}

	/**
	 * Redirect any non-existing method to JDocument
	 *
	 * @param   string  $method     Method called
	 * @param   array   $arguments  Arguments passed to the method
	 *
	 * @return  mixed
	 * @since   __DEPLOY_VERSION__
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array([Factory::getDocument(), $method], $arguments);
	}
}
