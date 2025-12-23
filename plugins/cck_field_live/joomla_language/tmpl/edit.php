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

use Joomla\CMS\Language\Text;

JCckDev::initScript( 'live', $this->item );
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( Text::_( 'COM_CCK_CONSTRUCTION' ), Text::_( 'PLG_CCK_FIELD_LIVE_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_languages', '', $config, array( 'label'=>'Language', 'selectlabel'=>'Current', 'storage_field'=>'language' ) );
		echo JCckDev::renderForm( 'core_bool', '', $config, array( 'label'=>'All Languages', 'defaultvalue'=>'0', 'options'=>'Yes=1||No=0', 'storage_field'=>'all' ) );
		?>
    </ul>
</div>