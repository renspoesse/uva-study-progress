# uva-study-progress

StudyProgress Dashboard for the University of Amsterdam, 2ndYD

## Development

1. As the local source code is mounted into the container(s) at runtime, any dependencies should be installed in the local folder: `composer install --ignore-platform-reqs`.
2. If you don't have an `APP_KEY` yet you should run `php artisan key:generate --show` and add the value to `.env.development.local`.
3. Run `composer run docker-build` to build the local Docker images.
4. Start the development environment with the `docker-compose up` command.
5. Optionally, run `composer run docker-migrate` and `composer run docker-seed` to prepare your database.

By default, the following services are accessible from your browser:

- `http://localhost:8888/api`
- `http://localhost:8889`, phpMyAdmin

`docker-compose down` will stop the services. Next time, you'll only have to run `docker-compose up` again to start developing.

## Roles

The following roles are used in the application and should be provided by the LTI tool consumer:

1. Administrator: `feb_dashboard_admin` (was: `rens_1`)
2. Study Adviser: `feb_dashboard_studyadviser` (was: `rens_2`)
3. Student: `urn:lti:role:ims/lis/Learner`

## LTI setup

For Canvas LMS, an XML is available for easily integrating the tool at `/lti/canvas.xml`.

In general, the following LTI parameters are required:

- Launch URL: `https://path.to.tool/lti/launch`.
- Custom parameter**: `custom_student_number` should provide a unique, display-friendly student identifier.

Optional parameters:

- Custom parameter**: `custom_role_membership` provides a way to override standard LTI roles. Useful when a tool consumer / LMS doesn't support the required roles.

** Note: Blackboard LMS seems to automatically prefix custom parameters with `custom_`.

Most tool consumers will automatically provide all other parameters necessary. The [LTI documentation](https://www.imsglobal.org/activity/learning-tools-interoperability) can be consulted for instructions on creating a complete request.
