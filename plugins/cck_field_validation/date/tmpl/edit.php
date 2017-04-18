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

echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Format', 'selectlabel'=>'Select', 'options'=>'International=international||EN=en||FR=fr||US=us', 'bool8'=>false, 'required'=>'required', 'storage_field'=>'region' ) );
echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Time', 'defaultvalue'=>'0', 'selectlabel'=>'', 'storage_field'=>'time' ) );
echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Separator', 'selectlabel'=>'Any', 'options'=>'-=-||.=.||/=/', 'bool8'=>false, 'storage_field'=>'separator' ) );
echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Restriction', 'selectlabel'=>'None', 'options'=>'State is Future=isFuture', 'storage_field'=>'range' ) );
echo '<li><label>'.JText::_( 'COM_CCK_ALERT' ).' (2)</label>'
   . JCckDev::getForm( 'core_dev_text', '', $config, array( 'label'=>'Alert', 'storage_field'=>'range_alert' ) )
   . '</li>';
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'range_fieldname' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#alert').parent().find('label').append('<span class="star"> </star>');
	if ($('#separator').val() != '') {
		$('#alert').addClass('validate[required]').parent().find('span.star').html(' *');
	}
	$('#separator').isVisibleWhen('region','en,fr,us');
	$('#range,#range_fieldname,#range_alert').isVisibleWhen('region','international');
	$("div#layout").on("change", "#separator", function() {
		if ($(this).val() != '') {
			$('#alert').addClass('validate[required]').parent().find('span.star').html(' *');
		} else {
			$('#alert').removeClass('validate[required]').parent().find('span.star').html('');
		}
	});
});
</script>