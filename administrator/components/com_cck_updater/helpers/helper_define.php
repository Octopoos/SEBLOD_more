<?php
/**
* @version 			SEBLOD Updater 1.x
* @package			SEBLOD Updater Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
if ( JFile::exists( JPATH_COMPONENT.'/_VERSION.php' ) ) {
	$version	=	JFile::read( JPATH_COMPONENT.'/_VERSION.php' );
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

define( 'CCK_VERSION', 			( @$version ) ? $version : '1.x' );
define( 'CCK_NAME',				'cck_updater' );
define( 'CCK_TITLE',			'CCK_UPDATER' );
define( 'CCK_ADDON',			'com_'.CCK_NAME );
define( 'CCK_LABEL',			JText::_( CCK_ADDON.'_ADDON' ) );
define( 'CCK_COM',				'com_cck' );
define( 'CCK_MODEL',			CCK_TITLE.'Model' );
define( 'CCK_TABLE',			CCK_NAME.'_Table' );
define( 'CCK_WEBSITE',			'http://www.seblod.com' );

define( 'CCK_LINK',				'index.php?option=com_'.CCK_NAME );

$root	=	JURI::root( true );
define( 'JROOT_CCK',			$root );
define( 'JROOT_MEDIA_CCK',		$root.'/media/cck' );
define( 'JPATH_LIBRARIES_CCK',	JPATH_SITE.'/libraries/cck' );
?>