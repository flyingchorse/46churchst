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
  $html          = '';
	$switched_blog = false;

	if ( is_multisite() && ! empty( $blog_id ) && get_current_blog_id() !== (int) $blog_id ) {
		switch_to_blog( $blog_id );
		$switched_blog = true;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );

	// We have a logo. Logo is go.
	if ( $custom_logo_id ) {
		$custom_logo_attr = array(
			'class'   => 'custom-logo',
			'loading' => false,
		);

		$unlink_homepage_logo = (bool) get_theme_support( 'custom-logo', 'unlink-homepage-logo' );

		if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
			/*
			 * If on the home page, set the logo alt attribute to an empty string,
			 * as the image is decorative and doesn't need its purpose to be described.
			 */
			$custom_logo_attr['alt'] = '';
		} else {
			/*
			 * If the logo alt attribute is empty, get the site title and explicitly pass it
			 * to the attributes used by wp_get_attachment_image().
			 */
			$image_alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
			}
		}

		/**
		 * Filters the list of custom logo image attributes.
		 *
		 * @since 5.5.0
		 *
		 * @param array $custom_logo_attr Custom logo image attributes.
		 * @param int   $custom_logo_id   Custom logo attachment ID.
		 * @param int   $blog_id          ID of the blog to get the custom logo for.
		 */
		$custom_logo_attr = apply_filters( 'get_custom_logo_image_attributes', $custom_logo_attr, $custom_logo_id, $blog_id );

		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass it
		 * because wp_get_attachment_image() already adds the alt attribute.
		 */
		$image = wp_get_attachment_image( $custom_logo_id, 'full', false, $custom_logo_attr );

		if ( $unlink_homepage_logo && is_front_page() && ! is_paged() ) {
			// If on the home page, don't link the logo to home.
			$html = sprintf(
				'<span class="custom-logo-link">%1$s</span>',
				$image
			);
		} else {
			$aria_current = is_front_page() && ! is_paged() ? ' aria-current="page"' : '';

			$html = sprintf(
				'<a href="%1$s" class="custom-logo-link" rel="home"%2$s>%3$s</a>',
				esc_url( home_url( '/' ) ),
				$aria_current,
				$image
			);
		}
	} elseif ( is_customize_preview() ) {
		// If no logo is set but we're in the Customizer, leave a placeholder (needed for the live preview).
		$html = sprintf(
			'<a href="%1$s" class="custom-logo-link" style="display:none;"><img class="custom-logo" alt="" /></a>',
			esc_url( get_site_url(1,'/') )
		);
	}

	if ( $switched_blog ) {
		restore_current_blog();
	}

	/**
	 * Filters the custom logo output.
	 *
	 * @since 4.5.0
	 * @since 4.6.0 Added the `$blog_id` parameter.
	 *
	 * @param string $html    Custom logo HTML output.
	 * @param int    $blog_id ID of the blog to get the custom logo for.
	 */
  return $html;
}

add_filter('get_custom_logo', __NAMESPACE__ . '\\siteBrand');

?>