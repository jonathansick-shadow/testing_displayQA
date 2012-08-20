<?php
include_once("config.php");
if ($display_errors) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

# echo phpinfo();
$menu = new Menu();
$page = new Page(getDefaultTitle(), getDefaultH1(), $menu);

$page->appendContent(failureSelectionBoxes());
$page->appendContent(listFailures());

$page->addSidebar(writeTable_timestamps());
$page->addsidebar(writeTable_summarizeMetadata(array("dataset")));

echo $page;
