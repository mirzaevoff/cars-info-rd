<?php
/*
Plugin Name: Cars Info RD
Description: Getting car info by VIN code.
Version: Alpha 1.0.0
Author: Мирзаев Ахрорбек (mirby)
Author URI: https://ravendigital.uz
*/

require 'functions.php';

require 'admin/functions.php';
// registering scripts
add_action( 'wp_enqueue_scripts', 'rd_registerAssets' );

// registering scripts
add_action( 'admin_enqueue_scripts', 'rd_adminRegisterAssets' );

//registering shortcodes
add_shortcode( 'rd_getspecs', 'rd_GetSpecs' );

//registering menu page
add_action('admin_menu', 'cird_settingsMenu');

register_activation_hook( __FILE__, 'createTable' );
register_uninstall_hook( __FILE__, 'deleteTable' );
