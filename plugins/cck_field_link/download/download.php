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
class plgCCK_Field_LinkDownload extends JCckPluginLink
{
	protected static $type	=	'download';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		// Prepare
		$link_class		=	$link->get( 'class', '' );
		$link_more		=	( $config['client'] == 'intro' /*|| $config['client'] == 'list' || $config['client'] == 'item'*/ ) ? '&client='.$config['client'] : '';
		$xi				=	0;

		// Set
		if ( is_array( $field->value ) ) {
			$collection			=	$field->name;
			
			foreach ( $field->value as $f ) {
				$query			=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$config['id'].' AND a.field = "'.(string)$f->name.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$xi;
				$field->hits	=	(int)JCckDatabase::loadResult( $query ); //@
				
				$link_more2		=	$link_more.'&collection='.$collection.'&xi='.$xi;
				$f->link		=	'index.php?option=com_cck&task=download'.$link_more2.'&file='.$f->name.'&id='.$config['id'];
				$f->link_class	=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$xi++;
			}
			$field->link		=	'#';	//todo
		} else {
			$collection			=	'';
			
			$query				=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$config['id'].' AND a.field = "'.(string)$field->name.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$xi;
			$field->hits		=	(int)JCckDatabase::loadResult( $query ); //@
			$field->link		=	'index.php?option=com_cck&task=download'.$link_more.'&file='.$field->name.'&id='.$config['id'];
			$field->link_class	=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
		}
	}
}
?>