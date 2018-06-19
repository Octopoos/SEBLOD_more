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

// JCckContent
class JCckContentCck_Site extends JCckContent
{
	// preSave
	protected function preSave( $table_instance_name, &$data )
	{
		if ( $table_instance_name == 'base' && $this->isNew() ) {
			$config		=	array(
							'isNew'=>true,
							'storages'=>array( '#__cck_core_sites'=>array() ),
							'type'=>$this->getType()
						);
			$fields		=	array();

			if ( isset( $data['_'] ) ) {
				foreach ( $data['_'] as $k=>$v ) {
					$fields[$k]	=	new stdClass;
					
					$fields[$k]->value	=	$v;
				}

				unset( $data['_'] );
			}

			$groups	=	$this->_preSave_onCckPostBeforeStore( $config, $fields );

			if ( $groups ) {
				$data['groups']	=	$groups;
			}

			unset( $config );
			unset( $fields );
		}
	}

	// _preSave_onCckPostBeforeStore
	private function _preSave_onCckPostBeforeStore( $config, $fields )
	{
		if ( !JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			return '';
		}

		$event		=	'onCckPostBeforeStore';
		$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new JRegistry( $p->options );

					include_once JPATH_SITE.$p->scriptfile; /* Variables: $fields, $config, $user */
				}
			}
		}

		if ( isset( $config['storages']['#__cck_core_sites']['groups'] ) ) {
			return $config['storages']['#__cck_core_sites']['groups'];
		}
	}
}
?>