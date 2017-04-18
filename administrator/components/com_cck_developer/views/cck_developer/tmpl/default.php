<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$config	=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true );
$params	=	JComponentHelper::getParams( 'com_cck_developer' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option=' . $this->option ); ?>" method="post" id="adminForm" name="adminForm">
<div>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
    <div class="<?php echo $this->css['w100']; ?>">
        <div class="seblod first cpanel_news full">
            <div class="legend top center plus"><?php echo CCK_LABEL .' &rarr; '. JText::_( 'COM_CCK_ADDON_'.CCK_NAME ); ?></div>
            <ul class="adminformlist">
                <li style="text-align:center;">
                    <?php echo JText::_( 'COM_CCK_ADDON_'.CCK_NAME.'_DESC' ); ?>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
    </div>                    
	<div class="seblod-less cpanel_news full">
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', JText::_( 'COM_CCK_PANE_SEBLOD_PLUGIN' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <div class="seblod">
            <ul class="adminformlist">
                <?php
				$attr	=	'onclick="javascript:Joomla.submitbutton(\'createPlugin\');"';
				
				echo '<li><label>'.JText::_( 'COM_CCK_NAME' ).'<span class="star"> *</span></label>'
				 .	 JCckDev::getForm( 'more_developer_name', '', $config, array( 'css'=>'validate[required,custom[plugin_name]]' ) )
				 .	 '</li>';
				echo '<li><label>'.JText::_( 'COM_CCK_TYPE_GROUP' ).'<span class="star"> *</span></label>'
				 .	 JCckDev::getForm( 'more_developer_type', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field_link', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field_live', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field_restriction', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field_typo', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_field_validation', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_storage', '', $config )
				 .	 JCckDev::getForm( 'more_developer_group_storage_location', '', $config )
				 .	 '</li>';
				
				$creation_date	=	( $params->get( 'creation_date', 0 ) == 1 ) ? $params->get( 'creation_date_custom', '2012' ) : JFactory::getDate()->format( $params->get( 'creation_date_format', 'F Y' ) );
				echo JCckDev::renderForm( 'more_developer_creation_date', $creation_date, $config );
				echo JCckDev::renderForm( 'more_developer_description', $params->get( 'description', 'SEBLOD 3.x - www.seblod.com // by Octopoos - www.octopoos.com' ), $config );
				echo JCckDev::renderForm( 'more_developer_version', '', $config, array(), array(), 'w100' );
				echo JCckDev::renderForm( 'more_developer_submit', '', $config, array(  'storage'=>'dev', 'attributes'=>$attr, 'css'=>'btn-primary' ), array(), 'flt-right' );
				?>
            </ul>
        </div>
        <?php echo JCckDevAccordion::end(); ?>
	</div>
    <div class="clr"></div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<?php
	$config['validation']['plugin_name']	=	'"plugin_name":{"regex": /^[a-z0-9_]+$/,"alertText":"* '.JText::_( 'COM_CCK_PLUGIN_NAME_VALIDATION' ).'"}';
	JCckDev::validate( $config );
    echo JHtml::_( 'form.token' );
	?>
</div>

<?php
Helper_Display::quickCopyright();
?>
</div>
</form>

<script type="text/javascript">
(function ($){
	JCck.Dev = {
        submit: function(task) {
            Joomla.submitbutton(task);
        }
    }
	Joomla.submitbutton = function(task) {
		if ($("#adminForm").validationEngine("validate",task) === true) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
	$(document).ready(function() {
		$('#group_cck_field').isVisibleWhen('type','cck_field',false);
		$('#group_cck_field_link').isVisibleWhen('type','cck_field_link',false);
		$('#group_cck_field_live').isVisibleWhen('type','cck_field_live',false);
		$('#group_cck_field_restriction').isVisibleWhen('type','cck_field_restriction',false);
		$('#group_cck_field_typo').isVisibleWhen('type','cck_field_typo,cck_field_typo_form',false);
		$('#group_cck_field_validation').isVisibleWhen('type','cck_field_validation',false);
		$('#group_cck_storage').isVisibleWhen('type','cck_storage',false);
		$('#group_cck_storage_location').isVisibleWhen('type','cck_storage_location',false);
    });
})(jQuery);
</script>