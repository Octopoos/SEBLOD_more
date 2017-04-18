<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Field_TypoPre extends JCckPluginTypo
{
	protected static $type	=	'pre';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$mode	=	(int)$typo->get( 'typo_mode', '0' );

		if ( !$mode ) {
			$value	=	parent::g_hasLink( $field, $typo, $field->$target );
		} else {
			$value	=	$field->$target;
		}
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );

		if ( $mode ) {
			$field->typo		=	parent::g_hasLink( $field, $typo, $field->typo );
			$field->typo_mode	=	1;
		} else {
			/*if ( $field->link == '' ) {*/
			parent::g_addProcess( 'beforeRenderContent', self::$type, $config, array( 'name'=>$field->name ) );
			/*}*/
		}
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$value	=	htmlentities( $value );
		
		return '<pre>'.$value.'</pre>';
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_Field_TypoBeforeRenderContent
	public static function onCCK_Field_TypoBeforeRenderContent( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( isset( $fields[$name]->html ) && $fields[$name]->typo_target != 'html' ) {
			if ( strpos( $fields[$name]->typo, '<a' ) !== false ) {
				$fields[$name]->typo	=	'<pre>'.htmlentities( $fields[$name]->html ).'</pre>';
			}
		}
	}
}
?>