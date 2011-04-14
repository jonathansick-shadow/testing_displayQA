<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new Menu();
$page = new Page("LSST Pipetest", "LSST Pipe Test Summary", $menu);

$page->appendContent("<h2>".getDefaultTest()."</h2><br/>\n");
$page->appendContent(writeTable_ListOfTestResults("."));
$page->appendContent(writeFigures("."));

echo $page;
