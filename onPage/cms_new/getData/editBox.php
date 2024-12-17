<?php
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    $out = "";
    global $cmsVersion,$cmsName;
    $cmsVersion = $_GET[cmsVersion];
    $cmsName = $_GET[cmsName]; // "skCom";// $_GET[cmsName];
    // $cmsVersion = $_GET[cmsVersion];
    include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php");


    
    // include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms_editBox.php");
    $out .= cmsEditBox_getContent($_GET);
    
    echo ($out);
    
    
    

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
