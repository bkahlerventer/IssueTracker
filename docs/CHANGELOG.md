# 4.0.4 Day Mon 00 2004 Edwin Robertson <tm@issue-tracker.com>
* SF Bug #906147 Selecting
* SF Bug #886448 Group creation problem
* SF Bug #977513 Login clears query string
* SF Bug #908007 Syntax in const.php
* SF Bug #906148 Users cannot create report
* SF Bug #964830 affected_rows function in DBI
* SF Bug #952911 Users not removed from notifications, incorrect information
* Rewrote code that handles downloads to work correctly with Windows
* Updated version of Smarty

# 4.0.3 Wed Feb 25 2004 Edwin Robertson <tm@issue-tracker.com>
* SF Bug #898298 4.02 (Main) Group Standing obeying preferences?
* SF Bug #898217 4.02 not allowing user to create report
* SF Bug #902045 Inactive groups still availible as "Move to" itmes
* SF Bug #889307 Custom statuses don't show up in issue list
  Fix has existed for this since 4.0 but I kept forgetting to put it in :(
* SF Bug #891433 Handling spaces in module names in crumbs bar
* Corrected issue with notifications going to all users that have an
  entry in the group_users table
* Added missing public_address function to groups module
* Corrected multiple issues in the parser involving file attachments
* Removed port from auto definition of _URL_ constant due to it appearing
  twice in most cases, if the port is not automatically added you will
  need to define _URL_ manually

# 4.0.2 Fri Jan 30 2004 Edwin Robertson <tm@issue-tracker.com>
* SF Bug #823233 Column headers toggle more than sort only 
* SF Bug #841789 mailto link not right 
* SF Bug #848574 Unable to make preference changes for any user but 'admin' 
* SF Bug #869676 When editing an event - nothing shows up in text area 
* SF Bug #870989 "Severity" in group preferences does nothing 
* SF Bug #870996 issue_notify doesn't look at notifications table right 
* SF Bug #871030 Javascript session announcements are confusing 
* SF Bug #871375 redirect() function does not work with safari 
* SF Bug #873459 Registered Status 
* SF Bug #874790 file download url error 
* SF Bug #874796 error deleting uploaded issue file 
* SF Bug #874807 bad redirect when Issue File deleted
* SF Bug #876665 Dates are reported in wrong time zone
* SF Bug #877962 Issue listing Preferences are not remembered
* SF Bug #877997 emailing issue does not work
* SF Bug #878185 Date formats hard coded in modules
* SF Support #832879 "No such file" error on CSSs
* Display email address of requestor for issues opened through email parser


# 4.0.1 Thu Oct 09 2003 Edwin Robertson <tm@issue-tracker.com> 
* Fixed defining of _URL_ when SCRIPT_URI is not given by server - Ryan G
* Added check for IIS to const.php so that _URL_ will be assigned correctly
* Corrected bug in issue searching that allowed users to search all groups

# 4.0 Wed Oct 08 2003 Edwin Robertson <tm@issue-tracker.com>
* theme system rewritten
* menu structure rewritten
* categories/statues/projects modules merged into admin module
* css rewrite and addition of css generation functions
* conversion of all html to Smarty template engine
* combined some shorter module action scripts
* removed old unused files
* fixed a multiple bugs in file uploads/downloads
* support for modules that don't require authentication
* rewrote breadcrumb generation
* removed requirement for register_globals to be turned on
* addition of issue_log for logging of status, severity, etc. changes
* added link to main navigation to jump to new issue page
* users can now choose what fields are shown in group issue listing
* added ability to upload multiple files at once
* added ability to upload file at issue creation
* all announcements now expire after 1 week
* added ability for admins to remove files from the system
* added support for mysql to database layer
* included classes for database sessions
* complete rewrite to email parsing system
* email parser can now parse file attachments
* addition of group standing panel to main page
* addition of group types, issues or hours based and enforcement
* addition of internal statuses and severities
* complete rewrite to reports module
* addition of graphing support to reports module
* added ability to save report options
* removed requirement for register_globals to be turned on
* complete rewrite to permissions system
* addition of sub-group support (grouping of groups)
* ability to move issues between groups
* ability to copy issue details to create new issue
* addition of client timezone support
* added preferences for date format
* added support for wrapping of event text
* addition of issue due dates (currently not enforced)
* new module help system
* completely rewritten data caching for certain tables
* removed logging errors to database, may return at later time
* rewrite to error handling code
* addition of simple debugger
* moved all administration menu items to admin panel
* UI cleanup
* massive code cleanup
* much, much more that I never wrote down :)


