<?php

/* 
 * Copyright (C) 2014 Fabrício Godoy <skarllot@gmail.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once 'lib/nusoap/nusoap.php';
require_once 'functions/logon.php';

if (isset($_REQUEST['json']) && $_REQUEST['json'] == 1) {
    require_once 'json/logon.php';
    return;
}

$namespace = 'urn:netpaper:logon';
$server = new soap_server;
$server->configureWSDL('netpaper', $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

$server->register('createFirstLogin',
    array('token' => 'xsd:string',
        'user' => 'xsd:string',
        'password' => 'xsd:string',
        'email' => 'xsd:string',
        'name' => 'xsd:string'),
    array('return' => 'xsd:boolean'),
    $namespace,
    $namespace . '#createFirstLogin',
    'rpc',
    'encoded',
    'Creates a new login when no other logins exists.'
);
$server->register('doLogon',
    array('token' => 'xsd:string',
        'user' => 'xsd:string',
        'password' => 'xsd:string'),
    array('return' => 'xsd:boolean'),
    $namespace,
    $namespace . '#logon',
    'rpc',
    'encoded',
    'Tries to log on using specified user and password.'
);
$server->register('hasUsers',
    array('token' => 'xsd:string'),
    array('return' => 'xsd:boolean'),
    $namespace,
    $namespace . '#hasUsers',
    'rpc',
    'encoded',
    'Returns whether has any user registered.'
);

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

/*
vim: ts=4 sw=4
*/
