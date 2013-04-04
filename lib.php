<?php
/**
 * Library of useful functions
 * @copyright 2012 Bruno Sampaio
 * @package core
 * @subpackage application
 */

defined('MOODLE_INTERNAL') || die;

define('APPLICATIONS_TABLE', 'local_applications');

// Add Link to Navigation Block
global $PAGE;
if(isloggedin()) $PAGE->navigation->add(get_string('my-applications', 'local_applications'), new moodle_url('/local/applications/index.php'));

/**
 * Verify if applications tables exists.
 * @return bool
 */
function local_applications_table_exists() {
	global $DB;
	
	$dbman = $DB->get_manager();
	
	return $dbman->table_exists(APPLICATIONS_TABLE);
}

/**
 * Print Table Error
 * @return string (HTML)
 */
function local_applications_print_table_error($classes='') {
	return 
		html_writer::start_tag('div', array('class' => "notifyproblem $classes")).
			get_string('error-database-table', 'local_applications').
		html_writer::end_tag('div');
}

/**
 * Print Id Error
 * @return string (HTML)
 */
function local_applications_print_id_error() {
	return 
		'<p class="errormessage">'.
			get_string('error-noid', 'local_applications').
		'</p>';
}

/**
 * Print Permissions Error
 * @return string (HTML)
 */
function local_applications_print_permissions_error($classes='') {
	return 
		html_writer::start_tag('div', array('class' => "notifyproblem $classes")).
			get_string('error-permissions', 'local_applications').
		html_writer::end_tag('div');
}

/**
 * Get current user applications.
 * @return objects array
 */
function local_applications_get_user_apps($conditions=array()) {
	global $DB, $USER;
	
	$conditions['userid'] = $USER->id;
	$applications = $DB->get_records(APPLICATIONS_TABLE, $conditions, 'name');
	
	return $applications;
}

/**
 * Get current user favorite applications.
 * @return objects array
 */
function local_applications_get_user_favorite_apps() {
	return local_applications_get_user_apps(array('favorite' => 1));
}

/**
 * Create a application and either return a $application object
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $data - all the data needed for an entry in the 'applications' table
 * @return object new application instance
 */
function local_applications_create($data) {
	global $DB;
	
	$data->servicesalt = uniqid('', true);
	$id = $DB->insert_record(APPLICATIONS_TABLE, $data);
	$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$id));
	
	add_to_log(SITEID, APPLICATIONS_TABLE, 'new', 'view.php?id='.$id, $application->name.' (ID '.$id.')');
	
	return $application;
}

/**
 * Update an application.
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $data - all the data needed for an entry in the 'application' table
 * @return void
 */
function local_applications_update($data) {
	global $DB;
	
	// Update with the new data
	$data->servicesalt = uniqid('', true);
    $DB->update_record(APPLICATIONS_TABLE, $data);

	$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$data->id));
	
	add_to_log($application->id, APPLICATIONS_TABLE, "update", "edit.php?id=$application->id", $application->id);
}

/**
 * Change Favorite.
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $data - all the data needed for an entry in the 'application' table
 * @return void
 */
function local_applications_change_favorite($data) {
	global $DB;
	
	$data->favorite = !$data->favorite;
	local_applications_update($data);
}

/**
 * Delete an application.
 *
 * Please note this functions does not verify any access control,
 * the calling code is responsible for all validation (usually it is the form definition).
 *
 * @param object $id  - application identifier
 * @return boolean
 */
function local_applications_delete($id) {
	global $DB;
	
	$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$id));
	if(empty($application)) {
		return false;
	}
	else {
		$DB->delete_records(APPLICATIONS_TABLE, array('id' => $id));
		
		add_to_log(SITEID, APPLICATIONS_TABLE, "delete", "view.php?id=$application->id", "$application->name (ID $application->id)");
		
		return true;
	}
}

/**
 * Can the current user edit this application?
 * @param int $id
 * @return boolean
 */
function local_applications_can_edit($userid) {
    global $USER;

    return isloggedin() && !isguestuser() && ($USER->id == $userid);
}
