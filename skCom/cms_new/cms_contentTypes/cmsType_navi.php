<?php // charset:UTF-8
class cmsType_navi_base extends cmsClass_content_show {
    function getName() {
        return "Navigation";
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $standard = $this->navi_standard($data);
        
        $this->frameWidth = $frameWidth;
        
        $direction = $data[direction];
        if (!$direction) $direction = $standard[direction];
        // $direction = "vert";
        $startLevel = $data[startLevel];
        if (is_null($startLevel)) $startLevel = $standard[startLevel];
       
        
        $showLevel = $_SESSION[showLevel];
        if (!$showLevel) $showLevel = 0;
  
        // $useCache = cmsCache_state();
        $useCache = 0;
        if ($useCache) {
            $cacheStr = "";
            $cacheStr .= "dir-".$direction;
            if ($showLevel) $cacheStr .= "|lev-".$showLevel;
            if ($frameWidth) $cacheStr .= "|wid-".$frameWidth;
            
//            $cacheData = array();
//            $cacheData["dir"] = "hori";
//            $cacheData["level"] = $_SESSION[showLevel];
//            $cacheData["width"] = $frameWidth;
//            $cacheData["useMd5"] = 0;
//             $cache_outStr = cmsCache_get("navi", $cacheStr,"");
//             $cacheFile = cmsCache_getFileName("navi", $cacheStr,"");
//             echo ("CacheFile = $cacheFile <br />");
//            if ($cache_outStr) {
//                echo ("CacheOutStr = <br>".$cache_outStr);
//                return 0;
//            }
        }
        
        
        
        
        $naviList = cms_navi_getNaviList($startLevel);
        
        switch ($direction) {
            case "hori" :
                $out = $this->navi_horizontal($contentData,$data,$naviList,$standard,$frameWidth);
                break;
            case "vert" :
                $out = $this->navi_vertical($contentData,$data,$naviList,$standard,$frameWidth);
                break;

            case "vertRight" :
                $out = $this->navi_verticalRight($contentData,$data,$naviList,$standard,$frameWidth);
                break;


            default :
                echo ("unkown Direction in navi->show() $direction <br />");
        }
        
        if ($out AND $useCache) {
            if ($useCache) {
                $res = cmsCache_save("navi", $cacheStr,0,$out);
            }
        }
        echo ($out);
        
        
        
        
        // $this->cms_navi_showLayout($contentData,$frameWidth);
    }
    
    
    function navi_standard($data) {
        $standard = array();
        $standard[border] = 1;
        $standard[naviBorder] = 0;
        $standard[borderColor] = "#fff";
        $standard[backcolor] = "#ccc";
        $standard[paddingStep] = 5;
        $standard[paddingLeft] = 5;
        $standard[maxLevel] = 3;
        $standard[selectMaxLevel] = 3;
        $standard[direction] = "hori";
        $standard[startLevel] = 0;
        
        switch ($data[directon]) {
            case "hori" : 
                break;
            case "vert" :
                break;                
        }
        
        return $standard;
    }
    
    
    function navi_horizontal($contentData,$data,$naviList,$standard,$frameWidth) {
      
        $showLevel = $_SESSION[showLevel];
        if (!$showLevel) $showLevel = 0;
        
        $border = $standard[naviBorder];
        $mainWidth = $frameWidth - (2 * $border) - 0;
        $divData = array();
        $divData[style] = ""; //width:".$mainWidth."px;";
        //$divData[id] = "mainmenu";
        
        $outText = "";
        
        $outText .= div_start_str("main_menu main_menu_horizontal",$divData);
        $outText .= "<div id='mainmenu'>";

        // show_array($naviList,2);

        $breadCrumb = "";
        $outText .= "<ul class=''>\n";
        // echo("  <li class='pureCssEmptySmall'>HIER</li>\n");

        $isSelectedLevel = 0;
        $outData = $this->navi_showLevel_0(0,$isSelectedLevel,"hori",$naviList,$data,$standard,$breadCrumb);
        $out = $outData[out];
        $subOut = $outData[subOut];
        if ($out) {
            $outText .= $out; 
        }
        
        
        // echo("  <li class='pureCssEmptySmall'></li>\n");
        $outText .= "</ul>\n";
        
        if ($subOut) {
            $outText .= "<br >";
            $outText .= "<ul class=''>\n";
            $outText .= $subOut;
            $outText .= "</ul>";
        }
        
        
        $outText .= "</div>";
        $outText .= div_end_str("main_menu main_menu_horizontal","before");
        return $outText;
    }


     function navi_vertical($contentData,$data,$naviList,$standard,$frameWidth) {
        $showLevel = $_SESSION[showLevel];
        if (!$showLevel) $showLevel = 0;

        $border = $standard[naviBorder];
        $mainWidth = $frameWidth - (2 * $border) - 0;
        $divData = array();
        // $divData[style] = "width:".$mainWidth."px;";
        //$divData[id] = "mainmenu";

        div_start("main_menu main_menu_vertical",$divData);
        echo ("<div id='mainmenu'>");

        // show_array($naviList,2);

        $level_1_width = $data[level_1_width];
        $style = "";
        
        if ($level_1_width) {
            $level_1_width = $this->position_getWidth($level_1_width,$frameWidth);
            $style .= "width:".$level_1_width."px;";
        }
        $breadCrumb = "";
        if ($style) $style = "style='$style'";
        echo("<ul class='' $style >\n");
        // echo("  <li class='pureCssEmptySmall'></li>\n");


        $isSelectedLevel = 0;
        
        $outData = $this->navi_showLevel_0(0,$isSelectedLevel,"vert",$naviList,$data,$standard,$breadCrumb);
        $out = $outData[out];
        $subOut = $outData[subOut];
        if ($out) echo ($out); 
       
        // echo("  <li class='pureCssEmptySmall'></li>\n");
        echo("</ul>\n");
        echo ("</div>");
        div_end("main_menu main_menu_vertical","before");
    }


function navi_verticalRight($contentData,$data,$naviList,$standard,$frameWidth) {
        $showLevel = $_SESSION[showLevel];
        if (!$showLevel) $showLevel = 0;

        $border = $standard[naviBorder];
        $mainWidth = $frameWidth - (2 * $border) - 0;
        $divData = array();
        $divData[style] = "width:".$mainWidth."px;";
        //$divData[id] = "mainmenu";

        div_start("main_menu main_menu_vertical",$divData);
        echo ("<div id='mainmenu'>");

        // show_array($naviList,2);

        $breadCrumb = "";
        echo("<ul class=''>\n");
        // echo("  <li class='pureCssEmptySmall'></li>\n");

        $isSelectedLevel = 0;
        $this->navi_showLevel_0(0,$isSelectedLevel,"vertRight",$naviList,$data,$standard,$breadCrumb);

        // echo("  <li class='pureCssEmptySmall'></li>\n");
        echo("</ul>\n");
        echo ("</div>");
        div_end("main_menu main_menu_vertical","before");
    }


    function navi_showLevel_0($aktLevel,$selectedLevel,$direction,$naviList,$data,$standard,$breadCrumb="") {
        
        $out = "";
        $subOut = "";
        $myId = $_SESSION[userId];
        $maxLevel = $data[maxLevel];
        if (!$breadCrumb) $breadCrumb = "";
        // show_array($standard);
        
        if (!$maxLevel) $maxLevel = $standard[maxLevel];        
        
        // echo ("akt / max : $aktLevel / $maxLevel <br>");
        $showLevel = $_SESSION[showLevel];
        if (is_null($showLevel)) $showLevel = 0;
       // echo ("<li >ShowLevel = $showLevel</li>");

        $firstShow = 0;
        foreach($naviList as $id => $page) {
            $pageNavigation = $page[navigation];
            $pageShowLevel  = $page[showLevel];

            $show = 1; // $page[show];
            if (!$pageNavigation) $show = 0;
            
            
            if ($show) {
                $subData = $page[data];
                
            
                if ($showLevel < $pageShowLevel) {
                    $show = 0;
                     // echo ("Hide because $showLevel ist größer $myLevel <br>");
                    if ($pageShowLevel == 3) {
                        $allowedUser = $subData[allowedUser];
                        if ($allowedUser AND $myId) {
                            // echo ("Spezielle User $page[name] $page[id] auswahl allowed='$allowedUser' myId = $myId <br>");
                            $userPos = strpos($allowedUser,"|".$myId."|");
                            if (is_int($userPos)) {
                                // echo ("<h3>Allowed because is in allowedList </h3>");
                                $show = 1;
                            }
                        }
                    }
                    
                    
                } else {
                    $show = 1;
                    if ($pageShowLevel == 3) {
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
                $id   = $page[id];
                $title = $page[title];
                $name = $page[name];
                $title = cms_text_getLg($title);
                $goPage = $name.".php";
                if ($page[addUrl]) $goPage .= "?$page[addUrl]";
                
                $showName = $title;
                if ($showName == "") $showName = $name;
                $class = "";
                $subClass = "naviFrame_level_".($aktLevel+1);
                $linkClass = "";
            
               
                
                $subNavi = 0;
                if (is_array($page[subNavi]) AND count($page[subNavi])) {
                    $subNavi = 1;
//                    if ($maxLevel) {
//                        if ($aktLevel > $maxLevel) $subNavi = 0;
//                    }                     
                }
                $subLevelShow = 0;
                if ($subNavi) {
                    if ($data[showSubNavi]) {
                        // if ($page[select]) $subLevelShow = 1;
                        //if ($page[selectPage] OR $page[subSelect]) $subLevelShow = 1;
                        // if ($selectedLevel == "afterSelect") $subLevelShow = 1;
                        
                        // $out .= "<ul class='subLevelDiv_".($aktLevel+1)."' style='' >";                        
                    } 
                    
                    if (!$subLevelShow) {                    
                        $class .= " hasSub";
                    }
                }
                
                // if ($subLevelShow) {
                    
                    
                    switch ($selectedLevel) {
                        case "select" :
                            // $class .= " subLevelSelectBefore subLevel_$aktLevel";
                            $linkClass .= " subLevelLink_$aktLevel";
                            break;
                        case "subSelect" :
                            // $class .= " subLevelSubSelectBefore subLevel_$aktLevel";
                            $linkClass .= " subLevelLink_$aktLevel";
                            break;
                        case "beforeSelect" :
                            // $class .= " subLevelSubSelectBefore subLevel_$aktLevel";
                            $linkClass .= " subLevelLink_$aktLevel";
                            break;
                        
                        default : 
                            echo ("<h1> '$selectedLevel' </h1>");
                    }
               //}                 

                if ($aktLevel == 0 OR $data[showSubNavi]) {
                    switch ($direction) {
                        case "hori" : $subClass .= " posBottom"; break;
                        case "vert" : 
                            $subClass .= " posRight";
                            $class .= " mainVert";
                            break;
                         case "vertRight" :
                            $subClass .= " posLeft";
                            $class .= " mainVert";
                            break;

                    }
                }

                switch ($direction) {
                    case "vertRight" :
                        $subClass .= " posLeft";
                       //  $class .= " mainVert";
                        break;
                }

                if (!$firstShow) {
                    $class .= " firstItem";
                    $firstShow = 1;
                }
//                if ($page[selectPage]) {
//                    $linkClass .= " select";
//                    $class .= " select";
//                }
//                if ($page[subSelect]) {
//                    $linkClass .= " subSelect";
//                    $class .= " subSelect";
//                }

                switch ($page[select]) {
                    case "select" :
                        $linkClass .= " select";
                        $class .= " select";
                        break;
                    case "subSelect" :
                        $linkClass .= " subSelect";
                        $class .= " subSelect";
                        break;
                    case "beforeSelect" :
                        $linkClass .= " beforeSelect";
                        $class .= " beforeSelect";
                        break;
                }

                
                // get Item
                $subBreadCrumb = "";
                if ($breadCrumb) $subBreadCrumb = $breadCrumb."|";
                $subBreadCrumb .= $showName;
                // $item = $this->navi_getItem($direction,$page,$subBreadCrumb,$subNavi,$aktLevel);
                
                
                if ($subNavi) {
                   
                    $isSelectedLevel = 0;
                    // if ($page[select]) $isSelectedLevel = $page[select];
                    // $isSelectedLevel = "nischt";
                    // if ($selectedLevel) $isSelectedLevel = "afterSelect";
//                    if ($page[beforeSelect]) $isSelectedLevel = "beforeSelect";
//                    if ($page[selectPage]) $isSelectedLevel = "select";
//                    if ($page[subSelect]) $isSelectedLevel = "subSelect";
                    
                    $li_Item =  $this->navi_showItem($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel);
                    $out .= $li_Item;

                    $outData = $this->navi_showLevel_0($aktLevel+1,$isSelectedLevel,$direction,$page[subNavi],$data,$standard,$subBreadCrumb);
                    $subOut = $outData[out];
                    // $subSubOut = $outData[subOut];
                    
                    if ($page[selectPage] OR $page[subSelect]) {
                        if ($data[showSubNavi]) {
                            $outData = $this->navi_showLevel_0($aktLevel+1,$isSelectedLevel,$direction,$page[subNavi],$data,$standard,$subBreadCrumb);
//                            foreach ($outData as $key => $value) {
//                                echo ("$key = ".strlen($value)." <br>");
//                            }
                            $subOut = 0;
                            $subSubOut = $outData[out];                            
                        }
                        
                    }
                    
                    
                    
                    if ($subOut) { // untermenu vorhanden
                        switch ($aktLevel) {
                            case 0 :  $level_width = $data[level_2_width]; break;
                            case 1 :  $level_width = $data[level_3_width]; break;
                            case 2 :  $level_width = $data[level_4_width]; break;
                                default :
                                    //  $level_width = 300+$aktLevel;
                        }
                       
                        $style = "";
                        if ($level_width) {
                            $level_width = $this->position_getWidth($level_width,$this->frameWidth);
                            $style .= "width:".$level_width."px;";
                        }
                        if ($style) $style = "style='$style'";
                        
                        //jetzt unterMenu von Ebene 1
                        
                        
                        $out .= "      <ul class='$subClass' $style >\n";
                        //  foreach ($page[subNavi] as $key => $value) echo ("<li>SubPage $key $value[name]</li>");
                        
                        // show Untermenu
                        $out .= $subOut;
                        $out .= "      </ul>\n";
                        
                    }
                    
                    // echo("  <!--[if lte IE 6]></td></tr></table></a><![endif]-->");
                    $out .= "</li>\n";
                    
                    if ($direction == "vert" AND $subSubOut) {
                        $out .= $subSubOut;
                        $subSubOut = "";
                    }
                } else {
                    $li_Item =  $this->navi_showItem($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel);
                    $out .= $li_Item;                    
                }
                
                
               
            } // end of Show
            else {
                // echo ("<li>dont Show $page[name] $page[show] $page[navigation]</li>");
            }
        } // end of foreach
        return array("out"=>$out,"subOut"=>$subSubOut);
    }



    function navi_showItem($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel) {
        $useLink = 1;
        $useLinkDiv = 0;
        $item = $this->navi_getItem($direction,$page,$goPage,$linkClass,$subBreadCrumb,$subNavi,$aktLevel);
        if (is_array($item)) {
            if ($item[noLink]) $useLink = 0;
            if ($item[divLink]) $useLinkDiv = 1;

            $item = $item[out];
        }

        $res = $this->navi_showItem_own($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel);
        if ($res) return $res;
        $out = "";
        $out .= "<li class='$class'>";
        
        if ($useLink) $out .= "<a class='$linkClass' href='$goPage'>";
        if ($useLinkDiv) {
            $out.= div_start_str("naviItemDiv");
            $out .= "<a class='hiddenLink' href='$goPage'>LINK</a>";
        }

        
        // if ($page[select]) $out .= "s='$page[select]' ";
        // $out .= $aktLevel." - "; //.$item;
        // $out .= "sel='$page[selectPage]' subSel='$page[subSelect]' ";
        $out .= $item;
        $showSelect = 0;
        if ($showSelect AND $page[select]) {
            switch ($page[select]) {
                case "beforeSelect" : $out .= " s='b'"; break;
                case "subSelect" : $out .= " s='s'"; break;
                case "select" : $out .= " s='p'"; break;
                default:
                    $out .= "s='$page[select]'";
            }
        }
        
        if ($useLink) $out .= "</a>";
        if ($useLinkDiv) {
            $out.= div_end_str("naviItemDiv","before");
        }

        if ($subNavi) {
            // no </li>
        } else {
            $out .= "</li>\n";
        }
        return $out;
    }

    function navi_showItem_own($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel) {
        return 0;
    }


    function navi_getItem($direction,$page,$goPage,$linkClass,$breadCrumb,$subNavi,$level) {
        $out = $this->navi_getItem_own($direction,$page,$goPage,$linkClass,$breadCrumb,$subNavi,$level);
        if ($out) return ($out);

        $out = "";

        $title = $page[title];
        $title = cms_text_getLg($title);
        $name = $page[name];
                
        $showName = $title;
        if ($showName == "") $showName = $name;
        
        $out = "";
        if ($subNavi) {
        //    $out .= "<span>";
        }
        $out .= $showName;
        
        // $out.= " -> ".$breadCrumb;
        if ($subNavi) {
 //            $out .= "</span>";
        }
        return $out;
                
    }

    function navi_getItem_own($direction,$page,$goPage,$linkClass,$breadCrumb,$subNavi,$level) {
        $out = "";
        return $out;
    }

  function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;

        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();


        $direction = $data[direction];
        if (!$direction) $direction = "hori";
        // MainData
        $addData = array();
        $addData["text"] = "Navigationsrichtung";
        $addData["input"] = $this->navi_SelectDirection($direction,"editContent[data][direction]");
        $res[] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Breite Ebene 1";
        $addData["input"] = "<input type='text' value='$data[level_1_width]' name='editContent[data][level_1_width]' />";
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = "Breite Ebene 2";
        $addData["input"] = "<input type='text' value='$data[level_2_width]' name='editContent[data][level_2_width]' />";
        $res[] = $addData;
        
        $addData = array();
        $addData["text"] = "Breite Ebene 3";
        $addData["input"] = "<input type='text' value='$data[level_3_width]' name='editContent[data][level_3_width]' />";
        $res[] = $addData;
        
        
        // MainData
        switch ($direction) {
            case "hori" : 
                break;
            case "vert" :
               
                
                $addData = array();
                $addData["text"] = "Aktive Unterebenen anzeigen";
                $showSubNavi = $data[showSubNavi];
                if ($showSubNavi) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' name='editContent[data][showSubNavi]' $checked >";
                $res[] = $addData;
                break;
                
                
        }
        
        

        $addData = array();
        $addData["text"] = "Startebene"; 
        $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
        $res[] = $addData;


        $addData = array();
        $addData["text"] = "Maximale Tiefe";
        $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
        $res[] = $addData;


        return $res;
    }


    function navi_getDirections() {
         $res = array();
         $res[hori] = array("name"=>"Horizontal");
         $res[vert] = array("name"=>"Vertikal");
         $res[vertRight] = array("name"=>"Vertikal-Rechts");

         return $res;
     }


    function navi_SelectDirection($type,$dataName,$showData=array()) {
        $typeList = $this->navi_getDirections();
        $str = "";

        if ($showData[submit]) {
            $submitStr = "onChange='submit()'";
        } else {
            $submitStr = "";
        }

        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' $submitStr >";
        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code'";
             if ($code == $type)  $str.= " selected='1' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";
        return $str;
    }

}







function cmsType_navi_class() {
    if ($GLOBALS[cmsTypes]["cmsType_navi.php"] == "own") $naviClass = new cmsType_navi();
    else $naviClass = new cmsType_navi_base();
    return $naviClass;
}


function cmsType_navi($contentData,$frameWidth) {
    $naviClass = cmsType_navi_class();
    $naviClass->show($contentData,$frameWidth);    
}



function cmsType_navi_editContent($editContent,$frameWidth) {
    $naviClass = cmsType_navi_class();
    $res = $naviClass->navi_editContent($editContent,$frameWidth);
    return $res;
}

 
?>
