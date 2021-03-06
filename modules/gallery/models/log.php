<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2013 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class Log_Model_Core extends ORM {
  /**
   * @see ORM::__get()
   */
  public function __get($column) {
    if ($column == "user") {
      // This relationship depends on an outside module, which may not be present so handle
      // failures gracefully.
      try {
        return identity::lookup_user($this->user_id);
      } catch (Exception $e) {
        Kohana_Log::add("alert", "Unable to load user with id $this->user_id");
        return null;
      }
    } else {
      return parent::__get($column);
    }
  }
}
