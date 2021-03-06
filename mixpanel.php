<?php
/*
Plugin Name: Monaeo MixPanel for WordPress
Plugin URI: https://github.com/monaeo/mixpanel-wordpress
Description: A relatively easy way to integrate MixPanel with your WordPress site (forked by Monaeo)
Author: Monaeo <dev@monaeo.com>
Version: 0.4
Author URI: http://monaeo.com/
*/

// if( is_admin() ){
   //require_once dirname( __FILE__ ) . '/meta-box.php';
// } else {
//   require_once dirname( __FILE__ ) . '/page.php';
// }

namespace mixpanel;

class mixPanel {
	
	// Returns the contents of a parsed PHP file as a string
	public static function get_require_contents($file) {
		if (is_file($file)) {
			ob_start();
			require $file;
			return ob_get_clean();
		}
		return false;
	}
	
	public function __construct(){
        if(is_admin()){
	    	add_action('admin_menu', array($this, 'add_settings_page'));
	    	add_action('admin_init', array($this, 'mixpanel_init'));
	    	require_once dirname( __FILE__ ) . '/meta-box.php';

		} else {
			require_once dirname( __FILE__ ) . '/page.php';
		}
    }


     public function add_settings_page(){
        // This page will be under "Settings"
		add_options_page('Mixpanel Options', 'Mixpanel Options', 'manage_options', 'mixpanel-admin', array($this, 'create_settings_page'));
    }

	public function create_settings_page(){
?>
	<div class="wrap">
	    <?php screen_icon(); ?>
	    <h2>Mixpanel Settings</h2>		
	    <?php settings_errors(  ) ?>	
	    <form method="post" action="options.php">
	    <?php
            // This prints out all hidden setting fields
		    settings_fields('mixpanel_settings_group');	
		    do_settings_sections('mixpanel_options');
		?>
	        <?php submit_button(); ?>
	    </form>
	</div>
	<h1>Integrating With Contact Forms</h1>
	<p>To create a people profile when a contact form is sumitted add <code>on_sent_ok: monaeo_form_onsuccess(id)</code>, to the "Additional Settings" tab of the contact form. Set <code>id</code> to the id of the contact form from the shortcode (eg. 786).</p>
	<p>To post an event, add <code>mixpanel.track('Event Name')</code>.
<?php
    }
	
	public function print_section_info(){
		print 'Enter your Mixpanel settings below:';
    }

	function my_text_input( $args ) {
	    $name = esc_attr( $args['name'] );
	    $value = esc_attr( $args['value'] );
	    if(strlen($value) > 0) {
	    	$size = strlen($value) + 2; 
	    } else {
	    	$size = 10; 
	    }
	    echo "<input type='text' name='$name' size='$size' value='$value' />";
	}


    public function mixpanel_init(){		
		register_setting('mixpanel_settings_group', 'mixpanel_settings'); # array($this, 'validate'));
      	$settings = (array) get_option( 'mixpanel_settings' );

        add_settings_section(
		    'mixpanel_settings_section',
		    'Mixpanel ',
		    array($this, 'print_section_info'),
		    'mixpanel_options'
		);	
		
		add_settings_field(
		    'token_id', 
		    'Mixpanel Token ID', // human readable part
		    array($this, 'my_text_input'),  // the function that renders the field
		    	'mixpanel_options', 
		    	'mixpanel_settings_section', array(
			    	'name' => 'mixpanel_settings[token_id]',
			    	'value' => $settings['token_id'],
				)	 
		);	

		add_settings_field(
		    'debug_mode', 
		    'Debug Mode? (true/false)', // human readable part
		    array($this, 'my_text_input'),  // the function that renders the field
		    	'mixpanel_options', 
		    	'mixpanel_settings_section', array(
			    	'name' => 'mixpanel_settings[debug_mode]',
			    	'value' => $settings['debug_mode'],
				)	 
		);	
		
		add_settings_field(
		    'subdomain_cookies', 
		    'Use Subdomain Cookie? (true/false)', // human readable part
		    array($this, 'my_text_input'),  // the function that renders the field
		    	'mixpanel_options', 
		    	'mixpanel_settings_section', array(
			    	'name' => 'mixpanel_settings[subdomain_cookie]',
			    	'value' => $settings['subdomain_cookie'],
				)	 
		);	

	}



	public function validate( $input ) {
		   $output = get_option( 'mixpanel_settings' );

		
	    if ( ctype_alnum( $input['token_id'] ) ) {
	        $output['token_id'] = $input['token_id'];
	    } else {
	    	echo "Adding Error \n"; #die; 
	        add_settings_error( 'mixpanel_options', 'token_id', 'The Mixpanel Token looks invalid (should be alpha numeric)' );
	    }

	    return $output;

	}


}

$mixPanel = new mixPanel(); 

?>
