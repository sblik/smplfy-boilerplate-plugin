<?php

namespace SMPLFY\boilerplate;
class SMPLFY_SettingsModel {
	private bool $exampleCheckbox;
	private string $exampleText;
	private string $examplePassword;

	function __construct( bool $exampleCheckbox, string $exampleText, string $examplePassword ) {
		$this->exampleCheckbox = $exampleCheckbox;
		$this->exampleText     = $exampleText;
		$this->examplePassword = $examplePassword;
	}

	public function get_example_text(): string {
		return $this->exampleText;
	}

	public function get_example_password(): string {
		return $this->examplePassword;

	}

	public function is_example_checkbox(): bool {
		return $this->exampleCheckbox;
	}
}