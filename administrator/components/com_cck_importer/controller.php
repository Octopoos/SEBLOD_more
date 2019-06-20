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

// Controller
class CCK_ImporterController extends JControllerLegacy
{
	protected $default_view	=	'cck_importer';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		$app	=	JFactory::getApplication();
		$id		=	$app->input->getInt( 'id' );
		$layout	=	$app->input->get( 'layout', 'default' );
		$view	=	$app->input->get( 'view', $this->default_view );
		
		if ( !( $layout == 'edit' || $layout == 'edit2' ) ) {
			Helper_Admin::addSubmenu( $this->default_view, $view );
		}
		
		parent::display();
		
		return $this;
	}
	
	// importFromFile
	public function importFromFile()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
				
		$model			=	$this->getModel( 'cck_importer' );
		$params			=	JComponentHelper::getParams( 'com_cck_importer' );
		$output			=	$params->get( 'output', 1 );
		$session_data	=	array();
		
		if ( $file = $model->importFromFile( $session_data, $params ) ) {
			$file			=	JCckDevHelper::getRelativePath( $file, false );
			if ( $output > 0 ) {
				$msg		=	JText::_( 'COM_CCK_SUCCESSFULLY_IMPORTED' );
				if ( $session_data['log']['count']['regressed'] > 0 ) {
					$msg	.=	'&nbsp;&nbsp;&nbsp;&nbsp;'.JText::sprintf( 'COM_CCK_IMPORTER_LOG2', $session_data['log']['count']['created'], $session_data['log']['count']['updated'], $session_data['log']['count']['cancelled'], $session_data['log']['count']['regressed'] );
				} else {
					$msg	.=	'&nbsp;&nbsp;&nbsp;&nbsp;'.JText::sprintf( 'COM_CCK_IMPORTER_LOG', $session_data['log']['count']['created'], $session_data['log']['count']['updated'], $session_data['log']['count']['cancelled'] );
				}
				$msg		.=	' <a href="'.JUri::base().'index.php?option=com_cck&task=download&file='.$file.'" style="color:#000000;">( '.JText::_( 'COM_CCK_LOG' ).' )</a>';
				$this->setRedirect( CCK_LINK, $msg, 'message' );
			} else {
				$this->setRedirect( JUri::base().'index.php?option=com_cck&task=download&file='.$file );
			}
		} else {
			$this->setRedirect( CCK_LINK, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// importFromFileAjax
	public function importFromFileAjax()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app			=	JFactory::getApplication();
		$model			=	$this->getModel( 'cck_importer_ajax' );
		$session		=	JFactory::getSession();
		$session_id		=	'cck_importer_batch';
		$session_data	=	$session->get( $session_id );

		if ( !$session_data ) {
			$params			=	JComponentHelper::getParams( 'com_cck_importer' );
			$session_data	=	array(
									'auto_inc'=>0,
									'tasks'=>array()
								);
			
			$model->importFromFile_start( $session_data, $params );

			if ( $session_data['csv']['total'] > 0 ) {
				$session->set( $session_id, $session_data );
				$this->setRedirect( CCK_LINK.'&do='.$session_data['csv']['total'] );
			} else {
				$this->setRedirect( CCK_LINK, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
			}
		} else {
			$map_data	=	$app->input->post->getString( 'map_data', '' );
			$start		=	$app->input->getInt( 'start', 0 );
			$end		=	$app->input->getInt( 'end', 0 );
			$return		=	'';

			if ( $map_data ) {
				$model->importFromFile_map( $session_data, $map_data );
			}

			$model->importFromFile_process( $session_data, $start, $end );
			$session->set( $session_id, $session_data );

			if ( $end >= $session_data['csv']['total'] ) {
				$params	=	JComponentHelper::getParams( 'com_cck_importer' );
				$output	=	$params->get( 'output', 1 );
				
				if ( $file = $model->importFromFile_end( $session_data, $params ) ) {
					$file			=	JCckDevHelper::getRelativePath( $file, false );
					if ( $output > 0 ) {
						$msg		=	JText::_( 'COM_CCK_SUCCESSFULLY_IMPORTED' );
						if ( $session_data['log']['count']['regressed'] > 0 ) {
							$msg	.=	'&nbsp;&nbsp;&nbsp;&nbsp;'.JText::sprintf( 'COM_CCK_IMPORTER_LOG2', $session_data['log']['count']['created'], $session_data['log']['count']['updated'], $session_data['log']['count']['cancelled'], $session_data['log']['count']['regressed'] );
						} else {
							$msg	.=	'&nbsp;&nbsp;&nbsp;&nbsp;'.JText::sprintf( 'COM_CCK_IMPORTER_LOG', $session_data['log']['count']['created'], $session_data['log']['count']['updated'], $session_data['log']['count']['cancelled'] );
						}
						$msg		.=	' <a href="'.JUri::base().'index.php?option=com_cck&task=download&file='.$file.'" style="color:#000000;">( '.JText::_( 'COM_CCK_LOG' ).' )</a>';
						$return		=	array(
											'link'=>CCK_LINK."&do=ok",
											'message'=>$msg,
											'message_type'=>'message'
										);
					} else {
						$return		=	array(
											'file'=>$file,
											'link'=>JUri::base().'index.php?option=com_cck&task=download&file='.$file
										);
					}
				} else {
					$return			=	array(
											'link'=>CCK_LINK."&do=ok",
											'message'=>JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ),
											'message_type'=>'error'
										);
				}

				$session->set( $session_id, '' );
				$session->set( 'cck_importer_batch_ok', $return );
			}

			echo ( is_array( $return ) ) ? json_encode( $return ) : '{}';
		}
	}

	// prepareFile
	public function prepareFile()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$model	=	$this->getModel( 'cck_importer' );
		$params	=	JComponentHelper::getParams( 'com_cck_importer' );
		
		if ( $model->prepareFile( $params ) ) {
			$this->setRedirect( CCK_LINK, JText::_( 'COM_CCK_SUCCESSFULLY_PREPARED' ), 'message' );
		} else {
			$this->setRedirect( CCK_LINK, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}

	// purge
	public function purge()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$params	=	JComponentHelper::getParams( 'com_cck_importer' );
		if ( $params->get( 'output', 0 ) < 2 ) {
			$path	=	JPATH_SITE.'/'.$params->get( 'output_path', 'tmp/' );
			
			jimport( 'joomla.filesystem.file' );
			jimport( 'joomla.filesystem.folder' );
			if ( JFolder::exists( $path ) ) {
				$files	=	JFolder::files( $path );
				if ( count( $files ) ) {
					foreach ( $files as $file ) {
						if ( $file != 'index.html' ) {
							JFile::delete( $path.$file );
						}
					}
				}
			}
		}
		
		$this->setRedirect( CCK_LINK, JText::_( 'COM_CCK_SUCCESSFULLY_PURGED' ), 'message' );
	}
}
?>