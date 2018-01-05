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

$options	=	JCckDev::fromJSON( $this->item->options );
?>

<?php echo JCckDev::renderLegend( JText::_( 'PLG_CCK_ECOMMERCE_PAYMENT_'.$type.'_LABEL' ), JText::_( 'PLG_CCK_ECOMMERCE_PAYMENT_'.$type.'_DESC' ) ); ?>
<ul class="adminformlist adminformlist-2cols">
    <?php
	//
    ?>
</ul>