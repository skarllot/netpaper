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

func InitModels(dbm *gorp.DbMap) error {
	models.DefineAllTables(dbm)
	if err := dbm.CreateTablesIfNotExists(); err != nil {
		return err
	}

	txn, err := dbm.Begin()
	if err != nil {
		return err
	}
	var num int64

	num, err = DbVersionCount(txn)
	if err != nil {
		txn.Rollback()
		return err
	}
	if num < 1 {
		models.InitDbVersionTable(txn)
	}

	num, err = LanguageCount(txn)
	if err != nil {
		txn.Rollback()
		return err
	}
	if num < 1 {
		models.InitLanguageTable(txn)
	}

	txn.Commit()
	return nil
}
