<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * enrol_teacherskey file description here.
 *
 * @package    enrol_teacherskey
 * @copyright  2022 Alex Sidorov <alex.sidorov@ya.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined("MOODLE_INTERNAL") || die();

class enrol_teacherskey_plugin extends enrol_plugin {

    public function get_fio_default_value($userid, $courseid) {
        global $DB;
        if ($DB->record_exists('teacherskey_data', array('userid' => $userid, 'courseid' => $courseid))) {
            return $DB->get_record('teacherskey_data', array('userid' => $userid, 'courseid' => $courseid))->fio;
        }else return $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => 3))->data;
    }

    public function enrol_page_hook(stdClass $instance)
    {
        global $CFG, $OUTPUT, $USER;
        require_once($CFG->dirroot."/enrol/teacherskey/locallib.php");
        $enrolstatus = $this->can_self_enrol($instance);
        if (true === $enrolstatus){
            $form = new teacherskey_from(null, $instance);

            $defaultdata = new stdClass();
            $defaultdata->fio = $this->get_fio_default_value($USER->id, $instance->courseid);
            $defaultdata->truetechers = false;
            $form->set_data($defaultdata);


            if ($form->is_cancelled()) {
                redirect($CFG->wwwroot . "/my", get_string('cancelm', 'enrol_teacherskey'));
            } else if ($data = $form->get_data()) {

                if ($data->truetechers == 1){
                    $this->enrol_self($data, $instance);
                } else{
                    \core\notification::error(get_string('error_teachers_checkbox', 'enrol_teacherskey'));
                }
            }


        }else {
            // This user can not self enrol using this instance. Using an empty form to keep
            // the UI consistent with other enrolment plugins that returns a form.
            $data = new stdClass();
            $data->header = $this->get_instance_name($instance);
            $data->info = $enrolstatus;

            // The can_self_enrol call returns a button to the login page if the user is a
            // guest, setting the login url to the form if that is the case.
            $url = isguestuser() ? get_login_url() : null;
            $form = new enrol_self_empty_form($url, $data);
        }

        ob_start();
        $form->display();
        $output = ob_get_contents();
        ob_get_clean();
        return $OUTPUT->box($output);

    }

    /**
     * Add new instance of enrol plugin.
     * @param object $course
     * @param array $fields instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_instance($course, array $fields = null) {
        return parent::add_instance($course, $fields);
    }

    public function add_default_instance($course){
        $fields = $this->get_instance_defaults();
        return $this->add_instance($course, $fields);
    }

    public function get_instance_defaults()
    {
        $fields = array();
        $fields['status'] = $this->get_config('status');
        $fields['roleid'] = $this->get_config('roleid');
        return $fields;
    }

    public function can_add_instance($courseid)
    {
        global $CFG, $DB;

        $coursecontext = context_course::instance($courseid);

        if(!has_capability('moodle/course:enrolconfig', $coursecontext) or !has_capability('enrol/teacherskey:config', $coursecontext)){
            return false;
        }

        return true;

    }

    public function can_self_enrol(stdClass $instance, $checkuserenrolment = true)
    {
        global $DB, $USER, $OUTPUT;

        if($checkuserenrolment){

            if(isguestuser()){
                return get_string('noguestaccess', 'teacherskey').$OUTPUT->contine_button(get_login_url());
            }

            // Check if user is already enroled.
            if ($DB->get_record('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
                return get_string('canntenrol', 'enrol_self');
            }

        }

        if ($instance->status != ENROL_INSTANCE_ENABLED) {
            return get_string('cannten', 'enrol_self');
        }

        return true;
    }

    public function get_enrol_info(stdClass $instance)
    {
        return true;
    }

    public function use_standard_editing_ui()
    {
        return true;
    }

    private function enrol_self($data = null, stdClass $instance)
    {
        global $CFG, $DB, $USER;



       $timestart = time();
        if ($instance->enrolperiod) {
            $timeend = $timestart + $instance->enrolperiod;
        } else {
            $timeend = 0;
        }


        if(in_array($data->fio, array(null, false, ''))){
            return;
        }


        $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);

        $save_data = new stdClass();
        $save_data->courseid = $data->id;
        $save_data->userid = $USER->id;
        $save_data->fio = $data->fio;

        if(!$DB->record_exists('teacherskey_data', array('courseid' => $instance->courseid, 'userid' => $USER->id))) {
            $DB->insert_record('teacherskey_data', $save_data);
        }else{
            $save_data->id = $DB->get_record('teacherskey_data', array('courseid' => $instance->courseid, 'userid' => $USER->id))->id;
            $DB->update_record('teacherskey_data', $save_data);
        }

        \core\notification::success(get_string('youenrolledincourse', 'enrol'));

        $groups = $DB->get_records('groups', array('courseid'=>$instance->courseid), 'id', 'id, enrolmentkey');
        var_dump($groups);
        foreach ($groups as $group) {
                // Add user to group.
                require_once($CFG->dirroot.'/group/lib.php');
                groups_add_member($group->id, $USER->id);
                break;

        }


    }

    public function can_delete_instance($instance) {
        $context = context_course::instance($instance->courseid);
        return has_capability('enrol/teacherskey:config', $context);
    }

    /**
     * Return an array of valid options for the status.
     *
     * @return array
     */
    protected function get_status_options() {
        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
            ENROL_INSTANCE_DISABLED => get_string('no'));
        return $options;
    }

    /**
     * Gets a list of roles that this user can assign for the course as the default for self-enrolment.
     *
     * @param context $context the context.
     * @param integer $defaultrole the id of the role that is set as the default for self-enrolment
     * @return array index is the role id, value is the role name
     */
    public function extend_assignable_roles($context, $defaultrole) {
        global $DB;

        $roles = get_assignable_roles($context, ROLENAME_BOTH);
        if (!isset($roles[$defaultrole])) {
            if ($role = $DB->get_record('role', array('id' => $defaultrole))) {
                $roles[$defaultrole] = role_get_name($role, $context, ROLENAME_BOTH);
            }
        }
        return $roles;
    }

    /**
     * Return an array of valid options for the newenrols property.
     *
     * @return array
     */
    protected function get_newenrols_options() {
        $options = array(1 => get_string('yes'), 0 => get_string('no'));
        return $options;
    }

    public function edit_instance_form($instance, MoodleQuickForm $mform, $context)
    {
        global $CFG, $DB;

        $nameattribs = array('size' => '20', 'maxlength' => '255');
        $mform->addElement('text', 'name', get_string('custominstancename', 'enrol'), $nameattribs);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'server');

        $options = $this->get_status_options();
        $mform->addElement('select', 'status', get_string('status', 'enrol_self'), $options);
        $mform->addHelpButton('status', 'status', 'enrol_self');

        $options = $this->get_newenrols_options();
        $mform->addElement('select', 'customint6', get_string('newenrols', 'enrol_self'), $options);
        $mform->addHelpButton('customint6', 'newenrols', 'enrol_self');
        $mform->disabledIf('customint6', 'status', 'eq', ENROL_INSTANCE_DISABLED);

        $roles = $this->extend_assignable_roles($context, $instance->roleid);

        $mform->addElement('select', 'roleid', get_string('role', 'enrol_self'), $roles);

    }

    /**
     * The self enrollment plugin has several bulk operations that can be performed.
     * @param course_enrolment_manager $manager
     * @return array
     */
    public function get_bulk_operations(course_enrolment_manager $manager) {
        global $CFG;
        require_once($CFG->dirroot.'/enrol/self/locallib.php');
        $context = $manager->get_context();
        $bulkoperations = array();
        if (has_capability("enrol/self:manage", $context)) {
            $bulkoperations['editselectedusers'] = new enrol_self_editselectedusers_operation($manager, $this);
        }
        if (has_capability("enrol/self:unenrol", $context)) {
            $bulkoperations['deleteselectedusers'] = new enrol_self_deleteselectedusers_operation($manager, $this);
        }
        return $bulkoperations;
    }

    public function allow_manage(stdClass $instance) {
        // Simply making this function return true will render the edit enrolment action in the participants list if the user has the 'enrol/pluginname:manage' capability.
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        // Simply making this function return true will render the unenrolment action in the participants list if the user has the 'enrol/pluginname:unenrol' capability.
        return true;
    }

}

/**
 * Display the list Teachers in the course menu.
 *
 * @param settings_navigation $navigation The settings navigation object
 * @param stdClass $course The course
 * @param context $context Course context
 */
function enrol_teacherskey_extend_navigation_course($navigation, $course, $context) {
    if(($context->contextlevel === 50) &&
        has_capability('gradereport/grader:view', $context)) {
        $url = new moodle_url('/enrol/teacherskey/listteachers.php', ['courseid' => $course->id]);
        $navigation->add(get_string('teacherslist', 'enrol_teacherskey'), $url, navigation_node::TYPE_CONTAINER, null, 'enrol_teacherskey');
    }
}

function enrol_teacherskey_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course){
    global $USER;
    if (\tool_certificate\permission::can_view_list($user->id)) {
        if ($USER->id == $user->id) {
            $link = get_string('teacherslist', 'enrol_teacherskey');
        } else {
            $link = get_string('teacherslist', 'enrol_teacherskey');
        }
        $url = new moodle_url('/enrol/teacherskey/listteachers_student.php', $iscurrentuser ? ['userid' => $user->id] : [] );
        $node = new core_user\output\myprofile\node('miscellaneous', 'enrolteacherskey', $link, null, $url);
        $tree->add_node($node);
    }
}

