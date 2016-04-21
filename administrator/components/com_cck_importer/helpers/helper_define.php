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

if ( is_file( JPATH_COMPONENT.'/_VERSION.php' ) ) {
	require_once JPATH_COMPONENT.'/_VERSION.php';
	$version	=	new JCckImporterVersion;
}

// -------- -------- -------- -------- -------- -------- -------- -------- // Core

define( 'CCK_VERSION', 			( isset( $version ) && is_object( $version ) ) ? $version->getShortVersion() : '1.x' );
define( 'CCK_NAME',				'cck_importer' );
define( 'CCK_TITLE',			'CCK_IMPORTER' );
define( 'CCK_ADDON',			'com_'.CCK_NAME );
define( 'CCK_LABEL',			JText::_( CCK_ADDON.'_ADDON' ) );
define( 'CCK_COM',				'com_cck' );
define( 'CCK_MODEL',			CCK_TITLE.'Model' );
define( 'CCK_TABLE',			CCK_NAME.'_Table' );
define( 'CCK_WEBSITE',			'http://www.seblod.com' );

define( 'CCK_LINK',				'index.php?option=com_'.CCK_NAME );

$root	=	JUri::root( true );
define( 'JROOT_CCK',			$root );
define( 'JROOT_MEDIA_CCK',		$root.'/media/cck' );
define( 'JPATH_LIBRARIES_CCK',	JPATH_SITE.'/libraries/cck' );
?>