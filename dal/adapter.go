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
	"database/sql"
	"github.com/go-gorp/gorp"
	_ "github.com/go-sql-driver/mysql"
)

type Adapter struct {
	dbmap *gorp.DbMap
}

func Create(driverName, dataSourceName string) (a *Adapter, err error) {
	var db *sql.DB
	db, err = sql.Open(driverName, dataSourceName)
	if err != nil {
		return a, err
	}
	err = db.Ping()
	if err != nil {
		return a, err
	}

	a = &Adapter{}
	a.dbmap = &gorp.DbMap{Db: db, Dialect: gorp.MySQLDialect{"InnoDB", "UTF8"}}
	return a, err
}

func (a *Adapter) Close() (err error) {
	return a.dbmap.Db.Close()
}