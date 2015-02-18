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
	"code.google.com/p/gcfg"
	"errors"
	"fmt"
	"strconv"
	"time"
)

type Configuration struct {
	Application
	Database
}

type Application struct {
	Port          uint16
	Secret        string
	TokenLife     string
	AuthTokenLife string
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
	err := gcfg.ReadFileInto(c, path)
	if err == nil &&
		c.Application.GetTokenLifeDuration() == 0 &&
		c.Application.GetAuthTokenLifeDuration() == 0 {
		err = errors.New(fmt.Sprintf(
			"The value '%s' for TokenLife duration is invalid",
			c.Application.TokenLife))
	}
	return err
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

	c.Application.Port = 8080
	c.Application.Secret = "tlUi5qmMvq8tG/09+qBDHkbGoAMyK0FxDXIUWI2Z24bTatgNx" +
		"HRWcmvtZfymm6YjOR5NiXBf9y0eyaqW+misFp0+UPHUNvwPHY2+caCOseXZ"
	c.Application.TokenLife = "1m"
}

func (app *Application) GetAuthTokenLifeDuration() time.Duration {
	d, err := time.ParseDuration(app.AuthTokenLife)
	if err != nil {
		return 0
	}
	return d
}

func (app *Application) GetTokenLifeDuration() time.Duration {
	d, err := time.ParseDuration(app.TokenLife)
	if err != nil {
		return 0
	}
	return d
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
