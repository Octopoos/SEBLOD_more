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

$app	=	JFactory::getApplication();
$config	=	JCckDev::init( array(), true, array( 'item'=>new stdClass, 'tmpl'=>'ajax' ) );
$lang 	=	JFactory::getLanguage();
$type	=	$app->input->getString( 'ajax_type', 'joomla_article' );
$lang->load( 'plg_cck_storage_location_'.$type, JPATH_ADMINISTRATOR, null, false, true );
Helper_Include::addDependencies( $this->getName(), $this->getLayout(), 'ajax' );

$layer	=	JPATH_PLUGINS.'/cck_storage_location/'.$type.'/tmpl/importer.php';
if ( file_exists( $layer ) ) {
	include_once $layer;
}
?>