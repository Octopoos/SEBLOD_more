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
$options2   =   JCckDev::fromJSON( $this->item->options2 );
$options    =   JCckDev::fromSTRING( @$options2['itemids'] );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_sef', @$options2['sef'], $config, array( 'selectlabel'=>'Inherited', 'storage_field'=>'json[options2][sef]' ) );
        echo JCckDev::renderForm( 'core_menuitem', @$options2['itemid'], $config, array( 'selectlabel'=>'Inherited', 'options'=>'Use Value=optgroup||Field=-2', 'storage_field'=>'json[options2][itemid]' ) );
        echo JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['itemid_fieldname'], $config, array( 'label'=>'Field Name', 'storage_field'=>'json[options2][itemid_fieldname]' ) );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Always=0||Only If Different=1' ) );
        echo JCckDev::renderForm( 'core_dev_select', @$options2['content'], $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Use Value=optgroup||Field=2', 'storage_field'=>'json[options2][content]' ) );
        echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['content_fieldname'], $config, array( 'label'=>'Field Name', 'storage_field'=>'json[options2][content_fieldname]' ) );

        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) );
        echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'Alternative Behavior', 'defaultvalue'=>'0', 'options'=>'None=0||Redirection=2' ) );
        echo JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' );
        echo JCckDev::renderForm( 'core_menuitem', $options, $config, array( 'selectlabel'=>'', 'options'=>'', 'bool3'=>1, 'rows'=>30, 'css'=>'input-xlarge', 'storage_field'=>'json[options2][string][itemids]' ), array(), 'w100' );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#json_options2_itemid_fieldname,#blank_li2').isVisibleWhen('json_options2_itemid','-2');
    $('#json_options2_content_fieldname,#blank_li').isVisibleWhen('json_options2_content','2');
    $('#json_options2_string_itemids,#blank_li2').isVisibleWhen('bool2','2');
});
</script>