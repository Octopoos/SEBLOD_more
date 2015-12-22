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

JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true, 'fieldPicker'=>true ) );
$options	=	JCckDev::fromSTRING( $this->item->options );
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_dev_bool', $this->item->bool, $config, array( 'label'=>'Behavior', 'defaultvalue'=>'0', 'options'=>'Autocomplete=1||Concatenate=0', 'storage_field'=>'bool' ) );        
        echo JCckDev::renderForm( 'core_dev_text', @$options2['latitude'], $config, array( 'label'=>'Latitude Field', 'storage_field'=>'json[options2][latitude]', 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Address', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) );
        echo JCckDev::renderForm( 'more_address_to_coordinates_types', @$options2['types'], $config );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['longitude'], $config, array( 'label'=>'Longitude Field', 'storage_field'=>'json[options2][longitude]', 'required'=>'required' ) );
		echo JCckDev::renderForm( 'more_address_to_coordinates_restrictions_country', @$options2['restrictions_country'], $config );
        echo '<li><label>'.JText::_( 'COM_CCK_COUNTRY_FIELD' ).'</label>'
         .   JCckDev::getForm( 'core_dev_text', @$options2['country'], $config, array( 'label'=>'', 'storage_field'=>'json[options2][country]', 'css'=>'input-small', 'required'=>'' ) )
         .   JCckDev::getForm( 'core_dev_select', @$options2['country_type'], $config, array( 'label'=>'', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Full=1||Short=0', 'storage_field'=>'json[options2][country_type]' ) )
         .   '</li>';
        echo JCckDev::renderForm( 'core_dev_select', @$options2['bypass'], $config, array( 'label'=>'Bypass', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'No=0||Yes=optgroup||Countries=1||Countries and Regions=2', 'storage_field'=>'json[options2][bypass]' ) );
        echo JCckDev::renderForm( 'core_dev_text', @$options2['city'], $config, array( 'label'=>'City Field', 'storage_field'=>'json[options2][city]', 'required'=>'required' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#sortable_core_options').isVisibleWhen('bool','0');
    $('#json_options2_types,#json_options2_restrictions_country,#json_options2_bypass,#json_options2_country').isVisibleWhen('bool','1');
});
</script>