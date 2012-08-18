<?php
/*
Plugin Name: Gallery To Slideshow
Plugin URI: http://www.mattvarone.com
Description: Converts the WordPress built-in gallery into a Responsive jQuery FlexSlider SlideShow.
Author: Matt Varone
Version: 1.4.6
Author URI: http://www.mattvarone.com
*/

/**
* Gallery To Slideshow Initialize
*
* @package Gallery To Slideshow
* @author Matt Varone
*/
    
/*
|--------------------------------------------------------------------------
| GALLERY TO SLIDESHOW CONSTANTS
|--------------------------------------------------------------------------
*/
define( 'MV_GALLERY_TO_SLIDESHOW_VERSION', '1.4.6' );

/*
|--------------------------------------------------------------------------
| GALLERY TO SLIDESHOW INCLUDES
|--------------------------------------------------------------------------
*/

if ( ! is_admin() )
    require_once( plugin_dir_path( __FILE__ ) . 'inc/gallery-to-slideshow-class.php' );
    
/**
 * Load Textdomain
 *
 * @access      private
 * @since       1.1.4
 * @return      void
*/

if ( ! function_exists( 'mv_gallery_to_slideshow_load_textdomain' ) ) {
function mv_gallery_to_slideshow_load_textdomain() {
        load_plugin_textdomain( 'mv-gallery-to-slideshow', false, dirname( plugin_basename( __FILE__ ) ) . '/lan' );
    }
}
add_action( 'init', 'mv_gallery_to_slideshow_load_textdomain' );

/*
|--------------------------------------------------------------------------
| GALLERY TO SLIDESHOW ON ACTIVATION
|--------------------------------------------------------------------------
*/

if ( ! function_exists( 'mv_gallery_to_slideshow_activation' ) )
{
    
    /** 
    * Gallery To Slideshow Activation
    *
    * @package Gallery To Slideshow
    * @since 1.0
    *
    */
    
    function mv_gallery_to_slideshow_activation()
    {
        // check compatibility
        if ( version_compare( get_bloginfo( 'version' ), '3.4' ) >= 0 )
        deactivate_plugins( basename( __FILE__ ) );
    }
    
    register_activation_hook( __FILE__, 'mv_gallery_to_slideshow_activation' );
}