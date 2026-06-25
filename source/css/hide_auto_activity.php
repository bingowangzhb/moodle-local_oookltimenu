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

define('NO_DEBUG_DISPLAY', true);
require_once(__DIR__ . '/../../../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
require_login($course);

header('Content-Type: text/css; charset=utf-8');

$sql = "SELECT cm.id
          FROM {course_modules} cm
          JOIN {modules} m ON m.id = cm.module
          JOIN {lti} lti ON lti.id = cm.instance
         WHERE m.name = :modname
           AND cm.course = :courseid
           AND cm.deletioninprogress = 0
           AND (lti.name = :name OR " . $DB->sql_like('lti.name', ':namelike', false) . ")
      ORDER BY cm.id ASC";
$cmids = $DB->get_fieldset_sql($sql, [
    'modname' => 'lti',
    'courseid' => $courseid,
    'name' => '[OOOK LTI AUTO]',
    'namelike' => '%OOOK LTI AUTO%',
]);

if (empty($cmids)) {
    echo ".inplaceeditable[data-itemtype=\"activityname\"][data-value=\"[OOOK LTI AUTO]\"],\n";
    echo ".activity-item[data-activityname=\"[OOOK LTI AUTO]\"] {\n";
    echo "  display: none !important;\n";
    echo "}\n";
    exit;
}

$selectors = [];
foreach ($cmids as $cmid) {
    $cmid = (int)$cmid;
    if ($cmid <= 0) {
        continue;
    }

    $selectors[] = '#module-' . $cmid;
    $selectors[] = 'li.activity[data-for="cmitem"][data-id="' . $cmid . '"]';
    $selectors[] = 'a.aalink[href$="/mod/lti/view.php?id=' . $cmid . '"]';
    $selectors[] = 'a.aalink[href*="/mod/lti/view.php?id=' . $cmid . '&"]';
    $selectors[] = '.inplaceeditable[data-itemtype="activityname"][data-itemid="' . $cmid . '"]';

    $selectors[] = '#course-index-cm-' . $cmid;
    $selectors[] = 'li.courseindex-item[data-for="cm"][data-id="' . $cmid . '"]';
    $selectors[] = 'a.courseindex-link[href$="id=' . $cmid . '"]';
    $selectors[] = 'a.courseindex-link[href*="id=' . $cmid . '&"]';
}

$selectors = array_values(array_unique($selectors));
if (empty($selectors)) {
    exit;
}

echo implode(",\n", $selectors) . " {\n";
echo "  display: none !important;\n";
echo "}\n";
