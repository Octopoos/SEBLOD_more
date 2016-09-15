<?php
/**
* @version 			SEBLOD Developer 1.x
* @package			SEBLOD Developer Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Script
class com_cck_developerInstallerScript extends JCckInstallerScriptComponent
{
	protected $cck;
	protected $cck_title	=	'Developer';
	protected $cck_name		=	'cck_developer';
	
	// install
	function install( $parent )
	{
		parent::install( $parent );
	}
	
	// uninstall
	function uninstall( $parent )
	{
		parent::uninstall( $parent );
	}
	
	// update
	function update( $parent )
	{
		parent::update( $parent );
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		parent::preflight( $type, $parent );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		$db		=	JFactory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck"' );
		$res	=	$db->loadResult();
		$reg	=	new JRegistry;
		$reg->loadString( $res );
		$v		=	substr( $reg->get( 'version', '2.0.0' ), 0, 5 );
		
		if ( version_compare( $v, '2.0.5', '<' ) ) {
			$parent->getParent()->message	=	'Download SEBLOD 3.x here: <a target="_blank" href="https://www.seblod.com">www.seblod.com</a>';
			$parent->getParent()->abort();
			
			$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "com_'.$this->cck_name.'"' );
			$eid	=	$db->loadResult();
			$db->setQuery( 'SELECT update_site_id FROM #__update_sites_extensions' );
			$uid	=	$db->loadObjectList();
			
			$db->setQuery( 'DELETE a.* FROM #__update_sites AS a WHERE a.update_site_id = '.(int)$uid );
			$db->execute();
			$db->setQuery( 'DELETE a.* FROM #__update_sites_extensions AS a WHERE a.extension_id = '.(int)$eid );
			$db->execute();
			$db->setQuery( 'DELETE a.* FROM #__menu AS a WHERE a.component_id = '.(int)$eid );
			$db->execute();
			$db->setQuery( 'DELETE a.* FROM #__extensions AS a WHERE a.extension_id = '.(int)$eid );
			$db->execute();
			
			JFactory::getApplication()->enqueueMessage( 'SEBLOD 2.1.0 (or >) is required.', 'error' );
		} else {
			CCK_Install::manageAddon( $type, array( 'title'=>$this->cck_title, 'name'=>$this->cck_name ) );
			CCK_Install::import( $parent, 'admin/install', $this->cck );
			
			if ( $type == 'install' ) {
				$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1}}';
				$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "'.(string)$this->cck->element.'"';
				$db->setQuery( $query );
				$db->execute();
			}
		}
	}
}
?>