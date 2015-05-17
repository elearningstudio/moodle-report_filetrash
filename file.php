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
 * File containing report_filetrash class
 * @package    report_filetrash
 * @copyright  2013 Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');

$filename = optional_param('filename', '0', PARAM_TEXT);
$filepath = optional_param('filepath', '0', PARAM_TEXT);

require_login();

$context = context_system::instance();
require_capability('report/filetrash:view', $context);

$path = $filepath . '/' . $filename;

if (!is_file($path)) {
    // File does not exist.
    echo get_string('doesnotexist', 'report_filetrash');
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));

header("Cache-Control: private");

header("Content-Disposition: attachment; filename=" . $filename);
report_filetrash_compare::readfile_chunked($path);
