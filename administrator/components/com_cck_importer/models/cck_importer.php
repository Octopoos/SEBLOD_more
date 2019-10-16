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
class CCK_ImporterModelCCK_Importer extends JModelLegacy
{
	// importFromFile
	public function importFromFile( &$session, $params )
	{
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 0 );
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/field.php';
		
		require_once JPATH_SITE.'/plugins/cck_storage/standard/standard.php';
		require_once JPATH_SITE.'/plugins/cck_storage/custom/custom.php';

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

		// -------- -------- -------- -------- -------- -------- -------- -------- // Process

		$config		=	array(
							'auto_inc'=>0,
							'component'=>'com_cck_importer',
							'key'=>$session['options']['key'],
							'key_column'=>@$session['options']['key_column'],
							'key_table'=>@$session['options']['key_table'],
							'params'=>$session['params'],
							'table'=>( ( isset( $session['options']['table'] ) && $session['options']['table'] ) ? $session['options']['table'] : '' ),
							'tasks'=>array(),
							'type'=>$session['options']['content_type']
						);

		Helper_Import::preProcess( $session ); /* TODO: move to storage plug-in */

		Helper_Import::process( $session, 0, $session['csv']['total'], 0, $config );

		Helper_Import::postProcess( $session ); /* TODO: move to object plug-in */

		return $this->importFromFile_end( $session, $params, $config );
	}

	// importFromFile_end
	public function importFromFile_end( &$session, $params, $config )
	{
		ob_start();
		JCck::callFunc_Array( 'plgCCK_Storage_Location'.$session['location'].'_Importer', 'onCCK_Storage_LocationAfterImports', array( array(), &$config ) );
		ob_get_clean();

		if ( $config['auto_inc'] > 0 && $config['auto_inc'] >= $session['table_inc'] ) {
			$config['auto_inc']++;
			JCckDatabase::execute( 'ALTER TABLE '.$session['table'].' AUTO_INCREMENT='.$config['auto_inc'] );
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

	// prepareFile
	public function prepareFile( $params )
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';

		$app				=	JFactory::getApplication();
		$file				=	Helper_Import::uploadFile( JRequest::getVar( 'upload_file', null, 'files', 'array' ) );
		$session			=	array();
		$session['options']	=	$app->input->get( 'options', array(), 'array' );
		
		if ( $file === false ) {
			return false;
		}

		require_once JPATH_SITE.'/plugins/cck_storage_location/'.$session['options']['storage_location'].'/classes/importer.php';

		$allowed_columns	=	JCck::callFunc( 'plgCCK_Storage_Location'.$session['options']['storage_location'].'_Importer', 'getColumnsToImport' );
		$csv				=	Helper_Import::getCsv( $file, $session['options']['separator'], $allowed_columns, $params->get( 'csv_length', 1000 ), $params->get( 'encoding_list', "7bit,8bit,ASCII,BASE64,HTML-ENTITIES,\r\nISO-8859-1,ISO-8859-2,ISO-8859-3,ISO-8859-4,ISO-8859-5,ISO-8859-6,ISO-8859-7,\r\nISO-8859-8,ISO-8859-9,ISO-8859-10,ISO-8859-13,ISO-8859-14,ISO-8859-15,\r\nUTF-32,UTF-32BE,UTF-32LE,UTF-16,UTF-16BE,UTF-16LE,UTF-7,UTF7-IMAP,UTF-8,\r\nWindows-1252,Windows-1254" ) );

		// Prepare
		$columns			=	array(
									'`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT'
								);
		$table_name			=	'#__cck_tmp_'.uniqid();

		foreach ( $csv['columns'] as $column ) {
			$columns[]		=	'`'.$column.'` TEXT NOT NULL';
		}

		JCckDatabase::execute( 'CREATE TABLE IF NOT EXISTS '.$table_name.' ( '.implode( ',', $columns ).', PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;' );

		if ( $csv['total'] ) {
			$db		=	JFactory::getDbo();
			$query	=	$db->getQuery( true );

			$query->insert( $table_name )
				  ->columns( $db->quoteName( $csv['columns'] ) );

			foreach ( $csv['rows'] as $row ) {
				$query->values( '"'.implode( '","', $row ).'"' );
			}

			$db->setQuery( $query );
			$db->execute();
		}

		return true;
	}
}
?>