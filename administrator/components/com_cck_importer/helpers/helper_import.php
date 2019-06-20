<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Import 
{
	// addContentType
	public static function addContentType( $title, $storage_location )
	{
		require JPATH_ADMINISTRATOR.'/components/com_cck/tables/type.php';
		require JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_workshop.php';
		
		$style						=	Helper_Workshop::getDefaultStyle();
		
		$table						=	JTable::getInstance( 'Type', 'CCK_Table' );
		$table->title				=	$title;
        $table->folder				=	1;
		$table->template_admin		=	$style->id;
		$table->template_site		=	$style->id;
		$table->template_content	=	$style->id;
		$table->template_intro		=	$style->id;
		$table->published			=	1;
		$table->indexed				=	'none';

		if ( $storage_location == '' ) {
			$storage_location		=	'joomla_article';
		}
		$table->storage_location	=	$storage_location;
		
		$rules	=	array( 'core.create'=>array(),
						   'core.create.max.parent'=>array( '8'=>"0" ),
						   'core.create.max.parent.author'=>array( '8'=>"0" ),
						   'core.create.max.author'=>array( '8'=>"0" ),
						   'core.delete'=>array(),
						   'core.delete.own'=>array(),
						   'core.edit'=>array(),
						   'core.edit.own'=>array() );
		$rules	=	new JAccessRules( $rules );
		$table->setRules( $rules );
		
		$table->check();
		$table->store(); 
		
		return $table;
	}
	
	// addField
	public static function addField( $name , $sto_table, $sto_location, $sto, $custom, $overrides = array() )
	{
		$field						=	JTable::getInstance( 'Field', 'CCK_Table' );
		$field->title				=	ucwords( str_replace( '_', ' ', $name ) );   
		$field->name				=	$name;
		$field->folder				=	1;
		$field->type				=	'text';
		$field->label				=	ucfirst( str_replace( '_', ' ', $name ) );
		$field->storage				=	strtolower( $sto );
		$field->storage_location	=	$sto_location;
		$field->storage_table		=	$sto_table;
		$field->storage_field		=	( $field->storage != 'standard' ) ? $custom : $name; 
		$field->display				=	3;
		$field->published			=	1;
		
		if ( isset( $overrides[$name] ) && count( $overrides[$name] ) ) {
			foreach ( $overrides[$name] as $k=>$v ) {
				if ( property_exists( $field, $k ) ) {
					$field->$k	=	$v;
				}
			}
		}

		$field_obj	=	array(
							'divider'=>'',
							'id'=>0,
							'options'=>'',
							'storage'=>$field->storage,
							'storage_field'=>$field->storage_field,
							'storage_table'=>$field->storage_table,
							'type'=>'text'
						);

		if ( $field->store() ) {
			$field_obj->id	=	$field->id;
		}
		
		return $field_obj;
	}
	
	// addTypeFields
	public static function addTypeFields( $type_id, $field_id, $ordering )
	{
		$client		=	'admin';
		$position	=	'mainbody';
		$access		=	1;
		$values		= 	"( ". "'$type_id'," ."'$field_id',"."'$client',". "'$ordering',". "'$access',"."'$position' )";
		$query		= 	'INSERT INTO #__cck_core_type_field ( typeid, fieldid , client, ordering , access, position )'
		   	     	.	' VALUES ' . $values;
		JCckDatabase::execute( $query );
	}
	
	// addTypeFields_Core
	public static function addTypeFields_Core( $type_id, $field_name, $ordering, $sto_location ) 
	{
		$client		=	'admin';
		$position	=	'mainbody';
		$access		=	1;
		
		$query 		= "SELECT s.id FROM #__cck_core_fields AS s WHERE s.storage_location='$sto_location' and s.storage_field = '$field_name'";
		$field_id	=	JCckDatabase::loadResult( $query );
		
		$values		= 	"(". "'$type_id'," ."'$field_id',"."'$client',". "'$ordering',"."'$access',"."'$position')";
		$query		= 	'INSERT INTO #__cck_core_type_field ( typeid, fieldid , client, ordering , access, position)'
		   	     	.	' VALUES ' . $values;
		JCckDatabase::execute( $query );
	}

	// getCsv
	public static function getCsv( $file, $separator, $allowed_columns, $length, $encoding_list )
	{
		$csv	=	array(
						'columns'=>array(),
						'columns_info'=>array(),
						'count'=>0,
						'rows'=>array(),
						'total'=>0
					);
		$data	=	file_get_contents( $file );
		$i 		=	0;

		if ( trim( $encoding_list ) != '' ) {
			$encoding_list	=	str_replace( array( "\r\n", "\r", "\n" ), ',', trim( $encoding_list ) );
			$encoding_list	=	explode( ',', $encoding_list );
			$encoding_list	=	array_diff( $encoding_list, array( '' ) );
		}
		
		if ( is_array( $encoding_list ) && count( $encoding_list ) ) {
			$encoding		=	mb_detect_encoding( $data, $encoding_list );

			if ( !preg_match( '/./u', $data ) ) {
				$data	=	iconv( $encoding, 'UTF-8', $data );
				
				JFile::write( $file, $data );
			}
		}

		if ( ( $handle = fopen( $file, "r" ) ) !== false ) {
			while ( ( $data = fgetcsv( $handle, $length, $separator ) ) !== false ) {
				if ( $i == 0 ) {
					$csv['columns']	=	$data;   
				} else {
					$csv['rows'][]	=	$data;  
				}
				$i++;
			}
			fclose( $handle );
		}

		$csv['count']	=	count( $csv['columns'] );

		if ( $csv['count'] ) {
			foreach ( $csv['columns'] as $k=>$column ) {
				$info	=	null;
				$pos	=	strpos( $column, '{' );
				
				// Get More Info
				if ( $pos !== false ) {
					$info		=	substr( $column, $pos );
					$info		=	new JRegistry( $info );
					$column	=	substr( $column, 0, $pos );
				}

				// Fix CSV issue
				if ( $k == 0 ) {
					$column	=	preg_replace( '/[^A-Za-z0-9_#\(\)\|]/', '', $column );
				}

				// Alter Case (when allowed)
				if ( !in_array( $column, $allowed_columns ) ) {
					$column	=	strtolower( $column );
				}
				$csv['columns'][$k]	=	$column;

				// Set More Info
				if ( is_object( $info ) ) {
					$csv['columns_info'][$column]	=	$info->toArray();
				}
			}
		}

		$csv['total']	=	count( $csv['rows'] );

		return $csv;
	}

	// getMappingOptions
	public static function getMappingOptions( $session_data )
	{
		$objects	=	array();
		$options	=	array(
							0=>'- '.JText::_( 'COM_CCK_IGNORED' ).' -=clear',
							1=>JText::_( 'COM_CCK_IMPORT_INTO' ).' =optgroup',
							2=>'- '.JText::_( 'COM_CCK_INHERITED' ).' -='
						);

		if ( $session_data['options']['content_type'] ) {
			$query	=	'SELECT DISTINCT a.id, a.name, a.type, a.label, b.label as label2,'
					.	' a.storage, a.storage_location, a.storage_table, a.storage_field, a.storage_field2'
					.	' FROM #__cck_core_fields AS a'
					.	' LEFT JOIN #__cck_core_type_field as b ON b.fieldid = a.id'
					.	' LEFT JOIN #__cck_core_types as c ON c.id = b.typeid'
					.	' WHERE c.name = "'.$session_data['options']['content_type'].'"'
					.	' ORDER BY a.name ASC';

			$fields	=	JCckDatabase::loadObjectList( $query );
			$lang	=	JFactory::getLanguage();
			$tables	=	array();

			foreach ( $fields as $field ) {
				if ( !isset( $tables[$field->storage_table] ) ) {
					$tables[$field->storage_table]	=	JCckDatabase::getTableFullColumns( $field->storage_table );
				}

				$data_type		=	'';
				$field->label	=	( $field->label2 && $field->label2 != 'clear' && $field->label2 != ' ' ) ? $field->label2 : ( ( $field->label ) ? $field->label : $field->title );
				
				if ( trim( $field->label ) ) {
					$key			=	'COM_CCK_' . str_replace( ' ', '_', trim( $field->label ) );

					if ( $lang->hasKey( $key ) ) {
						$field->label	=	JText::_( $key );
					}
				}

				if ( $field->storage == 'standard' && isset( $tables[$field->storage_table][$field->storage_field]->Type )
				  && $tables[$field->storage_table][$field->storage_field]->Type ) {
					$data_type		=	'&nbsp;&nbsp;&mdash;&nbsp;&nbsp;'.strtoupper( str_replace( ' unsigned', '', $tables[$field->storage_table][$field->storage_field]->Type ) );
				}
				
				$objects[]	=	(object)array( 'text'=>$field->label.$data_type, 'value'=>$field->name );
			}

			JCckDevHelper::sortObjectsByProperty( $objects, 'text' );

			foreach ( $objects as $object ) {
				$options[]	=	$object->text.'='.$object->value;
			}
		}

		return implode( '||', $options );
	}
	
	// findField
	public static function findField( $field_name )
	{
		return JCckDatabase::loadObject( 'SELECT id, type, options, divider, storage, storage_table, storage_field FROM #__cck_core_fields WHERE name = "'.$field_name.'"' );
	}

	// findFieldStorage
	public static function findFieldStorage( $fieldname )
	{		
		return JCckDatabase::loadResult( 'SELECT s.storage FROM #__cck_core_fields AS s WHERE s.name = "'.$fieldname.'"' );
	}
	
	// findFieldStorageById
	public static function findFieldStorageById( $id )
	{		
		return JCckDatabase::loadObject( 'SELECT s.storage, s.storage_table, s.storage_field FROM #__cck_core_fields AS s WHERE s.id = '.(int)$id );
	}
	
	// findFieldById
	public static function findFieldById( $id )
	{
		return JCckDatabase::loadObject( 'SELECT s.* FROM #__cck_core_fields AS s WHERE s.id = '.(int)$id ); //#
	}

	// initSession
	public static function initSession( &$session, $params )
	{
		$app	=	JFactory::getApplication();
		$file	=	Helper_Import::uploadFile( JRequest::getVar( 'upload_file', null, 'files', 'array' ) );

		if ( $file === false ) {
			$session['csv']['total']	=	0;

			return false;
		}

		// Init
		$session['options']						=	$app->input->get( 'options', array(), 'array' );
		$session['options']['csv_length']		=	$params->get( 'csv_length', 1000 );
		$session['options']['encoding_list']	=	$params->get( 'encoding_list', "7bit,8bit,ASCII,BASE64,HTML-ENTITIES,\r\nISO-8859-1,ISO-8859-2,ISO-8859-3,ISO-8859-4,ISO-8859-5,ISO-8859-6,ISO-8859-7,\r\nISO-8859-8,ISO-8859-9,ISO-8859-10,ISO-8859-13,ISO-8859-14,ISO-8859-15,\r\nUTF-32,UTF-32BE,UTF-32LE,UTF-16,UTF-16BE,UTF-16LE,UTF-7,UTF7-IMAP,UTF-8,\r\nWindows-1252,Windows-1254" );
		$session['options']['key']				=	( isset( $session['options']['key'] ) ) ? $session['options']['key'] : '';
			
		if ( $session['options']['key'] == -1 && isset( $session['options']['key_fieldname'] ) && $session['options']['key_fieldname'] != '' ) {
			$key_field							=	JCckDatabase::loadObject( 'SELECT storage_field, storage_table FROM #__cck_core_fields WHERE name = "'.$session['options']['key_fieldname'].'"' );
			$session['options']['key']			=	$session['options']['key_fieldname'];
			$session['options']['key_column']	=	$key_field->storage_field;
			$session['options']['key_table']	=	$key_field->storage_table;
		}
		
		$session['location']	=	$session['options']['storage_location'];
		$session['storage']		=	$session['options']['storage'];

		// Init (2)				
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$session['location'].'/classes/importer.php';

		$properties				=	array( 'custom', 'table' );
		$properties				=	JCck::callFunc( 'plgCCK_Storage_Location'.$session['location'], 'getStaticProperties', $properties );
		$session['custom']		=	$properties['custom'];
		$session['table']		=	( isset( $session['options']['table'] ) && $session['options']['table'] ) ? $session['options']['table'] : '';

		if ( $properties['table'] ) {
			$session['table']	=	$properties['table'];
		}

		$allowed_columns			=	JCck::callFunc( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'getColumnsToImport', $session['table'] );
		$session['table_columns']	=	array_flip( $allowed_columns );
		$session['table_inc']		=	(int)JCckDatabase::loadResult( 'SELECT MAX(id) FROM '.$session['table'] );

		// Init (3)		
		$session['csv']				=	Helper_Import::getCsv( $file, $session['options']['separator'], $allowed_columns, $session['options']['csv_length'], $session['options']['encoding_list'] );
		$session['log']['buffer']	=	array( 'cancelled'=>'', 'created'=>'', 'regressed'=>'', 'updated'=>'' );
		$session['log']['count']	=	array( 'cancelled'=>0, 'created'=>0, 'regressed'=>0, 'updated'=>0 );
		$session['log']['header']	=	str_putcsv( $session['csv']['columns'], $session['options']['separator'] )."\n";
		$session['log']['header2']	=	str_putcsv( array( 0=>'id', 1=>'name', 2=>'username', 3=>'email' ), $session['options']['separator'] )."\n";
		$session['log']['pks']		=	array();

		// Init (Params)
		$plg_location		=	JPluginHelper::getPlugin( 'cck_storage_location', $session['location'] );
		$plg_params			=	new JRegistry( $plg_location->params );
		$values				=	$app->input->get( 'values', array(), 'array' );
		
		$session['params']	=	$plg_params->toArray();
		
		if ( count( $values ) ) {
			foreach ( $values as $k=>$v ) {
				if ( $v != '' ) {
					$session['params']['base_default-'.$k]	=	$v;
				}
			}
		}
		$session['params']['ordering']		=	( isset( $session['options']['reordering'] ) && $session['options']['reordering'] ) ? -1 : -2;
		$session['params']['force_password']	=	( isset( $session['options']['force_password'] ) ) ? $session['options']['force_password'] : 0;

		// Init (Processings)
		$session['processings']		=	array();
		
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$session['processings']	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
		}

		// --
		if ( $file && JFile::exists( $file ) ) {
			JFile::delete( $file );
		}
	}

	// prepareExisting
	public static function prepareExisting( &$session )
	{
		$content_type_id	=	JCckDatabase::loadResult( 'SELECT id FROM #__cck_core_types WHERE name = "'.$session['options']['content_type'].'"' );
		$ordering			=	JCckDatabase::loadResult( 'SELECT MAX(ordering) FROM #__cck_core_type_field WHERE typeid = "'.$content_type_id.'"' );
		$session['table2']	=	'#__cck_store_form_'.$session['options']['content_type'];
		
		if ( $session['location'] != 'free' ) {
			JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$session['table2'].' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
		}

		for ( $i = 0; $i < $session['csv']['count']; $i++ ) {
			$fieldname						=	str_replace( ' ', '_', $session['csv']['columns'][$i] );
			$session['csv']['columns'][$i]	=	$fieldname;

			if ( isset( $session['table_columns'][$fieldname] ) ) {
				$session['fields'][$i]		=	(object)array(
													'storage'=>( $fieldname == $session['custom'] ? 'custom': 'standard' ),
													'storage_field'=>$fieldname,
													'storage_table'=>$session['table']
												);
			} else {
				$data_type		=	'TEXT';

				if ( isset( $session['csv']['columns_info'][$fieldname]['data_type'] ) && $session['csv']['columns_info'][$fieldname]['data_type'] != '' ) {
					$data_type	=	$session['csv']['columns_info'][$fieldname]['data_type'];
				}

				if ( !(int)JCckDatabase::loadResult( 'SELECT COUNT(id) FROM #__cck_core_fields WHERE name = "'.$fieldname.'"' ) ) {
					if ( isset( $session['options']['workflow'] ) && $session['options']['workflow'] ) {
						// OK
					} else {
						if ( $session['storage'] == 'standard' ) {
							JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldname.' '.$data_type.' NOT NULL' );
						}
						
						$session['fields'][$fieldname]	=	Helper_Import::addField( $fieldname, ( $session['storage'] == 'standard' ? $session['table2'] : $session['table'] ), $session['location'], $session['storage'], $session['custom'], $session['csv']['columns_info'] );

						Helper_Import::addTypeFields( $content_type_id, $session['fields'][$i]->id, ++$ordering );
					}
				} else {
					$session['fields'][$fieldname]	=	Helper_Import::findField( $fieldname );

					if ( $session['fields'][$fieldname]->storage == 'standard' && !$session['fields'][$fieldname]->storage_table ) {
						JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$session['fields'][$fieldname]->storage_field.' '.$data_type.' NOT NULL' );
					}
				}
			}
		}
	}

	// prepareNew
	public static function prepareNew( &$session )
	{
	}

	// process
	public static function process( &$session, $start, $end, $current, &$config )
	{
		JPluginHelper::importPlugin( 'cck_field' );

		$dispatcher	=	JEventDispatcher::getInstance();
		$fields		=	$session['fields'];

		for ( ; $start < $end; $start++ ) {
			$config['error']			=	false;
			$config['id']				=	0;
			$config['isNew']			=	1;
			$config['log']				=	'';
			$config['pk']				=	0;
			$config['x']				=	$current;
			$more						=	array();
			$pk							=	0;
			$row						=	$session['csv']['rows'][$start];

			// Prepare
			for ( $i = 0; $i < $session['csv']['count']; $i++ ) {
				$config['prepare_input']	=	(int)$session['options']['prepare_input'];
				$idx						=	isset( $fields[$session['csv']['columns'][$i]] ) ? $session['csv']['columns'][$i] : $i;
				$isCore						=	false;

				if ( $session['options']['force_utf8'] ) {
					$search		=	array( chr(145), chr(146), chr(147), chr(148), chr(149), chr(150), chr(151), chr(153) );
					$replace	=	array( '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bull;', '&ndash;', '&mdash;', '&#153;' );
					$row[$i]	=	str_replace( $search, $replace, $row[$i] );

					if ( mb_detect_encoding( $row[$i]) != 'UTF-8' ) {
						$row[$i]	=	utf8_encode( $row[$i] );
					}
				}
				
				$fields[$idx]->value	=	'';
				
				if ( isset( $fields[$idx]->prepare_input ) ) {
					$config['prepare_input']	=	$fields[$idx]->prepare_input;
				}
				if ( $config['prepare_input'] && isset( $fields[$idx]->type ) && $fields[$idx]->type ) {
					$dispatcher->trigger( 'onCCK_FieldPrepareImport', array( &$fields[$idx], $row[$i], &$config ) );
				} else {
					$fields[$idx]->value	=	$row[$i];
				}
				
				if ( $fields[$idx]->storage_table == $session['table'] ) {
					if ( $fields[$idx]->storage == 'standard') {
						$config['storages'][$session['table']][$fields[$idx]->storage_field]	=  	$fields[$idx]->value;
					} else {
						if ( $session['custom'] != '' ) {
							$config['storages'][$session['table']][$session['custom']]		.=	JCck::callFunc_Array( 'plgCCK_Storage'.$fields[$idx]->storage, 'onCCK_StoragePrepareImport', array( $obj[$i], $fields[$idx]->value, &$config ) );
						}
					}
					$config['storages'][$session['table']]['_']				=	new stdClass;
					$config['storages'][$session['table']]['_']->table			=	$session['table'];
					$config['storages'][$session['table']]['_']->location		=	$session['location'];
				} else {
					$storage_table										=	( isset( $fields[$idx]->storage_table ) ) ? $fields[$idx]->storage_table : $table;
					if ( $storage_table == '' ) {
						$storage_table									=	'none';
					}
					if ( !isset( $config['storages'][$storage_table]['_'] ) ) {
						$config['storages'][$storage_table]['_']			= 	new stdClass;
						$config['storages'][$storage_table]['_']->table		=	$storage_table;
						$config['storages'][$storage_table]['_']->location	=	$session['location'];
					}
					if ( !isset( $more[$storage_table] ) ) {
						$more[$storage_table]								=	'';
					}
					$storage_field											=	( isset( $fields[$idx]->storage_field ) ) ? $fields[$idx]->storage_field : $session['csv']['columns'][$i];
					
					$config['storages'][$storage_table][$storage_field]		=	$fields[$idx]->value;	
				}
			}

			// -------- --------- -------- //
		
			if ( count( $more ) ) {
				foreach ( $more as $t=>$m ) {
					// Get :: More Key		
					if ( $config['key_table'] && $config['key_column'] ) {
						if ( isset( $config['storages'][$t][$config['key']] ) && $config['storages'][$t][$config['key']] != '' ) {
							$pk		=	(int)JCckDatabase::loadResult( 'SELECT id FROM '.$config['key_table'].' WHERE '.$config['key_column'].' = "'.$config['storages'][$t][$config['key']].'"' );
							break;
						}
					}
				}
			}
			
			// BeforeImport
			$event	=	'onCckPreBeforeImport';
			if ( isset( $session['processings'][$event] ) ) {
				foreach ( $session['processings'][$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			/* TODO#SEBLOD: beforeImport */

			$event	=	'onCckPostBeforeImport';
			if ( isset( $session['processings'][$event] ) ) {
				foreach ( $session['processings'][$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			// Import Core
			JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationImport', array( $config['storages'][$session['table']], &$config, $pk ) );

			// Log
			$session['log']['count'][$config['log']]++;		
			$session['log']['buffer'][$config['log']]	.=	str_putcsv( $session['csv']['rows'][$current], $session['options']['separator'] )."\n";
			$session['log']['pks'][]					=	$config['pk'];

			if ( !$config['error'] ) {
				// Import More
				if ( count( $more ) ) {
					foreach ( $more as $t=>$m ) {
						if ( $config['storages'][$t]['_']->table != 'none' ) {
							JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationImport', array( $config['storages'][$t], &$config, $config['pk'] ) );
						}
					}
				}

				// AfterImport
				$event	=	'onCckPreAfterImport';
				if ( isset( $session['processings'][$event] ) ) {
					foreach ( $session['processings'][$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}

				/* TODO: afterImport */

				$event	=	'onCckPostAfterImport';
				if ( isset( $session['processings'][$event] ) ) {
					foreach ( $session['processings'][$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}
			}

			unset( $session['csv']['rows'][$start] );
			$current++;
		}
	}

	// uploadFile
	public static function uploadFile( $file )
	{
		$tempFolder			=	JFactory::getConfig()->get( 'tmp_path' );
		$fileName 			=	JFile::makeSafe( $file['name'] );
		$src				=	$file['tmp_name'];
		$dest				=	$tempFolder.'/'.$fileName;
		$ext				=	strtolower( JFile::getExt( $fileName ) );
		if ( !( $ext == 'csv' || $ext == 'txt' ) ) {
			return false;
		}
		if ( ! JFile::upload( $src, $dest ) ) {
			return false;
		}
		
		return $dest;
	}
}

// str_putcsv
if ( !function_exists( 'str_putcsv' ) ) {
	function str_putcsv( $input, $delimiter = ',', $enclosure = '"' )
	{
		$fp	=	fopen( 'php://temp', 'r+' );
		
		fputcsv( $fp, $input, $delimiter, $enclosure );
		rewind( $fp );
		$data	=	fread($fp, 1048576);
		fclose( $fp );
		
		return rtrim( $data, "\n" );
	}
}
?>