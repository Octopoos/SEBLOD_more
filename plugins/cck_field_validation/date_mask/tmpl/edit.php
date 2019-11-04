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

echo JCckDev::renderForm( 'core_dev_select', 'Y-m-d', $config, array( 'label'=>'Format', 'defaultvalue'=>'', 'selectlabel'=>'International',
						  'options'=>'Free=-1',
						  'storage_field'=>'format' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Free Format', 'storage_field'=>'format_custom' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#format_custom').isVisibleWhen('format','-1');
	$('#alert').parent().hide();
});
</script>