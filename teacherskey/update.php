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
 *  enrol_teacherskey file description here.
 *
 * @package    enrol_teacherskey
 * @copyright  2022 alex sidorov <alex.sidorof@ya.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once('updatelib.php');


require_login();
global $USER, $PAGE, $DB;
$id_change_fio = $_GET['id'];

$context= \context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header(get_string('teacherslist', 'enrol_teacherskey'));


$form = new update_from(null, $id_change_fio);
if ($form->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect($CFG->wwwroot . "/enrol/teacherskey/listteachers_student.php?userid=".$USER->id);
} else if ($fromform = $form->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    $update = new stdClass();
    $update->id = $id_change_fio;
    $update->fio = $fromform->fio;

    if ($DB->update_record('teacherskey_data', $fromform)){
        \core\notification::success(get_string('good_update', 'enrol_teacherskey'));
        redirect($CFG->wwwroot . "/enrol/teacherskey/listteachers_student.php?userid=".$USER->id);
    }else{
        \core\notification::error(get_string('error_update', 'enrol_teacherskey'));
        redirect($CFG->wwwroot . "/enrol/teacherskey/listteachers_student.php?userid=".$USER->id);
    }

} else {
    $form->display();
}

echo $OUTPUT->footer();
