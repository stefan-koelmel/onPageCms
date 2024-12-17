<?php // charset:UTF-8
    //header('Content-Type: text/html; charset=UTF-8');
    session_start();
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

   // $query = utf8_decode($query);

    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $searchString = $_GET[searchString];
    $contentId    = $_GET[contentId];
    
  
    
    
     if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/cms/cms_connect.php")) {        
        include($_SERVER['DOCUMENT_ROOT']."/".$cmsName."/cms/cms_connect.php");
        // echo ("FOUND  /".$cmsName."/cms/cms_connect.php <br>");
    } else {
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php")) {        
            include($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php");
        
        } else {
            include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
            echo ("SUCHE /includes/connect.php<br>");
        }
    }
    
    $classList = array("cmsClass_content_base","cmsClass_content_language","cmsClass_content_editSelect","cmsClass_content_editType","cmsClass_content_edit","cmsClass_content_showData","cmsClass_content_show","cmsClass_content_data_show");
    //$classList = array("cmsClass_content_base","cmsClass_content_language","cmsClass_content_editType","cmsClass_content_edit","cmsClass_content_editSelect","cmsClass_content_showData","cmsClass_content_show","cmsClass_content_data_show");
    foreach ($classList as $key => $value) {
        $fn = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/".$value.".php";
        $exist = file_exists($fn);
        if ($exist) {
            // echo ("$value exist <br>");
            include($fn);
        } else {
            echo ("not exist $fn <br>");
        }
    }

    
    
    $contentTypeFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentType.php";
    if (!file_exists($contentTypeFile)) {
        echo ("Not exist ContentTypeFile<br>");
        die();
    }
    include($contentTypeFile);
    
    if (!intval($contentId)) {
        echo ("Keine ContentId erhalten $contentId <br>");
        die();
    }
    
    $pageFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_page.php";
    if (!file_exists($pageFile)) {
        echo ("Not exist PageFile<br>");
        die();
    }
    include ($pageFile);
    
    
    
    $contentFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_content.php";
    if (!file_exists($contentFile)) {
        echo ("Not exist ContentFile<br>");
        die();
    }
    include ($contentFile);
    
    $helpFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/help.php";
    if (!file_exists($helpFile)) {
        echo ("Not exist helpFile<br>");
        die();
    }
    include ($helpFile);
    
    $pageStylesFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/pageStyles.php";
    if (!file_exists($pageStylesFile)) {
        echo ("Not exist pageStylesFile<br>");
        die();
    }
    include ($pageStylesFile);
    
    
    
    // GET CONTENT DATA
    $contentData = cms_content_getId($contentId);
   
    $searchFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_contentTypes/cmsType_search.php";
    if (!file_exists($searchFile)) {
         echo "SearchFile '$searchFile not exist <br>";
         die();        
    }
    
    include($searchFile);
    $searchClass = cmsType_search_class();
   //  $myName = $searchClass->getName();    
    
    $str = $searchClass->show_result($searchString,$contentData,$frameWidth);
    if ($str) echo ($str);
    
    return 0;
    

    
?>
