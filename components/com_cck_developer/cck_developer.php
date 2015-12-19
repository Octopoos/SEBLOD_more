<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$controller	=	JControllerLegacy::getInstance( 'CCK_Developer' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
?>