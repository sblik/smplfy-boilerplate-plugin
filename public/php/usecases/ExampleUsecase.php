<?php
/**
 *  A usecase generally refers to a specific human action or the result of an action that happens on the site and contains
 * various functions that should happen as a result.
 *
 *  One or more of the functions are usually tied to a hook e.g. a Gravity Forms "after_submission" hook. See the Gravity Forms Adapter for how they are linked.
 */

namespace SMPLFY\boilerplate;

use SmplfyCore\SMPLFY_Log;

class ExampleUsecase {
	private ExampleRepository $exampleRepository;

	public function __construct( ExampleRepository $exampleRepository ) {
		$this->exampleRepository = $exampleRepository;
	}

	function example_function( $entry ) {
		$exampleEntry = $this->exampleRepository->get_one_for_current_user();

		SMPLFY_Log::info( "ENTRY: ", $entry );
	}

}