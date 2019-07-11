<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/include.php';

// Helper
class Helper_Include extends CommonHelper_Include
{
	// addDependencies
	public static function addDependencies( $view, $layout, $tmpl = '' )
	{		
		$doc	=	JFactory::getDocument();
		$script	=	( $tmpl == 'ajax' ) ? false : true;
		
		if ( $script === true ) {
			JHtml::_( 'bootstrap.tooltip' );
			// JHtml::_( 'formbehavior.chosen', 'select:not(.no-chosen)' );
			
			JCck::loadjQuery( true, true, array( 'cck.dev-3.18.0.min.js', 'jquery.ui.effects.min.js', 'jquery.json.min.js' ) );
		}
		
		$paths	=	array( 'media/cck/css/definitions/all.css' );
		Helper_Include::addStyleSheets( true, $paths );
		
		$doc->addStyleDeclaration( 'div.loading {position:absolute; right:10px; top:26px;} div.seblod div.legend{margin-left:0!important}' );
	}
}
?>