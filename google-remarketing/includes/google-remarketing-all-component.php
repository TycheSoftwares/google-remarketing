<?php
/**
 * It will Add all the Boilerplate component when we activate the plugin.
 * @author  Tyche Softwares
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WP_Google_Remarketing_Component' ) ) {
	/**
	 * It will Add all the Boilerplate component when we activate the plugin.
	 * 
	 */
	class WP_Google_Remarketing_Component {

        
	    
		/**
		 * It will Add all the Boilerplate component when we activate the plugin.
		 */
		public function __construct() {
            
			$is_admin = is_admin();

			if ( true === $is_admin ) {

                require_once( "component/tracking-data/ts-tracking.php" );
                require_once( "component/deactivate-survey-popup/class-ts-deactivation.php" );

                require_once( "component/welcome-page/ts-welcome.php" );
                // require_once( "component/faq-support/ts-faq-support.php" );
                require_once( "component/pro-notices-in-lite/ts-pro-notices.php" );
                
                $wp_google_remarketing_plugin_name          = self::ts_get_plugin_name();;
                $wp_google_remarketing_locale               = self::ts_get_plugin_locale();

                $wp_google_remarketing_file_name            = 'google-remarketing/google-remarketing.php';
                $wp_google_remarketing_plugin_prefix        = 'wp_google_remarketing';
                $wp_google_remarketing_lite_plugin_prefix   = 'wp_google_remarketing';
                $wp_google_remarketing_plugin_folder_name   = 'google-remarketing/';
                $wp_google_remarketing_plugin_dir_name      = dirname ( untrailingslashit( plugin_dir_path ( __FILE__ ) ) ) . '/google-remarketing.php' ;
                $wp_google_remarketing_plugin_url           = dirname ( untrailingslashit( plugins_url( '/', __FILE__ ) ) );

                $wp_google_remarketing_get_previous_version = get_option( 'wp_google_remarketing_version', '1' );

                $wp_google_remarketing_blog_post_link       = 'https://www.tychesoftwares.com/docs/docs/google-remarketing/usage-tracking/';

                $wp_google_remarketing_plugins_page         = '';
                $wp_google_remarketing_plugin_slug          = '';
                $wp_google_remarketing_pro_file_name        = '';

                $wp_google_remarketing_settings_page        = '';

                new WP_Google_Remarketing_TS_tracking ( $wp_google_remarketing_plugin_prefix, $wp_google_remarketing_plugin_name, $wp_google_remarketing_blog_post_link, $wp_google_remarketing_locale, $wp_google_remarketing_plugin_url, $wp_google_remarketing_settings_page, '', '', '', $wp_google_remarketing_file_name );
                new WP_Google_Remarketing_TS_Tracker ( $wp_google_remarketing_plugin_prefix, $wp_google_remarketing_plugin_name );

                $wp_google_remarketing_deativate = new WP_Google_Remarketing_TS_deactivate;
                $wp_google_remarketing_deativate->init ( $wp_google_remarketing_file_name, $wp_google_remarketing_plugin_name );

                $user = wp_get_current_user();
                
                if ( in_array( 'administrator', (array) $user->roles ) ) {
                
                    new WP_Google_Remarketing_TS_Welcome ( $wp_google_remarketing_plugin_name, $wp_google_remarketing_plugin_prefix, $wp_google_remarketing_locale, $wp_google_remarketing_plugin_folder_name, $wp_google_remarketing_plugin_dir_name, $wp_google_remarketing_get_previous_version );
                }
                // $ts_pro_wp_google_remarketing = self::wp_google_remarketing_get_faq ();
                // new wp_google_remarketing_TS_Faq_Support( $wp_google_remarketing_plugin_name, $wp_google_remarketing_plugin_prefix, $wp_google_remarketing_plugins_page, $wp_google_remarketing_locale, $wp_google_remarketing_plugin_folder_name, $wp_google_remarketing_plugin_slug, $ts_pro_wp_google_remarketing, '', $wp_google_remarketing_file_name );
                
                if ( in_array('woocommerce/woocommerce.php', get_option('active_plugins') ) ) {
                    $ts_pro_notices = self::wp_google_remarketing_get_notice_text ();
                    new WP_Google_Remarketing_ts_pro_notices( $wp_google_remarketing_plugin_name, $wp_google_remarketing_lite_plugin_prefix, $wp_google_remarketing_plugin_prefix, $ts_pro_notices, $wp_google_remarketing_file_name, $wp_google_remarketing_pro_file_name );
                }

            }
        }

         /**
         * It will retrun the plguin name.
         * @return string $ts_plugin_name Name of the plugin
         */
		public static function ts_get_plugin_name () {
            $wpgr_plugin_dir = dirname ( dirname ( __FILE__ ) ) ;
            $wpgr_plugin_dir .= '/google-remarketing.php';
           
            $ts_plugin_name = '';
            $plugin_data = get_file_data( $wpgr_plugin_dir, array( 'name' => 'Plugin Name' ) );
            if ( ! empty( $plugin_data['name'] ) ) {
                $ts_plugin_name = $plugin_data[ 'name' ];
            }
            return $ts_plugin_name;
        }

        /**
         * It will retrun the Plugin text Domain
         * @return string $ts_plugin_domain Name of the Plugin domain
         */
        public static function ts_get_plugin_locale () {
            $wpgr_plugin_dir =  dirname( dirname ( __FILE__ ) ) ;
            $wpgr_plugin_dir .= '/google-remarketing.php';

            $ts_plugin_domain = '';
            $plugin_data = get_file_data( $wpgr_plugin_dir, array( 'domain' => 'Text Domain' ) );
            if ( ! empty( $plugin_data['domain'] ) ) {
                $ts_plugin_domain = $plugin_data[ 'domain' ];
            }
            return $ts_plugin_domain;
        }
        
        /**
         * It will Display the notices in the admin dashboard for the pro vesion of the plugin.
         * @return array $ts_pro_notices All text of the notices
         */
        public static function wp_google_remarketing_get_notice_text () {
            $ts_pro_notices = array();

            $wp_google_remarketing_locale               = self::ts_get_plugin_locale();

            $message_first = wp_kses_post ( __( 'Thank you for using WooCommerce Print Invoice & Delivery Note plugin! Now make your deliveries more accurate by allowing customers to select their preferred delivery date & time from Product Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpnotice&utm_medium=first&utm_campaign=WPExportMenus">Get it now!</a></strong>', $wp_google_remarketing_locale ) );  

            $message_two = wp_kses_post ( __( 'Never login to your admin to check your deliveries by syncing the delivery dates to the Google Calendar from Product Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=WPExportMenus">Get it now!</a></strong>', $wp_google_remarketing_locale ) );

            $message_three = wp_kses_post ( __( 'You can now view all your deliveries in list view or in calendar view from Product Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=WPExportMenus">Get it now!</a></strong>.', $wp_google_remarketing_locale ) );

            $message_four = wp_kses_post ( __( 'Allow your customers to pay extra for delivery for certain Weekdays/Dates from Product Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=WPExportMenus">Have it now!</a></strong>.', $wp_google_remarketing_locale ) );

            $message_five = wp_kses_post ( __( 'Customers can now edit the Delivery date & time on cart and checkout page or they can reschedule the deliveries for the already placed orders from Product Delivery Date Pro for WooCommerce. <strong><a target="_blank" href= "https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/checkout?edd_action=add_to_cart&download_id=16&utm_source=wpnotice&utm_medium=first&utm_campaign=WPExportMenus">Have it now!</a></strong>.', $wp_google_remarketing_locale ) );

		// message six
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpnotice&utm_medium=sixth&utm_campaign=WPExportMenus';
            $message_six = wp_kses_post ( __( 'Boost your sales by recovering up to 60% of the abandoned carts with our Abandoned Cart Pro for WooCommerce plugin. You can capture customer email addresses right when they click the Add To Cart button. <strong><a target="_blank" href= "'.$_link.'">Grab your copy of Abandon Cart Pro plugin now</a></strong>.', $wp_google_remarketing_locale ) );
            
            $wp_google_remarketing_message_six = array ( 'message' => $message_six, 'plugin_link' => 'woocommerce-abandon-cart-pro/woocommerce-ac.php' );
		// message seven
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpnotice&utm_medium=seventh&utm_campaign=WPExportMenus';
            $message_seven = wp_kses_post ( __( 'Don\'t loose your sales to abandoned carts. Use our Abandon Cart Pro plugin & start recovering your lost sales in less then 60 seconds.<br> 
            <strong><a target="_blank" href= "'.$_link.'">Get it now!</a></strong>', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_seven = array ( 'message' => $message_seven, 'plugin_link' => 'woocommerce-abandon-cart-pro/woocommerce-ac.php' );
        
        // message eight
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpnotice&utm_medium=eight&utm_campaign=WPExportMenus';
            $message_eight = wp_kses_post ( __( 'Send Abandoned Cart reminders that actually convert. Take advantage of our fully responsive email templates designed specially with an intent to trigger conversion. <br><strong><a target="_blank" href= "'.$_link.'">Grab your copy now!</a></strong>', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_eight = array ( 'message' => $message_eight, 'plugin_link' => 'woocommerce-abandon-cart-pro/woocommerce-ac.php' );

		// message nine
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-abandoned-cart-pro/?utm_source=wpnotice&utm_medium=ninth&utm_campaign=WPExportMenus';
            $message_nine = wp_kses_post ( __( 'Increase your store sales by recovering your abandoned carts for just $119. No profit sharing, no monthly fees. Our Abandoned Cart Pro plugin comes with a 30 day money back guarantee as well. :). Use coupon code ACPRO20 & save $24!<br>
            <strong><a target="_blank" href= "'.$_link.'">Purchase now</a></strong>', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_nine = array ( 'message' => $message_nine, 'plugin_link' => 'woocommerce-abandon-cart-pro/woocommerce-ac.php' );
            
		// message ten  
	        $_link = 'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/?utm_source=wpnotice&utm_medium=tenth&utm_campaign=WPExportMenus';
            $message_ten = wp_kses_post ( __( 'Allow your customers to select the Delivery Date & Time on the Checkout Page using our Order Delivery Date Pro for WooCommerce Plugin. <br> 
            <strong><a target="_blank" href= "'.$_link.'">Shop now</a></strong> & be one of the 20 customers to get 20% discount on the plugin price. Use the code "ORDPRO20". Hurry!!', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_ten = array ( 'message' => $message_ten, 'plugin_link' => 'order-delivery-date/order_delivery_date.php' );

		// message eleven
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin/?utm_source=wpnotice&utm_medium=eleven&utm_campaign=WPExportMenus';
            $message_eleven = wp_kses_post ( __( ' Allow your customers to book an appointment or rent an apartment with our Booking and Appointment for WooCommerce plugin. You can also sell your product as a resource or integrate with a few Vendor plugins. <br>Shop now & Save 20% on the plugin with the code "BKAP20". Only for first 20 customers. <strong><a target="_blank" href= "'.$_link.'">Have it now!</a></strong>', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_eleven = array ( 'message' => $message_eleven, 'plugin_link' => 'woocommerce-booking/woocommerce-booking.php' );

		// message 12
            $_link = 'https://www.tychesoftwares.com/store/premium-plugins/deposits-for-woocommerce/?utm_source=wpnotice&utm_medium=twelve&utm_campaign=WPExportMenus';
            $message_twelve = wp_kses_post ( __( ' Allow your customers to pay deposits on products using our Deposits for WooCommerce plugin.<br>
            <strong><a target="_blank" href= "'.$_link.'">Purchase now</a></strong> & Grab 20% discount with the code "DFWP20". The discount code is valid only for the first 20 customers.', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_twelve = array ( 'message' => $message_twelve, 'plugin_link' => 'woocommerce-deposits/deposits-for-woocommerce.php' );

		// message 13 
	        $_link = 'https://www.tychesoftwares.com/store/premium-plugins/product-delivery-date-pro-for-woocommerce/?utm_source=wpnotice&utm_medium=thirteen&utm_campaign=WPExportMenus';
            $message_thirteen = wp_kses_post ( __( 'Allow your customers to select the Delivery Date & Time for your WooCommerce products using our Product Delivery Date Pro for WooCommerce Plugin. <br> 
            <strong><a target="_blank" href= "'.$_link.'">Shop now</a></strong>', $wp_google_remarketing_locale ) );
            $wp_google_remarketing_message_thirteen = array ( 'message' => $message_thirteen, 'plugin_link' => 'product-delivery-date/product-delivery-date.php' );

            $ts_pro_notices = array (
                1 => $message_first,
                2 => $message_two,
                3 => $message_three,
                4 => $message_four,
                5 => $message_five,
                6 => $wp_google_remarketing_message_six,
                7 => $wp_google_remarketing_message_seven,
                8 => $wp_google_remarketing_message_eight,
                9 => $wp_google_remarketing_message_nine,
                10 => $wp_google_remarketing_message_ten,
                11 => $wp_google_remarketing_message_eleven,
                12 => $wp_google_remarketing_message_twelve,
                13 => $wp_google_remarketing_message_thirteen
            );

            return $ts_pro_notices;
        }
		
		/**
         * It will contain all the shortcodes which need to be display on the shortcodes page.
         * @return array $ts_shortcodes All questions and answers.
         * 
         */
        public static function wp_google_remarketing_get_faq() {

            $ts_wp_google_remarketing = array ();

            $ts_wp_google_remarketing = array(
                1 => array (
                        'question' => 'Exported menus are not getting imported',
                        'answer'   => 'Menus are not getting imported if you have a PHP 7 version. The issue is from the WordPress Imported.
                        <br/><br/>
                        Below is the fix which needs to be applied in the wordpress-importer.php file on line number 884.
                        <br/><br/>
                        Change $$meta[‘key’] = $meta[‘value’];
                        <br/><br/>
                        To ${$meta[‘key’]} = $meta[‘value’];'
                    ), 
                2 => array (
                        'question' => 'Can I export menus created for a month.?',
                        'answer'   => 'Yes, you can export the WordPress menus filtering by Months.
                        '
                    )
            );

            return $ts_wp_google_remarketing;
        }
	}
	$WP_Google_Remarketing_Component = new WP_Google_Remarketing_Component();
}