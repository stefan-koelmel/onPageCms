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

            default :
                $res = "Unkown '$selectType' in editContent_selectSettings ";

        }
        return $res;
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

            $showName = cms_text_getLg($pageData[title]);
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
                    

                    $showName = cms_text_getLg($subPageData[title]);
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
                            
                            $showName = cms_text_getLg($subSubPageData[title]);
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
                                    $showName = cms_text_getLg($subSubSubPageData[title]);
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
                    $newValue[icon] = "<img src='/cms_".$this->cmsVersion."/cmsImages/select_direction_".$key.".png' style='float:left;'>";
                    
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
}
?>
