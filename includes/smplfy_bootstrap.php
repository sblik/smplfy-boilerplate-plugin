<?php
/**
 *  Loads specified files and all files in specified directories initialises dependencies
 */

namespace SMPLFY\boilerplate;

use Exception;
use SmplfyCore\SMPLFY_Require;

function bootstrap_boilerplate_plugin() {
	require_boilerplate_dependencies();

	DependencyFactory::create_plugin_dependencies();
}

/**
 * When adding a new directory to the custom plugin, remember to require it here
 *
 * @return void
 */
function require_boilerplate_dependencies() {

	$require = new SMPLFY_Require( SMPLFY_NAME_PLUGIN_DIR );
	try {
		$require->file( 'includes/enqueue_scripts.php' );
		$require->file( 'admin/DependencyFactory.php' );

		$require->directory( 'admin/settings' );
		$require->directory( 'public/php/types' );
		$require->directory( 'public/php/entities' );
		$require->directory( 'public/php/repositories' );
		$require->directory( 'public/php/usecases' );
		$require->directory( 'public/php/adapters' );
	} catch ( Exception $e ) {
		error_log( $e->getMessage() );
	}
}

