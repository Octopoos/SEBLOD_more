<?php
/**
* @version 			SEBLOD 3.x More ~ $Id: index.php alexandrelapoux $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$backstretch_id	=	$cck->id; //todo
$duration		=	$cck->getStyleParam( 'backstretch_duration', 5000 );
$fade			=	$cck->getStyleParam( 'backstretch_fade', 0 );
$fieldnames		=	$cck->getFields( 'element', '', false );
$items			=	$cck->getItems();
$images			=	'';
$selector		=	$cck->getStyleParam( 'backstretch_selector', '' );
$selector		=	( $selector == '$' || $selector == '' ) ? '$' : '$("'.$selector.'")';
$root			=	JUri::root( true );

// -- Render
foreach ( $items as $item ) {
	foreach ( $fieldnames as $fieldname ) {
		$html	=	$item->renderfield( $fieldname );
		preg_match_all( '#<img .*src=(?:"|\')(.+)(?:"|\').*>#Uis', $html, $image );
		if ( isset( $image[1][0] ) && $image[1][0] ) {
			$images	.=	'"'.$root.'/'.$image[1][0].'",';
		}
	}
}
if ( $images ) {
	$images	=	substr( $images, 0, -1 );	
} else {
	return;
}

if ( $cck->id_class ) {
	echo '<div id="'.$backstretch_id.'" class="'.trim( $cck->id_class ).'"></div>';

	$height	=	$cck->getStyleParam( 'backstretch_height', 'auto' );
	if ( $height && $height != 'auto' ) {
		$cck->addCSS( '#'.$backstretch_id.'{height:'.str_replace( 'px', '', $height ).'px;}' );
	}
}

// -- Finalize
$cck->addScript( $cck->base.'/templates/'.$cck->template.'/js/jquery.backstretch.min.js' );
$cck->addJS( $selector.'.backstretch(['.$images.'], {fade: '.$fade.',duration: '.$duration.'});' );
$cck->finalize();
?>