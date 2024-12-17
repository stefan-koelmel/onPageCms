<?php // charset:UTF-8
class cmsType_subPageNavi_base extends cmsType_contentTypes_base {
    function getName() {
        return "Seiten Navigation";
    }
    
    function show($contentData,$frameWidth) {
        // echo ("Seiten Navigation<br>");
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $navType = $data[type];
        if (!$navType) $navType = "subPage";
        
        switch ($navType) {
            case "subPage" :
                $this->show_subPage($contentData,$frameWidth); 
                break;
            case "mainPage" :
                $this->show_mainPage($contentData,$frameWidth); 
                break;
            case "index" :
                $this->show_index($contentData,$frameWidth); 
                break;            
            
            case "parallel" :
                $this->show_parallel($contentData,$frameWidth); 
                break;            
        }
    }
        
    function show_subPage($contentData,$frameWidth) {    
        global $pageData;
        $pageId = $pageData[id];
        $pageList = cms_page_getSubPage($pageId);
        
        if ($pageData[dynamic]) {
            $showDynamic = 1;
            
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            // echo ("<h1> Dynamic SEITEN ÜBER dieser Seite -> Unterseiten </h1>");
            $dynamic_1_type = $dynamicData[dataSource];
            $dynamic_1_value = $_GET[$dynamic_1_type];
            if ($dynamic_1_value) $showDynamic = 2;
            
            $dynamic_2 = $dynamicData[dynamic2];
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) $showDynamic = 3;
            }
            
            if ($showDynamic) {
                // echo ("Show Dynamic Page from dynamicLevel = $showDynamic <br>");
                $pageList = array();
                
                
                $dynList = cms_dynamicPage_getList($dynamicData,$showDynamic);
                
                if (is_array($dynList) AND count($dynList)) {
                    $pageList = array();
                    
                    foreach ($dynList as $id => $dynamicPage) {
                        
                        
                        $pageAdd = $pageData;
                        $pageAdd[title] = $dynamicPage[name];
                        if ($dynamicPage[image]) {
                            $imageId = $dynamicPage[image];
                            if (!intval($imageId) AND $imageId) {
                                $imageList = explode("|",$imageId);
                                $imageId = $imageList[1];
                            }
                            $pageAdd[imageId] = $imageId;
                        }
                        $url = $pageData[name].".php";
                        if ($dynamicPage[url]) $url .= "?".$dynamicPage[url];
                        $pageAdd[url] = $url;    
                        //  echo ("id $id $dynamicPage[name] <br>");
                        // echo (" - > url $url <br>");
                        $pageAdd[active] = $dynamicPage[active];
                        
                        $pageList[] = $pageAdd;
                    }
                }
            }
       }
        
     
        
        if (is_array($pageList) AND count($pageList)) {
            $class = "subPage";
            $this->show_pageList($pageList, $pageData, $contentData, $frameWidth, $class);
        }

    }
    
    function show_mainPage($contentData,$frameWidth) {
        $pageData = $GLOBALS[pageData];
       
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $useIcons    = $data[icon];
        $iconSize   = $data[iconSize];
        if (!$iconSize) $iconSize = 30;

        $useInfo     = $data[info];

        
        $mainPage = intval($pageData[mainPage]);
        if ($mainPage == 0) {
            $mainPageData = cms_page_getData("index");
        } else {
            $mainPageData = cms_page_getData($mainPage);
        }
        
        if ($pageData[dynamic]) {
            $showDynamic = 0;
            
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            // echo ("<h1> Dynamic </h1>");
            $dynamic_1_type = $dynamicData[dataSource];
            $dynamic_1_value = $_GET[$dynamic_1_type];
            if ($dynamic_1_value) $showDynamic = 1;
            
            $dynamic_2 = $dynamicData[dynamic2];
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) $showDynamic = 2;
            }
            
            
            if ($showDynamic) {
                $url = $pageData[name].".php";
                $addUrl = "";
                $goName = "Unbekannt";
                
                switch ($showDynamic) {
                    case 2 :
                        $addUrl = "?".$dynamic_1_type."=".$dynamic_1_value;
                        $name1 = cms_dynamicPage_getInfo($dynamicData,1);
                        $goName = $name1; // $pageData[title]." | ".$name1;
                        break;
                    case 1 : 
                        $addUrl = "";
                        $goName = $pageData[title];
                }
                
                //echo ("Show Dynamic Page from dynamicLevel = $showDynamic <br/ >");
                // echo ("--> gehe zu ".$url.$addUrl." Name = '$goName' <br />");
                $mainPageData[url] = $url.$addUrl;
                $mainPageData[title] = $goName;
                $pageList = array();
            }
            
            
        }
        
        
        
        if ($mainPageData) {
            $url = $mainPageData[url];
            if (!$url) $url = $mainPageData[name].".php";
            $title = $mainPageData[title];
            
            $text = "Übergeordnete Seite '$title'";
             
            if ($useIcons) {
                $mainIcon = $mainPageData[imageId];
                if (!$mainIcon) {
                    $pageInfo = cms_page_getInfoBack($mainPageData);
                    show_array($pageInfo);
                    $mainIcon = $pageInfo["icon"];
                }
                $img = "No ICON";
                if ($mainIcon) {
                    $imgData = cmsImage_getData_by_Id($mainIcon);
                    $showData = array();
                    $img = cmsImage_showImage($imgData, $iconSize, $showData);
                }
                $text = $img."&nbsp; ".$text;
                
            }
            
            
            
           
            
            
            if (!$title) $title = $mainPageData[name];
            echo ("<a href='$url' class='mainLinkButton mainSecond'>$text</a><br>");
        }
        
    }
    
    function show_index($contentData,$frameWidth) {
        
        global $pageData;
        $pageId = $pageData[id];
      

        $data = $contentData[data];
        if (!is_array($data)) $data = array();


        $pageList = cms_page_getSubPage(0,"sort");
        if (is_array($pageList) AND count($pageList)) {
            $class = "index";
            $this->show_pageList($pageList,$pageData,$contentData,$frameWidth,$class);            
        }
       
    }
    
    
    function show_parallel($contentData,$frameWidth) {
        $pageData = $GLOBALS[pageData];
        // show_array($pageData);
        $hideOwn = 0;
        $pageList = cms_page_getParallelPage($pageData,"sort",$hideOwn);
        
        
        if ($pageData[dynamic]) {
            $showDynamic = 0;
            
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            $dynamic_1_type = $dynamicData[dataSource];
            $dynamic_1_value = $_GET[$dynamic_1_type];
            if ($dynamic_1_value) $showDynamic = 1;
            
            $dynamic_2 = $dynamicData[dynamic2];
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) $showDynamic = 2;
            }
            
            if ($showDynamic) {
                // echo ("Show Dynamic Page from dynamicLevel = $showDynamic <br>");
                $pageList = array();
                
                
                $dynList = cms_dynamicPage_getList($dynamicData,$showDynamic);
                
                if (is_array($dynList) AND count($dynList)) {
                    $pageList = array();
                    
                    foreach ($dynList as $id => $dynamicPage) {
                        $pageAdd = $pageData;
                        $pageAdd[title] = $dynamicPage[name];
                        if ($dynamicPage[image]) {
                            $imageId = $dynamicPage[image];
                            if (!intval($imageId) AND $imageId) {
                                $imageList = explode("|",$imageId);
                                $imageId = $imageList[1];
                            }
                            $pageAdd[imageId] = $imageId;
                        }
                        // if ($dynamicPage[image]) $pageAdd[imageId] = $dynamicPage[image];
                        $url = $pageData[name].".php";
                        if ($dynamicPage[url]) $url .= "?".$dynamicPage[url];
                        $pageAdd[url] = $url;    
                        // echo ("id $id $dynamicPage[name] $pageAdd[imageId]<br>");
                        // echo (" - > url $url <br>");
                        $pageAdd[active] = $dynamicPage[active];
                        
                        $pageList[] = $pageAdd;
                    }
                }
            }
       }
        
        
       if (is_array($pageList) AND count($pageList)) {
            $class = "parallel";
            $this->show_pageList($pageList,$pageData,$contentData,$frameWidth,$class);
       }
        
    }
    
    function show_pageList($pageList,$pageData,$contentData,$frameWidth,$class) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $columnCount = $data[columnCount];
        if (!$columnCount) $columnCount = 4;
        $columnAbs   = $data[columnAbs];
        if (!$columnAbs) $columnAbs = 10;
        $columnHeight   = $data[columnHeight];
        if (!$columnHeight) $columnHeight = 50;
        
        $rowAbs      = $data[rowAbs];
        if (!$rowAbs) $rowAbs = 10;

        $useIcons    = $data[icon];
        $iconSize   = $data[iconSize];
        if (!$iconSize) $iconSize = 30;

        $iconWidth   = $data[iconWidth];
        if (!$iconWidth) $iconWidth = 50;
        $useInfo     = $data[info];

        if ($useIcons) {
            $mainIcon = $pageData[imageId];
            if (!$mainIcon) {
                $pageInfo = cms_page_getInfoBack($pageData);
                // show_array($pageInfo);
                $mainIcon = $pageInfo["icon"];
            }
        }


        // echo ("ca=$columnCount cAbs=$columnAbs rAbs=$rowAbs uI=$useIcons uI=$useInfo <br>");

        $columnWidth = ($frameWidth - (($columnCount-1) * $columnAbs)) / $columnCount;
        $columnWidth = floor($columnWidth);

        $absWidth = $frameWidth - ($columnCount * $columnWidth);
        $columnLastAbs = $absWidth - (($columnCount-2)*$columnAbs);


        $borderWidth = 1;
        $padding = 5;
        $columnWidth = $columnWidth - (2 * $borderWidth) - (2*$padding);

        $useNav = 1;
        $hideAdmin = 1;

        if (is_array($pageList) AND count($pageList)) {
            $nr = 0;
            $zeilen = 0;
            for ($i=0;$i<count($pageList);$i++) {
                $subPageData = $pageList[$i];
                $subPageId = $subPageData[id];
                $show = 1;
               
                $subData = $subPageData[data];
                if ($subData and !is_array($subData)) {
                    $subData = str2Array($subData);
                }
                if (!is_array($subData)) $subData = array();
                
                
                // hide Index
                if ( $pageData[name] == "index" and $subPageData[name] == "index") $show =0;
                
                
                if ($useNav) {
                    $navShow = $subPageData[navigation];
                    if (!$navShow) $show = 0;
                }
                
                if ($hideAdmin) {
                    if (substr($subPageData[name],0,5)=="admin") {
                        $show = 0;
                    }                     
                }
                
                if (count($subData) and $class=='index') {
                    // echo ("SUBDATA FOR $subPageData[name]<br>");
                    foreach($subData as $subKey => $subValue) {
                        if (subStr($subKey,strlen($subKey)-3) == "Set" ) {
                            $sessionName = subStr($subKey,0,strlen($subKey)-3);
                            if ($subValue AND !$_SESSION[$sessionName]) $show = 0;
                            //echo ("check Session for $sessionName <br>");
                        } else {
                            echo ("subPageNavi->show_index() - Not Session".subStr($subKey,strlen($subKey)-3)." <- $subKey <br>");
                        }
                    }
                }
                
                if ($show) {

                    $nr++;
                    if ($nr == 1) {
                        $style = "";
                        if ($zeilen>0) $style .= "margin-top:".$rowAbs."px;";
                        div_start("subPageNavi_Line subPageNavi_Line_$class",$style);
                    }

                    $style="width:".$columnWidth."px;margin-right:".$columnAbs."px;";
                    if ($nr == $columnCount) $style="width:".$columnWidth."px;";
                    if ($nr == $columnCount-1) $style="width:".$columnWidth."px;margin-right:".$columnLastAbs."px;";

                    $style .= "border-width".$borderWidth."px;padding:".$padding."px;";
                    $style .= "height:".$columnHeight."px;";


                    $name = $subPageData[name];
                    $title = $subPageData[title];
                    $imageId = $subPageData[imageId];

                    $showName = $title;
                    if (!$showName) $showName = $title;

                    $url = $subPageData[url];
                    if (!$url) $url = $subPageData[name].".php";

                    
                    $itemClass = "subPageNavi cmsFrameLink";
                    if ($class) $itemClass.= " subPageNavi_$class";
                    
                    $selected = 0;
                    if ($pageData[id] == $subPageId) $selected = 1;
                    if ($subPageData[active]) $selected = 1;
                    if ($selected) {
                        $itemClass.= " subPageNavi_selected ";
                         if ($class) $itemClass.= " subPageNavi_selected_$class";
                    }
                    echo ("<div class='$itemClass' style='$style' >");
                    echo ("<div class='hiddenData'><a href='$url' class='hiddenLink'></a></div>");

                    if ($useIcons) {
                        if ($subPageData[imageId]) $useIconId = $subPageData[imageId];
                        else $useIconId = $mainIcon;

                        $img = "No ICON";
                        if ($useIconId) {
                            if (intval($useIconId)) $imageId = $useIconId;
                            else {
                                $imageList = explode("|",$useIconId);
                                $imageId = $imageList[1];
                            }
                            
                            $imgData = cmsImage_getData_by_Id($imageId);
                            $showData = array();
                            $img = cmsImage_showImage($imgData, $iconSize, $showData);
                        }

                        div_start("subPageNavi_Icon","width:".$iconSize."px;margin-right:5px;");
                        echo ("$img");
                        div_end("subPageNavi_Icon");

                        $textWidth = $columnWidth - $iconSize - 5;
                        div_start("subPageNavi_Text","width:".$textWidth."px;float:left;");
                    }



                    echo ("<div class='subPageNavi_title'>$showName</div>");

                   // echo ("<span class='subPageNavi_title' style=''>$showName</span><br />");

                    if ($useInfo) {
                        $showText = $subPageData[description];
                        if (!$showText) {
                            $showText = $subPageData[pageTitle];
                        }
                        if ($showText) {
                            $showText = php_clearStr($showText);
                            echo ("<span class='subPageNavi_info' >$showText</span>");
                        }
                    }

    //                 show_array($subPageData);

                    if ($useIcons) {
                        div_end("subPageNavi_Text","after");
                    }

                  
                    echo ("</div>"); // End of Item

                    if ($nr == $columnCount) {
                        $nr = 0;
                        div_end("subPageNavi_Line subPageNavi_Line_$class",before);
                        $zeilen++;
                    }




                    // show_array($subPageData);
                } // end of Show
            }

            if ($nr != 0) {
                $nr = 0;
                div_end("subPageNavi_Line subPageNavi_Line_$class",before);
            }
        }
    }
    
    function editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();


        // MainData
        
        // Navigations-Art
        $type = $data[type];
        if (!$type) $type = "subPage";
        $addData = array();
        $addData["text"] = "Navigations Art";
        $addData["input"] = cms_navi_SelectNaviType($type,"editContent[data][type]");
        $res[] = $addData;
        
        switch ($type) {
            case "mainPage" :
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
                break;
            
            
            case "subPage" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = "Navigationsrichtung";
                $addData["input"] = cms_navi_SelectDirection($data[direction],"editContent[data][direction]",array("submit"=>1));
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Startebene"; 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                // $res[] = $addData;


                $addData = array();
                $addData["text"] = "Maximale Tiefe";
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                // $res[] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
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
                break;
                
            case "index" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = "Navigationsrichtung";
                $addData["input"] = cms_navi_SelectDirection($data[direction],"editContent[data][direction]");
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Startebene"; 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                // $res[] = $addData;


                $addData = array();
                $addData["text"] = "Maximale Tiefe";
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                // $res[] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
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
                $addData["text"] = "Zeilen Anzahl";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $res[] = $addData;

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
                break;
                
           case "parallel" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = "Navigationsrichtung";
                $addData["input"] = cms_navi_SelectDirection($data[direction],"editContent[data][direction]");
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Startebene"; 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                // $res[] = $addData;


                $addData = array();
                $addData["text"] = "Maximale Tiefe";
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                // $res[] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
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
                $addData["text"] = "Zeilen Anzahl";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $res[] = $addData;

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
                break;
                
                
        }
        
        



        return $res;
    }
    

}







function cmsType_subPageNavi_class() {
    if ($GLOBALS[cmsTypes]["cmsType_subPageNavi.php"] == "own") $naviClass = new cmsType_subPageNavi();
    else $naviClass = new cmsType_subPageNavi_base();
    return $naviClass;
}


function cmsType_subPageNavi($contentData,$frameWidth) {
    $subPageNaviClass = cmsType_subPageNavi_class();
    $subPageNaviClass->show($contentData,$frameWidth);
}



function cmsType_subPageNavi_editContent($editContent,$frameWidth) {
    $subPageNaviClass = cmsType_subPageNavi_class();
    $res = $subPageNaviClass->editContent($editContent,$frameWidth);
    return $res;
}


function cmsType_subPageNavi_getName() {
    $subPageNaviClass = cmsType_subPageNavi_class();
    $res = $subPageNaviClass->getName();
    return $res;
}
?>
