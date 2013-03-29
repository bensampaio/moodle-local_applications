<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

class application_edit_form extends moodleform {
    protected $application;
    protected $context;

    function definition() {
        global $USER, $CFG, $DB;

        $mform =& $this->_form;

        $application = $this->_customdata['application']; // this contains the data of this form
        $context = get_context_instance(CONTEXT_SYSTEM);

        $this->application  = $application;
        $this->context = $context;


		/// form definition with new application general settings
		//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));


		//Name
		$nameapplication = get_string('field-name', 'local_applications');
        $mform->addElement('text', 'name', $nameapplication, 'maxlength="254" size="100"');
        $mform->addRule('name', get_string('missingname'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);


		//Description
		$descriptionapplication = get_string('field-description', 'local_applications');
        $mform->addElement('textarea', 'description', $descriptionapplication, 'wrap="virtual" rows="10" cols="100"');
        $mform->setType('description', PARAM_RAW);


		// Tool Type
        $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'lti'), array());
        $mform->addHelpButton('typeid', 'external_tool_type', 'lti');

        foreach (lti_get_types_for_add_instance() as $id => $type) {
            if ($id != 0) {
                $attributes = array( 'globalTool' => 1, 'domain' => $type->tooldomain);
            }
			else {
                $attributes = array();
            }

            $tooltypes->addOption($type->name, $id, $attributes);
        }


		//URL
		$urlapplication = get_string('launch_url', 'lti');
        $mform->addElement('text', 'toolurl', $urlapplication, 'maxlength="2082" size="100"');
		$mform->addRule('toolurl', get_string('missingurl'), 'required', null, 'client');
        $mform->setType('toolurl', PARAM_MULTILANG);


		//Icon
		$iconapplication = get_string('icon_url', 'lti');
        $mform->addElement('text', 'icon', $iconapplication, 'maxlength="2082" size="100"');
        $mform->setType('icon', PARAM_MULTILANG);

		
		//Key
		$mform->addElement('text', 'resourcekey', get_string('resourcekey', 'lti'));
        $mform->setType('resourcekey', PARAM_TEXT);
        $mform->addHelpButton('resourcekey', 'resourcekey', 'lti');


		//Secret
        $mform->addElement('passwordunmask', 'secret', get_string('password', 'lti'));
        $mform->setType('secret', PARAM_TEXT);
        $mform->addHelpButton('secret', 'password', 'lti');
		
		
		//Launch Container
		$launchoptions=array();
        $launchoptions[LTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'lti');

		$mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'lti'), $launchoptions);
        $mform->setDefault('launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
        $mform->addHelpButton('launchcontainer', 'launchinpopup', 'lti');

		//Favorite
		$favoriteapplication = get_string('field-favorite', 'local_applications');
        $mform->addElement('advcheckbox', 'favorite', $favoriteapplication, 'Default: No', array('group' => 1), array(0, 1));
        $mform->setType('favorite', PARAM_BOOL);


		//-------------------------------------------------------------------------------
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'lti'));

        $mform->addElement('advcheckbox', 'sendname', '&nbsp;', get_string('share_name', 'lti'), array('group' => 1), array(0, 1));
        $mform->setDefault('sendname', 1);
        $mform->addHelpButton('sendname', 'share_name', 'lti');

        $mform->addElement('advcheckbox', 'sendemail', '&nbsp;', get_string('share_email', 'lti'), array('group' => 1), array(0, 1));
        $mform->setDefault('sendemail', 1);
        $mform->addHelpButton('sendemail', 'share_email', 'lti');


		//--------------------------------------------------------------------------------
        $this->add_action_buttons();
		//--------------------------------------------------------------------------------
        
		$mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

		$mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);

		/// finally set the current form data
		//--------------------------------------------------------------------------------
        $this->set_data($application);
	}
}

