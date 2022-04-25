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
 * ${PLUGINNAME} file description here.
 *
 * @package    ${PLUGINNAME}
 * @copyright  2022 alex <${USEREMAIL}>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/enrol/locallib.php');

class update_from extends moodleform
{
    protected $id_change_fio;

    private function get_fio_default_value($id){
        global $DB;
        return $DB->get_record('teacherskey_data', array('id' => $id));
    }

    public function definition()
    {
        global $USER, $CFG, $PAGE;
        $myform = $this->_form;
        $id_change_fio = $this->_customdata;

        $row = $this->get_fio_default_value($id_change_fio);
        $attributes = array('size' => '30', 'placeholder' => get_string('fio', 'enrol_teacherskey'));
        $myform->addElement('text', 'fio', get_string('labelfio', 'enrol_teacherskey'), $attributes);
        $myform->setType('fio', PARAM_NOTAGS);
        $myform->setDefault('fio', $row->fio);

        $this->add_action_buttons();

        $myform->addElement('hidden', 'id');
        $myform->setType('id', PARAM_INT);
        $myform->setDefault('id', $id_change_fio);

        $myform->addElement('hidden', 'courseid');
        $myform->setType('courseid', PARAM_INT);
        $myform->setDefault('courseid', $row->courseid);

    }

    function validate($data, $files)
    {
        return array();
    }
}