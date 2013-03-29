<?php

/**
 * A page displaying applications.
 * @copyright 2012 Bruno Sampaio
 */

require_once('../../config.php');
require_once('lib.php');
require_login();

$strapplications = get_string('applications', 'local_applications');
$straddapplication = get_string('add-application', 'local_applications');
$streditapplication = get_string('edit-application', 'local_applications');
$strdeleteapplication = get_string('delete-application', 'local_applications');
$stremptyapplications = get_string('warning-empty', 'local_applications');

$site = get_site();

$url = new moodle_url('/local/applications/index.php');
$PAGE->set_url($url);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

if(!local_applications_table_exists()) {	//Table not exists
	redirect(new moodle_url($CFG->wwwroot.'/local/applications/error.php'));
}
else {
	$PAGE->navbar->add($strapplications);
	$PAGE->set_title("$site->shortname: $strapplications");
	$PAGE->set_heading($strapplications);

	//now the page contents
	echo $OUTPUT->header();
	
	//Buttons
	echo $OUTPUT->container_start('buttons');
		echo $OUTPUT->single_button(new moodle_url('edit.php'), $straddapplication, 'get');
	echo $OUTPUT->container_end();

	echo $OUTPUT->box_start('applicationboxes');
	
	$applications = local_applications_get_user_apps();
	
	//Table is empty
	if(empty($applications)) {	
		echo html_writer::start_tag('div', array('class' => 'empty'));
			echo $stremptyapplications;
		echo html_writer::end_tag('div');
	}
	else {
		echo html_writer::start_tag('ul', array('class'=>'unlist'));
		foreach ($applications as $application) {
			$url_options = array('id' => $application->id);
			
			echo html_writer::start_tag('li');
				echo html_writer::start_tag('div', array('class'=>'applicationbox clearfix'));
                	echo html_writer::start_tag('div', array('class'=>'info'));

					//Application Name
				    echo html_writer::start_tag('h3', array('class'=>'name'));
						echo html_writer::link(new moodle_url('view.php', $url_options), $application->name);
					echo html_writer::end_tag('h3');

					//Application Logo
					echo html_writer::link(new moodle_url('view.php', $url_options), '<img src="'.$application->icon.'" alt="'.$application->name.'"/>', array('class' => 'icon'));

				echo html_writer::end_tag('div');

				//Application Description
				echo html_writer::start_tag('div', array('class'=>'summary'));
					echo '<p>'.$application->description.'</p>';
				echo html_writer::end_tag('div');

				//Application Options
				echo html_writer::start_tag('div', array('class'=>'options'));

					//Edit
					$options = array('title' => $streditapplication);
					$image = '<img src="'.$OUTPUT->pix_url('t/edit').'" alt="'.$options['title'].'" />';
					echo html_writer::link(new moodle_url('edit.php', $url_options), $image, $options);
					
					//Favorite
					if($application->favorite) {
						$options = array('title' => get_string('remove-favorite-application', 'local_applications'));
						$image = '<img src="'.$OUTPUT->pix_url('nofavorite', 'local_applications').'" alt="'.$options['title'].'" />';
					}
					else {
						$options = array('title' => get_string('add-favorite-application', 'local_applications'));
						$image = '<img src="'.$OUTPUT->pix_url('favorite', 'local_applications').'" alt="'.$options['title'].'" />';
					}
					
					echo html_writer::link(new moodle_url('favorite.php', $url_options), $image, $options);

					//Delete
					$options = array('title' => $strdeleteapplication);
					$image = '<img src="'.$OUTPUT->pix_url('t/delete').'" alt="'.$options['title'].'" />';
					echo html_writer::link(new moodle_url('delete.php', $url_options), $image, $options);

				echo html_writer::end_tag('div');
				
			echo html_writer::end_tag('li');
        }
		echo html_writer::end_tag('ul');
	}
	
	echo $OUTPUT->box_end();

	echo $OUTPUT->footer();
}


