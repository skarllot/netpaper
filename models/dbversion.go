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
package models

import "github.com/go-gorp/gorp"

type DbVersion struct {
	Value string `db:"value" json:"value"`
}

const (
	CURRENT_DB_VERSION = "0.1"
)

func DefineDbVersionTable(dbm *gorp.DbMap) {
	t := dbm.AddTableWithName(DbVersion{}, "dbversion")
	t.ColMap("value").SetMaxSize(9).SetNotNull(true)
}

func InitDbVersionTable(txn *gorp.Transaction) {
	version := &DbVersion{CURRENT_DB_VERSION}
	if err := txn.Insert(version); err != nil {
		panic(err)
	}
}
