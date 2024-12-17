<?php // charset:UTF-8
class cmsType_company_base extends cmsType_contentData_show_base {

    function getName (){
        return "Hersteller";
    }

    function company_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $viewMode = $data[viewMode];
//        $company = $_GET[company];
//        if ($company) {
//            $viewMode = "single";
//        }
       // echo ("<h1>CompanyShow $viewMode</h1>");
        switch ($viewMode) {
            case "list" :
                $this->company_showList($contentData, $frameWidth);
                break;
            case "table" :
                $this->company_showTable($contentData, $frameWidth);
                break;
            case "single" :
                $this->company_showCompany($contentData, $frameWidth);
                break;
            case "slider" :
                $this->company_showSlider($contentData, $frameWidth);
                break;
            default :
                echo ("UNKOWN VIEWMODE IN company_show ".$data[viewMode]."<br>");                            
        }
    }
    
    function company_showList($contentData, $frameWidth) {
        // echo "Not Ready <b>ShowList</b><br>";
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        // show_array($data);
        $showList = $this->dataShow_List();
//        echo ("<h1>ShowList</h1>");
//        foreach ($showList as $key => $value) {
//            $show = $data[$key."_show"];
//            if ($show) {
//                echo ("<b> SHOW $key </b><br />");
//            }
//            //echo ("show $key => $value <br>");
//        }


        $showData = array();
        $showData[titleLine] = $data[titleLine];

        $showData[pageing] = array();
        $showData[pageing][count] = $data[pageingCount];
        $showData[pageing][showTop] = $data[pageingTop];
        $showData[pageing][showBottom] = $data[pageingBottom];
        $showData[pageing][viewMode] = "small"; // small | all//


        $companyList = $this->company_getList($contentData);

        $showList = array();
        $showList["image"] = array("name"=>"Artikelbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $this->showList_List($companyList,$showList,$showData,$frameWidth);

      
    }
    
    function company_showTable($contentData, $frameWidth) {
        div_start("companyList","width:".$frameWidth."px;");

        $companyList = $this->company_getList($contentData);
        
        $this->data_showTable("company",$companyList,$contentData,$frameWidth);

        div_end("companyList","before");
    }
    
    function company_showCompany($contentData, $frameWidth) {
        $companyData = $this->company_getCompany($contentData);
        if (is_array($companyData)) {
            $dataType = "companyShow";
            $out = $this->dataBox_show($dataType,$companyData,$contentData,$frameWidth);
            echo ($out);   
            return 0;
        }
        
        echo ($company);   
        
    }
    function company_showSlider($contentData, $frameWidth) {
        $companyList  = $this->company_getList($contentData);
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

        for ($i=0;$i<count($companyList);$i++) {
            $company = $companyList[$i];
            $divStr = "";
          
            if ($clickAction) {
                $divStr .= "<div class='companySliderItem sliderItem sliderItemClick'>";
                $divStr .= "<a href='$company[goPage]' class='hiddenLink' >$company[name]</a>";
                // foreach ($project as $key => $value) $divStr .= "| $key=$value |";
            }

            $dataType = "companyShow";
            $outData = $this->dataBox_show($dataType,$company,$contentData,$frameWidth);

            $divStr .= $outData; 
           
            if ($clickAction) $divStr .= "</div>";
            $contentList[] = $divStr;
        }

        $type = null;
        $name = "companySlider";
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
//        
//        $direction = $data[direction];
//        if (!$direction) $direction = "horizontal";
//        $loop      = $data[loop];
//        if (!$loop) $loop = 0;
//        $pause = $data[pause];
//        if (!$pause) $pause = 5000;
//        $speed = $data[speed];
//        if (!$speed) $speed = 500;
//        $navigate = $data[navigate];
//        $pager     = $data[pager];
//
//        $showData[loop] = $loop;
//        $directionList = array("vertical","horizontal","fade");
//        $showData[direction] = $direction; // $directionList[0];
//
//        $showData[speed] = $speed;
//
//        $showData[pause] = $pause;
//        $showData[navigate] = $navigate;
//        $showData[page] = $pager;
        // show_array($showData);


        cmsSlider($type,$name,$contentList,$showData,$width,$height);
        
    }
    
    
    
   function company_getList($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
       
        $filter = $data[filter];
        $sort = $data[sort];

        $debug = 0;
        $filter = array();
       
        if ($data[dynamicCategory]) {
            $categoryGet = $_GET[category];
            if ($categoryGet) $categoryId = $categoryGet;
        }
        
        if ($categoryId) $filter[category] = $categoryId;
        
        $companyList = cmsCompany_getList($filter,$sort);
        
        
        $mainLink = $GLOBALS[pageData][name].".php";
        $addLink = "";
        if ($categoryId) $addLink = "category=$categoryId";
        
        for ($i=0;$i<count($companyList);$i++) {
            $companyId = $companyList[$i][id];
            
            $add = $addLink;
            if ($add) $add.= "&";
            $add .= "company=$companyId";
            
            $goLink = $mainLink."?".$add;
            // echo ("GOLINK $goLink <br>");
            
            $companyList[$i][goPage] = $goLink;
            
        }
        
        return $companyList;
   }
   
    function company_getCompany($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        if ($data[dynamicCategory]) {
            $categoryGet = $_GET[category];
            if ($categoryGet) $categoryId = $categoryGet;
        }
        
        if ($data[dynamicCompany]) {
            $companyGet = $_GET[company];
            if ($companyGet) $companyId = $companyGet;
        }
        
        if ($data[clickAction] == "dynamicId") {
            $companyGet = $_GET[company];
            if ($companyGet) $companyId = $companyGet;
            //echo ("<h1> DYNAMIC COMPANY $companyId</h1>");
        }
        
        if ($companyId) {
            $company= cmsCompany_get(array("id"=>$companyId));
            return $company;
        }
        
        return "notFound";
    }
        
       
   
   function clickAction_getOwnList() {
       $res = array();
       $res["goUrl"] = 0;//"Hersteller Homepage öffnen";
       $res["showProduct"] = 0;//"Produkte zeigen";
       $res["showCategory"] = 0; //"Kategorie zeigen";
       
       $res["goPage"] = "Seite zeigen";
       $res["dynamicId"] = "Hersteller zeigen";
       
       return $res;
   }
    
//    function company_show($contentData,$frameWidth) {
//        $pageInfo = $GLOBALS[pageInfo];
//
//        div_start("company","width:".$frameWidth."px;");
//
//
//        $data = $contentData[data];
//        if (!is_array($data)) $data = array();
//       
//        $filter = array();
//        // CustomFilter
//        if ($data[filter_category]) $filter[category] = $data[filter_category];
//        if ($data[filter_company])  $filter[id] = $data[filter_company];
//
//        // FILTER
//        // if ($data[filterCategory]) $filter["category"] = $data[filterCategory];
//
//        foreach ($_GET as $key => $value) {
//            switch ($key) {
//                case "category" : $filter["category"] = $value; break;
//                case "company" : $filter["id"] = $value; break;
//                case "product" : $filter["product"] = $value; break;
//            }
//        }
//
//        // SORT
//        $sort = $_GET[sort];
//
//
//        $this->showList_customFilter($contentData,$frameWidth);
//
//        
//        $viewMode = $data[viewMode];
//       // echo ("<h1> ViewMode = $viewMode</h1>");
//        if (!$viewMode) $viewMode = "info";
//        
//        
//         $companyList = cmsCompany_getList($filter,$sort);
//        
//        
//        switch ($viewMode) {
//            case "info" : $this->company_showInfo($contentData,$frameWidth,$filter,$sort); break;
//            case "randomOne" : $this->company_showRandomOne($contentData,$frameWidth,$filter,$sort); break;
//
//            default : 
//                echo ("unkown View in company_show $viewMode <br />");
//
//        }
//        div_end("company");
//    }


    function company_showRandomOne($contentData,$frameWidth,$filter,$sort) {


        $debug = 0;
        // FILTER PRODUCT
        $filterCompany = $data[filterCompany];
        if ($filterCompany) {
            if ($debug) echo ("Filter Company $filterCompany <br />");
            switch ($filterCompany) {
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


        $companyList = cmsCompany_getList($filter,$sort);
        $anz = count($companyList);
        //echo ("Hersteller anzahl $anz <br />");

        $companyId = rand(0,$anz-1);
        $company = $companyList[$companyId];

        
        $name = $company[name];
        $info = $company[info];
        $company = $company[company];
        $show = $company[show];
        $new = $company["new"];
        $highlight = $company[highlight];
        $imageId = intval($company[image]);


        $imageData = cmsImage_getData_by_Id($imageId);

        $showData = array();
        $showData[frameWidth] = $frameWidth;
        $showData[frameHeight] = $frameWidth;
        $showData[vAlign] = "bottom";
        $showData[hAlign] = "left";
        $showData[title] = $company[name];
        $showData[alt] = $company[name];
        $showData[name] = $company[name];

        $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
        echo ($imgStr);
        echo ("<h1>$name</h1>");
        echo ($info);
        //echo ("Hersteller anzahl $anz $companyId <br />");

        
        
        

        div_end("company","before");
    }

    function company_showInfo($contentData,$frameWidth,$filter,$sort) {
        $companyList = cmsCompany_getList($filter, $sort);
        for ($i = 0;$i<count($companyList);$i++) {
            $company = $companyList[$i];
            div_start("companyInfoBox","width:".$frameWidth."px;margin-bottom:10px;");

            $imgWidth = 120;
            $padding = 10;
            $leftWidth = $imgWidth;
            $rightWidth = $frameWidth - $leftWidth - $padding;

            div_start("companyInfoBox_left","width:".$leftWidth."px;float:left;margin-right:".$padding."px;");
           
            $imageId = intval($company[image]);
            if ($imageId) {

                $imageData = cmsImage_getData_by_Id($imageId);

                $showData = array();
                $showData[frameWidth] = $imgWidth;
                // $showData[frameHeight] = $frameWidth;
                $showData[vAlign] = "top";
                $showData[hAlign] = "left";
                $showData[title] = $company[name];
                $showData[alt] = $company[name];
                $showData[name] = $company[name];

                $imgStr = cmsImage_showImage($imageData, $imgWidth, $showData);
                echo ($imgStr);
            } else {
                echo "Kein Hersteller Logo";
            }


            div_end("companyInfoBox_left");

            div_start("companyInfoBox_right","width:".$rightWidth."px;float:left;");
            echo ("<h1>$company[name]</h1>");
            
            echo ("$company[info]");
            // show_array($company);
            $category = $company[category];
            echo ("Kategorie: $category <br />");
            $url      = $company[url];
            echo ("Homepage: $url <br />");

            div_end("companyInfoBox_right");



            div_end("companyInfoBox","before");
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
        $filterList[company]["name"] = "Hersteller";
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





    function company_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();

        $mainTab = "company";
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }

        
        // ShowList
        $showList = $this->companyShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "companyShow";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
         // Image Settings
        $dontShow = array();
        $addList = $this->cmsImage_editSettings($editContent,$frameWidth,$dontShow);                
        $addToTab = "image";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        // Add FILTER
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // ACTION 
        $addList = $this->action_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "action";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        return $res;
    }
    
    
    function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for companyListe </h1>");
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Slider";
        $res["single"] = "Hersteller";
            
        return $res;
    }
    
     function dataShow_List() {
        return $this->companyShow_List();
    }
    
    function companyShow_List() {
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

function cmsType_company_class() {
    if ($GLOBALS[cmsTypes]["cmsType_company.php"] == "own") $companyClass = new cmsType_company();
    else $companyClass = new cmsType_company_base();
    return $companyClass;
}

function cmsType_company($contentData,$frameWidth) {
    
    $companyClass = cmsType_company_class();
    $companyClass->company_show($contentData,$frameWidth);
}



function cmsType_company_editContent($editContent,$frameWidth) {
    $companyClass = cmsType_company_class();
    return $companyClass->company_editContent($editContent,$frameWidth);
}


?>
