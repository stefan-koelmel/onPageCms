<?php

class cmsClass_content_editType extends cmsClass_content_editSelect {

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

    function editContent_languageString($value,$dataName,$showData=array()) {
        
       // echo ("editContent_languageString $value $dataName <br>");
        
        $strReplace = str_replace("]","", $dataName);
        $help = explode("[",$strReplace);
        if (count($help) > 1) {
            $setDataSource = $help[0];
            $setFormName = $dataName;
          
            $setDataName = "";
            for ($i=1;$i<count($help);$i++) {
                if ($setDataName) $setDataName.="][";
                $setDataName .= $help[$i];                
            }
            // echo ("SET source=$setDataSource form=$setFormName dataName = $setDataName <br>");
        }
        
        // convert Value to Array
        if (is_string($value)) {
            $help = str2Array($value);
            if (is_array($help)) $value = $help;
        }
        
        
        // CREATE ARRAY if not Exist
        if (!is_array($value)) {
            $showData["defaultText"] = $value;
            $lgList = cms_text_getSettings();
            $setText = $value;
            $value = array();
            foreach ($lgList as $lgCode => $lgData) {
                $value[$lgCode] = $setText;
            }
        } 
        
        if (!$setDataSource) $setDataSource = "editContent";
        
        if (!$showData[dataSource]) $showData[dataSource] = $setDataSource;
        if (!$showData[formName])   $showData[formName] = $setDataSource;
        if (!$showData[dataName])   $showData[dataName] = $setDataName;
        if (!$showData[editMode])   $showData["editMode"] = "SimpleLg";
        if (!$showData[mode])       $showData["mode"] = "SimpleLine";
        if (!$showData[title])      $showData[title] = "NO TITLE";
        if (!$showData[text])       $showData[text] = $value;
        
        $res = $this->edit_text($showData);
        
        return $res;
        
        
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
                
//                $ml_divName = "cmsInput_multiLanguage";
//                if (is_array($lgList) ) $ml_divName .= " cmsInput_multiLanguage_selected";
//                $showStr .= "<div class='$ml_divName' >ml</div>";

                $help = explode("|",  cms_text_lg_show());
                $lgShow = array();
                for($i=0;$i<count($help);$i++) $lgShow[$help[$i]] = 1;


                foreach ($lgList as $lgCode => $value) {
                    $show = $value[show];
                    $lgName = $value[name];
                    $text = $textList[$lgCode];
                    // echo ("get Text $text for $lgCode  $formName $dataName <br>");


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
        
        $textData = $this->textData[$type];
        
        $addData = array();
        $name = "Text";
        $mode = "Simple";
        $view = "text";
        $out = "array";
        $viewMode = "line"; // block;

        
        $show_css = 0;
        $show_color = 0;
        $show_lgSelect = 0;
        $formName = "editText[$type]";
        $width = 100;
        $height = 100;
        $styleType = $type;
        $styleEmpty = "Stil wählen";
        $maxChar = 0;

        $useType = $type;
        if (substr($type,0,6)=="button") $useType = "button";

        $wireframeOptions = 0;
        $mobileOptions = 0;
        
        switch ($useType) {
            case "headline" :
                $name = "Überschrift";
                break;
            case "text" :
                $name = "Text";
                $styleType = "align";
                $maxChar = 1;
                break;
            case "frameHeadline" :
                $name = "Rahmenüberschrift";
                $styleType = "headline";
                if ($this->wireframeEnabled) $wireframeOptions = 1;
                if ($this->mobileEnabled) $mobileOptions = 1;
                break;
            case "frameHeadtext" :
                $name = "Rahmen-Text Oben";
                $styleType = "align";
                if ($this->wireframeEnabled) $wireframeOptions = 1;
                if ($this->mobileEnabled) $mobileOptions = 1;
                $maxChar = 1;
                break;
            case "frameSubtext" :
                $name = "Rahmen-Text Unten";
                $styleType = "align";
                if ($this->wireframeEnabled) $wireframeOptions = 1;
                if ($this->mobileEnabled) $mobileOptions = 1;
                $maxChar = 1;
                break;
            case "button" :
                $name = "Button";
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
        if ($showData[viewMode]) $viewMode = $showData[viewMode];


        $addData = array();

        $editLanguages = $this->editContent_languages();

        $addData["text"] = $name;
        $input = "";
        if ($show_css) {
            $selectShow = array("empty"=>$styleEmpty,"style"=>"width:150px;");
            $selectShow[viewMode] = "select";
            // $input .= $this->filter_select("style", $textData[css],$formName."[css]",array("empty"=>$styleEmpty,"style"=>"width:150px;"),$styleType)."\n";
            $input .= $this->editContent_selectSettings($styleType,$textData[css],$formName."[css]",$selectShow);

        }
        
        if ($show_color) $input .= $this->selectColor($textData[data],$formName,$type)."\n";

        // if ($show_lgSelect) $input .= $this->editContent_languageSelect($editLanguages);


        $help = explode("|",  cms_text_lg_show());
        $lgShow = array();
        for($i=0;$i<count($help);$i++) $lgShow[$help[$i]] = 1;

        $lgList = cms_text_getSettings();
        $showStr = "";

        $textInput = "";
        $textInput .= "<input type='hidden' value='".$textData[id]."' name='".$formName."[id]'>\n";
        
        $divStyle = "";
        switch ($viewMode) {
            case "block" : 
                $divStyle .= ""; 
                $addShowDiv .= "";
                $addEditDiv .= "";
                break;
            case "line"  : 
                $divStyle .= ""; 
                $addShowDiv .= " cmsInput_showLanguage_oneLine";
                $addEditDiv .= " cmsInput_editLanguage_oneLine";
                break;
        }
            
        
        foreach ($lgList as $lgCode => $value) {
            $show   = $value[show];
            $lgName = $value[name];
            $text   = $textData["lg_".$lgCode];

            
            
            // ShowStr
            $className = "cmsInput_showLanguage_$lgCode";
            $className .= $addShowDiv;
            
            
            
            if (!$lgShow[$lgCode]) $className.= " cmsInput_showLanguage_hidden";
            $showStr .= "<div class='$className' style='$divStyle'>\n";
            $showStr.= $lgName.": <b>".$text."</b><br />\n";
            $showStr.= "</div>\n";

            $className = "cmsInput_editLanguage_$lgCode";
            $className .= $addEditDiv;
            if ($value[show]!="edit") $className.= " cmsInput_editLanguage_hidden";
            $textInput .= "<div class='$className' style='$divStyle'>";
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
        
        if ($maxChar) {
            $maxChar = $this->editContent[data][$useType."_maxChar"];
            $formName = "editContent[data][".$useType."_maxChar]";
            $input .= " maxChar: <input type='text' value='$maxChar' name='$formName' style='width:30px;' />";
            if ($maxChar) {
                $readMore = $this->editContent[data][$useType."_readMore"];
                if ($readMore) $checked="checked='checked'"; else $checked = 0;
                $formName = "editContent[data][".$useType."_readMore]";
                $input .= " readMore: <input type='checkbox' cvalue='1' $checked name='$formName' />";
            }
        }
        // $textInput .= $showStr;

        if ($mobileOptions ) {
            $mobileData = array();
            $mobileData[mode] = "simpleShow";
            $mobileData[viewMode] = "select";
            
            $selectValue = $this->editContent[data]["mobilShow".$useType];
            $formName    = "editContent[data][mobilShow".$useType."]";
            $add = $this->editContent_selectSettings("mobileShow",$selectValue,$formName,$mobileData);
            
            $input .= " Mobil: ".$add;
        }
        
        
        
        if ($input) {
            
            // if ($mobileOptions) $input .= " MOBILE OPTIONS ! ";
//            if ($wireframeOptions) $input .= " WIREFRAME OPTIONS ! ";
            
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

    function editContent_sliderSettings() {
        if (is_object($this->mainClass)) $useClass = $this->mainClass;
        else $useClass = $this;

        $res = array();


        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderType"); //"Wechsel";
        $direction = $useClass->editContent[data][direction];
        $input  = $useClass->slider_direction_select($direction,"editContent[data][direction]",array());
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[] = $addData;


        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderLoop"); //"Auto Loop";
        $loop = $useClass->editContent[data][loop];
        $checked = "";
        if ($loop) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
        $addData["mode"] = "Simple";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderImageTime"); //"Zeit für Bild in ms";
        $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$useClass->editContent[data][pause]."'>";
        $addData["mode"] = "More";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderChangeTime"); //"Zeit für Wechsel in ms";
        $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$useClass->editContent[data][speed]."'>";
        $addData["mode"] = "More";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderNavigation"); //"Navigation";
        $navigate = $useClass->editContent[data][navigate];
        $checked = "";
        if ($navigate) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
        $addData["mode"] = "More";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = $useClass->lga("contentType_slider","sliderSelect"); //"Einzelauswahl";
        $pager = $useClass->editContent[data][pager];
        $checked = "";
        if ($pager) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
        $addData["mode"] = "More";
        $res[] = $addData;

        return $res;
    }

    function editContent_imageSettings($dontShow=array()) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $res = array();
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();



        if (!$dontShow[ratio]) {
            $addData = array();
            $addData[text] = $useClass->lga("contentType_image","imageRatio"); //Festes Verhältnis";
            $ratio = $data[ratio];
            if ($ratio) $checked="checked='checked'";
            else $checked = "";
            $input .= "<input class='cmsShowCheckBox' id='checkbox_ratio' type='checkbox' $checked name='editContent[data][ratio]' />";

            $className = "cmsCheckBoxDiv";
            if (!$ratio) $className .= " cmsShowEdit_hidden";
            $input .= "<div id='cmsEditType_ratio' class='$className' style=''>";
            $input .= $useClass->lga("contentType_image","imageRatioValue",":");
            $input .= "<input type='text' style='width:30px' name='editContent[data][ratioX]' value='$data[ratioX]' />";
            $input .= ":";
            $input .= "<input type='text' style='width:30px' name='editContent[data][ratioY]' value='$data[ratioY]' />";
            $input .= "</div>";
            $addData[input] = $input;
            $addData[mode] = "Simple";
            $res[] = $addData;

        }

        if (!$dontShow[crop]) {
            
            $addData = array();
            $crop = $data[crop];
            if ($crop) $checked="checked='checked'";
            else $checked = "";
            $addData[text] = $useClass->lga("contentType_image","imageCrop");;

            $className = "cmsCheckBoxDiv";
            if (!$crop) $className .= " cmsShowEdit_hidden";
            $input = "<input class='cmsCropCheckBox' type='checkbox' $checked name='editContent[data][crop]' />";
            $addData[input] = $input;
            $addData[mode] = "Simple";
            $res[] = $addData;

        }

        if (!$dontShow[position]) {
            $addData = array();
            $showData = array("viewMode"=>"select");
            $addData["text"] = $useClass->lga("contentType_image","imagePosition"); // "Bild-Position";
            $addData["input"] = $this->editContent_selectSettings("imageCut",array("vAlign"=>$data[hAlign],"hAlign"=>$data[vAlign]),array("vAlign"=>"editContent[data][hAlign]","hAlign"=>"editContent[data][vAlign]"),$showData); // cmsEdit_imagePosition("editContent[data][hAlign]","editContent[data][vAlign]",$data[hAlign],$data[vAlign]);
            $addData[mode] = "Simple";
            $res[] = $addData;
        }

        if (!$dontShow[zoom]) {

            $addData = array();
            $addData["text"] = $useClass->lga("contentType_image","imageZoom"); //"Zoom Bild";
            $checked = "";
            if ($data[zoom]) $checked = " checked='checked'";
            $addData["input"] = "<input type='checkbox' value='1' name='editContent[data][zoom]' $checked >";
            $addData[mode] = "Simple";
            $res[] = $addData;
        }

        if (!$dontShow[resize]) {

            $addData = array();
            $resize = $data[resize];
            if ($resize) $checked="checked='checked'";
            else $checked = "";
            $addData[text] = $useClass->lga("contentType_image","imageResize"); // "Vergrößern wenn zu klein";
            $input = "<input type='checkbox' $checked name='editContent[data][resize]' />";
            $addData["input"] = $input;
            $addData[mode] = "More";
            $res[] = $addData;
        }
        return $res;
    }

    


    function editContent_imageList($imageListStr,$showData) {
        if ($this->mainClass) $useClass = $this->mainClass;
        else $useClass = $this;

        $res = array();
        $data = $useClass->editContent[data];
        if (!is_array($data)) $data = array();

        $width =  400;
        $height = 200;
        $imgWidth = 100;
        $imgHeight = 75;
        $imageAdd = 0;
        $imageUpload = 0;
        $imageSortAble = 0;
        $imageDeleteAble = 0;
        $delimiter = "|";
        $dataName = "imageList";
        $showMode = "line";
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "width"           : $width = $value; break;
                case "height"          : $height = $value; break;
                case "imageUpload"     : $imageUpload = $value; break;
                case "imageAdd"        : $imageAdd = $value; break;
                case "imageFolder"     : $imageFolder = $value; break;
                case "dataName"        : $dataName = $value; break;
                case "imgWidth"        : $imgWidth = $value; break;
                case "imgHeight"       : $imgHeight = $value; break;
                case "showMode"        : $showMode = $value; break;
                case "imageSortAble"   : $imageSortAble = $value; break;
                case "imageDeleteAble" : $imageDeleteAble = $value; break;


            }
        }

        if (!$imageFolder) $imageFolder = "images/";
        $imgListData = cmsImage_imageList_getListFormString($imageListStr);

        $imgList = $imgListData[imgList];
        $delimiter = $imgListData[delimiter];
        // show_array($imgList);
        $str = "";

        $divData = array();
        $divData["class"] = "";
        // if ($imageSortAble) $divData["class"].= " cmsImageSortList";
        if ($imageDeleteAble) $divData["class"].= " cmsImageDeleteList";
        $divData[style] = ""; //"width:".$width."px;display:inline-block;";
        $str.= div_start_str("cmsImage_listSelect",$divData);
       // show_array($showData);


        $showListData = array();

        $showListData["showMode"] = $showMode;
        $showListData["imageSortAble"] = $imageSortAble;
        $showListData["imageDeleteAble"] = $imageDeleteAble;

        $cmsEditMode = $GLOBALS[cmsSettings][editMode];


        $str .= cmsImage_listContent($imgList,$delimiter,$dataName,$width,$height,$imgWidth,$imgHeight,$showListData);

        if ($imageAdd) {

            switch ($useClass->editModee) {
                case "onPage2" :
                    $str .= $useClass->lga("contentType_image","imageAdd",":"); //"Bild hinzufügen:";
                    $str .= "<img id='path:$imageFolder' src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='30px' height='30px' class='cmsImageSelectModul'> ";
                    break;
                    // cmsImageSelect cmsImageSelectModul

                default :
                    $str .= $useClass->lga("contentType_image","imageAdd",":"); //"Bild hinzufügen:";
                    $str .= "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='30px' height='30px' class='cmsImageSelect'> ";

                    $str .= "<input type='text' class='cmsImageId' tabindex='5000' style='width:30px;' name='imageAdd' value='' onFocus='submit()' >";

                    $divName = "cmsImageSelector";
                    $divData = array();
                    $divData[style] = "height:0px;background-color:#bbb;visible:none;overflow:hidden;";
                    $divData["folderName"] = $imageFolder;
                    $str.= div_start_str($divName,$divData);
                    $str.= cmsImage_selectList($imageFolder,0);
                    $str.= div_end_str($divName);

                    if ($imageUpload) {
                        $str .= "<input name='uploadImage' tabindex='5000' type='file' size='50' '  >"; //  maxlength='10000000' onChang='submit()
                        $str .= "<input name='uploadFolder' type='text' size='50' class='cmsImagePathSelector' readonly='readonly'  value='$imageFolder' >";
                    }


            }




        }


        $str.= div_end_str("cmsImage_listSelect");
    //$res = cmsImage_List($code,$showData);
        return $str;
    }

    
    function data_getTableContent() {
       
        $table = $this->pageClass->cmsName."_cms_".$this->tableName;
        
        $query = "SHOW COLUMNS FROM $table";
        $result = mysql_query($query);
        $fields = array();
        
        if (!$result) {
            echo "Error in Query $query ".mysql_error()."<br>";
            return $fields;
        }
        while ( $meta = mysql_fetch_assoc($result)) {

            $name = $meta[Field];
            $type = $meta[Type];
            $data = array();
            switch ($type) {
                case "varchar(1)" : $type= "onOff";                            
            }
            $cont = 0;
            $noFilter = 0;
            switch ($name) {
                case "id" : 
                    $showId = 0;
                    if ($this->contentData[data][viewMode] == "single") $showId = 1;
                    if ($this->contentData[data][viewMode] == $this->tableName) $showId = 1;
                    
                    //  echo ("<h3> SHOW ID viewMode=".$this->contentData[data][viewMode]." tableName=$this->tableName showId = $showId <br>");
                    if (!$showId) $noFilter = 1;
                    $cont = $name;
                    break;

                case "category" : $cont = $name; break;
                case "subCategory" : $cont = $name; break;
                case "company" : $cont = $name; break;
                case "product" : $cont = $name; break;
                case "location" : $cont = $name; break;
                case "url" : $cont = $name; break;
                case "link" : $cont = "url"; break;
                case "name" : $cont = "headline"; break;
                case "subName" : $cont = "headline"; break;

                case "info"     : $cont = "text"; break;
                case "longInfo" : $cont = "text"; break;

                case "data" : $cont = "data"; $noFilter=1; break;
                case "sort" : $cont = "sort"; $noFilter=1; break;

                case "image" : $cont = $name; break;
                case "imageId" : $cont = "image"; break;

                case "lastMod" : $cont = $name; $noFilter=1;break;
                case "changeLog" : $cont = $name; $noFilter=1; break;

            }
            $data[type] = $type;
            if ($cont) $data[content] = $cont;
            if ($noFilter) $data[noFilter] = $noFilter;

            $fields[$name] = $data;

//            echo ("Add '$name' type='$type' ");
//            if ($cont) echo ("content = '$cont' ");
//            echo ("<br>");                                    
        }        
        return $fields;
    }


    // EDIT FUNCTIONS
    function editContent_ViewMode($editContent,$frameWidth) {
        // echo ("editContent_ViewMode($editContent,$frameWidth)<br />");
        $viewModeList = $this->filter_select_getList("viewMode");
        if (!is_array($viewModeList)) return 0;
        if (count($viewModeList) == 0) return 0;
        $res = array();
        // $res[showName] = "ARTIKEL";

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

        
        $lgaType = "editData";
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
                $addData["text"] = $this->lga($lgaType,$viewMode."_row"); // "Anzahl in Reihe";
                if (!$data[dataRow]) $data[dataRow] = 3;
                $input  = "<input name='editContent[data][dataRow]' style='width:100px;' value='".$data[dataRow]."'>";
                $addData["input"] = $input;
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgaType,$viewMode."_rowAbs"); //"Abstand in Reihe";
                if (!$data[dataRowAbs]) $data[dataRowAbs] = 10;
                $input  = "<input name='editContent[data][dataRowAbs]' style='width:100px;' value='".$data[dataRowAbs]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgaType,$viewMode."_rowHeight"); //"Reihen höhe";
                $input  = "<input name='editContent[data][dataColHeight]' style='width:100px;' value='".$data[dataColHeight]."'>";
                $addData["input"] = $input;
                $addData[mode] = "Simple";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgaType,$viewMode."_colAbs"); //"Abstand Zeilen";
                if (!$data[dataColAbs]) $data[dataColAbs] = 10;
                $input  = "<input name='editContent[data][dataColAbs]' style='width:100px;' value='".$data[dataColAbs]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = $this->lga($lgaType,$viewMode."_maxCount"); // "Maximale Projekttanzahl";
                $input  = "<input name='editContent[data][maxCount]' style='width:100px;' value='".$data[maxCount]."'>";
                $addData["input"] = $input;
                $addData[mode] = "More";
                $res[] = $addData;
                
                if ($this->mobileEnabled) {
                    $addData = array();
                    $addData["text"] = $this->lga($lgaType,$viewMode."_mobileView"); //  "Mobile Show";
                    $selectValue = $data[mobileTableView];
                    $formName = "editContent[data][mobileTableView]";
                    $showData = array();
                    $showData["viewMode"] = "select";
                    $showData["mode"] = "showType";
                    $showData["type"] = "dataTable";
                    
                    $showData["columnCount_value"] = $data[mobileTableView_count];
                    $showData["columnCount_formName"] = "editContent[data][mobileTableView_count]";
                    
                    $showData["columnWidth_value"] = $data[mobileTableView_width];
                    $showData["columnWidth_formName"] = "editContent[data][mobileTableView_width]";
                    
                    $input  = $this->editContent_selectSettings("mobileShow", $selectValue, $formName, $showData);
                    $addData["input"] = $input;
                    $addData[mode] = "More";
                    $res[] = $addData;
                }
                
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

    
    function data_filter_idList() {
        $filterList = array();
        // $filterList[tableName] = $this->tableName;
        
        $filter = array();
        $sort = "id";
        foreach ($this->editContent[data] as $key => $filterValue) {
            if (substr($key,0,7)!= "filter_") continue;
            
            $filterKey = substr($key,7);
            if ($filterKey == "id") continue;
            
            if (!$filterValue) continue;
            
            switch ($filterValue) {
                case "on" : $filterValue = "1"; break;
                case "off" : $filterValue = "0"; break;                
                    
            }
            
            $filter[$filterKey] = $filterValue;
        
        }
        
        foreach ($filter as $key=>$value) {
            echo ("Filter $this->tableName with $key = '$value' <br>");
        }
        
        
        switch ($this->tableName) {
            case "project" :
                $dataList = cmsProject_getList($filter,$sort);
                break;
        }
      
        if (is_array($dataList)) {
            foreach ($dataList as $key => $value) {
                $id = $value[id];
                $name = $value[name];
                $filterList[$id] = $name;
            }                
        }
      
        
        
        
        return $filterList;
        
        
    }
    
    function data_filter_getList($dataKey,$dataType=0) {
        $filterList = array();

        switch ($dataKey) {
            case "show"      : $dataType = "onOff"; break;
            case "highlight" : $dataType = "onOff"; break;                
        }
        
        if ($dataType) {
            switch ($dataType) {
                case "onOff" :
                    $filterList["on"] = "Ja";
                    $filterList["off"] = "Nein";
                    break;
            }
        }
        return $filterList;
        
        
        
    }
    
    function data_filter_select($filterKey,$filterValue,$filterType,$filterContent) {
        $filterList = array(); // 0=>"");
        switch ($filterContent) {
            case "show" : $filterType = "onOff"; break;
            case "highlight" : $filterType = "onOff"; break;
                
            
            
            case "id" :
                $filterList = $this->data_filter_idList();
                break;
                
                
                
            
            case "image" : 
                $filterList["exist"] = "mit Bild";
                $filterList["more"] = "mehrere Bilder";
                $filterList["noImage"] = "kein Bild";
                break;
            
            case "text" :
                $filterList["content"] = "mit Inhalt";
                $filterList["noContent"] = "ohne Inhalt";
                break;
            case "headline" :
                $filterList["content"] = "mit Inhalt";
                $filterList["noContent"] = "ohne Inhalt";
                break;    
            
            case "category" :
                $filterList["category"] = "mit Kategorie";
                $filterList["noCategory"] = "ohne Kategorie";
                break;
        }
        
        switch ($filterType) {
            case "onOff" : 
                $filterList["on"] = "Ja";
                $filterList["off"] = "Nein";
                break;
            
            case "date" :
                $filterList["today"] = "Heute";
                $filterList["week"] = "Diese Woche";
                $filterList["nextWeek"] = "Nächste Woche";
                $filterList["lastWeek"] = "Letzte Woche";
                
                $filterList["month"] = "Diesen Monat";
                $filterList["nextMonth"] = "Nächsten Monat";
                $filterList["lastMonth"] = "Letzten Monat";
                
                $filterList["past"] = "Vergangenheit";
                $filterList["furure"] = "Zukunft";
                break;
                
        }
        
        $str .= "$filterValue <select name='editContent[data][filter_".$filterKey."]' style='width:200px;' >";
        
        $str .= "<option value='' $selected ></option>";
        foreach ($filterList as $key => $value) {
            if ($filterValue == $key) $selected="selected='selected'";
            else $selected ="";
            $str .= "<option value='$key' $selected >$value</option>";            
        }
        $str .= "</select>";
        
        
        // $str .= "data_filter_select(key=$filterKey,value=$filterValue,type=$filterType,cont=$filterContent) ";
        return $str;
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
        
        
        if ($this->tableName) {
            
            
            $tableContent = $this->data_getTableContent();
            // echo ("<h3>Get Filter for Table $this->tableName $tableContent</h3>");
            
            // if (!is_array($tableContent)) $tableContent = array();
            foreach ($tableContent as $filterKey => $filterData) {
                // echo ("FILTER $filterKey <br>");
                $name = $filterData[name];
                if (!$name) $name = "'$filterKey' ";
               
                $filterContent = $filterData[content];
                $filterType    = $filterData[type];
                $noFilter=$filterData[noFilter];
                if ($noFilter) continue;
                
                $addData = array();
                $addData["text"] = "nach $name";
                
                $inputList = array();
                
                // $inputList[] = "Content ='$filterContent' ";
                
                
                
                $filterValue = $this->editContent[data]["filter_".$filterKey];
                $checked = "";
                
             //   if ($filterValue) { $checked = "checked='checked'"; $toggleClass .= " inputToggleSelected"; }
               //  $addInput = "Daten filtern: <input type='checkbox' id='$toggleId' $checked name='editContent[data][filter_".$filterKey."]' value='1'>";
                $addInput = $this->data_filter_select($filterKey,$filterValue,$filterType,$filterContent);
                $inputList[] = $addInput;
                
                
                $customFilterShow = $this->editContent[data]["customFilter_".$filterKey];
                $checked = "";
                $toggleClass = "inputToggleSelect";
                $toggleId    = "toggleSelect_".$filterKey;
                    
                if ($customFilterShow) { $checked = "checked='checked'"; $toggleClass .= " inputToggleSelected"; }
                $addInput = array();
                $addInput[input] = "Filter anzeigen: <input type='checkbox' id='$toggleId' class='$toggleClass' $checked name='editContent[data][customFilter_".$filterKey."]' value='1'>Filter anzeigen ";
                $addInput[mode] = "toggleSelect";
                $addInput[state] = $customFilterShow;
                $inputList[] = $addInput;
                    
                //$inputList[] = "Filter zeigen";
                
                
                $addData["input"] = $inputList;
                $addData["mode"] = "Simple";

                $res[] = $addData;
                
                
            }
            
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
                $inputList = array();
                
                $inputList[] = $this->filter_select($filterType,$filterValue,"editContent[data][filter_".$filterKey."]",$showData,$filter,$sort);
                
                $customFilter = $filterData[customFilter];
                if ($customFilter) {
                    $customFilterShow = $editContent[data]["customFilter_".$filterKey];
                    $checked = "";
                    
                    
                    $toggleClass = "inputToggleSelect";
                    $toggleId    = "toggleSelect_".$filterKey;
                    
                    if ($customFilterShow) { $checked = "checked='checked'"; $toggleClass .= " inputToggleSelected"; }
                    $addInput = array();
                    $addInput[input] = "Filter anzeigen: <input type='checkbox' id='$toggleId' class='$toggleClass' $checked name='editContent[data][customFilter_".$filterKey."]' value='1'>Filter anzeigen ";
                    $addInput[mode] = "toggleSelect";
                    $addInput[state] = $customFilterShow;
                    
                    $inputList[] = $addInput; //"<input type='checkbox' $checked name='editContent[data][customFilter_".$filterKey."]' onChange='submit()' value='1'>Filter anzeigen ";
                    
                    
                    $customFilterView = $editContent[data]["customFilterView_".$filterKey];
                    $addInput = array();
                    
                    $showData = array();
                    $addInput[input] = " Filterart: ".$this->editContent_customview_select($customFilterView,$filterKey,$showData);
                    $addInput[mode] = "toggleItem";
                    $addInput[id] = "toggleItem_".$filterKey;
                    $addInput[state] = $customFilterShow;                    
                    $inputList[] = $addInput;

                    
                    
                  
                }
                $addData["input"] = $inputList;
                $addData["mode"] = "Simple";



                $res[] = $addData;
            }

        }
        return $res;
    }

    function editContent_filter_getList() {
        $filterList = array();

        // $filterList[produkt] = array("name"=>"Produkt","filter"=>array(),"sort"=>"name");

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
        $list[toggleSitch] = "JA / NEIN auswahl";
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
    
    function editcontent_dataBox_definition ($showLeftRight,$showMiddle,$showDescription) {
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();
                
        
         if ($showLeftRight) {
            if ($showMiddle) $addData["text"] = $this->lga("contentType_box","nameLRC"); //"Rechts / Mitte / Links";
            else $addData["text"] = $this->lga("contentType_box","nameLR");"Rechts / Links";
            $input = "";
            $input .= $this->lga("contentType_box","leftWidth");
            $input .= "<input type='text' style='width:40px;' value='$data[LR_left]' name='editContent[data][LR_left]' />";
            $input .= $this->lga("contentType_box","leftDist",":");
            $input .= "<input type='text' style='width:40px;'value='$data[LR_abs]' name='editContent[data][LR_abs]' />";
            if ($showMiddle) {
                $input .= $this->lga("contentType_box","middleWidth",":");
                $input .= "<input type='text' style='width:40px;' value='$data[LR_center]' name='editContent[data][LR_center]' />";
                $input .= $this->lga("contentType_box","middleDist",":");
                $input .= "<input type='text' style='width:40px;'value='$data[LRC_abs]' name='editContent[data][LRC_abs]' />";
            }
            $input .= $this->lga("contentType_box","rightWidth",":");
            $input .= "<input type='text' style='width:40px;'value='$data[LR_right]' name='editContent[data][LR_right]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            $res[] = $addData;
        }

        if ($showDescription) {
            $addData["text"] = $input .= $this->lga("contentType_box","descriptionWidth",":");"Bezeichnungsbreite";
            $input = "";
            $input .= "<input type='text' style='width:40px;' value='$data[spanWidth]' name='editContent[data][spanWidth]' />";
            $addData["input"] = $input;
            $addData["mode"] = "More";
            
            $res[] = $addData;
        }
        return $res;
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


    function filter_select($filterType,$code,$formName,$showData,$filter=array(),$sort="") {
        // echo "$filterType,$code,$dataName,$showData,$filter,$sort<br />" ;
        if ($showData[filter]) $filter = $showData[filter];
        if ( $showData[sort])  $sort =   $showData[sort];
        // echo ("FILTER $filter SORT = $sort <br />");
        $selectList = $this->filter_select_getList($filterType,$filter,$sort="");
        $str = "";

        $style = "min-width:100px;";
        
        $viewMode = $showData["viewMode"];
        if (!$viewMode) $viewMode = "dropdown";
        
        
        if ($showData[style]) $style = $showData[style];
        
        
        // Convert LG
        foreach ($selectList as $key => $value) {
            if ($value == "lg" OR $value == "lga") {
                $selectList[$key] = $this->lga("select",$filterType."_".$key);
                continue;
            }
            if (is_array($value)) {
                if ($value[name]=="lga" OR $value[name] == "lg") {
                    $selectList[$key][name] = $this->lga("select",$filterType."_".$key);
                }
            }
        }
        
        
        if (!is_array($selectList)) return "no selectList in filter_select type='$filterType' ";
        if (!count($selectList)) return "no Items in selectList in filter_select type='$filterType' ";
        switch ($viewMode) {
            case "dropdown" : 
                //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
                $str.= "<select name='$formName' class='cmsSelectType'  style='$style' ";
                if ($showData[submit]) $str.= "onChange='submit()' ";
                $str .= "value='$code' >";

                $empty = $showData["empty"];
                if (is_integer($empty) AND $empty == 0) {
                    $emptyStr = "";
                } else {
                    $emptyStr = $this->lga("select",$filterType."_empty");
                    if (!$emptyStr) $emptyStr = "Kein Filter";

                    if ($showData["empty"]) $emptyStr = $showData["empty"];

                    if ($emptyStr) {
                        $str.= "<option value='0'";
                        if (!$code) $str.= " selected='1' ";
                        $str.= ">$emptyStr</option>";
                    }
                }


                $outValue = "name";
                if ($showData[out]) $outValue = $showData[out];

                foreach ($selectList as $key => $value) {

                    if ($value == "lg" OR $value == "lga") {
                        $value = $this->lga("select",$filterType."_".$key);
                    }

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
                break;
              
            case "selectIcon" :                
                foreach ($selectList as $key => $value) {
                    $newValue = array();
                    $newValue[title] = $value;
                    $newValue[text] = $value;
                    
                    $filterType = "direction";
                    $imageKey = $key;
                    if ($imageKey == "vert") $imageKey = "verti";
                    
                    $newValue[icon] = "<img src='/cms_".$this->cmsVersion."/cmsImages/select_".$filterType."_".$imageKey.".png' style='float:left;'>";
                    
                    
                    
                  //  $newValue[icon] = "<img src='/cms_".$this->cmsVersion."/cmsImages/select_direction_".$key.".png' style='float:left;'>";
                    
                    $selectList[$key] = $newValue;
                }
                
                $str .= $this->editContent_selectIcon_standard($code,$selectList,$formName,$emptyStr);
                break;
                
            case "select" :
                $str .= $this->editContent_select_standard($code,$selectList,$formName,$emptyStr);
                // $res .= "not ready $viewMode in editContent_selectDirection";                
                break;
                
            default :
                $str = "unkown viewMode($viewMode) in filter_selectfor type($filterType)";
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
            
            case "direction" :
                $res = $this->directionFilter_filter_getList($filter,$sort);
                break;
            
            case "dataFilter" :
                $res = array(""=>"Alle zeigen","0"=>"Nein","1"=>"Ja");
                break;
            
            

            default :
                if ($this->tableName) {
                    $dataList = $this->data_getTableContent();
                    if ($dataList[$filterType]) {
                        //echo ("Filter from DataValue $filterType <br>");
                        // foreach ($filter as $key => $value) echo ("Filter[$key] = $value <br>");
                        
                        $res = $this->data_filter_getList($filterType);
                        
                        // $res = $this->data_filter_select($filterType, $filterValue, "onOff", $filterType);
                    }
                } 
                
                if (!is_array($res)) {
                    $res = $this->filter_select_getList_own($filterType,$filter,$sort);
                }
                if (!is_array($res)) {
                     echo ("<h1> filter_select_getList($filterType,$filter,$sort) </h1>");
                }

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
    
    function directionFilter_filter_getList($filter,$sort) {
        $res = array();
        $res[hori] = "lg"; //array("name"=>"Horizontal");
        $res[vert] = "lg"; // array("name"=>"Vertikal");
        return $res;
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
        if ($this->pageClass->adminView) {
            echo ("<h1>ADMIN VIEW</h1>");
        }
        
        
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

            if ($pageing_count <= 0) $pageing_count = 10;
            
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
        
        if ($this->pageClass->adminView) $divStart = "adminShowData"; 
        else $divStart = "cmsShowData";

        div_start($divStart);   

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
        div_end($divStart);
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
        
        if ($this->pageClass->adminView) $divStart = "adminDataPageing";
        else $divStart = "cmsDataPageing";
        
        div_start($divStart."Frame");
        if (!$pageing_mode) $pageing_mode = "small";
        switch ($pageing_mode) {
            case "small" :
                if ($pageing_actPage > 2) {
                    echo ("<a href='".$pageing_url."page=1' title='Seite 1' class='".$divStart."Button' ><<</a>");
                }
                if ($pageing_actPage > 1) {
                    echo ("<a href='".$pageing_url."page=".($pageing_actPage-1)."' title='Seite ".($pageing_actPage-1)."' class='".$divStart."Button' ><</a>");
                }

                echo (" Seite $pageing_actPage / $pageing_pageCount ");


                if ($pageing_actPage < $pageing_pageCount) {
                    echo ("<a href='".$pageing_url."page=".($pageing_actPage+1)."' title='Seite ".($pageing_actPage+1)."' class='".$divStart."Button'>></a> ");
                }
                if ($pageing_actPage < ($pageing_pageCount-1)) {
                    echo ("<a href='".$pageing_url."page=$pageing_pageCount' title='letzte Seite' class='".$divStart."Button' >>></a> ");
                }
                break;
            case "all" :
                for ($i=1;$i<=$pageing_pageCount;$i++ ) {
                    if ($i==$pageing_actPage) {
                        echo ("<span class='".$divStart."ButtonAktiv'>$i</span>");
                    } else {
                        echo ("<a href='".$pageing_url."page=$i' class='".$divStart."Button' title='Seite $i' >$i</a>");
                    }
                }
                break;
        }
        div_end($divStart."Frame");
    }

    function showList_titleLine($data,$showData,$sortUrl,$frameWidth) {

        // echo ("showList_getLine($data,$showData,$frameWidth)<br />");
        $actSort = $_GET[sort];
        
        if ($this->pageClass->adminView) $divStart = "adminShowData";
        else $divStart = "cmsShowData";
        
        
        $str = "";
        $divName = $divStart."TitleLine";
        $divData = array();
        //$height = $showData["image"]["height"];
        // $divData[style] .= "width:".$frameWidth."px;";
        $divData[style] .= "margin-top:3px;";
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

                $style = "";
                // $style = "float:left;width:".$width."px;";
                if ($width) $style .= "width:".$width."px;";
                if ($align) $style .= "text-align:$align;";
                $divData2 = array();
                $divData2[style] = $style;

                $divName2 = $divStart."TitleItem ".$divStart."TitleItem_$key";
                $link = "";
                if ($sort) {
                    $divName2 .= " ".$divStart."Sort";
                    $link = $sortUrl."sort=".$key;
                    if ($actSort == $key) {
                        $divName2 .= " ".$divStart."Sort_down";
                        $divData2[sortUp] = $key."_up";
                        $link = $sortUrl."sort=".$key."_up";
                        $name .= " <img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/sort_down_black.png' width='10px' border='0'>";
                    }
                    if ($actSort == $key."_up") {
                        $divName2 .= " ".$divStart."Sort_up";
                        $divData2[sortDown] = $key;
                        $link = $sortUrl."sort=".$key;
                        $name .= " <img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/sort_up_white.png' width=10px border=0>";
                    }
                }

                if ($link) {
                    // $str.= "<a href='".$link."'>";
                    $divData2["link"] = $link;
                }
                    
                // echo ("Link $link <br />");
                
                $str.= div_start_str($divName2,$divData2);
                $str.= $name;
                $str.= div_end_str($divName2);
                // if ($link) $str.= "</a>";
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


        if ($this->pageClass->adminView) $divStart = "adminShowData";
        else $divStart = "cmsShowData";

        // $str.= "<a href='$goPage' >";
        $divName = $divStart."Line";
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        $mainHeight = $showList["image"]["height"];
        if ($showData[height]) $mainHeight = $showData[height];

        // if ($mainHeight) $divData[style] .= "height:".$mainHeight."px;"; //line-height:".$height."px;";
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
                        if (!$width) $width = 100;
                        if (!$height) {
                            $height = floor ($width / 4 * 2);
                            // echo ("new Height = $height (w=$width <br>");
                        }
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
                        if (is_array($cont)) $cont = $this->lgStr($cont);
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
                $style= "";
                if ($width) $style .= "width:".$width."px;";
                if ($align) $style .= "text-align:$align;";
                // if ($mainHeight) $style .= "height:".$mainHeight."px;";

                if ($cont[0] === "a") {
                    //$cont = "AAA".$cont;
                    $help = str2Array($cont);
                    if (is_array($help)) {
                        $lg = $this->lgStr($help);
                        if ($lg) $cont = $lg;
                    }
                }
                
                $str.= div_start_str($divStart."Item ".$divStart."Item_$key",$style);
                $str.= $cont;
                $str.= div_end_str($divStart."Item ".$divStart."Item_$key");
            }
        }
        $str.= div_end_str($divName); // ,"before");
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
        
        
        if ($this->pageClass->adminView) {
            echo ("ADMINFILTER <br>");
        }
        
        
        
        $data = $contentData[data];
        if (!is_array($data)) return 0;
        $showFilter = array();
        foreach ($data as $key => $value) {
            if (substr($key,0,13) == "customFilter_") {
                $filterName = substr($key,13);
                $filterShow = $value;
                $filterViewType = $data["customFilterView_".$filterName];
                
                if ($filterShow) {
                    $showFilter[$filterName] = $filterViewType;
                    // echo ("SHOW FILTER $filterName $filterShow $filterViewType <br>");                
                } 
            }
        }
        if (count($showFilter) == 0) return 0;

        foreach ($_POST as $key => $value) {#
            // echo ("Post $key = $value <br />");
        }

        $reloadList = array();

        $filterDataList = $this->editContent_filter_getList();
        
        if ($this->pageClass->adminView) {
            $divStart = "adminCustomFilter";
            $filterShow = $_SESSION[adminFilter];
        }
        else {
            $divStart = "cmsCustomFilter";
            $filterShow = 1;
        }
        
        $divName = $divStart."List";

        // if (!$divName) $divName = "cmsCustomFilterList";
        
        // $adminFilter = $_SESSION[adminFilter];
        // echo ("<h2>ADMINFILTER = $adminFilter</h2>");
        if (!$filterShow) $divName .= " cmsAdminHidden";

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
                    
                case "dataFilter" : 
                    $str .= $this->filter_select("dataFilter", $code, $dataName, $showData);
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
        $filterList[hidden]["filter"] = array("show"=>"0");
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

?>