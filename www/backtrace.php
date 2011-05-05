<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new SpecificTestMenu();
$page = new Page(getDefaultTitle(), getDefaultH1(), $menu);

$page->appendContent("<h2>".getCurrentUriDir()."</h2><br/>\n");
$page->appendContent(writeTable_OneTestResult($_GET['label']) . "<br/>\n");
$page->appendContent(write_OneBackTrace($_GET['label']));

echo $page;
