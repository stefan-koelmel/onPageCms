<?php

class cmsClass_content_editSelect extends cmsClass_content_language  {

    function editContent_selectSettings($selectType,$selectValue,$formName,$showData=array()) {
        
        
        
        $res = "";
        switch ($selectType) {
            case "salut" :
                $res = $this->editContent_selectSalut($selectValue,$formName,$showData);
                break;
            case "link" :
                $res = $this->editContent_selectLink($selectValue,$formName,$showData);
                break;

            case "contentType" :
                $res = $this->editContent_selectType($selectValue,$formName,$showData);
                break;

            case "headline" :
                $res = $this->editContent_selectHeadline($selectValue,$formName,$showData);
                break;

            case "align" :
                $res = $this->editContent_selectAlign($selectValue,$formName,$showData);
                break;
            
            case "direction" :
                $res = $this->editContent_selectDirection($selectValue,$formName,$showData);
                break;

            case "imagePosition" :
                $res = $this->editContent_selectImagePosition($selectValue,$formName,$showData);
                break;

            case "imageCut" :
                $res = $this->editContent_selectImageCut($selectValue,$formName,$showData);
                break;
            
            case "calendar" :
                $res = $this->editContent_selectCalendar($selectValue,$formName,$showData);
                break;
            
            case "mobileShow": 
                $res = $this->editContent_selectMobileShow($selectValue,$formName,$showData);
                break;
            
            case "linebreak":
                $res = $this->editContent_selectLinebreak($selectValue,$formName,$showData);
                break;

            default :
                $res = "Unkown '$selectType' in editContent_selectSettings ";

        }
        return $res;
    }
    
    function editContent_selectLinebreak($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode];
        if (!$viewMode) $viewMode = "checkbox";
        $name = $showData[str];
        if (!$name) $name ="no linebreak";
        $str = "";
        switch ($viewMode) {
            case "select" :
                $class = "cmsEdit_checkbox ";
                if ($selectValue) $class.= " cmsEdit_checkbox_selected";
                $class .= " cmsEdit_checkbox_linebreak";
                $str .= "<div class='$class' title='$name' >";
                if ($selectValue) { 
                    $out .= "1";
                    $val = 1;
                } else {
                    $out .= "0";
                    $val = 0;                    
                }
                $str .= "<div class='checkboxOut'>$out</div>";
                $str .= "<input class='checkboxVal' type='hidden' value='$val' name='$formName' style='width:10px;' />";
                $str .= "</div>";                
                break;
           case "checkbox" :
               $str .= "$name";
               if ($selectValue) $checked = "checked='checked'"; else $checked = "";
               $str .= "<input type='checkbox' $checked value='1' name='$formName' />";
               break;
        }
        return $str;
    }
    
    
    function editContent_dropdown_standard($selectValue,$selectList,$formName,$emptyText) {
        $res = "<select value='$selectValue' name='$formName' >";
        
        
        if ($emptyText) {
            if (!$selectValue) $sel = "selected='selected'";
            else $sel = "";
            $res.= "<option value='$key' $sel>$emptyText</option>";       
        }
        
        foreach ($selectList as $key => $value) {
            if ($key == $selectValue) $sel = "selected='selected'";
            else $sel = "";
            $res.= "<option value='$key' $sel>$value</option>";            
        }
        $res .= "</select>";
        return $res;
    }

    
    
    function editContent_select_standard($selectValue,$selectList,$formName,$emptyText) {
        $str = div_start_str("cmsSelectDropdown");

        $str.= div_start_str("cmsSelectDropdown_showSelect");
        if ($selectValue) $str .= "<".$selectValue.">".$selectList[$selectValue]."</".$selectValue.">";
        else $str .= "$emptyText";
        
        
        $str.= div_end_str("cmsSelectDropdown_showSelect");

        $str .= "<input type='hidden' class='cmsSelectDropdown_input' value ='$selectValue' name='$formName' />";

        $str .= div_start_str("cmsSelectDropdown_selectFrame");
        foreach ($selectList as $key => $name) {
            $posDivName = "cmsSelectDropdown_selectPos cmsSelectDropdown_$key";
            if ($key == $selectValue) $posDivName .= " cmsSelectDropdown_selectPos_selected";
            $str .= div_start_str($posDivName,array("title"=>$name,"style"=>"display:block;"));
            $str .= $name;
            $str .= div_end_str($posDivName);
        }
        $str .= div_end_str("cmsSelectDropdown_selectFrame","before");
        $str .= div_end_str("cmsSelectDropdown");



        $res .= $str; // "headLine SELECT not ready";
        return $res;
    }
    
    function editContent_selectIcon_standard($selectValue,$selectList,$formName,$emptyText) {
                
        $icon = 0;
        $title = 0;
        $res .= div_start_str("cmsSelectIcon");
        $res .= "<input type='hidden' class='cmsSelectIcon_input' name='$formName' value='$selectValue'>\n";

        $res .= div_start_str("cmsSelectIcon_showFrame");
        foreach ($selectList as $key => $value) {
            $divName = "cmsSelectIcon_selectPos cmsSelectIcon_".$key;
            if ($key == $selectValue) $divName .= " cmsSelectIcon_selectPos_selected";

            if (is_array($value)) {                
                $icon = $value["icon"];
                $text = $value["text"];
                $title = $value["title"];
            } else {
                $text = $value;
            }

            $res .= div_start_str($divName,array("title"=>$title));
            if ($icon) $res .= $icon; 
            if ($text) $res .= "<div class='cmsSelectIcon_showText' >$text</div>";
                
            $res .= div_end_str($divName);
        }

        $res .= div_end_str("cmsSelectIcon_showFrame","before");
        $res .= div_end_str("cmsSelectIcon");
        return $res;
    }
    
    function editContent_selectSalut($selectValue,$formName,$showData) {
        $salutList = array();
        $salutList[1]=array("name"=>$this->lg("salut","Mister"));

        $salutList[2]=array("name"=>$this->lg("salut","Madam"));
        $salutList[3]=array("name"=>$this->lg("salut","Company"));

        $showData[showList] = $salutList;

        $showData["empty"] = $this->lg("salut","Select");


        $res = cmsUser_selectSalut($selectValue,$formName,$showData);
        return $res;

    }


    function editContent_selectLink($selectValue,$formName,$showData) {
        $pageList = cms_page_getSortList();
        // foreach($pageList as $key => $value) echo ("page $key = $value <br />");
        $str = "";
        
        $viewMode = $showData[viewMode];
        if (!$viewMode) $viewMode = "simple";
        
        
        switch ($viewMode) {
            case "linkWindow" :
                $res = $this->editContent_selectLinkWindow($selectValue,$formName,$showData);
                return $res;
                break;
            
            case "linkShow" :
                $res = $this->editContent_selectLinkWindow($selectValue, $formName, $showData);
                return $res;
                break;
                    
            case "simple" : break;
            
            default :
                return "unkownViewMode $viewMode in selectLink() ";
        }
        
        $showLinkType = $showData[linkType];        
        if ($showLinkType) {
            $str.= "page:";
        }
        

       
        $str.= "<select name='$formName' class='cmsSelectType' style='min-width:200px;' value='$selectValue' >";
        $level = 1;

        $pageId = $selectValue;

        // noLink
        $str.= "<option value='noLink'";
        if (!$selectValue OR $selectValue=="noLink") $str.= " selected='1' ";
        $str.= ">Kein Link</option>";

        foreach ($pageList as $pageName => $pageData) {
            $id = $pageData[id];
            
            $pageCode = $id;
            if ($pageData[link]) {
                $pageCode .= "|".$pageData[link];
            }
            
            $str.= "<option value='$pageCode'";

            if ($selectValue == $pageCode) $str.= " selected='1' ";

            $showName = $this->lgStr($pageData[title]);
            if ($showName=="") $showName = $pageData[name];
         
            $str.= ">$showName</option>";



            if (is_array($pageData[subNavi])) {
                $level++;
                foreach($pageData[subNavi] as $subName => $subPageData ) {
                    $id = $subPageData[id];
                    $pageCode = $id;
                    if ($subPageData[link]) {
                        $pageCode .= "|".$subPageData[link];
                    }
            
                    $str.= "<option value='$pageCode'";
                    

                    $showName = $this->lgStr($subPageData[title]);
                    if ($showName=="") $showName = $subPageData[name];
                    
                    if ($selectValue == $pageCode) $str.= " selected='1' ";
                    $str.= ">";
                    for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                    
                    $str .= "$showName</option>";

                    // 2. Ebene
                    if (is_array($subPageData[subNavi])) {
                        $level++;
                        foreach( $subPageData[subNavi] as $subName => $subSubPageData ) {
                            $id = $subSubPageData[id];  
                            $pageCode = $id;
                            if ($subPageData[link]) {
                                $pageCode .= "|".$subSubPageData[link];
                            }
            
                            $str.= "<option value='$pageCode'";
                            
                            $showName = $this->lgStr($subSubPageData[title]);
                            if ($showName=="") $showName = $subSubPageData[name];
                            
                            if ($selectValue == $pageCode) $str.= " selected='1' ";
                            $str.= ">";
                            for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                           
                            $str .= "$showName</option>";

                            // 3. Ebene
                            if (is_array($subSubPageData[subNavi])) {
                                $level++;
                                foreach( $subSubPageData[subNavi] as $subName => $subSubSubPageData ) {
                                    $id = $subSubSubPageData[id];
                                    $str.= "<option value='$id'";

                                    if ($selectValue == $id) $str.= " selected='1' ";
                                    $str.= ">";
                                    for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                                    $showName = $this->lgStr($subSubSubPageData[title]);
                                    if ($showName=="") $showName = $subSubSubPageData[name];
                                    $str .= "$showName</option>";

                                }
                                $level--;
                            }


                        }
                        $level--;
                    }



                }
                $level--;
            }

        }
        $str.= "</select>";
        return $str;
    }
    
    
    
    function editContent_input($inputType,$code,$formName,$showData) {
        switch ($inputType) {
            case "toggle" : 
                $res = $this->editContent_inputToggle($code,$fromName,$showData);
                break;
            case "checkbox" :
                $res .= $this->editContent_inputCheckbox($code,$formName,$showData);
                break;
            default:
                $res .= "unkown inputType($inputType) in editContent_input()";
        }
        return $res;
    }
    
    function editContent_inputToggle($code,$formName,$showData) {
        // $width = 300;
        $count = 3;
        $mode = "single";
        $outValue = "name";
        $mainFrame = 1;
        $dontShow = array();
        $toggleList = array();
        $itemClass = "";
        $showForm = 1;
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "width" : $width = $value; break;
                case "count" : if ($value) $count = $value; break;
                case "mode"  : $mode = $value; break;
                
                case "class" : $class = $value; break;
                case "itemClass" : $itemClass = $value;
                case "out"   : $outValue = $value; break;
                case "url"   : $url = $value; break;
                case "dontMainFrame" : $mainFrame = 0; break;
                case "empty" : break;
                case "sort" : $sort = $value; break;
                case "showForm" : $showForm = $value; break;
                case "dontShow" : if (is_array($value)) $dontShow = $value; break;
                case "list" : $toggleList = $value; break;

                default :
                    echo ("unkown Mode in cmsCategory_selectCategory_toogle #$key=$value<br>");
            }
        }
        
    


        $border = 1;
        $padding = 3;
        $width = $width-2*$border;
        $divName = "cmsToggleSelect cmsToggleNew ";
        //$str .= div_start_str($class."_contentFrame","display:inline-block;");


        if ($class) $divName .= " ".$class;


        $divData = array();
        if ($width) $divData[style] = "width:".$width."px;";
        if ($mode=="multi") $divName .= "cmsToggleSelect_multi";
        
        
//        $divData[toggleMode] = $mode;
//        $divData[mainCat] = $filter[mainCat];
//        $divData[count] = $count;
//        $divData[dataName] = $dataName;
       //  if ($url) $divData[url] = $url;

        $str.=div_start_str($divName,$divData);
        $widthItem = floor(($width - ($count*$border) -($count*$padding)- $border) / $count);
        
        
        switch ($mode) {
            case "multi" :
                $out = "|";
                $exList = explode("|",$code);
                $codeList = array();
                for ($i=0;$i<count($exList);$i++) {
                    $id = $exList[$i];
                    if ($id) {
                        // echo ("id $id in $code<br>");
                        $codeList[$id] = 1;
                        // $out .= $id."|";
                    }
                }
                // show_array($codeList);
                break;
            default :
                $out = "";
        }

        foreach ($toggleList as $key => $value) {
            if ($dontShow[$key]) continue;
            if (is_array($value)) {
                $name = "array";
                if (is_string($value[name])) $name = $value[name];
                if (is_array($value)) $value = $this->lgStr($value);
                
            } else {
                $name = $value;
            }
                        

            $divNameItem = "cmsToggleItem";
            
            switch ($mode) {
                case "multi" :
                    //echo ("Suche $categoryId in codeList $codeList[$categoryId] <br>");
//                                 if ($codeList[$categoryId]) {
//                                     $out .= $categoryId."|";
//                                     $divNameItem .= " cmsToggleSelected";
//                                     //  echo ("Found $id<br>");
//                                 }
                    break;

               case "single" :
                   if ($code == $key) {
                       $out = $categoryId;
                       $divNameItem .= " cmsToggleSelected";
                   }
                   break;

               default :
           }
           
           
           
           if ($itemClass) $divNameItem .= " ".$itemClass; // ."_".$key;


           $columnNr++;
           $divDataItem = array();
           $divDataItem[title] = $key;
           // $divDataItem[style] = "width:".$widthItem."px;";
//                    $divDataItem[toggleName] = $categoryName;
//                    $divDataItem[toggleId] = $categoryId;
//                    $divDataItem[toggleClass] = $class;

            if ($lineNr) $divDataItem[style] .= "border-top-width:0px;";
            if ($columnNr == $count) {
                $divDataItem[style] .= "border-right-width:1px;";
                $columnNr = 0;
                $lineNr++;
            }
             if ($i == count($categoryList)-1) {
                $divDataItem[style] .= "border-right-width:1px;";
                $columnNr = 0;
            }



            $str .= div_start_str($divNameItem,$divDataItem);
            $str .= $name;
            $str .= div_end_str($divNameItem);
        }
        if ($showForm) $str .= "<input class='cmsToggleValue' type='hidden_' name='$formName' readonly='readonly' value='$out' >";        
        $str.= div_end_str($divName,"before");
        // $str.= div_end_str($class."_contentFrame");
        
        return $str;
    }
    
    function editContent_selectLinkWindow($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode]; 
        $linkSelect = $showData[linkSelect];
        if ($linkSelect) {
           $linkCode = $showData[linkSelect_code];
           $linkForm = $showData[linkSelect_formName];
           $linkStr  = $showData[linkSelect_str];
        }

        
        $linkStyle = $showData[linkStyle];
        if ($linkStyle) {
            $styleCode = $showData[linkStyle_code];
            $styleForm = $showData[linkStyle_formName];
            $styleStr  = $showData[linkStyle_str];
            // echo ("STYLE $linkStyle $styleCode $styleForm $styleStr <br> ");
        }
        
        // foreach ($showData as $key => $value) echo ("data $key = $value <br>");
        
        switch ($viewMode) {
            
            case "linkWindow" :
                $code = "type:page|page:4|url:http://test.de|target:subGame|button:MicroPage|style:second|";
                // $code = "type:url|url:http://test.de|target:subGame|page:4|button:MicroPage|style:second|";
                //$code = "type:url|url:http://test.de|target:subGame|style:0|button:MicroPage|";
                
                
                $codeList = explode("|",$linkCode);
                $linkData = $this->contentShow_getLinkData($linkCode);
//                for ($i=0;$i<count($codeList);$i++) {
//                    $str = $codeList[$i];
//                    if (!$str) continue;
//                    $pos = strpos($str,":");
//                    if (!$pos) {
//                        echo ("NO DOPPEL IN '$str' <br>");
//                    } else {
//                        $key = substr($str,0,$pos);
//                        $value = substr($str,$pos+1);
//                        $linkData[$key]  = $value;
//                    }
//                }
//                
               
                $linkType = $linkData[type]; 
                if (!$linkType) $linkType = "page";
                $linkTypeList = array("page"=>"","url"=>""); // ,"window"=>"");
                
                foreach ($linkTypeList as $key => $value) {
                    $lg = $this->lga("editContent","linkType_".$key);
                    $linkTypeList[$key] = $lg;
                }
                
                if ($showData[linkName]) $name = $showData[linkName];
                
                switch ($linkType) {
                    case "url" :
                        $name = $linkData[url];
                        if (substr($name,0,7) == "http://") $name = substr($name,7);
                        if (substr($name,0,4) == "www.") $name = substr($name,4);
                        
                        
                        break;
                        
                    case "page" :
                        $pageId = intval($linkData[page]);
                        
                        if ($pageId == "moLink") $pageId = 0;
                        if ($pageId ) {
                            $pageData = $this->page_getData($pageId);
                            if (is_array($pageData)) {
                                $name = $pageData[title];
                                // echo ("$pageId $pageData <br>");
                                if (is_array($name)) $name = $this->lgStr($name);
                            }
                        } 
                        break;
                        
                    default :
                        $name = "unkown $linkType";
                        
                }
               
                if ($linkData[button]) $name = $linkData[button];
                if (!$name) $name = "LINK";
                // $str .= "Name = '$name' ";
                
                $str .= "<div class='cmsEdit_selectLink' >";
               
                if ($linkSelect) {                   
                     $str .= "<input type='hidden' class='cmsEdit_selectLink_type' value='$linkType' />";
                     $str .= "<input type='hidden' class='cmsEdit_selectLink_save'  value='$linkCode' name='$linkForm' />"; // style='width:400px;'
                }
                
                if ($linkStyle) {
                   //  $str .= "<input"
                }
                
                $str .= "<div class='cmsEdit_selectLink_show' >";
                
                $button = $name;
                switch ($styleCode) {
                    case "main" :
                        $button = "<div class='mainJavaButton mainSmallButton' >$button</div>"; 
                        break;
                    case "second" :
                        $button = "<div class='mainJavaButton mainSmallButton mainSecond'>$button</div>";
                        break;
                    case "readMore" :
                        $button = "<div class='mainJavaButton mainSmallButton mainReadMore'>$button</div>";
                    case 0 :
                        break;
                    
                    default :
                        $button .= "unkown Style $styleCode ->$name";                
                }

                
                if ($linkSelect) {
                    $typeClass = "cmsEdit_selectLink_showTarget ";
                    if ($linkType) "cmsEdit_selectLink_showTarget_$linkType";
                    $str .= "<div class='$typeClass'>";
                    switch ($linkType) {
                        case "page" : $str .= "cms"; break;
                        case "url" : $str .= "url"; break;
                        case "window" :$str .= "win"; break;
                        default :
                            $str .= "???";
                    }
                    $str .= "</div>";
                    //$str .= "<div class='cmsEdit_selectLink_showLink'>link $linkData[page] </div>";
                }
                if ($linkStyle) {
                    // $str .= "<div class='cmsEdit_selectLink_showStyle'>style $styleCode</div>";
                }
                
                $str .= "<div class='cmsEdit_selectLink_showLink'>$button</div>";
                
                // $str .= "<div style='cmsMainButton' >button</div>";
                $str .= "</div>";
                ////////////// FRAME /////////
                
                $frameClass = "cmsEdit_selectLink_frame";
               // if ($linkForm != "editContent[data][url_link]") $frameClass .= " cmsEdit_selectLink_frameHidden";
                $frameClass .= " cmsEdit_selectLink_frameHidden";
                
                $str .= "<div class='$frameClass' >";
             
                if ($linkSelect) {     
                    //linkType
                    $toggleData = array();
                    $toggleData["list"] = $linkTypeList;
                    $toggleData["class"] = "cmsEdit_selectLink_changeType";
                    $toggleData["itemClass"] = "cmsEdit_selectLink_typeChange";
                    $toggleData[showForm] = 0;
                    // $toggleData[dontShow] = array("window"=>1);
                    $str .= $this->editContent_input("toggle",$linkType,"linkType",$toggleData);         


                    foreach ($linkTypeList as $key =>$value ) {
                        $subDiv = "cmsEdit_selectLink_input cmsEdit_selectLink_input$key ";
                        if ($key != $linkType) $subDiv .= " cmsEdit_selectLink_frameHidden";
                        $str .= "<div class='$subDiv'>";

                        switch ($key) {
                            case "page" :
                                $str .= $linkStr;
                                $linkShowData = array();
                                $pageId = $linkData[page];
                                $str .= $this->editcontent_selectLink($pageId,"help[page]",$linkShowData);
                                break;

                            case "url" :
                                $str .= $linkStr;
                                $url = $linkData[url];
                                if (!$url) $url = "http://www.";
                                $str .= "<input class='cmsEdit_selectLink_url' type='text' value='$url' name='linkHelp' />";
                                $str .= "<br />";
                                $target = $linkData[target];
                                $str .= $this->lga("editContent","linkSelect_target",":");
                                $str .= "<input class='cmsEdit_selectLink_target' type='text' value='$target' name='linkHelp' />";
                                $str .= "<br />";
                                break;
                            default :
                                $str .= "HIER IST DER INHALT von $key / $value <br>";
                        }
                        $str .= "</div>";

                    }

                    $button = $linkData[button];
                    $str .= $this->lga("editContent","linkSelect_button",":");
                    $str .= "<input type='text' class='cmsEdit_selectLink_button' value='$button' name='buttonName' /><br />";
                }
                if ($linkStyle) {
                    $str .= $styleStr;
                    $styleData = array("empty"=>$styleEmpty,"style"=>"width:150px;","class"=>"cmsEdit_selectLink_style");
                    $str .= $this->filter_select("button",$styleCode,$styleForm,$styleData)."<br />";
                }       
                $str .= "<div class='mainJavaButton mainSmallButton' >main</div>"; 
                $str .= "<div class='mainJavaButton mainSmallButton mainSecond'>Sekundär</div>";
                $str .= "<div class='mainJavaButton mainSmallButton mainReadMore'>mehrLesen</div>";
                
                $str .= "</div>";
                $str .= "</div>";
                
                break;
            
            case "linkShow" :
                if ($linkSelect) {
                    $str .= $linkStr;
                    $linkData = array();
                    $str .= $this->editcontent_selectLink($linkCode,$linkForm,$linkData);
                }
                if ($linkStyle) {
                    $str .= $styleStr;
                    $styleData = array("empty"=>$styleEmpty,"style"=>"width:150px;");
                    $str .= $this->filter_select("button",$styleCode,$styleForm,$styleData);
                }                
                break;
                
            default :
                $str .= "unkown ViewMode ($viewMode) in selectLinkWindow()";
        }
        return $str;
        
        if ($linkSelect) {
            $str .= " $linkStr<select></select>";
        }
        if ($linkStyle) {
            $str .= " $styleStr <select></select>";
        }
        
        return $str;
        
        
    }

    function editContent_selectType($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode];
        if (!$viewMode) $viewMode = "dropdown";
        $width = $showData[width];
        if (!$width) $width = 200;
        $res = "";
        switch ($viewMode) {
            case "dropdown" :
                $typeList = cms_contentType_getTypes();
                $res.= "<select name='$formName' onChange='submit()' class='cmsSelectType' style='min-width:".$width."px;' value='$selectValue' >";


                foreach ($typeList as $code => $typeData) {
                    if ($typeData["use"]) {
                        $res.= "<option value='$code'";
                        if ($code == $selectValue)  $res.= " selected='1' ";
                        $res.= ">$typeData[name]</option>";
                    }
                }
                $res.= "</select>";
                break;

            case "select" :
                $useName = $showData[useName];
                if ($useName) {
                    $typeList = cms_contentType_getTypes();
                    $useName = $typeList[$selectValue][name];
                }

                $res .= "<div id='".$showData[id]."' style='height:20px;width:".$width."px;display:inline-block;' class='layerDrop ui-droppable'>";
                if ($useName) $res.= $useName;
                else $res .= $selectValue;
                $res .= "</div>";
                $res .= "<input id='".$showData[id]."_type' type='hidden' name='$formName' value='$selectValue' />";
                break;
            default :
                $res .= "unkown ViewMode in editContent_selectType '$viewMode' ";
        }
        return $res;
    }


    function editContent_selectHeadline($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode];

        $res = ""; // edit_content_selectHeadline($selectValue,$formName,$showData)";
        switch ($viewMode) {
            case "dropdown" :
                $res .= $this->filter_select("headline",$selectValue,$formName,$showData);
                break;

            case "select" :

                $selectList = array();
                $selectList["h1"] = $this->lga("contentSelect","headline_h1");
                $selectList["h2"] = $this->lga("contentSelect","headline_h2");
                $selectList["h3"] = $this->lga("contentSelect","headline_h3");
                $selectList["h4"] = $this->lga("contentSelect","headline_h4");

                $str = div_start_str("cmsSelectDropdown");

                $str.= div_start_str("cmsSelectDropdown_showSelect");
                if ($selectValue) $str .= "<".$selectValue.">".$selectList[$selectValue]."</".$selectValue.">";
                else $str .= "no HeadLine Select";
                $str.= div_end_str("cmsSelectDropdown_showSelect");

                $str .= "<input type='hidden' class='cmsSelectDropdown_input' value ='$selectValue' name='$formName' />";

                $str .= div_start_str("cmsSelectDropdown_selectFrame");
                foreach ($selectList as $key => $name) {
                    $posDivName = "cmsSelectDropdown_selectPos cmsSelectDropdown_$key";
                    if ($key == $selectValue) $posDivName .= " cmsSelectDropdown_selectPos_selected";
                    $str .= div_start_str($posDivName,array("title"=>$name,"style"=>"display:block;"));
                    $str .= "<".$key.">".$name."</".$key.">";
                    $str .= div_end_str($posDivName);
                }
                $str .= div_end_str("cmsSelectDropdown_selectFrame","before");
                $str .= div_end_str("cmsSelectDropdown");



                $res .= $str; // "headLine SELECT not ready";
                break;

            default :
                $res .= "unkown viewMode '$viewMode' for headline";
        }
        return $res;
    }

    function editContent_selectAlign($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode];

        $res = ""; // edit_content_selectHeadline($selectValue,$formName,$showData)";
        $viewMode = "selectIcon";
        
        switch ($viewMode) {
            case "dropdown" :
                $res .= $this->filter_select("text",$selectValue,$formName,$showData);
                break;

            
            
            
            case "selectIcon" :
                $selectList = array();
                $selectList[left] = "lga";
                $selectList[center] = "lga";
                $selectList[right] = "lga";
                $selectList[justify] = "lga";
                
                if (is_array($showData[dontShow])) {
                    foreach ($showData[dontShow] as $key => $v) {
                        unset($selectList[$key]);
                    }
                }
                
                $res = "";
                if (is_array($selectList)) {
                    foreach ($selectList as $key => $value) {
                        if ($value == "lga" or !$value) {
                           $value = $this->lga("contentSelect","direction_".$key);
                           $selectList[$key] = $value;
                        }
                        // $res.= "selectList $key => $value |";
                    }
                }


                $emptyText = $showData[emptyText];
                if ($emptyText === "lga") {
                    $emptyText = $this->lga("contentSelect","direction_emptyText");
                }
                
                foreach ($selectList as $key => $value) {
                    $newValue = array();
                    $newValue[title] = $value;
                    // $newValue[text] = $value;
                    $newValue[icon] = "<img src='/cms_".$this->cmsVersion."/cmsImages/select_hAlign_".$key.".png' style='float:left;'>";
                    
                    $selectList[$key] = $newValue;
                }
                $res = $this->editContent_selectIcon_standard($selectValue, $selectList, $formName, $emptyText);
                break;
                
            case "select":
                $res .= cmsEdit_HorizontalAlign($formName,$selectValue);
                break;
              break;

            default :
                $res .= "unkown viewMode '$viewMode' for headline";
        }
        return $res;
    }
    
    function editContent_selectDirection($selectValue,$formName,$showData) {
        $res = "";
        $selectList = $showData[selectList];
        if (is_array($selectList)) {
            foreach ($selectList as $key => $value) {
                if ($value == "lga" or !$value) {
                    $value = $this->lga("contentSelect","direction_".$key);
                    $selectList[$key] = $value;
                }
                // $res.= "selectList $key => $value |";
            }
        }
        
       
        $emptyText = $showData[emptyText];
        if ($emptyText === "lga") {
            $emptyText = $this->lga("contentSelect","direction_emptyText");
        }
        
        $viewMode = $showData[viewMode];
        switch ($viewMode) {
            case "dropdown" :
                $res .= $this->editContent_dropdown_standard($selectValue,$selectList,$formName,$emptyText);
                
                // $res .= "not ready $viewMode in editContent_selectDirection";
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
                    
                    $selectList[$key] = $newValue;
                }
                
                $res .= $this->editContent_selectIcon_standard($selectValue,$selectList,$formName,$emptyText);
                break;
                
         case "select" :
               $res .= $this->editContent_select_standard($selectValue,$selectList,$formName,$emptyText);
                // $res .= "not ready $viewMode in editContent_selectDirection";                
                break;
            default :
               $res .= "unkown ViewMode in ($viewMode) in editContent_selectDirection";
                
        }
        
        return $res;
    }

    function editContent_selectImagePosition($selectValue,$formName,$showData) {
        $viewMode = $showData[viewMode];
        switch ($viewMode) {
            case "dropdown" :
                $res = "not read ImagePos with dropdown";
                break;
            case "select" :
                $res = cms_content_selectStyle("imagePosition",$selectValue,$formName);
                break;
            default :
               
        }

        return $res;
    }


    function editContent_selectImageCut($selectValue,$formName,$showData) {
        
        $value_hAlign = $selectValue[hAlign];
        $value_vAlign = $selectValue[vAlign];

        if (!$value_hAlign) $value_hAlign = "center";
        if (!$value_vAlign) $value_vAlign = "middle";

        $form_hAlign = $formName[hAlign];
        $form_vAlign = $formName[vAlign];

        $viewMode = $showData[viewMode];


        switch ($viewMode) { 
            case "dropdown" :
                $res = "not Ready '$viewMode' in editContent_selectImageCut ";
                break;

            case "select" :
                $res = "";
                $res.= "<div class='cmsPosSelectFrame' >";
                $res.= "<input type='hidden' name='$form_hAlign' value='$value_hAlign' class='cmsPosInput_posH' >";
                $res.= "<input type='hidden' name='$form_vAlign' value='$value_vAlign' class='cmsPosInput_posV' >"; //  <br />";

                $li_V = array("t"=>"top","m"=>"middle","b"=>"bottom");
                $li_H = array("l"=>"left","c"=>"center","r"=>"right");

                foreach ($li_V as $v_key => $v_name) {
                    foreach ($li_H as $h_key => $h_name) {

                        $className = "cmsPosSelect";
                        if ($value_hAlign == $h_name AND $value_vAlign == $v_name) $className .= " cmsPosSelected";

                        $res.= "<div class='$className' id='pos_".$v_name."_".$h_name."' ></div>";
                    }
                }
                $res .= "</div>";
                break;


            
            default:
                $res = "unkown viewMode ($viewMode) in editContent_selectImagePosition";
        }
        
       //  $res = cmsEdit_imagePosition( $form_hAlign,$form_vAlign,$value_hAlign,$value_vAlign);
        
        // "editContent[data][hAlign]","editContent[data][vAlign]",$data[hAlign],$data[vAlign]);
        return $res;
    }
    
    
    function editContent_selectCalendar($selectValue,$formName,$showData=array()) {
        
        
        list($year,$month,$day) = explode("-",$selectValue);
        
        echo ("$year $month $day ");
        if (!$year OR !$month OR !$day) {
            $selectValue = null;
            list($year,$month,$day) = explode("-",date("Y-m-d"));
        }
        
        
        
        $showWeekDay = $showData[weekDay];
        
        $res = "";
        // $res .= "editContent_selectCalendar($selectValue,$formName,$showData)"; 
        // $res .= "calendar $day $month $year / $formName ";
        
        if ($selectValue) {
            if ($showWeekDay) {
                $hour = 12;
                $min  = 0;
                $sec  = 0;
                $ts = mktime($hour, $min, $sec, $month, $day, $year);
                $weekDay = date("w",$ts);
                $weekDay = cmsDates_dayStr($weekDay);
                $weekStr = $weekDay.", ";
            } else {
                $weekStr = "";
            }

            $dayStr = $day.".".$month.".".$year;
        } else {
            $dayStr = "";
            $weekStr = "";
        }
        
        $res.= div_start_str("cmsCaldendarFrame");
        
        $res .= div_start_str("cmsCalendarInput");
        $showStr = $weekStr.$dayStr;
        $res .= "<input type='text' class='cmsCalendarSelect' value='$showStr' style='min-width=:100px;' />";
        $res.= "<div class='cmsCalendarButton cmsCalendarSelect' >cal</div>";
        $res .= div_end_str("cmsCalendarInput");
        
        
        $calendarDiv = "cmsCalendarSelectFrame cmsCalendarSelectFrame_hidden";
        $res .= div_start_str($calendarDiv);
        $res .= cmsDate_getMonthView($selectValue,"cmsCalendarSelect",200);
        $res .= div_end_str($calendarDiv);
        
        $res .= "<input class='cmsCalendarValue' type='hidden' value='$selectValue' name='$formName' style='width:70px;'  />";
        $res .= "<div class='cmsCalendarFrameHelp'></div>";
        $res.= div_end_str("cmsCaldendarFrame");
        
        // inputStr
        
        
        
        
        return $res;
    }
    
    
    function editContent_selectMobileShow($selectValue,$formName,$showData) {
        $str = "";
        
        $mode = $showData[mode];
        if (!$mode) $mode = "simpleShow";
        switch ($showData[mode]) {
            case "simpleShow" :
                $list = array("show"=>"zeigen","hide"=>"nicht zeigen","landscape"=>"nur Querformat","portrait"=>"nur Hochformat","only"=>"nur Mobil");
                break;
            
            case "showType" :
                $type = $showData[type];
                
                $list = array("show"=>"wie Desktop","below"=>"Untereinander");
                
                switch ($type) {
                    case "dataTable":
                        $list["columnWidth"] = "Spaltenbreite";
                        $list["columnCount"] = "Anzahl Spalten";
                        break;
                }
                break;
            
            case "wireframe" :
                $list = array("show"=>"auch mobil","hide"=>"nicht mobil","only"=>"nur Mobil"); // "landscape"=>"nur Querformat","portrait"=>"nur Hochformat",);
                break;
            
            default :
                return "unkown mode ($showData[mode]) in editContent_selectMobileShow";
        }
        
        // translate
        foreach ($list as $key => $value) {
        }
        
        
        $viewMode = "select";
        if ($showData[viewMode]) $viewMode = $showData[viewMode];
        
        switch ($viewMode) {
            case "select" :
                $str .= "<select name='$formName'>";
                foreach ($list as $key => $value) {
                    if ($selectValue == $key) $selected = "selected='selected'"; else $selected = "";
                    $str .= "<option value='$key' $selected>$value</oprion>";                    
                }
                $str .= "</select>\n";
                break;
            default :
                return "unkown viewMode ($viewMode) in editContent_selectMobileShow";
        }
        
        if ($mode == "showType") {
            switch ($selectValue) {
                case "columnWidth" :
                    
                    $value = $showData["columnWidth_value"];
                    $form  = $showData["columnWidth_formName"];
                    $str .= "min Breite: <input type='text' value='$value' name='$form' style='width:30px;' />";
                    break;
                
                case "columnCount" :
                    $value = $showData["columnCount_value"];
                    $form  = $showData["columnCount_formName"] = "editContent[data][mobileTableView_count]";
                    
                    // $str .= "fN='$formName'";
                    $str .= "spalten Anzahl: <input type='text' value='$value' name='$form' style='width:30px;' />";
                    break;
            }
        }
        
        return $str;
        
        
    }
}
?>
