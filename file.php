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

require('../../config.php');

require_once($CFG->dirroot . '/report/filetrash/lib.php');

$id = optional_param('id', $SITE->id, PARAM_INT);
$filename = optional_param('filename', '0', PARAM_TEXT);
$filepath = optional_param('filepath', '0', PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_login($course);

$path = $filepath . '/' . $filename;

$parts = explode('/', pathinfo($path, PATHINFO_DIRNAME));


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
readfile($path);