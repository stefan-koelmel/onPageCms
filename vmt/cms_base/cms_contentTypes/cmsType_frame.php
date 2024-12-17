<?php // charset:UTF-8
class cmsType_frame_base {
    function getName($anz) {
        return "Spalten - $anz";
    }
    
    function show($contentData,$frameWidth,$anz) {
        $data = $contentData[data];
        $mainBorder = $data[mainBorder];
        $mainBorderColor = $data[mainBorderColor];
        $mainBackColor = $data[mainBackColor];

        $layout = $contentData[layout];

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

        //"width:".$frameWidth."px;border:1px solid #f00;");
        if ($debug) echo ("$mainStyle <br />");

        $frameData = $data[frameData];

        $data = $contentData[data];
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


        if ($_SESSION[edit] AND !$contentData[dontShowDummy] ) {
            $this->dummyFrame($contentData, $frameWidth, $anz);
        }
        
        $frameData = $this->frameArray_Data($contentData, $frameWidth, $anz, $debug);
        $frameArray = $frameData[frameArray];
        $mainStyle  = $frameData[mainStyle];
        
        
        
        div_start("cmsTypeFrame",$mainStyle); 
        
        $i = 0;
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
                $borderFrame = $frameSettings[border];
                $paddingFrame = $frameSettings[padding];
                $spacingFrame = $frameSettings[spacing];

                $special_before = cmsFrame_getSpecial_before($frameStyle,$contentData,$framWidth);
                $special_after  = cmsFrame_getSpecial_after($frameStyle,$contentData,$framWidth);

                if (is_array($special_before)) {
                    //echo ("<h1>Special Before</h1>");
                    // show_array($special_before);
                }

                if (is_array($special_after)) {
                    // echo ("<h1>Special After</h1>");
                    //show_array($special_after);
                }


                $width = $width - (2*$borderFrame) - (2*$paddingFrame);
                $divName .= " $frameStyle";

            }

            if ($border>0) {
                if (strlen($borderColor)==0) $borderColor = "#fff";
                $style.= "border:".$border."px solid $borderColor;";
                $width = $width - (2*$border);
            }
            $style .= "float:left;width:".$width."px;";
            if ($abs > 0) $style.="margin-right:".$abs."px;";
            $style.= "min-height:50px;";
            if (strlen($backColor)>0) $style.= "background-color:$backColor;";

            $divData[style] = $style;
            if ($_SESSION[edit]) {
                $divData[id] = "dragFrame_".$contentData[id]."_$i";
                $divData["class"] = "dragFrame";
            }
            
            div_start($divName,$divData);
            if ($debug) echo ($style."<br />");
            $showIn = "frame_".$contentData[id]."_$i";
            // echo ("Get Inhalt für $showIn - ");
            if ($layout) {
                // echo ("Frame Inhalt Layer $layout | $showIn <br />");
                // cms_layout_showContentTypes ($layout,$showIn,$width);
                cms_layout_showFrameContent($layout,$showIn,$width);
            } else {
                if ($_SESSION[edit]) $spacerAdd = "spacerDrop";
                echo ("<div class='spacer spacerFrame $spacerAdd' style=''>&nbsp;</div>");
                
                
                cms_content_show($showIn,$width);
            }
            div_end($divName);

        }
        div_end("cmsTypeFrame","before");
    }
    
    
    function dummyFrame($contentData,$frameWidth,$anz,$debug=0) {
        $frameData = $this->frameArray_Data($contentData, $frameWidth, $anz, $debug);
        $frameArray = $frameData[frameArray];
        $mainStyle  = $frameData[mainStyle];

        
        $divDummy = "cmsContentFrame_Dummy";
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

            $style .= "float:left;width:".$width."px;";
            if ($abs > 0) $style.="margin-right:".$abs."px;";
            $style .= "text-align:center;";
            $divData[style] = $style;

            $divName = "cmsContentFrame_DummyFrame cmsContentFrame_DummyFrame_$i";
            div_start($divName,$divData);
            echo ("Frame $i ");
            echo ("&nbsp;$width $border $abs");
            div_end($divName);                
        }
        div_end($divDummy,"before");
        
    }
    
    
    function frameArray_Data($contentData,$frameWidth,$anz,$debug=0) {
        $data = $contentData[data];
        $mainBorder = $data[mainBorder];
        $mainBorderColor = $data[mainBorderColor];
        $mainBackColor = $data[mainBackColor];

        $layout = $contentData[layout];

        $mainStyle = "";
        if($mainBorder>0) {
            $mainStyle .= "border:".$mainBorder."px solid $mainBorderColor;";
            $frameWidth = $frameWidth - (2*$mainBorder);
        }
        $mainStyle .= "width:".$frameWidth."px;";

        if ($mainBackColor) $mainStyle .= "background-color:$mainBackColor;";

        

        $frameData = $data[frameData];

        if (!is_array($data)) $data = array();

       

       
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
        $frameData = array();
        $frameData[frameArray] = $frameArray;
        $frameData[mainStyle]  = $mainStyle;
        
        return $frameData;
    }


    function frameArray($contentData,$frameWidth,$anz,$debug=0) {
        if ($debug) echo ("<h1> getFrameArray $frameWidth</h1>");
        $mainBorder = $contentData[data][mainBorder];
        $mainBorderColor = $contentData[data][mainBorderColor];
        $mainBackColor = $contentData[data][mainBackColor];

        $layout = $contentData[layout];

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

        $data = $contentData[data];
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


    function editContent($editContent,$frameWidth=1000,$anz=2) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();


        $frameArray = $this->frameArray($editContent,$frameWidth,$anz,0); 





        $res = array();


        $res["frame$anz"] = array();


        $divContent = "";
        foreach ($frameArray as $key => $value) {
            // $divContent .= "$key = $value <br />";
            foreach ($value as $frameKey => $frameValue ) {
                // $divContent .= " -> $frameKey = $frameValue <br />";
            }
        }
        $nr = 0;
        $divAbstand = div_start_str("cmsFrameData_abs",array("style"=>"width:".$width."px;margin-top:5px;"));
        foreach ($frameArray as $frameNr => $frameData) {
            $nr++;
            $width = $frameData[width];
            $border = $frameData[border];
            $abs = $frameData[abs];
            $width = $width - (2*$border);
            $divContent .= div_start_str("cmsFrameData_".$frameNr,array("style"=>"width:".$width."px;margin-right:".$abs."px;border:".$border."px solid #cd2;float:left;"));

            $divContent .= "<b>Spalte $nr</b><br />"; // $width $border $abs</b><br />";

            $divContent .= span_text_str("Spalten Breite:",100);
            $divContent .= "<input type='text' name='editContent[data][width$nr]' value='".$data["width$nr"]."' ><br />";


            $divContent .= "Rahmen Stil";
            $divContent .= cms_content_selectStyle("frameStyle",$data["frameStyle$nr"],"editContent[data][frameStyle$nr]");
            $frameSettings[] = $addData;

            //frameWidth
            /*$divContent .= span_text_str("Rahmenstärke:",100);
            $divContent .= "<input type='text' name='editContent[data][border$nr]' value='".$data["border$nr"]."' ><br />";

            //frameColor
            $divContent .= span_text_str("Rahmenfarbe:",100);
            $divContent .= "<input type='text' name='editContent[data][borderColor$nr]' value='".$data["borderColor$nr"]."' ><br />";

            //frameColor
            $divContent .= span_text_str("Hintergrundfarbe:",100);
            $divContent .= "<input type='text' name='editContent[data][backColor$nr]' value='".$data["backColor$nr"]."' >";
            */
            $divContent .= div_end_str("cmsFrameData_".$frameNr);

            if ($nr < $anz) {
                $divAbsWidth = 200;
                $abs = $data["abs$nr"];
                $divAbstand .= div_start_str("frameAbsEmpty","width:".($width-($divAbsWidth/2)+($abs/2))."px;float:left;");
                $divAbstand .= "&nbsp;";
                $divAbstand .= div_end_str("frameAbsEmpty");

                $divAbstand .= div_start_str("frameAbsContent","width:".$divAbsWidth."px;text-align:center;float:left;");
                $divAbstand .= "Abstand: "; // zwichen $nr und ".($nr+1)."<br />";
                $divAbstand .= "<input type='text' name='editContent[data][abs$nr]' style='width:40px' value='".$data["abs$nr"]."' >";
                $divAbstand .= div_end_str("frameAbsContent");
            }
        }
        $divContent .= "<div style='clear:both;'></div>";
        $divAbstand .= div_end_str("cmsFrameData_abs","before");
        // $divContent .= $divAbstand;

        $divStr = $divAbstand.$divContent;


        $div = array();
        $div[divname] = "cmsFrameData";
        $div[style] = "width:".$frameWidth."px";
        $div[content] = $divStr;
        $addData["div"] = $div;

        $res["frame$anz"][] = $addData;



        // MainData
        $addData = array();
        $addData["text"] = "Rahmen Stärke";
        $addData["input"] = "<input type='text' name='editContent[data][mainBorder]' value='$data[mainBorder]' >";
        $res[frameMain][] = $addData;

        $addData["text"] = "Rahmenfarbe";
        $addData["input"] = "<input type='text' name='editContent[data][mainBorderColor]' value='$data[mainBorderColor]' >";
        $res[frameMain][] = $addData;

        $addData["text"] = "Hintergrundfarbe";
        $addData["input"] = "<input type='text' name='editContent[data][mainBackColor]' value='$data[mainBackColor]' >";
        $res[frameMain][] = $addData;


        return $res;
    }
    
    
}

function cms_contentType_Frame($contentData,$frameWidth,$anz) {
    echo ("Use old Version cms_contentType_Frame <br />");
    cmsType_frame($contentData,$frameWidth,$anz);
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

function cmsType_frame($contentData,$frameWidth,$anz) {
    $frameClass = cmsType_frame_class();
    $frameClass->show($contentData,$frameWidth,$anz);
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
