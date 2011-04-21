<?php

include_once("Html.php");
include_once("libdb.php");


######################################
# true/false color
# color text green if true, else red 
######################################
function tfColor($string, $tf) {
    if ( $tf ) {
        $colorout = "<font color=\"#00aa00\">$string</font>";
    } else {
        $colorout = "<font color=\"#880000\">$string</font>";
    }
    return $colorout;
}

function hiLoColor($value, $lo, $hi) {
    $fvalue = floatval($value);
    if ($fvalue >= $lo and $fvalue <=$hi) {
	$colorout = "<font color=\"#00aa00\">$value</font>";
    } elseif ($fvalue < $lo) {
	$colorout = "<font color=\"#000088\">$value</font>";
    } elseif ($fvalue > $hi) {
	$colorout = "<font color=\"#880000\">$value</font>";
    }
    return $colorout;
}


#######################################
#
#
#######################################
function getCurrentUriDir() {
    $uri = $_SERVER['REQUEST_URI'];
    $dirs = preg_split("/\//", $uri);
    $dir = $dirs[count($dirs)-2];
    return $dir;
}


function verifyTest($value, $lo, $hi) {
    $pass = true;  # default true (ie. no limits were set)
    if ($lo and $hi) {
	$pass = ($value >= $lo and $value <=$hi);
    }
    if ($lo and !$hi) {
	$pass = ($value >= $lo);
    }
    if (!$lo and $hi) {
	$pass = ($value <= $hi);
    }
    return $pass;
}


function getDefaultTest() {

    if (array_key_exists('test', $_GET)) {
	$testDir = $_GET['test'];
	setcookie('displayQA_test', $testDir);
    } elseif (array_key_exists('displayQA_test', $_COOKIE)) {
	$testDir = $_COOKIE['displayQA_test'];
    } else {
	$d = @dir(".");
	while(false !== ($f = $d->read())) {
	    if (ereg("test_", $f) and is_dir($f)) {
		$testDir = $f;
		break;
	    }
	}
    }
    return $testDir;
}

function writeTable_ListOfTestResults() {

    $testDir = getDefaultTest();
    
    $table = new Table("width=\"90%\"");

    $headAttribs = array("align=\"center\"");
    $table->addHeader(
	array("Label", "Timestamp", "Value", "Limits", "Comment"),
	$headAttribs
	);

    global $dbFile;
    $db = connect($testDir);
    $cmd = "select * from summary order by label";
    $result = $db->query($cmd);

    $tdAttribs = array("align=\"left\"", "align=\"center\"",
		       "align=\"right\"", "align=\"center\"",
		       "align=\"left\" width=\"200\"");
    foreach ($result as $r) {
	list($test, $lo, $value, $hi, $comment) =
	    array($r['label'], $r['lowerlimit'], $r['value'], $r['upperlimit'], $r['comment']);
	
	$pass = verifyTest($value, $lo, $hi);
	if (!$lo) { $lo = "None"; }
	if (!$hi) { $hi = "None"; }
	
	if (!$pass) {
	    $test .= " <a href=\"backtrace.php?label=$test\">Backtrace</a>";
	}
	$mtime = date("Y-m_d H:i:s", $r['entrytime']);

	$loStr = sprintf("%.3f", $lo);
	$hiStr = sprintf("%.3f", $hi);
	$valueStr = sprintf("%.3f", $value);

	$table->addRow(array($test, $mtime,
			     hiLoColor($valueStr, $lo, $hi), "[$loStr, $hiStr]", $comment), $tdAttribs);
    }
    $db = NULL;
    return $table->write();
    
}
function displayTable_ListOfTestResults($testDir) {
    echo writeTable_ListOfTestResults($testDir);
}



function writeTable_OneTestResult($label) {

    $testDir = getDefaultTest();
    
    if (empty($label)) {
	return "<h2>No test label specified. Cannot display test result.</h2><br/>\n";
    }
    
    $table = new Table("width=\"90%\"");
    
    $headAttribs = array("align=\"center\"");
    $table->addHeader(
	array("Label", "Timestamp", "Value", "Limits", "Comment"),
	$headAttribs
	);
    #$table->addHeader(array("Label", "Timestamp", "LowerLimit", "Value", "UpperLimit", "Comment"));

    global $dbFile;
    #$mtime = date("Y-m_d H:i:s", filemtime("$testDir/$dbFile"));
    $db = connect($testDir);
    $cmd = "select * from summary where label='$label'";

    $result = $db->query($cmd);
    
    $tdAttribs = array("align=\"left\"", "align=\"center\"",
		       "align=\"right\"", "align=\"center\"",
		       "align=\"left\" width=\"200\"");
    foreach ($result as $r) {
	list($test, $timestamp, $lo, $value, $hi, $comment, $backtrace) =
	    array($r['label'], $r['entrytime'], $r['lowerlimit'], $r['value'], $r['upperlimit'],
		  $r['comment'], $r['backtrace']);
	
	$pass = verifyTest($value, $lo, $hi);
	if (!$lo) { $lo = "None"; }
	if (!$hi) { $hi = "None"; }

	$mtime = date("Y-m_d H:i:s", $r['entrytime']);

	$loStr = sprintf("%.3f", $lo);
	$hiStr = sprintf("%.3f", $hi);
	$valueStr = sprintf("%.3f", $value);

	$table->addRow(array($test, $mtime,
			     hiLoColor($valueStr, $lo, $hi), "[$loStr, $hiStr]", $comment), $tdAttribs);
	#$table->addRow(array($test, date("Y-m-d H:i:s", $timestamp),
	#		     $lo, hiLoColor($value, $pass), $hi, $comment));
    }
    $db = NULL;

    return $table->write();
}
function write_OneBacktrace($label) {

    $testDir = getDefaultTest();
    
    $out = "<h2>Backtrace</h2><br/>\n";
    if (empty($label)) {
	return "<b>No test label specified. Cannot display backtrace.</b><br/>\n";
    }
    
    global $dbFile;
    $db = connect($testDir);
    $cmd = "select * from summary where label='$label'";
    $result = $db->query($cmd);

    $backtrace = "";
    foreach ($result as $r) {
	$backtrace .= $r['backtrace'];
    }
    $db = NULL;

    $out .= preg_replace("/\n/", "<br/>\n", $backtrace);
    $out = preg_replace("/(\t|\s{4})/", "&nbsp;&nbsp;", $out);
    
    return $out;
    
}
function displayTable_OneTestResult($testDir, $label) {
    echo writeTable_OneTestResult($testDir);
}






function writeFigures() {

    $testDir = getDefaultTest();
    
    $out = "";
    $d = @dir("$testDir");
    while(false !== ($f = $d->read())) {
    	if (! ereg(".(png|PNG|jpg|JPG)", $f)) { continue; }

	# get the image path
    	$path = "$testDir/$f";
    	$mtime = date("Y-m_d H:i:s", filemtime($path));

	# get the caption
	$db = connect($testDir);
	$cmd = "select caption from figure where filename = '$f'";
	$result = $db->query($cmd)->fetchColumn();

	$img = new Table();
	$img->addRow(array("<center><img src=\"$path\" width=\"512\"></center>"));
	$img->addRow(array($result));
	$img->addRow(array("Timestamp: $mtime"));
	$out .= $img->write();
    }
    return $out;
}
function displayFigures($testDir) {
    echo writeFigures($testDir);
}




function summarizeTest($testDir) {
    $summary = array();

    global $dbFile;
    #$mtime = date("Y-m_d H:i:s", filemtime("$testDir/$dbFile"));

    $db = connect($testDir);
    $testCmd = "select count(*) from summary";
    $nTest = $db->query($testCmd)->fetchColumn();
    
    $passCmd = "select * from summary order by label";
    $results = $db->query($passCmd);
    $nPass = 0;
    $timestamp = 0;
    foreach($results as $result) {
	if (verifyTest($result['value'], $result['lowerlimit'], $result['upperlimit'])){
	    $nPass += 1;
	}
	$timestamp = $result['entrytime'];
    }

    $ret = array();
    $ret['name'] = $testDir;
    $ret['entrytime'] = $timestamp;
    $ret['ntest'] = $nTest;
    $ret['npass'] = $nPass;
    return $ret;
}


function getGroup() {
   if (array_key_exists('group', $_GET)) {
	$group = $_GET['group'];
	setcookie('displayQA_group', $group);
    } elseif (array_key_exists('displayQA_group', $_COOKIE)) {
	$group = $_COOKIE['displayQA_group'];
    } else {
	$group = "";
    }
   return $group;
}

function writeTable_SummarizeAllTests() {
    $dir = "./";

    $group = getGroup();
    

    ## go through all directories and look for .summary files
    $d = @dir($dir) or dir("");
    $dirs = array();
    while(false !== ($testDir = $d->read())) {
	$dirs[] = $testDir;
    }
    sort($dirs);
    
    $d = @dir($dir) or dir("");
    $table = new Table("width=\"90%\"");
    $table->addHeader(array("Test", "mtime", "No. Tests", "No. Passed", "Fail Rate"));
    #while(false !== ($testDir = $d->read())) {
    foreach ($dirs as $testDir) {
	# only interested in directories, but not . or ..
	if ( ereg("^\.", $testDir) or ! is_dir("$testDir")) {
	    continue;
	}
	# only interested in the group requested
	if (! ereg("test_".$group, $testDir)) {
	    continue;
	}

	# if our group is "" ... ignore other groups
	$parts = preg_split("/_/", $testDir);
	if ( $group == "" and (count($parts) > 2)) {
	    continue;
	}

	$summ = summarizeTest($testDir);
	$testLink = "<a href=\"summary.php?test=$testDir\">$testDir</a>";

	$passLink = tfColor($summ['npass'], ($summ['npass']==$summ['ntest']));
	$failRate = "n/a";
	if ($summ['ntest'] > 0) {
	    $failRate = 1.0 - 1.0*$summ['npass']/$summ['ntest'];
	    $failRate = tfColor(sprintf("%.3f", $failRate), ($failRate == 0.0));
	}
	if ($summ['entrytime'] > 0) {
	    $timestampStr = date("Y-m-d H:i:s", $summ['entrytime']);
	} else {
	    $timestampStr = "n/a";
	}
	
	$table->addRow(array($testLink, $timestampStr,
			     $summ['ntest'], $passLink, $failRate));
    }
    return $table->write();
    
}
function getGroupList() {
    $dir = "./";
    $groups = array();
    $d = @dir($dir) or dir("");
    while(false !== ($testDir = $d->read())) {
	$parts = preg_split("/_/", $testDir);

	if (count($parts) > 2) {
	    $group = $parts[1];
	} else {
	    $group = "";
	}
	
	if (array_key_exists($group, $groups)) {
	    $groups[$group] += 1;
	} else {
	    $groups[$group] = 1;
	}
    }
    ksort($groups);
    return $groups;
}

function writeTable_SummarizeAllGroups() {
    $dir = "./";

    $groups = getGroupList();
    
    ## go through all directories and look for .summary files
    $table = new Table("width=\"90%\"");
    $table->addHeader(array("Test", "mtime", "TestSets", "TestSets Passed", "Tests", "Tests Passed", "Fail Rate"));
    foreach ($groups as $group=>$n) {

	$nTestSets = 0;
	$nTestSetsPass = 0;
	$nTest = 0;
	$nPass = 0;
	
	$d = @dir($dir) or dir("");
	$dirs = array();
	while(false !== ($testDir = $d->read())) {
	    $dirs[] = $testDir;
	}
	asort($dirs);
	
	$lastUpdate = 0;
	$d = @dir($dir) or dir("");
	#while(false !== ($testDir = $d->read())) {
	foreach ($dirs as $testDir) {
	    if (!ereg("test_".$group, $testDir)) {
		continue;
	    }
	    # must deal with default group "" specially
	    $parts = preg_split("/_/", $testDir);
	    if (count($parts) > 2 and $group == "") {
		continue;
	    }
	    
	    $summ = summarizeTest($testDir);
	    $nTestSets += 1;
	    $nTest += $summ['ntest'];
	    $nPass += $summ['npass'];
	    if ($summ['ntest'] == $summ['npass']) {
		$nTestSetsPass += 1;
	    }
	    if ($summ['entrytime'] > $lastUpdate) {
		$lastUpdate = $summ['entrytime'];
	    }
	}

	if ($group == "") {
	    $testLink = "<a href=\"group.php?group=\">Top level</a>";
	} else {
	    $testLink = "<a href=\"group.php?group=$group\">$group</a>";
	}

	$passLink = tfColor($nPass, ($nPass==$nTest));
	$failRate = "n/a";
	if ($nTest > 0) {
	    $failRate = 1.0 - 1.0*$nPass/$nTest;
	    $failRate = tfColor(sprintf("%.3f", $failRate), ($failRate == 0.0));
	}
	if ($lastUpdate > 0) {
	    $timestampStr = date("Y-m-d H:i:s", $lastUpdate);
	} else {
	    $timestampStr = "n/a";
	}
	
	$table->addRow(array($testLink, $timestampStr,
			     $nTestSets, $nTestSetsPass, $nTest, $passLink, $failRate));
    }
    return $table->write();
    
}

#function writeTable_SummarizeAllGroups() {
#    $groups = getGroupList();
#    $tables = "";
#    foreach ($groups as $group=>$n) {
#	if ($group == "") {
#	    $tables .= "<br/><h2>Top level tests</h2><br/>\n";
#	} else {
#	    $tables .= "<br/><h2>Test groups</h2><br/>\n";
#	}
#	$tables .= writeTable_SummarizeAllTests($group);
#    }
#    return $tables;
#}

function displayTable_SummarizeAllTests() {
    echo writeTable_SummarizeAllTests($group);
}



function writeTable_Logs() {

    $testDir = getDefaultTest();
    $db = connect($testDir);

    # first get the tables ... one for each ccd run
    $cmd = "select name from sqlite_sequence where name like 'log%'";
    $dbtables = $db->query($cmd);

    # make links at the top of the page
    $tables = "";
    $ul = new UnorderedList();
    foreach ($dbtables as $dbtable) {

	$name = $dbtable['name'];
	$ul->addItem("<a href=\"#$name\">$name</a>");
	
	$cmd = "select * from $name";
	$logs = $db->query($cmd);

	$tables .= "<h2 id=\"$name\">$name</h2><br/>";
	
	$table = new Table("width=\"80%\"");
	$table->addHeader(array("Module", "Message", "Date", "Level"));
	foreach ($logs as $log) {

	    # check for tracebacks from TestData
	    $module = $log['module'];
	    $msg = $log['message'];
	    if (ereg("testQA.TestData$", $module)) {
		# get the idString from the message
		$idString = preg_replace("/:.*/", "", $msg);
		$module .= " <a href=\"backtrace.php?label=$idString\">Backtrace</a>";
	    }
	    $table->addRow(array($module, $msg, $log['date'], $log['level']));
	}
	$tables .= $table->write();
    }
    $contents = "<h2>Data Used in This Test</h2><br/>" . $ul->write() . "<br/><br/>";
    return $contents . $tables;
}

function displayTable_Logs() {
    echo writeTable_Logs();
}


function writeTable_EupsSetups() {

    $testDir = getDefaultTest();
    $db = connect($testDir);

    # first get the tables ... one for each ccd run
    $cmd = "select name from sqlite_sequence where name like 'eups%'";
    $dbtables = $db->query($cmd);

    # make links at the top of the page
    $tables = "";
    $ul = new UnorderedList();
    foreach ($dbtables as $dbtable) {

	$name = $dbtable['name'];
	$ul->addItem("<a href=\"#$name\">$name</a>");
	
	$cmd = "select * from $name";
	$logs = $db->query($cmd);

	$tables .= "<h2 id=\"$name\">$name</h2><br/>";
	
	$table = new Table("width=\"80%\"");
	$table->addHeader(array("Product", "Version", "Timestamp"));
	foreach ($logs as $log) {
	    $table->addRow(array($log['product'],$log['version'],date("Y-m-d H:i:s", $log['entrytime'])));
	}
	$tables .= $table->write();
    }
    $contents = "<h2>Data Sets Used in This Test</h2><br/>" . $ul->write() . "<br/><br/>";
    return $contents . $tables;

}

function displayTable_EupsSetups() {
    echo writeTable_EupsSetups();
}



?>