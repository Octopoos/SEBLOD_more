<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldAddress_To_Coordinates extends JCckPluginField
{
	protected static $type		=	'address_to_coordinates';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	htmlspecialchars( $value );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( $field->bool == 0 ) {
			$form	=	'';
		} else {
			$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
			$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
			$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
			$form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		}

		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->bool == 1 ) {
				$options2	=	new JRegistry( $field->options2 );

				if ( $field->state ) {
					parent::g_addProcess( 'beforeRenderForm', self::$type, $config, array( 'name'=>$field->name, 'id'=>$id, 'bypass'=>'0'/* $options2->get( 'bypass', '0' ) */, 'lat'=>$options2->get( 'latitude' ), 'lng'=>$options2->get( 'longitude' ), 'postal_code'=>$options2->get( 'postal_code' ), 'city'=>$options2->get( 'city' ), 'country'=>$options2->get( 'country' ), 'country_type'=>$options2->get( 'country_type', '0' ), 'r_type'=>$options2->get( 'types' ), 'r_country'=>$options2->get( 'restrictions_country' ) ) );
				}
			}
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		
		if ( $field->bool == 0 ) {
			$fieldnames	=	explode( '||', $field->options );
			$options2	=	new JRegistry( $field->options2 );
			$latitude	=	$options2->get( 'latitude' );
			$longitude	=	$options2->get( 'longitude' );
			parent::g_addProcess( 'beforeStore', self::$type, $config, array( 'name'=>$name, 'latitude'=>$latitude, 'longitude'=>$longitude, 'fieldnames'=>$fieldnames ) );
		}

		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldBeforeStore
	public static function onCCK_FieldBeforeStore( $process, &$fields, &$storages, &$config = array() )
	{
		$map		=	new JGoogleEmbedMaps();
		$address	=	'';
		if ( count( $process['fieldnames'] ) && $process['latitude'] && $process['longitude'] ) {
			foreach ( $process['fieldnames'] as $name ) {
				$address	.=	$fields[$name]->value.' ';
			}
			$address		=	trim( $address );
			if ( $address ) {
				$geocode	=	$map->geocodeAddress( $address );
				$lat		=	$process['latitude'];
				$lng		=	$process['longitude'];
				$fields[$lat]->value	=	$geocode['geometry']['location']['lat'];
				$fields[$lng]->value	=	$geocode['geometry']['location']['lng'];
				$config['storages'][$fields[$lat]->storage_table][$fields[$lat]->storage_field]	=	$fields[$lat]->value;
				$config['storages'][$fields[$lng]->storage_table][$fields[$lng]->storage_field]	=	$fields[$lng]->value;
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// onCCK_FieldBeforeRenderForm
	public static function onCCK_FieldBeforeRenderForm( $process, &$fields, &$storages, &$config = array() )
	{
		$name	=	$process['name'];

		if ( !$fields[$name]->state ) {
			return;
		}
		
		self::_addScripts( $process['id'], array(
									'bypass'=>$process['bypass'],
									'lat'=>$process['lat'],
									'lng'=>$process['lng'],
									'postal_code'=>$process['postal_code'],
									'city'=>$process['city'],
									'country'=>$process['country'],
									'country_type'=>$process['country_type'],
									'r_type'=>$process['r_type'],
									'r_country'=>$process['r_country']
								), $config );
	}

	// _addScripts
	protected static function _addScripts( $id, $params = array(), &$config = array() )
	{
		static $loaded	=	0;

		if ( $params['bypass'] == '0' ) {
			$params['bypass'] == '-1';
		}
		$doc	=	JFactory::getDocument();
		$opts	=	'types: ['.( $params['r_type'] ? '"'.$params['r_type'].'"' : '' ).'], '
				.	'componentRestrictions: {'.( $params['r_country'] ? 'country: "'.$params['r_country'].'"' : '' ).'}';
		$js		=	'
					jQuery(document).ready(function($){
						var $ac = $("#"+"'.$id.'");
						var $el = document.getElementById("'.$id.'");
						var $lat = $("#"+"'.$params['lat'].'");
						var $lat2 = $("#_"+"'.$params['lat'].'");
						var $lng = $("#"+"'.$params['lng'].'");
						var $lng2 = $("#_"+"'.$params['lng'].'");
						var $country = $("#"+"'.@$params['country'].'");
						var $city = $("#"+"'.@$params['city'].'");
						var $zipcode = $("#"+"'.@$params['postal_code'].'");
						
						var autocomplete = new google.maps.places.Autocomplete($el,{'.$opts.'});
						var country_target = "'.( $params['country_type'] == '1' ? 'long_name' : 'short_name' ).'";
						
						google.maps.event.addListener(autocomplete, "place_changed", function() {
							var coor = '.$params['bypass'].';
							var place = autocomplete.getPlace();
							if (!place.geometry) {
								return;
							}
							var address = "";
							if (place.address_components) {
								var len = place.address_components.length;
								address = [
									(place.address_components[0] && place.address_components[0].short_name || ""),
									(place.address_components[1] && place.address_components[1].short_name || ""),
									(place.address_components[2] && place.address_components[2].short_name || ""),
									(place.address_components[3] && place.address_components[3].short_name || ""),
									(place.address_components[4] && place.address_components[4].short_name || "")
								].join(" ");
								var components_by_type = {};
								for (var i = 0; i < place.address_components.length; i++) {
									var c = place.address_components[i];
									components_by_type[c.types[0]] = c;
								}
								if ($zipcode.length) {
									$zipcode.val(components_by_type["postal_code"].long_name);
								}
								if ($city.length) {
									$city.val(components_by_type["locality"].long_name);
								}
								if ($country.length) {
									$country.val(components_by_type["country"][country_target]);
								}
							}
							if (place.geometry.location) {
								var latitude = place.geometry.location.lat();
								var longitude = place.geometry.location.lng();
							} else {
								var latitude = "";
								var longitude = "";
							}
							$lat.val(latitude); if ($lat2.length){ $lat2.text(latitude); }
							$lng.val(longitude); if ($lng2.length){ $lng2.text(longitude); }
						});
					});
					';
		$doc->addScriptDeclaration( $js );

		if ( $loaded ) {
			return;
		}
		$lib	=	JUri::getInstance()->getScheme().'://maps.googleapis.com/maps/api/js?language='.substr( JFactory::getLanguage()->getTag(), 0, 2 ).'&libraries=places';
		$loaded	=	1;
		
		if ( method_exists( 'JCckDev', 'addScript' ) ) {
			JCckDev::addScript( $lib );
		} else {
			$doc->addScript( $lib );
		}		
	}
}
?>