<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_Menu_Item extends JCckContent
{	
	// preSave
	public function preSave( $instance_name, &$data )
	{
		if ( $instance_name == 'base' ) {
			if ( !isset( $data['parent_id'] ) ) {
				$data['parent_id']	=	( $this->getPk() ) ? $this->{'_instance_'.$instance_name}->parent_id : 1;
			}
			if ( !$this->getPk() || ( $data['parent_id'] != $this->{'_instance_'.$instance_name}->parent_id ) ) {
				$this->{'_instance_'.$instance_name}->setLocation( $data['parent_id'], 'last-child' );
			}
		}
	}

	// postSave
	public function postSave( $instance_name, $data )
	{
		if ( $instance_name == 'base' ) {
			$this->{'_instance_'.$instance_name}->rebuildPath( $this->getPk() );
		}
	}
}
?>