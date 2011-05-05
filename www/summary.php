<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new Menu();
$page = new Page(getDefaultTitle(), getDefaultH1(), $menu);

$page->appendContent("<h2>".getDefaultTest()."</h2><br/>\n");
$mapFigs = writeMappedFigures();
$figs = writeFigures();
$page->appendContent($mapFigs);
$page->appendContent($figs);
if (strlen($mapFigs.$figs) == 0) {
    $active = getActive();
    $page->appendContent("No Figures matching $active.  Select a valid sensor, or view <a href=\"summary.php?active=all\">all</a>.<br/><br/>");
}
$page->appendContent(writeTable_ListOfTestResults("."));

$page->addSidebar(writeTable_metadata());
$page->addSidebar(writeMappedFigures("navmap"));

echo $page;
