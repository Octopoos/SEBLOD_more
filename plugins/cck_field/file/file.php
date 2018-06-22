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

// Plugin
class plgCCK_FieldFile extends JCckPluginField
{
	protected static $type	=	'file';
	protected static $friendly	=	1;
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
		
		// Prepare
		if ( $value != '' && $field->bool ) {
			$options2	=	JCckDev::fromJSON( $field->options2 );
			$path		=	$options2['path'];
			$value		=	$path.$value;
		}

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
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		jimport( 'joomla.filesystem.folder' );
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$path		=	substr( $options2['path'], 0, -1 );
		$files		=	JFolder::files( JPATH_SITE.DIRECTORY_SEPARATOR.$path, '.', $field->bool2, true );
		$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
		if ( $legal_ext == 'custom' ) {
			$legal_ext	=	$options2['legal_extensions'];
		} else {
			$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions' );
			if ( !$legal_ext ) {
				$legal_ext	=	$options2['legal_extensions'];
			}
		}
		$legal_ext	=	explode( ',', $legal_ext );
		$opts		=	array();
		if ( trim( $field->selectlabel ) ) {
			if ( $config['doTranslation'] ) {
				$field->selectlabel	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $field->selectlabel ) ) );
			}
			$opts[]	=	JHtml::_( 'select.option',  '', '- '.$field->selectlabel.' -', 'value', 'text' );
		}
		if ( $files ) {
			foreach( $files as $val ) {
				$val 	=	str_replace( '\\', '/', $val );
				$val 	=	substr( strstr( $val, $options2['path'] ), strlen( $options2['path'] ) );
				$pos	=	strrpos( $val, '.' );
				$ext	=	substr( $val, $pos+1 );

				if ( in_array( $ext, $legal_ext ) ) {
					$opts[] = JHtml::_( 'select.option', $val, $val );
				}
			}
		}
		
		if ( $value && strpos( $value, @$options2['path'] ) !== false ) {
			$file = str_replace( @$options2['path'], '', $value );
		}
		$selectedFileList	=	( $value && @$file ) ? $file : $value;
		$form				=	JHtml::_( 'select.genericlist', $opts, $name, 'class="inputbox select '.$validate.'"', 'value', 'text', $selectedFileList );
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<select', '', '', $config );
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
		if ( $value != '' ) {
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
			if ( $legal_ext == 'custom' ) {
				$legal_ext	=	$options2['legal_extensions'];
			} else {
				$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions' );
				if ( !$legal_ext ) {
					$legal_ext	=	$options2['legal_extensions'];
				}
			}
			$legal_ext	=	explode( ',', $legal_ext );
			$pos		=	strrpos( $value, '.' );
			$ext		=	substr( $value, $pos+1 );

			if ( !in_array( $ext, $legal_ext ) ) {
				$value	=	'';
			}
			if ( $value && !$field->bool ) {
				$value	=	$options2['path'].$value;
			}
		}
		
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
	
	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>