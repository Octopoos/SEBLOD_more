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

// Controller
class CCK_ImporterController extends JControllerLegacy
{
	protected $text_prefix	=	'COM_CCK_IMPORTER';
	
	// display
	public function display( $cachable = false, $urlparams = false )
	{
		parent::display( true );
	}
}
?>