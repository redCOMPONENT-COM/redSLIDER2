<?php
/**
 * @package    RedSLIDER.Installer
 *
 * @copyright  Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Find redCORE installer to use it as base system
if (!class_exists('Com_RedcoreInstallerScript'))
{
	$searchPaths = array(
		// Install
		dirname(__FILE__) . '/redCORE',
		// Discover install
		JPATH_ADMINISTRATOR . '/components/com_redcore'
	);

	if ($redcoreInstaller = JPath::find($searchPaths, 'install.php'))
	{
		require_once $redcoreInstaller;
	}
}

/**
 * Script file of redSLIDER component
 *
 * @package  RedSLIDER.Installer
 *
 * @since    2.0
 */
class Com_RedSliderInstallerScript extends Com_RedcoreInstallerScript
{
	/**
	 * Method to install the component
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function installOrUpdate($parent)
	{
		parent::installOrUpdate($parent);

		$this->com_install();

		return true;
	}

	/**
	 * Main redSLIDER installer Events
	 *
	 * @return  void
	 */
	private function com_install()
	{
		// Diplay the installation message
		$this->displayInstallMsg();
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		// Error handling
		JError::SetErrorHandling(E_ALL, 'callback', array('Com_RedSliderInstallerScript', 'error_handling'));

		// Check redSLIDER library
		$where = array(
			$db->qn('e.type') . ' = ' . $db->quote('library'),
			$db->qn('e.element') . ' = ' . $db->quote('redslider')
		);
		$query = $db->getQuery(true);
		$query->select('count(*) AS count')
			->from($db->qn('#__extensions', 'e'))
			->where($where);
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($result->count > 0)
		{
			$app->enqueueMessage(JText::_('COM_REDSLIDER_UNINSTALL_ERROR_DELETE_LIBRARY_FIRST'), 'error');

			$app->redirect('index.php?option=com_installer&view=manage');
		}

		// Uninstall extensions
		$this->com_uninstall();
	}

	/**
	 * Main redSLIDER uninstaller Events
	 *
	 * @return  void
	 */
	private function com_uninstall()
	{
	}

	/**
	 * Error handler
	 *
	 * @param   array  $e  Exception array
	 *
	 * @return  void
	 */
	public static function error_handling(Exception $e)
	{
	}

	/**
	 * Display install message
	 *
	 * @return void
	 */
	public function displayInstallMsg()
	{
		echo '<p><img src="' . JUri::root() . '/media/com_redslider/images/redslider_logo.jpg" alt="redSLIDER Logo" width="500"></p>';
		echo '<br /><br /><p>Remember to check for updates at:<br />';
		echo '<a href="http://www.redcomponent.com/" target="_new">';
		echo '<img src="' . JUri::root() . '/media/com_redslider/images/redcomponent_logo.jpg" alt="">';
		echo '</a></p>';
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		parent::postflight($type, $parent);

		// Redirect to the welcome screen.
		if ($type === 'discover_install')
		{
			$app = JFactory::getApplication();

			return $app->redirect('index.php?option=com_redslider&view=welcome&type=' . $type);
		}

		return $parent->getParent()->setRedirectURL('index.php?option=com_redslider&view=welcome&type=' . $type);
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		if ($type == "update")
		{
			$this->migrationData();
		}
	}

	/**
	 * Method for migration data from old version
	 * 
	 * @return  boolean  True if success. False otherwise.
	 */
	public function migrationData()
	{
		return true;
	}
}
