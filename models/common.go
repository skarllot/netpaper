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
	"net"
)

type IPTypeConverter struct{}

func (self IPTypeConverter) ToDb(val interface{}) (interface{}, error) {
	switch t := val.(type) {
	case net.IP:
		return t.String(), nil
	}
	return val, nil
}

func (self IPTypeConverter) FromDb(target interface{}) (gorp.CustomScanner, bool) {
	switch target.(type) {
	case net.IP:
		binder := func(holder, target interface{}) error {
			s, ok := holder.(string)
			if !ok {
				return errors.New("Unable to convert database value to string")
			}
			target = net.ParseIP(s)
			return nil
		}
		return gorp.CustomScanner{new(net.IP), target, binder}, true
	}

	return gorp.CustomScanner{}, false
}
