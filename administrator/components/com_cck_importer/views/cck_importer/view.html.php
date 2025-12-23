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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// View
class CCK_ImporterViewCCK_Importer extends JCckBaseLegacyView
{
	// display
	public function display( $tpl = null )
	{
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', []);

		parent::display( $tpl );
	}

	// prepareToolbar
	protected function prepareToolbar()
	{
		$bar	=	Toolbar::getInstance( 'toolbar' );
		$canDo	=	Helper_Admin::getActions();
		
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/separator.php';
		
		ToolbarHelper::title( CCK_LABEL, 'cck-seblod' );
		
		if ( $canDo->get( 'core.admin' ) ) {
			if ( ComponentHelper::getParams( 'com_cck_importer' )->get( 'output', 0 ) < 2 ) {
				ToolbarHelper::custom( 'purge', 'delete', 'delete', 'COM_CCK_PURGE', false );
				$bar->appendButton( 'CckSeparator' );
			}
			ToolbarHelper::preferences( CCK_ADDON, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		$bar->appendButton( 'CckLink', 'archive', Text::_( 'COM_CCK_SESSIONS' ), Route::_( 'index.php?option=com_cck&view=sessions&extension=com_cck_importer' ), '_self' );
		Helper_Admin::addToolbarSupportButton();
	}
}
?>