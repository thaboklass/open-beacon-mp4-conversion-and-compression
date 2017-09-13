<?php
/*
 Plugin Name: Open Beacon MP4 Conversion and Compression
 Plugin URI: http://openbeacon.biz/?p=298
 Description: A simple and intuitive plugin that allows WordPress administrators to convert video to MP4 format, create snapshots of video and compress existing MP4 files using FFmpeg locally or remotely. To get started: 1) Click the "Activate" link, 2) Go to OB MP4 ConComp Central 3) Click the "Settings" tab and configure the plugin to your requirements and 4) Click the "Central" tab and start converting and compressing.
 Author: Thabo David Klass
 Version: 1.0.1
 Author URI: http://openbeacon.biz/
*/


/*------------------------------------------------------------------*
 * Constants and dependencies
/*------------------------------------------------------------------*/

/**
 * Define constants
 * 
 */

define( 'OB_MP4_CC_VERSION', '1.0.1' );
define( 'OB_MP4_CC_ROOT' , dirname( __FILE__ ) );
define( 'OB_MP4_CC_FILE_PATH' , OB_MP4_CC_ROOT . '/' . basename( __FILE__ ) );
define( 'OB_MP4_CC_URL' , plugins_url( '/', __FILE__ ) );


/**
 * Include other plugin dependencies
 * 
 */

require OB_MP4_CC_ROOT . '/includes/ob-mp4-cc-central.php';
require OB_MP4_CC_ROOT . '/includes/ob-mp4-cc-upload.php';
require OB_MP4_CC_ROOT . '/includes/ob-mp4-cc-assistant.php';

/*------------------------------------------------------------------*
 * Prepare the plugin to function
/*------------------------------------------------------------------*/


/**
* Custom post type backend callback
*
* @param    none
* @return   none
*/

function ob_mp4_c_and_c_post_type_init() {
    new ob_mp4_cc_upload();
}


/**
* Deactivation callback: removes assorted data
* that will be added in later versions of OBVC
*
* @param    none
* @return   none
*/

function ob_mp4_conversion_and_compression_deactivate() {
    // do nothing, not yet; at least not in version 1.0.1
}

// This initializes the custom post type, taxonomy and other relevant backend forms
add_action('init', 'ob_mp4_c_and_c_post_type_init');

$ob_mp4_cc_central_1 = new ob_mp4_cc_central();

// This add a central page
add_action('admin_menu', array($ob_mp4_cc_central_1, 'ob_mp4_cc_add_menu_page'));

// This adds settings functionality to the settings page
add_action('admin_init', array($ob_mp4_cc_central_1, 'ob_mp4_cc_initialize_options'));

register_deactivation_hook(__FILE__, 'ob_mp4_conversion_and_compression_deactivate');
?>