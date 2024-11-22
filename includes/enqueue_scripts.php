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

	wp_enqueue_script( 'heartbeat' );
	//Register script with the 'jquery' and 'heartbeat' to ensure those scripts are loaded before wp-heartbeat-example.js is executed
	wp_register_script( 'smplfy-demo-heartbeat-script', SMPLFY_NAME_PLUGIN_URL . 'public/js/wp-heartbeat-example.js', array(
		'jquery',
		'heartbeat'
	), null, true );

	wp_register_script( 'smplfy-demo-frontend-script', SMPLFY_NAME_PLUGIN_URL . '/includes_/js/frontend.js', array( 'jquery' ), null, true );
	wp_register_style( 'smplfy-demo-frontend-styles', SMPLFY_NAME_PLUGIN_URL . '/includes_/css/frontend.css' );

	wp_enqueue_script( 'smplfy-demo-frontend-script' );
	//Ensure our heartbeat script only runs on the page we want it to, to avoid excessive computation on the client side
	if ( $post->ID == 999 ) {
		wp_enqueue_script( 'smplfy-demo-heartbeat-script' );
		// Localize the script with data. Gives our script on the client side data from the backend to use
		wp_localize_script( 'smplfy-demo-heartbeat-script', 'heartbeat_object',
			array(
				'user_id' => $current_user->ID,
				'page_id' => $post->ID
			)
		);
	}

	wp_enqueue_style( 'smplfy-demo-frontend-styles' );


}

add_action( 'wp_enqueue_scripts', 'enqueue_boilerplate_frontend_scripts' );
