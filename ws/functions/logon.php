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
require_once 'bll/Logon.php';
require_once 'bll/Session.php';

function createFirstLogin($token, $user, $password, $email, $name) {
    try {
        $session = \bll\Session::getInstance($token);
        $logon = \bll\Logon::getInstance($session);
        return $logon->createFirstLogin($user, $password, $email, $name);
    } catch (Exception $ex) {
        return new nusoap_fault($ex->getCode(), 'logon.createFirstLogin',
                $ex->getMessage(), $ex);
    }
}

function doLogon($token, $user, $password) {
    try {
        $session = \bll\Session::getInstance($token);
        $logon = \bll\Logon::getInstance($session);
        return $logon->initialize($user, $password);
    } catch (Exception $ex) {
        return new nusoap_fault($ex->getCode(), 'logon.doLogon',
                $ex->getMessage(), $ex);
    }
}

function hasUsers($token) {
    try {
        $session = \bll\Session::getInstance($token);
        $logon = \bll\Logon::getInstance($session);
        return $logon->hasUsers();
    } catch (Exception $ex) {
        return new nusoap_fault($ex->getCode(), 'logon.hasUsers',
                $ex->getMessage(), $ex);
    }
}
