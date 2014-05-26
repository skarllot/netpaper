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
 * Represents a row from Ldap table.
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class LdapRow {
    /**
     *
     * @var string
     */
    public $domain_name;
    /**
     *
     * @var string
     */
    public $base_dn;
    /**
     *
     * @var string
     */
    public $servers_name;
    /**
     *
     * @var boolean
     */
    public $use_ssl;
    /**
     *
     * @var boolean
     */
    public $use_tls;
    /**
     *
     * @var integer
     */
    public $port;
    /**
     *
     * @var string
     */
    public $filter;
    
    /**
     * 
     * @param array $row
     * @return \dal\LdapRow
     */
    public static function getInstance(array $row) {
        $ret = new LdapRow();
        $ret->domain_name = $row['domain_name'];
        $ret->base_dn = $row['base_dn'];
        $ret->servers_name = $row['servers_name'];
        $ret->use_ssl = ((bool)$row['use_ssl']);
        $ret->use_tls = ((bool)$row['use_tls']);
        $ret->port = ((int)$row['port']);
        $ret->filter = $row['filter'];
        return $ret;
    }
}

/*
vim: ts=4 sw=4
*/
