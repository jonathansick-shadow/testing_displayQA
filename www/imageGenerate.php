<?php

$path      = $_GET['imgen_path'];
if (! file_exists($path) || filesize($path) < 10) {
    system($path.".sh 2>&1", $output);
}

header('Content-Type:image/png');
echo file_get_contents($path);




