<?php
/*
 *
 * * Enqueue scripts on Website
 *
 *  */

namespace SMPLFY\boilerplate;
function bs_enqueue_boilerplate_frontend_scripts() {
	global $current_user;
	global $post;
	$current_user = wp_get_current_user();

	wp_register_script( 'bs-demo-frontend-script', BS_NAME_PLUGIN_URL . '/includes/js/frontend.js', array( 'jquery' ), null, true );
	wp_register_style( 'bs-demo-frontend-styles', BS_NAME_PLUGIN_URL . '/includes/css/frontend.css' );

	wp_enqueue_script( 'bs-demo-frontend-script' );
	wp_enqueue_style( 'bs-demo-frontend-styles' );
}

add_action( 'wp_enqueue_scripts', 'bs_enqueue_boilerplate_frontend_scripts' );
