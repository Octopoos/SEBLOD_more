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

echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Decimals Separator', 'defaultvalue'=>',', 'size'=>'8', 'storage_field'=>'input_decimals_separator', 'required'=>'required', 'attributes'=>'placeholder="Input"' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Decimals Separator', 'defaultvalue'=>'.', 'size'=>'8', 'storage_field'=>'output_decimals_separator', 'required'=>'required', 'attributes'=>'placeholder="Storage"' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Thousands Separator', 'size'=>'8', 'storage_field'=>'input_thousands_separator', 'attributes'=>'placeholder="Input"' ) );
echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Thousands Separator', 'size'=>'8', 'storage_field'=>'output_thousands_separator', 'attributes'=>'placeholder="Storage"' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#alert').isVisibleWhen('decimals','-1');
});
</script>