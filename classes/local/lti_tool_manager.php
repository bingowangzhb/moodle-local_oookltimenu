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

defined('MOODLE_INTERNAL') || die();

/**
 * Creates and updates the site-level LTI tool used by the OOOK course menu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 OOOK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_tool_manager {

    /** Plugin component name. */
    private const COMPONENT = 'local_oookltimenu';

    /** LTI key type values used by mod_lti. */
    public const KEYTYPE_KEYSET = 'JWK_KEYSET';
    public const KEYTYPE_RSA = 'RSA_KEY';

    /** Setting names stored in config_plugins. */
    private const CONFIG_FIELDS = [
        'toolname',
        'toolurl',
        'tooldescription',
        'initiatelogin',
        'redirectionuris',
        'keytype',
        'publickeyset',
        'publickey',
        'coursevisible',
        'sendname',
        'sendemailaddr',
        'launchcontainer',
        'navenabled',
    ];

    /**
     * Return the plugin LTI configuration with defaults applied.
     *
     * @return array
     */
    public static function get_config(): array {
        $defaults = self::get_defaults();
        foreach (self::CONFIG_FIELDS as $field) {
            $value = get_config(self::COMPONENT, $field);
            if ($value !== false) {
                $defaults[$field] = $value;
            }
        }
        $defaults['targettypeid'] = (int)get_config(self::COMPONENT, 'targettypeid');
        return $defaults;
    }

    /**
     * Normalise submitted configuration.
     *
     * @param array $data Submitted values.
     * @return array
     */
    public static function normalise_config(array $data): array {
        $config = self::get_defaults();
        foreach (self::CONFIG_FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $config[$field] = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
            }
        }

        $config['toolname'] = clean_param($config['toolname'], PARAM_TEXT);
        $config['toolurl'] = clean_param($config['toolurl'], PARAM_URL);
        $config['tooldescription'] = clean_param($config['tooldescription'], PARAM_TEXT);
        $config['initiatelogin'] = clean_param($config['initiatelogin'], PARAM_URL);
        $config['publickeyset'] = clean_param($config['publickeyset'], PARAM_URL);
        $config['publickey'] = clean_param($config['publickey'], PARAM_TEXT);
        $config['keytype'] = ($config['keytype'] === self::KEYTYPE_RSA) ? self::KEYTYPE_RSA : self::KEYTYPE_KEYSET;
        $config['sendname'] = empty($config['sendname']) ? 0 : 1;
        $config['sendemailaddr'] = empty($config['sendemailaddr']) ? 0 : 1;
        $config['navenabled'] = empty($config['navenabled']) ? 0 : 1;

        $launchcontainer = (int)$config['launchcontainer'];
        $allowedcontainers = [2, 3, 4, 5];
        $config['launchcontainer'] = in_array($launchcontainer, $allowedcontainers, true) ? $launchcontainer : 3;

        $coursevisible = (int)$config['coursevisible'];
        $allowedcoursevisible = [0, 1, 2];
        $config['coursevisible'] = in_array($coursevisible, $allowedcoursevisible, true) ? $coursevisible : 1;

        $redirects = preg_split('/\r\n|\r|\n/', (string)$config['redirectionuris']);
        $redirects = array_map('trim', $redirects);
        $redirects = array_filter($redirects, static fn($value) => $value !== '');
        $config['redirectionuris'] = implode("\n", array_map(static function(string $url): string {
            return clean_param($url, PARAM_URL);
        }, $redirects));

        return $config;
    }

    /**
     * Validate a normalised LTI tool configuration.
     *
     * @param array $config Normalised values.
     * @return string[] Error messages.
     */
    public static function validate_config(array $config): array {
        global $CFG;

        $errors = [];
        if ($config['toolname'] === '') {
            $errors[] = get_string('errorrequiredtoolname', self::COMPONENT);
        }
        if (!self::is_valid_url($config['toolurl'])) {
            $errors[] = get_string('errorrequiredtoolurl', self::COMPONENT);
        }
        if (!self::is_valid_url($config['initiatelogin'])) {
            $errors[] = get_string('errorrequiredinitiatelogin', self::COMPONENT);
        }

        $redirects = preg_split('/\r\n|\r|\n/', (string)$config['redirectionuris']);
        $redirects = array_filter(array_map('trim', $redirects), static fn($value) => $value !== '');
        if (empty($redirects)) {
            $errors[] = get_string('errorrequiredredirecturis', self::COMPONENT);
        } else {
            foreach ($redirects as $redirect) {
                if (!self::is_valid_url($redirect)) {
                    $errors[] = get_string('errorinvalidredirecturi', self::COMPONENT, $redirect);
                }
            }
        }

        if ($config['keytype'] === self::KEYTYPE_KEYSET && !self::is_valid_url($config['publickeyset'])) {
            $errors[] = get_string('errorrequiredpublickeyset', self::COMPONENT);
        }
        if ($config['keytype'] === self::KEYTYPE_RSA && trim((string)$config['publickey']) === '') {
            $errors[] = get_string('errorrequiredpublickey', self::COMPONENT);
        }

        require_once($CFG->dirroot . '/mod/lti/upgradelib.php');
        if (function_exists('mod_lti_verify_private_key')) {
            $privatekeywarning = mod_lti_verify_private_key();
            if (!empty($privatekeywarning)) {
                $errors[] = $privatekeywarning;
            }
        }

        return $errors;
    }

    /**
     * Whether the submitted config is still the initial empty state.
     *
     * @param array $config Normalised values.
     * @return bool
     */
    public static function is_empty_config(array $config): bool {
        return $config['toolurl'] === ''
            && $config['initiatelogin'] === ''
            && $config['redirectionuris'] === ''
            && $config['publickeyset'] === ''
            && $config['publickey'] === '';
    }

    /**
     * Create or update the managed Moodle LTI tool type.
     *
     * @param array $config Normalised and validated values.
     * @return int The LTI tool type id.
     */
    public static function sync_tool(array $config): int {
        global $CFG, $DB, $SITE, $USER;

        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $typeid = (int)get_config(self::COMPONENT, 'targettypeid');
        $type = null;
        if ($typeid > 0) {
            $type = $DB->get_record('lti_types', ['id' => $typeid]);
        }

        if (!$type) {
            $admin = get_admin();
            $createdby = !empty($USER->id) ? (int)$USER->id : (!empty($admin->id) ? (int)$admin->id : 0);
            $type = (object)[
                'state' => LTI_TOOL_STATE_CONFIGURED,
                'course' => $SITE->id,
                'createdby' => $createdby,
                'timecreated' => time(),
            ];
            $typeid = lti_add_type($type, self::build_lti_config($config));
        } else {
            $type->state = LTI_TOOL_STATE_CONFIGURED;
            $type->course = $SITE->id;
            lti_update_type($type, self::build_lti_config($config));
            $typeid = (int)$type->id;
        }

        return $typeid;
    }

    /**
     * Return platform registration details for the managed tool.
     *
     * @return array
     */
    public static function get_platform_details(): array {
        global $CFG, $DB;

        $typeid = (int)get_config(self::COMPONENT, 'targettypeid');
        if ($typeid <= 0) {
            return [];
        }

        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $type = $DB->get_record('lti_types', ['id' => $typeid]);
        if (!$type) {
            return [];
        }

        if (function_exists('get_tool_type_config')) {
            return get_tool_type_config($type);
        }

        return [
            'platformid' => $CFG->wwwroot,
            'clientid' => $type->clientid ?? '',
            'deploymentid' => $typeid,
            'publickeyseturl' => (new \moodle_url('/mod/lti/certs.php'))->out(false),
            'accesstokenurl' => (new \moodle_url('/mod/lti/token.php'))->out(false),
            'authrequesturl' => (new \moodle_url('/mod/lti/auth.php'))->out(false),
        ];
    }

    /**
     * Default values for the settings form.
     *
     * @return array
     */
    private static function get_defaults(): array {
        return [
            'toolname' => 'oook-lti13-tool',
            'toolurl' => '',
            'tooldescription' => '',
            'initiatelogin' => '',
            'redirectionuris' => '',
            'keytype' => self::KEYTYPE_KEYSET,
            'publickeyset' => '',
            'publickey' => '',
            'coursevisible' => 1,
            'sendname' => 1,
            'sendemailaddr' => 1,
            'launchcontainer' => 3,
            'navenabled' => 1,
        ];
    }

    /**
     * Build a config object accepted by mod_lti's lti_add_type/lti_update_type.
     *
     * @param array $config Normalised values.
     * @return \stdClass
     */
    private static function build_lti_config(array $config): \stdClass {
        return (object)[
            'lti_typename' => $config['toolname'],
            'lti_toolurl' => $config['toolurl'],
            'lti_description' => $config['tooldescription'],
            'lti_ltiversion' => LTI_VERSION_1P3,
            'lti_keytype' => $config['keytype'],
            'lti_publickeyset' => $config['keytype'] === self::KEYTYPE_KEYSET ? $config['publickeyset'] : '',
            'lti_publickey' => $config['keytype'] === self::KEYTYPE_RSA ? $config['publickey'] : '',
            'lti_initiatelogin' => $config['initiatelogin'],
            'lti_redirectionuris' => $config['redirectionuris'],
            'lti_customparameters' => self::get_fixed_custom_parameters(),
            'lti_coursevisible' => (int)$config['coursevisible'],
            'lti_launchcontainer' => (int)$config['launchcontainer'],
            'lti_sendname' => empty($config['sendname']) ? LTI_SETTING_NEVER : LTI_SETTING_ALWAYS,
            'lti_sendemailaddr' => empty($config['sendemailaddr']) ? LTI_SETTING_NEVER : LTI_SETTING_ALWAYS,
            'lti_acceptgrades' => LTI_SETTING_NEVER,
            'lti_forcessl' => 0,
            'lti_contentitem' => 0,
        ];
    }

    /**
     * Fixed custom LTI launch parameters required by the OOOK tool.
     *
     * @return string
     */
    public static function get_fixed_custom_parameters(): string {
        return implode("\n", [
            'course_id=$Context.id',
            'user_id=$User.id',
            'email=$Person.email.primary',
            'course_title=$Context.title',
        ]);
    }

    /**
     * Basic URL validation compatible with Moodle URL fields.
     *
     * @param string $url URL to validate.
     * @return bool
     */
    private static function is_valid_url(string $url): bool {
        if ($url === '') {
            return false;
        }
        return clean_param($url, PARAM_URL) === $url && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
