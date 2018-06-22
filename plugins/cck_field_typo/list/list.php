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

// Plugin
class plgCCK_Field_TypoList extends JCckPluginTypo
{
	protected static $type	=	'list';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
		
	// onCCK_Field_TypoPrepareContent
	public function onCCK_Field_TypoPrepareContent( &$field, $target = 'value', &$config = array() )
	{		
		if ( self::$type != $field->typo ) {
			return;
		}
		
		// Prepare
		$typo	=	parent::g_getTypo( $field->typo_options );
		$value	=	$field->$target;
		
		// Set
		if ( $field->typo_label ) {
			$field->label	=	self::_typo( $typo, $field, $field->label, $config );
		}
		$field->typo		=	self::_typo( $typo, $field, $value, $config );
	}
	
	// _typo
	protected static function _typo( $typo, $field, $value, &$config = array() )
	{
		$class		=	$typo->get( 'class', '' );
		$html		=	'';
		$tag		=	'ul';
		
		if ( isset( $field->values ) && count( $field->values ) ) {
			$target	=	$field->typo_target;
			foreach ( $field->values as $k=>$v ) {
				$v	=	parent::g_hasLink( $v, $typo, $v->$target );
				if ( $v != '' ) {
					$html	.=	'<li>'.$v.'</li>';
				}
			}
			if ( $html != '' ) {
				$html	=	'<'.$tag.' class="'.$class.'">'.$html.'</'.$tag.'>';	
			}
		} else if ( is_array( $value ) && count( $value ) ) {
			foreach( $value as $v ) {
				if ( is_array( $v ) ) {
					$v	=	current( $v );
				}
				if ( is_object( $v ) ) {
					$v2		=	JCck::callFunc( 'plgCCK_Field'.$v->type, 'onCCK_FieldRenderContent', $v );
					if ( $v2 != '' ) {
						$html	.=	'<li>'.$v2.'</li>';
					}
				}
			}
			if ( $html != '' ) {
				$html	=	'<'.$tag.' class="'.$class.'">'.$html.'</'.$tag.'>';	
			}
		} elseif ( $field->divider ) {
			$value	=	explode( $field->divider, $value );
			if ( is_array( $value ) && count( $value ) ) {
				foreach( $value as $v ) {
					if ( $v != '' ) {
						$html	.=	'<li>'.$v.'</li>';
					}
				}
				
			}
			if ( $html != '' ) {
				$html	=	'<'.$tag.' class="'.$class.'">'.$html.'</'.$tag.'>';
			}
		}
		
		return $html;
	}
}
?>