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

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'Inherited' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Redirection', 'selectlabel'=>'None', 'options'=>'Current=current', 'storage_field'=>'redirection' ) );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_attributes', '', $config, array( 'label'=>'Custom Attributes', 'storage_field'=>'attributes' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'storage_field'=>'target' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>24, 'storage_field'=>'rel' ) );
		echo JCckDev::renderForm( 'core_tmpl', '', $config );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'1', 'options'=>'Apply=1||Prepare=0', 'storage_field'=>'state' ) );
		echo JCckDev::renderBlank();
        ?>
    </ul>
</div>