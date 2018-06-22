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
class plgCCK_Field_LiveLorem_Ipsum extends JCckPluginLive
{
	protected static $type	=	'lorem_ipsum';
	protected static $text	=	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ultricies, nisl at interdum viverra, lectus mauris cursus magna, sed blandit est massa sed libero. Praesent fermentum facilisis porttitor. Phasellus volutpat elit vel ante adipiscing ac consequat tellus accumsan. Aenean mattis libero id dui lobortis venenatis. Suspendisse imperdiet ipsum ut magna tristique faucibus. Ut luctus rutrum feugiat. Ut risus lacus, tincidunt ultricies aliquet vitae, dapibus vitae dolor. Sed ornare, eros ut gravida aliquam, orci est vestibulum libero, at facilisis urna justo nec velit. Nunc turpis quam, accumsan gravida fermentum non, scelerisque non nisi. Suspendisse eu magna sit amet tortor facilisis luctus.';
	
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
		$n			=	(int)$options->get( 'limit', 2 );
		$text		=	explode( ' ', self::$text );
		
		// Prepare
		for ( $i = 0; $i < $n; $i++ ) {
			$live	.=	$text[$i].' ';
		}
		
		// Set
		$value		=	trim( $live );
	}
}
?>