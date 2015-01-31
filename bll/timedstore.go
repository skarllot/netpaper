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
	"errors"
	"fmt"
	"sync"
	"time"
)

type TimedStore struct {
	items    map[string]*TimedItem
	duration time.Duration
	mutex    *sync.Mutex
}

type TimedItem struct {
	expireAt time.Time
	duration time.Duration
	value    interface{}
}

func (i *TimedItem) IsExpired() bool {
	return time.Now().After(i.expireAt)
}

func (s *TimedStore) New(d time.Duration) *TimedStore {
	return &TimedStore{
		items:    make(map[string]*TimedItem),
		duration: d,
		mutex:    &sync.Mutex{},
	}
}

func (s *TimedStore) NewItem(id string, value interface{}) *TimedItem {
	s.removeExpired()
	i := &TimedItem{
		expireAt: time.Now().Add(s.duration),
		duration: 0,
		value:    value,
	}

	s.mutex.Lock()
	defer s.mutex.Unlock()
	s.items[id] = i

	return i
}

func (s *TimedStore) GetItem(id string) (interface{}, error) {
	s.removeExpired()

	s.mutex.Lock()
	defer s.mutex.Unlock()

	v, err := s.unsafeGet(id)
	if err != nil {
		return nil, err
	}
	s.refreshItem(v)
	return v.value, nil
}

func (s *TimedStore) refreshItem(i *TimedItem) {
	d := s.duration
	if i.duration != 0 {
		d = i.duration
	}
	i.expireAt = time.Now().Add(d)
}

func (s *TimedStore) removeExpired() {
	s.mutex.Lock()
	defer s.mutex.Unlock()

	for i := range s.items {
		if s.items[i].IsExpired() {
			delete(s.items, i)
		}
	}
}

func (s *TimedStore) RemoveItem(id string) error {
	s.removeExpired()

	s.mutex.Lock()
	defer s.mutex.Unlock()

	_, err := s.unsafeGet(id)
	if err != nil {
		return err
	}

	delete(s.items, id)
	return nil
}

func (s *TimedStore) SetItem(id string, value interface{}) error {
	s.removeExpired()

	s.mutex.Lock()
	defer s.mutex.Unlock()

	v, err := s.unsafeGet(id)
	if err != nil {
		return err
	}

	s.refreshItem(v)
	v.value = value
	return nil
}

func (s *TimedStore) SetItemDuration(id string, d time.Duration) error {
	s.removeExpired()

	s.mutex.Lock()
	defer s.mutex.Unlock()

	v, err := s.unsafeGet(id)
	if err != nil {
		return err
	}

	v.expireAt = time.Now().Add(d)
	v.duration = d
	return nil
}

func (s *TimedStore) unsafeGet(id string) (*TimedItem, error) {
	v, ok := s.items[id]
	if !ok {
		return nil, errors.New(
			fmt.Sprintf("The requested id '%s' does not exist or is expired", id))
	}
	return v, nil
}
