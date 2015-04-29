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
	"encoding/json"
	"github.com/skarllot/raiqub"
	"io"
	"io/ioutil"
	"net/http"
)

const (
	HTTP_BODY_MAX_LENGTH = 1048576
	// WebDAV; RFC 4918
	StatusUnprocessableEntity = 422
)

func JsonWrite(w http.ResponseWriter, status int, content interface{}) {
	raiqub.HttpHeader_ContentType_Json().SetWriter(w.Header())
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(content)
}

func JsonRead(body io.ReadCloser, obj interface{}, w http.ResponseWriter) bool {
	content, err := ioutil.ReadAll(io.LimitReader(body, HTTP_BODY_MAX_LENGTH))
	if err != nil {
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return false
	}

	if err := body.Close(); err != nil {
		JsonWrite(w, http.StatusInternalServerError, err.Error())
		return false
	}

	if err := json.Unmarshal(content, obj); err != nil {
		JsonWrite(w, StatusUnprocessableEntity, err.Error())
		return false
	}

	return true
}
