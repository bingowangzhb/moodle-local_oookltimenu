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

namespace local_oookltimenu\local;

/**
 * Hook callbacks for local_oookltimenu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 ambow
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /** @var string Marker name for auto-created hidden LTI activities. */
    private const AUTO_INSTANCE_NAME = '[OOOK LTI AUTO]';
    /** @var bool Prevent duplicate injection when both hook and legacy callback run. */
    private static bool $hidemarkupinjected = false;
    /** @var bool Prevent duplicate CSS injection from navigation callback. */
    private static bool $hidecssrequired = false;

    /**
     * Add OOOK LTI item into secondary navigation on course pages.
     *
     * @param \core\hook\navigation\secondary_extend $hook
     * @return void
     */
    public static function extend_secondary_navigation(\core\hook\navigation\secondary_extend $hook): void {
        global $COURSE, $PAGE;

        if (!isloggedin() || isguestuser()) {
            return;
        }

        if (empty($COURSE->id) || (int)$COURSE->id === SITEID) {
            return;
        }

        // Keep helper activities hidden even when the OOOK navigation entry is disabled.
        if (!self::$hidecssrequired && !empty($PAGE)) {
            $cssurl = new \moodle_url('/local/oookltimenu/source/css/hide_auto_activity.php', [
                'courseid' => (int)$COURSE->id,
            ]);
            $PAGE->requires->css($cssurl);
            self::$hidecssrequired = true;
        }

        if (!self::is_nav_enabled()) {
            return;
        }

        $url = self::get_course_menu_url((int)$COURSE->id);
        if ($url === null) {
            return;
        }

        $secondarynav = $hook->get_secondaryview();
        $node = \navigation_node::create(
            get_string('menuitemdefault', 'local_oookltimenu'),
            $url,
            \navigation_node::TYPE_SETTING,
            null,
            'oookltimenu'
        );
        $node->set_force_into_more_menu(false);

        // Place OOOK after "grades" and before "More" (if present).
        $keys = $secondarynav->get_children_key_list();
        $insertbefore = null;
        $gradeindex = array_search('grades', $keys, true);
        if ($gradeindex !== false && isset($keys[$gradeindex + 1])) {
            $insertbefore = (string)$keys[$gradeindex + 1];
        } else if (in_array('moremenu', $keys, true)) {
            $insertbefore = 'moremenu';
        } else if (in_array('competencies', $keys, true)) {
            $insertbefore = 'competencies';
        }
        if ($insertbefore !== null) {
            $secondarynav->add_node($node, $insertbefore);
        } else {
            $secondarynav->add_node($node);
        }
    }

    /**
     * Whether the course navigation entry is enabled.
     *
     * @return bool
     */
    public static function is_nav_enabled(): bool {
        $enabled = get_config('local_oookltimenu', 'navenabled');
        return $enabled === false || (int)$enabled === 1;
    }

    /**
     * Build the OOOK course menu URL, or null when it should not be shown.
     *
     * @param int $courseid Course id.
     * @return \moodle_url|null
     */
    public static function get_course_menu_url(int $courseid): ?\moodle_url {
        if (!self::is_nav_enabled()) {
            return null;
        }

        $targettypeid = (int)get_config('local_oookltimenu', 'targettypeid');
        if ($targettypeid <= 0) {
            if (!has_capability('moodle/site:config', \context_system::instance())) {
                return null;
            }
            return new \moodle_url('/admin/settings.php', ['section' => 'local_oookltimenu']);
        }

        return new \moodle_url('/local/oookltimenu/page.php', [
            'courseid' => $courseid,
            'typeid' => $targettypeid,
        ]);
    }

    /**
     * Hide the auto-created helper LTI activity from course page and course index.
     *
     * Keep the activity launchable (visible=1) but remove UI noise for all roles.
     *
     * @param \core\hook\output\before_standard_top_of_body_html_generation $hook
     * @return void
     */
    public static function hide_auto_activity_on_course_page(
        \core\hook\output\before_standard_top_of_body_html_generation $hook
    ): void {
        $markup = self::get_hide_auto_activity_markup_for_current_page();
        if ($markup !== '') {
            $hook->add_html($markup);
        }
    }

    /**
     * Build hide markup for current course page.
     *
     * @return string
     */
    public static function get_hide_auto_activity_markup_for_current_page(): string {
        global $PAGE;

        if (self::$hidemarkupinjected) {
            return '';
        }
        if (!isloggedin() || isguestuser()) {
            return '';
        }
        if (empty($PAGE->course) || empty($PAGE->course->id) || (int)$PAGE->course->id === SITEID) {
            return '';
        }
        if (empty($PAGE->context) || (int)$PAGE->context->contextlevel !== CONTEXT_COURSE) {
            return '';
        }

        $courseid = (int)$PAGE->course->id;
        $cmids = self::find_all_auto_lti_cmids_in_course($courseid);

        $selectors = [];
        foreach ($cmids as $cmid) {
            $cmid = (int)$cmid;
            $selectors[] = "#module-{$cmid}";
            $selectors[] = "li.activity[data-for=\"cmitem\"][data-id=\"{$cmid}\"]";
            $selectors[] = "a.aalink[href$=\"/mod/lti/view.php?id={$cmid}\"]";
            $selectors[] = "a.aalink[href*=\"/mod/lti/view.php?id={$cmid}&\"]";
            $selectors[] = ".inplaceeditable[data-itemtype=\"activityname\"][data-itemid=\"{$cmid}\"]";
            $selectors[] = "#course-index-cm-{$cmid}";
            $selectors[] = "li.courseindex-item[data-for=\"cm\"][data-id=\"{$cmid}\"]";
            $selectors[] = "a.courseindex-link[href$=\"id={$cmid}\"]";
            $selectors[] = "a.courseindex-link[href*=\"id={$cmid}&\"]";
        }
        $selectorcss = implode(",\n", array_values(array_unique($selectors)));
        $stylehtml = '';
        if ($selectorcss !== '') {
            $stylehtml = '<style>' . "\n" .
                $selectorcss . " {\n    display: none !important;\n}\n" .
                '</style>';
        }
        $cmidscsv = implode(',', array_map(static function (int $id): string {
            return (string)$id;
        }, $cmids));
        $scripturl = (new \moodle_url('/local/oookltimenu/source/js/hide_auto_activity.js'))->out(false);

        self::$hidemarkupinjected = true;

        return $stylehtml .
            '<div id="local-oookltimenu-hidecfg" data-cmids="' . s($cmidscsv) .
            '" data-marker="' . s(self::AUTO_INSTANCE_NAME) . '"></div>' .
            '<script src="' . s($scripturl) . '"></script>';
    }

    /**
     * Find existing hidden LTI activity for this course + tool type, or create one.
     *
     * @param int $courseid
     * @param int $typeid
     * @return int
     */
    public static function ensure_hidden_lti_cmid(int $courseid, int $typeid): int {
        global $DB;

        if ($typeid <= 0) {
            throw new \moodle_exception('missingtypeidconfig', 'local_oookltimenu');
        }

        if (!$DB->record_exists('lti_types', ['id' => $typeid])) {
            throw new \moodle_exception('invalidtooltypeid', 'local_oookltimenu');
        }

        $existing = self::find_hidden_lti_cmid($courseid, $typeid);
        if ($existing > 0) {
            self::ensure_not_on_coursepage($existing, $courseid);
            return $existing;
        }

        $coursecontext = \context_course::instance($courseid);
        $cancreate = has_capability('mod/lti:addpreconfiguredinstance', $coursecontext)
            || has_capability('mod/lti:addinstance', $coursecontext)
            || has_capability('moodle/course:manageactivities', $coursecontext);

        // If there is an old auto-created record but it is currently hidden,
        // try to recover it for teachers/managers.
        if ($cancreate) {
            $hidden = self::find_any_auto_lti_cmid($courseid, $typeid);
            if ($hidden > 0 && self::force_visible_not_on_coursepage($hidden, $courseid)) {
                return $hidden;
            }
        }

        if (!$cancreate) {
            throw new \moodle_exception('autoltiinitrequired', 'local_oookltimenu');
        }

        $cmid = self::create_hidden_lti_cmid($courseid, $typeid);
        if ($cmid <= 0) {
            throw new \moodle_exception('autolticreatefailed', 'local_oookltimenu');
        }
        self::ensure_not_on_coursepage($cmid, $courseid);
        return $cmid;
    }

    /**
     * Find hidden LTI module id auto-created by this plugin.
     *
     * @param int $courseid
     * @param int $typeid
     * @return int
     */
    private static function find_hidden_lti_cmid(int $courseid, int $typeid): int {
        global $DB;

        $sql = "SELECT cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {lti} lti ON lti.id = cm.instance
                 WHERE m.name = :modname
                   AND cm.course = :courseid
                   AND cm.deletioninprogress = 0
                   AND cm.visible = 1
                   AND lti.typeid = :typeid
                   AND lti.name = :name
              ORDER BY cm.id ASC";
        $records = $DB->get_records_sql($sql, [
            'modname' => 'lti',
            'courseid' => $courseid,
            'typeid' => $typeid,
            'name' => self::AUTO_INSTANCE_NAME,
        ], 0, 1);
        if (empty($records)) {
            return 0;
        }
        $first = reset($records);
        return (int)$first->id;
    }

    /**
     * Find any auto-created LTI module id regardless of visibility.
     *
     * @param int $courseid
     * @param int $typeid
     * @return int
     */
    private static function find_any_auto_lti_cmid(int $courseid, int $typeid): int {
        global $DB;

        $sql = "SELECT cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {lti} lti ON lti.id = cm.instance
                 WHERE m.name = :modname
                   AND cm.course = :courseid
                   AND cm.deletioninprogress = 0
                   AND lti.typeid = :typeid
                   AND lti.name = :name
              ORDER BY cm.id ASC";
        $records = $DB->get_records_sql($sql, [
            'modname' => 'lti',
            'courseid' => $courseid,
            'typeid' => $typeid,
            'name' => self::AUTO_INSTANCE_NAME,
        ], 0, 1);
        if (empty($records)) {
            return 0;
        }
        $first = reset($records);
        return (int)$first->id;
    }

    /**
     * Find all auto-created LTI module ids in a course.
     *
     * @param int $courseid
     * @return int[]
     */
    private static function find_all_auto_lti_cmids_in_course(int $courseid): array {
        global $DB;

        $sql = "SELECT cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module
                  JOIN {lti} lti ON lti.id = cm.instance
                 WHERE m.name = :modname
                   AND cm.course = :courseid
                   AND cm.deletioninprogress = 0
                   AND (lti.name = :name OR " . $DB->sql_like('lti.name', ':namelike', false) . ")
              ORDER BY cm.id ASC";
        $records = $DB->get_records_sql($sql, [
            'modname' => 'lti',
            'courseid' => $courseid,
            'name' => self::AUTO_INSTANCE_NAME,
            'namelike' => '%OOOK LTI AUTO%',
        ]);
        if (empty($records)) {
            return [];
        }

        return array_map(static function ($r): int {
            return (int)$r->id;
        }, array_values($records));
    }

    /**
     * Ensure course module is visible but not shown on course page.
     *
     * @param int $cmid Course module id.
     * @param int $courseid Course id.
     * @return bool
     */
    private static function force_visible_not_on_coursepage(int $cmid, int $courseid): bool {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');
        try {
            // Keep module launchable while removing it from course page/course index.
            set_coursemodule_visible($cmid, 1);
            self::ensure_not_on_coursepage($cmid, $courseid);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Ensure module is not shown on course page/index while still launchable.
     *
     * @param int $cmid
     * @param int $courseid
     * @return void
     */
    private static function ensure_not_on_coursepage(int $cmid, int $courseid): void {
        global $CFG, $DB;

        $visibleoncoursepage = (int)$DB->get_field('course_modules', 'visibleoncoursepage', ['id' => $cmid]);
        if ($visibleoncoursepage === 0) {
            return;
        }

        $DB->set_field('course_modules', 'visibleoncoursepage', 0, ['id' => $cmid]);
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache($courseid, true);
    }

    /**
     * Create hidden LTI instance in section 0 and return course module id.
     *
     * @param int $courseid
     * @param int $typeid
     * @return int
     */
    private static function create_hidden_lti_cmid(int $courseid, int $typeid): int {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $course = get_course($courseid);
        $module = $DB->get_record('modules', ['name' => 'lti'], 'id', MUST_EXIST);

        $moduleinfo = (object)[
            'add' => 'lti',
            'module' => (int)$module->id,
            'modulename' => 'lti',
            'course' => $courseid,
            'section' => 0,
            'name' => self::AUTO_INSTANCE_NAME,
            'intro' => '',
            'introformat' => FORMAT_HTML,
            'visible' => 1,
            'visibleoncoursepage' => 0,
            'cmidnumber' => '',
            'groupmode' => 0,
            'groupingid' => 0,
            'completion' => 0,
            'completionview' => 0,
            'completionexpected' => 0,
            'showdescription' => 0,
            'typeid' => $typeid,
            'toolurl' => '',
            'securetoolurl' => '',
            'resourcekey' => '',
            'password' => '',
            'launchcontainer' => LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS,
            'instructorchoicesendname' => LTI_SETTING_DELEGATE,
            'instructorchoicesendemailaddr' => LTI_SETTING_DELEGATE,
            'instructorchoiceallowroster' => LTI_SETTING_DELEGATE,
            'instructorchoiceallowsetting' => LTI_SETTING_DELEGATE,
            'instructorcustomparameters' => '',
            'instructorchoiceacceptgrades' => LTI_SETTING_NEVER,
            'grade' => 0,
        ];

        try {
            $created = add_moduleinfo($moduleinfo, $course);
        } catch (\Throwable $e) {
            return 0;
        }
        if (empty($created->coursemodule) || !is_numeric($created->coursemodule)) {
            return 0;
        }
        return (int)$created->coursemodule;
    }
}
