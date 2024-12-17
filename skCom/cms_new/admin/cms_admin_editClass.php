<?php // charset:UTF-8

class cmsAdmin_editClass_base extends cmsType_contentTypes_base {
    
    
    function emptyData() {
        return array();
    }
    
    function emptyListFilter() {
        $filter = array();
        $filter[show] = 1;
        $ownFilter = $this->emptyListFilter_own();
        foreach ($ownFilter as $key => $value) {
            $filter[$key] = $value;
        }
        return $filter;
    }
    
    function emptyListFilter_own() {
        return array();
    }  
    
    function emptyListSelect() {
        $select = array();
        $ownSelect = $this->emptyListSelect_own();
        foreach ($ownSelect as $key => $value) {
            $select[$key] = $value;
        }        
        return $select;
    }
    
        
    
    
    function emptyListSelect_own() {
        $ownSelect = array();
        $ownSelect[dateRange] = "month";
        return $ownSelect;
    }


    function admin_uploadImage() {
        //  show_array($_POST);
        $imageFile = $_POST[uploadImage];
        $imageFolder = $_POST[uploadFolder];

        if (isset($_FILES)) {
//            echo ("<h1>Files Exist ".$_FIlES. " anz = ".count($_FILES)."</h1>");
//            foreach ($_FILES as $key => $value ) {
//                echo ("FILES $key = $value <br />");
//            }
            // echo ("Upload Image to $imageFolder $_FILES<br />");
            $imageId = cmsImage_upload_File($_FILES[uploadImage],$imageFolder);
            return $imageId;
        }
        
    }
    
    function cacheRefresh($id,$saveData,$mode) {}

    function checkList($dataList) {}


    function showListFilter_specialView($filterData) {
//        $specialView,"specialView",array("submit"=>1),$filter,$sort);
//        if ($specialViewSelect) {
//            $spanData = array("width"=>$leftWidth);
//            if ($error[specialView]) $spanData["class"] = "inputError";
//            echo (span_text_str("spezielle Ansichten:",$spanData));
//            echo ($specialViewSelect."<br />");
    }


    function showListFilter_contentView($filterData) {
        
    }

    function showListFilter_action_reload($key) {
        return 1;
    }


    function showListFilter_action($data) {
        global $pageInfo;
        $reloadPage = array();
        foreach ($data as $key => $value) {
            // echo ("POST $key = $value <br /> ");
            $filterData[$key] = $value;
            if ($this->showListFilter_action_reload($key)) {
                if ($value == "0") $value = "not";
                if ($value == "") $value = "not";
                $reloadPage[$key] = $value;
                // echo ("POST and reload $key = $value <br /> ");
            }
        }

        if (count($reloadPage)>0) {
              // add GET DATA to reload if not in POST
            foreach ($_GET as $key => $value) {
                // echo ("GET $key = $value <br>");
                switch ($key) {
                    case "page" : break;
                    default :
                        if (!$reloadPage[$key]) $reloadPage[$key]=$value;
                        if (!$filterData[$key]) $filterData[$key]=$value;
                }
            }
            // CREATE reload URL
            $reload = "";
            foreach($reloadPage as $key => $value) {
                if ($value != "not") {
                    if ($reload=="") $reload .="?";
                    else $reload .= "&";
                    $reload .= $key."=".$value;
                }
            }
            // reload Page
            $reload = $pageInfo[page].$reload;
            // echo ("Reload '$reload <br />");
            echo ("<h1> Sammle Daten </h1>");
            reloadPage($reload,0);
            return "relaod";
        } else {
                // add GET DATA to reload if not in POST
            foreach ($_GET as $key => $value) {
                switch ($key) {
                    case "page" : break;
                    default :
                        if (!$filterData[$key]) $filterData[$key]=$value;
                }
            }
        }
        return $filterData;
    }


   function select_specialView($code,$dataName,$showData,$filter,$sort) {
        $specialList = $this->select_specialView_list($filter,$sort);
        // echo ("SpecialList Count ".count($specialList)."<br />");
        $str = "";

        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";
        $str.= ">Normale Ansicht</option>";

        foreach ($specialList as $specialId => $value) {
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

    function admin_show_filterSort($filter,$sort) {
        if ($_SESSION[showLevel]>=9) {
            echo ("<b>Filter:</b>");
            if (is_array($filter)) {
                foreach ($filter as $key => $value) echo (" #$key = $value ");
            }
            echo ("<br />");
            echo ("<b>Sort:</b> $sort<br />");
        }
    }

    function admin_showFilter($frameWidth) {

        $filterList = $this->admin_get_filterList();

        $contentData = array("data"=>array());
        $contentData[data][filter] = array();
        foreach ($filterList as $filterKey => $filterValue) {
            if (is_array($filterValue)) {
                
                // echo ("Filter List $filterKey = $filterValue <br />");
                //show_array($filterValue);
                $contentData[data]["customFilterView_".$filterKey] = "dropdown";
            }
        }

        $res = $this->showList_customFilter($contentData,$frameWidth);
        if (!is_array($res)) $res = array();
        return $res;
    }

     function editContent_filter_getList() {
        $filterList = $this->admin_get_filterList();
        return $filterList;
    }

    function admin_get_filterList() {
        $filterList = array();

        $ownList = $this->admin_get_filterList_own();
        if (is_array($ownList)) {
            foreach ($ownList as $filterKey => $filterValue) {
                $filterList[$filterKey] = $filterValue;
            }
        }
        return $filterList;
    }

    function admin_get_filterList_own() {
        return array();
    }



    function customFilter_specialView_getList_own() {
        return $this->admin_get_specialFilterList_own();
    }

    function admin_get_specialFilterList_own() {}



    function showListFilter($leftWidth,$filterData) {
        $out = "";
        $filter = array();
        $sort   = "";
        $specialFilter = $this->showListFilter_specialView($filterData);
        if (is_array($specialFilter)) {

            $spanData = array("width"=>$leftWidth);
            $filterName = "spezielle Ansichten";
            $out .= span_text_str($filterName.":",$spanData);

            $submit = 1;
            $str = "";

            $specialView = $filterData[specialView];
            $submitStr = "";
            if ($submit) $submitStr = "onChange='submit()' ";

            $str.= "<select name='specialView' class='cmsSelectType'  style='min-width:200px;' $submitStr value='$specialValue' >";


            $str.= "<option value='0'";
            if (!$specialView) $str.= " selected='1' ";
            $str.= ">Normale Ansicht</option>";

            foreach ($specialFilter as $specialId => $value) {
                 if ($value) {
                    if (is_array($value)) $specialName = $value[name];
                    else $specialName = $value;
                    $str.= "<option value='$specialId'";
                    if ($specialView == $specialId) {
                        if (is_array($value[filter])) {
                            foreach ($value[filter] as $filterKey => $filterValue  ) {
                                echo (" - - - > > > $filterKey = $filterValue <br />");
                                $filter[$filterKey] = $filterValue;
                            }
                        }
                        $str.= " selected='1' ";
                    }
                    $str.= ">$specialName</option>";
                 }
            }
            $str.= "</select><br />\n";
            $out .= $str;        
        }

        $contentFilter = $this->showListFilter_contentView($filterData);
        if (is_array($contentFilter)) {
             foreach ($contentFilter as $key => $value) {
                // show_array($value);

                $spanData = array("width"=>$leftWidth);
                $filterName = "spezielle Ansichten";
                if ($value[name]) $filterName = $value[name];
                $out .= span_text_str($filterName.":",$spanData);
                $out .= $value[output];
                if ($value[filter]) $filter[$key] = $value[filter];
                $out .= "<br />\n";
            }
        }

        if ($out) {
            echo ("<form method='post' >");

            echo ($out);
            echo ("</form>");
        }
        return array("filter"=>$filter,"sort"=>$sort);
    }

    function query_queryData($saveData,$editShow=array(),$dontAddQuery=0) {
        //  foreach ($saveData as $k => $v) echo ("$k=$v<br />");
        
        if ($dontAddQuery == "location") {
            if (is_array($saveData[data])) {
                unset ($saveData[data][street]);
                unset ($saveData[data][streetNr]);
                unset ($saveData[data][plz]);
                unset ($saveData[data][city]);
                unset ($saveData[data][url]);
                if (!$saveData[data][ticketUrl]) unset($saveData[data][ticketUrl]);
            }
        }
        
        foreach ($editShow as $key => $data) {
            // echo ("$key => $data <br />");
            switch ($data[type]) {
                case "checkbox" :
                    if ($data[show]) {
                        if (!$saveData[$key]) $saveData[$key] = 0;
                        // echo "$key Checkbox '$saveData[$key]' <br />";
                    }
                    break;
                    
                case "float" :
                    $floatStr = $saveData[$key];
                    //echo ("SAVE FLOAT '$floatStr' --> ");
                    $komma = $data[komma];
                    if (!$komma) $komma = ",";
                    $deli1000 = $data["1000"];
                    if (!$deli1000) $deli1000 = ".";
                    
                    // remove 1000er 
                    $floatStr = str_replace($deli1000,"",$floatStr);
                    // echo ("after remove 1000er $deli1000 => $floatStr <br>");
                    
                    if ($komma != ".") {
                        $floatStr = str_replace($komma,".",$floatStr);
                        // echo ("after replace komma $komma => $floatStr <br>");
                    }
                    $float = floatval($floatStr);
                    
                    $saveData[$key] = $float;                    
                    break;
                default :
                    // echo (" -> type = $data[type] <br />");
            }            
            
        }


        $query = "";
        foreach ($saveData as $key => $value) {
            // echo ("KEY = $key and Value = $value <br />");
            switch ($key) {
                case "id" :
                    $saveDataId = $value;
                    break;
                case "lastMod" : break;
                case "changeLog" : break;                

                default :
                    if (is_array($value)) {
                        foreach ($value as $dataKey => $dataValue) {
                            
                            
                            
                            if ($dataValue) {
                                $value[$dataKey] = php_clearStr($dataValue);
//                            } else {
//                                unset($value[$dataKey]);
                            }
                        }
                        if (count($value)) $value = array2Str($value);
                        else $value = "";
                        
                    } else {
                        $value = str_replace("&#039;","\'", $value);
                        $value = str_replace("&#034;",'\"', $value);

                    }

                    if ($query != "") $query.= ", ";
                    $query .= "`$key`='$value'";
            }
        }

        // ADD lastMod to Query
        $lastMod = $this->query_addLastMod();
        if ($lastMod) {
            $saveData[lastMod] = $lastMod;
            if ($query != "") $query.= ", ";
            $query .= "`lastMod`='$lastMod'";
        }

        // ADD ChangeLog to Query
        $changeLog = $this->query_addChangeLog($mode);
        if ($changeLog) {
            $saveData[changeLog] = $changeLog;
            if ($query != "") $query.= ", ";
            $query .= "`changeLog`='$changeLog'";
        }
        
        return array("query"=>$query,"saveDataId"=>$saveDataId);
    }


    function query_saveData($saveData, $mode,$tableName) {
        $queryData = $this->query_queryData($saveData);
        $query = $queryData[query];
        $saveDataId = $queryData[saveDataId];
        //echo ("QueryStr = $query<br />");
        //echo ("SaveDataId   = $saveDataId<br />");


        if ($mode == "new") {
            $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query  ";
        }
        if ($mode == "edit") {
            $query = "UPDATE `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query WHERE `id` = $saveDataId ";
        }
        // echo ("Query = $query<br />");
        $result = mysql_query($query);
        return $result;
    }

    function query_addLastMod() {
        $lastMod = date("Y-m-d H:i:s");
        return $lastMod;
        if ($query != "") $query.= ", ";
        $query .= "`lastMod`='".date("Y-m-d H:i:s")."'";
        return $query;
    }

    function query_addChangeLog($mode,$old_changeLog=0) {
        if (!is_string($old_changeLog)) $old_changeLog = $_POST[saveData][changeLog];
       
        if ($old_changeLog) {
            // echo ("<h1>Alter ChangeLog $old_changeLog</h1>");
            $changeLog = date("Y-m-d H:i:s").",".$_SESSION[userId].",".$mode;

            $changeList = explode("|",$old_changeLog);
            for ($i=0;$i<count($changeList);$i++) {
                if ($i<5) {
                    $changeLog .= "|".$changeList[$i];
                    // echo ("Append $changeList[$i]<br />");
                }
            }
            // echo ("<b>New ChangeLog = $changeLog </b> <br />");


        } else {
            $changeLog = date("Y-m-d H:i:s").",".$_SESSION[userId].",".$mode;
        }
        return $changeLog;
   
    }
    
    function edit_showSettings_fn() {
        $fn = $fn = $_SERVER[DOCUMENT_ROOT]."/".$GLOBALS[cmsName]."/cms/admin/".$this->tableName."_show.settings";
        return $fn;
    }
    
    function edit_getShowSettings() {
        $fn = $this->edit_showSettings_fn();
        $res = array();
        if (file_exists($fn)) {
            $text = loadText($fn);
            if ($text) {
                $settings = str2Array($text);
                if (is_array($settings)) {
                    $settings = php_clearPost($settings);
                    return $settings;
                }
            }
        }
        return $res;
    }
    
    function edit_editShow() {
        // echo "Edit editShow $this->tableName <br>";
        
       
        $sqlFields = $this->edit_getSQLFields($this->tableName);
        
        $editShow =  $_POST[editShow]; 
        if (is_array($editShow)) {
            
        }
        
        if ($_POST[editSave]) {
            echo "<h1>Speichern</h1>";        
            if (is_array($editShow)) {
                foreach($editShow as $key => $value) {
                   
                    if (!$value[show]) $editShow[$key][show] = 0;
                    if (!$value[needed]) $editShow[$key][needed] = 0;
                    
                    
                    // echo ("SAVE $key = $value show=".$editShow[$key][show]." needed=".$editShow[$key][needed]."<br>");
                    
                }
                $saveText = array2Str($editShow);
//                div_start("SaveText ");
//                echo ($saveText);
//                div_end("SaveText ");
                
                $checkRes = str2Array($saveText) ;
                if (is_array($checkRes)) {
                    cms_infoBox("Eingabe Einstellungen gespeichert <br>");
                    $reload = 2;
                } else {
                    cms_errorBox("Fehler beim speichern der Eingabe Einstellungen");
                    $reload = 0;                    
                }
                
                if ($reload) {
                    $fn = $this->edit_showSettings_fn();
                    saveText($saveText,$fn);
                
                    $goPage = cms_page_goPage("view=editShow");
                    reloadPage($goPage,2);
                    
                    return 0;
                }
                
                
               
                // echo ("$fn <br>");
            }
        }
        
        if ($_POST[editCancel]) {
            echo ("<h1>Abgebrochen </h1>");
            $goPage = cms_page_goPage("view=new");
            reloadPage($goPage,2);
            return 0;            
        }
        
        
        if (is_array($editShow)) {
            foreach($editShow as $key => $value) {
                // echo ("<h1>$key</h1>");
                foreach ($value as $k => $v) {
                    //echo ("$k = $v <br>");
                    $sqlFields[$key][$k]=$v;
                }
            }
        }
         
        echo ("<form method='post' >");
        div_start("cmsContentEditFrame cmsTypeSortable");
        
        $typeList = $this->edit_typeList();
        foreach ($sqlFields as $sqlField => $data) {
            // echo ("<h3>$sqlField </h3>");
            
            
            $name = $data[name];
            $show = $data[show];
            $need = $data[need];
            $type = $data[type];
            
            div_start("cmsInputLine cmsTypeSort");

            div_start("cmsInputLeft");
            $show = $data[show];
            if ($show) $checked = "checked='checked'";
            else $checked = "";
            echo ("<input class='cmsShowCheckBox' id='showType_$sqlField' type='checkbox' $checked value='1' name='editShow[$sqlField][show]' />");
            echo ("<b>$sqlField</b>");

            
            div_end("cmsInputLeft");
            
            div_start("cmsInputRight");
            
            $divName = "cmsShowEdit";
            if (!$show) $divName .= " cmsShowEdit_hidden";
            div_start($divName,array("id"=>"cmsEditType_$sqlField"));
           
            // name
            echo (" Name: <input type='text' value='$data[name]' name='editShow[$sqlField][name]' style='width:70px;' />");
            
            // type 
            echo (" Typ: ".$this->edit_selectType($data[type],"editShow[$sqlField][type]",array("submit"=>1)));
             
            echo (" oneLine: ".$this->edit_selectNext($data[next],"editShow[$sqlField][next]",$sqlFields));
            
            echo (" width: <input type='text' value='$data[width]' name='editShow[$sqlField][width]' style='width:70px;' />");
            
            echo ("<br />");
            // readOnly
            if ($data[readonly]) $checked = "checked='checked'";
            else $checked = "";
            echo (" <input type='checkbox' $checked value='1' name='editShow[$sqlField][readonly]' />readOnly");
            // disabled
            if ($data[disabled]) $checked = "checked='checked'";
            else $checked = "";
            echo (" <input type='checkbox' $checked value='1' name='editShow[$sqlField][disabled]' />disabled");
            
            // hidden
            if ($data[hidden]) $checked = "checked='checked'";
            else $checked = "";
            echo (" <input type='checkbox' $checked value='1' name='editShow[$sqlField][hidden]' />hidden");
            
            // showLevel
            echo ("Zeigen ".cms_user_selectlevel($data[showLevel], $_SESSION[userLevel],"editShow[$sqlField][showLevel]"));
            
            echo ("<br />");
            
            if (is_array($typeList[$type])) {
                $out = "";
                
                foreach ($typeList[$type] as $k => $v) {
                    switch($k) {
                        case "name" : break;
                        case "dataSource" : 
                            $dataSource = $data[dataSource];
                            $out.= "Quelle: ".$this->edit_selectDataSource($data[dataSource],"editShow[$sqlField][dataSource]",array("submit"=>1),$sqlField);
                            switch ($dataSource) {
                                case "category" :
                                    switch ($sqlField) {
                                        case "subCategory" : 
                                            $out .= "<input type='text' value='subCat' name='editShow[$sqlField][showFilter][mainCat]' readonly='readonly' />";
                                            //$out .= "SubCategory Category: $category "; // .$this->edit_selectCategory($data[showFilter][mainCat],"editShow[$sqlField][showFilter][mainCat]", array("submit"=>0),array("mainCat"=>0),"name");
                                            break;
                                        
                                        case "category" :
                                            $category = $data[showFilter][mainCat];
                                            $out .= "Select Category: ".$this->edit_selectCategory($data[showFilter][mainCat],"editShow[$sqlField][showFilter][mainCat]", array("submit"=>0),array("mainCat"=>0),"name");
                                    }
                            }
                            break;
                        
                        case "toggleMode" :
                            $out .= " Modus :".$this->edit_selectToggleMode($data[showData][mode],"editShow[$sqlField][showData][mode]",array("submit"=>0),$sqlField);
                            break;
                        case "toggleCount" :
                            $out .= "Anzahl <input type='text' value='".$data[showData]["count"]."' name='editShow[$sqlField][showData][count]' style='width:30px;' />";
                            break;
                        
                        case "filter" : 
                            break;
                        case "empty" :
                            $out .= "Bezeichnung <input type='text' value='".$data[showData]["empty"]."' name='editShow[$sqlField][showData][empty]' />";
                            break;
                        
                        
                        case "sort" :
                            $out.= " sort: ".$this->edit_selectSort($data[showData][sort],"editShow[$sqlField][showData][sort]",array("onChange"=>1),$sqlField);
                            break;
                        
                        case "imageUpload" :
                            if ($data[$k]) $checked = "checked='checked'";
                            else $checked = "";
                            $out .= "Bildupload erlaubt: <input type='checkbox' $checked value='1' name='editShow[$sqlField][$k]' />"; 
                            break;
                        case "imageFolder" :
                            $out .= " Upload Ordner: <input type='text' value='$data[imageFolder]' name='editShow[$sqlField][imageFolder]'  />"; 
                            break;                    
                    
                        case "height" :
                            $out .= " Höhe: <input type='text' value='$data[height]' name='editShow[$sqlField][height]' style='width:50px;' />"; 
                            break;
                        
                        case "komma" :
                            $out .= " Trennzeichen: <input type='text' value='$data[komma]' name='editShow[$sqlField][komma]' style='width:20px;' />"; 
                            break;                    
                        case "1000" :
                            $out .= " 1000er Zeichen: <input type='text' value='".$data["1000"]."' name='editShow[$sqlField][1000]' style='width:20px;' />"; 
                            break;                       
                        case "deci" :
                            $out .= " Nachkommastellen: <input type='text' value='$data[deci]' name='editShow[$sqlField][deci]' style='width:20px;' />"; 
                            break;
                            
                        default :
                            $out .= "$k = $v | ";
                    }
                    
                    
                }
                if ($out) {                    
                    echo ($out."<br />");
                }
//                foreach ($typeList[$type] as $k => $v) {
//                    if ($k != "name") echo ("$k => $v <br>");
//                }
                
                
            }
            
            // need
            if ($data[needed]) $checked = "checked='checked'";
            else $checked = "";
            echo (" benötigt <input type='checkbox' $checked value='1' name='editShow[$sqlField][needed]' />");
            
            echo (" Fehlertext <input type='text' value='$data[needError]' name='editShow[$sqlField][needError]' />");
            
            
                
            // Tip
            echo ("<br />Tip:<textarea style='width:400px;height:50px;' name='editShow[$sqlField][tip]'>$data[tip]</textarea >");    
                
                
                
            div_end($divName);
            div_end("cmsInputRight");
            div_end("cmsInputLine cmsTypeSort","before");
           
           
            
            
            
            
            
            if (!$typeList[$type]) {
                $typeList[$type] = 1;
                echo ("Not in TypeList $type <br>");
            }
            
//            foreach ($data as $key => $value) {
//                switch ($key) {
//                    case "name" : break;
//                    case "type" : break;
//                    
//                    case "show" : break;
//                    case "width" : break;
//                    case "height" : break;
//                    case "showLevel" : break;
//                    case "mode" : break;
//                    
//                    case "readonly" : break;
//                    case "needed" : break;
//                    case "needError" : break;
//                
//                    case "showData" : break;
//                    case "showFilter" : break;
//                    case "showSort" : break;
//                    case "dataSource" : break;
//                    
//                    case "imageUpload" : break;
//                    case "imageFolder" : break;
//                    case "komma" : break;
//                    case "1000" : break;
//                    case "deci" : break;
//                    default :
//                        echo ("unkown key '$key' = $value <br>");
//                }
//                
//            }
        }
        
        echo ("<input type='submit' value='speichern' name='editSave' class='cmsInputButton' />");
        echo ("<input type='submit' value='abbrechen' name='editCancel' class='cmsInputButton cmsSecond' />");
        
        div_end("cmsContentEditFrame cmsTypeSortable");
       echo ("</form>");
   }
   
   function edit_typeList() {
       $typeList = array();
       $typeList[text] = array("name"=>"Zeile");
       $typeList[password] = array("name"=>"Passwort");
       $typeList[textarea] = array("name"=>"Textfeld","height"=>1);
       $typeList[dropdown] = array("name"=>"Auswahl - Dropdown","dataSource"=>1,"filter"=>1,"empty"=>1,"sort"=>1);

       $typeList[toggle] = array("name"=>"Auswahl Klickbar","dataSource"=>1,"filter"=>1,"sort"=>1,"toggleMode"=>1,"toggleCount"=>1);

       $typeList[imageSelectList] = array("name"=>"Bild-Liste", "imageUpload"=>"Upload erlaubt","imageFolder"=>"UploadOrdner","height"=>1);
       $typeList[float] = array("name"=>"Komma Zahl", "komma"=>"Kommastellen", "1000"=>"Dezimalpunkt", "deci"=>"TrennZeichen" );                    
       $typeList[integer] = array("name"=>"Ganze Zahl");
       $typeList[checkbox] = array("name"=>"JA / NEIN");
       $typeList[dateTime] = array("name"=>"Datum");
       $typeList[changeLog] = array("name"=>"changeLog");
       return $typeList;
   }
   
   function edit_selectType($value,$dataName,$data=array()) {
       $list = $this->edit_typeList();
       
       $out = "<select value='$value' name='$dataName' ";
       if ($data[submit]) $out .= "onchange='submit()'";
       $out .= ">";
       foreach ($list as $key => $data) {
           
           if ($value == $key) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$key' $selected>$data[name]</option>";
           
           
       }
       $out .= "</select>";
       return $out;
   }
   
   
   function edit_selectNext($value,$dataName,$list=array(),$data=array()){
       
       $out = "<select value='$value' name='$dataName' ";
       if ($data[submit]) $out .= "onchange='submit()'";
       $out .= " style='width:100px;' >";
       
       // empty Line
       if (!$value) $selected="selected='selected'";
       else $selected = "";
       $out .= "<option value='' $selected >-</option>";
       
       foreach ($list as $key => $data) {
           if ($value == $key) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$key' $selected>";
           $name = $data[name];
           if (!$name) $name = $key;
//           $name = $key;
           $out .= "$name";
           $out .= "</option>";
           
           
       }
       $out .= "</select>";
       return $out;
   
   }
   
   function edit_selectDataSource($value,$dataName,$showData,$sqlField) {
       // echo (" edit_selectDataSource($value,$dataName,$showData,$sqlField) ");
       $out = "<select value='$value' name='$dataName' ";
       if ($showData[submit]) $out .= "onchange='submit()'";
       $out .= ">";
       
       $list = array();
       $list[category] = array("name"=>"Kategorie");
       $list[company] = array("name"=>"Hersteller");
       $list[product] = array("name"=>"Produkte");
       $list[project] = array("name"=>"Projekte");
       $list[dates] = array("name"=>"Termine");
       $list[location] = array("name"=>"Ort");
      
       // empty Line
       if (!$value) $selected="selected='selected'";
       else $selected = "";
       $out .= "<option value='' $selected >nicht gewählt</option>";
       
       
       foreach ($list as $key => $data) {
           if ($value == $key) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$key' $selected>";
           $name = $data[name];
           if (!$name) $name = $key;
//           $name = $key;
           $out .= "$name";
           $out .= "</option>";
       }
       $out .= "</select>";
       return $out;
   }
                
   function edit_selectToggleMode($value,$dataName,$showData,$sqlField) {
       $out = "<select value='$value' name='$dataName' ";
       if ($showData[submit]) $out .= "onchange='submit()'";
       $out .= ">";
       
       $list = array();
       $list[single] = array("name"=>"Einfach");
       $list[multi] = array("name"=>"Mehrfach");
       
       foreach ($list as $key => $data) {
           if ($value == $key) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$key' $selected>";
           $name = $data[name];
           if (!$name) $name = $key;
//           $name = $key;
           $out .= "$name";
           $out .= "</option>";
       }
       $out .= "</select>";
       return $out;
   }
   
   
   function edit_selectCategory($value,$dataName,$showData,$filter,$sort) {
       $out = "<select value='$value' name='$dataName' ";
       if ($showData[submit]) $out .= "onchange='submit()'";
       $out .= ">";
       
       $list = cmsCategory_getList($filter, $sort,"assoId");
       
       // empty Line
       if (is_null($value)) $selected="selected='selected'";
       else $selected = "";
       $out .= "<option value='' $selected >keine Kategorie</option>";
       
       if (!is_null($value) AND $value=="0") $selected="selected='selected'";
       else $selected = "";
       $out .= "<option value='0' $selected >Haupt Kategorie</option>";
       
       foreach ($list as $catId => $catName) {
           if ($value == $catId) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$catId' $selected >$catName</option>";
           
           $subFilter = $filter;
           $subFilter[mainCat] = $catId;
           $subCatList = cmsCategory_getList($subFilter, $sort,"assoId");
           if (is_array($subCatList) AND count($subCatList)) {
               foreach ($subCatList as $subCatId => $subCatName) {
                    if ($value == $subCatId) $selected="selected='selected'";
                    else $selected = "";
                    $out.= "<option value='$subCatId' $selected ><div style='display:block;height:30px;'> &nbsp; &nbsp; -> $subCatName </div></option>";
                    
                    // 3.Ebene von Category
                    $subSubFilter = $filter;
                    $subSubFilter[mainCat] = $subCatId;
                    $subSubCatList = cmsCategory_getList($subSubFilter, $sort,"assoId");
                    if (is_array($subSubCatList) AND count($subSubCatList)) {
                        foreach ($subSubCatList as $subSubCatId => $subSubCatName) {
                             if ($value == $subSubCatId) $selected="selected='selected'";
                             else $selected = "";
                             $out.= "<option value='$subSubCatId' $selected ><div style='display:block;height:30px;'> &nbsp; &nbsp; &nbsp; &nbsp; -> $subSubCatName</div></option>";



                        }

                    }
                    
                    
               }
               
           }
       }
       $out .= "</select>";
       return $out;
       
       
   }
   
   function edit_selectSort($value,$dataName,$showData,$sqlField) {
       // echo (" edit_selectDataSource($value,$dataName,$showData,$sqlField) ");
       $out = "<select value='$value' name='$dataName' ";
       if ($showData[submit]) $out .= "onchange='submit()'";
       $out .= ">";
       
       $list = array();
       $list[id] = array("name"=>"id");
       $list[name] = array("name"=>"name");
       $list[date] = array("name"=>"Datum");
       
       foreach ($list as $key => $data) {
           if ($value == $key) $selected="selected='selected'";
           else $selected = "";
           $out.= "<option value='$key' $selected>";
           $name = $data[name];
           if (!$name) $name = $key;
//           $name = $key;
           $out .= "$name";
           $out .= "</option>";
           
           
           
       }
       $out .= "</select>";
       return $out;
   }
   
   function edit_editList() {
       echo "Edit editList $this->tableName <br>";
       
   }

    function edit_getSQLFields($tableName) {
        $list = array();

        $settinglist = $this->edit_getShowSettings();
        $list = $settinglist;
        
        $ownEdit = $this->edit_show_own($tableName,$sepcialData);
        if (is_array($ownEdit)) {
            foreach ($ownEdit as $name=>$value) {
                //echo ("<h3>$name</h3>");
                if (is_array($list[name])) {
                    foreach ($value as $showKey => $showValue) {
                        if (is_array($showValue)) {
                            foreach ($showValue as $subShowKey => $subShowValue) {
                                if (is_null($list[$name][$showKey][$subShowKey])) {
                                    //echo ("Set in $showKey -> $subShowKey to $subShowValue <br>");
                                    // $list[$name][$showKey][$subShowKey] = $subShowValue;
                                } else {
                                    // echo ("Not set in $showKey -> $subShowKey because exist $subShowValue <=> ".$list[$name][$showKey][$subShowKey]." <br>");
                                }
                            }
                            
                        } else {
                        
                            if (is_null($list[$name][$showKey])) {
                                //echo ("Set $showKey to $showValue <br>");
                                $list[$name][$showKey] = $showValue;
                            } else {
                                // echo ("Not set $showKey because exist $showValue <=> ".$list[$name][$showKey]." <br>");
                            }
                        }

                    }
                } else {
                    $list[$name] = $value;
                }
            }
        }
        
//        if (is_array($settinglist)) {
//            foreach ($settinglist as $name=>$value) {
//                $list[$name] = $value;
//            }
//        }
//        

        // if (is_array($list) AND count($list)) return $list;
        global $cmsName;
        $table = $cmsName."_cms_".$tableName;

        $queryRow = "select * from `".$table."` LIMIT 1,1";
        $resultRow = mysql_query($queryRow);
        if (!$resultRow) {
            die('Anfrage fehlgeschlagen: $queryRow ' . mysql_error());
        }

        $i = 0;
        $res = array();
        while ($i < mysql_num_fields($resultRow)) {
            // echo "Information für Feld $i:<br />\n";
            $meta = mysql_fetch_field($resultRow, $i);
            if (!$meta) {
                echo "Keine Information vorhanden<br />\n";
            } else {
                $name = $meta->name;
                if (!is_array($list[$name])) {
                    // echo ("<h1> ADD $name to $list </h1>");
                    switch ($name) {
                        case "lastMod" :
                            $list[lastMod] = array("name"=>"Letzte Änderung","show"=>1,"showLevel"=>8,"type"=>"dateTime","mode"=>"text","width"=>"small","readonly"=>1);
                            $list[lastMod][needed] = 0;
                            break;

                        case "changeLog" :
                            $list[changeLog] = array("name"=>"Protokoll","show"=>1,"showLevel"=>9,"type"=>"changeLog","width"=>"standard","readonly"=>1);
                            $list[changeLog][needed] = 0;
                            break;

                        default :
                            $list[$name] = array("name"=>"'$name'","show"=>1,"type"=>"text","width"=>"standard");
                    }                    
                }
            }
            $i++;
        }

        return $list;
    }


    function edit_show($tableName,$sepcialData=array()) {
        $editShow = array();
        if ($tableName) $this->tableName = $tableName;
//        $editShow = $this->edit_getShowSettings();
//        
//        $ownEdit = $this->edit_show_own($tableName,$sepcialData);
//        if (is_array($ownEdit)) {
//            foreach ($ownEdit as $name=>$value) {
//                $editShow[$name] = $value;
//            }
//        }
        $editShow = $this->edit_getSQLFields($tableName);
        global $cmsName;
        $table = $cmsName."_cms_".$tableName;

        $queryRow = "select * from `".$table."` LIMIT 1,1";
        $resultRow = mysql_query($queryRow);
        if (!$resultRow) {
            die('Anfrage fehlgeschlagen: $queryRow ' . mysql_error());
        }

        $i = 0;
        $res = array();
        while ($i < mysql_num_fields($resultRow)) {
            // echo "Information für Feld $i:<br />\n";
            $meta = mysql_fetch_field($resultRow, $i);
            if (!$meta) {
                echo "Keine Information vorhanden<br />\n";
            } else {
                $name = $meta->name;
                if (!is_array($editShow[$name])) {
                    switch ($name) {
                        case "lastMod" :
                            $editShow[lastMod] = array("name"=>"Letzte Änderung","show"=>1,"showLevel"=>8,"type"=>"dateTime","mode"=>"text","width"=>"small","readonly"=>1);
                            $editShow[lastMod][needed] = 0;
                            break;

                        case "changeLog" :
                            $editShow[changeLog] = array("name"=>"Protokoll","show"=>1,"showLevel"=>9,"type"=>"changeLog","width"=>"standard","readonly"=>1);
                            $editShow[changeLog][needed] = 0;
                            break;

                        default :
                            $editShow[$name] = array("name"=>"'$name'","show"=>1,"type"=>"text","width"=>"standard");
                    }                    
                }
            }
            $i++;
        }

        return $editShow;
    }

    function edit_show_own($tableName,$sepcialData) {}

    
   
    
    
    function editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight,$showButtons=1) {
        if (!$leftWidth) $leftWidth = 150;
        if (!$rightWidth) $rightWidth = 400;
        if (!$standardHeight) $standardHeight = 50;
        if ($showButtons) {
            $buttonList = $this->editButtons($saveData);
            $this->editShow_Buttons($buttonList,"hidden");
        }
        

        foreach ($editShow as $key => $value) {
            $show = $value[show];
            if ($show) {
                // Width
                $width = $value[width];
                switch ($width) {
                    case "standard" : $width = $rightWidth; break;
                    case "small"    : $width = floor($rightWidth / 3); break;
                    case "half"     : $width = floor($rightWidth / 2); break;
                    default :
                        if ($width) {
                            $width = $this->position_getWidth($width, $rightWidth);                            
                        } else {
                            $width = $rightWidth;
                        }
                }

                // Height
                $height = $value[height];
                switch ($height) {
                    case "standard" : $height = $standardHeight; break;
                    case "double"   : $height = $standardHeight * 2; break;
                    case "small"    : $height = floor($standardHeight / 3); break;
                    default :
                        if ($height) {
                            $intVal = intval($height);
                            if (is_integer($intVal)) $height = $intVal;
                            else {
                                echo ("unkown Height '$height' use Standard<br />");
                                $height = $standardHeight;
                            }
                        } else {
                            $height = $standardHeight;
                        }
                }


                $editType = $value[type];
                $editLevel = $value[showLevel];
                // echo ("ShowLevelEdit = $editLevel ShowLevelPage".$_SESSION[showLevel]." <br />");
                if ($editLevel > 0 AND $editLevel > $_SESSION[showLevel]) {
                    $editType = "hidden";
                }




                if ($showNext) {
                    $showNext = 0;
                    $nextDelimiter = "<div style='width:10px;display:inline-block;'>&nbsp;</div>";
//                    $nextDelimiter = "&nbsp; ";
//                    if ($value[nextDelimiter]) $nextDelimiter = " ".$value[nextDelimiter]." ";
                    $titleStr = $nextDelimiter; // "&nbsp; ";
                    // echo ("SHOW NEXT nextW = $lastWidth w = $width rw=$rightWidth ");
                    $width = $rightWidth - $lastWidth - 5;
                    //echo (" -- > $width <br />");
                    // $width = $width -7;
                } else {
                    // Title Str
                    $editName = $value[name];
                    if ($value[next]) {
                        // echo "Next exist $editType $value[next]<br />";
                        $next = $value[next];
                        $nextName = $editShow[$next][name];
                        
                        // echo ("NextName $next = '$nextName' <br />");
                        if (is_string($nextName)) {
                            $editName .= " / ".$nextName;
                        }
                        if ($editShow[$next][tip]) {
                            $editName .= "<div id='tip_$key' class='cmsInputLineTip'>i";
                            $editName .= "<div id='tipBox_$key' class='cmsInputLineTipBox'>".$editShow[$next][tip]."</div>";
                            $editName .= "</div>";  
                        }
                        $lastWidth = $width;
                        $width = $width - 5 ;
                        
                    }
                    $spanData = array("width"=>$leftWidth);
                    if ($error[$key]) $spanData["class"] = "inputError";
                    $titleStr = span_text_str($editName.":",$spanData);
                    $titleStr = "<div class='cmsInputLine'><div class='cmsInputLeft' style='float:left;width:".$leftWidth."px;'>";
                    $titleStr .= $editName.":";
                    if ($value[tip]) {
                        $titleStr .= "<div id='tip_$key' class='cmsInputLineTip'>i";
                        $titleStr .= "<div id='tipBox_$key' class='cmsInputLineTipBox'>$value[tip]</div>";
                        $titleStr .= "</div>";     

                    }
                    
                    $setRightWidth = $rightWidth;
                   //  if ($value[tip]) $setRightWidth = $rightWidth + 20;
                    
                    $titleStr .= "</div><div class='cmsInputRight' style='float:left;width:".$setRightWidth."px;'>";

                }
                
                // WHidth Correcztion
                $widthSub = 0;
                switch ($editType) {
                    case "text" : $widthSub = 4; break;
                    case "integer" :$widthSub = 4; break;
                    case "float" : $widthSub = 4; break;                       
                    case "password" : $widthSub = 4; break;
                    case "hidden":
                    case "textarea": $widthSub = 4; break;
                    case "checkbox" : break;
                    case "date" : $widthSub = 4; break;
                    case "time" : $widthSub = 4; break;
                    case "dateTime" : $widthSub = 4; break;
                    case "changeLog" : break;
                    case "imageSelect" : break;
                    case "imageSelectList" : break;
                    case "autoComplete" : $widthSub = 4; break;
                    case "dropdown" : $widthSub = 0; break;                        
                    case "toggle" : break;                        
                    case "data"  : break;                        
                    case "special"  : break;                   
                }
                $width = $width -$widthSub;


                $editStyle = "";
                if ($width) $editStyle = "width:".$width."px;";
                if ($height AND $editType == "textarea") $editStyle.="height:".$height."px;";
                
                $readOnly = "";
                $idStr = "";
                if ($value[id]) $idStr = "id='$value[id]'";
                if ($value[idStr]) $idStr = "id='$value[idStr]'";
                if ($value[readonly]) $readOnly = "readonly='readonly'";
                $disabled = "";
                if ($value[disabled]) $disabled = "disabled='disabled' ";
                $hidden = "";
                if ($value[hidden]) {
                    $editType = "hidden";
                }                   
                
                $editClass = "";
                if ($value["class"]) $editClass="class='".$value["class"]."'";
                // echo ("class='$editClass'<br />");
                // echo ("$editType Style ='$editStyle' read=$readOnly dis=$disabled<br />");

                $dataName = "saveData[$key]";
                if ($value[name] == "Special") {
                    //show_array($value);
                }
                if ($value[dataName]) {
                    $dataName = $value[dataName];                    
                }
                
                
                
                $breakAtEnd = 1;               
                switch ($editType) {
                    case "text" :
                        $breakAtEnd = $this->editShowInput_text        ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;
                    
                    case "integer" :
                        $breakAtEnd = $this->editShowInput_integer     ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;
                    
                    case "float" :
                        $breakAtEnd = $this->editShowInput_float       ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "password" :
                        $breakAtEnd = $this->editShowInput_password    ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "hidden":
                        $breakAtEnd = $this->editShowInput_hidden      ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "textarea":
                        $breakAtEnd = $this->editShowInput_textarea    ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                     case "checkbox" :
                        $breakAtEnd = $this->editShowInput_checkbox    ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                     case "date" :
                        $breakAtEnd = $this->editShowInput_date        ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                     case "time" :
                        $breakAtEnd = $this->editShowInput_time        ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "dateTime" :
                        $breakAtEnd = $this->editShowInput_dateTime    ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "changeLog" :
                        $breakAtEnd = $this->editShowInput_changeLog   ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "imageSelect" :
                        $breakAtEnd = $this->editShowInput_imageSelect ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "imageSelectList" :
                        $breakAtEnd = $this->editShowInput_imageSelectList($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "autoComplete" :
                        $breakAtEnd = $this->editShowInput_autoComplete ($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "dropdown" :
                        $breakAtEnd = $this->editShowInput_dropdown($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "toggle" :
                        if ($key == "subCategory") {
                            // echo ("<h1>SubCategory</h1>");
                            if ($value[showFilter][mainCat] = "subCat") {
                                //if (intval($saveData[category])) {
                                    if (intval($saveData[category])) {
                                        $value[showFilter][mainCat] = intval($saveData[category]);
                                    } else {
                                        $value[showFilter][mainCat] = "-";
                                    }
                                    
                                    $value[showData]["class"] = "cmsToggle_subCategory";
                                // }
                                // echo ("<h3> SubCat $saveData[category] </h3>");
                                
                            }
                            // show_array($value);
                        }
                        
                        
                        
                        $breakAtEnd = $this->editShowInput_toggle($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
                        break;

                    case "data"  :
                        $breakAtEnd = $this->editShowInput_data($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth,$rightWidth,$standardHeight);
                        break;

                    case "special"  :
                        $breakAtEnd = $this->editShowInput_special($key,$saveData[$key],$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth,$rightWidth,$standardHeight);
                        break;


                    default :
                        echo ("unkown Type in $key = '$editType' ");
                }
                
               
                if ($value[tip]) {
                    // echo ("Tip");
                    //echo ("<div class='cmsInputLineTip'>i</div>");
                }
                
                if ($breakAtEnd) {
                    if ($next) {
                        $showNext = $next;                        
                        $next = 0;
                    } else {
                        echo ("</div><div style='clear:both;'></div></div>\n");
                        // echo("<br />\n");
                    }
                }
            }

        }
        if($showButtons) {
            // echo ("ready <br />");
            $this->editShow_Buttons($buttonList);
        }
    }


    function editShowInput_data($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth,$rightWidth,$standardHeight) {
        $ownData = $this->editShowInput_data_own($saveData,$key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth,$rightWidth,$standardHeight);
        if (is_array($ownData)) return $ownData["break"];
        // echo ($titleStr);
        // show_array($value);

        // echo ("<h1>Key $key</h1>");
        

        // echo ("<h1>Code $code</h1>");
        if (is_array($code)) {
            $saveData = array();
            foreach($code as $dataKey => $dataValue ) {
                $saveData[$dataKey] = $dataValue; ///$dataValue;
            }
            // show_array($code);
        } else {
            foreach ($value[showData] as $showKey => $showValue) {
                $saveData[$showKey] = $code;
            }
        }
        
        

        //echo ("<h2>Savedata </h2>");
        // show_array($saveData);

        if (is_array($value[showData])) {
            $editShow = array();

            foreach ($value[showData] as $showKey => $showValue) {
                $editShow[$showKey] = $showValue;
                //$saveData[$showKey] = $showKey;
                // echo ("DATA -->> $showKey = $showValue <br />");
                //$code[$key] = $name;
                $editShow[$showKey][dataName] = "saveData[$key][$showKey]";

            }
            // echo ("<h1>editShow </h1>");
            // show_array($editShow);

            $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight,0);

        }
       
                
        // echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $idStr $readOnly $disabled  >");
        $break = 0;
        return $break;
    }
    function editShowInput_data_own($saveData,$key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth,$rightWidth,$standardHeight) {}

                  
    function editShowInput_text($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_text_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        // echo("editShowInput_text($key,$code,$dataName,$value,$editStyle,$editClass,$idStr,$readOnly,$disabled<br />");
        echo ($titleStr);
        echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $idStr $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_text_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}

    function editShowInput_integer($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $int = intval($code);
        
        $ownData = $this->editShowInput_integer_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        // echo("editShowInput_text($key,$code,$dataName,$value,$editStyle,$editClass,$idStr,$readOnly,$disabled<br />");
        echo ($titleStr);
        echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $idStr $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_integer_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth){}
    
    function editShowInput_float($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {

        
        // show_array($value);
        $komma = $value[komma];
        if (!$komma) $komma = ",";
        $deli1000 = $value["1000"];
        if (!$deli1000) $deli1000 = ".";
        $deci = $value[deci];
        if (!$deci) $deci = 2;
        
        
        $floatStr = number_format($code, $deci, $komma,$deli1000);
        // echo ("$code $floatStr <br>");
        
        $ownData = $this->editShowInput_float_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        // echo("editShowInput_text($key,$code,$dataName,$value,$editStyle,$editClass,$idStr,$readOnly,$disabled<br />");
        echo ($titleStr);
        echo ("<input type='text' name='$dataName' value='$floatStr' style='$editStyle' $editClass $idStr $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    
    function editShowInput_float_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}
    
    function editShowInput_password($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_password_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];echo ($titleStr);
        echo ("<input type='password' name='$dataName' value='$code' style='$editStyle' $editClass $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_password_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_hidden($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_hidden_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        // echo ($titleStr);
        echo ("<input type='hidden' name='$dataName' value='$code' style='$editStyle' $editClass $readOnly $disabled  >");
        $break = 0;
        return $break;
    }
    function editShowInput_hidden_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}

    function editShowInput_textarea($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_textarea_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        
       
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);
        $openClose = $value[openClose];        
        echo ("<div style='width:".$width."px;display:inline-block;'>");
        if ($openClose) {            
            if ($code) {
                echo ("<div class='toggleTextArea cmsSmallButton cmsSecond' style='width:120px' id='$key'>Eingabe ausblenden</div>");
                // echo ("open<br />");
                $editClass = "class='id_$key'";
            } else {
                
                echo ("<div class='toggleTextArea cmsSmallButton' style='width:120px' id='$key'>Eingabe einblenden</div>");
                // $editStyle = "height:0px;visibility:hidden;";
                $editClass = "class='hiddenTextArea id_$key'";
                //echo ("<input type='hidden' name='$dataName' ")
            }


        }
        //  echo ("editShowInput_textarea($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) <br>");
        // echo ("$editStyle<br />");
        echo ("<textarea name='$dataName' style='$editStyle' $editClass $readOnly $disabled   >$code</textarea>");
        echo ("</div>");
        $break = 1;
        return $break;
    }
    function editShowInput_textarea_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_checkbox($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_checkbox_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);
        $checked = "";
        if ($code) $checked = "checked='checked'";
        echo ("<input type='checkbox' name='$dataName' $disabled $readOnly $editClass value='1' $checked >");
        $break = 1;
        return $break;
    }
    function editShowInput_checkbox_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_date($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_date_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showMode = $value[mode];
        if (!$showMode) {
            show_array($value);
            $showMode = "simple";
        }
        
        
        echo ("vi = $showMode ");
       
      //echo ("editShowInput_date($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {");
       //  echo "Date $code $showMode <br />";
        // show_array($value);
        if ($code) {
            $day = substr($code,8,2);
            $month = substr($code,5,2);
            $year = substr($code,0,4);
        }
        if (!intval($day)) $day = "";
        if (!intval($month)) $month = "";
        if (!intval($year)) $year = "";
        
        $id = "";
        if ($idStr) $id = $idStr;
        if ($editClass) $editClassStr = $editClass;
        if ($value["class"]) $editClass = $value["class"];
        // echo ("$editClass <br />");

        switch ($showMode) {
            case "simple" : 
                 $editClassStr = "";
                 if ($editClass) $editClassStr = "class='".$editClass."_Day'";
                 echo ("<input type='text' $id $editClassStr name='date_".$key."_day' value='$day' style='width:20px;' $editClass $readOnly $disabled  > ");

                 if ($editClass) $editClassStr = "class='".$editClass."_Month'";
                 echo ("<input type='text' $editClassStr name='date_".$key."_month' value='$month' style='width:20px;' $editClass $readOnly $disabled  > ");

                 if ($editClass) $editClassStr = "class='".$editClass."_Year'";
                 echo ("<input type='text' $editClassStr name='date_".$key."_year' value='$year' style='width:40px;' $editClass $readOnly $disabled  >");
                 // echo ($day.".".$month.".".$year);
                 break;
            
        }
        // echo ("Mode = $showMode ");
        // echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_date_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_time($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_time_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showMode = $value[mode];
        if (!$showMode) {
            show_array($value);
            $showMode = "simple";
        }

        if ($code) {
            $hour = substr($code,0,2);
            $min = substr($code,3,2);
            $sec = substr($code,6,2);
        }

        switch ($showMode) {
            case "simple" : {
                 echo ("<input type='text' name='time_".$key."_hour' value='$hour' style='width:20px;' $editClass $readOnly $disabled  > ");
                 echo ("<input type='text' name='time_".$key."_min' value='$min' style='width:20px;' $editClass $readOnly $disabled  > ");
                 echo ("<input type='hidden' name='time_".$key."_sec' value='$sec' style='width:20px;' $editClass $readOnly $disabled  >");
                 // echo ($day.".".$month.".".$year);
                 break;
            }
        }
        // echo ("Mode = $showMode ");
        // echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_time_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_dateTime($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_dateTime_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showMode = $value[mode];
        if (!$showMode) {
            // show_array($value);
            $showMode = "simple";
        }

        if ($code) {
            $day = substr($code,8,2);
            $month = substr($code,5,2);
            $year = substr($code,0,4);

            $hour = substr($code,11,2);
            $min = substr($code,14,2);
            $sec = substr($code,17,2);
        } else {
            echo ("nicht vorhanden");
            $break = 1;
            return $break;
        }

        switch ($showMode) {
            case "text" :
                $ts = mktime($hour, $min, $sec, $month, $day, $year);
                $weekDay = date("w",$ts);
                $weekDay = cmsDates_dayStr($weekDay);
                echo ($weekDay.", ".$day.".".$month.".".$year." ".$hour.":".$min);
                break;

            case "simple" : 
                echo ("<input type='text' name='date_".$key."_day' value='$day' style='width:20px;' $editClass $readOnly $disabled  > ");
                echo ("<input type='text' name='date_".$key."_month' value='$month' style='width:20px;' $editClass $readOnly $disabled  > ");
                echo ("<input type='text' name='date_".$key."_year' value='$year' style='width:40px;' $editClass $readOnly $disabled  >");

                echo ("<input type='text' name='time_".$key."_hour' value='$hour' style='width:20px;' $editClass $readOnly $disabled  > ");
                echo ("<input type='text' name='time_".$key."_min' value='$min' style='width:20px;' $editClass $readOnly $disabled  > ");
                echo ("<input type='hidden' name='time_".$key."_sec' value='$sec' style='width:20px;' $editClass $readOnly $disabled  >");
                 // echo ($day.".".$month.".".$year);
                break;
            
        }
        // echo ("Mode = $showMode ");
        // echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_dateTime_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_changeLog($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_changeLog_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showMode = $value[mode];
        if (!$showMode) {
            $showMode = "simple";
        }

        $changeList = explode ("|",$code);

        if (count($changeList)==0) {
            echo ("Keine ProtokollDaten ");
             $break = 1;
            return $break;
        }
        div_start("changeLogList","width:".($width-2)."px;border:1px solid #eee;display:inline-block;");


       
        for ($i=0;$i<count($changeList);$i++) {
            $change = explode(",",$changeList[$i]);
            if (count($change)==3) {
                div_start("changeLogList_Line","width:".($width-2)."px;");
                $mod = $change[0];
                $userId = $change[1];
                $mode = $change[2];

                $day = substr($mod,8,2);
                $month = substr($mod,5,2);
                $year = substr($mod,0,4);

                $hour = substr($mod,11,2);
                $min = substr($mod,14,2);
                $sec = substr($mod,17,2);

                $ts = mktime($hour, $min, $sec, $month, $day, $year);
                $weekDay = date("w",$ts);
                $weekDay = cmsDates_dayStr($weekDay);
                div_start("changeLogList_Date","float:left;width:200px;");//.($width-210)."px;");
                echo ($weekDay.", ".$day.".".$month.".".$year." ".$hour.":".$min);
                div_end("changeLogList_Date");


                $userData = cmsUser_getById($userId);
                $userName = "unbekannt $userId";
                if (is_array($userData)) {
                    if ($userData[vName] AND $userData[nName]) $userName = $userData[vName]." ".$userData[nName];
                    else {
                        if ($userData[userName]) $userName = $userData[userName];
                    }
                }
                
                div_start("changeLogList_User","float:left;width:150px;");
                echo ($userName);
                div_end("changeLogList_User");       
                div_start("changeLogList_Mode","float:left;width:100px;");


                switch ($mode) {
                    case "edit" : echo "bearbeitet"; break;
                    case "new" : echo "angelegt"; break;
                    case "import" : echo "importiert"; break;
                    default :
                        echo ("$mode");
                }
                div_end("changeLogList_Mode");
                div_end("changeLogList_Line","before");
            }
        }
        echo ("<input type='hidden' name='$dataName' value='$code' style='width:100%' >");
        div_end("changeLogList");
        $break = 1;
        return $break;
    }
    function editShowInput_changeLog_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}



    function editShowInput_imageSelect($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_imageSelect_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        // Standard Bild
        global $cmsVersion;

        div_start("cmsImageInput","display:inline-block;width:".$width."px;");
        $img = "<img src='cms_".$cmsVersion."/images/image.gif' alt='Bild wählen'width='30px' height='30px' class='cmsImageSelect'>";

        echo ("Image is = $code <br />");

        if (intval($code)>0) { 
            $imageId = intval($code);
        } else {
            $imageList = explode("|",$code);
            $imageId = intval($imageList[1]);
        }
        if ($imageId > 0) {
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $imageWidth = $value[imgWidth];
                $imageHeight = $value[imgHeight];
                if (!$imageWidth) $imageWidth = 100;
                if (!$imageHeight) $imageHeight = 100;
                $showImageData = array("class"=>"cmsImageSelect","frameWidth"=>$imageWidth,"frameHeight"=>$imageHeight,"vAlign"=>"top","hAlign"=>"left");
                $img = cmsImage_showImage($imageData,100,$showImageData);
            }
        }
        echo($img."&nbsp;<br />");
        echo ("<input type='hidden' class='cmsImageId' style='width:30px;' name='$dataName' value='$code' >");
        $imageFolder = "images/";
        if ($value[imageFolder]) {
            $addFolder = $value[imageFolder];

            if ($addFolder[0] == "/") $addFolder = substr($addFolder,1);
            if ($addFolder[strlen($addFolder)-1] != "/") $addFolder.= "/";
            $imageFolder .= $addFolder;
        }
        if ($value[imageUpload]) {
            echo("<input name='uploadImage' type='file' size='50' onChang='submit()'  >"); //  maxlength='10000000'
            echo("<input name='uploadFolder' type='text' size='50' class='cmsImagePathSelector' readonly='readonly'  value='$imageFolder' >");
        }

        $divName = "cmsImageSelector";
        $divData = array();
        $divData[style] = "height:0px;width:".$width."px;background-color:#bbb;visible:none;overflow:hidden;";
        $divData["folderName"] = $imageFolder;
       
        div_start($divName,$divData);
        echo (cmsImage_selectList($imageFolder,0));
        div_end($divName);
        $imageFolder = "/";

        
        div_end("cmsImageInput");
       
        ///show_array($value);
        $break = 1;
        return $break;
    }
    function editShowInput_imageSelect_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}

    function editShowInput_imageSelectList($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_imageSelectList_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        // show_array($value);
        // show_array($value);

        $showData = array();
        $showData[dataName] = $dataName;
        $showData[width] = $width;
        $showData[height] = $height;
        $showData[imgHeight] = $value[imgHeight];
        $showData[imgWidth] = $value[imgWidth];
        $showData[imgHeight] = $value[imgHeight];
        

        $showData[lineCount] = 3;
        if ($value[lineCount] AND $value[imgHeight]) {
            $showData[lineCount] = $value[lineCount];
            $showData[height] = $value[lineCount] * ($value[imgHeight]+4);
            // echo ("Height is $showData[height] <br /> ");
        }
        
        $showData[imageAdd] = 1;
        
       
        if ($value[imageUpload]) {
             $showData[imageUpload] = $value[imageUpload];

        }
        $imageFolder = "images/";
        
        if ($value[imageFolder]) {
            $addFolder = $value[imageFolder];
            if ($addFolder[0] == "/") $addFolder = substr($addFolder,1);
            if ($addFolder[strlen($addFolder)-1] != "/") $addFolder.= "/";
            $imageFolder .= $addFolder;
        }
        $showData[imageFolder] = $imageFolder;
        $showData["imageSortAble"] = 1;
        $showData["imageDeleteAble"] = 1;
        $out = $this->editContent_imageList($code,$showData);
        echo ($out);
        ///show_array($value);
        $break = 1;
        return $break;
    }
    function editShowInput_imageSelectList_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}



    function editShowInput_dropdown($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_dropDown_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showData   = $value[showData]; //array("class"=>"adminCompany_Category","show"=>1,"style"=>"width:".$width."px;");
        $showData["style"] .= $editStyle;
        
        $showFilter = $value[showFilter]; //array("mainCat"=>0,"show"=>1);
        $showSort   = $value[showSort];

        if ($readOnly) $showData[readonly] = 1;
        if ($disabled) $showData[disabled] = 1;
        
        
        $dataSource = $key;
        if ($value[dataSource]) $dataSource = $value[dataSource];


        switch ($dataSource) {
            case "category" :
                $out = cmsCategory_selectCategory($code,$dataName,$showData,$showFilter,$showSort);
                break;
            case "location" :
                $out = cmsLocation_selectLocation($code,$dataName,$showData,$showFilter,$showSort);
                break;
            case "company" :
                $out = cmsCompany_selectCompany($code,$dataName,$showData,$showFilter,$showSort);
                break;
            case "userLevel" :
                $out = cmsUser_selectUserLevel($code,$dataName,$showData,$showFilter,$showSort);
                break;
            case "salut" :
                $out = cmsUser_selectSalut($code,$dataName,$showData,$showFilter,$showSort);
                break;

            default :
                $out = "unkown '$key' in show $editType <br />";
        }
        echo ($out);
        $break = 1;
        return $break;
    }
    function editShowInput_dropdown_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}

    function editShowInput_toggle($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_toggle_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);

        $showData   = $value[showData]; //array("class"=>"adminCompany_Category","show"=>1,"style"=>"width:".$width."px;");
        $showData[width] = $width;
        // echo ("Set showData[width] to $width <br />");
        $showFilter = $value[showFilter]; //array("mainCat"=>0,"show"=>1);
        $showSort   = $value[showSort];

        $dataSource = $key;
        if ($value[dataSource]) $dataSource = $value[dataSource];

        switch ($dataSource) {
            case "category" :
                if ($showFilter[mainCat] == "subCat") {
                    // show_array($value);
                    // echo ("SHOW SUBVCAT<br>");
                }
                
                
                
                
                
                //$out = "Kategoriy";
                $out = cmsCategory_selectCategory_toogle($code,$dataName,$showData,$showFilter,$showSort);
                break;
             case "region" :
                $out = cmsCategory_selectCategory_toogle($code,$dataName,$showData,$showFilter,$showSort);
                break;

            case "company" :
                $out = cmsCompany_selectCompany_toggle($code,$dataName,$showData,$showFilter,$showSort);
                break;

            default :
                if (is_array($dataSource)) {
                    // echo ("toggle with DataSource <br />");
                    $out = $this->toggle_select($code, $dataName, $showData, $dataSource);
                } else {
                    $out = "unkown '$key' in show toggle '$editType' <br />";
                }
        }
        echo ($out);

        $break = 1;
        return $break;
    }
    function editShowInput_toggle_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}


    function editShowInput_autoComplete($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_autoComplete_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];


        

        // echo (" type='hidden' id='$key' readonly='readonly' name='$dataName' value='$code' <br />");

       

        echo ($titleStr);
        $showData   = $value[showData]; //array("class"=>"adminCompany_Category","show"=>1,"style"=>"width:".$width."px;");
        $showData[style] .= $editStyle;

        if ($_SESSION[showLevel]>7) $showData[style] = "width:".($width-60)."px;";  //
         
        $showFilter = $value[showFilter]; //array("mainCat"=>0,"show"=>1);
        $showSort   = $value[showSort];
        $inputDataName = $key."Name";

        if ($readOnly) $showData[readonly] = $readOnly;
        if ($disabled)  $showData[disabled] = $disabled;

        $dataSource = $key;
        if ($value[dataSource]) $dataSource = $value[dataSource];
        
        switch ($dataSource) {
            case "location" :
                $out = cmsLocation_selectLocation_auto($code,$inputDataName,$showData,$showFilter,$showSort);
                break;
            case "category" :
                $out = cmsCategory_selectCategory_auto($code,$inputDataName,$showData,$showFilter,$showSort);
                break;
            case "company" :
                $out = cmsCompany_selectCategory_auto($code,$inputDataName,$showData,$showFilter,$showSort);
                break;
            default :
                $out = "unkown '$key' in show $editType <br />";
        }
        echo ($out);

        if ($_SESSION[showLevel]>7) { // superAdMin
            // echo (span_text_str($value[name]."-Id:",$leftWidth));
            echo ("<input type='text' id='".$key."Id' class='adminDates_".$key."Id' style='width:50px;' readonly='readonly' name='$dataName' value='$code'><br />");

        } else {
            echo ("<input type='hidden' id='".$key."Id' class='adminDates_".$key."Id' readonly='readonly' name='$dataName' value='$code'>");
        }
       

        $break = 1;
        return $break;
    }
    function editShowInput_autoComplete_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}

    function editShowInput_special($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {
        $ownData = $this->editShowInput_special_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth);
        if (is_array($ownData)) return $ownData["break"];
        echo ($titleStr);
        echo ("<input type='text' name='$dataName' value='$code' style='$editStyle' $editClass $idStr $readOnly $disabled  >");
        $break = 1;
        return $break;
    }
    function editShowInput_special_own($key,$code,$dataName,$value,$titleStr,$editStyle,$editClass,$idStr,$readOnly,$disabled,$width,$height,$leftWidth) {}



/// BUTTONS

    function editShow_Buttons($buttonList,$mainButtons=0) {
        if ($mainButtons) {
            echo ("<div style='width:300px;height:0px;overflow:hidden;' >");
        }
        
        if ($_SESSION[userLevel]== 9) {
            $link = cms_page_goPage("view=editShow");
            $buttonList[editShow] = array("type"=>"link","name"=>"Eingabe bearbeiten","link"=>$link,"class"=>"cmsInputButton cmsSecond");
        }
        
        
        foreach($buttonList as $key => $value) {
            $disabled = "";
            $show = 1;
            if (!is_null($value[show])) $show = $value[show];

            if ($show) {
                switch ($value[type]) {
                    case "submit" :
                        if ($value[disabled]) $disabled = "disabled=disabled";
                        if (!$mainButtons) {
                            echo("<input type='submit' $disabled class='".$value["class"]."' name='$value[value]' value='$value[name]'>");
                        } else {
                            if ($value[mainButton]) {
                                echo("<input type='submit' $disabled class='".$value["class"]."' name='$value[value]' value='$value[name]'>");
                            }
                        }
                        break;
                    case "link" :
                        if (!$mainButtons) {
                            echo ("<a href='$value[link]' class='".$value["class"]."' >$value[name]</a>");
                        }
                        break;

                    default :
                        echo ("unkown Type in $key = '$value[type] <br />");
                }
            }
        }
         if ($mainButtons) {
            echo ("</div>");
        }
      
    }


    function specialPostList($data,$tableName=0) {
        $specialData = array();

        foreach ($data as $key => $value ) {
            switch ($key) {
                case "deleteData" :
                    $goPage = $this->goPage(array("deleteData"=>1));
                    $deleteId = $_POST[saveData][id];
                    $out = "Daten wirklich löschen ?<br />"; // $goPage";
                    $out .= $this->deleteMoreText($_POST);
                    $out .= "<form method='post'>";
                    $out .= "<input type='hidden' value='$deleteId' name='deleteId'>";
                    $out .= "<input type='submit' class='cmsInputButton cmsSecond' name='doDelete' value='JA' >";
                    $out .= "<a href='".$goPage."' class='cmsLinkButton' >NEIN</a> ";
                    $out .= "</form>";
                    //$out .= "<br /><a href='".$goPage."&doDelete=$deleteId'>JA</a> ";                    
                    echo cms_infoBox($out);
                    break;

                case "doDelete" : echo ("<h1>$key = $value </h1>"); break;
                case "deleteId" : 
                    echo ("<h1>$key = $value </h1>");
                    if ($data[doDelete]=="JA" AND $value>0) {
                        $deleteId = $value;
                       // echo "Delete from Data width id = $deleteId <br />";
                        $goPage = $this->goPage(array("doDelete"=>1,"deleteId"=>1,"view"=>1,"id"=>1));
                        //echo ("goPage after delete = $goPage <br />");
                        $this->cacheRefresh($deleteId, $_POST[saveData], "delete");
                        if ($tableName) {
                            $query = "DELETE FROM `$GLOBALS[cmsName]_cms_".$tableName."` Where `id`=$deleteId; ";
                            $result = mysql_query($query);
                            if ($result) {
                                cms_infoBox("Daten gelöscht");
                                reloadPage($goPage,3);
                                die;
                            } else {
                                cms_errorBox("Fehler beim Datenb löschen <br />$query");
                            }
                            
                        } else {
                            echo "<h1>No TableName in specialPostList</h1>";
                            $specialData[deleteId]=$deleteId;
                        }
                    }

                    break;

                case "saveAdress" :
                    //echo ("saveAdress $key = $value <br />");
                    $specialData[$key] = $value;
                    //echo ("<h1>specialData $specialData</h1>");
                    //show_array($specialData);
                    // echo ("ready<br />");
                    break;
                    
                case "saveData" : break;
                  
                case "cancelSave" : break;
                case "editSave" : break;
                case "duplicate" : break;
                case "uploadImage" : break;
                case "uploadFolder" ;
                    $anz = count($_FILES[uploadImage]);
                    if ($anz) {
                        $name = $_FILES[uploadImage][name];
                        if ($name) {
                            // show_array($_FILES[uploadImage]);

                            // echo ("FILES COUNT $anz <br />");
                            $imageId = $this->admin_uploadImage();
                            // echo ("Image ID = $imageId <br />");
                            $imageId = intval($imageId);
                            if ($imageId) {
                                $specialData[imageId] = $imageId;
                            } else {
                                // echo ("No Integer $imageId!!!!<br />");
                            }
                        }
                    }
                    break;
                case "moveDownInList" :
                    if (is_array($value)) {
                        $imageStr = $data[saveData][image];
                        $newImageListStr = cmsImage_imageList_action("down",$value,$imageStr);
                        $specialData["imageListStr"] = $newImageListStr;
                    }
                    break;
                case "moveUpInList" :
                    if (is_array($value)) {
                        $imageStr = $data[saveData][image];
                        $newImageListStr = cmsImage_imageList_action("up",$value,$imageStr);
                        $specialData["imageListStr"] = $newImageListStr;
                    }
                    break;
                case "deleteFromList" :
                    if (is_array($value)) {
                        $imageStr = $data[saveData][image];
                        //echo ("imageStr =$imageStr <br />");
                        $newImageListStr = cmsImage_imageList_action("del",$value,$imageStr);
                        $specialData["imageListStr"] = $newImageListStr;
                    }
                    break;
                case "imageAdd" :
                    if ($value) {
                        $imageStr = $data[saveData][image];
                       //  echo ("<h3>imageAdd = '$value' </h3>");
                        $newImageListStr = cmsImage_imageList_action("add",$value,$imageStr);
                        $specialData["imageListStr"] = $newImageListStr;

                    }
                    break;

                case "categoryName" :
                    $categoryName = $value;
                    $catData = cmsCategory_get(array("name"=>$categoryName."%"));
                    if (is_array($catData)) {
                        $categoryId = $catData[id];
                        $specialData[categoryId] = intval($categoryId);
                        $specialData[categoryName] = $catData[name];
                    }
                    break;

                 case "locationName" :
                    $locationName = $value;
                    $locationData = cmsLocation_get(array("name"=>$locationName."%"));
                    // echo ("Get LocationName is $value $locationData<br />");
                    if (is_array($locationData)) {
                        $locationId = $locationData[id];
                        $specialData[locationId] = intval($locationId);
                        $specialData[locationName] = $locationName;
                    }
                    break;

                case "regionName" :
                    $regionName = $value;
                    $regionData = cmsCategory_get(array("name"=>$regionName."%"));
                    // echo ("Get LocationName is $value $locationData<br />");
                    if (is_array($regionData)) {
                        $regionId = $regionData[id];
                        $specialData[regionId] = intval($regionId);
                        $specialData[regionName] = $regionName;
                    }
                    break;


                case "link" :  $specialData[$key] = $value; break;      
                case "dateLinkString" :  $specialData[$key] = $value; break;
                case "linkDate" :
                    $specialData[linkDate] = $value;
                    break;
                case "addSubDate" :
                    $specialData[addSubDate] = $value;
                    break;

                case "articleLinkString" :  $specialData[$key] = $value; break;
                case "linkArticle" :
                    $specialData[linkArticle] = $value;
                    break;
                


                default :
                    $unkownMode = "";
                    if (substr($key,0,5)=="time_") $unkownMode = "time";
                    if (substr($key,0,5)=="date_") $unkownMode = "date";
                    if (substr($key,0,11) == "delSubDate_") $unkownMode = "delSubDate";
                    
                    switch ($unkownMode) {
                        case "delSubDate" :
                            $delSubId  = substr($key,11);
                            // echo ("DELETE SUB DATE WITH ID $delSubId <br />");
                            $specialData[delSubDate] = $delSubId;
                            break;

                        case "date" :
                            $dateCode = explode("_",$key);
                            $code = $dateCode[1];
                            $type = $dateCode[2];
                            if ($type == "day") {
                                $day   = intval($data["date_".$code."_day"]);
                                $month = intval($data["date_".$code."_month"]);
                                $year  = intval($data["date_".$code."_year"]);
                                if ($day < 1 and $day > 31) $day = 0;
                                if ($month < 1 and $month > 12) $month = 0;
                                if ($year < 0 and $year > 2200) $year = 0;
                                if ($day AND $month AND $year) {
                                    if ($day<10) $day="0".$day;
                                    else $day = "".$day;
                                    if ($month<10) $month="0".$month;
                                    else $month = "".$month;
                                    if ($year < 100) {
                                        if ($year < 30) $year = 2000+$year;
                                        else $year = 1900 + $year;
                                    }
                                    $date = "".$year."-".$month."-".$day;
                                    $specialData[$code] = $date;
                                    // echo ("Datum von $code = $date <br />");
                                } else {
                                    if (!$day ) $day = "  ";
                                    else {
                                        if ($day<10) $day="0".$day;
                                        else $day = "".$day;
                                    }
                                    if ($month<10) $month="0".$month;
                                    else $month = "".$month;
                                    if ($year < 100) {
                                        if ($year < 30) $year = 2000+$year;
                                        else $year = 1900 + $year;
                                    }
                                    $date = "".$year."-".$month."-".$day;
                                    $specialData[$code] = $date;

//                                    echo ("getDay width date_".$code."_day ->".$data["date_".$code."_day"]."=$day<br /> ");
//                                    echo ("getMonth width date_".$code."_month ->".$data["date_".$code."_month"]."=$month<br /> ");
//                                    echo ("getYear width date_".$code."_year ->".$data["date_".$code."_year"]." =$year<br /> ");
                                }
                            } else {
                                // echo ("dont use $code $type = $value <br />");
                            }
                            break;
                            
                        case "time" :
                            $timeCode = explode("_",$key);
                            $code = $timeCode[1];
                            $type = $timeCode[2];
                            if ($type == "hour") {
                                $hour   = intval($data["time_".$code."_hour"]);
                                $min = intval($data["time_".$code."_min"]);
                                $sec  = intval($data["time_".$code."_sec"]);
                                if ($hour < 0 and $hour > 24) $hour = -1;
                                if ($min < 0 and $min > 60) $min = -1;
                                if ($sec < 0 and $sec > 60) $sec = -1;
                                if ($hour>=0 AND $min>=0 AND $sec>=0) {
                                    if ($hour<10) $hour="0".$hour;
                                    else $hour = "".$hour;
                                    if ($min<10) $min="0".$min;
                                    else $min = "".$min;
                                    if ($sec<10) $sec="0".$sec;
                                    else $sec = "".$sec;
                                    
                                    $time = "".$hour.":".$min.":".$sec;
                                    $specialData[$code] = $time;
                                    // echo ("Uhrzeit von $code = $time <br />");
                                } else {
                                    echo ("getHour width time_".$code."_hour ->".$data["time_".$code."_hour"]."=$hour<br /> ");
                                    echo ("getMinute width time_".$code."_min ->".$data["time_".$code."_min"]."=$min<br /> ");
                                    echo ("getSecond width time_".$code."_sec ->".$data["time_".$code."_sec"]." =$sec<br /> ");
                                }
                            } else {
                                // echo ("dont use $code $type = $value <br />");
                            }
                            break;

                        default;
                            echo "<h1>unkownValue in specialPostList #$key = '$value' </h1>";
                    }
                
            }
        }
        // show_array($specialData);
        return $specialData;
    }
    
    function deleteMoreText($saveData) {
        return "";
    }


    function backLink() {
        if (!is_array($_SESSION[lastPages])) $_SESSION[lastPages] = array();#
        $anz = count($_SESSION[lastPages]);
        if ($anz > 1) {
            $lastUrl = $_SESSION[lastPages][$anz-2];
            return $lastUrl;
        }
       
    }


    
    function editButtons($saveData) {






        $buttonList = array();
        
        $addData = array();
        $addData[type] = "submit";
        $addData["class"] = 'cmsInputButton';
        $addData[name] = "Speichern";
        $addData[value] = "editSave";
        $addData[mainButton] = 1;
        $buttonList[save] = $addData;
        
        $addData = array();
        $addData[type] = "submit";
        $addData["class"] = 'cmsInputButton cmsSecond';
        $addData[name] = "abbrechen";
        $addData[value] = "cancelSave";
        $buttonList[cancel] = $addData;   
        
        if ($saveData[id]) {
            $addData = array();
            $addData[type] = "submit";
            $addData["class"] = 'cmsInputButton cmsSecond';
            $addData[name] = "löschen";
            $addData[value] = "deleteData";
            $buttonList[delete] = $addData;  
        }
        
        
        $buttonList = $this->editButtons_own($buttonList,$saveData);
//        foreach ($ownButtonList as $key => $value) {
//            $buttonList[$key] = $value;
//        }
        
        
        return $buttonList;
        
    }
    
    function editButtons_own($buttonList,$saveData) {
        
        
        return $buttonList;
        
    }

    function checkError($saveData,$editShow) {
        $error = array();
        foreach ($saveData as $key => $value) {
            $type = $editShow[$key][type];
            $needed = $editShow[$key][needed];
            // echo ("Check $key with $value -> type = $type NNEEDED = $needed <br />");
            $needError = "Keine Daten für $key $value $type";
            if ($needed) {
                // echo ("Check $key with $value -> type = $type <br />");
                $needError = $editShow[$key][needError];
                if (!$needError) $needError = "Keine Daten für $key $value $type";
                switch ($needed) {
                    case "date" :
                        list($year,$month,$day) = explode("-",$value);
                        $errorDate = 1;
                        if ($year > 2000) {
                            if ($month >= 1 AND $month <= 12) {
                                if ($day>=1 AND $day <= 31) $errorDate = 0;
                            }
                        }
                        if ($errorDate) {
                            $error[$key] = $needError;
                        }
                        // echo ("DATTEEE $key => $value <br>");
                        break;
                    case "time" : 
                        list($h,$m,$s) = explode("-",$value);
                        if ($h+$m == 0) {
                             $error[$key] = $needError;
                        }
                        break;
                        
                    case "textContent":
                        // echo "Check Content $value <br />";
                        if (is_string($value) AND strlen($value)>0) {
                            //  echo "All OK !<br />";
                        } else {
                            $error[$key] = $needError;
                        }
                        break;
                        
                        
                    case "1":
                        if ($type == "float") { 
                            $floatStr = $value;
                            $komma = $editShow[$key][komma];
                            if (!$komma) $komma = ",";
                            $deli1000 = $editShow[$key]["1000"];
                            if (!$deli1000) $deli1000 = ".";

                            // remove 1000er 
                            $floatStr = str_replace($deli1000,"",$floatStr);
                            
                            // replace DECI
                            if ($komma != ".") {
                                $floatStr = str_replace($komma,".",$floatStr);                            
                            }
                            $value = floatval($floatStr);
                        }
                        
                        
                        // echo "Check Content $value <br />";
                        if (is_string($value) AND strlen($value)>0) {
                            if ($value == "0") {
                                switch ($type) {
                                    case "dropdown"     : $error[$key] = $needError; break;
                                    case "autoComplete" : $error[$key] = $needError; break;
                                    case "toggle"       : $error[$key] = $needError; break;
                                    case "imageSelect"  : $error[$key] = $needError; break;
                                }
                                // echo ("Result = 0 Type =$type <br />");
                                // show_array($editShow[$key]);
                            } else {
                                // echo "All OK $value !<br />";
                            }
                        } else {
                            if ($value) {
                                // echo ("checkValue $value for $type <br>");
                            } else {
                                $error[$key] = $needError;
                            }
                        }
                       
                        break;

                    default :
                        echo ("unkown $needed $key => $value ");
                        $error[$key] = $needError;
                }
                
            }

            
        }
        
        if ($error[location]) {            
            if ($saveData[locationStr]) {                
                // echo ($saveData[locationStr]."<br>");
                unset($error[location]);
            } else {
                echo "No Location <br>";
            }
            
        }
        
        
        $error = $this->checkError_own($error,$saveData,$editShow);
        
        
        return $error;
    }

    function checkError_own($error,$saveData,$editShow) {
        return $error;
    }


}





?>
