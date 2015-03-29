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
	"bytes"
	"encoding/base64"
	"net/http"
	"strings"
)

const (
	BASIC_PREFIX = "Basic "
)

type HttpAuthenticable interface {
	TryAuthentication(r *http.Request, user, secret string) bool
}

type HttpBasicAuthenticator struct {
	HttpAuthenticable
}

func (self HttpBasicAuthenticator) BasicAuth(next http.Handler) http.Handler {
	if self.HttpAuthenticable == nil {
		panic("HttpAuthenticable cannot be nil")
	}

	f := func(w http.ResponseWriter, r *http.Request) {
		user, secret := parseAuthBasicHeader(r.Header.Get("Authorization"))
		if len(user) > 0 &&
			len(secret) > 0 &&
			self.TryAuthentication(r, user, secret) {
			next.ServeHTTP(w, r)
			return
		}

		w.Header().Set("WWW-Authenticate", "Basic realm=\"Restricted\"")
		http.Error(w, http.StatusText(http.StatusUnauthorized),
			http.StatusUnauthorized)
	}

	return http.HandlerFunc(f)
}

func parseAuthBasicHeader(header string) (user, secret string) {
	if !strings.HasPrefix(header, BASIC_PREFIX) {
		return
	}
	payload, err := base64.StdEncoding.DecodeString(header[len(BASIC_PREFIX):])
	if err != nil {
		return
	}
	pair := bytes.SplitN(payload, []byte(":"), 2)
	if len(pair) != 2 {
		return
	}

	user, secret = string(pair[0]), string(pair[1])
	user, secret = strings.TrimSpace(user), strings.TrimSpace(secret)
	return
}
