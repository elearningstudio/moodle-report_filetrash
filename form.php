<?php
// This file is part of the File Trash report by Barry Oosthuizen - http://elearningstudio.co.uk
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
 * report_filetrash form definition.
 *
 * @package   report_filetrash
 * @copyright 2013 Barry Oosthuizen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

/**
 * The form for editing the group settings.
 *
 * @copyright 2013 Barry Oosthuizen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_filetrash_form extends moodleform {

    /** 
     * Form definition
     */
    public function definition() {

        $mform = & $this->_form;
        $orphanedfiles = $this->_customdata['orphanedfiles'];
        $filecount = count($orphanedfiles);
        $directory = html_writer::span(get_string('directory', 'report_filetrash'), 'bold trashheader');
        $name = html_writer::span(get_string('filename', 'report_filetrash'), 'bold trashheader');
        $size = html_writer::span(get_string('filesize', 'report_filetrash'), 'bold trashheader');
        $extensionheader = html_writer::span(get_string('extension', 'report_filetrash'), 'bold trashheader');

        if ($filecount > 0) {
            $i = 0;
            $mform->addElement('checkbox', 'selectall', get_string('selectall', 'report_filetrash'));
            foreach ($orphanedfiles as $file) {
                $i++;
                $filepath = $file['filepath'];
                $filename = $file['filename'];
                $filekey = $file['filekey'];
                $filesize = $file['filesize'];
                $extension = $file['extension'];

                $link = new moodle_url('/report/filetrash/file.php',
                        array('filepath' => $filepath, 'filename' => $filename));
                $filelink = html_writer::link($link, $filename);
                $header = html_writer::div($directory . $filepath);
                $body = html_writer::div($name . $filelink);
                if (empty($extension)) {
                    $extensiondetails = '';
                } else {
                    $extensiondetails = html_writer::div($extensionheader . $extension);
                }
                $footer = html_writer::div($size . $filesize);
                $filedetails = html_writer::div($header . $body . $extensiondetails . $footer, 'filetrashdetails');

                $mform->addElement('checkbox', 'orphan_' . $filekey, $i . '. ', $filedetails);
            }

            $mform->addElement('submit', 'submit', get_string('delete'), 'submit', null);
        } else {
            $mform->addElement('static', 'nofiles', '', get_string('nofiles', 'report_filetrash'));
        }
    }

    /**
     * store_options
     * 
     * Store selected options (files to delete) in the database
     * 
     * @param object $data
     * @param array $indexedfiles
     * @return object $success
     */
    public function store_options($data, $indexedfiles) {
        global $DB, $USER;

        $markedfiles = get_object_vars($data);
        $filestodelete = array();

        foreach ($markedfiles as $file => $todelete) {
            if ($file !== 'submit' && $file !== 'selectall') {
                $filestodelete[] = substr($file, 7);
}
        }
        $files = array();

        foreach ($filestodelete as $key => $file) {
            if (empty($file)) {
                unset($filestodelete[$key]);
                continue;
            }
            $key = $indexedfiles[$file];
            $filename = $key['filename'];
            $filepath = $key['filepath'];
            $path = $filepath . '/' . $filename;
            $files[] = $path;
        }

        $data = new stdClass();
        $data->sessionid = sesskey();
        $data->userid = $USER->id;
        $serializedfiles = serialize($files);
        $data->filestodelete = $serializedfiles;
        $id = $DB->insert_record('report_filetrash', $data);
        $success = new stdClass();
        $success->id = $id;
        $success->filestodelete = $serializedfiles;
        return $success;
    }
}
