<?php // charset:UTF-8
class cmsType_bookmark_base extends cmsClass_content_show {

    function getName (){
        return "Favoriten";
    }

    function contentType_show($contentData,$frameWidth) {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
       
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $direction = $data[direction];
        
        $useIcon = $data[icon];
        $iconSize = $data[iconSize];
        $useInfo = $data[info];
        
        $rowAbs = $data[rowAbs];
        $columnHeight = $data[columnHeight];
        
        $borderWidth = 1;
        $padding = 5;
        $iconAbs = 5;
        // $frameWidth = $frameWidth - 4;
        
        div_start("bookMarkList");
        
        $frameStyle = "background-color:#f0f;";
        
        switch ($direction) {
            case "hori" :
                $columnCount = $data[columnCount];
                if (!$columnCount) $columnCount = 3;
                $columnAbs = $data[columnAbs];
                if (!$columnAbs) $columnAbs = 10;
                
                $itemStyle = "margin-right:".$columnAbs."px;";
                
                
                $columnWidth = ($frameWidth - (($columnCount-1) * $columnAbs)) / $columnCount;
                $columnWidth = floor($columnWidth);
                
                $itemStyle .= "width:".$columnWidth."px;";
                if ($columnHeight) $itemStyle .= "height:".$columnHeight."px;";
                if ($rowAbs) $itemStyle .= "margin-bottom:".$rowAbs."px;";
                
                $frameStyle .= "margin-right:-".$columnAbs."px;";

                $absWidth = $frameWidth - ($columnCount * $columnWidth);
                $columnLastAbs = $absWidth - (($columnCount-2)*$columnAbs);
                $columnWidth = $columnWidth - (2 * $borderWidth) - (2*$padding);
                break;
                
            case "vert" :
                $columnWidth = $frameWidth - (2 * $borderWidth) - (2*$padding);
                $columnCount = 1;
                
                break;
        }
        
        if ($useIcon) {
            if ($iconSize > $columnHeight) {
                $columnHeight = $iconSize; // + 2* $padding;
                // echo ("Set Height to $columnHeight because $iconSize <br>");
            }
        }
        
        $activePage = cmsHistory_aktUrl();
        $userId = $this->session_get(userId);

        $actProject = $this->session_get(project);
        $actDrill   = $this->session_get(drill);
        
        
        $userId = user::userId();
        $userevel = user::userLevel();
        $userData = user::userData();
        $bookMarkList = user::bookmark_List();
        // echo ("is =$userId level=$userevel data=$userData bookmark=$bookMarkList <br>");
        if (!is_array($bookMarkList) ) {
            return "noBookMarkList";            
        }
        
        div::start("bookMarkFrame",$frameStyle);
        foreach ($bookMarkList as $pageName => $value) {
            // echo ("BOOK MARK is $pageName = $value $value[name] <br>");
            if (!$pageName) {
                continue;
            }
            $pageInfo = page::infoBack($pageName);
            if (!$pageInfo) continue;
            
            $url = $pageInfo[url];
            $name = lg::lgStr($pageInfo[name],1);
            $breadCrumb = $pageInfo[breadCrumb];
            
            // echo ("$name $url $breadCrumb <br>");
            
            $divNameLine = "bookmarkItem";
            $divData = array();
            $divData[style] = $itemStyle;
                
            $itemOut = div::start_str($divNameLine,$divData);
            
            $itemLink = "<a href='$url' class='hiddenLink bookmarkLink' >$name</a>";
            $itemOut .= div::div_str("hiddenData",null,$itemLink);
                

            if ($useIcon) {
//                    div_start("bookmarkIcon","float:left;width:".$iconSize."px;margin-right:".$iconAbs."px;");
//                    if ($icon) {
//                        $imgData = cmsImage_getData_by_Id($icon);
//                        $showData = array();
//                        $img = cmsImage_showImage($imgData, $iconSize, $showData);
//                    } else {
//                        $img = "&nbsp;";
//                    }
//
//                    echo ($img);
//                    div_end("bookmarkIcon")
                $textWidth = $columnWidth - $iconSize - $iconAbs;
            } else {
                $textWidth = $columnWidth;
            }
            
            
            $itemOut .= div::start_str("bookmarkTitle","width:".$textWidth."px;");
            $itemOut .= $name;
           
            $itemOut .= "<br />";
                
            
            $itemOut .= $breadCrumb;
            $itemOut .= div::end_str("bookmarkTitle");  
            
            
            $itemOut .= div::end_str($divNameLine,"before");

            
            echo ($itemOut);
                
            
            
        }
        div::end("bookMarkFrame","before");
        div_end("bookMarkList","before");
        
//        $is_bookMark = user::bookmark_isBookMark();
//        $indexBookMark = user::bookmark_isBookMark("index");
//        
//        echo ("Seite is BookMark = $is_bookMark / Index = $indexBookMark <br>");
//        $toggleRes = user::bookmark_toogle();
//        echo ("ToggleResult = $toggleRes <br>");
        return 1;
        
        
        $bookMarkList = cmsUserData_bookmarkList($userId);
        $nr = 0;
        $zeile = 0;
        for ($i=0;$i<count($bookMarkList);$i++) {
            $bookmark = $bookMarkList[$i];
            
            $breadCrumb = $bookmark[breadCrumb];
            $name = $bookmark[name];
            $url = $bookmark[url];
            $data = $bookmark[data];
            if ($data) $data = str2Array ($data);
            // show_array($data);
           
            $off = strpos($url,".");
            if ($off) $pageName = substr($url,0,$off);
            else $pageName = $url;
            //echo ("PAGE = $pageName<br>");
            $pageData = $this->page_getData($pageName);
            if (!$name and is_array($pageData)) {
                $name = $pageData[title];
                $name = $this->lgStr($name);
                if (!$name) $name = $pageData[name];
                $icon = $pageData[imageId];
            }
            
            
            $pageDataData = $pageData[data];
            if (!is_array($pageDataData)) $pageDataData = array(); 
            
            
            $link = $url;
            
            $show = 1;

            if ($show) {
                
                $nr++;
                switch ($direction) {
                    case "hori" :
                        
                        if ($nr == 1) {
                            $style = "";
                            if ($zeile>0) $style .= "margin-top:".$rowAbs."px;";
                            div_start("subPageNavi_Line",$style);
                        }
                        
                        $style="width:".$columnWidth."px;margin-right:".$columnAbs."px;";
                        if ($nr == $columnCount) $style="width:".$columnWidth."px;";
                        if ($nr == $columnCount-1) $style="width:".$columnWidth."px;margin-right:".$columnLastAbs."px;";

                        $style .= "border-width:".$borderWidth."px;padding:".$padding."px;";
                        $style .= "height:".$columnHeight."px;";
                        break;
                        
                    case "vert" :
                        $style="width:".$columnWidth."px;margin-right:0px;";      
                        $style .= "height:".$columnHeight."px;";
                         $style .= "border-width:".$borderWidth."px;padding:".$padding."px;";
                        if ($nr > 1) {
                             $style .= "margin-top:".$rowAbs."px;";
                        }
                        break;
                    
                }
                    
                $pageInfo = cms_page_getInfoBack($url);
                $name = $pageInfo[name];
                $name = $this->lgStr($name);
                
                $breadCrumb = $pageInfo[breadCrumb];
                $icon = $pageInfo[icon];

                $divNameLine = "bookmarkItem";
                $divData = array();
                $divData[style] = $style;
                
                
                div_start($divNameLine,$divData);
                
                echo ("<div class='hiddenData'><a href='$url' class='hiddenLink'></a></div>");
                

                if ($useIcon) {
                    div_start("bookmarkIcon","float:left;width:".$iconSize."px;margin-right:".$iconAbs."px;");
                    if ($icon) {
                        $imgData = cmsImage_getData_by_Id($icon);
                        $showData = array();
                        $img = cmsImage_showImage($imgData, $iconSize, $showData);
                    } else {
                        $img = "&nbsp;";
                    }

                    echo ($img);
                    div_end("bookmarkIcon");
                    $textWidth = $textWidth = $columnWidth - $iconSize - $iconAbs;
                } else {
                    $textWidth = $columnWidth;
                }
                div_start("bookmarkTitle","width:".$textWidth."px;");
                
                $outAdd = "";
                //if ($pageDataData[projectSet]) {
//                    if ($data[project]) {
//                        $projData = cmsCategory_getById($data[project]);
//                        if ($data[project] != $actProject) {
//                            if (strpos($url,"?")) $url.= "&";
//                            else $url.= "?";
//                            $url .= "setProject=$data[project]";
//                        }
//                        $projName = $projData[name];
//                        $outAdd .= " $projName";
//                        if ($data[drill]) {
//                            if ($data[drill] != $actDrill) {
//                                if (strpos($url,"?")) $url.= "&";
//                                else $url.= "?";
//                                $url .= "setDrill=$data[drill]";
//                            }
//                            $drillData = cmsCategory_getById($data[drill]);
//                            $drillName = $drillData[name];
//                            $outAdd .= " / $drillName";
//                        }
//                    }
                
                
                
                echo ("<a href='$url' class='bookmarkLink'>");
                echo ($name);
                echo ("</a>");
                
                echo ($outAdd);
             
                echo ("<br />");
                echo ($breadCrumb);

                div_end("bookmarkTitle");

                div_end($divNameLine,"before");
                
                

                if ($nr == $columnCount AND $direction == "hori") {
                    $nr = 0;
                    div_end("subPageNavi_Line",before);
                    $zeile++;
                }
            
            }
            
            
           
            
            
            
            
        }
        if ($nr != 0 AND $direction == "hori") {
            div_end("subPageNavi_Line",before);            
        }
        
        div_end("bookMarkList","before");
       
            
    }

    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        
        $res = array();
        $res[bookmark] = array();
        $res[bookmark][showName] = $this->lga("content","bookmarkTab");
        $res[bookmark][showTab] = "Simple";
        
        $direction = $data[direction];
        // show_array($data);
        if (!$direction) $direction = "hori";
        $addData = array();
        $addData["text"] = $this->lga("contenType_bookmark","directiom"); // "Navigationsrichtung";
        $addData["input"] = cms_navi_SelectDirection($direction,"editContent[data][direction]",array("submit"=>1));
        $addData["mode"] = "Simple";
        $res[bookmark][] = $addData;
        
        
        switch ($direction) {
            case "hori" :
                
                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 3;
                $addData["text"] = $this->lga("contenType_bookmark","columnCount"); // "Spalten Anzahl";
                $addData["input"] = "<input type='text' name='editContent[data][columnCount]' value='$data[columnCount]' >";
                $addData["mode"] = "Simple";
                $res[bookmark][] = $addData;

                $addData = array();
                if (!$data[columnAbs]) $data[columnAbs] = 10;
                $addData["text"] = $this->lga("contenType_bookmark","columnDist"); // "Spalten Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][columnAbs]' value='$data[columnAbs]' >";
                $addData["mode"] = "More";
                $res[bookmark][] = $addData;


                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = $this->lga("contenType_bookmark","lineHeight"); //"Spalten Höhe";
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $addData["mode"] = "More";
                $res[bookmark][] = $addData;

                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = $this->lga("contenType_bookmark","lineDist"); //"Zeilen Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $addData["mode"] = "More";
                $res[bookmark][] = $addData;

                
                
                break;
            case "vert" :
                
                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = $this->lga("contenType_bookmark","lineHeight"); //"Spalten Höhe";
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $addData["mode"] = "More";
                $res[bookmark][] = $addData;
                
                
                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = $this->lga("contenType_bookmark","lineDist"); //"Zeilen Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $addData["mode"] = "More";
                $res[bookmark][] = $addData;
                break;
            
            default :
                echo ("unkown Direction $direction <br>");
                    
        }
        
        $addData = array();
        $addData["text"] = $this->lga("contenType_bookmark","showIcon"); //"Icon zeigen";
        if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
        $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
        $addData["mode"] = "More";
        $res[bookmark][] = $addData;

        $addData = array();
        if (!$data[iconSize]) $data[iconSize] = 30;
        $addData["text"] = $this->lga("contenType_bookmark","iconSize"); //"Icon Größe";
        $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
        $addData["mode"] = "More";
        $res[bookmark][] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("contenType_bookmark","showInfo"); //"Info zeigen";
        if ($data[info]) $checked = "checked='checked'"; else $checked = "";
        $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
        $addData["mode"] = "More";
        $res[bookmark][] = $addData;
        
        
               
        return $res;
    }
}

function cmsType_bookmark_class() {
    if ($GLOBALS[cmsTypes]["cmsType_bookmark.php"] == "own") $bookmarkClass = new cmsType_bookmark();
    else $bookmarkClass = new cmsType_bookmark_base();
    return $bookmarkClass;
}

function cmsType_bookmark($contentData,$frameWidth) {
    $bookmarkClass = cmsType_bookmark_class();
    $res = $bookmarkClass->show($contentData,$frameWidth);
    return $res;    
}


function cmsType_bookmark_editContent($editContent,$frameWidth) {
    $bookmarkClass = cmsType_bookmark_class();
    return $bookmarkClass->bookmark_editContent($editContent,$frameWidth);
}

function cmsType_bookmark_getName() {
    $bookmarkClass = cmsType_bookmark_class();
    $name = $bookmarkClass->getName();
    return $name;
}


?>
