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
 * Displays live view of recent logs
 *
 * This file generates live view of recent logs.
 *
 * @package    report_filetrash
 * @copyright  2013 Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$confirmdelete = optional_param('confirmdelete', false, PARAM_BOOL);
$context = context_system::instance();
$PAGE->set_context($context);
require_login(null, false);
require_capability('report/filetrash:view', $context);
raise_memory_limit(MEMORY_HUGE);

$filetrash = get_string('pluginname', 'report_filetrash');

$continueurl = new moodle_url('/report/filetrash/index.php');

if ($confirmdelete) {
    $url = new moodle_url('/report/filetrash/delete.php');

    $PAGE->set_url($url);
    $PAGE->set_pagelayout('report');
    $PAGE->set_title($filetrash);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('pluginname', 'report_filetrash'));
    $files = new report_filetrash_files();
    $errors = $files->delete();
    if (count($errors) > 0) {
        echo html_writer::tag('p', get_string('deletedfailed', 'report_filetrash'));
        foreach ($errors as $key => $error) {
            echo html_writer::tag('p', $error);
        }
    } else {
        echo html_writer::tag('p', get_string('deleted', 'report_filetrash'));
    }

    $link = html_writer::link($continueurl, get_string('continue'));
    echo html_writer::tag('p', $link);
} else {
    redirect($continueurl);
}

echo $OUTPUT->footer();
