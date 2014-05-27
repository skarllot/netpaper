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
require_once 'bll/Session.php';
require_once 'bll/InvalidSessionException.php';
require_once 'dal/Connection.php';
require_once 'dal/UserAdapter.php';
require_once 'dal/UserRow.php';
require_once 'dal/LdapAdapter.php';
require_once 'dal/LanguageAdapter.php';
require_once 'dal/LanguageRow.php';

/**
 * Logon procedures
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class Logon {
    /**
     *
     * @var \dal\Connection
     */
    private $conn;
    /**
     *
     * @var \dal\UserAdapter
     */
    private $duser;
    /**
     *
     * @var \dal\LanguageAdapter
     */
    private $dlang;
    /**
     *
     * @var \dal\LdapAdapter
     */
    private $dldap;
    /**
     *
     * @var \bll\Session
     */
    private $session;

    private function __construct(Session $session) {
        $this->conn = new \dal\Connection();
        $this->conn->connect();
        $this->duser = new \dal\UserAdapter($this->conn);
        $this->dldap = new \dal\LdapAdapter($this->conn);
        $this->dlang = new \dal\LanguageAdapter($this->conn);
        $this->session = $session;
    }
            
    function __destruct() {
        $this->conn = NULL;
    }
    
    /**
     * Creates a new login when no other logins exists.
     * @param string $user
     * @param string $password
     * @param string $email
     * @param string $name
     * @param int $langid
     * @return boolean
     */
    function createFirstLogin($user, $password, $email, $name, $langid) {
        if ($this->hasUsers())
            return FALSE;
        
        $lang = $this->dlang->getLanguageById($langid);
        if (!isset($lang)) {
            return FALSE;
        }
        
        $userrow = new \dal\UserRow();
        $userrow->user = $user;
        $userrow->password = self::saltPassword($user, $password);
        $userrow->email = $email;
        $userrow->name = $name;
        $userrow->admin = TRUE;
        $userrow->is_ldap = FALSE;
        $userrow->language = $langid;
        return $this->duser->createUser($userrow);
    }
    
    /**
     * Gets a new instance of Logon class.
     * @param \bll\Session $session
     * @return \bll\Logon
     * @throws \bll\InvalidSessionException
     */
    public static function getInstance(Session $session) {
        if (!isset($session)) {
            throw new \bll\InvalidSessionException();
        }
        
        return new Logon($session);
    }
    
    /**
     * Get supported languages list.
     * @return \dal\LanguageRow[]
     */
    function getLanguages() {
        return $this->dlang->getLanguages();
    }

    /**
     * Returns whether has any user registered.
     * @return boolean
     */
    function hasUsers() {
        return ($this->duser->getUsersCount() > 0);
    }
    
    /**
     * Tries to log on using specified user and password.
     * @param string $user
     * @param string $password
     * @return boolean
     */
    function initialize($user, $password) {
        $this->session->setUser(NULL);
        $this->session->setLanguage(NULL);
        
        $userrow = $this->duser->getUser($user);
        if (!isset($userrow)) {
            return FALSE;
        }
        
        if ($userrow->is_ldap) {
            $ldaprow = $this->dldap->getConfig();
            if (!isset($ldaprow)) {
                return FALSE;
            }
            
            if (!\dal\LdapAdapter::validateUser(
                    $ldaprow->servers_name, $ldaprow->domain_name,
                    $user, $password)) {
                return FALSE;
            }
        } else {
            $password = self::saltPassword($user, $password);
            if ($userrow->password != $password) {
                return FALSE;
            }
        }
        
        $lang = $this->dlang->getLanguageById($userrow->language);
        $this->session->setUser($userrow);
        $this->session->setLanguage($lang);
        return TRUE;
    }


    private static function saltPassword($user, $password) {
		return hash('sha256',
			sha1(strval(strlen($user))).$user.
			sha1(strval(strlen($password))).$password.
			hash('sha256', $user)
		);
	}
}
