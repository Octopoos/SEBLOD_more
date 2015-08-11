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

// JCckUpdaterVersion
final class JCckUpdaterVersion
{
	public $RELEASE = '1.2';
	
	public $DEV_LEVEL = '0';

	// getShortVersion
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL;
	}
}
?>