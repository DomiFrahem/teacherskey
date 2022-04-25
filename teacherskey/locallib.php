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

    public function definition()
    {

        global $CFG, $PAGE;

        $myform = $this->_form;
        $instance = $this->_customdata;
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/enrol/teacherskey/js/teacherskey.js'));

        $attributes = array('size' => '30', 'placeholder' => get_string('fio', 'enrol_teacherskey'));
        $myform->addElement('text', 'fio', get_string('labelfio', 'enrol_teacherskey'), $attributes);

        $myform->addElement('checkbox', 'truetechers', get_string('truetechers', 'enrol_teacherskey'), null, array('onchange' => 'change_readonly(this)'));

        $myform->addElement('hidden', 'id');
        $myform->setType('id', PARAM_INT);
        $myform->setDefault('id', $instance->courseid);

        $this->add_action_buttons();

    }

    function validate($data, $files){
        return array();
    }
}