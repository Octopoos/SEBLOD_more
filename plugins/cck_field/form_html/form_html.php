<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Registry\Registry;

// Plugin
class plgCCK_FieldForm_Html extends JCckPluginField
{
	protected static $type		=	'form_html';
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
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['live']			=	null;
		$data['match_mode']		=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['live']			=	null;
		$data['markup']			=	null;
		$data['markup_class']	=	null;
		$data['validation']		=	null;
		$data['variation']		=	null;
		
		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
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
		$field->display	=	0;
		$field->value	=	'';
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
		$value		=	( $value != ' ' ) ? $value : '';
		
		// Prepare
		$registry	=	new Registry( $field->options2 );
		$html		=	$registry->get( 'html', '*value*' );

		if ( $html != '' ) {
			$matches	=	'';
			$search		=	'#\*([a-zA-Z0-9_]*)\*#U';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $target ) {
					if ( $target == 'value' ) {
						$html	=	str_replace( '*'.$target.'*', $value, $html );
					} elseif ( isset( $field->$target ) ) {
						if ( is_array( $field->$target ) ) {
							$html	=	str_replace( '*'.$target.'*', ( ( isset( $field->{$target}[0] ) ) ? $field->{$target}[0] : '' ), $html );
						} else {
							$html	=	str_replace( '*'.$target.'*', $field->$target, $html );
						}
					}
				}
			}
		}
		if ( $html != '' && strpos( $html, '$cck->get' ) !== false ) {
			$matches	=	'';
			$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,\[\]]*)\' ?\)(;)?#';
			preg_match_all( $search, $html, $matches );
			if ( count( $matches[1] ) ) {
				parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'matches'=>$matches ) );
			}
		}

		// Set
		$field->form	=	$html;
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
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		$field->markup	=	'none';

		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		$field->markup	=	'none';

		return parent::g_onCCK_FieldRenderForm( $field );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events

	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];
		
		if ( count( $process['matches'][1] ) ) {
			foreach ( $process['matches'][1] as $k=>$v ) {
				$fieldname		=	$process['matches'][2][$k];
				$search			=	'';
				$target			=	strtolower( $v );
				$value			=	'';
				
				$pos					=	strpos( $target, 'safe' );

				if ( $pos !== false && $pos == 0 ) {
					$target				=	substr( $target, 4 );

					if ( isset( $fields[$fieldname] ) ) {
						$value			=	$fields[$fieldname]->$target;
						$value			=	JCckDev::toSafeID( $value );
					}
				} else {
					if ( isset( $fields[$fieldname] ) ) {
						$value			=	$fields[$fieldname]->$target;
					}
				}

				$fields[$name]->form	=	str_replace( $process['matches'][0][$k], $value, $fields[$name]->form );
			}
		}
	}
}
?>