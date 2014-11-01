<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_ValidationDate extends JCckPluginValidation
{
	protected static $type		=	'date';
	protected static $regexs	=	array( 'international'=>'/^\d{4}[-](0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])$/',
										   'en'=>'/^([0]?[1-9]{1}|[12]\d{1}|3[01])[\/\-\.]([0]?[1-9]|1[0-2])[\/\-\.]\d{4}$/',
										   'fr'=>'/^([0]?[1-9]{1}|[12]\d{1}|3[01])[\/\-\.]([0]?[1-9]|1[0-2])[\/\-\.]\d{4}$/',
										   'us'=>'/^(0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])[\/\-\.]\d{4}$/'
									);
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$definition	=	self::_getDefinition( $field );
		if ( !is_array( $definition ) ) {
			return;
		}
		$validation	=	parent::g_onCCK_Field_ValidationPrepareForm( $field, $fieldId, $config, 'regex', $definition );
		
		$field->validate[]	=	'custom['.$validation->name.']';
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		$definition	=	self::_getDefinition( $field );
		parent::g_onCCK_Field_ValidationPrepareStore( $name, $value, $config, self::$type, 'regex', $definition );
	}
	
	// _getDefinition
	protected static function _getDefinition( $field )
	{
		$options	=	parent::g_getValidation( $field->validation_options );

		if ( isset( $options->region ) && $options->region ) {
			$region	=	$options->region;
		} else {
			// todo: detection
			return;
		}
		$regex	=	self::$regexs[$region];
		if ( isset( $options->separator ) && $options->separator && $region != 'international' ) {
			$regex	=	str_replace( '[\/\-\.]', '\\'.$options->separator, $regex );
		}

		return array( 'definition'=>$regex, 'suffix'=>$region );
	}
}
?>