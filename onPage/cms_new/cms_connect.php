<?php // charset:UTF-8
    $useDatabase = "cmsTop2";
    switch ($useDatabase) {
        case "sk" :
            $dbHost     = "db1229.1und1.de";
            $dbUser     = "dbo360967548";
            $dbDatabase = "db360967548";
            $dbPassword = "nmzu70wsx";
            break;

        case "cmsTop" :
            $dbHost     = "localhost";
            $dbUser     = "web6";
            $dbDatabase = "usr_web6_1";
            $dbPassword = "nmzu70wsx";
            break;
        case "cmsTop2" :
            $dbHost     = "localhost";
            $dbUser     = "web6";
            $dbDatabase = "usr_web6_1";
            $dbPassword = "nmzu70wsx";
            break;
    }  
   
    @$link = mysql_connect($dbHost, $dbUser, $dbPassword);
    @mysql_select_db($dbDatabase, $link);      
?>