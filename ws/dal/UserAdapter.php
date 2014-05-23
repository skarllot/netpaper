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

namespace dal;

require_once 'dal/Adapter.php';
require_once 'dal/UserRow.php';

/**
 * Adapter to connecto to User database table.
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class UserAdapter extends Adapter {

    const SQL_GET_USER_COUNT = 'SELECT count(id) AS count FROM user';
    const SQL_CREATE_USER = 'INSERT INTO user
		(user, password, email, name, admin, is_ldap, language)
		VALUES (:user, :pass, :email, :name, :admin, :isldap, :lang)';
    const SQL_IS_LDAP = 'SELECT is_ldap FROM user WHERE user = :user';
    const SQL_GET_USER = 'SELECT id, user, password, email, name, admin,
        is_ldap, language FROM user WHERE user = :user';
    const SQL_GET_USER_WITH_PASSWORD = 'SELECT id, user, password, email, name,
        admin, is_ldap, language FROM user
        WHERE user = :user AND password = :pass';

    /**
     * 
     * @param \dal\UserRow $row
     * @return boolean
     */
    function createUser(UserRow $row) {
        $ret = $this->conn->insert(self::SQL_CREATE_USER,
                array(':user' => $row->user, ':pass' => $row->password,
                    ':email' => $row->email, ':name' => $row->name,
                    ':admin' => $row->admin, ':isldap' => $row->is_ldap,
                    ':lang' => $row->language)
                );
        if ($ret['count'] != 1)
            return False;

        return True;
    }

    /**
     * 
     * @param string $user
     * @param string $password
     * @return \dal\UserRow
     */
    function getUser($user, $password = NULL) {
        if (!isset($password)) {
            $rows = $this->conn->query(self::SQL_GET_USER, array(':user' => $user));
        } else {
            $rows = $this->conn->query(self::SQL_GET_USER_WITH_PASSWORD,
                    array(':user' => $user, ':pass' => $password));
        }
        
        if (count($rows) != 1)
            return NULL;
        return UserRow::getInstance($rows[0]);
    }

    /**
     * 
     * @return integer
     */
    function getUsersCount() {
        $rows = $this->conn->query(self::SQL_GET_USER_COUNT, array());
        if (count($rows) != 1)
            return -1;

        return $rows[0]['count'];
    }

    function isLdap($user) {
        $rows = $this->conn->query(self::SQL_IS_LDAP, array(':user' => $user));
        if (count($rows) != 1)
            return False;

        return ((bool) $rows[0]['is_ldap']);
    }

}

/*
vim: ts=4 sw=4
*/
