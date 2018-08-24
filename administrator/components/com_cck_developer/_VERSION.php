<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDeveloperVersion
final class JCckDeveloperVersion
{
	public $RELEASE = '1.4';
	
	public $DEV_LEVEL = '1';

	// getShortVersion
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL;
	}
}
?>