<?php

/**
 * Adapter for handling Gravity Forms events
 */
class SMPLFY_GravityFormsAdapter {

	private ExampleUsecase $exampleUsecase;

	public function __construct( ExampleUsecase $exampleUsecase ) {
		$this->exampleUsecase = $exampleUsecase;

		$this->register_hooks();
		$this->register_filters();
	}

	/**
	 * Register gravity forms hooks to handle custom logic
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'gform_after_submission_9999', [ $this->exampleUsecase, 'example_function' ], 10, 4 );
	}

	/**
	 * Register gravity forms filters to handle custom logic
	 *
	 * @return void
	 */
	public function register_filters() {

	}
}