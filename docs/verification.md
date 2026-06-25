# Verification Notes

Date: 2026-06-25

## Completed Locally

- Git worktree was clean before starting this verification pass.
- Release ZIP was generated at `dist/local_oookltimenu_v2026062505.zip`.
- ZIP root directory is `oookltimenu/`.
- ZIP contains `oookltimenu/version.php`.
- ZIP contains `oookltimenu/lang/en/local_oookltimenu.php`.
- ZIP contains no `zh_cn` language directory.
- ZIP contains no dotfiles such as `.gitignore` or `.gitattributes`.
- Static string checks found no `local_oookltimenuauto` references.
- Static string checks found no `pre-api-lti.oook.cn` references.
- `version.php` declares component `local_oookltimenu`, minimum Moodle version `2023100400`, and release `1.0.0`.

## Local Environment Limitations

The current workstation cannot complete Moodle PHP checks locally:

- Available PHP is `E:\Tools\php-7.3.6-Win32-VC15-x64\php.exe`.
- Moodle 4.3+ and this plugin require a newer PHP runtime for meaningful validation.
- `phpcs` is not installed.
- Docker is not installed.
- Composer is present, but it uses the unavailable/invalid PHP runtime and could not be used for Moodle Plugin CI locally.
- `..\oook-lti13-tool\.tmp-moodle-src` is Moodle source code, not an installed Moodle site with a real `config.php` and database.

## GitHub Actions CI

A Moodle Plugin CI workflow is provided at `.github/workflows/moodle-ci.yml`.

It runs on push and pull request against:

- Moodle branch: `MOODLE_403_STABLE`
- PHP: `8.1`
- Databases: `pgsql`, `mariadb`

The workflow installs Moodle and the plugin, then runs:

- `moodle-plugin-ci phplint`
- `moodle-plugin-ci phpcs --max-warnings 0`
- `moodle-plugin-ci phpdoc --max-warnings 0`
- `moodle-plugin-ci validate`
- `moodle-plugin-ci savepoints`

## Manual Tests Still Required

Run these in a real Moodle 4.3+ site with supported PHP:

- Install `dist/local_oookltimenu_v2026062505.zip` through Moodle's plugin installer.
- Confirm the plugin appears under `Site administration > Plugins > Local plugins`.
- Save a valid LTI 1.3 provider configuration.
- Confirm Moodle creates the managed External tool configuration.
- Configure the external provider with Moodle's generated platform details.
- Open a course as a teacher and click `OOOK`.
- Confirm the hidden `[OOOK LTI AUTO]` helper activity is created.
- Open the same course as a student and click `OOOK`.
- Confirm the LTI launch succeeds without manually adding an External tool activity.
- Disable the course menu setting and confirm the menu item is hidden.
- Uninstall the plugin and confirm helper activities and the managed External tool are removed.
