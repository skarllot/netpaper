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
package config

import (
	"code.google.com/p/gcfg"
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"strconv"
)

type Configuration struct {
	Database
}

type Database struct {
	Engine   string
	Host     string
	Port     uint16
	Name     string
	User     string
	Password string
	Protocol string
	DbArgs   string
}

func (c *Configuration) Load(path string) error {
	c.loadDefaults()
	return gcfg.ReadFileInto(c, path)
}

func (c *Configuration) loadDefaults() {
	c.Database.Engine = "mysql"
	c.Database.Host = "localhost"
	c.Database.Port = 3306
	c.Database.Name = "netpaper"
	c.Database.User = "netpaper"
	c.Database.Password = ""
	c.Database.Protocol = "tcp"
	c.Database.DbArgs = ""
}

func (c *Configuration) LoadJson(path string) error {
	c.loadDefaults()
	b, err := ioutil.ReadFile(path)
	if err != nil {
		return err
	}
	err = json.Unmarshal(b, c)
	if err != nil {
		return err
	}

	return nil
}

func (c *Configuration) SaveJson(path string) error {
	b, err := json.MarshalIndent(c, "", "  ")
	if err != nil {
		return err
	}
	err = ioutil.WriteFile(path, b, 0664)
	if err != nil {
		return err
	}

	return nil
}

func (db *Database) GetConnectionString() (string, error) {
	switch db.Engine {
	case "mysql":
		return db.getMysqlConnectionString(), nil
	default:
		if len(db.Engine) == 0 {
			return "", errors.New(
				"No engine name defined into configuration file")
		} else {
			return "", errors.New(
				fmt.Sprintf("The engine '%s' was not implemented", db.Engine))
		}

	}
}

func (db *Database) getMysqlConnectionString() string {
	if len(db.DbArgs) > 0 {
		db.DbArgs = "?" + db.DbArgs
	}

	return fmt.Sprintf("%s:%s@%s([%s]:%s)/%s%s",
		db.User, db.Password, db.Protocol,
		db.Host, strconv.Itoa(int(db.Port)), db.Name, db.DbArgs)
}
