<?php // charset:UTF-8
class cmsAdmin_layout_base extends cmsAdmin_editClass_base {
    
    function admin_dataSource() {
        return "layout";
    }
    
    function show_list() {
        $this->show();
    }
    
    function show() {
        $frameWidth = $this->frameWidth;
        
        $wireframeState = cmsWireframe_state();
        
        $filter = array("type"=>"theme");
        if ($wireframeState) {
            $theme = $GLOBALS[cmsSettings][wireframe_theme];
            // echo "<h1>Wirframe $theme</h1>";
            $filter["theme"] = "wireframe";
        } else {
            $theme = $GLOBALS[cmsSettings][normal_theme];
            // echo ("<h1>Normale Seite - $theme</h1>");
            $filter["theme"] = "normal";
        }
        
        
        
        
        $themeList = cmsStyle_getList($filter,"name","assoId");
        if (is_array($themeList)) {
            foreach ($themeList as $id => $themeValue) {
                $theme = $themeValue[theme];
                $type = $themeValue[type];
                $name = $themeValue[name];
                // echo ("$id $theme $type $name <br />");
                
            }
        }
        
        
        
        // EDIT PAGE LAYOUT
        $editLayout = $_GET[editLayout];
        if ($editLayout) {
            $pageWidth = $GLOBALS[cmsSettings][width];
            cms_content_show($editLayout,$pageWidth);
            return 0;
        }

        // edit LAYOUT-Types
        $view = $_GET[view];
        switch ($view) {
            case "layoutTypes" :
                $this->showTypes($view,$frameWidth);
                return 0;
            case "layoutColors" :
                $this->showColor($view,$frameWidth);
                return 0;   
        } 


        $this->showPageList($frameWidth);
        echo ("<h1>Layout Elemente bearbeiten</h1>");
        $this->showTypes(null,$frameWidth);
        $this->showColor(null,$frameWidth);

    }
    
    function showPageList($frameWidth) {
        echo ("<h1>Seiten Layouts bearbeiten</h1>");
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `name` LIKE 'layout_%' ";
        $result = mysql_query($query);
        if (!$result) {
            cms_errorBox("Fehler beim Abfragen der Layouts");
            return 0;
        }
        while ($layout = mysql_fetch_assoc($result)) {
            $id = $layout[id];
            $name = $layout[name];
            $title= $layout[title];

            echo ("Layout '$title' ");
            if ($name != $editLayout) {
                echo ("<a href='$pageInfo[pageName]?editLayout=$name' class='cmsSmallButton'  >editieren </a>");
            }
            echo ("<br>");

        }  
    }
    
    
    function showTypes($view,$frameWidth) {
        if (!$view) {
            $goPage = cms_page_goPage("view=layoutTypes");
            echo ("<a href='$goPage' style='width:200px;' class='cmsLinkButton' >Stile bearbeiten</a>");
            return 0;        
        }
        
         if (cmsWireframe_state()) {
            $theme = $GLOBALS[cmsSettings][wireframe_theme];    
            $mode  = "Wireframe";
        } else {
            $theme = $GLOBALS[cmsSettings][normal_theme];
            $mode  = "Normal";
        }
        
        if (!$theme OR $theme == "none") {
            echo ("<h1>Kein Theme ausgewählt</h1>");
            if (cmsWireframe_state()) {
                echo ("Stilvorlage:");
                echo (cmsStyle_selectTheme($editData[normal_theme],"editData[normal_theme]","wireframe"));
            } else {
                echo ("Stilvorlage:");
                echo (cmsStyle_selectTheme($editData[wireframe_theme],"editData[wireframe_theme]","normal")."<br />");
            }
            
    
            
            return 0;
        }
        
        $themeName = $theme;
        if (substr($themeName,0,7)  == "normal-") $themeName = substr($themeName,7);
        if (substr($themeName,0,10) == "wireframe-") $themeName = substr($themeName,10);
        
        echo ("<h3>Layout Elemente bearbeiten - Theme = '$themeName' / $mode</h3>");
       
        
        
       
//        foreach ($frameList as $name => $value) {
//            echo ("$name = $value <br/>");        
//        }

         // LAYOUT FRAME
        div_start("cmsLayout_frame","background-color:transparent;display:inline-block;width:100%;margin:5px 0;padding:5px 0;");

        
        $mainTypes = array();
        $mainTypes[header] = "Header";
        $mainTypes[navi] = "Navigation";
        $mainTypes[titleLine] = "Kopfzeile";
        $mainTypes[content] = "Inhalt";
        $mainTypes[footer] = "Footer";
        
        $editLayout = $_GET[layout];
        
        // div_start("cmsLayoutFrame","width:100%;");
        
        foreach ($mainTypes as $show => $showName) {
            
            // SPACER START
            switch ($show) {
                case "header" :
                    $this->spacer_show($theme,"pageStart",$frameWidth);
                    break;
                default:
            }
            
            
            switch ($show) {
                case "content__" :
                    $this->showTypesContent($theme,$editLayout,$frameWidth);
                    break;
                
                default :
                    if ($editLayout == $show) {
                        $edit = 1;
                        $divName = $show." cmsLayout_FrameSettings";
                        $editLink = "";
                        // $addItems = $this->editLayout($show,$theme,$frameWidth);
                        //div_start("cmsLayoutContentFrame");
                        $divData[style] = "width:auto;display:block;";
                    } else {
                        $edit = 0;
                        $divName = $show; // ." cmsFrameLink";
                        $editLink = cms_page_goPage("view=layoutTypes&layout=".$show."#editLayout");
                    }
                    
                    
                    div_start("cmsLayoutFrame","width:".$frameWidth."px;");
                    div_start("cmsLayoutFrame_left","width:100px;float:left;");
                    
                    if ($editLink) {
                        echo("<a href='$editLink' class='layoutTitle' style='display:block;' >$showName</a>");
                    } else {
                        echo ("<span class='layoutTitle layoutTitleActive' style='display:block;'>$showName</span><br/>");
                    }
                    div_end("cmsLayoutFrame_left");
                    div_start("cmsLayoutFrame_right","width:".($frameWidth-150)."px;min-height:16px;float:left;");
                    
                    if ($edit) {
                        $this->editLayout($show,$theme,$frameWidth-100);
                        // div_start("cmsLayoutContentFrame");
                    }

                    if ($show=="content") {
                        $divData[style] .= "min-height:22px;";
                    }
                    // echo ("$show <br/>");
                    $inhalt = 0;
                    div_start($divName,$divData);
                    switch ($show) {
                        case "navi" :
                            // echo ("Navigation <br>");
                            $this->layout_navigation();
                            $inhalt = 1;
                            break;
                        default :
                            // echo("Inhalt $show <br>");

                    }
                    // if ($editLink) echo("<a href='$editLink' class='hiddenLink' >edit</a>");
                    // echo ("<span class='layoutTitle'>$showName</span><br/>");

                    if ($editLayout == $show) {
                        echo ($addItems);
                    }
                    
                    $textList = $this->edit_textList($show);
                    if (count($textList)) {
                        foreach ($textList as $textKey => $textValue) {
                            $textDivName = $show.$textKey. " layoutText";
                            $textDivData = array("id"=>"text_".$textKey);
                            div_start($textDivName,$textDivData);
                            echo ($textValue);
                            div_end($textDivName);    
                        }
                        $inhalt = 1;
                    }
                    
                    $buttonList = $this->edit_buttonList($show);
                    if (count($buttonList)) {
                        div_start("cmsLayoutButtonFrame");
                        foreach ($buttonList as $buttonKey => $buttonValue) {
                            echo ("<a id='button_".$buttonKey."' style='display:inline-block;margin-right:10px;' href='#' class='".$show.$buttonKey." layoutButton'>$buttonValue</a>");
                        }
                        div_end("cmsLayoutButtonFrame");
                        $inhalt = 1;
                    }
                    
                    if ($inhalt == 0 AND $show!= "content") {
                        echo "Kein Inhalt für $show ";
                    }
                    
                    
                    div_end ($divName);    

                    // if ($editLayout == $show) div_end("cmsLayoutContentFrame");
                    
                    
                    // echo ("Hier Inhalt");
                    div_end("cmsLayoutFrame_right");

                    /// SPACER RIGHT
//                    div_start("cmsLayoutFrame_spacer");
//                    $editSpacerLink = "#";
//                    echo ("<a href='$editSpacerLink' class='layoutSpacer' >$show</a>");
//                    div_end ("cmsLayoutFrame_spacer");

                    div_end("cmsLayoutFrame","before");

                    if ($show == "content") {
                       $this->showTypesContent($theme,$editLayout,$frameWidth);
                    }
                    
            }
            
            // After
            switch ($show) {
//             
                case "content" :
                    div_start("spacerContentEnd");
                    div_end("spacerContentEnd");
                    // div_end("content_box");
                    $this->spacer_show($theme,"contentEnd",$frameWidth);
                    break;
                
//                case "footer" :
////                    div_start("spacerfooter");
////                    div_end("spacerfooter");
//                    $this->spacer_show($theme,$show,$frameWidth);
//                    break;
                default :
                    $this->spacer_show($theme,$show,$frameWidth);
                    break; 
            }
            
        }
        div_end("cmsLayout_frame");
        
        $goPage = cms_page_goPage("");
        echo ("<a href='$goPage'  class='cmsLinkButton secondButton cmsSecond ' >zurück</a><br/>");
        $show = 0;
        if ($_POST[layoutSave] OR $show) {
            div_start("cssSave","background-color:#fff;border:1px solid #000;");
            $this->createStyle($theme);
            div_end("cssSave");
        }
            
        
        
    }

    function layout_navigation() {
        echo ("<div class='main_menu' >");
        echo ("<div id='mainmenu'>");
        echo ("<ul class=''>");
        echo ("<li class=' firstItem'><a class='' href='#'>NAV 1</a></li>");
        echo ("<li class='select'><a class='select' href='#'>NAV 2-select</a></li>");
        echo ("<li class='hasSub subSelect'><a class='subSelect' href='#'>NAV 3-subSelect</a>");

        echo ("<ul>");
        echo ("<li class=' firstItem'><a class='' href='#'>SUB 1</a></li>");
        echo ("<li class=' hasSub select'><a class='select' href='#'>SUB 2</a>");
        echo ("<ul>");
        echo ("<li class=' firstItem'><a class='' href='#'>SUB 2 / 1</a></li>");
        echo ("<li class=' select'><a class='select' href='#'>SUB 2 / 2</a>");
        echo ("<li class=' '><a class='' href='#'>SUB 2 / 3</a></li>");
        echo ("<li class=' '><a class='' href='#'>SUB 2 / 4</a>");
        echo ("</ul>");

        echo ("</li>");


        echo("</ul>");
        echo ("</li>");
        echo ("<li class='hasSub '><a class='' href='#'>NAV 4</a>");
         echo ("<ul>");
        echo ("<li class='firstItem'><a class='' href='#'>SUB 1</a></li>");
        echo ("<li class=''><a class='' href='#'>SUB 2</a>");
        echo ("</ul>");
        echo ("</li>");
        echo ("</ul>");
        echo ("</div>");
        echo ("</div>");
    }
    
    function edit_buttonList($editType) {
        $button_list = array();
        switch ($editType) {
            case "footer" :
                $button_list = array("Link"=>"Normaler Link","Active"=>"Aktiver Link");
                break;
            case "titleLine" :
                $button_list = array("breadCrumbLink"=>"Link","breadCrumbActive"=>"Aktive");
                break;
        }
        return $button_list;                
    }
    
    
    function edit_textList($editType) {
        $text_list = array();
        switch ($editType) {
            case "header" :
                $text_list = array("Name"=>"Name","Slogan"=>"Slogan");
                break;
            
//            case "titleLine" :
//                $text_list = array("breadCrumbLink"=>"Link","breadCrumbActive"=>"Aktive");
//                break;
        }
        return $text_list;                
    }
    
    
    function editLayout($editType,$theme,$frameWidth) {
        div_start("cmsContentEditFrame",array("id"=>"editLayout"));
        
        $show_background = 1;
        $show_font = 1;
        $show_margin = 1;
        $show_border = 1;
        $show_radius = 1;
        $show_padding = 1;
        $textData = $this->text_get($theme,$editType);
        $text_list = $this->edit_textList($editType);

        $buttonData = $this->button_get($theme,$editType);
        $button_list = $this->edit_buttonList($editType);



        
        switch ($editType) {
            case "header" :
                $show_background = 1;
                $show_font = 1;
                $show_margin = 1;
                $show_border = 1;
                $show_radius = 1;
                $show_padding = 1;
                break;
                
            case "navi" :
                $show_background = 1;
                $show_font = 1;
                $show_margin = 1;
                $show_border = 1;
                $show_radius = 1;
                $show_padding = 1;
                break;
            case "footer" :
                $show_background = 1;
                $show_font = 1;
                $show_margin = 1;
                $show_border = 1;
                $show_radius = 1;
                $show_padding = 1;
                
                break;
        }
        
        
        $filter = array();
        $filter[theme] = $theme;
        $filter[type] = $editType;
        
        // get LayoutData
        $layoutData = cmsStyle_get($filter);
        if (is_array($layoutData)) {
            foreach ($layoutData as $key => $value) {
                $splitList = explode(";",$value);
                $cssList = array();
                for($i=0;$i<count($splitList);$i++) {
                    if ($splitList[$i]) {
                        list($key2,$val2) = explode(":",$splitList[$i]);
                        if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                    }
                }
                switch ($key) {
                    case "id": 
                        $layoutId = $value; 
                        break;
                    case "type": break;
                    case "name": break;
                    case "theme" : break;
                    case "background" :
                        $backgroundColor = $cssList["background-color"];
                        $backgroundColor_data = $cssList[backgroundColor_data];

                        $backgroundRollColor = $cssList["background-roll-color"];
                        $backgroundRollColor_data = $cssList[backgroundRollColor_data];
                        break;

                    case "margin" :
                        $marginStr = $cssList[margin];
                        $marginValue = $this->splitWidthStr($marginStr,"margin");
                       
                        $margin = $cssList["margin"];
                        $marginTop = $cssList["margin-top"];
                        $marginRight = $cssList["margin-right"];
                        $marginBottom = $cssList["margin-bottom"];
                        $marginLeft = $cssList["margin-left"];
                        // echo ("MARGIN '$marginStr' -> $margin ( $marginTop / $marginRight / $marginBottom / $marginLeft )<br/>");
                        break;
                    case "border" :
//                        foreach ($cssList as $cssKey => $cssValue) {
//                            echo ("$key $cssKey = $cssValue <br>");
//                        }
                        $borderStyle = $cssList["border-style"];
                        $borderColor = $cssList["border-color"];
                        $borderColor_data = $cssList["borderColor_data"];
                        $borderRollColor = $cssList["border-roll-color"];
                        $borderRollColor_data = $cssList["borderRollColor_data"];
                        $border = $cssList["border-width"];
                        $borderTop = $cssList["border-top-width"];
                        $borderRight = $cssList["border-right-width"];
                        $borderBottom = $cssList["border-bottom-width"];
                        $borderLeft = $cssList["border-left-width"];
                        // echo ("BORDER WIDTH '$borderStr' -> $border ( $borderTop $borderRight $borderBottom $borderLeft )<br/>");
                        break;
                    case "color" :
                        $color = $cssList[color];
                        $color_data = $cssList[color_data];
                        
                        $rollColor = $cssList[rollColor];
                        $rollColor_data = $cssList[rollColor_data];

                        // echo ("color $color / $color_data rollColor $rollColor,$rollColor_data <br />");
                        break;
                    case "radius" :
                        
                        $radiusStr = $value;
                        if (substr($radiusStr,0,14) == "border-radius:") $radiusStr = substr($radiusStr,14);
                        $radiusValue = $this->splitWidthStr($radiusStr,"radius");
                        // echo ("Radius $radiusStr ".substr($radiusStr,0,14)." is = $value <br>");
                        if ($radiusValue[main]) {
                            $radius = $radiusValue[main];
                        } else {
                            $radius = "";
                            $radiusLeft = $radiusValue[left];
                            $radiusRight = $radiusValue[right];
                            $radiusTop = $radiusValue[top];
                            $radiusBottom = $radiusValue[bottom];
                        }
                        break;
                    case "padding" :
//                        foreach ($cssList as $cssKey => $cssValue) {
//                            echo ("$key $cssKey = $cssValue <br>");
//                        }
                        $padding = $cssList["padding"];
                        $paddingTop = $cssList["padding-top"];
                        $paddingRight = $cssList["padding-right"];
                        $paddingBottom = $cssList["padding-bottom"];
                        $paddingLeft = $cssList["padding-left"];


                        break;
                    case "font":
                        break;
                    case "data" :
                        break;



                    default :
                        echo ("<b>Unkown $key </b><br>");
                         foreach ($cssList as $cssKey => $cssValue) {
                            echo ("$key $cssKey = $cssValue <br>");
                        }
                }
            }
        } else {
            $layoutData = array();
        }
        
        
        $save = 0;
        if ($_POST[layoutSaveClose]) { $save = 1; $close = 1;}
        if ($_POST[layoutSave]) { $save = 1; $close = 0;}
        if ($save) {
            $layout = $_POST[layout];
            // show_array($layout);

            // margin
            $marginStr = "margin:".cmsLayout_getWidthStr($layout,"margin");
            $marginStr = $this->getWidthStr($layout,"margin");
            
            $margin = $layout[margin];
            $marginTop = $layout[marginTop];
            $marginRight = $layout[marginRight];
            $marginBottom = $layout[marginBottom];
            $marginLeft = $layout[marginLeft];
            // echo ("WidthStr margin : '$widthStr' <br>");

            // border
            $borderColor = $layout[borderColor];
            if ($borderColor) {
                $borderColorStr = "border-color:#$borderColor;";
            }
           
            $borderRollColor = $layout[borderRollColor];
            if ($borderRollColor) $borderRollColorStr = "border-roll-color:#$borderRollColor;";
            
            $borderStyle = $layout[borderStyle];
            if ($borderStyle) $borderStyleStr = "border-style:".$borderStyle.";";
            
            $borderStr = $this->getWidthStr($layout,"border");
            
            $border = $layout[border];
            $borderTop = $layout[borderTop];
            $borderRight = $layout[borderRight];
            $borderBottom = $layout[borderBottom];
            $borderLeft = $layout[borderLeft];
            // echo ("Border : '$borderStr' <br>");


            // radius
            $radiusStr = "border-radius:".cmsLayout_getWidthStr($layout,"radius");
            $radius = $layout[radius];
            $radiusTop = $layout[radiusTop];
            $radiusRight = $layout[radiusRight];
            $radiusBottom = $layout[radiusBottom];
            $radiusLeft = $layout[radiusLeft];
            // echo ("Radius : '$radiusStr' <br>");

            // padding
            $paddingStr = $this->getWidthStr($layout,"padding");
            $padding = $layout[padding];
            $paddingTop = $layout[paddingTop];
            $paddingRight = $layout[paddingRight];
            $paddingBottom = $layout[paddingBottom];
            $paddingLeft = $layout[paddingLeft];
            // echo ("Padding : '$paddingStr' <br>");

            $saveData = array();
            if ($layout[id]) $saveData[id] = $layout[id];

            
            $saveData[theme] = $theme;
            $saveData[type] = $editType;
            $saveData[name] = $editType;
            
            $saveStr = "";
            if ($layout[backgroundColor]) {
                $backgroundColor = $layout["backgroundColor"];
                $saveStr .= "background-color:#".$layout["backgroundColor"].";";
                $myKey = "backgroundColor";
                $backgroundColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundColor_data) $saveStr .= $myKey."_data:".$backgroundColor_data.";";
            }
            if ($layout[backgroundRollColor]) {
                $backgroundRollColor = $layout[backgroundRollColor];
                $saveStr .= "background-roll-color:#".$layout[backgroundRollColor].";";
                $myKey = "backgroundRollColor";
                $backgroundRollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundRollColor_data) $saveStr .= $myKey."_data:".$backgroundRollColor_data.";";
            }
            // echo ("<h1>$saveStr</h1>");
            $saveData[background] = $saveStr;

            // color
            $saveStr = "";
            if ($layout[color]) {
                $saveStr .= "color:#".$layout["color"].";";
                $myKey = "color";
                $color_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($color_data) $saveStr .= $myKey."_data:".$color_data.";";
                // echo ("<h1>Color $colorData</h1>");
            }

            if ($layout[rollColor]) {
                $saveStr .= "rollColor:#".$layout["rollColor"].";";
                $myKey = "rollColor";
                $rollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($rollColor_data) $saveStr .= $myKey."_data:".$rollColor_data.";";
                //  echo ("<h1>$key = $colorData</h1>");
            }
            $saveData[color] = $saveStr;

            $saveData[margin] = $marginStr;

            
            // border
            $saveStr = "";
            if ($borderStr) $saveStr .= $borderStr;
            if ($borderColorStr) {
                $saveStr .= $borderColorStr;
                $myKey = "borderColor";
                $borderColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($borderColor_data) $saveStr .= $myKey."_data:".$borderColor_data.";";               
            }
            if ($borderRollColor) {
                $saveStr .= $borderRollColorStr;
                $myKey = "borderRollColor";
                $borderRollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($borderRollColor_data) $saveStr .= $myKey."_data:".$borderRollColor_data.";";                
            }
            if ($borderStyle) $saveStr .= $borderStyleStr;
            $saveData[border] = $saveStr;
            
            
            $saveData[radius] = $radiusStr;

            $saveData[padding] = $paddingStr;

            $saveData[font] = "";

            $saveData[data] = "";
            
            /// SAVE TEXT
            $textData = $this->text_save($theme,$editType,$layout,$textData);
            
            /// SAVE BUTTONS 
            $buttonData = $this->button_save($theme,$editType,$layout,$buttonData);
            
            
           
            $saveResult = cmsStyle_save($saveData);
            if ($saveResult) {
                if (!$layoutId) {
                    $layoutId = $saveResult;
                    $layoutData["id"] = $layoutId;
                }
                cms_infoBox("Layout gespeichert! $saveResult");
                if ($close) $goPage = cms_page_goPage("view=layoutTypes");
                else $goPage = cms_page_goPage("view=layoutTypes&layout=".$editType."");

                reloadPage($goPage,2);
                // echo ("<a href='$goPage' >reload</a><br />");
            } else {
                cms_errorBox("Fehler beim layout speichern");
                foreach ($saveData as $key => $value);
            }
            
            // show_array($saveData);
            
            

        }
        
        
        
        
        
        // echo ("EDIT LAYOUT '<b>$editType</b><br />");
        
        
        $goPage = cms_page_goPage("view=layoutTypes&layout=$editType");
        echo ("<form method='post' action='".$goPage."' >");
        echo ("<input type='hidden' value='$layoutData[id]' name='layout[id]' />");
        // Tabellen Start
        $table = "";
        $table .= "<table class='cmsEditTable' >";
        
        // Tabellen Start Zeile
        $table .= "<tr class='cmsEditTableLine cmsEditTableLine_head' >";
        $table .= "<td class='cmsEditTableColumnTitle' >Bereich</td>";
        $table .= "<td class='cmsEditTableColumn'>Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Rollover</td>";
        $table .= "<td class='cmsEditTableColumn'>Standard-Wert</td>";
        $table .= "<td class='cmsEditTableColumn'>Oben</td>";
        $table .= "<td class='cmsEditTableColumn'>Rechts</td>";
        $table .= "<td class='cmsEditTableColumn'>Unten</td>";
        $table .= "<td class='cmsEditTableColumn'>Links</td>";
        $table .= "<td class='cmsEditTableColumn'>Stil</td>";
        $table .= "</tr>";

       
       
        
        if ($show_background) {
            $line = "";
            $line .= "<tr class='cmsEditTableLine' >";
            $line .= "<td class='cmsEditTableColumnTitle'>Hintergrund</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$backgroundColor,"backgroundColor",$backgroundColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$backgroundRollColor,"backgroundRollColor",$backgroundRollColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "</tr>";

            $table .= $line;
        }

        // SCHRIFT
        if ($show_font) {
            $line = "";
            $line .= "<tr class='cmsEditTableLine' >";
            $line .= "<td class='cmsEditTableColumnTitle'>Schriftfarbe</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$color,"color",$color_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$rollColor,"rollColor",$rollColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("fontStyle",$font,"font",$font_data)."</td>";
            $line .= "</tr>";
            $table .= $line;
        }

        // MARGIN
        if ($show_margin) {
            $line = "";
            $line .= "<td class='cmsEditTableColumnTitle'>Außen-Abstand</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$margin,"margin")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginTop,"marginTop")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginRight,"marginRight")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginBottom,"marginBottom")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginLeft,"marginLeft")."</td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "</tr>";
            $table .= $line;
        }
            

        // Rahmen   
        if ($show_border) {
            $line = "";
            $line .= "<td  class='cmsEditTableColumnTitle'>Rahmen</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$borderColor,"borderColor",$borderColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$borderRollColor,"borderRollColor",$borderRollColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$border,"border")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderTop,"borderTop")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderRight,"borderRight")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderBottom,"borderBottom")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderLeft,"borderLeft")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("dropdown",$borderStyle,"borderStyle","borderStyle")."</td>";        
            $line .= "</tr>";
            $table .= $line;
        }


         // Radius
        if ($show_radius) {
            $line = "";
            $line .= "<td  class='cmsEditTableColumnTitle'>Radius</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radius,"radius")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusTop,"radiusTop")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusRight,"radiusRight")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusBottom,"radiusBottom")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusLeft,"radiusLeft")."</td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "</tr>";
            $table .= $line;
        }
        
        // Innenanstand
        if ($show_padding) {
            $line = "";
            $line .= "<td  class='cmsEditTableColumnTitle'>Innerer Abstand</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>-</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$padding,"padding")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingTop,"paddingTop")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingRight,"paddingRight")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingBottom,"paddingBottom")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingLeft,"paddingLeft")."</td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "</tr>";
            $table .= $line;
        }
        
//        foreach ($font_list as $key => $name) {
//            $line = "";
//            $line .= "<td class='cmsEditTableColumnTitle'>Schrift $name</td>";
//            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$color,"color",$color_data)."</td>";
//            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$rollColor,"rollColor",$rollColor_data)."</td>";
//            $line .= "<td class='cmsEditTableColumn'>-</td>";
//            $line .= "<td class='cmsEditTableColumn'>-</td>";
//            $line .= "<td class='cmsEditTableColumn'>-</td>";
//            $line .= "<td class='cmsEditTableColumn'>-</td>";
//            $line .= "<td class='cmsEditTableColumn'>-</td>";
//            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("fontStyle",$font,"font",$font_data)."</td>";           
//            $line .= "</tr>";
//            $table .= $line;
//        }
        
        
        
        

        $table .= "</table>";
        echo ($table);
        
        /// SHOW EDIT TEXT
        if (count($text_list)) {
            $this->text_showEdit($theme,$editType,$textData);
        }
        
        /// SHOW EDIT BUTTON
        if (count($button_list)) {
            $this->button_showEdit($theme,$editType,$buttonData);
        }
        
        
        echo ("<input type='submit' class='cmsInputButton' value='speichern' name='layoutSave' />");
        echo ("<input type='submit' class='cmsInputButton' value='speichern und schließen' name='layoutSaveClose' />");
        
        $goPage = cms_page_goPage("view=layoutTypes");
        echo ("<a href='$goPage' class='cmsLinkButton cmsSecond' >abbrechen</a>");
        
        echo ("</form>");

        div_end("cmsContentEditFrame");
      
    }
    
    function createStyle($theme) {
        
        $ent = "\n";
        $tab = "   ";
        
        $css = "";
        
        $css .= $this->createStyle_special($theme,$tab,$ent);
        
        $css .= $this->createStyle_frame($theme,$tab,$ent);
        
        
        $css .= $this->createStyle_spacer($theme,$tab,$ent);
        
        $cssFile = "style/$theme-style.css";
        echo ("<h1>CSS - $cssFile</h1>");
        echo (str_replace(array($ent,$tab),array("&nbsp;<br/>","&nbsp; &nbsp; "),$css));
        
        
        if (file_exists($cssFile)) {
            echo ("File EXIST $cssFile <br>");
        } else {
            echo ("FILE NOT exist $cssFile <br>");
        }
        $compactCss = 1;
        if ($compactCss) {
            $css = str_replace($tab,"", $css);
            $css = str_replace(";".$ent,";", $css);
            $css = str_replace("{".$ent,"{ ", $css);
            $css = str_replace($ent.$ent.$ent,$ent, $css);
            $css = str_replace($ent.$ent,$ent, $css);
        }
        saveText($css, $cssFile);
        
    }
    
    function createStyle_frame($theme,$tab,$ent) {
        $frameList = cmsStyle_getList(array("type"=>"frame","theme"=>$theme),null,"assoName");
        $css = "";
        foreach($frameList as $name => $layoutData) {
            // echo ("<h1>$name $layoutData </h1>");
            $cssList = array();
            foreach($layoutData as $key => $value) {
                $splitList = explode(";",$value);
                for($i=0;$i<count($splitList);$i++) {
                    if ($splitList[$i]) {
                        list($key2,$val2) = explode(":",$splitList[$i]);
                        if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                    }
                }
            }
            
            $frameStr = ".$name {".$ent;
            $frameHoverStr = ".$name:hover {".$ent;
            
            
            foreach ($cssList as $key => $value) {
                switch ($key) {
                    case "backgroundColor_data" : break;
                    case "background-color" :
                        $useColor = $value;
                        $colorData = $cssList["backgroundColor_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            // echo ("Background FARBE $value -> $data= '$colorData' $useColor<br>");

                        }
                        $frameStr .= "$tab background-color:$useColor;".$ent;
                        break;
                   

                    case "backgroundRollColor_data" : break;
                    case "background-roll-color" :
                        $useColor = $value;
                        $colorData = $cssList["backgroundRollColor_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            // echo ("Background Roll FARBE $value -> $data = '$colorData' $useColor<br>");

                        }
                        $frameHoverStr .= "$tab background-color:$value;".$ent; break;

                    case "color_data" : break;
                    case "color" :
                        echo "COLOR $value <br />";
                        $useColor = $value;
                        $colorData = $cssList["color_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            echo ("Schrift FARBE  $value -> $data= '$colorData' $useColor<br>");

                        }
                        $frameStr .= "$tab color:$value;".$ent;
                        break;

                    case "rollColor_data" : break;
                    case "rollColor" :
                        $useColor = $value;
                        $colorData = $cssList["rollColor_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            // echo ("Schrift Roll FARBE $value -> $data= '$colorData' $useColor<br>");

                        }
                        $frameHoverStr .= "$tab color:$value;".$ent;
                        break;

                    case "borderColor_data" : break;
                    case "border-color" :
                        $useColor = $value;
                        $colorData = $cssList["borderColor_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                        }
                        $frameStr .= "$tab border-color:$value;".$ent;
                        break;

                    case "borderRollColor_data" : break;
                    case "border-roll-color" :
                        $useColor = $value;
                        $colorData = $cssList["borderRollColor_data"];
                        if ($colorData) {
                            $useColor = cmsStyle_getColor($colorData);
                            // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                        }
                        $frameHoverStr .= "$tab border-color:$value;".$ent;
                        break;



                    //case "border-roll-color" : $frameHoverStr .= "$tab border-color:$value;$ent"; break;
                    default :
                        $add = 0;
                        if (substr($key,0,6) == "margin") {
                            $add = 1;
                            $frameStr .= "$tab $key:$value;".$ent; break;
                        }
                        if (substr($key,0,6) == "border") {
                            $add = 1;
                            $frameStr .= "$tab $key:$value;".$ent; break;
                        }
                        
                        if (substr($key,0,7) == "padding") {
                            $add = 1;
                            $frameStr .= "$tab $key:$value;".$ent; break;
                        }
                        
                        if ($add == 0) {
                        
                            echo ("$key => $value <br>");
                        }
                }
                
            }
            
            
            
            
            
            $frameStr .= "}$ent";
           
            $frameHoverStr .= "}$ent";

            
            
            if ($frameStr) {
                $css.= $frameStr.$ent;
            }
            if ($frameHoverStr) {
                $css .= $frameHoverStr.$ent;
            }
            
            
            $headCss = $this->createStyle_head($theme,$name,$ent,$tab);
            if ($headCss) {
                $css .= $ent.$headCss;
            }
           
        }
         return $css;
       
        
    }
    
    function createStyle_special($theme,$tab,$ent) {
        $specialList = array("header"=>"Header","footer"=>"Fußzeile","navi"=>"Navigation","titleLine"=>"Kopfzeile");
        $filter = array();
        $filter[theme] = $theme;
        $css = "";
        
        foreach ($specialList as $special => $name) {
            $filter[type] = $special;
            
            $cssData = cmsStyle_get($filter);
            if (is_array($cssData)) {
                $cssList = array();
                foreach($cssData as $key => $value) {
                    $splitList = explode(";",$value);
                    for($i=0;$i<count($splitList);$i++) {
                        if ($splitList[$i]) {
                            list($key2,$val2) = explode(":",$splitList[$i]);
                            if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                        }
                    }
                }
                
                $frameStr = "";
                $frameHoverStr = "";
                foreach ($cssList as $key => $value) {
                    switch ($key) {
                        case "backgroundColor_data" : break;
                        case "background-color" :
                            $useColor = $value;
                            $colorData = $cssList["backgroundColor_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Background FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab background-color:$useColor;".$ent;
                            break;


                        case "backgroundRollColor_data" : break;
                        case "background-roll-color" :
                            $useColor = $value;
                            
                            $colorData = $cssList["backgroundRollColor_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                               

                            }
                            echo ("Background Roll Farbe $value ->'$colorData' $useColor<br>");
                            $frameHoverStr .= "$tab background-color:$useColor;".$ent; break;

                        case "color_data" : break;
                        case "color" :
                            $useColor = $value;
                            $colorData = $cssList["color_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift FARBE  $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab color:$value;".$ent;
                            break;

                        case "rollColor_data" : break;
                        case "rollColor" :
                            $useColor = $value;
                            $colorData = $cssList["rollColor_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift Roll FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab color:$useColor;".$ent;
                            break;

                        case "borderColor_data" : break;
                        case "border-color" :
                            $useColor = $value;
                            $colorData = $cssList["borderColor_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab border-color:$useColor;".$ent;
                            break;

                        case "borderRollColor_data" : break;
                        case "border-roll-color" :
                            $useColor = $value;
                            $colorData = $cssList["borderRollColor_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab border-color:$useColor;".$ent;
                            break;



                        //case "border-roll-color" : $frameHoverStr .= "$tab border-color:$value;$ent"; break;
                        default :
                            $add = 0;
                            if (substr($key,0,6) == "margin") {
                                $add = 1;
                                if ($value == "") $value = 0;
                                $frameStr .= "$tab $key:$value;".$ent; 
                                
                            }
                            if (substr($key,0,6) == "border") {
                                $add = 1;
                                if ($value == "") $value = 0;
                                $frameStr .= "$tab $key:$value;".$ent; 
                                
                            }

                            if (substr($key,0,7) == "padding") {
                                $add = 1;
                                if ($value == "") $value = 0;
                                $frameStr .= "$tab $key:$value;".$ent; 
                            }

                            if ($add == 0) {

                                echo ("$key => $value <br>");
                            }
                    }

                }
                
                if ($frameStr) {
                    $css .= ".".$special." {".$ent;
                    $css .= $frameStr;
                    $css .= "}".$ent.$ent;
                }
                
                if ($frameHoverStr) {
                    $css .= ".".$special.":hover {".$ent;
                    $css .= $frameHoverStr;
                    $css .= "}".$ent.$ent;
                }
                
                /// Create TEXT
                $css .= $this->createStyle_specialText($theme, $special, $tab, $ent);
                
                /// CREATE BUTTONS 
                $css .= $this->createStyle_specialButton($theme, $special, $tab, $ent);
//                echo ("Create Style $theme $special $name<br/>");
//                echo ("FrameStr = $frameStr <br />");
//                echo ("Fr;Hover = $frameHoverStr <br/>");
                            
            } else {
                echo ("<h3>No CASS-Data for '$special' </h3>");
            }
            
            
            
           
        }
        
        return $css;
        
        
        
    }
    
    function createStyle_specialText($theme, $special, $tab, $ent) {
        $textList = $this->edit_textList($special);
        $css = "";
        $filter = array("theme"=>$theme,"type"=>"text");
        $out = 1;
        foreach ($textList as $textKey => $textName) {
            $filter[name] = $special.$textKey;
            $cssData = cmsStyle_get($filter);
            if ($out) echo ("<h3>Texts for $special -> $textKey $cssData </h3>");
            if (is_array($cssData)) {
                $cssList = array();
                foreach($cssData as $key => $value) {
                    $splitList = explode(";",$value);
                    for($i=0;$i<count($splitList);$i++) {
                        if ($splitList[$i]) {
                            list($key2,$val2) = explode(":",$splitList[$i]);
                            if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                        }
                    }
                }
                
                $frameStr = "";
                $frameHoverStr = "";
                foreach ($cssList as $key => $value) {
                    switch ($key) {
                        case "background-color-data" : break;
                        case "background-color" :
                            $useColor = $value;
                            $colorData = $cssList["background-color_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                if ($out) echo ("Background FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab background-color:$useColor;".$ent;
                            break;


                        case "background-roll-color-data" : break;
                        case "background-roll-color" :
                            $useColor = $value;
                            
                            $colorData = $cssList["background-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                            }
                            if ($out) echo ("Background Roll Farbe $value ->'$colorData' $useColor<br>");
                            $frameHoverStr .= "$tab background-color:$useColor;".$ent; 
                            break;

                        case "font-color-data" : break;
                        case "font-color" :
                            $useColor = $value;
                            $colorData = $cssList["font-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift FARBE  $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab color:$value;".$ent;
                            break;

                        case "font-roll-color-data" : break;
                        case "font-roll-color" :
                            $useColor = $value;
                            $colorData = $cssList["font-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift Roll FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab color:$useColor;".$ent;
                            break;

                        case "border-color-data" : break;
                        case "border-color" :
                            $useColor = $value;
                            $colorData = $cssList["border-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab border-color:$useColor;".$ent;
                            break;

                        case "border-roll-color-data" : break;
                        case "border-roll-color" :
                            $useColor = $value;
                            $colorData = $cssList["border-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab border-color:$useColor;".$ent;
                            break;

                        case "shadow-color-data" : break;
                        case "shadow-left" :break;
                        case "shadow-right" : break;
                        case "shadow-color" :
                            // box-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px #"+shadowColor);
                            
                            $shadowLeft  = intval($cssList["shadow-left"]);
                            $shadowRight = intval($cssList["shadow-right"]);
                            
                            $shadowMax = $shadowLeft;
                            if ($shadowRight > $shadowMax) $shadowMax = $shadowRight;
                            
                            if ($shadowMax < 0 ) $shadowMax = $shadowMax * -1;
                            $useColor = $value;
                            $colorData = $cssList["shadow-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                
                                if ($shadowLeft != 0)  $shadowLeft .= "px";
                                if ($shadowRight != 0) $shadowRight .= "px";
                                if ($shadowMax != 0)   $shadowMax .= "px";
                                
                                // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");
                                $frameStr .= "$tab text-shadow:$shadowLeft $shadowRight $shadowMax $useColor;".$ent;
                            }
                                
                            break;
                            
                        case "border-radius" :
                            $borderRadius = intval($value);
                            if ($shadowRight > 0) $shadowRight .= "px";
                            $frameStr .= $tab."border-radius:$borderRadius;".$ent;
                            break;
                            
                        case "font-data" :
                            list($size,$bold,$kursiv,$underline) = explode("|",$value);
                            if ($size) {
                                $size = intval($size);
                                if ($size > 0) {
                                    $frameStr .= $tab."font-size:".$size."px;$ent";
                                    $frameStr .= $tab."line-height:".$size."px;$ent";
                                }
                            }
                            if ($bold) $frameStr .= $tab."font-weight:bold;".$ent;
                            else $frameStr .= $tab."font-weight:normal;".$ent;

                            if ($kursiv) $frameStr .= $tab."font-style:italic;".$ent;
                            else $frameStr .= $tab."font-style:normal;".$ent;

                            if ($underline) $frameStr .= $tab."text-decoration:underline;".$ent;
                            else $frameStr .= $tab."text-decoation:none;".$ent;
                            break;
                            
                    case "font-roll-data" :
                            list($size,$bold,$kursiv,$underline) = explode("|",$value);
                            if ($size) {
                                $size = intval($size);
                                if ($size > 0) {
                                    $frameHoverStr .= $tab."font-size:".$size."px;".$ent;
                                    $frameHoverStr .= $tab."line-height:".$size."px;$ent";
                                }
                            }
                            if ($bold) $frameHoverStr .= $tab."font-weight:bold;".$ent;
                            else $frameHoverStr .= $tab."font-weight:normal;".$ent;

                            if ($kursiv) $frameHoverStr .= $tab."font-style:italic;".$ent;
                            else $frameHoverStr .= $tab."font-style:normal;".$ent;

                            if ($underline) $frameHoverStr .= $tab."text-decoration:underline;".$ent;
                            else $frameHoverStr .= $tab."text-decoation:none;".$ent;
                            break;                            

                        //case "border-roll-color" : $frameHoverStr .= "$tab border-color:$value;$ent"; break;
                        default :
                            $add = 0;
//                            if (substr($key,0,6) == "margin") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }
//                            if (substr($key,0,6) == "border") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }
//
//                            if (substr($key,0,7) == "padding") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }

                            if ($add == 0) {
                                 if ($textKey == "Link") {
                                    echo ("$textKey <b>$key </b> => $value <br>");
                                 }
                            }
                    }

                }
                
                if ($frameStr) {
                    $css .= ".".$special.$textKey." {".$ent;
                    $css .= $frameStr;
                    $css .= "}".$ent.$ent;
                }
                
                if ($frameHoverStr) {
                    $css .= ".".$special.$textKey.":hover {".$ent;
                    $css .= $frameHoverStr;
                    $css .= "}".$ent.$ent;
                }
            }
//                
//                
//                $css .= $this->createStyle_specialText($theme, $special, $tab, $ent);
            
            
           
            
            
        }
        
        
        
        
        
        return $css;
    }
    
    
    
    function createStyle_specialButton($theme, $special, $tab, $ent) {
        $buttonList = $this->edit_buttonList($special);
        $css = "";
        $filter = array("theme"=>$theme,"type"=>"button");
        $out = 1;
        foreach ($buttonList as $buttonKey => $buttonName) {
            $filter[name] = $special.$buttonKey;
            $cssData = cmsStyle_get($filter);
            if ($out) echo ("<h3>Buttons for $special -> $buttonKey $cssData </h3>");
            if (is_array($cssData)) {
                $cssList = array();
                foreach($cssData as $key => $value) {
                    $splitList = explode(";",$value);
                    for($i=0;$i<count($splitList);$i++) {
                        if ($splitList[$i]) {
                            list($key2,$val2) = explode(":",$splitList[$i]);
                            if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                        }
                    }
                }
                
                $frameStr = "";
                $frameHoverStr = "";
                foreach ($cssList as $key => $value) {
                    switch ($key) {
                        case "background-color-data" : break;
                        case "background-color" :
                            $useColor = $value;
                            $colorData = $cssList["background-color_data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                if ($out) echo ("Background FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab background-color:$useColor;".$ent;
                            break;


                        case "background-roll-color-data" : break;
                        case "background-roll-color" :
                            $useColor = $value;
                            
                            $colorData = $cssList["background-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                            }
                            if ($out) echo ("Background Roll Farbe $value ->'$colorData' $useColor<br>");
                            $frameHoverStr .= "$tab background-color:$useColor;".$ent; 
                            break;

                        case "font-color-data" : break;
                        case "font-color" :
                            $useColor = $value;
                            $colorData = $cssList["font-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift FARBE  $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab color:$value;".$ent;
                            break;

                        case "font-roll-color-data" : break;
                        case "font-roll-color" :
                            $useColor = $value;
                            $colorData = $cssList["font-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Schrift Roll FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab color:$useColor;".$ent;
                            break;

                        case "border-color-data" : break;
                        case "border-color" :
                            $useColor = $value;
                            $colorData = $cssList["border-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameStr .= "$tab border-color:$useColor;".$ent;
                            break;

                        case "border-roll-color-data" : break;
                        case "border-roll-color" :
                            $useColor = $value;
                            $colorData = $cssList["border-roll-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $frameHoverStr .= "$tab border-color:$useColor;".$ent;
                            break;

                        case "shadow-color-data" : break;
                        case "shadow-left" :break;
                        case "shadow-right" : break;
                        case "shadow-color" :
                            // box-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px #"+shadowColor);
                            
                            $shadowLeft  = intval($cssList["shadow-left"]);
                            $shadowRight = intval($cssList["shadow-right"]);
                            
                            $shadowMax = $shadowLeft;
                            if ($shadowRight > $shadowMax) $shadowMax = $shadowRight;
                            
                            if ($shadowMax < 0 ) $shadowMax = $shadowMax * -1;
                            $useColor = $value;
                            $colorData = $cssList["shadow-color-data"];
                            if ($colorData) {
                                $useColor = cmsStyle_getColor($colorData);
                                
                                if ($shadowLeft != 0)  $shadowLeft .= "px";
                                if ($shadowRight != 0) $shadowRight .= "px";
                                if ($shadowMax != 0)   $shadowMax .= "px";
                                
                                // echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");
                                $frameStr .= "$tab box-shadow:$shadowLeft $shadowRight $shadowMax $useColor;".$ent;
                            }
                                
                            break;
                            
                        case "border-radius" :
                            $borderRadius = intval($value);
                            if ($shadowRight > 0) $shadowRight .= "px";
                            $frameStr .= $tab."border-radius:$borderRadius;".$ent;
                            break;
                            
                        case "font-data" :
                            list($size,$bold,$kursiv,$underline) = explode("|",$value);
                            if ($size) {
                                $size = intval($size);
                                if ($size > 0) {
                                    $frameStr .= $tab."font-size:".$size."px;$ent";
                                    $frameStr .= $tab."line-height:".$size."px;$ent";
                                }
                            }
                            if ($bold) $frameStr .= $tab."font-weight:bold;".$ent;
                            else $frameStr .= $tab."font-weight:normal;".$ent;

                            if ($kursiv) $frameStr .= $tab."font-style:italic;".$ent;
                            else $frameStr .= $tab."font-style:normal;".$ent;

                            if ($underline) $frameStr .= $tab."text-decoration:underline;".$ent;
                            else $frameStr .= $tab."text-decoation:none;".$ent;
                            break;
                            
                    case "font-roll-data" :
                            list($size,$bold,$kursiv,$underline) = explode("|",$value);
                            if ($size) {
                                $size = intval($size);
                                if ($size > 0) {
                                    $frameHoverStr .= $tab."font-size:".$size."px;".$ent;
                                    $frameHoverStr .= $tab."line-height:".$size."px;$ent";
                                }
                            }
                            if ($bold) $frameHoverStr .= $tab."font-weight:bold;".$ent;
                            else $frameHoverStr .= $tab."font-weight:normal;".$ent;

                            if ($kursiv) $frameHoverStr .= $tab."font-style:italic;".$ent;
                            else $frameHoverStr .= $tab."font-style:normal;".$ent;

                            if ($underline) $frameHoverStr .= $tab."text-decoration:underline;".$ent;
                            else $frameHoverStr .= $tab."text-decoation:none;".$ent;
                            break;                            

                        //case "border-roll-color" : $frameHoverStr .= "$tab border-color:$value;$ent"; break;
                        default :
                            $add = 0;
//                            if (substr($key,0,6) == "margin") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }
//                            if (substr($key,0,6) == "border") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }
//
//                            if (substr($key,0,7) == "padding") {
//                                $add = 1;
//                                if ($value == "") $value = 0;
//                                $frameStr .= "$tab $key:$value;".$ent; break;
//                            }

                            if ($add == 0) {
                                 if ($buttonKey == "Link") {
                                    echo ("$buttonKey <b>$key </b> => $value <br>");
                                 }
                            }
                    }

                }
                
                if ($frameStr) {
                    $css .= ".".$special.$buttonKey." {".$ent;
                    $css .= $frameStr;
                    $css .= "}".$ent.$ent;
                }
                
                if ($frameHoverStr) {
                    $css .= ".".$special.$buttonKey.":hover {".$ent;
                    $css .= $frameHoverStr;
                    $css .= "}".$ent.$ent;
                }
            }
//                
//                
//                $css .= $this->createStyle_specialButton($theme, $special, $tab, $ent);
            
            
           
            
            
        }
        
        
        
        
        
        return $css;
    }
    
    

    function createStyle_head($theme,$frameName,$ent,$tab) {
        $headList = array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");
       //  echo ("<h2>Create Head Style for '$frameName' </h2>" );
        
        $headStr = "";
        $headHoverStr = "";
        
        $out = "css";
        $filter = array();
        $filter[theme] = $theme;
        $filter[type] = "headline";

        $head = "";
        
        foreach($headList as $headKey => $headName ) {
            $filter[name] = $frameName."_".$headKey;
            
            $headData = cmsStyle_get($filter,$out);

            // echo ("<h3>headData for $headKey name=$filter[name] $headData </h3>");
            if (is_array($headData)) {
                // .noFrame .frameTextHeadLine_h3

                $headFrameStr = ".".$frameName." .textHeadline_".$headKey.", .".$frameName." .frameTextHeadLine_".$headKey."{".$ent;
                $headFrameHoverStr = ".".$frameName." .textHeadline_".$headKey.":hover, .".$frameName." .frameTextHeadLine_".$headKey.":hover {".$ent;
                $headStr = ".".$frameName." .textHeadline_".$headKey." ".$headKey.", .".$frameName." .frameTextHeadLine_".$headKey." ".$headKey."{".$ent;
                $headHoverStr = ".".$frameName." .textHeadline_".$headKey." ".$headKey.":hover, .".$frameName." .frameTextHeadLine_".$headKey." ".$headKey.":hover {".$ent;
                // $headStr .= $tab."background-color:#f00;".$ent;


                foreach ($headData as $key => $value) {
                    switch ($key) {
                        case "color_data" : break;
                        case "color" :
                            $useColor = $value;
                            $colorData = $cssList["color_data"];
                            if ($colorData) {
                                $useColor = "#".cmsStyle_getColor($colorData);
                                //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            // $headStr .= $tab."color:#$useColor;".$ent;
                            $headFrameStr .= $tab."color:#$useColor;".$ent;
                            $headStr .= $tab."color:inherit;".$ent;

                            break;

                        case "rollColor_data" : break;
                        case "rollColor" :
                            $useColor = $value;
                            $colorData = $cssList["rollColor_data"];
                            if ($colorData) {
                                $useColor = "#".cmsStyle_getColor($colorData);
                                //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $headFrameHoverStr .= $tab."color:#$useColor;".$ent;
                            $headHoverStr .= $tab."color:#$useColor;".$ent;
                            break;

                        case "borderColor_data" : break;
                        case "borderColor" :
                            $useColor = $value;
                            $colorData = $cssList["borderColor_data"];
                            if ($colorData) {
                                $useColor = "#".cmsStyle_getColor($colorData);
                                //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $headFrameStr .= $tab."border-color:#$useColor;".$ent;
                            break;

                        
                        case "borderRollColor_data" : break;
                        case "borderRollColor" :
                            $useColor = $value;
                            $colorData = $cssList["borderRollColor_data"];
                            if ($colorData) {
                                $useColor = "#".cmsStyle_getColor($colorData);
                                //  echo ("Rahmen FARBE $value -> $data= '$colorData' $useColor<br>");

                            }
                            $headFrameHoverStr .= $tab."border-color:#$useColor;".$ent;
                            break;
                        

                        case "border-style" :
                            $headFrameStr .= $tab.$key.":".$value.";".$ent;
                            break;

                        case "font" :
                            list($size,$bold,$kursiv,$underline) = explode("|",$value);

                            if ($bold) $headStr .= $tab."font-weight:bold;".$ent;
                            else $headStr .= $tab."font-weight:normal;".$ent;

                            if ($kursiv) $headStr .= $tab."font-style:italic;".$ent;
                            else $headStr .= $tab."font-style:normal;".$ent;

                            if ($underline) $headStr .= $tab."text-decoration:underline;".$ent;
                            else $headStr .= $tab."text-decoation:none;".$ent;
                            break;
                        
                    default :
                        
                        if (substr($key,0,6)== "border") {
                            if ($value) $value .= "";
                            $headFrameStr .= $tab.$key.":".$value.";".$ent;
                        } else {
                            // echo ("<h1 style='color:#f00;'>$key = $value </h1>");
                        }
                    }

                }

                // close HeadLine Frame css;
                $headFrameStr .= "}".$ent;
                $headFrameHoverStr .= "}".$ent;

                // close HeadLine Text css;
                $headStr .= "}".$ent;
                $headHoverStr .= "}".$ent;

                if ($headFrameStr) {
                    $head .= $headFrameStr.$ent;
                    $head .= $ent;
                }
                if ($headFrameHoverStr) {
                    $head .= $headFrameHoverStr.$ent;
                    $head .= $ent;
                }


                if ($headStr) {
                    $head .= $headStr.$ent;
                    $head .= $ent;
                }
                if ($headHoverStr) {
                    $head .= $headHoverStr.$ent;
                    $head .= $ent;
                }

            }

            


        }
        return $head;
        
    }
    
    
    function createStyle_spacer($theme,$tab,$ent) {
        $filter = array("type"=>"spacer","theme"=>$theme);
        
        
        $sort = "id";
        $out = "assoName";
        $spacerList = cmsStyle_getList($filter,$sort,$out);
        
        $out = 0;
        $css = "";
        
        foreach ($spacerList as $spacerType => $spacerValue) {
            if ($out) echo ("<h1> SPACER $spacerType $spacerValue </h1>");
            
            $cssList = array();
            foreach($spacerValue as $key => $value) {
                $splitList = explode(";",$value);
                for($i=0;$i<count($splitList);$i++) {
                    if ($splitList[$i]) {
                        list($key2,$val2) = explode(":",$splitList[$i]);
                        if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                    }
                }
            }
                
            
            $backgroundColor = "transparent";
            $backgroundRollColor = null;
            $lineStyle = "solid";
            $lineColor = "#666";
            $lineHeight = 0;
            $height = 0;
            foreach ($cssList as $key => $value) {
                switch ($key) {
                    case "background-color-data" : break;
                    case "background-color" :
                        $backgroundColor = $value;
                        $colorData = $cssList["background-color_data"];
                        if ($colorData) {
                            $backgroundColor = cmsStyle_getColor($colorData);
                            if ($out) echo ("Background FARBE $value -> $data= '$colorData' $useColor<br>");
                        }
                        break;

                    case "background-roll-color-data" : break;
                    case "background-roll-color" :
                        $backgroundRollColor = $value;

                        $colorData = $cssList["background-roll-color-data"];
                        if ($colorData) {
                            $backgroundRollColor = cmsStyle_getColor($colorData);
                        }
                        if ($out) echo ("Background Roll Farbe $value ->'$colorData' $useColor<br>");
                        break;

                    case "line-color-data" : break;
                    case "line-color" :
                        $lineColor = $value;
                        $colorData = $cssList["line-color-data"];
                        if ($colorData) {
                            $lineColor = cmsStyle_getColor($colorData);
                        }                        
                        break;
                    case "line-height" :
                        
                            $lineHeight = intval($value);
                        
                        break;
                    case "height" :
                         
                            $height = intval($value);
                        
                        break;
                                               

                        //case "border-roll-color" : $frameHoverStr .= "$tab border-color:$value;$ent"; break;
                    default :
                        $add = 0;
                        echo ("$buttonKey <b>$key </b> => $value <br>");
                }
             
                    
                
                
            }
            
            $css .= ".spacer".$spacerType."{".$ent;
            
            $marginHeight = floor(($height - $lineHeight) / 2);
            if ($marginHeight > 0 ) $marginHeight .= "px";
            
            if ($lineHeight > 0) $lineHeight .= "px";
            
            $css .= $tab."margin:$marginHeight 0;".$ent;
            if ($lineHeight) {
                $css .= $tab."border-top: $lineHeight solid $lineColor;".$ent;
            } else {
                $css .= $tab."border-top: 0;".$ent;
            }
            $css .= $tab."display:block;".$ent;
            $css .= $tab."height:0;".$ent;
            $css .= "}".$ent;
            
            
            
        }
        
        
        
        
        return $css;
        
    }
    
    function showTypesContent($theme,$editLayout,$frameWidth) {
        
        
        
        $filter = array("type"=>"frame","theme"=>$theme);
        $sort = "id";
        $out = "assoName";
        $frameList = cmsStyle_getList($filter,$sort,$out);

        $standardFrames = array("noFrame","frame1","frame2","rollFrame");
        for ($i=0;$i<count($standardFrames);$i++) {
            $name = $standardFrames[$i];
            if (!is_array($frameList[$name])) $frameList[$name] = array();
        }
        
        div_start("content");    

        $headList = array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");

        
        $i = 0;
        foreach($frameList as $name => $value ) {
            
            div_start("cmsLayoutFrame","width:".$frameWidth."px;");
            div_start("cmsLayoutFrame_left","width:100px;float:left;background-color:transparent;");
              
            $edit = 0;
            if ($editLayout == $name) {
                $edit = 1;
                $editLink = 0;
            } else {
                $editLink = cms_page_goPage("view=layoutTypes&layout=$name");
            }
           //  echo("<a href='$editLink' class='hiddenLink' >edit</a>");
            if ($editLink) {
                echo("<a href='$editLink' class='layoutTitle' style='display:block;' >$name</a>");
            } else {
                echo ("<span class='layoutTitle' style='display:block;'>$name</span><br/>");
            }
            
            div_end("cmsLayoutFrame_left");
            div_start("cmsLayoutFrame_right","width:".($frameWidth-150)."px;min-height:16px;float:left;");
                    
            
            
            
            
            $edit = 0;
            $myStyle = "";
            if ($editLayout == $name) {
                div_start("layoutEditFrame cmsContentEditFrame","margin:0 -2px;border-width:2px;padding:0px;background-color:inherit;");               
                $myStyle = $this->editLayout_frame($theme,$name,$value,$frameWidth);
                $edit = 1;
                // div_end("cmsContentEditFrame");
            }
            
            $divData = array();
            if ($myStyle) {
                // echo ("<b>myStyle='$myStyle'</b><br>");
                $divData[style] = $myStyle;
            }
            
            $divName = $name; // ." cmsFrameLink";
            if ($edit) {
                $divName = $name." cmsLayout_FrameSettings";
            }
            
            div_start($divName,$divData);
           // $editLink = cms_page_goPage("view=layoutTypes&layout=$name");
            // echo("<a href='$editLink' class='hiddenLink' >edit</a>");
            // echo ("<span class='layoutTitle'>$name</span><br/>");
            // echo ("$name<br/>");

            foreach($headList as $head => $headText) {
                div_start("textHeadline textHeadline_$head");
                echo ("<$head>Überschrift 1</$head>");
                div_end("textHeadline textHeadline_$head");
            }
            echo ("Hier steht ein Text der relativ lang ist und auch eigentlich eine neue Zeile erzeugen sollte, da der Text länger ist als der Rahmen breit ist! Upps reicht doch nicht und nun? Vielleicht einfach noch mehr Text.");

            div_end($divName);

            if ($edit) {
                div_end("layoutEditFrame cmsContentEditFrame");
            }
            
            div_end("cmsLayoutFrame_right");

            /// SPACER RIGHT
//            div_start("cmsLayoutFrame_spacer");
//            $editSpacerLink = "#";
//            echo ("<a href='$editSpacerLink' class='layoutSpacer' >$show</a>");
//            div_end ("cmsLayoutFrame_spacer");

            div_end("cmsLayoutFrame","before");
            
            
            

            // Content Spacer
            if ($i<count($frameList)-1) {
                $dontEditSpacer = 0;
                if ($i > 0) $dontEditSpacer = 1;
                $this->spacer_show($theme,"ContentType",$frameWidth,$dontEditSpacer);
                
//                div_start("spacerContentType");
//                div_end("spacerContentType");
            }
            $i++;
        }


        // Spacer Content End
//        div_start("spacercontent");
//        div_end("spacercontent");

        // Content END
        div_end("content");
    }
    
    function spacer_show($theme,$spacerType,$frameWidth,$dontEditSpacer=0) {
        $spacerHeight = 0;
        $activeSpacer = $_GET[spacer];
       
        div_start("cmsLayoutFrame","width:".$frameWidth."px;height:1px;position:relative;overflow:visible;");
        if ($activeSpacer == $spacerType) {
           $edit = 1;
           if ($dontEditSpacer) $edit = 0;
           $spacerHeight = 5;
           
        }
        div_start("cmsLayoutFrame_left","width:100px;height:1px;float:left;background-color:transparent;display:block;");
        echo("&nbsp;");
        div_end("cmsLayoutFrame_left");

        //div_start("cmsLayoutFrame_right","width:".($frameWidth-150)."px;min-height:16px;float:left;");
        $divName = "spacer spacer".$spacerType;
        if ($edit) {
            $divSpacerName = "spacerFrame";
            div_start($divSpacerName,"width:".($frameWidth-150)."px;background-color:transparent;height:auto;float:left;display:block;");
            $this->spacer_showEdit($theme,$spacerType);
            $divName .= " cmsLayout_FrameSettings";
        }
        

        div_start($divName,"width:".($frameWidth-150)."px;;background-color:#f00;float:left;");
        echo ("&nbsp;");
        div_end($divName);
        
        if ($edit) {
            div_end($divSpacerName);
        }
        
        //div_end("cmsLayoutFrame_right");
                
        div_start("cmsLayoutFrame_spacer","position:absolute;left:".($frameWidth-50)."px;");
        $editSpacerLink = cms_page_goPage("view=layoutTypes&spacer=$spacerType");
        if ($edit) {
            echo ("<span class='layoutSpacer layoutSpacerActive' >Abstand</span>");
        } else {
            echo ("<a href='$editSpacerLink' class='layoutSpacer' title='Spacer $spacerType' >Abstand</a>");
        }
        div_end ("cmsLayoutFrame_spacer");
        div_end("cmsLayoutFrame","before");
    }
    
    function spacer_showEdit($theme,$spacerType){
      
        div_start("cmsContentEditFrame");
        echo ("<h1> EDIT SPACER $spacerType </h1>");
        $layoutData = array();
        $filter = array("theme"=>$theme,"type"=>"spacer","name"=>$spacerType);
        $cssData = cmsStyle_get($filter,"css");
        if (is_array($cssData)) {
            foreach($cssData as $key => $value) {
                switch ($key) {
                    case "id" : $layoutData[id] = $value; break;
                    case "background-color" : $backgroundColor = $value; break;
                    case "background-color-data" : $backgroundColor_data = $value; break;
                    case "background-roll-color" : $backgroundRollColor = $value; break;
                    case "background-roll-color-data" : $backgroundRollColor_data = $value; break;
                    case "line-color" : $lineColor = $value; break;
                    case "line-color-data" : $lineColor_data = $value; break;
                    case "line-height" : $lineHeight = $value; break;
                    case "height" : $height = $value; break;
                    case "" : break;    
                    default :     
                        echo("GET $key = $value <br>");
                }
            }
        }
        
        
        
        
        $save = 0;
        if ($_POST[layoutSaveClose]) { $save = 1; $close = 1;}
        if ($_POST[layoutSave]) { $save = 1; $close = 0;}
        if ($save) {
            $layout = $_POST[layout];
            // show_array($layout);
            $saveId = $layout[id];
            
            $backStr = "";
            
            if ($layout[spacer_backgroundColor]) {
                $backgroundColor = $layout["spacer_backgroundColor"];
                $backStr .= "background-color:#".$backgroundColor.";";
                $myKey = "spacer_backgroundColor";
                $backgroundColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundColor_data) $backStr .= "background-color-data:".$backgroundColor_data.";";
            }
            if ($layout[spacer_backgroundRollColor]) {
                $backgroundRollColor = $layout["spacer_backgroundRollColor"];
                $backStr .= "background-roll-color:#".$backgroundRollColor.";";
                $myKey = "spacer_backgroundRollColor";
                $backgroundRollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundRollColor_data) $backStr .= "background-roll-color-data:".$backgroundRollColor_data.";";
            }
            
            $marginStr = "";
            if (!is_null($layout[spacer_height])) {
                $height = intval($layout[spacer_height]);
                $marginStr .= "height:".$height.";";
            }
            
            
            $borderStr = "";
            if ($layout[spacer_lineColor]) {
                $lineColor = $layout["spacer_lineColor"];
                $borderStr .= "line-color:#".$lineColor.";";
                $myKey = "spacer_lineColor";
                $lineColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($lineColor_data) $borderStr .= "line-color-data:".$lineColor_data.";";
            }
            
            if (!is_null($layout[spacer_lineHeight])) {
                $lineHeight = intval($layout[spacer_lineHeight]);
                $borderStr .= "line-height:".$lineHeight.";";
            }
                
            
            $saveData = array();
            if ($layout[id]) $saveData[id] = $layout[id];

            $saveData[type] = "spacer";
            $saveData[theme] = $theme;
            $saveData[name] = $spacerType;
            $saveData[id]   = $saveId;
            $saveData[background] = $backStr;
            $saveData[margin] = $marginStr;
            $saveData[border] = $borderStr;
              
              
            // show_array($saveData);
            $saveResult = cmsStyle_save($saveData);
            if ($saveResult) {
                if (!$saveId) {
                    $saveId = $saveResult;
                    $layoutData[id] = $saveId;                    
                }
                cms_infoBox("Spacer gespeichert!");
                if ($close) $goPage = cms_page_goPage("view=layoutTypes");
                else $goPage = cms_page_goPage("view=layoutTypes&spacer=$spacerType");
                reloadPage($goPage,1);            
            } else {
               cms_errorBox("Fehler beim Spacer speichern!"); 
            }
            
        }
        
        $goPage = cms_page_goPage("view=layoutTypes&spacer=$spacerType");
        echo ("<form method='post' action='$goPage' >");
        echo ("id=$layoutData[id]<input type='hidden' value='$layoutData[id]' name='layout[id]' /><br />");
        
        
        
        $table = "";
        $table .= "<table class='cmsEditTable' >";
        $table .= "<tr class='cmsEditTableLine cmsEditTableLine_head' >";
        $table .= "<td class='cmsEditTableColumnTitle' >Bereich</td>";
        $table .= "<td class='cmsEditTableColumn'>Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Höhe</td>";
        $table .= "<td class='cmsEditTableColumn'>Linie</td>";
//        $table .= "<td class='cmsEditTableColumn'>Oben</td>";
//        $table .= "<td class='cmsEditTableColumn'>Rechts</td>";
//        $table .= "<td class='cmsEditTableColumn'>Unten</td>";
//        $table .= "<td class='cmsEditTableColumn'>Links</td>";
//        $table .= "<td class='cmsEditTableColumn'>Stil</td>";
        $table .= "</tr>";

        $line = "";
        $line .= "<tr class='cmsEditTableLine' >";
        $line .= "<td class='cmsEditTableColumnTitle'>Abstand $spacerType</td>";
        $line .= "<td class='cmsEditTableColumn'>";
        $line .= $this->layoutInput("color",$backgroundColor,"spacer_backgroundColor",$backgroundColor_data);
        $line .= " roll: ";
        $line .= $this->layoutInput("color",$backgroundRollColor,"spacer_backgroundRollColor",$backgroundRollColor_data);
        $line .= "</td>";
        $line .= "<td class='cmsEditTableColumn'>";
        $line .= $this->layoutInput("value",$height,"spacer_height");
        $line .= "</td>";
        $line .= "<td class='cmsEditTableColumn'>";
        $line .= $this->layoutInput("value",$lineHeight,"spacer_lineHeight");
        $line .= $this->layoutInput("color",$lineColor,"spacer_lineColor",$lineColor_data);
        $line .= "</td>";
//        $line .= "<td class='cmsEditTableColumn'>-</td>";
//        $line .= "<td class='cmsEditTableColumn'>-</td>";
//        $line .= "<td class='cmsEditTableColumn'>-</td>";
//        $line .= "<td class='cmsEditTableColumn'>-</td>";
//        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "</tr>";
        
        $table .= $line;


       
        $table .= "</table>";
        echo ($table);
        echo ("<input type='submit' class='cmsInputButton' value='speichern' name='layoutSave' />");
        echo ("<input type='submit' class='cmsInputButton' value='speichern und schließen' name='layoutSaveClose' />");
        
        $goPage = cms_page_goPage("view=layoutTypes");
        echo ("<a href='$goPage' class='cmsLinkButton cmsSecond' >abbrechen</a>");
        
        echo ("</form>");

        div_end("cmsContentEditFrame");
        
      
        
        
        
        
        
    }
    
    function editLayout_frame($theme,$name,$layoutData,$frameWidth) {
        div_start("cmsContentEditFrame");
        
        if (is_array($layoutData)) {
            foreach ($layoutData as $key => $value) {
                $splitList = explode(";",$value);
                $cssList = array();
                for($i=0;$i<count($splitList);$i++) {
                    if ($splitList[$i]) {
                        list($key2,$val2) = explode(":",$splitList[$i]);
                        if ($key2 AND !is_null($val2)) $cssList[$key2] = $val2;                     
                    }
                }
                switch ($key) {
                    case "id": break;
                    case "type": break;
                    case "name": break;
                    case "background" :
                        $backgroundColor = $cssList["background-color"];
                        $backgroundColor_data = $cssList[backgroundColor_data];

                        $backgroundRollColor = $cssList["background-roll-color"];
                        $backgroundRollColor_data = $cssList[backgroundRollColor_data];
                        break;

                    case "margin" :
                        $marginStr = $cssList[margin];
                        $marginValue = $this->splitWidthStr($marginStr,"margin");
                       
                        $margin = $cssList["margin"];
                        $marginTop = $cssList["margin-top"];
                        $marginRight = $cssList["margin-right"];
                        $marginBottom = $cssList["margin-bottom"];
                        $marginLeft = $cssList["margin-left"];
                        // echo ("MARGIN '$marginStr' -> $margin ( $marginTop / $marginRight / $marginBottom / $marginLeft )<br/>");
                        break;
                    case "border" :
//                        foreach ($cssList as $cssKey => $cssValue) {
//                            echo ("$key $cssKey = $cssValue <br>");
//                        }
                        $borderStyle = $cssList["border-style"];
                        $borderColor = $cssList["border-color"];
                        $borderColor_data = $cssList["borderColor_data"];
                        $borderRollColor = $cssList["border-roll-color"];
                        $borderRollColor_data = $cssList["borderRollColor_data"];
                        $border = $cssList["border-width"];
                        $borderTop = $cssList["border-top-width"];
                        $borderRight = $cssList["border-right-width"];
                        $borderBottom = $cssList["border-bottom-width"];
                        $borderLeft = $cssList["border-left-width"];
                        // echo ("BORDER WIDTH '$borderStr' -> $border ( $borderTop $borderRight $borderBottom $borderLeft )<br/>");
                        break;
                    case "color" :
                        $color = $cssList[color];
                        $color_data = $cssList[color_data];
                        
                        $rollColor = $cssList[rollColor];
                        $rollColor_data = $cssList[rollColor_data];

                        // echo ("color $color / $color_data rollColor $rollColor,$rollColor_data <br />");
                        break;
                    case "radius" :
                        break;
                    case "padding" :
//                        foreach ($cssList as $cssKey => $cssValue) {
//                            echo ("$key $cssKey = $cssValue <br>");
//                        }
                        $padding = $cssList["padding"];
                        $paddingTop = $cssList["padding-top"];
                        $paddingRight = $cssList["padding-right"];
                        $paddingBottom = $cssList["padding-bottom"];
                        $paddingLeft = $cssList["padding-left"];


                        break;
                    case "font":
                        break;
                    case "data" :
                        break;



                    default :
                        echo ("<b>Unkown $key </b><br>");
                         foreach ($cssList as $cssKey => $cssValue) {
                            echo ("$key $cssKey = $cssValue <br>");
                        }
                }
            }
        }
        
       
        // GET HEADLINE DATA
        $headLine = $this->headline_get($theme,$name);
        
        
        $save = 0;
        if ($_POST[layoutSaveClose]) { $save = 1; $close = 1;}
        if ($_POST[layoutSave]) { $save = 1; $close = 0;}
        if ($save) {
            $layout = $_POST[layout];
            // show_array($layout);

            // margin
            $marginStr = "margin:".cmsLayout_getWidthStr($layout,"margin");
            $marginStr = $this->getWidthStr($layout,"margin");
            
            $margin = $layout[margin];
            $marginTop = $layout[marginTop];
            $marginRight = $layout[marginRight];
            $marginBottom = $layout[marginBottom];
            $marginLeft = $layout[marginLeft];
            // echo ("WidthStr margin : '$widthStr' <br>");

            // border
            $borderColor = $layout[borderColor];
            if ($borderColor) {
                $borderColorStr = "border-color:#$borderColor;";
            }
           
            $borderRollColor = $layout[borderRollColor];
            if ($borderRollColor) $borderRollColorStr = "border-roll-color:#$borderRollColor;";
            
            $borderStyle = $layout[borderStyle];
            if ($borderStyle) $borderStyleStr = "border-style:".$borderStyle.";";
            
            $borderStr = $this->getWidthStr($layout,"border");
            
            $border = $layout[border];
            $borderTop = $layout[borderTop];
            $borderRight = $layout[borderRight];
            $borderBottom = $layout[borderBottom];
            $borderLeft = $layout[borderLeft];
            // echo ("Border : '$borderStr' <br>");


            // radius
            $radiusStr = "border-radius:".cmsLayout_getWidthStr($layout,"radius");
            $radius = $layout[radius];
            $radiusTop = $layout[radiusTop];
            $radiusRight = $layout[radiusRight];
            $radiusBottom = $layout[radiusBottom];
            $radiusLeft = $layout[radiusLeft];
            // echo ("Radius : '$radiusStr' <br>");

            // padding
            $paddingStr = $this->getWidthStr($layout,"padding");
            $padding = $layout[padding];
            $paddingTop = $layout[paddingTop];
            $paddingRight = $layout[paddingRight];
            $paddingBottom = $layout[paddingBottom];
            $paddingLeft = $layout[paddingLeft];
            // echo ("Padding : '$paddingStr' <br>");

            $saveData = array();
            if ($layout[id]) $saveData[id] = $layout[id];

            $saveData[type] = "frame";
            $saveData[theme] = $theme;
            $saveData[name] = "$name";
            
            $saveStr = "";
            if ($layout[backgroundColor]) {
                $backgroundColor = $layout["backgroundColor"];
                $saveStr .= "background-color:#".$layout["backgroundColor"].";";
                $myKey = "backgroundColor";
                $backgroundColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundColor_data) $saveStr .= $myKey."_data:".$backgroundColor_data.";";
            }
            if ($layout[backgroundRollColor]) {
                $backgroundRollColor = $layout[backgroundRollColor];
                $saveStr .= "background-roll-color:#".$layout[backgroundRollColor].";";
                $myKey = "backgroundRollColor";
                $backgroundRollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($backgroundRollColor_data) $saveStr .= $myKey."_data:".$backgroundRollColor_data.";";
            }
            // echo ("<h1>$saveStr</h1>");
            $saveData[background] = $saveStr;

            // color
            $saveStr = "";
            if ($layout[color]) {
                $saveStr .= "color:#".$layout["color"].";";
                $myKey = "color";
                $color_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($color_data) $saveStr .= $myKey."_data:".$color_data.";";
                // echo ("<h1>Color $colorData</h1>");
            }

            if ($layout[rollColor]) {
                $saveStr .= "rollColor:#".$layout["rollColor"].";";
                $myKey = "rollColor";
                $rollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($rollColor_data) $saveStr .= $myKey."_data:".$rollColor_data.";";
                //  echo ("<h1>$key = $colorData</h1>");
            }
            $saveData[color] = $saveStr;

            $saveData[margin] = $marginStr;

            
            // border
            $saveStr = "";
            if ($borderStr) $saveStr .= $borderStr;
            if ($borderColorStr) {
                $saveStr .= $borderColorStr;
                $myKey = "borderColor";
                $borderColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($borderColor_data) $saveStr .= $myKey."_data:".$borderColor_data.";";               
            }
            if ($borderRollColor) {
                $saveStr .= $borderRollColorStr;
                $myKey = "borderRollColor";
                $borderRollColor_data = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                if ($borderRollColor_data) $saveStr .= $myKey."_data:".$borderRollColor_data.";";                
            }
            if ($borderStyle) $saveStr .= $borderStyleStr;
            $saveData[border] = $saveStr;
            
            
            $saveData[radius] = $radiusStr;

            $saveData[padding] = $paddingStr;

            $saveData[font] = "";

            $saveData[data] = "";
            
            
            // SAVE HEADLINE
            $headLine = $this->headline_save($theme,$layout,$name);
            

            $saveResult = cmsStyle_save($saveData);
            // show_array($saveData);
            
            cms_infoBox("Layout gespeichert!");
            if ($close) $goPage = cms_page_goPage("view=layoutTypes");
            else $goPage = cms_page_goPage("view=layoutTypes&layout=$name");
            
            
            // reloadPage($goPage,20);
            
            


        }
        
        echo ("<form method='post' action='$goPage' >");
        echo ("id=$layoutData[id]<input type='hidden' value='$layoutData[id]' name='layout[id]' /><br />");
        
         // background
        
        
       

        if ($backgroundColor) {
            $style .= "background-color:#$backgroundColor;";
        }

        if ($layoutData["margin"]) $style.=$layoutData[margin];
        
        if ($layoutData["border"]) $style.=$layoutData[border];
        // Abstand Aussen   
        

        // Rahmen    
      

            $style.="border-style:$borderStyle;";
            $style.="border-color:$borderColor;";
            if ($borderStr) $style.= $borderStr;

        if ($radiusStr) {
            $style .= $radiusStr;
        }


        if ($paddingStr) {
            $style .= $paddingStr;
        }



        $divData = array();
        $divData[style] = $style;


        // edititen
       
        $goPage = cms_page_goPage("view=layoutTypes&layout=$name");
        
        $table = "";
        $table .= "<table class='cmsEditTable' >";
        $table .= "<tr class='cmsEditTableLine cmsEditTableLine_head' >";
        $table .= "<td class='cmsEditTableColumnTitle' >Bereich</td>";
        $table .= "<td class='cmsEditTableColumn'>Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Rollover</td>";
        $table .= "<td class='cmsEditTableColumn'>Standard-Wert</td>";
        $table .= "<td class='cmsEditTableColumn'>Oben</td>";
        $table .= "<td class='cmsEditTableColumn'>Rechts</td>";
        $table .= "<td class='cmsEditTableColumn'>Unten</td>";
        $table .= "<td class='cmsEditTableColumn'>Links</td>";
        $table .= "<td class='cmsEditTableColumn'>Stil</td>";
        $table .= "</tr>";

        $line = "";
        $line .= "<tr class='cmsEditTableLine' >";
        $line .= "<td class='cmsEditTableColumnTitle'>Hintergrund</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$backgroundColor,"backgroundColor",$backgroundColor_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$backgroundRollColor,"backgroundRollColor",$backgroundRollColor_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "</tr>";
        
        $table .= $line;


        $line = "";
        $line .= "<tr class='cmsEditTableLine' >";
        $line .= "<td class='cmsEditTableColumnTitle'>Schriftfarbe</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$color,"color",$color_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$rollColor,"rollColor",$rollColor_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "</tr>";

        $table .= $line;

        // MARGIN
        $line = "";
        $line .= "<td class='cmsEditTableColumnTitle'>Außen-Abstand</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$margin,"margin")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginTop,"marginTop")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginRight,"marginRight")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginBottom,"marginBottom")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$marginLeft,"marginLeft")."</td>";
        $line .= "<td class='cmsEditTableColumn'></td>";
        $line .= "</tr>";
        $table .= $line;

        // Rahmen      
        $line = "";
        $line .= "<td  class='cmsEditTableColumnTitle'>Rahmen</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$borderColor,"borderColor",$borderColor_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$borderRollColor,"borderRollColor",$borderRollColor_data)."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$border,"border")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderTop,"borderTop")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderRight,"borderRight")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderBottom,"borderBottom")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$borderLeft,"borderLeft")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("dropdown",$borderStyle,"borderStyle","borderStyle")."</td>";        
        $line .= "</tr>";
        $table .= $line;


         // Rahmen
        $line = "";
        $line .= "<td  class='cmsEditTableColumnTitle'>Radius</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radius,"radius")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusTop,"radiusTop")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusRight,"radiusRight")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusBottom,"radiusBottom")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$radiusLeft,"radiusLeft")."</td>";
        $line .= "<td class='cmsEditTableColumn'></td>";
        $line .= "</tr>";
        $table .= $line;
        
        // Innenanstand
        $line = "";
        $line .= "<td  class='cmsEditTableColumnTitle'>Innerer Abstand</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>-</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$padding,"padding")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingTop,"paddingTop")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingRight,"paddingRight")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingBottom,"paddingBottom")."</td>";
        $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$paddingLeft,"paddingLeft")."</td>";
        $line .= "<td class='cmsEditTableColumn'></td>";
        $line .= "</tr>";
        $table .= $line;
        
        
        $headlineList = array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");
        
        foreach ($headlineList as $headKey => $headValue) {
            
            $headData = $headLine[$headKey];
            
            $headId                  = $headData[id];
            
            $headColor               = $headData[color];
            $headColor_data          = $headData[color_data];
            $headRollColor           = $headData[rollColor];
            $headRollColor_data      = $headData[rollColor_data];
            
            $headStyle               = $headData[style_data];
            // echo ("$headKey $headStyle <br>");
            
            $headBorderColor          = $headData[borderColor];
            $headBorderColor_data     = $headData[borderColor_data];
            $headBorderRollColor      = $headData[borderRollColor];
            $headBorderRollColor_data = $headData[borderRollColor_data];
            
            $headBorder              = $headData[border];
            $headBorderTop           = $headData[borderTop];
            $headBorderRight         = $headData[borderRight];
            $headBorderBottom        = $headData[borderBottom];
            $headBorderLeft          = $headData[borderLeft];
            
            $headBorderStyle         = $headData[borderStyle];
            
            
            // Schrift
            $line = "";            
            $line .= "<td class='cmsEditTableColumnTitle'>$headValue";
            $line .= "<input type='hidden___' value='$headId' name='layout[".$headKey."Id]' style='width:20px' >";
            $line .= "</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$headColor,$headKey."Color",$headColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$headRollColor,$headKey."RollColor",$headRollColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("fontStyle",$headStyle,$headKey."Style",$headStyle_data)."</td>";
            $line .= "</tr>";
            $table .= $line;
            
            $line = "";
            $line .= "<td class='cmsEditTableColumnTitle'>Rahmen $headValue</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$headBorderColor, $headKey."BorderColor",$headBorderColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("color",$headBorderRollColor, $headKey."BorderRollColor",$headBorderRollColor_data)."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$headBorder, $headKey."Border")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$headBorderTop, $headKey."BorderTop")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$headBorderRight, $headKey."BorderRight")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$headBorderBottom, $headKey."BorderBottom")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("width",$headBorderLeft, $headKey."BorderLeft")."</td>";
            $line .= "<td class='cmsEditTableColumn'>".$this->layoutInput("dropdown",$headBorderStyle, $headKey."BorderStyle","borderStyle")."</td>";    
            
            $line .= "</tr>";
            $table .= $line;
        }

        $table .= "</table>";
        echo ($table);
        echo ("<input type='submit' class='cmsInputButton' value='speichern' name='layoutSave' />");
        echo ("<input type='submit' class='cmsInputButton' value='speichern und schließen' name='layoutSaveClose' />");
        
        $goPage = cms_page_goPage("view=layoutTypes");
        echo ("<a href='$goPage' class='cmsLinkButton cmsSecond' >abbrechen</a>");
        
        echo ("</form>");

        div_end("cmsContentEditFrame");
        
        return $style;
        
    }
    
    function layoutInput($type,$value,$dataName=null,$data=null) {
        $str .= "";
        switch ($type) {
            case "color" :
                $str = $this->layoutInput_color($value,$dataName,$data);
                break;
            case "width" :
                $str = $this->layoutInput_width($value,$dataName);
                break;
            case "dropdown" :
                $str = $this->layoutInput_dropdown($value,$dataName,$data);
                break;

            case "fontStyle" :
                $str .= $this->layoutInput_fontStyle($value, $dataName, $data);
                break;
            
            case "value" :
                $str .= $this->layoutInput_value($value, $dataName, $data);
                break;
            
            default:
               $str .= "unkownType '$type'";
        }    
        return $str;

    }
    
    function layoutInput_value($value, $dataName, $data) {
          $str .= "<input id='$dataName' style='width:30px;' class='cmsEditLayoutValue' type='text' value='$value' name='layout[$dataName]' style='width:50px;' />";
          return $str;
    }
    
    function layoutInput_color($value,$dataName,$colorData) {
        if ($value[0] == "#") $value = substr($value,1);

        $color_blend = "100";
        $color_saturation = "0";
        $color_id = "";
        if ($colorData) {
            list($color_id,$color_blend,$color_saturation) = explode("|",$colorData);
        }
        $divName = "colorBox cmsEditSelectColor";
        switch ($color_id) {
            case "none" :
                $divName .= " colorNone";
                break;
            case "trans" :
                $divName .= " colorTransparent";
                break;
        } 

        
        // 12 c0eb37
        
        $divData = array();
        $divData[style] = "background-color:#$value;";
        $divData[id] = "drop_".$dataName;
        $str .= div_start_str($divName,$divData);
        $str .= "$color_saturation";
        $str .= div_end_str($divName);
        $editType = "hidden";
        
        $str .= "<input id='$dataName' class='cmsEditLayoutValue' readOnly='readOnly' type='$editType' value='$value' name='layout[$dataName]' style='width:50px;' />";
        $str .= "<input id='".$dataName."_colorId' readOnly='readOnly' class='' type='$editType' value='$color_id' name='layout[".$dataName."_colorId]' style='width:20px;' />";
        $str .= "<input id='".$dataName."_colorBlend' readOnly='readOnly' class='' type='$editType' value='$color_blend' name='layout[".$dataName."_colorBlend]' style='width:20px;' />";
        $str .= "<input id='".$dataName."_colorSaturation' readOnly='readOnly' class='' type='$editType' value='$color_saturation' name='layout[".$dataName."_colorSaturation]' style='width:20px;' />";
        return $str;
    }
    function layoutInput_width($value,$dataName) {
        $value = str_replace("px","",$value);
        
        
        $str .= "<input id='$dataName' class='cmsEditLayoutValue' type='text' value='$value' name='layout[$dataName]' style='width:20px;' />";
        return $str;
    }

    function layoutInput_dropdown($value,$dataName,$data) {
        switch ($data) {
            case "borderStyle" :
                $list = array();
                $list[none] = "Kein Rahmen";
                $list[solid] = "einfacher Rahmen";
                $list[double] = "doppelter Rahmen";
                $list[dashed] = "gestrichelter Rahmen";
                $list[dotted] = "gepunktete Rahmen";
                $list[groove] = "groove Rahmen";
                $list[ridge] = "ridge Rahmen";
                $list[inset] = "inset Rahmen";
                $list[outset] = "outset Rahmen";
                break;
            default :
                $str .= "unkownType $data";
        }
        
        
        if (is_array($list)) {
            $str .= "<select id='$dataName' class='cmsEditLayoutValue' type='text' value='$value' name='layout[$dataName]'  >";
            foreach ($list as $key => $name ) {
                $selected="";
                if ($key == $value) $selected="selected='selected'";
                $str .= "<option value='$key' $selected>$name</option>";
            }
            $str .= "</select>";
                
            
        }
        return $str;
    }
    
    function layoutInput_fontStyle($value, $dataName, $data) {
        
        // echo ("STYLE ='$value' ");
        list($size,$bold,$kursiv,$underline) = explode("|",$value);
        // echo ("size='$size' bold='$bold' kursiv='$kursiv' underline='$underline' <br> ");
        $str = "";
        
        $fontSizeList = array("8","9","10","11","12","14","16","18","20","24","30","36","40","48");
        $str .= "<select name='layout[".$dataName."_size]' id='".$dataName."_size' class='cmsEditLayoutValue' >";
        
        if ($size) $selected = "";
        else $selected = "selected='selected'";
        $str .= "<option $selected value='0' >nicht</option>";
        
        for ($i=0;$i<count($fontSizeList);$i++) {
            $si = $fontSizeList[$i];
            if ($si == $size) $selected = "selected='selected'";
            else $selected = "";
            $str .= "<option $selected value='$si' >$si Punkt</option>";
        }
        $str .= "</select>";
        
        
        
        $showType = "hidden";
        
        $className = "cmsEditCheckBox cmsEditHeadLine checkBox_bold";
        if ($bold) {
           $className .= " cmsEditCheckBox_active";
           $value = "1";
        } else $value = "0";
        
        $str .= "<div id='checkbox_".$dataName."_bold' class='$className' >";
        $str.="F";
        $str.="<input id='".$dataName."_bold' class='cmsEditLayoutValue' readOnly='readOnly' type='$showType' value='$value' name='layout[".$dataName."_bold]' style='width:10px;' />";
        $str.="</div>";
        
        
        $className = "cmsEditCheckBox cmsEditHeadLine checkBox_kursiv";
        if ($kursiv) {
           $className .= " cmsEditCheckBox_active";
           $value = "1";
        } else $value = "0";        
        $str .= "<div id='checkbox_".$dataName."_kursiv' class='$className' >";
        $str.="K";
        $str.="<input id='".$dataName."_kursiv'' class='cmsEditLayoutValue' readOnly='readOnly' type='$showType' value='$value' name='layout[".$dataName."_kursiv]' style='width:10px;' />";
        $str.="</div>";
        
        $className = "cmsEditCheckBox cmsEditHeadLine checkBox_underline";
        if ($underline) {
           $className .= " cmsEditCheckBox_active";
           $value = "1";
        } else $value = "0";
        $str .= "<div id='checkbox_".$dataName."_underline'class='$className' >";
        $str .= "U";
        $str .= "<input id='".$dataName."_underline'' class='cmsEditLayoutValue' readOnly='readOnly' type='$showType' value='$value' name='layout[".$dataName."_underline]' style='width:10px;' />";
        $str .= "</div>";
        
        return $str;
     
        $str .= "<input id='$dataName' class='cmsEditLayoutValue' readOnly='readOnly' type='$editType' value='$value' name='layout[$dataName]' style='width:50px;' />";
        $str .= "<br /><input id='".$dataName."_colorId' readOnly='readOnly' class='' type='$editType' value='$color_id' name='layout[".$dataName."_colorId]' style='width:20px;' />";
        $str .= "<input id='".$dataName."_colorBlend' readOnly='readOnly' class='' type='$editType' value='$color_blend' name='layout[".$dataName."_colorBlend]' style='width:20px;' />";
        $str .= "<input id='".$dataName."_colorSaturation' readOnly='readOnly' class='' type='$editType' value='$color_saturation' name='layout[".$dataName."_colorSaturation]' style='width:20px;' />";
        
    }
    
    function getWidthStr($layout,$type) {
        

        // value
        $value = $layout[$type];
        if (!is_null($value)) {
            if ($value == "0") $valueUse = "0";
            else {
                if (intval($value)) $valueUse = intval($value)."px";
            }            
        } else {
            $valueUse = "0";
        }

        // Top
        $valueTop = $layout[$type."Top"];
        if (!is_null($valueTop)) {
            if (intval($valueTop) OR $valueTop=="0") $valueTopUse = intval($valueTop);
        }

        // Right
        $valueRight = $layout[$type."Right"];
        if (!is_null($valueRight)) {
            if (intval($valueRight) OR $valueRight=="0") $valueRightUse = intval($valueRight);
        }
        // Bottom
        $valueBottom = $layout[$type."Bottom"];
        if (!is_null($valueBottom)) {
            if (intval($valueBottom) OR $valueBottom=="0") $valueBottomUse = intval($valueBottom);
        }
        // Left
        $valueLeft = $layout[$type."Left"];
        if (!is_null($valueLeft)) {
            if (intval($valueLeft) OR $valueLeft=="0") $valueLeftUse = intval($valueLeft);
        }
        
        
        switch ($type) {
            case "border" :
                $str = "";
                $str .= "border-width:$valueUse;";
                if (!is_null($valueTopUse)) {
                    $str .= "border-top-width:$valueTopUse";
                    if ($valueTopUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueRightUse)) {
                    $str .= "border-right-width:$valueRightUse";
                    if ($valueRightUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueBottomUse)) {
                    $str .= "border-bottom-width:$valueBottomUse";
                    if ($valueBottomUse) $str.="px";
                    $str .= ";";
                }
             
                if (!is_null($valueLeftUse)) {
                    $str .= "border-left-width:$valueLeftUse";
                    if ($valueLeftUse) $str.="px";
                    $str .= ";";
                }
                //echo ("<h3>BORDER-WIDTH:$str</h3>");
                return $str;
                break;
               
             
                
            case "margin" :
                $str = "";
                $str .= "margin:$valueUse;";
                if (!is_null($valueTopUse)) {
                    $str .= "margin-top:$valueTopUse";
                    if ($valueTopUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueRightUse)) {
                    $str .= "margin-right:$valueRightUse";
                    if ($valueRightUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueBottomUse)) {
                    $str .= "margin-bottom:$valueBottomUse";
                    if ($valueBottomUse) $str.="px";
                    $str .= ";";
                }
             
                if (!is_null($valueLeftUse)) {
                    $str .= "margin-left:$valueLeftUse";
                    if ($valueLeftUse) $str.="px";
                    $str .= ";";
                }
                //echo ("<h3>MARGIN -> $str</h3>");
                return $str;
                break;

            case "padding" :
                $str = "";
                $str .= "padding:$valueUse;";
                if (!is_null($valueTopUse)) {
                    $str .= "padding-top:$valueTopUse";
                    if ($valueTopUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueRightUse)) {
                    $str .= "padding-right:$valueRightUse";
                    if ($valueRightUse) $str.="px";
                    $str .= ";";
                }
                if (!is_null($valueBottomUse)) {
                    $str .= "padding-bottom:$valueBottomUse";
                    if ($valueBottomUse) $str.="px";
                    $str .= ";";
                }

                if (!is_null($valueLeftUse)) {
                    $str .= "padding-left:$valueLeftUse";
                    if ($valueLeftUse) $str.="px";
                    $str .= ";";
                }
                //echo ("<h3>padding -> $str</h3>");
                return $str;
                break;
                
            default :
                $style = 0;
                if ($type== "h1Border") $style = 1;
                if ($type== "h2Border") $style = 1;
                if ($type== "h3Border") $style = 1;
                if ($type== "h4Border") $style = 1;
                
                if ($style) {
                   
                    $str = "";
                    if (!$valueUse) $valueUse="0";
                    else $valueUse = intval($valueUse)."px";
                    $str .= "border-width:$valueUse;";
                    if (!is_null($valueTopUse)) {
                        $str .= "border-top-width:$valueTopUse";
                        if ($valueTopUse) $str.="px";
                        $str .= ";";
                    }
                    if (!is_null($valueRightUse)) {
                        $str .= "border-right-width:$valueRightUse";
                        if ($valueRightUse) $str.="px";
                        $str .= ";";
                    }
                    if (!is_null($valueBottomUse)) {
                        $str .= "border-bottom-width:$valueBottomUse";
                        if ($valueBottomUse) $str.="px";
                        $str .= ";";
                    }

                    if (!is_null($valueLeftUse)) {
                        $str .= "border-left-width:$valueLeftUse";
                        if ($valueLeftUse) $str.="px";
                        $str .= ";";
                    }
                    //echo ("<h3>BORDER-WIDTH:$str</h3>");
                    return $str;
               
                }
                
                
        }
        

        if (!is_null($valueLeftUse) OR !is_null($valueRightUse) OR !is_null($valueTopUse) or !is_null($valueBottomUse)) {
            $valueStr = "";

           
            // add Top
            if (is_null($valueTopUse)) $valueStr.=$valueUse;
            else {
                if ($valueTopUse) $valueStr.=$valueTopUse."px";
                else $valueStr .= "0";
            }

            // add Right
            if (is_null($valueRightUse)) $valueStr.=" ".$valueUse;
            else {
                if ($valueRightUse) $valueStr.=" ".$valueRightUse."px";
                else $valueStr .= " 0";
            }

             // add Bottom
            if (is_null($valueBottomUse)) $valueStr.=" ".$valueUse;
            else {
                if ($valueBottomUse) $valueStr.=" ".$valueBottomUse."px";
                else $valueStr .= " 0";
            }

            // add Left
            if (is_null($valueLeftUse)) $valueStr.=" ".$valueUse;
            else {
                if ($valueLeftUse) $valueStr.=" ".$valueLeftUse."px";
                else $valueStr .= " 0";
            }


            $valueStr.= ";";           
        } else {
            $valueStr = $valueUse.";";
        }
        return $valueStr;

    }
    
    function headline_get($theme,$frameName) {
        $headLine = array();
        
        $headList = array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");
        
        foreach ($headList as $headKey => $name) {
            $filter = array();
            $filter[type] = "headline";
            $filter[theme] = $theme;
            $filter[name] = $frameName."_".$headKey;
            $out = "css";
            $headData = cmsStyle_get($filter, $out);
            
            
            $headLine[$headKey] = array();
            
            // echo ("<h2>GET HEADLINE DATA $headKey for $frameName </h2>");
            if (is_array($headData)) {
                
                $headLine[$headKey][id] = $headData[id];
                $headLine[$headKey][color] = $headData[color];
                $headLine[$headKey][color_data] = $headData[color_data];
                
                $headLine[$headKey][rollColor] = $headData[rollColor];
                $headLine[$headKey][rollColor_data] = $headData[rollColor_data];
                
                $headLine[$headKey][borderColor] = $headData[borderColor];
                $headLine[$headKey][borderColor_data] = $headData[borderColor_data];
                
                $headLine[$headKey][borderRollColor] = $headData[borderRollColor];
                $headLine[$headKey][borderRollColor_data] = $headData[borderRollColor_data];
                
                $headLine[$headKey][border] = $headData["border-width"];
                $headLine[$headKey][borderTop] = $headData["border-top-width"];
                $headLine[$headKey][borderRight] = $headData["border-right-width"];
                $headLine[$headKey][borderBottom] = $headData["border-bottom-width"];
                $headLine[$headKey][borderLeft] = $headData["border-left-width"];
                
                $headLine[$headKey][borderStyle] = $headData["border-style"];
                
                
                $headLine[$headKey][style_data] = $headData[font];
                
//                foreach($headData as $key => $value) {
//                    echo ("$key = $value <br>");
//                    
//                }
            } else {
                // echo "NO Data $headData !<br/>";
            }
            
//            foreach ($headLine[$headKey] as $key => $value) {
//                echo " GET $key = $value <br> ";
//            }
            
            
        }
        return $headLine;
        
        
    }
    
    function headline_save($theme,$layout,$frameName) {
        $headLine = array();
        foreach($layout as $key => $value) {
            $headKey = "";
            if (substr($key,0,2) == "h1") $headKey = "h1";
            if (substr($key,0,2) == "h2") $headKey = "h2";
            if (substr($key,0,2) == "h3") $headKey = "h3";
            if (substr($key,0,2) == "h4") $headKey = "h4";

            if ($headKey) {
                if (!is_array($headLine)) $headLine[$headKey] = array();
                $key = substr($key,2);

                switch ($key) {
                    case "Id" : $headLine[$headKey][id] = $value;
                    case "Color" : 
                        $headColor = $value; 
                        if ($headColor) {
                            $headLine[$headKey][color] = $headColor;
                            $myKey = $headKey.$key;
                            $headColorData = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                            $headLine[$headKey][color_data] = $headColorData;
                        }
                        break;
                    case "RollColor" : 
                        $headColor = $value; 
                        if ($headColor) {
                            $headLine[$headKey][rollColor] = $headColor;
                            $myKey = $headKey.$key;
                            $headColorData = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                            if ($headColorData) $headLine[$headKey][rollColor_data] = $headColorData;
                        }
                        break;

                    case "BorderColor" : 
                        $headColor = $value; 
                        if ($headColor) {
                            $headLine[$headKey][borderColor] = $headColor;
                            $myKey = $headKey.$key;
                            $headColorData = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                            if ($headColorData) $headLine[$headKey][borderColor_data] = $headColorData;
                        }
                        break;
                    case "BorderRollColor" : 
                        $headColor = $value; 
                        if ($headColor) {
                            $headLine[$headKey][borderRollColor] = $headColor;
                            $myKey = $headKey.$key;
                            $headColorData = $layout[$myKey."_colorId"]."|".$layout[$myKey."_colorBlend"]."|".$layout[$myKey."_colorSaturation"];
                            if ($headColorData) $headLine[$headKey][borderRollColor_data] = $headColorData;
                        }
                        break;   

                    case "Style_bold" :
                        $myKey = $headKey."Style";
                        // echo ("STYLE $myKey ");
                        $headSize = $layout[$myKey."_size"];
                        $headBold = $layout[$myKey."_bold"];
                        $headKursiv = $layout[$myKey."_kursiv"];
                        $headUnderLine = $layout[$myKey."_underline"];

                        $headStyle = $headSize."|".$headBold."|".$headKursiv."|$headUnderLine";
                        $headLine[$headKey][style_data] = $headStyle;
                        break;

                    case "Border" :
                        $myKey = $headKey."Border";
                        $borderStr = $this->getWidthStr($layout,$myKey);
                        // echo ("<h1>FRAME $headKey STR = '$borderStr </h1>");
                        // $headLine[$headKey][style_data] = $headStyle;
                        
                        $headLine[$headKey][borderStr] = $borderStr;
                        $headLine[$headKey][border] = $layout[$headKey."Border"];
                        $headLine[$headKey][borderTop] = $layout[$headKey."BorderTop"];
                        $headLine[$headKey][borderRight] = $layout[$headKey."BorderRight"];
                        $headLine[$headKey][borderBottom] = $layout[$headKey."BorderBottom"];
                        $headLine[$headKey][borderLeft] = $layout[$headKey."BorderLeft"];
                        break;
                    
                    case "BorderStyle" :
                        $headLine[$headKey][borderStyle] = $value;
                        break;

                    case "BorderTop" : break;
                    case "BorderRight" : break;
                    case "BorderBottom" : break;
                    case "BorderLeft" : break;

                    case "Style_size" : break;
                    case "Style_kursiv" : break;
                    case "Style_underline" : break;


                    case "Color_colorId" : break;
                    case "Color_colorBlend" : break;
                    case "Color_colorSaturation" : break;



                    case "RollColor_colorId" : break;
                    case "RollColor_colorBlend" : break;
                    case "RollColor_colorSaturation" : break;

                    case "BorderColor_colorId" : break;
                    case "BorderColor_colorBlend" : break;
                    case "BorderColor_colorSaturation" : break;

                    case "BorderRollColor_colorId" : break;
                    case "BorderRollColor_colorBlend" : break;
                    case "BorderRollColor_colorSaturation" : break;

                    default : 
                        //  echo ("SAVE $headKey $key = $value<br>");

                }









            }
        }
        
        
        

        //  echo ("<h1>HEADLINE DATA for $frameName </h1>");
        foreach($headLine as $headKey => $value) {
            $headLineId = $value[id];
            
            // echo ("<h3>$headKey id='$headLineId'</h3>");
            
            
            
            $saveData = array();
            $saveData[id] = $headLineId;
            $saveData[type] = "headline";
            $saveData[theme] = $theme;
            $saveData[name] = $frameName."_".$headKey;
            
            $saveData[background] = "";
            
            $saveStr = "";
            if ($value[color]) {
                $saveStr.="color:".$value[color].";";
                if ($value[color_data]) $saveStr .= "color_data:".$value[color_data].";";
            }
            if ($value[rollColor]) {
                $saveStr.="rollColor:".$value[rollColor].";";
                if ($value[rollColor_data]) $saveStr .= "rollColor_data:".$value[rollColor_data].";";
            }
            $saveData[color] = $saveStr;
            
            $saveData[margin] = "";
            
            $saveStr = "";
            if ($value[borderStr]) {
                $saveStr .= $value[borderStr];                
            }
            if ($value[borderColor]) {
                $saveStr.="borderColor:".$value[borderColor].";";
                if ($value[borderColor_data]) $saveStr .= "borderColor_data:".$value[borderColor_data].";";
            }
            if ($value[borderRollColor]) {
                $saveStr.="borderRollColor:".$value[borderRollColor].";";
                if ($value[borderRollColor_data]) $saveStr .= "borderRollColor_data:".$value[borderRollColor_data].";";
            }
            
            if ($value["borderStyle"]) {
                $saveStr .= "border-style:".$value[borderStyle].";";
            }
            
            $saveData[border] = $saveStr;
            $saveData[radius] = "";
            $saveData[padding] = "";
           
            $saveStr = "";
            if ($value[style_data]) {
                $saveStr .= "font:".$value[style_data];
            }
            $saveData[font] = $saveStr;
            $saveData[data] = "";
                
            $saveResult = cmsStyle_save($saveData);
            if ($saveResult) {
                if (!$headLineId) {
                    cms_infoBox("Neue Headline gespeichert id = $saveResult ");
                    $headLine[$headKey][id] = $saveResult;
                }
            }
            
            foreach ($saveData as $key => $val ) {
                // echo ("SaveHeadline $headKey $key = $val <br/>");
            }
                
            
                      
//            foreach($value as $key => $val) {
//                echo ("$key => $val <br>");
//            }
        }
        return $headLine;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /// TEXT EDIT                                                            ///
    ////////////////////////////////////////////////////////////////////////////
    
    function text_showEdit($theme,$editType,$textData) {
        $table = "";
        $table .= "<table class='cmsEditTable' >";

        // Tabellen Start Zeile
        $table .= "<tr class='cmsEditTableLine cmsEditTableLine_head' >";
        $table .= "<td class='cmsEditTableColumnTitle' >TEXT</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift-Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift over</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift-Schatten</td>";
        
//        $table .= "<td class='cmsEditTableColumn'>Innerer-Abstand</td>";
//        $table .= "<td class='cmsEditTableColumn'>Schrift-Farbe</td>";
//        $table .= "<td class='cmsEditTableColumn'>Rahmen-Farbe</td>";
//        $table .= "<td class='cmsEditTableColumn'>Schrift-Farbe</td>";
//        $table .= "<td class='cmsEditTableColumn'>Schrift</td>";
//        $table .= "<td class='cmsEditTableColumn'>Schrift over</td>";
//        $table .= "<td class='cmsEditTableColumn'>Radius</td>";
//        $table .= "<td class='cmsEditTableColumn'>Schatten</td>";
//        $table .= "<td class='cmsEditTableColumn'></td>";
        $table .= "</tr>";
        $text_list = $this->edit_textList($editType);
        foreach ($text_list as $key => $name) {
            $data = $textData[$key];
            $textId = $data[id];
            //  echo ("Show Text $key $data '$textId' <br />");
            // show_array($data);
            $backColor = $data["background-color"];
            $backColor_data = $data["background-color-data"];

            $backRollColor = $data["background-roll-color"];
            $backRollColor_data = $data["background-roll-color-data"];
            
            // echo ("back $backColor / $backColor_data ROLL = $backRollColor / $backRollColor_data <br / >");
            

            $borderColor = $data["border-color"];
            $borderColor_data = $data["border-color-data"];

            $borderRollColor = $data["border-roll-color"];
            $borderRollColor_data = $data["border-roll-color-data"];

            $fontColor = $data["font-color"];
            $fontColor_data = $data["font-color-data"];

            $fontRollColor = $data["font-roll-color"];
            $fontRollColor_data = $data["font-roll-color-data"];

            $font = $data["font-data"]; //"12|0|0|1";
            $fontRoll = $data["font-roll-data"]; //"12|0|0|1";
            
            $borderRadius = $data["border-radius"];
            $shadowLeft = $data["shadow-left"];
            $shadowRight = $data["shadow-right"]; //"6";
            $shadowColor = $data["shadow-color"]; //"#00f";
            $shadowColor_data = $data["shadow-color-data"]; //"";

            $textName ="text_".$key."_";

            $line = "";
            $line .= "<td  class='cmsEditTableColumnTitle'>Text '$name'";
            $line .= "<input type='hidden__' style='width:30px;' readonly='readonly' name='layout[".$textName."id]' value='$textId' >";
            $line .= "</td>";
            
            // Schrift Farbe
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("color",$fontColor,$textName."fontColor",$fontColor_data);
            $line .= " / ";
            $line .= $this->layoutInput("color",$fontRollColor,$textName."fontRollColor",$fontRollColor_data);
            $line .= "</td>";
            
            // Schrift
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("fontStyle", $font, $textName."font", $font_data);
            $line .= "</td>";
            
            // SchriftOver
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("fontStyle", $fontRoll, $textName."fontRoll", $fontRoll_data);
            $line .= "</td>";
            
            
            // Schatten
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("value", $shadowLeft,$textName."shadowLeft");
            $line .= $this->layoutInput("value", $shadowRight,$textName."shadowRight");
            $line .= $this->layoutInput("color",$shadowColor,$textName."shadowColor",$shadowColor_data);
            $line .= "</td>";
            
//            //Hintergrund
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("color",$backColor,$textName."backColor",$backColor_data);
//            $line .= " over: ";
//            $line .= $this->layoutInput("color",$backRollColor,$textName."backRollColor",$backRollColor_data);
//            $line .= "</td>";
//            // Rahmen Farbe
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("color",$borderColor,$textName."borderColor",$borderColor_data);
//            $line .= " over: ";
//            $line .= $this->layoutInput("color",$borderRollColor,$textName."borderRollColor",$borderRollColor_data);
//            $line .= "</td>";
//            // Schrift Farbe
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("color",$fontColor,$textName."fontColor",$fontColor_data);
//            $line .= " / ";
//            $line .= $this->layoutInput("color",$fontRollColor,$textName."fontRollColor",$fontRollColor_data);
//            $line .= "</td>";
//
//            // Schrift
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("fontStyle", $font, $textName."font", $font_data);
//            $line .= "</td>";
//            
//            // SchriftOver
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("fontStyle", $fontRoll, $textName."fontRoll", $fontRoll_data);
//            $line .= "</td>";
//
//            // Radius
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("value", $borderRadius,$textName."borderRadius");
//            $line .= "</td>";
//
//            // Schatten
//            $line .= "<td class='cmsEditTableColumn'>";
//            $line .= $this->layoutInput("value", $shadowLeft,$textName."shadowLeft");
//            $line .= $this->layoutInput("value", $shadowRight,$textName."shadowRight");
//            $line .= $this->layoutInput("color",$shadowColor,$textName."shadowColor",$shadowColor_data);
//            $line .= "</td>";
//            $line .= "<td class='cmsEditTableColumn'></td>";
            
            $line .= "</tr>";
            $table .= $line;
        }
        $table .= "</table>";
        echo ($table);
    }
    
    function text_get($theme,$editType) {
        $out = 0;
        if ($out) echo ("<h1> Text Get $theme / $editType </h1>");
        $textList = $this->edit_textList($editType);
        $textData = array();
        foreach ($textList as $textKey => $textName) {
            if ($out) echo ("<h2>Get TEXT $textKey => ".$editType.$textKey." </h2>");
            $textData[$textKey] = array();
            $filter = array("theme"=>$theme,"name"=>$editType.$textKey,"type"=>"text");
            $data = cmsStyle_get($filter,"css");
            if (is_array($data)) {
                $id = $data[id];
                if ($out) echo ("GET $data for $textKey $typeName $id <br />");
                $textData[$textKey] = $data;
            } else {
                if ($out) echo ("No Data '$data' ".$editType.$textKey." <br>");
            }
        }
        return $textData;        
    }
    
    function text_save($theme,$editType,$layout,$textData=array()) {
        // echo ("<h1>text_save( $theme,$editType,$saveData)</h1>");
        $out = 1;
        $textList = $this->edit_textList($editType);
        foreach ($textList as $textKey => $textName) {
            $saveId = 0;
            // echo ("<h2>Save Text $textKey = $textName </h2>");

            $start = "text_".$textKey."_";
            
            
            $saveId   = $layout[$start."id"];
            $saveName = $editType.$textKey;
            
            echo ("<h3>SAVE start='$start' id=$saveId name=$saveName </h3>");

            $saveType = "text";

            $saveBack = "";
            $saveBorder = "";
            $saveColor  = "";
            $saveMargin = "";
            $saveRadius = "";
            $savePadding = "";
            $saveFont = "";
            $saveData = "";
            

            // Background
            $backColor = $layout[$start."backColor"];
            if ($backColor) {
                $saveBack .= "background-color:#".$backColor.";";
                $textData[$textKey]["background-color"] = $backColor;
                $backData = $layout[$start."backColor_colorId"]."|".$layout[$start."backColor_colorBlend"]."|".$layout[$start."backColor_colorSaturation"];
                $saveBack .= "background-color-data:".$backData.";";
                $textData[$textKey]["background-color-data"] = $backData;
                if ($out) echo ("Background = $backColor // $backData <br>");
            }

            $backRollColor = $layout[$start."backRollColor"];
            if ($backRollColor) {
                $saveBack .= "background-roll-color:#".$backRollColor.";";
                $textData[$textKey]["background-roll-color"] = $backRollColor;
                $backData = $layout[$start."backRollColor_colorId"]."|".$layout[$start."backRollColor_colorBlend"]."|".$layout[$start."backRollColor_colorSaturation"];
                $saveBack .= "background-roll-color-data:".$backData.";";
                $textData[$textKey]["background-roll-color-data"] = $backData;
                if ($out) echo ("BackgroundRoll = $backRollColor // $backData <br>");
            }
            // Shadow
            $shadowColor = $layout[$start."shadowColor"];
            if ($shadowColor) {
                $saveBack .= "shadow-color:#".$shadowColor.";";
                $textData[$textKey]["shadow-color"] = $shadowColor;
                $shadowData = $layout[$start."shadowColor_colorId"]."|".$layout[$start."shadowColor_colorBlend"]."|".$layout[$start."shadowColor_colorSaturation"];
                $saveBack .= "shadow-color-data:".$shadowData.";";
                $textData[$textKey]["shadow-color-data"] = $shadowData;
                if ($out) echo ("Shadow = $shadowColor // $shadowData <br>");
            }
            $shadowLeft   = $layout[$start."shadowLeft"];
            if ($shadowLeft) $saveBack .="shadow-left:$shadowLeft;";
            $textData[$textKey]["shadow-left"] = $shadowLeft;
            
            $shadowRight  = $layout[$start."shadowRight"];
            if ($shadowRight) $saveBack .="shadow-right:$shadowRight;";
            $textData[$textKey]["shadow-right"] = $shadowRight;
           
            // Border
            $borderColor = $layout[$start."borderColor"];
            if ($borderColor) {
                $saveBorder .= "border-color:#".$borderColor.";";
                $textData[$textKey]["border-color"] = $borderColor;
                $borderData = $layout[$start."borderColor_colorId"]."|".$layout[$start."borderColor_colorBlend"]."|".$layout[$start."borderColor_colorSaturation"];
                $saveBorder .= "border-color-data:".$borderData.";";
                $textData[$textKey]["border-color-data"] = $borderData;
                if ($out) echo ("BORDER = $borderColor // $borderData <br>");
            }

            $borderRollColor = $layout[$start."borderRollColor"];
            if ($borderRollColor) {
                $saveBorder .= "border-roll-color:#".$borderRollColor.";";
                $textData[$textKey]["border-roll-color"] = $borderRollColor;
                $borderData = $layout[$start."borderRollColor_colorId"]."|".$layout[$start."borderRollColor_colorBlend"]."|".$layout[$start."borderRollColor_colorSaturation"];
                $saveBorder .= "border-roll-color-data:".$borderData.";";
                $textData[$textKey]["border-roll-color-data"] = $borderData;
                if ($out) echo ("BORDER-ROLL = $borderRollColor // $borderData <br>");
            }
            
            $borderRadius = $layout[$start."borderRadius"];
            if ($borderRadius) $saveBorder .= "border-radius:".$borderRadius.";";
            $textData[$textKey]["border-radius"] = $borderRadius;
            
            // FONT
            $fontColor = $layout[$start."fontColor"];
            if ($fontColor) {
                $saveFont .= "font-color:#".$fontColor.";";
                $textData[$textKey]["font-color"] = $fontColor;
                $fontData = $layout[$start."fontColor_colorId"]."|".$layout[$start."fontColor_colorBlend"]."|".$layout[$start."fontColor_colorSaturation"];                
                $saveFont .= "font-color-data:".$fontData.";";
                $textData[$textKey]["font-color-data"] = $fontData;
                if ($out) echo ("FONT = $fontColor // $fontData <br>");
            }

            $fontRollColor = $layout[$start."fontRollColor"];
            if ($fontRollColor) {
                $saveFont .= "font-roll-color:#".$fontRollColor.";";
                $textData[$textKey]["font-roll-color"] = $fontRollColor;
                $fontData = $layout[$start."fontRollColor_colorId"]."|".$layout[$start."fontRollColor_colorBlend"]."|".$layout[$start."fontRollColor_colorSaturation"];
                $saveFont .= "font-roll-color-data:".$fontData.";";
                $textData[$textKey]["font-roll-color-data"] = $fontData;
                if ($out) echo ("FONT-ROLL = $fontRollColor // $fontData <br>");
            }

            $fontStr = $layout[$start."font_size"]."|".$layout[$start."font_bold"]."|".$layout[$start."font_kursiv"]."|".$layout[$start."font_underline"];
            if ($fontStr) {
                $saveFont .= "font-data:$fontStr;";
                $textData[$textKey]["font-data"] = $fontStr;
                if ($out) echo ("FONT '$fontStr' <br>");
            }

            
            $fontStr = $layout[$start."fontRoll_size"]."|".$layout[$start."fontRoll_bold"]."|".$layout[$start."fontRoll_kursiv"]."|".$layout[$start."fontRoll_underline"];
            if ($fontStr) {
                $saveFont .= "font-roll-data:$fontStr;";
                $textData[$textKey]["font-roll-data"] = $fontStr;
                if ($out) echo ("FONT-Roll '$fontStr' <br>");
            }

            
          
            
            $saveData = array();
            $saveData[id]         = $saveId;
            $saveData[theme]      = $theme;
            $saveData[type]       = $saveType;
            $saveData[name]       = $saveName;
            $saveData[background] = $saveBack; 
            $saveData[margin]     = $saveMargin;
            $saveData[border]     = $saveBorder;
            $saveData[padding]    = $savePadding;
            $saveData[font]       = $saveFont;
            // show_array($saveData);
            $saveResult = cmsStyle_save($saveData);
            if ($saveResult) {
                cms_infoBox("Text gespeichert $saveResult");
                if (!$saveId) {
                    $saveId = $saveResult;
                    if ($out) echo ("neue ID is $saveId <br>");
                    $textData[$textKey][id] = $saveId;
                }
            } else {
                cms_errorBox("Text nicht gespeichert $saveResult");
            }
           //  echo ("SAVE $saveId <br>");

        }
        
        
        foreach ($layout as $key => $value ) {
            if (substr($key,0,7) == "text_") {
                $key = substr($key,7);
                $typeOff = strpos($key,"_");
                if ($typeOff) {
                    $textType = substr($key,0,$typeOff);
                    $key = substr($key,$typeOff+1);
                   //  echo ("Save text '$textType' '$key'  $value <br/>");
                }
            }
        }
        return $textData;
    }

    
    
    ////////////////////////////////////////////////////////////////////////////
    /// BUTTON EDIT                                                          ///
    ////////////////////////////////////////////////////////////////////////////

    function button_showEdit($theme,$editType,$buttonData) {
        $table = "";
        $table .= "<table class='cmsEditTable' >";

        // Tabellen Start Zeile
        $table .= "<tr class='cmsEditTableLine cmsEditTableLine_head' >";
        $table .= "<td class='cmsEditTableColumnTitle' >Buttons</td>";
        $table .= "<td class='cmsEditTableColumn'>Hintergrund-Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Rahmen-Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift-Farbe</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift</td>";
        $table .= "<td class='cmsEditTableColumn'>Schrift over</td>";
        $table .= "<td class='cmsEditTableColumn'>Radius</td>";
        $table .= "<td class='cmsEditTableColumn'>Schatten</td>";
        $table .= "<td class='cmsEditTableColumn'></td>";
        $table .= "</tr>";
        $button_list = $this->edit_buttonList($editType);
        foreach ($button_list as $key => $name) {
            $data = $buttonData[$key];
            $buttonId = $data[id];
            //  echo ("Show Button $key $data '$buttonId' <br />");
            // show_array($data);
            $backColor = $data["background-color"];
            $backColor_data = $data["background-color-data"];

            $backRollColor = $data["background-roll-color"];
            $backRollColor_data = $data["background-roll-color-data"];
            
            // echo ("back $backColor / $backColor_data ROLL = $backRollColor / $backRollColor_data <br / >");
            

            $borderColor = $data["border-color"];
            $borderColor_data = $data["border-color-data"];

            $borderRollColor = $data["border-roll-color"];
            $borderRollColor_data = $data["border-roll-color-data"];

            $fontColor = $data["font-color"];
            $fontColor_data = $data["font-color-data"];

            $fontRollColor = $data["font-roll-color"];
            $fontRollColor_data = $data["font-roll-color-data"];

            $font = $data["font-data"]; //"12|0|0|1";
            $fontRoll = $data["font-roll-data"]; //"12|0|0|1";
            
            $borderRadius = $data["border-radius"];
            $shadowLeft = $data["shadow-left"];
            $shadowRight = $data["shadow-right"]; //"6";
            $shadowColor = $data["shadow-color"]; //"#00f";
            $shadowColor_data = $data["shadow-color-data"]; //"";

            $buttonName ="button_".$key."_";

            $line = "";
            $line .= "<td  class='cmsEditTableColumnTitle'>Button '$name'";
            $line .= "<input type='hidden__' style='width:30px;' readonly='readonly' name='layout[".$buttonName."id]' value='$buttonId' >";
            $line .= "</td>";
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("color",$backColor,$buttonName."backColor",$backColor_data);
            $line .= " over: ";
            $line .= $this->layoutInput("color",$backRollColor,$buttonName."backRollColor",$backRollColor_data);
            $line .= "</td>";
            // Rahmen Farbe
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("color",$borderColor,$buttonName."borderColor",$borderColor_data);
            $line .= " over: ";
            $line .= $this->layoutInput("color",$borderRollColor,$buttonName."borderRollColor",$borderRollColor_data);
            $line .= "</td>";
            // Schrift Farbe
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("color",$fontColor,$buttonName."fontColor",$fontColor_data);
            $line .= " / ";
            $line .= $this->layoutInput("color",$fontRollColor,$buttonName."fontRollColor",$fontRollColor_data);
            $line .= "</td>";

            // Schrift
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("fontStyle", $font, $buttonName."font", $font_data);
            $line .= "</td>";
            
            // SchriftOver
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("fontStyle", $fontRoll, $buttonName."fontRoll", $fontRoll_data);
            $line .= "</td>";

            // Radius
            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("value", $borderRadius,$buttonName."borderRadius");
            $line .= "</td>";

            $line .= "<td class='cmsEditTableColumn'>";
            $line .= $this->layoutInput("value", $shadowLeft,$buttonName."shadowLeft");
            $line .= $this->layoutInput("value", $shadowRight,$buttonName."shadowRight");
            $line .= $this->layoutInput("color",$shadowColor,$buttonName."shadowColor",$shadowColor_data);
            $line .= "</td>";
            $line .= "<td class='cmsEditTableColumn'></td>";
            $line .= "</tr>";
            $table .= $line;
        }
        $table .= "</table>";
        echo ($table);
    }
            
    function button_get($theme,$editType) {
        // echo ("<h1> button Get $theme / $editType </h1>");
        $buttonList = $this->edit_buttonList($editType);
        $buttonData = array();
        foreach ($buttonList as $buttonKey => $buttonName) {
            // echo ("<h2>Get Button $buttonKey => ".$editType.$buttonKey." </h2>");
            $buttonData[$buttonKey] = array();
            $filter = array("theme"=>$theme,"name"=>$editType.$buttonKey,"type"=>"button");
            $data = cmsStyle_get($filter,"css");
            if (is_array($data)) {
                $id = $data[id];
                // echo ("GET $data for $buttonKey $typeName $id <br />");
                $buttonData[$buttonKey] = $data;
            } else {
                // echo ("No Data '$data' ".$editType.$buttonKey." <br>");
            }
        }
        return $buttonData;
        
    }

    function button_save($theme,$editType,$layout,$buttonData=array()) {
        // echo ("<h1>button_save( $theme,$editType,$saveData)</h1>");
        $out = 0;
        $buttonList = $this->edit_buttonList($editType);
        foreach ($buttonList as $buttonKey => $buttonName) {
            $saveId = 0;
            // echo ("<h2>Save Button $buttonKey = $buttonName </h2>");

            $start = "button_".$buttonKey."_";
            
            
            $saveId   = $layout[$start."id"];
            $saveName = $editType.$buttonKey;
            
            if ($out) echo ("<h3>SAVE start='$start' id=$saveId name=$saveName </h3>");

            $saveType = "button";

            $saveBack = "";
            $saveBorder = "";
            $saveColor  = "";
            $saveMargin = "";
            $saveRadius = "";
            $savePadding = "";
            $saveFont = "";
            $saveData = "";
            

            // Background
            $backColor = $layout[$start."backColor"];
            if ($backColor) {
                $saveBack .= "background-color:#".$backColor.";";
                $buttonData[$buttonKey]["background-color"] = $backColor;
                $backData = $layout[$start."backColor_colorId"]."|".$layout[$start."backColor_colorBlend"]."|".$layout[$start."backColor_colorSaturation"];
                $saveBack .= "background-color-data:".$backData.";";
                $buttonData[$buttonKey]["background-color-data"] = $backData;
                if ($out) echo ("Background = $backColor // $backData <br>");
            }

            $backRollColor = $layout[$start."backRollColor"];
            if ($backRollColor) {
                $saveBack .= "background-roll-color:#".$backRollColor.";";
                $buttonData[$buttonKey]["background-roll-color"] = $backRollColor;
                $backData = $layout[$start."backRollColor_colorId"]."|".$layout[$start."backRollColor_colorBlend"]."|".$layout[$start."backRollColor_colorSaturation"];
                $saveBack .= "background-roll-color-data:".$backData.";";
                $buttonData[$buttonKey]["background-roll-color-data"] = $backData;
                if ($out) echo ("BackgroundRoll = $backRollColor // $backData <br>");
            }
            // Shadow
            $shadowColor = $layout[$start."shadowColor"];
            if ($shadowColor) {
                $saveBack .= "shadow-color:#".$shadowColor.";";
                $buttonData[$buttonKey]["shadow-color"] = $shadowColor;
                $shadowData = $layout[$start."shadowColor_colorId"]."|".$layout[$start."shadowColor_colorBlend"]."|".$layout[$start."shadowColor_colorSaturation"];
                $saveBack .= "shadow-color-data:".$shadowData.";";
                $buttonData[$buttonKey]["shadow-color-data"] = $shadowData;
                if ($out) echo ("Shadow = $shadowColor // $shadowData <br>");
            }
            $shadowLeft   = $layout[$start."shadowLeft"];
            if ($shadowLeft) $saveBack .="shadow-left:$shadowLeft;";
            $buttonData[$buttonKey]["shadow-left"] = $shadowLeft;
            
            $shadowRight  = $layout[$start."shadowRight"];
            if ($shadowRight) $saveBack .="shadow-right:$shadowRight;";
            $buttonData[$buttonKey]["shadow-right"] = $shadowRight;
           
            // Border
            $borderColor = $layout[$start."borderColor"];
            if ($borderColor) {
                $saveBorder .= "border-color:#".$borderColor.";";
                $buttonData[$buttonKey]["border-color"] = $borderColor;
                $borderData = $layout[$start."borderColor_colorId"]."|".$layout[$start."borderColor_colorBlend"]."|".$layout[$start."borderColor_colorSaturation"];
                $saveBorder .= "border-color-data:".$borderData.";";
                $buttonData[$buttonKey]["border-color-data"] = $borderData;
                if ($out) echo ("BORDER = $borderColor // $borderData <br>");
            }

            $borderRollColor = $layout[$start."borderRollColor"];
            if ($borderRollColor) {
                $saveBorder .= "border-roll-color:#".$borderRollColor.";";
                $buttonData[$buttonKey]["border-roll-color"] = $borderRollColor;
                $borderData = $layout[$start."borderRollColor_colorId"]."|".$layout[$start."borderRollColor_colorBlend"]."|".$layout[$start."borderRollColor_colorSaturation"];
                $saveBorder .= "border-roll-color-data:".$borderData.";";
                $buttonData[$buttonKey]["border-roll-color-data"] = $borderData;
                if ($out) echo ("BORDER-ROLL = $borderRollColor // $borderData <br>");
            }
            
            $borderRadius = $layout[$start."borderRadius"];
            if ($borderRadius) $saveBorder .= "border-radius:".$borderRadius.";";
            $buttonData[$buttonKey]["border-radius"] = $borderRadius;
            
            // FONT
            $fontColor = $layout[$start."fontColor"];
            if ($fontColor) {
                $saveFont .= "font-color:#".$fontColor.";";
                $buttonData[$buttonKey]["font-color"] = $fontColor;
                $fontData = $layout[$start."fontColor_colorId"]."|".$layout[$start."fontColor_colorBlend"]."|".$layout[$start."fontColor_colorSaturation"];                
                $saveFont .= "font-color-data:".$fontData.";";
                $buttonData[$buttonKey]["font-color-data"] = $fontData;
                if ($out) echo ("FONT = $fontColor // $fontData <br>");
            }

            $fontRollColor = $layout[$start."fontRollColor"];
            if ($fontRollColor) {
                $saveFont .= "font-roll-color:#".$fontRollColor.";";
                $buttonData[$buttonKey]["font-roll-color"] = $fontRollColor;
                $fontData = $layout[$start."fontRollColor_colorId"]."|".$layout[$start."fontRollColor_colorBlend"]."|".$layout[$start."fontRollColor_colorSaturation"];
                $saveFont .= "font-roll-color-data:".$fontData.";";
                $buttonData[$buttonKey]["font-roll-color-data"] = $fontData;
                if ($out) echo ("FONT-ROLL = $fontRollColor // $fontData <br>");
            }

            $fontStr = $layout[$start."font_size"]."|".$layout[$start."font_bold"]."|".$layout[$start."font_kursiv"]."|".$layout[$start."font_underline"];
            if ($fontStr) {
                $saveFont .= "font-data:$fontStr;";
                $buttonData[$buttonKey]["font-data"] = $fontStr;
                if ($out) echo ("FONT '$fontStr' <br>");
            }

            
            $fontStr = $layout[$start."fontRoll_size"]."|".$layout[$start."fontRoll_bold"]."|".$layout[$start."fontRoll_kursiv"]."|".$layout[$start."fontRoll_underline"];
            if ($fontStr) {
                $saveFont .= "font-roll-data:$fontStr;";
                $buttonData[$buttonKey]["font-roll-data"] = $fontStr;
                if ($out) echo ("FONT-Roll '$fontStr' <br>");
            }

            
          
            
            $saveData = array();
            $saveData[id]         = $saveId;
            $saveData[theme]      = $theme;
            $saveData[type]       = $saveType;
            $saveData[name]       = $saveName;
            $saveData[background] = $saveBack; 
            $saveData[margin]     = $saveMargin;
            $saveData[border]     = $saveBorder;
            $saveData[padding]    = $savePadding;
            $saveData[font]       = $saveFont;
            // show_array($saveData);
            $saveResult = cmsStyle_save($saveData);
            if ($saveResult) {
                if ($out) cms_infoBox("Button gespeichert $saveResult");
                if (!$saveId) {
                    $saveId = $saveResult;
                    if ($out) echo ("neue ID is $saveId <br>");
                    $buttonData[$buttonKey][id] = $saveId;
                }
            } else {
                cms_errorBox("Button nicht gespeichert $saveResult");
            }
           //  echo ("SAVE $saveId <br>");

        }
        
        
        foreach ($layout as $key => $value ) {
            if (substr($key,0,7) == "button_") {
                $key = substr($key,7);
                $typeOff = strpos($key,"_");
                if ($typeOff) {
                    $buttonType = substr($key,0,$typeOff);
                    $key = substr($key,$typeOff+1);
                   //  echo ("Save button '$buttonType' '$key'  $value <br/>");
                }
            }
        }
        return $buttonData;
    }
    
    
    function splitWidthStr($str,$type=null) {
        $res = array();
        if (!$str) return $res;
        // echo ("Str $str <br>");
        $str = str_replace(array("px",";"),"",$str);
       //  echo ("ohnePx $str <br/>");
        
        $values = explode(" ",$str);
        if (count($values)== 1) {
            $res[main] = $values[0];            
        } else {
            $res[top] = $values[0];
            $res[right] = $values[1];
            $res[bottom] = $values[2];
            $res[left] = $values[3];
        }
        return $res;
        
        
        
        
    }
            
    

    function showColor($view,$frameWidth) {
        if (!$view) {
            $goPage = cms_page_goPage("view=layoutColors");
            echo ("<a href='$goPage' style='width:200px;' class='cmsLinkButton' >Farben</a>");
            return 0;        
        }
        echo ("<h1>Farben bearbeiten</h1>");
      
        
        
        
        
        
        $colorList = cmsStyle_getList(array("type"=>"color"), $sort,"assoId");
        
        if ($_POST[saveColor]) {
            //  echo ("SAVE <br>");
            $saveColorList = $_POST[colors];
            if (is_array($saveColorList)) {
                // show_array($saveColorList);
                foreach($saveColorList as $colorId => $colorValue) {
                    // echo ("Save Color $colorId > $colorValue <br>");
                    $saveId = $colorId;
                    $saveName = $colorValue[name];
                    $saveColor = $colorValue[color];
                    // echo ("Save Color $saveId name='$saveName' color='$saveColor' <br>");
                    if (is_array($colorList[$saveId])) {
                        
                        $change = array();
                        if ($colorList[$saveId][name] != $saveName) $change[] = "name";
                        if ($colorList[$saveId][color] != $saveColor) $change[] = "color";
                        
                        if (count($change)) {
                            echo ("Save Color because diffrent ");
                            for($i=0;$i<count($change);$i++) echo ($change[$i]." ");
                            echo ("<br>");
                            $saveData = array();
                            $saveData[id] = $saveId;
                            $saveData[type] = "color";
                            $saveData[name] = $saveName;
                            $saveData[color] = $saveColor;
                            // show_array($saveData);
                            $saveResult = cmsStyle_save($saveData);
                            if ($saveResult) {
                                $colorList[$saveId][name] = $saveName;
                                $colorList[$saveId][color] = $saveColor;                                
                            }
                            
                            // $saveId $saveName $saveColor <br>");
                        }
                    } else {
                        echo ("ColorList not exist for $saveId <br>");
                    }
                }
            }
            
            
            
            $newColor = $_POST[newColor];
            $newColorName = $newColor[name];
            $newColorColor = $newColor[color];
            // echo ("<h1>NEUE FARBE</h1>");
            // echo ("Name : '$newColorName' Farbe: '$newColorColor' <br/>");
            // show_array($newColor);
            
            if ($newColorColor) {
                if ($newColorName) {
                    echo ("<h1>NEUE FARBE</h1>");
                     echo ("Save NEW COLOR name='$newColorName' value='$newColorColor' <br>");                    
                    $saveData = array();
                    $saveData[type] = "color";
                    $saveData[name] = $newColorName;
                    $saveData[color] = $newColorColor;
                    
                    $saveResult = cmsStyle_save($saveData);
                    if ($saveResult) {
                        echo ("Farbe gespeichert! $saveResult <br>");
                        $saveData[id] = $saveResult;
                        $colorList[$saveResult] = $saveData;       
                        
                        $newColorName = "";
                        $newColorColor = "";
                    }
                } else {
                    cms_errorBox("Keine Name für neue Farbe");
                }
            }
            
            
            
        }
        
        
        
        $goPage = cms_page_goPage("view=layoutColors");
        echo ("<form method='post' action='$goPage' >");
        foreach ($colorList as $name => $value) {
            $id = $value[id];
            $name = $value[name];
            $color = $value[color];
            // echo ("color $name $value <br>");
            echo ("<div style='width:30px;height:30px;display:inline-block;background-color:#$color;' >-</div>");
            echo ("<input type='hidden' value='$value[id]' name='colors[".$id."][id]' />");
            echo ("<input type='text' value='$value[name]' name='colors[".$id."][name]' />");
            echo ("<input type='text' value='$color' name='colors[".$id."][color]' />");
            
            echo ("<br />");           
        }
        
        // New Color
        echo ("<input type='hidden' value='' name='newColor[id]' />");
        echo ("<input type='text' value='$newColorName' name='newColor[name]' />");
        echo ("<input type='text' value='$newColorColor' name='newColor[color]' />");
        echo ("<br />"); 
        
        echo ("<input type='submit' class='cmsInputButton' name='saveColor' value='speichern' />");
        
        echo ("</form>");
        
        
        
        
        $goPage = cms_page_goPage("");
        echo ("<a href='$goPage' class='cmsLinkButton secondButton cmsSecond ' >zurück</a><br/>");
    }


}

function cms_admin_cmsLayout($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_layout_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_layout();

    } else {
        $class = new cmsAdmin_layout_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);
}








function cmsLayout_getWidthStr($layout,$type) {
    
    
    // value
    $value = $layout[$type];
        
    // Top
    $valueTop = $layout[$type."Top"];
    if (!is_null($valueTop)) {
        if (intval($valueTop) OR $valueTop=="0") $valueTopUse = intval($valueTop);
    }

    // Right
    $valueRight = $layout[$type."Right"];
    if (!is_null($valueRight)) {
        if (intval($valueRight) OR $valueRight=="0") $valueRightUse = intval($valueRight);
    }
    // Bottom
    $valueBottom = $layout[$type."Bottom"];
    if (!is_null($valueBottom)) {
        if (intval($valueBottom) OR $valueBottom=="0") $valueBottomUse = intval($valueBottom);
    }
    // Left
    $valueLeft = $layout[$type."Left"];
    if (!is_null($valueLeft)) {
        if (intval($valueLeft) OR $valueLeft=="0") $valueLeftUse = intval($valueLeft);
    }



    $valueBottom = $layout[valueBottom];
    if (!is_null($valueLeftUse) OR !is_null($valueRightUse) OR !is_null($valueTopUse) or !is_null($valueBottomUse)) {
        $valueStr = "";

        // add Top
        if (is_null($valueTopUse)) $valueStr.=$value."px";
        else {
            if ($valueTopUse) $valueStr.=$valueTopUse."px";
            else $valueStr .= "0";
        }

        // add Right
        if (is_null($valueRightUse)) $valueStr.=" ".$value."px";
        else {
            if ($valueRightUse) $valueStr.=" ".$valueRightUse."px";
            else $valueStr .= " 0";
        }

         // add Bottom
        if (is_null($valueBottomUse)) $valueStr.=" ".$value."px";
        else {
            if ($valueBottomUse) $valueStr.=" ".$valueBottomUse."px";
            else $valueStr .= " 0";
        }

        // add Left
        if (is_null($valueLeftUse)) $valueStr.=" ".$value."px";
        else {
            if ($valueLeftUse) $valueStr.=" ".$valueLeftUse."px";
            else $valueStr .= " 0";
        }


        $valueStr.= ";";

    } else {
        $valueStr = $value."px;";
    }
    return $valueStr;

}
?>
