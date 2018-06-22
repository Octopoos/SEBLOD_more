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

JCckDev::initScript( 'live', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', 'Y-m-d', $config, array( 'label'=>'Format', 'defaultvalue'=>'', 'selectlabel'=>'Auto',
								  'options'=>'Free=-1||Use JText=optgroup||DATE_FORMAT_LC=DATE_FORMAT_LC||DATE_FORMAT_LC1=DATE_FORMAT_LC1||DATE_FORMAT_LC2=DATE_FORMAT_LC2||DATE_FORMAT_LC3=DATE_FORMAT_LC3||DATE_FORMAT_LC4=DATE_FORMAT_LC4||DATE_FORMAT_JS1=DATE_FORMAT_JS1||DATE_FORMAT_TZ=DATE_FORMAT_TZ',
								  'storage_field'=>'format' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Free Format', 'storage_field'=>'format_custom' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Modifier', 'storage_field'=>'modify' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'Apply Time Zone', 'defaultvalue'=>'0', 'storage_field'=>'timezone' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Return JText', 'cols'=>50, 'rows'=>1, 'storage_field'=>'return_jtext' ), array(), 'w100' );
		?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#format_custom').isVisibleWhen('format','-1');
});
</script>