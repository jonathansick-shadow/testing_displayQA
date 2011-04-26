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
#$page->appendContent(writeMappedFigures("."));
$page->appendContent(writeFigures("."));
$page->appendContent(writeTable_ListOfTestResults("."));

$page->addSidebar(writeTable_metadata());
$page->addSidebar(writeMappedFigures("."));

echo $page;
