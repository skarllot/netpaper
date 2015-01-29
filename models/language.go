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

type Language struct {
	Id   int64  `db:"id" json:"id"`
	Code string `db:"code" json:"code"`
	Name string `db:"name" json:"name"`
}

func DefineLanguageTable(dbm *gorp.DbMap) {
	t := dbm.AddTableWithName(Language{}, "language")
	t.SetKeys(true, "id")
	t.ColMap("code").SetMaxSize(5).SetNotNull(true)
	t.ColMap("name").SetMaxSize(45).SetNotNull(true)
}

func InitLanguageTable(dbm *gorp.DbMap) {
	languages := []*Language{
		&Language{0, "en-US", "English (Default)"},
		&Language{0, "pt-BR", "Português (Brasil)"},
	}

	for _, l := range languages {
		if err := dbm.Insert(l); err != nil {
			panic(err)
		}
	}
}
