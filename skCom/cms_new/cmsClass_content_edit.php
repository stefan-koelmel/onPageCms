<?php
class cmsClass_content_edit extends cmsClass_content_editType {
    function init_edit() {

        $this->editId = $_GET[editId];
        $this->editMode = $GLOBALS[cmsSettings][editMode];
        $this->showMode = $_SESSION[editMode];

        if ($this->contentId == $this->editId) $this->doEdit = 1;
        else $this->doEdit = 0;

        global $pageInfo;
        $goPage = $pageInfo[page];
       
        $addPage = "";
        if ($_GET[editLayout]) $addPage .= "&editLayout=".$_GET[editLayout];
        
        
        if ($_GET[layerNr]) $addPage .= "&editLayer=".$_GET[layerNr];
        if ($_GET[flipLayerNr]) $addPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];

        // SELECT TAB
        $this->selectEdit = $this->contentType;
        
        if ($_POST[selectedTab]) {
            $this->selectEdit = $_POST[selectedTab]; 
            $addPage .= "&selectedTab=".$this->selectEdit;
        } else {
            if ($_GET[selectedTab]) {
                $this->selectEdit = $_GET[selectedTab];
                $addPage .= "&selectedTab=".$this->selectEdit;
            }
        }
        
        $this->goPage = $goPage;
        $this->addPage = $addPage;
        // if ($addPage) echo ("ADD PAGE $addPage <br>");
    }


    function edit_content() {
        $frameWidh = $this->frameWidth;


        $this->edit_head();

        if ($this->doEdit) {
            $res = $this->edit_content_show();
            return $res;
        }

        return 0;
    }

    function edit_head() {
        $this->contentData = $this->contentData;
        $this->frameWidth = $this->frameWidth;


        global $pageInfo;
        $pageId = $this->contentData[pageId];
        
        $layoutName = $this->contentData[layout];
        if ($layoutName) return 0;
        $style = "width:".($this->frameWidth-2)."px;";
        if ($layoutName) $style = "background-color:#bbf;width:".($this->frameWidth-2)."px;";

        // editMode

      

        $edit = $_SESSION[edit];

        $editId = $_GET[editId];
        $editMode = $_GET[editMode];
        
        switch ($this->editMode) {
            case "onPage":
                $exit = $this->edit_head_onPage();
                if ($exit) return 0;
                break;
            
            case "siteBar" :
                $exit = $this->edit_head_siteBar();
                if ($exit) return 0;
                break;
  
            case "onPage2" :
                $active = 0;
                $exit = $this->edit_head_onPageNew($active);
                if ($exit) return 0;
                break;
            default :
                echo ("unkown EditMode $this->editMode <br>");
                return 0;
        }

    }


    function edit_content_show() {
        $style = "width:".($this->frameWidth-2)."px;";

        $divData = array();
        $divData[style] = $style;
        // $divData[contentId] = $this->contentId;
        $divData[id] = "editFrame_".$this->contentId;
        // $divData[style] ="border:1px solid #99f;";

        $addEditClass = "cmsEditToggle";
        if (!$this->edit) $addEditClass .= " cmsEditHidden";


        $divName = "cmsContentEditFrame $pageId $addEditClass";
        //if ($this->contentData[type] == "contentName") $divName.= " cmsContentHeadModification";
        // if (substr($this->contentData[type],0,5)== "frame") $divName.= " cmsContentHeadFrame";
        div_start($divName,$divData);

        $editMode = $_GET[editMode];
        $showContentButtons = 1;
        if ($editMode) {
            $showContentButtons = $this->edit_Actions($editMode);            
        } else {
            if ($this->doEdit) $editMode = "editContentData";
        }

        if ($showContentButtons) {
            if ($layoutName) {
                echo ("<strong>$layoutName</strong> ");
                // echo ("$this->contentData[type] -'$pageId' ");
                echo ("<a href='admin_cmsLayout.php?editLayout=$layoutName'>Layout</a> ");
            } else {
                $headData = array();
                $headData[id] = "cmsContentEditFrameHead_".$this->contentId;
                $headName = "cmsContentEditFrameHead";

                switch ($this->editMode) {
                    case "onPage" :
                        $active = 1;
                        div_start($headName,$headData);
                        $this->edit_head_onPage($active);

                        div_end($headName);
                        break;

                    case "onPage2" :
                        $active = 1;
                        div_start($headName,$headData);
                        $this->edit_head_onPageNew($active);
                        div_end($headName);
                        break;

                }
            }
        }

        $res = 0;
        if ($editMode == "editContentData") {
            $res = $this->edit_show_input();
        }

        div_end($divName);
        return $res;
    }

    function edit_show_input() {
         div_start("cmsContentEditFrameContent");

         $showInput = 1;
         $this->editContent = $this->contentData;
         
         
         $res = $this->edit_post_Action();
         if ($res === "hide") $showInput = 0;
         if (is_array($res)) {
            echo ("ACTION Res = $res inputShow = $showInput <br>");
         }
         
         
         if ($showInput) {
            // get ButtonStr
            $buttonStr = $this->edit_show_buttons();
             
            // get List for Input
            $this->edit_getInputList();
         
            // Start FORM
            echo ($this->edit_show_form_start());

            // SHOW TABS
            $this->edit_show_tabs();

            // SHOW TAB FRAMES
            $this->edit_show_tab_input();

            // SHOW BUTTONS
            echo ($buttonStr);
            
            // END OF FRAME
            echo ($this->edit_show_form_end());

            // $editRes = cms_content_edit($this->contentData,$this->frameWidth);

            $tempContent = $editRes[tempContent];
            $out = $editRes[outPut];
            if (!$newMode) echo ($out);
         }
         
         div_end("cmsContentEditFrameContent");
         if (is_array($tempContent)) echo ("TempContent get in head<br />");
         return $tempCountent;
    }

    function edit_show_tabs() {
        // TABLISTE ////////////////////////////////////////////////////////////////
        $out .= div_start_str("cmsEditTabLine",$divData);
        foreach ($this->inputList as $key => $value) {
            $show = 1;
            $showName = $key;
            if (is_array($value)) {
                if ($value[showName]) {
                    $showName = $value[showName];
                    // echo ("showName for $key = $showName $value<br>");
                    unset($inputList[$key][showName]);
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
                if ($key == $this->selectEdit) $divName .= " _selected";
            } else {
                if ($key == $this->selectEdit) $divName .= " cmsEditTab_selected";
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
                    if ($this->showMode == "Simple") {
                        $divName .= " editMode_hidden";
                    }
                    break;
                case "Admin" :
                    if ($this->showMode == "Simple" OR $this->showMode == "More") {
                        $divName .= " editMode_hidden";
                    }
                    break;

            }


            $divData = array(); //"editName"=>$key);
            $divData[id] = "cmsEditTab_".$key;
            $out .= div_start_str($divName,$divData);
            $out .= $showName;
            $out .= div_end_str($divName);
        }
        $out .= "<input type='hidden' class='cmsEditTabName' name='selectedTab' value='$this->selectEdit' style='width:50px;height:12px;font-size:10px' >";

        $out .= div_end_str("cmsEditTabLine","before");

        echo ($out);
        return $inputList;

    }

    function edit_show_tab_input() {

        $editMode = $_SESSION[editMode];
        $border = 1;
        $padding = 3;
        $leftWidth = 200;

        $rightWidth = $this->frameWidth - $leftWidth - (2 * ($padding + $border));

        foreach ($this->inputList as $key => $value) {
            $divName = "cmsEditFrame cmsEditFrame_$key";
            if ($key != $this->selectEdit) $divName .= " cmsEditFrameHidden";
            $divData = array(); //"editName"=>$key);
            $divData[id] = "cmsEditTabFrame_".$key;
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


       //  $out = str_replace("editContent[", "editContent[".$this->editContent[id]."][", $out);

        
        echo ($out);
    }

    function edit_show_buttons() {
        $out = div_start_str("cmsEditFrameButtons");
        $out .= "<input type='submit' class='cmsInputButton' name='editContentSave' value='".$this->lga("content","saveButton")."' />";
        $out .= "<input type='submit' class='cmsInputButton' name='editContentSaveClose' value='".$this->lga("content","saveCloseButton")."' />";
        $out .= "<input type='submit' class='cmsInputButton cmsSecond' name='editContentCancel' value='".$this->lga("content","cancelButton")."' />";
        $out .= div_end_str("cmsEditFrameButtons","before");
        return $out;
    }
    
    function edit_show_form_start() {
        $out = "<form method='post' >";
        return $out;
    }
    
    function edit_show_form_end() {
        $out = "</form>";
        return $out;
    }

    
    function edit_getInputList() {
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
        $settings = $this->editContent_settings();
        $tabInput[settings] = $settings;

        // FRAME SETTINGS 
        if ($showFrameSettings) {
            $frameSettings = $this->editContent_frameSettings(); 
            $tabInput["frame"] = $frameSettings;
        }


        // FRAME TEXT
        if ($showFrametext) {
             $tabInput["frameText"] = $this->editContent_frameText($this->editContent,$this->frameWidth,$frameText);
        }

        // SYSTEM FRAME
        if ($this->editContent[frameStyle] == "systemFrame") {
            $systemFrame = $this->editContent_systemFrame($this->editContent,$this->frameWidth,$frameText);
            if (is_array($systemFrame)) {
                $tabInput["systemFrame"] = $systemFrame;
            }
        }


        $special_edit = cmsFrame_getSpecial_edit($frameStyle, $this->editContent,$this->frameWidth);
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
            $wireFrameSettings = $this->editContent_wireframeSettings($this->editContent,$this->frameWidth,$editText);
            if (is_array($wireFrameSettings)) {
                $tabInput[wireframe] = $wireFrameSettings;            
            }
        }

        $selectEdit = $type;

        $editData = $this->edit_myInputData();
        if (is_array($editData)) {

            $addInput = $editData[addInput];
            $tabInputAdd = $editData[tabInput];
            $showContentName = $editData[showContentName];
            $selectEdit = $editData[selectEdit];
                // show_array($subDataRes);
            foreach ($tabInputAdd as $key => $value ) {
                if (is_string($key)) {
                    if ($value == "hideTab") {
                        unset ($tabInput[$key]);
                    } else {
                        $tabInput[$key] = $value;
                    }
                } else {
                    // echo ("<b>$key ist not a String ($value) </b>");
                }
            }
        }
        

        if ($_SESSION[showLevel] == 9) {
            $res = $this->editContent_Data();
            if (is_array($res)) $tabInput[data] = $res;
            
            $res = $this->editContent_Language();
            if (is_array($res)) $tabInput[languages] = $res;            
        }
        $this->inputList = $tabInput;
        return $tabInput;
        
    }
    
    function editContent_Data() {
        $showData = array();

        $showData[showName] = $this->lga("content","tabData");
        $showData[showTab] = "Admin";
        
        $addData = array();
        
        
        $addData[text] = "Daten";
        $addData[input] = "Trulla";
        $div = array();
        $div[divname] = "cmsDataList";
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        $outData = "";
        foreach ($this->editContent[data] as $key => $value ) {
            $outData .= "$key = '$value' <br />";
        }
        $div[content] = $outData;

        $addData["div"] = $div;

        $showData[] = $addData;
        return $showData;
        
    }
        
    function editContent_Language() {
        $showData = array();
        
        $showData[showName] = $this->lga("content","tabLanguages");
        $showData[showTab] = "Admin";

        $addData = array();
        $addData[text] = "Languages";
        $addData[input] = "Trulla";
        $div = array();
        $div[style] = "width:100%;background-color:#fff;visible:visible;overflow:visible;";
        $outData = "";
        $outData = $this->text_editDb();
        $div[content] = $outData;

        $addData["div"] = $div;

        $showData[] = $addData;
        return $showData;
    }

    function edit_myInputData() {
        $tabInput = array();



        if (method_exists ($this ,"contentType_editContent")) {
            $addInput = $this->contentType_editContent();

        } else {
            echo ("<h1>function contentType_editContent not exist for $this->contentName ($this->contentType)</h1>");
            $addInput = array();
        }

        if (is_array($addInput)) {
            foreach($addInput as $key => $value) {
                 if (is_integer($key)) {
                    //echo ("addInput $key = $value <br />");
                    if (!is_array($tabInput[$this->contentType])) $tabInput[$this->contentType] = array();
                    $tabInput[$this->contentType][] = $value;
                } else {


                    if (!is_array($value)) {
                        switch ($key) {
                            case "showName" : $tabInput[$key] = $value; break;
                            case "showTab"  : $tabInput[$key] = $value; break;
                            default :
                                if ($value == "hideTab") {
                                    // echo ("DONT SHOW TAB $key <br>");
                                    $tabInput[$key] = $value;
                                } else {
                                    echo ("$key is not value $value <br> ");
                                }
                        }

                    } else {
                    //echo ("addInput is Asso-Array $key = $value <br />");
                    // show_array($value);
                    foreach($value as $key2 => $value2) {
                        if (is_integer($key2)) {
                            // echo ("addInput is Integer $key2 = $value2 to target $key <br />");
                            if (!is_array($tabInput[$this->contentType][$key])) $tabInput[$this->contentType][$key] = array();
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

    function getGoPage($add) {
        $goPage = $this->goPage;
        // echo ("$add - $add[0] ");
        if ($add[0]=="&") {
            $add[0]="?"; 
            /// echo ("newAdd = $add <br>");
        }        
        $goPage .= $add;
        return $goPage;
    }
        
        
        
    function edit_post_Action() {
        global $pageInfo,$pageData;

        $pageId = $contentData[pageId];
        $editId = $contentData[id];

        /// ABBRECHEN
        $cancel = $_POST[editContentCancel];
        if ($cancel) {
            return $this->edit_post_cancel();    
            $this->tempContent = 0;
        }
        /// ende of ABBRECHEN
        
        /// SPEICHERN
        $saveClose = $_POST[editContentSaveClose];
        $save = $_POST[editContentSave];
        if ($saveClose OR $save) {
            $this->tempContent = 0;
            return $this->edit_post_save($saveClose);
        }
        
        
        $editText = $_POST[editText];
        if (is_array($editContent)) {
            $res = $this->text_saveNew($editText,0);
            $this->tempContent = 1;
        }
        
        $editContent = $_POST[editContent];
        if (is_array($editContent)) {
            $doSave = 0;
            $res = $this->content_save($editContent,0);
            $this->tempContent = 1;
        }
        
       
        
    }
    
    function edit_post_cancel() {
        cms_infoBox("Editieren abgebrochen ");
       
        $add = $this->addPage;
        $add .= "#inh_".$this->contentId;
        $goPage = $this->getGoPage($add);
        
        $doReload = 1;
        if ($doReload) reloadPage($goPage,1);
        else echo ("<a href='$goPage' class='cmsLinkButton' >Reload</a>");
        
        // 
        // return array("outPut"=>$out,"tempContent"=>$contentData);
        return "hide";
    }
    
    function edit_post_save($close) {
        
        $reload = 1;
        $doSave = 1;
        
        // Save Text
        $editText = $_POST[editText];
        if (is_array($editText)) {
            
            $textSaveResult = $this->text_saveNew($editText,$doSave);
            $textError = $textSaveResult[error];
            $textChange = $textSaveResult[change]; 
            $textOut = $textSaveResult[out];
            if ($textError) {
                $out = "Fehler beim Text speichern '$textError'";
                if ($textOut) $out .= "<br />".$textOut;
                cms_errorBox($out);              
                $reload = 0;
            }            
        }
        
        // SAVE DefaultText
        $save_textDb_result = $this->text_default_save();
        $error = $save_textDb_result[error];
        $out   = $save_textDb_result[out];
        $check = $save_textDb_result[check];
        if ($error) {
            $reload = 0;
            cms_errorBox($out);
        }


        // UPDATE OTHER CONTENT
        $updateOtherContent = $_POST[updateOtherContent];
        if (is_array($updateOtherContent)) {
            $errorOther = $this->updateOtherContent($updateOtherContent);
            if ($errorOther) {
                $reload = 0;
                
            }
        }


        // Save Content
        $editContent = $_POST[editContent];
        if (is_array($editContent)) {
            
            $contentSaveResult = $this->content_save($editContent,$doSave);
            
            $contentError  = $contentSaveResult[error];
            $contentChange = $contentSaveResult[change]; 
            $contentOut    = $contentSaveResult[out];
            $contentValue  = $contentSaveResult[value];
            if ($contentError) {
                $out = "Fehler beim Inhalt speichern '$contentError'";
                if ($contentOut) $out .= "<br />".$contentOut;
                cms_errorBox($out);              
                $reload = 0;
            } else {
                if (!$contentOut) {
                    $contentOut = "Inhalt gespeichert - $contentChange Veränderungen ";
                }
            }      
        }
        
        if ($reload) {
            $add = $this->addPage;
            $waitTime = 20;
            if ($close) {
                $waitTime = 1;
                cms_infoBox("Inhalt gespeichert");
                $returnValue = "hide";                
            } else {
                // stay Open
                $add .= "&editMode=editContentData&editId=".$this->contentId;
                $ok = $_GET[ok] + 1;
                $add .= "&ok=$ok";
                $waitTime = 2;
                cms_infoBox("Inhalt gespeichert");
                $returnValue = 1;
            }          
            
            $add .= "#editFrame_".$this->contentId;
            $goPage = $this->getGoPage($add);
            
            $doReload = 1;
            if ($doReload) {
                reloadPage($goPage,$waitTime);
            } else {
                echo ("<a href='$goPage' class='cmsLinkButton' >Reload</a>");
            }
            return $returnValue;
        }
    }
    
    function content_save($editContent,$doSave=0) {
        $contentError = 0;
        $contentChange = 0;
        $contentOut = "";
        $contentValue =  $editContent;
     
        $updateData = 1;
        
        $saveData = array();
        foreach ($editContent as $key => $value) {
            switch($key) {
                case "layout" : break;
                case "oldType" : break;
                

                default :
                    if ($value != $this->contentData[$key] ) {
                        // echo ("CHANGE $key from ".$this->contentData[$key]." to $value <br />");
                        $saveData[$key] = $value;
                        if ($updateData) {
                            // $this->contentData[$key] = $value;
                            $this->editContent[$key] = $value;                            
                        }
                    }
            }
        }
        
        if (!$doSave) {
            if ($updateData) {
                $this->contentData = $this->editContent;
            }
            return 0;
        }
        
        
        if ($editContent[oldType]) {
            $oldType = $editContent[oldType];
            if ($updateData) {
                // unset($this->contentData[oldType]);
                unset($this->editContent[oldType]);
            }
            $saveData[type] = $editContent[type];
        }
        
        
        if (count($saveData) > 0) {
            $contentChange = count($saveData);
            
            foreach ($saveData as $key => $value ) {
                // echo ("CHANGE $key = $value <br>");
            }
            // ADD ID AND TYPE
            
            $saveData[type] = $editContent[type];
            $saveData[id]   = $editContent[id];
            
            
            
            $doChange = 1;
            if ($doChange) $saveResult = cms_content_save($editContent[id],$saveData,$this->contentData);
            else $saveResult = "deactivated";            
            if ($saveResult == 1) {
                // cms_infoBox("Content gespeichert '$pageId' ");
            } else {
                $contentError++;
                $contentOut = "Fehler beim Content Speichern '$saveResult' ";
            } 
        } else { // keine Veränderung
            $saveResult = 1;
            
            if ($textChange == 0) {
                $contentOut = "Keine Veränderung";
            }
        }
        if ($updateData) {
            $this->contentData = $this->editContent;
        }
        $contentSaveResult = array("error"=>$contentError,"change"=>$contentChange,"out"=>$contentOut,"value"=>$contentValue);
        return $contentSaveResult;
    }


    function updateOtherContent($updateOtherContent) {
        if (!is_array($updateOtherContent)) return 0;
        $error = 0;
        foreach ($updateOtherContent as $otherId => $otherValue) {
            // echo ("Update $otherId = $otherValue <br>");
            $save = array();
            $save[id] = $otherId;
            foreach ($otherValue as $key => $value) {

                if (is_array($value)) {
                    $hasCont = 0;
                    foreach ($value as $k => $v) {
                        if ($v) $hasCont++;
                    }
                    if ($hasCont) {
                        $value = array2Str($value);
                        $save[$key] = $value;
                    }
                    continue;
                } 
                $save[$key] = $value;
                // echo ("--> $key = $value <br>");
            }
            if (count($save)>1 AND $otherId) {
                $res = cms_content_update($save,"notCompare");
                // echo ("Save $otherId Save Result = $res<br>");
                if (!$res) $error++;
            }
        }
        return $error;

    }

    function edit_Actions($editMode) {
        $showContentButtons = 1;

        switch ($editMode) {
            case "editContentData" :
                if ($this->doEdit) {
                    //$tempContent = cms_content_edit($this->contentData,$this->frameWidth);
                    // if (is_array($tempContent)) echo ("TempContent get in head<br />");
                    $showContentButtons = 1;
                }
                break;

            case "deleteContent" :
                if ($this->doEdit) {
                    $del = $_GET[del];
                    if ($del) {
                       
                        $add = $this->addPage;
                        
                        if ($del == "YES") {
                            $res = cms_content_delete($this->contentId);
                            if ($res == 1) {
                                
                                cms_infoBox("Content gelöscht !! ");
                                reloadPage($goPage,2);
                                $showContentButtons = 0;
                                // return "delete";
                            } else {
                                cms_errorBox("Content nicht gelöscht !!");
                            }
                        }
                        if ($del == "NO") {
                            $add .= "#editFrame_".$this->contentId;
                            $goPage = $this->getGoPage($add);
                            cms_infoBox("Löschen abgebrochen");
//                            $goPage .= "#editFrame_".$this->contentId;
                            reloadPage($goPage,2);
                            $showContentButtons = 0;
                        }

                    } else {
                        echo ("<br />Wollen Sie diesen Inhalt wirklich löschen?<br />");
                        $goPage = "$pageInfo[page]?editMode=deleteContent&editId=$this->contentId";
                        if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];

                        echo ("<a href='$goPage&del=YES#editFrame_".$this->contentId."'>JA</a> ");
                        echo ("<a href='$goPage&del=NO#editFrame_".$this->contentId."'>NEIN</a> ");
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
        return $showContentButtons;
    }


    function edit_head_onPageNew($active=0) {
        // Content is in Edit
        if ($this->doEdit) {
            if ($active) {
                echo ("<div class='cmsContentFrame_editButton cmsContentFrame_editButton_showFrame' style='display:inline-block;'>");
                echo ("<img src='/cms_base/cmsImages/cmsEdit.png' border='0px'>");
                echo ("</div>");

                // edit verschieben
                echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

                // edit Löschen
                $goPage = $this->goPage;
                $goPage .= "?editMode=deleteContent&editId=$this->contentId;#editFrame_$this->contentId";
                // echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsDelete.png' border='0px'></a>");

                echo ("Typ: <b>$this->contentName</b> id:$this->contentId");
            }
            return 0;
        }

        
        $javaEdit = 0;

        $style = "width:auto;";
        if ($this->doEdit) {
            $style = "width:100%;opacity:100%;position:relativ;";
        }

        $addEditClass = "cmsEditToggle";
        if (!$this->edit) $addEditClass .= " cmsEditHidden";

        if ($this->contentType == "navi") $style.="z-index:1001;";
        echo ("<div class='cmsEditBox $addEditClass' style='$style'>");


//        
        $add = $this->addPage;
        $add = "";
        if ($_GET[editLayout]) $add .= "&editLayout=".$_GET[editLayout];
        $add .= "&editMode=editContentData&editId=".$this->contentId;
        $add .= "#editFrame_".$this->contentId;
        $goPage = $this->getGoPage($add);
   
        // edit Edit
        $editClass = "cmsContentFrame_editButton cmsContentFrame_editButton_showFrame";
        if ($javaEdit) $editClass .= " cmsContentFrame_editJavaButton";
        echo ("<div class='$editClass' id='editButtonID_$this->contentId' style='display:inline-block;'>");
        if (!$javaEdit) echo ("<a href='$goPage'>");
        echo ("<img src='/cms_base/cmsImages/cmsEdit.png' border='0px'>");
        if (!$javeEdit) echo ("</a>");
        echo ("</div>");

        // edit verschieben
        echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

        // edit Löschen
        echo ("<div class='cmsContentFrame_deleteButton' id='deleteContent_$this->contentId' >");
        echo ("<img src='/cms_base/cmsImages/cmsDelete.png' border='0px'>");
        echo ("</div>");

        echo ("<div class='cmsContentFrame_deleteAction cmsContentFrame_deleteAction_hidden deleteContent_$this->contentId'> ");
        echo ("Löschen:");
        $add = "";
        if ($_GET[editLayout]) $add .= "editLayout=".$_GET[editLayout];
        $goPage = cms_page_goPage($add);
        // echo ($goPage);
        echo ("<form action='$goPage' method='post' class='cmsInlineForm' >");
        echo ("<input type='submit' class='cmsSmallButton' name='deleteContent_$this->contentId' value='JA' />");

        echo ("<input type='submit' class='cmsSmallButton cmsSecond' name='deleteCancel' value='NEIN' />");
        echo ("</form>");
        // echo ("<a href='#' class='cmsSmallButton cmsSecond cmsContentFrame_deleteCancel'>NEIN</a> ");
        echo ("</div>");


        if ($this->doEdit) {
            echo ("Typ: $this->contentName ($this->contentType] id:$this->conentId");
        }
       
        $editDivData = array();
        $editDivData["id"] = "editContent_".$this->contentId;
        $editDivData["style"] = "width:".($this->frameWidth-2)."px";
        div_start("cmsContentEditFrameContent cmsContentEditFrameContent_hidden",$editDivData);
        // $editRes = cms_content_edit($this->contentData,$this->frameWidth);
        div_end("cmsContentEditFrameContent cmsContentEditFrameContent_hidden");

        echo ("</div>");

        $exit = 1;
        // if ($this->doEdit) $exit = ;
        return $exit;

    }

    function edit_head_onPage() {
        // Content is in Edit
        if ($this->doEdit) {

            //                        if ($GLOBALS[userLevel] > 8) {
//                            echo ("$this->contentName ($this->contentType) -'$pageId' $this->contentId ");
//                        } else {
//                            echo ("Inhalt vom Typ <b> $this->contentName</b> &nbsp; ");
//                        }
//
//                        if ($_GET[layerNr] OR $_GET[flipLayerNr]) {
//                            echo ("layerNr = $_GET[layerNr] > ");
//                            foreach ($_GET as $key => $value ) echo (" $key='$value - ");
//                        }
//
//
//                        // verschieben
//                        echo("<div class='cmsContentHeadButton dragButton'>verschieben</div>");
//
//                        // editieren
//                        $goPage = "$pageInfo[page]?editMode=editContentData&editId=$this->contentId";
//                        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
//                        if ($_GET[layerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[layerNr];
//                        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
//                        echo ("<a href='$goPage' class='cmsContentHeadButton' >edit</a>");
//
//                        // löschen
//                        $goPage = $pageInfo[page]."?editMode=deleteContent&editId=$this->contentId";
//                        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
//                        if ($_GET[layerNr]) $goPage .= "deleteLayer=".$_GET[layerNr];
//                        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
//                        $goPage .= "#editFrame_".$this->contentId;
//                        echo ("<a href='$goPage' class='cmsContentHeadButton' >löschen</a>");
//
//                        // Content Up
//                        $goPage = $pageInfo[page]."?contentUp=$this->contentId";
//                        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
//                        if ($_GET[layerNr]) $goPage .= "editLayer=".$_GET[layerNr];
//                        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
//                        $goPage .= "#editFrame_".$this->contentId;
//                        echo ("<a href='$goPage' class='cmsContentHeadButton' >&#8593;</a>");
//
//                        // Content Down
//                         $goPage = $pageInfo[page]."?contentDown=$this->contentId";
//                        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
//                        if ($_GET[layerNr]) $goPage .= "editLayer=".$_GET[layerNr];
//                        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
//                        $goPage .= "#editFrame_".$this->contentId;
//                        echo ("<a href='$goPage' class='cmsContentHeadButton' >&#8595;</a>");


            $divData = array();
            $divData[style] = "width:".($this->frameWidth+20)."px;left:-5px;";
            $divData[headId] = "head_".$this->contentId;
            $divData[id]  = "editFrame_".$this->contentId;

            div_start("cmsEditLine cmsEditLineOpen",$divData);

            div_start("cmsEditLineLine","width:".($this->frameWidth-10)."px;");
            div_end("cmsEditLineLine");

            $divData = array();
            $divData[style] = "left:0px;top:0px;"; //.($this->frameWidth-10)."px";
            $divData[headId] = "head_".$this->contentId;
            div_start("cmsEditLineBox",$divData);
            $goPage = $pageInfo[page];
            echo("<a href='$goPage'>X</a>");
            div_end("cmsEditLineBox");
            div_end("cmsEditLine cmsEditLineOpen","before");
            return 0;
        }


        $divData = array();
        $divData[style] = "width:".($this->frameWidth+20)."px";
        $divData[headId] = "head_".$this->contentId;
        div_start("cmsEditLine",$divData);
        div_start("cmsEditLineLine","width:".($this->frameWidth-10)."px;");
        div_end("cmsEditLineLine");

        $divData = array();
        $divData[style] = "left:0px;top:0px;"; //.($this->frameWidth-10)."px";
        $divData[headId] = "head_".$this->contentId;

        $goPage = $pageInfo[page];
        $goPage .= "?editMode=editContentData&editId=".$this->contentId;
        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
        if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
        $goPage .= "#editFrame_".$this->contentId;


        echo("<a href='$goPage'>");
        div_start("cmsEditLineBox",$divData);
        echo ("E");
        div_end("cmsEditLineBox");
        echo("</a>");
        div_end("cmsEditLine","before");

        return 1;
    }


    function edit_head_Sitebar() {
        // Content is in Edit
        if ($this->doEdit) return 1;

        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";
        echo ("<div class='cmsEditBox $addEditClass'  >");
        $goPage = $pageInfo[page];
        $goPage .= "?editMode=editContentData&editId=".$this->contentId;
        if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
        if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
        if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
        $goPage .= "#editFrame_".$this->contentId;

        // Roll Image
        // echo ("<img src='/cms_base/cmsImages/cmsEditClose.png' border='0px'>");

        // edit Edit
        echo ("<div class='cmsContentFrame_editButton' id='editButtonID_$this->contentId' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsEdit.png' border='0px'></div>");

        // edit verschieben
        echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

        // edit Löschen
        echo ("<div class='cmsContentFrame_deleteButton' style='display:inline-block;'>");
        $goPage = $pageInfo[page];
        $goPage .= "?editMode=deleteContent&editId=$this->contentId;#editFrame_$this->contentId";
        echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsDelete.png' border='0px'></a>");
        echo ("</div>");
        // edit Cut
        // echo (" <a href='$goPage'><img src='/cms_base/cmsImages/cmsCut.png' border='0px'></a>");


        echo ("</div>");

        return 1;
    }



    ////////////////////////////////////////////////////////////////////////////
    // CONTENT EDIT                                                           //
    ////////////////////////////////////////////////////////////////////////////

    function editContent_settings() {
        $frameText = $this->textData;
        $showType = 1;
        $showLevel = 1;
        $showToLevel = 1;
        $showContentName = 1;


        $settings = array();
        $settings[showName] = $this->lga("content","tabSettings"); //"Einstellungen";
        $settings[showTab] = "More";

        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_settingsIdText",":");//ContentId";
        $addData["input"] = "<input type='text' readonly='readonly' name='editContent[id]' value='".$this->editContent[id]."' />";
        $addData["mode"] = "Admin";
        $settings[] = $addData;


        if ($showType) {    // Typ
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsTypeText",":");//"Content-Typ:";
            if ($layoutName OR $_GET[editLayout]) {
                 $addData["input"] = cms_contentLayout_selectType($this->editContent[type],"editContent[type]","button");
            } else {
                $showData = array("viewMode"=>"select","width"=>190,"id"=>"selectContentType");
                $input = $this->editContent_SelectSettings("contentType",$this->contentType,"editContent[type]",$showData);

                $addData["input"] = $input; //cms_content_SelectType($this->editContent[type],"editContent[type]","button");
            }
            $addData["mode"] = "More";
            $settings[] = $addData;
        }

        if ($showLevel) { // UserLevel
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsLevelText",":");//"Anzeigen ab";
            $addData["input"] = cms_user_selectLevel($this->editContent[showLevel],$_SESSION[userLevel],"editContent[showLevel]");
            $addData["mode"] = "More";
            $settings[] = $addData;

            // echo ("HIER $this->editContent[showLevel] <br>");
            if ($this->editContent[showLevel] == 3) {
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
            $addData["input"] = cms_user_selectLevel($this->editContent[toLevel],$_SESSION[userLevel],"editContent[toLevel]");
            $addData["mode"] = "More";
            $settings[] = $addData;
        }

        // $contentType_class = cms_contentTypes_class();
        $special_viewFilter = $this->use_special_viewFilter($this->editContent);
        if (is_array($special_viewFilter)) {
            foreach($special_viewFilter as $key => $value) {
                $settings[] = $value;
            }
        }

        if ($showContentName) { // ContentName
            $addData = array();
            $addData["text"] = $this->lga("content","frameText_settingsContentNameText",":");"Inhalt verfügbar unter";
            $addData["input"] = "<input type='text' value='".$this->editContent[contentName]."' style='min-width:196px;' name='editContent[contentName]'>";
            $addData["mode"] = "Admin";
            $settings[] = $addData;
        }
        return $settings;
    }

    function editContent_frameSettings() {
        // $this->frameWidth = $this->frameWidth;
        $frameText = $this->textData;

        $frameSettings = array();

        $frameSettings[showName] = $this->lga("content","tabFrame");
        $frameSettings[showTab] = "Simple";

        
        
        
        // Rahmen Titel
        $showData=array();
        $showData[title] = $this->lga("content","frameText_frameTitleText",":");
        $addData = $this->editContent_languageString($this->editContent[title],"editContent[title]",$showData);
        $frameSettings[] = $addData;
        
//        $addData = array();
//        $addData["text"] = $this->lga("content","frameText_frameTitleText",":");
//        $showData = array();
//        $showData["editMode"] = "SimpleLg";
//        $showData["dataSource"] = "editContent";
//        $showData["formName"] = "editContent";
//        $showData["dataName"] = "title";
//        $showData["title"] = $this->lga("content","frameText_frameTitleText",":");
//        
//        $addData["input"] = $this->edit_text($showData);
//        
//        $addData["input"] = "<input type='text' value='".$this->editContent[title]."' style='min-width:196px;' name='editContent[title]'>";
//        $addData["mode"] = "More";
//        $frameSettings[] = $addData;
//
//        $showData = array();
//        $showData["editMode"] = "SimpleLg";
//        $showData["dataSource"] = "editContent";
//        $showData["formName"] = "editContent";
//        $showData["dataName"] = "title";
//        $showData["mode"] = "SimpleLine";
//        $showData["title"] = $this->lga("content","frameText_frameTitleText",":");
//        $frameSettings[] = $this->edit_text($showData);


        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameStyleText",":");//"Rahmen Stil";
        $addData["input"] = cms_content_selectStyle("frameStyle",$this->editContent[frameStyle],"editContent[frameStyle]",array("submit"=>1));
        $addData["mode"] = "Simple";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameCloseAbleText",":");//"Rahmen schließbar";
        $frameClose = $this->editContent[data][frameClose];
        if ($frameClose) $checked = "checked='checked'";
        else $checked = "";
        $input =  "<input type='checkbox' $checked value='1' name='editContent[data][frameClose]' />";

        if ($frameClose) {
            $frameCloseLoad = $this->editContent[data][frameCloseLoad];
            if ($frameCloseLoad) $checked = "checked='checked'";
            else $checked = "";
            $input .= $this->lga("content","frameText_frameClosedText",":")."<input type='checkbox' $checked value='1' name='editContent[data][frameCloseLoad]' />";
            $input .= $this->lga("content","frameText_frameCloseTitleText",":");
            // $input .= "<input type='text' value='".$this->editContent[data][frameCloseText]."' name='editContent[data][frameCloseText]' />";

            $showData = array();
            $showData[formName] = "editContent[data]";
            $showData[dataSource] = "content";
            $showData[editMode] = "SimpleLg"; // array("simple","language","textDb")[0];


            $showData[title] = "Name";
            $showData[dataName] = "frameCloseText";
            $showData[text] = $this->editContent[data][frameCloseText];
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
//        $addData["input"] = cms_content_selectStyle("float",$this->editContent[frameFloat],"editContent[frameFloat]");
//        $frameSettings[] = $addData;

        
        $linkShowData = array("linkType"=>1);
        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameLinkText",":");// "Rahmen Link";
        $input = "";cms_page_SelectMainPage($this->editContent[frameLink], "editContent[frameLink]");
        $input .= $this->editContent_selectSettings("link",$this->editContent[frameLink], "editContent[frameLink]",$linkShowData);
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameWidthText",":");//"Rahmen-Breite";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameWidth]' value='".$this->editContent[frameWidth]."' >";
        $addData["mode"] = "Admin";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("content","frameText_frameHeightText",":");//"Rahmen-Höhe";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameHeight]' value='".$this->editContent[frameHeight]."' >";
        $addData["mode"] = "Admin";
        $frameSettings[] = $addData;


        $addData = array();
        $addData["text"]  = $this->lga("content","frameText_frameAbs",":");//"Rahmen-Höhe";
        $input = "";
        $input .= $this->lga("content","frameText_frameAbs_left",":")."<input type='text' style='width:50px;' name='editContent[data][frameAbsLeft]' value='".$this->editContent[data][frameAbsLeft]."' > ";
        $input .= $this->lga("content","frameText_frameAbs_right",":")."<input type='text' style='width:50px;' name='editContent[data][frameAbsRight]' value='".$this->editContent[data][frameAbsRight]."' > ";
        $input .= $this->lga("content","frameText_frameAbs_top",":")."<input type='text' style='width:50px;' name='editContent[data][frameAbsTop]' value='".$this->editContent[data][frameAbsTop]."' > ";
        $input .= $this->lga("content","frameText_frameAbs_bottom",":")."<input type='text' style='width:50px;' name='editContent[data][frameAbsBottom]' value='".$this->editContent[data][frameAbsBottom]."' > ";
        $addData["input"] = $input;
        $addData["mode"]  = "Admin";
        $frameSettings[]  = $addData;
        
        
        $frameStyle = $this->editContent[frameStyle];
        return $frameSettings;
    }


    function editContent_frameText() {
        $this->editContent = $this->editContent;
        $this->frameWidth = $this->frameWidth;
        $frameText = $this->frameText;

        $data = $this->editContent[data];

        if (!is_array($data)) $data = array();

        $id = $this->editContent[id];
        $pageId = $this->editContent[pageId];
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
        $addData["input"] =  "<input type='hidden'  name='textId' value='".$this->editContent[id]."' >";
        $res[] = $addData;

        $showData = array();
        $showData[css] = 1;
        $showData[view] = "text";
        $showData[color] = 0;
        $showData[width] = $this->frameWidth;
        $showData[name] = $this->lga("content","frameText_headlineText");
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $this->editContent_text("frameHeadline",$editText[frameHeadline], $showData);
        $res[] = $addData;


        $showData = array();
        $showData[css] = 1;
        $showData[view] = "textarea";
        $showData[color] = 0;
        $showData[width] = $this->frameWidth;
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
        $showData[width] = $this->frameWidth;
        $showData[height] = 30;
        $showData[name] = $this->lga("content","frameText_bottomText");"Rahmen-Text Unten";
        $showData[lgSelect] = 1;
        $showDara[mode] = "More";
        $addData = $this->editContent_text("frameSubtext",$editText[frameSubtext], $showData);
        $res[] = $addData;

        return $res;
    }

    function editContent_systemFrame() {
        $this->editContent = $this->editContent;
        $this->frameWidth = $this->frameWidth;
        $frameText = $this->frameText;

        $editList = cmsSystemFrame_editList($this->editContent);
        return $editList;
    }


    function editContent_wireframeSettings() {
        $this->editContent = $this->editContent;
        $this->frameWidth = $this->frameWidth;
        $frameText = $this->frameText;

        $wireFrame_enabled = cmsWireframe_enabled();
        if (!$wireFrame_enabled) return 0;

        $wireFrameSettings = cmsWireframe_editContent($this->editContent,$this->frameWidth,$editText,$this);
        return $wireFrameSettings;
    }
    
    function show_editFrame_endData() {
        if ($_SESSION[showLevel] == 9) {
            
            $area = "editText";
            $data = $this->edit_textDb;
            $hidden = 1;
            $mainDiv = 0;
            $out = $this->text_editDb_showArea($area,$data,$hidden,$mainDiv);
            if ($out) {
                div_start("cmsEditText_help cmsEditText_help_$area");
                echo ($out);
                div_end("cmsEditText_help cmsEditText_help_$area");
            }
            
           
        }
    }
}
?>