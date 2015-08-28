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
$display_mode	=	(int)$cck->getStyleParam( 'comparison_display', '0' );
$html			=	'';
$items			=	$cck->getItems();
$fieldnames		=	$cck->getFields( 'element', '', false );
$multiple		=	( count( $fieldnames ) > 1 ) ? true : false;
$positions		=	$cck->getPositions();

$count			=	count( $items );
$class			=	100 / $count;
if ( !is_integer( $class ) ) {
	$class		=	(int)$class;
	$class		=	(string)$class.'f';
}
$class			=	'cck-w'.(string)$class.' cck-fl';

// -- Render
?>
<div class="<?php echo $cck->id_class; ?>">
<?php
	if ( $count ) {
		if ( $display_mode == 2 ) {
			foreach ( $items as $item ) {
				$row	=	$item->renderPosition( 'element' );
				if ( $row ) {
					$row	=	'<div class="'.$class.'">'.$row.'</div>';
				}
				$html	.=	$row;
			}
		} elseif ( $display_mode == 1 ) {
			foreach ( $items as $pk=>$item ) {
				$row	=	$cck->renderItem( $pk );
				if ( $row ) {
					$row	=	'<div class="'.$class.'">'.$row.'</div>';
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
				if ( $row ) {
					$row	=	'<div class="'.$class.'">'.$row.'</div>';
				}
	            $html	.=	$row;
			}
		}
		echo $html;
	}
?>
</div>
<?php
// -- Finalize
$cck->finalize();
?>