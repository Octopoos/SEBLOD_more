<?php
/**
* @version 			SEBLOD Updater 1.x
* @package			SEBLOD Updater Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCK_UpdaterViewCCK_Updater extends JCckBaseLegacyView
{
	// prepareToolbar
	protected function prepareToolbar()
	{
		$canDo	=	Helper_Admin::getActions();
		
		JToolBarHelper::title( CCK_LABEL, 'cck-seblod' );
		
		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( CCK_ADDON, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		Helper_Admin::addToolbarSupportButton();
	}
}
?>