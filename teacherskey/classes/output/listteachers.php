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
namespace enrol_teacherskey\output;

defined('MOODLE_INTERNAL') || die;

global $CFG;

class listteachers extends \table_sql{

    public function __construct($uniqueid){
        parent::__construct($uniqueid);

        $columns = array('fiostudent', 'coursename', 'fio');
        $this->define_columns($columns);

        $headers = array(get_string('fiostudent', 'enrol_teacherskey'),
            get_string('coursename', 'enrol_teacherskey'),
            get_string('fio', 'enrol_teacherskey'));
        $this->define_headers($headers);
        $this->pageable(true);
    }

    function col_fiostudent($values){
        // If the data is being downloaded than we don't want to show HTML.

        if ($this->is_downloading()) {
            return $values->fiostudent;
        } else {
            return '<a href="/moodle/user/view.php?id='.$values->id.'">'.$values->fiostudent.'</a>';
        }
    }

}