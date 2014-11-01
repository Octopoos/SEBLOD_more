<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
		$collection		=	''; // todo: collection
		$link			=	( $config['client'] == 'intro' /*|| $config['client'] == 'list' || $config['client'] == 'item'*/ ) ? '&client='.$config['client'] : '';
		$xi				=	0;

		// Prepare
		$query			=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$config['id'].' AND a.field = "'.(string)$field->name.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$xi;
		$hits			=	JCckDatabase::loadResult( $query ); //@

		// Set
		$field->hits	=	( $hits ) ? $hits : 0;
		$field->link	=	'index.php?option=com_cck&task=download'.$link.'&file='.$field->name.'&id='.$config['id'];
	}
}
?>