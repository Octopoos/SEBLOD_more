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
class plgCCK_FieldButton_Reset extends JCckPluginField
{
	protected static $type		=	'button_reset';
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
	
	// onCCK_FieldConstruct_TypeForm
	public static function onCCK_FieldConstruct_TypeForm( &$field, $style, $data = array(), &$config = array() )
	{
		$data['computation']	=	NULL;
		$data['live']			=	NULL;
		$data['validation']		=	NULL;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
										'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
									);
			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']	=	$config['construction']['variation'][self::$type];
		}

		parent::onCCK_FieldConstruct_TypeForm( $field, $style, $data, $config );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		$data['live']		=	NULL;
		$data['match_mode']	=	NULL;
		$data['validation']	=	NULL;

		if ( !isset( $config['construction']['variation'][self::$type] ) ) {
			$data['variation']	=	array(
										'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
										'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
										'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
										'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED2' ) ),
										'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
										'102'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TOOLBAR' ) ),
										'toolbar_button'=>JHtml::_( 'select.option', 'toolbar_button', JText::_( 'COM_CCK_TOOLBAR_BUTTON' ) ),
										'103'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
									);
			$config['construction']['variation'][self::$type]	=	$data['variation'];
		} else {
			$data['variation']	=	$config['construction']['variation'][self::$type];
		}

		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		$field->label2	=	trim( @$field->label2 );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value			=	$field->label;
		$field->label	=	'';
		
		// Prepare
		$class	=	'button btn' . ( $field->css ? ' '.$field->css : '' );
		$click	=	' onclick="jQuery(\'#'.$config['formId'].'\').clearForm();"';
		$attr	=	'class="'.$class.'"'.$click . ( $field->attributes ? ' '.$field->attributes : '' );
		if ( $field->bool ) {
			$label	=	$value;
			if ( JCck::on() ) {
				if ( $field->bool6 == 3 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
					$attr		.=	' title="'.$value.'"';
				} elseif ( $field->bool6 == 2 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
				} elseif ( $field->bool6 == 1 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
				}
			}
			$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
			$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.'>'.$label.'</button>';
			$tag	=	'button';
		} else {
			$form	=	'<input type="submit" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
			$tag	=	'input';
		}
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			if ( $field->variation == 'toolbar_button' ) {
				if ( !isset( $options2 ) ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
				}
				$field->form	=	'';
				$icon			=	( isset( $options2['icon'] ) && $options2['icon'] ) ? 'icon-'.$options2['icon'] : '';
				$html			=	'<button class="btn btn-small'.( $field->css ? ' '.$field->css : '' ).'" onclick="jQuery(\'#'.$config['formId'].'\').clearForm();" href="#"><i class="'.$icon.'"></i> '.$value.'</button>';
				JToolBar::getInstance( 'toolbar' )->appendButton( 'Custom', $html, @$options2['icon'] );
			} else {
				parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<'.$tag, ' ', '', $config );
			}
		}
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
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>