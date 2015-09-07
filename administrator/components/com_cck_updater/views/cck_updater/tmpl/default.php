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

$canDo  =   Helper_Admin::getActions();
$config	=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true );
$params	=	JComponentHelper::getParams( 'com_cck_updater' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
JFactory::getDocument()->addStyleDeclaration( '#system-message-container.j-toggle-main.span10{width: 100%;}' );
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
                $domain     =   'update.seblod.com';
                $uri        =   JUri::getInstance();
                if ( (int)$params->get( 'proxy', '0' ) ) {
                    $domain =   Helper_Admin::getProxy( $params, 'proxy_segment' );
                }
                $items      =   JCckDatabase::loadObjectList( 'SELECT * FROM #__updates WHERE extension_id != 0 AND detailsurl LIKE "%'.$domain.'%" ORDER BY FIELD(type,"component","package","plugin"), element ASC' );
                $lang       =   JFactory::getLanguage();
                $extensions =   array();
                if ( count( $items ) ) {
                    jimport( 'joomla.filesystem.file' );

                    foreach ( $items as $i=>$item ) {
                        if ( isset( $extensions[$item->name] ) ) {
                            continue;
                        }
                        $extensions[$item->name]    =   '';
                        $pos                        =   strpos( $item->name, 'pkg_' );

                        if ( $pos !== false && $pos == 0 ) {
                            $path   =   JPATH_ADMINISTRATOR.'/manifests/packages/'.$item->name.'.xml';

                            if ( is_file( $path ) ) {
                                $xml    =   JCckDev::fromXML( $path );

                                if ( isset( $xml->files ) ) {
                                    if ( isset( $xml->files->file ) && count( $xml->files->file ) ) {
                                        foreach ( $xml->files->file as $file ) {
                                            $file   =   (string)$file;
                                            if ( strpos( $file, '.zip' ) !== false ) {
                                                $file   =   substr( $file, 0, -4 );
                                            }
                                            $extensions[$file]  =   '';
                                        }
                                    }
                                }
                            }
                            $lang->load( $item->name.'.sys', JPATH_SITE, $lang->getTag(), true );
                        } else {
                            $lang->load( $item->name.'.sys', JPATH_ADMINISTRATOR, $lang->getTag(), true );    
                        }
                        $item->name =   JText::_( $item->name );
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
        <?php if ( $canDo->get( 'core.admin' ) ) { ?>
        <div class="seblod cck-padding-top-0 cck-overflow-visible">
            <ul class="adminformlist">
                <?php
                $attr   =   'onclick="if (document.adminForm.boxchecked.value==0){alert(\''.htmlspecialchars( JText::_( 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST' ) ).'\');}else{ Joomla.submitbutton(\'update\')}"';
                echo '<li class="btn-group dropup flt-right">'
                 .   JCckDev::getForm( 'more_updater_submit', '', $config, array( 'label'=>'Update Now', 'storage'=>'dev', 'attributes'=>$attr, 'css'=>( JCck::on() ? 'btn-primary' : 'inputbutton' ) ) );
                ?>
            </ul>
        </div>
        <?php } ?>
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