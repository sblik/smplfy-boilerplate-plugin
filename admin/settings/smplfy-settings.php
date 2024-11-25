<?php

namespace SMPLFY\boilerplate;

use SmplfyCore\SMPLFY_Log;

add_action( 'admin_init', 'SMPLFY\boilerplate\smplfy_settings_init' );

/**
 * custom option and settings
 */
function smplfy_settings_init(): void {
	register_setting( 'smplfy_boiler_plate', 'smplfy_boilerplate_options' );
	//SMPLFY Core Settings
	add_settings_section(
		'smplfy_boilerplate_section_developers_main',
		'boilerplate Settings',
		'SMPLFY\boilerplate\smplfy_boilerplate_section_developers_callback',
		'smplfy_boiler_plate'
	);
	smplfy_add_main_settings_field( 'smplfy_boilerplate_example_checkbox', 'Boilerplate Example Checkbox: ' );
	smplfy_add_main_settings_field( 'smplfy_boilerplate_example_text', 'Boilerplate Example Text:' );
	smplfy_add_main_settings_field( 'smplfy_boilerplate_example_password', 'Boilerplate Example Password:' );

}

//Function to make creating setting field easier
function smplfy_add_main_settings_field( string $id, string $title ): void {
	$callback = "SMPLFY\boilerplate\\{$id}_cb";
	add_settings_field(
		$id,
		$title,
		$callback,
		'smplfy_boiler_plate',
		'smplfy_boilerplate_section_developers_main',
		array( 'label_for' => $id, )
	);
}

/**
 * @param array $args The settings array, defining title, id, callback.
 */
function smplfy_boilerplate_section_developers_callback( array $args ): void {
	?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>"></p>
	<?php
}

/**
 * A function is required for each field ID. In the format *field_id_from_add_setting_field*_cb
 */

//Add the setting smplfy_boilerplate_example_checkbox as a checkbox
function smplfy_boilerplate_example_checkbox_cb( $args ): void {
	smplfy_add_setting_field( $args, 'smplfy_boilerplate_example_checkbox', 'checkbox' );
}

//Add the setting smplfy_boilerplate_example_checkbox as a text field
function smplfy_boilerplate_example_text_cb( $args ): void {
	smplfy_add_setting_field( $args, 'smplfy_boilerplate_example_text', 'text' );
}

//Add the setting smplfy_boilerplate_example_checkbox as a password field (characters hidden when viewed in page)
function smplfy_boilerplate_example_password_cb( $args ): void {
	smplfy_add_setting_field( $args, 'smplfy_boilerplate_example_password', 'password' );
}

/**
 * @param array $args
 * @param string $setting_id
 * @param string $type
 */
function smplfy_add_setting_field( array $args, string $setting_id, string $type ): void {
	$value = get_smplfy_setting_value( $setting_id );

	if ( $type == 'checkbox' ) {
		smplfy_checkbox_field( $args, $value, $type );

		return;
	}

	smplfy_field( $args, $value, $type );
}

/**
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 * @param $value
 * @param $type
 */
function smplfy_checkbox_field( array $args, $value, $type ) {
	$value = checked( "on", $value, false );

	?>
    <input type="<?php echo esc_attr( $type ) ?>"
           id="<?php echo esc_attr( $args['label_for'] ); ?>"
           name="smplfy_boilerplate_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
		<?php echo $value ?>
    >
	<?php
}

function smplfy_field( $args, $value, $type ) {
	?>
    <input type="<?php echo esc_attr( $type ) ?>"
           id="<?php echo esc_attr( $args['label_for'] ); ?>"
           name="smplfy_boilerplate_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
           value="<?php echo $value ?>"
    >
	<?php
}

function get_smplfy_setting_value( $setting_id ): ?string {
	$options = get_option( 'smplfy_boilerplate_options' );

	return esc_attr( $options[ $setting_id ] );
}

/**
 * This function allows for the value of the settings to be used in the plugin
 * See SMPLFY_SettingsModel
 * Example:
 *  $pluginSettings = get_smplfy_boilerplate_settings();
 *  $password = $pluginSettings->get_example_password();
 *
 */
function get_smplfy_boilerplate_settings(): SMPLFY_SettingsModel {
	$settings         = get_option( 'smplfy_boilerplate_options' );
	$example_checkbox = esc_attr( $settings['smplfy_boilerplate_checkbox'] );
	$example_text     = esc_attr( $settings['smplfy_boilerplate_text'] );
	$example_password = esc_attr( $settings['smplfy_boilerplate_password'] );

	return new SMPLFY_SettingsModel( $example_checkbox, $example_text, $example_password );
}