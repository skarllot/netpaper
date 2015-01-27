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
package controllers

import (
	"database/sql"
	"fmt"
	"github.com/go-gorp/gorp"
	_ "github.com/go-sql-driver/mysql"
	"github.com/revel/revel"
	"github.com/skarllot/netpaper/app/dal"
	"github.com/skarllot/netpaper/app/models"
	"strings"
)

func init() {
	revel.OnAppStart(InitDb)
	revel.InterceptMethod((*GorpController).Begin, revel.BEFORE)
	revel.InterceptMethod((*GorpController).Commit, revel.AFTER)
	revel.InterceptMethod((*GorpController).Rollback, revel.FINALLY)
}

func getConnectionString() string {
	host := revel.Config.StringDefault("db.host", "")
	port := revel.Config.StringDefault("db.port", "3306")
	user := revel.Config.StringDefault("db.user", "")
	pass := revel.Config.StringDefault("db.password", "")
	dbname := revel.Config.StringDefault("db.name", "netpaper")
	protocol := revel.Config.StringDefault("db.protocol", "tcp")
	dbargs := revel.Config.StringDefault("db.args", " ")

	if len(strings.Trim(dbargs, " ")) > 0 {
		dbargs = "?" + dbargs
	} else {
		dbargs = ""
	}
	return fmt.Sprintf("%s:%s@%s([%s]:%s)/%s%s",
		user, pass, protocol, host, port, dbname, dbargs)
}

var InitDb func() = func() {
	connectionString := getConnectionString()
	db, err := sql.Open("mysql", connectionString)
	if err != nil {
		revel.ERROR.Fatal(err)
	} else {
		Dbm = &gorp.DbMap{
			Db:      db,
			Dialect: gorp.MySQLDialect{"InnoDB", "UTF8"}}
	}

	models.DefineLanguageTable(Dbm)
	models.DefineSessionTable(Dbm)
	models.DefineUserTable(Dbm)
	err = Dbm.CreateTablesIfNotExists()
	if err != nil {
		revel.ERROR.Fatal(err)
	}

	txn, err := Dbm.Begin()
	if err != nil {
		revel.ERROR.Fatal(err)
	}
	if c, _ := dal.LanguageCount(txn); c < 1 {
		models.InitLanguageTable(Dbm)
	}
	txn.Commit()
}
