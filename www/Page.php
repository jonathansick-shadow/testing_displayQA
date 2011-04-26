<?php
include_once("Html.php");

class Page {

    private $_css = "style.css";
    private $_content = array();
    private $_title;
    private $_h1;
    private $_menu;
    private $_sidebars = array();

    public function __construct($title, $h1, $menu) {
	$this->_title = $title;
	$this->_h1    = $h1;
	$this->_menu  = $menu;
    }

    public function appendContent($content) {
	$this->_content[] = $content;
    }

    public function __toString() {

	# build the htmlheader
	$s = $this->_docType();
	$s .= $this->_htmlhead();

	# add the body
	$s .= "<body>\n";

	########################
	# header and menu
	$hDiv = new Div("id=\"header\"");
	$hDiv->append("<h1>$this->_h1</h1>");
	$hDiv->append($this->_menu->write());

	
	########################
	# content
	$contentDiv = new Div("id=\"content\"");

	# main content id=right
	$rightDiv = new Div("id=\"right\"");
	foreach($this->_content as $content) {
	    $rightDiv->append($content);
	}

	# sidebar content id=left
	$leftDiv = new Div("id=\"left\"");
	if (count($this->_sidebars) == 0 ) {
	    $this->getLinks();
	    $this->getAttribution();
	}
	$leftDiv->append($this->_getSidebars());
	
	# push right and left content on
	$contentDiv->append($rightDiv->write());
	$contentDiv->append($leftDiv->write());

	
	$s .= $hDiv->write();
	$s .= $contentDiv->write();
	$s .= "</body>\n";
	$s .= "</html>\n";
	return $s;
    }

    public function addSidebar($content, $title="") {
	$d = new Div("class=\"box\"");
	if (strlen($title) > 0) {
	    $d->append("<h2>$title</h2>\n");
	}
	$d->append($content);
	$this->_sidebars[] = $d;
    }

    public function getLinks() {

	# create the list of links
	$ul = new UnorderedList();
	$ul->addItem("<a href=\"http://lsstcorp.org\">LSST Home</a>");

	$this->addSidebar($ul->write(), "Links: ");
    }

    public function getAttribution() {
	$d = new Div("style=\"font-size: 0.8em;\"");
	$d->append("Original design by <a href=\"http://www.minimalistic-design.net\">Minimalistic Design</a>");
	$this->addSidebar($d->write());
    }
    
    private function _docType() {
	$s = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"".
	    "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
	"<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
	return $s;
    }
    
    private function _htmlhead() {
	$title = $this->_title;
	$css   = $this->_css;
	if (! file_exists($css) ) {
	    $css = "../$css";
	}
	$s = "<head>\n".
	    "<title>$title</title>\n".
	    "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\" media=\"screen\" />\n".
	    "</head>\n";
	return $s;
    }
    

    private function _getSidebars() {
	$out = "";
	foreach ($this->_sidebars as $sidebar) {
	    $out .= $sidebar->write();
	}
	return $out;
    }
    
    	
  }

