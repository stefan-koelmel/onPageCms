<?php // charset:UTF-8
class cmsType_navi_base extends cmsType_contentTypes_base {
    function getName() {
        return "Navigation";
    }
    
    function show($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $standard = $this->navi_standard($data);
        
        
        $direction = $data[direction];
        if (!$direction) $direction = $standard[direction];
        // $direction = "vert";
        $startLevel = $data[startLevel];
        if (is_null($startLevel)) $startLevel = $standard[startLevel];
       
        $naviList = cms_navi_getNaviList($startLevel);
        
        switch ($direction) {
            case "hori" :
                $this->navi_horizontal($contentData,$data,$naviList,$standard,$frameWidth);
                break;
            case "vert" :
                $this->navi_vertical($contentData,$data,$naviList,$standard,$frameWidth);
                break;

            case "vertRight" :
                $this->navi_verticalRight($contentData,$data,$naviList,$standard,$frameWidth);
                break;


            default :
                echo ("unkown Direction in navi->show() $direction <br />");
        }
        
        
        
        
        
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
        $divData[style] = "width:".$mainWidth."px;";
        //$divData[id] = "mainmenu";
        div_start("main_menu main_menu_horizontal",$divData);
        echo ("<div id='mainmenu'>");

        // show_array($naviList,2);

        $breadCrumb = "";
        echo("<ul class=''>\n");
        // echo("  <li class='pureCssEmptySmall'></li>\n");

        
        $res = $this->navi_showLevel_0(0,"hori",$naviList,$data,$standard,$breadCrumb);
        if ($res) echo ($res); 
        
        // echo("  <li class='pureCssEmptySmall'></li>\n");
        echo("</ul>\n");
        echo ("</div>");
        div_end("main_menu main_menu_horizontal","before");
    }


     function navi_vertical($contentData,$data,$naviList,$standard,$frameWidth) {
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


        $res = $this->navi_showLevel_0(0,"vert",$naviList,$data,$standard,$breadCrumb);
        if ($res) echo ($res); 
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


        $this->navi_showLevel_0(0,"vertRight",$naviList,$data,$standard,$breadCrumb);

        // echo("  <li class='pureCssEmptySmall'></li>\n");
        echo("</ul>\n");
        echo ("</div>");
        div_end("main_menu main_menu_vertical","before");
    }


    function navi_showLevel_0($aktLevel,$direction,$naviList,$data,$standard,$breadCrumb="") {
        
        $out = "";
        
        $maxLevel = $data[maxLevel];
        if (!$breadCrumb) $breadCrumb = "";
        // show_array($standard);
        
        if (!$maxLevel) $maxLevel = $standard[maxLevel];        
        
        // echo ("akt / max : $aktLevel / $maxLevel <br>");
        $showLevel = $_SESSION[showLevel];


        $firstShow = 0;
        foreach($naviList as $id => $page) {
            $pageNavigation = $page[navigation];
            $pageShowLevel  = $page[showLevel];

            $show = 1; // $page[show];
            // echo ("<li>$page $page[name]</li>");
            if (!$pageNavigation) $show = 0;
            if ($showLevel < $pageShowLevel) $show = 0;
            
            if ($show) {
                $id   = $page[id];
                $title = $page[title];
                $name = $page[name];
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
               
                if ($subNavi) $class .= " hasSub";

                if ($aktLevel == 0) {
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
                if ($page[selectPage]) {
                    $linkClass .= " select";
                    $class .= " select";
                }
                if ($page[subSelect]) {
                    $linkClass = " subSelect";
                    $class .= " subSelect";
                }


                
                // get Item
                $subBreadCrumb = "";
                if ($breadCrumb) $subBreadCrumb = $breadCrumb."|";
                $subBreadCrumb .= $showName;
                // $item = $this->navi_getItem($direction,$page,$subBreadCrumb,$subNavi,$aktLevel);
                
                
                if ($subNavi) {
                    $li_Item =  $this->navi_showItem($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel);
                    $out .= $li_Item;

                    $subOut = $this->navi_showLevel_0($aktLevel+1,$direction,$page[subNavi],$data,$standard,$subBreadCrumb);
                    if ($subOut) { // untermenu vorhanden
                        //jetzt unterMenu von Ebene 1
                        $out .= "      <ul class='$subClass'>\n";
                        //  foreach ($page[subNavi] as $key => $value) echo ("<li>SubPage $key $value[name]</li>");
                        
                        // show Untermenu
                        $out .= $subOut;
                        $out .= "      </ul>\n";
                    }
                    
                    // echo("  <!--[if lte IE 6]></td></tr></table></a><![endif]-->");
                    $out .= "</li>\n";
                } else {
                    $li_Item =  $this->navi_showItem($direction,$page,$subNavi,$class,$linkClass,$goPage,$subBreadCrumb,$subNavi,$aktLevel);
                    $out .= $li_Item;
                }
            } // end of Show
//            else {
//                // echo ("<li>dont Show $page[show] $page[navigation]</li>");
//            }
        } // end of foreach
        return $out;
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

        $out .= $item;
        
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

  function navi_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();



        // MainData
        $addData = array();
        $addData["text"] = "Navigationsrichtung";
        $addData["input"] = $this->navi_SelectDirection($data[direction],"editContent[data][direction]");
        $res[] = $addData;

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
