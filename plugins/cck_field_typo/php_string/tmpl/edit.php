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

JCckDev::initScript( 'typo', $this->item );
$texts	=	array(
				'COM_CCK_PHP_STRING_START',
				'COM_CCK_PHP_STRING_LENGTH',
				'COM_CCK_PHP_STRING_SEARCH',
				'COM_CCK_PHP_STRING_REPLACE',
				'COM_CCK_PHP_STRING_LOWER_FIRST',
				'COM_CCK_PHP_STRING_STRIPTAGS_FIRST',
				'COM_CCK_PHP_STRING_NEEDLE',
				'COM_CCK_STRING',
				'COM_CCK_WIDTH',
				'COM_CCK_DECIMALS',
				'COM_CCK_DECIMALS_SEPARATOR',
				'COM_CCK_THOUSANDS_SEPARATOR'
			);
foreach ( $texts as $t ) {
	JText::script( $t );
}
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_TYPO_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Function', 'selectlabel'=>'Select', 'options'=>'number_format||str_repeat||str_replace||strip_tags||strtolower||strtoupper||substr||substr_count||ucfirst||ucwords||wordwrap', 'bool8'=>0, 'storage_field'=>'function' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Php String Start', 'storage_field'=>'arg1', 'required'=>'required' ) );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Php String Lower first', 'defaultvalue'=>'0', 'storage_field'=>'force' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Php String Length', 'storage_field'=>'arg2' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li2" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'...', 'storage_field'=>'arg3' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li3" value="" />' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Php String Suffix Overflow', 'storage_field'=>'suffix_overflow' ) );
        ?>
    </ul>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Php String Prefix', 'storage_field'=>'prefix' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Php String Suffix', 'storage_field'=>'suffix' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_bool', '', $config, array( 'label'=>'Behavior', 'selectlabel'=>'', 'defaultvalue'=>'0', 'options'=>'Auto=0||Typo Label=1', 'storage_field'=>'typo_label' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'label'=>'Typo Target', 'selectlabel'=>'Auto', 'defaultvalue'=>'', 'options'=>'Value=value', 'storage_field'=>'typo_target' ) );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	var v = $('#function').val();
	if (v == 'str_replace') {
		$('#force').parent().hide();
		$('#arg1').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_SEARCH')+'<span class="star"> *</span>');
		$('#arg2').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_REPLACE'));
		$('#arg1,#arg2,#blank_li').parent().show();
		$('#arg3,#blank_li3').parent().hide();
		$('#suffix_overflow,#blank_li2').parent().hide();
	} else if (v == 'substr') {
		$('#force').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_STRIPTAGS_FIRST'));
		$('#arg1').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_START')+'<span class="star"> *</span>');
		$('#arg2').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_LENGTH'));
		$('#arg1,#arg2').parent().show();
		$('#arg3,#blank_li3,#blank_li').parent().hide();
		$('#suffix_overflow,#blank_li2').parent().show();
	} else if (v == 'substr_count' || v == 'str_repeat' || v == 'wordwrap') {
		$('#force').parent().hide();
		if (v == 'wordwrap') {
			$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_WIDTH')+'<span class="star"> *</span>');
		} else if (v == 'substr_count') {
			$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_NEEDLE')+'<span class="star"> *</span>');	
		} else {
			$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_STRING')+'<span class="star"> *</span>');	
		}
		$('#arg2,#blank_li').parent().hide();
		$('#arg3,#blank_li3').parent().hide();
		$('#suffix_overflow,#blank_li2').parent().hide();
	} else if (v == 'number_format') {
		$('#force').parent().hide();
		$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_DECIMALS')+'<span class="star"> *</span>');
		$('#arg2').parent().show().find('label').html(Joomla.JText._('COM_CCK_DECIMALS_SEPARATOR'));
		$('#arg3').parent().show().find('label').html(Joomla.JText._('COM_CCK_THOUSANDS_SEPARATOR'));
		$('#suffix_overflow,#blank_li2,#blank_li3,#blank_li').parent().hide();
	} else {
		if ( v == 'ucfirst' || v == 'ucwords' ) {
			$('#force').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_LOWER_FIRST'));
		} else {
			$('#force').parent().hide();
		}
		$('#arg1,#arg2,#blank_li').parent().hide();
		$('#arg3,#blank_li3').parent().hide();
		$('#suffix_overflow,#blank_li2').parent().hide();
	}
	$("form#adminForm").on("change", "#function", function() {
		var v = $(this).val();
		if (v == 'str_replace') {
			$('#force').parent().hide();
			$('#arg1').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_SEARCH')+'<span class="star"> *</span>');
			$('#arg2').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_REPLACE'));
			$('#arg1,#arg2,#blank_li').parent().show();
			$('#arg3,#blank_li3').parent().hide();
			$('#suffix_overflow,#blank_li2').parent().hide();
		} else if (v == 'substr') {
			$('#force').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_STRIPTAGS_FIRST'));
			$('#arg1').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_START')+'<span class="star"> *</span>');
			$('#arg2').parent().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_LENGTH'));
			$('#arg1,#arg2').parent().show();
			$('#arg3,#blank_li3,#blank_li').parent().hide();
			$('#suffix_overflow,#blank_li2').parent().show();
		} else if (v == 'substr_count' || v == 'str_repeat' || v == 'wordwrap') {
			$('#force').parent().hide();
			if (v == 'wordwrap') {
				$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_WIDTH')+'<span class="star"> *</span>');
			} else if (v == 'substr_count') {
				$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_NEEDLE')+'<span class="star"> *</span>');
			} else {
				$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_STRING')+'<span class="star"> *</span>');
			}
			$('#arg2,#blank_li').parent().hide();
			$('#arg3,#blank_li3').parent().hide();
			$('#suffix_overflow,#blank_li2').parent().hide();
		} else if (v == 'number_format') {
			$('#force').parent().hide();
			$('#arg1').parent().show().find('label').html(Joomla.JText._('COM_CCK_DECIMALS')+'<span class="star"> *</span>');
			$('#arg2').parent().show().find('label').html(Joomla.JText._('COM_CCK_DECIMALS_SEPARATOR'));
			$('#arg3').parent().show().find('label').html(Joomla.JText._('COM_CCK_THOUSANDS_SEPARATOR'));
			$('#suffix_overflow,#blank_li2,#blank_li3,#blank_li').parent().hide();
		} else {
			if ( v == 'ucfirst' || v == 'ucwords' ) {
				$('#force').parent().show().find('label').html(Joomla.JText._('COM_CCK_PHP_STRING_LOWER_FIRST'));
			} else {
				$('#force').parent().hide();
			}
			$('#arg1,#arg2,#blank_li').parent().hide();
			$('#arg3,#blank_li3').parent().hide();
			$('#suffix_overflow,#blank_li2').parent().hide();
		}
	});
});
</script>