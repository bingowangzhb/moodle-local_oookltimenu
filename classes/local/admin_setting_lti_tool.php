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
 * Admin setting for the managed LTI 1.3 tool.
 *
 * @package    local_oookltimenu
 * @copyright  2026 ambow
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_lti_tool extends \admin_setting {
    /** @var string[] Config fields saved by this setting. */
    private const FIELDS = [
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
     * Return the current setting values.
     *
     * @return array
     */
    public function get_setting() {
        return lti_tool_manager::get_config();
    }

    /**
     * Validate, save, and sync the managed core LTI tool.
     *
     * @param mixed $data Submitted setting data.
     * @return string Empty string on success, error text on failure.
     */
    public function write_setting($data) {
        if (!is_array($data)) {
            return get_string('errorinvalidconfig', 'local_oookltimenu');
        }
        if (empty($data)) {
            return '';
        }

        $config = lti_tool_manager::normalise_config($data);
        if (lti_tool_manager::is_empty_config($config)) {
            foreach (self::FIELDS as $field) {
                if (!$this->config_write($field, $config[$field])) {
                    return get_string('errorsetting', 'admin');
                }
            }
            if (!$this->config_write('targettypeid', 0)) {
                return get_string('errorsetting', 'admin');
            }
            return '';
        }

        $errors = lti_tool_manager::validate_config($config);
        if (!empty($errors)) {
            return implode('<br>', $errors);
        }

        try {
            $typeid = lti_tool_manager::sync_tool($config);
        } catch (\Throwable $e) {
            return get_string('errorsyncfailed', 'local_oookltimenu', $e->getMessage());
        }

        foreach (self::FIELDS as $field) {
            if (!$this->config_write($field, $config[$field])) {
                return get_string('errorsetting', 'admin');
            }
        }

        if (!$this->config_write('targettypeid', $typeid)) {
            return get_string('errorsetting', 'admin');
        }

        return '';
    }

    /**
     * Render the setting controls.
     *
     * @param mixed $data Current setting data.
     * @param string $query Search query.
     * @return string
     */
    public function output_html($data, $query = '') {
        $config = is_array($data) ? $data : $this->get_setting();
        $config = lti_tool_manager::normalise_config($config);
        $fullname = $this->get_full_name();
        $inputid = $this->get_id() . '_toolname';

        $html = \html_writer::start_div('local-oookltimenu-settings');
        $html .= $this->text_input($fullname, 'toolname', $config['toolname'], true);
        $html .= $this->text_input($fullname, 'toolurl', $config['toolurl'], true);
        $html .= $this->textarea($fullname, 'tooldescription', $config['tooldescription'], false, 4);
        $html .= $this->text_input($fullname, 'initiatelogin', $config['initiatelogin'], true);
        $html .= $this->textarea($fullname, 'redirectionuris', $config['redirectionuris'], true, 3);
        $html .= $this->select_keytype($fullname, $config['keytype']);
        $html .= $this->text_input($fullname, 'publickeyset', $config['publickeyset'], false);
        $html .= $this->textarea($fullname, 'publickey', $config['publickey'], false, 5);
        $html .= $this->fixed_custom_parameters();
        $html .= $this->select_coursevisible($fullname, (int)$config['coursevisible']);
        $html .= $this->select_launchcontainer($fullname, (int)$config['launchcontainer']);
        $html .= $this->checkbox($fullname, 'navenabled', !empty($config['navenabled']));
        $html .= $this->checkbox($fullname, 'sendname', !empty($config['sendname']));
        $html .= $this->checkbox($fullname, 'sendemailaddr', !empty($config['sendemailaddr']));
        $html .= $this->platform_details();
        $html .= \html_writer::end_div();

        return format_admin_setting(
            $this,
            $this->visiblename,
            $html,
            $this->description,
            $inputid,
            '',
            null,
            $query
        );
    }

    /**
     * Render a text input row.
     *
     * @param string $fullname Full setting form name.
     * @param string $field Field key.
     * @param string $value Current value.
     * @param bool $required Whether the field is required.
     * @return string
     */
    private function text_input(string $fullname, string $field, string $value, bool $required): string {
        $attrs = [
            'type' => 'text',
            'name' => $fullname . '[' . $field . ']',
            'id' => $this->get_id() . '_' . $field,
            'value' => $value,
            'class' => 'form-control',
        ];
        if ($required) {
            $attrs['required'] = 'required';
        }
        return $this->field_row($field, \html_writer::empty_tag('input', $attrs));
    }

    /**
     * Render a textarea row.
     *
     * @param string $fullname Full setting form name.
     * @param string $field Field key.
     * @param string $value Current value.
     * @param bool $required Whether the field is required.
     * @param int $rows Row count.
     * @return string
     */
    private function textarea(string $fullname, string $field, string $value, bool $required, int $rows): string {
        $attrs = [
            'name' => $fullname . '[' . $field . ']',
            'id' => $this->get_id() . '_' . $field,
            'class' => 'form-control',
            'rows' => $rows,
        ];
        if ($required) {
            $attrs['required'] = 'required';
        }
        return $this->field_row($field, \html_writer::tag('textarea', s($value), $attrs));
    }

    /**
     * Render the key type select.
     *
     * @param string $fullname Full setting form name.
     * @param string $value Current key type.
     * @return string
     */
    private function select_keytype(string $fullname, string $value): string {
        $options = [
            lti_tool_manager::KEYTYPE_KEYSET => get_string('keytypekeyset', 'local_oookltimenu'),
            lti_tool_manager::KEYTYPE_RSA => get_string('keytypersa', 'local_oookltimenu'),
        ];
        return $this->field_row('keytype', \html_writer::select(
            $options,
            $fullname . '[keytype]',
            $value,
            false,
            [
                'id' => $this->get_id() . '_keytype',
                'class' => 'form-select',
            ]
        ));
    }

    /**
     * Render the launch container select.
     *
     * @param string $fullname Full setting form name.
     * @param int $value Current launch container value.
     * @return string
     */
    private function select_launchcontainer(string $fullname, int $value): string {
        $options = [
            2 => get_string('launchcontainerembed', 'local_oookltimenu'),
            3 => get_string('launchcontainerembednoblocks', 'local_oookltimenu'),
            4 => get_string('launchcontainernewwindow', 'local_oookltimenu'),
            5 => get_string('launchcontainerexistingwindow', 'local_oookltimenu'),
        ];
        return $this->field_row('launchcontainer', \html_writer::select(
            $options,
            $fullname . '[launchcontainer]',
            $value,
            false,
            [
                'id' => $this->get_id() . '_launchcontainer',
                'class' => 'form-select',
            ]
        ));
    }

    /**
     * Render the tool visibility select.
     *
     * @param string $fullname Full setting form name.
     * @param int $value Current visibility value.
     * @return string
     */
    private function select_coursevisible(string $fullname, int $value): string {
        $options = [
            0 => get_string('coursevisiblehidden', 'local_oookltimenu'),
            1 => get_string('coursevisiblepreconfigured', 'local_oookltimenu'),
            2 => get_string('coursevisibleactivitychooser', 'local_oookltimenu'),
        ];
        return $this->field_row('coursevisible', \html_writer::select(
            $options,
            $fullname . '[coursevisible]',
            $value,
            false,
            [
                'id' => $this->get_id() . '_coursevisible',
                'class' => 'form-select',
            ]
        ));
    }

    /**
     * Render a checkbox row.
     *
     * @param string $fullname Full setting form name.
     * @param string $field Field key.
     * @param bool $checked Current state.
     * @return string
     */
    private function checkbox(string $fullname, string $field, bool $checked): string {
        $html = \html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $fullname . '[' . $field . ']',
            'value' => 0,
        ]);
        $attrs = [
            'type' => 'checkbox',
            'name' => $fullname . '[' . $field . ']',
            'id' => $this->get_id() . '_' . $field,
            'value' => 1,
        ];
        if ($checked) {
            $attrs['checked'] = 'checked';
        }
        $html .= \html_writer::empty_tag('input', $attrs);
        return $this->field_row($field, $html);
    }

    /**
     * Render platform details for the external tool provider.
     *
     * @return string
     */
    private function platform_details(): string {
        $details = lti_tool_manager::get_platform_details();
        if (empty($details)) {
            return \html_writer::div(
                get_string('platformdetailsmissing', 'local_oookltimenu'),
                'alert alert-info mt-3'
            );
        }

        $clientid = $details['clientid'] ?? '';
        $clientidhtml = '';
        if ($clientid !== '') {
            $clientidhtml = \html_writer::div(
                \html_writer::tag('strong', get_string('clientidready', 'local_oookltimenu')) .
                \html_writer::tag('code', s((string)$clientid), ['class' => 'd-block mt-2']),
                'alert alert-success mt-3'
            );
        }

        $rows = '';
        foreach ($details as $key => $value) {
            $rows .= \html_writer::tag('dt', get_string('platform_' . $key, 'local_oookltimenu'));
            $rows .= \html_writer::tag('dd', s((string)$value));
        }

        return $clientidhtml . \html_writer::div(
            \html_writer::tag('h4', get_string('platformdetails', 'local_oookltimenu')) .
            \html_writer::tag('dl', $rows, ['class' => 'local-oookltimenu-platformdetails']),
            'alert alert-secondary mt-3'
        );
    }

    /**
     * Render fixed custom parameters.
     *
     * @return string
     */
    private function fixed_custom_parameters(): string {
        $html = \html_writer::tag('pre', s(lti_tool_manager::get_fixed_custom_parameters()), [
            'class' => 'border rounded bg-light p-2 mb-0',
        ]);
        return $this->field_row('customparameters', $html);
    }

    /**
     * Render one field row.
     *
     * @param string $field Field key.
     * @param string $element Field HTML.
     * @return string
     */
    private function field_row(string $field, string $element): string {
        $label = \html_writer::tag(
            'label',
            get_string('setting_' . $field, 'local_oookltimenu'),
            ['for' => $this->get_id() . '_' . $field, 'class' => 'form-label']
        );
        $help = \html_writer::div(
            get_string('setting_' . $field . '_desc', 'local_oookltimenu'),
            'form-text'
        );
        return \html_writer::div($label . $element . $help, 'mb-3');
    }
}
