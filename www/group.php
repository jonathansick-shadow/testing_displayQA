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

$group = getGroup();
$page->appendContent("<h2>Group: $group</h2><br/>");
$page->appendContent(writeTable_SummarizeAllTests());

$page->addSidebar(writeTable_timestamps($group));

echo $page;
