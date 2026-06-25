# local_oookltimenuauto

Adds an `OOOK` item into course secondary navigation and launches a managed LTI 1.3 tool in an iframe.

Requires Moodle 4.3 or later.

## Flow

1. An administrator installs the plugin.
2. The administrator configures the external LTI 1.3 tool on the plugin settings page.
3. The plugin creates or updates a Moodle site-level External tool configuration through core `mod_lti`.
4. The generated Moodle platform details are shown on the settings page for registration in the external tool.
5. A user clicks `OOOK` in a course.
6. The plugin finds or auto-creates one hidden LTI activity in that course bound to the managed LTI tool type.
7. The plugin page renders an iframe and loads `/mod/lti/launch.php?id={cmid}`.
8. Moodle core `mod_lti` executes the LTI 1.3 launch flow.

## Settings

- Site administration -> Plugins -> Local plugins -> OOOK LTI course menu
- Configure the managed LTI 1.3 tool:
  - Tool launch URL
  - Tool description
  - Initiate login URL
  - Redirect URI(s)
  - JWKS URL or public RSA key
  - Tool configuration usage
  - User name/email sharing
  - Launch container
  - Course navigation visibility

The plugin writes these fixed custom parameters to the Moodle External tool configuration:

```text
course_id=$Context.id
user_id=$User.id
email=$Person.email.primary
course_title=$Context.title
```

After saving, copy the generated platform details into the external LTI tool:

- Platform ID
- Client ID
- Deployment ID
- Public keyset URL
- Access token URL
- Authentication request URL

## Course menu behavior

1. User clicks `OOOK` in a course.
2. Plugin finds (or auto-creates) one hidden LTI activity in that course bound to configured `typeid`.
3. Plugin page renders iframe and loads `/mod/lti/launch.php?id={cmid}`.
4. Moodle native LTI launch flow (including LTI 1.3 OIDC) is executed by core `mod/lti`.

If the managed LTI tool has not been configured yet, the course menu item is hidden from non-admin users. Site administrators are linked to the plugin settings page.

The navigation item can also be disabled from the plugin settings page without deleting the managed LTI tool.
