<?php
if (!is_admin()) add_action( 'wp_print_scripts', 'woothemes_add_javascript' );
function woothemes_add_javascript( ) {
	wp_enqueue_script('jquery');    
	wp_enqueue_script( 'superfish', get_bloginfo('template_directory').'/includes/js/superfish.js', array( 'jquery' ) );
	wp_enqueue_script( 'wootabs', get_bloginfo('template_directory').'/includes/js/woo_tabs.js', array( 'jquery' ) );
	wp_enqueue_script( 'general', get_bloginfo('template_directory').'/includes/js/general.js', array( 'jquery' ) );
}
?>