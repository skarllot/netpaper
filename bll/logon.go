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
	"github.com/gorilla/context"
	"github.com/skarllot/netpaper/dal"
	rqhttp "github.com/skarllot/raiqub/http"
	"log"
	"net/http"
)

type Logon struct {
	Context *AppContext
}

const (
	basicAuthPrefix = "Basic "
	authContextKey  = "auth"
)

func (self *Logon) TryAuthentication(r *http.Request, user, secret string) bool {
	txn, err := self.Context.dbm.Begin()
	if err != nil {
		log.Printf("TryAuthentication error: %s\n", err.Error())
		return false
	}

	userRow, err := dal.GetUser(txn, user, "")
	if err != nil {
		txn.Rollback()
		log.Printf("TryAuthentication error: %s\n", err.Error())
		return false
	}
	txn.Commit()

	if userRow == nil || !userRow.ValidatePassword(secret) {
		return false
	}

	context.Set(r, authContextKey, userRow)
	return true
}

func (self *Logon) Validate(w http.ResponseWriter, r *http.Request) {
	JsonWrite(w, http.StatusOK, "")
}

func (self *Logon) Destroy(w http.ResponseWriter, r *http.Request) {
	context.Delete(r, authContextKey)
	JsonWrite(w, http.StatusOK, "")
}

func (self *Logon) Routes() rqhttp.Routes {
	return rqhttp.Routes{
		rqhttp.Route{
			"AuthValidate",
			"GET",
			"/auth/validate",
			true,
			self.Validate,
		},
		rqhttp.Route{
			"AuthDestroy",
			"POST",
			"/auth/destroy",
			true,
			self.Destroy,
		},
	}
}
