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

JCckDev::forceStorage();
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <strong><?php echo JText::_('PLG_CCK_FIELD_CAPTCHA_RECAPTCHA_NOTICE' );?></strong>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        echo JCckDev::renderForm( 'core_label', $this->item->label, $config );
        echo JCckDev::renderForm( 'core_size', $this->item->size, $config, array( 'defaultvalue'=>8 ) );

        echo JCckDev::renderForm( 'core_bool', @$options2['theme'], $config, array( 'label'=>'Theme', 'options'=>'BlackGlass=blackglass||Clean=clean||Red=red||White=white', 'defaultvalue'=>'red', 'storage_field'=>'json[options2][theme]' ) );
        echo '<li><label>'.JText::_( 'COM_CCK_LANGUAGE' ).'</label>'
         .   JCckDev::getForm( 'core_bool', $this->item->bool, $config, array( 'label'=>'Language', 'options'=>'Auto=0||Custom=1' ) )
         .   JCckDev::getForm( 'core_bool', @$options2['tag'], $config, array( 'label'=>'Language', 'options'=>'Dutch=nl||English=en||French=fr||German=de||Portuguese=pt||Russian=ru||Spanish=es||Turkish=tr', 'defaultvalue'=>'en', 'css'=>'input-small', 'storage_field'=>'json[options2][tag]' ) )
         .   '</li>';

        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_tag').isVisibleWhen('bool','1',false);
});
</script>