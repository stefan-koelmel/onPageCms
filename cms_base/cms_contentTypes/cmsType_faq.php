<?php // charset:UTF-8
class cmsType_faq_base extends cmsType_contentData_show_base {

    function getName (){
        return "FAQ";
    }

     function faq_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        
        
        $newData = array();
        $editData = array();
        $reload = 0;
        
        // GET CATEGORY LIST FOR FAQ
        $catGet = cmsCategory_get(array(name=>"FAQ"));
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
                //echo ("Echo Close $catId => $value <br>");
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
        
        
        
        
        if ($_POST) {
            foreach ($_POST as $key => $value) {
                 // echo ("Save $key = $value <br />");
                 if (substr($key,0,8)== "faqEdit_") { 
                    $faqEditId = substr($key,8);
                    echo ("FAQ EDIT to $faqEditId <br>");
                    $editData[$faqEditId] = $_POST["faq_edit"][$faqEditId];
                    if (is_array($editData[$faqEditId])) {
                        // foreach ($editData[$faqEditId] as $faqKey => $faqText) {
                        $saveData = array();
                        $saveData[id] = $faqEditId;
                        $saveData[head_dt] = $editData[$faqEditId][headline];
                        $saveData[text_dt] = $editData[$faqEditId][text];
                        $saveRes = cmsFaq_save($saveData);
                        if ($saveRes) $reload = 10;
                        //echo ("SAVE FAQ DATA with id = $faqEditId  Result = $saveRes <br>");
                        //show_array($saveData);                        
                    }
                }
                                // NEW FAQ
                if (substr($key,0,7)== "faqAdd_") { 
                    $faqNewId = substr($key,7);
                    echo ("FAQ ADD to $faqNewNr <br>");
                    $newData[$faqNewId] = $_POST["faq_new"][$faqNewId];
                    if (is_array($newData[$faqNewId])) {
                        $saveData[cat] = $faqNewId;
                        $saveData[head_dt] = $newData[$faqNewId][headline];
                        $saveData[text_dt] = $newData[$faqNewId][text];
                        $saveRes = cmsFaq_save($saveData);
                        if ($saveRes) $reload = 2;
                       
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
        }
        
        if ($reload) {
            reloadPage(cms_page_goPage(),$reload);
        }
        
        
        $showNew = 0;
        $edit = 0;
        if ($_SESSION[edit] AND $_SESSION[userLevel] > 6) {
            $showNew = 1;
            $edit = 1;
            echo ("<form method='post' >");
           
            div_start("faq_output faq_output_hidden");
            echo ("Hier würde der Output stehen");
            div_end("faq_output faq_output_hidden");
        }
        
        div_start("faq_list");
        
        foreach ($catList as $catId => $cat) {
            if (!$cat[view]) continue;
            
            $close = $cat[close];
            
        //for ($i=0;$i<count($catList);$i++) {
          //  $cat = $catList[$i];
            $catName = $cat[name];
            //$catId   = $cat[id];
           
            $mainTitleDiv = "faq_main";
            if ($close) $mainTitleDiv .= " faq_main_close";
            
            div_start($mainTitleDiv,array("id"=>"faq_main_".$catId));
            echo ("$catName");
            div_end($mainTitleDiv);
            
            $mainDivName = "faq_main_content";
            if ($close) $mainDivName .= " faq_main_content_hidden";
            $mainDivName = " faq_sort";
            div_start($mainDivName,array("id"=>"faq_main_content_".$catId));
            
            
            $faqList = $cat["faqList"]; // cmsFaq_getList(array("cat"=>$catId),"sort");
            for ($f=0;$f<count($faqList);$f++) {
                $faq = $faqList[$f];
            
            
                $headLine = $faq[head];
                $faqId    = $faq[id];
                $text     = $faq[text];
                
                
                
                if ($edit) {
                    div_start("faq_item",array("id"=>"faq_item_".$faqId,"style"=>"position:relative;"));
                    //div_start("faq_editLine");
               
                    div_start("cmsEditBox faq_editBox","width:auto;");
                    div_start("faq_edit_button cmsContentFrame_editButton",array("id"=>"faq_edit_".$faqId));
                    echo ('<img border="0px" src="/cms_base/cmsImages/cmsEdit.png">');
                    div_end ("faq_edit_button cmsContentFrame_editButton");
                    
                    div_start("faq_move_button ",array("id"=>"faq_move_".$faqId));
                    echo ('<img border="0px" src="/cms_base/cmsImages/cmsMove.png">');
                    div_end ("faq_move_button ");
                    
                    div_start("faq_delete_button",array("id"=>"faq_delete_".$faqId));
                    echo ('<img border="0px" src="/cms_base/cmsImages/cmsDelete.png">');
                    div_end("faq_delete_button");

                    div_start("faq_delete_action faq_delete_action_hidden",array("id"=>"faq_deleteFrame_".$faqId));
                    echo ('Löschen:');
                    echo ("<input class='cmsSmallButton' type='submit' value='JA' name='delete_faq_".$faqId."' />");
                    echo ("<input class='cmsSmallButton cmsSecond' type='submit' value='NEIN' name='delete_faq_cancel' />");
                    div_end("faq_delete_action faq_delete_action_hidden");
                    
                    div_end("cmsEditBox faq_editBox");
                }
                
                if ($edit) {
                    $divName = "faq_edit_input";
                    if ($faqEditId != $faqId) $divName .= " faq_edit_input_hidden";
                    div_start($divName,array("id"=>"faq_edit_form_".$faqId));
                    echo ("<h3>FAQ Text bearbeiten</h3>");
                    echo ("<input style='width:100%;' type='text' value='".$headLine."' name='faq_edit[$faqId][headline]' /> ");
                    echo ("<textarea style='width:100%;height:80px;' name='faq_edit[".$faqId."][text]'>$text</textarea>");
                    echo ("<input type='submit' class='cmsInputButton' name='faqEdit_$faqId' value='speichern' >");
                   
                    div_end($divName);
                }
                
                
                // headLine
                $mode = "single";
                $divHeadName = "faq_headline";
                if ($mode == "single") $divHeadName = "faq_headline_single ".$divHeadName;
                
                div_start($divHeadName,array("id"=>"faq_".$faqId));
                echo ("$headLine");
                div_end($divHeadName);
                
                if ($edit) {
                    //div_end("faq_editLine","before");
                }
                
               

                // show Text
                $divNameText = "faq_text faq_text_hidden";
                div_start($divNameText,array("id"=>"faqText_".$faqId));
                echo ("$text");
                div_end($divNameText);
                if ($edit) {
                    div_end("faq_item");
                }
            }
            
            
            
            
            if ($showNew) {
                $divName = "faq_new_button"; 
                if ($faqNewNr == $catId) $divName .= " faq_new_button_active";
                
                div_start($divName,array("id"=>"faq_new_".$catId));
                echo ("newText<br>");
                div_end($divName);
                
                $divName = "faq_new_input";
                if ($faqNewNr != $catId) $divName .= " faq_new_input_hidden";
                
                
                div_start($divName,array("id"=>"faq_new_form_".$catId));
                echo ("<input style='width:100%;' type='text' value='".$newData[$catId][headline]."' name='faq_new[$catId][headline]' /> ");
                echo ("<textarea style='width:100%;height:80px;' name='faq_new[$catId][text]'>".$newData[$catId][text]."</textarea>");
                echo ("<input type='submit' class='cmsInputButton' name='faqAdd_$catId' value='speichern' >");               
                div_end($divName);
            }
            
            
            
            div_end($mainDivName);
            
            
            
            
            
        }
        
        if ($edit) {
             echo ("</form>");
        }
        
        div_end("faq_list");
        // show_array($catList);
        
        
    }
    




    function faq_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        $catGet = cmsCategory_get(array(name=>"FAQ"));
        if (!is_array($catGet)) {
            $newCatData = array("name"=>"FAQS","mainCat"=>0,"show"=>1);
            $newCatId = cmsCategory_save($newCatData);
            // echo ("NewCatID = $newCatId <br>");
            $faqId = $newCatId;
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
        foreach ($catList as $catId => $cat) {
            $catName = $cat[name];
            // $catId  = $cat[id];
            $catSort    = $cat[sort];
            if ($catSort > $maxSort) $maxSort = $catSort;
            $addData = array();
            $addData[text] = "Hauptebene".$i;
            $i++;
            $input = "<input type='text' value='$catName' name='catSave[".$catId."][name]' />";
            $input .= "<input type='text' value='$catSort' style='width:30px;' name='catSave[".$catId."][sort]' />";
            
            if ($data["view_".$catId]) $checked="checked='checked'";
            else $checked = "";            
            $input .= "Sichtbar: <input type='checkbox' value='1' $checked name='editContent[data][view_".$catId."]' />";
            
            if ($data["close_".$catId]) $checked="checked='checked'";
            else $checked = "";
            $input .= "Geschlossen: <input type='checkbox' value='1' $checked name='editContent[data][close_".$catId."]' />";
            
            $addData[input] = $input;
            $res[] = $addData;            
        }
        
        $maxSort ++;
        $addData = array();
        $addData[text] = "neue Hauptebene";
        $input = "<input type='text' value='' name='catSave[new][name]' />";
        $input .= "<input type='text' value='$maxSort' name='catSave[new][sort]' />";
        $addData[input] = $input;
        $res[] = $addData;    
        
        

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
    $faqClass->faq_show($contentData,$frameWidth);
}



function cmsType_faq_editContent($editContent,$frameWidth) {
    $faqClass = cmsType_faq_class();
    return $faqClass->faq_editContent($editContent,$frameWidth);
}


?>
