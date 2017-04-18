<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_ValidationNumber_Format extends JCckPluginValidation
{
	protected static $type	=	'number_format';
	protected static $regex	=	'';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, &$value, &$config )
	{
		if ( $value == '' ) {
			return;
		}
		$validation	=	parent::g_getValidation( $field->validation_options, false );

		// Decimals
		$pos		=	strrpos( $value, $validation->get( 'input_decimals_separator', ',' ) );
		if ( $pos !== false && $pos > 0 && isset( $value[$pos] ) ) {
			$replace		=	$validation->get( 'output_decimals_separator', '.' );
			$value[$pos]	=	$replace;
		}

		// Thousands
		$search		=	$validation->get( 'input_thousands_separator', '' );
		$replace	=	$validation->get( 'output_thousands_separator', '' );
		$value		=	str_replace( $search, $replace, $value );
	}
}
?>