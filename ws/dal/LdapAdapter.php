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
require_once 'dal/LdapRow.php';
require_once 'lib/ldap.php';

/**
 * Adapter to connecto to Ldap database table.
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class LdapAdapter extends Adapter {
    const SQL_GET_CONFIG = 'SELECT domain_name, base_dn, servers_name,
			use_ssl, use_tls, port, filter FROM ldap';
    
    /**
     * 
     * @return \dal\LdapRow
     */
    function getConfig() {
        $rows = $this->conn->query(self::SQL_GET_CONFIG, array());
        if (count($rows) != 1)
            return NULL;
        
        return \dal\LdapRow::getInstance($rows[0]);
    }
    
    /**
     * 
     * @param string $servers
     * @param string $domain
     * @param string $user
     * @param string $password
     * @return boolean
     */
    public static function validateUser($servers, $domain, $user, $password) {
        foreach (split('[; ,&]', $servers) as $item) {
            $ldap = new \ldap\LDAP($item, $domain, $user, $password);
            $users = $ldap->get_users($user);
            
            if (!empty($users[0]['name']))
                return TRUE;
        }
        
        return FALSE;
    }
}

/*
vim: ts=4 sw=4
*/
