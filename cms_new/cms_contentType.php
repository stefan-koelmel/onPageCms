<?php // charset:UTF-8

class cmsType_contentTypes_base {
    
    function lg($type=0,$code=0,$add="") {
        if (!$type) return "no Type";
        if (!$code) {
            $offSet = strpos($type,"_");
            if (!$offSet) return "no Code";
            $code = substr($type,$offSet+1);
            $type = substr($type,0,$offSet);
        }
        
        if (!is_array($_SESSION[defaultText][$type])) return $this->lg_notFound($type, $code);
        
        $textData = $_SESSION[defaultText][$type][$code];
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
            echo ("ADD $type $code to $defaultText_notFound <br />");
        }
        return $str;
    }
    
    function lga($type=0,$code=0,$add="",$setDefault=null) {
        if (!$type) return "no Type";
        if (!$code) {
            $offSet = strpos($type,"_");
            if (!$offSet) return "no Code";
            $code = substr($type,$offSet+1);
            $type = substr($type,0,$offSet);
        }
        
        if (!is_array($_SESSION[adminText][$type])) return $this->lg_admin_notFound($type, $code,$setDefault);
        
        $textData = $_SESSION[adminText][$type][$code];
        if (!is_array($textData)) return $this->lg_admin_notFound($type, $code,$setDefault);
        
        $adminLg = cms_text_adminLg();
        
        $str = $textData[$adminLg];
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
    
    
    ////////////////////////////////////////////////////////////////////////////
    // CONTENT EDIT                                                           //
    ////////////////////////////////////////////////////////////////////////////
    
    function editContent_settings($editContent,$frameWidth,$frameText) {
        
        $showType = 1;
        $showLevel = 1;
        $showToLevel = 1;
        $showContentName = 1;
        
        
        $settings = array();
        $settings[showName] = $this->lga("content","tabSettings"); //"Einstellungen";
        $settings[showTab] = "More";

        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_settingsIdText",":");//ContentId";        
        $addData["input"] = "<input type='text' readonly='readonly' name='editContent[id]' value='$editContent[id]' />";
        $addData["mode"] = "Admin";
        $settings[] = $addData;


        if ($showType) {    // Typ
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsTypeText",":");//"Content-Typ:";
            if ($layoutName OR $_GET[editLayout]) {
                 $addData["input"] = cms_contentLayout_selectType($editContent[type],"editContent[type]","button");
            } else {
                 $addData["input"] = cms_content_SelectType($editContent[type],"editContent[type]","button");
            }
            $addData["mode"] = "More";
            $settings[] = $addData;
        }

        if ($showLevel) { // UserLevel
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsLevelText",":");//"Anzeigen ab";
            $addData["input"] = cms_user_selectLevel($editContent[showLevel],$_SESSION[userLevel],"editContent[showLevel]");
            $addData["mode"] = "More";
            $settings[] = $addData;

            // echo ("HIER $editContent[showLevel] <br>");
            if ($editContent[showLevel] == 3) {
                $addData = array();
                $addData["text"] = "Spezielle BenutzerSteuerung";
                $addData["input"] = cmsUser_selectUserList($data,$_SESSION[userLevel],"editContent[data]");
                $addData["mode"] = "More";
                $settings[] = $addData;
            }
        }

        if ($showToLevel) { // UserLevel
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsLevelToText",":");//"Anzeigen bis";
            $addData["input"] = cms_user_selectLevel($editContent[toLevel],$_SESSION[userLevel],"editContent[toLevel]");
            $addData["mode"] = "More";
            $settings[] = $addData;
        }

        // $contentType_class = cms_contentTypes_class();
        $special_viewFilter = $this->use_special_viewFilter($editContent);
        if (is_array($special_viewFilter)) {
            foreach($special_viewFilter as $key => $value) {
                $settings[] = $value;            
            }
        }

        if ($showContentName) { // ContentName
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsContentNameText",":");"Inhalt verfügbar unter";
            $addData["input"] = "<input type='text' value='$editContent[contentName]' style='min-width:196px;' name='editContent[contentName]'>";
            $addData["mode"] = "Admin";
            $settings[] = $addData;
        }
        return $settings;
    }
    
    function editContent_frameSettings($editContent,$frameWidth,$frameText) {
        $frameSettings = array();
         
        $frameSettings[showName] = $this->lga("content","tabFrame");
        $frameSettings[showTab] = "Simple";
        
        // Rahmen Titel
        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameTitleText",":");
        $addData["input"] = "<input type='text' value='$editContent[title]' style='min-width:196px;' name='editContent[title]'>";
        $addData["mode"] = "More";
        $frameSettings[] = $addData;
        
        
       
        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameStyleText",":");//"Rahmen Stil";
        $addData["input"] = cms_content_selectStyle("frameStyle",$editContent[frameStyle],"editContent[frameStyle]",array("submit"=>1));
        $addData["mode"] = "Simple";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameCloseAbleText",":");//"Rahmen schließbar";
        $frameClose = $editContent[data][frameClose];
        if ($frameClose) $checked = "checked='checked'";
        else $checked = "";
        $input =  "<input type='checkbox' $checked value='1' name='editContent[data][frameClose]' />";
       
        if ($frameClose) {
            $frameCloseLoad = $editContent[data][frameCloseLoad];
            if ($frameCloseLoad) $checked = "checked='checked'";
            else $checked = "";
            $input .= $this->lga("content","frameText_frameClosedText",":")."<input type='checkbox' $checked value='1' name='editContent[data][frameCloseLoad]' />";
            $input .= $this->lga("content","frameText_frameCloseTitleText",":");
            // $input .= "<input type='text' value='".$editContent[data][frameCloseText]."' name='editContent[data][frameCloseText]' />";
            
            $showData = array();
            $showData[formName] = "editContent[data]";
            $showData[dataSource] = "content";
            $showData[editMode] = "SimpleLg"; // array("simple","language","textDb")[0];
    
    
            $showData[title] = "Name";
            $showData[dataName] = "frameCloseText";
            $showData[text] = $editContent[data][frameCloseText];
            $showData[width] = 200;
            $showData[mode] = "SimpleLine";
            $addData = $this->edit_text($showData);
            $input .= $addData[input];
//            foreach ($addData as $key => $value) {
//                echo ("key $key => $value <br>");
//            }
                
//   
//            $addData = array();
//            $addData[text] = "Name";
//            $addData[input] = "<input type='text' name='".$formName."[title]' style='width:".($inputWidth/2-4)."px' value='$pageData[title]' >\n";
//            $addData[mode] = "Simple";
//            
//            
//            $input .= $this->editContent_text($type, $textData, $showData);
            
            
        }
        $addData[input] = $input;
        $addData["mode"] = "More";
        $frameSettings[] = $addData;


//        $addData = array();
//        $addData["text"] = "Umbruch";
//        $addData["input"] = cms_content_selectStyle("float",$editContent[frameFloat],"editContent[frameFloat]");
//        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameLinkText",":");// "Rahmen Link";
        $addData["input"] = cms_page_SelectMainPage($editContent[frameLink], "editContent[frameLink]");
        $addData["mode"] = "More";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameWidthText",":");//"Rahmen-Breite";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameWidth]' value='".$editContent[frameWidth]."' >";
        $addData["mode"] = "Admin";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameHeightText",":");//"Rahmen-Höhe";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameHeight]' value='".$editContent[frameHeight]."' >";
        $addData["mode"] = "Admin";
        $frameSettings[] = $addData;


        $frameStyle = $editContent[frameStyle];
        return $frameSettings;
    }
    
    
    function editContent_frameText($editContent,$frameWidth,$editText=0) {
        $data = $editContent[data];

        if (!is_array($data)) $data = array();

        $id = $editContent[id];
        $pageId = $editContent[pageId];
        $editId = $_GET[editId];
        $editMode = $_GET[editMode];

        $contentCode = "text_$id";
        if (!is_array($editText)) {
            $editText = cms_text_getForContent($contentCode,1);
        }
        
        $getText = $_POST[editText];
        if (is_array($getText)) {
            $editText = $getText;
        }
//        if (!is_array($getText)) {
//            // echo ("Get Text from Database<br>");
//            
//        } else {
//            // echo ("get Text form POST<br>");
//           // 
//        }
        // show_array($editText);
        $res = array();
        $res[showTab] = "More";
        $res[showName] = $this->lga("content","tabFrameText");


        $addData = array();
        $addData["text"] = "hidden-Text Id";
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
        $res[] = $addData;

        $showData = array();
        $showData[css] = 1;
        $showData[view] = "text";
        $showData[color] = 0;
        $showData[width] = $frameWidth;
        $showData[name] = $this->lga("content","frameText_headlineText");
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $this->editContent_text("frameHeadline",$editText[frameHeadline], $showData);
        $res[] = $addData;


        $showData = array();
        $showData[css] = 1;
        $showData[view] = "textarea";
        $showData[color] = 0;
        $showData[width] = $frameWidth;
        $showData[height] = 30;
        $showData[name] = $this->lga("content","frameText_topText");// "Rahmen-Text Oben";
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $this->editContent_text("frameHeadtext",$editText[frameHeadtext], $showData);
        $res[] = $addData;

        $showData = array();
        $showData[css] = 1;
        $showData[view] = "textarea";
        $showData[color] = 0;
        $showData[width] = $frameWidth;
        $showData[height] = 30;
        $showData[name] = $this->lga("content","frameText_bottomText");"Rahmen-Text Unten";
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $this->editContent_text("frameSubtext",$editText[frameSubtext], $showData);
        $res[] = $addData;

        return $res;
    }
    
    function editContent_systemFrame($editContent,$frameWidth,$frameText) {
        $editList = cmsSystemFrame_editList($editContent);
        return $editList;
    }
    
    
    function editContent_wireframeSettings($editContent,$frameWidth,$editText) {
       $wireFrame_enabled = cmsWireframe_enabled();
       if (!$wireFrame_enabled) return 0;
    
       $wireFrameSettings = cmsWireframe_editContent($editContent,$frameWidth,$editText,$this);
       return $wireFrameSettings;
    }

    function goPage($notList=array(),$addList=array()) {
        global $pageInfo;
        $data = array();
        foreach ($_GET as $key => $value) $data[$key]=$value;
        // foreach ($_POST as $key => $value) $data[$key]=$value;
        foreach ($addList as $key => $value) $data[$key]=$value;
        $goPage = "";
        foreach($data as $key=>$value) {
            if ($notList[$key]) {
                // dont Add
            } else {
                if ($goPage == "") $goPage .= "?";
                else $goPage.= "&";
                $goPage .= $key."=".$value;
            }
        }
        $goPage = $pageInfo[page].$goPage;
        return $goPage;       
    }

    
    function use_special_viewFilter($editContent) {
        return 0;
    }
    
    function useType($type) {

        switch ($type) {
            case "companyList" : $useType = 1; break;
            case "empty" : $useType = 0; break;
            case "content" : $useType = 0; break;
            default :
                $useType = 1;
        }
        return $useType;
    }

    function typeName($type) {
        switch ($type) {
            case "frame1" : $type = "frame";$typeAdd ="1"; break;
            case "frame2" : $type = "frame";$typeAdd ="2"; break;
            case "frame3" : $type = "frame";$typeAdd ="3"; break;
            case "frame4" : $type = "frame";$typeAdd ="4"; break;
            default :
                $typeAdd = null;                
        }
        
        if (function_exists("cmsType_".$type."_class")) {
            // echo ("Class $type exist ");
            $class = call_user_func("cmsType_".$type."_class");
            if (is_object($class)) {
                if (method_exists($class,"getName")) {
                    $typeName = $class->getName($typeAdd);
                   // echo ("classe GetName exist '$typeName'<br />");
                    if ($typeName) return $typeName;
                }

            }
            echo ("Class $class -> $typeName<br />");
                    // call_user_method($typeName, $obj)
                    
        } else {
            echo ("!!Function cmsType_".$type."_class not exist <br /> ");
        }
        
        
        
        switch ($type) {
           //  case "contentName" : $typeName = "Gespeicherter Inhalt"; break;
//            case "image" : $typeName = "Bild"; break;
//            case "login" : $typeName = "An- und Abmelden"; break;
//            case "text" : $typeName = "Text"; break;
//            case "textImage" : $typeName = "Text und Bild"; break;
//            case "dateList" : $typeName = "Termin Liste"; break;
//            case "flip" : $typeName = "Wechselnde Inhalte"; break;
//            case "ownPhp" : $typeName = "Eigene Scripte"; break;
//            case "social" : $typeName = "Soziale Dienste"; break;
//            case "companyList" : $typeName = "Hersteller Liste"; break;
//            case "content" : $typeName = "Inhalt"; break;
//            case "basket" : $typeName = "Warenkorb"; break;
//            case "empty" : $typeName = "Leer"; break;
//            case "footer" : $typeName = "Fußzeile"; break;
            case "frame1" : $typeName = "1 Spalten"; break;
            case "frame2" : $typeName = "2 Spalten"; break;
            case "frame3" : $typeName = "3 Spalten"; break;
            case "frame4" : $typeName = "4 Spalten"; break;
//            case "header" : $typeName = "Kopfzeile"; break;
//            case "navi" : $typeName = "Navigation"; break;
//            case "map" : $typeName = "Karte"; break;
//            case "contactForm" : $typeName = "Kontaktforumlar"; break;
//            case "imageList" : $typeName = "Bild-Liste"; break;
//            case "productShow" : $typeName = "Produkt"; break;
            default :
                if (function_exists("cmsType_".$type."_class")) {
                    // echo ("Class $type exist ");
                    $class = call_user_func("cmsType_".$type."_class");
                    if (is_object($class)) {
                        if (method_exists($class,"getName")) {
                            $typeName = $class->getName();
                            // echo ("classe GetName exist '$typeName'<br />");
                            if ($typeName) return $typeName;
                        }
                        
                    }
                    echo ("Class $class -> $typeName<br />");
                    // call_user_method($typeName, $obj)
                    
                } else {
                    echo ("Function cmsType_".$type."_class not exist <br /> ");
                }
                
               // echo ("case '".$type."' : typeName = ''; break;<br />");
                $typeName = "AutoName ".$type;
        }
        return $typeName;
    }
    
    function editContent_languages() {
        $languageList = cms_text_getSettings();
        $res = "";
        foreach ($languageList as $key => $value) {
            if ($value[editable]) {
                $res[$key] = $value[active];
            }
        }
        // $res = array("dt"=>1,"en"=>0);
        return $res;
    }
    
    function editContent_languageSelect($languageList,$selectType="") {
        
        if (!is_array($languageList)) $languageList = $this->editContent_languages ();
        switch ($selectType) {
            case "" : 
                break;
            case "Edit" : 
                $selectStr = cms_text_lg_edit();
                break;
            case "Show" :
                $selectStr = cms_text_lg_show();
                break;
        }
        
        
        
        if (!is_null($selectStr)) {
            $selectList = array();
            $select = explode("|",$selectStr);
            for ($i=0;$i<count($select);$i++) $selectList[$select[$i]]=1;
        }
        
        $str .= "";
        $str .= "<div class='cmsInput_languageLine";
        if ($selectType) $str .= " cmsInput_languageSelect_".$selectType;
        $str .= "'>";
        // foreach ($languageList as $key => $value) $str .= "$key=$value | ";
        foreach ($languageList as $key => $data) {
            if (is_array($data)) {
                $active = $data[active];
            } else {
                $active = $data;
            }
            if (is_array($selectList)) $active = $selectList[$key];
            
            $className = "cmsInput_selectLanguage";
            if ($active) $className .= " cmsInput_selectLanguage_selected";
            $str .= "<div class='$className'>$key</div>";            
        }
        $str .= "<div style='clear:both;'></div>";
        $str .= "</div>";
        return $str;
    }
    
    
    function edit_text($showData=array()) {
        
        $lg = cms_text_getLanguage();
        
        // dataSoucre
        if (!$showData[dataSource]) return "noDataSouce";
        $dataSoucre = $showData[dataSource];
       
        // formName
        if (!$showData[formName]) $showData[formName] = $dataSoucre; 
        $formName = $showData[formName];
        
        // $dataName
        if (!$showData[dataName]) $showData[dataName] = "unkown";
        $dataName = $showData[dataName];
        
        // Title
        if (!$showData[title]) $showData[title] = $dataName;         
        $title    = $showData[title];
        
        // Text
        if (!$showData[text]) $showData[text] = "";
        $text = $showData[text];
        
        // width
        if (!$showData[width]) $showData[width] = 100;
        $width    = $showData[width];
        
        // height
        if (!$showData[height]) $showData[width] = 30;
        $height    = $showData[height];
        
        // mode
        if (!$showData[mode]) $showData[mode] = "Simple";
        $mode = $showData[mode];
        
        // editMode 
        if (!$showData[editMode]) $showData[editMode] = "Simple";
        $editMode = $showData[editMode];
        
        // viewMode 
        if (!$showData[viewMode]) $showData[viewMode] = "text";
        $viewMode = $showData[viewMode];
        
        // output 
        if (!$showData[out]) $showData[out] = "array";
        $out = $showData[out];
        
        if (!$showData[inputClass]) $showData[inputClass] = "";
        $inputClass = $showData[inputClass];
        
        // echo ("<h1>editMode = $editMode mode = '$mode'</h1>");
        if (is_array($text)) {
            $textList = $text;
            
        } else {
            $textList = array();
            if (substr($text,0,3) == "lg|") {
                $help = explode("|",$text);
                for($i=1;$i<count($help);$i++) {
                    list($lgCode,$lgStr) = explode(":",$help[$i]);
                    if ($lgCode) $textList[$lgCode] = $lgStr;
                    // echo ("Found $lgCode $lgStr <br> ");
                }
                // $editMode = "SimpleLg";
            } else {
                if ($text) {
                    $lg = cms_text_getLanguage();
                    $textList[$lg] = $text;
                }
            }
        }
        
        
        
        $addData = array();
        $addData[text] = $title;
        $addData[input] = "";
        $addData[mode] = $mode;
        
        $input = "";
        // $input .= "EditMode $editMode ".substr($text,0,3)." ";        
        switch ($editMode) {
            case "Simple" :
                if (is_array($textList)) {
                    foreach ($textList as $key => $value) {
                        $type = "text";
                        if ($key != $lg) {
                            $type = "hidden";
                            $viewMode = "text";
                        }
                        switch ($viewMode) {
                            case "text" : 
                                $input .= "<input class='$inputClass' type='$type' name='".$formName."[".$dataName."][$key]' style='width:".$width."px' value='$value' >\n";
                                break;
                            case "textarea" :
                                $input .= "<textarea class='$inputClass' name='".$formName."[".$dataName."][$key]' style='width:".$width."px;height".$height."px;' >$value</textarea>\n";
                                break;
                        }                        
                    }
                }
                // $input .= "<input type='text' name='".$formName."[".$dataName."]' style='width:".$width."px' value='$text' >\n";
                break;
            
            case "SimpleLg" :
                $lgList = cms_text_getSettings();
                $lgEdit = cms_text_lg_edit();
                
                $lgShow = cms_text_lg_show();
                
                $showStr = "";
                
                $help = explode("|",  cms_text_lg_show());
                $lgShow = array();
                for($i=0;$i<count($help);$i++) $lgShow[$help[$i]] = 1;
                
                
                foreach ($lgList as $lgCode => $value) {
                    $show = $value[show];
                    $lgName = $value[name];
                    $text = $textList[$lgCode];
                    
                    
                    // ShowStr 
                    $style = "";
                    $className = "cmsInput_showLanguage_$lgCode";
                    if (!$lgShow[$lgCode]) $className.= " cmsInput_showLanguage_hidden"; 
                    
                    if ($mode == "SimpleLine") {
                        $className .= " cmsInput_showLanguage_oneLine";                       
                    }
                    
                    $showStr .= "<div class='$className'>"; 
                    $showStr.= $lgName.": '".$text."'";
                    $showStr.= "</div>";
                    
                    $className = "cmsInput_editLanguage cmsInput_editLanguage_$lgCode";
                    if ($show != "edit") $className.= " cmsInput_editLanguage_hidden"; 
                    if ($mode == "SimpleLine") $className .= " cmsInput_editLanguage_oneLine";
                    $input .= "<div class='$className'>"; 
                    
                    switch ($viewMode) {
                        case "text" : 
                            $input .= "<input title='$lgName'  class='$inputClass' type='text' name='".$formName."[".$dataName."][$lgCode]' style='width:".$width."px;' value='$text' >\n";
                            break;
                        case "textarea" :
                            $input .= "<textarea title='$lgName' class='$inputClass' name='".$formName."[".$dataName."][$lgCode]' style='width:".$width."px;height:".$height."px;'>$text</textarea>\n";
                            break;                                                    
                    }
                    $input .= "</div>";
                  
                }
                $input = $showStr.$input;                
                break;
                
        }
        
        switch ($out) {
            case "array" :
                $addData[input] = $input;
                return $addData;
                break;
            case "input" :
                return $input;
                break;
            default :
                return "unkown output '$out'";
                
        }
        
       
    }
    
    
    function editContent_text($type,$textData=array(),$showData=array()) {
        $addData = array();
        $name = "Text";
        $mode = "Simple";
        $view = "text";
        $out = "array";
        
        $show_css = 0;
        $show_color = 0;
        $show_lgSelect = 0;
        $formName = "editText[$type]";
        $width = 100;
        $height = 100;
        $styleType = $type;
        $styleEmpty = "Stil wählen";
        
        $useType = $type;
        if (substr($type,0,6)=="button") $useType = "button";
        
        switch ($useType) {
            case "headline" : 
                $name = "Überschrift";
                break;
            case "text" : 
                $name = "Text";
                break;
            case "frameHeadline" :
                $name = "Rahmenüberschrift";
                $styleType = "headline";
                break;
            case "frameHeadtext" :
                $name = "Rahmen-Text Oben";
                $styleType = "text";
                break;
            case "frameSubtext" :
                $name = "Rahmen-Text Unten";
                $styleType = "text";
                break;
            case "button" :
                $name = "BUTOOM";
                $styleType = "button";
                break;                
        }

        switch ($styleType) {
            case "headline" :
                $styleEmpty = $this->lga("select","headline_empty");//"Bitte Überschrift wählen";
                break;
            case "text" :
                $styleEmpty = $this->lga("select","text_empty");//"Text Stil wählen";
                break;  
            case "button" :
                $styleEmpty = $this->lga("select","button_empty");
                break;
        }
        
        if ($showData[formName]) $formName = $showData[formName];
        if ($showData[name]) $name = $showData[name];
        if ($showData[width]) $width = $showData[width];
        if ($showData[height]) $height = $showData[height];
        if ($showData[css]) $show_css = 1;
        if ($showData[color]) $show_color = 1;
        if ($showData[mode]) $mode = $showData[mode];
        if ($showData[lgSelect]) $show_lgSelect = 1;
        if ($showData[view]) $view = $showData[view];
        if ($showData[out]) $out = $showData[out];
        
        
        $addData = array();
        
        $editLanguages = $this->editContent_languages();
        
        $addData["text"] = $name;
        $input = "";
        if ($show_css) $input .= $this->filter_select("style", $textData[css],$formName."[css]",array("empty"=>$styleEmpty,"style"=>"width:150px;"),$styleType);

        if ($show_color) $input .= $this->selectColor($textData[data],$formName,$type);
        
        // if ($show_lgSelect) $input .= $this->editContent_languageSelect($editLanguages);
        
        
        $help = explode("|",  cms_text_lg_show());
        $lgShow = array();
        for($i=0;$i<count($help);$i++) $lgShow[$help[$i]] = 1;

        $lgList = cms_text_getSettings();
        $showStr = "";
        
        $textInput = "";
        $textInput .= "<input type='hidden' value='".$textData[id]."' name='".$formName."[id]'>";
        foreach ($lgList as $lgCode => $value) {
            $show   = $value[show];
            $lgName = $value[name];
            $text   = $textData["lg_".$lgCode];
                    
            // ShowStr 
            $className = "cmsInput_showLanguage_$lgCode";
            if (!$lgShow[$lgCode]) $className.= " cmsInput_showLanguage_hidden"; 
            $showStr .= "<div class='$className'>"; 
            $showStr.= $lgName.": <b>".$text."</b><br />";
            $showStr.= "</div>";
        
            $className = "cmsInput_editLanguage_$lgCode";
            if ($value[show]!="edit") $className.= " cmsInput_editLanguage_hidden"; 
            $textInput .= "<div class='$className'>"; 
            switch ($view) {
                case "text" :
                    $textInput .= "<input title='$lgName' type='text' style='width:".$width."px;' name='".$formName."[lg_".$lgCode."]' value='". $text."' />";
                    break;
                case "textarea" :
                    $textInput .= "<textarea name='".$formName."[lg_".$lgCode."]' style='width:".$width."px;height:".$height."px;' >".$text."</textarea>";
                    break;
            }
            // $textInput .= "<input type='text' style='width:".$width."px;' name='".$formName."[lg_".$lgCode."]' value='". $textData["lg_".$lgCode]."' />";
            $textInput .= "</div>\n";
            
           
        }
        // $textInput .= $showStr;
        
        if ($input) {
            $addData[input] = $input.$showStr;
            $addData[secondLine] = $textInput;
        } else {
            $addData[input] = $showStr.$textInput;
        }
        
        $addData["mode"] = $mode;
        
        switch ($out) {
            case "array" :
                return $addData;
                break;
            case "input" :
                return $showStr.$textInput;
                
        }  
        return $addData;
    }


    function editContent_imageList($code,$showData) {
        $res = cmsImage_List($code,$showData);
        return $res;
    }
    
    

    // EDIT FUNCTIONS
    function editContent_ViewMode($editContent,$frameWidth) {
        // echo ("editContent_ViewMode($editContent,$frameWidth)<br />");
        $viewModeList = $this->filter_select_getList("viewMode");
        if (!is_array($viewModeList)) return 0;
        if (count($viewModeList) == 0) return 0;
        $res = array();
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $viewMode = $data[viewMode];
        if ($_POST[editContent][data][viewMode]) $viewMode = $_POST[editContent][data][viewMode];
        else if ($_POST[editContent][data]) $viewMode = $_POST[editContent][data][viewMode];
        
        $addData = array();
        $addData["text"] = "Darstellung";
        $addData["input"] = $this->filter_select("viewMode",$viewMode,"editContent[data][viewMode]",array("submit"=>1,"empty"=>"Bitte Darstellung wählen"));
        $addData[mode] = "Simple";
        $res[] = $addData;
        
        switch ($viewMode) {
            case "list" :
                $addData = array();
                $addData["text"] = "Kopfzeile ";
                if ($data[titleLine]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][titleLine]' $checked value='1' >\n";
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Liste in Seiten aufteilen";
                if ($data[pageing]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageing]' $checked value='1' >\n";
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Anzahl pro Seite";
                $addData["input"] = "<input name='editContent[data][pageingCount]' style='width:100px;' value='".$data[pageingCount]."'>";
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Seitennavigation oben";
                if ($data[pageingTop]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageingTop]' $checked value='1' >\n";
                $addData[mode] = "Simple";
                $res[] = $addData;
                
                $addData = array();
                $addData["text"] = "Seitennavigation unten";
                if ($data[pageingBottom]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageingBottom]' $checked value='1' >\n";
                $addData[mode] = "More";
                $res[] = $addData;
                break;

            case "table" :
                $addData = array();
                $addData["text"] = "Anzahl in Reihe";
                if (!$data[dataRow]) $data[dataRow] = 3;
                $input  = "<input name='editContent[data][dataRow]' style='width:100px;' value='".$data[dataRow]."'>";
                $addData["input"] = $input;
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Abstand in Reihe";
                if (!$data[dataRowAbs]) $data[dataRowAbs] = 10;
                $input  = "<input name='editContent[data][dataRowAbs]' style='width:100px;' value='".$data[dataRowAbs]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Reihen höhe";
                $input  = "<input name='editContent[data][dataColHeight]' style='width:100px;' value='".$data[dataColHeight]."'>";
                $addData["input"] = $input;
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Abstand Zeilen";
                if (!$data[dataColAbs]) $data[dataColAbs] = 10;
                $input  = "<input name='editContent[data][dataColAbs]' style='width:100px;' value='".$data[dataColAbs]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Maximale Projekttanzahl";
                $input  = "<input name='editContent[data][maxCount]' style='width:100px;' value='".$data[maxCount]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;
                break;

            case "slider" :
                $addData = array();
                $addData["text"] = "Wechsel";
                $direction = $editContent[data][dataDirection];
                $input  = $this->slider_direction_select($direction,"editContent[data][dataDirection]",array());
                $addData["input"] = $input;
                $addData[mode] = "Simple";
                $res[] = $addData;


                $addData = array();
                $addData["text"] = "Auto Loop";
                $loop = $editContent[data][dataLoop];
                $checked = "";
                if ($loop) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][dataLoop]' $checked >";
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Bild in ms";
                $addData["input"] = "<input name='editContent[data][dataPause]' style='width:100px;' value='".$editContent[data][dataPause]."'>";
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Wechsel in ms";
                $addData["input"] = "<input name='editContent[data][dataSpeed]' style='width:100px;' value='".$editContent[data][dataSpeed]."'>";
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Navigation";
                $navigate = $editContent[data][dataNavigate];
                $checked = "";
                if ($navigate) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][dataNavigate]' $checked >";
                $addData[mode] = "Admin";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Einzelauswahl";
                $pager = $editContent[data][dataPager];
                $checked = "";
                if ($pager) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][dataPager]' $checked >";
                $addData[mode] = "Admin";
                $res[] = $addData;
                break;

           

            default :
                $addEdit = $this->editContent_ViewMode_ownViewMode($viewMode,$editContent,$frameWidth);
                if (is_array($addEdit)) {
                    for ($i=0;$i<count($addEdit);$i++) {
                        $res[] = $addEdit[$i];
                    }
                }

        }
        return $res;
    }
    
    function editContent_ViewMode_ownViewMode($viewMode,$editContent,$frameWidth) {

    }

    function editContent_filterView_dynamic($dynamic_type,$data) {
        if (!$dynamic_type) return 0;
        $res = 0;
        switch ($dynamic_type) {
            case "category" :
                $addData = array();
                $addData["text"] = "Dynamische Category";
                $dynamicCategory = $data[dynamicCategory];
                if ($dynamicCategory) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicCategory]' />";
                $addData["mode"] = "More";
                $res = $addData;
                break;
            
            case "project" :
                $addData = array();
                $addData["text"] = "Dynamische Projektliste";
                $dynamicProject = $data[dynamicProject];
                if ($dynamicProject) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicProject]' />";
                $addData["mode"] = "More";
                $res = $addData;
                break;
            
            case "product" :
                $addData = array();
                $addData["text"] = "Dynamische Produkte";
                $dynamicProduct = $data[dynamicProduct];
                if ($dynamicProduct) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicProduct]' />";
                $addData["mode"] = "More";
                $res = $addData;
                break;
                
            case "company" :
                $addData = array();
                $addData["text"] = "Dynamische Hersteller";
                $dynamicCompany = $data[dynamicCompany];
                if ($dynamicCompany) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicCompany]' />";
                $addData["mode"] = "More";
                $res = $addData;
                break;  
                
            case "article" :
                $addData = array();
                $addData["text"] = "Dynamischer Artikel";
                $dynamicArticle = $data[dynamicArticle];
                if ($dynamicArticle) $checked = "checked='checked'";
                else $checked = "";
                $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicArticle]' />";
                $addData["mode"] = "More";
                $res = $addData;                
                break;                        
                
            default :
                echo ("<h1>UNKOWN DYNAMIC_Type '$dynamic_type' ADD IN editContent_filterView_dynamic  </h1>");
        }
        return $res;
    }
    

    function editContent_filterView($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        $res = array();
        
        $pageData = $GLOBALS[pageData];
        $dynamic = $pageData[dynamic];
        if ($dynamic) {
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            
            // ADD DYNAMIC 1
            $dynamic_type_1 = $dynamicData[dataSource];
            $addDynamic = $this->editContent_filterView_dynamic($dynamic_type_1,$data);
            if (is_array($addDynamic)) $res[] = $addDynamic;
            
            // ADD DYNAMIC 2
            $dynamic_type_2 = $dynamicData[dataSource2];
            $addDynamic = $this->editContent_filterView_dynamic($dynamic_type_2,$data);
            if (is_array($addDynamic)) $res[] = $addDynamic;
            
             // ADD DYNAMIC 3
            $dynamic_type_3 = $dynamicData[dataSource3];
            $addDynamic = $this->editContent_filterView_dynamic($dynamic_type_3,$data);
            if (is_array($addDynamic)) $res[] = $addDynamic;
            
            
//            foreach($dynamicData as $key =>$value ) echo ("$key =$value <bR>");
//            $addData = array();
//            $addData["text"] = "Dynamische Projektliste";
//            $dynamicProject = $data[dynamicProject];
//            if ($dynamicProject) $checked = "checked='checked'";
//            else $checked = "";
//            $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicProject]' />";
//            $res[] = $addData;
        }
        
        
        $filterList = $this->editContent_filter_getList();
        foreach ($filterList as $filterKey => $filterData ) {
            if (is_array($filterData)) {
                $name = $filterData[name];
                // echo ("Add Filter $name $filterData<br />");
                $filterValue = $editContent[data]["filter_".$filterKey];
                if ($_POST[editContent][data]["filter_".$filterKey]) $filterValue = $_POST[editContent][data]["filter_".$filterKey];
                else if ($_POST[editContent][data]) $filterValue = $_POST[editContent][data]["filter_".$filterKey];
                $filterType = $filterData[type];
                $filter = $filterData[filter];
                $sort   = $filterData [sort];
                $showData = $filterData[showData];
                // show_array($filterData);
                $addData = array();
                $addData["text"] = "nach $name";
                $input = $this->filter_select($filterType,$filterValue,"editContent[data][filter_".$filterKey."]",$showData,$filter,$sort);

                $customFilter = $filterData[customFilter];
                if ($customFilter) {
                    $customFilterShow = $editContent[data]["customFilter_".$filterKey];
                    if ($_POST[editContent][data]["customFilter_".$filterKey]) $customFilterShow = $_POST[editContent][data]["customFilter_".$filterKey];
                    else if ($_POST[editContent][data]) $customFilterShow = $_POST[editContent][data]["customFilter_".$filterKey];
                    $checked = "";
                    if ($customFilterShow) $checked = "checked='checked'";
                    $input .= " Filter anzeigen: <input type='checkbox' $checked name='editContent[data][customFilter_".$filterKey."]' onChange='submit()' value='1'>";
                    // echo ("$filterKey 'customFilter_.$filterKey' $customFilterShow <br />");
                    if ($customFilterShow) {
                        $customFilterView = $editContent[data]["customFilterView_".$filterKey];
                        if ($_POST[editContent][data]["customFilterView_".$filterKey]) $customFilterView = $_POST[editContent][data]["customFilterView_".$filterKey];
                        else if ($_POST[editContent][data]) $customFilterView = $_POST[editContent][data]["customFilterView_".$filterKey];
                    
                        $showData = array("submit"=>1);
                        $input .= " Filterart: ".$this->editContent_customview_select($customFilterView,$filterKey,$showData);
                    }
                }
                $addData["input"] = $input;
                $addData["mode"] = "Simple";



                $res[] = $addData;
            }

        }
        return $res;
    }

    function editContent_filter_getList() {
        $filterList = array();

        $filterList[produkt] = array("name"=>"Produkt","filter"=>array(),"sort"=>"name");

        $ownFilterList = $this->editContent_filter_getList_own();
        if (is_array($ownFilterList)) {
            foreach ($ownFilterList as $key => $value) {
                $filterList[$key]= $value;
            }
        }
        return $filterList;
    }

    function editContent_filter_getList_own() {
    }

    function editContent_customview_select($code,$filterKey,$showData) {
        $selectList = $this->editContent_customview_select_getList();

        $dataName = "editContent[data][customFilterView_".$filterKey."]";

        $str = "";

        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Bitte Filterart wählen</option>";

        foreach ($selectList as $key => $value) {
            $str.= "<option value='$key'";
            if ($key == $code)  $str.= " selected='1' ";
            $name = $value;
            $str.= ">$name</option>";
        }
        $str.= "</select>";
        return $str;
    }

    function editContent_customview_select_getList() {
        $list = array();
        $list[dropdown] = "Dropdown Menü";
        $list[selectListSingle] = "Auswahlliste einzelnd";
        $list[selectListMultiple] = "Auswahlliste mehrfach";
        $list[autoComplte] = "Autocomplete";
        return $list;
    }
    
     function page_select($code,$dataName,$showData) {

        $selectList = $this->page_select_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Bitte Seite wählen</option>";

        foreach ($selectList as $key => $value) {
            $str.= "<option value='$key'";
            if ($key == $code)  $str.= " selected='1' ";

            if (is_array($value)) {
                $levelStr = "";
                if ($value[level]) {
                    $level = $value[level];
                    for ($l=1;$l<$level;$l++) $levelStr .= "&nbsp; ";
                }

                $name = $value[title];
                if (!$name) $name = $value[name];

                // $name = "";
                // foreach($value as $key => $data) $name.= "|$key=$data |";




            } else {
                $name = $value;
            }

            $str.= ">$name</option>";
        }
        $str.= "</select>";
        return $str;
    }

    function page_select_getList() {

        $res = cms_page_getSortList();

        $ownList = $this->page_select_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }


    function page_select_getOwnList() {
        $res = array();
        return $res;
    }
    
    function slider_direction_getList() {
        $res = array();
        $res["horizontal"] = "Horizontal";
        $res["vertical"] = "Vertikal";
        $res["fade"] = "Überblendung";

        $ownList = $this->slider_direction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }
    
    function slider_direction_getOwnList() {
        return array();
    }
    
    function slider_direction_select($code,$dataName,$showData=array()) {

        $selectList = $this->slider_direction_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:80px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Keine Aktion</option>";

        foreach ($selectList as $key => $value) {
            if (is_string($value)) {
                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$value</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }




    
     function target_select($code,$dataName,$showData) {

        $selectList = $this->target_select_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Keine Aktion</option>";

        foreach ($selectList as $key => $value) {
            $str.= "<option value='$key'";
            if ($key == $code)  $str.= " selected='1' ";
            $str.= ">$value</option>";
        }
        $str.= "</select>";
        return $str;
    }

    function target_select_getList() {
        $res = array();
        $res["frame"] = "im Rahmen";
        $res["page"] = "Seite";
        $res["popup"] = "PopUp Fenster";

        $ownList = $this->target_select_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }
    
    function target_select_getOwnList() {
        $res = array();
        return $res;
    }

    
    function selectPosition($code,$dataName,$showData=array()) {
        
        switch ($showData[mode]) {
            case "box" :
                $str .= "<div class='cmsDataBox_positionFrame' style=''>";
                
                $class = "cmsDataBox_positionSelect cmsDataBox_positionTop";
                if ($code == "top") $class.= " cmsDataBox_positionSelected";
                $str .= "<div class='$class' style=''></div>";
                
                $class = "cmsDataBox_positionSelect cmsDataBox_positionLeft";
                if ($code == "left") $class.= " cmsDataBox_positionSelected";
                $str .= "<div class='$class' style=''></div>";
                
                $class = "cmsDataBox_positionSelect cmsDataBox_positionCenter";
                if ($code == "center") $class.= " cmsDataBox_positionSelected";
                $str .= "<div class='$class' style=''></div>";
                
                $class = "cmsDataBox_positionSelect cmsDataBox_positionRight";
                if ($code == "right") $class.= " cmsDataBox_positionSelected";
                $str .= "<div class='$class' style=''></div>";
                
                $class = "cmsDataBox_positionSelect cmsDataBox_positionBottom";
                if ($code == "bottom") $class.= " cmsDataBox_positionSelected";
                $str .= "<div class='$class' style=''></div>";
                
                $str .= "<input type='hidden' class='cmsDataBox_positionInput' name='$dataName' value='$code' style='width:30px;' />";
                
                $str .= "</div>";
                break;
            default :
                $selectList = array("top"=>"Oben","left"=>"Links","center"=>"Mitte","right"=>"Rechts","bottom"=>"Unten");

                $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:70px;' ";
                if ($showData[submit]) $str.= "onChange='submit()' ";
                $str .= "value='$code' >";

                $emptyStr = "Keine Position";
                if ($showData["empty"]) $emptyStr = $showData["empty"];

                if ($emptyStr) {
                    $str.= "<option value='0'";
                    if (!$code) $str.= " selected='1' ";
                    $str.= ">$emptyStr</option>";
                }

                $outValue = "name";
                if ($showData[out]) $outValue = $showData[out];
                foreach ($selectList as $key => $value) {
                    if ($value) {
                        if (is_array($value)) {
                            $name = $value[$outValue];
                        } else {
                            $name = $value;
                        }

                        $str.= "<option value='$key'";
                        if ($key == $code)  $str.= " selected='1' ";
                        $str.= ">$name</option>";
                    }
                }
                $str.= "</select>";
                
                
        }
        return $str;
    }
    
    function position_getWidth($widthStr,$frameWidth) {

        if (strpos($widthStr,"%")) { 
            $proz = intval(substr($widthStr,0,strpos($widthStr,"%")));
            $width = floor($frameWidth * $proz / 100);               
        }
        if (strpos($widthStr,"px")) { 
            $width = intval(substr($widthStr,0,strpos($widthStr,"px")));                
        }               
        if (!$width) {
            if (intval($widthStr)) $width = intval($widthStr);
        }
        return $width;
    }
    
    function position_frameValue($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $LR_left = $data[LR_left];
        $LR_left_abs = $data[LR_abs];
        $LR_center = $data[LR_center];
        $LR_center_abs = $data[LRC_abs];
        $LR_right = $data[LR_right];
        
//        echo ("Left '$LR_left' '$LR_left_abs' <br>");
//        echo ("Right '$LR_right' '$LR_right_abs' <br>");
//        echo ("Center '$LR_center' '$LR_center_abs' <br>");
        
        
        if ($LR_left_abs) {
            if (strpos($LR_left_abs,"%")) { 
                $proz = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"%")));
                $LR_left_abs = floor($frameWidth * $proz / 100);               
            }
            if (strpos($LR_left_abs,"px")) { 
                $LR_left_abs = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"px")));                
            }               
        } else {
            $LR_left_abs = 10;
        }
        
        if ($LR_center_abs) {
            if (strpos($LR_center_abs,"%")) { 
                $proz = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"%")));
                $LR_center_abs = floor($frameWidth * $proz / 100);               
            }
            if (strpos($LR_center_abs,"px")) { 
                $LR_center_abs = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"px")));                
            }               
        } 
        
        // leftWidth
        if ($LR_left) {
            if (strpos($LR_left,"%")) { 
                $proz = intval(substr($LR_left,0,strpos($LR_left,"%")));
                $LR_left = floor($frameWidth * $proz / 100);               
            }
            if (strpos($LR_left_abs,"px")) { 
                $LR_left = intval(substr($LR_left,0,strpos($LR_left,"px")));                
            }    
        }
        
        // centerWidth
        if ($LR_center) {
            if (strpos($LR_center,"%")) { 
                $proz = intval(substr($LR_center,0,strpos($LR_center,"%")));
                $LR_center = floor($frameWidth * $proz / 100);               
            }
            if (strpos($LR_center,"px")) { 
                $LR_center = intval(substr($LR_center,0,strpos($LR_center,"px")));                
            }    
        }
        
        // rightWidth
        if ($LR_right) {
            if (strpos($LR_right,"%")) { 
                $proz = intval(substr($LR_right,0,strpos($LR_right,"%")));
                $LR_right = floor($frameWidth * $proz / 100);               
            }
            if (strpos($LR_right,"px")) { 
                $LR_right = intval(substr($LR_right,0,strpos($LR_right,"px")));                
            }    
        }
        
        
        // EXIST MIDDLE
        $middle = 0;
        if ($LR_center or $LR_center_abs) $middle = 1;
       
        
        $space = $frameWidth;
        if ($LR_left_abs) { 
            $space = $space-$LR_left_abs;
            $anz = 2;
        }
        if ($middle) {
            $anz = 3;
            $space = $space-$LR_center_abs;
        }
        
        // echo ("FrameWidth = $frameWidth Space = $space Anzahl =$anz <br>");
        // echo ("Breite links=$LR_left mitte=$LR_center rechts=$LR_right <br>");
        
        if ($LR_left) {
            $leftWidth = $LR_left;
            $space = $space - $LR_left;
            $anz--;            
        } else {
            //echo ("<h1>HIER $LR_left </h1>");
            $leftWidth = "auto";
        }
        
        if ($middle) {
            if ($LR_center) {
                $centerWidth = $LR_center;
                $space = $space - $LR_center;
                $anz--;            
            } else {
                $centerWidth = "auto";
            }
        }
        
        
        if ($LR_right) {
            $rightWidth = $LR_right;
            $space = $space - $LR_right;
            $anz--;            
        } else {
            $rightWidth = "auto";
        }
        
        
        // echo ("Danach $anz $space / links=$leftWidth mitte=$centerWidth rechts=$rightWidth <br> ");
        
        if ($leftWidth == "auto") {
            $width = floor($space / $anz);
            $leftWidth = $width;
            $space = $space - $width;
            $anz--;
        }
        
        if ($middle AND $centerWidth == "auto") {
            $width = floor($space / $anz);
            $centerWidth = $width;
            $space = $space - $width;
            $anz--;
        }
        
        if ($rightWidth == "auto") {
            $width = floor($space / $anz);
            $rightWidth = $width;
            $space = $space - $width;
            $anz--;
        }
        
        $res = array();
        
        $res[top_width] = $frameWidth;
        $res[top_abs] = 0;
        $res[top_text] = array();
        
        $res[left_width] = $leftWidth;
        $res[left_abs] = $LR_left_abs;
        $res[left_text] = array();
        
        if ($middle) {
            $res[center_width] = $centerWidth;
            $res[center_abs] = $LR_center_abs;
            $res[center_text] = array();
        }
        
        $res[right_width] = $rightWidth;
        $res[right_abs] = 0;
        $res[right_text] = array();
        
        $res[bottom_width] = $frameWidth;
        $res[bottom_abs] = 0;
        $res[bottom_text] = array();
        
        return $res;
    }
    
    
    function position_frameShow($posData,$class,$frameWidth) {
        
        
        $topText = $posData[top_text];
        
        $leftText = $posData[left_text];
        $centerText = $posData[center_text];
        $rightText = $posData[right_text];
        
        $bottomText = $posData[bottom_text];
        
        if (count($topText)) {
            $key = "top";
            $width = $posData[$key."_width"];
            $abs   = $posData[$key."_abs"];
            $style = "width:".$width."px;";
            if ($abs) $style .= "margin-right:".$abs."px;";
            $divName = "positionFrame_".$key;
            div_start($divName,$style);
            foreach ($topText as $textKey => $text) {
                $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                div_start($contentFrameName);
                // echo ("<h1>$contentFrameName</h1>");
                echo ($text);
                div_end($contentFrameName);
            }                      
            div_end($divName);       
        }
        
       
        
        if (count($leftText) OR count($centerText) or count($rightText)) {
            div_start("positionFrame_LR");
            if (count($leftText)) {
                $key = "left";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "positionFrame_".$key;
                div_start($divName,$style);
                foreach ($leftText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    div_start($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");
                    echo ($text);
                    div_end($contentFrameName);
                }
                div_end($divName);
            }
            
            if (count($centerText)) {
                $key = "center";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "positionFrame_".$key;
                div_start($divName,$style);
                foreach ($centerText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    div_start($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");
                    echo ($text);
                    div_end($contentFrameName);
                }                      
                div_end($divName);                
            }
            
            if (count($rightText)) {
                $key = "right";
                $width = $posData[$key."_width"];
                $abs   = $posData[$key."_abs"];
                $style = "width:".$width."px;";
                if ($abs) $style .= "margin-right:".$abs."px;";
                $divName = "positionFrame_".$key;
                div_start($divName,$style);
                foreach ($rightText as $textKey => $text) {
                    $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                    div_start($contentFrameName);
                    // echo ("<h1>$contentFrameName</h1>");                    
                    echo ($text);
                    div_end($contentFrameName);
                }      
                div_end($divName);                 
            }
            div_end("positionFrame_LR","before");
            
        }
        
        
        if (count($bottomText)) {
            $key = "bottom";
            $width = $posData[$key."_width"];
            $abs   = $posData[$key."_abs"];
            $style = "width:".$width."px;";
            if ($abs) $style .= "margin-right:".$abs."px;";
            $divName = "positionFrame_".$key;
            div_start($divName,$style);
            foreach ($bottomText as $textKey => $text) {
                $contentFrameName = "positionFrame_content ".$class."_".$textKey;
                div_start($contentFrameName);
                // echo ("<h1>$contentFrameName</h1>");
                echo ($text);
                div_end($contentFrameName);
            }                      
            div_end($divName);       
            
        }
    }
    
    function selectView($code,$dataName,$viewList,$showData) {
        $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:10px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Darstellung";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($viewList as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $name = $value[$outValue];
                } else {
                    $name = $value;
                }

                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$name</option>";
            }
        }
        $str.= "</select>";
        return $str;
        
    }
    
    function selectStyle($code,$dataName,$viewList,$showData) {
         $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:70px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Kein Stil";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        foreach ($viewList as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $name = $value[$outValue];
                } else {
                    $name = $value;
                }

                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$name</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }

    function clickAction_select($code,$dataName,$showData) {

        $selectList = $this->clickAction_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Keine Aktion</option>";

        foreach ($selectList as $key => $value) {
            if (is_string($value)) {
                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$value</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }

    function clickAction_getList() {
        $res = array();
        $res["goUrl"] = "Hersteller Homepage öffnen";
        $res["showProduct"] = "Produkte zeigen";
        $res["showCategory"] = "Kategorien zeigen";

        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            if ($value) {
            
                $res[$key] = $value;
            }
            if ($value === 0) {
                unset($res[$key]);
            }
        }
        return $res;
    }

    function clickAction_getOwnList() {
        $res = array();
        return $res;
    }


     function mouseAction_select($code,$dataName,$showData) {

        $selectList = $this->mouseAction_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $str.= ">Keine Aktion</option>";

        foreach ($selectList as $key => $value) {
            if (is_string($value)) {
                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$value</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }

    function mouseAction_getList() {
        $res = array();

        $res["showProduct"] = "Produktliste zeigen";
        $res["showCategory"] = "Kategorieliste zeigen";

        $ownList = $this->mouseAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function mouseAction_getOwnList() {
        $res = array();
        return $res;
    }

    
    function filter_select($filterType,$code,$dataName,$showData,$filter=array(),$sort="") {
        // echo "$filterType,$code,$dataName,$showData,$filter,$sort<br />" ;
        if ($showData[filter]) $filter = $showData[filter];
        if ( $showData[sort])  $sort =   $showData[sort];
        // echo ("FILTER $filter SORT = $sort <br />");
        $selectList = $this->filter_select_getList($filterType,$filter,$sort="");
        $str = "";
        
        $style = "min-width:100px;";
        if ($showData[style]) $style = $showData[style];
        if (is_array($selectList) AND count($selectList)) {
           
            //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
            $str.= "<select name='$dataName' class='cmsSelectType'  style='$style' ";
            if ($showData[submit]) $str.= "onChange='submit()' ";
            $str .= "value='$code' >";

            $emptyStr = $this->lga("select",$filterType."_empty");
            if (!$emptyStr) $emptyStr = "Kein Filter";
            
            if ($showData["empty"]) $emptyStr = $showData["empty"];

            if ($emptyStr) {
                $str.= "<option value='0'";
                if (!$code) $str.= " selected='1' ";
                $str.= ">$emptyStr</option>";
            }

            $outValue = "name";
            if ($showData[out]) $outValue = $showData[out];

            foreach ($selectList as $key => $value) {
                if ($value) {
                    if (is_array($value)) {
                        $name = $value[$outValue];
                    } else {
                        $name = $value;
                    }

                    $str.= "<option value='$key'";
                    if ($key == $code)  $str.= " selected='1' ";
                    $str.= ">$name</option>";
                }
            }
            $str.= "</select>";
        }
        return $str;
    }

    function filter_select_getList($filterType,$filter=array(),$sort="") {
        switch ($filterType) {
            case "style" :
                $convert = 1;
                $res = $this->styleList_filter_select_getList($filter);
                break;
            case "headline" :
                $convert=1;
                $res = $this->styleList_filter_select_getList($filterType);
                break;
            
            case "text" :
                $res = $this->styleList_filter_select_getList($filterType);
                break;
            
            case "button" : 
                $convert = 1;
                $res = $this->styleList_filter_select_getList($filterType);
                break;
            
            case "contentName" :
                $res = $this->contentName_filter_select_getList($filter,$sort);
                break;
            case "product" :
                $res = $this->product_filter_select_getList($filter,$sort);
                break;
            case "company" :
                $res = $this->company_filter_select_getList($filter,$sort);
                break;
            
            case "project" :
                $res = $this->project_filter_select_getList($filter,$sort);
                break;
            
            case "category" :
                $res = $this->category_filter_select_getList($filter,$sort);
                break;
             case "region" :
                $res = $this->category_filter_select_getList($filter,$sort);
                break;
             case "viewMode" :
                $res = $this->viewMode_filter_select_getList($filter,$sort);
                break;
            case "dateRange" :
                $res = $this->dateRange_filter_select_getList($filter,$sort);
                break;
            case "specialView" :
                $res = $this->customFilter_specialView_getList($filter,$sort); //specialView_filter_select_getList($filter,$sort);
                    ////_filter_select_getList($filter,$sort);
                break;

            default :
                echo ("<h1> filter_select_getList($filterType,$filter,$sort) </h1>");
                $res = $this->filter_select_getList_own($filterType,$filter,$sort);

        }
        
        if ($convert) {
            if (is_array($res)) {
                foreach($res as $key => $text) {
                    $str = $this->lga("select",$filterType."_$key","",array("dt"=>$text));
                    if (is_string($str) AND $str != "select_".$filterType."_".$key ) {
                        $res[$key] = $str;
                    }
                }
            }
            
            if ($emptyText) {
                $str = $editClass->lga("select",$filterType."_empty","",array("dt"=>$emptyText));
                if (is_string($str) AND $str != "select_".$filterType."_empty") {
                    $emptyText = $str;
                }
            }
        }
        
        
        return $res;
    }

    function filter_select_getList_own($filterType,$filter,$sort) {}

    function selectColor($code,$dataName,$setId) {
        $showColor = "inherit";
        
        if (is_array($code)) {
            $color = $code[color];
            $colorId = $code[colorId];
            $colorBlend = $code[colorBlend];
            $colorSaturation = $code[colorSaturation];
            // show_array($code);
            $showColor = cmsStyle_getColor($code);
            // echo ("<b>COLOR = $showColor </b>");
        }
        
        
        $str = "";
        //       drop_backgroundColor" class="colorBox cmsEditSelectColor ui-droppable" style="background-color:#;">0</div>
        $divName = "colorBox cmsEditSelectColor";
        
        
        // if ($color) $showColor = "#".$color;
        
        
        if (is_array($code)) {
            
        }
        
        
        $divData = array();
        $divData[id] = "text_".$setId;
        $divData[style] = "background-color:$showColor;margin-left:5px;"; //width:20px;height:20px;display:inline-block;border:1px solid #f00;";
        $str .= div_start_str($divName,$divData);
        $str .= "C";
        
        
        
//        ).val(colorId);
//             $("#"+setId+"_colorBlend").val(colorBlend);
//             $("#"+setId+"_colorSaturation").val(colorSaturation);"
        $str .= div_end_str($divName);
        // $str .= $setId." Code='$code'" ;
        $str .= "<input type='hidden' id='".$setId."' style='width:30px;' value='$color' name='".$dataName."[data][color]'/>";
        $str .= "<input type='hidden' id='".$setId."_colorId' style='width:30px;' value='$colorId' name='".$dataName."[data][colorId]' />";
        $str .= "<input type='hidden' id='".$setId."_colorBlend' style='width:30px;' value='$colorBlend' name='".$dataName."[data][colorBlend]' />";
        $str .= "<input type='hidden' id='".$setId."_colorSaturation' style='width:30px;' value='$colorSaturation' name='".$dataName."[data][colorSaturation]' />";
        return $str;
    }
    
    function contentName_filter_select_getList($filter,$sort) {
        $res = cmsContentName_getList($filter,$sort);
        return $res;
    }

    function product_filter_select_getList($filter,$sort) {
        $res = array();
        $res["all"] = "Alle Produkte";
        $res["new"] = "Neue Produkte";
        $res["highlight"] = "Highlight Produkte";

        $ownList = $this->product_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function product_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

     function viewMode_filter_select_getList($filter,$sort) {
        // echo ("viewMode_filter_select_getList($filter,$sort)<br />");
        $res = array();
        // $res["all"] = "Alle Produkte";
        // $res["new"] = "Neue Produkte";
        // $res["highlight"] = "Highlight Produkte";

        $ownList = $this->viewMode_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            if (is_array($value)) $name = $value[name];
            else $name = $value;
            $res[$key] = $name;
        }
        return $res;
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }


    function styleList_filter_select_getList($styleName,$sort=""){
        $res = array();
        switch ($styleName) {
            case "headline" :
                $res = array ("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4");
                break;
            case "text" :
                $res = array ("left"=>"Links-Bündig","center"=>"Zentriert","right"=>"Rechts-Bündig","block"=>"Blocksatz");                
                break;
            
            case "button" :
                $res = array ("main"=>"Haupt - Button","second"=>"Sekundärer Button","readMore"=>"weiter lesen");
                break;                
        }
        
        
        $ownList = $this->styleList_filter_select_getOwnList($styleName,$sort);
        foreach ($ownList as $key => $value) {
            if (is_array($value)) $name = $value[name];
            else $name = $value;
            $res[$key] = $name;
        }
        return $res;
    }

    function styleList_filter_select_getOwnList($styleName,$sort) {
        $res = array();
        return $res;
    }

    


    function company_filter_select_getList($filter,$sort) {
        $res = array();
        $companyList = cmsCompany_getList($filter, $sort);
        for ($i=0;$i<count($companyList);$i++) {
            $id = $companyList[$i][id];
            $name = $companyList[$i][name];
            $res[$id] = $name;
        }


        $ownList = $this->company_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function company_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

   
    
    function project_filter_select_getList($filter,$sort) {
        $res = array();
        $categoryList = cmsProject_getList($filter, $sort);
        if (is_array($categoryList) AND count($categoryList)) {
            for ($i=0;$i<count($categoryList);$i++) {
                $id = $categoryList[$i][id];
                $name = $categoryList[$i][name];
                $res[$id] = $name;
            }

            $ownList = $this->project_filter_select_getOwnList($filter,$sort);
            if (is_array($ownList) AND count($ownList)) {
                foreach ($ownList as $key => $value) {
                    $res[$key] = $value;
                }
            }
        }
        return $res;
    }
    
    function project_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }
    
    
    
    function category_filter_select_getList($filter,$sort) {
       // echo (" category_filter_select_getList($filter,$sort)<br />");
        $res = array();
        $categoryList = cmsCategory_getList($filter, $sort);
        if (is_array($categoryList) AND count($categoryList)) {
            for ($i=0;$i<count($categoryList);$i++) {
                $id = $categoryList[$i][id];
                $name = $categoryList[$i][name];
                $res[$id] = $name;
            }

            $ownList = $this->category_filter_select_getOwnList($filter,$sort);
            if (is_array($ownList) AND count($ownList)) {
                foreach ($ownList as $key => $value) {
                    $res[$key] = $value;
                }
            }
        }
        return $res;
    }
    
    
    

    function category_filter_select_getOwnList($filter,$sort) {}

    function dateRange_filter_select_getList($filter=array(),$sort="") {
        $filterList = cmsDates_dateRange_getList();
        return $filterList;
    }


    function toggle_select($code,$dataName,$showData,$toggleList) {
        // echo ("toggle_select($code,$dataName,$showData,$toggleList)<br />");
        $width = 300;
        $count = 3;
        $mode = "single";
        $class = "";
        $outValue = "name";
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "width" : $width = $value; break;
                case "count" : $count = $value; break;
                case "mode"  : $mode = $value; break;
                case "class" : $class = $value; break;
                case "out"   : $outValue = $value; break;
                case "empty" : break;

                default :
                    echo ("unkown Mode in cmsCategory_selectCategory_toogle #$key=$value<br />");
            }
        }

        $border = 1;
        $padding = 3;
        $width = $width-2*$border;
        $divName = "cmsToggleSelect";
        if ($class) $divName .= " ".$class;
        $divData = array();
        $divData[style] = "width:".$width."px;";
        $divData[toggleMode] = $mode;
        $str.=div_start_str($divName,$divData);
        $widthItem = ($width - ($count*$border) -($count*$padding)- $border) / $count;
        switch ($mode) {
            case "multi" :
                $out = "|";
                $exList = explode("|",$code);
                $codeList = array();
                for ($i=0;$i<count($exList);$i++) {
                    $id = $exList[$i];
                    if ($id) {
                        // echo ("id $id in $code<br />");
                        $codeList[$id] = 1;
                        // $out .= $id."|";
                    }
                }
                // show_array($codeList);
                break;
            default :
                $out = "";
        }
        $columnNr = 0;
        foreach ($toggleList as $toggleId => $toggleName) {
            // echo ("$toggleId = $toggleName <br />");
            $divNameItem = "cmsToggleItem";
            switch ($mode) {
                 case "multi" :
                     //echo ("Suche $categoryId in codeList $codeList[$categoryId] <br />");
                     if ($codeList[$toggleId]) {
                         $out .= $toggleId."|";
                         $divNameItem .= " cmsToggleSelected";
                         //  echo ("Found $id<br />");
                     }
                     break;

                case "single" :
                    if ($code == $toggleId) {
                        $out = $toggleId;
                        $divNameItem .= " cmsToggleSelected";
                    }
                    break;

                default :
            }

            $divNameItem .= " ".$class."_".$toggleId;
            $columnNr++;
            $divDataItem = array();
            $divDataItem[style] = "width:".$widthItem."px;";
            $divDataItem[toggleName] = $toggleName;
            $divDataItem[toggleId] = $toggleId;
            $divDataItem[toggleClass] = $class;

            if ($columnNr == $count) {
                $divDataItem[style] .= "border-right-width:1px;";
                $columnNr = 0;
            }


            $str .= div_start_str($divNameItem,$divDataItem);
            $str .= $toggleName;
            $str .= div_end_str($divNameItem);
        }
        $str.= div_end_str($divName,"before");
        $str .= "<input type='hidden' id='$class' name='$dataName' readonly='readonly' value='$out' >";
        return $str;
    }

    
    
    function show_frameValue($contentData,$frameWidth) {
         $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $LR_left = $data[LR_left];
        $LR_left_abs = $data[LR_abs];
        $LR_center = $data[LR_center];
        $LR_center_abs = $data[LRC_abs];
        $LR_right = $data[LR_right];

//        echo ("Left '$LR_left' '$LR_left_abs' <br>");
//        echo ("Right '$LR_right' '$LR_right_abs' <br>");
//        echo ("Center '$LR_center' '$LR_center_abs' <br>");


        if ($LR_left_abs) {
            if (strpos($LR_left_abs,"%")) {
                $proz = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"%")));
                $LR_left_abs = floor($frameWidth * $proz / 100);
            }
            if (strpos($LR_left_abs,"px")) {
                $LR_left_abs = intval(substr($LR_left_abs,0,strpos($LR_left_abs,"px")));
            }
        } else {
            $LR_left_abs = 10;
        }

        if ($LR_center_abs) {
            if (strpos($LR_center_abs,"%")) {
                $proz = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"%")));
                $LR_center_abs = floor($frameWidth * $proz / 100);
            }
            if (strpos($LR_center_abs,"px")) {
                $LR_center_abs = intval(substr($LR_center_abs,0,strpos($LR_center_abs,"px")));
            }
        }

        // leftWidth
        if ($LR_left) {
            if (strpos($LR_left,"%")) {
                $proz = intval(substr($LR_left,0,strpos($LR_left,"%")));
                $LR_left = floor($frameWidth * $proz / 100);
            }
            if (strpos($LR_left_abs,"px")) {
                $LR_left = intval(substr($LR_left,0,strpos($LR_left,"px")));
            }
        }

        // centerWidth
        if ($LR_center) {
            if (strpos($LR_center,"%")) {
                $proz = intval(substr($LR_center,0,strpos($LR_center,"%")));
                $LR_center = floor($frameWidth * $proz / 100);
            }
            if (strpos($LR_center,"px")) {
                $LR_center = intval(substr($LR_center,0,strpos($LR_center,"px")));
            }
        }

        // rightWidth
        if ($LR_right) {
            if (strpos($LR_right,"%")) {
                $proz = intval(substr($LR_right,0,strpos($LR_right,"%")));
                $LR_right = floor($frameWidth * $proz / 100);
            }
            if (strpos($LR_right,"px")) {
                $LR_right = intval(substr($LR_right,0,strpos($LR_right,"px")));
            }
        }


        // EXIST MIDDLE
        $middle = 0;
        if ($LR_center or $LR_center_abs) $middle = 1;


        $space = $frameWidth;
        if ($LR_left_abs) {
            $space = $space-$LR_left_abs;
            $anz = 2;
        }
        if ($middle) {
            $anz = 3;
            $space = $space-$LR_center_abs;
        }

        // echo ("FrameWidth = $frameWidth Space = $space Anzahl =$anz <br>");
        // echo ("Breite links=$LR_left mitte=$LR_center rechts=$LR_right <br>");

        if ($LR_left) {
            $leftWidth = $LR_left;
            $space = $space - $LR_left;
            $anz--;
        } else {
            //echo ("<h1>HIER $LR_left </h1>");
            $leftWidth = "auto";
        }

        if ($middle) {
            if ($LR_center) {
                $centerWidth = $LR_center;
                $space = $space - $LR_center;
                $anz--;
            } else {
                $centerWidth = "auto";
            }
        }


        if ($LR_right) {
            $rightWidth = $LR_right;
            $space = $space - $LR_right;
            $anz--;
        } else {
            $rightWidth = "auto";
        }


        // echo ("Danach $anz $space / links=$leftWidth mitte=$centerWidth rechts=$rightWidth <br> ");

        if ($leftWidth == "auto") {
            $width = floor($space / $anz);
            $leftWidth = $width;
            $space = $space - $width;
            $anz--;
        }

        if ($middle AND $centerWidth == "auto") {
            $width = floor($space / $anz);
            $centerWidth = $width;
            $space = $space - $width;
            $anz--;
        }

        if ($rightWidth == "auto") {
            $width = floor($space / $anz);
            $rightWidth = $width;
            $space = $space - $width;
            $anz--;
        }

        $res = array();

        $res[top_width] = $frameWidth;
        $res[top_abs] = 0;
        $res[top_text] = array();

        $res[left_width] = $leftWidth;
        $res[left_abs] = $LR_left_abs;
        $res[left_text] = array();

        if ($middle) {
            $res[center_width] = $centerWidth;
            $res[center_abs] = $LR_center_abs;
            $res[center_text] = array();
        }

        $res[right_width] = $rightWidth;
        $res[right_abs] = 0;
        $res[right_text] = array();

        $res[bottom_width] = $frameWidth;
        $res[bottom_abs] = 0;
        $res[bottom_text] = array();

        return $res;
    }




    ////////////////////////////////////////////////////////////////////////////
    /// S H O W L I S T                                                      ///
    ////////////////////////////////////////////////////////////////////////////






    function showList_List($data,$showList,$showData,$frameWidth) {
        // echo ("showList_List($data,$showList,$showData,$frameWidth)<br />");


        global $pageInfo;
        if (!is_array($showData)) {
            $showData = array();
            // pageing
            $showData[pageing] = array();
            $showData[pageing][count] = 20;
            $showData[pageing][showTop] = 1;
            $showData[pageing][showBottom] = 1;
            $showData[pageing][viewMode] = "small"; // small | all

            // Title Line
            $showData[titleLine] = 1;
        }
        if (is_array($data)) {
            if ($data[query]) {
                $query = $data[query];
                $anz   = $data[count];                
            } else {
                $anz = count($data);
                if ($anz == 0) {
                    echo ("Keine Daten Gefunden <br />");
                    return 0;
                }
            }
        }

        if (is_array($showData[pageing])) {
            $pageing = 1;
            $pageing_count = $showData[pageing][count];
            $pageing_showTop = $showData[pageing][showTop];
            $pageing_showBottom = $showData[pageing][showBottom];
            $pageing_viewMode = $showData[pageing][viewMode];

            // Seiten anzahl
            $pageing_pageCount = ceil($anz / $pageing_count);
            // echo ("Seitenanzahl = $pageing_pageCount ($anz / $pageing_count) <br />");

            // aktuelle Seite
            $pageing_actPage = $_GET[page];
            if (!$pageing_actPage) $pageing_actPage = 1;

            $pageing_startNr = ($pageing_actPage-1) * $pageing_count;
            $pageing_endNr   = $pageing_startNr + $pageing_count;
            if ($pageing_endNr > $anz) $pageing_endNr = $anz;

            // echo ("StartNr Page($pageing_actPage) = $pageing_startNr  / $pageing_endNr <br /> ");

            $pageing_url = $this->showList_getUrl("page"); //$pageInfo[page].$pageing_url;

            if ($anz < $pageing_count) {
                $pageing = 0;
            }
        } else {
            $pageing = 0;
            $pageing_showTop = 0;
            $pageing_showBottom = 0;
            $pageing_startNr = 0;
            $pageing_endNr = $anz;
        }

        $showTitle = 1;
        if ($showData[titleLine] == "") $showData[titleLine] = 0;
        if (is_integer($showData[titleLine])) $showTitle = $showData[titleLine];
        // echo ("TitleLine $showTitle $showData[titleLine] <br />");

        $height = $showData[height];
        if (!$height) {
            $height = 60;
            $showData[height] = $height;
        }

        // GET SORT URL
        $sortUrl = $this->showList_getUrl("sort");       


        // Show TOP Pageing Navigation
        if ($pageing AND $pageing_showTop) {

            $this->showList_pageingLine($pageing_actPage,$pageing_pageCount,$pageing_viewMode,$pageing_url,$frameWith);
            //  echo ("PageingTop <br />");
        }




        // Show Title Line
        if ($showTitle) {
            $titleStr = $this->showList_titleLine("Title",$showList,$sortUrl,$frameWidth);
            echo ($titleStr);
        }
        if (is_string($query)) {
            // echo ("Data is String $query");
            // echo (" Limit $pageing_startNr, $pageing_endNr <br");
            $query .= " LIMIT $pageing_startNr, $pageing_endNr";
            // echo ("Query = $query <br />");
            $result = mysql_query($query);
            while ($dataLine = mysql_fetch_assoc($result)) {
                $dataLine = $this->showList_checkData($dataLine);
                $line = $this->showList_dataLine($dataLine,$showData,$showList,$frameWidth);
                echo ($line);
            }

        } else {

            // show Data
            for ($i=$pageing_startNr;$i<$pageing_endNr;$i++) {
                $dataLine = $this->showList_checkData($data[$i]);
                $line = $this->showList_dataLine($dataLine,$showData,$showList,$frameWidth);
                echo ($line);
            }
        }

        // Show Bottom Pageing Navigation
        if ($pageing AND $pageing_showBottom) {
            $this->showList_pageingLine($pageing_actPage,$pageing_pageCount,$pageing_viewMode,$pageing_url,$frameWith);            
        }
    }

    function showList_getUrl($urlType) {
        global $pageInfo;
        $url = "";
        foreach ($_GET as $key=>$value) {
            $add = 1;
            switch ($key) {
                case "sort" :
                    switch ($urlType) {
                        case "sort" ; $add = 0; break;
                    }
                    break;

                case "page" :
                    switch ($urlType) {
                        case "sort" ; $add = 0; break;
                        case "page" ; $add = 0; break;
                    }
                    break;

            }
            if ($add) {
                if ($url=="") $url.="?";
                else $url .= "&";
                $url .= $key."=".$value;
            }
        }

        foreach ($_POST as $key=>$value) {
            $add = 1;
            switch ($key) {
                case "sort" :
                    switch ($urlType) {
                        case "sort" ; $add = 0; break;
                    }
                    break;

                case "page" :
                    switch ($urlType) {
                        case "sort" ; $add = 0; break;
                        case "page" ; $add = 0; break;
                    }
                    break;

            }
            if ($add) {
                if ($url=="") $url.="?";
                else $url .= "&";
                $url .= $key."=".$value;
            }
        }

        if ($url == "") $url = "?";
        else $url .= "&";
        $url = $pageInfo[page].$url;
        return $url;
    }
        

    function showList_pageingLine($pageing_actPage,$pageing_pageCount,$pageing_mode,$pageing_url,$frameWith) {
        div_start("cmsPageingFrame");
        if (!$pageing_mode) $pageing_mode = "small";
        switch ($pageing_mode) {
            case "small" :
                if ($pageing_actPage > 2) {
                    echo ("<a href='".$pageing_url."page=1' title='Seite 1' class='cmsPageingButton' ><<</a>");
                }
                if ($pageing_actPage > 1) {
                    echo ("<a href='".$pageing_url."page=".($pageing_actPage-1)."' title='Seite ".($pageing_actPage-1)."' class='cmsPageingButton' ><</a>");
                }

                echo (" Seite $pageing_actPage / $pageing_pageCount ");

                
                if ($pageing_actPage < $pageing_pageCount) {
                    echo ("<a href='".$pageing_url."page=".($pageing_actPage+1)."' title='Seite ".($pageing_actPage+1)."' class='cmsPageingButton'>></a> ");
                }
                if ($pageing_actPage < ($pageing_pageCount-1)) {
                    echo ("<a href='".$pageing_url."page=$pageing_pageCount' title='letzte Seite' class='cmsPageingButton' >>></a> ");
                }
                break;
            case "all" :
                for ($i=1;$i<=$pageing_pageCount;$i++ ) {
                    if ($i==$pageing_actPage) {
                        echo ("<span class='cmsPageingButtonAktiv'>$i</span>");
                    } else {
                        echo ("<a href='".$pageing_url."page=$i' class='cmsPageingButton' title='Seite $i' >$i</a>");
                    }                    
                }
                break;
        }
        div_end("cmsPageingFrame");
    }

    function showList_titleLine($data,$showData,$sortUrl,$frameWidth) {
        
        // echo ("showList_getLine($data,$showData,$frameWidth)<br />");
        $actSort = $_GET[sort];

        $str = "";
        $divName = "cmsShowDataTitleLine";
        $divData = array();
        //$height = $showData["image"]["height"];
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        //if ($height) $divData[style] .= "height:".$height."px;";
        $str.= div_start_str($divName,$divData);

        foreach ($showData as $key => $value) {
            if (is_array($value)) {
                $width = $value[width];
                $height = $value[height];
                $name = $value[name];
                $align = $value[align];
                $sort = 1;
                if (is_int($value[sort])) $sort = $value[sort];
                if (!$name) $name = "key=$name";


                $style = "float:left;width:".$width."px;";
                if ($align) $style .= "text-align:$align;";
                $divData2 = array();
                $divData2[style] = $style;

                $divName2 = "cmsShowDataTitleItem cmsShowDataTitleItem_$key";
                $link = "";
                if ($sort) {
                    $divName2 .= " cmsShowDataSort";
                    $link = $sortUrl."sort=".$key;
                    if ($actSort == $key) {
                        $divName2 .= " cmsShowDataSort_down";
                        $divData2[sortUp] = $key."_up";
                        $link = $sortUrl."sort=".$key."_up";
                        $name .= " <img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/sort_down_white.png' width='10px' border='0'>";
                    }
                    if ($actSort == $key."_up") {
                        $divName2 .= " cmsShowDataSort_up";
                        $divData2[sortDown] = $key;
                        $link = $sortUrl."sort=".$key;
                        $name .= " <img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/sort_up_white.png' width=10px border=0>";
                    }
                }

                if ($link) $str.= "<a href='".$link."'>";
                //  echo ("Link $link <br />");
                $str.= div_start_str($divName2,$divData2);
                $str.= $name;
                $str.= div_end_str($divName2);
                if ($link) $str.= "</a>";
            }
        }
        $str.= div_end_str($divName,"before");
        return $str;
    }

    function showList_checkData($data) {
        // echo ("contente showList_checkData($data) <br>");
        return $data;
    }
    
    
    function showList_dataLine($data,$showData,$showList,$frameWidth) {
        if (!is_array($data) ) { // showTitleLine
            echo ("no Array ($data) in showList_getLine()<br />");
            return 0;
        }

        $id = $data[id];
        $str = "";
        $goPage = "";
        $goPageList = array();
        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "edit" : break;
                case "id" : break;

                default :
                    $goPageList[$key]=$value;
            }
        }
        // GO PAGE
        if ($data[goPage]) {
            $goPage = $data[goPage];
        } else {
            foreach ($goPageList as $key => $value) {
                if ($goPage == "") $goPage.= "?";
                else $goPage .= "&";
                $goPage .= "$key=$value";
            }


            if ($goPage == "") $goPage.= "?";
            else $goPage .= "&";
            $goPage .= "view=edit&id=$data[id]";
        }
        
        
       



        // $str.= "<a href='$goPage' >";
        $divName = "cmsShowDataLine";
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        $mainHeight = $showList["image"]["height"];
        if ($showData[height]) $mainHeight = $showData[height];
        
        if ($mainHeight) $divData[style] .= "height:".$mainHeight."px;"; //line-height:".$height."px;";
        $divData[id] = $data[id];
        if ($goPage) $divName .= " listItemClick";
        
        $str.= div_start_str($divName,$divData);
        if ($goPage) {
            // $str .= "<div class='hiddenLink' style=''>";
            $str .= "<a href='$goPage' class='hiddenLink' >GO</a>";
            //$str .= "</div>";
        }

        foreach ($showList as $key => $value) {
            if (is_array($value)) {
                $width = $value[width];
                $height = $value[height];
                $name = $value[name];
                $type = $value[type];

                $cont = "key=$key";
                switch ($key) {

                    case "image" :
                        $cont = "kein Bild";
                        $imageId = intval($data[image]);
                        // more Images
                        if ($data[image][0] == "|") {
                            $cont = "mehrere<br />Bilder";
                            $imgStr = $data[image];
                            $imgStr = substr($imgStr,1,strlen($imgStr)-2);
                            $imgList = explode("|",$imgStr);
                            $imageId = $imgList[0];
                            //$cont = "";
                            //for ($imgNr=0;$imgNr<count($imgList);$imgNr++) {
                            //    $cont .= $imgNr.":".$imgList[$imgNr]." - ";
                            //}

                        }
                       
                        
                        if ($imageId > 0) {
                            $imageData = cmsImage_getData_by_Id($imageId);
                            if (is_array($imageData)) {
                                $cont = cmsImage_showImage($imageData,$width,array("frameHeight"=>$height,"frameWidth"=>$width,"vAlign"=>"middle","hAlign"=>"center"));
                            }
                        }
                        if ($data)
                        break;
                    case "category" :
                        if (is_integer(strpos($data[$key],"|"))) {
                            $cont = "";
                            $catList = explode("|",$data[$key]);
                            for ($i=0;$i<count($catList);$i++) {
                                $categoryId = intval($catList[$i]);
                                if ($categoryId) {
                                    $res = cmsCategory_get(array("id"=>$categoryId));
                                    if (is_array($res)) {
                                        $categoryName = $res[name];
                                        if ($cont != "") $cont .= " &#149; ";
                                        $cont .= $categoryName;
                                    }
                                }
                            }

                        } else {
                            $res = cmsCategory_get(array("id"=>$data[$key]));
                            if (is_array($res)) $cont = $res[name];
                            else $cont = "keine Rubrik ";
                        }
                        break;

                    case "company" :
                        $res = cmsCompany_get(array("id"=>$data[$key]));
                        if (is_array($res)) $cont = $res[name];
                        else $cont = "keine Hersteller";
                        break;

                    case "location" :
                        $res = cmsLocation_get(array("id"=>$data[$key]));
                        if (is_array($res)) $cont = $res[name];
                        else {
                            if ($data[locationStr]) {
                                $cont = "'".$data[locationStr]."'";
                            } else {
                                $cont = "keine Ort";
                            }
                        }
                        break;

                    case "salut" :
                        $res = cmsUser_getSalut($data[$key]);
                        if ($res) $cont = $res;
                        else $cont = "keine Anrede";
                        break;

                    case "userLevel" :
                        $res = cmsUser_getUserLevel($data[$key]);
                        if ($res) $cont = $res;
                        else $cont = "keine Benutzerebene";
                        break;

                    case "name" :
                        $cont = $data[$key];
                        if (!$cont) $cont = "kein Name";
                        break;


                    case "show" :
                        if ($data[$key]) $cont = "1";
                        else $cont = 0;
                        break;

                    case "new" :
                        if ($data[$key]) $cont = "1";
                        else $cont = "";
                        break;

                    case "highlight" :
                        if ($data[$key]) $cont = "$data[$key]";
                        else $cont = "";
                        break;


                    case "region" :
                        $res = cmsCategory_get(array("id"=>$data[$key]));
                        if (is_array($res)) $cont = $res[name];
                        else $cont = "keine Region";
                        break;

                    case "date" :
                        $datum = $data[$key];
                        $day = substr($datum,8,2);
                        $month = substr($datum,5,2);
                        $year = substr($datum,2,2);
                        if ($width < 60) $cont = $day.".".$month;
                        else $cont = $day.".".$month.".".$year;
                        break;
                    case "fromDate" :
                        $datum = $data[$key];
                        $day = substr($datum,8,2);
                        $month = substr($datum,5,2);
                        $year = substr($datum,2,2);
                        if ($width < 60) $cont = $day.".".$month;
                        else $cont = $day.".".$month.".".$year;
                        // $cont .= "<br />$width";
                        break;
                    case "toDate" :
                        $datum = $data[$key];
                        $day = substr($datum,8,2);
                        $month = substr($datum,5,2);
                        $year = substr($datum,2,2);
                        if ($width < 60) $cont = $day.".".$month;
                        else $cont = $day.".".$month.".".$year;
                        break;

                    case "time" :
                        $time = $data[$key];
                        $cont = substr($time,0,5);
                        break;
                    
                    case "subCat":
                        if ($data[$key] == 1 ) $cont="<a href='$pageInfo[page]?mainCat=$data[id]'>zeigen</a>";
                        else $cont="<a href='$pageInfo[page]?view=new&mainCat=$data[id]'>anlegen</a>";
                        break;

                    case "basket" :
                        $dataType = $key;
                        // show_array($data);
                        $cont = $this->showList_showBasket($dataType,$data,$data[basket],$width);
//                        $cont = "</a>";
//                        $cont .= "<input type='text' style='width:20px' value='1' name='basket[$id][amount]' />";
//                        $cont .= "<input type='submit' stle='width:20px' value='add' name='basket[$id][add]' />";
//                        if ($data[count]) {
//                            $cont .= "anz ".$data[count]."<br />";
//                        }
//                        $cont.= "<a href='$goPage' >";
                        break;


                    default :
                        switch ($type) {
                            case "checkbox" :
                                if ($data[$key]) $cont = "1";
                                else $cont = "0";
                                break;
                            case "float" :
                                $deci = $value[deci];
                                if (!$deci) $deci = 2;
                                $komma = $value[komma];
                                if (!$komma) $komma = ",";
                                $deli1000 = $value["1000"];
                                if (!$deli1000) $deli1000 = ".";
                                $float = $data[$key];
                                $cont = number_format($float,$deci,$komma,$deli1000);
                                break;



                            default :
                                if ($data[$key]) $cont = $data[$key];
                                
                        }


                }

                $align = $value[align];

                $style = "float:left;width:".$width."px;";
                if ($align) $style .= "text-align:$align;";
                if ($mainHeight) $style .= "height:".$mainHeight."px;";

                $str.= div_start_str("cmsShowDataItem cmsShowDataItem_$key",$style);
                $str.= $cont;
                $str.= div_end_str("cmsShowDataItem cmsShowDataItem_$key");
            }
        }
        $str.= div_end_str($divName,"before");
         $str.= "</a>";
        return $str;
    }
    
    
    function showList_showBasket($dataType,$data,$content,$targetWidth) {


        $basketId = $content[basketId];
        $inBasket = $content[inBasket];

        $basketItem = array();
        $basketItem[basketId] = $basketId;
        $basketItem[name] = $data[name];
        $basketItem[vk] = $data[vk];
        $basketItem[shipping] = $data[shipping];
        $basketItem[dataSource] = "???"; // $basketId
        $basketItem[maxCount] = $data[count];
        
        $showData = array();
        $showData[hideDiv] = 0;
        $showData["class"] = "listItemNoClick";
        
        // show_array($basketItem);


        
        $out = cmsType_basket_showItem($basketItem,$showData);
        return $out;
    }
    /// end of S H O W L I S T                                               ///
    ////////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////////
    /// S H O W L I S T  -  F I L T E R                                      ///
    ////////////////////////////////////////////////////////////////////////////
    function showList_customFilter($contentData,$frameWidth,$divName=null) {
        $data = $contentData[data];
        if (!is_array($data)) return 0;
        $showFilter = array();
        foreach ($data as $key => $value) {
            if (substr($key,0,17) == "customFilterView_") {

                $filterViewType = $value;
                $filterName = substr($key,17);
                $showFilter[$filterName] = $filterViewType;
               
            } 
        }
        if (count($showFilter) == 0) return 0;

        foreach ($_POST as $key => $value) {#
            // echo ("Post $key = $value <br />");
        }

        $reloadList = array();

        $filterDataList = $this->editContent_filter_getList();
        
        
        if (!$divName) $divName = "cmsCustomFilterList";
        
        $str = div_start_str($divName); // ,"width:".($frameWidth-2)."px;");

        $filterWidth = 300;
        
        $standardFilter = $this->emptyListSelect();
        
        $select = array();
        $str .= "<form method='POST'>";
        foreach ($showFilter as $filterName => $filterViewType) {
            // echo ("<h1>Filter $filterName $filterViewType </h1>");
            $filterData = $filterDataList[$filterName];
            $name     = $filterData[name];
            $showData = $filterData["showData"];
            $filter   = $filterData["filter"];
            $sort     = $filterData["sort"];
            $dataName = "filter_".$filterData["dataName"];
            //echo ("Filter $dataName $standardFilter[$dataName]<br />");
            $code = $standardFilter[$filterData["dataName"]];
            if (!is_null($_GET[$dataName])) $code = $_GET[$dataName];
            if (is_string($_POST[$dataName])) {
                $code = $_POST[$dataName];
                // echo ("$dataName $code <br />");
                $reloadList[$dataName] = $code;

            }
            if (!$showData[style]) $showData[style] = "width:".$filterWidth."px;";

            // echo ("DataName $dataName inhalt = $code Type = $filterData[type] <br />");
            $str .= div_start_str("cmsCustomFilter_line");
            $str .= div_start_str("cmsCustomFilter_left");
            $str .= "Filtern nach $name :";
            $str .= div_end_str("cmsCustomFilter_left");
            $str .= div_start_str("cmsCustomFilter_right");
            
            switch ($filterData[type]) {
                case "text" :
                    if ($showData[submit]) $submitStr = "onchange='submit()'";
                    else $submitStr = "";
                    $str.= "<input type='text' style='width:300px;' $submitStr name='$dataName' value='$code'>";
                    if ($code) $select[$dataName] = $code;
                    break;

                case "category" :
                    $str .= cmsCategory_selectCategory($code,$dataName,$showData,$filter,$sort);
                    if ($code) $select[$dataName] = $code;
                    break;
                case "company" :
                    $str .= cmsCompany_selectCompany($code,$dataName,$showData,$filter,$sort);
                    if ($code) $select[$dataName] = $code;
                    break;
                case "product" :
                    $str .= cmsProduct_selectProduct($code,$dataName,$showData,$filter,$sort);
                    if ($code) $select[$dataName] = $code;
                    break;
                case "userLevel" :
                    if ($code) $str .= cmsUser_selectUserLevel($code, $dataName, $showData, $showFilter, $showSort);
                    break;

                case "specialView" :
                    $str .= $this->customFilter_select_specialView($code, $dataName, $showData, $showFilter, $showSort);
                    if ($code) $select[$dataName] = $code;
                    break;
                case "dateRange" :
                    $str .= $this->customFilter_select_dateRange($code, $dataName, $showData, $showFilter, $showSort);
                    if ($code) $select[$dataName] = $code;
                    break;

                case "location" :
                    
                    switch ($showData[type]) {
                        case "autoComplete" :
                            // echo ("code=$code dataName=$dataName <br />");
                            $str .= cmsLocation_selectLocation_auto($code, $dataName, $showData, $filter, $sort)."<br />";
                            if ($code) $select[$dataName] = $code;
                            break;
                        case "simple" :
                            $str .= "<input type='text' onChange='submit()' style='width:".$filterWidth."px' name='$dataName' value='$code' ><br />";
                            if ($code) $select[$dataName] = $code;
                            break;
                        default :
                            // show_array($showData);
                            $str .= cmsLocation_selectLocation($code,$dataName,$showData,$filter,$sort);
                            if ($code) $select[$dataName] = $code;

                    }
                    break;

                default :
                    echo "Unkown FilterType $filterData[type] <br />";
            }
            $str .= div_end_str("cmsCustomFilter_right");
            $str .= div_end_str("cmsCustomFilter_line","before");          
        }


        // Suche Button
        $str .= div_start_str("cmsCustomFilter_line");
        $str .= div_start_str("cmsCustomFilter_left");
        $str .= "&nbsp;";
        $str .= div_end_str("cmsCustomFilter_left");
        $str .= div_start_str("cmsCustomFilter_right");
        $str .= "<input type='submit' class ='mainInputButton' value='suchen' name='search' />";
        $str .= div_end_str("cmsCustomFilter_right");
        $str .= div_end_str("cmsCustomFilter_line","before");


        if (count($reloadList)>0) {
            $doReload = 0;
            $goPage = "";
            $getAdd = array();
            foreach ($_GET as $key => $value) {
                switch ($key) {
                    case "page" : break; // Dont Add Page after Filtering Data;
                    
                    default :
                            if (is_string($reloadList[$key])) { // OR $reloadList[$key] == "0") {
                                 // echo "$key is in get has also content in reloadList $reloadList[$key] <br />";
                                 $doReload = 1;
                            } else {
                                // echo ("Add $key ".is_string($reloadList[$key])."<br />");
                                if ($goPage=="") $goPage.= "?";
                                else $goPage.= "&";
                                $goPage .= $key."=".$value;
                                $getAdd[$key] = $value;
                            }
                }
            }
            
            $postAdd = array();
            foreach ($reloadList as $key => $value) {
                $standardName = substr($key,7);
               // echo ("reload $key = $value standard($standardName)='".$standardFilter[$standardName]."'<br />");
                $doEmpty = 0;
                switch ($key) {
                     case "filter_userLevel" : if ($value=="notSelected") $doReload = 1; break;
                     case "filter_dateRange" : 
                         if ($getAdd[date]) {
                             echo ("Filter DateRange = $value<br />");
                             echo ("Date = $_GET[date]<br />");
                             unset($getAdd[date]);
                         }
                             
                }
                if ($value AND $value != "notSelected") { // OR $doEmpty) {
                    $add = 1;
                
                    if ($standardFilter[$standardName]) {
                        if ($standardFilter[$standardName] == $value) {
                            // echo (" Standard $standardFilter[$standardName] = $value <br />");
                            $add = 0;
                        }
                    }   
                    $doReload = 1;
                    if ($add) {
                        if ($goPage=="") $goPage.= "?";
                        else $goPage.= "&";
                        $goPage .= $key."=".$value;
                        $postAdd[$key] = $value;
                    }
                } else {
                    if ($standardFilter[$standardName]) {;
                        // echo (" STandard $standardFilter[$standardName] = $value <br />");
                        $doReload = 1;
                        if ($goPage=="") $goPage.= "?";
                        else $goPage.= "&";
                        $goPage .= $key."=".$value;          
                        $postAdd[$key] = $value;
                    }
                }
                

            }

            if ($doReload) {
                echo ("<h1>Sammle Daten</h1>");
                $goPage = "";
                foreach ($getAdd as $key => $value) $goPage.= "&$key=$value";
                foreach ($postAdd as $key => $value) $goPage.= "&$key=$value";
                $goPage = php_addUrl($GLOBALS[pageInfo][page],$goPage);
                
                // $goPage = $GLOBALS[pageInfo][page].$goPage;
                // echo ("<h1>reload = '$goPage' </h1>");
                // echo ("<h2>newGo = '$newGo' <br />");
                reloadPage($goPage,0);
                $stop = 1;
            }
        }

      
        $str .= "</form>";
        // show_array($filterData);
        $str .= div_end_str($divName);
        if ($stop) {
            die();
        } else {
            echo ($str);
        }
        return $select;

    }
    
    function emptyListSelect() {
        return array();
    }

    function customFilter_select_specialView($code, $dataName, $showData, $showFilter, $showSort) {
        // echo ("customFilter_specialView<br />");
        $filterList = $this->customFilter_specialView_getList();


        $str = "";

        $editStyle = "min-width:200px;";
        if ($showData["style"]) $editStyle .= $showData[style];

        $str.= "<select name='$dataName' class='cmsSelectType'  style='$editStyle' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Normale Ansichten";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        foreach ($filterList as $specialId => $value) {
             if ($value) {
                if (is_array($value)) $specialName = $value[name];
                else $specialName = $value;
                $str.= "<option value='$specialId'";
                if ($code == $specialId)  $str.= " selected='1' ";
                $str.= ">$specialName</option>";
             }
        }
        $str.= "</select>";
        return $str;
    }

    function customFilter_specialView_getList() {
        // echo ("customFilter_specialView_getList<br />");
        $filterList = array();

        $filterList[hidden] = array();
        $filterList[hidden]["name"] = "Ausgeblendete Daten";
        //$filterList[hidden]["type"] = "specialView";
        // $filterList[hidden]["showData"] = array("submit"=>1,"empty"=>"Region wählen");
        $filterList[hidden]["filter"] = array("show"=>0);
        // $filterList[hidden]["sort"] = "name";
        $filterList[hidden]["dataName"] = "specialView";
        // $filterList[hidden][customFilter] = 1;


        $ownFilterList = $this->customFilter_specialView_getList_own();
        if (is_array($ownFilterList)) {
            foreach ($ownFilterList as $key => $value) {
                $filterList[$key] = $value;
            }
        }
        return $filterList;
    }

    function customFilter_specialView_getList_own() {
        // echo ("customFilter_specialView_getList<br />");
    }


    function customFilter_select_dateRange($code, $dataName, $showData, $showFilter, $showSort) {
        // echo ("<h1>customFilter_select_dateRange</h1><br />");
        // echo ("customFilter_select_dateRange($code, $dataName, $showData, $showFilter, $showSort) <br />");
        $filterList = $this->dateRange_filter_select_getList();


        $str = "";

        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $emptyStr = "Normale Ansichten";
        if ($showData["empty"]) $emptyStr = $showData["empty"];

        if ($emptyStr) {
            $str.= "<option value='0'";
            if (!$code) $str.= " selected='1' ";
            $str.= ">$emptyStr</option>";
        }

        foreach ($filterList as $specialId => $value) {
             if ($value) {
                if (is_array($value)) $specialName = $value[name];
                else $specialName = $value;
                $str.= "<option value='$specialId'";
                if ($code == $specialId)  $str.= " selected='1' ";
                $str.= ">$specialName</option>";
             }
        }
        $str.= "</select>";
        //echo $str;
        return $str;
    }
    
    /// end of S H O W L I S T  -  F I L T E R                               ///
    ////////////////////////////////////////////////////////////////////////////

}

function cms_contentTypes_class() {
    global $cmsName;
    $ownPhpFile = "cms/cms_contentType.php";
    $exist = file_exists($ownPhpFile);
    if ($exist) {
        require_once ($ownPhpFile);
        // echo ("Exist  $ownPhpFile <br />");
        $instance =  new cmsType_contentTypes();
    } else {
        //echo ("File $ownPhpFile not exist <br>");
        $instance =  new cmsType_contentTypes_base();
    }
    return $instance;
}
    

function cmsType_addJavaScript() {
    global $cmsVersion;
    echo("<script src='/cms_".$cmsVersion."/cms.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cmsEdit.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cmsSitemap.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_editBox.js' type='text/javascript'></script>\n");
    
    
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_image.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_flip.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_axure.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_faq.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_social.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_company.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_dateList.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_search.js' type='text/javascript'></script>\n");

    $pageName = $GLOBALS[pageData][name];
    
    switch ($pageName) {
        case "admin_cmsLayout" :
            $jsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/admin/cms_admin_layout.js";
            if (file_exists($jsFile)) {
                echo("<script src='/cms_".$cmsVersion."/admin/cms_admin_layout.js'></script>");
            }
            break;
    }
    
    
    
    $useType = site_session_get(cmsSettings,useType);
    if (is_string($useType)) $useType = str2Array ($useType);
    // foreach ($useType as $key => $value) echo ("CMS SETTINGS - USE TYPE -> $key = $value <br>");
    
    $useData = site_session_get(cmsSettings,specialData);
    if (is_string($useData)) $useData = str2Array ($useData);
    // foreach ($useData as $key => $value) echo ("CMS SETTINGS - USE DATA -> $key = $value <br>");
    
    cmsType_owm_addJavascript();
    $useBasket = 0;
    if ($useType[basket]) $useBasket = 1;
    if ($useData[basket]) $useBasket = 1;
    
    if ($useBasket) {
        //echo ("<h1> USER BASKET </h1>");
        echo("<script src='/cms_".$cmsVersion."/cmsBasket.js' type='text/javascript'></script>\n");    
    }
}


function cmsType_owm_addJavascript() {    
    if (file_exists($_SERVER[DOCUMENT_ROOT]."/cms/cms_contentTypes")) $path = "/cms/cms_contentTypes/";
    if (file_exists($_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/cms/cms_contentTypes/")) $path = "/".$GLOBALS[cmsName]."/cms/cms_contentTypes/";
    if (!$path) return 0;    
    
    $handle = opendir($_SERVER[DOCUMENT_ROOT].$path);
    while ($file = readdir ($handle)) {
        if ($file == ".") continue;
        if ($file == "..") continue;
        if (is_dir($file)) continue;
       
        $fileTypeList = explode(".",$file);
        $fileType = $fileTypeList[count($fileTypeList)-1];
        if ($fileType != "js") continue;
        // echo ("file = $file <br>");  
        echo("<script src='".$path.$file."' type='text/javascript'></script>\n");           
    }
    return 1;    
}

function cms_contentType_getTypes($sortet=1) {
    $contentType_class = cms_contentTypes_class();
    $typeList = array();
    $cmsSettings = site_session_get(cmsSettings);
    //show_array($GLOBALS[cmsTypes]);
    foreach ($GLOBALS[cmsTypes] as $key => $value ) {
        
        $type = substr($key,8,strlen($key)-8-4);

        
        $use = cms_contentType_checkType($type);
        if (!$use) {
            if ($value != "own") {
                // echo ("NOT USE $type TypeMode = $value <br />");
                continue;
            }
        }
        // echo ("Check Type $type <br />");
        
        
        if ($type == "frame") {
            if (function_exists("cmsType_".$type)) $existShow = 1;
            if (function_exists("cmsType_".$type."_editContent")) $existEdit = 1;
            if ($existShow AND $existEdit) {
                for ($i=1;$i<=4;$i++) {
                    $type = "frame".$i;


//                    if (is_array($cmsSettings[useType])) $use = $cmsSettings[useType][$type];
//                    else $use = $contentType_class->useType($type);
                    //if ($use) {
                    $name = "Auto $type";
                    if (function_exists("cmsType_".$type."_getName")) {
                        $name = call_user_func("cmsType_".$type."_getName", $contentData,$frameWidth);
                        //  echo ("Function exist ".cmsType."_".$type."_getName ==> '$name' <br />");
                    } else {
                        $name = $contentType_class->typeName($type);
                    }
                    $typeList[$type] = array("name"=>"$name","type"=>$type,"use"=>$use);
                    // echo ("Check for '$type' $use $name <br />");
                }
            }
        } else {
            // echo ("$key = $value ");
            if (!$typeList[$type]) {
                if (function_exists("cmsType_".$type)) $existShow = 1;
                if (function_exists("cmsType_".$type."_editContent")) $existEdit = 1;
                if ($existShow AND $existEdit) {
                    // echo ("Exist $key ! ");
                    //show_array($cmsSettings[useType]);
                    $use = 0;
                    if (is_array($cmsSettings[useType])) $use = $cmsSettings[useType][$type];
                    else $use = $contentType_class->useType($type);
                    // echo ("Use = $use ");
//                    if ($use) {
                        $name = "Auto $type";
                        if (function_exists("cmsType_".$type."_getName")) {

                            $name = call_user_func("cmsType_".$type."_getName", $contentData,$frameWidth);
                            // echo ("Function exist ".cmsType."_".$type."_getName ==> '$name' <br />");
                        } else {
                            $name = $contentType_class->typeName($type);
                        }
                        
                        //echo ("$type = $name <br>");
                    // }
                    $typeList[$type] = array("name"=>"$name","type"=>$type,"use"=>$use);

                } else {
                    echo ("not Exist $type $existShow $existEdit <br />");
                }
                                   
            }            
        }
    }

    asort($typeList);

    return ($typeList);
}

function cms_contentType_defaultName($type) {
    $typeName = $type;
    switch ($type) {
        case "contentName" : $typeName = "Gespeicherter Inhalt"; break;
        case "image" : $typeName = "Bild"; break;
        case "login" : $typeName = "An- und Abmelden"; break;
        case "text" : $typeName = "Text"; break;
        case "textImage" : $typeName = "Text und Bild"; break;
        case "dateList" : $typeName = "Termin Liste"; break;
        case "flip" : $typeName = "Wechselnde Inhalte"; break;
        case "ownPhp" : $typeName = "Eigene Scripte"; break;
        case "social" : $typeName = "Soziale Dienste"; break;
        case "companyList" : $typeName = "Hersteller Liste"; break;
        case "content" : $typeName = "Inhalt"; break;
        case "basket" : $typeName = "Warenkorb"; break;
        case "empty" : $typeName = "dontUse"; break;
        case "footer" : $typeName = "Fußzeile"; break;
        case "frame1" : $typeName = "1 Spalten"; break;
        case "frame2" : $typeName = "2 Spalten"; break;
        case "frame3" : $typeName = "3 Spalten"; break;
        case "frame4" : $typeName = "4 Spalten"; break;
        case "header" : $typeName = "Kopfzeile"; break;
        case "navi" : $typeName = "Navigation"; break;
        case "map" : $typeName = "Karte"; break;
        case "contactForm" : $typeName = "Kontaktforumlar"; break;
        case "subPageNavi" : $typeName = "Seiten Navigation"; break;
        case "wireframe" : $typeName ="Wireframe"; break;
        case "faq" : $typeName ="Fragen und Antworten"; break;
        case "table" : $typeName ="Tabellen"; break;
       
        case "search" : $typeName ="Suche"; break;
        case "axure" : $typeName ="Axure Einbindung"; break;
        case "bookmark" : $typeName ="Favoriten"; break;
        
        case "locationList" : $typeName = "dontUse"; break;
        case "productShow" : $typeName = "dontUse"; break;
        case "categoryList" : $typeName ="dontUse"; break;
        
        default :
            echo ("No Default TypeName for $type <br />");                  
    }
    return $typeName;
   
}


function cms_contentType_allwaysUse() {
    $res = array();
    $res["image"] = 1;
  
    $res["login"] = 1;
    $res["text"] = 1;
    $res["textImage"] = 1;

    $res["content"] = 1;

    $res["footer"] = 1;

    $res["header"] = 1;
    $res["navi"] = 1;
       
    return $res;
}

function cms_contentType_getSortetList($sortet=0) {
    switch ($sortet) {
        case 1 :
            $setSort = array("header","content","footer","text","textImage","image","table","frame1","frame2","frame3","frame4","map","login","contactForm","bookmark","navi","subPageNavi","flip","search","social","basket");
            $typeList = array();
            for($i=0;$i<count($setSort);$i++) {
                $type = $setSort[$i];
                $typeList[$type] = 0;            
            }
            $getList = cms_contentType_getTypes();
            foreach ($getList as $key => $value) {
                $typeList[$key] = $value;
            } 
            break;
        case "all" :
            // echo ("<h1>ALL ContentTypes </h1>");
            $typeList = cms_contentType_getTypes("sortet");
            $disabledTypes = site_session_get(disabledTypes);
           
            if (is_array($disabledTypes)) {
                foreach ($disabledTypes as $type => $value) {
                    $typeName = cms_contentType_defaultName($type);
                    if ($typeName === "dontUse") continue;
                    if (!$typeName) $typeName = "<k>$key</k>";
                    $typeList[$type] = array("name"=>$typeName);
                }
            }            
            break;
            
        default:
            $typeList = cms_contentType_getTypes("sortet");
    }

    $res = array();
    $res[page] = array();
    $res[layout] = array();
    $res[data] = array();

    foreach ($typeList as $key => $value) {
        if (!$value) continue;
        $target = 0;
        $data = 0;
        switch ($key) {
            case "login" : $target=array("layout"); break; 
            case "footer" : $target="page"; break; 
            case "header" : $target="page"; break; 
            case "content" : $target="page"; break; 
            case "navi" : $target=array("layout"); break;
            case "subPageNavi" : $target="layout"; break;
            case "social" : $target=array("layout"); break; 
            
            case "image" : $target="layout"; break; 
            case "imageList" : $target="layout"; break; 
            case "imageSlider" : $target="layout"; break; 
            case "map" : $target="layout"; break; 
            case "frame" : $target = "layout"; break;
            case "frame1" : $target = "layout"; break; 
            case "frame2" : $target = "layout"; break; 
            case "frame3" : $target = "layout"; break; 
            case "frame4" : $target = "layout"; break; 
            case "contactForm" : $target="layout"; break; 
            case "flip" : $target="layout"; break; 
            case "text" : $target="layout"; break; 
            case "textImage" : $target="layout"; break;
            case "contentName" : $target="layout"; break;
            
            case "article" : $target="data"; $data="article"; break;
            case "articlesList" : $target="data"; $data="article"; break;

            case "company" : $target="data"; $data="company"; break;
        
            case "companyShow" : $target="data"; $data="company"; break;
            case "companyList" : $target="data"; $data="company"; break;

            case "categoryList" : $target="data"; $data="category"; break;
            case "category"     : $target="data"; $data="caregory"; break;

            case "locationList" : $target="data"; $data="location"; break;

            case "product" : $target="data"; $data="product"; break;
//            case "productList" : $target="data"; $data="product"; break;
//            case "productShow" : $target="data"; $data="product"; break;

            case "user" : $target = "data"; $data="user"; break;

            case "project" : $target="data"; $data="project"; break;
//            case "projectList" : $target="data"; $data="project"; break;
//            case "projectShow" : $target="data"; $data="project"; break;

        
            case "date" : $target="data"; $data="date"; break;
            case "dateList" : $target="data"; $data="date"; break;

            case "basket" : $target="layout"; break;
            case "ownPhp" : $target="layout"; break;
            case "empty" : $target="not"; break;

            case "bookmark" : $target="layout"; break;
            
            case "dynamicContent" : $target="layout"; break;
            
            case "vmt" : $target="layout"; break; 
        
            case "faq" : $target="data"; $data="faq"; break;
            case "table" : $target="layout"; break;
            case "wireframe" : $target="layout"; break;
            case "axure" : $target="layout"; break;
            case "search" : $target="layout"; break;
        
        
            default :
                //  echo ('cms_contentType_getSortetList() case "'.$key.'" : $target=""; break; <br>');
                $target = "layout";
                
        }

        if (is_string($target)) $target = array($target);

        for ($i=0;$i<count($target);$i++) {
            switch ($target[$i]) {
                case "data" :
                    if ($data) {
                        $res[data][$key] = $value;
                    } else {
                        echo ("not set $data for $target $key <br>");
                    }
                    break;
                default :
                    // echo ("add $key to $target[$i] <br>");
                    $res[$target[$i]][$key] = $value;
            }
        }



    }
    return $res;
}


function cms_content_SelectType($type,$dataName,$viewmode="select") {
    $typeList = cms_contentType_getTypes();

//    $sortList = array();
//    foreach ($typeList as $key => $value) {
//        if (is_array($value)) $name = $value[name];
//        else $name = $value;
//        $sortList[$key] = $name;
//    }
//    asort($sortList);
    $str = "";
    
    switch ($viewmode) {
        case "button" :
            // $str .= "Type = $type ";
            if ($type) {
                $layerName = $typeList[$type];
                if (is_array($layerName)) $layerName = $layerName[name];
                if (!$layerName) $layerName = "unbekannt ($layerType)";
                
            } else {
                $layerName = "nicht definiert";
            }
                
            $str .= "<div id='selectContentType' style='height:20px;width:190px;display:inline-block;' class='layerDrop ui-droppable'>$layerName</div>";
            $str .= "<input id='selectContentType_type' onChange='submit()' type='hidden' name='$dataName' value='$type' />";
            break;
            
        case "select" :
            $str.= "<select name='$dataName' onChange='submit()' class='cmsSelectType' style='min-width:200px;' value='$type' >";
            if (is_array($sortList) AND count($sortList)){
                foreach ($sortList as $code => $name) {
                    if ($typeData["use"]) {
                        $str.= "<option value='$code'";
                        if ($code == $type)  $str.= " selected='1' ";
                        $str.= ">$name</option>";
                    }
                }
            }

            foreach ($typeList as $code => $typeData) {
                if ($typeData["use"]) {
                    $str.= "<option value='$code'";
                    if ($code == $type)  $str.= " selected='1' ";
                    $str.= ">$typeData[name]</option>";
                }
            }
            $str.= "</select>";
        default :
            $str .= "select Type - unkown viewmode '$viewmode' ";

    }
    
    

    
   
    return $str;
}

function cms_contentLayout_getTypes() {
    $typeList = cms_contentType_getTypes();
    

    $typeList[header] = array("name"=>"Kopfzeile");
    $typeList[navi] = array("type"=>"navigation","name"=>"Navigation");
    $typeList[content] = array("type"=>"content","name"=>"Inhalt");
    

    $typeList[login] = array("name"=>"Anmelden","id"=>4);
    $typeList[footer] = array("name"=>"Fußzeile");
    asort($typeList);
    return ($typeList);
}

function cms_contentLayout_selectType($type,$dataName) {
    $typeList = cms_contentLayout_getTypes();

    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' value='$type' >";


    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";
    return $str;


}

function cms_contentType_checkType($type) {
    // echo ("<h1>Type = $type </h1>");
    $cmsSettings = site_session_get(cmsSettings);
    if (!is_array($cmsSettings)) return "no cmsSettings";
   
    if (!is_array($cmsSettings[useType])) {
        $change = 0;
        foreach ($cmsSettings as $key => $value) {
            switch ($key) {
                case "data" : 
                    if (is_string($value)) {
                        $newVal = str2Array($value);
                        if (is_array($newVal)) {
                            
                            site_session_set("cmsSettings,".$key,$newVal);
                            $change++;
                        }
                    }
                    break;
                case "specialData" : 
                     if (is_string($value)) {
                        $newVal = str2Array($value);
                        if (is_array($newVal)) {
                            
                            site_session_set("cmsSettings,".$key,$newVal);
                            $change++;
                        }
                    }
                    break;
                case "useType" :
                     if (is_string($value)) {
                        $newVal = str2Array($value);
                        if (is_array($newVal)) {
                            
                            site_session_set("cmsSettings,".$key,$newVal);
                            $change++;
                        }
                    }
                    break;
               }
        }
        if ($change) {
//            $GLOBALS[cmsSettings] = $
        }
        if (!is_array($cmsSettings[useType])) {
           
//            foreach ($typeList as $key => $value) {
//                echo ("TYPE $key = $value <br>");
//            }
//            
//            foreach ($allwaysUse as $key => $value) {
//                echo ("USE $key = $value <br>");
//            }
//                
            
            //echo ("SES = site_session_get(cmsSettings,[useType] GLOB = $GLOBALS[cmsSettings][useType] <br />");
            return 1;
            return "no useType $change";
        }
    }
    
    if (substr($type,0,5)=="frame") $type = "frame";
    
    // foreach ($cmsSettings[useType] as $key => $value) echo ("ShowType $key => $value <br />");
    $use = intval($cmsSettings[useType][$type]);
    // echo ("USE TYPE $type = $use <br />");
    return $use;    
}


function cms_contentType_showClass($viewMode,$contentData,$frameWidth,$getData=array()) {
    $type = $contentData[type];
    $newContentData = cms_contentType_oldTypes($type,$contentData);
    if (is_array($newContentData)) {
        $contentData = $newContentData;
        $type = $contentData[type];
    }

    $new = 0;

    switch ($type) {
        case "header" :$new = 1; break;
        case "footer" : $new = 1; break;
        case "navi" : $new = 1; break;
        case "content" : $new = 1; break;

        case "login" : $new = 1; break;    
        case "axure" : $new = 1; break;
        case "bookmark" : $new = 1; break;
        case "faq" : $new = 1; break;
        case "map" : $new = 1; break;
        case "search" : $new = 1; break;
        case "contactForm" : $new = 1; break;
        case "subPageNavi" : $new = 1; break;
        case "text" : $new = 1; break;
        case "image" :$new = 1; break;
        case "textImage" :$new = 1; break;
        case "flip" : $new = 1; break;
        case "user" : $new = 1; break;
        case "table" : $new = 1; break;
        case "ownPhp" : $new = 1; break;
        case "frame1" : $new = 1; $type="frame"; break;
        case "frame2" : $new = 1; $type="frame"; break;
        case "frame3" : $new = 1; $type="frame"; break;
        case "frame4" : $new = 1; $type="frame"; break;

        case "product" : $new = 1; break;
        case "category" : $new = 1; break;
        case "article" : $new = 1; break;
        case "company" : $new = 1; break;
        case "project" : $new = 1; break;
        case "date" : $new = 1; break;

        default :
            echo("<div style='background-color:#f00;color:#fff;'>Not New Contenttypeshow for $type</div>");
    }

    if (!$new) return 0;

    $functionName = "cmsType_".$type."_class";
    if (!function_exists($functionName)) return "class Function not exist";


    $newClass = call_user_func($functionName);
    if (!is_object($newClass)) return "no Class get";

    switch ($viewMode) {
        case "content" : $callFunction = "content_show"; break;
        case "layout"  : $callFunction = "layout_show"; break;
        default:
            return "no Function defined for '$viewMode";
    }




    if (!method_exists ($newClass ,$callFunction)) return "Method $callFunction not exist";


    $newClass->$callFunction($contentData,$frameWidth);
    return $newClass;

}

function cms_contentType_show($contentData,$frameWidth,$getData=array()) {

    // NEW CONTENTTYPE SHOW
    $viewMode = "content";
    $newClass = cms_contentType_showClass($viewMode,$contentData,$frameWidth);
    if (is_object($newClass)) return $newClass;
    if ($newClass) echo ("<h3>Error by call NewFunction $newClass </h3>");


    $type = $contentData[type];
    $newContentData = cms_contentType_oldTypes($type,$contentData);
    if (is_array($newContentData)) {
        $contentData = $newContentData;
        $type = $contentData[type];
    }
    
    $new = 0;
   
    switch ($type) {
        case "axure" : $new = 1; break;
        case "bookmark" : $new = 1; break;
        case "faq" : $new = 1; break;
        case "map" : $new = 1; break;
        case "search" : $new = 1; break;
        case "contactForm" : $new = 1; break;
        case "subPageNavi" : $new = 1; break;
        case "text" : $new = 1; break;
        case "image" :$new = 1; break;
        case "textImage" :$new = 1; break;
        case "flip" : $new = 1; break;
        case "user" : $new = 1; break;
        case "table" : $new = 1; break;
        case "ownPhp" : $new = 1; break;
        case "frame1" : $new = 1; $type="frame"; break;
        case "frame2" : $new = 1; $type="frame"; break;
        case "frame3" : $new = 1; $type="frame"; break;
        case "frame4" : $new = 1; $type="frame"; break;
        
        case "product" : $new = 1; break;
        case "category" : $new = 1; break;
        case "article" : $new = 1; break;
        case "company" : $new = 1; break;
        case "project" : $new = 1; break;
        case "date" : $new = 1; break;
        
        default :
            echo("<div style='background-color:#f00;color:#fff;'>Not New Contenttypeshow for $type</div>");
    } 
     
    if ($new) {
        if (function_exists("cmsType_".$type)) {
            $newClass = call_user_func("cmsType_".$type, $contentData,$frameWidth);
            if (!is_object($newClass)) echo ("<h1>NEW CLASS ($type) RETURNS NO OBJECT</h1>");
            // else echo ("<h3>$newClass->contentType, $newClass->contentName </h3>");
            return $newClass;
        } else {
            echo ("unkown Type in cms_contentType_show '$type' <br />");
            if (is_array($contentData)) {
                foreach ($contentData as $key => $value) echo ("#$key = $value **");
                echo ("<br />");
            }
        }
        return 0;
    }
    // echo ("NewClass = ".$newClass." <br>");
    
    
    $showType = cms_contentType_checkType($type);
    if (is_string($showType)) {
        cms_errorBox("Fehler beim Anzeigen von Modul $type <br />$showType");
        return 0;
    }
    if (!$showType) {
        if ($_SESSION[editable]) {
            // foreach($_SESSION as $key => $value) echo ("$key = $value <br />");
            cms_errorBox("Das Anzeigen des Modules '$type' ist nicht aktiviert!<br />Aktivieren Sie das Modul unter CMS-Einstellungen.");
        }
        return 0;
    }
    
    
    
    $newContentData = cms_contentType_oldTypes($type,$contentData);
    if (is_array($newContentData)) {
        $contentData = $newContentData;
        $type = $contentData[type];
    }
    
    
   
    $isContentName = 0;
    $editAble = $_SESSION[userLevel]>6;
    
    
    
      
    global $pageEditAble;
    
    
    $edit = $_SESSION[edit];
    $dragAble = 1;
    switch ($type) {
        case "contentName" :
            
            if ($edit) {
                  
    
                $contentId = $contentData[id];
                if ($dragAble ) {
                    echo ("<div class='cmsContentFrameBox dragBox' id='dragContent_$contentData[id]' >");     
                }
                // <div id="dragContent_51" class="cmsContentFrameBox dragBox ui-draggable">
                $tempContent = cms_contentType_head($contentData,$frameWidth);
                
                $divName = "cmsContent cmsContentFrame_$contentId";
   
                $divName .= " ".$type."_box ".$type."_boxId_$contentId ".$type."_boxPage_".$GLOBALS[pageInfo][pageName];
                $divData = array();
                $divData[id] = "inh_".$contentId;
                div_start($divName,$divData);
                echo ("Das ist der ContentName FRAME ");
                div_end($divName);
                if (is_array($tempContent )) {
                    echo ("This is in EditeMode<br />");
                    $contentData = $tempContent;
                }
                if ($dragAble ) {
                    echo ("</div>");
                }
            }
            $contentData = cmsType_contentName_data($contentData,$frameWidth);
            // if (is_array($myContentData)) $contentData = $myContentData;
            // else $contentData = array("type"=>"unkown");
            $isContentName = 1;
            break;
    }
    
    
    // if (substr($contentData[pageId],0,6)== "layer_") $dragAble = 0;
    if ($dragAble AND $pageEditAble) {
        echo ("<div class='cmsContentFrameBox dragBox' id='dragContent_$contentData[id]' >");       
    }

    $ownFrameWidth = cms_getWidth($contentData[frameWidth],$frameWidth);
    $ownFrameHeight = cms_getWidth($contentData[frameHeight],$frameWidth);
    // echo ("fw = $frameWidth Own Width / height $ownFrameWidth $ownFrameHeight <br>");
           
    if ($ownFrameWidth) $frameWidth = $ownFrameWidth;

    if ($pageEditAble) {
        if ($isContentName) {
             // echo ("dont show head of ContentName '$contentData[contentName]' <br />");
            
            // $tempContent = cms_contentType_head($contentData,$frameWidth);
        } else {
            $tempContent = cms_contentType_head($contentData,$frameWidth);
            if (is_array($tempContent )) {
                // echo ("<b>This is in EditeMode -> $tempContent[id]</b><br />");
                if (!$tempContent[id]) {
                    echo ("<h1> NO Content Id - $contentData[id]</h1>");
                    // show_array($tempContent);
                    $tempContent[id] = $contentData[id];
                }
                $contentData = $tempContent;
            }            
        }
    }

    $contentId = $contentData[id];
    $type = $contentData[type];
    $data = $contentData[data];
    if (is_string($data)) $data = str2array($data);
    if (!is_array($data)) $data = array();
    
    
    // GET TEXT FOR Content
    $textId = "text_".$contentId;
    
    
    
    
    if (is_array($_POST[editText]) AND $edit ) {
        $editID = $_GET[editId];
        if ($contentId == $editId) {
            // echo "Get Text from Edit Text <br>";
            // $textData = $_POST[editText];
        }
    }
    if (!is_array($textData)) {
        $textData = cms_text_getForContent($textId);
    }
    
    // show_array($textData);
    
    // GET FRAME DATA
    $frameStyle = $contentData[frameStyle];
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
//    show_array($frameSettings);
    // echo("Bo / pa / sp $border $padding $spacing $frameWidth<br>");
    $orgFrameWidth = $frameWidth;
    $frameWidth = $frameWidth - ($marginLeft+$marginRight) - ($borderLeft+$borderRight) - ($paddingLeft + $paddingRight);
    // echo ("New FrameWidth $frameWidth ($marginLeft $marginRight) - ($borderLeft $borderRight)  pad = $paddingLeft $paddingRight <br>");
    
    $special_before = cmsFrame_getSpecial_before($frameStyle,$contentData,$frameWidth,$textData);
    $special_after  = cmsFrame_getSpecial_after($frameStyle,$contentData,$frameWidth,$textData);

    //echo ("fw = $frameWidth $marginLeft $marginRight $borderLeft / $borderRight $paddingLeft / $paddingRight <br>");
    if (is_array($special_before)) {
        //echo ("<h1>Special Before</h1>");
        // show_array($special_before);
    }

    if (is_array($special_after)) {
        // echo ("<h1>Special After</h1>");
        //show_array($special_after);
    }


    if ($data[frameClose]) {
        $closeState = "open";
        if ($data[frameCloseLoad]) {
            $closeState = "close";
        } 
        
        
        $openCloseText = $data[frameCloseText];
        $openCloseText = cms_text_getLg($openCloseText);
        if (!$openCloseText) $openCloseText = "&nbsp;";
        div_start("cmsContentFrame_".$closeState,array("style"=>"width:".$orgFrameWidth."px;","id"=>$contentId));
        div_start("cmsContentFrame_".$closeState."_text");
        echo ($openCloseText);
        div_end("cmsContentFrame_".$closeState."_text");
        div_start("cmsContentFrame_".$closeState."_button");
        // echo ("o");
        div_end("cmsContentFrame_".$closeState."_button");
        div_end("cmsContentFrame_".$closeState,"before");
        
    }

    
    $divData = array();
    
    $divStyle = "width:".$frameWidth."px;";
    $divStyle = "";
    if ($ownFrameHeight) {
        
        $myHeight = $ownFrameHeight;
        if ($marginTop) $myHeight = $myHeight - $marginTop;
        if ($marginBottom) $myHeight = $myHeight - $marginBottom;
        
        if ($borderTop) $myHeight = $myHeight - $borderTop;
        if ($borderBottom) $myHeight = $myHeight - $borderBottom;
        
        if ($paddingTop) $myHeight = $myHeight - $paddingTop;
        if ($paddingBottom) $myHeight = $myHeight - $paddingBottom;
        
        // echo ("myHeight = $myHeight ($ownFrameHeight) m= $marginTop / $marginBottom b= $borderTop / $borderBottom p= $paddingTop / $paddingBottom <br>");
        $divStyle .= "height:".$myHeight."px;";
    }
    
    if ($edit) $divStyle .= "min-height:16px;";
    //$frameFloat = $contentData[frameFloat];
    //if ($frameFloat AND $frameFloat != "none")  $divStyle .= "float:$frameFloat;";
    // echo ($divStyle."<br />");
    $divData[style] = $divStyle;

    $paddingTop = $contentData[data][paddingTop];
    if ($paddingTop) {
        //  echo ("Padding Top ".$padding."<br />");
        $divData[style] .= "padding-top:".$paddingTop."px;";
    }

    $divName = "cmsContent cmsContentFrame_$contentId";
    if ($closeState == "close") $divName .= " cmsContentFrame_hidden";
    $divName .= " ".$type."_box ".$type."_boxId_$contentId ".$type."_boxPage_".$GLOBALS[pageInfo][pageName];

    $frameLink = $contentData[frameLink];
    if ($frameLink AND $frameLink != "noLink") {
        $pageId = intval($frameLink);
        if ($pageId > 0) {
            $pageData = cms_page_getData($pageId);
            if (is_array($pageData)) {
                $frameLink = $pageData[name].".php";
               // echo ("frameLink $frameLink <br />");
                $divName .= " cmsFrameLink";
                $divData["link"] = $frameLink;
            }
        }        
    }
    $divData[id] = "inh_".$contentId;
   
    
    if ($frameStyle) $divName .= " $frameStyle";    

    
    
    div_start($divName,$divData);
    // echo ("style '$divData[style]' <br/>");
    
    if (is_string($special_before[output])) {
        echo ($special_before[output]);
    }
    
     // FRAME TEXT - Überschrift
    cms_contentType_frameText($contentData,$textData,"frameHeadline",$frameWidth);
    
    // FRAME TEXT - Text Oben
    cms_contentType_frameText($contentData,$textData,"frameHeadtext",$frameWidth);
    
    
    
    
    switch ($type) {
        case "text"      : cms_contentType_Text($contentData,$frameWidth); break;
        case "textImage" : cmsType_TextImage($contentData,$frameWidth); break;
        case "image"     : cmsType_Image($contentData,$frameWidth); break;
        case "frame1"    : cmsType_Frame($contentData,$frameWidth,1); break;
        case "frame2"    : cmsType_Frame($contentData,$frameWidth,2); break;
        case "frame3"    : cmsType_Frame($contentData,$frameWidth,3); break;
        case "frame4"    : cmsType_Frame($contentData,$frameWidth,4); break;
        case "login"     : cmsType_Login($contentData,$frameWidth); break;
        case "social"    : cmsType_Social($contentData,$frameWidth); break;
        case "ownPhp"    : cmsType_ownPhp($contentData,$frameWidth); break;
        case "dateList"  : cmsType_dateList($contentData,$frameWidth); break;


        case "header"   : cmsType_header($contentData,$frameWidth); break;
        case "navi"     : cmsType_navi($contentData,$frameWidth); break;
        case "content"  : cmsType_content($contentData,$frameWidth); break;
        case "footer"   : cmsType_footer($contentData,$frameWidth); break;

        
        case "flip"      : cmsType_flip($contentData,$frameWidth); break;
        case "contentName" : cmsType_contentName($contentData,$frameWidth); break;
        
        default :             
            if (function_exists("cmsType_".$type)) {
                call_user_func("cmsType_".$type, $contentData,$frameWidth);
            } else {
                
                echo ("unkown Type in cms_contentType_show '$type' <br />");
                if (is_array($contentData)) {
                    foreach ($contentData as $key => $value) echo ("#$key = $value **");
                    echo ("<br />");
                }
               
            }
    }
 
    // FRAME TEXT - UNTEN
    cms_contentType_frameText($contentData,$textData,"frameSubtext",$frameWidth);
    
    if (is_string($special_after[output])) {
        echo ($special_after[output]);
    }
    
    div_end($divName);
    
   
    if ($dragAble AND $pageEditAble) {
        // echo ("</div>");
        echo ("</div>");
    }
    $showSpacer = 1;
    if (substr($contentData[pageId],0,6)== "layer_") $showSpacer = 0;
    if ($showSpacer) {
    
        $spacerAdd = "";
        $last = 0;
        if ($contentData[last]) {
            $spacerAdd .= "spacerLast";
            $last = 1;
        }
        if ($pageEditAble) {
            $spacerClass = "spacer spacerEdit";
            if ($_SESSION[edit]) $spacerClass .= " spacerDrop";
            
            //if ($_SESSION[edit]) 
            echo ("<div id='spacerId_$contentId' class='$spacerClass'>");
        }
        // if ($_SESSION[edit]) $spacerAdd .= " spacerDrop";
        //echo ("<div id='spacerId_$contentId' class='spacer spacerContentType spacerContentType_$type $spacerAdd'>&nbsp;</div>");
        if ($last) {
            echo ("&nbsp;");
        } else {
            echo ("<div class='spacer spacerContentType spacerContentType_$type $spaceAdd'>&nbsp;</div>");
        }
        if ($pageEditAble) {
            // if ($_SESSION[edit]) 
            echo ("</div>"); //  id='spacerId_$contentId' class='spacerDrop'>");
        }
    } else {
        // echo ("No Spacer $contentData[pageId] <br>");
    }

    //echo ("<div class='spacerContent' style='background-color:#7f7;height:30px;'>TYPE = $type &nbsp;</div>");

}

function cms_contentType_oldTypes($type,$contentData) {
    
    $newData = array();
    $showData = 0;
    switch ($type) {
        case "articlesList" : $newType = "article"; $newData[viewMode] = "table"; break;
        case "companyList" : $newType = "company"; $newData[viewMode] = "table"; break;
        case "productList" : $newType = "product"; $newData[viewMode] = "table"; break;
        case "categoryList" : $newType = "category"; $newData[viewMode] = "table"; break;
        
        case "imageList"   : $newType = "image"; $newData[viewMode] = "table"; $showData=0; break;            
        case "imageSlider" : $newType = "image"; $newData[viewMode] = "slider"; break;
        default :
            return 0;
        
    }
    
    
    if ($newType) {
        $contentData[type] = $newType;
        $contentData[oldType] = $type;
        if ($showData  OR $_SESSION[userLevel] == 9) echo ("<b>Change Type from $type => $newType </b><br />");
    }
    
    foreach ($newData as $key => $value) {
        $contentData[data][$key] = $value;
        if ($showData) echo ("Set in Data $key => $value <br>");
    }
    
    if ($showData) {
        if (is_array($contentData[data])) {
            foreach ($contentData[data] as $key => $value ) {
                echo "$key = $value <br>";
            }

        } else {
            echo ("no Array $contentData[data] <br>");
        }
    }
    return $contentData;
}

function cms_contentType_frameText($contentData,$textData,$textType,$frameWidth) {
   
    if (!is_array($textData[$textType])) return 0;
    
    $text = $textData[$textType][text];
    if (!$text) {
        // echo ("No Content for $textType / $text <br>");
        return 0;
    }
    
    $wireFrameOn = $contentData[data][wireframe];
    $wireframeState = cmsWireframe_state();
    if ($wireFrameOn AND $wireframeState) {
        $wireFrameData = $contentData[wireframe];
        if (!is_array($wireframeData)) $wireframeData = array();
        switch ($textType) {
            case "frameHeadline" :
                $wireOn = $wireFrameData[headLine];
                $wireText = cms_text_getLg($wireFrameData[headLineText]);
                echo ("<h1>WireHead $wireText $wireOn</h1>");
                break;
            case "frameHeadtext" :
                $wireOn = $wireFrameData[subHeadLine];
                $wireText = cms_text_getLg($wireFrameData[subHeadLineText]);
                break;
            case "frameSubtext" :
                $wireOn = $wireFrameData[text];
                $wireText = cms_text_getLg($wireFrameData[textText]);
                break;
            default :
                echo "unkown TextType $textType <br>";
        }
        // echo ("WireOn $wireOn wireText = '$wireText' $textType<br>");
        if ($wireOn) $text = cmsWireframe_text($wireText,$text);
    }
    
    
    $css = $textData[$textType][css];
    
    $data = $textData[$textType][data];
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
        switch ($textType) {
            case "frameHeadline" : 
                $className .= " textHeadLine_".$css." frameTextHeadLine_".$css;
                $addStart = "<".$css.">";
                $addEnd = "</".$css.">";               
                break;
        }
    }
    div_start($className,$style);
    if ($addStart) echo ($addStart);
    echo ($text);
    if ($addEnd) echo ($addEnd);
    div_end($className);
}

function cms_contentType_head($contentData,$frameWidth) {
    global $pageInfo;
    $pageId = $contentData[pageId];
    $contentId = $contentData[id];
    $layoutName = $contentData[layout];
    if ($layoutName) return 0;
    $style = "width:".($frameWidth-2)."px;";
    if ($layoutName) $style = "background-color:#bbf;width:".($frameWidth-2)."px;";

    // editMode
    $cmsEditMode = $GLOBALS[cmsSettings][editMode];
    if (!$cmsEditMode) $cmsEditMode = "onPage";
    // echo ("<h1>cmsEditMode = $cmsEditMode</h1>");

    $edit = $_SESSION[edit];
    
    $editId = $_GET[editId];
    $editMode = $_GET[editMode];
    switch ($cmsEditMode) {
        case "onPage":
            if ($contentId != $editId) {
                $divData = array();
                $divData[style] = "width:".($frameWidth+20)."px";
                $divData[headId] = "head_".$contentId;
                div_start("cmsEditLine",$divData);
                div_start("cmsEditLineLine","width:".($frameWidth-10)."px;");
                div_end("cmsEditLineLine");

                $divData = array();
                $divData[style] = "left:0px;top:0px;"; //.($frameWidth-10)."px";
                $divData[headId] = "head_".$contentId;

                $goPage = $pageInfo[page];
                $goPage .= "?editMode=editContentData&editId=".$contentId;
                if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
                if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                $goPage .= "#editFrame_".$contentId;


                echo("<a href='$goPage'>");
                div_start("cmsEditLineBox",$divData);
                echo ("E");
                div_end("cmsEditLineBox");
                echo("</a>");
                div_end("cmsEditLine","before");
                return 0;
            }
            break;
            
        case "siteBar" :
            if ($contentId != $editId) {
                
                $addEditClass = "cmsEditToggle";
                if (!$edit) $addEditClass .= " cmsEditHidden";
                echo ("<div class='cmsEditBox $addEditClass'  >");
                $goPage = $pageInfo[page];
                $goPage .= "?editMode=editContentData&editId=".$contentId;
                if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
                if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                $goPage .= "#editFrame_".$contentId;
        
                // Roll Image
                // echo ("<img src='/cms_base/cmsImages/cmsEditClose.png' border='0px'>");

                // edit Edit
                echo ("<div class='cmsContentFrame_editButton' id='editButtonID_$contentId' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsEdit.png' border='0px'></div>");

                // edit verschieben
                echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

                // edit Löschen
                echo ("<div class='cmsContentFrame_deleteButton' style='display:inline-block;'>");
                $goPage = $pageInfo[page];
                $goPage .= "?editMode=deleteContent&editId=$contentId;#editFrame_$contentId";
                echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsDelete.png' border='0px'></a>");
                echo ("</div>");
                // edit Cut
                // echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsCut.png' border='0px'></a>");
                
                
                echo ("</div>");
                return 0;
            } 
            return 0;
            break;


        case "onPage2" :
            $javaEdit = 0;

            if ($contentId != $editId) {
                $style = "width:auto;";
                if ($contentId == $editId) {
                    $style = "width:100%;opacity:100%;position:relativ;";
                }
                
                $addEditClass = "cmsEditToggle";
                if (!$edit) $addEditClass .= " cmsEditHidden";
                
                if ($contentData[type] == "navi") $style.="z-index:1001;";
                echo ("<div class='cmsEditBox $addEditClass' style='$style'>");
                $goPage = cms_page_goPage("editMode=editContentData&editId=".$contentId);
//                $goPage = $pageInfo[page];
//                $goPage .= "?editMode=editContentData&editId=".$contentId;
                
                $flipNr = 0;
                $flipId = 0;
                if ($_GET[flipLayerNr]) $flipNr = $_GET[flipLayerNr];
                if ($_GET[flipId] ) $flipId = $_GET[flipId];
                if (!$flipNr AND substr($contentData[pageId],0,6)=="layer_") {
                    list($layer,$flipId,$flipNr) = explode("_",$contentData[pageId]);
                    // echo ("<b>$layer $flipId $flipNr </b>");
                }
                
                
                if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
                if ($flipNr) $goPage .= "&flipNr=$flipNr";
                if ($flipId) $goPage .= "&flipId=$flipId";
               //  if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                $goPage .= "#editFrame_".$contentId;
                
                // Roll Image
                // echo ("<img src='/cms_base/cmsImages/cmsEditClose.png' border='0px'>");

                // edit Edit

                $editClass = "cmsContentFrame_editButton cmsContentFrame_editButton_showFrame";
                if ($javaEdit) $editClass .= " cmsContentFrame_editJavaButton";
                echo ("<div class='$editClass' id='editButtonID_$contentId' style='display:inline-block;'>");
                if (!$javaEdit) echo ("<a href='$goPage'>");
                echo ("<img src='/cms_base/cmsImages/cmsEdit.png' border='0px'>");
                if (!$javeEdit) echo ("</a>");
                echo ("</div>");

                // edit verschieben
                echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

                // edit Löschen
                echo ("<div class='cmsContentFrame_deleteButton' id='deleteContent_$contentId' >");
                echo ("<img src='/cms_base/cmsImages/cmsDelete.png' border='0px'>");
                echo ("</div>");

                echo ("<div class='cmsContentFrame_deleteAction cmsContentFrame_deleteAction_hidden deleteContent_$contentId'> ");
                echo ("Löschen:");
                $add = "";
                if ($_GET[editLayout]) $add .= "editLayout=".$_GET[editLayout];
                $goPage = cms_page_goPage($add);
                // echo ($goPage);
                echo ("<form action='$goPage' method='post' class='cmsInlineForm' >");
                echo ("<input type='submit' class='cmsSmallButton' name='deleteContent_$contentId' value='JA' />");
                
                echo ("<input type='submit' class='cmsSmallButton cmsSecond' name='deleteCancel' value='NEIN' />");
                echo ("</form>");
                // echo ("<a href='#' class='cmsSmallButton cmsSecond cmsContentFrame_deleteCancel'>NEIN</a> ");
                echo ("</div>");
                

                if ($contentId == $editId) {
                    echo ("Typ: $contentData[type] id:$contentData[id]");
                }
                // edit Cut
                // echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsCut.png' border='0px'></a>");

                
                $editDivData = array();
                $editDivData["id"] = "editContent_".$contentId;
                $editDivData["style"] = "width:".($frameWidth-2)."px";
                div_start("cmsContentEditFrameContent cmsContentEditFrameContent_hidden",$editDivData);
                // $editRes = cms_content_edit($contentData,$frameWidth);
                div_end("cmsContentEditFrameContent cmsContentEditFrameContent_hidden");



                echo ("</div>");

//                $divName = "cmsEditFrame cmsEditFrame_$contentId cmsEditFrame_hidden";
//                div_start($divName,"background-color:#ab7;width:100%;position:relative;");
//                /*$editRes = cms_content_edit($contentData,$frameWidth);
//                $tempContent = $editRes[tempContent];
//                $out = $editRes[outPut];
//                echo ($out);*/
//                div_end($divName);

                return 0;
            }           
            break;
            
    }
    
    $divData = array();
    $divData[style] = "width:".($frameWidth+20)."px;left:-5px;";
    $divData[headId] = "head_".$contentId;
    $divData[id]  = "editFrame_".$contentId;
    
    switch ($cmsEditMode) {
        case "onPage" :
            div_start("cmsEditLine cmsEditLineOpen",$divData);
    
            div_start("cmsEditLineLine","width:".($frameWidth-10)."px;");
            div_end("cmsEditLineLine");

            $divData = array();
            $divData[style] = "left:0px;top:0px;"; //.($frameWidth-10)."px";
            $divData[headId] = "head_".$contentId;
            div_start("cmsEditLineBox",$divData);
            $goPage = $pageInfo[page];
            echo("<a href='$goPage'>X</a>");
            div_end("cmsEditLineBox");
            div_end("cmsEditLine cmsEditLineOpen","before");
            break;

        case "onPage2" :
           // div_start("cmsEditLine cmsEditLineOpen",$divData);

           
            break;

    }
    
    
    



    $divData = array();
    $divData[style] = $style;
    // $divData[contentId] = $contentId;
    $divData[id] = "editFrame_".$contentId;
    // $divData[style] ="border:1px solid #99f;";

    $addEditClass = "cmsEditToggle";
    if (!$edit) $addEditClass .= " cmsEditHidden";


    $divName = "cmsContentEditFrame $pageId $addEditClass";
    //if ($contentData[type] == "contentName") $divName.= " cmsContentHeadModification";
    // if (substr($contentData[type],0,5)== "frame") $divName.= " cmsContentHeadFrame";
    div_start($divName,$divData);
    $editMode = $_GET[editMode];
    
    $showContentButtons = 1;

    switch ($editMode) {
        case "editContentData" :
            if ($editId == $contentId) {
                //$tempContent = cms_content_edit($contentData,$frameWidth);
                // if (is_array($tempContent)) echo ("TempContent get in head<br />");
                $showContentButtons = 1;
            }
            break;

        case "deleteContent" :
            if ($editId == $contentId) {
                $del = $_GET[del];
                if ($del) {
                    $goPage = $pageInfo[page];
                    if ($_GET[editLayout]) $goPage.="?editLayout=".$_GET[editLayout];
                    if ($del == "YES") {
                        $res = cms_content_delete($contentId);
                        if ($res == 1) {
                            cms_infoBox("Content gelöscht !! ");
                            reloadPage($goPage,2);
                            // return "delete";
                        } else {
                            cms_errorBox("Content nicht gelöscht !!");
                        }
                    }
                    if ($del == "NO") {
                        cms_infoBox("Löschen abgebrochen");
                        $goPage .= "#editFrame_".$contentId;
                        reloadPage($goPage,2);
                        $showContentButtons = 0;
                    }

                } else {
                    echo ("<br />Wollen Sie diesen Inhalt wirklich löschen?<br />");
                    $goPage = "$pageInfo[page]?editMode=deleteContent&editId=$contentId";
                    if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];
                    
                    echo ("<a href='$goPage&del=YES#editFrame_".$contentId."'>JA</a> ");
                    echo ("<a href='$goPage&del=NO#editFrame_".$contentId."'>NEIN</a> ");
                    $showContentButtons = 0;
                }
            }
            break;
        case "contentUp" :
            echo ("Content Sort Up!");
            break;

        case "contentDown" :
            echo ("Content Sort Down!");
            break;
    }
    
    if ($showContentButtons) {
        if ($layoutName) {
            echo ("<strong>$layoutName</strong> ");
            // echo ("$contentData[type] -'$pageId' ");
            echo ("<a href='admin_cmsLayout.php?editLayout=$layoutName'>Layout</a> ");
        } else {
            $headData = array();
            $headData[id] = "cmsContentEditFrameHead_".$contentId;
            $headName = "cmsContentEditFrameHead";

            switch ($cmsEditMode) {
                case "onPage" :
                    div_start($headName,$headData);
                    if ($GLOBALS[userLevel] > 8) {
                        echo ("$contentData[type] -'$pageId' $contentId ");
                    } else {
                        echo ("Inhalt vom Typ <strong> $contentData[type]</strong> &nbsp; ");
                    }

                    if ($_GET[layerNr] OR $_GET[flipLayerNr]) {
                        echo ("layerNr = $_GET[layerNr] > ");
                        foreach ($_GET as $key => $value ) echo (" $key='$value - ");
                    }


                    // verschieben
                    echo("<div class='cmsContentHeadButton dragButton'>verschieben</div>");

                    // editieren
                    $goPage = "$pageInfo[page]?editMode=editContentData&editId=$contentId";
                    if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                    if ($_GET[layerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[layerNr];
                    if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                    echo ("<a href='$goPage' class='cmsContentHeadButton' >edit</a>");
                    
                    // löschen
                    $goPage = $pageInfo[page]."?editMode=deleteContent&editId=$contentId";
                    if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                    if ($_GET[layerNr]) $goPage .= "deleteLayer=".$_GET[layerNr];
                    if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                    $goPage .= "#editFrame_".$contentId;
                    echo ("<a href='$goPage' class='cmsContentHeadButton' >löschen</a>");

                    // Content Up
                    $goPage = $pageInfo[page]."?contentUp=$contentId";
                    if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                    if ($_GET[layerNr]) $goPage .= "editLayer=".$_GET[layerNr];
                    if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                    $goPage .= "#editFrame_".$contentId;
                    echo ("<a href='$goPage' class='cmsContentHeadButton' >&#8593;</a>");

                    // Content Down
                     $goPage = $pageInfo[page]."?contentDown=$contentId";
                    if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                    if ($_GET[layerNr]) $goPage .= "editLayer=".$_GET[layerNr];
                    if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                    $goPage .= "#editFrame_".$contentId;
                    echo ("<a href='$goPage' class='cmsContentHeadButton' >&#8595;</a>");
                    div_end($headName);
                    break;

                case "onPage2" :
                    div_start($headName,$headData);
                    echo ("<div class='cmsContentFrame_editButton cmsContentFrame_editButton_showFrame' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsEdit.png' border='0px'></div>");

                    // edit verschieben
                    echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

                    // edit Löschen
                    $goPage = $pageInfo[page];
                    $goPage .= "?editMode=deleteContent&editId=$contentId;#editFrame_$contentId";
                    echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsDelete.png' border='0px'></a>");



                    echo ("Typ: <b>$contentData[type]</b> id:$contentData[id]");
                    div_end($headName);
                    break;

            }
        }
    }   
    if ($editMode == "editContentData") {
        if ($editId == $contentId) {
            // echo("<br />");
            div_start("cmsContentEditFrameContent");
            $editRes = cms_content_edit($contentData,$frameWidth);
           
            $tempContent = $editRes[tempContent];
//            $tempContent[id] = $contentId;
//            $tempContent[type] = $contentId;
            $out = $editRes[outPut];
            if (!$newMode) echo ($out);
            div_end("cmsContentEditFrameContent");
            if (is_array($tempContent)) echo ("TempContent get in head<br />");

        }
    }
    div_end($divName);
    return $tempContent;

}

function cms_content_edit($contentData,$frameWidth) {
    global $pageInfo,$pageData;
    
    $editClass = cms_contentTypes_class();

    $pageId = $contentData[pageId];
    $editId = $contentData[id];
    
    /// ABBRECHEN
    $cancel = $_POST[editContentCancel];
    if ($cancel) {
        cms_infoBox("Editieren abgebrochen '$pageInfo[page]'");
        $add = "";
        if ($_GET[editLayout]) $add.="?editLayout=".$_GET[editLayout];
        if ($_POST[flipLayerNr]) $add .= "?flipId=".$_POST[flipId]."&flipLayerNr=".$_POST[flipLayerNr];
        $goPage = cms_page_goPage($add);
        $goPage.="#inh_".$editId;
        echo ("<a href='$goPage'>$goPage</a>");
        reloadPage($goPage,1);
        // return array("outPut"=>$out,"tempContent"=>$contentData);
        return 0;
    }
    /// ende of ABBRECHEN

    /// SPEICHERN
    $saveClose = $_POST[editContentSaveClose];
    $save = $_POST[editContentSave];
    $editContent = $_POST[editContent];
    if ($saveClose OR $save) {
        $saveData = array();

        $editText = $_POST[editText];
        $textChange = 0;
        if (is_array($editText)) {
            $textSaveResult = cms_contentType_text_save($editText);
            $textError = $textSaveResult[error];
            $textChange = $textSaveResult[change]; //cms_contentType_text_save($editText);
            if ($textError) {
                cms_errorBox("Fehler beim Text speichern '$textError'");
                echo ("<h1>TextSaveResult</h1>");
                show_array($textSaveResult);
            }
            
        }


        foreach ($editContent as $key => $value) {
            switch($key) {
                case "layout" : break;
                
                default :
                    if ($value != $contentData[$key] ) {
                        // echo ("CHANGE $key from $contentData[$key] to $value <br />");
                        $saveData[$key] = $value;
                    }  
            }
                
        }
        if ($contentData[oldType]) {
            // echo ("OLD TYPE is $contentData[oldType] $new = $editContent[type] <nr>");
            $saveData[type] = $editContent[type];
        }

        $saveData[type] = $editContent[type];
        if (count($saveData) > 0) {
            $saveResult = cms_content_save($editId,$saveData,$contentData);
            if ($saveResult == 1) {
                cms_infoBox("Content gespeichert'$pageId'");
                
            } else {
                if ($saveResult == "noChange") { 
                    $saveResult = 1;
                } else {
                    echo ("SAVEResult $saveResult <br>");
                    $saveResult = 0;
                }
                //$saveResult = 1;
            }
        } else { // keine Veränderung
            $saveResult = 1;
            if ($textChange == 0) {
                cms_infoBox("Keine Veränderung '$pageId'");

            }
        }

        if ($saveResult == 1) { // kein Fehler
            $goPage = $pageInfo[page];
            $reload = 1;
            if ($textError) $reload = 0;
            
            if ($saveClose) { // speichern und schließen
                
                
            
                
                $add = "";
                if ($_GET[editLayout]) $add .="editLayout=".$_GET[editLayout];
                if ($_POST[flipLayerNr]) $add .= "flipId=".$_POST[flipId]."&flipLayerNr=".$_POST[flipLayerNr];
                
                
                $goPage = cms_page_goPage($add);
                $goPage .= "#inh_".$editId;
                
                
                //echo ("Save and Close - Go Page = '$goPage' <br />");
                if ($reload) reloadPage($goPage,1);
                else {
                    echo "<a href='$goPage' >Reload</a><br />";
                }
                return array("outPut"=>$out,"tempContent"=>$editContent);
                return 0;
            }
            if ($save) { // speichern
                $activeTab = $_POST[selectedTab]; 
                $activeFlip = $_POST["activFlip_".$editId];
                // echo ("Check ActivFlip 'activeFlip $editId = $activeFlip <br />");
                // echo ("Selected TAB = $activeTab <br>");
                
                $goPage = cms_page_goPage("editMode=editContentData&editId=$editId");
                if ($activeTab) $goPage .="&selectedTab=$activeTab";
                if ($activeFlip) {
                    echo ("<h1> ACTIVE FLIP $activFlip </h1>");
                    $goPage .= "&activeFlip_".$editId."=$activeFlip";
                }
                
                if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];
                if ($_POST[flipLayerNr]) $goPage .= "&flipId=".$_POST[flipId]."&flipLayerNr=".$_POST[flipLayerNr];
                $ok = $_GET[ok] + 1;
                $goPage .= "&ok=$ok";
                $goPage .= "#editFrame_".$editId;
                // $reload = 0;
                // show_array($_POST);
                //  echo ("Save ! - Go Page = '$goPage' $reload<br />");
                if ($reload) reloadPage($goPage,1);
                else {
                    echo "<a href='$goPage' >Reload</a><br />";
                }
            }
        }
    } else {
        if (is_array($editContent)) {
            // echo ("Change with submit ");
            $tempContent = $editContent;
            //show_array($editContent);
        }
    }
    /// ende of SPEICHERN

    if (!is_array($editContent)) {
        $editContent = $contentData;
        //show_array($contentData);
    }
    if (!$editContent[id] AND $editId ) $editContent[id] = $editId;
    
    $layoutName = $editContent[layout];

    $type = $editContent[type];
    if ($layoutName) echo ("<strong>$layoutName</strong> ");
    // echo ("Edit Content $type: '$pageId' $editId<br />");

    $leftWidth = 200;
    if ($frameWidth < 400) $leftWidth = floor($frameWidth / 3);
    
    $out = "";
    $div1 = div_start_str("inputLine").div_start_str("inputLeft","width:".$leftWidth."px;float:left;padding-top:5px;");
    $div2 = div_end_str("inputLeft").div_start_str("inputRight","float:left;");
    $div3 = div_end_str("inputRight").div_end_str("inputLine","before");
    // foreach ($contentData as $key => $value) echo ("content : $key = $value <br />");
    $out .= "<form action='#editFrame_$editId' class='cmsEditContentForm' method='post'>";
    if ($layoutName) {
        $out .= "<input type='hidden' name='editContent[layout]' value='layoutName'>";
    }

    if ($_GET[flipId]) $out .= "<input type='text' name='flipId' value='$_GET[flipId]'>";
    if ($_GET[flipLayerNr]) $out .= "<input type='text' name='flipLayerNr' value='$_GET[flipLayerNr]'>";
    

    
    $showType = 1;
    $showLevel = 1;
    $showContentName = 1;
    $showFrameSettings = 1;
    $showFrametext = 1;
    $showWireFrame = 1;
    
    $showToLevel = 1;
   
    // Special Data
    $addInput = array();

    $tabInput = array();

    // SETTINGS
    $settings = $editClass->editContent_settings($editContent,$frameWidth,$frameText);
    $tabInput[settings] = $settings;

    // FRAME SETTINGS 
    if ($showFrameSettings) {
        $frameSettings = $editClass->editContent_frameSettings($editContent,$frameWidth,$frameText); 
        $tabInput["frame"] = $frameSettings;
    }
    
    
    // FRAME TEXT
     if ($showFrametext) {
         if (!$editContent[id]) {
             echo ("<h1> keine ID $editContent[id] -> editId = $editId <br>");
             show_array($_POST);
         }
         $tabInput["frameText"] = $editClass->editContent_frameText($editContent,$frameWidth,$frameText);         
    }
    
    // SYSTEM FRAME
    if ($editContent[frameStyle] == "systemFrame") {
        $systemFrame = $editClass->editContent_systemFrame($editContent,$frameWidth,$frameText);
        if (is_array($systemFrame)) {
            $tabInput["systemFrame"] = $systemFrame;
        }
    }

   
    $special_edit = cmsFrame_getSpecial_edit($frameStyle, $editContent,$frameWidth);
    if (is_array($special_edit)) {
        foreach ($special_edit as $addTo => $addValue) {
            if (is_array($addValue)) {
                for ($i=0;$i<=count($addValue);$i++) {
                    if (is_array($tabInput[$addTo]) AND is_array($addValue[$i])) {
                        $tabInput[$addTo][] = $addValue[$i];
                        // echo ("add Special to '$addTo' = $addValue[$i]<br />");
                    } else {
                        // echo ("add Special to '$addTo' = $addValue[$i]<br />");
                    }
                }
            } else {
                $out .= "Special Edit $key => $value <br />";
            }
        }
    }


    // WIREFRAME SETTINGS
    if ($showWireFrame) {
        $wireFrameSettings = $editClass->editContent_wireframeSettings($editContent,$frameWidth,$editText);
        if (is_array($wireFrameSettings)) {
            $tabInput[wireframe] = $wireFrameSettings;            
        }
    }
    
    $selectEdit = $type;

    
    $editData = cms_contentType_editContent($type, $editContent, $frameWidth);

   
    $addInput = $editData[addInput];
    $tabInputAdd = $editData[tabInput];
    $showContentName = $editData[showContentName];
    $selectEdit = $editData[selectEdit];
        // show_array($subDataRes);
    foreach ($tabInputAdd as $key => $value ) {
        if (is_string($key)) {
            // echo ("Add $key ($value) to tabinput <br>");
            $tabInput[$key] = $value;
        } else {
            // echo ("<b>$key ist not a String ($value) </b>");
        }
               
        
        // $tabInput[$key] = $value;
    }


    if ($_SESSION[showLevel] == 9) {
        $showData = array();
        
        $addData = array();
        $addData[text] = "Daten";
        $addData[input] = "Trulla";
        $div = array();
        $div[divname] = "cmsDataList";
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        $outData = "";
        foreach ($editContent[data] as $key => $value ) {
            $outData .= "$key = '$value' <br />";
        }
        $div[content] = $outData;
        
        $addData["div"] = $div;
        
        $showData[] = $addData;
        $tabInput["data"] = $showData;
    }

    // Add Tabs for editContent
    if ($type == "contentName") {
        $contentId = $contentData[data][contentName];
        $subContentData = cmsType_contentName_data($contentData,$frameWidth);
        $subContentType = $subContentData[type];
        //show_array($contentData);
        // echo ("Show Content $contentId , $subContentType <br />");
        $subDataRes = cms_contentType_editContent($subContentType, $subContentData, $frameWidth);
        $subAddInput = $subDataRes[addInput];
        $subTabInput = $subDataRes[tabInput];
        $subShowContentName = $subDataRes[showContentName];
        $subSelectEdit = $subDataRes[selectEdit];
        // show_array($subDataRes);
        foreach ($subTabInput as $key => $value ) {
            // echo ("subTabInput $key = $value <br />");
            $tabInput["content_".$key] = $value;
        }      
    }
      
    

    if ($_POST[selectedTab]) {
        $selectEdit = $_POST[selectedTab];
        // echo ("Set selectEdit to $selectEdit by post<br />");
    } else {
        if ($_GET[selectedTab]) $selectEdit = $_GET[selectedTab];
    }
    
    

    $divData = array();
    $divData["selectTab"] = $selectEdit;

    $editMode = $_SESSION[editMode];

    
    // TABLISTE ////////////////////////////////////////////////////////////////               
    $out .= div_start_str("cmsEditTabLine",$divData);
    foreach ($tabInput as $key => $value) {
        $show = 1;
        $showName = $key;
        if (is_array($value)) {
            if ($value[showName]) {
                $showName = $value[showName];
                // echo ("showName for $key = $showName $value<br>");
                unset($tabInput[$key][showName]);
            }
            if ($value[showTab]) {
                $showTab = $value[showTab];
                // echo ("showTab for $key = $showTab <br>");
                // unset($tabInput[$key][showName]);
            }
        }
        
        
        $divName = "cmsEditTab cmsEditTab_".$key;
        if (substr($key,0,8) == "content_") {
            $divName .= " cmsEditTabModification";
            if ($key == $selectEdit) $divName .= " _selected";
        } else {        
            if ($key == $selectEdit) $divName .= " cmsEditTab_selected";
        }
        
        
        $showMode = "Simple";
        if ($value[showTab]) {
            $showMode = $value[showTab];
            unset($tabInput[$key][showTab]);
        }
        $divName .= " editMode_".$showMode;
       
            
        switch ($showMode) {
            case "Simple" : break;
            case "More" :
                if ($editMode == "Simple") {
                    $divName .= " editMode_hidden";
                }
                break;
            case "Admin" :
                if ($_SESSION[editMode] == "Simple" OR $_SESSION[editMode] == "More") {
                    $divName .= " editMode_hidden";
                }
                break;
            
        }
        
        
        $divData = array("editName"=>$key);
        $out .= div_start_str($divName,$divData);
        $out .= $showName;
        $out .= div_end_str($divName);
    }
    $out .= "<input type='hidden' class='cmsEditTabName' name='selectedTab' value='$selectEdit' style='width:50px;height:12px;font-size:10px' >";

    $out .= div_end_str("cmsEditTabLine","before");

    $editMode = $_SESSION[editMode];
    
    $rightWidth = $frameWidth - $leftWidth - (2 * (3 + 1));

    foreach ($tabInput as $key => $value) {
        $divName = "cmsEditFrame cmsEditFrame_$key";
        if ($key != $selectEdit) $divName .= " cmsEditFrameHidden";
        $divData = array("editName"=>$key);
        $out .= div_start_str($divName,$divData);
     
        $start = 0;
        if ($value[showTab]) {
            unset($value[showTab]);
            
        }
        
        for ($i=$start;$i<count($value);$i++) {
            if (is_array($value[$i])) {
                $showInput = 1;
                $showMode = $value[$i]["mode"];

                $lineDivName = "inputLine";
                if ($showMode) {
                    $lineDivName .= " editMode_".$showMode;
                    switch ($showMode) {
                        case "Simple" : break;
                        case "More" :
                            if ($editMode == "Simple") {
                                $lineDivName .= " editMode_hidden";
                            }
                            break;
                        case "Admin" :
                            if ($_SESSION[editMode] == "Simple" OR $_SESSION[editMode] == "More") {
                                $lineDivName .= " editMode_hidden";
                            }
                            break;


                    }
                } else {
                    if ($_SESSION[userLevel] == 9) {
                        $lineDivName .= " editMode_unkown";
                    }
                }

               

                if (is_array($value[$i][div])) {
                    $divName2 = $value[$i][div][divname];
                    $divStyle = $value[$i][div][style];
                    $divContent = $value[$i][div][content];
                    $divData2 = array();
                    foreach ($value[$i][div] as $divKey => $data) {
                        switch ($divKey) {
                            case "divName" : $divName2 = $data; break;
                            case "style"   : $divData2[style] = $data; break;
                            case "content" : $divContent2 = $data; break;
                            default :
                                $divData2[$divKey] = $data;
                        }
                    }

                    $out .= div_start_str($divName2,$divData2);
                    $out .= $divContent2;
                    $out .= div_end_str($divName2);

                } else {
                    $editText  = $value[$i][text];
                    $editInput = $value[$i][input];
                    $showInput = 1;

                    if (substr($editText,0,6) == "hidden") { // Hidden Input
                        $showInput = 0;
                       // if ($GLOBALS[userLevel] < 9) $showInput = 0;
                        $editText = "adminShow".$editText;
                        //echo ("Hidden Input $GLOBALS[userLevel] - $GLOBALS[showLevel] <br />");
                    }

                    if ($showInput) {

                        $out .= div_start_str($lineDivName) ;
                        $out .= div_start_str("inputLeft","width:".$leftWidth."px;");
                        $out .= $editText;
                        $out .= div_end_str("inputLeft");
                        
                        $out .= div_start_str("inputRight","width:".$rightWidth."px;");
                        $out .= $editInput;
                        //if ($secondLine) $out.= $seccondLine;
                        $out .= div_end_str("inputRight");
                        $out .= div_end_str($lineDivName,"before");

//                        $out .= $div1.$editText.":".$div2;
//                        $out .= $editInput;
//                        $out .= $div3;
                    } else {
                        $out .= $editInput;
                    } 
                      
                    if ($value[$i][secondLine]) {
                        if ($showInput) {
                            $out .= div_start_str($lineDivName) ;
                            $out .= div_start_str("inputSecondLine");
                            $out .= $value[$i][secondLine];
                            $out .= div_end_str("inputSecondLine");
                            $out .= div_end_str($lineDivName,"before");
                        } else {
                            $out .= $value[$i][secondLine];
                            
                        }
                    }
                }
            } else {
                switch ($value[$i]) {
                    case "emptyLine" : $out .= "&nbsp;<br />"; break;

                    default:
                        if ($value[$i]) {
                            $out .= "unkown addInput in $i Key($key) = $value[$i]<br />";
                        }
                        // show_array($value,1);
                }
            }
        }
        $out .= div_end_str($divName);
    }
    
  
   //  $out = str_replace("editContent[", "editContent[".$editContent[id]."][", $out);
    
    $out .= div_start_str("cmsEditFrameButtons");
        
    $out .= "<input type='submit' class='cmsInputButton' name='editContentSave' value='".$editClass->lga("content","saveButton")."' />";
    $out .= "<input type='submit' class='cmsInputButton' name='editContentSaveClose' value='".$editClass->lga("content","saveCloseButton")."' />";
    $out .= "<input type='submit' class='cmsInputButton cmsSecond' name='editContentCancel' value='".$editClass->lga("content","cancelButton")."' />";
    $out .= div_end_str("cmsEditFrameButtons","before");
    $out .= "</form>";
    
    return array("outPut"=>$out,"tempContent"=>$tempContent);
    echo($out);

    return $tempContent;
}

function editContent_frameText($editContent,$frameWidth) {
    $editClass = cms_contentTypes_class();
    
    $data = $editContent[data];
     
    if (!is_array($data)) $data = array();

    $id = $editContent[id];
    $pageId = $editContent[pageId];
    $editId = $_GET[editId];
    $editMode = $_GET[editMode];

    $contentCode = "text_$id";
    $editText = $_POST[editText];
    if (!is_array($editText)) {
        // echo ("Get Text from Database<br>");
        $editText = cms_text_getForContent($contentCode,1);
    } else {
        // echo ("get Text form POST<br>");
       // 
    }
    // show_array($editText);
    $res = array();
    $res[showTab] = "More";
    $res[showName] = $editClass->lga("content","tabFrameText");
    
    
    $addData = array();
    $addData["text"] = "hidden-Text Id";
    $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
    $res[] = $addData;

    
//      $addData["text"] = "Überschrift";
//        $input = $this->filter_select("style", $editText[headline][css],"editText[headline][css]",array("empty"=>"Bitte Überschrift wählen","style"=>"width:150px;float:left;"),"headline");
//        $input .= $this->selectColor($editText[headline][data],"editText[headline][color]","headline");
//        $addData["input"] = $input;
//        $inputSecond  = "<input type='text' style='width:".$frameWidth."px;' name='editText[headline][text]' value='".$editText[headline][text]."' >";
//        $inputSecond .= "<input type='hidden' value='".$editText[headline][id]."' name='editText[headline][id]'>";  
//        $addData["secondLine"] = $inputSecond;
//        $res[text][] = $addData;
    
    $showData = array();
    $showData[css] = 1;
    $showData[view] = "text";
    $showData[color] = 0;
    $showData[width] = $frameWidth;
    $showData[name] = $editClass->lga("content","frameText_headlineText");
    $showData[lgSelect] = 1;
    $showDara[mode] = "More";
    $addData = $editClass->editContent_text("frameHeadline",$editText[frameHeadline], $showData);
    $res[] = $addData;



//    $addData = array();
//    $addData["text"] = "Überschrift";
//    $input = $editClass->filter_select("style", $editText[frameHeadline][css],"editText[frameHeadline][css]",array("empty"=>"Bitte Überschrift wählen","style"=>"width:150px;float:left;"),"headline");
//    $input .= $editClass->selectColor($editText[frameHeadline][data],"editText[frameHeadline]","frameHeadline");
//    $addData["mode"] = "More";
//    $addData["input"] = $input;
//
//    $inputSecond  = "<input type='text' style='width:".($frameWidth-10)."px;' name='editText[frameHeadline][text]' value='".$editText[frameHeadline][text]."' >";
//    $inputSecond .= "<input type='hidden' value='".$editText[frameHeadline][id]."' name='editText[frameHeadline][id]'>";
//    $addData["secondLine"] = $inputSecond;
//    $res[] = $addData;

//    $addData = array();
//    $addData["text"] = "Text Oben";
//    $input = $editClass->filter_select("style", $editText[frameHeadtext][css],"editText[frameHeadtext][css]",array("empty"=>"Bitte Text-Darstellung wählen","style"=>"width:150px;float:left;"),"text");
//    $input .= $editClass->selectColor($editText[frameHeadtext][data],"editText[frameHeadtext]","frameHeadtext");
//    $addData["input"] = $input;
//
//    $inputSecond  = "<textarea name='editText[frameHeadtext][text]' style='width:".($frameWidth-10)."px;height:50px;' >".$editText[frameHeadtext][text]."</textarea>";
//    $inputSecond .= "<input type='hidden' value='".$editText[frameHeadtext][id]."' name='editText[frameHeadtext][id]'>";
//    $addData["secondLine"] = $inputSecond;
//    $addData["mode"] = "More";
//    $res[] = $addData;

    $showData = array();
    $showData[css] = 1;
    $showData[view] = "textarea";
    $showData[color] = 0;
    $showData[width] = $frameWidth;
    $showData[height] = 30;
    $showData[name] = $editClass->lga("content","frameText_topText");// "Rahmen-Text Oben";
    $showData[lgSelect] = 1;
    $showDara[mode] = "More";
    $addData = $editClass->editContent_text("frameHeadtext",$editText[frameHeadtext], $showData);
    $res[] = $addData;



//    $addData = array();
//    $addData["text"] = "Text Unten";
//    $input = $editClass->filter_select("style", $editText[frameSubtext][css],"editText[frameSubtext][css]",array("empty"=>"Bitte Text-Darstellung wählen","style"=>"width:150px;float:left;"),"text");
//    $input .= $editClass->selectColor($editText[frameSubtext][data],"editText[frameSubtext]","frameSubtext");
//    $addData["input"] = $input;
//
//    $inputSecond  = "<textarea name='editText[frameSubtext][text]' style='width:".$frameWidth."px;height:50px;' >".$editText[frameSubtext][text]."</textarea>";
//    $inputSecond .= "<input type='hidden' value='".$editText[frameSubtext][id]."' name='editText[frameSubtext][id]'>";
//    $addData["secondLine"] = $inputSecond;
//    $addData["mode"] = "More";
//    $res[] = $addData;

    $showData = array();
    $showData[css] = 1;
    $showData[view] = "textarea";
    $showData[color] = 0;
    $showData[width] = $frameWidth;
    $showData[height] = 30;
    $showData[name] = $editClass->lga("content","frameText_bottomText");"Rahmen-Text Unten";
    $showData[lgSelect] = 1;
    $showDara[mode] = "More";
    $addData = $editClass->editContent_text("frameSubtext",$editText[frameSubtext], $showData);
    $res[] = $addData;

    return $res;
}

function cms_contentType_SelectEditMode($type,$dataName) {

    $typeList = array();
    $typeList[onPage] = array("name"=>"auf der Seite");
    // if ($_SESSION[userLevel] >= 9) 
    $typeList[onPage2] = array("name"=>"auf der Seite - Neu");
    $typeList[siteBar] = array("name"=>"NavigationsListe");
    $typeList[window] = array("name"=>"Fenster");
    


    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' value='$type' >";

     $str.= "<option value='0'";
     if ($code == $type)  $str.= " selected='1' ";
     $str.= ">Default</option>";

    foreach ($typeList as $code => $typeData) {
         $str.= "<option value='$code'";
         if ($code == $type)  $str.= " selected='1' ";
         $str.= ">$typeData[name]</option>";
    }
    $str.= "</select>";
    return $str;
}

function cms_contentType_editContent($type,$editContent,$frameWidth) {
    $showContentName = 1;
    $selectEdit = $type;
    
    
    
    $newContentData = cms_contentType_oldTypes($type,$editContent);
    if (is_array($newContentData)) {
        $editContent = $newContentData;
        $type = $contentData[type];
    }
    
    switch ($type) {
        case "text" :
            $addInput = cms_contentType_text_editContent($editContent,$frameWidth);
            // show_array($addInput);
            break;

        case "textImage" :
            $selectEdit = "text";
            $addInput = cmsType_textImage_editContent($editContent,$frameWidth);

            break;
        case "image" :
            echo ("HIER <br>");
            $addInput = cmsType_image_editContent($editContent,$frameWidth);
            break;

        case "contentName" :
            $addInput = cmsType_contentName_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;
        case "login" :
            $addInput = cmsType_Login_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;
        case "social" :
            $addInput = cmsType_social_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        case "ownPhp" :
            $addInput = cmsType_ownPhp_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        case "dateList" :
            $addInput = cmsType_dateList_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        case "flip" :
            $addInput = cmsType_flip_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        case "navi" :
            $addInput = cmsType_navi_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        case "footer" :
            $addInput = cmsType_footer_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

         case "header" :
            $addInput = cmsType_header_editContent($editContent,$frameWidth);
            $showContentName = 0;
            break;

        default :
            if (substr($type,0,5)=="frame") {
                $frameCount = substr($type,5);
                $addInput = cmsType_frame_editContent($editContent,$frameWidth,$frameCount);
                break;
            }

            if (function_exists("cmsType_".$type."_editContent")) {
                $addInput = call_user_func("cmsType_".$type."_editContent", $editContent,$frameWidth);
            } else {

                echo "unkown Type $editContent[type] <br />";
            }

    }
  
    // Convert addInput To $tabInput
    $tabInput = array();

    if (is_array($addInput)) {
        foreach($addInput as $key => $value) {
             if (is_integer($key)) {
                //echo ("addInput $key = $value <br />");
                if (!is_array($tabInput[$type])) $tabInput[$type] = array();
                $tabInput[$type][] = $value;
            } else {
                
                
                if (!is_array($value)) {
                    switch ($key) {
                        case "showName" : $tabInput[$key] = $value; break;
                        case "showTab"  : $tabInput[$key] = $value; break;
                        default :
                            echo ("$key is not value $value <br> "); 
                    }
                    
                } else {
                //echo ("addInput is Asso-Array $key = $value <br />");
                // show_array($value);
                foreach($value as $key2 => $value2) {
                    if (is_integer($key2)) {
                        // echo ("addInput is Integer $key2 = $value2 to target $key <br />");
                        if (!is_array($tabInput[$type][$key])) $tabInput[$type][$key] = array();
                        $tabInput[$key][] = $value2;
                    } else {
                        switch ($key2) {
                            case "showTab" :
                                if (!is_array($tabInput[$key])) $tabInput[$key] = array();
                                $tabInput[$key][$key2] = $value2;
                                break;
                            case "showName" : 
                                if (!is_array($tabInput[$key])) $tabInput[$key] = array();
                                $tabInput[$key][$key2] = $value2;
                                break;
                            
                            default :
                                // echo ("addInput is Asso-Array $key / $key2 <br />");
                                $tabInput[$key2] = $value2;
                        }
                        
                        
                    }
                } }
            }
        }
    }
    return array("addInput"=>$addInput,"tabInput"=>$tabInput,"showContentName"=>$showContentName,"selectEdit"=>$selectEdit);
}





/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
