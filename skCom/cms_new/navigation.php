<?php // charset:UTF-8

function cms_navi_show() {
    return 0;
    div_start("topFrame");
    echo ("<h1>Header</h1>");
    
    cms_navi_showLayout();
    div_end("topFrame");
    div_start("content","width:750px;float:left;");
}


function cms_navi_getNaviList($startLevel) {
    $naviList = cms_page_getSortList(1);
   
    if ($startLevel > 0) {
        $level = 1;
        // echo ("Start Level = $startLevel <br>");
        foreach ($naviList as $level1Code => $level1 ) {
            if ($level == $startLevel) {
                if ($level1[subSelect] OR $level1[selectPage]) {
                    // echo ("foundStartLevel <br>");
                    if (is_array($level1[subNavi])) {
                        return $level1[subNavi];
                    } else {
                        // echo ("NO SubNavi in Ebene $level<br>");
                        return array();
                    }
                    
                }
            }
            if ($level < $startLevel AND is_array($level1[subNavi])) {
                if ($level1[subSelect] OR $level1[selectPage]) {
                    $level = 2;
                    
                    foreach ($level1[subNavi] as $level2Code => $level2) {
                        if ($level2[subSelect] OR $level2[selectPage]) {
                            if ($level == $startLevel) {
                                // echo ("Found StartLevel in Ebene 2 <br>");
                                // echo ("$level2Code = $level2[name] $level2[subSelect] $level2[selectPage] <br>");
                                if (is_array($level2[subNavi])) {
                                    return $level2[subNavi];
                                } else {
                                    echo ("NO SubNavi in Ebene $level<br>");
                                    return array();
                                }
                            }

                            if ($level < $startLevel) {
                                $level = 3;
                                // echo ("Suche in Ebene $level<br>");
                                $level3 = $level2[subNavi];
                                foreach ($level2[subNavi] as $level3Code => $level3) {
                                    if ($level3[subSelect] OR $level3[selectPage]) {
                                        if ($level == $startLevel) {
                                            // echo ("Found StartLevel in Ebene $level <br>");
                                            // echo ("$level3Code = $level3[name] $level3[subSelect] $level3[selectPage] <br>");
                                       
                                            if (is_array($level3[subNavi])) {
                                                return $level3[subNavi];
                                            } else {
                                                echo ("NO SubNavi in Ebene $level<br>");
                                                return array();
                                            }
                                        }
                                    }
                                }
                                $level = 2;
                            }
                        }
                    }
                    $level = 1;
                }
            }            
        }
    }
    return $naviList;
}

function navi_getLink($name) {
    return $name;
    $serverPath = $_SERVER[REQUEST_URI];
    $serverPath = substr($serverPath,1,strlen($serverPath)-2);
    $pathList = explode("/",$serverPath);
    if ($pathList[0] == $GLOBALS[cmsName]) $ownCms = 1;
    if ($pathList[1] == "admin") $adminPath = 1;
    
    if (substr($name,0,5)== "admin" ) {
        if ($adminPath) $name = $name;
        else $name = "admin/$name";
    } else {
        if ($adminPath) {
            if ($ownCms) $name = "".$GLOBALS[cmsName]."/".$name;
        } else {
            $name = $name;
        }
    }
    
    // echo ("$_SERVER[PHP_SELF]");
    return $name;
    
    
}


function cms_navi_showLayout($contentData,$frameWidth) {
    if (!$frameWidth) $frameWidth = 150;
    $border = 1;
    $borderColor = "#fff";
    $backcolor = "#ccc";
    $paddingStep = 5;
    $paddingLeft = 5;
    $maxLevel = 3;
    $selectMaxLevel = 3;
    $direction = "vert";
    
    $data = $contentData[data];
    if (is_array($data)) {
        foreach($data as $key => $value) {
            switch ($key) {
                case "direction" : if ($value) $direction = $value; break;
                case "maxLevel" : if ($value) $maxLevel = intval($value); break;
                case "startLevel" : if ($value) $startLevel = intval($value); break;
                default :
                    echo ("unkown $key => $value <br>");
            }
        }
      
    }
   

    
    global $pageInfo,$pageData;
    $naviList = cms_navi_getNaviList($startLevel);


    $showLevel = $_SESSION[showLevel];
    if (!$showLevel) $showLevel = 0;
    

    switch($direction) {
        case "hori":
            $border = 0;
            $mainWidth = $frameWidth - (2 * $border) - 0;
            div_start("naviFrameHor","width:".$mainWidth."px;");

            // show_array($naviList,2);
            
            echo("<ul class='pureCssMenu pureCssMenum'>\n");
            echo("  <li class='pureCssEmptySmall'></li>\n");

            foreach($naviList as $id => $page) {
                $name = navi_getLink($page[name]);
                if ($page[addUrl]) {
                    $name .= "?$page[addUrl]";
                }
                
                
                $id   = $page[id];
                $title = $page[title];
                $pageNavigation = $page[navigation];
                $pageShowLevel = $page[showLevel];
                $show = $page[show];
                if ($pageNavigation AND $showLevel >= $pageShowLevel AND $show) {
                    $class = "pureCssMenui0";

                    $showName = $title;
                    if ($showName == "") $showName = $name;
                    //$classen = "naviMain";
                    //if ($page[selectPage]) $classen .= " naviActiv";
                    //if ($page[subSelect]) $classen .= " naviAboutActiv";

                    $class = "pureCssMenui0";                   
                    if ($page[selectPage]) $class = "pureCssMenuiA";
                    if ($page[subSelect]) $class = "pureCssMenuiB";

                    $classLink = $class." level_0_".$name;
                   //  if ($showName == "CMS Administration") show_array($page);
                    // $showName .= " $page[selectPage] - $page[subSelect]";
                    echo("  <li class='$class'><a class='$classLink' href='$name.php'>");
                    $anz = count($page[subNavi]);
                    if ($anz == 1) {

                        //echo ("eine Ebene <br>");
                        // show_array($page[subNavi]);

                    }
                    if (is_array($page[subNavi]) AND $maxLevel > 1 AND $anz>0) {
                        echo ("<span>$showName</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->\n");
                        echo ("      <ul class='pureCssMenum'>\n");

                        foreach($page[subNavi] as $subId => $subPage) {
                            $name = $subPage[name];
                            $goLink = $name;
                            $goLink .=".php";
                            if ($subPage[addUrl]) {
                                $goLink .= "?$subPage[addUrl]";
                            }
                            
                            
                            $id   = $subPage[id];
                            $title = $subPage[title];
                            $pageNavigation = $subPage[navigation];
                            $pageShowLevel = $subPage[showLevel];
                            if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                                $showName = $title;
                                if ($showName == "") $showName = $name;
                                echo("          <li class='pureCssMenui'><a class='pureCssMenui' href='$goLink'>");
                                $anzSub = count($subPage[subNavi]);
                                if (is_array($subPage[subNavi]) AND $maxLevel > 2 AND $anzSub>0 ) {
                                    echo ("<span>$showName</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->\n");
                                } else {
                                    echo ("$showName</a>\n");
                                }

                                if (is_array($subPage[subNavi]) AND $maxLevel > 2 AND $anz>0) {
                                    echo("              <ul class='pureCssMenum'>\n");

                                    foreach($subPage[subNavi] as $subSubId => $subSubPage) {

                                        $name = $subSubPage[name];
                                        $goLink = $name;
                                        $goLink .=".php";
                                        if ($subSubPage[addUrl]) {
                                            $goLink .= "?$subSubPage[addUrl]";
                                        }
                                        
                                        
                                        
                                        
                                        $id   = $subSubPage[id];
                                        $title = $subSubPage[title];
                                        //  echo($title);
                                        $pageNavigation = $subSubPage[navigation];
                                        $pageShowLevel = $subSubPage[showLevel];
                                        if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                                            $showName = $title;
                                            if ($showName == "") $showName = $name;

                                        // echo("          <li class='pureCssMenui'><a class='pureCssMenui' href='projekte.php?view=$view&sub=$nr'><span>$area</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->\n");
                                        // echo("              <ul class='pureCssMenum'>\n");

                                            echo("<li class='pureCssMenui'><a class='pureCssMenui' href='$goLink'>$showName - E2</a></li>\n");
                                        }
                                    }
                                    echo("              </ul>\n");
                                    echo("          <!--[if lte IE 6]></td></tr></table></a><![endif]--></li>\n");
                                }
                            }
                         }
                        echo("      </ul>\n");
                        echo("  <!--[if lte IE 6]></td></tr></table></a><![endif]--></li>\n");
                    } else {
                        echo("$showName</a></li>\n");
                    }


                    //echo ("<a href='$name.php' class='$classen'>$showName</a>");
                }
            }
            echo("  <li class='pureCssEmptySmall'></li>\n");
            echo("</ul>\n");





            div_end("naviFrameHor","before");

           
            break;

        case "vert" :
            $border = 0;

            $mainWidth = $frameWidth - (2 * $border) - 20;


            div_start("naviFrameVer","display:box;width:".$mainWidth."px;padding:".$border."px;");
            //echo ("<div class='mainMenu' style='display:box;width:".$mainWidth."px;'>");
            echo ("<ul id='css3menu1' class='topmenu' style='width:".$mainWidth."px;'>\n");

            foreach($naviList as $id => $page) {
                $name = navi_getLink($page[name]);
                $id   = $page[id];
                $title = $page[title];
                $pageNavigation = $page[navigation];
                $pageShowLevel = $page[showLevel];
                // ZEIGE 1. Ebene
                if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                    $showName = $title;
                    if ($showName == "") $showName = $name;
                    $classen = "naviMain";
                    if ($page[selectPage]) $classen .= "mainAktiv selected";

                    if (is_array($page[subNavi]) AND $maxLevel > 1) {
                        if ($page[selectPage] OR $page[subSelect]) { // aktuelle Seite ist Bericht

                            echo("<li class='mainAktiv selected' ><a href='$name.php' >$showName</a></li>\n");
                            foreach($page[subNavi] as $subId => $subPage) {
                                $name = $subPage[name];
                                $id   = $subPage[id];
                                $title = $subPage[title];
                                $pageNavigation = $subPage[navigation];
                                $pageShowLevel = $subPage[showLevel];
                                $showName = $title;
                                if ($showName == "") $showName = $name;
                                if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                                    if (is_array($subPage[subNavi]) AND $maxLevel > 2) {
                                        $classen = "mainAktiv level_1";
                                        if ($subPage[selectPage] OR $subPage[subSelect]) {
                                            $classen .= " selected";
                                            $showSubMenu = 1;
                                        } else {
                                            $showSubMenu = 0;
                                        }
                                        echo("<li  class='$classen'>");
                                        echo("<a href='$name.php' ><span>$showName</span></a>");
                                        if ($showSubMenu) {
                                            echo ("</li>\n");
                                        } else {
                                            echo ("<ul >");
                                        }
                                        foreach($subPage[subNavi] as $subSubId => $subSubPage) {
                                            $name = $subSubPage[name];
                                            $id   = $subSubPage[id];
                                            $title = $subSubPage[title];
                                            $pageNavigation = $subSubPage[navigation];
                                            $pageShowLevel = $subSubPage[showLevel];
                                            if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                                                $showName = $title;
                                                if ($showName == "") $showName = $name;
                                                if ($showSubMenu) {
                                                //if ($subSubPage[selectPage] OR $subSubPage[subSelect]) $showName .= " a";
                                                    $classen = "mainAktiv level_2";
                                                    if ($subSubPage[selectPage]) $classen .= " selected";
                                                    if ($subSubPage[subSelect]) $classen .= " subSelected";
                                                    echo("<li  class='$classen'>");
                                                    echo("<a href='$name.php' ><span>$showName</span></a>");
                                                    echo ("</li>\n");
                                                } else {
                                                    echo("<li >");
                                                    echo("<a href='$name.php' ><span>$showName</span></a>");
                                                    echo ("</li>\n");
                                                }
                                            }
                                        }
                                        if (!$showSubMenu) {
                                            echo ("</ul>");
                                            echo ("</li>");
                                        }

                                    } else { // No SubMenu for Level 2
                                        $classen = "mainAktiv level_1";
                                        if ($subPage[selectPage] OR $subPage[subSelect]) $classen .= " selected";
                                        echo("<li  class='$classen'>");
                                        echo("<a href='$name.php' >$showName</a>");
                                        echo ("</li>\n");
                                    }
                                }                                    
                            }
                        } else { // Level 1 nicht aktuell
                            echo ("<li><a href='$name.php'  ><span>$showName</span></a>");//<a href='konto.php' ><span>mein Konto</span></a>\n");
                            echo ("<ul>\n");

                            foreach($page[subNavi] as $subId => $subPage) {
                                $name = $subPage[name];
                                $id   = $subPage[id];
                                $title = $subPage[title];
                                $pageNavigation = $subPage[navigation];
                                $pageShowLevel = $subPage[showLevel];
                                $showName = $title;
                                if ($showName == "") $showName = $name;
                                $classen = "";
                                if ($pageNavigation AND $showLevel >= $pageShowLevel) { // zeige Level 1
                                    if (is_array($subPage[subNavi]) AND $maxLevel > 1) {
                                        echo("<li  class='$classen'>");
                                        echo("<a href='$name.php' ><span>$showName</span></a>");
                                        echo ("<ul>\n");
                                        foreach($subPage[subNavi] as $subId => $subSubPage) {
                                            $name = $subSubPage[name];
                                            $id   = $subSubPage[id];
                                            $title = $subSubPage[title];
                                            $pageNavigation = $subSubPage[navigation];
                                            $pageShowLevel = $subSubPage[showLevel];
                                            $showName = $title;
                                            if ($showName == "") $showName = $name;
                                            if ($pageNavigation AND $showLevel >= $pageShowLevel) {
                                                if (is_array($subSubPage[subNavi]) AND $maxLevel > 2) {
                                                    echo("<li >");
                                                    echo("<a href='$name.php' ><span>$showName</span></a>");
                                                    echo ("</li>\n");
                                                    foreach($subSubPage[subNavi] as $subSubId => $subSubSubPage) {
                                                    }
                                                } else {
                                                    echo("<li >");
                                                    echo("<a href='$name.php' ><span>$showName</span></a>");
                                                    echo ("</li>\n");
                                                }
                                            }
                                        }
                                        echo ("</ul>\n");
                                        echo ("</li>\n");
                                    } else { // No SubMenu for Level 2
                                        //$classen = "mainAktiv level_1";
                                        //if ($subPage[selectPage] OR $subPage[subSelect]) $classen .= " selected";
                                        echo("<li >");
                                        echo("<a href='$name.php' >$showName</a>");
                                        echo ("</li>\n");
                                    }
                                }
                            }
                            echo("</ul>\n");
                            echo("</li>\n");
                        }


                    }  else { // kein SubMenu for Level 1
                        $classen = "naviMain";
                        if ($page[selectPage]) $classen .= "mainAktiv selected";
                        echo("<li class='$classen' style='width:".$mainWidth."px;' >");
                        echo("<a href='$name.php'  >$showName</a>");
                    }
                    echo("</li>");
                }
            }
            echo("</ul>\n");
            div_end("naviFrameVer","before");
            break;

           

    }
  
}
?>
