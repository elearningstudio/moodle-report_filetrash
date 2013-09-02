Introduction
============
The filetrash utility is an admin report which lists all orhphaned files (files which are not referenced 
in the Moodle database) from both the Moodle data folder and the backup folder (if a separate backup
folder has been defined).

Required version of Moodle
==========================
This version works with Moodle version 2013050100 release 2.5

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/25/en/Installing_Moodle'.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    format relies on underlying core code that is out of my control.
 2. Put Moodle in 'Maintenance Mode' (docs.moodle.org/en/admin/setting/maintenancemode) so that there are no 
    users using it bar you as the administrator - if you have not already done so.
 3. Copy 'filetrash' to '/report/' if you have not already done so.
 4. Login as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
 5. Put Moodle out of Maintenance Mode.


Uninstallation
==============
1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
2. Go to Site administration > Reports > Manage reports and delete the filetrash report
3. In '/report/' remove the folder 'filetrash'.
4. Put Moodle out of Maintenance Mode.

Reporting Issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  The primary
release area is located on https://moodle.org/plugins/view.php?plugin=report_filetrash.  It is also essential that you are
operating the required version of Moodle as stated at the top - this is because the format relies on core functionality that
is out of its control.

When reporting an issue you can post in https://github.com/elearningstudio/moodle-report_filetrash/issues

It is essential that you provide as much information as possible, the critical information being the contents of the report's 
version.php file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

Version Information
===================
2013081400 - Version 0.9 - Betaa - Do not install on production sites. Once I have received enough positive feedback I will change
this to Stable after fixing any issues.
  1.  First version.
