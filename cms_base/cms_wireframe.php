<?php




class cms_wireframe_base {
    function color($type) {
        switch ($type) {
            case "back" : return 0; return "#33ff33";
            case "line" : return "#777777";                    
        }
    }
    
    
    function hex2rgb($hex) {
        $rgb[0]=hexdec(substr($hex,1,2));
        $rgb[1]=hexdec(substr($hex,3,2));
        $rgb[2]=hexdec(substr($hex,5,2));
        return($rgb);
    }
    
    function imageCross($lineColor) {
        
        if ($lineColor[0] == "#") $lineColor = substr($lineColor,1);
        
        $path = $_SERVER[DOCUMENT_ROOT]."/cms_".$GLOBALS[cmsVersion]."/images/wireframe/";
        
        //  echo ("Check ".$path."cross-".$lineColor.".png <br>");
        if (file_exists($path."cross-".$lineColor.".png")) {
            
            return $path."cross-".$lineColor.".png";
        }
        $fn = "cross.png";
        // echo ("$path / $fn <br>");
        if (file_exists($path.$fn)) {
            return $path.$fn;
        }        
    }
    
    
    function image($width=400,$height=300,$color=0) {
        $witdh = intval($width);
        $height = intval($height);
        if (!$width) return "noWidth!";
        if (!$height) return "noHeight";

        if (!$color) {
            $backColor = $this->color("back");
            $lineColor = $this->color("line");
        }
        
        switch ($_SERVER[HTTP_HOST]) {
            case "cms.stefan-koelmel.com" :
                $path = $_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/wireframe/";
                break;
            default :
                 $path = $_SERVER[DOCUMENT_ROOT]."/wireframe/";                
        }
        
        
        
        
        $fn = "wire_".$width."_".$height."_";
        if ($backColor[0] == "#") $fn .= substr($backColor,1)."_";
        else $fn .= $backColor."_";
        
        if ($lineColor[0] == "#") $fn .= substr($lineColor,1);
        else $fn .= $lineColor;
        
        
        
        $fn .= ".png";
        

        if (file_exists($path.$fn)) return "wireframe/".$fn;
        
        
        
        
        // echo ("$path <br>");
        
        
        // Create Image
        $image_new = imagecreatetruecolor($width, $height);
        
       
        $lineWidth = 1;
        if ($backColor) {
            $backRgb = $this->hex2rgb($backColor);
            $colBack=imagecolorallocate($image_new,$backRgb[0],$backRgb[1],$backRgb[2]);
        }
        
        $lineRgb = $this->hex2rgb($lineColor);
        
        // echo ("Create Image $width x $height $backColor / $lineColor <br>");
        
        
        
        
       
        // echo ("ColBack = $colBack<br>");
        
        $colLine=imagecolorallocate($image_new,$lineRgb[0],$lineRgb[1],$lineRgb[2]);
        // echo ("colLine = $colLine<br>");
        
        
        $alpha = imagecolorallocatealpha($image_new, 0, 0, 0, 127);
        imagefill($image_new, 0, 0, $alpha);
        
        if ($backColor) {
            // Hintergrund
            imagefill($image_new,0,0,$colBack);
        }
         
         
         
        
         
         $crossWithImage = 0;
         if ($crossWithImage) {
            // get FileName for Cross
            $imgCrossFileName = $this->imageCross($lineColor);
            if ($imgCrossFileName) {
                $imgCross = imagecreatefrompng($imgCrossFileName);
                $maxSize = $width;
                if ($height > $maxSize) $maxSize = $height;

                $crossWidth = imagesx($imgCross);
                $crossHeight = imagesy($imgCross);
                imageFill($imgCross,0,0,$colLine);
                
                
                $crossColor =  imagecolorat($imgCross , floor($crossWidth/2) , floor($crossHeight/2) );
                // echo ("Kreuzfarber = $crossColor <br>");
                $colLine = $crossColor;

                // echo ("Cross Image $imgCross $crossWidth x $crossHeight <br>");

                $crossOffX = floor(($crossWidth - $maxSize) / 2);
                $crossOffY = floor(($crossHeight - $maxSize) / 2);

                imagecopyresampled($image_new, $imgCross, 0, 0, $crossOffX, $crossOffY, $width, $height, $maxSize, $maxSize);
                imagedestroy($imgCross);
             } else {
                 // no CrossFileName
                 $crossWithImage = 0;
             }
         }
         
         if (!$crossWithImage) { 
             // Create Cross with Line
             imageline($image_new,0,0,$width+1,$height-1, $colLine);
             imageline($image_new,0,$height-1,$width,0, $colLine);             
         }
         
         
         // Rahmen
         // oben
         imagefilledrectangle($image_new, 0, 0, $width, $lineWidth-1, $colLine);
         
         // left
         imagefilledrectangle($image_new, 0, $lineWidth-1, $lineWidth-1, $height-$lineWidth, $colLine);
         
         // right
         imagefilledrectangle($image_new, $width-$lineWidth, $lineWidth, $width, $height-$lineWidth, $colLine);
         
         // bottom
         imagefilledrectangle($image_new, 0, $height-$lineWidth, $width, $height, $colLine);
         
         
        
        
        
        imagesavealpha($image_new, true);
        // echo ("save to $path $fn <br>");
        imagepng($image_new, $path.$fn);
        imagedestroy($image_new);
        
        return "wireframe/".$fn;   
    }
    
    function frameStart($width,$height,$class,$color=0) {
       
        $backImage = $this->image($width,$height,$color);

        $style = "background-image:url(".$backImage.");width:".$width."px;height:".$height."px;";
        //$style .= "line-height:".$height."px;font-size:14px;font-wight:bold;";
        $style .= "display:table-cell;vertical-align:middle;text-align:center;";
        $out  = "<div class='wireframe_Frame $class' style='$style' >";
        return $out;
    }
    
    function frameEnd() {
        // echo ("End of Frame $width x $height $class <br>");
        $out = "</div>";
        return $out;
    }


    function text($length,$nr=0) {
        if (is_array($length)) {
            
            
        }
        
        
        
         echo ("wireText $length $nr <br>");
//        if (is_array($length)) {
//            $length = cms_text_getLg($length);
//        }
        
        if ($nr) {
            $idStr = $nr;
            $nrStr = $nr;
            if (is_array($nr)) {
                $idStr = $nr["id"];
                $nrStr = $nr["nr"];
            }
        }
        
        
        // echo ("text $length $nr<br>");
        if (is_string($length)) {
            $idPos = strpos($length,"#id");
            if (is_integer($idPos) AND $idStr) {
                $text = str_replace("#id", $idStr, $length);
                // echo ("#id $text <br>");
                return $text;
            }
            $nrPos = strpos($length,"#nr");
            if (is_integer($nrPos) AND $nrStr) {
                $text = str_replace("#nr", $nrStr, $length);
                // echo ("#nr $text <br>");
                return $text;
            }
            
            list($from,$to) = explode("-",$length);
            if (intval($from) AND intval($to)) {
                // echo ("<h1> Text Frange From $from -> $to </h1>");
                $length = rand($from,$to);
                //echo ("Random = $length<br>");
            } else {
                if (intval($length)) {
                    $length = intval($length);
                }
                if (is_string($length)) {

                }
            }
        }


        
        if (!is_int($length)) {
           
            if (is_string($nr)) {
                 $stringLength = strlen($nr);
                 if ($stringLength) $length = $stringLength;
            }
        }
        
        
        if (!is_int($length) AND is_string($length)) return $length;
        
        $loremStr = $this->textLorem();


        $bisEmpty = strpos($loremStr," ",$length);
        $res = substr($loremStr,0,$bisEmpty);


        return $res;

    }

    function textLorem() {
        $str = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br />";
        $str .= "<br />";
        $str .= "Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br />";
        $str .= "<br />";
        $str .= "Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.<br />";
        $str .= "<br />";
        $str .= "At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero voluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.<br />";
        $str .= "<br />";
        $str .= "Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus.<br />";
        $str .= "<br />";
        $str .= "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br />";
        $str .= "<br />";
        $str .= "Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br />";
        $str .= "<br />";
        $str .= "Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud ex<br />";
        return $str;
    }
    
    function state() {
        // unset( $_SESSION[wireframe]);
        $state = $_SESSION[wireframe];
        
        if (is_null($state)) {
            // echo ("<h1>Kein WireframeState definiert!</h1>");
            $setState = $GLOBALS[cmsSettings][wireframeOn];
            $_SESSION[wireframe] = $setState;
            // echo ("Set State to $setState <br>");            
        }
        
        return $state;
    }
    
    function setState($set) {
        $state = $this->state();
        if ($state == $set) {
            return "noChange";
        }
        
        // echo ("Set WireframeState to $set <br>");
        
        $_SESSION[wireframe] = $set;
        $goPage = cms_page_goPage();
        reloadPage($goPage,0);
    }

    function enabled() {
        $cmsSettings = $GLOBALS[cmsSettings];
        if (is_array($cmsSettings)) {
            return ($cmsSettings[wireframe]);
        }

        // show_array($cmsSettings);
        return 0;
    }

    function editContent($editContent,$frameWidth,$editText,$editClass) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $wireframe = $data[wireframe];
        if ($wireframe) $checked = "checked='checked'";
        else $checked = "";


        $editList = array();
        $editList[showTab] = "More";
        $editList[showName] = $editClass->lga("content","frameText_tabWireframe");//"Wireframe-Optionen";
        


        $add = array();
        $add[text] = $editClass->lga("content","wireframe_wireframeOnText",":");//"Wireframe Optionen an";
        $add[input] = "<input class='editWireframe' type='checkbox' value='1' name='editContent[data][wireframe]' $checked />";
        $add[mode] = "Simple";
        $editList[] = $add;

        $disabled = "";
        if (!$wireframe) {
            // $disabled = "disabled='disabled'";
            $disabled = "readOnly='readOnly'";
            // return $editList;
        }
        
        $wireframeData = $editContent[wireframe];
        if (!is_array($wireframeData)) $wireframeData = array();
                
        $showData = array();
        $showData[formName] = "editContent[wireframe]";
        $showData[dataSource] = "content";
        $showData[editMode] = "SimpleLg"; // array("simple","language","textDb")[0];
        $showData[width] = 300;
        $showData[mode] = "SimpleLine";
        $showData[out] = "input";
        $showData[inputClass] = "editWireframe_option"; 
    
        $showData[title] = "Name";
        $showData[dataName] = "headLineText";
        $showData[text] = $wireframeData[headLineText];             
        $addInput = $editClass->edit_text($showData);
        
        $add = array();
        $add[text] = $editClass->lga("content","wireframe_headlineText",":");//"Überschrift";
        $headLine = $wireframeData[headLine];
        if ($headLine) $checked = "checked='checked'";
        else $checked = "";
        $input =  "<input class='editWireframe_option' type='checkbox' value='1' name='editContent[wireframe][headLine]' $disabled $checked />";
        // if ($headLine) {
            $input .= "Text ".$addInput; //<input class='editWireframe_option' type='text' value='$wireframeData[headLineText]' name='editContent[wireframe][headLineText]' style='width:300px;' />";
        // }
        $add[input] = $input;
        $add[mode] = "More";
        $editList[] = $add;

        $add = array();
        $add[text] = $editClass->lga("content","wireframe_secondHeadlineText",":");//"2. Überschrift";
        $subHeadLine = $wireframeData[subHeadLine];
        if ($subHeadLine) $checked = "checked='checked'";
        else $checked = "";
        
        
        
        
        
        $input =  "<input class='editWireframe_option' type='checkbox' value='1' name='editContent[wireframe][subHeadLine]' $disabled $checked />";
        // if ($subHeadLine) {
            $input .= "Text <input class='editWireframe_option' type='text' value='$wireframeData[subHeadLineText]' $disabled name='editContent[wireframe][subHeadLineText]' style='width:300px;' />";
        // }
        $add[input] = $input;
        $add[mode] = "More";
        $editList[] = $add;

        $add = array();
        $add[text] = $editClass->lga("content","wireframe_textText",":");//;
        $text = $wireframeData[text];
        if ($text) $checked = "checked='checked'";
        else $checked = "";
        $input =  "<input class='editWireframe_option' type='checkbox' value='1' name='editContent[wireframe][text]' $disabled $checked />";
        // if ($text) {
            $input .= "Text <input class='editWireframe_option' type='text' value='$wireframeData[textText]' $disabled name='editContent[wireframe][textText]' style='width:300px;' />";
        // }
        $add[input] = $input;
        $add[mode] = "More";
        $editList[] = $add;


        $add = array();
        $add[text] = $editClass->lga("content","wireframe_imageText",":");//"Bild";
        $image = $wireframeData[image];
        if ($image) $checked = "checked='checked'";
        else $checked = "";
        $input =  "<input class='editWireframe_option'type='checkbox' value='1' name='editContent[wireframe][image]' $disabled $checked />";
        // if ($image) {
            $input .= "Text <input class='editWireframe_option' type='text' value='$wireframeData[imageText]' $disabled name='editContent[wireframe][imageText]' style='width:300px;' />";
        // }
        $add[input] = $input;
        $add[mode] = "More";
        
        $editList[] = $add;



        

        return $editList;
    }

}



function cms_wireframe_Class() {
    $cmsName = $GLOBALS[cmsName];

    $ownFn = $_SERVER[DOCUMENT_ROOT]."/$cmsName/cms/cms_wireframe_own.php";
    // echo ("OnwFile = $ownFn <br>");
    if (file_exists($ownFn)) {
        include($ownFn);
        // echo ("<h1>EXIST</h1>");
        $wireframeClass = new cms_wireframe_own();
    } else {
        $wireframeClass = new cms_wireframe_base();
    }
    return $wireframeClass;
}

function cmsWireframe_image($width,$height,$color=0) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->image($width,$height,$color=0);   
    return $res;
}

function cmsWireframe_frameStart_str($width,$height,$class="",$color=0) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->frameStart($width,$height,$class,$color);
    return $res;
}

function cmsWireframe_frameStart($width,$height,$class="",$color=0) {
    $res = cmsWireframe_frameStart_str($width,$height,$class,$color);
    echo ($res);    
}

function cmsWireframe_frameEnd_str() {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->frameEnd();   
    return $res;
}

function cmsWireframe_frameEnd() {
    $res = cmsWireframe_frameEnd_str();
    echo ($res);   
}

function cmsWireframe_text($length,$nr=0) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->text($length,$nr);
    return $res;
}


function cmsWireframe_enabled() {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->enabled();
    return $res;
}

function cmsWireframe_editContent($editContent,$frameWidth,$editText,$editClass) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->editContent($editContent,$frameWidth,$editText,$editClass);
    return $res;
}

function cmsWireframe_state() {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->state();   
    return $res;
}

function cmsWireframe_setState($set) {
    $wireframeClass = cms_wireframe_Class();
    $res = $wireframeClass->setState($set);   
    return $res;
}


?>
