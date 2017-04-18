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

if ( is_file( JPATH_COMPONENT.'/_VERSION.php' ) ) {
	require_once JPATH_COMPONENT.'/_VERSION.php';
	$version	=	new JCckUpdaterVersion;
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

define( 'CCK_VERSION', 			( isset( $version ) && is_object( $version ) ) ? $version->getShortVersion() : '1.x' );
define( 'CCK_NAME',				'cck_updater' );
define( 'CCK_TITLE',			'CCK_UPDATER' );
define( 'CCK_ADDON',			'com_'.CCK_NAME );
define( 'CCK_LABEL',			JText::_( CCK_ADDON.'_ADDON' ) );
define( 'CCK_COM',				'com_cck' );
define( 'CCK_MODEL',			CCK_TITLE.'Model' );
define( 'CCK_TABLE',			CCK_NAME.'_Table' );
define( 'CCK_WEBSITE',			'https://www.seblod.com' );
define( 'CCK_LINK',				'index.php?option=com_'.CCK_NAME );
?>