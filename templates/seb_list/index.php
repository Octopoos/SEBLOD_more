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

// -- Initialize
require_once dirname(__FILE__).'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$class			=	trim( $cck->getStyleParam( 'class', '' ) );
$class			=	$class ? ' class="'.$class.'"' : '';
$display_mode	=	(int)$cck->getStyleParam( 'list_display', '0' );
$html			=	'';
$id_class		=	$cck->id_class;
$items			=	$cck->getItems();
$fieldnames		=	$cck->getFields( 'element', '', false );
$multiple		=	( count( $fieldnames ) > 1 ) ? true : false;
$count			=	count( $items );
$auto_clean		=	( $count == 1 ) ? $cck->getStyleParam( 'auto_clean', 0 ) : 0;

// -- Render
if ( $id_class ) {
?>
<div class="<?php echo trim( $cck->id_class ); ?>"><?php }
if ( !$auto_clean ) { ?>
<ul<?php echo $class; ?>>
<?php }
	if ( $count ) {
		if ( $display_mode == 2 ) {
			foreach ( $items as $item ) {
				$row	=	$item->renderPosition( 'element' );
				if ( $row ) {
					$row	=	'<li>'.$row.'</li>';
				}
				$html	.=	$row;
			}
		} elseif ( $display_mode == 1 ) {
			foreach ( $items as $pk=>$item ) {
				$row	=	$cck->renderItem( $pk );
				if ( $row && !$auto_clean ) {
					$row	=	'<li>'.$row.'</li>';
				}
				$html	.=	$row;
			}
		} else {
			foreach ( $items as $item ) {
				$row	=	'';
				foreach ( $fieldnames as $fieldname ) {
					$content	=	$item->renderField( $fieldname );
					if ( $content != '' ) {
						if ( $item->getMarkup( $fieldname ) != 'none' && ( $multiple || $item->getMarkup_Class( $fieldname ) ) ) {
							$row	.=	'<div class="cck-clrfix'.$item->getMarkup_Class( $fieldname ).'">'.$content.'</div>';
						} else {
							$row	.=	$content;
						}
					}
				}
				if ( $row && !$auto_clean ) {
					$row	=	'<li>'.$row.'</li>';
				}
	            $html	.=	$row;
			}
		}
		echo $html;
	}
if ( !$auto_clean ) {
?>
</ul>
<?php
}
if ( $id_class ) { ?>
</div>
<?php }

// -- Finalize
$cck->finalize();
?>