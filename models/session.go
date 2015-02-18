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
	"database/sql"
	"github.com/go-gorp/gorp"
	"time"
)

type Session struct {
	Id         int64          `db:"id" json:"id"`
	UserId     int64          `db:"user" json:"-"`
	AuthToken  string         `db:"auth_token" json:"authToken"`
	IpAddress  sql.NullString `db:"ipaddress" json:"ipAddress"`
	Ip6Address sql.NullString `db:"ip6address" json:"ip6Address"`
	CreatedAt  time.Time      `db:"created_at" json:"createdAt"`
	UpdatedAt  time.Time      `db:"updated_at" json:"updatedAt"`

	User *User `db:"-" json:"user"`
}

func DefineSessionTable(dbm *gorp.DbMap) {
	t := dbm.AddTableWithName(Session{}, "session")
	t.SetKeys(true, "id")
	t.ColMap("user").SetNotNull(true)
	t.ColMap("auth_token").SetNotNull(true)
	t.ColMap("ipaddress").SetMaxSize(15)
	t.ColMap("ip6address").SetMaxSize(39)
	t.ColMap("created_at").SetNotNull(true)
}
