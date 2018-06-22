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
class CCK_ImporterModelCCK_Importer_Todo extends JModelLegacy
{
	// importFromFile
	public function importFromFile( $params, &$log )
	{
		// -------- -------- -------- !!
		// -------- -------- --------
		if ( $session['options']['content_type'] == -1  ) { // New
			if ( !$session['options']['content_type_new'] ) {
				return false;
			}
			
			$count		=	count( $session['fieldnames'] );
			$ordering	=	1;
			$type		=	Helper_Import::addContentType( $session['options']['content_type_new'] );
			
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
				
				if ( $find == 0 ) {
					//add field in the table #__store_form_.../or #__jos 
					if ( $session['storage'] == 'standard' ) {
						$session['data'][$i]['sto_table']	= $session['table2'];
						JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldname.' TEXT NOT NULL' );
					} else { //custom or another
						$session['data'][$i]['sto_table']	= $session['table'];
					}
					$fieldid							=	Helper_Import::addField( $fieldname , $session['data'][$i]['sto_table'] , $session['location'], $session['storage'], $session['custom']);
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
						JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldid->storage_field.' TEXT NOT NULL' );
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
					$query		=	'DESCRIBE '.$session['table'].' '.$fieldname;  
					$yes		=	JCckDatabase::loadResult( $query ); 
					if ( !$yes ) {   
						//if field doesn't exists
						$query		= 	"SELECT COUNT(*) FROM #__cck_core_fields AS s WHERE s.name = '$fieldname' ";
						$find		=	JCckDatabase::loadResult( $query );
						if ( $find == 0 ) {  //field doesn't exist
							if ( $session['storage'] == 'standard' ) {
								$session['data'][$i]['sto_table']		=	$session['table2'];
								JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldname.' TEXT NOT NULL' );
								$session['fieldnames2'][$i]['storage']	=	$session['storage'];
							} else { 
								$session['data'][$i]['sto_table']		=	$session['table'];
								$session['fieldnames2'][$i]['storage']	=	$session['storage'];
							}
							//created the field
							$fieldid	=	Helper_Import::addField( $fieldname , $session['data'][$i]['sto_table'] , $session['location'], $session['storage'], $session['custom'] );
														
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
								JCckDatabase::execute( 'ALTER TABLE '.$session['table2'].' ADD '.$fieldid->storage_field.' TEXT NOT NULL' );
							}
							$session['data'][$i]['sto_table']	=	Helper_Import::findFieldById( $fieldid->id )->storage_table;
						}
					}
				}
			}
		}
		// -------- -------- --------
		// -------- -------- -------- !!
	}
}
?>