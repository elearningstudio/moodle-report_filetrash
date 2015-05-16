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
 * Libs, public API.
 *
 * NOTE: page type not included because there can not be any blocks in popups
 *
 * @package    report_filetrash
 * @copyright  2013 Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

class report_filetrash {

    public $dbfiles;
    public $directoryfiles;
    public $backupfiles;
    public $orphanedfiles;
    
    public function __construct() {

        $this->dbfiles = $this->get_current_files();
        $this->directoryfiles = $this->get_directory_files();
        $this->backupfiles = $this->get_backup_files();
        $this->orphanedfiles = $this->get_orphaned_files();
    }

    /**
     * get_size_format
     * 
     * Add a size format (e.g. bytes, MB, GB, etc) as suffix
     * 
     * @param type $bytes
     * @return string
     */
    public static function get_size_format($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) .
                    get_string('gb', 'report_filetrash');
        } else if ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) .
                    get_string('mb', 'report_filetrash');
        } else if ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) .
                    get_string('kb', 'report_filetrash');
        } else if ($bytes > 1) {
            $bytes = $bytes . get_string('bytes', 'report_filetrash');
        } else if ($bytes == 1) {
            $bytes = $bytes . get_string('byte', 'report_filetrash');
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * get_files
     * 
     * Get a list of files within a specific directory and all it's sub directories
     * 
     * @param string $directory
     * @return array $filenames
     */
    public static function get_files($directory) {
        $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST);
        $filenames = array();
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $file = (string) $fileinfo->getFilename();
                $path = (string) $fileinfo->getPath();
                $bytes = (string) $fileinfo->getSize();
                $size = self::get_size_format($bytes);
                $pathfile = glob($path . '/' . $file);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = (string)finfo_file($finfo, $pathfile[0]);
                $filenames[$file] = array(
                    'filename' => $file,
                    'filepath' => $path,
                    'filesize' => $size,
                    'extension' => $mime);
            }
        }
        return $filenames;
    }

    /**
     * get_directory_files
     * 
     * Get a list of files from the moodledata directory
     * 
     * @return array $files
     */
    public function get_directory_files() {
        global $CFG;

        $directory = $CFG->dataroot . '/filedir';
        $files = self::get_files($directory);
        return $files;
    }

    /**
     * get_backup_files
     * 
     * Get a list of files from the backup directory if defined
     * 
     * @return array $files
     */
    public function get_backup_files() {
        global $CFG;

        $config = get_config('backup');
        $directory = $config->backup_auto_destination;

        if (!empty($directory)) {
            $files = self::get_files($directory);
            return $files;
        } else {
            return array();
        }
    }

    /**
     * get_current_files
     * 
     * Get a list of files referenced in the files database table
     * 
     * @return array $files
     */
    public function get_current_files() {
        global $DB;

        $dbfiles = $DB->get_records_sql('SELECT DISTINCT contenthash from {files}');
        $files = array();
        foreach ($dbfiles as $file) {
            $filename = $file->contenthash;
            $files[$filename] = array('filename' => $filename);
        }
        return $files;
    }

    /**
     * get_orphaned_files
     * 
     * Get a list of orpaned files by finding the difference of files in the directory
     * vs files referenced in the database
     * 
     * @return array $indexedorphans
     */
    public function get_orphaned_files() {
        $indexedorphans = array();
        $currentfiles = array_merge($this->directoryfiles, $this->backupfiles);
        $orphans = array_diff_key($currentfiles, $this->dbfiles);
        $i = 0;
        foreach ($orphans as $orphan) {
            $i++;
            if ($orphan['filename'] == 'warning.txt') {
                continue;
            }
            $indexedorphans[$i] = array(
                'filename' => $orphan['filename'],
                'filepath' => $orphan['filepath'],
                'filesize' => $orphan['filesize'],
                'extension' => $orphan['extension'],
                'filekey' => $i);
        }
        return $indexedorphans;
    }

}
