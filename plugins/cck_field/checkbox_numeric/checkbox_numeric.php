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
class plgCCK_FieldCheckbox_Numeric extends JCckPluginField
{
	protected static $type		=	'checkbox_numeric';
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
		
		// todo: $field->values
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
		$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		if ( !is_array( $value ) ) {
			$value	=	explode( $divider, $value );
		}
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$opts		=	array();

		if ( isset( $options2['first'] ) && $options2['first'] != '' ) {
			$opts[]	=	JHtml::_( 'select.option', $options2['first'], $options2['first'], 'value', 'text' );
		}
		$val	=	( $options2['start'] ? $options2['start'] : 0 );
		$step	=	( $options2['step'] ? $options2['step'] : 0 );
		$limit 	=	( $options2['end'] ? $options2['end'] : 0 );
		$math	=	isset( $options2['math'] ) ? $options2['math'] : NULL;
		$force	=	( isset( $options2['force_digits'] ) && $options2['force_digits'] ) ? $options2['force_digits'] : 0;
		
		if ( $step && $val || $step && $limit || $step && $val && $limit ) {
			while ( 69 ) {
				if ( $force ) {
					$val	=	str_pad( $val, $force, '0' , STR_PAD_LEFT );
				}
				if ( $math == 0 && $val <= $limit  ) {
					$opts[]	=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$val	=	$val + $step;
				} elseif ( $math == 1 && $val <= $limit  ) {
					$opts[]	=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$val	=	$val * $step;
				} elseif ( $math == 2 && $val >= $limit  ) {
					$opts[]	=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$val	=	$val - $step;
				} elseif ( $math == 3 && $val > $limit  ) {
					$opts[]	=	JHtml::_('select.option', $val, $val, 'value', 'text' );
					$val	=	floor( $val / $step );
				} else {
					break;
				}
			}
		}
		if ( isset( $options2['last'] ) && $options2['last'] != '' ) {
			$opts[]	=	JHtml::_( 'select.option', $options2['last'], $options2['last'], 'value', 'text' );
		}

		$count	=	count( $opts );
		if ( $field->bool ) {
			$orientation	=	' vertical';
			$field->bool2	=	( !$field->bool2 ) ? 1 : $field->bool2;
			$modulo			=	$count % $field->bool2;
			$columns		=	(int)( $count / ( !$field->bool2 ? 1 : $field->bool2 ) );
		} else {
			$orientation	=	'';
		}
		$class				=	'checkboxes'.$orientation . ( $field->css ? ' '.$field->css : '' );
		$attr				=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form				=	'<fieldset id="'.$id.'" '.$attr.'>';
		$attr				=	'class="checkbox'.$validate.'" size="1"';

		if ( $field->bool && $field->bool2 > 1 && $count > 1 ) {
			$k	=	0;
			foreach ( $opts as $i=>$o ) {
				if ( $i == 0 ) {
					$form	.=	'<div class="cck-fl">';
				} elseif ( ( $modulo && ( $k % ($columns+1) == 0 ) )
						|| ( $modulo <= 0 && ( $k % $columns == 0 ) ) ) {
					$form	.=	'</div><div class="cck-fl">';
					$modulo--;
					$k	=	0;
				}
				$k++;
				$checked	=	( in_array( (string)$o->value, (array)$value ) ) ? ' checked="checked" ' : '';
				$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.' />';
				$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
			}
			$form		.=	'</div>';
		} else {
			foreach ( $opts as $i=>$o ) {
				$checked	=	( in_array( (string)$o->value, (array)$value ) ) ? ' checked="checked" ' : '';
				$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.' />';
				$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
			}
		}
		$form	.=	'</fieldset>';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->variation != 'hidden' ) { 
				self::_addScript();
			}
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
		
		// Init
		$divider			=	$field->match_value ? $field->match_value : $field->divider;
		$field->match_value	=	$divider;
		if ( is_array( $value ) ) {
			$value	=	implode( $divider, $value );
		}
		
		// Prepare
		$field->divider	=	$divider;
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Set
		$field->value	=	$value;
		
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
		
		// Prepare
		$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		if ( $divider ) {
			$nb 	=	count( $value );
			if ( is_array( $value ) && $nb > 0 ) {
				$value	=	implode( $divider, $value );
			}
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

	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}

	// _addScript
	protected static function _addScript()
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}

		$doc	=	JFactory::getDocument();
		$loaded	=	1;

		$doc->addStyleSheet( self::$path.'assets/css/style.css' );
	}
}
?>