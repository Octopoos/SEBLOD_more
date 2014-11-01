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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_bool', @$options2['storage_format'], $config, array( 'label'=>'Storage Format', 'options'=>'Folder Name=1' ) );
		//echo '<li><label>'.JText::_( 'COM_CCK_MULTIPLE_ROW' ).'</label>'
		// .	 JCckDev::getForm( 'core_bool3', $this->item->bool3, $config, array( 'defaultvalue'=>'0', 'label'=>'Multiple' ) )
		// .	 JCckDev::getForm( 'core_rows', $this->item->rows, $config, array( 'defaultvalue'=>10 ) )
		// .	 '</li>';
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config, array( 'defaultvalue'=>'Select a Folder' ) );
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<!--
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#rows').isVisibleWhen('bool3','1',false);
	$('#selectlabel').isVisibleWhen('bool3','0');
});
</script>
-->