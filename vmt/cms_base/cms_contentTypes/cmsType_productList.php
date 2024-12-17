<?php // charset:UTF-8
class cmsType_productList_base extends cmsType_contentTypes_base {

    function getName (){
        return "Produkt List";
    }

    function productList_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("productList","width:".$frameWidth."px;");

        $data = $contentData[data];
        $imgRow = intval($data[imgRow]);
        $imgRowAbs = intval($data[imgRowAbs]);
        $imgColAbs = intval($data[imgColAbs]);
        $maxCount = intval($data[maxCount]);
        // show_array($data);

        if (!is_int($imgRow)) $imgRow = 5;
        if (!is_int($imgRowAbs)) $imgRowAbs = 10;
        if (!is_int($imgColAbs)) $imgColAbs = 10;
        $rowWidth = floor(($frameWidth - (($imgRow-1)*$imgRowAbs )) / $imgRow);

        $filter = $data[filter];
        $sort = $data[sort];

        $debug = 0;
       //  show_array($_POST);

        $filter = array();
        if ($data[filter_category]) $filter[category] = $data[filter_category];
        if ($data[filter_company]) $filter[company] = $data[filter_company];

        if ($_POST[filter_category]) $filter[category] = $_POST[filter_category];
        if ($_POST[filter_company]) $filter[company] = $$_POST[filter_company];

        foreach($_GET as $key => $value) {
            switch($key) {
                case "category" : $filter["category"] = $value; break;
                case "company" : $filter["company"] = $value; break;
                case "product" : $filter["id"] = $value; break;
            }
        }


        $this->showList_customFilter($contentData,$frameWidth);

        // FILTER PRODUCT
        $filterProduct = $data[filterProduct];
        if ($filterProduct) {
            if ($debug) echo ("Filter Product $filterProduct <br />");
            switch ($filterProduct) {
                case "new" :
                    $filter["new"] = 1;
                    break;
                case "highlight" :
                    $filter["highlight"] = 1;
                    break;
            }
        }
        // FILTER Hersteller
        $filterCompany = $data[filterCompany];
        if ($filterCompany) {
            if ($debug) echo ("Filter Company $filterCompany <br />");
            $filter["company"] = $filterCompany;
        }
        // FILTER Category
        $filterCategory = $data[filterCategory];
        if ($filterCategory) {
            if ($debug) echo ("Filter Category $filterCategory <br />");
            $filter["category"] = filterCategory;
        }

        if ($debug AND count($filter)>0) {
            echo ("<h1>Filter</h1>");
            show_array($filter);
        }


        $productList = cmsProduct_getList($filter,$sort);


        if (count($productList)== 0 ) {
            echo ("Kein Produkt gefunden ");
        } else {

            if (intval($maxCount)>0 AND count($productList)>$maxCount) {
                // echo ("<h1>Anzahl Produkte $maxCount </h1>");
                $newList = array();
                $idList = array();

                while (count($newList) < $maxCount) {
                    $randomNr = rand(0, count($productList)-1);
                    $randomId = $productList[$randomNr][id];
                    if (!$idList["$randomNr"]) {
                        $idList["$randomNr"] = 1;
                        $newList[] = $productList[$randomNr];
                        // echo ("RandomNr=$randomNr RandomId=$randomId<br />");
                    } else {
                        // echo ("Allready in List $randomNr<br />");
                    }
                }
//                echo ("NewList count =".count($newList)."<br />");
//                echo ("Random IdList<br />");
                $productList = $newList;
                
           }



            $nr = 0;
            for ($i = 0; $i<count($productList); $i++) {
                $product = $productList[$i];
                $imageId = $product[image];
                if (intval($imageId)) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    // echo ("imageId $imageId <br />");
                    $nr++;
                    if ($nr == 1) {
                        div_start("productListLine","margin-bottom:".$imgColAbs."px");
                    }

                    $ratio = 4 /3;
                    $showHeight = $rowWidth / $ratio;
                    $divData = array();
                    $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
                    if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";

                    $divData[productId] = $product[id];
                    $divData[productUrl] = $product[url];
                    div_start("productListImageBox",$divData);
                    $showData = array();
                    $showData[frameWidth] = $rowWidth;
                    $showData[ratio] = $ratio;
                    $showData[frameHeight] = $showHeight;
                    $showData[vAlign] = "top";
                    $showData[hAlign] = "center";
                    $showData[title] = $product[name];
                    $showData[alt] = $product[name];
                    $showData[name] = $product[name];

                    $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
                    // show_array($imgData);
                    echo ($imgStr);
                     // show_array($product);

                    div_end("productListImageBox");

                    if ($nr == $imgRow) { // close Line
                        $nr = 0;
                        div_end("productListLine","before");
                    }
                }
            }

            if ($nr != 0) {
                div_end("productListLine","before");
            }
        }

        div_end("productList","before");
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for productListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        return $res;
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
        $filterList[company]["name"] = "Hersteller";
        $filterList[company]["type"] = "company";
        $filterList[company]["showData"] = array("submit"=>1,"empty"=>"Hersteller wählen");
        $filterList[company]["filter"] = array("show"=>1);
        $filterList[company]["sort"] = "name";
        $filterList[company]["dataName"] = "company";
        $filterList[company][customFilter] = 1;

        return $filterList;
    }








    function productList_editContent($editContent,$frameWidth) {
        $res = array();
        $mainTab = "productList";
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
        else if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;


        if ($clickAction) {
            if ($clickAction == "showProduct" OR $clickAction == "showCategory") {

                $clickTarget = $editContent[data][clickTarget];
                if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
                else if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
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
        


    function filter_select_getOwnList($filterType,$filter,$sort) {}

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

    function category_filter_select_getList($filter,$sort) {
        $res = array();
        $res = array();
        $categoryList = cmsCategory_getList($filter, $sort);
        for ($i=0;$i<count($categoryList);$i++) {
            $id = $categoryList[$i][id];
            $name = $categoryList[$i][name];
            $res[$id] = $name;
        }

        $ownList = $this->category_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function category_filter_select_getOwnList($filter,$sort) {
        $res = array();
        return $res;
    }

    //  function clickAction_select($code,$dataName,$showData) {

    function clickAction_getList() {
        $res = array();
        $res["showProduct"] = "Produkte zeigen";
        
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


    // function mouseAction_select($code,$dataName,$showData) {

    function mouseAction_getList() {
        $res = array();

        $res["showProduct"] = "ProduktInfo zeigen";
        
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

function cmsType_productList_class() {
    if ($GLOBALS[cmsTypes]["cmsType_productList.php"] == "own") $productListClass = new cmsType_productList();
    else $productListClass = new cmsType_productList_base();
    return $productListClass;
}

function cmsType_productList($contentData,$frameWidth) {
    $productListClass = cmsType_productList_class();
    $productListClass->productList_show($contentData,$frameWidth);
}



function cmsType_productList_editContent($editContent,$frameWidth) {
    $productListClass = cmsType_productList_class();
    return $productListClass->productList_editContent($editContent,$frameWidth);
}



?>
