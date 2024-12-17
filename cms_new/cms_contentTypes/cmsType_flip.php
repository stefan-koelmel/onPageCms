<?php // charset:UTF-8

class cmsType_flip_base extends cmsClass_content_show {
    function getName (){
        return "Wechselnder Inhalt";        
    }
    
    function contentType_init() {
        //  echo "INIT FLIPP <br>";
        
        $this->flipList = array();
        $res = $this->page_getContent("all",$this->contentId,"flip"); // $mainId, $mainType)
        
        foreach ($res as $contentId => $contentData) {
            $pageId = $contentData[pageId];
            list ($type,$id,$nr) = explode ("_",$pageId);
            
            if ($type != "layer") {
                echo ("ERROR IN INIT FLIP '$type' is not 'layer' <br>");
                continue;
            }
            if (intval($id) != $this->contentId) {
                echo ("ERROR IN INIT FLIP '$id' is not '$this->contentId' <br>");
                continue;
            }
            
            $addTo = "layer_".$nr;
            if (is_array($this->flipList[$addTo]) ) $this->flipList[$addTo] = array();
            
            $this->flipList[$addTo][$contentId] = $contentData;
            // $this->flipList[$addTo] = $contentData;
            // echo ("add Layer $contentId to $nr $addTo <br>");
           
        }
        
    }

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
    
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        // show_array($contentData);

        $res = array();

        // MainData

        $type = $data[typeSelect];
        $layerWidth = $data[layerWidth];
        $layerHeight = $data[layerHeight];
        $this->layerCount = $data[layerCount];


        //div_start("FlipFrame","border:1px solid #cfc");
        
        $this->flipNr = null;
        
        // GET AND CREATE LAYERCONTENT
        $this->flip_layerData();
        
        
       
        // foreach($data as $key => $value) echo ("$key => $value <br>");
        $this->typeSelect = $data[typeSelect];
        
        
        $divName = "cmsFlipFrame";
        if ($this->typeSelect) $divName .= " cmsFlipFrame_Type_".$this->typeSelect;
        div_start($divName,"position:relative;width:100%;");
        
        $this->typeSelect = $data[typeSelect];
        
        switch ($this->typeSelect) {
            case "roll"     : $this->flipShow_Roll(); break;         
            case "click"    : $this->flipShow_Click(); break;
            case "tab"      : $this->flipShow_Tab(); break;
            case "slider"   : $this->flipShow_Slider(); break;
            default :
                echo ("<h1> unkown Type ($typeSelect) in flip_show</h1>");
        }
        div_end("$divName");
        return 0;
    }
    
    
    function flipShow_Roll() {
        $flipData = array();
        $flipData[mainDivAdd] = " cmsFlipMainFrame_roll";
        $flipData[flipNr] = 1;
        $flipData[sameHeight] = 1;
        
        $flipData[mainDivStyle] = "width:100%;";
        
        $this->flipData = $flipData;
        
        $nr = 0;
        foreach ($this->layerData as $contentId => $layerValue) {
            $nr++;
            switch ($nr) {
                case 1 : $this->layerData[$contentId][addContentDiv] = " cmsFlipRollIn"; break;
                case 2 : $this->layerData[$contentId][addContentDiv] = " cmsFlipRollOut"; break;
                default :
                    $this->layerData[$contentId][dontShow] = 1; break;
            }
        }
        $this->flip_showContent();
    }
    
    function flipShow_Click() {
        $flipData = array();
        $flipData[mainDivAdd] = " cmsFlipMainFrame_click";
        $flipData[flipNr] = 1;
        $flipData[sameHeight] = 1;
        
        $flipData[mainDivStyle] = "width:100%;";


        foreach ($this->layerData as $contentId => $layerValue) {
            $this->layerData[$contentId][addContentDiv] = " cmsFlipClick";
        }

        $this->flipData = $flipData;
        $this->flip_showContent();
    }
    
    function flipShow_Tab() {
        $innerWidth = $this->innerWidth;
        $data = $this->contentData[data];
        
        $flipData = array();
        $flipData[mainDivAdd] = " cmsFlipMainFrame_tab";
        $flipData[sameHeight] = 1;
        
        $flipNr = $this->flipNr;
        if (!$flipNr) {
            $defaultFlip = 1;
            $flipNr = $defaultFlip;
        }
        
        $tabDirection = $data[tabDirection];
        if (!$tabDirection) $tabDirection = "hori";
        
        $tabWidth = $data[tabWidth];
        $tabHeight = $data[tabHeight];
        $tabFloat = $data[tabFloat];
       //  echo ("tab $tabDirection $tabWidth x $tabHeight $tabFloat");

        $mainTabStyle = "";
        $tabStyle = "";
        $contentStyle = "";

        $tabFloat = 0;

        $contentWidth = $this->innerWidth;
//        if ($tabFloat) {
//            $mainTabStyle .= "position:absolute;";
//        }


        switch($tabDirection) {
            case "hori" :
                $padding = 5;
                $border = 0;
                $paddingTab = 2;

                $innerWidth = $innerWidth - 2 * ($border + $padding);
                $contentStyle .= "width:".$innerWidth."px;";

                

                //  $mainTabStyle .= "width:".$frameWidth."px;";

                if ($tabHeight) {
                    $mainTabStyle .= "height:".$tabHeight."px;";

                    $tabStyle .= "height:".($tabHeight - 2*$paddingTab)."px;";
                    $tabStyle .= "line-height:".($tabHeight - 2*$paddingTab)."px;";

                }
                break;

            case "verti" :
                $tabWidth = $data[tabWidth];
                if (!$tabWidth) $tabWidth = 200;
                $mainTabStyle .= "width:".$tabWidth."px;float:left;display:inline-block;";
                $leftPos = $leftPos + $tabWidth;


                if ($tabHeight) $mainTabStyle.= "height:".(count($layerData)*$tabHeight)."px;";

                // $mainTabStyle .= "background-color:#f00;";
                $mainTabStyle .= "padding:0;margin:0px;";
                $padding = 5;
                // $tabWidth = $tabWidth-2*$padding;
                // Tab
                $tabStyle .= "display:block;";
                $tabStyle .= "margin:0;padding:".$padding."px;";
                $tabStyle .= "float:none;";
                $tabStyle .= "width:".($tabWidth-2*$padding)."px;";
                if ($tabHeight) $tabStyle .= "height:".$tabHeight."px;";

                if ($tabFloat) {    
                    $contentStyle .= "display:inline-block;position:relative;";
                } else {
                    $innerWidth = $innerWidth - $tabWidth;
                    $contentStyle .= "display:inline-block;position:relative;";

                }
                $contentStyle .= "width:".$innerWidth."px;padding:0;float:left;";
                $contentWidth = $contentWidth - $tabWidth;
                break;


        }

        $flipData[mainDivStyle] = $contentStyle;
        //  $flipData[innerWidth] = $innerWidth;
        
        $mainTabData = array();
        $mainTabData["style"] = $mainTabStyle;
        $mainTabData["id"] = $this->contentId;

        
        $out = "";
        $out .= div_start_str("cmsFlipFrame_tabFrame cmsFlipFrame_tabFrame_".$this->contentId,$mainTabData);
        foreach ($this->layerData as $layerId => $layerContent) {
            $name = $layerContent[layerName];
            $title = $layerContent[title];
            if (is_array($title)) $helpTitle = $title;
            else $helpTitle = str2array($title);
            $title = $this->lgStr($helpTitle);
            if ($title) $name = $title;

            if (!$name) $name = "noName $layerId";
            $divData = array();
            $divData[style] = $tabStyle;
            $myFlipNr = $layerContent[flipNr];
            $divName = "cmsFlipFrame_tab cmsFlipFrame_tab_".$myFlipNr;

            if ($myFlipNr == $flipNr) $divName .= " cmsFlipFrame_tab_selected";
            $out .= div_start_str($divName,$divData); // ,array("id"=>$myFlipNr,"style"=>$tabStyle));
            $out .= $name;
            $out .= div_end_str($divName);
        }
        $out .= div_end_str("cmsFlipFrame_tabFrame cmsFlipFrame_tabFrame_".$this->contentId,"before");
        
        $flipData[beforeOut] = $out;
        $flipData[flipNr] = $defaultFlip;
        $flipData[contentWidth] = $this->innerWidth - $tabWidth;
        
        
        $this->flipData = $flipData;
        //  foreach ($this->flipData as $key => $value) echo ("FlipData $key => $value <br>");
        $this->flip_showContent();
    }
    
    
    function flipShow_Slider() {
        $flipData = array();
        $flipData[mainDivAdd] = " cmsFlipMainFrame_slider";
        $flipData[flipNr] = 1;
        $flipData[sameHeight] = 1;
        
        $data = $this->contentData[data];
        
        $contentList = array();
        foreach ($this->layerData as $key => $value) {
            $sliderId = "cmsFlipContent_".$this->contentId."_".$value[flipNr];
            // echo ("SLIDER ID $sliderId <br>");
            // $contentList[] = $sliderId;
        }
        
        $name = "cmsFlipSlider_".$this->contentId;
        $showData = array();
        
        $showData[loop] = 1;
        $showData[notloop] = 1;
        $showData[direction] = "fade" ; // array("vertical","horizontal","fade")[2];

        $showData[speed] = 1000;
        $showData[pause] = 2000;
        $showData[startFrame] = 2;

        $showData[navigate] = 1;
        $showData[pager] = 1; 

        $width = $frameWidth;
        $height = null;
        $outPut = 0;
        
        
        $showData = array();
        $showData[width] = $this->innerWidth;
        
        $flipData[beforeOut] = slider::start($name,$this->contentData,$showData); // cmsSlider_start(null, $name, $contentList, $showData, $width, $height, $outPut);
        $flipData[afterOut]  = slider::end($name,$this->contentData,$showData); // cmsSlider_end(null, $name, $contentList, $showData, $width, $height, $outPut);
        
        $flipData[hideNotFlipNr] = 1;
        
        
        $this->flipData = $flipData;
        $this->flip_showContent();
    }

    
    function flip_showContent () {
        
        
        $this->flip_showFrameSelect();
        
        $flipNr = $this->flipNr;
        if (!$flipNr) $flipNr = $this->flipData[flipNr];
        
        $data = $this->contentData[data];
        
        $divNameMain ="cmsFlipMainFrame cmsFlipMainFrame_".$this->contentId;
        $divMainAdd = $this->flipData[mainDivAdd];
        if ($divMainAdd) $divNameMain .= $divMainAdd;
            
        if ($this->flipData[sameHeight]) $divNameMain .= " cmsFlipMainFrame_sameHeight";
        
        $mainStyle = $this->flipData[mainDivStyle];
        $contentStyle = $this->flipData[contentStyle];
        
        
        $width = $this->innerWidth;
        if ($this->flipData[contentWidth]) $width = $this->flipData[contentWidth];
        
        
        
        $tabDirection = $data[tabDirection];
        if (!$tabDirection) $tabDirection = "hori";
        switch ($tabDirection) {
            case "verti" :
                $tabWidth = $data[tabWidth];
                // foreach ($data as $key => $value) echo ("data = $key = $value <br>");
                $width = $width - $tabWidth;
                $leftPos = $leftPos + $tabWidth;
                // $contentStyle .= "width:".($this->innerWidth-$tabWidth)."px;";
                // $contentStyle .= "display:inline-block;float:left;";
                break;
            case "hori" :
                break;
                
        }
                
        $divDataMain = array();
        $divDataMain[id] = $this->contentId;
        if ($contentStyle) $divDataMain[style] = $contentStyle;
        
        div_start($divNameMain,$mainStyle);
        
        echo ("<div class='hiddenData cmsFlipMainFrame_".$this->contentId."_count'>$this->layerCount</div>");
        echo ("<div class='hiddenData cmsFlipMainFrame_".$this->contentId."_active'>$flipNr</div>");
       
        $beforeOut = $this->flipData[beforeOut];
        if ($beforeOut) echo ($beforeOut);
        
        
        foreach ($this->layerData as $layerId => $layerContent) {
            $layerNr = $layerContent[flipNr];
            
            $addContentDiv = $layerContent[addContentDiv];
            $dontShow = $layerContent[dontShow];
            if ($dontShow) continue;
            
            $layerType = $data["layer_".$layerNr."_type"];

            $divName = "cmsFlipContent cmsFlipContent_".$this->contentId." cmsFlipContent_".$this->contentId."_".$layerNr;
            
            if ($addContentDiv) $divName .= $addContentDiv;
            // $myFlipNr = $contentId."_".$layerNr;
            // $useFlip = $contentId."_". 
            switch ($this->typeSelect) {
                case "slider" :
                    break;
                default : 
                    if ($layerNr != $flipNr) $divName .= " cmsFlipContent_hidden";
            }
           
            div_start($divName,$contentStyle);
            //echo ("<h2>FLIP $layerNr</h2>");
            $pageId = "layer_".$this->contentId."_".$layerNr;
           
            $this->pageClass->content_show_flipContent($pageId,$width,$this->contentData[viewContent],$leftPos);

            div_end($divName);

        }
        
        $afterOut = $this->flipData[afterOut];
        if ($afterOut) echo ($afterOut);
    
        div_end($divNameMain);
    }
    
    
    

    
    function flip_layerData() {
        $layerData = array();
        $data = $this->contentData[data];
        $infoText = "";
        $errorText = "";
        for ($layerNr=1;$layerNr<=$this->layerCount;$layerNr++) {
            $pageId = "layer_".$this->contentId."_".$layerNr;
            $layerName = $data["layer_".$layerNr."_name"];
            $layerType = $data["layer_".$layerNr."_type"];
            
            // echo ("LayerName = '$layerName' Nr=$layerNr Typ = $layerType <br>");
            $layerContent = cms_content_get(array("pageId"=>$pageId));
            
            $flipCode = "layer_".$i;
           //  $layerContent = $this->flipList[$flipCode];
            
            
            if (!is_array($layerContent)) {
                
                if ($layerType) {
                    $layerContent = array();
                    $layerContent["pageId"] = $pageId;
                    $layerContent["type"] = $layerType;
                    $layerId = cms_content_save(null, $layerContent);
                    if ($layerId) $infoText .= "Create Content for $pageId <br>"; // cms_infoBox("Create Content for $pageId");
                    else $errorText .= "Fehler beim erstelen von $pageId <br>";
                    $layerContent[id] = $layerId;                    
                } else {
                    $errorText .= "Kein Modul ausgewählt für Layer $layerNr ";                    
                }
            } 
            
            if (is_array($layerContent)) {
                $layerId = $layerContent[id];
                
                if ($this->editId == $layerId) {
                    echo ("this Content is in EditMode <br>");
                    $this->flipNr = $layerNr;
                }
                    
                
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
        // $this->layerContent = $layerContent;
        $this->layerData = $layerData;        
    }

    function flip_showFrameSelect() {
        
        if (!$this->pageEditAble) return 0;
        
        $frameWidth = $this->innerWidth;
        $contentData = $this->contentData;
         
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $contentId = $this->contentId; 
        
        $divSelectName = "cmsFlipSelectFrame";
        $divSelectName .= " cmsEditToggle";
        if (!$this->edit) $divSelectName .= " cmsEditHidden";
        
        div_start($divSelectName,array("id"=>"cmsFlipSelect_".$this->contentId));

        div_start("cmsFlipTitleFrame","float:left;");
        echo ("Wählen Sie den Inhalt aus: ");
        div_end("cmsFlipTitleFrame");


        $useLink = 0;

        $flipNr = $this->flipNr;        
        // if (!$flipNr AND $this->flipData[flipNr]) $flipNr = $this->flipData[flipNr]; 
        
        // if (!$this->flipNr) $flipNr = 1;
        for ($i=1;$i<=$this->layerCount;$i++) {
            $divName = "cmsFlipFrameSelector";
            if ($i == $flipNr) $divName .= " cmsFlipFrameSelectorSelected";
            $divName .= " cmsFlipSelector_$this->contentId cmsFlipSelector_".$this->contentId."_".$i;

            $divData = array();
            // $divData[style] = "float:left;";
            // $divData[name] = "cmsFlipContent_".$contentId; // "select_".$flipId."_".$i;
            $divData[title] = $this->contentId."|".$i;
            // $divData[id] = $i;

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
      

      
    }
    
    
    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;

    //function flip_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
//        $res[showName] = "Wechselnde Inhalte";
//        $res[showTab] = "Admin";

        // MainData

        $lgType = "contentType_flip";


        $res["flip"] = array();
        $res["flip"][showTab] = "Simple";
        $res["flip"][showName] = lg::lga($lgType,"tabName");//Spalten"; 
        
        $type = $data[typeSelect];
//        echo ("<h1> TYP = '$type' </h1>");
//        foreach ($data as $key => $value) echo ("$key = $value <br />");
        if (!$type) $type = "roll";
        $addData = array();
        $addData["text"] = lg::lga($lgType,"flipType","","Wechel-Art");
        $addData["input"] = $this->flip_selectType($type,"editContent[data][typeSelect]",array("onChange"=>"submit()"));
        $addData["mode"] = "Simple";
        $res["flip"][] = $addData;

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
                $res["flip"][] = $addData;
                break;

            case "slider" :
                $addData = array();
                $addData["text"] = lg::lga($lgType,"layerCount","","Anzahl Ebenen");
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' style='width:70px;' value='$layerCount'>";
                $addData["mode"] = "Simple";
                $res["flip"][] = $addData;
                break;

            case "click" :
                $addData = array();
                $layerCount = $data[layerCount];
                if (!$layerCount) $layerCount = 2;
                $addData["text"] = lg::lga($lgType,"layerCount","","Anzahl Ebenen");
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' value='$layerCount'> in Milli-Sekunden";
                $addData["mode"] = "Simple";
                $res["flip"][] = $addData;
                break;

            case "tab" :
                $addData = array();
                $layerCount = $data[layerCount];
                if (!$layerCount) $layerCount = 2;
                $addData["text"] = lg::lga($lgType,"layerCount","","Anzahl Ebenen");
                $addData["input"] = "<input type='text' name='editContent[data][layerCount]' value='$layerCount'>";
                $addData["mode"] = "Simple";
                $res["flip"][] = $addData;
                
                $addData = array();
                $addData["text"] = lg::lga($lgType,"tabDirection","","Ausrichtung Tabs");
                
                $selectList = array();
                $selectList["hori"] = "lga";
                $selectList["verti"] = "lga";
                
                $showData = array("viewMode"=>"selectIcon","selectList"=>$selectList,"emptyText"=>0);
                $addData["input"] = $this->editContent_selectSettings("direction",$data[tabDirection], "editContent[data][tabDirection]", $showData);
                $addData["mode"] = "Simple";
                $res["flip"][] = $addData;
                
                
                $addData = array();
                $addData["text"] = lg::lga($lgType,"tabFloat","","Tabs überlagernd");
                $tabFloat = $data[tabFloat];
                if ($tabFloat) $checked="checked='checked'";
                else $checked = "";
                $addData["input"] = "<input $checked type='checkbox' name='editContent[data][tabFloat]'  value='1' />";
                $addData["mode"] = "More";
                $res["flip"][] = $addData;
                
                $addData = array();
                $tabWidth = $data[tabWidth];
                if (!$tabWidth) $tabWidth = 200;
                $addData["text"] = lg::lga($lgType,"tabWidth","","Breite Tab");
                $addData["input"] = "<input type='text' name='editContent[data][tabWidth]' value='$tabWidth'>";
                $addData["mode"] = "More";
                $res["flip"][] = $addData;
                
                $addData = array();
                $tabHeight = $data[tabHeight];
                if (!$tabHeight) $tabHeight = 20;
                $addData["text"] = lg::lga($lgType,"tabHeight","","Höhe Tab");
                $addData["input"] = "<input type='text' name='editContent[data][tabHeight]' value='$tabHeight'>";
                $addData["mode"] = "More";
                $res["flip"][] = $addData;
                
                break;
                
        }
        
        $addData = array();
        $addData["text"] = lg::lga($lgType,"sameHeight","","Gleiche Höhe");
        $sameHeight = $data[sameHeight];
        if ($sameHeight) $checked="checked='checked'";
        else $checked = "";
        $addData["input"] = "<input $checked type='checkbox' name='editContent[data][sameHeight]'  value='1' />";
        $addData["mode"] = "More";
        $res["flip"][] = $addData;


        for ($i=1;$i<=$layerCount;$i++) {
            $addTo = "layer_$i";
            $strType = lg::lga($lgType,"contentType","","InhaltTyp");
            $contList = $this->flipList[$addTo];
            if (is_array($contList) AND count($contList)) {
                
                // echo ("Found $contList $addTo <br>");
                foreach ($contList as $contId => $flipContent ) {

                    $layerType = $flipContent[type];

                    $addData = array();
                    $addData["text"] = $strType." $i";
                    if (user::userLevel()>8) $addData[text] .= "/$contId";
                    $showData=array("viewMode"=>"select","width"=>100,"id"=>$addTo,"useName"=>1);

                    $selectType = $this->editContent_SelectSettings("contentType", $layerType, "editContent[data][layer_".$i."_type]", $showData);
                    $input = $selectType;
        //            $input = "<div id='$addTo' style='height:20px;width:100px;display:inline-block;' class='layerDrop ui-droppable'>$layerName</div>";
        //            $input .= "<input id='layer_".$i."_type' type='hidden' name='editContent[data][layer_".$i."_type]' value='$layerType' />";
        //
                    switch ($type) {
                        case "tab" :
                            // $flipName = "layer_".$this->contentId."_".$i;

                            if (is_array($flipContent)) {
                                $flipTitle = $flipContent[title];
                                $flipContentId = $flipContent[id];
                                $showData=array("out"=>"input","width"=>100);
                                $input .= $this->editContent_languageString($flipTitle,"updateOtherContent[$flipContentId][title]", $showData);
                            }

                            // $input .= "<input type='text' name='editContent[data][layer_".$i."_name]' value='".$editContent[data]["layer_".$i."_name"]."' />";
                            break;
                    }


                    $addData["input"] = $input;
                    $addData["mode"] = "Simple";
                    $res["flip"][] = $addData;

                }
            } else {
                $layerType = $data["layer_".$i."_type"];
                if ($layerType) {
                    $layerName = $typeList[$layerType];
                    if (is_array($layerName)) $layerName = $layerName[name];
                    if (!$layerName) $layerName = "unbekannt ($layerType)";

                } else {
                    $layerName = "nicht definiert";
                }



                $addData = array();
                $addData["text"] = $strType." $i";
                $showData=array("viewMode"=>"select","width"=>100,"id"=>$addTo);

                $selectType = $this->editContent_SelectSettings("contentType", $layerType, "editContent[data][layer_".$i."_type]", $showData);
                $input = $selectType;
    //            $input = "<div id='$addTo' style='height:20px;width:100px;display:inline-block;' class='layerDrop ui-droppable'>$layerName</div>";
    //            $input .= "<input id='layer_".$i."_type' type='hidden' name='editContent[data][layer_".$i."_type]' value='$layerType' />";
    //
                switch ($type) {
                    case "tab" :
                        $flipName = "layer_".$this->contentId."_".$i;
                        $flipContent = cms_content_getList($flipName);
                        if (is_array($flipContent)) {
                            foreach ($flipContent as $key => $value) {
                                echo ("$flipName = cont $key = $value <br>");
                                $flipTitle = $value[title];
                                $flipContentId = $value[id];
                                $showData=array("out"=>"input","width"=>40);
                                $input .= $this->editContent_languageString($flipTitle,"updateOtherContent[$flipContentId][title]", $showData);
                            }
                        }

                        //$input .= "<input type='text' name='editContent[data][layer_".$i."_name]' value='".$editContent[data]["layer_".$i."_name"]."' />";
                        break;
                }


                $addData["input"] = $input;
                $addData["mode"] = "Simple";
                $res["flip"][] = $addData;


            }
            
        }
        
        switch ($type) {
            case "slider" :
                $res["flip"][] = array("text"=>"&nbsp;","input"=>"<b>".lg::lga($lgType,"sliderSettings")."</b>","mode"=>"simple");
                $addList = slider::input($editContent);
                foreach ($addList as $key => $value) {
                    if (is_array($value)) {
                        $res["flip"][] = $value;
                    }
                }
                break;
        }
        return $res;
    }


    function flip_selectType($type,$dataName,$dataAction=array()) {
        $typeList = array();
        $typeList[roll] = array("name"=>"Maus über Rahmen");
        $typeList[slider] = array("name"=>"Zeitgesteuert");
        $typeList[click] = array("name"=>"Maus Klick");
        $typeList[tab] = array("name"=>"Tabs");
        
        $lgType = "contentType_flip";
        foreach ($typeList as $key => $value) {
            $typeList[$key][name] = lg::lga($lgType,"type_".$key,"",$value[name]);
        }

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
    $res = $flipClass->show($contentData,$frameWidth);
    return $res;
}


function cmsType_flip_editContent($editContent,$frameWidth) {
    $flipClass = cmsType_flip_class();
    return $flipClass->flip_editContent($editContent,$frameWidth);
}





?>
