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

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$typeid = optional_param('typeid', 0, PARAM_INT);

$course = get_course($courseid);
require_login($course);

$params = ['courseid' => $courseid];
if ($typeid > 0) {
    $params['typeid'] = $typeid;
}
redirect(new moodle_url('/local/oookltimenuauto/page.php', $params));
