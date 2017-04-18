<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldVideo_Dailymotion extends JCckPluginField
{
	protected static $type	=	'video_dailymotion';
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
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$video		=	self::_addVideo( $value, $options2, $field->bool2 );

		// Set
		$field->value	=	$value;
		$field->html	=	( $video ) ? $video : $value;
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
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value, ENT_QUOTES );
		
		// Preview Video
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$preview	= 	isset( $options2['video_preview'] ) ? $options2['video_preview'] : '0';
		$width		= 	isset( $options2['video_width'] ) ? $options2['video_width'] : '300';
		$height		=	isset( $options2['video_height'] ) ? $options2['video_height'] : '300';
		$video		=	'';

		if ( $preview == 1 && $value ){
			$video	=	self::_addVideo( $value, $options2, $field->bool2 );
			$video	=	'<div style="float: clear;"></div><div class="video" style="float: left;">'.$video.'</div>';
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			if ( $field->minlength > 0 ) {
				$field->validate[]	=	'minSize['.$field->minlength.']';
			}
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />'.$video;
		
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
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

	// _addVideo
	protected function _addVideo( $value, $options2, $bool2 )
	{
		if ( trim( $value ) == '' ) {
			return;
		}

		$v_int	=	preg_match( '/video\/[^\_]*/i', $value, $v_value );
		if ( $v_int > 0 ) {
			$v_tag	=	$v_value[0];
		} else {
			$v_tag	=	'video/'.$value;
		}

		if ( isset( $v_tag ) ) {
			$width	=	( isset( $options2['video_width'] ) && $options2['video_width'] ) ?	$options2['video_width'] : '300';
			$height	=	( isset( $options2['video_height'] ) && $options2['video_height'] ) ? $options2['video_height'] : '300';
			
			if ( $bool2 == 0 ) {
				$video	=	'<iframe width="'.$width.'" height="'.$height.'" ';
				$video	.=	'frameborder="0" src="';
				$video	.=	'//www.dailymotion.com/embed/'.$v_tag.'?width='.$width;
				$video	.=	'" allowfullscreen></iframe>';
			} else {
				$scheme	=	JUri::getInstance()->getScheme();
				$video	= 	'<object width="'.$width.'" height="'.$height.'">';
				$video	.=	'<param value="'.$scheme.'://www.dailymotion.com/swf/'.$v_tag.'" name="movie"></param>';
				$video	.=	'<param name="allowFullScreen" value="true"></param>';
				$video	.=	'<param name="allowScriptAccess" value="always"></param>';
				$video	.=	'<param name="wmode" value="transparent"></param>';
				$video	.=	'<embed width="'.$width.'" height="'.$height.'" wmode="transparent" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" src="';
				$video	.=	$scheme.'://www.dailymotion.com/swf/'.$v_tag.'?width='.$width;
				$video	.=	'"></embed></object>';
			}
		} else {
			$video	=	$value;
		}
		
		return $video;
	}
}
?>