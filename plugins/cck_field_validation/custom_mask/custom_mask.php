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
class plgCCK_Field_ValidationCustom_Mask extends JCckPluginValidation
{
	protected static $type	=	'custom_mask';
	protected static $regex	=	'';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_ValidationPrepareForm
	public static function onCCK_Field_ValidationPrepareForm( &$field, $fieldId, &$config )
	{
		if ( self::$type != $field->validation ) {
			return;
		}
		
		$validation			=	parent::g_getValidation( $field->validation_options );
		
		// Set
		$field->attributes	.=	' data-inputmask="\'mask\':\''.$validation->custom.'\',\'placeholder\':\''.$validation->custom.'\'"';

		JFactory::getDocument()->addScript( JUri::root( true ).'/media/cck/js/jquery.inputmask.min.js' );
		JFactory::getDocument()->addScript( JUri::root( true ).'/media/cck/js/jquery.inputmask.binding.min.js' );
	}
	
	// onCCK_Field_ValidationPrepareStore
	public static function onCCK_Field_ValidationPrepareStore( &$field, $name, $value, &$config )
	{
		$validation	=	parent::g_getValidation( $field->validation_options );

		$regex	=	addcslashes( $validation->custom, '\^.$|()[]+?{}' );

		$regex		=	str_replace(
							array( 'a', '9', '*' ),
							array( '([a-zA-Z])', '([0-9])', '([a-zA-Z0-9])' ),
							$regex
						);
		
		// parent::g_onCCK_Field_ValidationPrepareStore( $name, $value, $config, self::$type, 'regex', $regex );
	}
}
?>