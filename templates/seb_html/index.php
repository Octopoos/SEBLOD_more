<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -- Initialize
require_once dirname(__FILE__).'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$attributes	=	$cck->id_attributes ? ' '.$cck->id_attributes : '';
$attributes	=	$cck->replaceLive( $attributes );
$html		=	$cck->getStyleParam( 'code' );

if ( $html != '' && strpos( $html, '$cck->get' ) !== false ) {
	$matches	=	'';
	$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
	preg_match_all( $search, $html, $matches );
	if ( count( $matches[1] ) ) {
		foreach( $matches[1] as $k=>$v ) {
			$args	=	$matches[2][$k];
			$method	=	'get'.$v;
			$html	=	str_replace( $matches[0][$k], $cck->$method($args), $html );
		}
	}
}
if ( $html != '' && strpos( $html, '$uri->get' ) !== false ) {
	$matches	=	'';
	$search		=	'#\$uri\->get([a-zA-Z]*)\( ?\'?([a-zA-Z0-9_]*)\'? ?\)(;)?#';
	preg_match_all( $search, $html, $matches );
	if ( count( $matches[1] ) ) {
		foreach ( $matches[1] as $k=>$v ) {
			$variable	=	$matches[2][$k];
			if ( $v == 'Current' ) {
				$request	=	( $variable == 'true' ) ? JUri::getInstance()->toString() : JUri::current();
				$html		=	str_replace( $matches[0][$k], $request, $html );						
			} else {
				$request	=	'get'.$v;
				$html		=	str_replace( $matches[0][$k], $app->input->$request( $variable, '' ), $html );
			}
		}
	}
}
if ( $html != '' && strpos( $html, 'J(' ) !== false ) {
	$matches	=	'';
	$search		=	'#J\((.*)\)#U';
	preg_match_all( $search, $html, $matches );
	if ( count( $matches[1] ) ) {
		foreach( $matches[1] as $text ) {
			$html	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $html );
		}
	}
}

// -- Render
if ( $cck->id_class != '' ) {
	echo '<div class="'.trim( $cck->id_class ).'"'.$attributes.'>'.$html.'</div>';
} else {
	echo $html;
}

// -- Finalize
$cck->finalize();
?>