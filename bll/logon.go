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
	"bytes"
	"encoding/base64"
	"encoding/json"
	"github.com/skarllot/netpaper/dal"
	"net/http"
	"strings"
)

type Logon struct {
	Context *AppContext
}

const (
	basicAuthPrefix = "Basic "
)

func (l *Logon) DoLogon(w http.ResponseWriter, r *http.Request) {
	//params := context.Get(r, "params").(httprouter.Params)

	auth := r.Header.Get("Authorization")
	if strings.HasPrefix(auth, basicAuthPrefix) {
		payload, err := base64.StdEncoding.DecodeString(auth[len(basicAuthPrefix):])
		if err == nil {
			pair := bytes.SplitN(payload, []byte(":"), 2)
			if len(pair) == 2 &&
				bytes.Equal(pair[0], []byte("user")) &&
				bytes.Equal(pair[1], []byte("pass")) {
				return
			}
		}
	}

	w.Header().Set("WWW-Authenticate", "Basic realm=Restricted")
	http.Error(w, http.StatusText(http.StatusUnauthorized), http.StatusUnauthorized)
}

func (l *Logon) HasUsers(w http.ResponseWriter, r *http.Request) {
	var err error
	var count int64

	l.Context.txn, err = l.Context.dbm.Begin()
	if err != nil {
		return
	}

	count, err = dal.UserCount(l.Context.txn)
	if err != nil {
		l.Context.txn.Rollback()
		l.Context.txn = nil
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(count > 0)
	l.Context.txn.Commit()
	l.Context.txn = nil
}
