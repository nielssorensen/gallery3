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
class Gallery_Controller_Admin_Dashboard extends Controller_Admin {
  public function action_index() {
    $view = new View_Admin("required/admin.html");
    $view->page_title = t("Dashboard");
    $view->content = new View("admin/dashboard.html");
    $view->content->blocks = BlockManager::get_html("dashboard_center");
    $view->sidebar = "<div id=\"g-admin-dashboard-sidebar\">" .
      BlockManager::get_html("dashboard_sidebar") .
      "</div>";
    $view->content->obsolete_modules_message = Module::get_obsolete_modules_message();
    print $view;
  }

  public function action_add_block() {
    Access::verify_csrf();

    $form = Hook_GalleryBlock::get_add_block_form();
    if ($form->validate()) {
      list ($module_name, $id) = explode(":", $form->add_block->id->value);
      $available = BlockManager::get_available_admin_blocks();

      if ($form->add_block->center->value) {
        BlockManager::add("dashboard_center", $module_name, $id);
        Message::success(
          t("Added <b>%title</b> block to the dashboard center",
            array("title" => $available["$module_name:$id"])));
      } else {
        BlockManager::add("dashboard_sidebar", $module_name, $id);
        Message::success(
          t("Added <b>%title</b> to the dashboard sidebar",
            array("title" => $available["$module_name:$id"])));
      }
    }
    HTTP::redirect("admin/dashboard");
  }

  public function action_remove_block($id) {
    Access::verify_csrf();

    $blocks_center = BlockManager::get_active("dashboard_center");
    $blocks_sidebar = BlockManager::get_active("dashboard_sidebar");

    if (array_key_exists($id, $blocks_sidebar)) {
      $deleted = $blocks_sidebar[$id];
      BlockManager::remove("dashboard_sidebar", $id);
    } else if (array_key_exists($id, $blocks_center)) {
      $deleted = $blocks_center[$id];
      BlockManager::remove("dashboard_center", $id);
    }

    if (!empty($deleted)) {
      $available = BlockManager::get_available_admin_blocks();
      $title = $available[join(":", $deleted)];
      Message::success(t("Removed <b>%title</b> block", array("title" => $title)));
    }

    HTTP::redirect("admin");
  }

  public function action_reorder() {
    Access::verify_csrf();

    $active_set = array();
    foreach (array("dashboard_sidebar", "dashboard_center") as $location) {
      foreach (BlockManager::get_active($location) as $id => $info) {
        $active_set[$id] = $info;
      }
    }

    foreach (array("dashboard_sidebar", "dashboard_center") as $location) {
      $new_blocks = array();
      foreach ((array) Request::current()->query($location) as $id) {
        $new_blocks[$id] = $active_set[$id];
      }
      BlockManager::set_active($location, $new_blocks);
    }
  }
}