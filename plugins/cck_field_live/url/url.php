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
class plgCCK_Field_LiveUrl extends JCckPluginLive
{
	protected static $type	=	'url';
	
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
		$method		=	$options->get( 'uri', 'current' );
		
		// Prepare
		if ( $method == 'custom' ) {
			$parts	=	$options->get( 'parts', 'scheme,user,pass,host,port,path,query,fragment' );
			$parts	=	explode( ',', $parts );
			$live	=	JUri::getInstance()->toString( $parts );
		} else {
			$live	=	JUri::$method();
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>