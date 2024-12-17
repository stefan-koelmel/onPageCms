<?php
    $host = "localhost";
    $user = "web6";
    $password = "nmzu70wsx";
    $database = "usr_web6_1";

    @$link = mysql_connect($host, $user, $password);
    // echo ("LINK $link<br/>");
    @mysql_select_db($database, $link);
        
?>

