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

// Init
if ( isset( $this ) && isset( $this->config ) ) {
	$config		=	&$this->config;
}

// Prepare
$item->params	=	is_object( $params ) ? clone $params : new JRegistry;
if ( !$loaded ) {
	//
}
$tag			=	$plg_params->get( 'item_tag_title', 'h2' );
$class			=	$plg_params->get( 'item_class_title', '' );
$class			=	$class ? ' class="'.$class.'"' : '';
?>

<div>
	<?php
	echo '<'.$tag.$class.'>'.$item->subject.'</'.$tag.'>';
	echo JHtml::_( 'content.prepare', $item->body );
	?>
</div>
<?php if ( $plg_params->get( 'item_separator', 1 ) ) { ?>
<hr />
<?php } ?>