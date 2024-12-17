<?php // charset:UTF-8
class cmsType_companyShow_base extends cmsType_contentTypes_base {

    function getName (){
        return "Hersteller";
    }

    function companyShow_show($contentData,$frameWidth) {
        $pageInfo = $GLOBALS[pageInfo];

        div_start("companyShow","width:".$frameWidth."px;");


        $data = $contentData[data];
        if (!is_array($data)) $data = array();
       
        $filter = array();
        // CustomFilter
        if ($data[filter_category]) $filter[category] = $data[filter_category];
        if ($data[filter_company])  $filter[id] = $data[filter_company];

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

        
        $viewMode = $data[viewMode];
       // echo ("<h1> ViewMode = $viewMode</h1>");
        if (!$viewMode) $viewMode = "info";
        
        
         $companyList = cmsCompany_getList($filter,$sort);
        
        
        switch ($viewMode) {
            case "info" : $this->companyShow_showInfo($contentData,$frameWidth,$filter,$sort); break;
            case "randomOne" : $this->companyShow_showRandomOne($contentData,$frameWidth,$filter,$sort); break;

            default : 
                echo ("unkown View in companyShow_show $viewMode <br />");

        }
        div_end("companyShow");
    }


    function companyShow_showRandomOne($contentData,$frameWidth,$filter,$sort) {


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

        
        
        

        div_end("companyShow","before");
    }

    function companyShow_showInfo($contentData,$frameWidth,$filter,$sort) {
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


     function viewMode_filter_select_getOwnList($filter,$sort) {
        // echo ("<h1> get ViewMode for productListe </h1>");
        $res = array();
        $res["list"] = 0;
        $res["table"] = 0; //"Tabelle";
        $res["info"] = "Hersteller Info";
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





    function companyShow_editContent($editContent,$frameWidth) {
        $res = array();

        $mainTab = "companyShow";
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

  
        return $res;
    }


}

function cmsType_companyShow_class() {
    if ($GLOBALS[cmsTypes]["cmsType_companyShow.php"] == "own") $companyShowClass = new cmsType_companyShow();
    else $companyShowClass = new cmsType_companyShow_base();
    return $companyShowClass;
}

function cmsType_companyShow($contentData,$frameWidth) {
    
    $companyShowClass = cmsType_companyShow_class();
    $companyShowClass->companyShow_show($contentData,$frameWidth);
}



function cmsType_companyShow_editContent($editContent,$frameWidth) {
    $companyShowClass = cmsType_companyShow_class();
    return $companyShowClass->companyShow_editContent($editContent,$frameWidth);
}


?>
