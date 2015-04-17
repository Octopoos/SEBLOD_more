<?php
/**
* @version 			SEBLOD Updater 1.x
* @package			SEBLOD Updater Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$config	=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true );
$params	=	JComponentHelper::getParams( 'com_cck_updater' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option=' . $this->option ); ?>" method="post" id="adminForm" name="adminForm">
<div>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
    <div class="<?php echo $this->css['w100']; ?>">
        <div class="seblod first cpanel_news full beta">
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
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', JText::_( 'COM_CCK_PANE_UPDATE_EXTENSIONS' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <div>
            <table class="<?php echo $this->css['table']; ?>">
                <thead>
                    <tr class="half">
                        <th width="32" class="center hidden-phone nowrap"><input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" /></th>
                        <th class="center"><?php echo JText::_( 'COM_CCK_EXTENSION' ); ?></th>
                        <th width="20%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_TYPE' ); ?></th>
                        <th width="20%" class="center nowrap"><?php echo JText::_( 'COM_CCK_VERSION' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $items  =   JCckDatabase::loadObjectList( 'SELECT * FROM #__updates WHERE extension_id != 0 AND detailsurl LIKE "%update.seblod.com%"' );
                if ( count( $items ) ) {
                    foreach ( $items as $i=>$item ) {
                        ?>
                        <tr class="row<?php echo $i % 2; ?> half">
                            <td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->update_id ); ?></td>
                            <td><?php echo '<a href="'.$item->infourl.'" target="_blank">'.$this->escape( $item->name ).'</a>'; ?></td>
                            <td class="center hidden-phone"><?php echo ucfirst( $item->type ); ?></td>
                            <td class="center"><?php echo $item->version; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php echo JCckDevAccordion::end(); ?>
	</div>
    <div class="clr"></div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
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
    });
})(jQuery);
</script>