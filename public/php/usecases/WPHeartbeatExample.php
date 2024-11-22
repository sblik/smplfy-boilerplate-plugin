<?php

namespace SMPLFY\boilerplate;


class WPHeartbeatExample {
	private ExampleRepository $exampleRepository;

	public function __construct( ExampleRepository $exampleRepository ) {
		$this->exampleRepository = $exampleRepository;
	}

	function receive_heartbeat( array $response, array $data ) {
		// If we didn't receive our data, don't send any back.
		if ( empty( $data['custom_heartbeat_data'] ) ) {
			return $response;
		}

		//Assign the data received by client side to variable (to make easier to read when assigning data within to variables)
		$customData = $data['custom_heartbeat_data'];
		$userID     = $customData['userId'];

		$exampleEntity = $this->exampleRepository->get_one_for_user( $userID );

		if ( ! empty( $exampleEntity ) ) {
			//Build response to give back to client side
			$response['entity_exists']                = true;
			$response['example_entity']['first_name'] = $exampleEntity->nameFirst;
			$response['example_entity']['last_name']  = $exampleEntity->nameLast;
			$response['example_entity']['email']      = $exampleEntity->email;
		} else {
			$response['entity_exists'] = false;
		}

		return $response;
	}
}