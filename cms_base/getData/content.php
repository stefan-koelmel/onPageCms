<?php // charset:UTF-8
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');


    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php";
    // echo ("cmsFile = $cmsFile <br>");
    
    include($cmsFile);
    
    $getData = array();

    $viewMode = "content";
    
    foreach ($_GET as $key => $value) {
        switch ($key) {
            case "cmsName" : break;
            case "cmsVersion" : break;
            case "width" : $frameWidth = $value; break;
            case "frameWidth" : $frameWidth = $value; break;
            case "view"  : $viewMode = $value; break;
            default :
                // echo ("Not Set $key => $value <br>");
                $getData[$key]= $value;
                
        }
    }
    
    foreach ($_SERVER as $key => $value ) {
        //echo ("$key = $value <br>");
    }
    
    $contentData = cms_content_get($getData);
    if (!is_array($contentData)) {
        echo ("KEIN INHALT GEFUNDEN $contentData <br>");
        die();
    }

    switch ($viewMode) {
        case "content" :
            cms_contentType_show($contentData,$frameWidth);
            break;
        case "editContent" :
            $res = cms_content_edit($contentData,$frameWidth);
            // echo ("Edit Result = $res <br>");

            echo ($res[outPut]);
            // foreach ($res as $key => $value ) echo ("$key = <br>");
            break;
        default :
            echo  ("unkown $viewMode in cms_base/getData/content.php");
    }
   
    
    
    

    
?>
