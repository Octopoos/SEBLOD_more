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

JLoader::register('RedirectTableLink', JPATH_ADMINISTRATOR .'/components/com_redirect/tables/link.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_Redirection extends JCckPluginLocation
{
	protected static $type			=	'joomla_redirection';
	protected static $type_alias	=	'Redirection';
	protected static $table			=	'#__redirect_links';
	protected static $table_object	=	array( 'Link', 'RedirectTable' );
	protected static $key			=	'id';
	
	protected static $access		=	'';
	protected static $author		=	'';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'';
	protected static $created_at	=	'created_date';
	protected static $custom		=	'';
	protected static $modified_at	=	'modified_date';
	protected static $parent		=	'';
	protected static $parent_object	=	'';
	protected static $status		=	'published';
	protected static $to_route		=	'a.id as pk, a.old_url';
	
	protected static $context		=	'com_redirect.link'; /* used for Delete/Save events */
	protected static $context2		=	'';
	protected static $contexts		=	array(); /* used for Content/Intro views */
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onContentAfterDelete',
											'afterSave'=>'onContentAfterSave',
											'beforeDelete'=>'onContentBeforeDelete',
											'beforeSave'=>'onContentBeforeSave'
										);
	protected static $ordering		=	array( 'alpha'=>'old_url ASC', 'newest'=>'created_date DESC', 'oldest'=>'created_date ASC', 'popular'=>'hits DESC' );
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_Storage_LocationConstruct
	public function onCCK_Storage_LocationConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( empty( $data['storage_table'] ) ) {
			$data['storage_table']	=	self::$table;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Storage_LocationPrepareContent
	public function onCCK_Storage_LocationPrepareContent( &$field, &$storage, $pk = 0, &$config = array(), &$row = null )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == self::$table ) {
			$storage = self::_getTable( $pk );
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
                $config['storages'][self::$table] =	self::_getTable( $pk );
			}
		}
	}
	
	// onCCK_Storage_LocationPrepareForm
	public function onCCK_Storage_LocationPrepareForm( &$field, &$storage, $pk = 0, &$config = array() )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Set
		if ( $table == self::$table ) {
			$storage = self::_getTable( $pk );
		} else {
			$storage = parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
		}
	}
	
	// onCCK_Storage_LocationPrepareItems
	public function onCCK_Storage_LocationPrepareItems( &$field, &$storages, $pks, &$config = array(), $load = false )
	{
		if ( self::$type != $field->storage_location ) {
			return;
		}
		
		// Init
		$table	=	$field->storage_table;
		
		// Prepare
		if ( $load ) {
			if ( $table == self::$table ) {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
			} else {
				$storages[$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$table.' WHERE id IN ('.$config['pks'].')', 'id' );
				if ( !isset( $storages[self::$table] ) ) {
					$storages['_']			=	self::$table;
					$storages[self::$table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.self::$table.' WHERE '.self::$key.' IN ('.$config['pks'].')', self::$key );
				}
			}
		}
	}
	
	// onCCK_Storage_LocationPrepareList
	public static function onCCK_Storage_LocationPrepareList( &$params )
	{
	}
	
	// onCCK_Storage_LocationPrepareOrder
	public function onCCK_Storage_LocationPrepareOrder( $type, &$order, &$tables, &$config = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		$order	=	( isset( self::$ordering[$order] ) ) ? $tables[self::$table]['_'] .'.'. self::$ordering[$order] : '';
	}
	
	// onCCK_Storage_LocationPrepareSearch
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config, &$inherit, $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Prepare
		if ( ! isset( $tables[self::$table] ) ) {
			$tables[self::$table]	=	array( '_'=>'t'.$t++,
												'fields' => array(),
											   'join' => 1,
											   'location'=> self::$type
										);
		}
		
		// Set
		$t_pk	=	$tables[self::$table]['_'];
		if ( ! isset( $tables[self::$table]['fields']['published'] ) ) {
			$query->where( $t_pk.'.published = 1' );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Store

	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app	=	JFactory::getApplication();
		$table	=	self::_getTable( $pk );
		
		if ( !$table ) {
			return false;
		}
		
		// Check
		$user 			=	JCck::getUser();
		$canDelete		=	$user->authorise( 'core.delete', 'com_cck.form.'.$config['type_id'] );
		$canDeleteOwn	=	$user->authorise( 'core.delete.own', 'com_cck.form.'.$config['type_id'] );
		if ( !$canDelete && !$canDeleteOwn ) {
			$app->enqueueMessage( JText::_( 'COM_CCK_ERROR_DELETE_NOT_PERMITTED' ), 'error' );
			return;
		}
		
		// Process
		$result	=	$app->triggerEvent( 'onContentBeforeDelete', array( self::$context, $table ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
		if ( !$table->delete( $pk ) ) {
			return false;
		}
		$app->triggerEvent( 'onContentAfterDelete', array( self::$context, $table ) );
		
		return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$isNew			=	true;
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		} else {
			$isNew			=	false;
		}
		
		// Init
		$app	=	JFactory::getApplication();
		$table	=	self::_getTable( $pk );
		$isNew	=	( $pk > 0 ) ? false : true;

		$config['params']	=	$this->params->toArray();
		
		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			$config['error']	=	true;

			return false;
		}
		
		// Prepare
		$table->bind( $data );
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Store
		$app->triggerEvent( 'onContentBeforeSave', array( self::$context, &$table, $isNew, $data ) );
		if ( !$table->store() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

			if ( $isNew ) {
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
			}
			$config['error']	=	true;
			
			return false;
		}
		
		// Checkin
		// parent::g_checkIn( $table );
		self::$pk	=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
		$app->triggerEvent( 'onContentAfterSave', array( self::$context, &$table, $isNew ) );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		$table	=	JTable::getInstance( 'Link', 'RedirectTable' );
		
		if ( $pk > 0 ) {
			$table->load( $pk );
		}
		
		return $table;
	}
	
	// _initTable
	protected static function _initTable( &$table, &$data, &$config, $force = false )
	{
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, $config['params'], $force );
		}
	}
	
	// _completeTable
	protected static function _completeTable( &$table, &$data, &$config )
	{
		parent::g_completeTable( $table, self::$custom, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // SEF
	
	// buildRoute
	public static function buildRoute( &$query, &$segments, $config, $menuItem = null )
	{
	}
	
	// getRoute
	public static function getRoute( $item, $sef, $itemId, $config = array() )
	{
		$route		=	'';
		
		return JRoute::_( $route );
	}
	
	// getRouteByStorage
	public static function getRouteByStorage( &$storage, $sef, $itemId, $config = array() )
	{
		if ( isset( $storage[self::$table]->_route ) ) {
			return JRoute::_( $storage[self::$table]->_route );
		}
		
		if ( $sef ) {
			$storage[self::$table]->_route	=	'';
		} else {
			$storage[self::$table]->_route	=	'';
		}
		
		return JRoute::_( $storage[self::$table]->_route );
	}
	
	// parseRoute
	public static function parseRoute( &$vars, &$segments, $n, $config )
	{
	}
	
	// setRoutes
	public static function setRoutes( $items, $sef, $itemId )
	{
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$item->link	=	self::getRoute( $item, $sef, $itemId );
			}
		}
	}
	
	// _getRoute
	public static function _getRoute( $itemId, $id, $option = '' )
	{
        return '';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff

	// checkIn
	public static function checkIn( $pk = 0 )
	{
		return true;
	}
}
?>