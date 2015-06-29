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
class plgCCK_Field_LinkJoomla_Menuitem extends JCckPluginLink
{
	protected static $type	=	'joomla_menuitem';
	
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
		$app		=	JFactory::getApplication();
		$itemId		=	$link->get( 'itemid', '' );
		$custom		=	$link->get( 'custom', '' );
		
		// Prepare
		if ( !$itemId ) {
			$itemId			=	$app->input->getInt( 'Itemid', 0 );
		}
		$link_attr			=	$link->get( 'attributes', '' );
		$link_class			=	$link->get( 'class', '' );
		$link_rel			=	$link->get( 'rel', '' );
		$link_target		=	$link->get( 'target', '' );
		$tmpl				=	$link->get( 'tmpl', '' );
		$tmpl				=	$tmpl ? 'tmpl='.$tmpl : '';
		$vars				=	$tmpl;
		$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );
		
		// Set
		$field->link		=	JRoute::_( 'index.php?Itemid='.$itemId );
		if ( $field->link ) {
			if ( $vars ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$vars : '?'.$vars;
			}
			if ( $custom ) {
				$field->link	.=	( strpos( $field->link, '?' ) !== false ) ? '&'.$custom : '?'.$custom;
			}
		}
		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? $link_target : ( isset( $field->link_target ) ? $field->link_target : '' );
	}
}
?>