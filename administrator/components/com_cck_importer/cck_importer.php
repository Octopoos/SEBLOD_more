<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ! JFactory::getUser()->authorise( 'core.manage', 'com_cck_importer' ) ) {
	return JError::raiseWarning( 404, JText::_( 'JERROR_ALERTNOAUTHOR' ) );
}

$lang	=	JFactory::getLanguage();
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->load( 'com_cck_core' );

jimport( 'cck.development.development' ); // (deprecated)
require_once JPATH_COMPONENT.'/helpers/helper_define.php';
require_once JPATH_COMPONENT.'/helpers/helper_display.php';
require_once JPATH_COMPONENT.'/helpers/helper_include.php';
require_once JPATH_COMPONENT.'/helpers/helper_admin.php';
require_once JPATH_COMPONENT.'/helpers/helper_output.php';

$controller	=	JControllerLegacy::getInstance( 'CCK_Importer' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>