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
 * Hook callback definitions for local_oookltimenu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 ambow
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => core\hook\navigation\secondary_extend::class,
        'callback' => 'local_oookltimenu\local\hook_callbacks::extend_secondary_navigation',
        'priority' => 0,
    ],
    [
        'hook' => core\hook\output\before_standard_top_of_body_html_generation::class,
        'callback' => 'local_oookltimenu\local\hook_callbacks::hide_auto_activity_on_course_page',
        'priority' => 0,
    ],
];
