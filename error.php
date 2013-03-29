<?php
/**
 * Applications Error Page
 * @copyright 2012 Bruno Sampaio
 */

require_once('../../config.php');
require_once('lib.php');

$site = get_site();

$strapplications = get_string('applications', 'local_applications');
$strerror = get_string('error-application', 'local_applications');

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/applications/error.php');
$PAGE->set_context(context_system::instance());

if(local_applications_table_exists()) {
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/'));
}
else {
	$PAGE->navbar->add($strapplications, new moodle_url('/local/applications/index.php'));
	$PAGE->navbar->add($strerror);

	$PAGE->set_title("$site->shortname: $strerror");
	$PAGE->set_heading("$strerror");

	echo $OUTPUT->header();
	echo $OUTPUT->heading("$strerror");
	
	echo local_applications_print_table_error();
	
	echo $OUTPUT->footer();
}