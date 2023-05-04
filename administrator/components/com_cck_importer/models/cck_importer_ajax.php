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
	public function importFromFile_map( &$session_data, $map_data )
	{
		$map_data	=	json_decode( $map_data, true );

		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';

		foreach ( $map_data as $column_name=>$column ) {
			$idx		=	array_search( $column_name, $session_data['csv']['columns'] );
			
			if ( $idx !== false ) {
				$field_name	=	$column['map'];
				
				if ( $field_name == 'clear' ) {
					unset( $session_data['csv']['columns'][$idx] );
					unset( $session_data['fields'][$idx] );
				} elseif ( $field_name != '' ) {
					$session_data['csv']['columns'][$idx]	=	$field_name;
					unset( $session_data['fields'][$idx] );

					if ( !isset( $session_data['fields'][$field_name] ) ) {
						$session_data['fields'][$field_name]	=	Helper_Import::findField( $field_name );
					}
				} else {
					$field_name	=	$column_name;
				}

				// Input
				if ( isset( $session_data['fields'][$field_name] ) ) {
					$session_data['fields'][$field_name]->prepare_input	=	$column['input'];
				} elseif ( isset( $session_data['fields'][$idx] ) ) {
					$session_data['fields'][$idx]->prepare_input			=	$column['input'];
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
			
			Helper_Import::prepareNew( $session );
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

		Helper_Import::preProcess( $session );
	}

	// importFromFile_process
	public function importFromFile_process( &$session, $start, $end )
	{
		$this->importFromFile_preflight( $session );

		$config		=	array(
						'auto_inc'=>$session['auto_inc'],
						'component'=>'com_cck_importer',
						'glue'=>$session['options']['glue'],
						'key'=>$session['options']['key'],
						'key_column'=>@$session['options']['key_column'],
						'key_table'=>@$session['options']['key_table'],
						'params'=>$session['params'],
						'table'=>$session['table'],
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
		
		Helper_Import::postProcess( $session ); /* TODO: move to object plug-in */
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
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationAfterImports', array( array(), &$config ) );
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