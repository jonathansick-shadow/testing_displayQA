<?php
if (true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
include_once("Menu.php");
include_once("Page.php");
include_once("libdisplay.php");

$menu = new Menu();
$page = new Page(getDefaultTitle(), "Help", $menu);


# Now write the main help content page.
include_once("Html.php");


$readme = join("", file("README"));
$page->appendContent("<pre>\n$readme\n</pre>\n");

########################################
# write the page
echo $page;
