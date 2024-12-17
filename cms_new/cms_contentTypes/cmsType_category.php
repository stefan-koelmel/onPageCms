<?php // charset:UTF-8
class cmsType_category_base extends cmsClass_content_data_show {

    function getName (){
        return "Kategorien";
    }

    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth  = $this->frameWidth;
        
        $pageInfo = $GLOBALS[pageInfo];

        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        // show_array($data)
        $filter = array();
        $sort = "name";
        if ($data[filterCategory]) $filter[mainCat]=$data[filterCategory];




        


        div_start("categoryList","width:".$frameWidth."px;");
        $viewMode = $data[viewMode];
        if (!$viewMode) $viewMode = "list";
        switch ($viewMode) {
            case "list" : $this->categoryList_showList($contentData,$frameWidth,$filter,$sort); break;
            case "table" : $this->categoryList_showTable($contentData,$frameWidth,$filter,$sort); break;
            default :
                echo ("unkown ViewMode in ' articlesList_show(), $viewMode<br />");
        }
        div_end("categoryList");
    }

    function categoryList_showList($contentData,$frameWidth,$filter,$sort) {
        echo ("Show List von Artikeln<br />");
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $showData = array();
        $showData[titleLine] = $data[titleLine];

        $showData[pageing] = array();
        $showData[pageing][count] = $data[pageingCount];
        $showData[pageing][showTop] = $data[pageingTop];
        $showData[pageing][showBottom] = $data[pageingBottom];
        $showData[pageing][viewMode] = "small"; // small | all//

        show_array($filter);
        $categoryList = cmsCategory_getList($filter,$sort);
        echo ("CategorieListe = $categoryList ".count($categoryList)." <br />");
       
        $showList = array();
        $showList["image"] = array("name"=>"Artikelbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);

        $this->showList_List($categoryList,$showList,$showData,$frameWidth);
    }

    function categoryList_showTable ($contentData,$frameWidth,$filter,$sort) {
        global $pageInfo;
        $data = $contentData[data];
        
        $clickAction = $data[clickAction];
        $mouseAction = $data[mouseAction];

        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - 2*$imgRowAbs ) / $imgRow;

       
        $categoryList = cmsCategory_getList($filter,$sort);


        if (count($categoryList)== 0 ) {
            echo ("Kein Produkt gefunden ");
        } else {
        


            $nr = 0;
            for ($i = 0; $i<count($categoryList); $i++) {
                $category = $categoryList[$i];
               
                
                $nr++;
                if ($nr == 1) {
                    div_start("categoryListLine","margin-bottom:".$imgColAbs."px");
    //
    //
    //                $showHeight = cmsImage_getShowHeight($imageData,$rowWidth);
    //                // echo ("AktImage = $i showHeight = $showHeight <br />");
    //                for ($nextNr=$i+1;$nextNr<$i+$imgRow;$nextNr++) {
    //                    $nextShowHeight = cmsImage_getShowHeight($imgDataList[$nextNr],$rowWidth);
    //                    if ($nextShowHeight > $showHeight ) $showHeight = $nextShowHeight;
    //                    // echo ("Check $nextNr $showHeight $nextShowHeight <br />");
    //                }
                }
                $divData = array();
                $divData[style] = "width:".$rowWidth."px;float:left;height:".$showHeight."px;";
                if ($nr < $imgRow) $divData[style].="margin-right:".$imgRowAbs."px";

                $divData[categoryId] = $category[id];
                $divData[categoryUrl] = $category[url];
                div_start("categoryListImageBox",$divData);
                $imageId = $category[image];
                if ($imageId) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    $showData = array();
                    $showData[frameWidth] = $rowWidth;
                    $showData[frameHeight] = $rowWidth;
                    $showData[vAlign] = "top";
                    $showData[hAlign] = "center";
                    $showData[title] = $category[name];
                    $showData[alt] = $category[name];
                    $showData[name] = $category[name];
                    $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
                    echo ($imgStr);
                } else {
                    $clickAction = $data[clickAction];
                    if ($clickAction == "filter") {
                        $goPage = "";
                        foreach($_GET as $key => $value ) {
                            switch($key) {
                                case "category" : break;
                                default :
                                    if ($goPage == "") $goPage .= "?";
                                    else $goPage .= "&";
                                    $goPage .= $key."=".$value;
                            }
                        }

                        if ($goPage == "") $goPage .= "?";
                        else $goPage .= "&";
                        $goPage .= "category=$category[id]";

                        $goPage = $pageInfo[page].$goPage;

                        echo ("<a href='$goPage' class='cmsLinkButton' style='width:".($rowWidth-20)."px;' >");
                    }
                    // no Image
                    $name = $category[name];
                    echo "$name";
                    if ($clickAction == "filter") echo ("</a>");
                    // show_array($data);
                }
                div_end("categoryListImageBox");

                if ($nr == $imgRow) { // close Line
                    $nr = 0;
                    div_end("categoryListLine","before");
                }
            }

            if ($nr != 0) {
                div_end("categoryListLine","before");
            }
        }
        
       //  div_end("categoryList","before");

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
    }


     function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["filter"] = "Filter";
        return $res;
    }


    

     function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $data = $this->editContent[data];
        
        $res = array();


        $mainTab = "categoryList";
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }



        // ADD Filter


        // Add ViewMode
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }


        // FILTER Category
        $filterCategory = $editContent[data][filterCategory];
        if ($_POST[editContent][data][filterCategory]) $filterCategory = $_POST[editContent][data][filterCategory];
        else if ($_POST[editContent][data]) $filterCategory = $_POST[editContent][data][filterCategory];
        $addData = array();
        $addData["text"] = "nach Kategorie";
        $addData["input"] = $this->filter_select("category",$filterCategory,"editContent[data][filterCategory]",array("submit"=>1),array("mainCat"=>0));
        $res[filter][] = $addData;

//
//
//         // FILTER PRODUKT
//        $filterArticles = $editContent[data][filterArticles];
//        if ($_POST[editContent][data][filterArticles]) $filterArticles = $_POST[editContent][data][filterArticles];
//        else if ($_POST[editContent][data]) $filterArticles = $_POST[editContent][data][filterArticles];
//        $addData = array();
//        $addData["text"] = "Artikel Filtern";
//        $addData["input"] = $this->filter_select("articles",$filterArticles,"editContent[data][filterArticles]",array("submit"=>1));
//        $res[filter][] = $addData;
//
//
//        // FILTER Hersteller
//        $filterCompany = $editContent[data][filterCompany];
//        if ($_POST[editContent][data][filterCompany]) $filterCompany = $_POST[editContent][data][filterCompany];
//        else if ($_POST[editContent][data]) $filterCompany = $_POST[editContent][data][filterCompany];
//        $addData = array();
//        $addData["text"] = "nach Herstellern";
//        $addData["input"] = $this->filter_select("company",$filterCompany,"editContent[data][filterCompany]",array("submit"=>1),$filter,$sort);
//        $res[filter][] = $addData;
//
//
//
//
//         // FILTER Category
//        $filterCategory = $editContent[data][filterRegion];
//        if ($_POST[editContent][data][filterRegion]) $filterRegion = $_POST[editContent][data][filterRegion];
//        else if ($_POST[editContent][data]) $filterRegion = $_POST[editContent][data][filterRegion];
//        $addData = array();
//        $addData["text"] = "nach Region";
//        $addData["input"] = $this->filter_select("category",$filterRegion,"editContent[data][filterRegion]",array("submit"=>1),array("mainCat"=>180));
//        $res[filter][] = $addData;



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
        $res["showCompany"] = "Hersteller zeigen";
        $res["showProduct"] = "Produkte zeigen";
        $res["filter"]      = "Als Filter anwenden";
        //  $res["showCategory"] = "Kategorien zeigen";

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
        $res["companyList"] = "Liste der Hersteller";
        $res["productList"] = "Liste von Produkten";
      
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

function cmsType_category_class() {
    if ($GLOBALS[cmsTypes]["cmsType_category.php"] == "own") $categoryListClass = new cmsType_category();
    else $categoryListClass = new cmsType_category_base();
    return $categoryListClass;
}

function cmsType_category($contentData,$frameWidth) {
    //  echo ("categoryList");
    if ($GLOBALS[cmsTypes]["cmsType_categoryList.php"] == "own") $categoryListClass = new cmsType_categoryList();
    else $categoryListClass = new cmsType_categoryList_base();
    $categoryListClass = cmsType_category_class();
    return $categoryListClass->show($contentData,$frameWidth);
}



function cmsType_category_editContent($editContent,$frameWidth) {
    if ($GLOBALS[cmsTypes]["cmsType_categoryList.php"] == "own") $categoryListClass = new cmsType_categoryList();
    else $categoryListClass = new cmsType_categoryList_base();

    return $categoryListClass->categoryList_editContent($editContent,$frameWidth);
}

function cmsType_category_getName() {
    $categoryListClass = cmsType_category_class();
    $name = $categoryListClass->getName();
    return $name;
}


?>
