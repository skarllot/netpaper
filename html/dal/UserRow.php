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

/**
 * Represents a row from User table.
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class UserRow {
    /**
     *
     * @var int
     */
    public $id;
    /**
     *
     * @var string
     */
    public $user;
    /**
     *
     * @var string
     */
    public $password;
    /**
     *
     * @var string
     */
    public $email;
    /**
     *
     * @var string
     */
    public $name;
    /**
     *
     * @var boolean
     */
    public $admin;
    /**
     *
     * @var boolean
     */
    public $is_ldap;
    /**
     *
     * @var int
     */
    public $language;
    
    public static function getInstance(array $row) {
        $ret = new UserRow();
        $ret->id = $row['id'];
        $ret->user = $row['user'];
        $ret->password = $row['password'];
        $ret->email = $row['email'];
        $ret->name = $row['name'];
        $ret->admin = $row['admin'];
        $ret->is_ldap = $row['is_ldap'];
        $ret->language = $row['language'];
        return $ret;
    }
}
