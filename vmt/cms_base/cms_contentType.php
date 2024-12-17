<?php // charset:UTF-8


class cmsType_contentTypes_base {

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
            echo ("Function cmsType_".$type."_class not exist <br /> ");
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
                    echo ("Dunction cmsType_".$type."_class not exist <br /> ");
                }
                
               // echo ("case '".$type."' : typeName = ''; break;<br />");
                $typeName = "AutoName ".$type;
        }
        return $typeName;
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
        $res[] = $addData;
        switch ($viewMode) {
            case "list" :
                $addData["text"] = "Kopfzeile ";
                if ($data[titleLine]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][titleLine]' $checked value='1' >\n";
                $res[] = $addData;

                $addData["text"] = "Liste in Seiten aufteilen";
                if ($data[pageing]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageing]' $checked value='1' >\n";
                $res[] = $addData;

                $addData["text"] = "Anzahl pro Seite";
                $addData["input"] = "<input name='editContent[data][pageingCount]' style='width:100px;' value='".$data[pageingCount]."'>";
                $res[] = $addData;

                $addData["text"] = "Seitennavigation oben";
                if ($data[pageingTop]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageingTop]' $checked value='1' >\n";
                $res[] = $addData;

                $addData["text"] = "Seitennavigation unten";
                if ($data[pageingBottom]) $checked = "checked='checked'"; else $checked = "";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pageingBottom]' $checked value='1' >\n";
                $res[] = $addData;
                break;

            case "table" :
                $addData["text"] = "Anzahl in Reihe";
                if (!$data[imgRow]) $data[imgRow] = 3;
                $input  = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$data[imgRow]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Abstand in Reihe";
                if (!$data[imgRowAbs]) $data[imgRowAbs] = 10;
                $input  = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$data[imgRowAbs]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Reihen höhe";
                $input  = "<input name='editContent[data][imgColHeight]' style='width:100px;' value='".$data[imgColHeight]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Abstand Zeilen";
                if (!$data[imgColAbs]) $data[imgColAbs] = 10;
                $input  = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$data[imgColAbs]."'>";
                $addData["input"] = $input;
                $res[] = $addData;

                $addData["text"] = "Maximale Projekttanzahl";
                $input  = "<input name='editContent[data][maxCount]' style='width:100px;' value='".$data[maxCount]."'>";
                $addData["input"] = $input;
                $res[] = $addData;
                break;

            case "slider" :
                $addData = array();
                $addData["text"] = "Wechsel";
                $direction = $editContent[data][direction];
                $input  = $this->slider_direction_select($direction,"editContent[data][direction]",array());
                $addData["input"] = $input;
                $res[] = $addData;


                $addData = array();
                $addData["text"] = "Auto Loop";
                $loop = $editContent[data][loop];
                $checked = "";
                if ($loop) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][loop]' $checked >";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Bild in ms";
                $addData["input"] = "<input name='editContent[data][pause]' style='width:100px;' value='".$editContent[data][pause]."'>";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Zeit für Wechsel in ms";
                $addData["input"] = "<input name='editContent[data][speed]' style='width:100px;' value='".$editContent[data][speed]."'>";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Navigation";
                $navigate = $editContent[data][navigate];
                $checked = "";
                if ($navigate) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][navigate]' $checked >";
                $res[] = $addData;

                $addData = array();
                $addData["text"] = "Einzelauswahl";
                $pager = $editContent[data][pager];
                $checked = "";
                if ($pager) $checked = " checked='checked'";
                $addData["input"] = "<input type='checkbox' name='editContent[data][pager]' $checked >";
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


    function editContent_filterView($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
        $res = array();
        
        $pageData = $GLOBALS[pageData];
        $dynamic = $pageData[dynamic];
        if ($dynamic) {
            $addData = array();
            $addData["text"] = "Dynamische Projektliste";
            $dynamicProject = $data[dynamicProject];
            if ($dynamicProject) $checked = "checked='checked'";
            else $checked = "";
            $addData["input"] = "<input type='checkbox' value='1' $checked name='editContent[data][dynamicProject]' />";
            $res[] = $addData;
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
            $res[$key] = $value;
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
        // echo "filter_select $filterType<br />" ;
        if ($showData[filter]) $filter = $showData[filter];
        if ( $showData[sort])  $sort =   $showData[sort];
        // echo ("FILTER $filter SORT = $sort <br />");
        $selectList = $this->filter_select_getList($filterType,$filter,$sort="");
        $str = "";
        if (is_array($selectList) AND count($selectList)) {
           
            //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
            $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
            if ($showData[submit]) $str.= "onChange='submit()' ";
            $str .= "value='$code' >";

            $emptyStr = "Kein Filter";
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
        // echo "filter_select_getList $filterType<br />" ;

        switch ($filterType) {
            case "style" :
                $res = $this->styleList_filter_select_getList($filter);
                break;
            case "headline" :
                $res = $this->styleList_filter_select_getList($filterType);
                break;
            case "text" :
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
        return $res;
    }

    function filter_select_getList_own($filterType,$filter,$sort) {}

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
                $res = array ("left"=>"Links-Bündig","center"=>"Zentriert","right"=>"Rechts-Bündig");
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
        foreach ($goPageList as $key => $value) {
            if ($goPage == "") $goPage.= "?";
            else $goPage .= "&";
            $goPage .= "$key=$value";
        }


        if ($goPage == "") $goPage.= "?";
        else $goPage .= "&";
        $goPage .= "view=edit&id=$data[id]";



        $str.= "<a href='$goPage' >";
        $divName = "cmsShowDataLine";
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        $mainHeight = $showList["image"]["height"];
        if ($showData[height]) $mainHeight = $showData[height];
        
        if ($mainHeight) $divData[style] .= "height:".$mainHeight."px;"; //line-height:".$height."px;";
        $divData[id] = $data[id];
        $str.= div_start_str($divName,$divData);

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


                    default :
                        switch ($type) {
                            case "checkbox" :
                                if ($data[$key]) $cont = "1";
                                else $cont = "0";
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
    /// end of S H O W L I S T                                               ///
    ////////////////////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////////////////////////////
    /// S H O W L I S T  -  F I L T E R                                      ///
    ////////////////////////////////////////////////////////////////////////////
    function showList_customFilter($contentData,$frameWidth) {
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
        
        
        $str = div_start_str("cmsCustomFilterList","width:".($frameWidth-2)."px;");

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
        $str .= "<input type='submit' class ='cmsInputButton' value='suchen' name='search' />";
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
        $str .= div_end_str("cmsCustomFilterList");
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
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_social.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_company.js' type='text/javascript'></script>\n");
    echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_dateList.js' type='text/javascript'></script>\n");
    //echo("<script src='/cms_".$cmsVersion."/cms_contentTypes/cmsType_location.js'></script>");

   // echo("<script src='/cms_".$cmsVersion."/autosuccest.js'></script>");

    
    // echo ("<script src='/cms_".$cmsVersion."/autosuccest.js'></script>");
    

}


function cms_contentType_getTypes() {
    $contentType_class = cms_contentTypes_class();
    $typeList = array();
    /*  $typeList[text]   = array("type"=>"text","name"=>"Überschrift und Text");
    $typeList[textImage] = array("type"=>"textImage","name"=>"Text mit Bild");
    $typeList[image]  = array("type"=>"image","name"=>"Bild","id"=>1);
    $typeList[frame1] = array("type"=>"frame1","name"=>"1 Spalte");
    $typeList[frame2] = array("type"=>"frame2","name"=>"2 Spalten");
    $typeList[frame3] = array("type"=>"frame3","name"=>"3 Spalten");
    $typeList[frame4] = array("type"=>"frame4","name"=>"4 Spalten");

    $typeList[contentName] = array("name"=>"Verfügbarer Inhalt");

    $typeList[flip] = array("type"=>"flip","name"=>"Inhalt wechseln");
    $typeList[dateList] = array("type"=>"dateList","name"=>"Termin-Liste");
    

    $typeList[login] = array("name"=>"Anmelden");
    $typeList[social] = array("name"=>"Sociale Dienste");
    $typeList[ownPhp] = array("name"=>"Eigene Scripte");*/

    global $cmsSettings;
   //  show_array($GLOBALS[cmsTypes]);
    foreach ($GLOBALS[cmsTypes] as $key => $value ) {

        $type = substr($key,8,strlen($key)-8-4);

        if ($type == "frame") {
            if (function_exists("cmsType_".$type)) $existShow = 1;
            if (function_exists("cmsType_".$type."_editContent")) $existEdit = 1;
            if ($existShow AND $existEdit) {
                for ($i=1;$i<=4;$i++) {
                    $type = "frame".$i;


                    if (is_array($cmsSettings[useType])) $use = $cmsSettings[useType][$type];
                    else $use = $contentType_class->useType($type);
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


            if (!$typeList[$type]) {
                if (function_exists("cmsType_".$type)) $existShow = 1;
                if (function_exists("cmsType_".$type."_editContent")) $existEdit = 1;
                if ($existShow AND $existEdit) {

                    //show_array($cmsSettings[useType]);
                    if (is_array($cmsSettings[useType])) $use = $cmsSettings[useType][$type];
                    else $use = $contentType_class->useType($type);
                    if ($use) {
                        $name = "Auto $type";
                        if (function_exists("cmsType_".$type."_getName")) {

                            $name = call_user_func("cmsType_".$type."_getName", $contentData,$frameWidth);
                            // echo ("Function exist ".cmsType."_".$type."_getName ==> '$name' <br />");
                        } else {
                            $name = $contentType_class->typeName($type);
                        }
                        $typeList[$type] = array("name"=>"$name","type"=>$type,"use"=>$use);
                        // echo ("$type = $name <br>");
                    }


                } else {
                    echo ("not Exist $type $existShow $existEdit <br />");
                }
                
                    //cmsType_contactForm($contentData,$frameWidth) {
                    // cmsType_contactForm_editContent($editContent) {
    //                  echo ("Module $key $type show=$existShow edit=$existEdit <br />");
            }
        }
    }

    asort($typeList);

    return ($typeList);
}

function cms_contentType_getSortetList() {
    $typeList = cms_contentType_getTypes();

    $res = array();
    $res[page] = array();
    $res[layout] = array();
    $res[data] = array();

    foreach ($typeList as $key => $value) {
        $target = 0;
        $data = 0;
        switch ($key) {
            case "login" : $target=array("page","layout"); break; 
            case "footer" : $target="page"; break; 
            case "header" : $target="page"; break; 
            case "content" : $target="page"; break; 
            case "navi" : $target=array("page","layout"); break;
            case "subPageNavi" : $target="layout"; break;
            case "social" : $target=array("layout","page"); break; 
            
            case "image" : $target="layout"; break; 
            case "imageList" : $target="layout"; break; 
            case "imageSlider" : $target="layout"; break; 
            case "map" : $target="layout"; break; 
            case "frame" : $target = "layout"; break;
            case "frame1" : $target="layout"; break; 
            case "frame2" : $target="layout"; break; 
            case "frame3" : $target="layout"; break; 
            case "frame4" : $target="layout"; break; 
            case "contactForm" : $target="layout"; break; 
            case "flip" : $target="layout"; break; 
            case "text" : $target="layout"; break; 
            case "textImage" : $target="layout"; break;
            case "contentName" : $target="layout"; break;
            
            case "articlesList" : $target="data"; $data="article"; break;

            case "companyShow" : $target="data"; $data="company"; break;
            case "companyList" : $target="data"; $data="company"; break;

            case "categoryList" : $target="data"; $data="category"; break;

            case "locationList" : $target="data"; $data="location"; break;

            case "productList" : $target="data"; $data="product"; break;
            case "productShow" : $target="data"; $data="product"; break;

            case "projectList" : $target="data"; $data="project"; break;
            case "projectShow" : $target="data"; $data="project"; break;

            case "dateList" : $target="data"; $data="date"; break;

            case "basket" : $target="not"; break;
            case "ownPhp" : $target="not"; break;
            case "empty" : $target="not"; break;

            case "bookmark" : $target="layout"; break;
            
            case "dynamicContent" : $target="layout"; break;
            
            case "vmt" : $target="layout"; break; 
        
        
            default :
                echo ('cms_contentType_getSortetList() case "'.$key.'" : $target=""; break; <br>');
                $target = "not";
        }

        if (is_string($target)) $target = array($target);

        for ($i=0;$i<count($target);$i++) {
            switch ($target[$i]) {
                case "data" :
                    if ($data) {
                        $res[data][$data][$key] = $value;
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


function cms_content_SelectType($type,$dataName) {
    $typeList = cms_contentType_getTypes();

//    $sortList = array();
//    foreach ($typeList as $key => $value) {
//        if (is_array($value)) $name = $value[name];
//        else $name = $value;
//        $sortList[$key] = $name;
//    }
//    asort($sortList);

    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$type' >";
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


function cms_contentType_show($contentData,$frameWidth,$getData) {
    //echo("cms_contentType_show($contentData,$frameWidth,$getData)<br />");
    $type = $contentData[type];
    $isContentName = 0;
    switch ($type) {
        case "contentName" :
            $edit = $_SESSION[edit];
            if ($edit) {
                $tempContent = cms_contentType_head($contentData,$frameWidth);
                if (is_array($tempContent )) {
                    echo ("This is in EditeMode<br />");
                    $contentData = $tempContent;
                }
            }
            $contentData = cmsType_contentName_data($contentData,$frameWidth);
            $isContentName = 1;
            break;
    }
    
    $dragAble = 1;
    if ($dragAble AND $_SESSION[edit]) {
        echo ("<div class='cmsContentFrameBox dragBox' id='dragContent_$contentData[id]' >");       
    }

    $ownFrameWidth = cms_getWidth($contentData[frameWidth],$frameWidth);
    $ownFrameHeight = cms_getWidth($contentData[frameHeight],$frameWidth);

           
    if ($ownFrameWidth) $frameWidth = $ownFrameWidth;

   
    $edit = $_SESSION[edit];
    if ($edit) {
        if ($isContentName) {
            // echo ("dont show head of ContentName $contentData[contentName] <br />");
        } else {
            $tempContent = cms_contentType_head($contentData,$frameWidth);
            if (is_array($tempContent )) {
                echo ("This is in EditeMode<br />");
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
    $textData = cms_text_getForContent($textId);
    
    // show_array($textData);
    
    // GET FRAME DATA
    $frameStyle = $contentData[frameStyle];
    $frameSettings = cmsFrame_getSettings($frameStyle);
    $border = $frameSettings[border];
    $padding = $frameSettings[padding];
    $spacing = $frameSettings[spacing];
//    show_array($frameSettings);
//    echo(" $border $padding $spacing <br>");

    $frameWidth = $frameWidth - (2*$border) - (2*$padding);
    
    $special_before = cmsFrame_getSpecial_before($frameStyle,$contentData,$frameWidth,$textData);
    $special_after  = cmsFrame_getSpecial_after($frameStyle,$contentData,$frameWidth,$textData);

    if (is_array($special_before)) {
        //echo ("<h1>Special Before</h1>");
        // show_array($special_before);
    }

    if (is_array($special_after)) {
        // echo ("<h1>Special After</h1>");
        //show_array($special_after);
    }


    

    
    $divData = array();
    
    $divStyle = "width:".$frameWidth."px;";
    $divStyle = "";
    if ($ownFrameHeight) $divStyle .= "height:".$ownFrameHeight."px;";
    //$frameFloat = $contentData[frameFloat];
    //if ($frameFloat AND $frameFloat != "none")  $divStyle .= "float:$frameFloat;";
    // echo ($divStyle."<br />");
    $divData[style] = $divStyle;

    $paddingTop = $contentData[data][paddingTop];
    if ($paddingTop) {
        //  echo ("Padding Top ".$padding."<br />");
        $divData[style] .= "padding-top:".$paddingTop."px;";
    }


    $divName = "cmsContentFrame_$contentId";
   
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
    
   
    
    
   
        

    
    
    if (is_string($special_before[output])) {
        echo ($special_before[output]);
    }
    
     // FRAME TEXT - Überschrift
    cms_contentType_frameText($textData,"frameHeadline",$frameWidth);
    
    // FRAME TEXT - Text Oben
    cms_contentType_frameText($textData,"frameHeadtext",$frameWidth);

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

        
        case "flip"     : cmsType_flip($contentData,$frameWidth); break;
        case "contentName" : cmsType_contentName($contentData); break;
        
        default :             
            if (function_exists("cmsType_".$type)) {
                call_user_func("cmsType_".$type, $contentData,$frameWidth);
            } else {

                echo ("unkown Type in cms_contentType_show $type<br />");
                foreach ($contentData as $key => $value) echo ("#$key = $value **");
                echo ("<br />");
            }
    }
 
    // FRAME TEXT - UNTEN
    cms_contentType_frameText($textData,"frameSubtext",$frameWidth);
    
    if (is_string($special_after[output])) {
        echo ($special_after[output]);
    }
    
  
    
    div_end($divName);
    if ($dragAble AND $_SESSION[edit]) {
        // echo ("</div>");
        echo ("</div>");
    }
    $spacerAdd = "";
    if ($contentData[last]) $spacerAdd .= "spacerLast";
    if ($_SESSION[edit]) $spacerAdd .= " spacerDrop";
    echo ("<div id='spacerId_$contentId' class='spacer spacerContentType spacerContentType_$type $spacerAdd'>&nbsp;</div>");

    //echo ("<div class='spacerContent' style='background-color:#7f7;height:30px;'>TYPE = $type &nbsp;</div>");

}

function cms_contentType_frameText($textData,$textType,$frameWidth) {
 
    if (!is_array($textData[$textType])) return 0;
    
    $text = $textData[$textType][text];
    if (!$text) return 0;
    
    $css = $textData[$textType][css];
    
    
    $className = "frameText_".$textType;
    if ($css) $className .= " frameText_".$css;
    div_start($className);
    echo ($text);
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
   //  echo ("<h1>cmsEditMode = $cmsEditMode</h1>");
    
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
                echo ("<div class='cmsEditBox'  >");
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

            if ($contentId != $editId) {
                $style = "width:auto;";
                if ($contentId == $editId) {
                    $style = "width:100%;opacity:100%;position:relativ;";
                }

                echo ("<div class='cmsEditBox' style='$style'>");
                $goPage = cms_page_goPage("editMode=editContentData&editId=".$contentId);
//                $goPage = $pageInfo[page];
//                $goPage .= "?editMode=editContentData&editId=".$contentId;
                if ($_GET[editLayout]) $goPage .= "&editLayout=".$_GET[editLayout];
                if ($_GET[layerNr]) $goPage .= "&flipLayerNr=".$_GET[layerNr];
                if ($_GET[flipLayerNr]) $goPage .= "&flipId=".$_GET[flipId]."&flipLayerNr=".$_GET[flipLayerNr];
                $goPage .= "#editFrame_".$contentId;

                // Roll Image
                // echo ("<img src='/cms_base/cmsImages/cmsEditClose.png' border='0px'>");

                // edit Edit

                echo ("<div class='cmsContentFrame_editButton cmsContentFrame_editButton_showFrame' id='editButtonID_$contentId' style='display:inline-block;'><a href='$goPage'><img src='/cms_base/cmsImages/cmsEdit.png' border='0px'></a></div>");

                // edit verschieben
                echo ("<div class='dragButton' style='display:inline-block;'><img src='/cms_base/cmsImages/cmsMove.png' border='0px'></div>");

                // edit Löschen
                echo ("<div class='cmsContentFrame_deleteButton' id='deleteContent_$contentId' >");
                echo ("<img src='/cms_base/cmsImages/cmsDelete.png' border='0px'>");
                echo ("</div>");

                echo ("<div class='cmsContentFrame_deleteAction cmsContentFrame_deleteAction_hidden deleteContent_$contentId'> ");
                echo ("Löschen:");
                $goPage = cms_page_goPage();
                echo ($goPage);
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
            if ($contentId != $editId) {
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

    $divName = "cmsContentEditFrame $pageId";
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



                    echo ("Typ: $contentData[type] id:$contentData[id]");
                    div_end($headName);
                    break;

            }
        }
    }

    if ($editMode == "editContentData") {
        if ($editId == $contentId) {
            // echo("<br />");
            $editRes = cms_content_edit($contentData,$frameWidth);
            $tempContent = $editRes[tempContent];
            $out = $editRes[outPut];
            if (!$newMode) echo ($out);
            if (is_array($tempContent)) echo ("TempContent get in head<br />");

        }
    }
    div_end($divName);
    return $tempContent;

}

function cms_content_edit($contentData,$frameWidth) {
    global $pageInfo,$pageData;

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
        reloadPage($goPage,1);
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

        if (count($saveData) > 0) {
            $saveResult = cms_content_save($editId,$saveData);
            if ($saveResult == 1) {
                cms_infoBox("Content gespeichert'$pageId'");
                
            } else {
                // error
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
                return 0;
            }
            if ($save) { // speichern
                $activeTab = $_POST[selectedTab]; 
                // echo ("Selected TAB = $activeTab <br>");
                
                $goPage = cms_page_goPage("editMode=editContentData&editId=$editId");
                if ($activeTab) $goPage .="&selectedTab=$activeTab";
                if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];
                if ($_POST[flipLayerNr]) $goPage .= "&flipId=".$_POST[flipId]."&flipLayerNr=".$_POST[flipLayerNr];
                $ok = $_GET[ok] + 1;
                $goPage .= "&ok=$ok";
                $goPage .= "#editFrame_".$editId;

                // show_array($_POST);
                // echo ("Save ! - Go Page = '$goPage' <br />");
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

    
    $out = "";
    $div1 = div_start_str("inputLine").div_start_str("inputLeft","width:200px;float:left;padding-top:5px;");
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
    
    $showToLevel = 1;
   
    // Special Data
    $addInput = array();

    $tabInput = array();

    $settings = array();
    if ($showType) {    // Typ
        $addData = array();
        $addData["text"] = "Content-Typ:";
        if ($layoutName OR $_GET[editLayout]) {
             $addData["input"] = cms_contentLayout_selectType($editContent[type],"editContent[type]");
        } else {
             $addData["input"] = cms_content_SelectType($editContent[type],"editContent[type]");
        }
        $settings[] = $addData;
    }
    
    if ($showLevel) { // UserLevel
        $addData = array();
        $addData["text"] = "Anzeigen ab";
        $addData["input"] = cms_user_selectLevel($editContent[showLevel],$_SESSION[userLevel],"editContent[showLevel]");
        $settings[] = $addData;
    }
    
    if ($showToLevel) { // UserLevel
        $addData = array();
        $addData["text"] = "Anzeigen bis";
        $addData["input"] = cms_user_selectLevel($editContent[toLevel],$_SESSION[userLevel],"editContent[toLevel]");
        $settings[] = $addData;
    }
    
    $contentType_class = cms_contentTypes_class();
    $special_viewFilter = $contentType_class->use_special_viewFilter($editContent);
    if (is_array($special_viewFilter)) {
        foreach($special_viewFilter as $key => $value) {
            $settings[] = $value;            
        }
    }
    

    if ($showContentName) { // ContentName
        $addData = array();
        $addData["text"] = "Inhalt verfügbar unter";
        $addData["input"] = "<input type='text' value='$editContent[contentName]' style='min-width:196px;' name='editContent[contentName]'>";
        $settings[] = $addData;
    }
    $tabInput[settings] = $settings;

    
    if ($showFrameSettings) {
        $frameSettings = array();
        $addData = array();
        $addData["text"] = "Rahmen Stil";
        $addData["input"] = cms_content_selectStyle("frameStyle",$editContent[frameStyle],"editContent[frameStyle]",array("submit"=>1));
        $frameSettings[] = $addData;
        
//        $addData = array();
//        $addData["text"] = "Umbruch";
//        $addData["input"] = cms_content_selectStyle("float",$editContent[frameFloat],"editContent[frameFloat]");
//        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = "Rahmen Link";
        $addData["input"] = cms_page_SelectMainPage($editContent[frameLink], "editContent[frameLink]");
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = "Rahmen-Breite";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameWidth]' value='".$editContent[frameWidth]."' >";
        $frameSettings[] = $addData;

        $addData = array();
        $addData["text"] = "Rahmen-Höhe";
        $addData["input"] ="<input type='text' style='width:100px;' name='editContent[frameHeight]' value='".$editContent[frameHeight]."' >";
        $frameSettings[] = $addData;


        $frameStyle = $editContent[frameStyle];
    
        $tabInput["frame"] = $frameSettings;
        
        
    }
    
    
    
     if ($showFrametext) {
         if (!$editContent[id]) {
             echo ("<h1> keine ID $editContent[id] -> editId = $editId <br>");
             show_array($_POST);
         }
         $tabInput["frameText"] = editContent_frameText($editContent,$frameWidth);$frameText;
    }
    
    
    
    
    
    if ($editContent[frameStyle] == "systemFrame") {
        $editList = cmsSystemFrame_editList($editContent);
//        echo ("Edit List = $editList");
//        show_array($editList);
        $tabInput["AktiverFrame"] = $editList;
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

            //$frameSettings[] = $special_edit[$i];
            // echo ("Special Edit $i,$special_edit[$i] <br />");
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
        $tabInput[$key] = $value;
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
        // echo ("SubAddInnput = $subAddInput<br />");
        // echo ("SubTabInnput = $subTabInput<br />");
        // echo ("SubShowContentName = $subShowContentName<br />");
        // echo ("SubSelectEdit = $subSelectEdit <br />");


    }
      
    

    if ($_POST[selectedTab]) {
        $selectEdit = $_POST[selectedTab];
        // echo ("Set selectEdit to $selectEdit by post<br />");
    } else {
        if ($_GET[selectedTab]) $selectEdit = $_GET[selectedTab];
    }
    
    

    $divData = array();
    $divData["selectTab"] = $selectEdit;

    

    $out .= div_start_str("cmsEditTabLine",$divData);
    foreach ($tabInput as $key => $value) {
        $divName = "cmsEditTab cmsEditTab_".$key;
        if (substr($key,0,8) == "content_") {
            $divName .= " cmsEditTabModification";
            if ($key == $selectEdit) $divName .= " _selected";
        } else {        
            if ($key == $selectEdit) $divName .= " cmsEditTab_selected";
        }
        $divData = array("editName"=>$key);
        $out .= div_start_str($divName,$divData);
        $out .= $key;
        $out .= div_end_str($divName);
    }
    $out .= "<input type='hidden' class='cmsEditTabName' name='selectedTab' value='$selectEdit' style='width:50px;height:12px;font-size:10px' >";

    $out .= div_end_str("cmsEditTabLine","before");

    

    foreach ($tabInput as $key => $value) {
        $divName = "cmsEditFrame cmsEditFrame_$key";
        if ($key != $selectEdit) $divName .= " cmsEditFrameHidden";
        $divData = array("editName"=>$key);
        $out .= div_start_str($divName,$divData);
        // echo ("<h1>$key</h1><br />");
        for ($i=0;$i<count($value);$i++) {
            if (is_array($value[$i])) {
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
                        $out .= $div1.$editText.":".$div2;
                        $out .= $editInput;
                        $out .= $div3;
                    } else {
                        $out .= $editInput;
                    } 
                      
                    if ($value[$i][secondLine]) {
                        if ($showInput) {
                            $out .= div_start_str("inputSecondLine");
                            $out .= $value[$i][secondLine];
                            $out .= div_end_str("inputSecondLine");
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

    
    $out .= div_start_str("cmsEditFrameButtons");
    $out .= "<input type='submit' class='cmsInputButton' name='editContentSaveClose' value='speichern und schließen' />";
    $out .= "<input type='submit' class='cmsInputButton' name='editContentSave' value='speichern' />";
    $out .= "<input type='submit' class='cmsInputButton cmsSecond' name='editContentCancel' value='abbrechen' />";
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
        $editText = cms_text_getForContent($contentCode);
    } else {
        // echo ("get Text form POST<br>");
       // 
    }
    // show_array($editText);
    $res = array();
    
    $addData = array();
    $addData["text"] = "hidden-Text Id";
    $addData["input"] =  "<input type='hidden'  name='textId' value='".$editContent[id]."' >";
    $res[] = $addData;

    $addData = array();
    $addData["text"] = "Überschrift";
    $input  = "<input type='text' style='width:".($frameWidth-10)."px;' name='editText[frameHeadline][text]' value='".$editText[frameHeadline][text]."' >";
    $input .= "<input type='hidden' value='".$editText[frameHeadline][id]."' name='editText[frameHeadline][id]'>";  
    $addData["input"] = $editClass->filter_select("style", $editText[frameHeadline][css],"editText[frameHeadline][css]",array("empty"=>"Bitte Überschrift wählen"),"headline");
    $addData["secondLine"] = $input;
    $res[] = $addData;

    $addData = array();
    $addData["text"] = "Text Oben";
    $input  = "<textarea name='editText[frameHeadtext][text]' style='width:".($frameWidth-10)."px;height:50px;' >".$editText[frameHeadtext][text]."</textarea>";
    $input .= "<input type='hidden' value='".$editText[frameHeadtext][id]."' name='editText[frameHeadtext][id]'>";    
    $addData["input"] = $editClass->filter_select("style", $editText[frameHeadtext][css],"editText[frameHeadtext][css]",array("empty"=>"Bitte Text-Darstellung wählen"),"text");        
    $addData["secondLine"] = $input;
    $res[] = $addData;

    $addData = array();
    $addData["text"] = "Text Unten";
    $input  = "<textarea name='editText[frameSubtext][text]' style='width:".$frameWidth."px;height:50px;' >".$editText[frameSubtext][text]."</textarea>";
    $input .= "<input type='hidden' value='".$editText[frameSubtext][id]."' name='editText[frameSubtext][id]'>";    
    $addData["input"] = $editClass->filter_select("style", $editText[frameSubtext][css],"editText[frameSubtext][css]",array("empty"=>"Bitte Text-Darstellung wählen"),"text");        
    $addData["secondLine"] = $input;
    $res[] = $addData;

    return $res;
}

function cms_contentType_SelectEditMode($type,$dataName) {

    $typeList = array();
    $typeList[onPage] = array("name"=>"auf der Seite");
    if ($_SESSION[userLevel] >= 9) $typeList[onPage2] = array("name"=>"auf der Seite - Neu");

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
    //            echo ("addInput $key = $value <br />");
                if (!is_array($tabInput[$type])) $tabInput[$type] = array();
                $tabInput[$type][] = $value;
            } else {
                //echo ("addInput is Asso-Array $key = $value <br />");
                // show_array($value);
                foreach($value as $key2 => $value2) {
                    if (is_integer($key2)) {
                       // echo ("addInput is Integer $key2 = $value2 <br />");
                        if (!is_array($tabInput[$type][$key])) $tabInput[$type][$key] = array();
                        $tabInput[$key][] = $value2;
                    } else {
                  //      echo ("addInput is Asso-Array $key / $key2 <br />");
                        $tabInput[$key2] = $value2;
                    }
                }
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
