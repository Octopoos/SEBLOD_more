<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'restriction', $this->item );

// Set
echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.admin.edit', array(
    'config'=>$config,
    'form'=>array(
        array(
            'fields'=>array(
                JCckDev::renderBlank(),
                JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Invert', 'type'=>'radio', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=1', 'css'=>'btn-group', 'storage_field'=>'do' ) )
            )
        )
    ),
    'html'=>'',
    'item'=>$this->item,
    'script'=>'',
    'type'=>'restriction'
) );
?>