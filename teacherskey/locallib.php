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

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/enrol/locallib.php');

class teacherskey_from extends moodleform {
    protected $instance;


    public function get_fio_default_value($userid, $courseid) {
        global $DB;
        if ($DB->record_exists('teacherskey_data', array('userid' => $userid, 'courseid' => $courseid))) {
            return $DB->get_record('teacherskey_data', array('userid' => $userid, 'courseid' => $courseid))->fio;
        }else return $DB->get_record('user_info_data', array('userid' => $userid, 'fieldid' => 3))->data;
    }

    public function definition()
    {

        global $USER, $CFG, $PAGE;

        $myform = $this->_form;
        $instance = $this->_customdata;
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/enrol/teacherskey/js/teacherskey.js'));


      //

        $attributes = array('size' => '30', 'placeholder' => get_string('fio', 'enrol_teacherskey'));
        $myform->addElement('text', 'fio', get_string('labelfio', 'enrol_teacherskey'), $attributes);
        $myform->setType('fio', PARAM_NOTAGS);
        $myform->setDefault('fio', $this->get_fio_default_value($USER->id, $instance->courseid));

        $myform->addElement('checkbox', 'truetechers', get_string('truetechers', 'enrol_teacherskey'), null);
        $myform->setDefault('truetechers', false);

        $myform->disabledIf('fio', 'truetechers', 'checked');
        $this->add_action_buttons();

        $myform->addElement('hidden', 'id');
        $myform->setType('id', PARAM_INT);
        $myform->setDefault('id', $instance->courseid);


    }

    function validate($data, $files){
        $errors  =  parent::validation($data, $files);

        if ($data['truetechers'] == false)
            $errors['errors_checkbox'] = get_string('enrol_teacherskey', 'errors_checkbox');

        return array();
    }
}