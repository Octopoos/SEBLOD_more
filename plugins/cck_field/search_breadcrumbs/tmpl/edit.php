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

// Init
JCckDev::forceStorage();
JCckDev::initScript( 'field', $this->item, array( 'hasOptions'=>true,
												  'customAttr'=>array( 'clear' ), 'customAttrLabel'=>Text::_( 'COM_CCK_CLEAR' ),
												  'fieldPicker'=>true ) );

$options	=	JCckDev::fromSTRING( $this->item->options );

// Set
$displayData	=	array(
						'config'=>$config,
						'form'=>array(
							array(
								'fields'=>array(
									JCckDev::renderForm( 'core_label', $this->item->label, $config ),
									JCckDev::renderForm( 'core_bool2', $this->item->bool2, $config, array( 'defaultvalue'=>1, 'label'=>'Clear All Toggle', 'options'=>'Hide=0||Show=1' ) ),
									JCckDev::renderForm( 'core_options', $options, $config, array( 'label'=>'Fields', 'rows'=>1 ), array( 'after'=>$this->item->init['fieldPicker'] ) )
								),
							),
							array(
								'fields'=>array(
									JCckDev::getForm( 'core_storage', $this->item->storage, $config )
								),
								'mode'=>'storage'
							)
						),
						'help'=>array(),
						'html'=>'',
						'item'=>$this->item,
						'script'=>$js
					);

echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.construction.cck_field.edit', $displayData );
?>