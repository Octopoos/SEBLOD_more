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
$options2       =   JCckDev::fromJSON( $this->item->options2 );
$defaultvalue   =   ( !isset( $options2['jtext'] ) ) ? '1' : '0';
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_defaultvalue', $this->item->defaultvalue, $config );
		echo JCckDev::renderForm( 'core_dev_text', @$options2['jtext'], $config, array( 'label'=>'Language Constant', 'defaultvalue'=>'COM_CCK_SEARCH_TOTAL', 'storage_field'=>'json[options2][jtext]' ) );
        echo JCckDev::renderForm( 'core_dev_bool', @$options2['alternative'], $config, array( 'label'=>'Use Alternative', 'defaultvalue'=>$defaultvalue, 'storage_field'=>'json[options2][alternative]' ) );
		
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>