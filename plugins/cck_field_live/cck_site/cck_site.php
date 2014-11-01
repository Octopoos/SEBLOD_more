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
class plgCCK_Field_LiveCck_Site extends JCckPluginLive
{
	protected static $type	=	'cck_site';
	
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
		
		// Prepare
		$property	=	$options->get( 'property' );
		if ( $property && JCck::isSite() ) {
			$site			=	JCck::getSite();
			$site_options	=	( is_object( $site ) ) ? new JRegistry( $site->options ) : new JRegistry;
			$live			=	$site_options->get( $property, '' );
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>