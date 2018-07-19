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

JLoader::register( 'MessagesTableMessage', JPATH_ADMINISTRATOR.'/components/com_messages/tables/message.php' );

// Plugin
class plgCCK_Storage_LocationJoomla_Message extends JCckPluginLocation
{
	protected static $type			=	'joomla_message';
	protected static $type_alias	=	'Message';
	protected static $table			=	'#__messages';
	protected static $table_object	=	array( 'Message', 'MessagesTable' );
	protected static $key			=	'message_id';
	
	protected static $access		=	'';
	protected static $author		=	'user_id_from';
	protected static $author_object	=	'';
	protected static $bridge_object	=	'';
	protected static $child_object	=	'';
	protected static $created_at	=	'';
	protected static $custom		=	'';
	protected static $modified_at	=	'';
	protected static $parent		=	'';
	protected static $parent_object	=	'';
	protected static $status		=	'state';
	protected static $to_route		=	'';
	
	protected static $context		=	'com_messages.message'; /* used for Delete/Save events */
	protected static $context2		=	'';
	protected static $contexts		=	array(); /* used for Content/Intro views */
	protected static $error			=	false;
	protected static $events		=	array(
											'afterDelete'=>'onContentAfterDelete',
											'afterSave'=>'',
											'beforeDelete'=>'onContentBeforeDelete',
											'beforeSave'=>''
										);
	protected static $ordering		=	array( 'alpha'=>'subject ASC', 'newest'=>'date_time DESC', 'oldest'=>'date_time ASC' );
	protected static $ordering2		=	array();
	protected static $pk			=	0;
	protected static $routes		=	array();
	protected static $sef			=	array( '1'=>'full',
											   '2'=>'full', '22'=>'id', '23'=>'alias', '24'=>'alias',
											   '3'=>'full', '32'=>'id', '33'=>'alias',
											   '4'=>'full', '42'=>'id', '43'=>'alias'
										);
	
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
			$storage			=	self::_getTable( $pk );
			$config['author']	=	$storage->{self::$author};
		} else {
			$storage			=	parent::g_onCCK_Storage_LocationPrepareContent( $table, $pk );
			if ( ! isset( $config['storages'][self::$table] ) ) {
				$config['storages'][self::$table]	=	self::_getTable( $pk );
				$config['author']					=	$config['storages'][self::$table]->{self::$author};
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
			$storage			=	self::_getTable( $pk );
			$config['asset']	=	'';
			$config['asset_id']	=	0;
			$config['author']	=	$storage->{self::$author};
		} else {
			$storage	=	parent::g_onCCK_Storage_LocationPrepareForm( $table, $pk );
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
			if ( count( $storages[self::$table] ) ) {
				$items			=	$storages[self::$table];
				krsort( $items );
				$item			=	current( $items );
				$read_context	=	$this->params->get( 'read_context', 2 );

				if ( $item->folder_id == JFactory::getApplication()->input->get( 't' ) ) {
					if ( $read_context == 2 ) {
						JCckDatabase::execute( 'UPDATE '.self::$table.' SET state = 0 WHERE folder_id = '.(int)$item->folder_id.' AND user_id_to = '.JFactory::getUser()->id );
					} else {
						if ( $item->message_id == JCckDatabase::loadResult( 'SELECT message_id FROM #__messages WHERE folder_id ='.(int)$item->folder_id.' ORDER BY message_id DESC' ) ) {
							if ( $item->user_id_to == JFactory::getUser()->id ) {
								self::_updateState( $item->message_id, '0' );
							}
						}
					}
				}
			}
		}
		$config['author']	=	$storages[self::$table][$config['pk']]->{self::$author};
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
	public function onCCK_Storage_LocationPrepareSearch( $type, &$query, &$tables, &$t, &$config = array(), &$inherit = array(), $user )
	{
		if ( self::$type != $type ) {
			return;
		}
		
		// Prepare
		if ( ! isset( $tables[self::$table] ) ) {
			$tables[self::$table]			=	array( '_'=>'t'.$t++,
													   'fields'=>array(),
													   'join'=>1,
													   'key'=>self::$key,
													   'location'=>self::$type
												);
		} else {
			$tables[self::$table]['key']	=	self::$key;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Store
	
	// onCCK_Storage_LocationDelete
	public static function onCCK_Storage_LocationDelete( $pk, &$config = array() )
	{
		$app		=	JFactory::getApplication();
		$dispatcher	=	JEventDispatcher::getInstance();
		$table		=	self::_getTable( $pk );

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
		$result	=	$dispatcher->trigger( 'onContentBeforeDelete', array( self::$context, $table ) );
		if ( in_array( false, $result, true ) ) {
			return false;
		}
        if ( !$table->delete( $pk ) ) {
			return false;
		}
		$dispatcher->trigger( 'onContentAfterDelete', array( self::$context, $table ) );

        return true;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected
	
	// _core
	protected function _core( $data, &$config = array(), $pk = 0 )
	{
		if ( ! $config['id'] ) {
			$config['id']	=	parent::g_onCCK_Storage_LocationPrepareStore();
		}
		
		// Init
		$table	=	self::_getTable( $pk );
		$isNew	=	( $pk > 0 ) ? false : true;
		self::_initTable( $table, $data, $config );
		
		// Check Error
		if ( self::$error === true ) {
			$config['error']	=	true;

			return false;
		}
		
		// Prepare
		if ( is_array( $data ) ) {
			$table->bind( $data );
		}
		$table->check();
		self::_completeTable( $table, $data, $config );
		
		// Check if the user is allowed to reply
		if ( $isNew ) {
			if ( $table->folder_id ) {
				$parent	=	JCckDatabase::loadObject( 'SELECT user_id_from, user_id_to FROM #__messages WHERE message_id ='.(int)$table->folder_id );

				if ( is_object( $parent ) ) {
					if ( !( $table->user_id_from == $parent->user_id_from || $table->user_id_from == $parent->user_id_to ) ) {
						JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_NO_ACCESS' ), 'error' );
						
						$config['error']	=	true;

						return false;
					}
				}
			}	
		}

		// Store
		$table->state			=	1;
		if ( !$table->store() ) {
			JFactory::getApplication()->enqueueMessage( $table->getError(), 'error' );

			if ( $isNew ) {
				parent::g_onCCK_Storage_LocationRollback( $config['id'] );
			}
			$config['error']	=	true;

			return false;
		}
		if ( $isNew ) {
			if ( !$table->folder_id ) {
				$table->folder_id	=	$table->message_id;
				$table->store();
			}
		}
		
		self::$pk	=	$table->{self::$key};
		if ( !$config['pk'] ) {
			$config['pk']	=	self::$pk;
		}
		
		/* TODO#SEBLOD: sendMail cf model? */
		
		$config['author']	=	$table->user_id_from;
		
		parent::g_onCCK_Storage_LocationStore( $data, self::$table, self::$pk, $config );
	}
	
	// _getTable
	protected static function _getTable( $pk = 0 )
	{
		$table	=	JTable::getInstance( 'Message', 'MessagesTable' );
		
		if ( $pk > 0 ) {
			$table->load( $pk );
		}
		
		return $table;
	}
	
	// _initTable
	protected function _initTable( &$table, &$data, &$config, $force = false )
	{
		if ( ! $table->{self::$key} ) {
			parent::g_initTable( $table, ( ( isset( $config['params'] ) ) ? $config['params'] : $this->params->toArray() ), $force );
		}
	}
	
	// _completeTable
	protected function _completeTable( &$table, &$data, &$config )
	{
		if ( ! (int)$table->date_time ) {
			$table->date_time	=	JFactory::getDate()->toSql();
		}
		
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
	public static function parseRoute( &$vars, $segments, $n, $config )
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

	// _updateState
	public static function _updateState( $pk, $state )
	{
		JCckDatabase::execute( 'UPDATE '.self::$table.' SET state = '.$state.' WHERE message_id = '.(int)$pk );
	}

	// checkIn
	public static function checkIn( $pk = 0 )
	{
		return true;
	}
}
?>