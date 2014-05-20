<?php

	require_once("lib/nusoap/nusoap.php");
	require_once("methods.php");

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

	$server->register('createFirstLogin',
		array('token' => 'xsd:string',
			'user' => 'xsd:string',
			'password' => 'xsd:string',
			'email' => 'xsd:string',
			'name' => 'xsd:string'),
		array('return' => 'xsd:boolean'),
		'urn:netpaper',
		'urn:netpaper#createFirstLogin',
		'rpc',
		'encoded',
		'Creates a new login when no other logins exists.'
	);
	$server->register('createSession',
		array(),
		array('return' => 'xsd:string'),
		'urn:netpaper',
		'urn:netpaper#createSession',
		'rpc',
		'encoded',
		'Creates a new session token.'
	);
	$server->register('destroySession',
		array('token' => 'xsd:string'),
		array('return' => 'xsd:boolean'),
		'urn:netpaper',
		'urn:netpaper#destroySession',
		'rpc',
		'encoded',
		'Destroys the requested session.'
	);
	$server->register('getDBVersion',
		array('token' => 'xsd:string'),
		array('return' => 'xsd:string'),
		'urn:netpaper',
		'urn:netpaper#getDBVersion',
		'rpc',
		'encoded',
		'Gets current version of database schema.'
	);
	$server->register('getLdapConfig',
		array('token' => 'xsd:string'),
		array('return' => 'tns:ldap'),
		'urn:netpaper',
		'urn:netpaper#getLdapConfig',
		'rpc',
		'encoded',
		'Gets LDAP configuration parameters.'
	);
	$server->register('hasUsers',
		array('token' => 'xsd:string'),
		array('return' => 'xsd:boolean'),
		'urn:netpaper',
		'urn:netpaper#hasUsers',
		'rpc',
		'encoded',
		'Returns whether has any user registered.'
	);
	$server->register('logon',
		array('token' => 'xsd:string',
			'user' => 'xsd:string',
			'password' => 'xsd:string'),
		array('return' => 'xsd:boolean'),
		'urn:netpaper',
		'urn:netpaper#logon',
		'rpc',
		'encoded',
		'Tries to log on using specified user and password.'
	);

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

/*
vim: ts=4 sw=4
*/
?>
