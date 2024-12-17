<?php // charset:UTF-8
class cmsType_faq_base extends cmsClass_content_show {

    function getName (){
        return "FAQ";
    }
    
    function contentType_init(){
        $this->editText = array();
        $add = array("editTextHeadline","editTextSaveButton","editTextCancelButton","editTextNewText");
        for ($i=0;$i<count($add);$i++) {
            $code = $add[$i];
            $lg = $this->lga("contentType_faq",$code);
            $this->editText[$code] = $lg;
        }        
    }
   

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
     // function faq_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();
        
        
        
        
        $newData = array();
        $editData = array();
        $reload = 0;
        
        // GET CATEGORY LIST FOR FAQ
        $catGet = cmsCategory_get(array(name=>"FAQS"));
        $faqId =  $catGet[id];
        $catList = cmsCategory_getList(array("mainCat"=>$faqId),"sort","assoIdList");
        
        // CHECK SHOW AND CLOSED
        foreach ($data as $key => $value) {
            if (substr($key,0,5) == "view_") {
                $catId = substr($key,5);
                // echo ("Echo VIEW $catId => $value <br>");
                if (is_array($catList[$catId])) {
                    $catList[$catId][view] = 1;
                }
            }
            if (substr($key,0,6) == "close_") {
                $catId = substr($key,6);
                // echo ("Echo Close $catId => $value <br>");
                if (is_array($catList[$catId])) {
                    $catList[$catId][close] = 1;
                }
            }
        }
        
        // GET CONTENT for catList
        foreach ($catList as $catId => $cat) {
            if (!$cat[view]) continue;
            
            $close = $cat[close];
            $faqList = cmsFaq_getList(array("cat"=>$catId),"sort");
            $catList[$catId][faqList] = $faqList;
        }
        
        $lgList = array("dt"=>"Deutsch","en"=>"english");
        $this->lgList = $lgList;
        $this->faq_save();
//        if ($_POST) {
//            foreach ($_POST as $key => $value) {
//                 echo ("Save $key = $value <br />");
//                 if (substr($key,0,8)== "faqEdit_") { 
//                    $faqEditId = substr($key,8);
//                    echo ("FAQ EDIT to $faqEditId <br>");
//                    $editData[$faqEditId] = $_POST["faq_edit"][$faqEditId];
//                    if (is_array($editData[$faqEditId])) {
//                        // foreach ($editData[$faqEditId] as $faqKey => $faqText) {
//                        $saveData = array();
//                        $saveData[id] = $faqEditId;
//                        foreach ($lgList as $lgCode => $lgName) {
//                            $saveData["head_".$lgCode] = $editData[$faqEditId]["head_".$lgCode];
//                            $saveData["text_".$lgCode] = $editData[$faqEditId]["text_".$lgCode];
//                        }
////                        $saveData[head_dt] = $editData[$faqEditId][headline];
////                        $saveData[text_dt] = $editData[$faqEditId][text];
//                        // foreach ($saveData as $k => $v) echo ("Save DATA $k = $v <br>");
//                        
//                        $saveRes = cmsFaq_save($saveData);
//                        if ($saveRes) $reload = 1;
//                        //echo ("SAVE FAQ DATA with id = $faqEditId  Result = $saveRes <br>");
//                        //show_array($saveData);                        
//                    }
//                }
//                
//                // NEW FAQ
//                if (substr($key,0,7)== "faqAdd_") { 
//                    $faqNewId = substr($key,7);
//                    echo ("FAQ ADD to $faqNewId form $key '".substr($key,5)."'<br>");
//                    // foreach ($_POST["faq_new"] as $key => $value) echo ("$key => $value <br>");
//                    $newData[$faqNewId] = $_POST["faq_new"][$faqNewId];
//                    // echo ("NEW DATA is $newData / $newData[$faqNewId] <br> ");
//                    
//                    if (is_array($newData[$faqNewId])) {
//                        $saveData[cat] = $faqNewId;
//                        foreach ($lgList as $lgCode => $lgName) {
//                            $saveData["head_".$lgCode] = $newData[$faqNewId]["head_".$lgCode];
//                            $saveData["text_".$lgCode] = $newData[$faqNewId]["text_".$lgCode];
//                        }
////                        $saveData[head_dt] = $newData[$faqNewId][headline];
////                        $saveData[text_dt] = $newData[$faqNewId][text];
//                        foreach ($saveData as $k => $v) echo ("saveData $k => $v <br>");
//                        
//                        $saveRes = cmsFaq_save($saveData);
//                        if ($saveRes) $reload = 0;
//                       
//                    }
//                }
//                
//                if ($key == "faq_sort_save") {
//                    $sortList = $_POST[faqSort];
//                    $error = 0;
//                    foreach ($sortList as  $sortId => $sortValue) {
//                        $sortCatId = $sortValue[catId];
//                        $sortSort = $sortValue[sort];
//                        // echo ("SortItem  $sortId => cat=$sortCatId, sort=$sortSort <br>");
//                        
//                        if ($sortId) {
//                            $saveData = array();
//                            $saveData[id] = $sortId;
//                            $saveData[sort] = $sortSort;
//                            $saveData[cat] = $sortCatId;
//                            $saveRes = cmsFaq_save($saveData);
//                            if (!$saveRes) $error++;
//                        }
//                    }
//                    if ($error) cms_errorBox ("Fehler beim Speichern der Reihenfoge");
//                    else {
//                        $reload = 1;
//                        cms_infoBox("Reihenfolge gespeichert");
//                    }
//                }                
//            }
//        }
//        
//        if ($reload) {
//            reloadPage(cms_page_goPage(),$reload);
//        }
        
        
        // echo ("<h1>EDIT = $this->edit editable = $this->editable pageEditAble = $this->pageEditAble </h1>");
        
        $showNew = 0;
        $edit = 0;
        if ($this->pageEditAble AND $this->userLevel > 6) {
            $showNew = 1;
            $edit = 1;
            echo ("<form method='post' >");
           
            div_start("faq_output faq_output_hidden");
            echo ("Hier würde der Output stehen");
            div_end("faq_output faq_output_hidden");
        }
        
        div_start("faq_list");
        
        $editToggleClass = "cmsEditToggle";
        if (!$this->edit) $editToggleClass .= " cmsEditHidden";
        
        $lg = $this->showLg;
        $defaultLg = $this->pageClass->defaultLg."___";
        $addLg = ($this->showLevel > 3);
        
        // echo ("GET FAY FOr $lg default=$defaultLg addLg = $addLg <br>");
        
        foreach ($catList as $catId => $cat) {
            if (!$cat[view]) continue;
            
            $close = $cat[close];
            
        //for ($i=0;$i<count($catList);$i++) {
          //  $cat = $catList[$i];
            $catName = $cat[name];
            //$catId   = $cat[id];
            
            $faqContainerClass = "faq_mainContainer";
            if ($close) $faqContainerClass .= " faq_mainContainer_close";
            
            div_start($faqContainerClass,array("id"=>"faq_mainContainer_".$catId));
            
            
            $mainTitleDiv = "faq_main";
            if ($close) $mainTitleDiv .= " faq_main_close";
            
            
            div_start($mainTitleDiv,array("id"=>"faq_main_".$catId));
            echo ("$catName");
            div_end($mainTitleDiv);
            
            $mainDivName = "faq_main_content";
            if ($close) $mainDivName .= " faq_main_content_hidden";
            $mainDivName .= " faq_sort";
            div_start($mainDivName,array("id"=>"faq_main_content_".$catId));
            
            
            
            
            
            $faqList = $cat["faqList"]; // cmsFaq_getList(array("cat"=>$catId),"sort");
            for ($f=0;$f<count($faqList);$f++) {
                $faq = $faqList[$f];
            
                $headLine = $faq["head_".$lg];
                if (!$headLine) {                   
                    if ($faq["head_".$defaultLg]) {
                        if ($addLg) $headLine = $defaultLg.":";
                        $headLine .= $faq["head_".$defaultLg];    
                    }
                }
                
                $text = $faq["text_".$lg];
                if (!$text) {
                    if ($faq["text_".$defaultLg]) {
                        if ($addLg) $text = $defaultLg.":";
                        $text .= $faq["text_".$defaultLg];    
                    }                                     
                }
                
                if (!$headLine OR !$text) {
                    foreach ($faq as $key => $value) {
                        $type = "";
                        if (substr($key,0,5) == "head_") $type = "head";
                        if (substr($key,0,5) == "text_") $type = "text";
                        
                        if ($type == "text" AND !$text ) {
                            if ($addLg) {
                                $lgCode = substr($key,5);
                                $text = $lgCode.":";
                            }
                            $text .= $value;
                            //echo ("SET Text to $value <br>");
                        }
                        if ($type == "head" AND !$headLine ) {
                            if ($addLg) {
                                $lgCode = substr($key,5);
                                $headLine = $lgCode.":";
                            }
                            $headLine .= $value;
                            // echo ("SET Headline to $value <br>");
                        }
                    }
                }
                
                $headLine = $this->lgClear($headLine);
                
                
                // $headLine = $faq[head];
                $faqId    = $faq[id];
                // $text     = $faq[text];
                
                
                $closeEdit = $this->faq_edit($faq,$carId);
//            
                
                // headLine
                $mode = "single";
                $divHeadName = "faq_headline";
                if ($mode == "single") $divHeadName = "faq_headline_single ".$divHeadName;
                
                div_start($divHeadName,array("id"=>"faq_".$faqId));
                $headLine = $this->showText($headLine);
                echo ($headLine);
                div_end($divHeadName);
                
                
                
               

                // show Text
                $divNameText = "faq_text faq_text_hidden";
                div_start($divNameText,array("id"=>"faqText_".$faqId));
                $text = $this->showText($text);
                echo ($text);
                div_end($divNameText);
//                if ($this->pageEditAble) {
//                    div_end("faq_item");
//                }
                
                if ($closeEdit) div_end("faq_item");
            }
            
            
            
            
            if ($showNew) {
                $divName = "faq_new_button"; 
                if ($faqNewNr == $catId) $divName .= " faq_new_button_active";
                $divName .= " ".$editToggleClass;
                
                div_start($divName,array("id"=>"faq_new_".$catId));
                echo ($this->editText["editTextNewText"]); //$this->lga("contentType_faq","editTextHeadline")
                div_end($divName);
                
                $divName = "faq_new_input";
                $divName .= " cmsContentEditFrame $editToggleClass";
                if ($faqNewNr != $catId) $divName .= " faq_new_input_hidden";
                
                
                div_start($divName,array("id"=>"faq_new_form_".$catId));
                echo ("<h2>Neues Thema hinzugügen zu $catName</h2>");
                foreach ($lgList as $lgCode => $lgName) {
                    echo ("<span class='cmsFaqEditStr'>Head $lgCode:</span>");
                    echo ("<input class='cmsFaqEditInput' type='text' value='".$newData[$catId]["head_".$lgCode]."' name='faq_new[$catId][head_".$lgCode."]' /> ");
                }


                foreach ($lgList as $lgCode => $lgName) {
                    echo ("<span class='cmsFaqEditStr'>Text $lgCode:</span>");
                    echo ("<textarea class='cmsFaqEditInput' style='height:80px;' name='faq_new[$catId][text_".$lgCode."]'>".$newData[$catId]["text_".$lgCode]."</textarea>");
                }
                
                
               //  echo ("<input style='width:100%;' type='text' value='".$newData[$catId][headline]."' name='faq_new[$catId][headline]' /> ");
                // echo ("<textarea style='width:100%;height:80px;' name='faq_new[$catId][text]'>".$newData[$catId][text]."</textarea>");
                echo ("<input type='submit' class='cmsInputButton' name='faqAdd_$catId' value='".$this->editText["editTextSaveButton"]."' >");               
                echo ("<input type='submit' class='cmsInputButton cmsSecond' name='faqAddCancel_$catId' value='".$this->editText["editTextCancelButton"]."' >");               
                div_end($divName);
            }
            
            
            
            div_end($mainDivName);
            
            div_end($faqContainerClass);
            
            
            
        }
        
        if ($edit) {
             echo ("</form>");
        }
        
        div_end("faq_list");
        // show_array($catList);
        
        
    }
    
    function faq_edit($faq,$catId) {
        if (!$this->pageEditAble) return 0;
        
        $editToggleClass = "cmsEditToggle";
        if (!$this->edit) $editToggleClass .= " cmsEditHidden";
        
        $faqId    = $faq[id];
                    
        div_start("faq_item",array("id"=>"faq_item_".$faqId,"style"=>"position:relative;"));
                    //div_start("faq_editLine");
               
        div_start("cmsEditBox faq_editBox $editToggleClass","width:auto;");
        div_start("faq_edit_button cmsContentFrame_editButton",array("id"=>"faq_edit_".$faqId));
        echo ('<img border="0px" src="/cms_'.$GLOBALS[cmsVersion].'/cmsImages/cmsEdit.png">');
        div_end ("faq_edit_button cmsContentFrame_editButton");

        div_start("faq_move_button ",array("id"=>"faq_move_".$faqId));
        echo ('<img border="0px" src="/cms_'.$GLOBALS[cmsVersion].'/cmsImages/cmsMove.png">');
        div_end ("faq_move_button ");

        div_start("faq_delete_button",array("id"=>"faq_delete_".$faqId));
        echo ('<img border="0px" src="/ cms_'.$GLOBALS[cmsVersion].'/cmsImages/cmsDelete.png">');
        div_end("faq_delete_button");

        div_start("faq_delete_action faq_delete_action_hidden",array("id"=>"faq_deleteFrame_".$faqId));
        echo ('Löschen:');
        echo ("<input class='cmsSmallButton' type='submit' value='JA' name='delete_faq_".$faqId."' />");
        echo ("<input class='cmsSmallButton cmsSecond' type='submit' value='NEIN' name='delete_faq_cancel' />");
        div_end("faq_delete_action faq_delete_action_hidden");

        div_end("cmsEditBox faq_editBox $editToggleClass");
               
                
                
        $divName = "faq_edit_input cmsContentEditFrame $editToggleClass";
        if ($faqEditId != $faqId) $divName .= " faq_edit_input_hidden";
        div_start($divName,array("id"=>"faq_edit_form_".$faqId));
        echo ("<h3>".$this->editText["editTextHeadline"]."</h3>");


        foreach ($this->lgList as $lgCode => $lgName) {
            echo ("<span class='cmsFaqEditStr'>Head $lgCode:</span>");
            echo ("<input class='cmsFaqEditInput' type='text' value='".$faq["head_".$lgCode]."' name='faq_edit[$faqId][head_".$lgCode."]' /> ");
        }


        foreach ($this->lgList as $lgCode => $lgName) {
            echo ("<span class='cmsFaqEditStr'>Text $lgCode:</span>");
            echo ("<textarea class='cmsFaqEditInput' style='height:80px;' name='faq_edit[$faqId][text_".$lgCode."]'>".$faq["text_".$lgCode]."</textarea>");
        }


        // echo ("<input style='width:100%;' type='text' value='".$headLine."' name='faq_edit[$faqId][headline]' /> ");

        echo ("<input type='submit' class='cmsInputButton' name='faqEdit_$faqId' value='".$this->editText["editTextSaveButton"]."' >");
        echo ("<input type='submit' class='cmsInputButton cmsSecond' name='faqCancel_$faqId' value='".$this->editText["editTextCancelButton"]."' >");
        div_end($divName);
        
        return 1;
    }
    
    function faq_save() {
        
        
        if (!$_POST) return 0;
        $lgList = $this->lgList;
        foreach ($_POST as $key => $value) {
                
            if (substr($key,0,8)== "faqEdit_") { 
               $faqEditId = substr($key,8);
               echo ("FAQ EDIT to $faqEditId <br>");
               $editData[$faqEditId] = $_POST["faq_edit"][$faqEditId];
               if (is_array($editData[$faqEditId])) {
                   // foreach ($editData[$faqEditId] as $faqKey => $faqText) {
                   $saveData = array();
                   $saveData[id] = $faqEditId;
                   foreach ($lgList as $lgCode => $lgName) {
                       $saveData["head_".$lgCode] = $editData[$faqEditId]["head_".$lgCode];
                       $saveData["text_".$lgCode] = $editData[$faqEditId]["text_".$lgCode];
                   }
//                        $saveData[head_dt] = $editData[$faqEditId][headline];
//                        $saveData[text_dt] = $editData[$faqEditId][text];
                   // foreach ($saveData as $k => $v) echo ("Save DATA $k = $v <br>");

                   $saveRes = cmsFaq_save($saveData);
                   if ($saveRes) $reload = 1;
                   //echo ("SAVE FAQ DATA with id = $faqEditId  Result = $saveRes <br>");
                   //show_array($saveData);                        
               }
           }
                
            // NEW FAQ
            if (substr($key,0,7)== "faqAdd_") { 
                $faqNewId = substr($key,7);
                echo ("FAQ ADD to $faqNewId form $key '".substr($key,5)."'<br>");
                // foreach ($_POST["faq_new"] as $key => $value) echo ("$key => $value <br>");
                $newData[$faqNewId] = $_POST["faq_new"][$faqNewId];
                // echo ("NEW DATA is $newData / $newData[$faqNewId] <br> ");

                if (is_array($newData[$faqNewId])) {
                    $saveData[cat] = $faqNewId;
                    foreach ($lgList as $lgCode => $lgName) {
                        $saveData["head_".$lgCode] = $newData[$faqNewId]["head_".$lgCode];
                        $saveData["text_".$lgCode] = $newData[$faqNewId]["text_".$lgCode];
                    }
//                        $saveData[head_dt] = $newData[$faqNewId][headline];
//                        $saveData[text_dt] = $newData[$faqNewId][text];
                    foreach ($saveData as $k => $v) echo ("saveData $k => $v <br>");

                    $saveRes = cmsFaq_save($saveData);
                    if ($saveRes) $reload = 0;

                }
            }
                
            if ($key == "faq_sort_save") {
                $sortList = $_POST[faqSort];
                $error = 0;
                foreach ($sortList as  $sortId => $sortValue) {
                    $sortCatId = $sortValue[catId];
                    $sortSort = $sortValue[sort];
                    // echo ("SortItem  $sortId => cat=$sortCatId, sort=$sortSort <br>");

                    if ($sortId) {
                        $saveData = array();
                        $saveData[id] = $sortId;
                        $saveData[sort] = $sortSort;
                        $saveData[cat] = $sortCatId;
                        $saveRes = cmsFaq_save($saveData);
                        if (!$saveRes) $error++;
                    }
                }
                if ($error) cms_errorBox ("Fehler beim Speichern der Reihenfoge");
                else {
                    $reload = 1;
                    cms_infoBox("Reihenfolge gespeichert");
                }
            }                
        
        }
        
        if ($reload) {
            reloadPage(cms_page_goPage(),$reload);
        }
        return 0;
    }
    



    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;
        
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        $res[faq][showName] = $this->lga("content","faqTab");
        $res[faq][showTab] = "Simple";
        
        $catGet = cmsCategory_get(array(name=>"FAQS"));
        
        $addData = array();
        $addData[text] = "Trulla";
        $addData[input] = "catGet ".$catGet;
        $res[faq][] = $addData;
       //  return $res;
        
        
        
        if (!is_array($catGet)) {
            $newCatData = array("name"=>"FAQS","mainCat"=>0,"show"=>1);
            $newCatId = cmsCategory_save($newCatData);
            // echo ("NewCatID = $newCatId <br>");
            // $faqId = $newCatId;
        } else {
            $faqId =  $catGet[id];
        }
        
        // showMainCats
        $catList = cmsCategory_getList(array("mainCat"=>$faqId),"sort","assoIdList");
        // show_array($catList);
        
        $catSave = $_POST[catSave];
        if (is_array($catSave)) {
            foreach ($catSave as $catId => $catValue) {
                if ($catId == "new") {
                    
                    $newCatName = $catValue[name];
                    $newCatSort = $catValue[sort];
                    if ($newCatName) {
                        $updateCat = array();
                        $updateCat[name] = $newCatName;
                        $updateCat[sort] = $newCatSort;
                        $updateCat[mainCat] = $faqId;
                        $updateCat[show] = 1;
                        show_array($updateCat);
                        $newId = cmsCategory_save($updateCat);
                        
                        $catList[$newId] = $updateCat;
                        
                    }                    
                } else {
                   
                    $updateCat = array();
                    if ($catValue[name] != $catList[$catId][name]) $updateCat[name]= $catValue[name];
                    if ($catValue[sort] != $catList[$catId][sort]) $updateCat[sort]= $catValue[sort];
                    
                    if (count($updateCat)) {
                        $updateCat[id] = $catId;
                        $saveRes  = cmsCategory_save($updateCat);
                        if ($saveRes) {
                            $catList[$catId][name] = $catValue[name];
                            $catList[$catId][sort] = $catValue[sort];
                        }
                    }
                }                
            }
        }
        
        $maxSort = 0;
        $i = 1;
        
        $showSort = 1;
        foreach ($catList as $catId => $cat) {
            $catName = $cat[name];
            // $catId  = $cat[id];
            $catSort    = $cat[sort];
            if ($catSort > $maxSort) $maxSort = $catSort;
            $addData = array();
            $addData[text] = $this->lga("contentType_faq","level"," ".$i); //"Hauptebene".$i;
            $i++;
            $input = "<input type='text' value='$catName' name='catSave[".$catId."][name]' />";
            if ($showSort) {
                $input .= " ".$this->lga("contentType_faq","levelSort",":");
                $input .= "<input type='text' value='$catSort' style='width:30px;' name='catSave[".$catId."][sort]' />";
            } else {
                $input .= "<input type='hidden' value='$catSort' style='width:30px;' name='catSave[".$catId."][sort]' />";
            }

            if ($data["view_".$catId]) $checked="checked='checked'";
            else $checked = "";  
            $input .= " ".$this->lga("contentType_faq","levelVisible",":");
            $input .= "<input type='checkbox' value='1' $checked name='editContent[data][view_".$catId."]' />";
            
            if ($data["close_".$catId]) $checked="checked='checked'";
            else $checked = "";
            $input .= " ".$this->lga("contentType_faq","levelClosed",":");
            $input .= "<input type='checkbox' value='1' $checked name='editContent[data][close_".$catId."]' />";
            
            $addData[input] = $input;
            $addData[mode] = "Simple";
            $res[faq][] = $addData;            
        }
        
        $maxSort ++;
        $addData = array();
        $addData[text] = $this->lga("contentType_faq","newLevel"," ".$i);"neue Hauptebene";
        $input = "<input type='text' value='' name='catSave[new][name]' />";
        if ($showSort) {
            $input .= " ".$this->lga("contentType_faq","levelSort",":");
            $input .= "<input type='text' value='$maxSort' style='width:30px;' name='catSave[new][sort]' />";
            // $input .= "<input type='text' value='$catSort' style='width:30px;' name='catSave[".$catId."][sort]' />";
        } else {
            $input .= "<input type='hidden' value='$maxSort' style='width:30px;' name='catSave[new][sort]' />";                
        }
        $addData[input] = $input;
        $addData[mode] = "More";
        $res[faq][] = $addData;    
        
        

//        $mainTab = "faq";
//        // Add ViewMode
//        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
//        if (is_array($viewModeList)) {
//            $addToTab = $mainTab;
//            for ($i=0;$i<count($viewModeList);$i++) {
//                // echo ("Add to $addToTab $viewModeList[$i]<br />");
//                $res[$addToTab][] = $viewModeList[$i];
//            }
//        }
//
//        
//        // ShowList
//        $showList = $this->faqShow_List();
//        $addList = $this->dataBox_editContent($data,$showList);
//       // show_array($addList);
//        $addToTab = "faqShow";
//        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
//        for ($i=0;$i<count($addList);$i++) {
//            // echo ("ADD $i $addList[$i] <br>");
//            $res[$addToTab][] = $addList[$i];
//        }
//        
//        
//        // Add FILTER
//        $filterList = $this->editContent_filterView($editContent,$frameWidth);
//        if (is_array($filterList)) {
//            $addToTab = "filter";
//            for ($i=0;$i<count($filterList);$i++) {
//                // echo ("Add to $addToTab $viewModeList[$i]<br />");
//                $res[$addToTab][] = $filterList[$i];
//            }
//        }
//
//        // ACTION 
//        $addList = $this->action_editContent($data,$showList);
//       // show_array($addList);
//        $addToTab = "action";
//        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
//        for ($i=0;$i<count($addList);$i++) {
//            // echo ("ADD $i $addList[$i] <br>");
//            $res[$addToTab][] = $addList[$i];
//        }
        return $res;
    }
    
    
    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for faqListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["faq"] = "Tabelle";
        $res["slider"] = "Slider";
        $res["single"] = "Hersteller";
            
        return $res;
    }
    
     function dataShow_List() {
        return $this->faqShow_List();
    }
    
    function faqShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        
//        $show[vk] = array("name"=>"Verkauspreis","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[shipping] = array("name"=>"Porto","description"=>"Bezeichnung zeigen","position"=>1);
//        $show[count] = array("name"=>"Anzahl","description"=>"Bezeichnung zeigen","position"=>1);
//        
//        $show[basket] = array("name"=>"Warenkorb","description"=>"Bezeichnung zeigen","position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
    }


}

function cmsType_faq_class() {
    if ($GLOBALS[cmsTypes]["cmsType_faq.php"] == "own") $faqClass = new cmsType_faq();
    else $faqClass = new cmsType_faq_base();
    return $faqClass;
}

function cmsType_faq($contentData,$frameWidth) {
    
    $faqClass = cmsType_faq_class();
    $res = $faqClass->show($contentData,$frameWidth);
    return $res;
}



function cmsType_faq_editContent($editContent,$frameWidth) {
    $faqClass = cmsType_faq_class();
    return $faqClass->faq_editContent($editContent,$frameWidth);
}


?>
