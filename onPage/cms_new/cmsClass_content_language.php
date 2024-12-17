<?php

class cmsClass_content_language extends cmsClass_content_base {

    function init_language() {
        $this->textId = "text_".$this->contentId;
        
        $this->adminLg = cms_text_adminLg();
        $this->showLg = $this->get_Actual_Language();
        $this->textData = $this->getTextData();   
        $this->editText = $this->textData;
        $this->originalText = $this->textData;

        $this->edit_textDb = array();
        $this->edit_admin_textDb = array();
        
        $this->wireFrameState = cmsWireframe_state();
        $this->wireFrameEnabled = $this->contentData[data][wireframe];
    }
    
    function get_Actual_Language() {
        return cms_text_getLanguage();
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
         
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;
        
        
        if (!is_object($useClass->wireClass)) $useClass->text_wireFrameInit();
        
        if (!is_object($useClass->wireClass)) return "<h1>NO WIREFRAMECLASS </h1>";
        
        // Check Text
        // echo("text_wireText($text,$wireText,$info lg = $this->showLg<br>");
        if (is_array($text)) {
            if ($text["lg_".$useClass->showLg]) $text = $text["lg_".$this->showLg];
            // if ($text[$this->showLg]) $text = $text[$this->showLg];
           
        }
        
        
        if (is_array($wireText)) {
            $wireText = $wireText[$this->showLg];
            // echo ("USE WireText $wireText <br>");
        }
        
        
        $wireData = array();
        $wireData[orgText] = $text;
        $wireData[wireText] = $wireText;
        foreach($info as $key => $value) {
            $wireData[$key]= $value;
        }
        
        $text = $useClass->wireClass->wireframe_getText($wireData);
        return $text;
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
        // echo ("text_getFromArray($textData) <br>");
//        if (is_array($str)) {
//            $lgList = $str;
//        } else {
//            if (substr($str,0,3)=="lg|") {
//                $lgList = array();
//                $help = explode("|",$str);           
//                for($i=1;$i<count($help);$i++) {
//                    list($lgCode,$lgStr) = explode(":",$help[$i]);
//                    if ($lgCode) $lgList[$lgCode] = $lgStr;           
//                    // echo ("add $lgCode $lgStr <br>");
//                }
//            }
//        }
//
//        if (!is_array($lgList)) return $str;
//        $lg = cms_text_getLanguage();
//        //echo ("Found $lgList $lg $lgList[$lg]<br>");
//
//        $str = $lgList[$lg];
//        if ($str) return $str;
//
//        $str = $lgList["lg_".$lg];
//        if (is_string($str)) return $str;
//
//        if ($useDefault) {
//            foreach($lgList as $lgCode => $lgStr) {
//                if ($lgStr) {
//                    $str = "";
//                    if ($_SESSION[showLevel]>3) $str .= $lgCode.":";
//                    $str .= $lgStr;
//                    return $str;
//                }
//            }
//        }
//        return $lgList[$lg];
        
        
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

        $str = $textData[$_SESSION[lg]];
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
        if (!$this->adminLg) echo ("no Edmin LG for $type $code <br />");
        if (!$type) return "no Type";
        if (!$code) {
            $offSet = strpos($type,"_");
            if (!$offSet) return "no Code";
            $code = substr($type,$offSet+1);
            $type = substr($type,0,$offSet);
        }
        // if ($type == "content") echo ("lga(".$type);
        
        
       
        
        if (!is_array($_SESSION[adminText][$type])) {
            if (!is_array($this->edit_admin_textDb[$type])) $this->edit_admin_textDb[$type] = array();
            if (!is_array($this->edit_admin_textDb[$type][$code])) $this->edit_admin_textDb[$type][$code] = array();        
            return $this->lg_admin_notFound($type, $code,$setDefault);
        }
        // if ($type == "content") echo (",".$code.") | ");
        
        $textData = $_SESSION[adminText][$type][$code];
         // Set To EDIT DB
        if (!is_array($this->edit_admin_textDb[$type])) $this->edit_admin_textDb[$type] = array();
        if (!is_array($this->edit_admin_textDb[$type][$code])) $this->edit_admin_textDb[$type][$code] = array();
        $this->edit_admin_textDb[$type][$code] = $textData;
        
        if (!is_array($textData)) return $this->lg_admin_notFound($type, $code,$setDefault);

      

        $str = $textData[$this->adminLg];
        if (!$str) return $this->lg_admin_notFound($type, $code,$textData);

        if ($add) $str .= $add;
        return ($str);
    }

    function lg_admin_notFound($type,$code,$textData=null) {
        $str = $type."_".$code;
        if ($_SESSION[userLevel]>=7) {
            global $adminText_notFound;
            if (!is_array($adminText_notFound)) $adminText_notFound = array();
            if (!is_array($adminText_notFound[$type])) $adminText_notFound[$type] = array();

            $setData = 1;
            if (is_array($textData)) $setData = $textData;
            if (!$adminText_notFound[$type][$code]) $adminText_notFound[$type][$code] = $setData;
            // echo ("Add to adminText not Found $type $code - $textData <br>");
        }
        return $str;
    }

   function text_editDb() {
       $out = "<h3>Text editieren</h3>";
       
       $showList = array();
       $showList["editText"] = array("name"=>"Allgemeiner Text","data"=>$this->edit_textDb);
       $showList["adminText"] = array("name"=>"Editier Text","data"=>$this->edit_admin_textDb);
       
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