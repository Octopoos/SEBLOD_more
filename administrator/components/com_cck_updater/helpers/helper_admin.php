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

require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/common/admin.php';

// Helper
class Helper_Admin extends CommonHelper_Admin
{
	// getProxy
	static function getProxy( $params, $segment, $scheme = false )
	{
		$uri	=	JUri::getInstance();

		if ( $params->get( 'proxy_domain' ) ) {
			$proxy	=	$params->get( 'proxy_domain' );
		} else {
			$proxy	=	$uri->getHost();
		}
		$proxy		=	$proxy.$params->get( $segment );

		if ( $scheme ) {
			$proxy	=	$uri->getScheme().'://'.$proxy;
		}

		return $proxy;
	}
}
?>