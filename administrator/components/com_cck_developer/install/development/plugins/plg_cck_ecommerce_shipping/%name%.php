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

// Plugin
class plgCCK_Ecommerce_Shipping%class% extends JCckPluginShipping
{
	protected static $type		=	'%name%';
	protected static $account	= 	array(
										'number'	=>'',
										'password'	=>''
									);
		
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

	// onCCK_Ecommerce_ShippingPrepare
	public function onCCK_Ecommerce_ShippingPrepare( &$shipping, &$order, &$config )
	{
		if ( self::$type != $shipping->type ) {
			return;
		}

		self::$account['number'] 	= 	$shipping_method->options->get( 'account_number' );
		self::$account['password'] 	= 	$shipping_method->options->get( 'account_password' );

		JFactory::getLanguage()->load( 'plg_cck_ecommerce_shipping_'.self::$type, JPATH_ADMINISTRATOR, null, false, true );
		
		$shipping_method	=	JCckEcommerce::getShippingMethod( $shipping->type );
		if ( $payment->live == 1 ) {
			$mode		=	'';
			$suffix		=	'';
		} else {
			$mode		=	'';
			$suffix		=	'';
		}

		//	Get Params
		$params 	=	array();

		//	Set
		$order->ship_html 	= 	'';
		$order->ship_return	= 	json_encode( $params );
		$order->weight		=	$params['weight'];
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Process
	
	// onCCK_Ecommerce_ShippingProcess
	public function onCCK_Ecommerce_ShippingProcess( &$shipping, &$order, &$config )
	{
		if ( self::$type != $shipping->type ) {
			return;
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	//
}
?>