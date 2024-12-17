<?php // charset:UTF-8
class cmsType_locationList_base extends cmsType_contentTypes_base {


    function getName (){
        return "Orte List";
    }


    function locationList_show($contentData,$frameWidth) {
        $viewMode = $contentData[data][viewMode];
        if (!$viewMode) $viewMode = "list";


        $id=$_GET[id];
        if ($id>0) {
            $this->location_showData($id);
            return 0;


        }

        
        $filter = array();
        $filter[show] = 1;
        $sort = "";

        $this->showList_customFilter($contentData,$frameWidth);
        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;

        foreach ($_data as $key => $value) {
            switch ($key) {
                case "sort" : break;
                case "page" : break;

                case "date" :
                    $day = substr($value,8,2);
                    $month = substr($value,5,2);
                    $year = substr($value,0,4);
                    $selectDay = mktime(12,0,0,$month,$day,$year);
                    $dayCode = intval(date("w",$selectDay));
                    $dayStr = cmsDates_dayStr($dayCode);
                    echo ("Datum = $dayStr, den ".date("d.m.Y",$selectDay)."<br />");
                    $filter[date] = $value;
                    break;

                case "category" : if ($value) $filter[$key] = $value; break;
                case "filter_category" : if ($value) $filter["category"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_region"   : if ($value) $filter["region"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;

                case "filter_dateRange"   :
                    // echo ("<h1> FILTER DATERANGE $value</h1>");
                    $dateRangeList = $this->dateRange_filter_select_getList();
                    //show_array($dateRangeList);
                    $dateRange = $dateRangeList[$value];
                    if (is_array($dateRange)) {
                        if ($dateRange[filter]) {
                            foreach ($dateRange[filter] as $filterKey => $filterValue) $filter[$filterKey] = $filterValue;
                        }

                    }
                    break;

                case "dateRange"   : if ($value) $filter[$key] = $value; break;

                case "filter_specialView"   :
                    $specialFilterList = $this->customFilter_specialView_getList();
                    if (is_array($specialFilterList[$value])) {
                        $specialFilter = $specialFilterList[$value];
                        // APPEND Filter to Filter
                        if (is_array($specialFilter[filter])) {
                            foreach($specialFilter[filter] as $key => $value ) {
                                $filter[$key] = $value;
                                // echo "append $key = $value to filter <br />";
                            }
                        }
                        if ($specialFilter[sort]) {
                            if (is_string($specialFilter[sort]))$sort = $specialFilter[sort];
                        }

                    } else {
                        echo ("Filter SpecialView $key = $value <br />");
                    }
                    break;

                default :
                    echo ("Unkown $key in get/post_data = $value <br />");

            }
        }

        // show_array($filter);


        div_start("locationList","width:".$frameWidth."px;");
        switch ($viewMode) {
            case "imageblock" :
                $this->locationList_show_imageblock($contentData,$frameWidth,$filter,$sort);
                break;
             case "list" :
                $this->locationList_show_list($contentData,$frameWidth,$filter,$sort);
                break;
        }
        div_end("locationList");
    }

     function locationList_show_list($contentData,$frameWidth,$filter,$sort) {

        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        $clickAction = $data[clickAction];
        $mouseAction = $data[mouseAction];

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        $divData[clickAction] = $clickAction;
        $divData[mouseAction] = $mouseAction;
        $divData[cmsName] = $GLOBALS[cmsName];
        div_start("locationList_list",$divData);

        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - 2*$imgRowAbs ) / $imgRow;


        $locationList = cmsLocation_getList($filter,$sort);


        $showList = array();
        $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40);
        $showList["name"] = array("name"=>"Name","width"=>400);
        $showList["category"] = array("name"=>"Name","width"=>150);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center");

        $showData = array();
        $showData[titleLine] = $data[titleLine];
        $showData[pageing] = array();
        $showData[pageing][count] = $data[pageingCount];
        $showData[pageing][showTop] = $data[pageingTop];
        $showData[pageing][showBottom] = $data[pageingBottom];
        $showData[pageing][viewMode] = "small"; // small | all//

        $this->showList_List($locationList,$showList,$showData,$frameWidth);
        
        div_end("locationList_list");
        // echo ("ClickAction = $clickAction <br />");

    }


    function location_showData($id) {
        echo ("Show Location with id $id <br />");
        $locationData = cmsLocation_getById($id,"text");
        show_array($locationData);
        
    }

    function locationList_show_imageblock($contentData,$frameWidth) {

        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        $clickAction = $data[clickAction];
        $mouseAction = $data[mouseAction];

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        $divData[clickAction] = $clickAction;
        $divData[mouseAction] = $mouseAction;
        $divData[cmsName] = $GLOBALS[cmsName];
        div_start("locationList",$divData);

        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - 2*$imgRowAbs ) / $imgRow;


        $locationList = cmsLocation_getList($filter,$sort);

        
        $nr = 0;
        for ($i = 0; $i<count($locationList); $i++) {
            $location = $locationList[$i];
            $imageId = $location[image];
            $imageData = cmsImage_getData_by_Id($imageId);
            $nr++;
            if ($nr == 1) {
                div_start("locationListLine","margin-bottom:".$imgColAbs."px");
            }
            $divData = array();
            $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
            if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";

            $divData[locationId] = $location[id];
            // $divData[locationUrl] = $location[url];
            switch ($clickAction) {
                case "goUrl" :
                    $divData[locationUrl] = $location[url];
                    break;
                case "showProduct" :
                    $divData[title] = "Zeige Produkte von ".$location[name];
                    break;

                case "showCategory" :
                    $divData[title] = "Zeige Produktgruppen von ".$location[name];
                    break;
            }

//            switch ($mouseAction) {
//                case "showCategory" :
//                    echo ("<div id='locationBox_".$location[id]."_roll' class='locationListRoll locationListRollHidd__en'>rollframe</div>");
//                    break;
//                case "showProduct" :
//                    echo ("<div id='locationBox_".$location[id]."_roll' class='locationListRoll locationListRollHidd__en'>rollframe</div>");
//                    break;
//            }


            $divData[id] = "locationBox_".$location[id];
            div_start("locationListImageBox",$divData);
            switch ($mouseAction) {
                case "showCategory" :
                    echo ("<div id='locationBox_".$location[id]."_roll' class='locationListRoll locationListRollHidden'>Taschen & Schlüssenanhänger</div>");
                    break;
                case "showProduct" :
                    echo ("<div id='locationBox_".$location[id]."_roll' class='locationListRoll locationListRollHidden'>rollframe</div>");
                    break;
            }
            $showData = array();
            $showData[frameWidth] = $rowWidth;
            $showData[frameHeight] = $rowWidth;
            $showData[vAlign] = "top";
            $showData[hAlign] = "center";
            $showData[title] = $location[name];
            if ($divData[title]) $showData[title] = $divData[title];
            $showData[alt] = $location[name];
            $showData[name] = $location[name];

            $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
            // show_array($imgData);
            echo ($imgStr);
             // show_array($location);

            div_end("locationListImageBox");

            if ($nr == $imgRow) { // close Line
                $nr = 0;
                div_end("locationListLine","before");
            }
        }

        if ($nr != 0) {
            div_end("locationListLine","before");
        }

       
        div_end("locationList","before");

        switch ($clickAction) {
            case "goUrl" :
                break;

            case "showProduct":
                $divName = "productShowFrame productShowFrameHidden";
                div_start($divName);
                echo ("Zeige Produkte<br />");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end($divName);
                break;

             case "showCategory":
                div_start("categoryShowFrame");
                echo ("Zeige Produktgruppen<br />");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end("categoryShowFrame");
                break;
        }
        // echo ("ClickAction = $clickAction <br />");

    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        return $res;
    }

    function customFilter_specialView_getList_own() {
        $specialList = array();


        $specialList[noneLocation] = array("id"=>"gone","name"=>"Termine ohne Ort");
        $specialList[noneLocation][filter] = array("show"=>1,"location"=>0);
        $specialList[noneLocation][sort] = "locationStr";


        $specialList[noneRegion] = array("id"=>"gone","name"=>"Termine ohne Region");
        $specialList[noneRegion][filter] = array("show"=>1,"region"=>0);
        $specialList[noneRegion][sort] = "date";
        return $specialList;
    }

    function editContent_filter_getList_own() {
        $filterList = array();
        $filterList[produkt] = 0;

        //$filterList[specialView]   = array();
        //$filterList[specialView]["name"] = "Spezielle Ansichten";
        //$filterList[specialView]["type"] = "specialView";
        //$filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["dataName"] = "specialView";
        //$filterList[specialView][customFilter] = 1;

        $filterList[produkt] = 0;
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Kategorie wählen");
        $filterList[category]["filter"] = array("mainCat"=>8,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Region wählen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        return $filterList;
    }

   

    

    function locationList_editContent($editContent,$frameWidth) {
        $res = array();


        $mainTab = "locationList";
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }

        // Add ViewMode
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }


        // Mouse ACTION
        $mouseAction = $editContent[data][mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else {
            if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        }
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else {
            if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        }
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;


        if ($clickAction) {
            if ($clickAction == "showProduct" OR $clickAction == "showCategory") {

                $clickTarget = $editContent[data][clickTarget];
                if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
                else {
                    if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
                }
                $addData = array();
                $addData["text"] = "Zeigen in";
                $addData["input"] = $this->target_select($clickTarget,"editContent[data][clickTarget]",array("submit"=>1));
                $res[action][] = $addData;


                switch ($clickTarget) {
                    case "page" :

                        $clickPage = $editContent[data][clickTarget];
                        if ($_POST[editContent][data][clickPage]) $clickPage = $_POST[editContent][data][clickPage];
                        else if ($_POST[editContent][data]) $clickPage = $_POST[editContent][data][clickPage];

                        $addData = array();
                        $addData["text"] = "Seite auswählen";
                        $addData["input"] = $this->page_select($clickPage,"editContent[data][clickPage]",array("submit"=>1));
                        $res[action][] = $addData;

                        break;
                    case "frame" :

                        break;
                    case "popup" :
                        $addData = array();
                        $addData["text"] = "Breite PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpWidth]' style='width:100px;' value='".$editContent[data][popUpWidth]."'>";
                        $res[action][] = $addData;

                        $addData = array();
                        $addData["text"] = "Höhe PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpHeight]' style='width:100px;' value='".$editContent[data][popUpHeight]."'>";

                        $res[action][] = $addData;
                        break;
                }
            }
        }


        return $res;
    }


    // function page_select($code,$dataName,$showData) {

    // function page_select_getList() {

    function page_select_getOwnList() {
        $res = array();
        return $res;
    }

    // function target_select($code,$dataName,$showData)
    // function target_select_getList()

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
            $str.= "<option value='$key'";
            if ($key == $code)  $str.= " selected='1' ";
            $str.= ">$value</option>";
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
            $str.= "<option value='$key'";
            if ($key == $code)  $str.= " selected='1' ";
            $str.= ">$value</option>";
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

    
}


function cmsType_locationList_class() {
    if ($GLOBALS[cmsTypes]["cmsType_locationList.php"] == "own") $locationListClass = new cmsType_locationList();
    else $locationListClass = new cmsType_locationList_base();
    return $locationListClass;
}

function cmsType_locationList($contentData,$frameWidth) {
    $locationListClass = cmsType_locationList_class();
    $locationListClass->locationList_show($contentData,$frameWidth);
}



function cmsType_locationList_editContent($editContent,$frameWidth) {
    $locationListClass = cmsType_locationList_class();
    return $locationListClass->locationList_editContent($editContent,$frameWidth);
}

function cmsType_locationList_getName() {
    $locationListClass = cmsType_locationList_class();
    $name = $locationListClass->getName();
    return $name;
}




?>
