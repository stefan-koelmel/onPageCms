<?php // charset:UTF-8
    $useDatabase = "cmsTop";
    switch ($useDatabase) {
        case "sk" :
            $host     = "db1229.1und1.de";
            $user     = "dbo360967548";
            $database = "db360967548";
            $password = "nmzu70wsx";
            break;

        case "cmsTop" :
            $host     = "localhost";
            $user     = "web6";
            $database = "usr_web6_1";
            $password = "nmzu70wsx";
            break;
    }
    @$link = mysql_connect($host, $user, $password);
    @mysql_select_db($database, $link);      
?>