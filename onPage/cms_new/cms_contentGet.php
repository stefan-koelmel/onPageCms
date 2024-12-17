<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=iso-8859-1');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    include("cms.php");
    $name = $_GET[name];
    $frame = $_GET[frame];
    $type = $_GET[type];
    $frameWidth = $_GET[frameWidth];
    $getData = $_GET;
    foreach($_GET as $key => $value) {
        //echo ("GET #$key = $value <br>");
    }
    global $cmsName,$cmsVersion;
    $cmsVersion = $_GET[cmsVersion];
    $cmsName = $_GET[cmsName];

    $contentNameId = intval($_GET[contentNameId]);
    if ($contentNameId>0) {
        //echo ("<h1>contentNameId = $contentNameId </h1>");
        $name = $contentNameId;
        $pageData = cms_content_getId($contentNameId);
        $type = $pageData[type];
        if (substr($type,0,5)=="frame") {
            $frameAnz = intval(substr($type,5));
            $pageId = $pageData[pageId];
            for ($i=1;$i<=$frameAnz;$i++) {
                $FramePageId = "frame_".$contentNameId."_".$i;
                // echo ("Show Content of Frame $i $FramePageId <br>");
                cms_content_show($FramePageId,$frameWidth,$getData);
            }
            
            //echo ("Frame !!!!,$pageId $frameAnz");
        }
        // show_array($pageData);
        die();
    }
   
    // foreach ($_GET as $key => $value) echo ("$key = $value <br>");
    cms_content_show($name,$frameWidth,$getData);
    //echo("Show Content for Name '$name' with frame= '$frame' <br>");
    //return "jkhkjhkj";

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
