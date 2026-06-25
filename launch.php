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
 * Redirect helper for the OOOK course menu launch.
 *
 * @package    local_oookltimenu
 * @copyright  2026 OOOK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$typeid = optional_param('typeid', 0, PARAM_INT);

$course = get_course($courseid);
require_login($course);

$params = ['courseid' => $courseid];
if ($typeid > 0) {
    $params['typeid'] = $typeid;
}
redirect(new moodle_url('/local/oookltimenu/page.php', $params));
