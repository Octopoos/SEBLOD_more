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
class plgCCK_FieldSearch_Pagination extends JCckPluginField
{
	protected static $type		=	'search_pagination';
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
		$data['live']		=	null;
		$data['match_mode']	=	null;
		$data['validation']	=	null;
		
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
		if ( $field->bool == 2 ) {
			$field->label	=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : 'Next' );
		} elseif ( $field->bool == 1 ) {
			$field->label	=	( @$field->label2 ) ? $field->label2 : ( ( $field->label ) ? $field->label : 'Previous' );
		}
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$app		=	JFactory::getApplication();
		$form		=	'';
		$value		=	'';
		$start		=	(int)$app->input->getInt( 'start', 0 );

		// Prepare
		if ( $field->bool == 2 ) {
			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'id'=>$id, 'label'=>$field->label, 'name'=>$name, 'behavior'=>$field->bool ) );
		} elseif ( $field->bool == 1 ) {
			parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'id'=>$id, 'label'=>$field->label, 'name'=>$name, 'behavior'=>$field->bool ) );
		} else {
			$form	=	'';
		}

		// Set
		$field->form	=	$form;
		$field->value	=	'';
		$field->label	=	'';
		
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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$name	=	$process['name'];

		$start	=	(int)$app->input->getint( 'start', 0 );
		$end	=	$config['limitend'];
		$prev	=	$start - $end;
		$next	=	$start + $end;

		if ( $process['behavior'] == 1 && ( $start > 0 ) ) {
			$link					=	JUri::current();
			if ( $prev > 0 ) {
				$link				.=	'?start='.$prev;
			}
			$fields[$name]->form	=	'<a href="'.$link.'">'.$process['label'].'</a>';
		} elseif ( $process['behavior'] == 2 && ( $next < $config['total'] ) ) {
			$link					=	JUri::current().'?start='.$next;
			$fields[$name]->form	=	'<a href="'.$link.'">'.$process['label'].'</a>';
		}
	}
}
?>