<?php // charset:UTF-8
function cms_header_show($pageData) {
    //echo("<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n");

    global $cmsSettings;
    
    $titleStr = $pageData[title];
    if ($pageData[pageTitle]) $titleStr = $pageData[pageTitle];


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
        $keywords = $pageData[keywords];
        
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
    
    if ($keywords) {
        //$keywords = htmlentities($keywords);
        echo("      <meta name='keywords' content='$keywords' />\n");
    }
    if ($description) {
        // $description = htmlentities($description);
        echo("      <meta name='description' content='$description' />\n");
    }

   
    $pageInfo = $GLOBALS[pageInfo];
    global $cmsName,$cmsVersion;
    
    
    echo ("<script type='text/javascript'>\n");
    echo (" var cmsVersion='$cmsVersion';\n");
    echo (" var cmsName='$cmsName';\n");
    echo ("</script>\n");
    

//    $root = $_SERVER[DOCUMENT_ROOT];
//    if (file_exists("$root/$cmsName")) {
//        $root .= "/".$cmsName;
//        $home = "/$cmsName";
//    }
    
    $cssMedia = "media='print,screen'";
    echo("<link media='screen' type='text/css' href='/cms_".$cmsVersion."/styles/main.css' rel='stylesheet' />\n");
   //  show_array($_SERVER);
    if (file_exists("style/main.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
        echo("<link $cssMedia type='text/css' href='style/main.css' rel='stylesheet' />\n");
    }
    if (file_exists("style/main-own.css")) {
        //  echo ("cmsName $root/style/main.css <br>");
        echo("<link $cssMedia type='text/css' href='style/main-own.css' rel='stylesheet' />\n");
    }
    
    
    echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/coda-slider.css' rel='stylesheet' />\n");
  
    $oldNavi = 0;
    if ($oldNavi) echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/mainNavi.css' rel='stylesheet' />\n");
    else {
        echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navigation.css' rel='stylesheet' />\n");
        if (file_exists("style/navigation-own.css")) {
            //  echo ("cmsName $root/style/main.css <br>");
            echo("<link $cssMedia type='text/css' href='style/navigation-own.css' rel='stylesheet' />\n");
        }
    }
        
    
    // cmsEdit Styles
    echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/cmsEdit.css' rel='stylesheet' />\n");
    $cmsEditColor = $GLOBALS[cmsSettings][editColor];
    if ($cmsEditColor) {
        $cmsFileName = "cmsEdit-".$cmsEditColor.".css";
        $root = $_SERVER[DOCUMENT_ROOT];
        $cmsStylePath = $root."/cms_".$GLOBALS[cmsVersion]."/styles/";
        //if (!file_exists($cmsStylePath.$cmsFileName)) {
            cms_header_createCSS("edit",$cmsEditColor);
        //}
        if (file_exists($cmsStylePath.$cmsFileName)) {
            echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/$cmsFileName' rel='stylesheet' />\n");            
        }
    }
    
    if ($oldNavi) echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navi-hor.css' rel='stylesheet' />\n");
    if ($oldNavi) echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/navi-ver.css' rel='stylesheet' />\n");
    
    
    /********* WIREFRAME - STYLES                                         *****/
     if ($GLOBALS[cmsSettings][wireframe]) {
        $wireframeState = cmsWireframe_state();
        if ($wireframeState) {
            echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_main.css' rel='stylesheet' />\n");
            if (file_exists("style/wireframe_main-own.css")) {
                echo("<link $cssMedia type='text/css' href='style/wireframe_main-own.css' rel='stylesheet' />\n");
            }
            
            if ($oldNavi) {
                echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_mainNavi.css' rel='stylesheet' />\n");
                echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-hor.css' rel='stylesheet' />\n");
                echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navi-ver.css' rel='stylesheet' />\n");
            } else {
                echo("<link $cssMedia type='text/css' href='/cms_".$cmsVersion."/styles/wireframe_navigation.css' rel='stylesheet' />\n");
                if (file_exists("style/wireframe_navigation-own.css")) {
                    echo("<link $cssMedia type='text/css' href='style/wireframe_navigation-own.css' rel='stylesheet' />\n");
                }
            
            }

          
        }
        
    }
    /********* WIREFRAME - STYLES - END                                   *****/
    if ($oldNavi) {
        if (file_exists("style/navi-hor.css"))     echo("<link $cssMedia type='text/css' href='style/navi-hor.css' rel='stylesheet' />\n");
        if (file_exists("style/navi-hor-own.css")) echo("<link $cssMedia type='text/css' href='style/navi-hor-own.css' rel='stylesheet' />\n");

        if (file_exists("style/navi-ver.css"))     echo("<link $cssMedia type='text/css' href='style/navi-ver.css' rel='stylesheet' />\n");
        if (file_exists("style/navi-ver-own.css")) echo("<link $cssMedia type='text/css' href='style/navi-ver-own.css' rel='stylesheet' />\n");
    }
    

    
    cms_header_add_javascript();  
    echo("  </head>\n");



    // reload Page on setShowLevel
    $setShowLevel = $_POST[setShowLevel];
    if (!is_null($setShowLevel) AND $setShowLevel != $_SESSION[showLevel]) {
        $_SESSION[showLevel] = $setShowLevel;
        global $pageInfo;
        reloadPage($pageInfo[$page]);
        //cms_infoBox("Set ShowLevel to ".$_SESSION[showLevel]);

    }
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
    


    //echo("<script src='/includes/jquery/color_picker/color_picker.js' type='text/javascript'></script>");


    // echo("<link href='/includes/color/js/jquery/color_picker/color_picker.css' rel='stylesheet' type='text/css'>");

 

   
}
?>
