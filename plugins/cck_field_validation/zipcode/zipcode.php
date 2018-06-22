<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_ValidationZipcode extends JCckPluginValidation
{
	protected static $type		=	'zipcode';
	protected static $regexs	=	array( 'au'=>'/^((0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2}))*$/',
										   'br'=>'/^([0-9]){5}([-])([0-9]){3}$/',
										   'cn'=>'/^([0-9]){6}$/',
										   'de'=>'/\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b/',
										   'es'=>'/^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$/',
										   'fr'=>'/^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$/',
										   'it'=>'/^(V-|I-)?[0-9]{5}$/',
										   'jp'=>'/^\d{3}-\d{4}$/',
										   'nl'=>'/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/',
										   'ru'=>'/^[0-9]{6}/',
										   'ua'=>'/^[0-9]{5}/',
										   'uk'=>'/^([A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2})*$/',
										   'us'=>'/^([0-9]{5}(?:-[0-9]{4})?)*$/',
										   'za'=>'/^([0-9]){4}$/'
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
			/* TODO#SEBLOD: detection */
			return;
		}
		
		return array( 'definition'=>self::$regexs[$region], 'suffix'=>$region );
	}
}
?>