<?php 
	function childtheme_enqueue_styles() {
  wp_enqueue_style( 'parent-style', 
    get_template_directory_uri() . '/style.css' );

  wp_enqueue_style( 'child-style',
    get_stylesheet_directory_uri() . '/style.css',
    array( 'parent-style' ),
    wp_get_theme()->get('Version')
  );
  if( is_front_page() ){
    wp_enqueue_script( 'twentyseventeen-global', get_theme_file_uri( '/assets/js/global.js' ), array( 'jquery' ), '1.0', true );
  }
}
add_action( 'wp_enqueue_scripts', 'childtheme_enqueue_styles' );



function childtheme_theme_menu_class($atts, $item, $args) {
  if( is_array( $atts ) ) {
    $atts['class'] = 'nav-menu-scroll-down';
  }
  return $atts;
}
add_filter('nav_menu_link_attributes','childtheme_theme_menu_class', 0,3);

function logo_size_change(){
	remove_theme_support( 'custom-logo' );
	add_theme_support( 'custom-logo', array(
	    'height'      => 500,
	    'width'       => 800,
	    'flex-height' => true,
	    'flex-width'  => true,
	) );
}
add_action( 'after_setup_theme', 'logo_size_change', 11 );


function tweakjp_rm_comments_att( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'tweakjp_rm_comments_att', 10 , 2 );

add_filter( 'twentyseventeen_front_page_sections', function(){ return 9; } );

/**
 * Site Brand
 *
 * Output site branding
 *
 * Use native WordPress site logo with custom (bootstrap friendly) markup
 * Falls back to text title if logo is not set.
 *
 * @param $html
 *
 * @return string
 */
function siteBrand($html)
{
  // grab the site name as set in customizer options
  $site = get_bloginfo('name');
  // Wrap the site name in an H1 if on home, in a paragraph tag if not.
  is_front_page() ? $title = '<h1>' . $site . '</h1>' : $title = '<p>' . $site . '</p>';
  // Grab the home URL
  $home = esc_url(home_url('/'));
  // Class for the link
  $class = 'custom-logo-link';
  // Set anchor content to $title
  $content = $title;
  // Check if there is a custom logo set in customizer options...
  if (has_custom_logo()) {
    // get the URL to the logo
    $logo    = wp_get_attachment_image(get_theme_mod('custom_logo'), 'full', false, array(
      'class'    => 'brand-logo img-responsive',
      'itemprop' => 'logo',
    ));
    // we have a logo, so let's update the $content variable
    $content = $logo;
    // include the site name markup, hidden with screen reader friendly styles
    $content .= '<span class="sr-only">' . $title . '</span>';
  }
  // construct the final html
  $html = sprintf('<a href="%1$s" class="%2$s" rel="home" itemprop="url">%3$s</a>', $home, $class, $content);

  // return the result to the front end
  return $html;
}

add_filter('get_custom_logo', __NAMESPACE__ . '\\siteBrand');

?>