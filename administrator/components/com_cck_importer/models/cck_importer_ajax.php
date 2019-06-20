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

jimport( 'joomla.filesystem.file' );

// Model
class CCK_ImporterModelCCK_Importer_Ajax extends JModelLegacy
{
	// importFromFile_preflight
	protected function importFromFile_preflight( $session )
	{
		set_time_limit( 60 );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		
		require_once JPATH_SITE.'/plugins/cck_storage/standard/standard.php';
		require_once JPATH_SITE.'/plugins/cck_storage/custom/custom.php';
		require_once JPATH_SITE.'/plugins/cck_storage/json/json.php';

		if ( isset( $session['location'] ) && $session['location'] ) {
			require_once JPATH_SITE.'/plugins/cck_storage_location/'.$session['location'].'/classes/importer.php';
		}

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
	}

	// importFromFile_init
	public function importFromFile_map( &$session, $map_data )
	{
		$map_data	=	json_decode( $map_data, true );

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';

		foreach ( $map_data as $column_name=>$column ) {
			$idx		=	array_search( $column_name, $session['csv']['columns'] );
			
			if ( $idx !== false ) {
				$field_name	=	$column['map'];
				
				if ( $field_name == 'clear' ) {
					unset( $session['csv']['columns'][$idx] );
					unset( $session['fields'][$idx] );
				} elseif ( $field_name != '' ) {
					$session['csv']['columns'][$idx]	=	$field_name;
					unset( $session['fields'][$idx] );

					if ( !isset( $session['fields'][$field_name] ) ) {
						$session['fields'][$field_name]	=	Helper_Import::findField( $field_name );
					}
				} else {
					$field_name	=	$column_name;
				}

				// Input
				if ( isset( $session['fields'][$field_name] ) ) {
					$session['fields'][$field_name]->prepare_input	=	$column['input'];
				} elseif ( isset( $session['fields'][$idx] ) ) {
					$session['fields'][$idx]->prepare_input			=	$column['input'];
				}
			}
		}
	}

	// importFromFile_start
	public function importFromFile_start( &$session, $params )
	{
		$this->importFromFile_preflight( $session );

		Helper_Import::initSession( $session, $params );

		// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

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

				if ( isset( $session['table_columns'][$fieldname] ) ) {
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
		} else {
			if ( !$session['options']['content_type'] ) {
				return false;
			}
			
			Helper_Import::prepareExisting( $session );	
		}

		// Optimize
		$query	=	'SELECT COUNT(a.fieldid)'
				.	' FROM #__cck_core_type_field AS a'
				.	' LEFT JOIN #__cck_core_types AS b ON b.id = a.typeid'
				.	' WHERE b.name="'.(string)$session['options']['content_type'].'" AND a.client="intro"';

		$session['options']['content_type_fields_intro']	=	JCckDatabase::loadResult( $query );

		// Non-standard fields
		// for ( $i = 0; $i < $session['count']; $i++ ) {
		// 	if ( $session['fieldnames2'][$i]['storage'] != 'standard' ) {
		// 		if ( $session['fieldnames'][$i] == $session['custom'] ) {
		// 			$session['fieldnames3'][$i]						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE storage_location = "'.$session['location'].'" AND storage_field2 = "'.$session['custom'].'"' );
		// 			$session['fieldnames3'][$i]->storage_field2		=	$session['fieldnames3'][$i]->storage_field;
		// 		} else {
		// 			$session['fieldnames3'][$i]						=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE name = "'.(string)$session['fieldnames'][$i].'"' );
		// 			if ( !$session['fieldnames3'][$i]->storage_field2 ) {
		// 				$session['fieldnames3'][$i]->storage_field2	=	$session['fieldnames3'][$i]->name;
		// 			}
		// 		}
		// 	}
		// }
	}

	// importFromFile_process
	public function importFromFile_process( &$session, $start, $end )
	{
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
		$current	=	$start;
		$end		=	( $end > $session['csv']['total'] ) ? $session['csv']['total'] : $end;

		ob_start();
		Helper_Import::process( $session, $start, $end, $current, $config );

		$session['tasks']	=	$config['tasks'];

		if ( $config['auto_inc'] > $session['table_inc'] ) {
			$session['auto_inc']	=	$config['auto_inc'];
		}

		ob_get_clean();
		
		// ******** MOVE BEFORE 2.5.0 ********
		// if ( $session['location'] == 'joomla_user' ) {
		// 	$regression	=	$session['options']['diff'];
		// 	if ( $regression ) {
		// 		$session['log']['all']	=	implode( ',', $session['log']['all'] );
		// 		$items					=	JCckDatabase::loadColumn( 'SELECT id FROM '.$session['table'].' WHERE id NOT IN ('.$session['log']['all'].')' );
		// 		$regression				=	array();
		// 		JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );
		// 		foreach ( $items as $item ) {
		// 			$table			=	JUser::getInstance( $item );
		// 			if ( !$table->authorise( 'core.admin' ) ) {
		// 				$table->block	=	1;
		// 				$table->save();
		// 				$regression[$session['log']['regressed']]			=	new stdClass;
		// 				$regression[$session['log']['regressed']]->name	=	$table->name;
		// 				$session['log']['regressed']++;
		// 				$temp								=	array( 'id'=>$table->id,
		// 															   'name'=>$table->name,
		// 															   'username'=>$table->username,
		// 															   'email'=>$table->email );
		// 				$session['log_buffer']['regressed']	.=	str_putcsv( $temp, $session['options']['separator'] )."\n";
		// 				unset( $temp );
		// 			}
		// 		}
		// 	}
		// }
		// ******** MOVE BEFORE 2.5.0 ********
	}

	// importFromFile_end
	public function importFromFile_end( &$session, $params )
	{
		$config		=	array(
							'auto_inc'=>$session['auto_inc'],
							'component'=>'com_cck_importer',
							'count'=>$session['csv']['count'],
							'table'=>$session['table'],
							'table_inc'=>$session['table_inc'],
							'tasks'=>$session['tasks'],
							'total'=>$session['csv']['total'],
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
		foreach ( $session['log']['buffer'] as $k=>$v ) {
			if ( $k != '_' && $v ) {
				$buffer	=	$v;
				if ( $k == 'regressed' ) {
					$buffer	=	chr(0xEF).chr(0xBB).chr(0xBF).$session['log']['header2'].$v;
				} else {
					$buffer	=	$session['log']['header'].$v;
				}
				JFile::write( $output->root.'/'.$k.'.csv', $buffer );
			}
		}
		
		return Helper_Output::finalize( $output );
	}
}
?>