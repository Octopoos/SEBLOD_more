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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

$app	=	Factory::getApplication();

if ( ! Factory::getUser()->authorise( 'core.manage', 'com_cck_importer' ) ) {
	return $app->enqueueMessage( Text::_( 'JERROR_ALERTNOAUTHOR' ), 'error' );
}

$lang	=	Factory::getLanguage();
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->load( 'com_cck_core' );

require_once JPATH_COMPONENT.'/helpers/helper_define.php';
require_once JPATH_COMPONENT.'/helpers/helper_display.php';
require_once JPATH_COMPONENT.'/helpers/helper_include.php';
require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
require_once JPATH_COMPONENT.'/helpers/helper_output.php';

$controller	=	BaseController::getInstance( 'CCK_Importer' );
$controller->execute( $app->input->get( 'task' ) );
$controller->redirect();
?>