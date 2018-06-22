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
	//addContentType
	public static function addContentType( $title, $storage_location )
	{
		require JPATH_ADMINISTRATOR.'/components/com_cck/tables/type.php';
		require JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_workshop.php';
		
		$style						=	Helper_Workshop::getDefaultStyle( 'seb_one' );
		
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
		$row					=	JTable::getInstance( 'Field', 'CCK_Table' );
		$row->title				=	ucwords( str_replace( '_', ' ', $name ) );   
		$row->name				=	$name;
		$row->folder			=	1;
		$row->type				=	'text';
		$row->label				=	ucfirst( str_replace( '_', ' ', $name ) );
		$row->storage			=	strtolower( $sto );
		$row->storage_location	=	$sto_location;
		$row->storage_table		=	$sto_table;
		$row->storage_field		=	( $row->storage != 'standard' ) ? $custom : $name; 
		$row->display			=	3;
		$row->published			=	1;
		
		if ( isset( $overrides[$name] ) && count( $overrides[$name] ) ) {
			foreach ( $overrides[$name] as $k=>$v ) {
				if ( property_exists( $row, $k ) ) {
					$row->$k	=	$v;
				}
			}
		}

		$row->store() ;
		
		return $row->id;
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
	
	// findFieldStorage
	public static function findFieldStorage( $fieldname )
	{
		$query 		=	'SELECT s.storage FROM #__cck_core_fields AS s WHERE s.name = "'.$fieldname.'"';
		$storage	=	JCckDatabase::loadResult( $query );
		
		return $storage; 
	}
	
	// findFieldStorageById
	public static function findFieldStorageById( $id )
	{
		$query 		=	'SELECT s.storage, s.storage_table, s.storage_field FROM #__cck_core_fields AS s WHERE s.id = '.(int)$id;
		$storage	=	JCckDatabase::loadObject( $query );
		
		return $storage; 
	}
	
	// findFieldById
	public static function findFieldById( $id )
	{
		$query 		=	'SELECT s.* FROM #__cck_core_fields AS s WHERE s.id = '.(int)$id; //#
		$field		=	JCckDatabase::loadObject( $query );
		
		return $field; 
	}
}
?>