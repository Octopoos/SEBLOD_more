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
class plgCCK_Field_LiveUrl extends JCckPluginLive
{
	protected static $type	=	'url';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array(), $inherit = array() )
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
		} elseif ( $method == 'path' ) {
			$path	=	JUri::getInstance()->getPath();

			if ( $path != '' && $path[0] == '/' ) {
				$path	=	substr( $path, 1 );
			}
			$path	=	explode( '/', $path );
			$parts	=	$options->get( 'segments', '' );
			$parts	=	explode( ',', $parts );
			$live	=	array();

			foreach ( $parts as $part ) {
				$k	=	$part - 1;
				if ( isset( $path[$k] ) && $path[$k] ) {
					$live[]	=	$path[$k];
				}
			}
			$live	=	implode( '/', $live );
		} else {
			$live	=	JUri::$method();
		}

		// Set
		$value	=	(string)$live;
	}
}
?>