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
require_once($CFG->dirroot . '/report/filetrash/form.php');

$confirmdelete = optional_param('confirmdelete', null, PARAM_TEXT);

admin_externalpage_setup('report_filetrash', '', null, '', array('pagelayout'=>'report'));

$context = context_system::instance();
$PAGE->set_context($context);
require_login(null, false);
require_capability('report/filetrash:view', $context);
raise_memory_limit(MEMORY_HUGE);

if ($confirmdelete) {
    $deleteurl = new moodle_url('/report/filetrash/delete.php', array('confirmdelete' => $confirmdelete));
    redirect($deleteurl);
}

$filetrash = get_string('pluginname', 'report_filetrash');

$url = new moodle_url('/report/filetrash/index.php');

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($filetrash);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_filetrash'));

$report = new report_filetrash_compare();

$customdata = array('orphanedfiles' => $report->orphanedfiles);
$form = new report_filetrash_form(null, $customdata);

if ($form->is_submitted()) {
    $data = $form->get_data();
    $cache = $form->store_options($data, $report->orphanedfiles);
    $filestodelete = unserialize($cache->filestodelete);
    $confirmurl = new moodle_url('/report/filetrash/index.php', array(
        'confirmdelete' => true,
        'cacheid' => $cache->id));
    echo html_writer::tag('p', get_string('confirm_delete', 'report_filetrash'));
    $i = 0;
    foreach ($filestodelete as $key => $file) {
        if ($file == '/') {
            continue;
        }
        $i++;
        echo html_writer::tag('p', $i . '. ' . $file);
    }
    echo html_writer::link($confirmurl, get_string('delete'));

} else {
    $form->display();
    $PAGE->requires->js_init_call('M.report_filetrash.init');
}

echo $OUTPUT->footer();
