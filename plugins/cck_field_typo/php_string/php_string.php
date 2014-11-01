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
class plgCCK_Field_TypoPhp_String extends JCckPluginTypo
{
	protected static $type	=	'php_string';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo		=	parent::g_getTypo( $field->typo_options );
		$target2	=	$typo->get( 'typo_target', '' );
		if ( $target2 ) {
			$target	=	$target2;
		}
		$value		=	parent::g_hasLink( $field, $typo, $field->$target );
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$function	=	$typo->get( 'function', '' );
		$arg1		=	$typo->get( 'arg1', '' );
		$arg2		=	$typo->get( 'arg2', '' );
		$arg3		=	$typo->get( 'arg3', '' );
		$force		=	$typo->get( 'force', '' );
		$prefix		=	$typo->get( 'prefix', '' );
		$suffix		=	$typo->get( 'suffix', '' );

		switch ( $function ) {
			case 'number_format':
				$value		=	number_format( (float)$value, $arg1, ( $arg2 ? $arg2 : '.' ), ( $arg3 ? $arg3 : '' ) );
				break;
			case 'str_repeat':
				$value		=	str_repeat( $arg1, (int)$value );
				break;
			case 'str_replace':
				$value		=	str_replace( $arg1, $arg2, $value );
				break;
			case 'strip_tags':
				$value		=	strip_tags( $value );
				break;
			case 'strtolower':
				$value		=	JString::strtolower( $value );
				break;
			case 'strtoupper':
				$value		=	JString::strtoupper( $value );
				break;
			case 'substr':
				if ( $force ) {
					$value	=	strip_tags( $value );
				}
				$value2		=	( $arg2 != '' ) ? JString::substr( $value, $arg1, $arg2 ) : JString::substr( $value, $arg1 );
				if ( $value2 != $value ) {
					$value	=	trim( $value2 ).$typo->get( 'suffix_overflow', '' );
				}
				break;
			case 'ucfirst':
				if ( $force ) {
					$value	=	JString::strtolower( $value );
				}
				$value		=	JString::ucfirst( $value );
				break;
			case 'ucwords':
				if ( $force ) {
					$value	=	JString::strtolower( $value );
				}
				$value		=	JString::ucwords( $value );
				break;
			case 'wordwrap':
				if ( $force ) {
					$value	=	strip_tags( $value );
				}
				$value		=	wordwrap( $value, $arg1 );
				break;
			default:
				break;
		}
		
		return $prefix.$value.$suffix;
	}
}
?>