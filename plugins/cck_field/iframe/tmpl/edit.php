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
		echo '<li><label>'.JText::_( 'COM_CCK_WIDTH_HEIGHT' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_width', @$options2['width'], $config )
		 .	 '<span class="variation_value" style="margin-right: 5px;">x</span>'
		 .	 JCckDev::getForm( 'core_options_height', @$options2['height'], $config )
		 .	 '<span class="variation_value">px</span></li>';
		echo '<li><label>'.JText::_( 'COM_CCK_LIVE_URL_VARIABLES' ).'</label>'
		 .	 JCckDev::getForm( 'core_bool', @$options2['full_var'], $config, array( 'label'=>'clear', 'options'=>'Custom=0||Full=1', 'storage_field'=>'json[options2][full_var]' ) )
		 .	 JCckDev::getForm( 'core_dev_text', @$options2['variable'], $config, array( 'label'=>'clear', 'size'=>'18', 'storage_field'=>'json[options2][variable]' ) )
		 .	 '</li>';
        echo JCckDev::renderForm( 'core_maxlength', $this->item->maxlength, $config );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_variable').isVisibleWhen('json_options2_full_var','0',false);
});
</script>