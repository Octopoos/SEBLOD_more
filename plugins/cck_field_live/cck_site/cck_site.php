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
		$target		=	$options->get( 'target' );

		if ( $target == '' ) {
			$target	=	'options';
		}
		if ( JCck::isSite() ) {
			if ( $property ) {
				$site			=	JCck::getSite();

				if ( is_object( $site ) ) {
					if ( $target == 'options' ) {
						$site_options	=	new JRegistry( $site->options );
						$live			=	$site_options->get( $property, '' );
					} elseif ( $target == 'configuration' ) {
						$site_config	=	new JRegistry( $site->configuration );
						$live			=	$site_config->get( $property, '' );
					} elseif ( isset( $site->$property ) ) {
						$live			=	$site->$property;	
					}
				}
			}
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>