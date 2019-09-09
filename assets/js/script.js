jQuery(document).ready(function($){
	var free_ship_target 		= parseFloat($('.pxw_woo_free_ship_alert_target').val());
	var free_ship_total_price 	= parseFloat($('.woocommerce-Price-amount.amount')[0].textContent.replace(/[^0-9.]/gi, ''));
	
	function check_free_shipping(free_ship_target ,free_ship_total_price){
		if(free_ship_total_price >= free_ship_target ){
			$('.pxw_woo_free_ship_alert_box').addClass('on');
			$('.pxw_woo_free_ship_alert_box').removeClass('off');
		
			$('.ship_on_target').addClass('active');
			$('.ship_off_target').removeClass('active');
		}else{
			$('.pxw_woo_free_ship_alert_box').addClass('off');
			$('.pxw_woo_free_ship_alert_box').removeClass('on');
			
			$('.ship_off_target').addClass('active');
			$('.ship_on_target').removeClass('active');
			
			var priceLeft = free_ship_target - free_ship_total_price;
			var currency = $('.pxw_woo_free_ship_alert_target_currency').val();
			$('.ship_off_target').append(' '+ currency + '' + priceLeft );
		}
	}
	
	check_free_shipping(free_ship_target ,free_ship_total_price);
	
	
	$(document).ajaxComplete(function(event, jqXHR, settings) {
		if ('/al/?wc-ajax=get_refreshed_fragments' === settings.url ){
			    var free_ship_target 		= parseFloat($('.pxw_woo_free_ship_alert_target').val());
				var free_ship_total_price 	= parseFloat($('.woocommerce-Price-amount.amount')[0].textContent.replace(/[^0-9.]/gi, ''));
				check_free_shipping(free_ship_target ,free_ship_total_price);
		}
	});
	
});