APPLICATIONS - MOODLE LOCAL

**IMPORTANT**: I am no longer maintaining this plugin. However if you require changes to it you can submit a Merge Request and I will gladly have a look into it.

DESCRIPTION
-----------

This local plugin introduces the application concept into Moodle. It allows users to add, edit, remove, or access external applications without leaving Moodle. For an application added to Moodle with this plugin can also be provided the information needed to automatically authenticate the user on the application when it is accessed. For this the application must support the IMS LTI standard (http://www.imsglobal.org/lti) and must allow users to be authenticated from external systems using this standard. The process to add applications to Moodle with this feature is very similar to the creation of "External Tool" activities in a course.

An example of an application that supports IMS LTI allowing users to be authenticated automatically is Epik (http://epik.di.fct.unl.pt/epik), an application to create educational games. You just need to create a new account and then you'll find the information needed, to create the application on Moodle, on your Epik user profile.

To manage applications was also created a Moodle block that provides the user a way to create new applications, view all his applications, and access his favorite applications (very similar to the courses block). This plugin can be found on the following link: https://moodle.org/plugins/view.php?plugin=block_applications_list.

I recommend you use this plugin with Cubic, a theme for Moodle that I also created (https://moodle.org/plugins/view.php?plugin=theme_cubic).


CONTENTS ORGANISATION
---------------------

	FOLDERS:
	- db: contains the "install.xml" file with applications table structure;
	- lang: contains languages files for English and Portuguese (Portugal);
	- pix: contains the icons for add or remove as favorite actions;
		
	FILES:
	- lib.php: defines all functions used for applications management and errors generation;
	- index.php: page to display all applications of current user;
	- edit.php: page to add or edit an application;
	- edit_form.php: form to add or edit an application, used on "edit.php" file;
	- favorite.php: page to change an application from favorite to unfavorite or vice-versa;
	- view.php: page to access an application;
	- launch.php: action invoked by "view.php" to send the IMS LTI request to the external application;
	- delete.php: page to delete an application;
	- error.php: applications error page;
	- version.php: plugin version information.
