<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

# echo phpinfo();
$menu = new Menu();
$page = new Page(getDefaultTitle(), getDefaultH1(), $menu);

$page->appendcontent("<h2>Failure Rates in Percent</h2>\n");
$page->appendContent(nGroupToggle());
$page->appendContent(writeTable_listTestResults());
$page->addSidebar(writeTable_timestamps());
$page->addsidebar(writeTable_summarizeMetadata(array("dataset")));

echo $page;
