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

// Controller
class CCK_UpdaterController extends JControllerLegacy
{
	protected $text_prefix	=	'COM_CCK_UPDATER';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
	}
}
?>