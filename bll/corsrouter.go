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
	"net/http"
	"strconv"
	"strings"
)

const (
	DEFAULT_CORS_ORIGIN_HEADER    = "Origin"
	DEFAULT_CORS_PREFLIGHT_METHOD = "OPTIONS"
)

var (
	HEADER_AC_ALLOW_CREDENTIALS = HttpHeader{
		"Access-Control-Allow-Credentials",
		"true",
	}
	HEADER_AC_ALLOW_HEADERS = HttpHeader{
		"Access-Control-Allow-Headers",
		"Origin, X-Requested-With, Content-Type, Accept, Authorization",
	}
	HEADER_AC_MAX_AGE = HttpHeader{
		"Access-Control-Max-Age",
		"86400",
	}
	HEADER_AC_ALLOW_METHODS = HttpHeader{
		"Access-Control-Allow-Methods",
		"OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT",
	}
	HEADER_AC_ALLOW_ORIGIN = HttpHeader{
		"Access-Control-Allow-Origin",
		"*",
	}
)

type CORSRouter struct {
	inner               http.Handler
	routes              map[string]string
	AllowOrigin         PredicateStringFunc
	PreflightMiddleware HttpMiddlewareFunc
}

type CORSHandler struct {
	PredicateOrigin PredicateStringFunc
	Headers         []string
	ExposedHeaders  []string
}

type PredicateStringFunc func(string) bool
type HttpMiddlewareFunc func(http.Handler) http.Handler
type IntStringTuple struct {
	Item1 int
	Item2 string
}

func NewCORSHandler() *CORSHandler {
	return &CORSHandler{
		PredicateOrigin: TrueForAll,
		Headers:         make([]string, 0),
		ExposedHeaders:  make([]string, 0),
	}
}

func (s *CORSHandler) CreateOptionsRoutes(routes Routes) Routes {
	list := make(Routes, 0, len(routes))
	hList := make(map[string]*CORSPreflight, len(routes))
	for _, v := range routes {
		preflight, ok := hList[v.Pattern]
		if !ok {
			preflight = &CORSPreflight{
				*s,
				make([]string, 0, 1),
				v.MustAuth,
			}
			hList[v.Pattern] = preflight
		}
		preflight.Methods = append(preflight.Methods, v.Method)
	}

	for k, v := range hList {
		list = append(list, Route{
			Name:        "",
			Method:      DEFAULT_CORS_PREFLIGHT_METHOD,
			Pattern:     k,
			MustAuth:    v.UseCredentials,
			HandlerFunc: v.ServeHTTP,
		})
	}
	return list
}

func TrueForAll(string) bool {
	return true
}

type CORSMethod struct {
	CORSHandler
	UseCredentials bool
}

func (s *CORSMethod) CORSMiddleware(next http.Handler) http.Handler {
	fn := func(w http.ResponseWriter, r *http.Request) {
		origin := r.Header.Get(DEFAULT_CORS_ORIGIN_HEADER)
		if r.Method != DEFAULT_CORS_PREFLIGHT_METHOD && origin != "" {
			if !s.PredicateOrigin(origin) {
				return
			}

			w.Header().Set(HEADER_AC_ALLOW_ORIGIN.Name, origin)
			w.Header().Set(
				HEADER_AC_ALLOW_CREDENTIALS.Name,
				strconv.FormatBool(s.UseCredentials))
			if len(s.Headers) > 0 {
				w.Header().Set(
					HEADER_AC_ALLOW_HEADERS.Name,
					strings.Join(s.Headers, ", "))
			} else {
				w.Header().Set(
					HEADER_AC_ALLOW_HEADERS.Name,
					HEADER_AC_ALLOW_HEADERS.Value,
				)
			}
		}
		next.ServeHTTP(w, r)
	}

	return http.HandlerFunc(fn)
}

type CORSPreflight struct {
	CORSHandler
	Methods        []string
	UseCredentials bool
}

func (s *CORSPreflight) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	origin := r.Header.Get(DEFAULT_CORS_ORIGIN_HEADER)
	status := http.StatusBadRequest
	defer func() {
		w.WriteHeader(status)
	}()

	if origin != "" {
		if !s.PredicateOrigin(origin) {
			status = http.StatusForbidden
			return
		}

		method := r.Header.Get("Access-Control-Request-Method")
		header := strings.Split(
			r.Header.Get("Access-Control-Request-Headers"),
			", ")

		if !StringSlice(s.Methods).Exists(method) {
			return
		}

		if len(s.Headers) == 0 {
			w.Header().Set(
				HEADER_AC_ALLOW_HEADERS.Name,
				HEADER_AC_ALLOW_HEADERS.Value)
		} else {
			if len(header) > 0 &&
				!StringSlice(s.Headers).ExistsAllIgnoreCase(header) {
				return
			}
			w.Header().Set(
				HEADER_AC_ALLOW_HEADERS.Name,
				strings.Join(s.Headers, ", "))
		}

		w.Header().Set(
			HEADER_AC_ALLOW_METHODS.Name,
			strings.Join(s.Methods, ", "))
		w.Header().Set(HEADER_AC_ALLOW_ORIGIN.Name, origin)
		w.Header().Set(
			HEADER_AC_ALLOW_CREDENTIALS.Name,
			strconv.FormatBool(s.UseCredentials))
		// Optional
		w.Header().Set(HEADER_AC_MAX_AGE.Name, HEADER_AC_MAX_AGE.Value)
		status = http.StatusOK
	} else {
		status = http.StatusNotFound
	}
}

type StringSlice []string

func (s StringSlice) IndexOf(str string) int {
	for i, v := range s {
		if str == v {
			return i
		}
	}

	return -1
}

func (s StringSlice) IndexOfIgnoreCase(str string) int {
	str = strings.ToLower(str)
	for i, v := range s {
		if str == strings.ToLower(v) {
			return i
		}
	}

	return -1
}

func (s StringSlice) Exists(str string) bool {
	return s.IndexOf(str) != -1
}

func (s StringSlice) ExistsIgnoreCase(str string) bool {
	return s.IndexOfIgnoreCase(str) != -1
}

func (s StringSlice) ExistsAllIgnoreCase(str []string) bool {
	for _, v := range str {
		if !s.ExistsIgnoreCase(v) {
			return false
		}
	}

	return true
}
