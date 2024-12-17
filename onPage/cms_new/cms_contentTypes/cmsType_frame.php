<?php // charset:UTF-8
class cmsType_frame_base extends cmsClass_content_show  {
    function getName() {
        return $this->frameCount."Spalten";
    }

    function contentType_init() {
        $type = $this->contentType;
        $this->frameCount = intval(substr($type,5));

    }
    
    function contentType_show() {
        $frameWidth = $this->innerWidth;
       
        $anz = $this->frameCount;
        $data = $this->contentData[data];

        if (is_array($data)) $data = array();
        $mainBorder = $data[mainBorder];
        $mainBorderColor = $data[mainBorderColor];
        $mainBackColor = $data[mainBackColor];

        $layout = $this->contentData[layout];

        //show_array($data);

        $debug = 0;
        if ($debug) div_start("Test","width:$framWidth;background-color:#0f0;");
        if ($debug) echo("Test");
        if ($debug) div_end("Test");

        //if (!$mainBorder) $mainBorder = 1;
        //if (!$mainBorderColor) $mainBorderColor = "#000";
        //if (!$mainBackColor) $mainBackColor = "#fff";

        $mainStyle = "";
        if($mainBorder>0) {
            $mainStyle .= "border:".$mainBorder."px solid $mainBorderColor;";
            $frameWidth = $frameWidth - (2*$mainBorder);
        }
        $mainStyle .= "width:".$frameWidth."px;";

        if ($mainBackColor) $mainStyle .= "background-color:$mainBackColor;";

        $showDummy = 0;
        if ($this->pageEditAble) {
            // echo ("FRAME DUMMY ctype ".$this->contentData[viewContent]." editLayout $this->layoutEdit <br> ");
            if ($this->contentData[viewContent] == "layout") {
                if ($this->layoutEdit) $showDummy = 1;
            }
            if ($this->contentData[viewContent] == "content") {
                if (!$this->layoutEdit) $showDummy = 1;
            }
            if ($showDummy) {
                $this->dummyFrame();
            }
        }

        
        $frameData = $this->frameArray_Data($this->contentData, $frameWidth, $anz, $debug);
        $frameArray = $frameData[frameArray];
        $mainStyle  = $frameData[mainStyle];
        
        
        div_start("cmsContentFrame cmsTypeFrame",$mainStyle);
        
        $i = 0;

        $leftPos = $this->contentData[leftPos];
        foreach($frameArray as $frameName => $frameData) {
            $i++;
            // show_array($frameData);
            $width  = $frameData[width];
            $abs    = $frameData[abs];
            $border = $frameData[border];
            $borderColor = $frameData[borderColor];
            $backColor = $frameData[backColor];
            $frameStyle = $frameData[frameStyle];
            $style = "";
          
            $divName = "cmsTypeSubframe_$i";


            if ($frameStyle) {
                // echo ("frameStyle = '$frameStyle' <br />");
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


                $special_before = cmsFrame_getSpecial_before($frameStyle,$this->contentData,$width);
                $special_after  = cmsFrame_getSpecial_after($frameStyle,$this->contentData,$width);

                if (is_array($special_before)) {
                    //echo ("<h1>Special Before</h1>");
                    // show_array($special_before);
                }

                if (is_array($special_after)) {
                 
                }

                $width = $width - ($marginLeft+$marginRight) - ($borderLeft+$borderRight) - ($paddingLeft + $paddingRight);

                //$width = $width - (2*$borderFrame) - (2*$paddingFrame);
                $divName .= " $frameStyle";

            }

            if ($border>0) {
                if (strlen($borderColor)==0) $borderColor = "#fff";
                $style.= "border:".$border."px solid $borderColor;";
                $width = $width - (2*$border);
            }
            $style .= "float:left;width:".$width."px;";
            if ($abs > 0) $style.="margin-right:".$abs."px;";
            $style.= "min-height:30px;";
            if (strlen($backColor)>0) $style.= "background-color:$backColor;";

            $divData[style] = $style;
            if ($_SESSION[edit]) {
                $divData[id] = "dragFrame_".$this->contentData[id]."_$i";
                $divData["class"] = "dragFrame";
            }
            
            div_start($divName,$divData);
            if ($debug) echo ($style."<br />");
            $showIn = "frame_".$this->contentId."_$i";

            $this->pageClass->content_show_frameContent($showIn,$width,$this->contentData[viewContent],$leftPos);


            $leftPos= $leftPos + $width + $abs;

            div_end($divName);

        }
        div_end("cmsContentFrame cmsTypeFrame","before");
    }
    
    
    function dummyFrame() {
//        $contentData = $this->contentData;
//        $frameWidth = $this->frameWidth;
//        $anz = $this->frameCount;
        $frameData = $this->frameArray_Data();
        $frameArray = $frameData[frameArray];
        $mainStyle  = $frameData[mainStyle];

        $edit = $_SESSION[edit];
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";
        
        $divDummy = "cmsContentFrame_Dummy $addEditClass";
        div_start($divDummy,$mainStyle);
        $i=0;
        foreach($frameArray as $frameName => $frameData) {
            $i++;
            // show_array($frameData);
            $width  = $frameData[width];
            $abs    = $frameData[abs];
            $border = 2;
            $style = "";
            $width = $width - 2*$border;

            $style .= "width:".$width."px;";
            if ($abs > 0) $style.="margin-right:".$abs."px;";
            $divData[style] = $style;

            $divName = "cmsContentFrame_DummyFrame cmsContentFrame_DummyFrame_$i";
            div_start($divName,$divData);
            echo ("Spalte $i");
            // echo ("&nbsp;$width $border $abs");
            div_end($divName);                
        }
        div_end($divDummy,"before");
        
    }
    
    
    function frameArray_Data() {
        // $this->contentData,$frameWidth,$anz,$debug=0) {
        $contentData = $this->contentData;
        $frameWidth = $this->innerWidth;
        $anz = $this->frameCount;

        $data = $this->contentData[data];
        $mainBorder = $data[mainBorder];
        $mainBorderColor = $data[mainBorderColor];
        $mainBackColor = $data[mainBackColor];

        $layout = $this->contentData[layout];

        $mainStyle = "";
        if($mainBorder>0) {
            $mainStyle .= "border:".$mainBorder."px solid $mainBorderColor;";
            $this->frameWidth = $this->frameWidth - (2*$mainBorder);
        }
        $mainStyle .= "width:".$frameWidth."px;";

        if ($mainBackColor) $mainStyle .= "background-color:$mainBackColor;";

        

        $frameData = $data[frameData];

        if (!is_array($data)) $data = array();

       

       
        $totalFrameWidth = $this->innerWidth;        
        $leftFrameWidth = $totalFrameWidth;
        $leftAuto = $this->frameCount;
        $frameArray = array();
        for ($i=1;$i<=$anz;$i++) {
            $border = intval($data["border$i"]);
            $borderColor = $data["borderColor$i"];
            $backColor = $data["backColor$i"];
            $frameStyle = $data["frameStyle$i"];
            
            // Frame Width
            $myFrameWidth = $data["width$i"];
            if ($myFrameWidth) {
                $offProz = strpos($myFrameWidth,"%");
                // echo ("My FrameWidth $i = $myFrameWidth ");
                if ($offProz) {
                    $proz = intval(substr($myFrameWidth,0,$offProz));
                    if (intval($proz)) $myFrameWidth = $frameWidth * $proz / 100;                   
                }
                $offPixel = strpos($myFrameWidth,"px");
                
                if ($offPixel) {
                    $myFrameWidth = intval(substr($myFrameWidth,0,$offPixel));                                   
                }
                // echo ("after : $myFrameWidth <br>");
            } 
            if (!intval($myFrameWidth)) $myFrameWidth = "auto";
            else {
                $leftFrameWidth = $leftFrameWidth - $myFrameWidth;
                $leftAuto--;
            }
            
            $myFrameAbs = $data["abs$i"];
            if (is_null($myFrameAbs) AND $i < $anz) $myFrameAbs = 10;
            if ($myFrameAbs) {
                $offProz = strpos($myFrameAbs,"%");
                // echo ("My FrameAbsh $i = $myFrameAbs ");
                if ($offProz) {
                    $proz = intval(substr($myFrameAbs,0,$offProz));
                    if (intval($proz)) $myFrameAbs = $frameWidth * $proz / 100;                   
                }
                $offPixel = strpos($myFrameAbs,"px");
                
                if ($offPixel) {
                    $myFrameAbs = intval(substr($myFrameAbs,0,$offPixel));                                   
                }
                // echo ("after : $myFrameAbs <br>");
            } 
            if (intval($myFrameAbs)) {
                $leftFrameWidth = $leftFrameWidth - $myFrameAbs;
            } else {
                $myFrameAbs = 0;
            }
            
            $frameArray["frame$i"] = array("width"=>$myFrameWidth,"border"=>$border,"borderColor"=>$borderColor,"backColor"=>$backColor,"frameStyle"=>$frameStyle,"abs"=>$myFrameAbs);
           
        }
        foreach($frameArray as $frameName => $frameData) {
            $width = $frameData[width];
            if ($width == "auto") {
                // echo ("AUTO WIDTH in $frameName rest = $leftFrameWidth auf $leftAuto ");
                $useWidth = floor($leftFrameWidth / $leftAuto);
                // echo ("neue Breite = $useWidth ");
                $leftFrameWidth = $leftFrameWidth - $useWidth;
                $leftAuto--;
                // echo ("DANACH rest = $leftFrameWidth auf $leftAuto <br>");    
                $frameArray[$frameName][width] = $useWidth;
            }                
        }
        
        $frameData = array();
        $frameData[frameArray] = $frameArray;
        $frameData[mainStyle]  = $mainStyle;
        $this->frameArray = $frameArray;
        $this->mainStyle = $mainStyle;
        return $frameData;
    }


    function frameArray() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $anz = $this->frameCount;
        
        if ($debug) echo ("<h1> getFrameArray $frameWidth</h1>");
        $mainBorder = $this->contentData[data][mainBorder];
        $mainBorderColor = $this->contentData[data][mainBorderColor];
        $mainBackColor = $this->contentData[data][mainBackColor];

        $layout = $this->contentData[layout];

        //show_array($data);

        // $debug = 0;
        if ($debug) div_start("Test","width:$frameWidth;background-color:#0f0;");
        if ($debug) echo("Test");
        if ($debug) div_end("Test");

        //if (!$mainBorder) $mainBorder = 1;
        //if (!$mainBorderColor) $mainBorderColor = "#000";
        //if (!$mainBackColor) $mainBackColor = "#fff";

        $mainStyle = "";
        if($mainBorder>0) {
            $mainStyle .= "border:".$mainBorder."px solid $mainBorderColor;";
            $frameWidth = $frameWidth - (2*$mainBorder);
        }
        $mainStyle .= "width:".$frameWidth."px;";

        if ($mainBackColor) $mainStyle .= "background-color:$mainBackColor;";

        // div_start("cmsTypeFrame",$mainStyle); //"width:".$frameWidth."px;border:1px solid #f00;");
        if ($debug) echo ("MainStyle = $mainStyle <br />");

        $frameData = $data[frameData];

        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();

        if ($debug) show_array($data);

        if ($debug) echo ("Frame Width = $frameWidth<br />");

        $totalFrameWidth = $frameWidth;
        $frameArray = array();
        for ($i=1;$i<=$anz;$i++) {
            $border = intval($data["border$i"]);
            $borderColor = $data["borderColor$i"];
            $backColor = $data["backColor$i"];
            $frameStyle = $data["frameStyle$i"];

            $frameArray["frame$i"] = array("width"=>"auto","border"=>$border,"borderColor"=>$borderColor,"backColor"=>$backColor,"frameStyle"=>$frameStyle);
            $subFrameWidth = $data["width$i"];
            if (strlen($subFrameWidth)>0) $frameArray["frame$i"][width] = $subFrameWidth;
            $subFrameAbs = intval($data["abs$i"]);
            if ($subFrameAbs>0) {
                $prozStart = strpos($subFrameAbs,"%");
                if ($prozStart) {
                    $proz = intval(substr($subFrameAbs,0,$prozStart));
                    if ($proz > 0) $subFrameAbs = $frameWidth * $proz / 100;
                }
                $totalFrameWidth = $totalFrameWidth-$subFrameAbs;
                $frameArray["frame$i"][abs] = $subFrameAbs;
            }
        }

        if ($debug) echo ("FrameWidth without Abs $totalFrameWidth <br />");

        //define single FrameWidth if not auto
        $leftFrameWidth = $totalFrameWidth;
        $autoCount = 0;
        foreach($frameArray as $frameName => $frameData) {
            $width = $frameData[width];
            $subFrameWidth = "";
            if ($debug) echo("&nbsp;<br />getWidth $width <br />");
            $prozStart = strpos($width,"%");
            if ($prozStart>0) {
                $proz = intval(substr($width,0,$prozStart));
                if ($proz > 0 AND $proz < 90) {
                    $subFrameWidth = intval($totalFrameWidth * $proz / 100);
                }
                if ($debug) echo("Prozent = '$proz' - $subFrameWidth % <br />");
            } else {
                $pixel = intval($width);
                if ($pixel > 0) {
                    $subFrameWidth = $pixel;
                     if ($debug) echo("Pixel = '$pixel' - $subFrameWidth px <br />");
                }
            }

            if (is_integer($subFrameWidth)) {
                $leftFrameWidth = $leftFrameWidth - $subFrameWidth;
                $frameArray[$frameName][width] = $subFrameWidth;
                if ($debug) echo ("$frameName width=$subFrameWidth px - Rest = $leftFrameWidth <br />");
            }
            if ($width == "auto") $autoCount++;

        }

        if ($autoCount > 0) {
            if ($debug) echo ("&nbsp;<br />autocount =$autoCount / leftWidth=$leftFrameWidth <br />");
            $autoWidth = intval ($leftFrameWidth /$autoCount);
            if ($debug) echo ("Set AutoWidth to $autoWidth<br />");
            $nr = 1;
            foreach($frameArray as $frameName => $frameData) {
                $width = $frameData[width];
                if ($width=="auto") {
                    if ($nr == $autoCount) { // letzter AutoWert
                        $frameArray[$frameName][width] = $leftFrameWidth;
                        if ($debug) echo ("Setze letzen Wert $leftFrameWidth<br />");
                    } else {
                        $frameArray[$frameName][width] = $autoWidth;
                        $leftFrameWidth = $leftFrameWidth - $autoWidth;
                        if ($debug) echo ("Setze autoWert $autoWidth<br />");
                    }
                    $nr++;
                }
            }
        }
        // show_array($frameArray,1);
        return $frameArray;
    }


    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth=$this->frameWidth;
        $anz= $this->frameCount;
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();


        $frameArray = $this->frameArray(); 

        $res = array();

        
        $lgType = "contentType_frame";

        $res["frame$anz"] = array();
        $res["frame$anz"][showTab] = "Simple";
        $res["frame$anz"][showName] = "$anz ".$this->lga($lgType,"tabName");//Spalten"; 
        
        $divContent = "";
        foreach ($frameArray as $key => $value) {
            // $divContent .= "$key = $value <br />";
            foreach ($value as $frameKey => $frameValue ) {
                // $divContent .= " -> $frameKey = $frameValue <br />";
            }
        }
        $nr = 0;
        
        $columnStr = $this->lga($lgType,"columnName");
        $columnWidthStr = $this->lga($lgType,"columnWidthName",":");
        $columnStyleStr = $this->lga($lgType,"columnStyleName",":");
        $columnAbsStr = $this->lga($lgType,"columnAbsName",":");
        
        $divAbstand = div_start_str("cmsFrameData_abs",array("style"=>"width:".$width."px;margin-top:5px;"));
        foreach ($frameArray as $frameNr => $frameData) {
            $nr++;
            $width = $frameData[width];
            $border = $frameData[border];
            $abs = $frameData[abs];
            $width = $width - (2*$border);
           
            $divContent .= div_start_str("cmsFrameData_".$frameNr,array("style"=>"width:".$width."px;margin-right:".$abs."px;border:".$border."px solid #cd2;float:left;"));
            
            $divContent .= "<b>$columnStr $nr</b><br />"; // $width $border $abs</b><br />";

            $divContent .= span_text_str($columnWidthStr,100);
            $divContent .= "<input type='text' name='editContent[data][width$nr]' value='".$data["width$nr"]."' ><br />";


            $divContent .= span_text_str($columnStyleStr,100);
            $divContent .= cms_content_selectStyle("frameStyle",$data["frameStyle$nr"],"editContent[data][frameStyle$nr]");
            //$frameSettings[] = $addData;
            
            if ($nr < $anz) {
                $divContent .= "<br />";
                $divContent .= span_text_str($columnAbsStr,100);
                $divContent .= "<input type='text' name='editContent[data][abs$nr]' style='width:40px' value='".$data["abs$nr"]."' >";
                // $divContent .= cms_content_selectStyle("frameStyle",$data["frameStyle$nr"],"editContent[data][frameStyle$nr]");
                //$frameSettings[] = $addData;
            }

            $divContent .= div_end_str("cmsFrameData_".$frameNr);

        }
        $divContent .= "<div style='clear:both;'></div>";
        $divAbstand .= div_end_str("cmsFrameData_abs","before");
        // $divContent .= $divAbstand;

        $divStr = $divAbstand.$divContent;
        $divStr = $divContent;

        $div = array();
        $div[divname] = "cmsFrameData";
        $div[style] = "width:".$frameWidth."px";
        $div[content] = $divStr;
        $addData["div"] = $div;

        $res["frame$anz"][] = $addData;

        $mainFrame = 0;
        if ($mainFrame) {
            $res[frameMain] = array();
            $res[frameMain][showTab] = "Admin";
            $res[frameMain][showName] = "Spalten Zustatz"; 
            // MainData
            $addData = array();
            $addData["text"] = "Rahmen Stärke";
            $addData["input"] = "<input type='text' name='editContent[data][mainBorder]' value='$data[mainBorder]' >";
            $addData["mode"] = "Admin";
            $res[frameMain][] = $addData;

            $addData["text"] = "Rahmenfarbe";
            $input .= ""; // <input type='text' name='editContent[data][mainBorderColor]' value='$data[mainBorderColor]' />";
            $input .= $this->selectColor($editContent[data][mainBorderColor],"editContent[data][mainBorderColor]","frameHeadline");
            $addData["input"] = $input;
            $addData["mode"] = "Admin";

            $res[frameMain][] = $addData;

            $addData["text"] = "Hintergrundfarbe";
            $addData["input"] = $this->selectColor($editContent[data][mainBackColor],"editContent[data][mainBackColor]","frameHeadline");
            //"<input type='text' name='editContent[data][mainBackColor]' value='$data[mainBackColor]' >";
            $addData["mode"] = "Admin";
            $res[frameMain][] = $addData;
        }

        return $res;
    }
    
    
}

function cms_contentType_Frame($contentData,$frameWidth,$anz) {
    echo ("Use old Version cms_contentType_Frame <br />");
    cmsType_frame($this->contentData,$frameWidth,$anz);
}

function cms_contentType_frame_editContent($editContent,$frameWidth=1000,$anz=2) {
    echo ("Use old Version cms_contentType_frame_editContent <br />");
    return cmsType_frame_editContent($editContent,$frameWidth,$anz);
}




function cmsType_frame_class() {
    if ($GLOBALS[cmsTypes]["cmsType_frame.php"] == "own") $frameClass = new cmsType_frame();
    else $frameClass = new cmsType_frame_base();
    return $frameClass;
}

function cmsType_frame($contentData,$frameWidth) {
    $frameClass = cmsType_frame_class();
    $res = $frameClass->show($contentData,$frameWidth);
    return $res;
}

function cmsType_frameDummy($contentData,$frameWidth,$anz) {
    $frameClass = cmsType_frame_class();
    $frameClass->dummyFrame($contentData,$frameWidth,$anz);
}

function cmsType_frame_editContent($editContent,$frameWidth,$anz) {
   $frameClass = cmsType_frame_class();
   return $frameClass->editContent($editContent,$frameWidth,$anz);
}

function cmsType_frame_getName($anz) {
    $frameClass = cmsType_frame_class();
    $name = $frameClass->getName();
    return $name;
}
?>
