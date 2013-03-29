<?php

/**
 * Edit application settings
 * @copyright 2012 Bruno Sampaio
 */

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');
require_login();

$id = optional_param('id', 0, PARAM_INT);	// application id

$site = get_site();

$strapplication = get_string('application', 'local_applications');
$strapplications = get_string('applications', 'local_applications');
$straddapplication = get_string('add-application', 'local_applications');
$streditapplication = get_string('edit-application', 'local_applications');

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/applications/edit.php');
$PAGE->set_context(context_system::instance());

if(!local_applications_table_exists()) {	//Table not exists
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/error.php'));
}
else {
	// basic access control checks
	if($id) { // editing application
		$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$id), '*', MUST_EXIST);
	    $PAGE->url->param('id',$id);
	
		if(!local_applications_can_edit($application->userid)) {
			echo local_applications_print_permissions_error();
		}
	}
	else {
		$application = array();
	}

	// first create the form
	$editform = new application_edit_form(NULL, array('application'=>$application));
	if($editform->is_cancelled()) {
		redirect(new moodle_url($CFG->wwwroot.'/local/applications/'));
	} 
	else if($data = $editform->get_data()) {
	    // process data if submitted

		if(empty($application->id)) {

	        // Create the application
	        $application = local_applications_create($data);

	    } else {
	        // Update the application
	        local_applications_update($data);
	    }
	    redirect(new moodle_url($CFG->wwwroot.'/local/applications/index.php'));
	}
	
	$PAGE->navbar->add($strapplications, new moodle_url('/local/applications/index.php'));

	if (!empty($application->id)) {
	    $PAGE->navbar->add($streditapplication);
	    $title = "$site->shortname: $streditapplication";
		$heading = $streditapplication;
	} else {
	    $PAGE->navbar->add($straddapplication);
	    $title = "$site->shortname: $straddapplication";
		$heading = $straddapplication;
	}

	$PAGE->set_title($title);
	$PAGE->set_heading($heading);

	echo $OUTPUT->header();
	echo $OUTPUT->heading($heading);

	// Print the form
	$editform->display();
	
	echo $OUTPUT->footer();
}
