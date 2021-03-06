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

JCckDev::forceStorage();
$options	=	JCckDev::fromSTRING( $this->item->options );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderBlank();
		echo JCckDev::renderForm( 'core_dev_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'', 'selectlabel'=>'Select', 'options'=>'Pagination=0||Links=optgroup||Next=2||Previous=1', 'required'=>'required', 'storage_field'=>'bool' ) );
		echo JCckDev::renderForm( 'core_extended', $this->item->extended, $config, array( 'label'=>'Field', 'required'=>'required' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#extended').isVisibleWhen('bool','0');
});
</script>