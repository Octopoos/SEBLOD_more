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

// Plugin
class plgCCK_Field_LiveRandom_Option extends JCckPluginLive
{
	protected static $type	=	'random_option';
	
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
		
		// Prepare
		$opts		=	explode( '||', $field->options );
		$count		=	count( $opts );

		if ( $count ) {
			$min	=	0;
			$max	=	$count - 1;
			$idx	=	(string)rand( $min, $max );
			$live	=	( isset( $opts[$idx] ) ) ? $opts[$idx] : '';
			if ( $live != '' && strpos( $live, '=' ) !== false ) {
				$lives	=	explode( '=', $live );
				$live	=	$lives[1];
			}
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>