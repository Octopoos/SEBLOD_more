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

$attributes	=	$cck->id_attributes ? ' '.$cck->id_attributes : '';
$attributes	=	$cck->replaceLive( $attributes );

// -- Render
if ( $cck->id_class != '' ) {
	echo '<div class="'.trim( $cck->id_class ).'"'.$attributes.'>'.$cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) ).'</div>';
} else {
	echo $cck->renderPosition( 'mainbody', '', $cck->h( 'mainbody' ) );
}

if ( $cck->countFields( 'modal' ) && JCck::on() ) {
	JHtml::_( 'bootstrap.modal', 'collapseModal' );
	?>
	<div class="modal hide fade" id="collapseModal">
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