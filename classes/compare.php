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
defined('MOODLE_INTERNAL') || die;

/**
 * Class used for finding orphaned files
 * @package    report_filetrash
 * @copyright  2013 onwards, Barry Oosthuizen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_filetrash_compare {

    /** Unique DB files by contenthash
     * @var array
     */
    public $dbfiles;
    /**
     * Unique directory files by filename
     * @var array
     */
    public $directoryfiles;
    /** Unique backup files by filename
     * @var array
     */
    public $backupfiles;
    /** Array of orphaned files
     * @var array
     */
    public $orphanedfiles;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->dbfiles = $this->get_current_files();
        $this->directoryfiles = $this->get_directory_files();
        $this->backupfiles = $this->get_backup_files();
        $this->orphanedfiles = $this->get_orphaned_files();
    }

    /**
     * Add a size format (e.g. bytes, MB, GB, etc) as suffix
     *
     * @param int $bytes
     * @return string
     */
    private function get_size_format($bytes) {
        if ($bytes >= 1073741824) {
            $bytes =  get_string('gb', 'report_filetrash', number_format($bytes / 1073741824, 2));
        } else if ($bytes >= 1048576) {
            $bytes = get_string('mb', 'report_filetrash', number_format($bytes / 1048576, 2));
        } else if ($bytes >= 1024) {
            $bytes = get_string('kb', 'report_filetrash', number_format($bytes / 1024, 2));
        } else if ($bytes > 1) {
            $bytes = get_string('bytes', 'report_filetrash', $bytes);
        } else if ($bytes == 1) {
            $bytes = get_string('byte', 'report_filetrash', $bytes);
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * Get a list of files within a specific directory and all it's sub directories
     *
     * @param string $directory
     * @return array $filenames
     */
    private function get_files($directory) {
        $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST);
        $filenames = array();
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $file = (string) $fileinfo->getFilename();
                $path = (string) $fileinfo->getPath();
                $bytes = (string) $fileinfo->getSize();
                $size = $this->get_size_format($bytes);
                $mime = $this->get_mime_type($file, $path);

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
     * Get the mime type for this file
     * 
     * @param string $file
     * @param string $path
     */
    private function get_mime_type($file, $path) {
        $showmimetype = get_config('report_filetrash', 'showfileinfo');

        if ($showmimetype) {
            $pathfile = glob($path . '/' . $file);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = (string)finfo_file($finfo, $pathfile[0]);
            return $mime;
        }
        return '';
    }

    /**
     * Get a list of files from the moodledata directory
     *
     * @return array $files
     */
    private function get_directory_files() {
        global $CFG;

        $directory = $CFG->dataroot . '/filedir';
        $files = $this->get_files($directory);
        return $files;
    }

    /**
     * Get a list of files from the backup directory if defined
     *
     * @return array $files
     */
    private function get_backup_files() {
        $config = get_config('backup');
        $directory = $config->backup_auto_destination;

        if (!empty($directory)) {
            $files = $this->get_files($directory);
            return $files;
        } else {
            return array();
        }
    }

    /**
     * Get a list of files referenced in the files database table
     *
     * @return array $files
     */
    private function get_current_files() {
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
     * Get a list of orpaned files by finding the difference of files in the directory
     * vs files referenced in the database
     *
     * @return array $indexedorphans
     */
    private function get_orphaned_files() {
        $indexedorphans = array();

        $ignoreautomatedbackupfolder = get_config('report_filetrash', 'ignoreautomatedbackupfolder');
        if (empty($ignoreautomatedbackupfolder)) {
            $currentfiles = array_merge($this->directoryfiles, $this->backupfiles);
        } else {
            $currentfiles = $this->directoryfiles;
        }

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

    /**
     * Read file chunk by chunk
     *
     * @param string $filename
     * @param boolean $retbytes
     * @return boolean
     */
    public static function readfile_chunked($filename, $retbytes = true) {
        $chunksize = 1 * (1024 * 1024);
        $buffer = '';
        $cnt = 0;
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            return $cnt;
        }
        return $status;
    }
}
