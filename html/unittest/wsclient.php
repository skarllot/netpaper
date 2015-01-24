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

require_once '../lib/nusoap/nusoap.php';

$is_json = False;
$address = 'http://localhost/netpaper/ws/%s.php?wsdl';

if (!isset($_REQUEST['method']))
    return;

header('Content-type: application/json');
extract($_REQUEST);
if (!isset($method) || empty($method))
    $method = '';
if (!isset($token) || empty($token))
    $token = '';
if (!isset($class) || empty($class))
    $class = '';

switch ($class) {
    case "session":
        $address = sprintf($address, 'session');
        switch ($method) {
            case "create":
                callSOAP($method, array());
                break;
            case "destroy":
                callSOAP($method, array($token));
                break;
            default:
                echo json_encode(array('error' =>
                    array('code' => NULL,
                        'message' => 'Invalid parameters supplied',
                        'innerError' => NULL)));
                break;
        }
        break;
    case "logon":
        $address = sprintf($address, 'logon');
        switch ($method) {
            case "createFirstLogin":
                callSOAP($method, array($token, $user, $password, $email, $name));
                break;
            case "doLogon":
                callSOAP($method, array($token, $user, $password));
                break;
            case "getLanguages":
                callSOAP($method, array($token));
                break;
            case "hasUsers":
                callSOAP($method, array($token));
                break;
            default:
                echo json_encode(array('error' =>
                    array('code' => NULL,
                        'message' => 'Invalid parameters supplied',
                        'innerError' => NULL)));
                break;
        }
        break;
    default:
        echo json_encode(array('error' =>
            array('code' => NULL,
                'message' => 'Invalid class supplied',
                'innerError' => NULL)));
        break;
}

function callSOAP($name, $params) {
    global $address;

    $wsdl = $address;
    $client = new soapclient($wsdl, true);
    $error = $client->getError();
    if ($error) {
        echo json_encode(array('error' =>
            array('code' => NULL,
                'message' => 'Error creating instance of soapclient',
                'innerError' => $error)));
        return;
    }

    $result = $client->call($name, $params);
    if (isset($result->faultcode)) {
        echo json_encode(array('error' =>
            array('code' => NULL,
                'message' => $result->fault,
                'innerError' => NULL)));
        return;
    }

    $error = $client->getError();
    if ($error) {
        echo json_encode(array('error' =>
            array('code' => $result['faultcode'],
                'message' => $result['faultstring'],
                'innerError' => NULL)));
        return;
    }
    
    $ret = json_encode(array('result' => $result));
    if (!$ret) {
        echo json_encode(array('error' =>
            array('code' => json_last_error(),
                'message' => json_last_error_msg(),
                'innerError' => NULL))
            );
    }
    echo $ret;

    return $result;
}

/*
  vim: ts=4 sw=4
 */
