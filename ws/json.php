<?php

	require_once("methods.php");

	header('Content-type: application/json');
	extract($_REQUEST);

	if(!isset($method) || empty($method))
		$method = '';
	if(!isset($token) || empty($token))
		$token = '';

	switch ($method) {
		case "createFirstLogin":
			$result = createFirstLogin($token, $user, $password, $email, $name);
			break;
		case "createSession":
			$result = createSession();
			break;
		case "destroySession":
			$result = destroySession($token);
			break;
		case "getDBVersion":
			$result = getDBVersion($token);
			break;
		case "getLdapConfig":
			$result = getLdapConfig($token);
			break;
		case "hasUsers":
			$result = hasUsers($token);
			break;
		case "logon":
			$result = logon($token, $user, $password);
			break;
		default:
			echo json_encode(array('error' =>
				array('code' => NULL,
				'description' => 'Invalid parameters supplied',
				'innerError' => NULL)));
			return;
	}

	if ($result instanceof nusoap_fault)
		echo json_encode(array('error' =>
			array('code' => $result->faultcode,
			'description' => $result->faultstring,
			'innerError' => NULL))
		);
	else
		echo json_encode(array('result'=>$result));

/*
vim: ts=4 sw=4
*/
?>
