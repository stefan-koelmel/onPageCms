<?php // charset:UTF-8

function cms_content_show($pageorFrame,$frameWidth,$getData="") {

    // if (is_array($getData)) foreach ($getData as $key => $value) echo ("$key = $value <br>");
    
    global $pageData,$pageInfo;
    $pageId = $pageData[id];
    
    
    $type = "";
    if (substr($pageorFrame,0,5)=="page_") $type = "page";
    if (substr($pageorFrame,0,6)=="frame_") $type = "frame";
    if (substr($pageorFrame,0,7)=="layout_") $type = "layout";
    if (substr($pageorFrame,0,8)=="dynamic_") $type = "page";
    
    
    
    
//    if ($pageData[dynamic] AND $type=="page") {
//        $newPage = cms_dynamicContent_showTitleBar($pageData);
//        echo ("<b>NEWcms_content_show($newPage)</b><br>");
//        $pageorFrame = $newPage;
//    }

    // foreach($GLOBALS as $key => $value) echo ("<b>".$key."</b> = ".$value."<br>");

    

    $myLevel = $_SESSION[showLevel];
    if (!$myLevel) $myLevel = 0;
    $myId = $_SESSION[userId];
    
    
    global $pageEditAble;
    
    $contentList = cms_content_getList($pageorFrame); //"page_$pageId");
    
    // echo ("SHOW CONTENT ".count($contentList)."<br>");
   
    
    
    if ($type == "page") {
        //echo ("cms_content_show($pageorFrame,$frameWidth,$getData) <br>");
        // foreach ($contentList as $key => $value) echo ("Cont $key = $value <br>");
    }
    
    // echo ("Type = $type => $pageorFrame <br>");
    
    
    
    $showEdit = $_SESSION[edit];
    if ($showEdit) {
        $showAddContent = 1;
        if ($_POST AND $type=="page") cms_content_savePost($pageorFrame);
        // if ($_POST AND $type=="dynamic") cms_content_savePost($pageorFrame);
        
        if ($_POST AND $type=="layout") cms_content_savePost($pageorFrame);
        
    }
    
        
    $maxSort = 0;
    
    if (count($contentList) > 0 ) {

        $sortCheck = 0;
        for ($i=0;$i<count($contentList);$i++) {

            $contentData = $contentList[$i];
            $subData = $contentData[data];
            
            // sortierung Korreturen - Automatisch
            $sort = $contentData[sort];
            $sortCheck++;
            if ($sortCheck != $sort) {
                // echo "Fehler in Sort || sollte : $sortCheck | ist $sort <br>";
                cms_content_changeSort($contentData[id],$sortCheck);
            }

            if ($sort>$maxSort) $maxSort = $sort;
            
            $show = 1;
            
            $contentShowLevel = $contentData[showLevel];
            if ($contentShowLevel > $myLevel) {
                $show = 0;
                if ($contentShowLevel == 3) {
                    $allowedUser = $subData[allowedUser];
                    if ($allowedUser AND $myId) {
                        // echo ("Spezielle User-auswahl allowed='$allowedUser' myId = $myId <br>");
                        $userPos = strpos($allowedUser,"|".$myId."|");
                        if (is_int($userPos)) {
                            // echo ("<h3>Allowed because is in allowedList </h3>");
                            $show = 1;
                        }
                    }
                }
            } else {
                 if ($contentShowLevel == 3) {
                    $forbiddenUser = $subData[forbiddenUser];
                    if ($forbiddenUser AND $myId) {
                        // echo ("Spezielle User asuwahl forbidden='$forbiddenUser' myId = $myId <br>");
                        $userPos = strpos($forbiddenUser,"|".$myId."|");
                        if (is_int($userPos)) {
                           
                            // echo ("<h3>Forbidden because is in forbiddenList </h3>");
                            $show = 0;
                            // if ($myLevel == 9) $show = 1; 
                        }
                    }
                }
            }
            
            $contentToLevel = $contentData[toLevel];
            
            if ($myLevel >= $contentToLevel AND $contentToLevel) {
                $show = 0;
            }
            
            $contentDataData = $contentData[data];
            if (is_array($contentDataData)) {
                foreach ($contentDataData as $subKey => $subValue) {
                    // echo ("SubData $subKey = $subValue <br>");
                    if (subStr($subKey,strlen($subKey)-3) == "Set" ) {
                        $sessionName = subStr($subKey,0,strlen($subKey)-3);
                        if ($subValue AND !$_SESSION[$sessionName]) $show = 0;
                        // echo ("check Session for $sessionName <br>");
                    } else {
    //                            echo ("subPageNavi->show_index() - Not Session".subStr($subKey,strlen($subKey)-3)." <- $subKey <br>");
                    }
                }
                
            }
            
            
            
            
            
            //if ($contentShowLevel <= $myLevel) {
            if ($show) {    
                if ($i+1==count($contentList)) $contentData[last] = 1;
                // echo ("show Content because show = $show<br>");
                cms_contentType_show($contentData,$frameWidth,$getData);
            } else {
                // echo ("dont show Content because show = $show<br>");
            }
           
        }
        
    } else {
        
        if ($_SESSION[showLevel] > 4 or $pageEditAble) {
            echo ("<div class='cmsContentNoData'>");



            switch ($type) {
                case "frame" : echo ("Kein Inhalt in diesem Spalte"); break;
                case "page" : echo ("Kein Inhalt auf dieser Seite - hier klicken um Inhalt hinzuzufügen"); break;
                case "layer" : echo ("Kein Inhalt auf diesem Layer"); break;
                default :
                    echo ("Kein Inhalt für diese '$type' verfügbar");
            }

            echo ("</div>\n");
        }
    }

    if ($showAddContent) {
        // echo ("MaxSort = $maxSort<br>");
        $maxSort++;
        cms_content_add($frameWidth,$pageorFrame,$maxSort,$getData);
    }



}






function cms_content_change_sort($contentList) {
    $contentUp = $_GET[contentUp];
    $contentDown = $_GET[contentDown];
    if ($contentUp OR $contentDown) {
        // echo ("Do Change Sort <br>");
    } else {
        return 0;
    }


    if ($contentUp) $changeId = $contentUp;
    if ($contentDown) $changeId = $contentDown;
    $sortCheck=0;

    for ($i=0;$i<count($contentList);$i++) {
        $contentData = $contentList[$i];
        $contentId = $contentData[id];
        $sort = $contentData[sort];
        $sortCheck++;
        // echo ("Content $contentId $sort $sortCheck <br>");
        if ($contentId == $changeId ) {
            // echo (" -- > Change hier! -- ");
            if ($contentUp) {
                if ($i== 0) {
                    // echo "<strong>ist erstes - No Change</strong><br>";
                    return 0;
                } else {
                    //echo ("nach Oben<br>");
                    //echo ("setze dieses auf $sortCheck - 1 <br>");
                    cms_content_changeSort($contentId,$sortCheck-1);

                    // echo ("setze das davor auf $sortCheck <br>");
                    $beforeContentId = $contentList[($i-1)][id];
                    cms_content_changeSort($beforeContentId,$sortCheck);
                    return 1;


                }

            }
            if ($contentDown) {
                if ($i+1 < count($contentList)) {
                    // echo ("nach Unten $i+1  / ".count($contentList)."<br>");


                    // echo ("setze dieses auf $sortCheck + 1 <br>");
                    cms_content_changeSort($contentId,$sortCheck+1);

                    // echo ("setze nächster auf $sortCheck <br>");
                    $nextContentId = $contentList[$i+1][id];
                    cms_content_changeSort($nextContentId,$sortCheck);
                    return 1;


                } else {
                    // echo "<strong>ist letztes - No Change</strong><br>";
                    return 0;
                }
            }
        }

    }

    return 0;
}


function cms_content_savePost($pageName) {
    foreach ($_POST as $key => $value ) {
        switch ($key) {
//            case "defaultText_save" :
//                $defaultText = $_POST[defaultText];
//                if (is_array($defaultText)) {
//                
//                    cms_defaultText_save($defaultText);
//                }
//                break;
//            
            
            case "saveLayout" :
                
                $layoutData = $_POST[layoutData];
                if (is_array($layoutData)) {
                    $pageContentId = $pageName;
                    if ($GLOBALS[pageContentId]) $pageContentId = $GLOBALS[pageContentId];
                    // echo ("<h1>Save Layout to => $pageContentId </h1>");
                    cms_content_saveLayout($layoutData,$pageContentId);
                }
                break;
             
            case "cancelSaveData" :
                $goPage = $GLOBALS[pageData][name].".php";
                $reload = 1;
                $waitTime = 0;
                if ($reload) {
                    reloadPage($goPage,$waitTime);
                }
                break;
                
                
            
            case "sortOrderSave" :
                $sortOrder = $_POST[sortOrder];
                if (is_array($sortOrder)) cms_content_saveOrder($sortOrder,$pageName);
                break;

            default :
                $show = 1;
                if (substr($key,0,14)=="deleteContent_") {
                    $deleteId = substr($key,14);
                    if (intval($deleteId)) {
                        $res = cms_content_delete($deleteId,1);
                        if ($res == 1) {
                            $goPage = $GLOBAL[pageInfo][page];
                            cms_infoBox("Content gelöscht !! ");
                            reloadPage($goPage,2);
                            // return "delete";
                        } else {
                            cms_errorBox("Content nicht gelöscht !!!");
                        }
                    }
                    $show = 0;
                }
                $show = 0;
                if ($show) {
                    echo ("Unkown Key in cms_content_savePost '$key' ".substr($key,0,13)." <br/ >");
                }
         }
    }
}


function  cms_content_saveLayout($layoutData,$pageName) {
    echo (" cms_content_saveLayout($layoutData,$pageName)<br>");
    // show_array($layoutData);
    // echo ($pageName."<br>");
    $updated = 0;
    $errors = 0;
    $added = 0;
    $addFrameList = array();
    
    $doSave = 1;
    
    foreach ($layoutData as $frame => $frameContentStr) {
        //  echo ("$frame $frameContentStr <br>");
         
         
        $frameId = substr($frame,strpos($frame,"_")+1);
        
        // echo ("FRAME : <b>$frame $frameId</b><br>");
        
        $frameContentList = explode(",",$frameContentStr);
        
        $setSort = 0;
       
        for($i=0;$i<count($frameContentList);$i++) {
            $frameName = $frameContentList[$i];
            
            if ($frameName) {
                // echo ("CONT : <b>$frameName ||| ".substr($frameName,0,13)."</b><br>");
                
                $insertName = "";
                if (substr($frameName,0,13) == "cmsDragModul_") {
                    $insertName = substr($frameName,13);
                }
                if (substr($frameName,0,16) == "newcmsDragModul_") {
                    $insertName = substr($frameName,16);
                }
                
                if ($insertName) {
                
                
                // if (substr($frameName,0,13) == "cmsDragModul_") {
                    // $insertName = substr($frameName,13);
                    // echo (" -- > Insert Modul $insertName <br>");     
                    
                    
                    $addFrame = 0;
                    if (substr($insertName,0,5)=="frame") {
                        $insertToFrame = substr($insertName,7);
                        $insertName = substr($insertName,0,6);
                        // echo ("<b>INSERT FRAME $insertName in frame '$insertToFrame' </b><br>");
                        $addFrame = 1;                        
                    }
                    
                    if (strpos($frameId,"_")) {
                        $setPageId = "frame_".$frameId;
                        
                        if (substr($frame,0,8) == "inFrame_") {
                            // echo ("   ----->>>> FrameId='$frameId' PageName='$pageName' frame='$frame'  <br>");                        
                            $newFrame = substr($frame,0,9);
                            $addFrameNr = $frame[strlen($frame)-1];
                            // echo ("Insert INTO NewFrame '$newFrame' FrameNr ='$addFrameNr' <br>");
                            $newFrameId = $addFrameList[$newFrame];
                            $setPageId = "frame_".$newFrameId."_".$addFrameNr;
                        }
                        
                    }
                    else $setPageId = $pageName;
                    
                    $setSort++;
                    
                    $query = "INSERT INTO `$GLOBALS[cmsName]_cms_content` SET `sort`='$setSort', `pageId`='$setPageId',`type`='$insertName' ";
                    if ($doSave) {
                        $result = mysql_query($query);
                    }
                    $added++;
                    $updated++;
                    if (!$result) {
                        echo ("Error in Insert Query $query<br>");
                        $errors++;
                        if ($addFrame) {
                            $addFrameId = "newID";
                        }
                    } else {
                        if ($addFrame) {
                            $addFrameId = mysql_insert_id();
                        }
                    }

                    if ($addFrame) {
                        // echo ("<b>INSERT ID OF FRAME($insertToFrame) = '$addFrameId' </b><br/>");
                        $addFrameList[$insertToFrame] = $addFrameId;
                    }

                    
                    
                } else {
                    $contentId = substr($frameName,strpos($frameName,"_")+1);
                    $contentData = cms_content_getId($contentId);
                    // echo (" -- > Old Modul $contentId <br>"); 
                    
                    if (is_array($contentData)) {
                        $isSort = $contentData[sort];
                        $isPageId = $contentData[pageId];
                        $contentType = $contentData[type];                    
                    }

                    if (strpos($frameId,"_")) $setPageId = "frame_".$frameId;
                    else $setPageId = $pageName;

                    $setSort++;

                    $update = 0;
                    if ($setPageId != $isPageId OR $setSort != $isSort) $update = 1;

                    if ($update) {
                        $updated ++;
                        //echo (" --> $contentId $frameName $contentType<br>");
                        //echo ("PageId: is:$isPageId <=> set:$setPageId <br>");
                        // echo ("sort: is:$isSort <=> set:$setSort <br>");
                        
                        $query = "UPDATE `$GLOBALS[cmsName]_cms_content` SET `sort`='$setSort', `pageId`='$setPageId' WHERE `id` = $contentId ";
                        if ($doSave) {
                            $result = mysql_query($query);
                        }
                        if (!$result) {
                            echo ("Error in Query $query.<br>");
                            $errors++;
                        }
                    }
                    
                }
                
                
            }
        }
       
         
         
    }
    $reload = 1;
    if ($updated) {
        $out = "Reihenfolge von $updated Modulen geändert.";
        
        if ($added) {
            $out .= "<br/>$added Module wurden hinzugefügt.";
        }
        if ($errors) {
            $out .= "<br>$errors Fehler sind aufgetreten";
            cms_errorBox($out);
            $reload = 0;
        } else {
            cms_infoBox($out);
        }
        $waitTime = 3;
    } else {
        cms_infoBox("Keine Änderungen");
        $waitTime = 1;
    }
    
    $add = "";
    if ($_GET[editLayout]) $add .= "editLayout=".$_GET[editLayout];
    
    $goPage = cms_page_goPage($add); // $GLOBALS[pageData][name].".php";
    
    
    if ($reload) {
        reloadPage($goPage,$waitTime);
    } else {
        echo ("goPage <a href='$goPage'>Neu Laden</a> <br>");
    }
 
    
}


function cms_content_saveOrder($sortOrder=array(),$pageName) {
 
    $updated = 0;
    $errors = 0;
    $added = 0;
    foreach ($sortOrder as $frame => $frameContentStr) {
        
        $frameId = substr($frame,strpos($frame,"_")+1);
        
        // echo ("$frame $frameId<br>");
        
        $frameContentList = explode(",",$frameContentStr);
        
        $setSort = 0;
        
        for($i=0;$i<count($frameContentList);$i++) {
            $frameName = $frameContentList[$i];
            
            if ($frameName) {
                // echo ("$frameName ".substr($frameName,0,13)."<br>");
                
                if (substr($frameName,0,13) == "cmsDragModul_") {
                    $insertName = substr($frameName,13);
                    echo ("Insert Modul $insertName <br>");
                    
                    if (strpos($frameId,"_")) $setPageId = "frame_".$frameId;
                    else $setPageId = $pageName;
                    
                    $setSort++;
                    
                    
                    
                    
                    $query = "INSERT INTO `$GLOBALS[cmsName]_cms_content` SET `sort`='$setSort', `pageId`='$setPageId',`type`='$insertName' ";
                    $result = mysql_query($query);
                    $added++;
                    $updated++;
                    if (!$result) {
                        echo ("Error in Insert Query $query<br>");
                        $errors++;
                    }
                } else {
                    $contentId = substr($frameName,strpos($frameName,"_")+1);
                    $contentData = cms_content_getId($contentId);
                    if (is_array($contentData)) {
                        $isSort = $contentData[sort];
                        $isPageId = $contentData[pageId];
                        $contentType = $contentData[type];                    
                    }


                    if (strpos($frameId,"_")) $setPageId = "frame_".$frameId;
                    else $setPageId = $pageName;

                    $setSort++;

                    $update = 0;
                    if ($setPageId != $isPageId OR $setSort != $isSort) $update = 1;

                    if ($update) {
                        $updated ++;
                        //echo (" --> $contentId $frameName $contentType<br>");
                        //echo ("PageId: is:$isPageId <=> set:$setPageId <br>");
                        // echo ("sort: is:$isSort <=> set:$setSort <br>");

                        $query = "UPDATE `$GLOBALS[cmsName]_cms_content` SET `sort`='$setSort', `pageId`='$setPageId' WHERE `id` = $contentId ";
                        $result = mysql_query($query);
                        if (!$result) {
                            echo ("Error in Query $query.<br>");
                            $errors++;
                        }

                    }
                }
                
            }
        }
       
        
        
        // echo ("change Sort in Layout $sortKey = $sortValue <br>");
        
    }
    if ($updated) {
        $out = "Reihenfolge von $updated Modulen geändert";
        if ($errors) {
            $out .= "<br>$errors Fehler sind aufgetreten";
            cms_errorBox($out);
        } else {
            cms_infoBox($out);
        }
        $waitTime = 3;
    } else {
        cms_infoBox("Keine Änderungen");
        $waitTime = 1;
    }
    

    $goPage = $GLOBALS[pageData][name].".php";
    $reload = 1;
    if ($reload) {
        reloadPage($goPage,$waitTime);
    } else {
        echo ("goPage <a href='$goPage'>Neu Laden</a> <br>");
    }
}



function cms_content_add($frameWidth,$addTo,$lastSort=1000,$getData="") {
    global $pageInfo,$pageData;

   // if (strpos())
    $addInto = $addTo;//."_".$pageData[id];

    $editMode = $_GET[editMode];
    $addToId = $_GET[addTo];


    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    switch ($cmsEditMode) {
        case "onPage2" : return "";
    }

    if ($editMode == "addContent" AND $addTo == $addToId) {
            
            $addContent = $_POST[addContent];
            $addContentSave = $_POST[addContentSave];
            $addContentCancel = $_POST[addContentCancel];
            if ($addContentCancel) {
                //echo("Reload $pageInfo[page] <br>");
                reloadPage($pageInfo[page],0);
                return 0;
            }

            
            if ($addContentSave) {
                $res = cms_content_insert($addContent);
                if ($res) {
                    //echo("Inhalt gespeichert<br>");
                    $goPage = $pageInfo[page];
                    $goPage .= "?editMode=editContentData&editId=$res";

                    if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                    reloadPage($goPage,0);
                }
                echo ("contentSave $res<br>");
            }

            if (!is_array($addContent)) {
                $addContent = array();
                $addContent[pageId] = $addInto;
                $addContent[sort] = $lastSort;
            }

            div_start("cmsContentAddFrame",array("id"=>"addContent"));
            $addUrl = "$pageInfo[page]?editMode=addContent&addTo=$addTo";
            foreach($_GET as $key => $value ) {
                switch ($key) {
                    case "editLayout" : $addUrl .= "&$key=$value"; break;
                }
            }


            // echo ("Add URL = $addUrl <br>");
            echo("<form action='$addUrl' method='post' class='cmsForm' >");
            echo ("<strong>Inhalt hinzufügen $pageData[title]</strong> ");
            echo ("<input type='hidden' value='$addContent[pageId]' name='addContent[pageId]' >");
            echo ("<input type='hidden' value='$addContent[sort]' name='addContent[sort]' >");
            echo("Name =$pageData[name]");
            if ($pageData[name] == "admin_cmsLayout") {
                echo ("- Inhalt: ".cms_contentLayout_selectType($addContent[type],"addContent[type]")." ");
            } else {
                echo ("- Typ: ".cms_content_SelectType($addContent[type],"addContent[type]")." ");
            }
            echo ("<input type='submit' class='cmsInputButton' name='addContentSave' value='hinzufügen'>");
            echo ("<input type='submit' class='cmsInputButton cmsSecond' name='addContentCancel' value='abbrechen'>");
            echo ("</form>");
            div_end("cmsContentAddFrame","before");
        //}
    } else {

        $addUrl = "$pageInfo[page]?editMode=addContent&addTo=$addTo";

        foreach($_GET as $key => $value ) {
            switch ($key) {
                case "editLayout" : $addUrl .= "&$key=$value"; break;
            }
        }
        $addUrl .= "#addContent";
        $addShow = "center";
        switch ($addShow) {
            case "left" :
                $divData = array();
                $divData[style] = "width:".($frameWidth+30)."px;left:-20px;";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLine",$divData);


                $divData = array();
                $divData[style] = "left:0px;top:0px;"; //.($frameWidth-10)."px";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLineBox",$divData);
                $goPage = $pageInfo[page];
                $goPage .= "?editMode=editContentData&editId=".$contentId;
                $goPage .= "#editFrame_".$contentId;
                echo("<a href='$addUrl'>+</a>");
                div_end("cmsEditLineBox");

                div_start("cmsEditLineLine","width:".($frameWidth-10)."px");
                div_end("cmsEditLineLine");
                
                div_end("cmsEditLine","before");
                break;
            case "right" :
                $divData = array();
                $divData[style] = "width:".($frameWidth+30)."px;";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLine",$divData);

                div_start("cmsEditLineLine","width:".($frameWidth)."px");
                div_end("cmsEditLineLine");

                $divData = array();
                $divData[style] = "left:0px;top:0px;"; //.($frameWidth-10)."px";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLineBox",$divData);
                  echo("<a href='$addUrl'>+</a>");
                div_end("cmsEditLineBox");

               

                div_end("cmsEditLine","before");
                break;

             case "center" :
                $divData = array();
                $divData[style] = "width:".($frameWidth+10)."px;";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLine",$divData);

                div_start("cmsEditLineLine","width:".(($frameWidth-28)/2)."px");
                div_end("cmsEditLineLine");

                $divData = array();
                $divData[style] = "left:0px;top:0px;"; //.($frameWidth-10)."px";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLineBox",$divData);
                $goPage = $pageInfo[page];
                $goPage .= "?editMode=editContentData&editId=".$contentId;
                $goPage .= "#editFrame_".$contentId;
                echo("<a href='$addUrl' title='Inhalt hinzufügen'>+</a>");
                div_end("cmsEditLineBox");

                div_start("cmsEditLineLine","width:".(($frameWidth-28)/2)."px");
                div_end("cmsEditLineLine");



                div_end("cmsEditLine","before");
                break;
        }


        
        $divData = array();

        return 0;


        echo ("<a href='$addUrl' class='cmsLinkButton' >neuen Inhalt hinzufügen</a><br>");
    }
}






/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
