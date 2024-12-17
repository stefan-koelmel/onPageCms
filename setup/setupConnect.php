<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    $user = $_GET[user];
    $pass = $_GET[pass];
    
    $out = 0;
    $cmsVersion = "base";
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_connect.php");
    
    
    $query = "SELECT * FROM `cms_testUser` WHERE `email` = '$user' AND `password` = '$pass' ";
    $result = mysql_query($query);
    if (!$result) {
        echo "no Connection to Database <br>";
        if ($out) echo ("$query<br>");
        die();
    }
    
    $anz = mysql_num_rows($result);
    if ($anz == 1) {
        if ($out) echo ("User gefunden !<br />");
        $userData = mysql_fetch_assoc($result);
        if (is_array($userData)) {
            $userId = $userData[id];
            echo ($userId);
        }        
        die();
    }
    
    if ($anz > 1) {
        echo ("Benutzer nicht eindeutig<br />");
        die();
    }
    
    
    // echo ("Anzahl gefunden $anz <br /> ");
   
    if ($out) echo ("User nicht mit email gefunden <br/>$query<br/> ");
    
    // check with email
    $query = "SELECT * FROM `cms_testUser` WHERE `userName` = '$user' AND `password` = '$pass' ";

    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 1) {
        if ($out) echo ("User gefunden !<br />");
        $userData = mysql_fetch_assoc($result);
        if (is_array($userData)) {
            $userId = $userData[id];
            echo ($userId);
        }        
        die();
    }
    
    if ($anz > 1) {
        echo ("Mehrere User gefunden <br />");
        die();
    }
    if ($out) echo ("Anzahl gefunden $anz <br /> ");
   
    if ($out) echo ("User nicht mit username gefunden <br/>$query<br/> ");
    
    echo ("notFound");
    
    
?>
