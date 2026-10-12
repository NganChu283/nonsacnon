<?php
// Preserve existing gallery.php#ph1 links for the original project.
$_GET['id']=$_GET['project']??1;
require __DIR__.'/project.php';
