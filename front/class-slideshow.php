<?php
/**
 * Class Slideshow
 *
 * Slideshow class for managing slideshow functionality.
 */
class Slideshow {

	/**
	 * Constructor to initialize the slideshow.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'slideshow_front_scripts' ) );
		add_shortcode( 'rtslideshow', array( $this, 'rtslideshow_callback' ) );
	}

	/**
	 * Enqueue front-end scripts and styles for the slideshow.
	 */
	public function slideshow_front_scripts() {
		wp_enqueue_style( 'rt_custom-style', SLIDESHOW_PATH . 'front/css/slider.css', array(), null );
		wp_enqueue_script( 'rt_jssor-script', SLIDESHOW_PATH . 'lib/rt-slider.min.js', array(), '1.0.0', true );
		wp_enqueue_script( 'rt_general-script', SLIDESHOW_PATH . 'front/js/general.js', array(), '1.0.0', true );
	}

	/**
	 * Shortcode callback for slideshow.
	 *
	 * @return string HTML content for the slideshow.
	 */
	public function rtslideshow_callback() {
		$slideshow_images = wp_json_decode( get_option( 'slideshow_settings' ), true );
		$content          = '';

		$content .= '<div id="gallery">
			<div class="gallery init">
				<div class="slideset">
					<div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:980px;height:600px;overflow:hidden;visibility:hidden;">
					   <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:980px;height:671px;overflow:hidden;">';

		$h = 1;
		if ( is_array( $slideshow_images ) && ! empty( $slideshow_images ) ) {
			foreach ( $slideshow_images as $slideshow_image ) {
				if ( ! empty( $slideshow_image ) ) {
					$content .= '<div class="slider-img">';
					$content .= '<img data-u="image" src="' . esc_url( $slideshow_image ) . '" />';
					$content .= '<img data-u="thumb" src="' . esc_url( $slideshow_image ) . '" />';
					$content .= '</div>';
					++$h;
				}
			}
		}
		$content .= '</div>
				<!-- Thumbnail Navigator -->
						<div data-u="thumbnavigator" class="jssort101" style="position:absolute;left:0px;bottom:0px;width:980px;height:100px;background-color:#000;" data-autocenter="1" data-scale-bottom="0.75">
							<div data-u="slides">
								<div data-u="prototype" class="p" style="width:190px;height:90px;">
									<div data-u="thumbnailtemplate" class="t"></div>
								</div>
							</div>
						</div>
						<!-- Arrow Navigator -->
						<div data-u="arrowleft" class="jssora106" style="width:55px;height:55px;top:162px;left:30px;" data-scale="0.75">
							<svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
								<circle class="c" cx="8000" cy="8000" r="6260.9"></circle>
								<polyline class="a" points="7930.4,5495.7 5426.1,8000 7930.4,10504.3 "></polyline>
								<line class="a" x1="10573.9" y1="8000" x2="5426.1" y2="8000"></line>
							</svg>
						</div>
						<div data-u="arrowright" class="jssora106" style="width:55px;height:55px;top:162px;right:30px;" data-scale="0.75">
							<svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;">
								<circle class="c" cx="8000" cy="8000" r="6260.9"></circle>
								<polyline class="a" points="8069.6,5495.7 10573.9,8000 8069.6,10504.3 "></polyline>
								<line class="a" x1="5426.1" y1="8000" x2="10573.9" y2="8000"></line>
							</svg>
						</div>
					</div> 
				</div>
			</div>
		</div>';

		return $content;
	}
}

// Initialize slideshow class.
new Slideshow();
