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

defined('MOODLE_INTERNAL') || die();

/**
 * Clean up LTI data created by local_oookltimenuauto.
 *
 * @return bool
 */
function xmldb_local_oookltimenuauto_uninstall(): bool {
    global $CFG, $DB;

    $typeids = local_oookltimenuauto_uninstall_get_managed_typeids();
    $success = local_oookltimenuauto_uninstall_delete_auto_lti_activities();

    if (!empty($typeids) && local_oookltimenuauto_uninstall_lti_tables_exist()) {
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        foreach ($typeids as $typeid) {
            if ($DB->record_exists('lti_types', ['id' => $typeid])) {
                lti_delete_type($typeid);
            }
        }
    }

    return $success;
}

/**
 * Find managed LTI tool type ids before plugin config is removed.
 *
 * @return int[]
 */
function local_oookltimenuauto_uninstall_get_managed_typeids(): array {
    global $DB;

    $typeids = [];
    $targettypeid = (int)get_config('local_oookltimenuauto', 'targettypeid');
    if ($targettypeid > 0) {
        $typeids[$targettypeid] = $targettypeid;
    }

    if (!local_oookltimenuauto_uninstall_lti_tables_exist()) {
        return array_values($typeids);
    }

    $namelike = $DB->sql_like('lti.name', ':namelike', false);
    $sql = "SELECT DISTINCT lti.typeid
              FROM {lti} lti
             WHERE lti.typeid > 0
               AND (lti.name = :name OR {$namelike})";
    $records = $DB->get_records_sql($sql, [
        'name' => local_oookltimenuauto_uninstall_auto_instance_name(),
        'namelike' => '%OOOK LTI AUTO%',
    ]);

    foreach ($records as $record) {
        $typeid = (int)$record->typeid;
        if ($typeid > 0) {
            $typeids[$typeid] = $typeid;
        }
    }

    return array_values($typeids);
}

/**
 * Delete helper LTI activities auto-created in courses.
 *
 * @return bool
 */
function local_oookltimenuauto_uninstall_delete_auto_lti_activities(): bool {
    global $DB;

    if (!local_oookltimenuauto_uninstall_lti_tables_exist()) {
        return true;
    }

    $namelike = $DB->sql_like('lti.name', ':namelike', false);
    $sql = "SELECT cm.id, cm.course
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
              JOIN {lti} lti ON lti.id = cm.instance
             WHERE m.name = :modname
               AND cm.deletioninprogress = 0
               AND (lti.name = :name OR {$namelike})
          ORDER BY cm.id ASC";
    $cms = $DB->get_records_sql($sql, [
        'modname' => 'lti',
        'name' => local_oookltimenuauto_uninstall_auto_instance_name(),
        'namelike' => '%OOOK LTI AUTO%',
    ]);

    $success = true;
    foreach ($cms as $cm) {
        try {
            local_oookltimenuauto_uninstall_delete_course_module((int)$cm->id, (int)$cm->course);
        } catch (Throwable $e) {
            $success = false;
            debugging(
                'Failed to delete OOOK auto-created LTI course module ' . (int)$cm->id . ': ' . $e->getMessage(),
                DEBUG_DEVELOPER
            );
        }
    }

    return $success;
}

/**
 * Delete a course module through the Moodle course API.
 *
 * @param int $cmid Course module id.
 * @param int $courseid Course id.
 * @return void
 */
function local_oookltimenuauto_uninstall_delete_course_module(int $cmid, int $courseid): void {
    global $CFG;

    require_once($CFG->dirroot . '/course/lib.php');

    if (class_exists(\core_courseformat\formatactions::class)) {
        \core_courseformat\formatactions::cm($courseid)->delete($cmid, false);
        return;
    }

    course_delete_module($cmid, false);
}

/**
 * Check whether core LTI tables are available.
 *
 * @return bool
 */
function local_oookltimenuauto_uninstall_lti_tables_exist(): bool {
    global $DB;

    $dbman = $DB->get_manager();
    return $dbman->table_exists('lti')
        && $dbman->table_exists('lti_types')
        && $dbman->table_exists('lti_types_config')
        && $dbman->table_exists('lti_types_categories')
        && $dbman->table_exists('course_modules')
        && $dbman->table_exists('modules');
}

/**
 * Auto-created helper activity name used by the plugin.
 *
 * @return string
 */
function local_oookltimenuauto_uninstall_auto_instance_name(): string {
    return '[OOOK LTI AUTO]';
}
