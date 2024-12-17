<?php

class cmsClass_content_language extends cmsClass_content_base {

    function init_language() {
        $this->textId = "text_".$this->contentId;
        
        $this->adminLg = $this->pageClass->adminLg; // $this->get_admin_lg();
        $this->showLg = $this->pageClass->showLg; // get_Actual_Language();
        $this->textData = $this->getTextData();   
        $this->editText = $this->textData;
        $this->originalText = $this->textData;

        $this->edit_textDb = array();
        $this->edit_admin_textDb = array();
      
        $this->wireframe_init();
    }
    
    function lgStr($str,$defaultLg=1) {
        return lg::lgStr($str,$defaultLg);
//        if (!is_object($this->pageClass)) {
//            echo ("NO PAGE CLASS in content_lgStr()");
//            return "NO PAGE CLASS in content_lgStr()";
//        }
//        return $this->pageClass->lgStr($str,$defaultLg);
    }
    
    function lgClear($str) {
        return $this->pageClass->lgClear($str);
    }
    
    function get_Actual_Language() {
        return lg::$show_lg();
//        $lg = $this->session_get(lg);
//        if ($lg) return $lg;
//        return cms_text_getLanguage();
    }
    
    function get_admin_lg() {
        return lg::adminLg();
//        $adminLg = $this->session_get(adminLanguage);
//        if ($adminLg) return $adminLg;
//        return cms_text_adminLg();
    }

    function getTextData() {
//        if ($_POST[editText]) {
//            $res = $this->text_save($_POST[editText]);
//        } else {
            $all_languages = 1;
            $res = cms_text_getForContent($this->textId,$all_languages);
        //}
        return $res;
    }
    
    function text_getForCode($code) {
        // echo ("text_getForCode($code) <br>");
        $textData = $this->textData[$code];
        if (is_array($textData)) {
            $text = $this->text_getFromArray($textData);
            return $text;
        }
    }
    
    
    
    function text_wireText($text,$wireText,$info=array()) {
        echo ("<h1>OLD text_wireText </h1>");
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        
        if (!is_object($useClass->wireClass)) $useClass->text_wireFrameInit();
        
        if (!is_object($useClass->wireClass)) return "<h1>NO WIREFRAMECLASS </h1>";
        
        // Check Text
      
        if (is_array($text)) $text = $this->lgStr($text);
      
        
        if (is_array($wireText)) {
            $wireText = $this->lgStr($wireText,0);
            // echo ("USE WireText $wireText <br>");
        }
        
        
        $wireData = array();
        $wireData[orgText] = $text;
        $wireData[wireText] = $wireText;
        foreach($info as $key => $value) {
            $wireData[$key]= $value;
        }
        
        foreach ($wireData as $key => $value) {
            echo ("wireData $key => $value <br>");
        }
        
        $text = $useClass->wireClass->wireframe_getText($wireData);
        return $text;
    }
    
    
    function wireframe_init() {
        if (!$this->pageClass->wireframeEnabled) {
            // echo "WIREFRAME is not Enabled !<br>";     
            $this->wireframeEnabled = 0;
            $this->wireframeState = 0;
            $this->wireframeData = array();
            $this->wireframeConentEnabled = 0;
            return 0;
        }
        
        $this->wireframeEnabled = 1;
        
        $this->wireframeState = $this->pageClass->wireframeState;
        // echo ("Wireframe STate = $this->wireframeState <br>");
        $this->wireframeData = $this->pageClass->wireframeData;
        
        $this->wireframeContentEnabled = $this->contentData[data][wireframe];
        if ($this->wireframeContentEnabled) {
            $this->wireframeContentData = $this->contentData[wireframe];
            // foreach ( $this->wireframeContentData as $k => $v) echo ("wd $k = $v <br>");
        } else {
            $this->wireframeContentData = array();            
        }        
    }
    
    
    function wireframe_wireText($wireType) {
        if (!$wireType) return "no wireType in wireframe_wireText()";
        $wireText = $this->wireframeContentData[$wireType."Text"];
        if (!$wireText) return "noWireText for $wireType";
        if (is_array($wireText)) {
            $wireText = $this->lgStr($wireText,0);
            if (substr($wireText,0,5)=="<span") {
                $end = strpos($wireText,"</span>");
                $wireText = substr($wireText,$end+7);
            }
            if (!is_string($wireText)) $wireText = "";
        }
        return $wireText;
    }
    
    function wireframe_use($wireType) {
        if (!$this->wireframeState) return 0;
        $wireOn = $this->wireframeContentData[$wireType];
        
        // checkMobile ???
        if ($this->mobileEnabled) {
            $wireOn = $this->wireframe_checkMobile($wireType,$wireOn);            
        }
        return $wireOn;
    }
    
    
    
    function wireframe_checkMobile($wireType,$wireOn) {
        $mobileShow = $this->wireframeContentData[$wireType."Mobile"];
        if (!$mobileShow) return $wireOn;
        
        $mobileState = $this->targetData[target];
        
        
        if ($mobileState == "pc") { // desktop Ansicht
            if ($mobileShow == "only") {
                // Ansicht ist Pc / Wireframe only on Mobil ==> 0
                return 0;
            }
            // Ansicht = PC / Mobile State egal ==> $wireOn
            return $wireOn;
        }
        
        // ANSICHT ist MOBIL
        
        switch ($mobileShow) {
            case "only" :
                // SHow only On Mobile => 1
                return 1;
                break;
            case "hide" :
                // Hide on Mobile => 0;
                return 0;
                break;
            case "show" :
                // SHOW on Mobile ==> 1
                return 1;
                
        }
        echo ("<h1>wireframe_checkMobil wireType =$wireType wireOn = $wireOn mobilShow = $mobileShow mobilState = $mobileState </h1>");
        
        
        return $wireOn;
        
        
    }

    function wireframe_text($wireData) {
        if (!is_array($wireData)) return "wireData is no array($wireData) in Text";
        /* DEFINITION 
        $wireData = array();
        $wireData[orgText]  = $text;          // Normaler Text
        $wireData[wireText] = $wireImageText; // WireText aus dataWireframe 
        $wireData[nr]       = $data[nr];
        $wireData[id]       = $data[id];
        $wireData[name]     = $data[name];
        $wireData[debug]    = 0;
         */   
        
        $debug = 0;
        if (is_integer($wireData[debug])) $debug = $wireData[debug];
        
        $wireType = $wireData[type];
        
        $str .= "";
        // orginal Text
        $orgText = $wireData[orgText];
        if (is_array($orgText)) {
            $orgText = $this->lgStr($orgText);
            // if ($orgText[$this->showLg]) $orgText = $wireText[$this->showLg];
            $wireData[$orgText] = $orgText;            
        }
        if ($debug) $str .= "org = $orgText <br>" ;
        if (is_string($orgText)) $orgLength =  strlen($orgText);
        else $orgLength = 0;
        if ($debug) $str .= "orgText=$orgText lenght=$orgLength <br />";
            
        // wireFrame Text
        $wireText = $wireData[wireText];
        if (is_null($wireText)) {
            if (!$wireType) return "noWireText in wireframe_text() ";
            $wireText = $this->wireframe_wireText($wireType);
            // echo ("<b>Wiretext $wireText get for $wireType </b> <br /> ");
            $wireData[wireText] = $wireText;                        
        }
        
        if (is_array($wireText)) {
            $wireText = $this->lgStr($wireRext);
            // if (is_string($wireText[$this->showLg])) $wireText = $wireText[$this->showLg];
            $wireData[wireText] = $wireText;         
            // echo ("GET Lg for '$wireText' lg = $this->showLg orgLength = $orgLength <br>");
            if (!is_string($wireText)) show_array($wireText);
        }
        if ($debug) $str .= "wireText = $wireText  <br>";
        
        if (is_string($wireText) AND $wireText === "") {
            $wireData[wireText] = $wireText; 
            $wireText = 0;
            
            if ($orgLength == 0) {
                // echo ("NO wireText and No $orgLength for $wireType <br>");
                return "";
            }
        }
        
        if (!is_string($wireText) AND $orgLength) {
            if ($debug) $str .= "No wireText defined - use Length from orgText ($orgLength)";
            $wireLength = $orgLength;
        } else {
            
            // check Length array (50-200) 500
            // echo ("wT = '$wireText' type=$wireType <br>");
            list($from,$to) = explode("-",$wireText);
            if (intval($from) AND intval($to)) {
               $wireLength = rand($from,$to);
                if ($debug) $str = "range from $from - $to ==> $wireLength <br>";
                //echo ("Random = $length<br>");
            } else {
                $wireVal = intval($wireText);
                if ($wireVal AND "$wireVal" == trim($wireText) ) {
                    $wireLength = intval($wireText);
                    if ($debug) $str .= "length = $wireLength <br>";
                }         
            }
            
            if (!$wireLength) { // no Lenth 
                if ($debug) $str .= "no Length get for $wireText <br>";            
            }
        }
       
        if ($wireLength) {
            if ($debug) $str.= "loremString with $wireLength chars <br>";
            $str .= $this->wireframe_loremStr($wireLength);
        } else {
            if ($debug) $str .= "No StringLength get -> use wireText $wireText <br>";
            $off = strpos($wireText,"#");
            if (is_integer($off)) {
                foreach ($wireData as $key => $value) {
                    if (!is_array($value)) continue;
                    $lgStr = $this->lgStr($value);
                    if (is_string($lgStr)) $wireData[$key] = $lgStr;
                    // echo ("wireData $key => $value / $lgStr <br>");
                }
                
                $str .= $this->wireframe_textReplace($wireText,$wireData);
            } else {
                $str .= $wireText;
            }                
        }
        return $str;
    }
    
    function wireframe_loremStr($wireLength) {
        
        $loremStr = $this->wireframeData[lorem];
        
        
        $bisEmpty = strpos($loremStr," ",$wireLength);
        $res = substr($loremStr,0,$bisEmpty);


        return $res;
    }
    
    function wireframe_textReplace($wireText,$wireData) {
        $str = "";
        $debug = 0;
        $start = 0;
        
        if ($debug) $str .= "getRepace for '$wireText' <br>";
        
        $find    = array();
        $replace = array();
        // echo ($wireText."<br>");
        $off = strpos($wireText,"#",$start);
        
        while (is_integer($off) ) {
            $nr ++;
        
            $end = strpos($wireText," ",$off);
            if (!$end) $end = strlen ($wireText);
            $secondEnd = strpos($wireText,"#",$off+1);
            if (!$secondEnd) $secondEnd = strlen ($wireText);
            if ($secondEnd < $end) {
                // echo ("secondEnd($secondEnd < end($end) off=$off <br> ");
                $end = $secondEnd+1;
                $inh = substr($wireText,$off,$end-$off);
                // echo ("found = $inh <br>");
            }
                    
           
            
            $inh = substr($wireText,$off,$end-$off);
            if (strlen($inh) > 1) {
                $code = substr($inh,1);
                $find[$inh] = $code;
            }
            $off = strpos($wireText,"#",$end);
            if ($debug) $str .= " $nr s=$off e=$end i='$inh' newOff ='$off' <br>";
        }
        
        $formatStr = 0;
        
        foreach ($find as $key => $code) {
            if (substr($code,0,4) == "text") {
                $wireLength = 0;
                $lengthStr = substr($code,4);
                list($from,$to) = explode("-",$lengthStr);
                if (intval($from) AND intval($to)) {
                    $wireLength = rand($from,$to);
                } else {
                    if (intval($lengthStr)) $wireLength = intval($lengthStr);
                }         
                $rep = "lorem".$length;
                if ($wireLength) {
                    $rep = $this->wireframe_loremStr($wireLength);
                } else {
                    $rep = "";
                }
            } else {
                $rep = 0;                
                switch ($key) {
                    case "#a#" : $formatStr = 1; $rep="no"; break;
                    case "#k#" : $formatStr = 1; $rep="no"; break;
                    case "#A#" : $formatStr = 1; $rep="no"; break;
                    case "#f#" : $formatStr = 1; $rep="no"; break;
                    case "#u#" : $formatStr = 1; $rep="no"; break;
                    
                    case "#org" :  $rep = $wireData[orgText]; break;
                    case "#pageName" : $rep = $this->pageName; break;
                    case "#pageTitle" : 
                        $pageTitle = $this->pageData[title];
                        if (is_array($pageTitle)) $pageTitle = $this->lgStr($pageTitle); // $pageTitle[$this->showLg];
                        $rep = $pageTitle; 
                        break;
                        
                    case "#pageId" : $rep = $this->pageId; break;
                    default :
                        $rep = $wireData[$code];                        
                }
//                if ($key == "#org") {
//                    $rep = $wireData[orgText];
//                } else {
//                    $rep = $wireData[$code];
//                }
                if (!$rep) {
                    if ($debug) $str .= "not found $key in wireData <br>";
                    $rep = "";
                }
            }
            if ($debug) $str .= "find '$key' rep='$rep' <br>";
            if ($rep != "no") {
                $wireText = str_replace($key, $rep,$wireText);            
            }
            
        }
        
        if ($formatStr) $wireText = $this->showText($wireText);
        $str .= $wireText;
        return $str;
    } 
    
    function wireframe_image($wireData) {
        if (!is_array($wireData)) return "wireData is no array($wireData) in Image";
        /* DEFINITION 
        $wireData = array();
        $wireData[wireText] = $wireImageText; // WireText aus dataWireframe 
        $wireData[width]    = $width;
        $wireData[height]   = $height;
        $wireData[nr]       = $data[nr];
        $wireData[id]       = $data[id];
        $wireData[name]     = $data[name];
        $wireData[debug]    = 0;
        
        */   
        
        
        $classStr = $wireData["class"];
        $idStr = $wireData["id"];
        $styleStr = $wireData["style"];
        
        $debug = 0;
        if (is_integer($wireData[debug])) $debug = $wireData[debug];  
        
        $width    = $wireData[width];
        $height   = $wireData[height];
        
        $witdh = intval($width);
        $height = intval($height);
        if (!$width) return "noWidth in wireframe_image() ($wireData[width]) ";
        if (!$height) return "noHeight in wireframe_image() ($wireData[height]) ";

        $wireType = $wireData[type];
        $wireText = $wireData[wireText];
        if (is_null($wireText)) {
            if (!$wireType) return "noWireText in wireframe_image() ";
            $wireText = $this->wireframe_wireText($wireType);
            $wireData[wireText] = $wireText;            
        }
        
        
        $str = "";
        
        if ($wireText) { // WireBox with Content
            $str .= $this->wireframe_imageBox_start($width,$height,$wireData);
            $str .= "<div class='wireframe_FrameText' style='padding-top:".($height/2-8)."px;' >";
            $str .= $this->wireframe_text($wireData);
            $str .= "</div>";
            $str .= $this->wireframe_imageBox_end();
            
        } else { // wireBox without Content
            $str .= $this->wireframe_imageBox($width,$height,$classStr,$idStr);            
        }
        return $str;
    }
    
    function wireframe_imageBox($width,$height,$classStr="",$idStr="",$color=0) {
        $wireImageFile = $this->wireframe_imageFile($width,$height,$color);
        $class = "noBorder";
        if ($classStr) $class.= " ".$classStr;
        if ($idStr) $idStr = "id='$idStr'";
        $str = "<image src='$wireImageFile' $idStr class='$classStr' />";
        return $str;
    }
    
    function wireframe_imageBox_start($width,$height,$wireData=array(),$color=0) {
        $backImage = $this->wireframe_imageFile($width,$height,$color);
        
        
        $class = "wireframe_Frame";
        if ($wireData["class"]) $class = $wireData["class"]." ".$class;
        if ($wireData["id"]) $idStr = "id='".$wireData[id]."'";
        
        
        $style = "background-image:url(".$backImage.");width:".$width."px;height:".$height."px;";
        if ($wireData[style]) $style .= $wireData[style];
        // $style .= "display:table-cell;vertical-align:middle;text-align:center;";
        $str  = "<div class='$class' $idStr style='$style' >";
        return $str;
    }
    
    function wireframe_imageBox_end() {
        // alt 
        // $str = cmsWireframe_frameEnd_str();
        $str = "</div>\n";
        return $str;
    }
    
    function wireframe_imageFile($width,$height,$color=0) {
        // alt 
        $wireImage = cmsWireframe_image($width, $height);
        
         
        
        $targetPath = $this->wireframeData[targetPath];
        $backColor  = $this->wireframeData[backColor];
        $lineColor  = $this->wireframeData[lineColor];
        
        // echo ("tp = $targetPath bC =$backColor lC = $lineColor <br>");
        return $wireImage;
        
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
    
    
    
             

    function text_wireImage($width, $height,$wireframeText=null,$wireInfo=array()) {
        $divClass  = $wireInfo[divClass];
        $wireColor = $wireInfo[wireColor];
    
       
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        if (!is_object($useClass->wireClass)) $useClass->text_wireFrameInit();

        if (!is_object($useClass->wireClass)) return "<h1>NO WIREFRAMECLASS </h1>";
        $str = "";

        if ($wireframeText) {
             $str .= $useClass->wireClass->frameStart($width,$height,$divClass,$wireColor);

             $str .= $wireframeText;
             $str .= $useClass->wireClass->frameEnd(); //$width,$height,$divName,$color);
        } else {
            $str .= $useClass->wireClass->image($width,$height,$wireColor);
        }
        return $str;
    }
    
    function text_wireFrameInit() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        $useClass->wireClass = cms_wireframe_Class();
        $useClass->wireClass->setMainClass($this);        
    }
    
    function text_getFromArray($textData) {
       
        $text = $this->lgStr($textData);
        return $text;
        
        if (is_array($textData)) {
            $text = $textData["lg_".$this->showLg];
            if (is_string($text)) return $text;
            
            $text = $textData[$this->showLg];
            if (is_string($text)) return $text;           
        }
      
    }

    function text_saveNew($saveData,$doSave=0) {
       if (!is_array($saveData)) {
           return 0;
       }
    
      //  $textId = $_POST[textId];
//       $editId = $_GET[editId];
//       if (!$editId) {
//           cms_errorBox("NO EditId by save_Text");
//           return 0;
//       }
        
       
       // foreach ($this->textData as $key => $value) echo ("textData $key = $value <br> ");
        
       $contentId = "text_".$this->contentId; // textId;
       echo ("<h1>Save $contentId</h1>");

       $change = 0;
       $error = 0;
       
       $this->editText = array();

       foreach ($saveData as $textCode => $saveTextData) {
           
            $compareData = $this->originalText[$textCode];
            if (!is_array($compareData)) $compareData = array();
            $change = 0;

            $textId = intval($saveTextData[id]);
            $hasContent = 0;
            foreach ($saveTextData as $key=> $value) {
                if (substr($key,0,3) == "lg_" AND strlen($value)) $hasContent++;
                if ($value != $compareData[$key]) {
                    // echo ("Change <b>$textCode</b> $key from '". $compareData[$key]."' to '$value' <br />");
                    $change++;
                    $this->textData[$textCode][$key] = $value;
                    if ($key == "lg_".$this->showLg) {
                        $this->textData[$textCode][text] = $value;
                    }
                }
            }

            if ($hasContent == 0 AND $textId) {
                if ($doSave) {
                    $delResult = cms_text_delete($textId);
                    echo ("KEIN INHALT in '$textCode' id = $textId result = $delResult<br />");
                    if (!$delResult) {
                         $error++;
                    }
                    unset($this->textData[$textCode]);
                    continue;
                } else { // No Save - only update
                    continue;
                }
            }


            if (!$hasContent) continue;

            $this->editText[$textCode] = $saveTextData;

            if (!$doSave) { // no Save only update
                continue;
            }

            if (!$change) continue;




            $saveTextData[name] = $textCode;
            $saveTextData[contentId] = $contentId;

            $standardSave = 0;
            switch ($textCode) {
                case "headline" :
                    $standardSave = 1;
                    break;
                case "text" :
                    $standardSave = 1;
                    break;
                default :
                    if (substr($textCode,0,6) == "button") {
//                        echo ("UPDATE BUTTON $textCode changes = $change <br />");
//                        foreach ($saveTextData as $key => $value ) echo ("save Button $key = $value <br />");
//                        $error++;
                        $standardSave = 1;
                    } else {
                        $standardSave = 1;
                        break;
                    }
            }


            if ($standardSave) {
                $doSave = 1;
                if ($doSave) {
                    $res = cms_text_save($saveTextData);
                    if (!$saveTextData[id]) {
                        $this->textData[$textCode][id] = $res;
                        echo ("INSERT NEW $textCode id = $res <br>");
                        $res = 1;
                    }
                }
                else $res = "deactivate";

                if ($res != 1) {
                    $error++;
                    if ($out) $out .= "<br />";
                    $out .= "Save Result for $textCode '$res' ";
                }
                else $change++;
            }

        }
       

        return array("change"=>$change,"error"=>$error,"out"=>$out);
    }

    function text_default_save() {
        $defaultText = $_POST[defaultText];
        $adminText   = $_POST[adminText];
        $error = 0;
        $check = 0;
        $out   = "";
        if (is_array($defaultText)) {
            $check++;
            $res = cms_defaultText_save($defaultText);
            $res = 0;
            if ($res) {
                $error++;
                $out .= "Fehler beim Sace Default_Text";
            }
                // += cms_defaultText_save($defaultText);            
        }
        if (is_array($adminText)) {
            $check++;
            $res= cms_adminText_save($adminText);         
            if ($res) {
                $error++;
                if ($out) $out .= "<br />";
                $out .= "Fehler beim Save Admin_Default_Text";
            }
        }
        $res = array("error"=>$error,"check"=>$check,"out"=>$out);
        return $res;
    }

    function lg($type=0,$code=0,$add="") {
        if (!$type) return "no Type";
        if (!$code) {
            $offSet = strpos($type,"_");
            if (!$offSet) return "no Code";
            $code = substr($type,$offSet+1);
            $type = substr($type,0,$offSet);
        }

        if (!is_array($_SESSION[defaultText][$type])) {
            if (!is_array($this->edit_textDb[$type])) $this->edit_textDb[$type] = array();
            if (!is_array($this->edit_textDb[$type][$code])) $this->edit_textDb[$type][$code] = array();        
            return $this->lg_notFound($type, $code,$setDefault);
        }
        

        $textData = $_SESSION[defaultText][$type][$code];
        // Set To EDIT DB
        if (!is_array($this->edit_textDb[$type])) $this->edit_textDb[$type] = array();
        if (!is_array($this->edit_textDb[$type][$code])) $this->edit_textDb[$type][$code] = array();
        $this->edit_textDb[$type][$code] = $textData;
        
        
        
        
        if (!is_array($textData)) return $this->lg_notFound($type, $code);

        
        $str = $textData[$this->showLg];
        if (!$str) return $this->lg_notFound($type, $code,$textData);

        if ($add) $str .= $add;
        return ($str);
    }

    function lg_notFound($type,$code,$textData=null) {
        $str = $type."_".$code;
        if ($_SESSION[userLevel]>=7) {
            global $defaultText_notFound;
            if (!is_array($defaultText_notFound)) $defaultText_notFound = array();
            if (!is_array($defaultText_notFound[$type])) $defaultText_notFound[$type] = array();


            $setData = 1;
            if (is_array($textData)) $setData = $textData;
            if (!$defaultText_notFound[$type][$code]) $defaultText_notFound[$type][$code] = $setData;
            // echo ("ADD $type $code to $defaultText_notFound <br />");
        }
        return $str;
    }

    function lga($type=0,$code=0,$add="",$setDefault=null) {
        return lg::lga($type,$code,$add,$setDefault);        
    }

   
   function text_editDb() {
       $out = "<h3>Text editieren</h3>";
       
       global $edit_adminText;
       global $edit_textText;
       $showList = array();
       $showList["editText"] = array("name"=>"Allgemeiner Text","data"=>$this->edit_textDb);
      //  $showList["adminText"] = array("name"=>"Editier Text","data"=>$this->edit_admin_textDb);
       $showList["newAdminText"] = array("name"=>"Editier Text Neu","data"=>$edit_adminText);
       foreach ($showList as $area => $data) {
           $editTextName = $data[name];
           $editTextData = $data[data];
           
           $out .= "<div id= 'cmsEditText_$area' class='cmsEditText cmsEditText_areaSelect' >";
           $out .= "<h4>$editTextName</h4>";
           $out .= "</div>";
           
           $out .= $this->text_editDb_showArea($area,$editTextData);
           
       }
       return $out;
   }
       
    function text_editDb_showArea($area,$data,$setHidden=1,$mainDiv=1) {
        global $adminText_notFound;
        
        $setHiddenClass = "cmsEditText_hidden";
        if (!$setHidden) $setHiddenClass = "";
        
        $outArea = "";
        $missArea = 0;
        
      
        foreach ($data as $type => $typeValue) {
            
            $outType = "";
            $missType = 0;
            
            // selectList Type
            $outType .= "<div  id='cmsEditText_type_$type' class='cmsEditText_typeSelect cmsEditText_typeList_$area' >";
            $outType .= "$type";
            $outType .= "</div>";
            
            $outType .= "<div id='".$area."**".$type."' class='cmsEditText_codeList cmsEditText_codeList_$type $setHiddenClass' >";
            ksort($typeValue);
            
            $outCode = "";
            $missCode = 0;
            foreach ($typeValue as $code => $codeValue) {
                
            
                if (!is_array($codeValue)) $codeValue = array();
                $adminLgStr = $codeValue[$this->adminLg];
                
                if ($adminLgStr) {
                    $hidden = $setHiddenClass;
                    $select = "";
                } else {
                    $hidden = "";
                    $select = "cmsEditText_codeSelect_selectActive";
                    $missCode++;
                }
                
                $outCode .= "<div id='cmsEditText_code_$code' class='cmsEditText_codeSelect cmsEditText_codeList_$code $select' >";
                $outCode .= "<span class='cmsEditText_codeStr'>$code</span>";
                $outCode .= "<span class='cmsEditText_lgStr'>$adminLgStr</span>";
                $outCode .= "</div>";
                
                $outCode .= "<div id='cmsEditText_".$area."_".$type."_".$code."' class='cmsEditText_editLanguage $hidden' >";
                
                $formName = "";
                switch ($area) {
                    case "adminText" :
                        $formName = "adminText";
                        break;   
                   case "newAdminText" :
                        $formName = "adminText";
                        if (is_array($adminText_notFound[$type][$code])) {
                            foreach ($adminText_notFound[$type][$code] as $k => $v) {
                                $codeValue[$k] = $v;
                            }
                        }
                        
                        break;   
                    case "editText" :
                        $formName = "defaultText";
                        break;   
                    default :
                        echo ("Unkown $area in text_editDb_showArea <br /> ");
                }
                
                $formName .= "[".$type."]";
                $formName .= "[".$code."]";
                
                $outCode .= " id=$codeValue[id] -<input type='hidden' name='".$formName."[id]' value='$codeValue[id]' />";
                $outCode .= " dt:<input type='text' style='width:".$textWidth."px;'  name='".$formName."[dt]' value='$codeValue[dt]' />";
                $outCode .= " en:<input type='text' style='width:".$textWidth."px;'  name='".$formName."[en]' value='$codeValue[en]' />";
                $outCode .= " fr:<input type='text' style='width:".$textWidth."px;'  name='".$formName."[fr]' value='$codeValue[fr]' />";
                $outCode .= "</div>";
                                
                
            }
            
            $typeHidden = $setHiddenClass;
            $typeSelect = "";
            if ($missCode) {
                $typeHidden = "";
                $typeSelect = "cmsEditText_typeSelect_selectActive";
                $missType++;
            }
            
            $outType = "<div  id='cmsEditText_type_$type' class='cmsEditText_typeSelect cmsEditText_typeList_$area $typeSelect' >";
            $outType .= $type;
            $outType .= "</div>";
            
            
            // Start Type LIST for area
            $outType .= "<div id='".$area."**".$type."' class='cmsEditText_codeList cmsEditText_codeList_$type $typeHidden' >";
            $outType .= $outCode;
            $outType .= "</div>";
            
            
            // Add Type to AREA
            $outArea .= $outType;
//            $outArea .= $outCode;
//            $outArea .= "</div>";
            
       }
       
       $areaHidden = $setHiddenClass;
       $areaSelect = "";
       if ($missType) {
           $areaHidden = "";
           $areaSelect = "cmsEditText_area_selectActive";
       }
       
       
       
       if ($mainDiv) $out .= "<div id='' class='cmsEditText_areaList cmsEditText_areaList_$area $areaHidden $areaSelect' >";

       $out .= $outArea;
       // close cmsEdit_typeList;
       if ($mainDiv) $out .= "</div>";
       return $out;
       
   }
    
}

?>