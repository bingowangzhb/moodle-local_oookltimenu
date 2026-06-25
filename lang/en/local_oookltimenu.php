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
 * English language strings for local_oookltimenu.
 *
 * @package    local_oookltimenu
 * @copyright  2026 OOOK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'OOOK LTI course menu';
$string['menuitemdefault'] = 'OOOK';
$string['targettypeid'] = 'Target LTI tool type id';
$string['targettypeid_desc'] = 'Required for menu launch. The plugin will auto-create a hidden LTI activity in each course and launch it in an iframe.';
$string['ltitoolconfig'] = 'Managed LTI 1.3 tool';
$string['ltitoolconfig_desc'] = 'Configure the external LTI 1.3 tool used by the OOOK course menu. Saving this page creates or updates a Moodle External tool configuration and stores its generated deployment id automatically.';
$string['managedtooldescription'] = 'Managed by the OOOK LTI course menu plugin.';
$string['setting_toolname'] = 'Tool name';
$string['setting_toolname_desc'] = 'Display name for the Moodle External tool configuration created by this plugin.';
$string['setting_toolurl'] = 'Tool launch URL';
$string['setting_toolurl_desc'] = 'The LTI 1.3 launch URL provided by the external tool.';
$string['setting_tooldescription'] = 'Tool description';
$string['setting_tooldescription_desc'] = 'Description saved on the Moodle External tool configuration.';
$string['setting_initiatelogin'] = 'Initiate login URL';
$string['setting_initiatelogin_desc'] = 'The OpenID Connect login initiation URL provided by the external tool.';
$string['setting_redirectionuris'] = 'Redirect URI(s)';
$string['setting_redirectionuris_desc'] = 'One redirect URI per line. These must match the redirect URI values used by the external tool.';
$string['setting_keytype'] = 'Public key type';
$string['setting_keytype_desc'] = 'Use a JWKS URL when the external tool publishes a keyset, or paste a public RSA key.';
$string['setting_publickeyset'] = 'JWKS URL';
$string['setting_publickeyset_desc'] = 'Required when the public key type is JWKS URL.';
$string['setting_publickey'] = 'Public RSA key';
$string['setting_publickey_desc'] = 'Required when the public key type is public RSA key.';
$string['setting_customparameters'] = 'Custom parameters';
$string['setting_customparameters_desc'] = 'These LTI custom parameters are fixed by the plugin and are saved to the Moodle External tool configuration.';
$string['setting_coursevisible'] = 'Tool configuration usage';
$string['setting_coursevisible_desc'] = 'Controls whether the managed Moodle External tool appears when teachers add external tool activities.';
$string['setting_launchcontainer'] = 'Launch container';
$string['setting_launchcontainer_desc'] = 'How Moodle should open the external tool from the hidden helper activity.';
$string['setting_navenabled'] = 'Show OOOK course menu';
$string['setting_navenabled_desc'] = 'When enabled, the OOOK item is added to course navigation. When disabled, the managed LTI tool remains configured but the course navigation item is hidden.';
$string['setting_sendname'] = 'Share user name';
$string['setting_sendname_desc'] = 'Send the launcher name to the external tool during LTI launch.';
$string['setting_sendemailaddr'] = 'Share user email';
$string['setting_sendemailaddr_desc'] = 'Send the launcher email address to the external tool during LTI launch.';
$string['keytypekeyset'] = 'JWKS URL';
$string['keytypersa'] = 'Public RSA key';
$string['coursevisiblehidden'] = 'Do not show; use only when launched by OOOK';
$string['coursevisiblepreconfigured'] = 'Show as preconfigured tool when adding an external tool';
$string['coursevisibleactivitychooser'] = 'Show in activity chooser and as a preconfigured tool';
$string['launchcontainerembed'] = 'Embed';
$string['launchcontainerembednoblocks'] = 'Embed, without blocks';
$string['launchcontainernewwindow'] = 'New window';
$string['launchcontainerexistingwindow'] = 'Existing window';
$string['platformdetails'] = 'Moodle platform details for the external tool';
$string['platformdetailsmissing'] = 'Platform details will be shown after the LTI tool configuration is saved successfully.';
$string['clientidready'] = 'Client ID to configure in your LTI tool';
$string['platform_platformid'] = 'Platform ID';
$string['platform_clientid'] = 'Client ID';
$string['platform_deploymentid'] = 'Deployment ID';
$string['platform_publickeyseturl'] = 'Public keyset URL';
$string['platform_accesstokenurl'] = 'Access token URL';
$string['platform_authrequesturl'] = 'Authentication request URL';
$string['errorinvalidconfig'] = 'Invalid LTI tool configuration.';
$string['errorrequiredtoolname'] = 'Tool name is required.';
$string['errorrequiredtoolurl'] = 'A valid tool launch URL is required.';
$string['errorrequiredinitiatelogin'] = 'A valid initiate login URL is required.';
$string['errorrequiredredirecturis'] = 'At least one valid redirect URI is required.';
$string['errorinvalidredirecturi'] = 'Invalid redirect URI: {$a}';
$string['errorrequiredpublickeyset'] = 'A valid JWKS URL is required when JWKS URL is selected.';
$string['errorrequiredpublickey'] = 'A public RSA key is required when public RSA key is selected.';
$string['errorsyncfailed'] = 'Failed to create or update the Moodle LTI tool: {$a}';
$string['missingtypeidconfig'] = 'No target LTI tool type id is configured for OOOK LTI menu.';
$string['invalidtooltypeid'] = 'Configured LTI tool type id is invalid or unavailable.';
$string['autoltiinitrequired'] = 'OOOK LTI has not been initialized in this course yet. Please ask a teacher to open OOOK LTI once to initialize it.';
$string['autolticreatefailed'] = 'Failed to initialize hidden OOOK LTI activity for this course.';
$string['privacy:metadata:externalpurpose'] = 'The plugin opens a Moodle LTI activity for the configured external tool. Moodle core mod_lti may send user and course context according to this plugin configuration and Moodle LTI privacy settings.';
$string['privacy:metadata:userid'] = 'The Moodle user ID may be sent to the external LTI tool.';
$string['privacy:metadata:username'] = 'The Moodle username may be sent to the external LTI tool.';
$string['privacy:metadata:fullname'] = 'The user full name may be sent to the external LTI tool when name sharing is enabled.';
$string['privacy:metadata:email'] = 'The user email address may be sent to the external LTI tool when email sharing is enabled.';
$string['privacy:metadata:role'] = 'The user course role may be sent to the external LTI tool.';
$string['privacy:metadata:courseid'] = 'The Moodle course ID may be sent to the external LTI tool.';
$string['privacy:metadata:coursefullname'] = 'The course full name may be sent to the external LTI tool.';
