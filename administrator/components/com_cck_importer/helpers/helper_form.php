<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/common/form.php';

// Helper
class Helper_Form extends CommonHelper_Form
{
	// getObjectPlugins
	public static function getObjectPlugins( &$field, $value, $name, $id, $config )
	{
		$value	=	( $value ) ? $value : 'joomla_article';
		
		return JHtml::_( 'select.genericlist', Helper_Admin::getPluginOptions( 'storage_location', 'cck_', false, false, true, array(), '/classes/importer.php' ), $name, 'class="inputbox select" '.$field->attributes, 'value', 'text', $value, $id );
	}
}
?>