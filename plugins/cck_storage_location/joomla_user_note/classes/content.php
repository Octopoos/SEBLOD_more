<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_User_Note extends JCckContent
{
	// postSave
	protected function postSave( $instance_name, $data )
	{
		if ( $instance_name == 'base' && !$this->getId() ) {
			if ( isset( $data['created_user_id'] ) && $data['created_user_id'] && $data['created_user_id'] != $this->_instance_base->created_user_id ) {
				$this->_instance_base->created_user_id	=	$data['created_user_id'];
			}

			$this->_instance_base->store();
		}
	}
}
?>