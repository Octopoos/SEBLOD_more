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
class plgCCK_Field_RestrictionMobile_Device extends JCckPluginRestriction
{
	protected static $type	=	'mobile_device';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Field_RestrictionPrepareContent
	public static function onCCK_Field_RestrictionPrepareContent( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}

	// onCCK_Field_RestrictionPrepareForm
	public static function onCCK_Field_RestrictionPrepareForm( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// onCCK_Field_RestrictionPrepareStore
	public static function onCCK_Field_RestrictionPrepareStore( &$field, &$config )
	{
		if ( self::$type != $field->restriction ) {
			return;
		}
		
		return true;
	}

	// _authorise
	protected static function _authorise( $restriction, &$field, &$config )
	{
		static $device_type	=	'';
		$do		=	$restriction->get( 'do', 0 );
		$type	=	$restriction->get( 'type', 'mobile' );

		if ( !$device_type ) {
			$device			=	new JCckDevice;
			$device_type	=	( $device->isMobile() ) ? ( $device->isTablet() ? 'tablet' : 'phone' ) : 'desktop';
		}
		
		if ( $type == 'mobile' ) {
			if ( $device_type == 'desktop' ) {
				return ( $do ) ? true : false;
			}
		} elseif ( $type != $device_type ) {
			return ( $do ) ? true : false;
		}
		
		return ( $do ) ? false : true;
	}
}
?>