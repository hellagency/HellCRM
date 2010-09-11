<?php
define('backupDBversion', '1.2.5a-200806190803');
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
// hack for Lynx browser that only supports one GETstring parameter
if (!empty($_REQUEST['lynxauth'])) {
	// backupDB.php?lynxauth=localhost.username.password.database[.backuptype]
	$lynxautharray = explode('.', $_REQUEST['lynxauth']);
	$_REQUEST['DB_HOST']     =  @$lynxautharray[0];
	$_REQUEST['DB_USER']     =  @$lynxautharray[1];
	$_REQUEST['DB_PASS']     =  @$lynxautharray[2];
	$_REQUEST['onlyDB']      =  @$lynxautharray[3];
	$_REQUEST['StartBackup'] = (@$lynxautharray[4] ? @$lynxautharray[4] : 'standard');
	$_REQUEST['mailto']      = (@$lynxautharray[5] ? @$lynxautharray[5] : '');
	$_REQUEST['nohtml']      = (isset($_REQUEST['nohtml']) ? $_REQUEST['nohtml'] : '0');
}
// end Lynx hack
/////////////////////////////////////////////////////////////////////

require_once('backupDB.config.php');
require_once('backupDB.functions.php');

/////////////////////////////////////////////////////////////////////

if (!defined('ADMIN_EMAIL') || (ADMIN_EMAIL == '')) {
	die('Please define ADMIN_EMAIL in backupDB.config.php');
}

if ((!defined('DB_HOST') || (DB_HOST == '')) || (!defined('DB_USER') || (DB_USER == '')) || (!defined('DB_PASS') || (DB_PASS == ''))) {
	echo '<html><head><body><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	echo 'database hostname: <input type="text" name="DB_HOST" value="'.(defined('DB_HOST') ? DB_HOST : 'localhost').'"><br>';
	echo 'database username: <input type="text" name="DB_USER" value="'.(defined('DB_USER') ? DB_USER : '').'"><br>';
	echo 'database password: <input type="text" name="DB_PASS" value="'.(defined('DB_PASS') ? DB_PASS : '').'"><br>';
	echo '<input type="submit" value="submit">';
	echo '</form></body></html>';
	exit;
}


if (!is_dir($backupabsolutepath)) {
	die('"'.htmlentities($backupabsolutepath).'" is not a directory');
} elseif (!is_writable($backupabsolutepath)) {
	die('"'.htmlentities($backupabsolutepath).'" is not writable');
}

if ($SuppressHTMLoutput) {
	ob_start();
} else {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}
echo '<h3><a href="http://silisoftware.com/scripts/backupDB">backupDB()</a> v'.backupDBversion.'</h3>';
echo '<h4>MySQL database backup</h4>';
if (isset($_REQUEST['StartBackup'])) {
	OutputInformation('', '<span id="cancellink"><a href="'.$_SERVER['PHP_SELF'].'">Cancel</a><br><br></span>', '<a href="'.$_SERVER['PHP_SELF'].'">Cancel</a><br><br>');
}
OutputInformation('', '<span id="statusinfo"></span>', 'DHTML display is disabled - you won\'t see anything until the backup is complete.');
flush();


if (!@mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
	mail(ADMIN_EMAIL, 'backupDB: FAILURE! Failed to connect to MySQL database (line '.__LINE__.')', 'Failed to connect to SQL database in file '.@$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."\n".mysql_error());
	die('There was a problem connecting to the database:<br>'."\n".mysql_error());
}
$mysql_server_info = mysql_get_server_info();
$TypeEngineKey = (version_compare_replacement($mysql_server_info, '4.0.0', '>=') ? 'Engine' : 'Type'); // MySQL 4.and higher, the 'Type' of database is now 'Engine' <thanks Philippe Soussan>

$ListOfDatabasesToMaybeBackUp = array();
if (defined('DB_NAME')) {
	$ListOfDatabasesToMaybeBackUp[] = DB_NAME;
} else {
	$db_name_list = mysql_list_dbs();
	while (list($dbname) = mysql_fetch_array($db_name_list)) {
		$ListOfDatabasesToMaybeBackUp[] = $dbname;
	}
}



if (isset($_REQUEST['StartBackup']) && ($_REQUEST['StartBackup'] == 'partial')) {

	echo '<script language="JavaScript">'.LINE_TERMINATOR.'<!--'.LINE_TERMINATOR.'function CheckAll(checkornot) {'.LINE_TERMINATOR;
	echo 'for (var i = 0; i < document.SelectedTablesForm.elements.length; i++) {'.LINE_TERMINATOR;
	echo '  document.SelectedTablesForm.elements[i].checked = checkornot;'.LINE_TERMINATOR;
	echo '}'.LINE_TERMINATOR.'}'.LINE_TERMINATOR.'-->'.LINE_TERMINATOR.'</script>';

	echo '<form name="SelectedTablesForm" action="'.$_SERVER['PHP_SELF'].'" method="post">';
	foreach ($ListOfDatabasesToMaybeBackUp as $dbname) {
		$tables = mysql_list_tables($dbname);
		if (is_resource($tables)) {
			echo '<table border="1"><tr><td colspan="'.ceil(mysql_num_rows($tables) / TABLES_PER_COL).'"><b>'.$dbname.'</b></td></tr><tr><td nowrap valign="top">';
			$tablecounter = 0;
			while (list($tablename) = mysql_fetch_array($tables)) {
				$TableStatusResult = mysql_query('SHOW TABLE STATUS LIKE "'.mysql_escape_string($tablename).'"');
				if ($TableStatusRow = mysql_fetch_assoc($TableStatusResult)) {
					if (in_array(@$TableStatusRow[$TypeEngineKey], $NeverBackupDBtypes)) {

						// no need to back up HEAP tables, and will generate errors if you try to optimize/repair

					} else {

						if ($tablecounter++ >= TABLES_PER_COL) {
							echo '</td><td nowrap valign="top">';
							$tablecounter = 0;
						}
						$SQLquery = 'SELECT COUNT(*) AS '.BACKTICKCHAR.'num'.BACKTICKCHAR.' FROM '.BACKTICKCHAR.mysql_escape_string($tablename).BACKTICKCHAR;
						mysql_select_db($dbname);
						$result = mysql_query($SQLquery);
						$row = @mysql_fetch_assoc($result);
						if (mysql_error()) {
							mail(ADMIN_EMAIL, 'backupDB: MySQL Error Report', $SQLquery."\n".mysql_error());
						}
						echo '<input type="checkbox" name="SelectedTables['.htmlentities($dbname, ENT_QUOTES).'][]" value="'.$tablename.'" checked>'.$tablename.' ('.$row['num'].')<br>';

					}
				}
			}
			echo '</td></tr></table><br>';
		}
	}
	if (isset($_POST['DB_HOST'])) {
		echo '<input type="hidden" name="DB_HOST" value="'.htmlspecialchars(@$_POST['DB_HOST'], ENT_QUOTES).'">';
		echo '<input type="hidden" name="DB_USER" value="'.htmlspecialchars(@$_POST['DB_USER'], ENT_QUOTES).'">';
		echo '<input type="hidden" name="DB_PASS" value="'.htmlspecialchars(@$_POST['DB_PASS'], ENT_QUOTES).'">';
	}
	echo '<input type="button" onClick="CheckAll(true)" value="Select All"> ';
	echo '<input type="button" onClick="CheckAll(false)" value="Deselect All"> ';
	echo '<input type="hidden" name="StartBackup" value="complete">';
	echo '<input type="submit" name="SelectedTablesOnly" value="Create Backup"></form>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'">Back to menu</a>';

} elseif (isset($_REQUEST['StartBackup'])) {

	if ($_REQUEST['StartBackup'] == 'structure') {
		$newfullfilename = $backupabsolutepath.$strubackupfilename;
	} elseif (isset($_REQUEST['SelectedTables']) && is_array($_REQUEST['SelectedTables'])) {
		$newfullfilename = $backupabsolutepath.$partbackupfilename;
	} else {
		$newfullfilename = $backupabsolutepath.$fullbackupfilename;
	}

	$starttime = getmicrotime();
	if (@$_REQUEST['StartBackup'] && ($_REQUEST['StartBackup'] != 'partial') && !$Disable_mysqldump && MySQLdumpVersion()) {

		$commandline  = 'mysqldump';
		$commandline .= ((@$_REQUEST['onlyDB'] != @$lynxautharray[3]) ? ' --databases '.$_REQUEST['onlyDB'] : ' --all-databases');
		if (BACKTICKCHAR == '`') {
			$commandline .= ' --quote-names';
		}
		$commandline .= (DB_HOST != 'localhost' ? ' --host='.DB_HOST : '');
		$commandline .= ' --user='.DB_USER;
		$commandline .= ' --password='.DB_PASS;
		if ($_REQUEST['StartBackup'] == 'structure') {
			$commandline .= ' --no-data';
		} elseif ($_REQUEST['StartBackup'] == 'complete') {
			$commandline .= ' --complete-insert';
		}
		switch (OUTPUT_COMPRESSION_TYPE) {
			case 'bzip2':
				if (!bzip2Version()) {
					die('ERROR: bzip2 does not appear to be installed');
				}
				$commandline .= ' | bzip2 -cf'.OUTPUT_COMPRESSION_LEVEL.' > '.$newfullfilename;
				break;
			case 'gzip':
				if (!gzipVersion()) {
					die('ERROR: gzip does not appear to be installed');
				}
				$commandline .= ' | gzip -cf'.OUTPUT_COMPRESSION_LEVEL.' > '.$newfullfilename;
				break;
			case 'none':
				$commandline .= ' > '.$newfullfilename;
				break;
			default:
				die('ERROR: OUTPUT_COMPRESSION_TYPE ('.htmlentities(OUTPUT_COMPRESSION_TYPE).') must be one of "bzip2", "gzip", "none"');
				break;
		}

		set_time_limit(300); // shouldn't take nearly this long for anything, but just in case...
		echo SafeExec($commandline);
		echo '<hr>';

	} else {

		switch (OUTPUT_COMPRESSION_TYPE) {
			case 'gzip':
			case 'none':
				// great
				break;
			case 'bzip2':
				if (!function_exists('bzopen')) {
					die('ERROR: PHP-bzip2 support does not appear to be installed, please change OUTPUT_COMPRESSION_TYPE to one of "gzip" or "none"');
				}
				break;
			default:
				die('ERROR: OUTPUT_COMPRESSION_TYPE ('.htmlentities(OUTPUT_COMPRESSION_TYPE).') must be one of "bzip2", "gzip", "none"');
				break;
		}
		if (((OUTPUT_COMPRESSION_TYPE == 'gzip')  && ($zp = @gzopen($backupabsolutepath.$tempbackupfilename, 'wb'.OUTPUT_COMPRESSION_LEVEL))) ||
			((OUTPUT_COMPRESSION_TYPE == 'bzip2') && ($bp = @bzopen($backupabsolutepath.$tempbackupfilename, 'wb'))) ||
			((OUTPUT_COMPRESSION_TYPE == 'none')  && ($fp = @fopen($backupabsolutepath.$tempbackupfilename, 'wb')))) {

			$fileheaderline  = '# backupDB() v'.backupDBversion.' (http://www.silisoftware.com)'.LINE_TERMINATOR;
			$fileheaderline .= '# mySQL backup ('.date('F j, Y g:i a').')   Type = ';
			if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
				bzwrite($bp, $fileheaderline, strlen($fileheaderline));
			} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
				gzwrite($zp, $fileheaderline, strlen($fileheaderline));
			} else {
				fwrite($fp, $fileheaderline, strlen($fileheaderline));
			}

			if ($_REQUEST['StartBackup'] == 'structure') {

				if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
					bzwrite($bp, 'Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
					gzwrite($zp, 'Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				} else {
					fwrite($fp, 'Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Structure Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				}
				$backuptype = 'full';
				unset($SelectedTables);

				foreach ($ListOfDatabasesToMaybeBackUp as $dbname) {
					set_time_limit(60);
					$tables = mysql_list_tables($dbname);
					if (is_resource($tables)) {
						$tablecounter = 0;
						while (list($tablename) = mysql_fetch_array($tables)) {
							$TableStatusResult = mysql_query('SHOW TABLE STATUS LIKE "'.mysql_escape_string($tablename).'"');
							if ($TableStatusRow = mysql_fetch_assoc($TableStatusResult)) {
								if (in_array($TableStatusRow[$TypeEngineKey], $NeverBackupDBtypes)) {

									// no need to back up HEAP tables, and will generate errors if you try to optimize/repair

								} else {

									$SelectedTables[$dbname][] = $tablename;

								}
							}
						}
					}
				}

			} elseif (isset($_REQUEST['SelectedTables']) && is_array($_REQUEST['SelectedTables'])) {

				if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
					bzwrite($bp, 'Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
					gzwrite($zp, 'Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				} else {
					fwrite($fp, 'Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Selected Tables Only'.LINE_TERMINATOR.LINE_TERMINATOR));
				}
				$backuptype = 'partial';
				$SelectedTables = $_REQUEST['SelectedTables'];

			} else {

				if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
					bzwrite($bp, 'Complete'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Complete'.LINE_TERMINATOR.LINE_TERMINATOR));
				} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
					gzwrite($zp, 'Complete'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Complete'.LINE_TERMINATOR.LINE_TERMINATOR));
				} else {
					fwrite($fp, 'Complete'.LINE_TERMINATOR.LINE_TERMINATOR, strlen('Complete'.LINE_TERMINATOR.LINE_TERMINATOR));
				}
				$backuptype = 'full';
				unset($SelectedTables);

				foreach ($ListOfDatabasesToMaybeBackUp as $dbname) {
					set_time_limit(60);
					$tables = mysql_list_tables($dbname);
					if (is_resource($tables)) {
						$tablecounter = 0;
						while (list($tablename) = mysql_fetch_array($tables)) {
							$TableStatusResult = mysql_query('SHOW TABLE STATUS LIKE "'.mysql_escape_string($tablename).'"');
							if ($TableStatusRow = mysql_fetch_assoc($TableStatusResult)) {
								if (in_array($TableStatusRow[$TypeEngineKey], $NeverBackupDBtypes)) {

									// no need to back up HEAP tables, and will generate errors if you try to optimize/repair

								} else {

									$SelectedTables[$dbname][] = $tablename;

								}
							}
						}
					}
				}

			}

			OutputInformation('', null, 'Checking tables...<br><br>');
			$TableErrors = array();
			foreach ($SelectedTables as $dbname => $selectedtablesarray) {
				mysql_select_db($dbname);
				$repairresult = '';
				$CanContinue = true;
				foreach ($selectedtablesarray as $selectedtablename) {
					OutputInformation('statusinfo', 'Checking table <b>'.$dbname.'.'.$selectedtablename.'</b>');
					set_time_limit(60);
					$result = mysql_query('CHECK TABLE '.BACKTICKCHAR.$selectedtablename.BACKTICKCHAR);
					while ($row = mysql_fetch_assoc($result)) {
						set_time_limit(60);
						if ($row['Msg_text'] == 'OK') {

							mysql_query('OPTIMIZE TABLE '.BACKTICKCHAR.$selectedtablename.BACKTICKCHAR);

						} else {

							OutputInformation('statusinfo', 'Repairing table <b>'.$selectedtablename.'</b>');
							$repairresult .= 'REPAIR TABLE '.BACKTICKCHAR.$selectedtablename.BACKTICKCHAR.' EXTENDED'."\n\n";
							$fixresult = mysql_query('REPAIR TABLE '.BACKTICKCHAR.$selectedtablename.BACKTICKCHAR.' EXTENDED');
							$ThisCanContinue = false;
							while ($fixrow = mysql_fetch_assoc($fixresult)) {
								$thisMessage = $fixrow['Msg_type'].': '.$fixrow['Msg_text'];
								$repairresult .= $thisMessage."\n";
								switch ($thisMessage) {
									case 'status: OK':
									case 'error: The handler for the table doesn\'t support repair':
										$ThisCanContinue = true;
										break;
								}
							}
							if (!$ThisCanContinue) {
								$CanContinue = false;
							}

							$repairresult .= "\n\n".str_repeat('-', 60)."\n\n";

						}
					}
				}

				if (!empty($repairresult)) {
					mail(ADMIN_EMAIL, 'backupDB: MySQL Table Error Report', $repairresult);
					echo '<pre>'.$repairresult.'</pre>';
					if (!$CanContinue) {
						if ($SuppressHTMLoutput) {
							ob_end_clean();
							echo 'errors';
						}
						exit;
					}
				}
			}
			OutputInformation('statusinfo', '');

			OutputInformation('', '<br><b><span id="topprogress">Overall Progress:</span></b><br>');
			$overallrows = 0;
			foreach ($SelectedTables as $dbname => $value) {
				mysql_select_db($dbname);
				echo '<table border="1"><tr><td colspan="'.ceil(count($SelectedTables[$dbname]) / TABLES_PER_COL).'"><b>'.$dbname.'</b></td></tr><tr><td nowrap valign="top">';
				$tablecounter = 0;
				for ($t = 0; $t < count($SelectedTables[$dbname]); $t++) {
					if ($tablecounter++ >= TABLES_PER_COL) {
						echo '</td><td nowrap valign="top">';
						$tablecounter = 1;
					}
					$SQLquery = 'SELECT COUNT(*) AS '.BACKTICKCHAR.'num'.BACKTICKCHAR.' FROM '.BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR;
					$result = mysql_query($SQLquery);
					$row = mysql_fetch_assoc($result);
					$rows[$t] = $row['num'];
					$overallrows += $rows[$t];
					echo '<span id="rows_'.$dbname.'_'.$SelectedTables[$dbname][$t].'">'.$SelectedTables[$dbname][$t].' ('.number_format($rows[$t]).' records)</span><br>';
				}
				echo '</td></tr></table><br>';
			}

			$alltablesstructure = '';
			foreach ($SelectedTables as $dbname => $value) {
				mysql_select_db($dbname);
				for ($t = 0; $t < count($SelectedTables[$dbname]); $t++) {
					set_time_limit(60);
					OutputInformation('statusinfo', 'Creating structure for <b>'.$dbname.'.'.$SelectedTables[$dbname][$t].'</b>');

					$fieldnames     = array();
					$structurelines = array();
					$result = mysql_query('SHOW FIELDS FROM '.BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR);
					while ($row = mysql_fetch_assoc($result)) {
						$structureline  = BACKTICKCHAR.$row['Field'].BACKTICKCHAR;
						$structureline .= ' '.$row['Type'];
						$structureline .= ' '.($row['Null'] ? '' : 'NOT ').'NULL';
						eregi('^[a-z]+', $row['Type'], $matches);
						$RowTypes[$dbname][$SelectedTables[$dbname][$t]][$row['Field']] = $matches[0];
						if (@$row['Default']) {
							if (eregi('^(tiny|medium|long)?(text|blob)', $row['Type'])) {
								// no default values
							} else {
								$structureline .= ' default \''.$row['Default'].'\'';
							}
						}
						$structureline .= ($row['Extra'] ? ' '.$row['Extra'] : '');
						$structurelines[] = $structureline;

						$fieldnames[] = $row['Field'];
					}
					mysql_free_result($result);

					$tablekeys    = array();
					$uniquekeys   = array();
					$fulltextkeys = array();
					$result = mysql_query('SHOW INDEX FROM '.BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR);
					$INDICES = array();
					while ($row = mysql_fetch_assoc($result)) {
						$INDICES[$row['Key_name']][$row['Seq_in_index']] = $row;
					}
					mysql_free_result($result);
					foreach ($INDICES as $index_name => $columndata) {
						$structureline  = '';
						if ($index_name == 'PRIMARY') {
							$structureline .= 'PRIMARY ';
						} elseif ((@$columndata[1]['Index_type'] == 'FULLTEXT') || ($columndata[1]['Comment'] == 'FULLTEXT')) {
							$structureline .= 'FULLTEXT ';
						} elseif (!$columndata[1]['Non_unique']) {
							$structureline .= 'UNIQUE ';
						}
						$structureline .= 'KEY';
						if ($index_name != 'PRIMARY') {
							$structureline .= ' '.BACKTICKCHAR.$index_name.BACKTICKCHAR;
						}
						$structureline .= ' (';
						$firstkeyname = true;
						foreach ($columndata as $seq_in_index => $row) {
							if (!$firstkeyname) {
								$structureline .= ',';
							}
							$structureline .= BACKTICKCHAR.$row['Column_name'].BACKTICKCHAR;
							if ($row['Sub_part']) {
								$structureline .= '('.$row['Sub_part'].')';
							}
							$firstkeyname = false;
						}
						$structureline .= ')';
						$structurelines[] = $structureline;
					}

					$TableStatusResult = mysql_query('SHOW TABLE STATUS LIKE "'.mysql_escape_string($SelectedTables[$dbname][$t]).'"');
					if (!($TableStatusRow = mysql_fetch_assoc($TableStatusResult))) {
						die('failed to execute "SHOW TABLE STATUS" on '.$dbname.'.'.$tablename);
					}

					$tablestructure  = 'CREATE TABLE '.($CreateIfNotExists ? 'IF NOT EXISTS ' : '').($dbNameInCreate ? BACKTICKCHAR.$dbname.BACKTICKCHAR.'.' : '').BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR.' ('.LINE_TERMINATOR;
					$tablestructure .= '  '.implode(','.LINE_TERMINATOR.'  ', $structurelines).LINE_TERMINATOR;
					$tablestructure .= ') TYPE='.$TableStatusRow[$TypeEngineKey];
					if ($TableStatusRow['Auto_increment'] !== null) {
						$tablestructure .= ' AUTO_INCREMENT='.$TableStatusRow['Auto_increment'];
					}
					$tablestructure .= ';'.LINE_TERMINATOR.LINE_TERMINATOR;

					$alltablesstructure .= str_replace(' ,', ',', $tablestructure);

				} // end table structure backup
			}
			if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
				bzwrite($bp, $alltablesstructure.LINE_TERMINATOR, strlen($alltablesstructure) + strlen(LINE_TERMINATOR));
			} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
				gzwrite($zp, $alltablesstructure.LINE_TERMINATOR, strlen($alltablesstructure) + strlen(LINE_TERMINATOR));
			} else {
				fwrite($fp, $alltablesstructure.LINE_TERMINATOR, strlen($alltablesstructure) + strlen(LINE_TERMINATOR));
			}

			$datastarttime = getmicrotime();
			OutputInformation('statusinfo', '');
			if ($_REQUEST['StartBackup'] != 'structure') {
				$processedrows    = 0;
				foreach ($SelectedTables as $dbname => $value) {
					set_time_limit(60);
					mysql_select_db($dbname);
					for ($t = 0; $t < count($SelectedTables[$dbname]); $t++) {
						$result = mysql_query('SELECT * FROM '.$SelectedTables[$dbname][$t]);
						$rows[$t] = mysql_num_rows($result);
						if ($rows[$t] > 0) {
							$tabledatadumpline = '# dumping data for '.$dbname.'.'.$SelectedTables[$dbname][$t].LINE_TERMINATOR;
							if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
								bzwrite($bp, $tabledatadumpline, strlen($tabledatadumpline));
							} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
								gzwrite($zp, $tabledatadumpline, strlen($tabledatadumpline));
							} else {
								fwrite($fp, $tabledatadumpline, strlen($tabledatadumpline));
							}
						}
						unset($fieldnames);
						for ($i = 0; $i < mysql_num_fields($result); $i++) {
							$fieldnames[] = mysql_field_name($result, $i);
						}
						if ($_REQUEST['StartBackup'] == 'complete') {
							$insertstatement = ($ReplaceInto ? 'REPLACE' : 'INSERT').' INTO '.BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR.' ('.BACKTICKCHAR.implode(BACKTICKCHAR.', '.BACKTICKCHAR, $fieldnames).BACKTICKCHAR.') VALUES (';
						} else {
							$insertstatement = ($ReplaceInto ? 'REPLACE' : 'INSERT').' INTO '.BACKTICKCHAR.$SelectedTables[$dbname][$t].BACKTICKCHAR.' VALUES (';
						}
						$currentrow       = 0;
						$thistableinserts = '';
						while ($row = mysql_fetch_array($result)) {
							unset($valuevalues);
							foreach ($fieldnames as $key => $val) {
								if ($row[$key] === null) {

									$valuevalues[] = 'NULL';

								} else {

									switch ($RowTypes[$dbname][$SelectedTables[$dbname][$t]][$val]) {
										// binary data dump, two hex characters per byte
										case 'tinyblob':
										case 'blob':
										case 'mediumblob':
										case 'longblob':
											$data = $row[$key];
											$data_len = strlen($data);
											if ($HexBLOBs && $data_len) {
												$hexstring = '0x';
												for ($i = 0; $i < $data_len; $i++) {
													$hexstring .= str_pad(dechex(ord($data{$i})), 2, '0', STR_PAD_LEFT);
												}
												$valuevalues[] = $hexstring;
											} else {
												$valuevalues[] = QUOTECHAR.mysql_escape_string($data).QUOTECHAR;
											}
											break;

										// just the (numeric) value, not surrounded by quotes
										case 'tinyint':
										case 'smallint':
										case 'mediumint':
										case 'int':
										case 'bigint':
										case 'float':
										case 'double':
										case 'decimal':
										case 'year':
											$valuevalues[] = mysql_escape_string($row[$key]);
											break;

										// value surrounded by quotes
										case 'varchar':
										case 'char':
										case 'tinytext':
										case 'text':
										case 'mediumtext':
										case 'longtext':
										case 'enum':
										case 'set':
										case 'date':
										case 'datetime':
										case 'time':
										case 'timestamp':
										default:
											$valuevalues[] = QUOTECHAR.mysql_escape_string($row[$key]).QUOTECHAR;
											break;
									}

								}
							}
							$thistableinserts .= $insertstatement.implode(', ', $valuevalues).');'.LINE_TERMINATOR;

							if (strlen($thistableinserts) >= BUFFER_SIZE) {
								if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
									bzwrite($bp, $thistableinserts, strlen($thistableinserts));
								} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
									gzwrite($zp, $thistableinserts, strlen($thistableinserts));
								} else {
									fwrite($fp, $thistableinserts, strlen($thistableinserts));
								}
								$thistableinserts = '';
							}
							if ((++$currentrow % STATS_INTERVAL) == 0) {
								set_time_limit(60);
								if ($DHTMLenabled) {
									OutputInformation('rows_'.$dbname.'_'.$SelectedTables[$dbname][$t], '<b>'.$SelectedTables[$dbname][$t].' ('.number_format($rows[$t]).' records, ['.number_format(($currentrow / $rows[$t])*100).'%])</b>');
									$elapsedtime = getmicrotime() - $datastarttime;
									$percentprocessed = ($processedrows + $currentrow) / $overallrows;
									$overallprogress = 'Overall Progress: '.number_format($processedrows + $currentrow).' / '.number_format($overallrows).' ('.number_format($percentprocessed * 100, 1).'% done) ['.FormattedTimeRemaining($elapsedtime).' elapsed';
									if (($percentprocessed > 0) && ($percentprocessed < 1)) {
										$overallprogress .= ', '.FormattedTimeRemaining(abs($elapsedtime - ($elapsedtime / $percentprocessed))).' remaining';
									}
									$overallprogress .= ']';
									OutputInformation('topprogress', $overallprogress);
								}
							}
							if (($currentrow % MYSQL_RECONNECT_INTERVAL) == 0) {
								mysql_close();
								if (!@mysql_connect(DB_HOST, DB_USER, DB_PASS)) {
									mail(ADMIN_EMAIL, 'backupDB: FAILURE! Failed to connect to MySQL database (line '.__LINE__.')', 'Failed to reconnect to SQL database (row #'.$currentrow.') on line '.__LINE__.' in file '.@$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."\n".mysql_error());
									die('There was a problem connecting to the database:<br>'."\n".mysql_error());
								}
								mysql_select_db($dbname);
							}
						}
						if ($DHTMLenabled) {
							OutputInformation('rows_'.$dbname.'_'.$SelectedTables[$dbname][$t], $SelectedTables[$dbname][$t].' ('.number_format($rows[$t]).' records, [100%])');
							$processedrows += $rows[$t];
						}
						if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
							bzwrite($bp, $thistableinserts.LINE_TERMINATOR.LINE_TERMINATOR, strlen($thistableinserts) + strlen(LINE_TERMINATOR) + strlen(LINE_TERMINATOR));
						} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
							gzwrite($zp, $thistableinserts.LINE_TERMINATOR.LINE_TERMINATOR, strlen($thistableinserts) + strlen(LINE_TERMINATOR) + strlen(LINE_TERMINATOR));
						} else {
							fwrite($fp, $thistableinserts.LINE_TERMINATOR.LINE_TERMINATOR, strlen($thistableinserts) + strlen(LINE_TERMINATOR) + strlen(LINE_TERMINATOR));
						}
					}
				}
			}
			if (OUTPUT_COMPRESSION_TYPE == 'bzip2') {
				bzclose($bp);
			} elseif (OUTPUT_COMPRESSION_TYPE == 'gzip') {
				gzclose($zp);
			} else {
				fclose($fp);
			}

			if (file_exists($newfullfilename)) {
				unlink($newfullfilename); // Windows won't allow overwriting via rename
			}
			rename($backupabsolutepath.$tempbackupfilename, $newfullfilename);

		} else {

			echo '<b>Warning:</b> failed to open '.$backupabsolutepath.$tempbackupfilename.' for writing!<br><br>';
			if (is_dir($backupabsolutepath)) {
				echo '<i>CHMOD 777</i> on the directory ('.htmlentities($backupabsolutepath).') should fix that.';
			} else {
				echo 'The specified directory does not exist: "'.htmlentities($backupabsolutepath).'"';
			}

		}

	}

	if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
		touch($newfullfilename);
		if (!chmod($newfullfilename, 0777)) {
			mail(ADMIN_EMAIL, 'backupDB: Failed to chmod()', 'Failed to chmod('.$newfullfilename.', 0777)');
		}
	}

	if (@$_REQUEST['mailto']) {
		if (version_compare_replacement(phpversion(), '4.3.2', '>=') && !defined('memory_get_usage') && !@ini_get('memory_limit')) {
			// no actual memory limit
			$maxfilesize = 10 * 1048576; // 10MB arbitary limit
		} else {
			// set maxfilesize to 25% of memory limit to allow for script overhead
			$maxfilesize = round(max(intval(ini_get('memory_limit')), intval(get_cfg_var('memory_limit'))) * 1048576 * 0.25);
		}
		if (filesize($newfullfilename) <= $maxfilesize) {
			if ($fp = @fopen($newfullfilename, 'rb')) {
				$emailattachmentfiledata = fread($fp, filesize($newfullfilename));
				fclose($fp);
				if (!EmailAttachment(ADMIN_EMAIL, ADMIN_EMAIL, 'backupDB: '.basename($newfullfilename), 'backupDB: '.basename($newfullfilename), $emailattachmentfiledata, basename($newfullfilename))) {
					mail(ADMIN_EMAIL, 'backupDB: Failed to email attachment ['.basename($newfullfilename).']', 'Failed to email attachment ['.basename($newfullfilename).']');
				}
				unset($emailattachmentfiledata);
			} else {
				mail(ADMIN_EMAIL, 'backupDB: FAILED: @fopen("'.$newfullfilename.'", "rb")', 'FAILED: @fopen("'.$newfullfilename.'", "rb")');
			}
		} else {
			mail(ADMIN_EMAIL, 'backupDB: Cannot email "'.$newfullfilename.'" (too large)', 'Cannot email "'.$newfullfilename.'" -- it is '.number_format(filesize($newfullfilename)).' bytes, which is more than the calculated maximum allowable size of '.number_format($maxfilesize).' bytes.');
		}
	}

	echo '<br>Backup complete in '.FormattedTimeRemaining(getmicrotime() - $starttime, 2).'.<br>';
	echo '<a href="'.str_replace(@$_SERVER['DOCUMENT_ROOT'], '', $backupabsolutepath).basename($newfullfilename).'"><b>'.basename($newfullfilename).'</b> ('.FileSizeNiceDisplay(filesize($newfullfilename), 2);
	echo ')</a><br><br><a href="'.$_SERVER['PHP_SELF'].'">Back to MySQL Database Backup main menu</a><br>';

	OutputInformation('cancellink', '');

} else {  // !$_REQUEST['StartBackup']

	if (file_exists($backupabsolutepath.$fullbackupfilename)) {
		echo 'It is now '.gmdate('F j, Y g:ia T', time() + date('Z')).'<br>';
		echo 'Last full backup of MySQL databases: ';
		$lastbackuptime = filemtime($backupabsolutepath.$fullbackupfilename);
		echo gmdate('F j, Y g:ia T', $lastbackuptime + date('Z'));
		echo ' (<b>'.FormattedTimeRemaining(time() - $lastbackuptime).'</b> ago)<br>';
		if ((time() - $lastbackuptime) < 86400) {
			echo 'Generally, backing up more than once a day is not neccesary.<br>';
		}
		echo '<br><a href="'.str_replace(@$_SERVER['DOCUMENT_ROOT'], '', $backupabsolutepath).$fullbackupfilename.'">Download previous full backup ('.FileSizeNiceDisplay(filesize($backupabsolutepath.$fullbackupfilename), 2).')</a> (right-click, Save As...)<br><br>';
	} else {
		echo 'Last backup of MySQL databases: <i>unknown</i>'.($backuptimestamp ? ' (incompatible with timestamping)' : '').'<br>';
	}

	$BackupTypesList = array(
		'complete'  => 'Full backup, complete inserts (recommended)',
		'standard'  => 'Full backup, standard inserts (smaller)',
		'partial'   => 'Selected tables only (with complete inserts)',
		'structure' => 'Table structure(s) only'
	);
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	if (isset($_POST['DB_HOST'])) {
		echo '<input type="hidden" name="DB_HOST" value="'.htmlspecialchars(@$_POST['DB_HOST'], ENT_QUOTES).'">';
		echo '<input type="hidden" name="DB_USER" value="'.htmlspecialchars(@$_POST['DB_USER'], ENT_QUOTES).'">';
		echo '<input type="hidden" name="DB_PASS" value="'.htmlspecialchars(@$_POST['DB_PASS'], ENT_QUOTES).'">';
	}
	echo '<select name="StartBackup">';
	foreach ($BackupTypesList as $key => $value) {
		echo '<option value="'.$key.'">'.htmlentities($value).'</option>';;
	}
	echo '</select><br>';
	echo '<input type="checkbox" name="mailto">Email backup file to admin email address ('.ADMIN_EMAIL.')<br>';
	echo '<input type="submit" value="Go">';
	echo '</form>';
}


if ($SuppressHTMLoutput) {
	ob_end_clean();
	header('Location: '.$fullbackupfilename);
}


if ($CloseWindowOnFinish) {
	// Auto close the browser after the script finishes.
	// This will allow task scheduler in Windows to work properly,
	// else the task will be considered running until the browser is closed
	echo '<script language="javascript">'."\n";
	echo 'window.opener = top;'."\n";
	echo 'window.close();'."\n";
	echo '</script>';
}

?>
