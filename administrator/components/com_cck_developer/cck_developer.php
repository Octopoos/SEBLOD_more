<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ! JFactory::getUser()->authorise( 'core.manage', 'com_cck_developer' ) ) {
	return JError::raiseWarning( 404, JText::_( 'JERROR_ALERTNOAUTHOR' ) );
}

$lang	=	JFactory::getLanguage();
$lang->load( 'com_cck_default', JPATH_SITE );
$lang->load( 'com_cck_core' );

require_once JPATH_COMPONENT.'/helpers/helper_define.php';
require_once JPATH_COMPONENT.'/helpers/helper_display.php';
require_once JPATH_COMPONENT.'/helpers/helper_include.php';
require_once JPATH_COMPONENT.'/helpers/helper_admin.php';

$controller	=	JControllerLegacy::getInstance( 'CCK_Developer' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>