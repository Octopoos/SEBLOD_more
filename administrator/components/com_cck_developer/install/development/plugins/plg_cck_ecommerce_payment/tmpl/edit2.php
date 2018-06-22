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

$options	=	JCckDev::fromJSON( $this->item->options );
?>

<?php echo JCckDev::renderLegend( JText::_( 'PLG_CCK_ECOMMERCE_PAYMENT_'.$type.'_LABEL' ), JText::_( 'PLG_CCK_ECOMMERCE_PAYMENT_'.$type.'_DESC' ) ); ?>
<ul class="adminformlist adminformlist-2cols">
    <?php
	//
    ?>
</ul>