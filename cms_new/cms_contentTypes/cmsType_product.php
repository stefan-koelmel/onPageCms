<?php // charset:UTF-8
class cmsType_product_base extends cmsClass_content_data_show {

    function getName (){
        return "Produkte";
      
    }


    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        $this->tableName = "product";
        $pageInfo = $GLOBALS[pageInfo];
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        switch ($data[viewMode]) {
            case "list" :
                $this->product_showList($contentData, $frameWidth);
                break;
            case "table" :
                $this->product_showTable($contentData, $frameWidth);
                break;
            case "single" :
                $this->product_showProduct($contentData, $frameWidth);
                break;
            case "slider" :
                $this->product_showSlider($contentData, $frameWidth);
                break;
            default :
                echo ("UNKOWN VIEWMODE IN product_show ".$data[viewMode]."<br>");                            
        }
    }
    
    function product_showList($contentData, $frameWidth) {
        // echo "Not Ready <b>ShowList</b><br>";
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        // show_array($data);
        $showList = $this->dataShow_List();
        foreach ($showList as $key => $value) {
            $show = $data[$key."_show"];
            if ($key == "basket") $useBasket = 1;
//            if ($show) {
//                echo ("<b> SHOW $key </b><br />");
//            }
            //echo ("show $key => $value <br>");
        }
        
        $this->filter = array("show"=>1);
        $this->sort   = "id";
        $this->data_showFilter();

        $productList = $this->product_getList($contentData);
        $this->data_showList("product",$productList);
        return 1;
        

        $showData = array();
        $showData[titleLine] = $data[titleLine];

        $showData[pageing] = array();
        $showData[pageing][count] = $data[pageingCount];
        $showData[pageing][showTop] = $data[pageingTop];
        $showData[pageing][showBottom] = $data[pageingBottom];
        $showData[pageing][viewMode] = "small"; // small | all//


        // cmsProduct_getList($filter,$sort,"___assoId");

//        if ($_POST) {
//            // show_array($_POST);
//            if ($_POST[basket]) {
//                foreach ($_POST[basket] as $key => $value) {
//                    if ($value[add]) {
//                        $addId = $key;
//                        $addAmount = $value[amount];
//                        $addSource = "product";
//
//                        $addData = cmsProduct_get(array("id"=>$addId));
//                        // $addData = $productList[$addId];
//                        if (is_array($addData)) {
//                            $add = array();
//                            $add[dataSource] = $addSource;
//                            $add[dataId] = $addId;
//                            $add[amount] = $addAmount;
//                            $add[name] = $addData[name];
//                            $add[value] = $addData[vk];
//                            $add[shipping] = $addData[shipping];
//
//                            echo ("Add To Basket $addId Anzahl: $addAmount <br />");
//                            $myPage = $GLOBALS[pageData][name].".php";
//                            $res = cmsType_basket_addToBasket($add,$myPage);
//                        }
//                    }
//                }
//            }
//        }
//
//        if ($useBasket) {
//            $goPage = $GLOBALS[pageData][name].".php";
//            echo ("Hat warenkorb! $goPage<bR>");
//            echo ("<form action='$goPage' method='post'>");
//
//            // show_array($_POST);
//        }

       
        // echo ("CategorieListe = $categoryList ".count($categoryList)." <br />");
       
        $showList = array();
        $showList["image"] = array("name"=>"Artikelbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        $showList["vk"] =  array("name"=>"Preis","type"=>"float","width"=>50,"align"=>"right","sort"=>1,"deci"=>2,"komma"=>",","1000"=>".");
        $showList["basket"] =  array("name"=>"Warenkorb","type"=>"basket","width"=>150,"align"=>"right","sort"=>1);

        $this->showList_List($productList,$showList,$showData,$frameWidth);

//        if ($useBasket) {
//
//            echo ("</form>");
//        }
    }
    
    function product_showProduct($contentData, $frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        if ($data[dynamicCategory]) {
            $categoryGet = $_GET[category];
            if ($categoryGet) $categoryId = $categoryGet;
        }
        
        if ($data[dynamicProduct]) {
            $productGet = $_GET[product];
            if ($productGet) $productId = $productGet;
        }
        
        if (!$productId) {
            if ($data[filterProduct]) {
                $productId = $data[filterProduct];
            }
        }
        
        if (!$productId) {
            echo ("Kein Projekt ausgewählt !<br />");
            return 0;
        }
        
        $productData = cmsProduct_get(array("id"=>$productId));
        
        $basketAvailible = function_exists("cmsBasket_getItemCount");
        
        if ($data[basket_show] AND $basketAvailible) {
            // echo ("SHOW BASKET <br>");
           
            $basketId = "product_".$productId;
            $inBasket = cmsBasket_getItemCount($basketId);
            
            $basket = array();
            $basket[basketId] = $basketId;
            $basket[inBasket] = $inBasket;
            $productData[basket] = $basket;
            //  show_array($basket);
            
        }
        
        
        
        $dataType = "productShow";
        $out = $this->dataBox_show($dataType,$productData,$contentData,$frameWidth);
        echo ($out);        
    }
    
    function product_showSlider($contentData, $frameWidth) {
        
        $productList  = $this->product_getList($contentData);
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $imageWidth = 300;

        $ratio = 1.0 * 4 / 3;
        $randomImage = 1;


        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
       
        $contentList = array();

        for ($i=0;$i<count($productList);$i++) {
            $product = $productList[$i];
            $divStr = "";
          
            if ($clickAction) {
                $divStr .= "<div class='productSliderItem sliderItem sliderItemClick'>";
                $divStr .= "<a href='$product[goPage]' class='hiddenLink' >$product[name]</a>";
                // foreach ($project as $key => $value) $divStr .= "| $key=$value |";
            }

            $dataType = "productShow";
            $outData = $this->dataBox_show($dataType,$product,$contentData,$frameWidth);

            $divStr .= $outData; 
           
            if ($clickAction) $divStr .= "</div>";
            $contentList[] = $divStr;
        }

        $type = null;
        $name = "productSlider";
        $showData = array();
        $width = $frameWidth;
        // $height =  $imageWidth / $ratio;

       //  echo ("DIRECTION = $data[direction]<br>");
        
        $direction = $data[dataDirection];
        if (!$direction) $direction = "horizontal";
        $loop      = $data[dataLoop];
        if (!$loop) $loop = 0;
        $pause = $data[dataPause];
        if (!$pause) $pause = 5000;
        $speed = $data[dataSpeed];
        if (!$speed) $speed = 500;
        $navigate = $data[dataNavigate];
        $pager     = $data[dataPager];

        $showData[loop] = $loop;
        $directionList = array("vertical","horizontal","fade");
        $showData[direction] = $direction; // $directionList[0];

        $showData[speed] = $speed;

        $showData[pause] = $pause;
        $showData[navigate] = $navigate;
        $showData[page] = $pager;
        // show_array($showData);


        cmsSlider($type,$name,$contentList,$showData,$width,$height);
        
        
        // echo "Not Ready <b>ShowSlider</b><br>";
    }

    function product_showTable($contentData, $frameWidth) {
        div_start("productList","width:".$frameWidth."px;");

        
        $this->filter = array("show"=>1);
        $this->sort   = "id";
        $this->data_showFilter();
        
        $productList = $this->product_getList($contentData);
        
        
        $this->data_showTable("product",$productList,$contentData,$frameWidth);

        div_end("productList","before");
    }
    
    
    function product_getList($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
       
        $filter = $data[filter];
        $sort = $data[sort];
        if (!$sort) $sort = "id";

        $debug = 0;
       //  show_array($_POST);

        $filter = array();
        if ($data[filter_category]) $filter[category] = $data[filter_category];
        if ($data[filter_company]) $filter[company] = $data[filter_company];

        if ($_GET[filter_category]) $filter[category] = $_GET[filter_category];
        if ($_GET[filter_company]) $filter[company] = $_GET[filter_company];
//
//        foreach($_GET as $key => $value) {
//            switch($key) {
//                case "category" : $filter["category"] = $value; break;
//                case "company" : $filter["company"] = $value; break;
//                case "product" : $filter["id"] = $value; break;
//            }
//        }
//
//        // FILTER PRODUCT
//        $filterProduct = $data[filterProduct];
//        if ($filterProduct) {
//            if ($debug) echo ("Filter Product $filterProduct <br />");
//            switch ($filterProduct) {
//                case "new" :
//                    $filter["new"] = 1;
//                    break;
//                case "highlight" :
//                    $filter["highlight"] = 1;
//                    break;
//            }
//        }
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
//
//        if ($debug AND count($filter)>0) {
//            echo ("<h1>Filter</h1>");
//            show_array($filter);
//        }
//        
//        
        if ($data[dynamicCategory]) {
            $categoryGet = $_GET[category];
            if ($categoryGet) $categoryId = $categoryGet;
        }
        
        if ($categoryId) $filter[category] = $categoryId;
        
        // echo ("SHOW $filter, $sort <br>");
        // show_array($filter);
        
        $filter = array();
        foreach ($this->filter as $key => $value) {
            $filter[$key] = $value;
            // echo ("FILTER = $key => $value <br> ");
        }

        $productList = cmsProduct_getList($filter,$sort);
        
        
        $mainLink = $GLOBALS[pageData][name].".php";
        $addLink = "";
        if ($categoryId) $addLink = "category=$categoryId";
        
        for ($i=0;$i<count($productList);$i++) {
            $productId = $productList[$i][id];
            $productName = $productList[$i][name];
            // echo ("Show $productId $productName <br>");
            $add = $addLink;
            if ($add) $add.= "&";
            $add .= "product=$productId";
            
            $goLink = $mainLink."?".$add;
            // echo ("GOLINK $goLink <br>");
            
            $productList[$i][goPage] = $goLink;
            
            if ($data[basket_show]) {
                $basketId = "product_".$productId;
                if (function_exists("cmsBasket_getItemCount")) {
                    $inBasket = cmsBasket_getItemCount($basketId);

                    $basket = array();
                    $basket[name] = $productName;
                    $basket[basketId] = $basketId;
                    $basket[dataSource] = "product";
                    $basket[dataId] = $productId;
                    $basket[inBasket] = $inBasket;
                    // show_array($basket);
                    $productList[$i][basket] = $basket;
                }
                // echo ("ADD BASKET $basketId <br>");
            }
        }
        
        return $productList;

        
    }

    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for productListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Slider";
        $res["single"] = "Produkt";
            
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
        $filterList[category]["filter"] = array("mainCat"=>1,"show"=>1);
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

    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $res = array();
        $mainTab = "product";
        
        $data = $editContent[data];
        if (!$data) $data = array();
        
        
        
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            $res[$mainTab][showName] = $this->lga("content","productTab");
            $res[$mainTab][showTab] = "Simple";
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }
        
        // ShowList
        $showList = $this->productShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "productShow";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        $res[$addToTab][showName] = $this->lga("content","productViewTab");
        $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        
        // Image Settings
        $dontShow = array();
        $addList = $this->cmsImage_editSettings($editContent,$frameWidth,$dontShow);                
        $addToTab = "image";
        $res[$addToTab][showName] = $this->lga("content","productImageTab");
        $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        

        // Add FILTER
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            $res[$addToTab][showName] = $this->lga("content","productFilterTab");
            $res[$addToTab][showTab] = "Simple";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

//        // ACTION 
//        $addList = $this->action_editContent($data,$showList);
//       // show_array($addList);
//        $addToTab = "productShow";
//        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
//        for ($i=0;$i<count($addList);$i++) {
//            // echo ("ADD $i $addList[$i] <br>");
//            $res[$addToTab][] = $addList[$i];
//        }
        
        
        // Mouse ACTION
        $mouseAction = $editContent[data][mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        
        $addToTab = "action";
        $res[$addToTab][showName] = $this->lga("content","productActionTab");
        $res[$addToTab][showTab] = "Simple";
        
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[action][] = $addData;

        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $addData["mode"] = "More";
        $res[action][] = $addData;


        if ($clickAction) {
            if ($clickAction == "showProduct" OR $clickAction == "showCategory") {

                $clickTarget = $editContent[data][clickTarget];
                if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
                else if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
                $addData = array();
                $addData["text"] = "Zeigen in";
                $addData["input"] = $this->target_select($clickTarget,"editContent[data][clickTarget]",array("submit"=>1));
                $addData["mode"] = "More";
                $res[action][] = $addData;


                switch ($clickTarget) {
                    case "page" :

                        $clickPage = $editContent[data][clickTarget];
                        if ($_POST[editContent][data][clickPage]) $clickPage = $_POST[editContent][data][clickPage];
                        else if ($_POST[editContent][data]) $clickPage = $_POST[editContent][data][clickPage];

                        $addData = array();
                        $addData["text"] = "Seite auswählen";
                        $addData["input"] = $this->page_select($clickPage,"editContent[data][clickPage]",array("submit"=>1));
                        $addData["mode"] = "More";
                        $res[action][] = $addData;

                        break;
                    case "frame" :

                        break;
                    case "popup" :
                        $addData = array();
                        $addData["text"] = "Breite PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpWidth]' style='width:100px;' value='".$editContent[data][popUpWidth]."'>";
                        $addData["mode"] = "Admin";
                        $res[action][] = $addData;

                        $addData = array();
                        $addData["text"] = "Höhe PopUp Fenster";
                        $addData["input"] = "<input name='editContent[data][popUpHeight]' style='width:100px;' value='".$editContent[data][popUpHeight]."'>";
                        $addData["mode"] = "Admin";

                        $res[action][] = $addData;
                        break;
                }
            }
        }


        return $res;
    }
        
    
    function dataShow_List() {
        return $this->productShow_List();
    }
    
    function productShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        
        $show[vk] = array("name"=>"Verkauspreis","description"=>"Bezeichnung zeigen","position"=>1);
        $show[shipping] = array("name"=>"Porto","description"=>"Bezeichnung zeigen","position"=>1);
        $show[count] = array("name"=>"Anzahl","description"=>"Bezeichnung zeigen","position"=>1);
        
        $show[basket] = array("name"=>"Warenkorb","description"=>"Bezeichnung zeigen","position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
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

function cmsType_product_class() {
    if ($GLOBALS[cmsTypes]["cmsType_product.php"] == "own") $productClass = new cmsType_product();
    else $productClass = new cmsType_product_base();
    return $productClass;
}

function cmsType_product($contentData,$frameWidth) {
    $productClass = cmsType_product_class();
    return $productClass->show($contentData,$frameWidth);
}



function cmsType_product_editContent($editContent,$frameWidth) {
    $productClass = cmsType_product_class();
    return $productClass->editContent($editContent,$frameWidth);
}



?>
