<?php // charset:UTF-8
   
    
function cms_header_show($pageData) {
    // Page IS EDITABLE
    global $pageShow,$pageEditAble;
    $pageShow = 1;
    $pageEditAble = 0;
  
    $showLevel = $_SESSION[userLevel];
    if (!$showLevel) $showLevel = 0;
    $myId     = $_SESSION[userId];
    
    $pageLevel = $pageData[showLevel];
    // echo ("$pageLevel $showLevel <br>");
    // $pageEditAble = 1;
    if ($pageLevel > $showLevel) {  // Seiten Level ist größer als Zeig Level von User
        $pageShow = 0;
        if ($pageLevel == 3) {
            
            $data = $pageData[data];
            $allowedUser = $pageData[data][allowedUser];
            // foreach ($data as $key => $value ) echo ("$key = $value <br>");
            // echo ("allowedUser $pageData[data] $allowedUser <br >");
            if ($allowedUser AND $myId) {
                $userPos = strpos($allowedUser,"|".$myId."|");
                if (is_int($userPos)) {
                    $pageShow = 1;
                    if ($pageShow) {
                        // echo ("PAGE SHOW = 1 name = $pageData[name]<br> ");
                        if ($pageData[name] == "test_".$myId) $pageEditAble = 1;
                        if ($pageData[name] == "test_sub_".$myId."_1") $pageEditAble = 1;
                        if ($pageData[name] == "test_sub_".$myId."_2") $pageEditAble = 1;
                        if ($pageData[name] == "test_sub_".$myId."_3") $pageEditAble = 1;
                    }                                        
                }
            }
        }
    } else { // Seiten Level ist kleiner als Zeige Level von User
        
        if ($showLevel > 6) $pageEditAble = 1;
        if ($pageLevel == 3) {
            $forbiddenUser =$GLOBALS[pageData][data][forbiddenUser];
            if ($forbiddenUser AND $myId) {
                $userPos = strpos($forbiddenUser,"|".$myId."|");
                if (is_int($userPos)) {
                    $pageEditAble = 0;
                }
            }
        }
    }
    // $pageData[editAble] = $pageEditAble;
    // echo ("<h1>PAGE SHOW = $pageShow EDITABLE = $pageEditAble</h1>");
    
    
    global $cmsSettings;
    
    
    
    
    
    
    
    $titleStr = $pageData[title];
    if ($titleStr) $titleStr = cms_text_getLg($titleStr);
    
    if ($pageData[pageTitle]) {
        if (is_array($pageData[pageTitle])) {
            $titleStr = "";
            foreach ($pageData[pageTitle] as $key => $value ) $titleStr .= "->$key='$value' ";
        }
        // $titleStr = "---".$pageData[pageTitle];
    }


    if (!is_array($cmsSettings)) {
        // echo ("no cmsSettings in Header <br />");
        if (is_array($_SESSION[cmsSettings])){
            $cmsSettings = $_SESSION[cmsSettings];
            //echo ("no cmsSettings get $cmsSettings <br />");
        } 
        if (!is_array($cmsSettings)) {
            $cmsSettings = cms_settings_get();
        }
    }
    
    $titleDelimiter = " | ";
    

    if (is_array($cmsSettings)) {
        // echo ("SET settings-title: '$cmsSettings[title]' <br />");
        // echo ("SET pageData-Title: '$titleStr' <br />");
        if ($cmsSettings[title]) $titleStr = $cmsSettings[title].$titleDelimiter.$titleStr;
        $description = $cmsSettings[description];
        $keywords    = $cmsSettings[keywords];
    }

    
    if ($pageData[dynamic]) {
        $dynamicAdd = cms_dynamicPage_breadCrumb($pageData); // ,$addArray=0)($dynamicData, $dynamicLevel);
        if (is_array($dynamicAdd) AND count($dynamicAdd)) {
            $dynamicName = $dynamicAdd[0][name];
            $titleStr .= $titleDelimiter.$dynamicName;
        }
        //echo ("$dynamicAdd <br>");
        // show_array($dynamicAdd);
    }
    
    if ($pageData[description]) $description = $pageData[description];
    if ($pageData[keywords]) {
        $keywords = cms_text_getLg($pageData[keywords]);
        // echo ("<h1>Keywords $keywords </h1>");
        if ($pageData[keywords][0] == "#") {
            list($dataSource,$output,$filterStr) = explode(":",substr($pageData[keywords],1));
            $filter = array();
            $filterList = explode(",",$filterStr);
            for ($i=0;$i<count($filterList);$i++) {
                list($key,$value) = explode("=",$filterList[$i]);
                if ($key AND $value) {
                    $filter[$key]=$value;
                    // echo ("Filter $key = $value <br>");
                }
            }
            switch ($dataSource) {
                case "company" : $list = cmsCompany_getList($filter, "name"); break;
                default :
                    $list = null;
            }
            // echo ($list."<br>");
            if (is_array($list) AND count($list)) {
                $setKeywords = "";
                for ($i=0;$i<count($list);$i++) {
                    $data = $list[$i];
                    $addStr = $list[$i][$output];
                    if ($output) {
                        if ($setKeywords) $setKeywords .= ",";
                        $setKeywords .= $addStr;
                    }
                }
            }
            if ($setKeywords) {
                // echo ($setKeywords."<br />");
                $keywords = $setKeywords;
            }
        } 
    }    


    $pageState = cms_page_state();
    switch ($pageState) {
        case "online" :
            break;
        case "inWork" :
            $titleStr = "Wartungsarbeiten";
            if ($cmsSettings[title]) $titleStr = $cmsSettings[title].$titleDelimiter.$titleStr;
            break;
        case "construction" :
            $titleStr = "Baustelle";
            if ($cmsSettings[title]) $titleStr = $cmsSettings[title].$titleDelimiter.$titleStr;
            break;
        default :
            echo ("UNKOWN PAGE STATE '$pageState' <br />");
    }
    
    
    echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n");

    echo ("<html xmlns='http://www.w3.org/1999/xhtml' lang='de' xml:lang='de' >\n");
    
    
   
    
   // echo ("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>");
   //  echo("<html >\n");
    echo("  <head>\n");
    echo("      <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n");
    echo("      <meta http-equiv='X-UA-Compatible' content='IE=9' />\n");
    echo ("<meta name='robots' content='index, follow' />\n");
    echo ("<meta name='revisit' content='after 20 days' />\n");
    
    //$titleStr = htmlentities($titleStr);
    echo("      <title>$titleStr</title>\n");
    echo ("<meta name='viewport' content='width = 320, user-scalable = yes, initial-scale = 1.0, maximum-scale = 1' />");
  

    if ($keywords) {
        //$keywords = htmlentities($keywords);
        echo("      <meta name='keywords' content='$keywords' />\n");
    }
    if ($description) {
        // $description = htmlentities($description);
        echo("      <meta name='description' content='$description' />\n");
    }

    $mobilPages = $GLOBALS[cmsSettings][mobilPages];
    if (!$mobilPages) $mobilPages = 0;
    $pageWidth  = $GLOBALS[cmsSettings][width];
    
    
    $pageInfo = $GLOBALS[pageInfo];
    global $cmsName,$cmsVersion,$showTarget;
    
    
    echo ("<script type='text/javascript'>\n");
    echo (" var cmsVersion='$cmsVersion';\n");
    echo (" var cmsName='$cmsName';\n");
    echo (" var mobilPage=$mobilPages;\n");
    echo (" var pageWidth=$pageWidth;\n");
    if ($mobilPages) {
        if ($_SESSION["target_width"]) echo (" var target_width=".$_SESSION["target_width"].";\n");
        if ($_SESSION["target_height"]) echo (" var target_height=".$_SESSION["target_height"].";\n");
        if ($_SESSION["target_orientation"]) echo (" var target_orientation='".$_SESSION["target_orientation"]."';\n");
        if ($_SESSION["target_target"]) echo (" var target_target='".$_SESSION["target_target"]."';\n");
    }
    echo ("</script>\n");
    
    if ($mobilPages) {
        echo ("<script type='text/javascript' src='/cms_base/cmsTarget.js'></script> \n");
    }
    
    switch ($pageState) {
        case "construction" : cms_header_construction_css($cmsName,$cmsVersion); break;            
        case "inWork" : cms_header_construction_css($cmsName,$cmsVersion); break;
        default:
            $css = cms_header_css($cmsName,$cmsVersion);
            // echo ("GET css<br>");
            echo ($css);
            
            cms_header_add_javascript(); 
    }
    
     
    echo("  </head>\n");

}

function cms_header_css($cmsName,$cmsVersion) {
    // $useCache = cmsCache_state();
    $useCache = 0;
    if ($GLOBALS[cmsSettings][wireframe]) {
        $wireframeState = cmsWireframe_state();
    }
    
    if ($useCache) {
        $cssWireMode = "normal";
        if ($wireframeState) $cssWireMode = "wireframe";
        
        $getCss = cmsCache_get("css",$cssWireMode,0);
        if ($getCss) {
            //echo ("Ingalt $getCss <br>");
            // echo (strlen($getCss)."lang <br>");
            return $getCss;
        }
    }
    
    
    $css = "";
    
    $cssMedia = "media='print,screen'";
    $wireFrameOn = 0;
    if ($GLOBALS[cmsSettings][wireframe]) {
       $wireframeState = cmsWireframe_state();
       if ($wireframeState) $wireFrameOn = 1;
    }
    $oldNavi = 0;
    
    ////////////////////////////////////////////////////////////////////////////
    /// STANDARD CMS - STYLES                                                ///
    ////////////////////////////////////////////////////////////////////////////
    $css .= "<link media='screen' type='text/css' href='/cms_".$cmsVersion."/styles/main.css' rel='stylesheet' />\n";
    
    // NAVI
   if ($oldNavi) {
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/mainNavi.css' rel='stylesheet' />\n";
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navi-hor.css' rel='stylesheet' />\n";
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navi-ver.css' rel='stylesheet' />\n";    
    } else {
        $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navigation.css' rel='stylesheet' />\n";        
    }
    
    if ($wireFrameOn) {
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_main.css' rel='stylesheet' />\n";
        if ($oldNavi) {
            $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_mainNavi.css' rel='stylesheet' />\n";
            $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-hor.css' rel='stylesheet' />\n";
            $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-ver.css' rel='stylesheet' />\n";
        } else {
            $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navigation.css' rel='stylesheet' />\n";
        }
    } else {
        
    }
    
    /// CODA SLIDER
    $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/coda-slider.css' rel='stylesheet' />\n";
    /// ZOOM BOX
    $css .= "<link rel='stylesheet' type='text/css' href='/includes/fancybox/jquery.fancybox-style.css' media='screen' />\n";
    
    ////////////////////////////////////////////////////////////////////////////
    /// EDIT - STYLES                                                        ///
    ////////////////////////////////////////////////////////////////////////////
   
    global $pageEditAble;
    if ($pageEditAble) {
   // if ($userLevel > 3 OR $edit OR $pageEditAble) {
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/cmsEdit.css' rel='stylesheet' />\n";
        $cmsEditColor = $GLOBALS[cmsSettings][editColor];
        if ($cmsEditColor) {
            $cmsFileName = "cmsEdit-".$cmsEditColor.".css";
            $root = $_SERVER[DOCUMENT_ROOT];
            $cmsStylePath = $root."/cms_".$GLOBALS[cmsVersion]."/styles/";
            // if (!file_exists($cmsStylePath.$cmsFileName)) {
                cms_header_createCSS("edit",$cmsEditColor);
            // }
            if (file_exists($cmsStylePath.$cmsFileName)) {
                $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/$cmsFileName' rel='stylesheet' />\n";            
            }
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /// THEMES - STYLE                                                       ///
    ////////////////////////////////////////////////////////////////////////////
    if ($wireFrameOn) $myTheme  = $GLOBALS[cmsSettings][wireframe_theme];
    else $myTheme  = $GLOBALS[cmsSettings][normal_theme];
    
    if ($myTheme != "none" AND $myTheme) {
        $myStyleFile = "style/$myTheme-style.css";
        if (file_exists($myStyleFile)) {
        // echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='/$myStyleFile' rel='stylesheet' />\n";
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /// OWN - STYLES                                                         ///
    ////////////////////////////////////////////////////////////////////////////
    if (file_exists("style/main-own.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
        $css .= "<link $cssMedia type='text/css' href='/style/main-own.css' rel='stylesheet' />\n";
    }
    if ($wireFrameOn) {
        if (file_exists("style/wireframe_main-own.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='/style/wireframe_main-own.css' rel='stylesheet' />\n";
        }
        if (file_exists("style/wireframe_navigation-own.css")) {
            $css.="<link $cssMedia type='text/css' href='/style/wireframe_navigation-own.css' rel='stylesheet' />\n";
        }
    } else {
        if (file_exists("style/navigation-own.css")) {
            //  echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='/style/navigation-own.css' rel='stylesheet' />\n";
        }
    }    
    
//    
//    /********* WIREFRAME - STYLES                                         *****/
//     if ($GLOBALS[cmsSettings][wireframe]) {
//        $wireframeState = cmsWireframe_state();
//        if ($wireframeState) {
//            $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_main.css' rel='stylesheet' />\n";
//            if (file_exists("style/wireframe_main-own.css")) {
//                $css.="<link $cssMedia type='text/css' href='style/wireframe_main-own.css' rel='stylesheet' />\n";
//            }
//            
//            if ($oldNavi) {
//                $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_mainNavi.css' rel='stylesheet' />\n";
//                $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-hor.css' rel='stylesheet' />\n";
//                $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-ver.css' rel='stylesheet' />\n";
//            } else {
//                $css.="<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navigation.css' rel='stylesheet' />\n";
//                if (file_exists("style/wireframe_navigation-own.css")) {
//                    $css.="<link $cssMedia type='text/css' href='style/wireframe_navigation-own.css' rel='stylesheet' />\n";
//                }
//            
//            }
//
//          
//        }
//        
//    }
    /********* WIREFRAME - STYLES - END                                   *****/
//    if ($oldNavi) {
//        if (file_exists("style/navi-hor.css"))     $css.="<link $cssMedia type='text/css' href='style/navi-hor.css' rel='stylesheet' />\n";
//        if (file_exists("style/navi-hor-own.css")) $css.="<link $cssMedia type='text/css' href='style/navi-hor-own.css' rel='stylesheet' />\n";
//
//        if (file_exists("style/navi-ver.css"))     $css.="<link $cssMedia type='text/css' href='style/navi-ver.css' rel='stylesheet' />\n";
//        if (file_exists("style/navi-ver-own.css")) $css.="<link $cssMedia type='text/css' href='style/navi-ver-own.css' rel='stylesheet' />\n";
//    }
    
    
    // zoomBox
    
    if ($useCache) {
        $res = cmsCache_save("css",$cssWireMode,0,$css);
    }
    
    return $css;
}
    

function cms_header_createCSS($mode,$color) {
    $find = 0;
    $rep = 0;
    switch ($color) {
        case "olive" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#f9fbdb","#f4f8bb","#eef494","#e9f070","#e3ec4d","#dde826","#d7e400","#c1cd00","#acb600","#969f00","#798000","#5a6000","#3c3f00","#1e2000","#000");
            break;
        
        case "blue" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#dbe7ed","#b8cfdb","#94b6c8","#709eb6","#4d86a4","#266c90","#00527d","#004a70","#004264","#003957","#002e46","#002234","#001723","#000c12","#000");
            break;
        
        case "green" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#e7eddb","#d0dbb8","#b8c894","#a0b670","#88a44d","#6e9026","#557d00","#4c7000","#446400","#003957","#304600","#243400","#182300","#0c1200","#000");
            break;
        
        case "orange" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#ffebdb","#ffd8b8","#ffc494","#ffb071","#ff9d4e","#ff8827","#ff7301","#e56701","#cc5c01","#b25001","#8f4001","#6b3000","#472000","#241000","#000");
            break;
        
        case "lightBlue" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#dbf5ff","#b8eaff","#94e0ff","#71d6ff","#4ecbff","#27c0ff","#01b5ff","#01a3e5","#0191cc","#017eb2","#01668f","#004c6b","#003247","#001a24","#000");
            break;
            
         case "pink" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#ffdbfb","#ffb8f7","#ff94f4","#ff70f0","#ff4dec","#ff4dec","#ff00e4","#e500cd","#cc00b6","#b2009f","#8f0080","#6b0060","#47003f","#240020","#000");
            break;
    
         case "red" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#ffe1e5","#ffc5cb","#ffa7b1","#ff8997","#ff6c7e","#ff4c62","#ff2d46","#e5283f","#cc2438","#b21f31","#8f1927","#6b131d","#470d13","#24060a","#000");
            break;
        
        case "default" :
            $find = array("#fff","#eee",   "#ddd",   "#ccc",   "#bbb",   "#aaa",   "#999",   "#888",   "#777",   "#666",   "#555",   "#444",   "#333",   "#222",   "#111",   "#000");
            $rep  = array("#fff","#eee","#ddd","#ccc","#bbb","#aaa","#999","#888","#777","#666","#555","#444","#333","#222","#111","#000");
            break;


        
    }
                
    switch ($mode) {
        case "edit" : 
            $orgFile = "cmsEdit-colorize.css";
            $cmsSaveFile = "cmsEdit-".$color.".css";
            break;
    }
    
    // echo ("cms_header_createCSS($mode,$color)<br>");
    $root = $_SERVER[DOCUMENT_ROOT];
    if (file_exists($root."/cms_".$GLOBALS[cmsVersion]."/styles/$orgFile")) {
        // echo "file $orgFile exists<br>";
        $cssText = loadText($root."/cms_".$GLOBALS[cmsVersion]."/styles/$orgFile");
        if (is_array($rep) AND is_array($find)) {
            $cssText = str_replace($find,$rep,$cssText);
            saveText($cssText, $root."/cms_".$GLOBALS[cmsVersion]."/styles/$cmsSaveFile");
            // echo ("css $mode $color created $cmsSaveFile<br>");
        }  else {
            echo ("no Color Array defined for color '$color' <br>");
        }     
    } else {
        echo ("orgFile $orgFile not exist <br>");
    }
            
    
    
    
    
    
}

function cms_header_construction_css($cmsName,$cmsVersion) {
     echo ("<link media='screen' type='text/css' href='/cms_".$cmsVersion."/styles/construction.css' rel='stylesheet' />\n");
}

function cms_header_add_javascript() {
    echo("<link media='screen' type='text/css' href='/includes/jquery/jquery.ui.all.css' rel='stylesheet' />\n");
    echo("<script src='/includes/jquery.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/jquery/ifx.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/jquery/idrop.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/jquery/idrag.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/jquery/iutil.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/jquery/islider.js' type='text/javascript'></script>\n");
    //echo("<script src='/includes/jquery/ui/jquery.ui.core.js'></script>");
    //echo("<script src='/includes/jquery/ui/jquery.ui.widget.js'></script>");
    //echo("<script src='/includes/jquery/ui/jquery.ui.position.js'></script>");
    //echo("<script src='/includes/jquery/ui/jquery.ui.autocomplete.js'></script>");
    //echo("<link media='screen' type='text/css' href='/includes/jquery/ui/jquery.ui.autocomplete.css' rel='stylesheet'>");

    echo ("<script src='/includes/jquery/auto_suggest.js' type='text/javascript'></script>\n");
   //  echo ("<script type='text/javascript' src='/includes/jquery-ui-1.8.20.custom.min.js'></script>\n");
    echo ("<script type='text/javascript' src='/includes/jquery-ui-1.7.2.custom.min.js'></script>\n");
    echo ("<script type='text/javascript' src='/includes/jquery.coda-slider-3.0.js'></script>\n");

    echo ("<script type='text/javascript' src='/includes/slides.jquery.js'></script>\n");
    echo ("<script type='text/javascript' src='/includes/jquery.bxSlider.js'></script>\n");
    // echo ("<script type='text/javascript' src='/includes/script-home.js'></script>\n");
    
    
    echo("<script src='/includes/fancybox/jquery.fancybox-script.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/fancybox/js-fancybox-config.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/fancybox/jquery.mousewheel.pack' type='text/javascript'></script>\n");


    //echo("<script src='/includes/jquery/color_picker/color_picker.js' type='text/javascript'></script>");


    // echo("<link href='/includes/color/js/jquery/color_picker/color_picker.css' rel='stylesheet' type='text/css'>");

 

   
}
?>