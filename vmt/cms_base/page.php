<?php // charset:UTF-8
function cms_page_show() {

    global $pageInfo,$pageData;
    
    cmsHistory_set($pageData);
    // echo ("Page is $pageInfo[page]");
    // foreach($pageInfo as $key => $value ) echo ("pI $key = $value <br>");

    $userLevel = $_SESSION[userLevel];

    if (!$userLevel) {
        $login = $_GET[login];
        if ($login=="1") {
            echo ("Sie sind angemeldet<br>");
            $_SESSION["userLevel"] = 9;
        } else {

        }
    }

    if ($userLevel > 0)  { // loggedIn
        if ($userLevel > 7) $editAble = 1;
    }
    $edit = $_SESSION[edit];
    $pageWidth = $GLOBALS[cmsSettings][width];

    $editLayout = $_GET[editLayout];
    // edit PageData
//    if ($editAble and $edit AND !$editLayout ) {
//        $editMode = $_GET[editMode];
//
//        div_start("cmsPageLine","width:".$pageWidth."px;");
//        echo ("Seite: $pageData[title] ");
//
//        if ($editMode == "pageData") {
//            cms_page_editData($pageData,$pageInfo);
//        } else {
//            echo ("<a href='$infoData[page]?editMode=pageData'>Seiten Information editieren</a><br>");
//        }
//        div_end("cmsPageLine","before");
//    }
    if (!is_array($GLOBALS[cmsSettings])) {
       
        $GLOBALS[cmsSettings] = cms_settings_get();
        // echo ("No Settings $GLOBALS[cmsName] - $GLOBALS[cmsSettings] <br>");
        $pageWidth = $GLOBALS[cmsSettings][width];
        // show_array($GLOBALS[cmsSettings]);
    }


    $layoutName = $GLOBALS[pageData][layout];
    if (!$layoutName) {
        // show_array($GLOBALS[cmsSettings]);

        if ($layoutName == 0 AND $GLOBALS[cmsSettings][layout]) {
            // echo ("TAke Standard Layout from Settinge ".$GLOBALS[cmsSettings][layout]."<br>");
            $layoutName = $GLOBALS[cmsSettings][layout];
        }
        //show_array($GLOBALS[pageData]);
        // echo ("No LayoutName $layoutName<br>");
        // $layoutName = $GLOBALS[cmsSettings][layoutName];
    }
    if ($layoutName) {
        $layoutData = cms_layout_getLayout($layoutName);
        
        cms_layout_show($layoutName,$pageWidth,$pageData);

        cmsType_addJavaScript();
       // echo("<script src='cms/cms_contentTypes/cmsType_flip.js'></script>");

       
        div_start("imagePreviewWindow",array("cmsName"=>$GLOBALS[cmsName]));
        div_end("imagePreviewWindow");


        div_start("imagePreviewContent");
        echo("&nbsp;");
        // echo ("<img src='' class='imagePreviewImage' alt='' width='0px' height='0px'>");
        div_end("imagePreviewContent");

        return 0;
    }


    echo ("LayoutName = $layoutName <br>");




    if ($pageData[breadcrumb]) {
       cms_titleLine($pageData,$pageWidth);
        


    }

    $pageWidth = 750;
    switch ($pageInfo[pageName]) {
        case "sitemap" :
            //include("cms/cms_sitemap.php");
            //echo ("Show Sitemap<br>");
            break;

         case "admin" :
             $view = $_GET[view];
             cms_admin_show($view);
            
            // echo ("Show Sitemap<br>");
            break;

        default :
            $normalPage = 1;
            if (substr($pageInfo[pageName],0,6) == "admin_") {
                $view = substr($pageInfo[pageName],6);
                cms_admin_show($view);
                $normalPage = 0;
                 echo ("Admin !!!");
            }
            
            if ($normalPage ) {
                 // echo ("is Admin??? ".substr($pageInfo[pageName],0,6)."<br>");
                 cms_content_show("page_$pageData[id]",$pageWidth);
            }
            
            //cms_page_showCms();
    }
    div_end("content","before");
  
    // echo("<script src='cms/cms_contentTypes/cmsType_flip.js'></script>");
   // echo("<script src='cms/cms_contentTypes/cmsType_Social.js'></script>");
}


?>
