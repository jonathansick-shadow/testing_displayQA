<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");

$menu = new Menu();
$page = new Page("LSST Pipetest", "SDQA Information", $menu);
$page->appendContent("Not Yet Implemented.\n");

echo $page;
