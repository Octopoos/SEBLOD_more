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

echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Regex', 'required'=>'required', 'storage_field'=>'regex' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#alert').addClass('validate[required]').parent().find('label').append('<span class="star"> *</star>');
});
</script>