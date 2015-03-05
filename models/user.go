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

import (
	"errors"
	"github.com/go-gorp/gorp"
)

type User struct {
	Id         int64   `db:"id" json:"id"`
	User       string  `db:"user" json:"user"`
	Password   *string `db:"password" json:"password"`
	Email      *string `db:"email" json:"email"`
	Name       string  `db:"name" json:"name"`
	IsAdmin    bool    `db:"is_admin" json:"isAdmin"`
	IsLdap     bool    `db:"is_ldap" json:"isLdap"`
	LanguageId int64   `db:"language" json:"-"`

	Language *Language `db:"-" json:"language"`
}

func DefineUserTable(dbm *gorp.DbMap) {
	t := dbm.AddTableWithName(User{}, "user")
	t.SetKeys(true, "id")
	t.ColMap("user").SetMaxSize(45).SetNotNull(true)
	t.ColMap("password").SetMaxSize(64)
	t.ColMap("name").SetNotNull(true)
	t.ColMap("is_admin").SetNotNull(true)
	t.ColMap("is_ldap").SetNotNull(true)
	t.ColMap("language").SetNotNull(true)
}

func (u *User) PreInsert(_ gorp.SqlExecutor) error {
	if len(u.User) < 5 {
		return errors.New("The user must contain at least 5 characters")
	}
	if len(u.Name) < 3 {
		return errors.New("The user name must contain at least 5 characters")
	}
	if u.Language == nil {
		return errors.New("No language defined for current user")
	}
	u.LanguageId = u.Language.Id
	return nil
}

func (u *User) PostGet(exe gorp.SqlExecutor) error {
	var obj interface{}
	var err error

	obj, err = exe.Get(Language{}, u.LanguageId)
	if err != nil {
		return err
	}
	u.Language = obj.(*Language)

	return nil
}
