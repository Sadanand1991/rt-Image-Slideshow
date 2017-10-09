<?php
/*
Plugin Name: RT Image Slideshow
Description: Sliding images in the WordPress fronted area.
Version: 1.0
Author: Sadanand Lonari
Text Domain: rt-image-slideshow
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define('SLIDESHOW_PATH', plugin_dir_url(__FILE__));

// include admin file 
require_once 'admin/settings-options.php';

// include front file 
require_once 'front/shortcode.php';