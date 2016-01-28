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
 * Lang strings.
 *
 * Language strings used by report_filetrash
 *
 * @package    report_filetrash
 * @copyright  2013 Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['filtetrash:view'] = 'View the file trash report';
$string['pluginname'] = 'File trash';
$string['doesnotexist'] = 'This file does not exist.';
$string['confirm_delete'] = 'Are you sure you want to delete the file(s) listed below?';
$string['deleted'] = 'The files you selected have been deleted successfully.';
$string['deletedfailed'] = 'There was a problem deleting the following files: ';
$string['nofiles'] = 'There are no orphaned files in the filedir directory of your moodledata folder';
$string['directory'] = 'Directory: ';
$string['extension'] = 'File type: ';
$string['filename']  = 'File name: ';
$string['filesize']  = 'File size: ';
$string['ignoreautomatedbackupfolder'] = 'Ignore files in automated backup folder';
$string['ignoreautomatedbackupfolder_desc'] = 'Check this if you have the automated backup system set to put files in a specified directory and wish to exclude them from this report';
$string['byte'] = '{$a} byte';
$string['bytes'] = '{$a} bytes';
$string['kb'] = '{$a} KB';
$string['mb'] = '{$a} MB';
$string['gb'] = '{$a} GB';
$string['tb'] = '{$a} TB';
$string['selectall'] = 'Select All';
$string['showfileinfo'] = 'Show file extensions in report?';
$string['showfileinfo_desc'] = 'This will slow down the report and may cause a timeout';
