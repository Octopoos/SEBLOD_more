<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_RestrictionIncrement extends JCckPluginRestriction
{
	protected static $type	=	'increment';
	
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
		
		$restriction	=	parent::g_getRestriction( $field->restriction_options );
		
		return self::_authorise( $restriction, $field, $config );
	}
	
	// _authorise
	protected static function _authorise( $restriction, &$field, &$config )
	{
		$do				=	$restriction->get( 'do', 0 );
		$execute		=	$restriction->get( 'execute', 0 );
		$identifier		=	$restriction->get( 'identifier_name', '' );

		if ( $identifier != '' ) {
			require_once JPATH_SITE.'/plugins/cck_field_typo/joomla_jgrid/joomla_jgrid.php';

			$i				=	plgCCK_Field_TypoJoomla_Jgrid::getStaticValue( $identifier );
		} else {
			static $i		=	0;
			static $loaded	=	array();

			$idx			=	$config['pk'];

			if ( !isset( $loaded[$idx] ) ) {
				$loaded[$idx]	=	++$i;
			}
		}

		if ( $i == $execute ) {
			$do	=	( $do ) ? false : true;
		} else {
			$do	=	( $do ) ? true : false;
		}
		
		if ( $do ) {
			return true;
		} else {
			$field->display	=	0;
			$field->state	=	0;

			return false;
		}
	}
}
?>