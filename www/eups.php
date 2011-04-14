<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new Menu();
$page = new Page("LSST Pipetest", "EUPS: setup products", $menu);

$page->appendContent(writeTable_EupsSetups());

echo $page;
