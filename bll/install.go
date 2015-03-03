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
	"github.com/skarllot/netpaper/dal"
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

	if count == 0 {
		JsonWrite(w, http.StatusOK, []string{})
	} else {
		JsonWrite(w, http.StatusForbidden, "")
	}
}
