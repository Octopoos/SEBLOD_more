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
class plgCCK_Field_LinkCustom_Js extends JCckPluginLink
{
	protected static $type	=	'custom_js';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$link_class				=	$link->get( 'class', '' );
		$link_onclick			=	htmlspecialchars( $link->get( 'custom' ) );

		$field->link			=	'javascript: void(0);';
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		
		if ( $link->get( 'confirm', 1 ) ) {
			$alert					=	$link->get( 'confirm_alert' ); // JText::_( 'COM_CCK_CONFIRM_DELETE' )
			if ( $config['doTranslation'] ) {
				if ( trim( $alert ) ) {
					$alert			=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $alert ) ) );
				}
			}
			$field->link_onclick	=	'if(!confirm(\''.addslashes( $alert ).'\')){return false;}else{'.$link_onclick.'}';
		} else {
			$field->link_onclick	=	$link_onclick;
		}
	}
}
?>