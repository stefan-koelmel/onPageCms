<?php // charset:UTF-8
class cmsType_companyList_base extends cmsType_contentTypes_base {
    function getName (){
        return "Hersteller List";
    }
    
    function companyList_show($contentData,$frameWidth) {

        $data = $contentData[data];
        if (!is_array($data)) $data = array();


       //  show_array($data);
        $filter = array();
        // CustomFilter
        if ($data[filter_category]) $filter[category] = $data[filter_category];
        if ($data[filter_company]) $filter[id] = $data[filter_company];

        // FILTER
        // if ($data[filterCategory]) $filter["category"] = $data[filterCategory];

        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "category" : $filter["category"] = $value; break;
                case "company" : $filter["id"] = $value; break;
                case "product" : $filter["product"] = $value; break;
            }
        }

        // SORT
        $sort = $_GET[sort];

        $this->showList_customFilter($contentData,$frameWidth);

        // show_array($filter);

        $companyList = cmsCompany_getList($filter,$sort);

        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "table";

        switch ($viewMode) {
            case "list" :
                $this->showData_list($companyList,$contentData,$frameWidth);
                break;
            case "table" :
                $this->showData_table($companyList,$contentData,$frameWidth);
                break;
        }

        
        
        // echo ("ClickAction = $clickAction <br />");

    }

    function showData_list($companyData,$contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $showData = array();
        $showData[height] = 60;

        
        if ($data[pageing]) {
            $showData[pageing] =array();
            $showData[pageing][count] = $data[pageingCount];
            $showData[pageing][showTop] = $data[pageingTop];
            $showData[pageing][showBottom] = $data[pageingBottom];
            $showData[pageing][viewMode] = "all"; // small | all
        }
        $titleLine = $data[titleLine];
        if (!$titleLine) $titleLine = 0;
        $showData[titleLine] = $titleLine;

        $showList = array();
        $showList["image"] = array("name"=>"Logo","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>400);
        // $showList["category"] = array("name"=>"Kategory","width"=>200);
        // $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        $this->showList_List($companyData,$showList,$showData,$frameWidth);
        
    }

    function showData_table($companyData,$contentData,$frameWidth) {
        // echo ("SHOW Table");

       //  show_array($contentData);

        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $clickAction = $data[clickAction];
        $clickTarget = $data[clickTarget];
        $clickPage = $data[clickPage];
        $mouseAction = $data[mouseAction];

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";
        $divData[clickAction] = $clickAction;
        $divData[clickTarget] = $clickTarget;
        $divData[clickPage]   = $clickPage;

        $divData[mouseAction] = $mouseAction;
        $divData[cmsName] = $GLOBALS[cmsName];
        div_start("companyList",$divData);

        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];
        $imgColHeight = $data[imgColHeight];
        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = floor(($frameWidth - (($imgRow-1)*$imgRowAbs) ) / $imgRow);
        if ($imgColHeight) {
            $prozPos = strpos($imgColHeight,"%");
            if ($prozPos) {
                $prozValue = intval(substr($imgColHeight,0,$prozPos));
                $showHeight = floor($rowWidth * $prozValue / 100);
            } else {
                $prozPx = strpos($imgColHeight,"p");
                if ($prozPx) {
                    $showHeight = intval(substr($imgColHeight,0,$prozPx));
                } else {
                    $getHeight = intval($imgColHeight);
                    if ($getHeight > 0) $showHeight = $getHeight;
                }
            }
        }




        $nr = 0;
        for ($i = 0; $i<count($companyData); $i++) {
            $company = $companyData[$i];
            $imageId = $company[image];
            
            $nr++;
            if ($nr == 1) {
                $styleLine = "margin-bottom:".$imgColAbs."px";
                if ($i+$imgRow >= count($companyData)) {
                    // echo ("letzte Zeile ".($i+$imgRow)." anz ".count($companyData)."<br />");
                    $styleLine = "";
                } //else {
                  //  echo ("nicht letze  Zeile ".($i+$imgRow)." anz".count($companyData)."<br />");
                // }
                div_start("companyListLine",$styleLine);
            }
            $divData = array();
            $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
            if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";

            $divData[companyId] = $company[id];
            // $divData[companyUrl] = $company[url];
            $showDiv = "";
            if ($clickAction) {
                switch ($clickAction) {
                    case "goUrl" :
                        $divData[companyUrl] = $company[url];
                        break;
                    case "showProduct" :
                        $divData[title] = "Zeige Produkte von ".$company[name];
                        break;

                    case "showCategory" :
                        $divData[title] = "Zeige Produktgruppen von ".$company[name];
                        break;

                    case "showCompany" :
                        $divData[title] = "Zeige Hersteller ".$company[name];
                        break;

                    case "contentName" :
                        $divData[title] = "Zeige Hersteller Produkte ".$company[name];
                        $divData[contentNameId] = $contentData[data][clickContentName];
                        $divData[frameWidth] = $frameWidth;
                        // show_array($divData);
                        break;

                    default :
                        echo ("unkown ClickAction $clickAction <br />");
                }
            }

//            switch ($mouseAction) {
//                case "showCategory" :
//                    echo ("<div id='companyBox_".$company[id]."_roll' class='companyListRoll companyListRollHidd__en'>rollframe</div>");
//                    break;
//                case "showProduct" :
//                    echo ("<div id='companyBox_".$company[id]."_roll' class='companyListRoll companyListRollHidd__en'>rollframe</div>");
//                    break;
//            }


            $divData[id] = "companyBox_".$company[id];
            div_start("companyListImageBox",$divData);
            switch ($mouseAction) {
                case "showCategory" :
                    echo ("<div id='companyBox_".$company[id]."_roll' class='companyListRoll companyListRollHidden'>Taschen & Schlüssenanhänger</div>");
                    break;
                case "showProduct" :
                    echo ("<div id='companyBox_".$company[id]."_roll' style='background-color:#fff;width:".$rowWidth."px;height:".$showHeight."px;' class='companyListRoll companyListRollHidden'>");
                    $productList = cmsProduct_getList(array("show"=>1,"company"=>$company[id]),"name");
                    // echo (count($productList)."- $company[id]");
                    if (count($productList)) {

                        $productNr = rand(0,count($productList)-1);
                        $product = $productList[$productNr];
                        $image = $product[image];
                        if (intval($image)) $productImageId = intval($image);
                        else {
                            $imageList = explode("|",$image);
                            $imageNr = rand(1,count($imageList)-2);
                            $productImageId = intval($imageList[$imageNr]);
                        }
                        $imageData = cmsImage_getData_by_Id($productImageId);
                        if (is_array($imageData)) {
                            $showData = array();
                            $showData[frameWidth] = $rowWidth;
                            $showData[frameHeight] = $showHeight; // idth;
                            $showData[vAlign] = "middle";
                            $showData[hAlign] = "center";
                            $showData[title] = $company[name];
                            if ($divData[title]) $showData[title] = $divData[title];
                            $showData[alt] = $company[name];
                            $showData[name] = $company[name];
                            $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
                            echo($imgStr);


                        }
                    }
                    echo ("</div>");
                    break;
            }
            $showData = array();
            $showData[frameWidth] = $rowWidth;
            $showData[frameHeight] = $showHeight; // idth;
            $showData[vAlign] = "middle";
            $showData[hAlign] = "center";
            $showData[title] = $company[name];
            if ($divData[title]) $showData[title] = $divData[title];
            $showData[alt] = $company[name];
            $showData[name] = $company[name];
            $imageData = cmsImage_getData_by_Id($imageId);
            if (is_array($imageData)) {
                $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
            // show_array($imgData);
                echo ($imgStr);
            } else {
                $divData = array();
                $paddingTop = floor($showHeight / 4);
                $divData["style"] = "width:".$rowWidth."px;height:".($showHeight-$paddingTop)."px;padding-top:".$paddingTop."px;text-align:center;"; //line-height:".($showHeight/2)."px;vertical-align:middle;";
                div_start("companyListTextBox",$divData);
                $name = $company[name];
                echo ("<h1>$name</h1>");
                div_end("companyListTextBox");
            }
             // show_array($company);

            div_end("companyListImageBox");

            if ($nr == $imgRow) { // close Line
                $nr = 0;
               
                div_end("companyListLine","before");

            }
        }

        if ($nr != 0) {
            div_end("companyListLine","before");
        }


        div_end("companyList","before");

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

            case "showCompany":
                $divName = "companyShowFrame productShowFrameHidden";
                div_start($divName);
                echo ("Zeige Hersteller<br />");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end($divName);
                break;


             case "showCategory":
                div_start("categoryShowFrame");
                echo ("Zeige Produktgruppen<br />");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end("categoryShowFrame");
                break;

            case "contentName":
                div_start("contentNameFrame contentNameFrameHidden");
                echo ("Zeige ContentName Frame<br />");
                //echo("<img src='' class='imagePreviewImage' width='0px' height:'0px'");
                div_end("contentNameFrame");
                break;
        }


    }


    function editContent_filter_getList_own() {
        $filterList = array();
        $filterList[produkt] = 0;
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Kategorie wählen");
        $filterList[category]["filter"] = array("mainCat"=>0,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[company]   = array();
        $filterList[company]["name"] = "Herstelle";
        $filterList[company]["type"] = "company";
        $filterList[company]["showData"] = array("submit"=>1,"empty"=>"Hersteller wählen");
        $filterList[company]["filter"] = array("show"=>1);
        $filterList[company]["sort"] = "name";
        $filterList[company]["dataName"] = "product";
        $filterList[company][customFilter] = 1;


        $filterList[product]   = array();
        $filterList[product]["name"] = "Produkte";
        $filterList[product]["type"] = "product";
        $filterList[product]["showData"] = array("submit"=>1,"empty"=>"Produkt wählen");
        $filterList[product]["filter"] = array("show"=>1);
        $filterList[product]["sort"] = "name";
        $filterList[product]["dataName"] = "product";
        $filterList[product][customFilter] = 1;

        return $filterList;
    }





    function companyList_editContent($editContent,$frameWidth) {
        $res = array();

        $mainTab = "companyList";
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
            switch ($clickAction) {
                case "contentName" :
                    $clickContentName = $editContent[data][clickContentName];
                    if ($_POST[editContent][data][clickContentName]) $clickContentName = $_POST[editContent][data][clickContentName];
                    else if ($_POST[editContent][data]) $clickContentName = $_POST[editContent][data][clickContentName];
                    $addData = array();
                    $addData["text"] = "Inhalt wählen";
                    $addData["input"] = $this->filter_select("contentName",$clickContentName,"editContent[data][clickContentName]",array(),$filter,$sort);
                    $res[action][] = $addData;
                    break;
            }

            if ($clickAction == "showProduct" OR $clickAction == "showCategory"  OR $clickAction == "showCompany" OR $clickAction == "contentName") {

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

                        $clickPage = $editContent[data][clickPage];
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

    function filter_select_getList_own($filterType,$filter,$sort) {
        switch ($filterType) {
            case "viewMode" :
                $res = $this->viewMode_filter_select_getList($filter,$sort);
                return $res;

        }
        echo ("function filter_select_getList_own($filterType,$filter,$sort) <br />");
        
    }
    function viewMode_filter_select_getList($filter,$sort) {
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        
        $ownList = $this->viewMode_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }


    function category_filter_select_getOwnList($filter,$sort) {
        $res = array();
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
        $res["contentName"] = "gespeicherter Inhalt";

        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function clickAction_getOwnList() {
        $res = array();
        $res["showCompany"] = "Hersteller zeigen";
        return $res;
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
        $res["showProduct"] = "Produkt zeigen";
        return $res;
    }

    
}

function cmsType_companyList_class() {
    if ($GLOBALS[cmsTypes]["cmsType_companyList.php"] == "own") $companyListClass = new cmsType_companyList();
    else $companyListClass = new cmsType_companyList_base();
    return $companyListClass;
}


function cmsType_companyList($contentData,$frameWidth) {
    $companyListClass = cmsType_companyList_class();
    $companyListClass->companyList_show($contentData,$frameWidth);
}



function cmsType_companyList_editContent($editContent,$frameWidth) {
    $companyListClass = cmsType_companyList_class();
    return $companyListClass->companyList_editContent($editContent,$frameWidth);
}


?>
