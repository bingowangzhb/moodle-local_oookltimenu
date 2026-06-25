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

namespace local_oookltimenu\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy Subsystem implementation for local_oookltimenu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 ambow
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe personal data sent to the external LTI tool through Moodle core LTI launch.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->link_external_location('External LTI provider.', [
            'userid' => 'privacy:metadata:userid',
            'username' => 'privacy:metadata:username',
            'fullname' => 'privacy:metadata:fullname',
            'email' => 'privacy:metadata:email',
            'role' => 'privacy:metadata:role',
            'courseid' => 'privacy:metadata:courseid',
            'coursefullname' => 'privacy:metadata:coursefullname',
        ], 'privacy:metadata:externalpurpose');

        return $collection;
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param int $userid The user id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param userlist $userlist The user list.
     */
    public static function get_users_in_context(userlist $userlist) {
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param approved_contextlist $contextlist The approved context list.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param \context $context The context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param approved_userlist $userlist The approved user list.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
    }

    /**
     * The plugin stores no user data in its own tables.
     *
     * @param approved_contextlist $contextlist The approved context list.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }
}
