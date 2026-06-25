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
if ($typeid <= 0) {
    $typeid = (int)get_config('local_oookltimenu', 'targettypeid');
}

if ($typeid <= 0) {
    throw new moodle_exception('missingtypeidconfig', 'local_oookltimenu');
}

$course = get_course($courseid);
require_login($course);

$context = context_course::instance($courseid);
$cmid = \local_oookltimenu\local\hook_callbacks::ensure_hidden_lti_cmid($courseid, $typeid);

$pageurl = new moodle_url('/local/oookltimenu/page.php', ['courseid' => $courseid, 'typeid' => $typeid]);
$iframeurl = new moodle_url('/mod/lti/launch.php', ['id' => $cmid]);

$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('menuitemdefault', 'local_oookltimenu'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_secondary_active_tab('oookltimenu');

echo $OUTPUT->header();
echo html_writer::start_div('local-oookltimenu-wrapper');
echo html_writer::tag(
    'iframe',
    '',
    [
        'src' => $iframeurl->out(false),
        'class' => 'local-oookltimenu-iframe',
        'title' => get_string('menuitemdefault', 'local_oookltimenu'),
        'allow' => 'fullscreen *',
        'loading' => 'eager',
    ]
);
echo html_writer::end_div();
echo html_writer::tag(
    'style',
    '.local-oookltimenu-wrapper{width:100%;min-height:70vh}' .
    '.local-oookltimenu-iframe{width:100%;height:82vh;border:0;background:#fff}',
    ['type' => 'text/css']
);
echo $OUTPUT->footer();
