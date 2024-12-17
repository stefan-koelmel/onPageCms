<?php // charset:UTF-8

class cmsType_flip_base extends cmsType_contentTypes_base {
    function getName (){
        return "Wechselnder Inhalt";        
    }

    function flip_show($contentData,$frameWidth,$getData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        // show_array($contentData);



        $res = array();

        // MainData

        $type = $data[typeSelect];
        $layerWidth = $data[layerWidth];
        $layerHeight = $data[layerHeight];
        $layerCount = $data[layerCount];


        //div_start("FlipFrame","border:1px solid #cfc");
        $edit = $_SESSION[edit];
        $editAble = $_SESSION[userLevel] > 6;
        $flipId = $contentData[id];
        
        // GET AND CREATE LAYERCONTENT
        $layerData = array();
        for ($layerNr=1;$layerNr<=$layerCount;$layerNr++) {
            $pageId = "layer_".$flipId."_".$layerNr;
            $layerName = $data["layer_".$layerNr."_name"];
            $layerType = $data["layer_".$layerNr."_type"];
            // echo ("LayerName = '$layerName' Nr=$layerNr Typ = $layerType <br>");
            $layerContent = cms_content_get(array("pageId"=>$pageId));
            if (!is_array($layerContent)) {
                
                if ($layerType) {
                    $layerContent = array();
                    $layerContent["pageId"] = $pageId;
                    $layerContent["type"] = $layerType;
                    $layerId = cms_content_save(null, $layerContent);
                    if ($layerId) cms_infoBox("Create Content for $pageId");
                    else cms_errorBox ("Fehler beim erstelen von $pageId ");                    
                    $layerContent[id] = $layerId;
                    
                } else {
                    cms_errorBox("Kein Modul ausgewählt für Layer $layerNr ");
                }
            } 
            
            if (is_array($layerContent)) {
                $layerId = $layerContent[id];
                if ($layerType != $layerContent[type]) {
                    echo ("Diffrent Typ in content $layerNr $layerType != $layerContent[type] <br> ");
                    $newLayerCountent = $layerContent;
                    $newLayerCountent[type] = $layerType;
                    $res = cms_content_save($layerContent[id], $newLayerCountent, $layerContent);
                    echo (" -- > updated res = $res <br>");
                }
                $layerData[$layerId] = $layerContent;
                $layerData[$layerId][flipNr]=$layerNr;
                $layerData[$layerId][layerName]=$layerName;
            }
        }
        
        
        if ($editAble) {
            // $this->show_editMode($contentData,$layerData,$frameWidth);
            // return 0;
        }
        
        // foreach($data as $key => $value) echo ("$key => $value <br>");
        
        div_start("cmsFlipFrame","position:relative;width:100%;");
        
        
        $contentId = $contentData[id];
        
        $divNameMain ="cmsFlipMainFrame cmsFlipMainFrame_".$contentId;
        $sameHeight = $data[sameHeight];
       
        $flipNr = 1;
        
        $typeSelect = $data[typeSelect];
        switch ($typeSelect) {
            case "roll" :
                $flipNr = 1;
                $divNameMain .= " cmsFlipMainFrame_roll";
                $sameHeight = 1;
                break;
            
            case "click" :
                $flipNr = 1;
                $divNameMain .= " cmsFlipMainFrame_click";
                // $sameHeight = 1;
                break;
            
            case "tab" :
                $flipNr = 1;
                $divNameMain .= " cmsFlipMainFrame_tab";
                // $sameHeight = 1;
                break;
            
            case "time" : 
                
                break;
            
            default :
                echo ("<h1> unkown Type ($typeSelect) in flip_show</h1>");
            
        }
        
        if ($sameHeight) $divNameMain .= " cmsFlipMainFrame_sameHeight";
        
        if ($_GET[flipNr]) $flipNr = $_GET[flipNr];
      
        
        $editId = $_GET[editId];
        if ($editId) {
            if (is_array($layerData[$editId])) {
                $flipNr = $layerData[$editId][flipNr];
                echo ("<script type='text/javascript'>");
                echo ("var flipFrameSelect='$editId';\n");
                // echo ("$(document).ready(function(){\n");
                //echo("alert('hier'+flipFrameSelect);\n");
                //echo ("setActivFlip('Start');\n");
                // echo (")};\n");
                // 
                
                echo ("</script>");
                // echo ("<h1>Layer with id $editId is in EditMode -> FlipNr =$flipNr </h1>");

            }
        }

        $padding = 0; 
        $innerWidth = $frameWidth - 2 * ($padding + $border);
        
       
        
        switch ($typeSelect) {
            case "tab" :
                
                $tabDirection = $data[tabDirection];
                
                $tabWidth = $data[tabWidth];
                $tabHeight = $data[tabHeight];
                $tabFloat = $data[tabFloat];
               //  echo ("tab $tabDirection $tabWidth x $tabHeight $tabFloat");
                
                $mainTabStyle = "";
                $tabStyle = "";
                $contentStyle = "";
                
                $tabFloat = 0;
                
                
                if ($tabFloat) {
                    $mainTabStyle .= "position:absolute;";
                }
                
                
                
                
                switch($tabDirection) {
                    case "hori" :
                        $mainTabStyle .= "width:".$frameWidth."px;";
                        
                        if ($tabHeight) $mainTabStyle .= "height:".$tabHeight."px;";
                        break;
                    
                    case "verti" :
                        if ($tabWidth) $tabWidth = 200;
                        $mainTabStyle .= "width:".$tabWidth."px;float:left;display:inline-block;";
                        
                        // if ($tabHeight) $mainTabStyle.= "height:".(count($layerData)*$tabHeight)."px;";
                        
                        // $mainTabStyle .= "background-color:#f00;";
                        $mainTabStyle .= "padding:0;margin:0px;";
                        // Tab
                        $tabStyle .= "display:block;";
                        $tabStyle .= "margin:0;padding:5px;";
                        $tabStyle .= "float:none;";
                        // $tabStyle .= "width:".$tabWidth."px;";
                        if ($tabHeight) $tabStyle .= "height:".$tabHeight."px;";
                        
                        if ($tabFloat) {    
                            $contentStyle .= "display:inline-block;position:relative;";
                        } else {
                            $innerWidth = $innerWidth - $tabWidth;
                            $contentStyle .= "display:inline-block;position:relative;";
                            
                        }
                        $contentStyle .= "width:".$innerWidth."px;padding:0;float:left;";
                        break;
                        
                        
                }
                
//                echo ("FRAME $frameWidth pas =$padding  bord=$border inner=$innerWidth <br>");
//                echo("<div style='width:200px;background-color:#00f;'>HALO</div>");
                 
                $mainTabData = array();
                $mainTabData["style"] = $mainTabStyle;
                $mainTabData["id"] = $contentId;
                
                //echo ("<div style='width:100%;'>");
                
//                echo ("<div class='cmsFlipFrame_tabFrame' style='width:200px;height:100px;display:inline-block;float:left;'>");
//                echo ("<div class='cmsFlipFrame_tab' style='$tabStyle'>TAB 1 </div>"); //display:block;background:#f99;height:30px;'>&nbsp;</div>");
//                echo ("<div style='display:block;background:#f00;height:30px;border:5px solid #000;padding:10px;position:relative;'>TAB</div>");
//
//                echo ("</div>");
//
//                echo ("<div style='float:left;width:400px;display:inline-block;background:#00f;padding:0;border:0;top:0;position:relative;'>");
//                echo ("HIer Der Inhalt");
//                echo ("</div>");
//                echo ("<div style='clear:both;'></div>");
                // echo ("</div>");
                
                
                div_start("cmsFlipFrame_tabFrame",$mainTabData);
                foreach ($layerData as $layerId => $layerContent) {
                    $name = $layerContent[layerName];
                    if (!$name) $name = "noName $layerId";
                    
                    $myFlipNr = $layerContent[flipNr];
                    $divName = "cmsFlipFrame_tab cmsFlipFrame_tab_".$myFlipNr;
                    
                    if ($myFlipNr == $flipNr) $divName .= " cmsFlipFrame_tab_selected";
                    div_start($divName,array("id"=>$myFlipNr,"style"=>$tabStyle));
                    echo ($name);
                    div_end($divName);
                }
                div_end("cmsFlipFrame_tabFrame","before");
                $padding = 5;
                break;
                
                
            case "time";    
                $contentList = array("12","23");
                $name = "TrillaSlider";
                $showData = array();
                $showData[loop] = 1;
                $showData[time] = 5000;
                $showData[wait] = 500;
                $width = $frameWidth;
                $height = null;
                $outPut = 0;
                $sliderStart = cmsSlider_start(null, $name, $contentList, $showData, $width, $height, $outPut);
                $sliderEnd = cmsSlider_end(null, $name, $contentList, $showData, $width, $height, $outPut);
        }
        
        $divDataMain = array();
        $divDataMain[id] = $contentId;
        $divDataMain[style] = $contentStyle;
        
        div_start($divNameMain,$divDataMain);
        echo ("<div class='hiddenData cmsFlipMainFrame_".$contentId."_count'>$layerCount</div>");
        echo ("<div class='hiddenData cmsFlipMainFrame_".$contentId."_active'>$flipNr</div>");
       
        
        if ($sliderStart) {
            // echo ("Silder Start lang ".strlen($sliderStart)."<br>");
            echo ($sliderStart);
        }
        
        
        
        
    
        foreach ($layerData as $layerId => $layerContent) {
            $layerNr = $layerContent[flipNr];
            // echo ($layerId. " $innerWidth <br/ >");

            // for ($layerNr=1;$layerNr<=$layerCount;$layerNr++) {
            $layerType = $data["layer_".$layerNr."_type"];

            $divName = "cmsFlipContent cmsFlipContent_".$contentId." cmsFlipContent_".$contentId."_".$layerNr;
            // $myFlipNr = $contentId."_".$layerNr;
            // $useFlip = $contentId."_". 
            switch ($typeSelect) {
                case "time" :
                    break;
                default : 
                    if ($layerNr != $flipNr) $divName .= " cmsFlipContent_hidden";
            }
           
            div_start($divName,"width:".$innerWidth."px;");

            $pageId = "layer_".$contentData[id]."_".$layerNr;
            // $layerContent = cms_content_get(array("pageId"=>$pageId));

            if (is_array($layerContent)) {
                $res = cms_contentType_show($layerContent,$innerWidth);
            } else {
                cms_errorBox("Kein Inhalt für $layerNr ");
            }
            div_end($divName);
        //  echo ("SHOW RESULT is $res <br>&nbsp;<br>");

        }
        
        if ($sliderEnd) {
            echo ($sliderEnd);
            // echo ("Silder End lang ".strlen($sliderEnd)."<br>");
        }
        div_end($divNameMain);
        // echo ("<div style='clear:both;'></div>");
        div_end("cmsFlipFrame","before");
               
    }


    function show_editMode($contentData,$layerData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $layerCount = $data[layerCount];
        
        // GET ACTIVE LAYERNR in editMode
        $editId = $_GET[editId];
        
        $edit = $_SESSION[edit];
        $editAble = $_SESSION[userLevel] > 6;
        
        if ($editId) {
            // echo ("<h1> $editId</h1>");
            if (is_array($layerData[$editId])) {
                $flipNr = $layerData[$editId][flipNr];
                //echo ("Layer with id $editId is in EditMode -> FlipNr =$flipNr <br>");

            }
        }



        $contentId = $contentData[id];
        if (is_array($getData[layerNr])) $layerNr = $getData[layerNr];
        
        
        $divSelectName = "cmsFlipSelectFrame";
        $divSelectName .= " cmsEditToggle";
        if (!$edit) $divSelectName .= "cmsEditHidden";
        
        div_start($divSelectName,array("id"=>$contentId));

        div_start("cmsFlipTitleFrame","float:left;");
        echo ("Wählen Sie den Inhalt aus: ");
        div_start("hiddenData frameWidth");
        echo ($frameWidth);
        div_end("hiddenData frameWidth");
        div_end("cmsFlipTitleFrame");


        $useLink = 0;

        if (!$flipNr) $flipNr = 1;
        for ($i=1;$i<=$layerCount;$i++) {
            $divName = "cmsFlipFrameSelector";
            if ($i == $flipNr) $divName .= " cmsFlipFrameSelectorSelected";
            $divName .= " cmsFlipSelector_".$flipId."_".$i;

            $divData = array();
            // $divData[style] = "float:left;";
            $divData[name] = "cmsFlipContent_".$contentId; // "select_".$flipId."_".$i;
            $divData[id] = $i;

            div_start($divName,$divData);
            

            if ($useLink) echo ("<a href='".$GLOBALS[pageData][name].".php?flipNr=".$contentId."_".$i."'>");
            $name = $data["layer_".$i."_name"];
            if (!$name) {
                $name = "Inhalt $i";
            }
            
            echo($name);
            if ($useLink) echo ("</a>");
            div_end($divName);
        }
        echo ("<form method='post' >");
        echo("<input id='activeFlip_".$contentId."' type='text' value='$flipNr' name='activeFlip_".$contentId."' />");
        echo ("</form>");
        
        div_end($divSelectName,"before");
        if (!$flipNr) { 
            $flipNr = $_GET[flipNr];
            // echo ("<h1>FlipNr $flipNr </h1>");
            if (!$flipNr) $flipNr = 1;
        }

        $divNameMain ="cmsFlipMainFrame cmsFlipMainFrame_".$contentId;
        if ($data[sameHeight]) $divNameMain .= " cmsFlipMainFrame_sameHeight";
        div_start($divNameMain);

        foreach ($layerData as $layerId => $layerContent) {
            $layerNr = $layerContent[flipNr];

            // for ($layerNr=1;$layerNr<=$layerCount;$layerNr++) {
            $layerType = $data["layer_".$layerNr."_type"];

            $divName = "cmsFlipContent cmsFlipContent_".$contentId." cmsFlipContent_".$contentId."_".$layerNr;
            // $myFlipNr = $contentId."_".$layerNr;
            // $useFlip = $contentId."_". 
            if ($layerNr != $flipNr) $divName .= " cmsFlipContent_hidden";

            div_start($divName,"width:".$frameWidth."px;");

            $pageId = "layer_".$contentData[id]."_".$layerNr;
            // $layerContent = cms_content_get(array("pageId"=>$pageId));

            if (is_array($layerContent)) {
                // $res = cms_contentType_show($layerContent,$frameWidth);
            } else {
                cms_errorBox("Kein Inhalt für $layerNr ");
            }
            div_end($divName);
        //  echo ("SHOW RESULT is $res <br>&nbsp;<br>");

        }
        div_end($divNameMain);

    //        }
    //            
    //           
    //            div_end($divNameMain);
    //            
        $divName = "cmsFlipContent cmsFlipContent_".$contentId."_help";
        div_start($divName);
        // echo ("Hier ist der Inhalt von $contentId <br>");
        div_end($divName);
    }

    function flip_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
//        $res[showName] = "Wechselnde Inhalte";
//        $res[showTab] = "Admin";

        // MainData

        
        $type = $data[typeSelect];
//        echo ("<h1> TYP = '$type' </h1>");
//        foreach ($data as $key => $value) echo ("$key = $value <br />");
        if (!$type) $type = "roll";
        $addData = array();
        $addData["text"] = "Wechel-Art";
        $addData["input"] = $this->flip_selectType($type,"editContent[data][typeSelect]",array("onChange"=>"submit()"));
        $addData["mode"] = "Simple";
        $res[] = $addData;

        /* $addData = array();
        $addData["text"] = "Rahmen Breite/Höhe";
        $addData["input"] = "<input type='text' name='editContent[data][layerWidth]' style='width:70px;' value='$data[layerWidth]' > / <input type='text' name='editContent[data][layerHeight]' style='width:70px;' value='$data[layerHeight]' >";
        $res[] = $addData;
        */
        
        $layerCount = $data[layerCount];
        if (!$layerCount) $layerCount = 2;

        switch ($type) {
            case "roll" :
                $addData = array();
                $addData["text"] = "hidden-Anzahl Ebenen";
                $addData["input"] = "<input type='hidden' name='editContent[data][layerCount]'  value='$layerCount' >";
                $addData["mode"] = "Simple";
                $res[] = $addData;
                break;

            case "time" :
                $addData = array();
                $addData["text"] = "Zeitwechsel nach";
                $addData["input"] = "<input type='text' name='editContent[data][msChange]' style='width:70px;' value='$data[msChange]'> ms";
                $addData["mode"] = "Simple";
                $res[] = $addData;
                
                $addData = array();
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' style='width:70px;' value='$layerCount'> in Milli-Sekunden";
                $addData["mode"] = "Simple";
                $res[] = $addData;
                break;

            case "click" :
                $addData = array();
                $layerCount = $data[layerCount];
                if (!$layerCount) $layerCount = 2;
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' value='$layerCount'> in Milli-Sekunden";
                $addData["mode"] = "Simple";
                $res[] = $addData;
                break;

            case "tab" :
                $addData = array();
                $layerCount = $data[layerCount];
                if (!$layerCount) $layerCount = 2;
                $addData["text"] = "Anzahl Ebenen";
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' value='$layerCount'>";
                $addData["mode"] = "Simple";
                $res[] = $addData;
                
                $addData = array();
                $addData["text"] = "Ausrichtung Tabs";
                $input = "<select value='$data[tabDirection]' name='editContent[data][tabDirection]' >";
                $sel = "";
                if ($data[tabDirection]=="hori") $sel = "selected='selected'";
                $input .= "<option value='hori' $sel>Horizontal</option>";
                $sel = "";
                if ($data[tabDirection]=="verti") $sel = "selected='selected'";
                $input .= "<option value='verti' $sel>Vertikal</option>";
                $input .= "</select>";
                $addData["input"] = $input;
                $addData["mode"] = "Simple";
                $res[] = $addData;
                
                
                $addData = array();
                $addData["text"] = "Tabs überlagernd";
                $tabFloat = $data[tabFloat];
                if ($tabFloat) $checked="checked='checked'";
                else $checked = "";
                $addData["input"] = "<input $checked type='checkbox' name='editContent[data][tabFloat]'  value='1' />";
                $addData["mode"] = "More";
                $res[] = $addData;
                
                $addData = array();
                $tabWidth = $data[tabWidth];
                if (!$tabWidth) $tabWidth = 200;
                $addData["text"] = "Breite Tab";
                $addData["input"] = "<input type='text' name='editContent[data][tabWidth]' value='$tabWidth'>";
                $addData["mode"] = "More";
                $res[] = $addData;
                
                $addData = array();
                $tabHeight = $data[tabHeight];
                if (!$tabHeight) $tabHeight = 20;
                $addData["text"] = "Höhe Tab";
                $addData["input"] = "<input type='text' name='editContent[data][tabHeight]' value='$tabHeight'>";
                $addData["mode"] = "More";
                $res[] = $addData;
                
                break;
                
        }
        
        $addData = array();
        $addData["text"] = "Gleiche Höhe";
        $sameHeight = $data[sameHeight];
        if ($sameHeight) $checked="checked='checked'";
        else $checked = "";
        $addData["input"] = "<input $checked type='checkbox' name='editContent[data][sameHeight]'  value='1' />";
        $addData["mode"] = "More";
        $res[] = $addData;
        
        
        $typeList = cms_contentType_getTypes();
        //show_array($typeList);
        
        for ($i=1;$i<=$layerCount;$i++) {
            $addTo = "layer_$i";
//            $res[$addTo] = array();
//            $layerType = $data["layer_".$i];
//            $addData["text"] = "Inhalt Typ";
//            $input = "<div id='$addTo' class='spacer spacerContentType spacerContentType_flip spacerLast spacerDrop ui-droppable'> </div>";
//            
//            $input = "<div id='$addTo' style='height:20px;width:200px;' class='layerDrop ui-droppable'> </div>";
//            
//            
//            $addData["input"] = $input."<input id='layer_".$i."_type' type='text' name='editContent[data][layer_".$i."]' value='$layerType' />";
//            $res[$addTo][] = $addData;
            
            $layerType = $data["layer_".$i."_type"];
            if ($layerType) {
                $layerName = $typeList[$layerType];
                if (is_array($layerName)) $layerName = $layerName[name];
                if (!$layerName) $layerName = "unbekannt ($layerType)";
                
            } else {
                $layerName = "nicht definiert";
            }
                
            
            
            $addData = array();
            $addData["text"] = "Inhalt Typ Layer $i";
            $input = "<div id='$addTo' style='height:20px;width:200px;display:inline-block;' class='layerDrop ui-droppable'>$layerName</div>";
            $input .= "<input id='layer_".$i."_type' type='hidden' name='editContent[data][layer_".$i."_type]' value='$layerType' />";
            
            switch ($type) {
                case "tab" :
                    
                    $input .= "<input type='text' name='editContent[data][layer_".$i."_name]' value='".$editContent[data]["layer_".$i."_name"]."' />";
                    break;
            }
            
            
            $addData["input"] = $input;
            $addData["mode"] = "Simple";
            $res[] = $addData;
            
            
            
        }
        
        return $res;
    }


    function flip_selectType($type,$dataName,$dataAction=array()) {
        $typeList = array();
        $typeList[roll] = array("name"=>"Maus über Rahmen");
        $typeList[time] = array("name"=>"Zeitgesteuert");
        $typeList[click] = array("name"=>"Maus Klick");
        $typeList[tab] = array("name"=>"Tabs");

        $str = "";
        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' ";
        foreach ($dataAction as $key => $value) {
            $str .= " $key='$value'";
        }
        $str .= " >";


        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code' ";
             if ($code == $type) $str.= " selected='selected' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";
        return $str;
    }
}

function cmsType_flip_class() {
    if ($GLOBALS[cmsTypes]["cmsType_flip.php"] == "own") $flipClass = new cmsType_flip();
    else $flipClass = new cmsType_flip_base();
    return $flipClass;
}


function cmsType_flip($contentData,$frameWidth,$getData=array()) {
    $flipClass = cmsType_flip_class();
    $flipClass->flip_show($contentData,$frameWidth,$getData);
}


function cmsType_flip_editContent($editContent,$frameWidth) {
    $flipClass = cmsType_flip_class();
    return $flipClass->flip_editContent($editContent,$frameWidth);
}





?>
