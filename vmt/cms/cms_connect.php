<?php // charset:UTF-8
    $host     = "localhost";
    $user     = "web6";
    $database = "usr_web6_1";
    $password = "nmzu70wsx";

    @$link = mysql_connect($host, $user, $password);
    @mysql_select_db($database, $link);
?>

