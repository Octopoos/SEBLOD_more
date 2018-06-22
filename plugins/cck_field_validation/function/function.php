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
class plgCCK_Field_ValidationFunction extends JCckPluginValidation
{
	protected static $type	=	'function';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$validation	=	parent::g_getValidation( $field->validation_options );
		
		if ( $validation->alert != '' ) {
			$name	=	'function_'.$fieldId;
			$alert	=	$validation->alert;
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
		} else {
			$lang   =	JFactory::getLanguage();
			$lang->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
			
			$name	=	'function';
			$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT' );
		}
		
		$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );
		$rule	=	'
				"'.$name.'":{
					"alertText":"'.$prefix.$alert.'"}
				';
		
		$config['validation'][$name]	=	$rule;
		$field->validate[]				=	'funcCall['.$validation->function.']';
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
	}
}
?>