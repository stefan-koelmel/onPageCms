<?php
    session_start();
    foreach ($_GET as $key => $value) {
        $old = $_SESSION[$key];
        $_SESSION[$key] = $value;
        echo ("SET SESSION [$key] From $old to $value <br>");
    }
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
