<?php
/*
Plugin Name: Google Remarketing Codes
Plugin URI: https://www.tychesoftwares.com/
Description: Include Google Remarketing Ad Codes on a per page or post basis. There is also a spot for a default code.
Author: Tyche Softwares
Version: 1.1
Author URI: https://www.tychesoftwares.com/
Text Domain: wp-google-remarketing
License: GPL2

	Forked from:
	http://wordpress.org/extend/plugins/adwords-remarketing/

*/

function call_wpGoogleRemarketing() 
{
    return new wpGoogleRemarketing();
}
call_wpGoogleRemarketing();

class wpGoogleRemarketing
{
	private $default_post_types = array( 'page', 'post' );
	
	const WGR_VERSION = '1.1';

	public function __construct()
	{
		// ADMIN STUFF
		if ( is_admin() )
		{
			$plugin = plugin_basename(__FILE__); 
			add_filter("plugin_action_links_$plugin", array( &$this, 'googleremarketing_settings_link' ) );		
		
			add_action(	'add_meta_boxes', 	array( &$this, 'add_googleremarketing_meta_box' ));
			add_action(	'save_post', 		array( &$this, 'save_googleremarketing_meta_box_content' ));
			
			add_action(	'admin_menu',		array( &$this, 'googleremarketing_admin_menu' ));
			add_action( 'admin_init', 		array( &$this, 'googleremarketing_register_settings' ) );

			require_once( plugin_dir_path(__FILE__) . 'includes/google-remarketing-all-component.php' );

			//add_filter( 'ts_deativate_plugin_questions', array( &$this, 'wgr_deactivate_add_questions' ), 10, 1 );
			add_filter( 'ts_tracker_data',               array( &$this, 'wgr_ts_add_plugin_tracking_data' ), 10, 1 );
			add_filter( 'ts_tracker_opt_out_data',       array( &$this, 'wgr_get_data_for_opt_out' ), 10, 1 );
			add_action( 'admin_init',                    array( &$this, 'wgr_admin_actions' ) );

		}
		else
		{
			add_action(	'wp_footer', 		array( &$this,'embed_googleremarketing_meta_box_content' ), 100);
		}
	}
	
	
	// SETTINGS LINK ON PLUGIN PAGE
	function googleremarketing_settings_link($links) 
	{ 
	  $settings_link = '<a href="options-general.php?page=googleremarketing">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
	
	
	// SETTINGS SETUP
	public function googleremarketing_register_settings()
	{
	
		// SETTINGS
		register_setting( 'googleremarketing-group', 'default_google_retracking_code', array( &$this,'googleremarketing_validate_settings' ) );
		register_setting( 'googleremarketing-group', 'google_retracking_post_types', array( &$this,'googleremarketing_validate_post_types_field' ) );
		
		
		// SECTION
		add_settings_section( 'default_settings', 'Default Settings', array( &$this,'googleremarketing_default_section' ), 'google-remarketing' );
		
		
		// DEFAULT CODE
		add_settings_field( 'default_google_retracking_code', 'Default Retracking Code:', array( &$this,'googleremarketing_default_code_field' ), 'google-remarketing', 'default_settings' );
	
	
		// POST TYPES
		add_settings_field( 'google_retracking_display_post_types', 'Display for Post Types:', array( &$this,'googleremarketing_post_types_field' ), 'google-remarketing', 'default_settings' );
	}
	
	
	// SECTION DESCRIPTION
	function googleremarketing_default_section()
	{
		echo "";
	}
		
		
	// RETRACKING CODE FIELD
	function googleremarketing_default_code_field() 
	{
	    ?>
	    <input type="text" name="default_google_retracking_code" value="<?php echo get_option('default_google_retracking_code'); ?>"  style="width: 90%;" />
	    <?php
	}
	
	
	// DISPLAY ON POST TYPES FIELD
	function googleremarketing_post_types_field()
	{
		$select_post_types = (array) get_option('google_retracking_post_types');
		if(!$select_post_types) { $select_post_types = $this->default_post_types; } // display on page & post by default
	
		$post_types = get_post_types();
		foreach( $post_types as $post_type)
		{
			$chcked = in_array($post_type, $select_post_types) ? " CHECKED " : "";
		?>
			<input type="checkbox" name="google_retracking_post_types[]" value="<?php echo $post_type ?>" <?php echo $chcked ?>/> <?php echo $post_type ?> &nbsp; 
		<?php 
		}
	}
	
	// SETTINGS PAGE MENU ITEM
	public function googleremarketing_admin_menu() 
	{
	    add_options_page( 'Google Remarketing', 'Google Remarketing', 'manage_options', 'googleremarketing', array( &$this, 'googleremarketing_settings' ) );
	}
	
	
	// DISPLAY SETTINGS
	public function googleremarketing_settings() 
	{
	    if (!current_user_can('manage_options')) 
	    {
	        wp_die('You do not have sufficient permissions to access this page.');
	    }
	?>
		
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2>Google Remarketing</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'googleremarketing-group' ); ?>
				<?php do_settings_sections( 'google-remarketing' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>		
	<?php
	}
	

	// VALIDATE DEFAULT CODE FIELD
	public function googleremarketing_validate_settings( $input )
	{
		return $this->googleremarketing_sanatize_code( $input );
	}
	
	
	// VALIDATE POST TYPES FIELD
	public function googleremarketing_validate_post_types_field( $input )
	{
		return (array) $input;
	}
	
	// DISPLAY METABOXES
	public function add_googleremarketing_meta_box()
	{
		$select_post_types = (array) get_option('google_retracking_post_types');
		if(!$select_post_types) { $select_post_types = $this->default_post_types; }
		
		
		foreach($select_post_types as $select_post_type)
		{
			add_meta_box ( 'googleremarketing_meta_box_name', 'Google Remarketing', array(&$this,'render_googleremarketing_meta_box_content'), $select_post_type, 'normal', 'low' );
		
		}
	}
	
	
	// META BOX DISPLAY
	public function render_googleremarketing_meta_box_content($post) 
	{
		$out = '<label for="myplugin_new_field">Remarketing Code or Image URL</label><br />';
		$out .= '<input type="text" id="input_googleremarketing" name="input_googleremarketing" value="' . get_post_meta($post->ID, 'input_googleremarketing', true) . '" size="90" />';
		echo $out;
	}
	
	
	// FIND THE URL 
	public function googleremarketing_sanatize_code( $code )
	{
		$code = stripslashes( $code );
		$code = preg_replace( '^.*<img.*src="^is', '', $code);
		$code = preg_replace( '^["|\'].*^is', '', $code);
		return $code;
	}
	
	
	// SAVE META BOX
	public function save_googleremarketing_meta_box_content($postid) 
	{
		if(defined('DOING_AUTOSAVE')&& DOING_AUTOSAVE) return;
		
		if(isset($_POST['input_googleremarketing']))
		{
			$code = $this->googleremarketing_sanatize_code( $_POST['input_googleremarketing'] );

			add_post_meta( $postid, 'input_googleremarketing', $code, 1);
			update_post_meta( $postid, 'input_googleremarketing', $code);
			return $code;
		}
	}
	
	// CALLED FROM wp_footer, EMBEDS THE IMAGE
	public function embed_googleremarketing_meta_box_content( )
	{
		global $post;
		$code = false;
		
		// CHECK PAGE OR POST FOR CODE
		if(is_page() OR is_single() OR is_singular())
		{
			if( $post->ID )
			{
				$code = get_post_meta( $post->ID, 'input_googleremarketing', true );
			}
		}
	
		// IF NOT CODE, CHECK FOR DEFAULT
		if(!$code AND get_option('default_google_retracking_code'))
		{
			$code = get_option('default_google_retracking_code');
		}
		
		// RETURN THE CODE
		if($code)
		{
			echo "\n\n<!-- Google Remarketing Pixel -->\n" . '<img src="'.$code.'" alt="" height="1" width="1" border="0" style="border:none !important;" />' . "\n\n";
		}
	}

	function wgr_deactivate_add_questions ( $wem_deactivate_questions ) {

		$wem_deactivate_questions = array(
			0 => array(
				'id'                => 4, 
				'text'              => __( "WordPress Menus are not exported not getting exported.", "wem" ),
				'input_type'        => '',
				'input_placeholder' => ''
				)

		);
		return $wem_deactivate_questions;
	}

	function wgr_admin_actions ( ) {
		/**
		 * We need to store the plugin version in DB, so we can show the welcome page and other contents.
		 */
		$wem_version_in_db = get_option( 'wp_google_remarketing_version' ); 
		if ( $wem_version_in_db != self::WGR_VERSION ) {
			update_option( 'wp_google_remarketing_version', self::WGR_VERSION );
			define ( 'WGR_VERSION', self::WGR_VERSION );
		}
	}

	/**
	 * Plugin's data to be tracked when Allow option is choosed.
	 *
	 * @hook ts_tracker_data
	 *
	 * @param array $data Contains the data to be tracked.
	 *
	 * @return array Plugin's data to track.
	 * 
	 */

	public static function wgr_ts_add_plugin_tracking_data ( $data ) {
		if ( isset( $_GET[ 'wp_google_remarketing_tracker_optin' ] ) && isset( $_GET[ 'wp_google_remarketing_tracker_nonce' ] ) && wp_verify_nonce( $_GET[ 'wp_google_remarketing_tracker_nonce' ], 'wp_google_remarketing_tracker_optin' ) ) {

			$plugin_data[ 'ts_meta_data_table_name' ] = 'ts_tracking_wgr_meta_data';
			$plugin_data[ 'ts_plugin_name' ]		  = 'Google Remarketing Codes';
			/**
			 * Add Plugin data
			 */
			$plugin_data[ 'wgr_plugin_version' ]      = self::WGR_VERSION;
			
			$plugin_data[ 'wgr_allow_tracking' ]      = get_option ( 'wgr_allow_tracking' );
			$data[ 'plugin_data' ]                    = $plugin_data;
		}
		return $data;
	}
	
	/**
	 * Tracking data to send when No, thanks. button is clicked.
	 *
	 * @hook ts_tracker_opt_out_data
	 *
	 * @param array $params Parameters to pass for tracking data.
	 *
	 * @return array Data to track when opted out.
	 * 
	 */
	public static function wgr_get_data_for_opt_out ( $params ) {
		$plugin_data[ 'ts_meta_data_table_name']   = 'ts_tracking_wgr_meta_data';
		$plugin_data[ 'ts_plugin_name' ]		   = 'Google Remarketing Codes';
		
		// Store count info
		$params[ 'plugin_data' ]  				   = $plugin_data;
		
		return $params;
	}
}