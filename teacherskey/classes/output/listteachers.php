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

//use stdClass;
//use stdClass;
//use templatable;
//use renderable;

defined('MOODLE_INTERNAL') || die;

global $CFG;
//require "$CFG->libdir/tablelib.php";
//use table_html;

class listteachers {
    protected $user;
    protected $course;
    protected $table;
    protected $role;
    public $data;
    public $header;

    public function __construct($user, $course) {
        $this->user = $user;
        $this->course = $course;
        $context = get_context_instance(CONTEXT_COURSE,  $course->id);
        $this->role = array_shift(get_user_roles($context, $user->id))->shortname;
        $this->data = $this->gdata();
        $this->header = $this->get_headers($this->data);
    }

    protected function gdata(){
        switch ($this->role){
            case 'student':
                return $this->get_data($this->course->id, $this->user->id);
                break;
            case 'guest':
                break;
            default:
                return $this->get_data($this->course->id);
        }
    }

    public function get_data_array(): array
    {
        $result = array();
        $index = 0;
        foreach($this->data as $key => $value){
            $index++;
            $result[] = array($index, $value->fiostudent, $value->fullname, $value->fio);
        }

        return $result;
    }

    protected function get_data($courseid, $userid = null){
        global $DB;

        if (is_null($userid)){
            $str_where_user = '';
        } else {
            $str_where_user = "mtd.userid = {$userid} and";
        }

        $sql = <<<EOF
            SELECT
                CONCAT(mu.lastname, ' ', mu.firstname, ' ', mu.middlename) as fiostudent,
                mc.fullname,
                mtd.fio
            from mdl_teacherskey_data mtd
            inner join mdl_course mc on mc.id = mtd.courseid
            inner join mdl_user mu on mu.id = mtd.userid
            where {$str_where_user} mtd.courseid = {$courseid};
        EOF;

        return $DB->get_records_sql($sql);
    }



    protected function get_headers($data)
    {
        $array = (array)$data;
        $headers = array();
        array_push($headers, '№');
        foreach (array_shift($array) as $key => $value){
            if (!in_array($key, array('fio', 'fiostudent'))) {
                array_push($headers, get_string($key));
            }
            else array_push($headers, get_string($key, 'enrol_teacherskey'));
        }
        return $headers;
    }
}