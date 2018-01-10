<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );

// Model
class CCK_ImporterModelCCK_Importer extends JModelLegacy
{
	// importFromFile
	public function importFromFile( $params, &$log )
	{
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 0 );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		
		require_once JPATH_SITE.'/plugins/cck_storage/standard/standard.php';
		require_once JPATH_SITE.'/plugins/cck_storage/custom/custom.php';
		
		// -------- -------- -------- -------- -------- -------- -------- -------- // Init
		
		$app				=	JFactory::getApplication();
		$session			=	array();
		$session['options']	=	$app->input->get( 'options', array(), 'array' );
		
		if ( $session['options']['key'] == -1 && isset( $session['options']['key_fieldname'] ) && $session['options']['key_fieldname'] != '' ) {
			$key_field		=	JCckDatabase::loadObject( 'SELECT storage_field, storage_table FROM #__cck_core_fields WHERE name = "'.$session['options']['key_fieldname'].'"' );
			$session['options']['key']			=	$session['options']['key_fieldname'];
			$session['options']['key_column']	=	$key_field->storage_field;
			$session['options']['key_table']	=	$key_field->storage_table;
		}
		$values				=	$app->input->get( 'values', array(), 'array' );
		$file				=	Helper_Import::uploadFile( JRequest::getVar( 'upload_file', NULL, 'files', 'array' ) );
		$storage			=	$session['options']['storage'];
		$storage_location	=	$session['options']['storage_location'];
		$output				=	Helper_Output::init( $storage_location, 'csv', $params );
		$plugin_location	=	JPluginHelper::getPlugin( 'cck_storage_location', $storage_location );
		
		if ( $file === false ) {
			return false;
		}
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$storage_location.'/classes/importer.php';

		$allowed_columns	=	JCck::callFunc( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'getColumnsToImport' );
		$core_columns		=	array_flip( $allowed_columns );

		// CSV Process
		$row 				=	0;
		$content			=	array();
		$fieldnames			=	array();
		$fieldnames_info	=	array();

		$data				=	file_get_contents( $file );
		$encodings 			=	$params->get( 'encoding_list', "7bit,8bit,ASCII,BASE64,HTML-ENTITIES,\r\nISO-8859-1,ISO-8859-2,ISO-8859-3,ISO-8859-4,ISO-8859-5,ISO-8859-6,ISO-8859-7,\r\nISO-8859-8,ISO-8859-9,ISO-8859-10,ISO-8859-13,ISO-8859-14,ISO-8859-15,\r\nUTF-32,UTF-32BE,UTF-32LE,UTF-16,UTF-16BE,UTF-16LE,UTF-7,UTF7-IMAP,UTF-8,\r\nWindows-1252,Windows-1254" );

		if ( trim( $encodings ) != '' ) {
			$encodings		=	str_replace( array( "\r\n", "\r", "\n" ), ',', trim( $encodings ) );
			$encodings		=	explode( ',', $encodings );
			$encodings		=	array_diff( $encodings, array( '' ) );
		}
		
		if ( is_array( $encodings ) && count( $encodings ) ) {
			$encoding		=	mb_detect_encoding( $data, $encodings );

			if ( !preg_match( '/./u', $data ) ) {
				$data	=	iconv( $encoding, 'UTF-8', $data );
				JFile::write( $file, $data );
			}
		}

		if ( ( $handle = fopen( $file, "r" ) ) !== false ) {
			while ( ( $data = fgetcsv( $handle, $params->get( 'csv_length', 1000 ), $session['options']['separator'] ) ) !== false ) {
				if ( $row == 0 ) {
					$fieldnames	=	$data;   
				} else {
					$content[]	=	$data;  
				}
				$row++;
			}
			fclose( $handle );
		}

		if ( count( $fieldnames ) ) {
			foreach ( $fieldnames as $k=>$fieldname ) {
				$info	=	null;
				$pos	=	strpos( $fieldname, '{' );
				
				// Get More Info
				if ( $pos !== false ) {
					$info		=	substr( $fieldname, $pos );
					$info		=	new JRegistry( $info );
					$fieldname	=	substr( $fieldname, 0, $pos );
				}

				// Fix CSV issue
				if ( $k == 0 ) {
					$fieldname	=	preg_replace( '/[^A-Za-z0-9_#\(\)\|]/', '', $fieldname );
				}

				// Alter Case (when allowed)
				if ( !in_array( $fieldname, $allowed_columns ) ) {
					$fieldname	=	strtolower( $fieldname );
				}
				$fieldnames[$k]	=	$fieldname;

				// Set More Info
				if ( is_object( $info ) ) {
					$fieldnames_info[$fieldname]	=	$info->toArray();
				}
			}
		}
		$total		=	count( $content );
		
		// -------- -------- -------- -------- -------- -------- -------- -------- //
		
		$header					=	str_putcsv( $fieldnames, $session['options']['separator'] )."\n";
		$header2				=	str_putcsv( array( 0=>'id', 1=>'name', 2=>'username', 3=>'email' ), $session['options']['separator'] )."\n";
		$log_buffer				=	array( 'cancelled'=>'', 'created'=>'', 'regressed'=>'', 'updated'=>'' );
		
		$properties				=	array( 'custom', 'table' );
		$properties				=	JCck::callFunc( 'plgCCK_Storage_Location'.$storage_location, 'getStaticProperties', $properties );
		$sto_location_custom	=	$properties['custom'];
		$sto_table				=	$properties['table'];
		
		// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

		if ( $session['options']['content_type'] == -1  ) { // New
			if ( !$session['options']['content_type_new'] ) {
				return false;
			}
			
			$count		=	count( $fieldnames );
			$data1		=	array();
			$ordering	=	1;
			$type		=	Helper_Import::addContentType( $session['options']['content_type_new'], $storage_location );
			
			// #__store_form_...
			if( $storage == 'standard' ) {
				$table	=	'#__cck_store_form_'.$type->name;
				JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
			}
			
			for ( $i = 0; $i < $count; $i++ ) {
				$fieldname			=	str_replace( ' ', '_', $fieldnames[$i] );
				$fieldnames[$i]		=	$fieldname;

				if ( isset( $core_columns[$fieldname] ) ) {
					$data1[$i]['sto_table']		=	$sto_table;	
					$fieldnames1[$i]['storage']	=	( $fieldname == $sto_location_custom )? 'custom': 'standard';
					
					//associer type/fields
					Helper_Import::addTypeFields_Core( $type->id, $fieldname, $ordering, $storage_location );
					$ordering++;
					
					continue;  
				}
				//custom field exist or not
				$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
				$find		=	JCckDatabase::loadResult( $query );
				$data_type	=	'TEXT';

				if ( isset( $fieldnames_info[$fieldname]['data_type'] ) && $fieldnames_info[$fieldname]['data_type'] != '' ) {
					$data_type	=	$fieldnames_info[$fieldname]['data_type'];
				}
				if ( $find == 0 ) {
					//add field in the table #__store_form_.../or #__jos 
					if ( $storage == 'standard' ) {
						$data1[$i]['sto_table']	= $table;
						JCckDatabase::execute( 'ALTER TABLE '.$table.' ADD '.$fieldname.' '.$data_type.' NOT NULL' );
					} else { //custom or another
						$data1[$i]['sto_table']	= $sto_table;
					}
					$fieldid							=	Helper_Import::addField( $fieldname, $data1[$i]['sto_table'], $storage_location, $storage, $sto_location_custom, $fieldnames_info );
					$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid ); 
					$fieldnames1[$i]['storage']			=	$storage_obj->storage; 
				} else {
					$query								=	"SELECT  s.id FROM #__cck_core_fields AS s WHERE s.name='$fieldname' ";
					$fieldid							=	JCckDatabase::loadResult( $query );
					$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid ); 
					$fieldnames1[$i]['storage']			=	$storage_obj->storage;
					$fieldnames1[$i]['storage_field']	=	$storage_obj->storage_field;
					$fieldnames1[$i]['storage_table']	=	$storage_obj->storage_table;
					if ( $fieldnames1[$i]['storage'] == 'standard' && !$fieldnames1[$i]['storage_table'] ) {
						JCckDatabase::execute( 'ALTER TABLE '.$table.' ADD '.$fieldid->storage_field.' '.$data_type.' NOT NULL' );
					}
					$data1[$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
				}
				
				//addTypeField
				Helper_Import::addTypeFields( $type->id, $fieldid, $ordering );
				$ordering++; 
			}
			$session['options']['content_type']	=	$type->name;
			
		} else {	// Existing
			if ( !$session['options']['content_type'] ) {
				return false;
			}
			
			//get the CT's id
			$namect		=	$session['options']['content_type'];
			$query		=	'SELECT v.id FROM #__cck_core_types AS v WHERE v.name = "'.$namect.'"'; 
			$ctypeid	=	JCckDatabase::loadResult( $query );
			
			$query		=	'SELECT MAX(ordering) FROM #__cck_core_type_field WHERE typeid = "'.$ctypeid.'" ';
			$ordering	=	JCckDatabase::loadResult( $query );
			
			//manipulation for fieldnames
			$data1	=	array();
			
			//created the table #__store_form_  	
			$table	=	'#__cck_store_form_'.$session['options']['content_type'];
			
			JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
			
			$count		=	count( $fieldnames );	
			for ( $i = 0;  $i < $count;  $i++ ) {
				$fieldname	=	str_replace( ' ', '_', $fieldnames[$i] );
				$fieldnames[$i]		=	$fieldname;   

				if ( isset( $core_columns[$fieldname] ) ) {
					$data1[$i]['sto_table']		= 	$sto_table;	
					$fieldnames1[$i]['storage']	=	( $fieldname == $sto_location_custom ) ? 'custom': 'standard';
					continue;  
				} else {
					$data_type	=	'TEXT';
					$query		=	"DESCRIBE $sto_table $fieldname";
					$yes		=	JCckDatabase::loadResult( $query );

					if ( isset( $fieldnames_info[$fieldname]['data_type'] ) && $fieldnames_info[$fieldname]['data_type'] != '' ) {
						$data_type	=	$fieldnames_info[$fieldname]['data_type'];
					}
					if ( !$yes ) {   
						//if field doesn't exists
						$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
						$find		=	JCckDatabase::loadResult( $query );
						if ( $find == 0 ) {  //field doesn't exist
							if ( $storage == 'standard' ) {
								$data1[$i]['sto_table']		=	$table;
								JCckDatabase::execute( 'ALTER TABLE '.$table.' ADD '.$fieldname.' '.$data_type.' NOT NULL' );
								$fieldnames1[$i]['storage']	=	$storage;
							} else { 
								$data1[$i]['sto_table']		=	$sto_table;
								$fieldnames1[$i]['storage']	=	$storage;
							}
							//created the field
							$fieldid	=	Helper_Import::addField( $fieldname, $data1[$i]['sto_table'], $storage_location, $storage, $sto_location_custom, $fieldnames_info );
														
							//association content type /field
							$query	=	"SELECT MAX(ordering) FROM #__cck_core_type_field WHERE typeid = $ctypeid ";
							
							$ordering++;
							Helper_Import::addTypeFields( $ctypeid, $fieldid, $ordering );
						} else {
							$query								=	"SELECT s.id, s.storage_field FROM #__cck_core_fields AS s WHERE s.name='$fieldname' ";
							$fieldid							=	JCckDatabase::loadObject( $query );
							$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid->id ); 
							$fieldnames1[$i]['storage']			=	$storage_obj->storage; 
							$fieldnames1[$i]['storage_field']	=	$storage_obj->storage_field;
							$fieldnames1[$i]['storage_table']	=	$storage_obj->storage_table;
							if ( $fieldnames1[$i]['storage'] == 'standard' && !$fieldnames1[$i]['storage_table'] ) {
								JCckDatabase::execute( 'ALTER TABLE '.$table.' ADD '.$fieldid->storage_field.' '.$data_type.' NOT NULL' );
							}
							$data1[$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
						}
					}
				}
			}
		}
		
		// -------- -------- -------- -------- -------- -------- -------- -------- // Store

		$config				=	array(
									'auto_inc'=>0,
									'component'=>'com_cck_importer',
									'key'=>$session['options']['key'],
									'key_column'=>@$session['options']['key_column'],
									'key_table'=>@$session['options']['key_table'],
									'tasks'=>array()
								);
		$config['params']	=	new JRegistry( $plugin_location->params );
		$config['params']	=	$config['params']->toArray();
		if ( count( $values ) ) {
			foreach ( $values as $k=>$v ) {
				if ( $v != '' ) {
					$config['params']['base_default-'.$k]	=	$v;
				}
			}
		}
		$config['params']['ordering']		=	( isset( $session['options']['reordering'] ) && $session['options']['reordering'] ) ? -1 : -2;	// todo
		$config['params']['force_password']	=	( isset( $session['options']['force_password'] ) ) ? $session['options']['force_password'] : 0;	// todo

		$config['type']		=	$session['options']['content_type'];
		$inc				=	(int)JCckDatabase::loadResult( 'SELECT MAX(id) FROM '.$sto_table );
		$processing			=	array();
		
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing		=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
		}

		//manipu for field names and contents.
		$count	=	count( $fieldnames );
		for ( $i = 0; $i < $count; $i++ ) {
			$field_storage	=	$fieldnames1[$i]['storage'];
			if ( $field_storage != 'standard' ) {
				if ( $fieldnames[$i] == $sto_location_custom ) {
					$query							=	"SELECT * FROM #__cck_core_fields WHERE storage_location = '$storage_location' and storage_field2 = '$sto_location_custom' ";
					$obj[$i]						=	JCckDatabase::loadObject( $query );
					$obj[$i]->storage_field2		=	$obj[$i]->storage_field;
				} else {
					$query							=	'SELECT * FROM #__cck_core_fields where name = "'.(string)$fieldnames[$i].'"';
					$obj[$i]   						=	JCckDatabase::loadObject( $query );
					if ( !$obj[$i]->storage_field2 ) {
						$obj[$i]->storage_field2	=	$obj[$i]->name;
					}
				}
			}
		}
		$j		=	0;
		$start	=	0;
		$end	=	$total;
		for ( $start = 0; $start < $end; $start++ ) { 
			$c					=	$content[$start];
			$pk					=	0;
			$config['x']		=	$j;
			$config['id']		=	0;
			$config['pk']		=	0;
			$config['log']		=	'';
			$config['error']	=	false;
			$config['isNew']	=	1;
			$fields				=	array();
			$more				=	array();

			// Prepare
			for ( $i = 0; $i < $count; $i++ ) {
				if ( $session['options']['force_utf8'] ) {
					$search		=	array( chr(145), chr(146), chr(147), chr(148), chr(149), chr(150), chr(151), chr(153) );
					$replace	=	array( '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bull;', '&ndash;', '&mdash;', '&#153;' );
					$c[$i]		=	str_replace( $search, $replace, $c[$i] );

					if ( mb_detect_encoding( $c[$i]) != 'UTF-8' ) {
						$c[$i]	=	utf8_encode( $c[$i] );
					}
				}
				if ( $data1[$i]['sto_table'] == $sto_table ) {
					if ( $fieldnames1[$i]['storage'] == 'standard') {
						if ( $fieldnames[$i] != '' ) {
							$config['storages'][$sto_table][$fieldnames[$i]]	=  	$c[$i];
						}
					} else {
						$field_storage	=	$fieldnames1[$i]['storage'];
						if ( $sto_location_custom != '' ) {
							$config['storages'][$sto_table][$sto_location_custom]	.=	JCck::callFunc_Array( 'plgCCK_Storage'.$field_storage, 'onCCK_StoragePrepareImport', array( $obj[$i], $c[$i], &$config ) );
						}
					}
					$config['storages'][$sto_table]['_']				=	new stdClass;
					$config['storages'][$sto_table]['_']->table			=	$sto_table;
					$config['storages'][$sto_table]['_']->location		=	$storage_location;
				} else {
					$storage_table										=	( isset( $fieldnames1[$i]['storage_table'] ) ) ? $fieldnames1[$i]['storage_table'] : $table;
					if ( $storage_table == '' ) {
						$storage_table									=	'none';
					}
					if ( !isset( $config['storages'][$storage_table]['_'] ) ) {
						$config['storages'][$storage_table]['_']			= 	new stdClass;
						$config['storages'][$storage_table]['_']->table		=	$storage_table;
						$config['storages'][$storage_table]['_']->location	=	$storage_location;
					}
					if ( !isset( $more[$storage_table] ) ) {
						$more[$storage_table]								=	'';
					}
					$storage_field											=	( isset( $fieldnames1[$i]['storage_field'] ) ) ? $fieldnames1[$i]['storage_field'] : $fieldnames[$i];
					
					$config['storages'][$storage_table][$storage_field]		=	$c[$i];

					$fields[$fieldnames[$i]]								=	new stdClass;
					$fields[$fieldnames[$i]]->value							=	$c[$i];
				}
			}

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
			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			/* TODO#SEBLOD: beforeImport */

			$event	=	'onCckPostBeforeImport';
			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			// Import Core
			JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationImport', array( $config['storages'][$sto_table], &$config, $pk ) );
			
			// Log
			$log[$config['log']]++;
			$log['all'][]				=	$config['pk'];
			$log_buffer[$config['log']]	.=	str_putcsv( $content[$j], $session['options']['separator'] )."\n";
			
			if ( !$config['error'] ) {
				// Import More
				if ( count( $more ) ) {
					foreach ( $more as $t=>$m ) {
						if ( $config['storages'][$t]['_']->table != 'none' ) {
							JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationImport', array( $config['storages'][$t], &$config, $config['pk'] ) );
						}
					}
				}

				// AfterImport
				$event	=	'onCckPreAfterImport';
				if ( isset( $processing[$event] ) ) {
					foreach ( $processing[$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}

				/* TODO: afterImport */

				$event	=	'onCckPostAfterImport';
				if ( isset( $processing[$event] ) ) {
					foreach ( $processing[$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}				
			}

			unset( $c[$i] );
			$j++;
		}
		
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationAfterImports', array( array(), &$config ) );

		if ( $config['auto_inc'] > 0 && $config['auto_inc'] >= $inc ) {
			$config['auto_inc']++;
			JCckDatabase::execute( 'ALTER TABLE '.$sto_table.' AUTO_INCREMENT='.$config['auto_inc'] );
		}
		
		if ( $file && JFile::exists( $file ) ) {
			JFile::delete( $file );
		}
		
		// ******** MOVE BEFORE 2.5.0 ********
		if ( $storage_location == 'joomla_user' ) {
			$regression	=	$session['options']['diff'];
			if ( $regression ) {
				$log['all']	=	implode( ',', $log['all'] );
				$items		=	JCckDatabase::loadColumn( 'SELECT id FROM '.$sto_table.' WHERE id NOT IN ('.$log['all'].')' );
				$regression	=	array();
				JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );
				foreach ( $items as $item ) {
					$table			=	JUser::getInstance( $item );
					if ( !$table->authorise( 'core.admin' ) ) {
						$table->block	=	1;
						$table->save();
						$regression[$log['regressed']]			=	new stdClass;
						$regression[$log['regressed']]->name	=	$table->name;
						$log['regressed']++;
						$temp						=	array( 'id'=>$table->id,
															   'name'=>$table->name,
															   'username'=>$table->username,
															   'email'=>$table->email );
						$log_buffer['regressed']	.=	str_putcsv( $temp, $session['options']['separator'] )."\n";
						unset( $temp );
					}
				}
			}
		}
		// ******** MOVE BEFORE 2.5.0 ********

		// Log
		foreach ( $log_buffer as $k=>$v ) {
			if ( $k != '_' && $v ) {
				$buffer	=	$v;
				if ( $k == 'regressed' ) {
					$buffer	=	chr(0xEF).chr(0xBB).chr(0xBF).$header2.$v;
				} else {
					$buffer	=	$header.$v;
				}
				JFile::write( $output->root.'/'.$k.'.csv', $buffer );
			}
		}
		
		return Helper_Output::finalize( $output );
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