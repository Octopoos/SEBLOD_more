<?php
/**
* @version 			SEBLOD Updater 1.x
* @package			SEBLOD Updater Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

// Controller
class CCK_UpdaterController extends JControllerLegacy
{
	protected $default_view	=	'cck_updater';
	
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
		
	// update
	public function update()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$model	=	$this->getModel( 'cck_updater' );
		$pks	=	$app->input->get( 'cid', array(), 'array' );
		$pks	=	ArrayHelper::toInteger( $pks );
		
		if ( $model->update( $pks ) ) {
			$this->setRedirect( CCK_LINK );
		} else {
			$this->setRedirect( CCK_LINK, JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' ), 'error' );
		}
	}
}
?>