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

// -- Initialize
require_once __DIR__.'/config.php';
$cck	=	CCK_Rendering::getInstance( $this->template );
if ( $cck->initialize() === false ) { return; }

// -- Prepare
$attributes	=	$cck->id_attributes ? ' '.$cck->id_attributes : '';
$attributes	=	$cck->replaceLive( $attributes );

// -- Render
if ( $cck->id_class != '' ) {
	echo '<div class="'.trim( $cck->id_class ).'"'.$attributes.'>'.$cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) ).'</div>';
} else {
	echo $cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) );
}

if ( $cck->countFields( 'modal' ) ) {
	JHtml::_( 'bootstrap.modal', 'collapseModal' );

	$class = $cck->getPosition( 'modal' )->css;
	$class = ( $class ) ? ' ' . $class : '';
	?>
	<div class="modal hide fade<?php echo $class; ?>" id="collapseModal">
		<?php echo $cck->renderPosition( 'modal' ); ?>
	</div>
<?php }

if ( $cck->countFields( 'hidden' ) ) { ?>
	<div style="display: none;">
		<?php echo $cck->renderPosition( 'hidden' ); ?>
	</div>
<?php }

// -- Finalize
$cck->finalize();
?>