<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_tag/joomla_tag.php';

// Class
class plgCCK_Storage_LocationJoomla_Tag_Importer extends plgCCK_Storage_LocationJoomla_Tag
{
	protected static $columns_excluded	=	array(); /* TODO#SEBLOD: */

	// getColumnsToImport
	public static function getColumnsToImport()
	{
		$table		=	self::_getTable();
		$columns	=	$table->getProperties();
		
		foreach ( self::$columns_excluded as $column ) {
			if ( array_key_exists( $column, $columns ) ) {
				unset( $columns[$column] );
			}
		}

		return array_keys( $columns );
	}

	// onCCK_Storage_LocationImport
	public static function onCCK_Storage_LocationImport( $data, &$config = array(), $pk = 0 )
	{
		if ( !$config['pk'] ) {
			// Init
			if ( !$pk ) {
				if ( isset( $config['key'] ) && $config['key'] ) {
					if ( isset( $data[$config['key']] ) && $data[$config['key']] != '' ) {
						$pk		=	(int)JCckDatabase::loadResult( 'SELECT '.self::$key.' FROM '.self::$table.' WHERE '.$config['key'].' = "'.$data[$config['key']].'"' );
					}
					$pk		=	( $pk > 0 ) ? $pk : 0;
				} else {
					$pk		=	( isset( $data[self::$key] ) && (int)$data[self::$key] > 0 ) ? (int)$data[self::$key] : 0;
				}
			}

			$app	=	JFactory::getApplication();
			$table	=	self::_getTable( $pk );
			$isNew	=	( $table->{self::$key} > 0 ) ? false : true;
			$iPk	=	0;
			
			if ( $isNew ) {
				if ( isset( $data[self::$key] ) ) {
					$iPk	=	$data[self::$key];
					unset( $data[self::$key] );
				}
				$config['log']	=	'created';
			} else {
				preg_match( '#::cck::(.*)::/cck::#U', $table->{self::$custom}, $matches );
				$config['id']	=	$matches[1];
				$config['log']	=	'updated';
			}
			if ( !$config['id'] ) {
				$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
			}

			self::_initTable( $table, $data, $config, true );
			
			// Prepare
			if ( !empty( $data ) ) {
				$table->bind( $data );
			}
			if ( !@$table->title ) {
				$table->title	=	JFactory::getDate()->format( 'Y-m-d-H-i-s' ) ;
				$table->check();
				$table->alias	.=	'-'.$config['x'];
			} else {
				$table->check();
			}
			self::_completeTable( $table, $data, $config );
			
			// Store
			JPluginHelper::importPlugin( 'content' );
			$app->triggerEvent( 'onContentBeforeSave', array( self::$context, &$table, $isNew ) );
			if ( !$table->store() ) {
				$error		=	true;

				if ( $isNew ) {
					$i		=	2;
					$alias	=	$table->alias.'-'.$i;
					$test	=	JTable::getInstance( 'Tag', 'TagsTable' );
					
					while ( $test->load( array( 'alias'=>$alias, 'parent_id'=>$table->parent_id ) ) ) {
						$alias		=	$table->alias.'-'.$i++;
					}
					$table->alias	=	$alias;

					if ( $table->store() ) {
						$error		=	false;
					}
				}
				if ( $error ) {
					$config['error']	=	true;
					$config['log']		=	'cancelled';
					$config['pk']		=	$pk;
					parent::g_onCCK_Storage_LocationRollback( $config['id'] );
					return false;
				}
			}

			$table->rebuildPath( $table->id );
			$table->rebuild( $table->id, $table->lft, $table->level, $table->path );

			$app->triggerEvent( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );
			
			// Tweak
			if ( $iPk > 0 ) {
				if ( JCckDatabase::execute( 'UPDATE '.self::$table.' SET '.self::$key.' = '.(int)$iPk.' WHERE '.self::$key.' = '.(int)$table->{self::$key} ) ) {
					$table->{self::$key}	=	$iPk;
					$config['auto_inc']		=	( $iPk > $config['auto_inc'] ) ? $iPk : $config['auto_inc'];
					// if ( $table->asset_id ) {
						// JCckDatabase::execute( 'UPDATE #__assets SET name = "com_content.category.'.$iPk.'" WHERE id = '.(int)$table->asset_id );
					// }
				}
			}
			
			if ( !$config['pk'] ) {
				$config['pk']	=	(int)$table->{self::$key};
			}
			$config['isNew']	=	(int)$isNew;
			$config['author']	=	$table->{self::$author};
			$config['parent']	=	$table->{self::$parent};
		}
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, $config['pk'], $config );
		
		return true;
	}
	
	// onCCK_Storage_LocationAfterImport
	public static function onCCK_Storage_LocationAfterImports( $fields, &$config = array() )
	{
	}
}
?>