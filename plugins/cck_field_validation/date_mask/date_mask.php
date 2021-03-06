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
class plgCCK_Field_ValidationDate_Mask extends JCckPluginValidation
{
	protected static $type	=	'date_mask';
	protected static $regex	=	'/^[a-zA-Z]+$/';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}

		$validation	=	parent::g_getValidation( $field->validation_options );

		$format		=	$validation->format;

		if ( $format == '-1' ) {
			$format	=	$validation->format_custom;
		}
		if ( $format ) {
			if ( strpos( $format, 'COM_CCK_' ) !== false || strpos( $format, 'DATE_FORMAT_' ) !== false ) {
				$format	=	JText::_( $format );
			}
		} else {
			$format	=	'yyyy-mm-dd';
		}

		$field->attributes	.=	' data-inputmask="\'alias\':\'datetime\', \'inputformat\':\''.$format.'\'"';

		JFactory::getDocument()->addScript( JUri::root( true ).'/media/cck/js/jquery.inputmask.min.js' );
		JFactory::getDocument()->addScript( JUri::root( true ).'/media/cck/js/jquery.inputmask.binding.min.js' );
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		/* TODO */
	}
}
?>