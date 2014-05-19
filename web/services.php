<?php
	require_once("config.inc.php");
	require_once("lib/nusoap/nusoap.php");
	require_once("session.php");
	require_once("db/connection.php");
	require_once("db/dbversion.php");
	require_once("db/ldap.php");
	require_once("db/user.php");

	$server = new soap_server;
	$server->configureWSDL('netpaper', 'urn:netpaper');
	$server->wsdl->schemaTargetNamespace = 'urn:netpaper';

	$server->wsdl->addComplexType('ldap', 'complexType', 'struct', 'all', '',
		array('domain_name' => array('name' => 'domain_name', 'type' => 'xsd:string'),
			'base_dn' => array('name' => 'base_dn', 'type' => 'xsd:string'),
			'servers_name' => array('name' => 'servers_name', 'type' => 'xsd:string'),
			'use_ssl' => array('name' => 'use_ssl', 'type' => 'xsd:boolean'),
			'use_tls' => array('name' => 'use_tls', 'type' => 'xsd:boolean'),
			'port' => array('name' => 'port', 'type' => 'xsd:unsignedShort'),
			'filter' => array('name' => 'filter', 'type' => 'xsd:string'))
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
	$server->register('getDBVersion',
		array('auth' => 'xsd:string'),
		array('return' => 'xsd:string'),
		'urn:netpaper',
		'urn:netpaper#getDBVersion',
		'rpc',
		'encoded',
		'Gets current version of database schema.'
	);
	$server->register('getLdapConfig',
		array('auth' => 'xsd:string'),
		array('return' => 'tns:ldap'),
		'urn:netpaper',
		'urn:netpaper#getDBVersion',
		'rpc',
		'encoded',
		'Gets current version of database schema.'
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

	function getLdapConfig($auth) {
		$session = initializeSession($auth);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$ldap = new Ldap($conn);
		return $ldap->getConfig();
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
