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
require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/filetrash/lib.php');
require_once($CFG->dirroot . '/report/filetrash/form.php');

$id = optional_param('id', $SITE->id, PARAM_INT);
$cacheid = optional_param('cacheid', $SITE->id, PARAM_INT);
$confirmdelete = optional_param('confirmdelete', null, PARAM_TEXT);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);

$context = context_course::instance($course->id);
require_capability('report/filetrash:view', $context);
$filetrash = get_string('pluginname', 'report_filetrash');

session_get_instance()->write_close();

$url = new moodle_url('/report/filetrash/index.php', array('id' => $course->id));

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($filetrash);

admin_externalpage_setup('reportfiletrash', '', null, '', array('pagelayout' => 'report'));
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'report_filetrash'));

$report = new report_filetrash();

$customdata = array('orphanedfiles' => $report->orphanedfiles);
$form = new report_filetrash_form(null, $customdata);

if ($confirmdelete == 'yes') {
    $form->process($cacheid);
    echo html_writer::tag('p', get_string('deleted', 'report_filetrash'));
    $continueurl = new moodle_url('/report/filetrash/index.php', array('id' => $course->id));
    $link = html_writer::link($continueurl, get_string('continue'));
    echo html_writer::tag('p', $link);
} else if ($form->is_submitted()) {
    $data = $form->get_data();
    $cache = $form->store_options($data, $report->orphanedfiles);
    $filestodelete = unserialize($cache->filestodelete);
    $confirmurl = new moodle_url('/report/filetrash/index.php', array(
        'id' => $course->id,
        'confirmdelete' => 'yes',
        'cacheid' => $cache->id));
    echo html_writer::tag('p', get_string('confirm_delete', 'report_filetrash'));
    $i = 0;
    foreach ($filestodelete as $key => $file) {
        $i++;
        echo html_writer::tag('p', $i . '. ' . $file);

    }
    echo html_writer::link($confirmurl, get_string('delete'));

} else {
    $form->display();
}

echo $OUTPUT->footer();
