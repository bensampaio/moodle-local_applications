<?php

/**
 * A page to display an application.
 * @copyright 2012 Bruno Sampaio
 */

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');
require_login();

$id = optional_param('id', 0, PARAM_INT);	// application id

$site = get_site();

$strapplications = get_string('applications', 'local_applications');
$striframeproblem = get_string('error-iframe', 'local_applications');

$PAGE->set_url('/local/applications/view.php');
$PAGE->set_context(context_system::instance());

if(!local_applications_table_exists()) {	//Table not exists
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/error.php'));
}
else {
	$title = get_string('error-application', 'local_applications');
	$PAGE->navbar->add($strapplications, new moodle_url('/local/applications/index.php'));
	$empty = true;
	
	if($id) {
		$application = $DB->get_record(APPLICATIONS_TABLE, array('id'=>$id), '*', MUST_EXIST);
	    $PAGE->url->param('id',$id);
	
		if(!empty($application)) {
			$empty = false;
			if(local_applications_can_edit($application->userid)) {
				
				$title = $application->name;

				$PAGE->navbar->add($title);
				$PAGE->set_title("$site->shortname: $title");
				
				// Get tool configuration
				$tool = lti_get_tool_by_url_match($application->toolurl);
				if ($tool) {
				    $toolconfig = lti_get_type_config($tool->id);
				} 
				else {
				    $toolconfig = array();
				}
				
				// Get launch container
				$launchcontainer = lti_get_launch_container($application, $toolconfig);
				if ($launchcontainer == LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS) {
					$PAGE->set_pagelayout('embedded');
				    $PAGE->blocks->show_only_fake_blocks();
				}
				else if ($launchcontainer == LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW) {
				    redirect('launch.php?id=' . $cm->id);
				} 
				else {
				    $PAGE->set_pagelayout('standard');
				}
				
				//now the page contents
				echo $OUTPUT->header();
				
				if ( $launchcontainer == LTI_LAUNCH_CONTAINER_WINDOW ) {
				    echo "<script language=\"javascript\">//<![CDATA[\n";
				    echo "window.open('launch.php?id=".$application->id."','".$applications->name."');";
				    echo "//]]\n";
				    echo "</script>\n";
				    echo "<p>".get_string("basiclti_in_new_window", "lti")."</p>\n";
				} else {
				    // Request the launch content with an iframe tag
				   echo '<iframe src="launch.php?id='.$application->id.'" frameborder="0" allowtransparency="true" >'.$striframeproblem.'</iframe>';
				}
			}
			else {
				$PAGE->navbar->add($title);
				$PAGE->set_title("$site->shortname: $title");
				$PAGE->set_heading($title);

				//now the page contents
				echo $OUTPUT->header();

				echo local_applications_print_permissions_error();
			}
		}
	}
	
	if($empty) {
		$PAGE->navbar->add($title);
		$PAGE->set_title("$site->shortname: $title");
		$PAGE->set_heading($title);

		//now the page contents
		echo $OUTPUT->header();
		
		echo local_applications_print_id_error();
	}
	
	echo $OUTPUT->footer();
}