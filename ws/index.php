<?php

	require_once("lib/nusoap/nusoap.php");
	require_once("methods.php");

    $namespace = 'urn:netpaper';
	$server = new soap_server;
	$server->configureWSDL('netpaper', $namespace);
	$server->wsdl->schemaTargetNamespace = $namespace;

	$server->wsdl->addComplexType('ldap', 'complexType', 'struct', 'all', '',
		array('domain_name' => array('name' => 'domain_name', 'type' => 'xsd:string'),
			'base_dn' => array('name' => 'base_dn', 'type' => 'xsd:string'),
			'servers_name' => array('name' => 'servers_name', 'type' => 'xsd:string'),
			'use_ssl' => array('name' => 'use_ssl', 'type' => 'xsd:boolean'),
			'use_tls' => array('name' => 'use_tls', 'type' => 'xsd:boolean'),
			'port' => array('name' => 'port', 'type' => 'xsd:unsignedShort'),
			'filter' => array('name' => 'filter', 'type' => 'xsd:string'))
	);

	$server->register('getLdapConfig',
		array('token' => 'xsd:string'),
		array('return' => 'tns:ldap'),
		$namespace,
		$namespace . '#getLdapConfig',
		'rpc',
		'encoded',
		'Gets LDAP configuration parameters.'
	);

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($HTTP_RAW_POST_DATA);

/*
vim: ts=4 sw=4
*/
?>
