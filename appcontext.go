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
package main

import (
	"database/sql"
	"encoding/json"
	"errors"
	"fmt"
	"github.com/go-gorp/gorp"
	_ "github.com/go-sql-driver/mysql"
	"github.com/skarllot/netpaper/dal"
	"github.com/skarllot/netpaper/models"
	"net/http"
)

type appContext struct {
	dbm *gorp.DbMap
	txn *gorp.Transaction
}

func (c *appContext) InitDb(engine, connectionString string) error {
	var db *sql.DB
	var err error

	if db, err = sql.Open(engine, connectionString); err != nil {
		return err
	}

	switch engine {
	case "mysql":
		c.dbm = &gorp.DbMap{
			Db:      db,
			Dialect: gorp.MySQLDialect{"InnoDB", "UTF8"}}
	default:
		return errors.New(fmt.Sprintf(
			"The engine '%s' is not implemented", engine))
	}

	models.DefineLanguageTable(c.dbm)
	models.DefineSessionTable(c.dbm)
	models.DefineUserTable(c.dbm)
	if err := c.dbm.CreateTablesIfNotExists(); err != nil {
		return err
	}

	txn, err := c.dbm.Begin()
	if err != nil {
		return err
	}
	if num, _ := dal.LanguageCount(txn); num < 1 {
		models.InitLanguageTable(c.dbm)
	}
	txn.Commit()
	return nil
}

func (c *appContext) HasUsers(w http.ResponseWriter, r *http.Request) {
	var err error
	var count int64

	if c.txn, err = c.dbm.Begin(); err != nil {
		return
	}

	if count, err = dal.UserCount(c.txn); err != nil {
		c.txn.Rollback()
		c.txn = nil
		return
	}
	json.NewEncoder(w).Encode(count > 0)
	c.txn.Commit()
	c.txn = nil
}
