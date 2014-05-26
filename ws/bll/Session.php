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

namespace bll;
require_once 'bll/InvalidSessionException.php';

class Session
{
	const VALIDATION_ID = 'wBSsq2MYvFbOcdcv';
    /**
     *
     * @var string
     */
    private $token;
    
    private function __construct($token) {
        $this->token = $token;
    }

    public function getIsAdmin() {
		return (isset($_SESSION['admin']) &&
			((bool)$_SESSION['admin']));
	}

	private function getIsValid() {
		return (isset($_SESSION['VALIDATION_ID']) &&
			$_SESSION['VALIDATION_ID'] == self::VALIDATION_ID);
	}

	public function getLanguage() {
		if (!isset($_SESSION['lang']))
			return -1;
		return $_SESSION['lang'];
	}
    
    /**
     * 
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    public function getUser() {
		if (!isset($_SESSION['user']))
			return NULL;
		return $_SESSION['user'];
	}

	public function setIsAdmin($isadmin) {
		$_SESSION['admin'] = $isadmin;
	}

	private function setIsValid($isvalid) {
		if ($isvalid) {
			$_SESSION['VALIDATION_ID'] = self::VALIDATION_ID;
        } else {
			$_SESSION['VALIDATION_ID'] = NULL;
        }
	}

	public function setLanguage($lang) {
		$_SESSION['lang'] = $lang;
	}

	public function setUser($user) {
		$_SESSION['user'] = $user;
	}

    /**
     * Creates a new session and returns a token.
     * @return \bll\Session
     */
	public static function create() {
		session_start();
        
        $ret = new Session(session_id());
        $ret->setIsValid(TRUE);
        return $ret;
	}

    /**
     * 
     * @param string $token
     * @return \bll\Session
     * @throws InvalidSessionException
     */
    public static function getInstance($token = NULL) {
		if (isset($token) && !empty($token)) {
			session_id($token);
        } else {
            $token = session_id();
        }
        if (empty($token)) {
            throw new InvalidSessionException();
        }

		session_start();
        $ret = new Session($token);
        if (!$ret->getIsValid()) {
            throw new InvalidSessionException();
        }
        return $ret;
    }

    /**
     * Destroys the requested session.
     * @return boolean
     */
	public function destroy() {
        if (!$this->getIsValid())
            return FALSE;
        $this->setIsValid(FALSE);

		$_SESSION = array();
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time() - 42000, '/');
		session_destroy();
		return True;
	}
}

/*
vim: ts=4 sw=4
*/
