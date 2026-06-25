<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Legacy callbacks for local_oookltimenu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 ambow
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Legacy callback fallback for adding hide markup near top of body.
 *
 * @return string
 */
function local_oookltimenu_before_standard_top_of_body_html(): string {
    return \local_oookltimenu\local\hook_callbacks::get_hide_auto_activity_markup_for_current_page();
}
