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
            cms_errorBox("keine LayoutDaten $cmsName erhalten für '$layoutName'<br>$query");
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
    $mobilPages = $GLOBALS[cmsSettings][mobilPages];
    if (!$mobilPages) $mobilPages = 0;
    if ($mobilPages) {
        $specialWidth = 0;

        $sizeList = array();
        $sizeList[iPhone] = array("Portrait"=>320,"Landscape"=>480);
        $sizeList[iPad]   = array("Portrait"=>1024,"Landscape"=>768);        

        $target_target = $_SESSION[target_target];

        if ($target_target == "Mobil") {
            $defaultTarget = "iPhone";
            // echo (" Target is $target_target defined by SiteBar $defaultTarget <br>");
            $target_target = $defaultTarget;
        }

        if ($target_target != "Pc") {
            $target_orientation = $_SESSION[target_orientation];
            // echo ("Target = $target_target Orientation = $target_orientation useWidth = $_SESSION[target_width] <br>");
            if (is_array($sizeList[$target_target])) {
                $specialWidth = $sizeList[$target_target][$target_orientation];
                // echo "SizeList Found / useWidth = $specialWidth <br>";
            }
        }



        if ($specialWidth AND $specialWidth < $pageWidth ) {
            // echo ("<b> SET WIDTH from $pageWidth to $specialWidth </b><br/>");
            $pageWidth = $specialWidth;
        }
    }   
    
    $divData[style] = "width:".$pageWidth."px;";
    
    
    global $pageData,$pageInfo,$pageShow,$pageEditAble;
    $pageId = $pageData[id];

    $myLevel = $_SESSION[showLevel];
    if (!$myLevel) $myLevel = 0;
    $myId = $_SESSION[userId];
    
    
    
    $showLevel = $pageData[showLevel];
    $subData = $pageData[data];
    $showContent = 1;
    
    
    $showEdit = $_SESSION[edit];
    if ($showEdit) {   
        $showAddContent = 1;
        if ($myLevel >= 7) $editAble = 1;
        
    } else {
        $pageName = $pageData[name];
        if (substr($pageName,0,6) == "admin_") {
            if ($myLevel>7) $showEdit = 1;
            // echo ("<h1>Show Edit because Admin $pageName</h1>");
        }
       
    }
    
    
    
    
    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    if ($pageEditAble ) {
        
        switch ($cmsEditMode) {
            case "siteBar" :
                echo ("<div class='cmsEditMainFrame' style='width:".(400+20+$pageWidth)."px;' >");
                $res = cmsEditBox_show();
                echo ("$res");
                $divData[style] .= "float:left;";
                break;
            case "onPage2" :
                echo ("<h1>HIER</h1>");
                echo ("<div class='layoutEditFrame' style='min-width:".($pageWidth+10+280)."px;' >");
                $divData[style] .= "float:left;";
                break;
        }
    }
    
    

    div_start("layoutFrame layoutCenter",$divData);
   
    
    
    $standardVersion = $GLOBALS[cmsVersion];
    global $defaultCmsVersion;
    if ($defaultCmsVersion) {
        div_start("testCmsVersion","background-color:#f00;display:block;padding:5px;color:#fff;font-size:14px;");
        $standardVersion = $defaultCmsVersion;        
        $testVersion = $_SESSION[cmsVersion];
        echo ("CMS VERSION <b>$testVersion</b> wird getestet - Standard CMS-Version ist <k>$standardVersion</k>");
        div_end("testCmsVersion");
    }
    
    
    
    $editLayout = $_GET[editLayout];
    if ($pageEditAble AND !$editLayout ) {
        cms_Layout_showEditPageData($cmsEditMode,$pageWidth);        
    }
    
    
    // echo ("cms_layout_show($layoutName,$pageWidth,$pageData)<br />");
    global $pageData,$pageInfo;
    $pageId = $pageData[id];

    
    
   
    if ($pageEditAble) {
        $showAddContent = 1;
    }
    
    $maxSort = 0;   
    if ($editLayout) {       
        $contentList = cms_content_getList($editLayout);
        // show_array($contentList);
        div_start("cmsEditLayout");
        echo ("Edit Layout <b>$editLayout</b> <a href='$pageInfo[page]' class='cmsLinkButton'>Layout schließen</a><br />");
        
        div_start("cmsContentStart cmsContentStart_hidden");
        echo ("&nbsp;");
        div_end("cmsContentStart cmsContentStart_hidden");
        
        if ($_POST) cms_content_savePost($editLayout);
         div_end("cmsEditLayout","before");
    } else {
        $contentList = cms_content_getList($layoutName); 
    }
    
    if (count($contentList) > 0 ) {
//         if ($editLayout) {
//             $newContentList = array();
//             foreach($contentList as $key => $value) {
//                 //  echo ("lay $key => $value <br />");
//                 $contId   = $value[id];
//                 $contType = $value[type];
//                 echo ("id = $contId / type = $contType <br>");
//                 if ($contType == "content") {
//                     $newContentList[] = $value;
//                 }
//                 if (substr($contType,0,5) == "frame") {
//                     $frameCount = intval(substr($contType,5));
//                     for ($i=1;$i<=$frameCount;$i++) {
//                         $contName = "frame_".$contId."_".$i;
//                         // echo ("Get Content for $contName <br>");
//                         $frameCont = cms_content_getList($contName);
//                         if (is_array($frameCont)) {
//                             for ($c=0;$c<count($frameCont);$c++) {
//                                 if ($frameCont[$c][type] == "content") {
//                                     $newContentList[] = $frameCont[$c];
//                                 }
//                             }
//                         }
//                     }
//                 }
//             }
//             if (count($newContentList)) {
//                $layoutContent = $contentList;
//                $contentList = $newContentList;
//             }
//         }
        

      
        $sortCheck = 0;
        div_start("dragFrame",array("id"=>"dragFrame_layout"));
        foreach ($contentList as $contentNr => $contentData) {
             // echo ("<content $contentNr = $contentData $contentData[type] <br />");
             $res = cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
//             if (is_object($res)) echo ("Result is Object <br> ");
//             else echo ("Result is '$res' <br>");
        }
    


//        for ($i=0;$i<count($contentList);$i++) {
//
//            $contentData = $contentList[$i];
//
//            // sortierung Korreturen - Automatisch
//            $sort = $contentData[sort];
////            $sortCheck++;
////            if ($sortCheck != $sort) {
////                // echo "Fehler in Sort || sollte : $sortCheck | ist $sort <br />";
////                cms_content_changeSort($contentData[id],$sortCheck);
////            }
//
////            if ($sort>$maxSort) $maxSort = $sort;
//            $contentShowLevel = $contentData[showLevel];
//
//            if ($contentShowLevel <= $showLevel) {
//                 if ($editLayout) {
//                     $contentType = $contentData[type];
//                     if ($contentType == "content") {
//                         // EIGENTLICHES LAYOUT ZEIGEN
//                         for($lay=0;$lay<count($layoutContent);$lay++) {
//                             $contentData = $layoutContent[$lay];
//                             if ($lay+1==count($layoutContent)) $contentData[last] = 1;
//                             // echo ("<h3>Show Cont Type = $contentData[type] sort = $contentData[sort] </h3>");
//                             cms_contentType_show($contentData,$pageWidth,$getData);
//                             // cms_layout_showContentTypes($contentData,$pageWidth,$getData);
//                         }
//
//                         // cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
//                     } else {
//                         if (substr($contentType,0,5)=="frame") {
//                             cms_layout_showContentTypes($layoutName,$contentData,$pageWidth);
//                         } else {
//                         }
//                     }
//                 } else {
//
//                     cms_layout_showContentTypes($layoutName,$contentData,$pageWidth,$showContent);
//                 }
//            }
//
//        }
        div_end("dragFrame");

    } else {
        echo ("Kein Inhalt für dieses Layout($layoutName) verfügbar $pageId<br />");
        echo ("<a href='admin_cmsLayout.php?editLayout=$layoutName' class='cmslinkButton'>Layout bearbeiten</a>");
        $showAddContent = 0;
    }
    if ($editLayout) {
       // div_end("dragFrame");
        // div_end("cmsEditLayout","before");
    }
    
   
    div_end("layoutFrame layoutCenter","before");

//    div_start("layout layoutRight");
//    echo ("&nbsp;");
//    div_end("layout layoutRight");
//    div_end("layout","before");
//
//    div_start("layoutContent layoutRight");
//    echo ("&nbsp;");
//    div_end("layoutContent layoutRight");
//    div_end("layout layoutContent","before");
    
    
    if ($pageEditAble) {
       
   //  if ($showEdit) {
        switch ($cmsEditMode) {
            case "sideBar" :
                echo ("<div style='clear:both;'></div>");
                echo ("</div>");
                break;
            case "onPage2" :
                
                // echo ("<div style='width:auto;background-color:#f00;float:left;'>");
                cmsModul_show();
                // echo ("</div>");
                echo ("<div style='clear:both;'></div>");
                echo ("</div>");
                break;
        }
        
        
    }
}



function cms_Layout_showEditPageData($cmsEditMode,$pageWidth) {
    // echo ("<h1>cms_Layout_showEditPageData</h1>");
    
    $editMode = $_GET[editMode];
    $edit = $_SESSION[edit];

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
             $addEditClass = "cmsEditToggle";
             if (!$edit) $addEditClass .= " cmsEditHidden";
             if ($editMode == "pageData") {
                echo ("<div class='cmsContentEditFrame cmsContentEditPage $addEditClass'>");
                
                cms_page_editData($pageWidth);
                echo ("</div>");
            } else {
                
                
                echo ("<div class='cmsEditBox $addEditClass' >");
                $goPage = $GLOBALS[pageInfo][page]."?editMode=pageData";
            // Roll Image
                echo ("<div class='cmsContentFrame_editPageButton' >");
                echo ("<a href='$goPage'>");
                
                echo ("<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsEditPage.png' border='0px'>");
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


function cms_layout_showContentTypes ($layoutName,$contentData,$frameWidth,$showContent=1,$editable=0) {
    $viewMode = "layout";
    $newClass = cms_contentType_showClass($viewMode,$contentData,$frameWidth);
   
    if (is_object($newClass)) return $newClass;
    if ($newClass) echo ("<h3>Error by call NewFunction $newClass </h3>");
    
    return 0;
    
    
    $type = $contentData[type];
    $data = $contentData[data];
    $contentId = $contentData[id];
   
    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    global $pageShow,$pageEditAble;
    
    $textId = "text_".$contentId;
    $textData = cms_text_getForContent($textId);
    
    if (is_string($data)) $data = str2array($data);
    if(!is_array($data)) $data = array();

    $edit = $_SESSION[edit];
    
    if ($edit OR $pageEditAble) {
       $contentData[layout] = $layoutName;
       cms_contentType_head($contentData,$frameWidth);
    }
    $contentData[layout] = $layoutName;

    $border = 0;
    $frameWidth = $frameWidth - 2 * $border;

    $ownFrameWidth = cms_getWidth($contentData[frameWidth],$frameWidth);
    $ownFrameHeight = cms_getWidth($contentData[frameHeight],$frameWidth);

    if ($ownFrameWidth) $frameWidth = $ownFrameWidth;

    $frameStyle = $contentData[frameStyle];
    $frameSettings = cmsFrame_getSettings($frameStyle);
    $border = $frameSettings[border];
    $padding = $frameSettings[padding];
    $spacing = $frameSettings[spacing];

    $frameWidth = $frameWidth - (2*$border) - (2*$padding);
    // $divStyle = "width:".$frameWidth."px;";
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
  // <div id="dragContent_212" class="cmsContentFrameBox dragBox ui-draggable">
    
    div_start($divName,$divData);
    
    // FRAME TEXT - Überschrift
    cms_contentType_frameText($contentData,$textData,"frameHeadline",$frameWidth);
    
    // FRAME TEXT - Text Oben
    cms_contentType_frameText($contentData,$textData,"frameHeadtext",$frameWidth);
    
    
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
        case "content"  : 
            // if ($showContent) {
                cms_layout_showContent($contentData,$frameWidth,$showContent,$editable); 
            // } else {
                //                echo ("<h1>Sie haben keine Berechtigung fpr diese SEITE !! </h1>");
                //             }
            break;
                
        case "footer"   : cmsType_footer($contentData,$frameWidth); break;

        case "flip"     : cmsType_flip($contentData,$frameWidth); break;
        case "contentName" : cms_contenType_contentName($contentData); break;
        
        default :
            if (function_exists("cmsType_".$type)) {
                call_user_func("cmsType_".$type, $contentData,$frameWidth);
            } else {
            

                echo ("unkown Type $type<br />");
                foreach ($contentData as $key => $value) echo ("#$key = $value **");
                echo ("<br />");
            }
    }
    
    // FRAME TEXT - UNTEN
    cms_contentType_frameText($contentData,$textData,"frameSubtext",$frameWidth);
    
    div_end($divName);
    
    echo ("<div id='spacerId_$contentId' class='spacer spacerLayout spacer$type'>&nbsp;</div>");
    
}



function cms_layout_showContent($contentData,$frameWidth) {
    
    global $pageShow,$pageEditAble;
    $data = $contentData[data];
    if (!is_array($contentData)) $data = array();
    
    $absLeft = 0;
    $absRight = 0;
    $absTop = 0;
    $absBottom = 0;
    // show_array($data);
    
    
    if ($data[absLeft]) $absLeft = $data[absLeft];
    if ($data[absRight]) $absRight = $data[absRight];
    if ($data[absTop]) $absTop = $data[absTop];
    if ($data[absBottom]) $absBottom = $data[absBottom];
    
    $showWidth = $frameWidth - $absLeft - $absRight;
    
    $pageData = $GLOBALS[pageData];
    $pageInfo = $GLOBALS[pageInfo];
    //show_array($pageData);
    // echo ("HIER INHALT OF $pageInfo[pageName]<br />");
    $divData = array();
    
    // $targetWidth = $_SESSION[target_width];
    // $widthProz = floor($frameWidth / $targetWidth * 100.0);
    
    // echo ("SHow Content fw=$frameWidth tw=$targetWidth proz=$widthProz <br>");
    
    
    
    
    // $divData[style] = "width:".$frameWidth."px;";
    // $divData[style] = "width:".$widthProz."%;";
    
    if ($absLeft) $divData[style].= "margin-left:".$absLeft."px;";
    if ($absRight) $divData[style].= "margin-right:".$absRight."px;";
    if ($absTop) $divData[style].= "margin-top:".$absTop."px;";
    if ($absBottom) $divData[style].= "margin-bottom:".$absBottom."px;";
    
    
    if ($_SESSION[edit]) {
        $divData["class"]= "dragFrame";
        $divData[id]="dragFrame_0";
    }
    
   
    
    $editLayout = $_GET[editLayout];
    if (!$editLayout) {
        div_start("titleLineFrame");
        cms_titleLine($pageData,$showWidth);
        div_end("titleLineFrame");
    }
    
    if (!$pageShow) {
        div_start("content",$divData); //"width:".$frameWidth."px;");
        div_start("pageNotAllowed");
        echo ("SIE HABEN KEINE BERECHTIGUNG FÜR DIESE SEITE");
        div_end("pageNotAllowed");
        div_end("content","before");
        return 0;
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
            // echo ("PAGE Is PAGE <br>");
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
            //
            
            
            if ($_SESSION[userLevel]>6) $editAble = 1;
            
            if ($pageEditAble) $editAble = 1;
            
            if ($editAble) {
                $spacerClass = "spacer spacerEdit";
                if ($_SESSION[edit]) $spacerClass .= " spacerDrop";
            
           
                echo ("<div id='spacerId_$contentId' class='$spacerClass'>");
            }
            
            echo ("<div class='spacer spacerContentType spacerContentStart'>&nbsp;</div>");
            if ($editAble) {
                // if ($_SESSION[edit]) 
                echo ("</div>"); //  id='spacerId_$contentId' class='spacerDrop'>");
            }
//            //
//            // Spacer Content Start
//           // if ($_SESSION[edit]) $spacerAdd = "spacerDrop";
//           // echo ("<div class='spacer spacerContentStart $spacerAdd'>&nbsp;</div>");
//             if ($_SESSION[edit]) echo ("<div class='spacer spacerDrop'>");
//        // if ($_SESSION[edit]) $spacerAdd .= " spacerDrop";
//        //echo ("<div id='spacerId_$contentId' class='spacer spacerContentType spacerContentType_$type $spacerAdd'>&nbsp;</div>");
//            echo ("<div class='spacer spacerContentType spacerContentType_$type'>&nbsp;</div>");
//            if ($_SESSION[edit]) echo ("</div>"); //
//     
            
            // show Content
            cms_content_show($pageId,$showWidth);
           
            div_end("content","before");
            echo ("<div class='cmsContentEnd cmsContentEnd_hidden'>");
            echo ("&nbsp;");
            echo ("</div>");
            break;
            
        case "admin" :
            div_start("adminFrame");
            cms_admin_show($adminView,$showWidth);
            div_end("adminFrame");
            break;
        
        case "sitemap" :
            global $cmsName,$cmsVersion;
            div_start("siteMapFrame"); // ,array("cmsName"=>$cmsName));
            include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_sitemap.php");

            cms_sitemap_show($frameWidth);
            div_end("siteMapFrame");

            break;
            
    }
    
    

}



?>
