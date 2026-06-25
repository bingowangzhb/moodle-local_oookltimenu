# Moodle Plugins Directory Metadata Draft

Use this file as the working copy for the Moodle Plugins directory submission form.

## Plugin Identity

- Plugin name: OOOK LTI course menu
- Frankenstyle component: `local_oookltimenu`
- Plugin type: Local plugin
- Repository: <https://github.com/bingowangzhb/moodle-local_oookltimenu>
- Issue tracker: <https://github.com/bingowangzhb/moodle-local_oookltimenu/issues>
- License: GNU GPL v3 or later
- Minimum Moodle version: 4.3

## Short Description

Adds an OOOK course navigation item that launches a configured LTI 1.3 tool through Moodle core mod_lti.

## Full Description

OOOK LTI course menu adds an `OOOK` item to Moodle course secondary navigation. The item launches an administrator-configured LTI 1.3 tool using Moodle core `mod_lti`.

The plugin creates and manages a Moodle site-level External tool configuration from its settings page. When a course menu launch is first opened by a user who can manage activities, the plugin creates one hidden helper LTI activity in that course. Later users reuse the same helper activity, while Moodle core handles the standard LTI 1.3 launch flow.

The plugin is intended for Moodle sites that already have an external LTI 1.3 provider and want to expose that provider from course navigation instead of requiring teachers to manually add an External tool activity to every course.

## Features

- Adds an `OOOK` item to course secondary navigation.
- Creates or updates a managed Moodle External tool configuration.
- Displays Moodle platform registration details for the external LTI provider.
- Auto-creates one hidden helper LTI activity per course when needed.
- Launches through Moodle core `mod_lti`, including Moodle's LTI 1.3 OIDC flow.
- Allows administrators to enable or disable the course navigation item without deleting the managed tool.
- Cleans up plugin-created helper activities and the managed External tool configuration on uninstall.

## Dependencies

- Moodle 4.3 or later.
- Moodle core `mod_lti` must be enabled.
- An external LTI 1.3 tool provider must be configured by the site administrator.

## External Services

This plugin requires an administrator-configured external LTI 1.3 provider. It does not include an LTI tool service, credentials, shared secrets, or API keys. Provider URLs are left blank by default and must be entered by the administrator.

The configured LTI provider receives launch data from Moodle core `mod_lti` according to the site configuration and the plugin's LTI settings.

## Privacy Summary

The plugin does not create its own database tables and does not store user records itself.

During LTI launch, Moodle core `mod_lti` may send user and course information to the configured external LTI provider. Depending on the administrator settings, this can include Moodle user ID, username, full name, email address, course role, course ID, and course full name.

The plugin implements Moodle Privacy API metadata for this external data flow.

## Suggested Tags

- lti
- lti13
- external tool
- course navigation
- local plugin

## Screenshots To Capture

- Plugin settings page before saving, showing the LTI 1.3 configuration fields.
- Plugin settings page after saving, showing generated Moodle platform details.
- Course page showing the `OOOK` item in secondary navigation.
- Launch page showing the configured external LTI tool opened from the course menu.

## Manual Test Checklist

- Install the plugin from a ZIP whose root directory is `oookltimenu`.
- Confirm the plugin appears under `Site administration > Plugins > Local plugins`.
- Save a valid LTI 1.3 provider configuration.
- Confirm the managed External tool is created in Moodle `mod_lti`.
- Configure the external provider with Moodle's generated platform details.
- Open a course as a teacher and click `OOOK`.
- Confirm the hidden helper LTI activity is created.
- Open the same course as a student and click `OOOK`.
- Confirm the launch succeeds without manually adding an External tool activity to the course.
- Disable the course menu setting and confirm the menu item is hidden.
- Uninstall the plugin and confirm plugin-created helper activities and the managed External tool are removed.
