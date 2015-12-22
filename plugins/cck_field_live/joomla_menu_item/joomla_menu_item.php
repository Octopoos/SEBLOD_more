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
class plgCCK_Field_LiveJoomla_Menu_Item extends JCckPluginLive
{
	protected static $type	=	'joomla_menu_item';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		jimport( 'joomla.application.menu' );

		$live		=	'';
		$menu_item	=	JFactory::getApplication()->getMenu()->getActive();
		$options	=	parent::g_getLive( $field->live_options );
		
		// Prepare
		$property	=	$options->get( 'property' );
		$param		=	$options->get( 'param' );
		
		if ( $property == 'param' && $param != '' ) {
			$live	=	$menu_item->params->get( $param, $options->get( 'default_value', '' ) );
		} elseif ( isset( $menu_item->$property ) ) {
			$live	=	$menu_item->$property;
		} else {
			$live	=	$options->get( 'default_value', '' );
		}
		
		// Set
		$value		=	(string)$live;
	}
}
?>