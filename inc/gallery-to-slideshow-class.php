<?php
/**
* Gallery To Slideshow Class
*
* @package    Gallery to Slideshow
* @author     Matt Varone
*/

if ( ! class_exists( 'MV_Gallery_To_Slideshow' ) ) {    
    
    class MV_Gallery_To_Slideshow {
        
        /** 
        * Boolean to store if the content has a gallery.
        *
        * @access   private
        * @since    1.0
        *
        */
        
        private $has_gallery = false;
        
        /** 
        * Gallery To Slideshow 
        * 
        * Hook into the init action.
        *
        * @access   private
        * @return   void
        * @since    1.0
        *
        */
        
        function __construct() {                
            add_action( 'init', array( &$this, 'init' ) );
            add_action( 'init', array( &$this, 'has_gallery_hook' ) );     
        }

        /** 
        * Init 
        * 
        * Sets the necessary action and filter for the Slideshows.
        *
        * @access   private
        * @return   void
        * @since    1.3
        */
        
        function init() {
            add_action( 'the_posts', array( &$this, 'have_gallery' ), 10, 1 );
            add_filter( 'post_gallery', array( &$this, 'gallery' ), 10, 2 );
        }
        
        
        /** 
        * Has Gallery Hook 
        * 
        * Allows to hook and fire the slideshow.
        *
        * @access   private
        * @return   void
        * @since    1.4.3
        */
        
        function has_gallery_hook() {
            if ( apply_filters( 'mv_gallery_to_slideshow_has_gallery' , false ) )
                $this->enqueue_assets();
        }
        
        /** 
        * Have Gallery 
        * 
        * Checks if the [gallery] shortcode is present.
        *
        * @access   private
        * @return   void
        * @since    1.0
        */
        
        function have_gallery( $posts ) {
            if ( empty( $posts ) || $this->has_gallery )
                return $posts;
        
            foreach ( $posts as $post ) {
                $pos = strstr( $post->post_content, '[gallery' );
                if ( false !== $pos && $pos >= 0 ) {
                    $this->has_gallery = true;
                    break;
                }
            }
        
            if ( $this->has_gallery )
                $this->enqueue_assets();
                        
            return $posts;
            
        }
        
        
        /** 
        * Enqueue Assets 
        * 
        * Adds JS and CSS assets actions.
        *
        * @access   private
        * @return   void
        * @since    1.0
        */
        
        function enqueue_assets() {
            add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_style' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_script' ) );          
        }
        
        
        /** 
        * Enqueue Style
        * 
        * @access   private
        * @return   void
        * @since    1.0
        */
        
        function enqueue_style() {
            $css = 'gallery-to-slideshow.css';
            
            $on_theme =  get_stylesheet_directory() . '/' . $css;
            $on_plugin = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) . 'css/' . $css;
            
            $stylesheet = ( file_exists( $on_theme ) ) ? get_stylesheet_directory_uri() . '/' . $css : $on_plugin;  
            
            $stylesheet = apply_filters( 'mv_gallery_to_slideshow_enqueue_style', $stylesheet );
            
            if ( $stylesheet ) wp_enqueue_style( 'gallery-to-slideshow', $stylesheet , false, MV_GALLERY_TO_SLIDESHOW_VERSION, 'all' );
        }
        
        
        /** 
        * Enqueue Script
        *
        * @access   private
        * @return   void
        * @since    1.0
        */
        
        function enqueue_script() {
            wp_register_script( 'flexslider', plugin_dir_url( dirname( __FILE__ ) ) . 'js/libs/flexslider/jquery.flexslider-min.js', array( 'jquery' ), '1.8', true );
            $env = ( WP_DEBUG ) ? 'dev' : 'min';
            wp_enqueue_script( 'gallery-to-slideshow', plugin_dir_url( dirname( __FILE__ ) ) . 'js/gallery.to.slideshow.' . $env . '.js', array( 'flexslider' ), MV_GALLERY_TO_SLIDESHOW_VERSION, true );
            
            // FlexSlider Params
            $params = array(
                'thumbnails'        => true,
                'slideshow'         => true, 
                'slideshowSpeed'    => 7000, 
                'animationDuration' => 600, 
                'mousewheel'        => false, 
                'controlNav'        => false, 
                'keyboardNav'       => false, 
                'directionNav'      => false, 
                'manualControls'    => '.pager li a img', 
                'pausePlay'         => false,
                'prevText'          => __( "Previous", 'mv-gallery-to-slideshow' ), 
                'nextText'          => __( "Next", 'mv-gallery-to-slideshow' ), 
                'pauseText'         => __( "Pause", 'mv-gallery-to-slideshow' ), 
                'randomize'         => false, 
                'slideToStart'      => 0, 
                'animationLoop'     => true, 
                'pauseOnAction'     => true, 
                'pauseOnHover'      => false, 
                'controlsContainer' => '', 
             );
            
            wp_localize_script( 'gallery-to-slideshow', 'mv_gallery_to_slideshow_js_params', apply_filters( 'mv_gallery_to_slideshow_js_params', $params ) );           
        }
        
        
        /** 
        * Gallery
        *
        * @access   private
        * @return   string
        * @since    1.0
        */
        
        function gallery( $gallery_html, $attr ) {
            global $post;

            if ( isset( $attr['orderby'] ) ) {
                $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );

                if ( ! $attr['orderby'] )
                unset( $attr['orderby'] );
            }

            $attr = wp_parse_args( $attr, array( 
                    'id'          => $post->ID, 
                    'order'       => 'ASC', 
                    'orderby'     => 'menu_order ID', 
                    'size'        => 'large', 
                    'link'        => '', 
                    'captions'    => 1, 
                    'exclude'     => '', 
                    'include'     => '', 
                    'numberposts' => -1, 
                    'offset'      => ''
             ) );
            
            $attr = apply_filters( 'mv_gallery_to_slideshow_attr', $attr );

            $attachments = get_children( array( 
                    'post_parent'       => $attr['id'], 
                    'post_status'       => 'inherit', 
                    'post_type'         => 'attachment', 
                    'post_mime_type'    => 'image', 
                    'order'             => $attr['order'], 
                    'orderby'           => $attr['orderby'], 
                    'exclude'           => $attr['exclude'], 
                    'include'           => $attr['include'], 
                    'numberposts'       => $attr['numberposts'], 
                    'offset'            => $attr['offset']
            ) );
            
            if ( empty( $attachments ) )
                return '';

            if ( is_feed() ) {
                $output = "\n";

                foreach ( $attachments as $attachment_id => $attachment )
                    $output .= wp_get_attachment_link( $attachment_id, $attr['size'], true ) . "\n";

                return $output;
            }
            
            $output = "\n\t<div class=\"gallery-to-slideshow-wrapper\">\n\t\t";
                        
            $header  = "<div class=\"gallery-to-slideshow-header\">\n\t\t\t";

            $header .= "<div class=\"gallery-to-slideshow-title\">" . apply_filters( 'mv_gallery_to_slideshow_title', __( 'Picture Gallery', 'mv-gallery-to-slideshow' ) ) . "</div>";
            
            $header .= "\n\t\t\t<div class=\"gallery-to-slideshow-caption\">" . get_the_title() . "</div>\n\t\t</div>\n\t\t";
            
            $header = apply_filters( 'mv_gallery_to_slideshow_header', $header );
                        
            $output .= $header;

            $output .= "<div class=\"gallery-to-slideshow\">\n\t\t\t<ul class=\"slides\">\n\t\t\t\t";

            $featured_id = function_exists( 'get_post_thumbnail_id' ) ? apply_filters( 'mv_gallery_to_slideshow_featured_id', get_post_thumbnail_id( $post->ID ) ) : 0;
            
            foreach ( $attachments as $attachment_id => $attachment ) {
                    if ( $featured_id == $attachment_id ) continue;

                    $title = esc_attr( $attachment->post_title, 1 );
                                        
                    $output .= '<li>';
                        
                    if ( isset( $attr['link'] ) && $attr['link'] != 'false' ) {
                        if ( 'file' == $attr['link'] ) {
                            // link to file: link="file"
                            $output .= wp_get_attachment_link( $attachment_id, $attr['size'], false, false ); 
                        } else if ( 'attachment' == $attr['link'] || '' == $attr['link'] ) {
                            // link to attachment page: link="attachment" or link="" ( WordPress sends it this way )
                            $output .= wp_get_attachment_link( $attachment_id, $attr['size'], true, false );
                        } else if ( 'post' == $attr['link'] ) {
                            // link to post: link="post"
                             $output .= sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
                                                    get_permalink( $attr['id'] ),
                                                    sprintf( __( 'Permalink to %s', 'mv-gallery-to-slideshow' ), get_the_title( $attr['id'] ) ),
                                                    wp_get_attachment_image( $attachment_id, $attr['size'] ) );
                        }
                    } else {
                        // no link or link="false"
                        $output .= wp_get_attachment_image( $attachment_id, $attr['size'] );
                    }
                    
                    if ( $attr['captions'] == 1 && strlen( $attachment->post_excerpt ) > 1 )
                        $output .= '<p class="flex-caption"><span>' . esc_html( $attachment->post_excerpt ) . '</span></p>';
                                        
                    $output .= "</li>\n\t\t\t\t";
            }

            $output .= "\n\t\t\t</ul>\n\t\t</div>\n\t</div><!-- .gallery-to-slideshow -->";
            
            $output = apply_filters( 'mv_gallery_to_slideshow_shortcode', $output );

            return $output;

        }
    }
    
    new MV_Gallery_To_Slideshow();
}