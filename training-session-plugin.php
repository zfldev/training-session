<?php
/*
 * Plugin Name: Training Sessions
 * Plugin URI: https://www.zfl.fau.de
 * Description: This Plugin adds a custom Post Type training_session. It may also capture CF7 (Contact Form 7) submits for the Post Type. Therefore add and hidden field name="post_type" and value="training_session", please also add the field name="dateformat" value="d.m.Y" and name="timeformat" value="H:i" to the CF7 Form. Set the value of dateformat and timeformat to the one users will input.
 * Author: Zentrum für Lehrerinnen- und Lehrerbildung der FAU
 * Author URI: https://www.zfl.fau.de/
 * Version: 1.3
 * License: GPLv3
 */
 

 
add_action( 'plugins_loaded', 'training_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function training_load_textdomain() {
  load_plugin_textdomain( 'TrainingTD', false, dirname(plugin_basename( __FILE__ )) . '/languages' );
}
 
/**
 * Install, deactivation and unistall hooks
 */
 
/* What to do when the plugin is activated? */
register_activation_hook(__FILE__,'training_session_install');

/* What to do when the plugin is deactivated? */
register_deactivation_hook( __FILE__, 'training_session_deactivation' );

/* What to do when the plugin is unistalled? */
register_uninstall_hook( __FILE__, 'training_session_uninstall' );


/**
 * Install
 */
function training_session_install() {
// Nothing at the moment
}
/**
 * Deactivation
 */
function training_session_deactivation() {
// Nothing at the moment
}

/**
 * Unistall
 */
function training_session_uninstall() {
// Nothing at the moment
}


// Register Custom Post Type
require_once ('posttypes/training-session.php'); 
// Integrate the Taxonomies
require_once ('taxonomies/training-category.php'); 
require_once ('taxonomies/training-tag.php');

/////////////////////////////////////////////////////////////////
// UI User Interface
/////////////////////////////////////////////////////////////////
require_once ('interfaces/training-admin-interface.php' ) ;

/////////////////////////////////////////////////////////////////
// Interface to Contact form 7
/////////////////////////////////////////////////////////////////
require_once ('interfaces/training-capture-cf7-form.php' ) ;

/////////////////////////////////////////////////////////////////
// Helpers
/////////////////////////////////////////////////////////////////
require_once ('helpers/training-helpers.php' ) ;

/////////////////////////////////////////////////////////////////
// Shortcodes
/////////////////////////////////////////////////////////////////
require_once ('shortcodes/training-shortcode.php' ) ;
	


/////////////////////////////////////////////////////////////////
// add_actions and add_filters
/////////////////////////////////////////////////////////////////

// capture input and send to 
add_action('wpcf7_before_send_mail', 'saveCF7FormToTrainingSession');

// create the Metabox for setting the Time of an event
add_action( 'admin_init', 'create_training_metabox' );

// Saving the metabox data
add_action ('save_post', 'save_training_details', 1, 2);

// Changing the admin view
add_filter ('manage_edit-training_session_columns', 'training_admin_edit_columns');
add_action ('manage_posts_custom_column', 'training_admin_custom_columns');


add_action('admin_head', 'training_admin_style');

function training_admin_style() {
  echo '<script type="text/javascript">
  		 function showMe (it, box) { 
   var vis = (box.checked) ? "block" : "none"; 
   document.getElementById(it).style.display = vis;
 } 
		</script> 
  		<style type="text/css">
        .training-error {color: red;}
        .training-delete {text-decoration: line-through;} 
        .training-warning {color: orange;} 
    	.training-ok {color: green;	} 
    	.meta-note {color: #555; }
    	li.date, li.time-start, li.time-end { float:left; display: inline; }
    	.clear {clear: both;}
    	.list-label{ font-weight: bold; }
    	#training_col_days {width: 180px;}
    	#training_col_contact {width: 190px;}
    	 </style>';
}

// // Hook into the 'wp_enqueue_scripts' action
// add_action( 'wp_enqueue_scripts', 'training_scripts' );

// // Hook into the 'wp_enqueue_scripts' action
// add_action( 'wp_enqueue_scripts', 'training_styles' );
 

// // Register Style
// function training_styles() {
// 	wp_register_style( 'training_session_styles', plugin_dir_url( __FILE__ ) . 'css/training-session.css', false, false );
// 	wp_enqueue_style( 'training_session_styles' );
// }
// // Register Admin Style
// function training_styles() {
// 	wp_register_style( 'training_session_styles', plugin_dir_url( __FILE__ ) . 'css/training-session.css', false, false );
// 	wp_enqueue_style( 'training_session_styles' );
// }

// // Register Script
// function training_scripts() {
// 
// 	//wp_deregister_script( 'training_session' );
// 	wp_register_script( 'training_session_scripts', plugin_dir_url( __FILE__ ) . 'js/training-session.js', false, false, false );
// 	wp_enqueue_script( 'training_session_spripts' );
// }







