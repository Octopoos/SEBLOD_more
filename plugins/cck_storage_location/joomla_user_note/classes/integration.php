<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_user_note/joomla_user_note.php';

// Class
class plgCCK_Storage_LocationJoomla_User_Note_Integration extends plgCCK_Storage_LocationJoomla_User_Note
{
	// onCCK_Storage_LocationAfterDispatch
	public static function onCCK_Storage_LocationAfterDispatch( &$data, $uri = array() )
	{
		$return	=	'&return_o='.substr( $uri['option'], 4 ).'&return_v='.$uri['view'];
		
		if ( !$uri['layout'] ) {
			if ( $uri['view'] != 'notes' ) {
				return;
			}
			$do	=	$data['options']->get( 'add', 1 );
			$data['options']->set( 'add_alt_link', 'index.php?option=com_users&view=note&layout=edit&cck=1' );
			if ( $do == 1 ) {
				JCckDevIntegration::addModalBox( $data['options']->get( 'add_layout', 'icon' ), $return, $data['options'] );
			} elseif ( $do == 2 ) {
				JCckDevIntegration::addDropdown( 'form', $return, $data['options'] );
			}
		} elseif ( $uri['layout'] == 'edit' && !$uri['id'] ) {
			if ( $uri['view'] != 'note' ) {
				return;
			}
			if ( $data['options']->get( 'add_redirect', 1 ) ) {
				JCckDevIntegration::redirect( $data['options']->get( 'default_type' ), $return.'s&u_id='.JFactory::getApplication()->input->get( 'u_id' ) );
			}
		}
	}
	
	// onCCK_Storage_LocationAfterRender
	public static function onCCK_Storage_LocationAfterRender( &$buffer, &$data, $uri = array() )
	{
		if ( $uri['layout'] ) {
			return;
		}
		
		$data['doIntegration']	=	true;
		$data['return_view']	=	'notes';
		$data['search']			=	'#<a href="(.*)index.php\?option=com_users&amp;task=note.edit&amp;id=([0-9]*)"#';
	}
}
?>