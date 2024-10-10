<?php
/**
 *  Loads specified files and all files in specified directories initialises dependencies
 */

namespace SMPLFY\boilerplate;

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

	require_file( 'includes_/enqueue_scripts.php' );
	require_file( 'includes_/admin/DependencyFactory.php' );

	require_directory( 'includes_/public/php/types' );
	require_directory( 'includes_/public/php/entities' );
	require_directory( 'includes_/public/php/repositories' );
	require_directory( 'includes_/public/php/usecases' );
	require_directory( 'includes_/public/php/adapters' );

}

