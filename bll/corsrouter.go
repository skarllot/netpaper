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
	rqhttp "github.com/skarllot/raiqub/http"
	"net/http"
	"strconv"
	"strings"
	"time"
)

const (
	DEFAULT_CORS_PREFLIGHT_METHOD = "OPTIONS"
	DEFAULT_CORS_MAX_AGE          = time.Hour * 24 / time.Second
	DEFAULT_CORS_METHODS          = "OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT"
	DEFAULT_CORS_ORIGIN           = "*"
)

type CORSRouter struct {
	inner               http.Handler
	routes              map[string]string
	AllowOrigin         PredicateStringFunc
	PreflightMiddleware rqhttp.HttpMiddlewareFunc
}

type CORSHandler struct {
	PredicateOrigin PredicateStringFunc
	Headers         []string
	ExposedHeaders  []string
}

type PredicateStringFunc func(string) bool
type IntStringTuple struct {
	Item1 int
	Item2 string
}

func NewCORSHandler() *CORSHandler {
	return &CORSHandler{
		PredicateOrigin: TrueForAll,
		Headers: []string{
			"Origin", "X-Requested-With", "Content-Type",
			"Accept", "Authorization",
		},
		ExposedHeaders: make([]string, 0),
	}
}

func (s *CORSHandler) CreateOptionsRoutes(routes rqhttp.Routes) rqhttp.Routes {
	list := make(rqhttp.Routes, 0, len(routes))
	hList := make(map[string]*CORSPreflight, len(routes))
	for _, v := range routes {
		preflight, ok := hList[v.Path]
		if !ok {
			preflight = &CORSPreflight{
				*s,
				make([]string, 0, 1),
				v.MustAuth,
			}
			hList[v.Path] = preflight
		}
		preflight.Methods = append(preflight.Methods, v.Method)
	}

	for k, v := range hList {
		list = append(list, rqhttp.Route{
			Name:       "",
			Method:     DEFAULT_CORS_PREFLIGHT_METHOD,
			Path:       k,
			MustAuth:   v.UseCredentials,
			ActionFunc: v.ServeHTTP,
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
		origin := rqhttp.HttpHeader_Origin().GetReader(r.Header)
		if r.Method != DEFAULT_CORS_PREFLIGHT_METHOD && origin.Value != "" {
			if !s.PredicateOrigin(origin.Value) {
				return
			}

			rqhttp.HttpHeader_AccessControlAllowOrigin().
				SetValue(origin.Value).
				SetWriter(w.Header())
			rqhttp.HttpHeader_AccessControlAllowCredentials().
				SetValue(strconv.FormatBool(s.UseCredentials)).
				SetWriter(w.Header())
			if len(s.Headers) > 0 {
				rqhttp.HttpHeader_AccessControlAllowHeaders().
					SetValue(strings.Join(s.Headers, ", ")).
					SetWriter(w.Header())
			} else {
				rqhttp.HttpHeader_AccessControlAllowHeaders().
					SetWriter(w.Header())
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
	origin := rqhttp.HttpHeader_Origin().GetReader(r.Header)
	status := http.StatusBadRequest
	defer func() {
		w.WriteHeader(status)
	}()

	if origin.Value != "" {
		if !s.PredicateOrigin(origin.Value) {
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
			rqhttp.HttpHeader_AccessControlAllowHeaders().
				SetWriter(w.Header())
		} else {
			if len(header) > 0 &&
				!StringSlice(s.Headers).ExistsAllIgnoreCase(header) {
				return
			}
			rqhttp.HttpHeader_AccessControlAllowHeaders().
				SetValue(strings.Join(s.Headers, ", ")).
				SetWriter(w.Header())
		}

		rqhttp.HttpHeader_AccessControlAllowMethods().
			SetValue(strings.Join(s.Methods, ", ")).
			SetWriter(w.Header())
		origin.SetWriter(w.Header())
		rqhttp.HttpHeader_AccessControlAllowCredentials().
			SetValue(strconv.FormatBool(s.UseCredentials)).
			SetWriter(w.Header())
		// Optional
		rqhttp.HttpHeader_AccessControlMaxAge().
			SetValue(strconv.Itoa(int(DEFAULT_CORS_MAX_AGE))).
			SetWriter(w.Header())
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
