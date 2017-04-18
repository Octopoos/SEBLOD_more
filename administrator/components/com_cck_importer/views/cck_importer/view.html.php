<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCK_ImporterViewCCK_Importer extends JCckBaseLegacyView
{
	// prepareToolbar
	protected function prepareToolbar()
	{
		$bar	=	JToolBar::getInstance( 'toolbar' );
		$canDo	=	Helper_Admin::getActions();
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/separator.php';
		
		JToolBarHelper::title( CCK_LABEL, 'cck-seblod' );
		
		if ( $canDo->get( 'core.admin' ) ) {
			if ( JComponentHelper::getParams( 'com_cck_importer' )->get( 'output', 0 ) < 2 ) {
				JToolBarHelper::custom( 'purge', 'delete', 'delete', 'COM_CCK_PURGE', false );
				$bar->appendButton( 'CckSeparator' );
			}
			JToolBarHelper::preferences( CCK_ADDON, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		$bar->appendButton( 'CckLink', 'archive', JText::_( 'COM_CCK_SESSIONS' ), JRoute::_( 'index.php?option=com_cck&view=sessions&extension=com_cck_importer' ), '_self' );
		Helper_Admin::addToolbarSupportButton();
	}
}
?>