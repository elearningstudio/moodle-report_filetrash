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
 * File containing file deletion class
 *
 * @package   report_filetrash
 * @copyright 2013 Barry Oosthuizen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Class used to delete selected orphaned files
 *
 * @copyright 2013 Barry Oosthuizen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_filetrash_files {

    /**
     * Delete selected files form their directories
     *
     * @return array $errors list of files that failed to be deleted
     */
    public function delete() {
        global $DB, $USER;

        $sesskey = sesskey();
        
        $params = array(
            'userid' => $USER->id,
            'sessionid' => $sesskey);

        $cachedrecord = $DB->get_record('report_filetrash', $params);

        $filestodelete = unserialize($cachedrecord->filestodelete);
        $errors = array();
        foreach ($filestodelete as $key => $path) {
            if ($path === '/') {
                continue;
            }
            $deleted = unlink($path);
            if (!$deleted) {
                $errors[] = $path;
            }
        }
        $DB->delete_records('report_filetrash', $params);
        return $errors;
    }

}
