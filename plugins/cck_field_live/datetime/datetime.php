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
class plgCCK_Field_LiveDateTime extends JCckPluginLive
{
	protected static $type	=	'datetime';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		$wrapper	=	$options->get( 'return_jtext', '' );
		
		// Prepare
		$format		=	$options->get( 'format', 'Y-m-d H-i-s' );
		$modify		=	$options->get( 'modify', '' );
		if ( $format == -1 ) {
			$format	=	$options->get( 'format_custom', 'Y-m-d H-i-s' );
		}
		if ( strpos( $format, 'COM_CCK_' ) !== false || strpos( $format, 'DATE_FORMAT_' ) !== false ) {
			$format	=	JText::_( $format );
		}
		if ( $modify != '' ) {
			$live	=	 JFactory::getDate()->modify( $modify )->format( $format );
		} else {
			$live	=	 JFactory::getDate()->format( $format );
		}
		
		// Set
		$value	=	( $wrapper ) ? JText::sprintf( $wrapper, $live ) : $live;
	}
}
?>