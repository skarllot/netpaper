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

package bll

import (
	"database/sql"
	"errors"
	"fmt"
	"github.com/go-gorp/gorp"
	_ "github.com/go-sql-driver/mysql"
	"github.com/skarllot/netpaper/dal"
	"github.com/skarllot/raiqub"
	"log"
	"net/http"
	"time"
)

type AppContext struct {
	config *Configuration
	dbm    *gorp.DbMap
	token  *raiqub.TokenCache
}

func (c *AppContext) InitDb() error {
	var db *sql.DB
	var cnxStr string
	var err error

	engine := c.config.Database.Engine
	cnxStr, err = c.config.Database.GetConnectionString()
	if err != nil {
		return err
	}
	if db, err = sql.Open(engine, cnxStr); err != nil {
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

	if err := dal.InitModels(c.dbm); err != nil {
		return err
	}

	return nil
}

func (c *AppContext) InitTokenStore() error {
	c.token = raiqub.NewTokenCache(
		c.config.Application.GetTokenLifeDuration(),
		c.config.Application.GetAuthTokenLifeDuration(),
		c.config.Application.Secret)
	return nil
}

func (c *AppContext) LoggingHandler(next http.Handler) http.Handler {
	fn := func(w http.ResponseWriter, r *http.Request) {
		t1 := time.Now()
		next.ServeHTTP(w, r)
		t2 := time.Now()
		log.Printf("[%s] %q %v (%d tokens)\n",
			r.Method, r.URL.String(), t2.Sub(t1), c.token.Count())
	}

	return http.HandlerFunc(fn)
}

func (c *AppContext) SetConfiguration(cfg *Configuration) {
	c.config = cfg
}
