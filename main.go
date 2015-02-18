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
	session := bll.Session{&appC}

	commonHandlers := alice.New(context.ClearHandler, appC.LoggingHandler, recoverHandler)
	router := NewRouter()
	router.Get("/session/create", commonHandlers.ThenFunc(session.Create))
	router.Get("/session/destroy/:token", commonHandlers.ThenFunc(session.Destroy))
	router.Get("/session/validate/:token", commonHandlers.ThenFunc(session.Validate))
	router.Get("/logon/doLogon", commonHandlers.ThenFunc(logon.DoLogon))
	router.Get("/logon/hasUsers", commonHandlers.ThenFunc(logon.HasUsers))
	fmt.Println("HTTP server listening on port", cfg.Application.Port)
	http.ListenAndServe(
		fmt.Sprintf(":%d", cfg.Application.Port),
		router.httpRouter)
}

func recoverHandler(next http.Handler) http.Handler {
	fn := func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if err := recover(); err != nil {
				log.Printf("panic: %+v", err)
				http.Error(w, http.StatusText(500), 500)
			}
		}()

		next.ServeHTTP(w, r)
	}

	return http.HandlerFunc(fn)
}
