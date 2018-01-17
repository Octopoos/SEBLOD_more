<?php
/**
* @version          SEBLOD eCommerce 1.x
* @package          SEBLOD eCommerce Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_Ecommerce_Payment%class% extends JCckPluginPayment
{
	protected static $type		=	'%name%';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Ecommerce_PaymentPrepare
	public function onCCK_Ecommerce_PaymentPrepare( &$payment, &$order, &$config )
	{
		if ( self::$type != $payment->type ) {
			return;
		}

		$gateway			=	JCckEcommerce::getGateway( self::$type );

		// Prepare Credentials
		if ( $payment->live == 1 ) {
			$suffix		=	'';
		} else {
			$suffix		=	'_sandbox';
		}

		// Prepare Parameters
		/* TODO */

		// Prepare Payments
		$pay_key 			=	'';
		$pay_html 			=	'';
		$count				=	count( $payment->stores );
		$cart_definition	=	JCckEcommerce::getCartDefinition( $order->type );

		if ( $cart_definition->multistore && $count ) {
			/* TODO */
		} else {
			if ( !isset( $payment->stores[$payment->store_id] ) ) {
				return;
			}
			//	Get Amount
		}	

		// Get the Token
		$pay_key 	=	''; /* TODO */

		// Set
		$order->pay_html	=	$pay_html;
		$order->pay_key		=	$pay_key;

		// Cart
		JCckDatabase::execute( 'UPDATE #__cck_more_ecommerce_carts SET pay_key = "'.$pay_key.'" WHERE id = '.(int)$payment->cart_id );

	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Process

	// onCCK_Ecommerce_PaymentProcess
	public function onCCK_Ecommerce_PaymentProcess( &$payment, &$order, &$config )
	{
		if ( self::$type != $payment->type ) {
			return;
		}

        /*
        dump('payment:process');
        */

	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Validate
		
	// onCCK_Ecommerce_PaymentValidate
	public function onCCK_Ecommerce_PaymentValidate( &$config )
	{
        $raw            =   file_get_contents( 'php://input' );
        $data           =   $this->_prepareData( $raw, $config );

        if ( $data['response'] === true ) {
            $this->_processData( $data, $config );
        } else {
            // KO
        }
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
    // _prepareData
    protected function _prepareData( $raw, &$config )
    {
        $data   =   array(
                        'order'=>array(),
                        'order_state'=>0,
                        'payments'=>array(),
                        'post'=>array(),
                        'response'=>'',
                        'suffix'=>''
                    );

		$raw    =   explode( '&', $raw );

        if ( count( $raw ) ) {
            foreach ( $raw as $keyval ) {
                $keyval     =   explode ( '=', $keyval );
                if ( count( $keyval ) == 2 ) {
                    if ( ! in_array( $keyval[0], $remove ) ) {
                        $v                              =   urldecode( $keyval[1] );
                        if ( $keyval[0] == 'suffix' ) {
                            $data['suffix']             =   $v;
                            continue;
                        }
                        $data['post'][urldecode( $keyval[0] )] =   $v;
                    }
                }
            }
        }

		$data['response']   =	'??'; /* TODO */

        return $data;
    }

    // _processData
    protected function _processData( $data, &$config )
    {
        // Check if payKey is valid
        if ( $config['pay_key'] == '' ) {
            return;
        }

        // Check if request has already been processed      
        if ( (int)JCckDatabase::loadResult( 'SELECT state FROM #__cck_more_ecommerce_orders WHERE pay_key = "'.$config['pay_key'].'"' ) >= 2 ) {
            return;
        }

        $gateway    =   JCckEcommerce::getGateway( self::$type );
        $success    =   false;

        //	Get Status
        $status     =   '??'; /* TODO */

        switch ( $status ) {
        	case 'a':
	            $data['order_state']    =   $gateway->options->get( 'status_failure', '-12' );
        		break;
        	case 'a':
                $data['order_state']    =   $gateway->options->get( 'status_other', '-11' );
        		break;
        	case 'a':
                $data['order_state']    =   $gateway->options->get( 'status_success_no_warranty', '1' );
        		break;       	
        	default:
        		$data['order_state']    =   $gateway->options->get( 'status_success', '8' );
        		break;
        }

        // Set Order Status & Triggers "onCckPaymentSuccess" if success
        parent::g_onCCK_PaymentValidate( $data, $success, $config );
    }
	//
}
?>