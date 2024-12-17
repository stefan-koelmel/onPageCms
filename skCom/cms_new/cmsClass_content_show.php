<?php

class cmsClass_content_show extends cmsClass_content_showData {

    function content_show($contentData,$frameWidth) {
        $this->init_content($contentData,$frameWidth);

        $this->show_frame();

        $this->show_spacer();
        return $this;
    }

    function layout_show($contentData,$frameWidth) {
        $this->init_layout($contentData,$frameWidth);

        $this->show_frame();

        $this->show_spacer();
        return $this;
    }


    function show($contentData,$frameWidth) {
        $this->init_content($contentData,$frameWidth);

        $this->show_frame();

        $this->show_spacer();
        return $this;
    }


    function show_frame() {
        
        $frameWidth = $this->frameWidth;


        $showType = cms_contentType_checkType($this->contentType);
        if (is_string($showType)) {
            cms_errorBox("Fehler beim Anzeigen von Modul $type <br />$showType");
            return 0;
        }

        if (!$showType) {
            if ($this->editAble) {
                // foreach($_SESSION as $key => $value) echo ("$key = $value <br />");
                cms_errorBox("Das Anzeigen des Modules '$type' ist nicht aktiviert!<br />Aktivieren Sie das Modul unter CMS-Einstellungen.");
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

        $dragAble = 1;
        if (!$this->pageEditAble) return 0;
        
        echo ("<div class='cmsContentFrameBox dragBox' id='dragContent_".$this->contentId."' >");
        
        
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

        $divName = "cmsContent cmsContentFrame_$contentId";
        
        if ($this->frameClose) $divName .= " cmsContentFrame_hidden";
        
        $divName .= " ".$type."_box ".$type."_boxId_$contentId ".$type."_boxPage_".$GLOBALS[pageInfo][pageName];

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
                $pageData = cms_page_getData($goPageId);
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
        $openCloseText = cms_text_getLg($openCloseText);
        if (!$openCloseText) $openCloseText = "&nbsp;";
        div_start("cmsContentFrame_".$closeState,array("style"=>"width:$useWidth;display:block;","id"=>$this->contentId));
        div_start("cmsContentFrame_".$closeState."_text");
        echo ($openCloseText);
        div_end("cmsContentFrame_".$closeState."_text");
        div_start("cmsContentFrame_".$closeState."_button");
        // echo ("o");
        div_end("cmsContentFrame_".$closeState."_button");
        div_end("cmsContentFrame_".$closeState,"before");
        return 1;
    }
    
    function show_frameText($textType) {       
        if (!is_array($this->textData[$textType])) return 0;
        
    
        $text = $this->textData[$textType][text];
        if (!$text) return 0;
        
    
        $wireFrameOn = $this->contentData[data][wireframe];
        $wireframeState = cmsWireframe_state();
        if ($wireFrameOn AND $wireframeState) {
            $wireFrameData = $this->contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            
            switch ($textType) {
                case "frameHeadline" :
                    $wireOn = $wireFrameData[headLine];
                    $wireText = $wireFrameData[headLineText];
                    $wireType = "frameHeadline";
                    // echo ("<h1>WireHead $wireText $wireOn</h1>");
                    break;
                case "frameHeadtext" :
                    $wireOn = $wireFrameData[subHeadLine];
                    $wireText = cms_text_getLg($wireFrameData[subHeadLineText]);
                    $wireType = "frameHeadtext";
                    break;
                case "frameSubtext" :
                    $wireOn = $wireFrameData[text];
                    $wireText = cms_text_getLg($wireFrameData[textText]);
                    $wireType = "frameSubtext";
                   
                    break;
                default :
                    echo "unkown TextType $textType <br>";
            }
            // echo ("WireOn $wireOn wireText = '$wireText' $textType<br>");
            if ($wireOn) $text = $this->text_wireText($text,$wireText,array("type"=>$textType,"id"=>1,"nr"=>2)); // cmsWireframe_text($wireText,$text);
        }


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
        
        $str = "";
        $str .= div_start_str($className,$style);
        if ($addStart) $str .= $addStart;
        $str .= $text;
        if ($addEnd) $str .= $addEnd;
        $str .= div_end_str($className);
        
        return $str;
    }
    
    

    function show_spacer() {
        $this->contentData = $this->contentData;

        $showSpacer = 1;
        if (substr($this->contentData[pageId],0,6) == "layer_") $showSpacer = 0;
        if ($showSpacer) {
            $spacerAdd = "";



            $spacerClass = "spacer ";

            switch ($this->contentData[viewContent]) {
                case "content" :
                    $spacerClass .= " spacerContent";
                    if ($this->pageEditAble AND !$this->layoutEdit) {
                        $spacerClass .= " spacerEdit";
                        if ($this->edit) $spacerClass.= " spacerDrop";
                    }
                    break;

                case "layout" ;
                    $spacerClass .= " spacerLayout";
                    if ($this->pageEditAble AND $this->layoutEdit) {
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

            
            echo ("<div id='$spacerId' class='$spacerClass'>");
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