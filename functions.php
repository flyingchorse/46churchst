<?php 
	function childtheme_enqueue_styles() {
		
		wp_dequeue_style( 'twentyseventeen-style-css' );
		
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


function tweakjp_rm_comments_att( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
}
add_filter( 'comments_open', 'tweakjp_rm_comments_att', 10 , 2 );

add_filter( 'twentyseventeen_front_page_sections', function(){ return 8; } );

?>