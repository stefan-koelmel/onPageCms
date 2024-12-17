<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    global $cmsName,$cmsVersion;
    if ($_GET[cmsName]) $cmsName = $_GET[cmsName];
    if ($_GET[cmsVersion]) $cmsVersion = $_GET[cmsVersion];
    //$cmsName = $_SESSION[cmsName];
    /*echo ("<h1>SESSION = $_SESSION[cmsName]</h1>");
    echo ("<h1>GLOBAL  = $GLOBALS[cmsName]</h1>");
    echo ("<h1>CMSNAME = $cmsName </h1>");
    foreach ($_SESSION as $key => $value) echo ("SES $key = $value <br>");*/
    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php";
    include($cmsFile);
    $name = $_GET[name];
    $frame = $_GET[frame];
    $type = $_GET[type];
    $frameWidth = $_GET[frameWidth];
    $getData = $_GET;

    //global $cmsName;
    //$cmsName = cms_getCmsName();

    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_sitemap.php");
   
    // foreach ($_GET as $key => $value) echo ("GET $key = $value <br>");
    cmsSiteMap_Edit($getData);
    //echo("Show Content for Name '$name' with frame= '$frame' <br>");
    //return "jkhkjhkj";*/

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
