<?php // charset:UTF-8

function cms_layout_getLayout($layoutName) {
    $cmsName = $GLOBALS[cmsName];
    if (!$cmsName) {
        // echo ("no cmsName in get_layout = '$GLOBALS[cmsName]' <br />");
        if ($GLOBALS[pageInfo][cmsName]) {
            $cmsName = $GLOBALS[pageInfo][cmsName];
            // echo ("Take from PageInfo $cmsName <br />");
        }

        if (!$cmsName AND $_SESSION[cmsName]) {
            $cmsName = $_SESSION[cmsName];
            // echo ("get cmsName fro session => $cmsName <br />");
        }
    }

    $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `name` = '$layoutName' ";

    $result = mysql_query($query);
    if ($result) {
        $anz = mysql_num_rows($result);
        if ($anz == 0) {
            cms_errorBox("keine LayoutDaten erhalten für '$layoutName'");
            return array();
        }
        if ($anz > 1) {
            cms_errorBox("keine eindeutigen LayoutDaten erhalten für '$layoutName'<br />$query");
            return array();
        }
        $layoutData = mysql_fetch_assoc($result);
        return $layoutData;
    }
    cms_errorBox("Fehler bei LayoutDaten <br />$query");
    return array();    
}

function cms_layout_layoutList() {
    $cmsName = $GLOBALS[cmsName];
    if (!$cmsName) {
        echo ("no cmsName in cms_layout_layoutList = '$GLOBALS[cmsName]' <br />");
        if ($GLOBALS[pageInfo][cmsName]) {
            $cmsName = $GLOBALS[pageInfo][cmsName];
            echo ("  Take from PageInfo $cmsName <br />");
        }

        if (!$cmsName AND $_SESSION[cmsName]) {
            $cmsName = $_SESSION[cmsName];
            echo ("  get cmsName fro session => $cmsName <br />");
        }
    }


    $res = array();
    $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `name` LIKE 'layout_%' ";
    $result = mysql_query($query);
    if (!$result) {
        $out = "Fehler beim Abfragen der Layouts";
        if ($_SESSION[userLevel]>=9) $out .= "Query = $query";
        cms_errorBox($out);
        return $res;
    }

    while ($layout = mysql_fetch_assoc($result)) {
        $id = $layout[id];
        $name = $layout[name];
        $title= $layout[title];

        $res[$name] = array("name"=>$title);
    }
    return $res;
}


function cms_layout_SelectLayout($type,$dataName) {
    $typeList = cms_layout_layoutList();
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";

     $str.= "<option value='0'";
     if ($code == $type)  $str.= " selected='1' ";
     $str.= ">Default</option>";

    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";
    return $str;
}


function cms_layout_show($layoutName,$pageWidth,$pageData) {
    global $cmsVersion,$cmsName;
    $divData = array();
    $divData[style] = "width:".$pageWidth."px;";
    $divData[cmsName] = $cmsName;
    $divData[cmsVersion] = $cmsVersion;
    
    global $pageData,$pageInfo;
    $pageId = $pageData[id];

    $showLevel = $_SESSION[showLevel];
    if (!$showLevel) $showLevel = 0;
    
    $contentList = cms_content_getList($layoutName); //"page_$pageId");

    $showEdit = $_SESSION[edit];
    if ($showEdit) {   
        // if ($_POST) cms_layout_savePost($contentList);
        
        $showAddContent = 1;
        if ($showLevel >= 7) $editAble = 1;
    }

    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    if ($showEdit) {
        switch ($cmsEditMode) {
            case "siteBar" :
                echo ("<div class='cmsEditMainFrame' style='width:".(400+20+$pageWidth)."px;' >");
                $res = cmsEditBox_show();
                echo ("$res");
                $divData[style] .= "float:left;";
                break;
            case "onPage2" :
                echo ("<div class='layoutEditFrame'>");
                $divData[style] .= "float:left;";
                break;
        }
    }
    
    div_start("LayoutFrame",$divData);
    
    $editLayout = $_GET[editLayout];
    if ($editAble and $showEdit AND !$editLayout ) {
        cms_Layout_showEditPageData($cmsEditMode);        
    }
    
    
    // echo ("cms_layout_show($layoutName,$pageWidth,$pageData)<br />");
    global $pageData,$pageInfo;
    $pageId = $pageData[id];

    $showLevel = $_SESSION[showLevel];
    if (!$showLevel) $showLevel = 0;
    
    $contentList = cms_content_getList($layoutName); //"page_$pageId");

    $showEdit = $_SESSION[edit];
    if ($showEdit) {
        $showAddContent = 1;
    }
    $maxSort = 0;
    $editLayout = $_GET[editLayout];
    if ($editLayout) {
        div_start("cmsEditLayout","background-color:#cff;padding:10px 0 10px 0;");
        echo ("Edit Layout $editLayout <a href='$pageInfo[page]' class='cmsLinkButton'>Layout schließen</a><br />");
    }
    if (count($contentList) > 0 ) {

        // Sortierung Manuell geändert
        $res = cms_content_change_sort($contentList);
        if ($res == 1) {
            echo ("Sort Change <br />");
            $goPage = $pageInfo[page];
            if ($_GET[editLayout]) $goPage.="?editLayout=".$_GET[editLayout];
            reloadPage($goPage);
            return 1;
        }


        $sortCheck = 0;
        for ($i=0;$i<count($contentList);$i++) {

            $contentData = $contentList[$i];

            // sortierung Korreturen - Automatisch
            $sort = $contentData[sort];
            $sortCheck++;
            if ($sortCheck != $sort) {
                // echo "Fehler in Sort || sollte : $sortCheck | ist $sort <br />";
                cms_content_changeSort($contentData[id],$sortCheck);
            }

            if ($sort>$maxSort) $maxSort = $sort;
            $contentShowLevel = $contentData[showLevel];
            if ($contentShowLevel <= $showLevel) {
                 if ($editLayout) {
                     $contentType = $contentData[type];
                     if ($contentType == "content") cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
                     else {
                         // echo (substr($contentType,0,5)."<br />");
                         if (substr($contentType,0,5)=="frame") {
                             cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
                         } else {
                             //echo (substr($contentType,0,5)."<br />");
                         }
                     }
                     //foreach($contentData as $key => $value) echo ("$key => $value -- ");
                    
                 } else {
                    cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
                 }
                
            }

        }

    } else {
        echo ("Kein Inhalt für dieses Layout($layoutName) verfügbar $pageId<br />");
        echo ("<a href='admin_cmsLayout.php?editLayout=$layoutName' class='cmslinkButton'>Layout bearbeiten</a>");
        $showAddContent = 0;
    }
    if ($editLayout) {
        div_end("cmsEditLayout","before");
    }

    div_end("LayoutFrame","before");
    
    if ($showEdit) {
        switch ($cmsEditMode) {
            case "sideBar" :
                echo ("<div style='clear:both;'></div>");
                echo ("</div>");
                break;
            case "onPage2" :
                //echo ("<div style='width:auto;background-color:#f00;float:left;'>");
                cmsModul_show();
                // echo ("</div>");
                echo ("<div style='clear:both;'></div>");
                echo ("</div>");
                break;
        }
        
        
    }
}



function cms_Layout_showEditPageData($cmsEditMode) {
    
    $editMode = $_GET[editMode];


    $mode = "old";
    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    switch ($cmsEditMode) {
        case "onPage"  : $mode = "old"; break;
        case "onPage2" : $mode = "new"; break;
        case "siteBar" : $mode = "new"; break;
        default:
            echo("unkown $cmsEditMode <br>");
    } 
    


    switch ($mode) {
        case "new" :
             if ($editMode == "pageData") {
                echo ("<div class='cmsContentEditFrame cmsContentEditPage '>");
                cms_page_editData($pageData,$pageInfo);
                echo ("</div>");
            } else {
                echo ("<div class='cmsEditBox' >");
                $goPage = $GLOBALS[pageInfo][page]."?editMode=pageData";
            // Roll Image
                echo ("<div class='cmsContentFrame_editPageButton' >");
                echo ("<a href='$goPage'>");
                
                echo ("<img src='/cms_base/cmsImages/cmsEditPage.png' border='0px'>");
                echo ("</a>");
                echo ("</div>");
                echo ("Seite Editieren");

                echo ("</div>");
            }
            break;

        case "old" :
            div_start("cmsPageLine","width:".$pageWidth."px;");
            echo ("Seite: $pageData[title] ");

            if ($editMode == "pageData") {
                cms_page_editData($pageData,$pageInfo);
            } else {
                echo ("<a href='$infoData[page]?editMode=pageData'>Seiten Information editieren</a><br>");
            }
            div_end("cmsPageLine","before");
            break;
    }
}






function cms_layout_showFrameContent($layoutName,$showName,$width) {
    // echo ("cms_layout_showFrameContent($layoutName,$showName,$width)<br />");

    global $pageData,$pageInfo;
    $pageId = $pageData[id];

    $showLevel = $_SESSION[showLevel];
    if (!$showLevel) $showLevel = 0;

    $contentList = cms_content_getList($showName); //"page_$pageId");
    $editLayout = $_GET[editLayout];

    $showEdit = $_SESSION[edit];
    if ($showEdit) {
        $showAddContent = 1;
    }
    $maxSort = 0;

    if (count($contentList) > 0 ) {
        $sortCheck = 0;
        for ($i=0;$i<count($contentList);$i++) {

            $contentData = $contentList[$i];
            $contentType = $contentData[type];
            if ($editLayout) { // Layout Editieren 
                if ($contentType == "content") {
                     cms_layout_showContentTypes($layoutName,$contentData,$width);
                }
            } else { // normale FRAME Inhalt - nicht Layout
                $contentShowLevel = $contentData[showLevel];
                if ($contentShowLevel <= $showLevel) {
                     cms_layout_showContentTypes($layoutName,$contentData,$width);
                }
            }
        }

    } else {
        echo ("Kein Inhalt für diese Seite verfügbar<br />");
    }
    
    /*if ($showAddContent) {
        // echo ("MaxSort = $maxSort<br />");
        $maxSort++;
        cms_content_add($pageorFrame,$maxSort);
    }*/





}


function cms_layout_showContentTypes ($layoutName,$contentData,$frameWidth) {
    $type = $contentData[type];
    $data = $contentData[data];


    $cmsEditMode = $GLOBALS[cmsSettings][editMode];

    
    if (is_string($data)) $data = str2array($data);
    if(!is_array($data)) $data = array();

    $edit = $_SESSION[edit];
    if ($edit) {
       $contentData[layout] = $layoutName;
       cms_contentType_head($contentData,$frameWidth);
    }
    $contentData[layout] = $layoutName;



    $ownFrameWidth = cms_getWidth($contentData[frameWidth],$frameWidth);
    $ownFrameHeight = cms_getWidth($contentData[frameHeight],$frameWidth);

    if ($ownFrameWidth) $frameWidth = $ownFrameWidth;

    $frameStyle = $contentData[frameStyle];
    $frameSettings = cmsFrame_getSettings($frameStyle);
    $border = $frameSettings[border];
    $padding = $frameSettings[padding];
    $spacing = $frameSettings[spacing];

    $frameWidth = $frameWidth - (2*$border) - (2*$padding);
    $divStyle = "width:".$frameWidth."px;";
    if ($ownFrameHeight) $divStyle .= "height:".$ownFrameHeight."px;";
    $divData = array("style"=>$divStyle);
    $divName = "cmsContentFrame_$contentId";
    $id = $contentData[id];
    $divName .= " ".$type."_box ".$type."_boxId_$id ".$type."_boxPage_".$GLOBALS[pageInfo][pageName];

    if ($frameStyle) $divName .= " $frameStyle";

    $showPlus = 0;
    if ($type == "content" AND $edit AND $cmsEditMode == "onPage2") {
        
        $showPlus = "add";
        if (substr($GLOBALS[pageInfo][page],0,5)=="admin") $showPlus = 0;
        
        if ($showPlus) {
            // div_start("cmsContentFramePlus","width:".($frameWidth+240)."px;");
            // $divData[style] .= "float:left;margin-right:10px;";
        }
    }

    
    div_start($divName,$divData);
    
    
    switch ($type) {
        case "text"     : cms_contentType_Text($contentData,$frameWidth); break;
        case "image"    : cmsType_Image($contentData,$frameWidth); break;
        case "frame1"   : 
            $contentData[dontShowDummy] = 1;
            cmsType_Frame($contentData,$frameWidth,1); 
            break;
        case "frame2"   : 
            $contentData[dontShowDummy] = 1;
            cmsType_Frame($contentData,$frameWidth,2); 
            break;
        case "frame3"   : 
             $contentData[dontShowDummy] = 1;
            cmsType_Frame($contentData,$frameWidth,3); 
            break;
        case "frame4"   : 
             $contentData[dontShowDummy] = 1;
            cmsType_Frame($contentData,$frameWidth,4); 
            break;
        case "login"    : cmsType_Login($contentData,$frameWidth); break;
        case "ownPhp"   : cmsType_ownPhp($contentData,$frameWidth); break;
        case "dateList" : cmsType_dateList($contentData,$frameWidth); break;

        case "header"   : cmsType_header($contentData,$frameWidth); break;
        case "navi"     : cmsType_navi($contentData,$frameWidth); break;
        case "social"   : cmsType_social($contentData,$frameWidth); break;
        case "content"  : cms_layout_showContent($frameWidth); break;
        case "footer"   : cmsType_footer($contentData,$frameWidth); break;

        case "flip"     : cmsType_flip($contentData,$frameWidth); break;
        case "contentName" : cms_contenType_contentName($contentData); break;
        
        default :
            echo ("<h1>HEIER </h1>");
            if (function_exists("cmsType_".$type)) {
                call_user_func("cmsType_".$type, $contentData,$frameWidth);
            } else {
            

                echo ("unkown Type $type<br />");
                foreach ($contentData as $key => $value) echo ("#$key = $value **");
                echo ("<br />");
            }
    }
    div_end($divName);
    echo ("<div id='spacerId_$contentId' class='spacer spacerLayout spacer$type'>&nbsp;</div>");
    
}



function cms_layout_showContent($frameWidth) {
    $pageData = $GLOBALS[pageData];
    $pageInfo = $GLOBALS[pageInfo];
    //show_array($pageData);
    // echo ("HIER INHALT OF $pageInfo[pageName]<br />");
    $divData = array();
    $divData[style] = "width:".$frameWidth."px;";
    
    if ($_SESSION[edit]) {
        $divData["class"]= "dragFrame";
        $divData[id]="dragFrame_0";
    }
    
    $editLayout = $_GET[editLayout];
    if (!$editLayout) {
        cms_titleLine($pageData,$frameWidth);
    }
    
    
    $pageType = "page";
    switch ($pageInfo[pageName]) {
        case "sitemap" : $pageType = "sitemap"; break;
        case "admin" : $pageType = "admin"; $adminView=""; break;
        default :
            if (substr($pageInfo[pageName],0,6) == "admin_") {
                $adminView = substr($pageInfo[pageName],6);  
                $pageType = "admin";
            }            
    }
    
    switch ($pageType) {
        case "page" :
            // Dynamic Header
            $pageId = "page_$pageData[id]";
            $dynamicPageId = cms_dynamicPage_showTitleBar($pageData);
            if ($dynamicPageId) {
                $pageId = $dynamicPageId;
            }
            $GLOBALS[pageContentId] = $pageId;
            
            // Div ContentStart
            echo ("<div class='cmsContentStart cmsContentStart_hidden'>");
            echo ("&nbsp;");
            echo ("</div>");
            
            div_start("content",$divData); //"width:".$frameWidth."px;");
            // Spacer Content Start
            if ($_SESSION[edit]) $spacerAdd = "spacerDrop";
            echo ("<div class='spacer spacerContentStart $spacerAdd'>&nbsp;</div>");
     
            
            // show Content
            cms_content_show($pageId,$frameWidth);
           
            div_end("content","before");
            echo ("<div class='cmsContentEnd cmsContentEnd_hidden'>");
            echo ("&nbsp;");
            echo ("</div>");
            break;
            
        case "admin" :
            div_start("adminFrame");
            cms_admin_show($adminView,$frameWidth);
            div_end("adminFrame");
            break;
        
        case "sitemap" :
            global $cmsName,$cmsVersion;
            div_start("SitemapFrame"); // ,array("cmsName"=>$cmsName));
            echo ("<h1> Sitemap</h1>");

            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_sitemap.php");

            cms_sitemap_show();
            div_end("SitemapFrame");

            break;
            
    }
    
    
    
//    echo ("<div class='cmsContentStart cmsContentStart_hidden'>");
//    echo ("&nbsp;");
//    echo ("</div>");
//    
//    
//    div_start("content",$divData); //"width:".$frameWidth."px;");
//    // Spacer Content Start
//    if ($_SESSION[edit]) $spacerAdd = "spacerDrop";
//    echo ("<div class='spacer spacerContentStart $spacerAdd'>&nbsp;</div>");
//
//    switch ($pageInfo[pageName]) {
//        case "sitemap" :
//            global $cmsName,$cmsVersion;
//            div_start("SitemapFrame",array("cmsName"=>$cmsName));
//            echo ("<h1> Sitemap</h1>");
//
//            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_sitemap.php");
//
//            cms_sitemap_show();
//            div_end("SitemapFrame");
//
//            break;
//
//        case "admin" :
//            
//            $view = $_GET[view];
//            cms_admin_show($view,$frameWidth);
//            break;
//
//        default :
//            $normalPage = 1;
//            if (substr($pageInfo[pageName],0,6) == "admin_") {
//                $view = substr($pageInfo[pageName],6);
//                cms_admin_show($view,$frameWidth);
//                $normalPage = 0;              
//            }
//
//            if ($normalPage ) {
//                 // echo ("is Admin??? ".substr($pageInfo[pageName],0,6)."<br />");
//                cms_content_show("page_$pageData[id]",$frameWidth);
//            }           
//    }
//    div_end("content","before");
//    echo ("<div class='cmsContentEnd cmsContentEnd_hidden'>");
//    echo ("HIER ENDE OF $pageInfo[pageName]<br />");
//    echo ("</div>");

   // echo("<script src='/cms/cms_contentTypes/cmsType_flip.js'></script>");

}



?>
