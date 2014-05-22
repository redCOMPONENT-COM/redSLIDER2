<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Table
 *
 * @copyright   Copyright (C) 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Template table
 *
 * @package     RedSLIDER.Backend
 * @subpackage  Table
 * @since       2.0.0
 */
class RedsliderTableTemplate extends RTable
{
	/**
	 * The name of the table with template
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $_tableName = 'redslider_templates';

	/**
	 * The primary key of the table
	 *
	 * @var string
	 * @since 2.0.0
	 */
	protected $_tableKey = 'id';

	/**
	 * Field name to publish/unpublish table registers. Ex: state
	 *
	 * @var  string
	 */
	protected $_tableFieldState = 'published';
}
