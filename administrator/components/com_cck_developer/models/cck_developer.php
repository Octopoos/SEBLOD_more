<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'cck.base.install.export' );

// Model
class CCK_DeveloperModelCCK_Developer extends JModelLegacy
{
	// createPlugin
	public function createPlugin( $params )
	{
		set_time_limit( 0 );
		
		$app		=	JFactory::getApplication();
		$name		=	$app->input->post->getString( 'name', '' );
		$name2		=	strtoupper( $name );
		$type		=	$app->input->post->getString( 'type', '' );
		$title		=	ucwords( str_replace( '_', ' ', $name ) );
		$class		=	str_replace( ' ', '_', $title );
		$group2		=	$app->input->post->getString( 'group_'.$type, 'UNCATEGORIZED' );
		$group2		=	$group2 ? $group2 : 'UNCATEGORIZED';
		$group		=	JText::_( 'COM_CCK_GROUP_EN_'.$group2 );
		// --------
		$paramsXML	=	array( 'author'=>$params->get( 'author', 'Octopoos' ),
							   'author_email'=>$params->get( 'author_email', 'contact@seblod.com' ),
							   'author_url'=>$params->get( 'author_url', 'https://www.seblod.com' ),
							   'copyright'=>$params->get( 'copyright', 'Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.' ),
							   'license'=>$params->get( 'license', 'GNU General Public License version 2 or later.' ),
							   'creation_date'=>$app->input->post->getString( 'creation_date', '' ),
							   'description'=>$app->input->post->getString( 'description', 'SEBLOD 3.x - www.seblod.com // by Octopoos - www.octopoos.com' ),
							   'version'=>$app->input->post->getString( 'version', '1.0.0' ) );
		// --------
		$config			=	JFactory::getConfig();
		$tmp_path		=	$config->get( 'tmp_path' );
		$tmp_dir 		=	uniqid( 'cck_' );
		$path 			= 	$tmp_path.'/'.$tmp_dir;
		$root			=	$path.'/plg_'.$type;
		//
		$output			=	$params->get( 'output', 0 );
		$output_path	=	$params->get( 'output_path', '' );
		$output_path	=	( $output == 2 && $output_path != '' && JFolder::exists( $output_path ) ) ? $output_path : ( ( $output == 1 && $output_path != ''  ) ? JPATH_SITE.'/'.$output_path : $tmp_path );
		
		if ( $name && $type ) {
			JFolder::copy( JPATH_ADMINISTRATOR.'/components/com_cck_developer/install/development/plugins/plg_'.$type, $root );
			
			$files	=	JFolder::files( $root, '.', true, true );
			if ( count( $files ) ) {
				foreach ( $files as $file ) {
					$buffer		=	file_get_contents( $file );
					$buffer		=	str_replace( array( '%class%', '%group%', '%GROUP%', '%name%', '%NAME%', '%title%' ), array( $class, $group, $group2, $name, $name2, $title ), $buffer );
					if ( JFile::getExt( $file ) == 'xml' ) {
						$buffer	=	str_replace( array( '%author%', '%author_email%', '%author_url%', '%copyright%', '%license%', '%creation_date%', '%description%', '%version%' ), $paramsXML, $buffer );
					}
					JFile::write( $file, $buffer );
					if ( strpos( $file, '%name%' ) !== false ) {
						JFile::move( $file, str_replace( '%name%', $name, $file ) );
					}
				}
			}
			
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/pclzip/pclzip.lib.php';
			$zip		=	$path.'/'.$name.'.zip';
			$archive	=	new PclZip( $zip );
			if ( $archive->create( $root, PCLZIP_OPT_REMOVE_PATH, $root ) == 0 ) {
				return false;
			}
			
			if ( JFile::exists( $zip ) ) {
				$file	=	$output_path.'/plg_'.$type.'_'.$name.'.zip';
				JFile::move( $zip, $file );
				
				if ( JFolder::exists( $path ) ) {
					JFolder::delete( $path );
				}
				
				return ( $output > 0 ) ? true : $file;
			}
		}
		
		return false;
	}
}
?>