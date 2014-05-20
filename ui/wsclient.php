<?php
	require_once("config.inc.php");
	require_once("lib/nusoap/nusoap.php");

	$is_json = False;

	if (isset($_REQUEST['method'])) {
		header('Content-type: application/json');
		extract($_REQUEST);
		$is_json = True;
		if(!isset($method) || empty($method))
			$method = '';
		if(!isset($token) || empty($token))
			$token = '';

		switch ($method) {
			case "createFirstLogin":
				createFirstLogin($token, $user, $password, $email, $name);
				break;
			case "createSession":
				createSession();
				break;
			case "destroySession":
				destroySession($token);
				break;
			case "getDBVersion":
				getDBVersion($token);
				break;
			case "getLdapConfig":
				getLdapConfig($token);
				break;
			case "hasUsers":
				hasUsers($token);
				break;
			case "logon":
				logon($token, $user, $password);
				break;
			default:
				echo json_encode(array('error' =>
					array('code' => NULL,
					'description' => 'Invalid parameters supplied',
					'innerError' => NULL)));
				break;
		}
	}

	function createFirstLogin($token, $user, $password, $email, $name) {
		return callSOAP('createFirstLogin',
			array($token, $user, $password, $email, $name));
	}

	function createSession() {
		return callSOAP('createSession', array());
	}

	function destroySession($token) {
		return callSOAP('destroySession', array($token));
	}

	function getDBVersion($token) {
		return callSOAP('getDBVersion', array($token));
	}

	function getLdapConfig($token) {
		return callSOAP('getLdapConfig', array($token));
	}

	function hasUsers($token) {
		return callSOAP('hasUsers', array($token));
	}

	function logon($token, $user, $password) {
		return callSOAP('logon', array($token, $user, $password));
	}

	function callSOAP($name, $params) {
		global $cfg;
		global $is_json;

		$wsdl = $cfg["WS_ADDRESS"];
		$client = new soapclient($wsdl, true);
		$error = $client->getError();
		if ($error) {
			echo json_encode(array('error' => 
				array('code' => NULL,
				'description' => 'Error creating instance of soapclient',
				'innerError' => $error)));
			return;
		}

		$result = $client->call($name, $params);
		if (isset($result->faultcode)) {
			echo json_encode(array('error' =>
				array('code' => NULL,
				'description' => $result->fault,
				'innerError' => NULL)));
			return;
		}

		$error = $client->getError();
		if ($error) {
			echo json_encode(array('error' =>
				array('code' => $result['faultcode'],
				'description' => $result['faultstring'],
				'innerError' => NULL)));
			return;
		}

		if ($is_json)
			echo json_encode(array('result'=>$result));

		return $result;
	}

/*
vim: ts=4 sw=4
*/
?>
