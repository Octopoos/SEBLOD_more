<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'link', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Javascript', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'defaultvalue'=>0, 'label'=>'Confirm', 'storage_field'=>'confirm' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'defaultvalue'=>'Please Confirm', 'size'=>32, 'storage_field'=>'confirm_alert' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>32, 'storage_field'=>'rel' ) );
		echo JCckDev::renderBlank();
		echo '<li><label>'.JText::_( 'COM_CCK_TITLE' ).'</label>'
			. JCckDev::getForm( 'core_dev_select', '', $config, array( 'selectlabel'=>'None', 'options'=>'Custom=2', 'storage_field'=>'title' ) )
			. JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Title', 'size'=>16, 'css'=>'input-medium', 'storage_field'=>'title_custom' ) )
			. '</li>';
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#title_custom').isVisibleWhen('title','2',false);
	$('#confirm_alert').isVisibleWhen('confirm','1');
});
</script>