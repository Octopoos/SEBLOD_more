<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'restriction', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_RESTRICTION_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Execute', 'defaultvalue'=>'1', 'options'=>'1=1||2=2||3=3||4=4||5=5||6=6||7=7||8=8||9=9', 'storage_field'=>'execute' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Identifier', 'defaultvalue'=>'', 'storage_field'=>'identifier_name' ) );
        ?>
    </ul>
</div>