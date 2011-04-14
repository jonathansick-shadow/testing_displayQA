<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}

include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new Menu();
$page = new Page("LSST Pipetest", "Test Logs", $menu);
$page->appendContent(writeTable_Logs());

echo $page;
