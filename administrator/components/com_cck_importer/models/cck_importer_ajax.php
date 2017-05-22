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
class CCK_ImporterModelCCK_Importer_Ajax extends JModelLegacy
{
	// debugAjax
	function debugAjax( $text = '' )
	{
		/*
		$table				=	JCckTable::getInstance( '#__cck_abc' );
		$table->snippet		=	$text;
		$table->datetime	=	JFactory::getDate()->toSql();
		$table->store();
		*/
	}

	// importFromFile_preflight
	function importFromFile_preflight( $session )
	{
		set_time_limit( 60 );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		
		require_once JPATH_SITE.'/plugins/cck_storage/standard/standard.php';
		require_once JPATH_SITE.'/plugins/cck_storage/custom/custom.php';
		require_once JPATH_SITE.'/plugins/cck_storage/json/json.php';

		if ( isset( $session['location'] ) && $session['location'] ) {
			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$session['location'].'/classes/importer.php';
		} else {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
		}
	}

	// importFromFile_start
	function importFromFile_start( &$session, $params )
	{
		// $this->debugAjax( 'start' );
		// #

		$this->importFromFile_preflight( $session );

		$app						=	JFactory::getApplication();
		$file						=	Helper_Import::uploadFile( JRequest::getVar( 'upload_file', NULL, 'files', 'array' ) );
		$session['options']			=	$app->input->get( 'options', array(), 'array' );
		$session['options']['key']	=	( isset( $session['options']['key'] ) ) ? $session['options']['key'] : '';

		if ( @$session['options']['csv_length'] == '' ) {
			$session['options']['csv_length']		=	1000;
		}
		if ( @$session['options']['encoding_list'] == '' ) {
			$session['options']['encoding_list']	=	"7bit,8bit,ASCII,BASE64,HTML-ENTITIES,\r\nISO-8859-1,ISO-8859-2,ISO-8859-3,ISO-8859-4,ISO-8859-5,ISO-8859-6,ISO-8859-7,\r\nISO-8859-8,ISO-8859-9,ISO-8859-10,ISO-8859-13,ISO-8859-14,ISO-8859-15,\r\nUTF-32,UTF-32BE,UTF-32LE,UTF-16,UTF-16BE,UTF-16LE,UTF-7,UTF7-IMAP,UTF-8,\r\nWindows-1252,Windows-1254";
		}
		if ( $session['options']['key'] == -1 && isset( $session['options']['key_fieldname'] ) && $session['options']['key_fieldname'] != '' ) {
			$key_field							=	JCckDatabase::loadObject( 'SELECT storage_field, storage_table FROM #__cck_core_fields WHERE name = "'.$session['options']['key_fieldname'].'"' );
			$session['options']['key']			=	$session['options']['key_fieldname'];
			$session['options']['key_column']	=	$key_field->storage_field;
			$session['options']['key_table']	=	$key_field->storage_table;
		}
		$session['values']			=	$app->input->get( 'values', array(), 'array' );
		$session['location']		=	$session['options']['storage_location'];
		$session['storage']			=	$session['options']['storage'];

		if ( $file === false ) {
			return false;
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$session['location'].'/classes/importer.php';

		$allowed_columns	=	JCck::callFunc( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'getColumnsToImport' );

		// CSV Process
		$i							=	0;
		$session['content']			=	array();
		$session['fieldnames']		=	array();
		$session['fieldnames_info']	=	array();

		$data				=	file_get_contents( $file );
		$encodings 			=	$session['options']['encoding_list'];

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
			while ( ( $data = fgetcsv( $handle, $session['options']['csv_length'], $session['options']['separator'] ) ) !== false ) {
				if ( $i == 0 ) {
					$session['fieldnames']	=	$data;
				} else {
					$session['content'][]	=	$data;
				}
				$i++;
			}
			fclose( $handle );
		}
		
		if ( count( $session['fieldnames'] ) ) {
			foreach ( $session['fieldnames'] as $k=>$fieldname ) {
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
				$session['fieldnames'][$k]	=	$fieldname;

				// Set More Info
				if ( is_object( $info ) ) {
					$session['fieldnames_info'][$fieldname]	=	$info->toArray();
				}
			}
		}
		
		$session['count']		=	count( $session['fieldnames'] );
		$session['total']		=	count( $session['content'] );
		$session['header']		=	str_putcsv( $session['fieldnames'], $session['options']['separator'] )."\n";
		$session['header2']		=	str_putcsv( array( 0=>'id', 1=>'name', 2=>'username', 3=>'email' ), $session['options']['separator'] )."\n";
		
		$properties				=	array( 'custom', 'table' );
		$properties				=	JCck::callFunc( 'plgCCK_Storage_Location'.$session['location'], 'getStaticProperties', $properties );
		$session['custom']		=	$properties['custom'];
		$session['table']		=	$properties['table'];
		$session['table_inc']	=	(int)JCckDatabase::loadResult( 'SELECT MAX(id) FROM '.$session['table'] );

		$plugin					=	JPluginHelper::getPlugin( 'cck_storage_location', $session['location'] );
		$params2				=	new JRegistry( $plugin->params );
		$session['params']		=	$params2->toArray();
		if ( count( $session['values'] ) ) {
			foreach ( $session['values'] as $k=>$v ) {
				if ( $v != '' ) {
					$session['params']['base_default-'.$k]	=	$v;
				}
			}
		}
		$session['params']['ordering']			=	( isset( $session['options']['reordering'] ) && $session['options']['reordering'] ) ? -1 : -2;	// todo
		$session['params']['force_password']	=	( isset( $session['options']['force_password'] ) ) ? $session['options']['force_password'] : 0;	// todo
		
		$session['processing']		=	array();
		
		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$session['processing']	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );
		}

		if ( $file && JFile::exists( $file ) ) {
			JFile::delete( $file );
		}

		// -------- -------- -------- !!
		// -------- -------- --------
		if ( $session['options']['content_type'] == -1  ) { // New
			if ( !$session['options']['content_type_new'] ) {
				return false;
			}
			
			$count		=	count( $session['fieldnames'] );
			$fields		=	array();
			$ordering	=	1;
			$type		=	Helper_Import::addContentType( $session['options']['content_type_new'], $session['location'] );
			
			// #__store_form_...
			if( $session['storage'] == 'standard' ) {
				$session['table2']	=	'#__cck_store_form_'.$type->name;
				JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$session['table2'].' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
			}
			
			for ( $i = 0; $i < $count; $i++ ) {
				$fieldname			=	str_replace( ' ', '_', $session['fieldnames'][$i] );
				$session['fieldnames'][$i]		=	$fieldname;  
				$isCore				=	Helper_Import::isCoreStorage_Location( $fieldname, $session['table'] );
				if ( $isCore ) {
					$session['data'][$i]['sto_table']		=	$session['table'];	
					$session['fieldnames2'][$i]['storage']	=	( $fieldname == $session['custom'] )? 'custom': 'standard';
					
					//associer type/fields
					Helper_Import::addTypeFields_Core( $type->id, $fieldname, $ordering, $session['location'] );
					$ordering++;
					
					continue;  
				}
				//custom field exist or not
				$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
				$find		=	JCckDatabase::loadResult( $query );
				$data_type	=	'TEXT';

				if ( isset( $session['fieldnames_info'][$fieldname]['data_type'] ) && $session['fieldnames_info'][$fieldname]['data_type'] != '' ) {
					$data_type	=	$session['fieldnames_info'][$fieldname]['data_type'];
				}
				if ( $find == 0 ) {
					//add field in the table #__store_form_.../or #__jos 
					if ( $session['storage'] == 'standard' ) {
						$session['data'][$i]['sto_table']	= $session['table2'];
						JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldname.' '.$data_type.' NOT NULL' );
					} else { //custom or another
						$session['data'][$i]['sto_table']	= $session['table'];
					}
					$fieldid							=	Helper_Import::addField( $fieldname, $session['data'][$i]['sto_table'], $session['location'], $session['storage'], $session['custom'], $session['fieldnames_info'] );
					$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid ); 
					$session['fieldnames2'][$i]['storage']			=	$storage_obj->storage; 
				} else {
					$query								=	"SELECT  s.id FROM #__cck_core_fields AS s WHERE s.name='$fieldname' ";
					$fieldid							=	JCckDatabase::loadResult( $query );
					$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid ); 
					$session['fieldnames2'][$i]['storage']			=	$storage_obj->storage;
					$session['fieldnames2'][$i]['storage_field']	=	$storage_obj->storage_field;
					$session['fieldnames2'][$i]['storage_table']	=	$storage_obj->storage_table;
					if ( $session['fieldnames2'][$i]['storage'] == 'standard' && !$session['fieldnames2'][$i]['storage_table'] ) {
						JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldid->storage_field.' '.$data_type.' NOT NULL' );
					}
					$session['data'][$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
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
			
			//created the table #__store_form_  	
			$session['table2']	=	'#__cck_store_form_'.$session['options']['content_type'];
			
			JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$session['table2'].' ( id int(11) NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );
			
			$count		=	count( $session['fieldnames'] );
			for ( $i = 0;  $i < $count;  $i++ ) {
				$fieldname	=	str_replace( ' ', '_', $session['fieldnames'][$i] );
				$session['fieldnames'][$i]		=	$fieldname;   
				$isCore				=	Helper_Import::isCoreStorage_Location( $fieldname, $session['table'] );
				if ( $isCore ) {
					$session['data'][$i]['sto_table']		= 	$session['table'];	
					$session['fieldnames2'][$i]['storage']	=	( $fieldname == $session['custom'] ) ? 'custom': 'standard';
					continue;  
				} else {
					$data_type	=	'TEXT';
					$query		=	'DESCRIBE '.$session['table'].' '.$fieldname;  
					$yes		=	JCckDatabase::loadResult( $query ); 

					if ( isset( $session['fieldnames_info'][$fieldname]['data_type'] ) && $session['fieldnames_info'][$fieldname]['data_type'] != '' ) {
						$data_type	=	$session['fieldnames_info'][$fieldname]['data_type'];
					}
					if ( !$yes ) {   
						//if field doesn't exists
						$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
						$find		=	JCckDatabase::loadResult( $query );
						if ( $find == 0 ) {  //field doesn't exist
							if ( $session['storage'] == 'standard' ) {
								$session['data'][$i]['sto_table']		=	$session['table2'];
								JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldname.' '.$data_type.' NOT NULL' );
								$session['fieldnames2'][$i]['storage']	=	$session['storage'];
							} else { 
								$session['data'][$i]['sto_table']		=	$session['table'];
								$session['fieldnames2'][$i]['storage']	=	$session['storage'];
							}
							//created the field
							$fieldid	=	Helper_Import::addField( $fieldname, $session['data'][$i]['sto_table'], $session['location'], $session['storage'], $session['custom'], $session['fieldnames_info'] );
														
							//association content type /field
							$query	=	"SELECT MAX(ordering) FROM #__cck_core_type_field WHERE typeid = $ctypeid ";
							
							$ordering++;
							Helper_Import::addTypeFields( $ctypeid, $fieldid, $ordering );
						} else {
							$query								=	"SELECT s.id, s.storage_field FROM #__cck_core_fields AS s WHERE s.name='$fieldname' ";
							$fieldid							=	JCckDatabase::loadObject( $query );
							$storage_obj						=	Helper_Import::findFieldStorageById( $fieldid->id ); 
							$session['fieldnames2'][$i]['storage']			=	$storage_obj->storage; 
							$session['fieldnames2'][$i]['storage_field']	=	$storage_obj->storage_field;
							$session['fieldnames2'][$i]['storage_table']	=	$storage_obj->storage_table;
							if ( $session['fieldnames2'][$i]['storage'] == 'standard' && !$session['fieldnames2'][$i]['storage_table'] ) {
								JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldid->storage_field.' '.$data_type.' NOT NULL' );
							}
							$session['data'][$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
						}
					}
				}
			}
		}
		// -------- -------- --------
		// -------- -------- -------- !!

		$query	=	'SELECT COUNT(a.fieldid)'
				.	' FROM #__cck_core_type_field AS a'
				.	' LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
				.	' WHERE b.name="'.(string)$session['options']['content_type'].'" AND a.client="intro"';
		$session['options']['content_type_fields_intro']	=	JCckDatabase::loadResult( $query );

		for ( $i = 0; $i < $session['count']; $i++ ) {
			if ( $session['fieldnames2'][$i]['storage'] != 'standard' ) {
				if ( $session['fieldnames'][$i] == $session['custom'] ) {
					$session['fieldnames3'][$i]						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE storage_location = "'.$session['location'].'" AND storage_field2 = "'.$session['custom'].'"' );
					$session['fieldnames3'][$i]->storage_field2		=	$session['fieldnames3'][$i]->storage_field;
				} else {
					$session['fieldnames3'][$i]						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE name = "'.(string)$session['fieldnames'][$i].'"' );
					if ( !$session['fieldnames3'][$i]->storage_field2 ) {
						$session['fieldnames3'][$i]->storage_field2	=	$session['fieldnames3'][$i]->name;
					}
				}
			}
		}
	}

	// importFromFile_continue
	function importFromFile_process( &$session, $start, $end )
	{
		// $this->debugAjax('process from '.$start.' to '.$end);
		// #

		$this->importFromFile_preflight( $session );

		$config		=	array(
						'auto_inc'=>$session['auto_inc'],
						'component'=>'com_cck_importer',
						'key'=>$session['options']['key'],
						'key_column'=>@$session['options']['key_column'],
						'key_table'=>@$session['options']['key_table'],
						'params'=>$session['params'],
						'tasks'=>$session['tasks'],
						'type'=>$session['options']['content_type'],
						'type_fields_intro'=>$session['options']['content_type_fields_intro']
					);

		ob_start();
		$j		=	$start;
		$end	=	( $end > $session['total'] ) ? $session['total'] : $end;
		for ( ; $start < $end; $start++ ) { 
			$c					=	$session['content'][$start];
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
			for ( $i = 0; $i < $session['count']; $i++ ) {
				if ( $session['options']['force_utf8'] ) {
					$search		=	array( chr(145), chr(146), chr(147), chr(148), chr(149), chr(150), chr(151), chr(153) );
					$replace	=	array( '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bull;', '&ndash;', '&mdash;', '&#153;' );
					$c[$i]		=	str_replace( $search, $replace, $c[$i] );

					if ( mb_detect_encoding( $c[$i]) != 'UTF-8' ) {
						$c[$i]	=	utf8_encode( $c[$i] );
					}
				}
				if ( $session['data'][$i]['sto_table'] == $session['table'] ) {
					if ( $session['fieldnames2'][$i]['storage'] == 'standard') {
						if ( $session['fieldnames'][$i] != '' ) {
							$config['storages'][$session['table']][$session['fieldnames'][$i]]	=  	$c[$i];
						}
					} else {
						$field_storage	=	$session['fieldnames2'][$i]['storage'];
						if ( $session['custom'] != '' ) {
							$config['storages'][$session['table']][$session['custom']]	.=	JCck::callFunc_Array( 'plgCCK_Storage'.$field_storage, 'onCCK_StoragePrepareImport', array( $session['fieldnames3'][$i], $c[$i], &$config ) );	
						}
					}
					$config['storages'][$session['table']]['_']				=	new stdClass;
					$config['storages'][$session['table']]['_']->table			=	$session['table'];
					$config['storages'][$session['table']]['_']->location		=	$session['location'];
				} else {
					$storage_table											=	( isset( $session['fieldnames2'][$i]['storage_table'] ) ) ? $session['fieldnames2'][$i]['storage_table'] : $session['table2'];
					if ( $storage_table == '' ) {
						$storage_table										=	'none';
					}
					if ( !isset( $config['storages'][$storage_table]['_'] ) ) {
						$config['storages'][$storage_table]['_']			= 	new stdClass;
						$config['storages'][$storage_table]['_']->table		=	$storage_table;
						$config['storages'][$storage_table]['_']->location	=	$session['location'];
					}
					if ( !isset( $more[$storage_table] ) ) {
						$more[$storage_table]								=	'';
					}
					$storage_field											=	( isset( $session['fieldnames2'][$i]['storage_field'] ) ) ? $session['fieldnames2'][$i]['storage_field'] : $session['fieldnames'][$i];
					$config['storages'][$storage_table][$storage_field]		=	$c[$i];

					$fields[$session['fieldnames'][$i]]						=	new stdClass;
					$fields[$session['fieldnames'][$i]]->value				=	$c[$i];
				}
			}
			
			if ( count( $more ) ) {
				foreach ( $more as $t=>$m ) {
					// Get :: More Key
					if ( $config['key_table'] && $config['key_column'] ) {
						if ( isset( $config['storages'][$t][$config['key']] ) && $config['storages'][$t][$config['key']] != '' ) {
							$pk		=	JCckDatabase::loadResult( 'SELECT id FROM '.$config['key_table'].' WHERE '.$config['key_column'].' = "'.$config['storages'][$t][$config['key']].'"' );
							break;
						}
					}
				}
			}
			
			// BeforeImport
			$event	=	'onCckPreBeforeImport';
			if ( isset( $session['processing'][$event] ) ) {
				foreach ( $session['processing'][$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			/*
			TODO: beforeImport
			*/

			$event	=	'onCckPostBeforeImport';
			if ( isset( $session['processing'][$event] ) ) {
				foreach ( $session['processing'][$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new JRegistry( $p->options );

						include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
					}
				}
			}

			// Import Core
			JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationImport', array( $config['storages'][$session['table']], &$config, $pk ) );

			// Log
			$session['log'][$config['log']]++;
			$session['log']['all'][]				=	$config['pk'];
			$session['log_buffer'][$config['log']]	.=	str_putcsv( $session['content'][$j], $session['options']['separator'] )."\n";
			
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
				if ( isset( $session['processing'][$event] ) ) {
					foreach ( $session['processing'][$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}

				/*
				TODO: afterImport
				*/

				$event	=	'onCckPostAfterImport';
				if ( isset( $session['processing'][$event] ) ) {
					foreach ( $session['processing'][$event] as $p ) {
						if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
							$options	=	new JRegistry( $p->options );

							include JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config */
						}
					}
				}
			}

			unset( $session['content'][$start] );
			$j++;
		}
		$session['tasks']	=	$config['tasks'];
		if ( $config['auto_inc'] > $session['table_inc'] ) {
			$session['auto_inc']	=	$config['auto_inc'];
		}
		ob_get_clean();
		
		// ******** MOVE BEFORE 2.5.0 ********
		if ( $session['location'] == 'joomla_user' ) {
			$regression	=	$session['options']['diff'];
			if ( $regression ) {
				$session['log']['all']	=	implode( ',', $session['log']['all'] );
				$items					=	JCckDatabase::loadColumn( 'SELECT id FROM '.$session['table'].' WHERE id NOT IN ('.$session['log']['all'].')' );
				$regression				=	array();
				require_once JPATH_LIBRARIES.'/joomla/user/user.php';
				foreach ( $items as $item ) {
					$table			=	JUser::getInstance( $item );
					if ( !$table->authorise( 'core.admin' ) ) {
						$table->block	=	1;
						$table->save();
						$regression[$session['log']['regressed']]			=	new stdClass;
						$regression[$session['log']['regressed']]->name	=	$table->name;
						$session['log']['regressed']++;
						$temp								=	array( 'id'=>$table->id,
																	   'name'=>$table->name,
																	   'username'=>$table->username,
																	   'email'=>$table->email );
						$session['log_buffer']['regressed']	.=	str_putcsv( $temp, $session['options']['separator'] )."\n";
						unset( $temp );
					}
				}
			}
		}
		// ******** MOVE BEFORE 2.5.0 ********
	}

	// importFromFile_end
	function importFromFile_end( &$session, $params )
	{
		// $this->debugAjax('end');
		// #

		// After Import
		$config		=	array(
							'auto_inc'=>$session['auto_inc'],
							'component'=>'com_cck_importer',
							'count'=>$session['count'],
							'table'=>$session['table'],
							'table_inc'=>$session['table_inc'],
							'tasks'=>$session['tasks'],
							'total'=>$session['total'],
							'type'=>$session['options']['content_type']
					);
		
		ob_start();
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationAfterImport', array( array(), &$config ) );
		ob_get_clean();
		
		if ( $session['auto_inc'] > 0 && $session['auto_inc'] >= $session['table_inc'] ) {
			$session['auto_inc']++;
			JCckDatabase::execute( 'ALTER TABLE '.$session['table'].' AUTO_INCREMENT='.$session['auto_inc'] );
		}

		// Output
		$output	=	Helper_Output::init( $session['location'], 'csv', $params );

		// Log
		foreach ( $session['log_buffer'] as $k=>$v ) {
			if ( $k != '_' && $v ) {
				$buffer	=	$v;
				if ( $k == 'regressed' ) {
					$buffer	=	chr(0xEF).chr(0xBB).chr(0xBF).$session['header2'].$v;
				} else {
					$buffer	=	$session['header'].$v;
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