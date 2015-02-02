<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );

// Model
class CCK_ImporterModelCCK_Importer extends JModelLegacy
{
	// importFromFile
	function importFromFile( $params, &$log )
	{
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 0 );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		
		require_once JPATH_SITE.'/plugins/cck_storage/standard/standard.php';
		require_once JPATH_SITE.'/plugins/cck_storage/custom/custom.php';
		
		// -------- -------- -------- -------- -------- -------- -------- -------- // Init
		
		$app				=	JFactory::getApplication();
		$options			=	$app->input->get( 'options', array(), 'array' );
		if ( $options['key'] == -1 && isset( $options['key_fieldname'] ) && $options['key_fieldname'] != '' ) {
			$key_field		=	JCckDatabase::loadObject( 'SELECT storage_field, storage_table FROM #__cck_core_fields WHERE name = "'.$options['key_fieldname'].'"' );
			$options['key']			=	$options['key_fieldname'];
			$options['key_column']	=	$key_field->storage_field;
			$options['key_table']	=	$key_field->storage_table;
		}
		$values				=	$app->input->get( 'values', array(), 'array' );
		$file				=	Helper_Import::uploadFile( JRequest::getVar( 'upload_file', NULL, 'files', 'array' ) );
		$storage			=	$options['storage'];
		$storage_location	=	$options['storage_location'];
		$output				=	Helper_Output::init( $options['storage_location'], 'csv', $params );
		$plugin_location	=	JPluginHelper::getPlugin( 'cck_storage_location', $storage_location );
		
		if ( $file === false ) {
			return false;
		}
		
		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$options['storage_location'].'/classes/importer.php';

		// CSV Process
		$row 		=	0;
		$content	=	array();
		$fieldnames	=	array();
		if ( ( $handle = fopen( $file, "r" ) ) !== false ) {
			while ( ( $data = fgetcsv( $handle, $params->get( 'csv_length', 1000 ), $options['separator'] ) ) !== false ) {
				if ( $row == 0 ) {
					$fieldnames	=	$data;   
				} else {
					$content[]	=	$data;  
				}
				$row++;
			}
			fclose( $handle );
		}
		if ( $fieldnames[0] != '' ) {
			$fieldnames[0]	=	preg_replace( '/[^A-Za-z0-9_#\(\)\|]/', '', $fieldnames[0] );
		}
		if ( count( $fieldnames ) ) {
			foreach ( $fieldnames as $k=>$fieldname ) {
				$fieldnames[$k]	=	strtolower( $fieldname );
			}
		}
		$total		=	count( $content );
		
		// -------- -------- -------- -------- -------- -------- -------- -------- //
		
		$header					=	str_putcsv( $fieldnames, $options['separator'] )."\n";
		$header2				=	str_putcsv( array( 0=>'id', 1=>'name', 2=>'username', 3=>'email' ), $options['separator'] )."\n";
		$log_buffer				=	array( 'cancelled'=>'', 'created'=>'', 'regressed'=>'', 'updated'=>'' );
		
		$properties				=	array( 'custom', 'table' );
		$properties				=	JCck::callFunc( 'plgCCK_Storage_Location'.$storage_location, 'getStaticProperties', $properties );
		$sto_location_custom	=	$properties['custom'];
		$sto_table				=	$properties['table'];
		
		// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

		if ( $options['content_type'] == -1  ) { // New
			if ( !$options['content_type_new'] ) {
				return false;
			}
			
			$count		=	count( $fieldnames );
			$data1		=	array();
			$ordering	=	1;
			$type		=	Helper_Import::addContentType( $options['content_type_new'] );
			
			// #__store_form_...
			if( $storage == 'standard' ) {
				$table	=	'#__cck_store_form_'.$type->name;
				JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;' );
			}
			
			for ( $i = 0; $i < $count; $i++ ) {
				$fieldname			=	str_replace( ' ', '_', $fieldnames[$i] );
				$fieldnames[$i]		=	$fieldname;  
				$isCore				=	Helper_Import::isCoreStorage_Location( $fieldname, $sto_table );
				if ( $isCore ) {
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
				
				if ( $find == 0 ) {
					//add field in the table #__store_form_.../or #__jos 
					if ( $storage == 'standard' ) {
						$data1[$i]['sto_table']	= $table;
						JCckDatabase::execute( 'ALTER IGNORE TABLE '.$table.' ADD '.$fieldname.' TEXT NOT NULL' );
					} else { //custom or another
						$data1[$i]['sto_table']	= $sto_table;
					}
					$fieldid							=	Helper_Import::addField( $fieldname , $data1[$i]['sto_table'] , $storage_location, $storage, $sto_location_custom);
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
						JCckDatabase::execute( 'ALTER IGNORE TABLE '.$table.' ADD '.$fieldid->storage_field.' TEXT NOT NULL' );
					}
					$data1[$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
				}
				
				//addTypeField
				Helper_Import::addTypeFields( $type->id, $fieldid, $ordering );
				$ordering++; 
			}
			$options['content_type']	=	$type->name;
			
		} else {	// Existing
			if ( !$options['content_type'] ) {
				return false;
			}
			
			//get the CT's id
			$namect		=	$options['content_type'];
			$query		=	'SELECT v.id FROM #__cck_core_types AS v WHERE v.name = "'.$namect.'"'; 
			$ctypeid	=	JCckDatabase::loadResult( $query );
			
			$query		=	'SELECT MAX(ordering) FROM #__cck_core_type_field WHERE typeid = "'.$ctypeid.'" ';
			$ordering	=	JCckDatabase::loadResult( $query );
			
			//manipulation for fieldnames
			$data1	=	array();
			
			//created the table #__store_form_  	
			$table	=	'#__cck_store_form_'.$options['content_type'];
			
			JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table.' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;' );
			
			$count		=	count( $fieldnames );	
			for ( $i = 0;  $i < $count;  $i++ ) {
				$fieldname	=	str_replace( ' ', '_', $fieldnames[$i] );
				$fieldnames[$i]		=	$fieldname;   
				$isCore				=	Helper_Import::isCoreStorage_Location( $fieldname, $sto_table );
				if ( $isCore ) {
					$data1[$i]['sto_table']		= 	$sto_table;	
					$fieldnames1[$i]['storage']	=	( $fieldname == $sto_location_custom ) ? 'custom': 'standard';
					continue;  
				} else {
					$query		=	"DESCRIBE $sto_table $fieldname";  
					$yes		=	JCckDatabase::loadResult( $query ); 
					if ( !$yes ) {   
						//if field doesn't exists
						$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
						$find		=	JCckDatabase::loadResult( $query );
						if ( $find == 0 ) {  //field doesn't exist
							if ( $storage == 'standard' ) {
								$data1[$i]['sto_table']		=	$table;
								JCckDatabase::execute( 'ALTER IGNORE TABLE '.$table.' ADD '.$fieldname.' TEXT NOT NULL' );
								$fieldnames1[$i]['storage']	=	$storage;
							} else { 
								$data1[$i]['sto_table']		=	$sto_table;
								$fieldnames1[$i]['storage']	=	$storage;
							}
							//created the field
							$fieldid	=	Helper_Import::addField( $fieldname , $data1[$i]['sto_table'] , $storage_location, $storage, $sto_location_custom );
														
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
								JCckDatabase::execute( 'ALTER IGNORE TABLE '.$table.' ADD '.$fieldid->storage_field.' TEXT NOT NULL' );
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
									'key'=>$options['key'],
									'key_column'=>@$options['key_column'],
									'key_table'=>@$options['key_table'],
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
		$config['params']['ordering']		=	( isset( $options['reordering'] ) && $options['reordering'] ) ? -1 : -2;	// todo
		$config['params']['force_password']	=	( isset( $options['force_password'] ) ) ? $options['force_password'] : 0;	// todo

		$config['type']		=	$options['content_type'];
		$inc				=	(int)JCckDatabase::loadResult( 'SELECT MAX(id) FROM '.$sto_table );
		
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
			$core				=	array();
			$more				=	array();

			// Prepare
			for ( $i = 0; $i < $count; $i++ ) {
				$c[$i]	=	( $options['force_utf8'] ) ? utf8_encode( $c[$i] ) : $c[$i];
				if ( $data1[$i]['sto_table'] == $sto_table ) {
					if ( $fieldnames1[$i]['storage'] == 'standard') {
						$core[$fieldnames[$i]]	=	$c[$i];
					}
					$field_storage	=	$fieldnames1[$i]['storage'];
					if ( $field_storage != 'standard' ) {
						$core[$sto_location_custom]	.=	JCck::callFunc_Array( 'plgCCK_Storage'.$field_storage, 'onCCK_StoragePrepareImport', array( $obj[$i], $c[$i], &$config ) );
					}
					$core['_']			=	new stdClass;
					$core['_']->table	=	$sto_table;
					$core['_']->location	=	$storage_location;
					if ( $fieldnames1[$i]['storage'] == 'standard' ) {
						$config['storages'][$sto_table][$fieldnames[$i]]	=  	$core[$fieldnames[$i]];
					}
					$config['storages'][$sto_table][$sto_location_custom]	=	$core[$sto_location_custom];
				} else {
					$storage_table											=	( isset( $fieldnames1[$i]['storage_table'] ) ) ? $fieldnames1[$i]['storage_table'] : $table;
					if ( !isset( $more[$storage_table] ) ) {
						$more[$storage_table]['_']							= 	new stdClass;
						$more[$storage_table]['_']->table					=	$storage_table;
						$more[$storage_table]['_']->location				=	$storage_location;
					}
					$more[$storage_table][$fieldnames[$i]]					=	$c[$i];
					$storage_field											=	( isset( $fieldnames1[$i]['storage_field'] ) ) ? $fieldnames1[$i]['storage_field'] : $fieldnames[$i];
					$config['storages'][$storage_table][$storage_field]		=	$more[$storage_table][$fieldnames[$i]];
				}
			}
			
			// Get :: More Key
			if ( $config['key_table'] && $config['key_column'] ) {
				if ( count( $more ) ) {
					foreach ( $more as $m ) {
						if ( isset( $m[$config['key']] ) && $m[$config['key']] != '' ) {
							$pk		=	JCckDatabase::loadResult( 'SELECT id FROM '.$config['key_table'].' WHERE '.$config['key_column'].' = "'.$m[$config['key']].'"' );
							break;
						}
					}
				}
			}
			
			// Process :: Core
			JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationImport', array( $core, &$config, $pk ) );

			// Process :: Log
			$log[$config['log']]++;
			$log['all'][]				=	$config['pk'];
			$log_buffer[$config['log']]	.=	str_putcsv( $content[$j], $options['separator'] )."\n";
			
			// Process :: More
			if ( !$config['error'] ) {
				if ( count( $more ) ) {
					foreach ( $more as $m ) {
						JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationImport', array( $m, &$config, $config['pk'] ) );
					}
				}
			}
			
			unset( $c[$i] );
			$j++;
		}
		
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$storage_location.'_Importer', 'onCCK_Storage_LocationAfterImport', array( array(), &$config ) );

		if ( $config['auto_inc'] > 0 && $config['auto_inc'] >= $inc ) {
			$config['auto_inc']++;
			JCckDatabase::execute( 'ALTER TABLE '.$sto_table.' AUTO_INCREMENT='.$config['auto_inc'] );
		}
		
		if ( $file && JFile::exists( $file ) ) {
			JFile::delete( $file );
		}
		
		// ******** MOVE BEFORE 2.5.0 ********
		if ( $storage_location == 'joomla_user' ) {
			$regression	=	$options['diff'];
			if ( $regression ) {
				$log['all']	=	implode( ',', $log['all'] );
				$items		=	JCckDatabase::loadColumn( 'SELECT id FROM '.$sto_table.' WHERE id NOT IN ('.$log['all'].')' );
				$regression	=	array();
				require_once JPATH_LIBRARIES.'/joomla/user/user.php';
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
						$log_buffer['regressed']	.=	str_putcsv( $temp, $options['separator'] )."\n";
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