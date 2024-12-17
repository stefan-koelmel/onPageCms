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
        
        $mobileNav = $data[mobileNav];
        if ($mobileNav) {
            $exit = $this->mobileNaviagtion();
            if ($exit) return 0;
        }                
        
        $standard = $this->navi_standard($data);
        
        $this->frameWidth = $frameWidth;
        
        $direction = $data[direction];
        if (!$direction) $direction = $standard[direction];
        // $direction = "vert";
        $startLevel = $data[startLevel];
        if (is_null($startLevel)) $startLevel = $standard[startLevel];
       
        
        $showLevel = $this->showLevel;
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
//            $cacheData["level"] = $this->showLevel;
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
        
        
        
        // if (is_object($this->pageClass)) echo ("pageClass exist <br>");
        // $naviList = $this->pageClass->page_groupList();
        
        $naviList = cms_navi_getNaviList($startLevel);
        // $naviList = page::groupList();
//        echo ("SubNavi <br>");
//        foreach ($naviList as $key => $value) {
//             $select = $value[select];
//             if ($select) {
//                 echo ("$key is has $select <br>");
//                 if (is_array($value[subNavi])) {
//                     echo ("-> and has subNavi <br>");
//                     foreach ($value[subNavi] as $key1 => $value1 ) {
//                         $select = $value1[select];
//                         if ($select) {
//                            echo (" ---> $key1 has $select <br>");
//                            if (is_array($value1[subNavi])) {
//                                echo ("-> and has subNavi <br>");
//                                foreach ($value1[subNavi] as $key2 => $value2 ) {
//                                    $select = $value2[select];
//                                    if ($select) {
//                                       echo (" ---> $key2 has $select <br>");
//                                       if (is_array($value1[subNavi])) {
//
//                                       }
//                                    }
//                                }
//                            }
//                         }
//                     }
//                 } 
//             }
//             //echo ("Navi $key $value <br>");
//        }
//            $title = $this->lgStr($value[title]);
//            if (is_array($title)) $title = $title[$lg];
//            echo ("$key = n=$value[name] t='$title' nav=$value[subNavi]<br>");
//            if (is_array($value[subNavi])) {
//                foreach ($value[subNavi] as $k => $v) {
//                     $title = $v[title];
//                     // if (is_array($title)) $title = $this->lgStr($v[title]);
//                
//                    echo (" -> $k  t='$title'<br>");
//                }
//            }
//        }
        
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
      
        $showLevel = $this->showLevel;
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
        $showLevel = $this->showLevel;
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
        $showLevel = $this->showLevel;
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
        
        //  echo ("navi_showLevel_0 $aktLevel $selectedLevel <br>");
        $out = "";
        $subOut = "";
        $myId = $this->session_get(userId);
        $maxLevel = $data[maxLevel];
        if (!$breadCrumb) $breadCrumb = "";
        // show_array($standard);
        
        if (!$maxLevel) $maxLevel = $standard[maxLevel];        
        
        // echo ("akt / max : $aktLevel / $maxLevel <br>");
        $showLevel = $this->showLevel;
        if (is_null($showLevel)) $showLevel = 0;
       // echo ("<li >ShowLevel = $showLevel</li>");

        $firstShow = 0;
        foreach($naviList as $id => $page) {
            $pageNavigation = $page[navigation];
            $pageShowLevel  = $page[showLevel];

            $show = 1; // $page[show];
            if (!$pageNavigation) $show = 0;
            
            if (!$show) continue;
            
            
            if ($page[hidden] ) {
                echo ("dont Show Page $id in Navi because $page[hidden] <br>");
                $show = 0;
                
            }
            if (!$show) continue;
            
            // SHOW 
            $id   = $page[id];
            $title = $page[title];
            $name = $page[name];
            $title = lg::lgStr($title); 
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
                if ($page[select]) $isSelectedLevel = $page[select];
                // $isSelectedLevel = "nischt";
                // if ($selectedLevel) $isSelectedLevel = "afterSelect";

               //  if ($page[beforeSelect]) $isSelectedLevel = "beforeSelect";
               //  if ($page[selectPage]) $isSelectedLevel = "select";
               //  if ($page[subSelect]) $isSelectedLevel = "subSelect";

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
        // if ($page[select]) $out .= "-".$page[select];
        $showSelect = 0 ;
        if ($showSelect AND $page[select]) {
            switch ($page[select]) {
                case "beforeSelect" : $out .= " s='bef'"; break;
                case "subSelect" : $out .= " s='sub'"; break;
                case "select" : $out .= " s='sel'"; break;
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
        $title = $this->lgStr($title);
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
        
        $lgaCode = "contentType_navi";
        $res = array();
        $res[navi][showName] = $this->lga("content","naviTab");
        $res[navi][showTab] = "Simple";
        // $res["more"] = array();
        
        
//        // GET TEXT
//        $editText = $_POST[editText];
//        if (!is_array($editText)) {
//            $id = $editContent[id];
//            $contentCode = "text_$id";
//            $editText = cms_text_getForContent($contentCode);
//        } 
//        
//        $sort = 1;
//        
//        $addData = array();
//        $addData["text"] = "hidden-Text Id";
//        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
//        $addData["mode"] = "Simple";
//        $res[header][] = $addData;
        


        $direction = $data[direction];
        if (!$direction) $direction = "hori";
        // MainData
        $addData = array();
        $addData["text"] = "Navigationsrichtung";
        $addData["input"] = $this->navi_SelectDirection($direction,"editContent[data][direction]");
        $addData["mode"] = "Simple";
        $res[navi][] = $addData;
        
        
        $addData = array();
        $addData["text"] = "Breite Ebene 1";
        $addData["input"] = "<input type='text' value='$data[level_1_width]' name='editContent[data][level_1_width]' />";
        $addData["mode"] = "More";
        $res[navi][] = $addData;
        
        $addData = array();
        $addData["text"] = "Breite Ebene 2";
        $addData["input"] = "<input type='text' value='$data[level_2_width]' name='editContent[data][level_2_width]' />";
        $addData["mode"] = "More";
        $res[navi][] = $addData;
        
        $addData = array();
        $addData["text"] = "Breite Ebene 3";
        $addData["input"] = "<input type='text' value='$data[level_3_width]' name='editContent[data][level_3_width]' />";
        $addData["mode"] = "More";
        $res[navi][] = $addData;
        
        
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
                $addData["mode"] = "Simple";
                $res[navi][] = $addData;
                break;
                
                
        }
        
        

        $addData = array();
        $addData["text"] = "Startebene"; 
        $addData["input"] = "<input type='text' name='editContent[data][startLevel]' value='$data[startLevel]' >";
        $addData["mode"] = "Simple";
        $res[navi][] = $addData;


        $addData = array();
        $addData["text"] = "Maximale Tiefe";
        $addData["input"] = "<input type='text' name='editContent[data][maxLevel]' value='$data[maxLevel]' >";
        $addData["mode"] = "Simple";
        $res[navi][] = $addData;


        if ($this->mobileEnabled) {
            $addData = array();
            $addData[text] = $this->lga($lgaCode,"MobileNavigation");
            $mobileNav = $data[mobileNav];
            if ($mobileNav) $checked = "checked='checked'"; else $checked ="";
            
            $addData[input] = "<input type='checkbox' value='1' name='editContent[data][mobileNav]' $checked />";
            $addData[mode] = "More";
            $res[navi][] = $addData;
            
            if ($mobileNav) {
                $addData = array();
                $addData[text] = "Titel Sichtbar"; // $this->lga($lgaCode,"MobileNavigation");
                $mobileShow = $data[mobileShowTitle];
                if ($mobileShow) $checked = "checked='checked'"; else $checked ="";
                $addData[input] = "<input type='checkbox' value='1' name='editContent[data][mobileShowTitle]' $checked />";
                $addData[mode] = "More";
                $res[navi][] = $addData;               
                
                $addData = array();
                $addData[text] = "Überlagernd"; // $this->lga($lgaCode,"MobileNavigation");
                $mobileOverlay= $data[mobileOverlay];
                if ($mobileOverlay) $checked = "checked='checked'"; else $checked ="";
                $addData[input] = "<input type='checkbox' value='1' name='editContent[data][mobileOverlay]' $checked />";
                $addData[mode] = "More";
                $res[navi][] = $addData;    
                
                $addData = array();
                $addData[text] = "Pfad-Navigation"; // $this->lga($lgaCode,"MobileNavigation");
                $mobileBreadCrumb = $data[mobileBreadCrumb];
                if ($mobileBreadCrumb) $checked = "checked='checked'"; else $checked ="";
                $addData[input] = "<input type='checkbox' value='1' name='editContent[data][mobileBreadCrumb]' $checked />";
                $addData[mode] = "More";
                $res[navi][] = $addData;               
            }
            
        }
        
        
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

    
    function mobileNaviagtion() {
        $exit = 0;
        
        if (!$this->mobileEnabled) return $exit;
        
        $exit = 1;
        
        if ($this->targetData[target] == "pc") {
            
            // echo ("ist Desktopaansicht - eigentlich exit<br>");
            $exit = 0;
            return $exit;
            
        }
        
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        $titleLine = $data[mobileShowTitle];
        $overlay   = $data[mobileOverlay];
        $breadCrumb = $data[mobileBreadCrumb];
        
        
        $pageListStr = $this->mobileNavigation_pageList();
        
        
        
        if ($titleLine) {
           $titleLineStr = $this->mobileNavigation_titleLine();            
        }
        
        echo ($titleLineStr);
        
        echo ($pageListStr);
        
       
        
        if ($breadCrumb) {
            $subOut = "";
            foreach ($this->subPageList as $pageId => $out ) {
                $subDiv = "mobileNavigation_subList mobileHidden";
                if ($overlay) $subDiv .= " mobileNavigation_pageList_overlay";
                $subData = array("id"=>"subList_$pageId");
                $subOut .= div_start_str($subDiv,$subData);
                $subOut .= $out;
                $subOut .= div_end_str($subDiv);        
            }
            echo ($subOut );
        }
        
        
        return $exit;
        
    }
    
    function mobileNavigation_titleLine() {     
        $clickAble = 1;
        $breadCrumb = $this->contentData[data][mobileBreadCrumb];
        
        
        $divName = "mobileNavigation_titleLine";
        if ($breadCrumb) $divName.= " mobileNavigation_titleLine_dontOpen";
        
        $divData = array("id"=>"mobileNav_".$this->contentId);
        $str = div_start_str($divName,$divData);
        if (!$breadCrumb ) {
            $str .= "TITLELINE ";
        } else {
            $itemDiv = "mobileNavigation_titleLine_item";
           
            $start = 0;
            foreach ($this->selectPageList as $pageId => $pageData) {
                if (!$start) {
                    if ($pageId != 1) {
                        $indexData = $this->page_getData("index");
                        if (is_array($indexData)) {
                            $link = $indexData[name].".php";
                            $name = $indexData[title];
                            if (is_array($name)) $name = $this->lgStr($name);
                            $str .= "<div class='$itemDiv' id='mainMenu' >";
                            $str .= $name; 
                            $str .= "<div class='mobileNavigation_titleLine_item_hasSub'>&nbsp;</div>";   
                            $str .= "</div>";
                        }
                    } 
                    $start = 1;
                }


                $link = $pageData[link];
                $name = $pageData[name];
                $level = $pageData[level];
                //  $str .= " $name | $link | $level >> ";


                $itemDiv = "mobileNavigation_titleLine_item";
               
                $str .= "<div class='$itemDiv' id='itemId_$pageId' >";
                $str .= $name;
                if ($this->subPageList[$pageId]) {
                    $str .= "<div class='mobileNavigation_titleLine_item_hasSub'>&nbsp;</div>";                
                }
                $str .= "</div>";
            }
        }
            
        
        $str .= div_end_str($divName);     
        return $str;
    }
    
    function mobileNavigation_pageList() {
        $overlay = $this->contentData[data][mobileOverlay];
        
        $this->selectPageList = array(); 
        $this->subPageList = array(); 
        $divName = "mobileNavigation_pageList";
        if ($overlay ) $divName .= " mobileNavigation_pageList_overlay";
        $divName .= " mobileHidden";
        $divData = array("id"=>"mobileNav_List_".$this->contentId);
        $str = div_start_str($divName,$divData);
        
        $naviList = cms_navi_getNaviList($startLevel);
        
        $level = 0;
        foreach ($naviList as $pageId => $pageData ) {
            $item = $this->mobileNavigation_item($level,$pageId,$pageData);
            if ($item) $str .= $item;
        }
        $str .= div_end_str($divName);   
        
        return $str;
    }
    
    function mobileNavigation_item($level,$pageId,$pageData) {
        $str = "";
     
        $show =  $pageData[navigation];
        if (!$show) return 0;
        
        $showLevel = $pageData[showLevel];
        $toLevel  = $pageData[toLevel];
        
        
        $name  = $pageData[name];
        $title = $pageData[title];
        $id    = $pageData[id];
        
        $select = $pageData[select];
    
        $title = $this->lgStr($title);
        $goPage = $name.".php";
        if ($pageData[addUrl]) $goPage .= "?$pageData[addUrl]";
        
        $str .= $title; //  "name='$name' title='$title' $goPage s='$select'";
        
        $itemDiv = "mobileNavigation_item mobileNavigation_level_$level";
        
        $itemStyle = "";
//        switch ($select) {
//            case "select" :  
//                
//                $itemStyle = "background-color:#f00;color:#fff;";
//                break;
//            case "subSelect" :
//                $itemStyle = "background-color:#f66;color:#eee;";
//                break;                
//        }
        
        if ($select) {
            $this->selectPageList[$id] = array("name"=>$title,"level"=>$level,"link"=>$goPage);
            $itemDiv .= " mobileNavigation_item_$select";
        }
        
        $subOut = "";
        if (is_array($pageData[subNavi])) {
            $subLevel = $level + 1; 
            foreach ($pageData[subNavi] as $subPageId => $subPageData) {
                $subItem = $this->mobileNavigation_item($subLevel,$subPageId,$subPageData);
                if ($subItem) $subOut .= $subItem;                
            }
        }
        
        
        $out = div_start_str($itemDiv,$itemStyle);
        if ($goPage) $out.= "<a href='$goPage' class='mobileNavigationLink'>";
        // if ($goPage) $out.= "<div class='mobileNavigationLink'>";
        $out .= $str;
        if ($goPage) $out.= "</a>";
        //if ($goPage) $out .= "</div>";
        if ($subOut) { // 
            $subClass = "mobileNavigation_sub";
            if ($select) $subClass .= " mobileNavigation_subOpen";
            else $subClass .= " mobileNavigation_subClose";
            
            $out .= "<div class='$subClass'>";
            $out .= "&nbsp;";
            $out .= "</div>";
            
        }
        $out .= div_end_str($itemDiv);
        
        if (is_array($pageData[subNavi])) {
//            $subOut = "";
//          
//            $subLebel = $level + 1; 
//            foreach ($pageData[subNavi] as $subPageId => $subPageData) {
//                $subItem = $this->mobileNavigation_item($subLebel,$subPageId,$subPageData);
//                if ($subItem) {
//                    $subOut .= $subItem;                                    
//                }
//            }
//            
            if ($subOut AND $select) {
                if ($select) {
                    $this->subPageList[$pageId] = $subOut;
                }
            }
           
            if ($subOut) {
                $subDiv = "mobileNavigation_itemList";
                if ($subLevel > 0) {
                    if (!$select) {
                       $subDiv .= " mobileHidden";
                    }
                }
                
                $out .= div_start_str($subDiv);
                $out .= $subOut;
                $out .= div_end_str($subDiv);
            }
        }
        
        if ($out) {
            $str = "<div class='trulla'>";
            $str .= $out;
            $str .= "</div>";
            return $str;
        }
        
        return $out;
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
