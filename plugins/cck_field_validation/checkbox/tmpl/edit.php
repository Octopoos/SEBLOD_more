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

echo JCckDev::renderForm( 'core_dev_select_numeric', '', $config, array( 'label'=>'Minimum', 'defaultvalue'=>'', 'selectlabel'=>'None', 'options2'=>'{"math":"0","start":"1","first":"","step":"1","last":"","end":"25","force_digits":"0"}', 'storage_field'=>'min' ) );
echo JCckDev::renderForm( 'core_dev_select_numeric', '', $config, array( 'label'=>'Maximum', 'defaultvalue'=>'', 'selectlabel'=>'None', 'options2'=>'{"math":"0","start":"1","first":"","step":"1","last":"","end":"25","force_digits":"0"}', 'storage_field'=>'max' ) );
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#alert').prop('disabled',true);
});
</script>