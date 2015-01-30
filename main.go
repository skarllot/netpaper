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
	"github.com/skarllot/netpaper/config"
	"log"
	"net/http"
	"runtime"
	"time"
)

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU())

	cfg := config.Configuration{}
	if err := cfg.Load("netpaper.gcfg"); err != nil {
		fmt.Println("Could not load configuration file:", err)
		return
	}
	cnxStr, err := cfg.GetConnectionString()
	if err != nil {
		fmt.Println("Could not determine database connection string:", err)
		return
	}

	appC := bll.AppContext{}
	err = appC.InitDb(cfg.Database.Engine, cnxStr)
	if err != nil {
		fmt.Println("Could not initialize database:", err)
		return
	}

	logon := bll.Logon{&appC}

	commonHandlers := alice.New(context.ClearHandler, loggingHandler, recoverHandler)
	router := NewRouter()
	router.Get("/logon/hasUsers", commonHandlers.ThenFunc(logon.HasUsers))
	http.ListenAndServe(":8080", router.httpRouter)
}

func loggingHandler(next http.Handler) http.Handler {
	fn := func(w http.ResponseWriter, r *http.Request) {
		t1 := time.Now()
		next.ServeHTTP(w, r)
		t2 := time.Now()
		log.Printf("[%s] %q %v\n", r.Method, r.URL.String(), t2.Sub(t1))
	}

	return http.HandlerFunc(fn)
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
