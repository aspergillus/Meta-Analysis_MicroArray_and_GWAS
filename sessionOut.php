<?php
Session_start();
Session_destroy();
header("refresh: 2; url=miArrayindex.php");
?>