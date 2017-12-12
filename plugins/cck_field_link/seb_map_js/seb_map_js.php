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
class plgCCK_Field_LinkSeb_Map_Js extends JCckPluginLink
{
	protected static $type	=	'seb_map_js';
	
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
		// Prepare
		$link_class	=	$link->get( 'class', '' );

		// Set
		$field->link			=	'javascript: void(0);';
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_onclick	=	'JCck.More.SebMap.openMarker('.(int)$config['pk'].','.(int)$link->get( 'zoom', '16' ).'); return false;';
	}
}
?>