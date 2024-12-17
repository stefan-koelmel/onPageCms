<?php // charset:UTF-8
   
    
function cms_header_show($pageData) {
    // Page IS EDITABLE
    global $pageShow,$pageEditAble;
    $pageShow = 1;
    $pageEditAble = 0;
  
    $showLevel = site_session_get(showLevel); // $_SESSION[userLevel];
    if (!$showLevel) $showLevel = 0;
    $userLevel = site_session_get(userLevel);
    $myId     = $_SESSION[userId];
    
    $pageLevel = $pageData[showLevel];
    
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
        
        if ($userLevel > 6) $pageEditAble = 1;
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
   
    pageClass_setValue("pageEditAble", $pageEditAble);
    pageClass_setValue("pageShow", $pageShow);
    
    
    
    
    
    
    
    
    
    $titleStr = $pageData[title];
//    if (is_array($titleStr)) {
//        foreach ($titleStr as $key => $value ) echo ("Title = $key = $value <br>") ;
//    }
//    
    if ($titleStr) $titleStr = cms_text_getLg($titleStr,0);
    
    if ($pageData[pageTitle]) {
        if (is_array($pageData[pageTitle])) {
            $titleStr = "";
            foreach ($pageData[pageTitle] as $key => $value ) $titleStr .= "->$key='$value' ";
        }
        // $titleStr = "---".$pageData[pageTitle];
    }
    
    $cmsSettings = site_session_get(cmsSettings);
    if (!is_array($cmsSettings)) {
        echo "no cmsSettings in Header <br>";
        $cmsSettings = cms_settings_get();
        site_session_set(cmsSettings,$cmsSettings);        
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
        $keywords = cms_text_getLg($pageData[keywords],0);
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
    echo ("<meta name='apple-mobile-web-app-capable' content='yes' />\n");
  

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
    
    // foreach($_SERVER as $key => $value ) echo ("SERVER $key = $value <br>");
    // ICONS
    $iconPath = "http://".$_SERVER[SERVER_NAME]."/";
    if (file_exists($_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/")) $iconPath.= $GLOBALS[cmsName]."/";
    
    if (file_exists("favicon.ico")) echo ("<link href='".$iconPath."favicon.ico' rel='SHORTCUT ICON'>\n");
    
    if (file_exists("icon.ico")) echo("<link href='".$iconPath."/icon.ico' rel='SHORTCUT ICON'>\n");
    
    
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
        echo ("<script type='text/javascript' src='/cms_".$cmsVersion."/cmsTarget.js'></script> \n");
        $targetWidth = $_SESSION[target_width];
        
        if ($targetWidth != $pageWidth AND $targetWidth>0) {
           $GLOBALS[cmsSettings][width] = $targetWidth / 2;
        }
    }
    
    switch ($pageState) {
        case "construction" : cms_header_construction_css($cmsName,$cmsVersion); break;            
        case "inWork" : cms_header_construction_css($cmsName,$cmsVersion); break;
        default:
            cms_header_add_javascript();
            
            $css = cms_header_css($cmsName,$cmsVersion);
            // echo ("GET css<br>");
            echo ($css);
            
           
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
        $css .= "<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navigation.css' rel='stylesheet' />\n";        
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
        
        $myStyleFile = "";
        if (file_exists($_SERVER[DOCUMENT_ROOT]."/style/".$myTheme."-style.css")) $myStyleFile = "/style/$myTheme-style.css";
        if (file_exists($_SERVER[DOCUMENT_ROOT]."/".$cmsName."/style/".$myTheme."-style.css")) $myStyleFile = "/".$cmsName."/style/$myTheme-style.css";
        
        if ($myStyleFile) {
        
            
        // echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='$myStyleFile' rel='stylesheet' />\n";
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /// OWN - STYLES                                                         ///
    ////////////////////////////////////////////////////////////////////////////
    if (file_exists("style/main-own.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
        $css .= "<link $cssMedia type='text/css' href='style/main-own.css' rel='stylesheet' />\n";
    }
    
    
    if ($wireFrameOn) {
        if (file_exists("style/wireframe_main-own.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='style/wireframe_main-own.css' rel='stylesheet' />\n";
        }
        if (file_exists("style/wireframe_navigation-own.css")) {
            $css.="<link $cssMedia type='text/css' href='style/wireframe_navigation-own.css' rel='stylesheet' />\n";
        }
    } else {
        if (file_exists("style/navigation-own.css")) {
            //  echo ("cmsName $root/style/main.css <br>");
            $css .= "<link $cssMedia type='text/css' href='style/navigation-own.css' rel='stylesheet' />\n";
        }
    }    
    
    $css .= cms_header_ownTypes_css($cssMedia);

  
    // zoomBox
    
    if ($useCache) {
        $res = cmsCache_save("css",$cssWireMode,0,$css);
    }
    
    return $css;
}

function cms_header_ownTypes_css($cssMedia) {    
    $css = "";
    // echo ("$_SERVER[DOCUMENT_ROOT] <br> ");
    if (file_exists($_SERVER[DOCUMENT_ROOT]."/cms/cms_contentTypes/")) $path = "/cms/cms_contentTypes/";
    if (file_exists($_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/cms/cms_contentTypes/")) $path = "/".$GLOBALS[cmsName]."/cms/cms_contentTypes/";
   
    if (!$path) return $css;    
    $handle = opendir($_SERVER[DOCUMENT_ROOT].$path);
    while ($file = readdir ($handle)) {
        if ($file == ".") continue;
        if ($file == "..") continue;
        if (is_dir($file)) continue;
       
        $fileTypeList = explode(".",$file);
        $fileType = $fileTypeList[count($fileTypeList)-1];
        if ($fileType != "css") continue;
        // echo ("file = $file <br>");     
        $css .= "<link $cssMedia type='text/css' href='".$path.$file."' rel='stylesheet' />\n";
    }
    return $css;    
}
    

function cms_header_createCSS($mode,$color) {
    $find = 0;
    $rep = 0;
    switch ($color) {
        case "black" :
            $find = array("#fff",   "#eee",   "#ddd","#ccc","#bbb","#aaa","#999","#888","#777","#666","#555","#444","#333","#222","#111","#000");
            $rep  = array("#010101","#101010","#212121","#323232","#434343","#545454","#676767","#787878","#898989","#989898","#ababab","#bababa","#cbcbcb","#dcdcdc","#ededed","#fefefe");
            $rep  = array("#202020","#272727","#2e2e2e","#323232","#434343","#545454","#676767","#787878","#898989","#989898","#ababab","#bababa","#cbcbcb","#dcdcdc","#ededed","#fefefe");
            //             a0          a9          2c          333
            break;
        
        case "white" :
            $find = array("#fff","#eee","#ddd","#ccc","#bbb","#aaa","#999","#888","#777","#666","#555","#444","#333","#222","#111","#000");
            $rep  = array("#fefefe","#fcfcfc","#fafafa","#ccc","#bababa","#a0a0a0","#989898","#878787","#787878","#676767","#565656","#454545","#323232","#212121","#101010","#010101");
            break;
        
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
    
    $cssOrgFile  = $root."/cms_".$GLOBALS[cmsVersion]."/styles/$orgFile";
    $cssSaveFile = $root."/cms_".$GLOBALS[cmsVersion]."/styles/$cmsSaveFile";
    
    if (file_exists($cssOrgFile)) {
        $create = 1;
        if (file_exists($cssSaveFile)) {
            $create = 0;
            // echo ("<h1>BOTH exists</h1>");
            $orgDate = fileatime($cssOrgFile);
            $saveDate = fileatime($cssSaveFile);
            $diffDate = $orgDate - $saveDate;
            if ($diffDate > 0) $create = 1;
            // echo ("orgDate = $orgDate saveDate = $saveDate diff = $diffDate <br>");
        }
        // $create = 1;
        if ($create) {
            echo ("<h3>Create Edit STyle $color </h3>");
        // echo "file $orgFile exists<br>";
            $cssText = loadText($cssOrgFile);
            if (is_array($rep) AND is_array($find)) {
                $cssText = str_replace($find,$rep,$cssText);
                saveText($cssText, $cssSaveFile);
                // echo ("css $mode $color created $cmsSaveFile<br>");
            }  else {
                echo ("no Color Array defined for color '$color' <br>");
            }     
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
    echo("<link media='screen' type='text/css' href='/includes/jquery.bxSlider.css' rel='stylesheet' />\n");
    // echo ("<script type='text/javascript' src='/includes/script-home.js'></script>\n");
    
    
    echo("<script src='/includes/fancybox/jquery.fancybox-script.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/fancybox/js-fancybox-config.js' type='text/javascript'></script>\n");
    echo("<script src='/includes/fancybox/jquery.mousewheel.pack' type='text/javascript'></script>\n");


    //echo("<script src='/includes/jquery/color_picker/color_picker.js' type='text/javascript'></script>");


    // echo("<link href='/includes/color/js/jquery/color_picker/color_picker.css' rel='stylesheet' type='text/css'>");

 

   
}
?>