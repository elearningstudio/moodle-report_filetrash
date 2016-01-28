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
 * Links and settings
 *
 * This file contains links and settings used by report_filetrash
 *
 * @package    report_filetrash
 * @copyright  2013 Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $url = $CFG->wwwroot . '/report/filetrash/index.php';
    $ADMIN->add('reports', new admin_externalpage('report_filetrash', get_string('pluginname', 'report_filetrash'), $url));

    $settings->add(new admin_setting_configcheckbox('report_filetrash/showfileinfo',
            get_string('showfileinfo', 'report_filetrash'), get_string('showfileinfo_desc', 'report_filetrash'), '0'));

    $settings->add(new admin_setting_configcheckbox('report_filetrash/ignoreautomatedbackupfolder',
        get_string('ignoreautomatedbackupfolder', 'report_filetrash'), get_string('ignoreautomatedbackupfolder_desc', 'report_filetrash'), '0'));
}
