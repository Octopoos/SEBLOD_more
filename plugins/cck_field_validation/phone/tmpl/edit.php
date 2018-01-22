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

echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Format', 'selectlabel'=>'Select', 'options'=>'International=international||FR=fr||UA=ua||UK=uk||US=us', 'bool8'=>false, 'required'=>'required', 'storage_field'=>'region' ), array(), 'w100' );
?>