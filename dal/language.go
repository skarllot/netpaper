/*
 * Copyright (C) 2015 Fabrício Godoy <skarllot@gmail.com>
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

package dal

import (
	"github.com/go-gorp/gorp"
)

const (
	SQL_GET_LANGUAGE_COUNT = `SELECT count(id) AS count FROM language`
)

func LanguageCount(txn *gorp.Transaction) (int64, error) {
	count, err := txn.SelectInt(SQL_GET_LANGUAGE_COUNT)
	if err != nil {
		return -1, err
	}
	return count, err
}
