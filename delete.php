<?php

/**
 * Delete application code.
 * @copyright 2012 Bruno Sampaio
 */

require_once('../../config.php');
require_once('lib.php');
require_login();

$strapplications = get_string('applications', 'local_applications');
$strdeleteapplication = get_string('delete-application', 'local_applications');

$id = required_param('id', PARAM_INT);	// application id

$PAGE->set_url('/local/applications/delete.php', array('id' => $id));
$PAGE->set_context(context_system::instance());

$site = get_site();

if(!local_applications_table_exists()) {	//Table not exists
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/error.php'));
}
else {
	if (!$application = $DB->get_record(APPLICATIONS_TABLE, array("id"=>$id))) {
		echo local_applications_print_id_error();
	}

	if (!local_applications_can_edit($application->userid)) {
		echo local_applications_print_permissions_error();
	}
	
	$PAGE->set_title("$site->shortname: $strdeleteapplication");

	$PAGE->navbar->add($strapplications, new moodle_url('/local/applications/index.php'));
	$PAGE->navbar->add($strdeleteapplication);

	$PAGE->set_heading($strdeleteapplication);

	echo $OUTPUT->header();
	echo $OUTPUT->heading($strdeleteapplication);

	if(local_applications_delete($id)) {
		echo html_writer::start_tag('div', array('class' => 'notifysuccess'));
			echo 'Application was deleted successfully.';
		echo html_writer::end_tag('div');
	}
	else {
		echo html_writer::start_tag('div', array('class' => 'notifyproblem'));
			echo 'Application couldn\'t be deleted.';
		echo html_writer::end_tag('div');
	}
	
	echo $OUTPUT->continue_button("index.php");

	echo $OUTPUT->footer();
}