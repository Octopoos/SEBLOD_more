<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_ValidationCheckbox extends JCckPluginValidation
{
	protected static $type	=	'checkbox';
	protected static $regex	=	'"none"';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$validation		=	parent::g_getValidation( $field->validation_options );
		
		if ( $validation->alert != '' ) {
		} else {
			$lang   =	JFactory::getLanguage();
			$lang->load( 'plg_cck_field_validation_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
			
			$prefix	=	JCck::getConfig_Param( 'validation_prefix', '* ' );		
			
			if ( (int)$validation->min > 0 ) {
				$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT3' );
				$alert2	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT4' );
				self::_validation( $field, $config, 'minCheckbox', (int)$validation->min, $prefix, $alert, $alert2 );
			}
			if ( (int)$validation->max > 0 ) {
				$alert	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT' );
				$alert2	=	JText::_( 'PLG_CCK_FIELD_VALIDATION_'.self::$type.'_ALERT2' );
				self::_validation( $field, $config, 'maxCheckbox', (int)$validation->max, $prefix, $alert, $alert2 );
			}
		}
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		// todo
	}
	
	// _validation
	protected static function _validation( &$field, &$config, $name, $limit, $prefix, $alert, $alert2 )
	{
		$rule	=	'
				"'.$name.'":{
					"regex": '.self::$regex.',
					"alertText": "'.$prefix.$alert.'",
					"alertText2":"'.$alert2.'"}
					';

		$config['validation'][$name]	=	$rule;
		$field->validate[]				=	$name.'['.$limit.']';
	}
}
?>