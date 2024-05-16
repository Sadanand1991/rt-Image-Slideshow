<?php
/**
 * Plugin Name: RT Image Slideshow
 * Description: Sliding images in the WordPress fronted area.
 * Version: 1.1
 * Author: Sadanand Lonari
 * Text Domain: rt-image-slideshow
 *
 * @package RT Image Slideshow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// plugin path.
define( 'SLIDESHOW_PATH', plugin_dir_url( __FILE__ ) );

// include admin file.
require_once 'admin/class-slideshow-settings-page.php';

// include front file.
require_once 'front/class-slideshow.php';
