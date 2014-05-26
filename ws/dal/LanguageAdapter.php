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
require_once 'dal/LanguageRow.php';

/**
 * Adapter to connect to User database table.
 *
 * @author Fabrício Godoy <skarllot@gmail.com>
 */
class LanguageAdapter extends Adapter {
    const SQL_GET_LANGUAGES = 'SELECT id, code, name FROM language';
    
    /**
     * 
     * @return \dal\LanguageRow[]
     */
    public function getLanguages() {
        $rows = $this->conn->query(LanguageAdapter::SQL_GET_LANGUAGES, array());
        
        $ret = array();
        foreach ($rows as $item) {
            $ret[] = LanguageRow::getInstance($item);
        }
        return $ret;
    }
}
