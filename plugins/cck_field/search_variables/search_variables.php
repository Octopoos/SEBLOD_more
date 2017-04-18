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
class plgCCK_FieldSearch_Variables extends JCckPluginField
{
	protected static $type		=	'search_variables';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
		$data['display']	=	1;
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['validation']	=	NULL;
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareForm( $field, $config );

		// Prepare
		$app		=	JFactory::getApplication();
		$form		=	'';
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$variables	=	explode( '||', $field->options );
		if ( count( $variables ) ) {
			foreach ( $variables as $k=>$name ) {
				if ( $name ) {
					$request	=	'get'.ucfirst( ( $options2['options'][$k]->type ) ? $options2['options'][$k]->type : '' );
					$value		=	$app->input->$request( $name, '' );
					$value		=	htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
					$form		.=	'<input class="inputbox hidden" type="hidden" id="'.$name.'" name="'.$name.'" value="'.$value.'" />';
				}
			}
		}
		$field->form	=	$form;
		$field->value	=	'';

		// Return
		if ( $return === true ) {
			return $field;
		}	
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareForm( $field, $config );

		// Prepare
		$app		=	JFactory::getApplication();
		$form		=	'';
		$options2	=	json_decode( $field->options2, true );
		$variables	=	explode( '||', $field->options );
		if ( count( $variables ) ) {
			foreach ( $variables as $k=>$name ) {
				if ( $name ) {
					$request	=	'get'.ucfirst( ( $options2['options'][$k]['type'] ) ? $options2['options'][$k]['type'] : '' );
					$value		=	$app->input->$request( $name, '' );
					$value		=	htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
					$form		.=	'<input class="inputbox hidden" type="hidden" id="'.$name.'" name="'.$name.'" value="'.$value.'" />';
				}
			}
		}
		$field->form	=	$form;
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>