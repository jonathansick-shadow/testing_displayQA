<?php

include_once("Html.php");

class Menu {

    protected $_tabs = array();
    
    public function __construct() {

	# a dictionary entry for each tab.
	# contains: (1) text on the tab, (2) path to link
	$this->_tabs["home"] = array("Home", "index.php");
	$this->_tabs["group"] = array("Group", "group.php");
	$this->_tabs["summ"] = array("Summary", "summary.php");
	$this->_tabs["log"]  = array("Logs",    "logs.php");
	$this->_tabs["sdqa"] = array("SDQA",    "sdqa.php");
	$this->_tabs["eups"] = array("EUPS",    "eups.php");
	$this->_tabs["help"] = array("Help",    "help.php");

	
    }

    
    public function __toString() {

	$uri = $_SERVER['REQUEST_URI'];
	
	## need to determine which page we're on
	## so we can highlight the appropriate tab
	$selected = array();
	$found = false;
	foreach (array_keys($this->_tabs) as $key) {
	    list($label, $path) = $this->_tabs[$key];
	    $path = preg_replace("/.*\//", "", $path);
	    $selected[$key] = false;
	    if ($path and ereg($path, $uri)) {
		$selected[$key] =  true;
		$found = true;
	    }
	}
	if (!$found) {
	    $selected["home"] = true;
	}

	$ul = new UnorderedList("id=\"nav\"");
	foreach (array_keys($this->_tabs) as $key) {
	    list($label, $path) = $this->_tabs[$key];
	    $ul->addItem("<a href=\"$path\">$label</a>", $selected[$key] ? "id=\"selected\"" : "");
	}
	$menu = "<div id=\"menu\">\n".$ul->write()."</div>\n";
    	return $menu;
    }

    public function write() { return $this->__toString(); }

  }


class SpecificTestMenu extends Menu {
    
    public function __construct() {
	parent::__construct();
	$this->_tabs["backtrace"] = array("Backtrace", "backtrace.php");
    }
}


