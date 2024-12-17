<?php

class cmsClass_content_show extends cmsClass_content_showData {

    function content_show($contentData,$frameWidth) {
        $this->init_content($contentData,$frameWidth);

        $this->divAdd = "";
        $show = $this->content_showContent();
        $show = $this->show_hiddenContent($show,"content");
        if (!$show) return 0;
       
        $showContent = $this->show_frame();
        
        if ($showContent) {
            $this->show_spacer();
        }
        return $this;
    }

    function layout_show($contentData,$frameWidth) {
        $this->init_layout($contentData,$frameWidth);
//        echo ("<h2>cmsClass_content_show ->layout_show() </h2>");
        $this->divAdd = "";
        $show = $this->content_showContent();
        // echo ("Layout show $show ".$this->targetData[target]."<br>");
        $show = $this->show_hiddenContent($show,"layout");
        if (!$show) return 0;
      

        $this->show_frame();

        $this->show_spacer();
        return $this;
    }
    
    function show($contentData,$frameWidth) {
        echo ("<h2>cmsClass_content_show ->show()  </h2>");
        $this->init_content($contentData,$frameWidth);

        $this->show_frame();

        $this->show_spacer();
        return $this;
    }
    
    
    function show_hiddenContent($show,$contentType) {
        if (!is_string($show)) return $show;
        
        switch ($show) {
            case "minLevel"    : $exit = 1; break;
            case "maxLevel"    : $exit = 1; break;
            case "mobileHide"  : 
                if ($this->targetData[target]=="pc") return 1;
                $exit = 1; 
                break;
            case "desktopHide" : $exit = 1; break;
            default :
                $exit = 1;
                $show = "unbekannter Grund ($show) für nichtzeigen";
        }
        
        if ($this->pageEditAble) {
            // if ($this->edit) 
            
            
            $showFrame = 1;
            if ($contentType == "content") {
                if ($this->layoutEdit) $showFrame = 0;                
            }
            if ($contentType == "layout") {
                if (!$this->layoutEdit) $showFrame = 0;
            }
            
            // echo "Show myType $contentType contentViewMode =  $this->contentViewMode layoutEsit = $this->layoutEdit => $showFrame<br>";
            
            
            if ($showFrame) {
                $hiddenClass = "cmsHiddenContent cmsEditToggle editMode_Admin";
                if (!$this->edit) $hiddenClass .= " cmsEditHidden";
                div_start($hiddenClass);
                $editable =  $this->show_editFrame_start();
                $this->show_editFrame_end();

                echo ("DONT SHOW : <b>$show</b> <br> ");

                div_end($hiddenClass);
            } else {
                // echo "DontShow Hidden Frame because $contentType != $this->contentViewMode <br>";
            }
        }    
        return 0;               
    }


   


    function content_showContent() {
        
        $levelShow = $this->content_showContent_Level();
        if ($levelShow) return $levelShow;
        
//        $mobile= $this->contentData[data][mobileShow];
//        $mobileShow = $this->content_showContent_mobile($mobile,);
//        if ($mobileShow) return $mobileShow;
//        
//        return 1;
//        
//        
//        $minLevel = $this->contentData[showLevel];
//        if ($minLevel AND $this->showLevel < $minLevel) return "minLevel";            
//        
//        $maxLevel = $this->contentData[toLevel];
//        if ($maxLevel AND$this->showLevel > $maxLevel)  return "maxLevel";               
        
        if ($this->mobileEnabled) {
            $mobileShow = $this->contentData[data][mobileShow];
            $mobileState = $this->targetData[target];
            
            // Not defined
            if (!$mobileShow) return 1;
            // echo ("Show MOBILE $mobileShow state = $mobileState <br>");
            if ($mobileState == "pc") {
                if ($mobileShow == "only") return "desktopHide";
            }
            
            switch ($mobileShow) {
                case "hide" : return "mobileHide";
                case "show" : return 1;
                case "only" : return 1;
                case "landscape" :
                    $this->divAdd .= " hidePortrait";
                    if ($this->targetData[orientation] != $mobileShow) $this->divAdd .= " orientationHidden";
                    return 1;
                
                case "portrait" :
                    $this->divAdd .= " hideLandscape";
                    if ($this->targetData[orientation] != $mobileShow) $this->divAdd .= " orientationHidden";
                    return 1;
                    
                default :
                    echo ("unkown Mobile SHow ($mobileShow) state = $mobileState <br>");
                    
            }
        }
        return 1;
    }
    
    function content_showContent_Level() {
        $minLevel = $this->contentData[showLevel];
        if ($minLevel AND $this->showLevel < $minLevel) return "minLevel";            
        
        $maxLevel = $this->contentData[toLevel];
        if ($maxLevel AND$this->showLevel > $maxLevel)  return "maxLevel";  
        return 0;
    }
    
    function content_showContent_mobile($mobileShow,$out="") {
        if (!$mobileShow) return 1;
        if (!$this->mobileEnabled) return 1;
        
        
        if ($this->targetData[target] == "pc" ) { // DESKTOP ANSICHT
            if ($mobileShow == "only") return 0;
            return null;
        }
        
        $div = "";
        $show = 1;
        switch ($mobileShow) {
            case "hide" : $show=0; break;
            case "show" : break;
            case "only" : break;
            
            case "landscape" :
                $div .= " hidePortrait";
                if ($this->targetData[orientation] != $mobileShow)  $div .= " orientationHidden";
                break;
                
            case "portrait" :
                $div .= " hideLandscape";
                if ($this->targetData[orientation] != $mobileShow)  $div .= " orientationHidden";
                break;
                
            default :
                echo ("unkown mobilShow($mobileShow) ind cmsClass_contentShow->content_showContent_mobile()<br />");
        }
        
        if ($out == "div") {
            if ($div) return $div;
        }
        return $show;
    }
    
    
    function show_frame() {
        
        $frameWidth = $this->frameWidth;


        $showType = cms_contentType_checkType($this->contentType);
        
        // echo ("Show ".$this->contentData[type]." showType=$showType <br />");
        if (is_string($showType)) {
            $type = $this->contentData[type];
            cms_errorBox("Fehler beim Anzeigen von Modul $type <br />$showType");
            return 0;
        }

        if (!$showType) {
            if ($this->pageEditAble) {
                $className = "cmsContent_disabled";
                $className .= " cmsEditToggle";
                if (!$this->edit) $className .= " cmsEditHidden";
                echo ("<div class='$className'>");
        
                $type = $this->contentData[type];
                // foreach($_SESSION as $key => $value) echo ("$key = $value <br />");
                cms_errorBox("Das Anzeigen des Modules '$type' ist nicht aktiviert!<br />Aktivieren Sie das Modul unter CMS-Einstellungen.");
                echo ("</div>");
            }
            return 0;
        }

       
        $isContentName = 0;
        $editAble = $_SESSION[userLevel]>6;

        $pageEditAble = $this->pageEditAble;

        // Edit FRAME 
        $editAble = $this->show_editFrame_start();
        
        //  echo ("id = ".$this->contentData[id]." ".$this->contentData[type]." ".$this->contentData[mainId]." left=".$this->contentData[leftPos]." <br>");
        
        // GET FRAME DATA
        $this->show_frame_data();
        
        $this->closeInFrame = 0;
         // Frame CloseAble
        if ($this->closeInFrame == 0) {
            $closeAble = $this->show_closeFrame_start();
        }
        
        // FRAME START
        $this->show_content_frame_start();

        if (is_string($special_before[output])) {
            echo ($special_before[output]);
        }

         // FRAME TEXT - Überschrift
        $res = $this->show_frameText("frameHeadline");
        if ($res) echo ($res);
       
        // FRAME TEXT - Text Oben
        $res = $this->show_frameText("frameHeadtext");
        if ($res) echo ($res);
       

        //  zeig Inhalt
        if (method_exists ($this ,"contentType_show")) {
            $this->contentType_show($this->contentData,$this->innerWidth);
        } else {
            echo ("<h1>function contentType_show not exist for $this->contentName ($this->contentType)</h1>");
        }


        // FRAME TEXT - UNTEN
        $res = $this->show_frameText("frameSubtext");
        if ($res) echo ($res);
       
        if (is_string($special_after[output])) {
            echo ($special_after[output]);
        }

        // Close Content Div
        $this->show_content_frame_end();

        // Close Edit Frame
        if ($editAble) $this->show_editFrame_end();
        return 1;
    }
    
    
    

    function show_editFrame_start() {
        switch ($this->contentViewMode) {
            case "content" :
                if ($this->layoutEdit) return 0;
                break;
            case "layout" :
                if (!$this->layoutEdit) return 0;
                break;
        }
        
        
//        $specialType = $this->contentData[specialView];
//        if ($specialType) switch ($specialType) {
//            case "contentName" :
//                $contentNameId = $this->contentData[specialId];
//                echo ("CONTENT EDIT 'contentName' specialId = $contentNameId orgId = $this->contentId<br />");
//                if ($contentNameId) return 0;
//                break;
//            default :
//                // echo ("unkown SpecialType $specialType in editframe_start()");
//        }

        $dragAble = 1;
        if (!$this->pageEditAble) return 0;
        
        
        $editFrameClass = "cmsContentFrameBox";
        if ($this->doEdit) $editFrameClass .= " cmsContenFrameBoxActive";
        
        echo ("<div class='$editFrameClass dragBox' id='dragContent_".$this->contentId."' >");
        
        
        if ($isContentName) {
                 // echo ("dont show head of ContentName '$contentData[contentName]' <br />");

                // $tempContent = cms_contentType_head($contentData,$this->frameWidth);
        } else {
            
            $this->edit_content();
                
            if ($this->tempContent) {
                echo ("<b>TEMP CONTENT $this->tempContent</b>");
            }
        }
        return 1;
    }
    
    function show_editFrame_end() {
        switch ($this->contentViewMode) {
            case "content" :
                if ($this->layoutEdit) return 0;
                break;
            case "layout" :
                if (!$this->layoutEdit) return 0;
                break;
        }

        if ($this->doEdit) {
            $this->show_editFrame_endData();
        }

        echo ("</div>");
    }

    
    function show_content_frame_start() {
        
        
        $contentId = $this->contentDataId;
        $type = $this->contentType;
        $data = $this->contentData[data];
        
        
        
//     
        $special_before = cmsFrame_getSpecial_before($frameStyle,$this->contentData,$this->innerWidth,$this->textData);
        $special_after  = cmsFrame_getSpecial_after($frameStyle,$this->contentData,$this->innerWidth,$this->textData);

        //echo ("fw = $frameWidth $marginLeft $marginRight $borderLeft / $borderRight $paddingLeft / $paddingRight <br>");
        if (is_array($special_before)) {
            //echo ("<h1>Special Before</h1>");
            // show_array($special_before);
        }

        if (is_array($special_after)) {
            // echo ("<h1>Special After</h1>");
            //show_array($special_after);
        }


        // Frame CloseAble
        // $closeAble = $this->show_closeFrame_start();
        
        $divData = array();

        $divStyle = "width:".$this->innerWidth."px;";
        $divStyle = "";
        if ($this->setInnerWidth) $divStyle .="width:".$this->setInnerWidth."px;";
        if ($this->innerHeight) $divStyle .="height:".$this->innerHeight."px;";
      
        if ($this->ownPaddingLeft) $divStyle .= "padding-left:".$this->ownPaddingLeft."px;";
        if ($this->ownPaddingRight) $divStyle .= "padding-right:".$this->ownPaddingRight."px;";
        if ($this->ownPaddingTop) $divStyle .= "padding-top:".$this->ownPaddingTop."px;";
        if ($this->ownPaddingBottom) $divStyle .= "padding-bottom:".$this->ownPaddingBottom."px;";
        
        
        
        if ($edit) $divStyle .= "min-height:16px;";
        //$frameFloat = $this->contentData[frameFloat];
        //if ($frameFloat AND $frameFloat != "none")  $divStyle .= "float:$frameFloat;";
        // echo ($divStyle."<br />");
        $divData[style] = $divStyle;

        $paddingTop = $this->contentData[data][paddingTop];
        if ($paddingTop) {
            //  echo ("Padding Top ".$padding."<br />");
            $divData[style] .= "padding-top:".$paddingTop."px;";
        }

        $divName = "cmsContent cmsContentFrame_$this->contentId";
        
        if ($this->divAdd) $divName.= $this->divAdd;
        
        if ($this->frameClose) $divName .= " cmsContentFrame_hidden";
        
        $divName .= " ".$type."_box ".$type."_boxId_$this->contentId ".$type."_boxPage_".$this->pageName;

        $frameLink = $this->contentData[frameLink];
        if ($frameLink AND $frameLink != "noLink") {
            $offSet = strpos($frameLink,"|");
            if ($offSet) {
                $goPageId = intval(substr($frameLink,0,$offSet));
                $goPageAdd = substr($frameLink,$offSet+1);
            } else {
                $goPageId = intval($buttonLink);
                $goPageAdd = "";
            }
            if ($goPageId) {
                $pageData = $this->page_getData($goPageId);
                if (is_array($pageData)) {
                    $frameLink = $pageData[name].".php";
                    if ($goPageAdd) $frameLink .= "?".$goPageAdd;
                    
                    
                    $divName .= " cmsFrameLink";
                    $divData["link"] = $frameLink;
                }
            }            
        }
        $divData[id] = "inh_".$this->contentId;
        
        $frameStyle  = $this->contentData[frameStyle];
        if ($frameStyle) $divName .= " $frameStyle";
        
        div_start($divName,$divData);
        $this->contentDivName = $divName;
        return $divName;        
    }
    
    function show_content_frame_end() {
         div_end($this->contentDivName,"before");
    }
    
    
    function show_frame_data() {
        $frameStyle  = $this->contentData[frameStyle];
        $frameWidth  = $this->contentData[frameWidth];
        $frameHeight = $this->contentData[frameHeight];
        
                
        $ownFrameWidth = cms_getWidth($frameWidth,$this->frameWidth);
        $ownFrameHeight = cms_getWidth($frameHeight,$this->frameWidth);
      
        $this->ownPaddingLeft    = cms_getWidth($this->contentData[data][frameAbsLeft],$this->frameWidth);
        $this->ownPaddingRight  = cms_getWidth($this->contentData[data][frameAbsRight],$this->frameWidth);
        $this->ownPaddingTop    = cms_getWidth($this->contentData[data][frameAbsTop],$ownFrameHeight);
        $this->ownPaddingBottom = cms_getWidth($this->contentData[data][frameAbsBottom],$ownFrameHeight);
        
        
        if (!$frameStyle) $frameStyle = "noFrame";
        $frameSettings = cmsFrame_getSettings($frameStyle);

        // border
        $borderLeft  = $frameSettings[border];
        $borderRight = $frameSettings[border];
        $borderData   = $frameSettings[borderData];
        if (is_array($borderData)) {
            $borderLeft   = $borderData[left];
            $borderRight  = $borderData[right];
            $borderTop    = $borderData[top];
            $borderBottom = $borderData[bottom];
        }
        // PADDING
        $paddingLeft = $frameSettings[padding];
        $paddingRight = $frameSettings[padding];
        $paddingData = $frameSettings[paddingData];
        if (is_array($paddingData)) {
            $paddingLeft   = $paddingData[left];
            $paddingRight  = $paddingData[right];
            $paddingTop    = $paddingData[top];
            $paddingBottom = $paddingData[bottom];
        }
        // MARGIN
        $marginLeft = $frameSettings[spacing];
        $marginRight = $frameSettings[spacing];
        $marginData = $frameSettings[marginData];
        if (is_array($marginData)) {
            $marginLeft   = $marginData[left];
            $marginRight  = $marginData[right];
            $marginTop    = $marginData[top];
            $marginBottom = $marginData[bottom];
        }
        
        if ($this->ownPaddingLeft+$this->ownPaddingRight > 0) {
            $paddingLeft = $paddingLeft + $this->ownPaddingLeft;
            $paddingRight = $paddingRight + $this->ownPaddingRight;
            
        }
        if ($this->ownPaddingTop+$this->ownPaddingBottom > 0) {
            
        }
        
        if ($ownFrameHeight) {
            $myHeight = $ownFrameHeight;
            if ($marginTop) $myHeight = $myHeight - $marginTop;
            if ($marginBottom) $myHeight = $myHeight - $marginBottom;

            if ($borderTop) $myHeight = $myHeight - $borderTop;
            if ($borderBottom) $myHeight = $myHeight - $borderBottom;

            if ($paddingTop) $myHeight = $myHeight - $paddingTop;
            if ($paddingBottom) $myHeight = $myHeight - $paddingBottom;
            $this->innerHeight = $myHeight;
            // echo ("myHeight = $myHeight ($ownFrameHeight) m= $marginTop / $marginBottom b= $borderTop / $borderBottom p= $paddingTop / $paddingBottom <br>");            
        }
        
        
        $useWidth =$this->frameWidth;
        if ($ownFrameWidth) {
            $useWidth = $ownFrameWidth;
            $this->setWidth = $useWidth;
        }
    //    show_array($frameSettings);
        // echo("Bo / pa / sp $border $padding $spacing $this->frameWidth<br>");
        $this->innerWidth = $useWidth - ($marginLeft+$marginRight) - ($borderLeft+$borderRight) - ($paddingLeft + $paddingRight);
        
        if ($ownFrameWidth) {
            $this->setInnerWidth = $this->innerWidth;
        }
        
        // echo ("New innerWidth  $this->innerWidth frame =$this->frameWidth ($marginLeft $marginRight) - ($borderLeft $borderRight)  pad = $paddingLeft $paddingRight <br>");
    }
    
    function show_closeFrame_start() {
        $this->frameClose = 0;
        $data = $this->contentData[data];
        $closeable = $data[frameClose];
        
        if (!$closeable) return 0;
        
   
        // if ($data[frameClose]) {
        $closeState = "open";
        if ($data[frameCloseLoad]) {
            $this->frameClose = 1;
            $closeState = "close";
        }

        $useWidth = "auto";
        if ($this->frameWidth) $useWidth = $this->frameWidth."px";
        // if ($this->setWidth) $useWidth = $this->setWidth."px";
        
        $openCloseText = $data[frameCloseText];
        if (is_array($openCloseText)) $openCloseText = $this->lgStr($openCloseText);
        if (!$openCloseText) $openCloseText = "&nbsp;";
        div_start("cmsContentFrame_".$closeState,array("style"=>"width:$useWidth;display:block;","id"=>$this->contentId));
        div_start("cmsContentFrame_".$closeState."_text");
        $openCloseText = $this->showText($openCloseText);
        echo ($openCloseText);
        div_end("cmsContentFrame_".$closeState."_text");
        div_start("cmsContentFrame_".$closeState."_button");
        // echo ("o");
        div_end("cmsContentFrame_".$closeState."_button");
        div_end("cmsContentFrame_".$closeState,"before");
        return 1;
    }
    
    function show_frameText_mobileCheck($textType,$show) {
        $mobileShow = $this->contentData[data]["mobilShow".$textType];
        if (!$mobileShow) return $show;
        
        $target = $this->targetData[target];
        if ($target == "pc") {
            if ($mobileShow == "only") return 0;
            return $show;
        }
        // IST MOBIL
        
        switch ($mobileShow) {
            case "only" : return 1;
            case "hide" : return 0;
            case "show" : return 1;
            
            case "landscape" :
                $res = " hidePortrait";
                if ($this->targetData[orientation] != $mobileShow) $res .= " orientationHidden";
                return $res;

            case "portrait" :
                $res = " hideLandscape";
                if ($this->targetData[orientation] != $mobileShow) $res .= " orientationHidden";
                return $res;
                    
            case "landscape" :
                
                
                
                return "Trulla";
                
            case "portrait" :
                return "tralla";
        }
        
        
        echo ("CHECK MOBIL $textType tar=$target $mobileShow <br> ");
    }
    
    function showText($text,$maxChar=0,$readMore=1) {
        if (!$maxChar) {
            $text = cmsText_clearOutPut($text);
            return $text;
        }
        
        $long = strlen($text);
        
        if ($long <= $maxChar) {
            $text = cmsText_clearOutPut($text);
            return $text;
        }
        
        $end = strpos($text," ",$maxChar);
        if (!is_integer($end)) {
            $str .= "NO INTEGER von END <br>";
            $str .= cmsText_clearOutPut($text);
            return $str;
        }
        
        $shortText = substr($text,0,$end);
        $longText = substr($text,$end+1);
        
        $shortText = $this->showText($shortText);
        $longText = $this->showText($longText);
        
        if ($readMore) {
            $str .= "<div class='cmsText_readMore' >";
            $str .= "<div class='cmsText_readMore_short'>";
            $str .= $shortText." ...";
            $str .= "</div>";
            $str .= "<div class='cmsText_readMore_button'>mehr Lesen</div>";
            
            
            $str .= "<div class='cmsText_readMore_all cmsText_readMore_hidden'>";
            $str .= cmsText_clearOutPut($text);
            $str .= "</div>";
            $str .= "<div class='cmsText_readLess_button cmsText_readMore_hidden'>ausblenden</div>";
            
            $str .= "</div>";
            return $str;
        }
        
        $str .= $shortText." ...";
        return $str;
        
        
        $str .= "short:'$shortText'<br />";
        $str .= "long:'$longText'<br />";
        
//        
//        $str .= "maxChar=$maxChar readMore=$readMore long= $long<br>";
//        
//        $str .= $text;
        
        
        return $str;
        
        
        
        
        
    }
    
    
    function show_frameText($textType) {       
        if (!is_array($this->textData[$textType])) return 0;
        
        
        $text = $this->textData[$textType][text];
        if (!$text) return 0;
        
        // Text Availble
        $show = 1;
        if ($this->mobileEnabled) {
            $show = $this->show_frameText_mobileCheck($textType,$show);
            if (!$show) return 0;
            if (is_string($show)) {
                $divMobileAdd = $show;
                // echo ("DivAdd is '$divMobileAdd' <br />");
            }
        }
       
        
        
        
        
        $wireFrameOn = $this->wireframeContentEnabled; // $this->contentData[data][wireframe];
        $wireframeState = $this->wireframeState; // cmsWireframe_state();
        if ($wireFrameOn AND $wireframeState) {
            $wireFrameData = $this->wireframeContentData;
            
            
            switch ($textType) {
                case "frameHeadline" :
                    $wireType = "frameHeadLine";
                    $wireOn = $this->wireframe_use($wireType);
                    $wireText = $this->wireframe_wireText($wireType); // wireFrameData[frameHeadLineText];
                    
                    
                    // echo ("<h1>WireHead $wireText $wireOn</h1>");
                    break;
                case "frameHeadtext" :
                    $wireType = "frameHeadText";
                    $wireOn = $this->wireframe_use($wireType);
                    $wireText = $this->wireframe_wireText($wireType);
                    // $wireOn = $this->wireframe_checkMobil($wireType,$wireOn);
                    // $wireText = $wireFrameData[frameHeadTextText];
                    break;
                case "frameSubtext" :
                    $wireType = "frameSubText";
                    $wireOn = $this->wireframe_use($wireType);
                    // $wireOn = $wireFrameData[frameSubText];
                    // $wireOn = $this->wireframe_checkMobil($wireType,$wireOn);
                    // $wireText = $wireFrameData[frameSubTextText];
                    $wireText = $this->wireframe_wireText($wireType);
                    break;
                default :
                    echo "unkown TextType $textType <br>";
            }
            // echo ("WireOn $wireOn wireText = '$wireText' $textType<br>");
            if ($wireOn) {
                $wireData = array();
                $wireData[orgText]  = $text;          // Normaler Text
                $wireData[type]     = $wireType;
                $wireData[nr]       = "nr?? in Frame";
                $wireData[id]       = $this->contentId;
                
                $name = $this->contentData[title];
                if (is_array($title)) $title=$this->lgStr($title);
                $wireData[name]     = $name;
                $wireData[debug]    = 0;
                
                // $wireData[wireText] = $this-wireRexr;
                $text = $this->wireframe_text($wireData);
                
                
            }
//                $text = $this->text_wireText($text,$wireText,array("type"=>$textType,"id"=>1,"nr"=>2)); // cmsWireframe_text($wireText,$text);
        }

        $maxChar = $this->contentData[data][$textType."_maxChar"];
        $readMore = $this->contentData[data][$textType."_readMore"];
       
        $css = $this->textData[$textType][css];
    
        $data = $this->textData[$textType][data];
        $style = null;
        if (is_array($data)) {
            $showColor = cmsStyle_getColor($data);
            if ($showColor) $style = "color:$showColor;border-color:$showColor;";

        }
        // echo ("Show $textType $text $showColor $style <br>");

        // textHeadline_h2
        $className = "frameText_".$textType;
        if ($css) {
            $className .= " frameText_".$css;
            switch ($frameType) {
                case "frameHeadline" : 
                    $className .= " textHeadLine_".$css." frameTextHeadLine_".$css;
                    $addStart = "<".$css.">";
                    $addEnd = "</".$css.">";               
                    break;
            }
        }
        
        if ($divMobileAdd) $className .= $divMobileAdd;
        
        $str = "";
        $str .= div_start_str($className,$style);
        if ($addStart) $str .= $addStart;
        $text = $this->showText($text,$maxChar,$readMore);
        $str .= $text;
        if ($addEnd) $str .= $addEnd;
        $str .= div_end_str($className);
        
        return $str;
    }
    
    

    function show_spacer() {
       
        $showSpacer = 1;
        if (substr($this->contentData[pageId],0,6) == "layer_") $showSpacer = 0;
        if ($showSpacer) {
            $spacerAdd = "";



            $spacerClass = "spacer ";
            
            if ($this->divAdd) $spacerClass .= $this->divAdd;

            $pageEditAble = $this->pageEditAble;
            switch ($this->contentData[specialType]) {
                case "contentName" :
                    $contentNameId = $this->contentData[specialId];
                    if (!$contentNameId) $pageEditAble = 0;
                    // echo ("NO EDIT SPACER after $contentName $contentNameId $pageEditAble <br>");                    
                    break;
            }
            
            
            switch ($this->contentData[viewContent]) {
                case "content" :
                    $spacerClass .= " spacerContent";
                    
                    $edit = $this->pageEditAble;
                   
                    
                    if ($pageEditAble AND !$this->layoutEdit) {
                    // if ($this->pageEditAble AND !$this->layoutEdit) {
                        
                        $spacerClass .= " spacerEdit";
                        if ($this->edit) $spacerClass.= " spacerDrop";
                    }
                    break;

                case "layout" ;
                    $spacerClass .= " spacerLayout";
                    if ($pageEditAble AND $this->layoutEdit) {
                        $spacerClass .= " spacerEdit";
                        if ($this->edit) $spacerClass.= " spacerDrop";
                    }
                    break;
            }

            $last = 0;
            switch ($this->contentData[specialView]) {
                case "first" :
                    $spacerClass .= " spacerFirst";
                    break;
                case "last" :
                    $spacerClass .= " spacerLast";
                    break;
            }

            $spacerClass .= " spacerType_".$this->contentType;

            $spacerId = "spacerId_".$this->contentId;

            
            echo ("<div id='$spacerId' class='$spacerClass' >");
            // echo ("Spacer id = $spacerId clas=$spacerClass <br />");
            echo ("</div>");
//            if ($this->contentData[last]) {
//                $spacerAdd .= "spacerLast";
//                $last = 1;
//            }
//
//
//
//            if ($pageEditAble) {
//                $spacerClass = "spacer spacerEdit";
//                if ($_SESSION[edit]) $spacerClass .= " spacerDrop";
//
//                //if ($_SESSION[edit])
//                echo ("<div id='spacerId_$this->contentId' class='$spacerClass'>");
//            }
            // if ($_SESSION[edit]) $spacerAdd .= " spacerDrop";
            //echo ("<div id='spacerId_$contentId' class='spacer spacerContentType spacerContentType_$type $spacerAdd'>&nbsp;</div>");
//            if ($last) {
//                echo ("&nbsp;");
//            } else {
//                echo ("<div class='spacer spacerContentType spacerContentType_$type $spaceAdd'>&nbsp;</div>");
//            }
//            if ($pageEditAble) {
//                // if ($_SESSION[edit])
//                echo ("</div>"); //  id='spacerId_$contentId' class='spacerDrop'>");
//            }
        } else {
            // echo ("No Spacer $this->contentData[pageId] <br>");
        }
    }



}

?>