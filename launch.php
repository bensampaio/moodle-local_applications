<?php
require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

if(!local_applications_table_exists()) {	//Table not exists
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/error.php'));
}
else {
	$id = required_param('id', PARAM_INT); // Application ID
	
	$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$id), '*', MUST_EXIST);
	$course = $DB->get_record('course', array('id' => 1), '*', MUST_EXIST);
	
	require_login($course);
	
	add_to_log($application->id, "application", "launch", "launch.php?id=$application->id", "$application->id");
	
	$application->cmid = 0;
	$application->course = 1;
	$application->intro = $application->description;
	$application->password = $application->secret;
	$application->instructorchoicesendname = $application->sendname;
	$application->instructorchoicesendemailaddr = $application->sendemail;
	$application->instructorcustomparameters = null;
	$application->instructorchoiceacceptgrades = null;
	$application->instructorchoiceallowroster = null;
	$application->debuglaunch = 0;
	lti_view($application);
}