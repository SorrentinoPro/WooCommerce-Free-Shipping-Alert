<?php
	class WC_Settings_Tab_Free_Ship_Alert{
		/**
		* Bootstraps the class and hooks required actions & filters.
		*
		*/
		public static function init() {
			//backend
			add_filter( 'woocommerce_get_sections_shipping',  __CLASS__ . '::pxw_woo_free_ship_alert_add_section' );
			add_filter( 'woocommerce_get_settings_shipping',  __CLASS__ . '::pxw_woo_free_ship_alert_settings', 10, 2 );
			
			//frontend
			if('yes' === get_option('wc_settings_tab_free_ship_alert_hide_delivery') ){
				add_filter( 'woocommerce_package_rates', __CLASS__ . '::pxw_woo_free_ship_alert_hide_methods', 100 );
			}
			if('yes' === get_option( 'wc_settings_tab_free_ship_alert_checkout' )){
				add_action('woocommerce_checkout_before_order_review', __CLASS__ . '::pxw_woo_free_ship_alert_box', 10, 0);
			}
			if('yes' === get_option( 'wc_settings_tab_free_ship_alert_cart' )){
				add_action('woocommerce_cart_totals_before_shipping', __CLASS__ . '::pxw_woo_free_ship_alert_box');
			}
		}
		/**
		* Create the section beneath the shipping tab
		**/
		public static function pxw_woo_free_ship_alert_add_section( $sections ) {
			$sections['pxw_woo_free_ship_alert'] = __( 'Free Shipping Alerts', 'pxw_woo_free_ship_alert' );
			return $sections;
		}
		
		/**
		* Add settings to the specific section we created before
		*/
		public static function pxw_woo_free_ship_alert_settings( $settings, $current_section ) {
			/**
			* Check the current section is what we want
			*
			* @param array $rates Array of settings for the free shipping tab.
			* @return array
			**/
			if ( 'pxw_woo_free_ship_alert' === $current_section ) {
				$settings_field = array();
				// Add Title to the Settings
				$settings_field[] = array( 
				'name' => __( 'Free Shipping Alerts Settings', 'pxw_woo_free_ship_alert' ), 
				'type' => 'title', 'desc' => __( 'The following options are used to configure Alerts position.', 
				'pxw_woo_free_ship_alert' ), 
				'id' => 'pxw_woo_free_ship_alert' 
				);
				// Hide other delivery method on freeshipping
				$settings_field[] = array(
				'name' => __( 'Hide other Delivery Methods', 'pxw_woo_free_ship_alert' ),
				'desc_tip' => __( 'Hide other Delivery Methods if free shipping become available.', 'pxw_woo_free_ship_alert' ),
                'type' => 'checkbox',
                'id'   => 'wc_settings_tab_free_ship_alert_hide_delivery',
				'default' => WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_hide_delivery')
				);
				// Alert Checkout Page
				$settings_field[] = array(
				'name' => __( 'Alert on Checkout', 'pxw_woo_free_ship_alert' ),
				'desc_tip' => __( 'Display an alert text on checkout page.', 'pxw_woo_free_ship_alert' ),
                'type' => 'checkbox',
                'id'   => 'wc_settings_tab_free_ship_alert_checkout',
				'default' => WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_checkout')
				);
				// Alert Cart Page
				$settings_field[] = array(
				'name' => __( 'Alert on Cart', 'pxw_woo_free_ship_alert' ),
				'desc_tip' => __( 'Display an alert text on shopping cart page.', 'pxw_woo_free_ship_alert' ),
                'type' => 'checkbox',
                'id'   => 'wc_settings_tab_free_ship_alert_cart',
				'default' => WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_cart')
				);
				// Alert On Target Text
				$settings_field[] = array(
				'name' => __( 'On Target Text', 'pxw_woo_free_ship_alert' ),
				'desc_tip' => __( 'Personalize a text to display while ON Target.', 'pxw_woo_free_ship_alert' ),
                'type' => 'textarea',
                'id'   => 'wc_settings_tab_free_ship_alert_text_on',
				'default' => WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_text_on')
				);
				// Alert Off Target Text
				$settings_field[] = array(
				'name' => __( 'Off Target Text', 'pxw_woo_free_ship_alert' ),
				'desc_tip' => __( 'Personalize a text to display while Off Target.', 'pxw_woo_free_ship_alert' ),
				'desc' => __( '<b>Note!!</b> the Target to be reached will be displayed after this text, keep in mind that while customizing this field.', 'pxw_woo_free_ship_alert' ),
                'type' => 'textarea',
                'id'   => 'wc_settings_tab_free_ship_alert_text_off',
				'default' => WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_text_off')
				);
				$settings_field[] = array( 'type' => 'sectionend', 'id' => 'pxw_woo_free_ship_alert' );
				return $settings_field;
				
			} else {
				/**
				* If not, return the standard settings
				**/
				return $settings;
			}
		}
		/**
		* Defaults options
		**/
		public static function defaults_op($op){
			$defaults_op = array(); 
			$defaults_op['wc_settings_tab_free_ship_alert_hide_delivery'] = 'yes';
			$defaults_op['wc_settings_tab_free_ship_alert_checkout'] = 'yes';
			$defaults_op['wc_settings_tab_free_ship_alert_cart'] = 'yes';
			$defaults_op['wc_settings_tab_free_ship_alert_text_on'] =  __('Great!  Free Shipping NOW Available!', 'pxw_woo_free_ship_alert') ;
			$defaults_op['wc_settings_tab_free_ship_alert_text_off'] =  __('Free Shipping, will be available after you add more items in your cart, the amount left:', 'pxw_woo_free_ship_alert') ;
			
			return $defaults_op[$op];
		}
		
		/**
		* Display alert box
		**/
		public static function pxw_woo_free_ship_alert_box(){
			global $wpdb,$table_prefix;
			
			// Here we get the value of the order min amount
			$min_amount = $wpdb->get_col("SELECT `option_value` FROM `".$table_prefix."options` WHERE `option_name` LIKE 'woocommerce_free_shipping_%_settings'");
			$min_amount = explode (";", $min_amount[0]);
			$min_amount = explode ('"', $min_amount[5]);
			$min_amount = $min_amount[1];
			
			echo '<div class="pxw_woo_free_ship_alert_box">';
			echo '<input type="hidden" class="pxw_woo_free_ship_alert_target"value="' . $min_amount . '"/>';
			echo '<input type="hidden" class="pxw_woo_free_ship_alert_target_currency"value="'.get_woocommerce_currency_symbol().'"/>';
			echo '<p class="woocommerce-info ship_off_target">'.
					'<span class="pxw_woo_free_ship_alert_top">'.
						'<span class="dashicons dashicons-warning" style="color: #fdfd00;font-size: 30px;width: 30px;"></span>Free Shipping Target: '.get_woocommerce_currency_symbol().$min_amount.
					'</span> <br>'.
					get_option( 'wc_settings_tab_free_ship_alert_text_off').
				'</p>';
			echo '<p class="woocommerce-info ship_on_target"><span class="dashicons dashicons-yes-alt" style="color: #84ff84;font-size: 30px;width: 30px;"></span>'.get_option( 'wc_settings_tab_free_ship_alert_text_on').'</p>';
			echo '</div>';
		}
		
		
		/**
		* Hide shipping rates when free shipping is available.
		* Updated to support WooCommerce 2.6 Shipping Zones.
		*
		* @param array $rates Array of rates found for the package.
		* @return array
		*/
		public static function pxw_woo_free_ship_alert_hide_methods( $rates ) {
			$free = array();
			foreach ( $rates as $rate_id => $rate ) {
				if ( 'free_shipping' === $rate->method_id ) {
					$free[ $rate_id ] = $rate;
					break;
				}
			}
			return ! empty( $free ) ? $free : $rates;
		}
		
		
	}
	WC_Settings_Tab_Free_Ship_Alert::init();