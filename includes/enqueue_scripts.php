<?php
/*
 *
 * * Enqueue scripts on Wesmplfyite
 *
 *  */

namespace SMPLFY\boilerplate;
function enqueue_boilerplate_frontend_scripts() {
	global $current_user;
	global $post;
	$current_user = wp_get_current_user();

	wp_register_script( 'smplfy-demo-frontend-script', SMPLFY_NAME_PLUGIN_URL . '/includes_/js/frontend.js', array( 'jquery' ), null, true );
	wp_register_style( 'smplfy-demo-frontend-styles', SMPLFY_NAME_PLUGIN_URL . '/includes_/css/frontend.css' );

	wp_enqueue_script( 'smplfy-demo-frontend-script' );
	wp_enqueue_style( 'smplfy-demo-frontend-styles' );
}

add_action( 'wp_enqueue_scripts', 'SMPLFY\boilerplate\enqueue_boilerplate_frontend_scripts' );
