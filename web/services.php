<?php
	require_once("config.inc.php");
	require_once("lib/nusoap/nusoap.php");
	require_once("session.php");
	require_once("db/connection.php");
	require_once("db/dbversion.php");

	$server = new soap_server;
	$server->configureWSDL('netpaper', 'urn:netpaper');
	$server->wsdl->schemaTargetNamespace = 'urn:netpaper';

	$server->register('getDBVersion',
		array('auth' => 'xsd:string'),
		array('return' => 'xsd:string'),
		'urn:netpaper',
		'urn:netpaper#getDBVersion',
		'rpc',
		'encoded',
		'Gets current version of database schema.'
	);
	$server->register('createSession',
		array(),
		array('return' => 'xsd:string'),
		'urn:netpaper',
		'urn:netpaper#createSession',
		'rpc',
		'encoded',
		'Creates a new authentication token.'
	);
	$server->register('destroySession',
		array('auth' => 'xsd:string'),
		array('return' => 'xsd:boolean'),
		'urn:netpaper',
		'urn:netpaper#destroySession',
		'rpc',
		'encoded',
		'Destroys the requested session.'
	);

	function createSession() {
		$session = new Session();
		return $session->create();
	}

	function destroySession($auth) {
		$session = new Session();
		return $session->destroy($auth);
	}

	function getDBVersion($auth) {
		$session = initializeSession($auth);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$dbversion = new DBVersion($conn);
		return $dbversion->getVersion();
	}

	function initializeSession($auth) {
		$session = new Session();
		if (!$session->start($auth))
			return NULL;

		return $session;
	}

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

/*
vim: ts=4 sw=4
*/
?>
