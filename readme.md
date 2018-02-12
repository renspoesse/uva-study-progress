# uva-study-progress

StudyProgress Dashboard for the University of Amsterdam, 2ndYD

## Roles

The following roles are used in the application and should be provided by the LTI tool consumer:

1. Administrator: `rens_1`*
2. Study Adviser: `rens_2`*
3. Student: `urn:lti:role:ims/lis/Learner`

\* Note: these roles will be renamed in a future version but are currently retained for compatibility.

## LTI setup

For Canvas LMS, an XML is available for easily integrating the tool at `/lti/canvas.xml`.

In general, the following LTI parameters are required:

- Launch URL: `https://path.to.tool/lti/launch`.
- Custom** parameter: `custom_student_number` should provide a unique, display-friendly student identifier.

Optional parameters:

- Custom** parameter: `custom_role_membership` provides a way to override standard LTI roles. Useful when a tool consumer / LMS doesn't support the required roles.

** Note: Blackboard LMS seems to automatically prefix custom parameters with `custom_`.

Most tool consumers will automatically provide all other parameters necessary. The [LTI documentation](https://www.imsglobal.org/activity/learning-tools-interoperability) can be consulted for instructions on creating a complete request.