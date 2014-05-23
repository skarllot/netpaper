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
        $this->session = $session;
    }
            
    function __destruct() {
        $this->conn = NULL;
    }
    
    /**
     * 
     * @param string $user
     * @param string $password
     * @param string $email
     * @param string $name
     * @return boolean
     */
    function createFirstLogin($user, $password, $email, $name) {
        if ($this->hasUsers())
            return FALSE;
        
        $userrow = new \dal\UserRow();
        $userrow->user = $user;
        $userrow->password = self::saltPassword($user, $password);
        $userrow->email = $email;
        $userrow->name = $name;
        $userrow->admin = TRUE;
        $userrow->is_ldap = FALSE;
        $userrow->language = 1;
        return $this->duser->createUser($userrow);
    }
    
    /**
     * 
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
     * 
     * @return boolean
     */
    function hasUsers() {
        return ($this->duser->getUsersCount() > 0);
    }
    
    /**
     * 
     * @param string $user
     * @param string $password
     * @return boolean
     */
    function initialize($user, $password) {
        $this->session->setUser(NULL);
        $this->session->setIsAdmin(FALSE);
        
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
        
        $this->session->setUser($user);
        $this->session->setIsAdmin($userrow->admin);
        $this->session->setLanguage($userrow->language);
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
