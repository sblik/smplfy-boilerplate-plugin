<?php
/**
 * recursively require all php files in a directory
 *
 * @param $dir
 *
 * @return void
 */

namespace SMPLFY\boilerplate;
function require_directory( $dir ) {
	if ( ! realpath( $dir ) ) {
		$dir = SMPLFY_NAME_PLUGIN_DIR . $dir; // Append plugin dir if it is a relative path
	}

	$items = glob( $dir . '/*' );

	foreach ( $items as $path ) {
		$isFile = preg_match( '/\.php$/', $path );

		if ( $isFile ) {
			require_once $path;
		} elseif ( is_dir( $path ) ) {
			require_directory( $path );
		}
	}
}

/**
 * require a single file
 *
 * @param $filePath
 *
 * @return void
 */
function require_file( $filePath ) {
	require_once SMPLFY_NAME_PLUGIN_DIR . $filePath;
}