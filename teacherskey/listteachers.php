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
require "$CFG->libdir/tablelib.php";

$userid = optional_param('userid', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$user = \core_user::get_user($userid ?: $USER->id, '*', MUST_EXIST);

if(isset($_GET['courseid'])){
    $courseid = $_GET['courseid'];
}else{
    redirect($CFG->wwwroot . "/my");
}

$context= \context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/enrol/teacherskey/listteachers.php'));

$table = new \enrol_teacherskey\output\listteachers('teacherskey');

$name_downloadfile = "listteachers-".date('Y-m-d');
$table->is_downloading($download, $name_downloadfile, 'listteachers');
//
//echo breadcrumbs
$course = get_course($courseid);
if (!$table->is_downloading()){
    $PAGE->set_pagelayout('standard');
    $PAGE->set_title(get_string('teacherslist', 'enrol_teacherskey'));
    $PAGE->set_heading(get_string('teacherslist', 'enrol_teacherskey'));
    $PAGE->navbar->add(get_string('courses'), new moodle_url('/course/index.php'));
    $PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', ['id' => $courseid]));
    $PAGE->navbar->add(get_string('teacherslist', 'enrol_teacherskey'), new moodle_url('/enrol/teacherskey/listteachers.php', ['courseid' => $courseid]));
    echo $OUTPUT->header(get_string('teacherslist', 'enrol_teacherskey'));
}




$fields = "mu.id, mtd.id mtdid, CONCAT(mu.lastname, ' ', mu.firstname, ' ', mu.middlename) as fiostudent, mc.fullname as coursename, mtd.fio";
$from = "{teacherskey_data} mtd
    inner join {course} mc on mc.id = mtd.courseid
    inner join {user} mu on mu.id = mtd.userid";
$where = 'mtd.courseid = :courseid';
$params = array('courseid' => $courseid);


$table->set_sql($fields, $from, $where, $params);
$table->define_baseurl(new moodle_url("/enrol/teacherskey/listteachers.php", ['courseid' => $courseid]));
$table->out(10, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
