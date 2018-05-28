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
		$form		=	'';
		$value		=	'';

		// Prepare
		parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'id'=>$id, 'name'=>$name, 'clear_all'=>$field->bool2, 'child_names'=>$field->options ) );
		
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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$app				=	JFactory::getApplication();
		$name				=	$process['name'];

		$class				=	( $fields[$name]->css ) ? ' class="'.$fields[$name]->css.'"' : '';
		$clear_all			=	false;
		$clear_all_js		=	'';
		$children			=	self::_getChildren( $process['child_names'], $config );
		$form				=	'';
		$submit				=	( isset( $config['submit'] ) && isset( $config['submit'] ) ) ? $config['submit'] : 'JCck.Core.submit';

		if ( count( $children ) ) {
			$doTranslation	=	$config['doTranslation'];

			foreach ( $children as $child ) {
				$val		=	'';
				$value		=	$app->input->get( $child->name, '', 'array' );

				if ( isset( $fields[$child->name] ) ) {
					$value	=	$fields[$child->name]->value;

					if ( $child->divider ) {
						$value	=	explode( $child->divider, $value );
					}
					if ( !is_array( $value ) ) {
						$value	=	array( 0=>$value );
					}

					if ( !$fields[$child->name]->state ) {
						continue;
					}
				}
				if ( $doTranslation ) {
					$config['doTranslation']	=	$child->bool8;
				}
				if ( is_array( $value ) ) {
					foreach ( $value as $v ) {
						$t	=	$v;
						
						if ( JCck::callFunc( 'plgCCK_Field'.$child->type, 'isFriendly' ) ) {
							$t	=	JCck::callFunc_Array( 'plgCCK_Field'.$child->type, 'getTextFromOptions', array( $child, $v, $config ) );
						}
						if ( $v != '' ) {
							$clear_all		=	true;
							$clear_all_js	.=	'jQuery(\'#'.$child->name.'\').myClear(\''.$v.'\'); ';
							$val			.=	'<a'.$class.' href="javascript:void(0);" onclick="jQuery(\'#'.$child->name.'\').myClear(\''.$v.'\'); '.$submit.'(\'search\');">'.$t.'</a>';
						}
					}
				}
				if ( $val != '' ) {
					if ( $child->label ) {
						if ( $child->label == 'clear' || $child->label == 'none' ) {
							$child->label	=	'';
						}
						if ( $config['doTranslation'] ) {
							if ( $child->label == '&nbsp;' ) {
								$child->label	=	'Nbsp';
							}
							if ( trim( $child->label ) ) {
								$child->label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $child->label ) ) );
							}
						}	
					}
					$form	.=	'<li><span>'.$child->label.'</span>'.JText::_( 'COM_CCK_PAIR_KEY_VALUE_SEPARATOR' ).$val.'</li>';
				}
				$config['doTranslation']	=	$doTranslation;
			}
		}
		if ( $process['clear_all'] && $clear_all ) {
			$form	=	'<li><a'.$class.' href="javascript:void(0);" onclick="'.$clear_all_js.' '.$submit.'(\'search\');">'.JText::_( 'COM_CCK_PLG_SEARCH_BREADCRUMBS_ALL_RESULTS' ).'</a></li>'.$form;
		}
		if ( $form ) {
			$form	=	'<ul>'.$form.'</ul>';
		}

		$fields[$name]->form	=	$form;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _getChildren
	protected static function _getChildren( $child_names, $config = array() )
	{
		$names	=	'"'.str_replace( '||', '","', $child_names ).'"';
		
		$query	= 	'SELECT a.name, a.type, a.label, a.options, a.options2, a.divider, a.bool2, a.bool3, a.bool8'
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