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
class plgCCK_FieldCheckbox_Dynamic extends JCckPluginField
{
	protected static $type	=	'checkbox_dynamic';
	protected static $friendly	=	1;
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Add Database Process
		if ( $data['bool2'] == 0 ) {
			$app 	= 	JFactory::getApplication();
			$ext	=	$app->getCfg( 'dbprefix' );

			if ( isset( $data['json']['options2']['table'] ) ) {
				$data['json']['options2']['table']	=	str_replace( $ext, '#__', $data['json']['options2']['table'] );
			}
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array() )
	{
		$data['variation'][]	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LIST' ) );
		$data['variation'][]	=	JHtml::_( 'select.option', 'list', JText::_( 'COM_CCK_DEFAULT' ) );
		$data['variation'][]	=	JHtml::_( 'select.option', 'list_filter', JText::_( 'COM_CCK_FORM_FILTER' ) );
		$data['variation'][]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );

		// Init
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$divider	=	'';
		
		// Prepare
		$divider			=	( $field->divider != '' ) ? $field->divider : ',';
		$field->options		=	self::_getOptionsList( $options2, $field->bool2 );

		// Set
		$field->text		=	parent::g_getOptionText( $value, $field->options, $divider, $config );		
		$field->value		=	$value;
		$field->typo_target	=	'text';
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
			$id		=	( @$inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( @$inherit['name'] != '' ) ? $inherit['name'] : $field->name;
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
		if ( $field->bool2 == 0 ) {
			$opt_table			=	isset( $options2['table'] ) ? ' FROM '.$options2['table'] : '';
			$opt_name			=	isset( $options2['name'] ) ? $options2['name'] : '';
			$opt_value			=	isset( $options2['value'] ) ? $options2['value'] : '';
			$opt_where			=	( isset( $options2['where'] ) && trim( $options2['where'] ) != '' ) ? ' WHERE '.$options2['where']: '';
			$opt_orderby		=	( isset( $options2['orderby'] ) && trim( $options2['orderby'] ) != '' ) ? ' ORDER BY '.$options2['orderby'].' '.( ( @$options2['orderby_direction'] != '' ) ? $options2['orderby_direction'] : 'ASC' ) : '';
			$opt_limit			=	( isset( $options2['limit'] ) && $options2['limit'] > 0 ) ? ' LIMIT '.$options2['limit'] : '';
			
			if ( $opt_name && $opt_value && $opt_table ) {
				$query			=	'SELECT '.$opt_name.','.$opt_value.$opt_table.$opt_where.$opt_orderby.$opt_limit;
				$query			=	JCckDevHelper::replaceLive( $query );
				$items			=	JCckDatabase::loadObjectList( $query );
			}
		} else {
			if ( @$options2['query'] != '' ) {
				// Language Detection
				$lang_code		=	'';
				//self::_languageDetection( $lang_code, $value, $options2 );
				$query	=	str_replace( '[lang]', $lang_code, $options2['query'] );
				$query	=	JCckDevHelper::replaceLive( $query );
				if ( ( strpos( $query, ' value ' ) != false ) || ( strpos( $query, ' value,' ) != false ) ) {
					$items	=	JCckDatabase::loadObjectList( $query );
				} else {
					$opts2	=	JCckDatabase::loadColumn( $query );
					if ( count( $opts2 ) ) {
						$opts2	=	array_combine( array_values( $opts2 ), $opts2 );
					}
					$opts	=	array_merge( $opts, $opts2 );
				}
			}
			$opt_name	=	'text';
			$opt_value	=	'value';
			$opt_group	=	'optgroup';
		}
		
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$o_name		=	isset( $opt_name ) ? $item->$opt_name : $item->text;
				$o_value	=	isset( $opt_value ) ? $item->$opt_value : $item->value;
				$opts[]		=	JHtml::_( 'select.option', $o_value, $o_name, 'value', 'text' );
			}
		}

		$count	=	count( $opts );
		if ( $field->bool ) {
			$orientation	=	' vertical';
			$field->bool5	=	( !$field->bool5 ) ? 1 : $field->bool5;
			$modulo			=	$count % $field->bool5;
			$columns		=	(int)( $count / ( !$field->bool5 ? 1 : $field->bool5 ) );
		} else {
			$orientation	=	'';
		}
		
		$class			=	'checkboxes'.$orientation . ( $field->css ? ' '.$field->css : '' );
		$attr			=	'class="'.$class.'"' . ( $field->attributes ? ' '.$field->attributes : '' );
		$form			=	'<fieldset id="'.$id.'" '.$attr.'>';
		$attr			=	'class="inputbox checkbox'.$validate.'" size="1"';
		if ( $field->bool && $field->bool5 > 1 && $count > 1 ) {
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
				$attr2		=	'';
				$checked	=	( in_array( (string)$o->value, (array)$value ) ? ' checked="checked" ' : '' );
				$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.$attr2.' />';

				$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
			}
			$form		.=	'</div>';
		} else {
			foreach ( $opts as $i=>$o ) {
				$checked	=	( in_array( (string)$o->value, (array)$value ) ? ' checked="checked" ' : '' );
				$form		.=	'<input type="checkbox" id="'.$id.$i.'" name="'.$name.'[]" value="'.$o->value.'" '.$checked.$attr.' />';
				$form		.=	'<label for="'.$id.$i.'">'.$o->text.'</label>';
			}
		}
		$form	.=	'</fieldset>';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->variation != 'hidden' ){
				self::_addStyle();
			}
		} else {
			if ( $field->variation == 'form_filter' ){
				self::_addStyle();
			}
			$field->text	=	parent::g_getOptionText( $value, $field->options, $divider, $config );
			parent::g_getDisplayVariation( $field, $field->variation, $value, $field->text, $form, $id, $name, '<input' );
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
			$name	=	( @$inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}

		// Set or Return
		$divider	=	( $field->divider != '' ) ? $field->divider : ',';
		$nb 		=	count( $value );
		if ( is_array( $value ) && $nb > 0 ) {
			$value	=	implode( $divider, $value );
		}
		$options2			=	JCckDev::fromJSON( $field->options2 );
		$field->options		=	self::_getOptionsList( $options2, $field->bool2 );
		
		// Validate
		$text	=	parent::g_getOptionText( $value, $field->options, $divider, $config );
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->text	=	$text;
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
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

	// _addStyle
	protected static function _addStyle()
	{
		static $loaded	=	0;
			if ( $loaded ) {
				return;
		}

		$doc	=	JFactory::getDocument();
		$loaded	=	1;

		$doc->addStyleSheet( self::$path.'assets/css/style.css' );
	}
	
	// _getOptionsList
	protected static function _getOptionsList( $options2, $bool2 )
	{
		$options	=	'';
		
		if ( $bool2 == 0 ) {
			$opt_table	=	( isset( $options2['table'] ) ) ? ' FROM '.$options2['table'] : '';
			$opt_name	=	( isset( $options2['name'] ) ) ? $options2['name'] : '';
			$opt_value	=	( isset( $options2['value'] ) ) ? $options2['value'] : '';
			$opt_where	=	( isset( $options2['where'] ) && trim( $options2['where'] ) != '' ) ? ' WHERE '.$options2['where']: '';
			
			if ( $opt_name && $opt_table ) {
				$query	=	'SELECT '.$opt_name.','.$opt_value.$opt_table.$opt_where;
				$query	=	JCckDevHelper::replaceLive( $query );
				$lists	=	JCckDatabase::loadObjectList( $query );
				if ( count( $lists ) ) {
					foreach ( $lists as $list ) {
						$options	.=	$list->$opt_name.'='.$list->$opt_value.'||';
					}
				}
			}
		} else {
			$opt_query	=	isset( $options2['query'] ) ? $options2['query'] : '';
			$opt_query	=	JCckDevHelper::replaceLive( $opt_query );
			$lists		=	JCckDatabase::loadObjectList( $opt_query );
			if ( count( $lists ) ) {
				foreach ( $lists as $list ) {
					$options	.=	@$list->text.'='.@$list->value.'||';
				}
			}
		}
		
		return $options;
	}
}
?>