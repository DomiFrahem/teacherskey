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
 * certificateelement_teachersname file description here.
 *
 * @package    certificateelement_teachersname
 * @copyright  2022 alex
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace certificateelement_teachersname;

class element extends \tool_certificate\element {

    public function render_form_elements($mform){
        global $USER, $DB;
        parent::render_form_elements($mform);
    }

    protected function get_fio($issue, $user){
        global $DB;
        if ($record = $DB->get_record('teacherskey_data', array('userid' => $user->id, 'courseid' => $issue->courseid))){
            return  $record->fio;
        }else {
            return $user->profile['fio'];
        }

    }
    /**
     * Handles saving the form elements created by this element.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data or partial data to be updated (i.e. name, posx, etc.)
     */
    public function save_form_data(\stdClass $data) {
        $data->data = 'teacherskey';
        parent::save_form_data($data);

    }


    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass $issue the issue we are rendering
     */
    public function render($pdf, $preview, $user, $issue) {
        if ($preview) {
            $value = $user->profile['fio'];
        }else {
            $value = $this->get_fio($issue, $user);
        }
        $text = format_text($value, FORMAT_HTML, ['context' => \context_system::instance()]);
        \tool_certificate\element_helper::render_content($pdf, $this, $text);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        $text = format_text($this->get_data(), FORMAT_HTML, ['context' => \context_system::instance()]);
        return \tool_certificate\element_helper::render_html_content($this, $text);
    }
//
//    public function prepare_data_for_form() {
//        $record = parent::prepare_data_for_form();
//        if (!empty($this->get_data())) {
//            $dateinfo = json_decode($this->get_data());
//            $record->teachersname = $dateinfo->teachersnamel;
//        }
//        return $record;
//    }
}