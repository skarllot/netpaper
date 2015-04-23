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

package main

import (
	"fmt"
	"github.com/gorilla/context"
	"github.com/gorilla/mux"
	"github.com/justinas/alice"
	"github.com/skarllot/netpaper/bll"
	"log"
	"net/http"
	"runtime"
)

const (
	CONFIG_FILE_NAME = "netpaper.gcfg"
)

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU())

	cfg := bll.Configuration{}
	if err := cfg.Load(CONFIG_FILE_NAME); err != nil {
		fmt.Println("Could not load configuration file:", err)
		return
	}

	appC := bll.AppContext{}
	appC.SetConfiguration(&cfg)
	err := appC.InitDb()
	if err != nil {
		fmt.Println("Could not initialize database:", err)
		return
	}
	err = appC.InitTokenStore()
	if err != nil {
		fmt.Println("Could not initialize token store:", err)
		return
	}

	logon := bll.Logon{&appC}

	commonHandlers := alice.New(
		context.ClearHandler,
		appC.LoggingHandler,
		recoverHandler)
	handlersAuth := commonHandlers.
		Append((bll.HttpBasicAuthenticator{&logon}).BasicAuth)
	cors := bll.NewCORSHandler()

	router := mux.NewRouter().StrictSlash(true)
	v1 := router.PathPrefix("/v1").Subrouter()

	routes := make(bll.Routes, 0)
	routes = append(routes, (&bll.Languages{&appC}).Routes()...)
	routes = append(routes, (&bll.Install{&appC}).Routes()...)
	routes = append(routes, logon.Routes()...)

	corsRoutes := cors.CreateOptionsRoutes(routes)
	routes = append(routes, corsRoutes...)

	for _, r := range routes {
		var handlers alice.Chain
		if r.MustAuth {
			handlers = handlersAuth
		} else {
			handlers = commonHandlers
		}
		handlers = handlers.Append(
			(&bll.CORSMethod{*cors, r.MustAuth}).CORSMiddleware)

		v1.
			Methods(r.Method).
			Path(r.Pattern).
			Name(r.Name).
			Handler(handlers.ThenFunc(r.HandlerFunc))
	}

	fmt.Println("HTTP server listening on port", cfg.Application.Port)
	err = http.ListenAndServe(
		fmt.Sprintf(":%d", cfg.Application.Port),
		router)
	if err != nil {
		fmt.Println("Could not initialize HTTP server:", err)
	}
}

func recoverHandler(next http.Handler) http.Handler {
	fn := func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if err := recover(); err != nil {
				log.Printf("panic: %+v", err)
				w.WriteHeader(http.StatusInternalServerError)
			}
		}()

		next.ServeHTTP(w, r)
	}

	return http.HandlerFunc(fn)
}
