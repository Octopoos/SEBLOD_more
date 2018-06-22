<?php
/**
* @version 			SEBLOD eCommerce 1.x
* @package			SEBLOD eCommerce Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$options    =   JCckDev::fromJSON( $this->item->options );
?>

<div class="seblod">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_SETTINGS' ), JText::_( 'PLG_CCK_ECOMMERCE_PAYMENT_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
        //
        ?>
    </ul>
</div>