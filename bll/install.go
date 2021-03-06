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
	"fmt"
	"github.com/skarllot/netpaper/dal"
	"github.com/skarllot/netpaper/models"
	rqhttp "github.com/skarllot/raiqub/http"
	"net/http"
)

type Install struct {
	Context *AppContext
}

func (self *Install) GetInstallStatus(w http.ResponseWriter, r *http.Request) {
	txn, err := self.Context.dbm.Begin()
	if err != nil {
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return
	}

	count, err := dal.UserCount(txn)
	if err != nil {
		txn.Rollback()
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return
	}
	txn.Commit()
	JsonWrite(w, http.StatusOK, count != 0)
}

func (self *Install) CreateFirstUser(w http.ResponseWriter, r *http.Request) {
	txn, err := self.Context.dbm.Begin()
	if err != nil {
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return
	}

	count, err := dal.UserCount(txn)
	if err != nil {
		txn.Rollback()
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return
	}
	if count != 0 {
		txn.Rollback()
		JsonWrite(w, http.StatusForbidden, "")
		return
	}

	var reqObj models.User
	if !JsonRead(r.Body, &reqObj, w) {
		return
	}

	reqObj.IsAdmin = true
	if err := reqObj.SetPassword(reqObj.Password); err != nil {
		txn.Rollback()
		JsonWrite(w, http.StatusBadRequest, err.Error())
		return
	}
	if err := dal.CreateUser(txn, &reqObj); err != nil {
		txn.Rollback()
		JsonWrite(w, http.StatusBadRequest, err.Error())
		return
	}

	txn.Commit()
	rqhttp.HttpHeader_Location().
		SetValue(fmt.Sprintf("/users/%d", reqObj.Id)).
		SetWriter(w.Header())
	JsonWrite(w, http.StatusCreated, reqObj)
}

func (self *Install) Routes() rqhttp.Routes {
	return rqhttp.Routes{
		rqhttp.Route{
			"GetInstallStatus",
			"GET",
			"/install",
			false,
			self.GetInstallStatus,
		},
		rqhttp.Route{
			"CreateFirstUser",
			"POST",
			"/install",
			false,
			self.CreateFirstUser,
		},
	}
}
