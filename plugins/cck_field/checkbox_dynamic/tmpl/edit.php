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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
		echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		
		echo JCckDev::renderForm( 'core_query', $this->item->bool2, $config );
		echo '<li><label>'.JText::_( 'COM_CCK_ORIENTATION' ).'</label>'
         .   JCckDev::getForm( 'core_orientation', $this->item->bool, $config )
         .   JCckDev::getForm( 'core_orientation_vertical', $this->item->bool5, $config, array( 'storage_field'=>'bool5' ) )
         .   '</li>';
		// 1
		echo JCckDev::renderForm( 'core_options_query', @$options2['query'], $config, array(), array(), 'w100' );
		// 2
		echo JCckDev::renderForm( 'core_options_table', @$options2['table'], $config );
		echo JCckDev::renderForm( 'core_options_name', @$options2['name'], $config );
		echo JCckDev::renderForm( 'core_options_where', @$options2['where'], $config );
		echo JCckDev::renderForm( 'core_options_value', @$options2['value'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_ORDER_BY' ).'</label>'
		.	 JCckDev::getForm( 'core_options_orderby', @$options2['orderby'], $config )
		.	 JCckDev::getForm( 'core_options_orderby_direction', @$options2['orderby_direction'], $config )
		.	 '</li>';
		echo JCckDev::renderForm( 'core_separator', $this->item->divider, $config );
		echo JCckDev::renderForm( 'core_options_limit', @$options2['limit'], $config );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool7, $config, array( 'label'=>'Check All Toggle', 'defaultvalue'=>'0', 'options'=>'Hide=0||Show=optgroup||Above=1||Below=2', 'storage_field'=>'bool7' ) );

		// Language
		echo JCckDev::renderForm( 'core_options_language_detection', @$options2['language_detection'], $config );
		echo '<li><label>'.JText::_( 'COM_CCK_LANGUAGE_CODES_DEFAULT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_language_codes', @$options2['language_codes'], $config, array( 'size' => 21 ) )
		 .	 JCckDev::getForm( 'core_options_language_default', @$options2['language_default'], $config, array( 'size' => 5 ) )
		 .	 '</li>';

        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_query').isVisibleWhen('bool2','1');
	$('#json_options2_table,#json_options2_name,#json_options2_value,#json_options2_where,#json_options2_orderby,#json_options2_limit,#blank_li').isVisibleWhen('bool2','0');
	$('#json_options2_language_detection,#json_options2_language_codes,#bool3,#custom_attr_toggle').isVisibleWhen('bool2','0,1');
	$('#bool5').isVisibleWhen('bool','1',false);
});
</script>