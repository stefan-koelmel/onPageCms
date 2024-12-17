<?php
    $host     = "localhost";
    $user     = "web723";
    $database = "usr_web723_1";
    $password = "qFnlbX1l";

    @$link = mysql_connect($host, $user, $password);
    @mysql_select_db($database, $link);    
?>