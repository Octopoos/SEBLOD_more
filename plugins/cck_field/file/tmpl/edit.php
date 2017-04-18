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

$options2	=	JCckDev::fromJSON( $this->item->options2 );
$media_ext	=	( $this->isNew ) ? '' : ( ( isset( $options2['media_extensions'] ) ) ? $options2['media_extensions'] : 'custom' );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
        echo JCckDev::renderForm( 'core_options_path', @$options2['path'], $config, array( 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_selectlabel', $this->item->selectlabel, $config, array( 'defaultvalue'=>'Select a File' ) );
		echo JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'label'=>'Recursively', 'defaultvalue'=>'0' ) );
		echo JCckDev::renderForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Storage Format', 'options'=>'File Name or Path=1||Full Path=0' ) );

		echo '<li><label>'.JText::_( 'COM_CCK_LEGAL_EXTENSIONS' ).'</label>'
		 .	 JCckDev::getForm( 'core_options_media_extensions', $media_ext, $config )
		 .	 JCckDev::getForm( 'core_options_legal_extensions', @$options2['legal_extensions'], $config, array( 'size'=>13, 'required'=>'required' ) )
		 .	 '</li>';
		echo JCckDev::renderBlank();
		
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_legal_extensions').isVisibleWhen('json_options2_media_extensions','custom',false);
});
</script>