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
$options2	=	JCckDev::fromJSON( $this->item->options2 );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_menuitem', @$options2['itemid'], $config, array( 'label'=>'Redirection', 'selectlabel'=>'None', 'storage_field'=>'json[options2][itemid]' ) );
		echo '<li><label>'.JText::_( 'COM_CCK_TIMEOUT_MS' ).'</label>'
			. JCckDev::getForm( 'core_dev_bool', @$options2['timeout'], $config, array( 'defaultvalue'=>'0', 'storage_field'=>'json[options2][timeout]' ) )
			. JCckDev::getForm( 'core_dev_text', @$options2['timeout_ms'], $config, array( 'size'=>12, 'storage_field'=>'json[options2][timeout_ms]' ) )
		 	.'</li>';

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
		echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
	</ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#json_options2_timeout_ms').isVisibleWhen('json_options2_timeout','1',false);
});
</script>