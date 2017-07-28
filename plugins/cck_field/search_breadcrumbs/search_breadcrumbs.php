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
class plgCCK_FieldSearch_Breadcrumbs extends JCckPluginField
{
	protected static $type		=	'search_breadcrumbs';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	'';

		// Prepare
		$app				=	JFactory::getApplication();
		$class				=	( $field->css ) ? ' class="'.$field->css.'"' : '';
		$form				=	'';
		$clear_all			=	false;
		$clear_all_js		=	'';
		$field->children	=	self::_getChildren( $field, $config );
		$submit				=	( isset( $config['submit'] ) && isset( $config['submit'] ) ) ? $config['submit'] : 'JCck.Core.submit';
		
		if ( count( $field->children ) ) {
			foreach ( $field->children as $child ) {
				$val		=	'';
				$value		=	$app->input->get( $child->name, '', 'array' );
				
				if ( is_array( $value ) ) {
					foreach ( $value as $v ) {
						$t	=	$v;

						if ( $child->options != '' && strpos( $child->options, '||' ) !== false ) {
							$t		=	parent::g_getOptionText( $v, $child->options, $child->divider, $config );
						}
						if ( $v != '' ) {
							$clear_all		=	true;
							$clear_all_js	.=	'jQuery(\'#'.$child->name.'\').myClear(\''.$v.'\'); ';
							$val			.=	'<a'.$class.' href="javascript:void(0);" onclick="jQuery(\'#'.$child->name.'\').myClear(\''.$v.'\'); '.$submit.'(\'search\');">'.$t.'</a>';
						}
					}
				}
				if ( $val != '' ) {
					$form	.=	'<li>'.$child->label.JText::_( 'COM_CCK_PAIR_KEY_VALUE_SEPARATOR' ).$val.'</li>';
				}
			}
		}
		if ( $field->bool2 && $clear_all ) {
			$form	=	'<li><a'.$class.' href="javascript:void(0);" onclick="'.$clear_all_js.$submit.'(\'search\');">'.JText::_( 'COM_CCK_CLEAR_FILTERS' ).'</a></li>'.$form;
		}
		if ( $form ) {
			$form	=	'<ul class="inline">'.$form.'</ul>';
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}

		$field->value	=	$value;
		
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
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
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
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
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
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _getChildren
	protected static function _getChildren( $parent, $config = array() )
	{
		$names	=	'"'.str_replace( '||', '","', $parent->options ).'"';
		
		$query	= 	'SELECT a.name, a.type, a.label, a.options, a.options2, a.divider'
				.	' FROM #__cck_core_fields AS a'
				.	' WHERE a.name IN ('.$names.') ORDER BY FIELD(name, '.$names. ')'
				;
		$fields	=	JCckDatabase::loadObjectList( $query, 'name' );

		if ( ! count( $fields ) ) {
			return array();
		}
		
		return $fields;
	}
}
?>