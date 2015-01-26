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
)

type UserAdapter struct {
	Adapter
	tmap *gorp.TableMap
}

type User struct {
	Id       int
	User     string
	Password string
	Email    string
	Name     string
	IsAdmin  bool `db:"admin"`
	IsLdap   bool `db:"is_ldap"`
	Language int
}

const (
	SQL_CREATE_USER = `INSERT INTO user
		(user, password, email, name, admin, is_ldap, language)
		VALUES (:user, :pass, :email, :name, :admin, :isldap, :lang)`
	SQL_GET_USER = `SELECT id, user, password, email, name, admin,
        is_ldap, language FROM user WHERE user = :user`
	SQL_GET_USER_COUNT         = `SELECT count(id) AS count FROM user`
	SQL_GET_USER_WITH_PASSWORD = `SELECT id, user, password, email, name,
        admin, is_ldap, language FROM user
        WHERE user = :user AND password = :pass`
	SQL_IS_LDAP = `SELECT is_ldap FROM user WHERE user = :user`
)

func (u *UserAdapter) GetUser(user, password string) (row *User, err error) {
	var qrows []User
	if len(password) == 0 {
		_, err = u.dbmap.Select(&qrows, SQL_GET_USER, map[string]interface{}{
			"user": user,
		})
	} else {
		_, err = u.dbmap.Select(&qrows, SQL_GET_USER_WITH_PASSWORD, map[string]interface{}{
			"user": user,
			"pass": password,
		})
	}
	if len(qrows) != 1 {
		return nil, err
	}

	return &qrows[0], err
}

func Register(a *Adapter) (u *UserAdapter) {
	u.dbmap = a.dbmap
	u.tmap = u.dbmap.AddTable(User{})
	u.tmap.SetKeys(true, "Id")
	return u
}
