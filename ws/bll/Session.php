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

class Session
{
	const VALIDATION_ID = 'wBSsq2MYvFbOcdcv';

	public static function getIsAdmin() {
		return (isset($_SESSION['admin']) &&
			((bool)$_SESSION['admin']));
	}

	public static function getIsValid() {
		return (isset($_SESSION['VALIDATION_ID']) &&
			$_SESSION['VALIDATION_ID'] == self::VALIDATION_ID);
	}

	public static function getLanguage() {
		if (!isset($_SESSION['lang']))
			return -1;
		return $_SESSION['lang'];
	}

	public static function getUser() {
		if (!isset($_SESSION['user']))
			return NULL;
		return $_SESSION['user'];
	}

	public static function setIsAdmin($isadmin) {
		$_SESSION['admin'] = $isadmin;
	}

	public static function setIsValid($isvalid) {
		if ($isvalid) {
			$_SESSION['VALIDATION_ID'] = self::VALIDATION_ID;
        } else {
			$_SESSION['VALIDATION_ID'] = NULL;
        }
	}

	public static function setLanguage($lang) {
		$_SESSION['lang'] = $lang;
	}

	public static function setUser($user) {
		$_SESSION['user'] = $user;
	}

	public static function createToken() {
		session_start();
		self::setIsValid(True);
		return session_id();
	}

	public static function setToken($token = NULL) {
		if (isset($token) && !empty($token))
			session_id($token);

		session_start();

		if (!self::getIsValid()) {
			self::destroyToken();
			return False;
		}
		return True;
	}

	public static function destroyToken($token = NULL) {
		if (isset($token) && !empty($token)) {
			session_id($token);
			session_start();
		}

		if (!self::getIsValid())
			return False;
		self::setIsValid(False);

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