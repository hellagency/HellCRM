<?php
/////////////////////////////////////////////////////////////////////
///                                                                //
// backupDB() - MySQL database backup utility                      //
//                                                                 //
// You should configure at least ADMIN_EMAIL below.                //
//                                                                 //
// See backupDB.txt for more information.                          //
//                                                                ///
/////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////
///////////////////         CONFIGURATION         ///////////////////
/////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////
//   You MUST modify these values:                                 //
/////////////////////////////////////////////////////////////////////

// If any MySQL table errors occur, a notice will be sent here
define('ADMIN_EMAIL', 'quentin.hello@ymail.com,maria.hello@laposte.net'); // eg: admin@example.com


/////////////////////////////////////////////////////////////////////
//   You SHOULD modify these values:                               //
/////////////////////////////////////////////////////////////////////

// If DB_HOST, DB_USER and/or DB_PASS are undefined or empty,
// you will be prompted to enter them each time the script runs
define('DB_HOST', (isset($_REQUEST['DB_HOST']) ? $_REQUEST['DB_HOST'] : 'mysql5-3.perso')); // usually 'localhost'
define('DB_USER', (isset($_REQUEST['DB_USER']) ? $_REQUEST['DB_USER'] : 'abddzeqw_sql'));  // MySQL username
define('DB_PASS', (isset($_REQUEST['DB_PASS']) ? $_REQUEST['DB_PASS'] : 'nBNyWvvu'));  // MySQL password

// Only define DB_NAME if you want to restrict to ONLY this
// database, otherwise all accessible databases will be backed up
if (!empty($_REQUEST['onlyDB'])) {
	define('DB_NAME', $_REQUEST['onlyDB']);
} else {
	define('DB_NAME', 'abddzeqw_sql');
}



/////////////////////////////////////////////////////////////////////
//   You MAY modify these values (defaults should be fine too):    //
/////////////////////////////////////////////////////////////////////

define('BACKTICKCHAR',             '`');
define('QUOTECHAR',                '\'');
define('LINE_TERMINATOR',          "\n");    // \n = UNIX; \r\n = Windows; \r = Mac
define('BUFFER_SIZE',              32768);   // in bytes
define('TABLES_PER_COL',           30);      // number of table names per column in partial table backup selection screen
define('STATS_INTERVAL',           500);     // number of records processed between each DHTML stats refresh
define('MYSQL_RECONNECT_INTERVAL', 100000);  // disconnect and reconnect to MySQL every <interval> rows, to prevent timeouts
define('OUTPUT_COMPRESSION_TYPE',  'gzip'); // 'bzip2', 'gzip', 'none'; best at "bzip2" for mysqldump-based backups, "gzip" for PHP-based backups
define('OUTPUT_COMPRESSION_LEVEL', 6);       // bzip2/gzip compression level (1=fastest,9=best)

$DHTMLenabled       = true;  // set $DHTMLenabled = FALSE to prevent JavaScript errors in incompatible browsers
                             // set $DHTMLenabled = TRUE to get the nice DHTML display in recent browsers
$dbNameInCreate     = true;  // if true: "CREATE TABLE `database`.`table`", if false: "CREATE TABLE `table`"

$CreateIfNotExists  = false; // if true: "CREATE TABLE IF NOT EXISTS `database`.`table`", if false: "CREATE TABLE `database`.`table`"

$ReplaceInto        = false; // if true: "REPLACE INTO ", if false: "INSERT INTO "

$HexBLOBs           = true;  // if true: blobs get data dumped as hex string; if false: blobs get data dumped as escaped binary string

$SuppressHTMLoutput = (@$_REQUEST['nohtml'] ? true : false); // disable all output for running as a cron job

$Disable_mysqldump  = true;  // LEAVE THIS AS "false"! If true, avoid use of "mysqldump" program to export databases which is *MUCH* *MUCH* faster than doing it row-by-row in PHP. Highly recommended to leave this at "false" (i.e. use mysqldump)

$backuptimestamp    = '.'.date('Y-m-d'); // timestamp
if (!empty($_REQUEST['onlyDB'])) {
	$backuptimestamp = '.'.$_REQUEST['onlyDB'].$backuptimestamp;
}
//$backuptimestamp    = ''; // no timestamp
$backupabsolutepath = dirname(__FILE__).'/'; // make sure to include trailing slash
$fileextension = ((OUTPUT_COMPRESSION_TYPE == 'bzip2') ? '.bz2' : ((OUTPUT_COMPRESSION_TYPE == 'gzip') ? '.gz' : ''));
$fullbackupfilename = 'db_backup'.$backuptimestamp.'.sql'.$fileextension;
$partbackupfilename = 'db_backup_partial'.$backuptimestamp.'.sql'.$fileextension;
$strubackupfilename = 'db_backup_structure'.$backuptimestamp.'.sql'.$fileextension;
$tempbackupfilename = 'db_backup.temp.sql'.$fileextension;

$NeverBackupDBtypes = array('HEAP');

// Auto close the browser after the script finishes.
// This will allow task scheduler in Windows to work properly,
// else the task will be considered running until the browser is closed
$CloseWindowOnFinish = false;

/////////////////////////////////////////////////////////////////////
///////////////////       END CONFIGURATION       ///////////////////
/////////////////////////////////////////////////////////////////////

?>