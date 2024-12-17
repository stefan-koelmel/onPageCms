<?php
    $db = "cmsTop2";
    switch ($db) {
        
        case "cmsTop2" :
            $dbHost     = "localhost";
            $dbUser     = "web6";
            $dbDatabase = "usr_web6_1";
            $dbPassword = "nmzu70wsx";
            break;
        
        default :
            $dbHost = "localhost";
            $dbUser = "web12";
            $dbPassword = "nmzu70wsx";
            $dbDatabase = "usr_web12_1";
    }
    
    @$link = mysql_connect($dbHost, $dbUser, $dbPassword);
    @mysql_select_db($dbDatabase, $link);
    
    
       
    
?>

