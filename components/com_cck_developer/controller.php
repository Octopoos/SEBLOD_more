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

// Controller
class CCK_DeveloperController extends JControllerLegacy
{
	protected $text_prefix	=	'COM_CCK_DEVELOPER';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
	}
}
?>