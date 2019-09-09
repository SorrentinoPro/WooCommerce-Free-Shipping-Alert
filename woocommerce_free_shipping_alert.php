<?php
	/**
	* Plugin Name: WooCommerce Free Shipping Alert
	* Plugin URI:  https://github.com/sorrentinof/WooCommerce-Free-Shipping-Alert
	* Description: The Plugin will add new tab in wooCommerce setting page -> shipping Tab, called "Free Shipping Alerts". There you will be able to customize alerts to display after which a buyer will be in title of free shipping. The amount required, will be shown on shopping cart and checkout page urging customers to reach the target price. 
	* Version:     1.0
	* Author:      Sorrentino Francesco.
	* Author URI:  https://sorrentino.pro
	* License:     GPL-2.0+
	* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
	* Text Domain: pxw_woo_free_ship_alert
	*/
	
	if ( ! defined( 'ABSPATH' ) ){
		exit;
	}
	
	/**
	* class
	*/	
	require_once plugin_dir_path(__FILE__) . 'includes/class.php';
	
	/**
	* Styling: loading stylesheets and javascript for the plugin.*
	*/
	function pxw_woo_freeship_enqueue_style(){
		
		global $pagenow;
		//echo $ScreenBase = get_current_screen()->base;
		
		//Global Style
		wp_enqueue_style( 'pwx_wo_exship_style', plugins_url('assets/css/style.css', __FILE__) );
		
		//Core media script
		wp_enqueue_media();
		//Global Script
		wp_enqueue_script('pwx_wo_exship_script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), date('d-m-Y-H:i:s'), true );
		
	}
	
	/**
	* Styling: loading stylesheets and javascript for the FrontEnd.
	*/
    add_action( 'wp_enqueue_scripts', 'pxw_woo_freeship_enqueue_style' );
	
	/**
	* Actions perform on activation of plugin.
	*/
	function pxw_woo_freeship_install() {
		//Add options to DB. 
		add_option( 'wc_settings_tab_free_ship_alert_hide_delivery', WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_hide_delivery'));
		add_option( 'wc_settings_tab_free_ship_alert_checkout', WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_checkout'));
		add_option( 'wc_settings_tab_free_ship_alert_cart', WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_cart'));
		add_option( 'wc_settings_tab_free_ship_alert_text_on', WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_text_on'));
		add_option( 'wc_settings_tab_free_ship_alert_text_off', WC_Settings_Tab_Free_Ship_Alert::defaults_op('wc_settings_tab_free_ship_alert_text_off'));
	}
	register_activation_hook( __FILE__, 'pxw_woo_freeship_install' );

	/**
	* Actions perform on delete/removal/deactivation of plugin.
	*/
	function pxw_woo_freeship_uninstall() {
		//Delete options from DB. 
		delete_option( 'wc_settings_tab_free_ship_target' );
		delete_option( 'wc_settings_tab_free_ship_alert_hide_delivery' );
		delete_option( 'wc_settings_tab_free_ship_alert_checkout' );
		delete_option( 'wc_settings_tab_free_ship_alert_cart' );
		delete_option( 'wc_settings_tab_free_ship_alert_text_off' );
		delete_option( 'wc_settings_tab_free_ship_alert_text_on' );
	}
	register_uninstall_hook( __FILE__, 'pxw_woo_freeship_uninstall');
	register_deactivation_hook(__FILE__, 'pxw_woo_freeship_uninstall');