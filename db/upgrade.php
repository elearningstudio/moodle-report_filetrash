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
 * report_filetrash upgrade.
 *
 * @package   report_filetrash
 * @copyright 2013 Barry Oosthuizen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade script
 *
 * @param int $oldversion
 * @return boolean
 */
function xmldb_report_filetrash_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2015060100) {
        
        $sql = 'TRUNCATE {report_filetrash}';
        $DB->execute($sql);

        // Define field deleted to be dropped from report_filetrash.
        $table = new xmldb_table('report_filetrash');
        $field = new xmldb_field('deleted');

        // Conditionally launch drop field deleted.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Filetrash savepoint reached.
        upgrade_plugin_savepoint(true, 2015060100, 'report', 'filetrash');
    }
    return true; // The upgrade is complete.
}
