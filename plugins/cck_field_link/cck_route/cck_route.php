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
class plgCCK_Field_LinkCck_Route extends JCckPluginLink
{
	protected static $type	=	'cck_route';
	
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
		$app			=	JFactory::getApplication();
		$fieldnames		=	$link->get( 'routes', '' );
		$fieldnames		=	explode( '||', $fieldnames );

		// Prepare
		$link_attr		=	$link->get( 'attributes', '' );
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		
		if ( count( $fieldnames ) ) {
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name, 'fieldnames'=>$fieldnames ) );
		}
		
		$field->link			=	'';
		$field->link_attributes	=	$link_attr ? $link_attr : ( isset( $field->link_attributes ) ? $field->link_attributes : '' );
		$field->link_class		=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		$field->link_rel		=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
		$field->link_state		=	$link->get( 'state', 1 );
		$field->link_target		=	$link_target ? $link_target : ( isset( $field->link_target ) ? $field->link_target : '' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_LinkBeforeRenderContent
	public static function onCCK_Field_LinkBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		$route	=	'';
		
		if ( count( $process['fieldnames'] ) ) {
			foreach ( $process['fieldnames'] as $fieldname ) {
				if ( isset( $fields[$fieldname] ) && $fields[$fieldname]->link != '' && $fields[$fieldname]->state ) {
					$route	=	$fields[$fieldname]->link;

					break;
				}
			}
		}
		if ( $route != '' ) {
			$fields[$name]->link	=	$route;
			$target					=	 $fields[$name]->typo_target;

			JCckPluginLink::g_setHtml( $fields[$name], $target );
		}
	}
}
?>