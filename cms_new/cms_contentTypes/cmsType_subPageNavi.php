<?php // charset:UTF-8
class cmsType_subPageNavi_base extends cmsClass_content_show {
    function getName() {
        return "Seiten Navigation";
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $navType = $data[type];
        //  echo ("Seiten Navigation $navType<br>");
        if (!$navType) $navType = "subPage";
       
        switch ($navType) {
            case "subPage" :
                $this->show_subPage(); 
                break;
            case "mainPage" :
                $this->show_mainPage(); 
                break;
            case "index" :
                $this->show_index(); 
                break;            
            
            case "parallel" :
                $this->show_parallel(); 
                break;

            case "scrollTop" :
                $this->show_scrollTop();
                break;

            case "scrollList" :
                $this->show_scrollList();
                break;
        }
    }
        
    function show_subPage() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        global $pageData;
        $pageId = $pageData[id];
//        echo ("SUBPAGE ID =$pageId $pageData <br>");
//        foreach ($pageData as $key => $value ) echo ("$key = $value <br>");
        $pageList = cms_page_getSubPage($pageId,"sort");
        
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
            $this->show_pageList($pageList,$pageData,$class);
        }

    }
    
    function show_mainPage() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $pageData = $GLOBALS[pageData];
       
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $useIcons    = $data[icon];
        $iconSize   = $data[iconSize];
        if (!$iconSize) $iconSize = 30;

        $useInfo     = $data[info];

        
        $mainPage = intval($pageData[mainPage]);
        if ($mainPage == 0) {
            $mainPageData = $this->page_getData("index");
        } else {
            // echo ("GET DATA FOR $mainPage <br>");
            $mainPageData = $this->page_getData($mainPage);
            // echo ("FERTIG<br>");
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
            if (is_array($title)) $title = $this->lgStr($title);
            
            $text = $title; // "Übergeordnete Seite '$title'";
             
            if ($useIcons) {
                $mainIcon = $mainPageData[imageId];
                if (!$mainIcon) {
                    $pageInfo = cms_page_getInfoBack($mainPageData);
                    // show_array($pageInfo);
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
            echo ("<a href='$url' class='mainLinkButton mainSecond'>$text</a><br />");
        }
        
    }
    
    function show_index() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        global $pageData;
        $pageId = $pageData[id];
      

        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        if (!$data[useNav]) $data[useNav] = 0;
        
        $pageList = cms_page_getSubPage(0,"sort");
        // foreach ($pageList as $key => $value) echo ("$key = $value <br>");
        if (is_array($pageList) AND count($pageList)) {
            $class = "index";
            $this->show_pageList($pageList,$pageData,$class);            
        }
       
    }
    
    
    function show_parallel() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
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
            $this->show_pageList($pageList,$pageData,$class);
       }
        
    }
    
    function show_pageList($pageList,$pageData,$class) {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
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
        
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = $this->wireframeState;
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            $wireframeImage = $wireframeData[image];
            // echo ("WireFrame Image<br />");
            $wireframeImageText = $wireframeData[imageText];
            // show_array($wireframeData);
        }


        //  echo ("ca=$columnCount cAbs=$columnAbs rAbs=$rowAbs uI=$useIcons uI=$useInfo <br>");

        $columnWidth = ($frameWidth - (($columnCount-1) * $columnAbs)) / $columnCount;
        $columnWidth = floor($columnWidth);

        $absWidth = $frameWidth - ($columnCount * $columnWidth);
        $columnLastAbs = $absWidth - (($columnCount-2)*$columnAbs);


        $borderWidth = 1;
        $padding = 5;
        $columnWidth = $columnWidth - (2 * $borderWidth) - (2*$padding);

        $useNav = $data[useNav];
        // foreach ($data as $key => $value) echo ("Data $key => $value <br>");
        $hideAdmin = 1;
        $myLevel = $this->showLevel;
        if (is_null($myLevel)) $myLevel = 0;
        $myId    = $this->session_get(userId);

        if (is_array($pageList) AND count($pageList)) {
            
            div_start("subPageNaviFrame");
            // echo ("<h1>Hier</h1>");
            $nr = 0;
            $zeilen = 0;
            foreach ($pageList as $pageName => $subPageData) {
            // for ($i=0;$i<count($pageList);$i++) {
                // $subPageData = $pageList[$i];
                $subPageId = $subPageData[id];
                $show = 1;
               
                $subData = $subPageData[data];
                if ($subData and !is_array($subData)) {
                    $subData = str2Array($subData);
                }
                if (!is_array($subData)) $subData = array();
                
                
                // hide Index
                if ($subPageData[name] == "index") $show =0;
                
                
                if ($useNav) {
                    $navShow = $subPageData[navigation];
                    if (!$navShow) $show = 0;
                }
                
                if ($hideAdmin) {
                    if (substr($subPageData[name],0,5)=="admin") {
                        $show = 0;
                    }                     
                }
                
                // echo ("Show = $show $subPageId $subPageData[title] <br>");
                if (count($subData) and $class=='index' AND $sessionName) {
                    // echo ("SUBDATA FOR $subPageData[name] - Ses = $sessionName ".$this->session_get($sessionName]."<br>");
                    foreach($subData as $subKey => $subValue) {
                        if (subStr($subKey,strlen($subKey)-3) == "Set" ) {
                            $sessionName = subStr($subKey,0,strlen($subKey)-3);
                            if ($subValue AND !$this->session_get($sessionName)) $show = 0;
                            // echo ("check Session for $sessionName <br>");
                        } else {
                            echo ("subPageNavi->show_index() - Not Session".subStr($subKey,strlen($subKey)-3)." <- $subKey <br>");
                        }
                    }
                }
                
                if ($show) {
                    $showLevel = $subPageData[showLevel];
                    if ($showLevel > $myLevel) {
                        $show = 0;    
                        // echo ("Hide because $showLevel ist größer $myLevel <br>");
                        if ($showLevel == 3) {
                            $allowedUser = $subData[allowedUser];
                            if ($allowedUser AND $myId) {
                                // echo ("Spezielle User asuwahl allowed='$allowedUser' myId = $myId <br>");
                                $userPos = strpos($allowedUser,"|".$myId."|");
                                if (is_int($userPos)) {
                                    // echo ("<h3>Allowed because is in allowedList </h3>");
                                    $show = 1;
                                }
                            }
                        }

                    } else {
                        // echo ("Zeigen weil $showLevel ist kleiner/gleich $myLevel <br>");
                        if ($showLevel == 3) {
                            $forbiddenUser = $subData[forbiddenUser];
                            if ($forbiddenUser AND $myId) {
                                // echo ("Spezielle User asuwahl forbidden='$forbiddenUser' myId = $myId <br>");
                                $userPos = strpos($forbiddenUser,"|".$myId."|");
                                if (is_int($userPos)) {
                                    //echo ("<h3>Forbidden because is in forbiddenList </h3>");
                                    $show = 0;
                                }
                            }
                        }
                    }
                }
                
                
                
                if ($show) {

                    $nr++;
                    $count ++;
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
                    

                    $showName = $this->lgStr($title);
                    
                    if (!$showName) $showName = $name;
                    
                    if ($wireFrameOn AND $wireframeState) {
                        if ($wireframeData[headLine]) {
                            $info = array("name"=>$showName,"nr"=>$count,"id"=>"id");
                            $showName = cmsWireframe_text($wireframeData[headLineText], $info);
                        }                        
                            
                    }

                    $url = $subPageData[url];
                    if (!$url) $url = $subPageData[name].".php";

                    
                    $itemClass = "subPageNavi cmsFrameLink";
                    if ($class) $itemClass.= " subPageNavi_$class";
                    
                    $selected = 0;
                    if ($pageData[id] == $subPageId) $selected = 1;
                    if ($subPageData[active]) $selected = 1;
                    if ($selected and $subPageData[dynamic]) {
                        $selected = 0;
                        if ($subPageData[active]) $selected = 1;
                        // echo ("<h3>$url</h3>");
                    }
                    
                    if ($selected) {
                        
                        $itemClass.= " subPageNavi_selected ";
                         if ($class) $itemClass.= " subPageNavi_selected_$class";
                    }
                    echo ("<div class='$itemClass' style='$style' >");
                    echo ("<div class='hiddenData'><a href='$url' class='hiddenLink'></a></div>");

                    if ($useIcons) {
                        if ($wireframeImage) {
                            if ($wireframeImageText) {
                                $img = cmsWireframe_frameStart_str($iconSize, $iconSize,"zoom_Div");
                                // $out .= "<a href='$bigImageStr' class=''>$wireframeImageText</a>";
                                $img .= $wireframeImageText;                    
                                $img .= cmsWireframe_frameEnd_str();
                            } else {
                                $imgStr = cmsWireframe_image($iconSize,$iconSize);
                                $img = "<img src='$imgStr' class='noBorder' />";
                            }
                        } else {
                            

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
                        echo ("des='$showText'");
                        if (is_array($showText)) foreach ($showText as $key => $value) echo ("$key = $value | ");
                        if (!$showText) {
                            $showText = $subPageData[pageTitle];
                        }
                        
                        $showText = lg::lgStr($showText);
                        
                        if ($wireFrameOn AND $wireframeState) {
                            $showText = cmsWireframe_text($wireframeData[textText], $showName);
                        }
                       
                        
                        if ($showText) {
                            if (is_array($showText)) foreach ($showText as $k =>$v ) echo ("$k=$v|");
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
                        div_end("subPageNavi_Line subPageNavi_Line_$class","before");
                        $zeilen++;
                    }




                    // show_array($subPageData);
                } // end of Show
            }

            if ($nr != 0) {
                $nr = 0;
                div_end("subPageNavi_Line subPageNavi_Line_$class","before");
            }
            
            div_end("subPageNaviFrame");
        }
    }

    function show_scrollTop() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        echo ("<div class='mainJavaButton cmsScroll'>Seitenanfang</div>");
    }

    function show_scrollList() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        
        $pageId = $GLOBALS[pageData][id];
        $pageName = "page_$pageId";
        $myId = "inh_".$contentData[id];
       //  echo ("<h1>Scroll List $pageName</h1>");
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        
        $myScroll = $this->page_getContent("all"); // "page_".$this->pageId); //,$mainId=null,$mainType=null)
      
        
        $direction = $data[direction];
        
        switch ($direction) {
            case "hori" :
                $divClass .= "";
                $divStyle .= "";
                break;
            case "vert" :
                $divClass .= "";
                $divStyle .= "display:block;margin:1px 0;";
                break;
            default:
                echo ("unkown Direction ($direction) in scrollList <br>");
        }
        // show_array($data);
        
        // $myScroll = cms_content_getAllList($pageName);
        // ShowList
        foreach ($myScroll as $id => $contData) {
            $show = 1;
            
            // Sich selber nicht zeigen
            if ($id == $myId) $show = 0;
            if ($data["hide_".$id]) {
                // echo ("Verstecke $id <br>");
                $show = 0;
            }
            
            if ($show) {
                $title = $contData[title];
                if (is_string($title)) {
                    $help = str2Array($title);
                    if (is_array($help)) {
                        $title = $this->lgStr($help); 
                    }
                }
                if (!$title) { 
                    $type = $contData[type];
                    $title = "Inhalt $type";
                }
                echo ("<div id='anker_".$contData[id]."' class='mainJavaButton cmsScroll' style='$divStyle' >$title</div>");
            }
        }
    }
    
    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();


        // MainData
        $res[subPageNavi][showName] = $this->lga("content","subPageTypeTab"); //"Wechselnde Inhalte";
        $res[subPageNavi][showTab] = "Admin";
        
        // Navigations-Art
        $type = $data[type];
        if (!$type) $type = "subPage";
        $addData = array();
        $addData["text"] = $this->lga("contentType_pageNavi","navigationType"); //"Navigations Art";
        $showData = array();
        $showData["empty"]= 0;
        $showData["submit"] = 1;
        $input = $this->filter_select("subNavi", $type, "editContent[data][type]", $showData);
        // $input .= cms_navi_SelectNaviType($type,"editContent[data][type]");
        $addData["input"] = $input; //cms_navi_SelectNaviType($type,"editContent[data][type]");
        $addData["mode"] = "Simple";
        $res[subPageNavi][] = $addData;
        
        $lgType = "editContent";
        $lgCode = "subNav";
        
        
        
        switch ($type) {
            case "mainPage" :
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."IconShow");
                if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[iconSize]) $data[iconSize] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."IconSize");
                $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."InfoShow");
                if ($data[info]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                break;
            
            
            case "subPage" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."Direction");
                $showData = array("empty"=>0,"submit"=>1);
                $addData["input"] = $this->filter_select("direction", $data[direction], "editContent[data][direction]", $showData);
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."StartLevel"); 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                $addData["mode"] = "Admin";
                $res[subPageNavi][] = $addData;


                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."MaxLevel");
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                $addData["mode"] = "Admin";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnCount");
                $addData["input"] = "<input type='text' name='editContent[data][columnCount]' value='$data[columnCount]' >";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[columnAbs]) $data[columnAbs] = 10;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnAbs");
                $addData["input"] = "<input type='text' name='editContent[data][columnAbs]' value='$data[columnAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;


                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."RowHeight");
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = "Zeilen Abstand";
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."IconShow");
                if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[iconSize]) $data[iconSize] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."IconSize");
                $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."InfoShow");
                if ($data[info]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;
                break;
                
            case "index" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."Direction");
                $showData = array("empty"=>0,"submit"=>1);
                $addData["input"] = $this->filter_select("direction", $data[direction], "editContent[data][direction]", $showData);
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."StartLevel"); 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                $addData["mode"] = "Admin";                
                // $res[] = $addData;


                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."MaxLevel");
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                $addData["mode"] = "Admin";
                // $res[] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnCount");
                $addData["input"] = "<input type='text' name='editContent[data][columnCount]' value='$data[columnCount]' >";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[columnAbs]) $data[columnAbs] = 10;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnAbs");
                $addData["input"] = "<input type='text' name='editContent[data][columnAbs]' value='$data[columnAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;


                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."RowHeight");
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = $this->lga($lgType,$lgCode."RowAbs");
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."IconShow");
                if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[iconSize]) $data[iconSize] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."IconSize");
                $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."InfoShow");
                if ($data[info]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;
                
                 $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."OnlyNav");
                if ($data[useNav]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][useNav]' value='1' $checked />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                break;
                
           case "parallel" :
                // Navigationsrichtung
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."Direction");
                $showData = array("empty"=>0,"submit"=>1);
                $addData["input"] = $this->filter_select("direction", $data[direction], "editContent[data][direction]", $showData);
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."OnlyNav");
                if ($data[useNav]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][useNav]' value='1' $checked />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."StartLevel"); 
                $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
                // $res[] = $addData;


                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."MaxLevel");
                $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
                // $res[] = $addData;

                $addData = array();
                if (!$data[columnCount]) $data[columnCount] = 4;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnCount");
                $addData["input"] = "<input type='text' name='editContent[data][columnCount]' value='$data[columnCount]' >";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[columnAbs]) $data[columnAbs] = 10;
                $addData["text"] = $this->lga($lgType,$lgCode."ColumnAbs");
                $addData["input"] = "<input type='text' name='editContent[data][columnAbs]' value='$data[columnAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;


                $addData = array();
                if (!$data[columnHeight]) $data[columnHeight] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."RowHeight");
                $addData["input"] = "<input type='text' name='editContent[data][columnHeight]' value='$data[columnHeight]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[rowAbs]) $data[rowAbs] = 10;
                $addData["text"] = $this->lga($lgType,$lgCode."RowAbs");
                $addData["input"] = "<input type='text' name='editContent[data][rowAbs]' value='$data[rowAbs]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."IconShow");
                if ($data[icon]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][icon]' value='1' $checked />";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                if (!$data[iconSize]) $data[iconSize] = 30;
                $addData["text"] = $this->lga($lgType,$lgCode."IconSize");
                $addData["input"] = "<input type='text' name='editContent[data][iconSize]' value='$data[iconSize]' >";
                $addData["mode"] = "More";
                $res[subPageNavi][] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."InfoShow");
                if ($data[info]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][info]' value='1' $checked />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                break;
                
            case "scrollList" :
                $pageId = $GLOBALS[pageData][id];
                $pageName = "page_$pageId";
                $myId = $contentData[id];
                
                
                $selectList = array();
                $selectList["hori"] = "lga";
                $selectList["verti"] = "lga";
               
                $addData = array();
                $addData["text"] = $this->lga($lgType,$lgCode."ScrollDirection");
                $showData = array("viewMode"=>"selectIcon","selectList"=>$selectList,"emptyText"=>0,"empty"=>0);
                // $input =  $this->editContent_selectSettings("direction",$data[direction], "editContent[data][direction]", $showData);
                $input = $this->filter_select("direction", $data[direction], "editContent[data][direction]", $showData);
                $addData["input"] = $this->filter_select("direction", $data[direction], "editContent[data][direction]", $showData);
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                
                
               //  $myScroll = cms_content_getAllList($pageName);

               
                $myScroll = $this->page_getContent("all");
               
                
                // ShowList
                $addToTab = "contentList";
                $contentStr = $this->lga("contentType_pageNavi","contenteName",": ");
                $dontShowStr = $this->lga("contentType_pageNavi","dontShow",": ");
                foreach ($myScroll as $id => $scrollData) {
                    $contId = $scrollData["id"];
                    if ($contId == $this->contentId) continue;
                   
                    $contType = $scrollData["type"];
                    $contTitle = $scrollData["title"];
                    
                    $mainId  = $scrollData["mainId"];
                    $mainType = $scrollData["mainType"];
                    if ($mainId) {
                        
                        //echo ("Found MainType $mainId $mainType cont $id / $contId / $contType <br>");
                    }
                    
                    $input = $dontShowStr; //"nicht zeigen ";
                    $addData = array();

                    $text = "";
                    if ($mainType) $text .= " -> $mainType -> ";
                    $text .= "Inhalt type=$contType";
                    $addData["text"] = $text; // "Inhalt type=$contType";
                    if ($data["hide_".$id]) $checked = "checked='checked'"; 
                    else $checked = "";
                    $input .= "<input type='checkbox' name='editContent[data][hide_".$id."]' value='1' $checked />";
                    
                    
                    if (is_string($contTitle)) {
                        $help = str2Array($contTitle);
                        if (is_array($help)) {
                            $contTitle = $help;
                            // sforeach ($contTitle as $k => $v) echo ("$contId $k = $v <br>");
                        }
                            
                    } 
                    
                        
                            
//                            $flipTitle = $value[title];
//                            $flipContentId = $value[id];
                    // $input .= "$contTitle id=$contId ";
                    $showData=array("out"=>"input","width"=>100);
                    $input .= $contentStr;
                    $input .= $this->editContent_languageString($contTitle,"updateOtherContent[$contId][title]", $showData);
//                        }
//                    }
                    
                    
                    
                   // $input .="Titel:<b>".span_text_str($contTitle)."</b>";
                     
//                    $input .= "<input type='hidden' name='oldTitle[$contId]' value='$contTitle' />";
//                    $input .= " Titel: <input type='text' name='setTitle[$contId]' value='$contTitle' />";
                    
                    $addData["input"] = $input;
                    $addData["mode"] = "More";
                    $res[subPageNavi][] = $addData;
                }
                break;

            case "scrollTop" :
                $showData = array();
                $showData[css] = 1;
                $showData[view] = "text";
                $showData[color] = 0;
                $showData[width] = 200;
                $showData[name] = $this->lga("contentType_pageNavi","buttonName");
                $showData[lgSelect] = 1;
                $showDara[mode] = "Simple";
                $addData = $this->editContent_text("scrollTop",$this->textData[scrollTop], $showData);
                $res[subPageNavi][] = $addData;


                $addData = array();
                $addData["text"] = $this->lga("contentType_pageNavi","buttonName"); // "Info zeigen";
                if ($data[info]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='type' name='editContent[data][buttonName]' value='$data[buttonName]'  />";
                $addData["mode"] = "Simple";
                $res[subPageNavi][] = $addData;
                break;

            default :
                echo "Unkown $type in edit SubPageNavi <br>";
        }        
        return $res;        
    }
    
    function filter_select_getList_own($filterType, $filter, $sort) {
        $res = array();
        switch ($filterType) {
            case "subNavi" :
                $res[subPage] = "lga"; // array("name"=>"Untergeordnete Seiten");
                $res[mainPage] = "lga"; // array("name"=>"Übergeordnete Seiten");
                $res[index] = "lga"; // array("name"=>"Unterseiten von Startseite");
                $res[parallel] = "lga"; // array("name"=>"Parallele Seite");
                $res[scrollTop] = "lga"; // array("name"=>"Sprungpunkt Seitenanfang");
                $res[scrollList] = "lga"; // array("name"=>"Sprungpunkt Liste");
                break;
            
            default :
                echo "unkown $filterType in subPageNavi <br>";
                
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
    return $subPageNaviClass->show($contentData,$frameWidth);
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
