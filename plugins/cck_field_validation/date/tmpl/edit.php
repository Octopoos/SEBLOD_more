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

echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Format', 'selectlabel'=>'Select', 'options'=>'International=international||EN=en||FR=fr||US=us', 'bool8'=>false, 'required'=>'required', 'storage_field'=>'region' ) );
echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Separator', 'selectlabel'=>'Any', 'options'=>'-=-||.=.||/=/', 'bool8'=>false, 'storage_field'=>'separator' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#alert').parent().find('label').append('<span class="star"> </star>');
	if ($('#separator').val() != '') {
		$('#alert').addClass('validate[required]').parent().find('span.star').html(' *');
	}
	$('#separator').isVisibleWhen('region','en,fr,us');
	$("div#layout").on("change", "#separator", function() {
		if ($(this).val() != '') {
			$('#alert').addClass('validate[required]').parent().find('span.star').html(' *');
		} else {
			$('#alert').removeClass('validate[required]').parent().find('span.star').html('');
		}
	});
});
</script>