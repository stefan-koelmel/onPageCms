<?php
    session_start();
    
    $sessionType = $_SESSION[sessionType];
    if ($sessionType == "cmsName") {
        $cmsName = $_GET[cmsName];
        if (!$cmsName) echo ("NO CMS_NAME <br>");
        if ($cmsName) {
            echo ("SET SESSION for CMSNAME $cmsName <br />");
        
            foreach ($_GET as $key => $value ) {
                if ($key == "cmsName") continue;
                
                $old = $_SESSION[$cmsName."_session"][$key];
                $_SESSION[$cmsName."_session"][$key] = $value;
                echo ("SET SESSION [$key] From $old to $value for $cmsName <br>");
            }            
        }
        
    } else {
    
        foreach ($_GET as $key => $value) {
            if ($key == "cmsName") continue;
            $old = $_SESSION[$key];
            $_SESSION[$key] = $value;
            echo ("SET $sessionType SESSION [$key] From $old to $value <br>");
        }
    }
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
