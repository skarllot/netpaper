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

header('Content-type: application/json');
extract($_REQUEST);

if(!isset($method) || empty($method)) {
    $method = '';
}
if(!isset($token) || empty($token)) {
    $token = '';
}

switch ($method) {
    case "create":
        $result = create();
        break;
    case "destroy":
        $result = destroy($token);
        break;
    default:
        echo json_encode(array('error' =>
            array('code' => NULL,
            'description' => 'Invalid parameters supplied',
            'innerError' => NULL)));
        return;
}

if ($result instanceof nusoap_fault) {
    echo json_encode(array('error' =>
        array('code' => $result->faultcode,
        'description' => $result->faultstring,
        'innerError' => NULL))
    );
} else {
    echo json_encode(array('result'=>$result));
}

/*
vim: ts=4 sw=4
*/
