<?php

namespace SMPLFY\boilerplate;
add_action( 'admin_menu', 'SMPLFY\boilerplate\smplfy_settings_page' );

/**
 * Add the top level menu page.
 */
function smplfy_settings_page() {
	add_menu_page(
		'boilerplate Plugin',
		'boilerplate',
		'manage_options',
		'boilerplate',
		'SMPLFY\boilerplate\smplfy_settings_page_html'
	);
}

function smplfy_settings_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error(
			'smplfy_messages',
			'smplfy_message',
			'Settings Saved',
			'updated'
		);
	}

	settings_errors( 'smplfy_messages' );
	?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
			<?php
			settings_fields( 'smplfy_boiler_plate' );
			do_settings_sections( 'smplfy_boiler_plate' );
			submit_button( 'Save Settings' );
			?>
        </form>
    </div>
	<?php
}