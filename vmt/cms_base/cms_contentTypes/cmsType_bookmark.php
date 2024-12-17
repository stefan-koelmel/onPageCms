<?php // charset:UTF-8
class cmsType_bookmark_base extends cmsType_contentTypes_base {

    function getName (){
        return "Favoriten";
    }

    function bookmark_show($contentData,$frameWidth) {

       
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
        
        div_start("bookMarkList");
        
        switch ($direction) {
            case "hori" :
                $columnCount = $data[columnCount];
                if (!$columnCount) $columnCount = 3;
                $columnAbs = $data[columnAbs];
                if (!$columnAbs) $columnAbs = 10;
                
                
                $columnWidth = ($frameWidth - (($columnCount-1) * $columnAbs)) / $columnCount;
                $columnWidth = floor($columnWidth);

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
        $userId = $_SESSION[userId];

        $actProject = $_SESSION[project];
        $actDrill   = $_SESSION[drill];
        
        
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
            $pageData = cms_page_getData($pageName);
            if (!$name and is_array($pageData)) {
                $name = $pageData[title];
                if (!$name) $name = $pageData[name];
                $icon = $pageData[imageId];
            }
            
            
            $pageDataData = $pageData[data];
            if (!is_array($pageDataData)) $pageDataData = array(); 
            
            
            $link = $url;
            
            $show = 1;
//            if ($pageDataData[projectSet]) {
//                $show = 0;
//                if ($actProject AND $data[project] == $actProject) {
//                    $show = 1;
//                }
////                if ($data[drill] == $actDrill) {
////                    $show = 1;
////                }
//            }
            
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

    function bookmark_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        
        $res = array();
        
        $direction = $data[direction];
        show_array($data);
        if (!$direction) $direction = "hori";
        $addData = array();
        $addData["text"] = "Navigationsrichtung";
        $addData["input"] = cms_navi_SelectDirection($direction,"editContent[data][direction]",array("submit"=>1));
        $res[] = $addData;
        
        
        switch ($direction) {
            case "hori" :
                
                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 3;
                $addData["text"] = "Spalten Anzahl";
                $addData["input"] = "<input type='text' name='editContent[data][columnCount]' value='$data[columnCount]' >";
                $res[] = $addData;

                $addData = array();
                if (!$data[columnAbs]) $data[columnAbs] = 10;
                $addData["text"] = "Spalten Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][columnAbs]' value='$data[columnAbs]' >";
                $res[] = $addData;


                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = "Spalten Höhe";
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $res[] = $addData;

                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = "Zeilen Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $res[] = $addData;

                
                
                break;
            case "vert" :
                
                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = "Spalten Höhe";
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $res[] = $addData;
                
                
                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = "Zeilen Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $res[] = $addData;
                break;
            
            default :
                echo ("unkown Direction $direction <br>");
                    
        }
        
        $addData = array();
        $addData["text"] = "Icon zeigen";
        if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
        $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
        $res[] = $addData;

        $addData = array();
        if (!$data[iconSize]) $data[iconSize] = 30;
        $addData["text"] = "Icon Größe";
        $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = "Info zeigen";
        if ($data[info]) $checked = "checked='checked'"; else $checked = "";
        $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
        $res[] = $addData;
        
        
               
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
    $bookmarkClass->bookmark_show($contentData,$frameWidth);
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
