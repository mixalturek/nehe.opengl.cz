<?php
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'tut_obsah.php';
header("Location: http://$host$uri/$extra");
exit();
?>
