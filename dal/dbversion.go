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
	"github.com/skarllot/netpaper/models"
)

const (
	SQL_GET_DBVERSION_COUNT   = `SELECT count(value) AS count FROM dbversion`
	SQL_GET_DBVERSION_VERSION = `SELECT value FROM dbversion`
)

type DbVersionUndefinedError struct {
	msg   string
	count int
}

func (err DbVersionUndefinedError) Error() string {
	return err.msg
}

func DbVersionCount(txn *gorp.Transaction) (int64, error) {
	count, err := txn.SelectInt(SQL_GET_DBVERSION_COUNT)
	if err != nil {
		return -1, err
	}
	return count, err
}

func GetVersion(txn *gorp.Transaction) (string, error) {
	var qrows []models.DbVersion
	var err error

	_, err = txn.Select(&qrows, SQL_GET_DBVERSION_VERSION)
	if err != nil {
		return "", err
	}

	switch v := len(qrows); v {
	case 0:
		return "", DbVersionUndefinedError{"No version defined to current database", 0}
	case 1:
		return qrows[0].Value, nil
	default:
		return "", DbVersionUndefinedError{"Multiple versions defined to current database", v}
	}
}
