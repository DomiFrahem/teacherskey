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
 * @copyright  2022 alex sidorov <alex.sidorof@ya.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once("$CFG->libdir/tablelib.php");
//echo "$CFG->libdir/tablelib.php";

$userid = optional_param('userid', 0, PARAM_INT);
// Requires a login.
require_login();

$user = \core_user::get_user($userid ?: $USER->id, '*', MUST_EXIST);

$courseid = $_GET['courseid'];

$PAGE->set_url(new moodle_url('/enrol/teacherskey/listteachers.php'));
$PAGE->set_context(\context_course::instance($courseid));
$PAGE->set_heading(get_string('teacherslist', 'enrol_teacherskey'));
$PAGE->set_title(get_string('teacherslist', 'enrol_teacherskey'));
$PAGE->set_pagelayout('standard');
//
//echo breadcrumbs
$course = get_course($courseid);
$PAGE->navbar->add(get_string('courses'), new moodle_url('/course/index.php'));
$PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('teacherslist', 'enrol_teacherskey'), new moodle_url('/enrol/teacherskey/listteachers.php', ['courseid' => $courseid]));
echo $OUTPUT->header(get_string('teacherslist', 'enrol_teacherskey'));
echo $OUTPUT->heading(get_string('teacherslist', 'enrol_teacherskey'));



echo html_writer::div(get_string('teacherskeydescription', 'enrol_teacherskey'));

$listteachers = new \enrol_teacherskey\output\listteachers($USER, $course);

$table = new html_table();
$table->head = (array)$listteachers->header;
$table->data = (array)$listteachers->get_data_array();

echo html_writer::table($table);

//echo "<pre>";
//var_dump( $listteachers->get_data_array());
//echo "</pre>";
//echo $OUTPUT->render_from_template('enrol_teacherskey/listteachers', $data->export_for_template());


echo $OUTPUT->footer();
