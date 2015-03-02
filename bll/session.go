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
	"github.com/gorilla/mux"
	"net/http"
)

type Session struct {
	Context *AppContext
}

func (s *Session) Create(w http.ResponseWriter, r *http.Request) {
	token := s.Context.token.NewToken()

	(JsonResponse{token}).Write(w, http.StatusCreated)
}

func (s *Session) Destroy(w http.ResponseWriter, r *http.Request) {
	id := mux.Vars(r)["id"]

	err := s.Context.token.RemoveToken(id)
	if err == nil {
		(JsonResponse{true}).Write(w, http.StatusOK)
	} else {
		(JsonError{err.Error()}).Write(w, http.StatusNotFound)
	}
}

func (s *Session) Validate(w http.ResponseWriter, r *http.Request) {
	id := mux.Vars(r)["id"]

	_, err := s.Context.token.GetValue(id)
	if err == nil {
		w.WriteHeader(http.StatusOK)
		(JsonResponse{true}).Write(w, http.StatusOK)
	} else {
		(JsonError{""}).Write(w, http.StatusNotFound)
	}
}
