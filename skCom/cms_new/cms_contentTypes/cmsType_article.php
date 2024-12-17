<?php // charset:UTF-8
class cmsType_article_base extends cmsClass_content_data_show {

    function getName (){
        return "Artikel";
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
         $data = $contentData[data];
         if (!is_array($data)) $data = array();
         
         $viewMode = $data[viewMode];
         // show_array($GLOBALS[pageData]);
         
         
         switch ($viewMode) {
             case "table" :
                 $this->article_showTable($contentData,$frameWidth);
                 break;
             case "list" :
                 $this->article_showList($contentData,$frameWidth);
                 break;
             case "slider" :
                 $this->article_showSlider($contentData,$frameWidth);
                 break;

             case "article" :
                 $this->article_showArticle($contentData,$frameWidth);
                 break;

             default: 
                 echo ("Unkown ShowMode in article_show '$viewMode' <br />");
                 
         }
    }

    function article_showArticle($contentData,$frameWidth) {      
        

        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        // show_array($data);

        if ($data[dynamicArticle]) {
            $articleGet = $_GET[article];
            if ($articleGet) $articleId = $articleGet;            
        }

        if (!$articleId) {
            if ($data[filterArticle]) {
                $articleId = $data[filterArticle];
            }
        }

        if (!$articleId) {
            echo ("Kein Artikel ausgewählt !<br />");
            return 0;
        }

        $article = cmsArticles_get(array("id"=>$articleId));
        // foreach ($project as $key => $value) echo ("<b>$key</b> = $value <br>");
        if (!is_array($article)) {
            echo ("Keine Artikel Daten erhalten <br />");
            return 0;
        }

        div_start("articleShow","width:".$frameWidth."px;");

        $dataType = "articleShow";
        $out = $this->dataBox_show($dataType,$article,$contentData,$frameWidth);
        echo ($out);
       
        if ($_SESSION[edit] AND $_SESSION[showLevel]>=7) {
            echo ("<a href='admin_cmsArticles.php?view=edit&id=$article[id]' class='cmsLinkButton'>Artikel editieren</a> <br />");
        }


        div_end("articleShow","before");
        return 0;
//        $pageInfo = $GLOBALS[pageInfo];
//
//        div_start("article","width:".$frameWidth."px;");
//        $data = $contentData[data];
//        if (!is_array($data)) $data = array();
//        $viewMode = $data[viewMode];
//        if (!$viewMode) $viewMode = "list";
//
//        $filter = array("fromDate"=>"<=".date("Y-m-d"),"toDate"=>">=".date("Y-m-d"));
//        $sort = "name";
//
//        $this->showList_customFilter($contentData,$frameWidth);
//        foreach ($_GET  as $key => $value) $_data[$key] = $value;
//        foreach ($_POST as $key => $value) $_data[$key] = $value;
//
//        foreach ($_data as $key => $value) {
//            switch ($key) {
//                case "sort" : break;
//                case "page" : break;
//
//                case "date" :
//                    $day = substr($value,8,2);
//                    $month = substr($value,5,2);
//                    $year = substr($value,0,4);
//                    $selectDay = mktime(12,0,0,$month,$day,$year);
//                    $dayCode = intval(date("w",$selectDay));
//                    $dayStr = cmsDates_dayStr($dayCode);
//                    echo ("Datum = $dayStr, den ".date("d.m.Y",$selectDay)."<br />");
//                    $filter[date] = $value;
//                    break;
//
//                case "category" : if ($value) $filter[$key] = $value; break;
//                case "filter_category" : if ($value) $filter["category"] = $value; break;
//                case "region"   : if ($value) $filter[$key] = $value; break;
//                case "filter_region"   : if ($value) $filter["region"] = $value; break;
//                case "region"   : if ($value) $filter[$key] = $value; break;
//
//                case "filter_dateRange"   :
//                    echo ("<h1> FILTER DATERANGE $value</h1>");
//                    $dateRangeList = $this->dateRange_filter_select_getList();
//                    //show_array($dateRangeList);
//                    $dateRange = $dateRangeList[$value];
//                    if (is_array($dateRange)) {
//                        if ($dateRange[filter]) {
//                            foreach ($dateRange[filter] as $filterKey => $filterValue) $filter[$filterKey] = $filterValue;
//                        }
//
//                    }
//                    break;
//
//                case "dateRange"   : if ($value) $filter[$key] = $value; break;
//
//                case "filter_specialView"   :
//                    $specialFilterList = $this->customFilter_specialView_getList();
//                    if (is_array($specialFilterList[$value])) {
//                        $specialFilter = $specialFilterList[$value];
//                        // APPEND Filter to Filter
//                        if (is_array($specialFilter[filter])) {
//                            foreach($specialFilter[filter] as $key => $value ) {
//                                $filter[$key] = $value;
//                                // echo "append $key = $value to filter <br />";
//                            }
//                        }
//                        if ($specialFilter[sort]) {
//                            if (is_string($specialFilter[sort]))$sort = $specialFilter[sort];
//                        }
//
//                    } else {
//                        echo ("Filter SpecialView $key = $value <br />");
//                    }
//                    break;
//
//                default :
//                    echo ("Unkown $key in get/post_data = $value <br />");
//
//            }
//        }
//
//        switch ($viewMode) {
//            case "list" : $this->article_showList($contentData,$frameWidth,$filter,$sort); break;
//            case "table" : $this->article_showTable($contentData,$frameWidth,$filter,$sort); break;
//            default :
//                echo ("unkown ViewMode in ' article_show(), $viewMode<br />");
//        }
//        div_end("article");
    }


    function article_showList($contentData,$frameWidth) {
        
        
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $showList = array();


        $showData = array();
        $showData[titleLine] = $data[titleLine];
        
        $showData[pageing] = array();
        $showData[pageing][count] = $data[pageingCount];
        $showData[pageing][showTop] = $data[pageingTop];
        $showData[pageing][showBottom] = $data[pageingBottom];
        $showData[pageing][viewMode] = "small"; // small | all//
        
        $article = $this->article_getList($contentData);

        $showList = array();
        $showList["image"] = array("name"=>"Artikelbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["fromDate"] = array("name"=>"Von","width"=>40);
        $showList["toDate"] = array("name"=>"Bis","width"=>40);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["category"] = array("name"=>"Rubrik","width"=>160,"align"=>"left");
        $showList["location"] = array("name"=>"Ort","width"=>160,"align"=>"left");
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);

        $this->showList_List($article,$showList,$showData,$frameWidth);
    }


    function showList_dataLine($data, $showData, $showList, $frameWidth) {
        div_start("articlesBox","width:".$frameWidth."px;min-height:100px;border:1px solid #ccc;margin-bottom:10px;");
        $name = $data[name];
        echo ("<h1>$name</h1>");
        $leftWidth = 200;
        $paddind = 10;
        $rightwidth = $frameWidth - $leftWidth - $padding;
        div_start("articlesBox_left","float:left;width:".$leftWidth."px;margin-right:".$padding."px;");
        echo ("Image ");
        div_end("articlesBox_left");
        div_start("articlesBox_right","float:left;width:".$rightWidth."px;");

        $subName = $data[subName];
        if ($subName) echo ("</h2>$subName</h2>");

        $info = $data[info];
        if ($info) echo ($info."<br />");

        $location    = $data[location];
        if ($location) {
            $locationData = cmsLocation_get(array("id"=>$location));
            if (is_array($locationData)) {
                echo ("Ort : $locationData[name]<br />");
            }
        }
        $category    = $data[category];
        if ($category) {
            $categoryData = cmsCategory_get(array("id"=>$category));
            if (is_array($categoryData)) {
                echo ("Rubrik : $categoryData[name]<br />");
            }
        }
        $region      = $data[region];
        if ($region) {
            $regionData = cmsCategory_get(array("id"=>$region));
            if (is_array($regionData)) {
                echo ("Region : $regionData[name]<br />");
            }
        }
        $url         = $data[url];
        $ticketUrl   = $data[ticketUrl];

        //  show_array($data);
        div_end("articlesBox_right");

        
        div_end("articlesBox");


    }

    function article_showTable($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
         
        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
        $articleList = $this->article_getList($contentData);
        if (!count($articleList)) {
            echo ("Keine Artikel gefunden <br>");
            return 0;
        }
        
        
        // show_array($projectList[0]);
        div_start("articleList","width:".$frameWidth."px;");
       
        $this->data_showTable("article",$articleList,$contentData,$frameWidth);

        div_end("articleList","before");
        return 1;
        
        
        
        
        $data = $contentData[data];
        $imgRow = $data[imgRow];
        $imgRowAbs = $data[imgRowAbs];
        $imgColAbs = $data[imgColAbs];

        if (!$imgRow) $imgRow = 3;
        if (!$imgRowAbs) $imgRowAbs = 10;
        if (!$imgColAbs) $imgColAbs = 10;

        $rowWidth = ($frameWidth - 2*$imgRowAbs ) / $imgRow;

        $filter = $data[filter];
        $sort = $data[sort];

        $debug = 0;


        $filter = array();
        // FILTER PRODUCT
        $filterArticles = $data[filterArticles];
        if ($filterArticles) {
            if ($debug) echo ("Filter Articles $filterArticles <br />");
            switch ($filterArticles) {
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


        $article = cmsArticles_getList($filter,$sort);


        if (count($article)== 0 ) {
            echo ("Kein Artikel gefunden ");
        } else {
        


            $nr = 0;
            for ($i = 0; $i<count($article); $i++) {
                $articles = $article[$i];
                $imageId = $articles[image];
                $imageData = cmsImage_getData_by_Id($imageId);
                $nr++;
                if ($nr == 1) {
                    div_start("articleLine","margin-bottom:".$imgColAbs."px");
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

                $divData[articlesId] = $articles[id];
                $divData[articlesUrl] = $articles[url];
                div_start("articleImageBox",$divData);
                $showData = array();
                $showData[frameWidth] = $rowWidth;
                $showData[frameHeight] = $rowWidth;
                $showData[vAlign] = "top";
                $showData[hAlign] = "center";
                $showData[title] = $articles[name];
                $showData[alt] = $articles[name];
                $showData[name] = $articles[name];

                $imgStr = cmsImage_showImage($imageData, $rowWidth, $showData);
                // show_array($imgData);
                echo ($imgStr);
                 // show_array($articles);

                div_end("articleImageBox");

                if ($nr == $imgRow) { // close Line
                    $nr = 0;
                    div_end("articleLine","before");
                }
            }

            if ($nr != 0) {
                div_end("articleLine","before");
            }
        }      
    }
    
    function article_showSlider($contentData,$frameWidth) {
        $articleList = $this->article_getList($contentData);
        $data = $contentData[data];
        if (!is_array($data)) $date = array();

        $imageWidth = 300;

        $ratio = 1.0 * 4 / 3;
        $randomImage = 1;


        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
        }
        
        
        $wireFrameOn = $data[wireframe];
        $wireframeState = cmsWireframe_state();
        if ($wireFrameOn AND $wireframeState) {
            $wireframeData = $contentData[wireframe];
            if (!is_array($wireframeData)) $wireframeData = array();
            // $wireframeNr = $article[id];
            // echo ("<h1> WirfreameNr $wireframeNr </h1>");
        }
        
        
        $contentList = array();

        for ($i=0;$i<count($articleList);$i++) {
            $article = $articleList[$i];
            $divStr = "";
          
            if ($clickAction) {
                $divStr .= "<div class='articleSliderItem sliderItem sliderItemClick'>";
                $divStr .= "<a href='$article[goPage]' class='hiddenLink' >$article[name]</a>";
                // foreach ($article as $key => $value) $divStr .= "| $key=$value |";
            }

            $dataType = "articleShow";
            $outData = $this->dataBox_show($dataType,$article,$contentData,$frameWidth);

            $divStr .= $outData; // $this->articleBox_show($article,$contentData,$frameWidth);
           
            if ($clickAction) $divStr .= "</div>";
            $contentList[] = $divStr;
        }

        $type = null;
        $name = "articleSlider";
        $showData = array();
        $width = $frameWidth;
        // $height =  $imageWidth / $ratio;



        $direction = $data[direction];
        if (!$direction) $direction = "horizontal";
        $loop      = $data[loop];
        if (!$loop) $loop = 0;
        $pause = $data[pause];
        if (!$pause) $pause = 5000;
        $speed = $data[speed];
        if (!$speed) $speed = 500;
        $navigate = $data[navigate];
        $pager     = $data[pager];

        $showData[loop] = $loop;
        $directionList = array("vertical","horizontal","fade");
        $showData[direction] = $direction; // $directionList[0];

        $showData[speed] = $speed;

        $showData[pause] = $pause;
        $showData[navigate] = $navigate;
        $showData[page] = $pager;
        // show_array($showData);


        cmsSlider($type,$name,$contentList,$showData,$width,$height);


    }
    
    
    function article_getList($contentData) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $filter = array();
        
        $pageData = $GLOBALS[pageData];
        
        if ($data[clickAction]) {
            $clickAction = $data[clickAction];
            $clickTarget = $data[clickTarget];
            $clickPage   = $data[clickPage];
            // echo ("KLICK action=$clickAction target=$clickTarget page=$clickPage <br>");
            
            if ($clickPage AND $clickPage != $pageData[id]) {
                // echo ("Klickpage ist not Actual Page");
                $clickPageData = cms_page_get(array("id"=>$clickPage));
                // show_array($clickPageData);
            }
        }
        
        if ($data[dynamicArticle]) {
            
            // if (!is_array($clickPageData))
            
            if (is_array($clickPageData)) $link = $clickPageData[name].".php";
            else $link = $pageData[name].".php";
            $addLink = "";
            
            $dynamicData = $pageData[data];
            if (!is_array($dynamicData)) $dynamicData = array();
            $dynamic_1 = $pageData[dynamic];
            $dynamic_2 = $dynamicData[dynamic2];
            
            if ($dynamic_1) {
                $dynamic_1_type = $dynamicData[dataSource];
                $dynamic_1_value = $_GET[$dynamic_1_type];
                if ($dynamic_1_value) {
                    if ($addLink) $addLink .= "&";
                    $addLink .= $dynamic_1_type."=".$dynamic_1_value;
                    switch ($dynamic_1_type) {
                        case "category" :
                            $filter[category] = $dynamic_1_value;
                            break;
                        case "article" : 
                            break;
                        default:
                            echo ("unkown dynamicTyp '$dynamic_1_type' = $dynamic_1_value <br> ");
                    }
                } else {
                    
                    
                }
            }
            
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                if ($dynamic_2_value) {
                    if ($addLink) $addLink .= "&";
                    $addLink .= $dynamic_2_type."=".$dynamic_2_value;
                    switch ($dynamic_2_type) {
                        case "category" :
                            $filter[category] = $dynamic_2_value;
                            break;
                        case "article" : 
                            break;
                        default:
                            echo ("unkown dynamicTyp '$dynamic_2_type' = $dynamic_2_value <br> ");
                    }
                }
            }
            
            
            
        }
        $maxCount = intval($data[maxCount]);
        
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
        $sort = "name";


        $articleList = cmsArticles_getList($filter,$sort,"out_");


        if (!count($articleList)) {
            return array();
        }
           // echo ("$maxCount anzahl=".count($articleList)."<br>");
        if ($maxCount>0 AND count($articleList)>$maxCount) {
            // echo ("<h1>Anzahl Projekte $maxCount </h1>");
            $newList = array();
            $idList = array();

            while (count($newList) < $maxCount) {
                $randomNr = rand(0, count($articleList)-1);
                $randomId = $articleList[$randomNr][id];
                if (!$idList["$randomNr"]) {
                    $idList["$randomNr"] = 1;
                    $newList[] = $articleList[$randomNr];
                    // echo ("RandomNr=$randomNr RandomId=$randomId<br />");
                } else {
                    // echo ("Allready in List $randomNr<br />");
                }
            }         
            $articleList = $newList;
        }
        if ($data[dynamicArticle]) {
            
//            echo ("<h1> $link -> $addLink </h1>");
//            echo ("dyn1 $dynamic_1_type $dynamic_1_value <br>");
//            echo ("dyn2 $dynamic_2_type $dynamic_2_value <br>");
            for ($i=0;$i<count($articleList);$i++) {
                $id = $articleList[$i][id];
                if ($dynamic_2_type == "article" AND !$addLink) {
                    // echo ("NO $dynamic_1_value from Type $dynamic_1_type <br>");
                    $dyn_value = "";
                    switch ($dynamic_1_type) {
                        case "category" :
                            $dyn_value = $articleList[$i][category];
                            // echo ("$dyn_value<br>");
                            break;
                        default :
                            echo ("unkown $dynamic_1_value in addLink to ArticleList<br>");
                    
                        
                    }
                    if ($dyn_value) {
                        $dyn_list = explode("|",$dyn_value);
                        if (count($dyn_list)>1) {
                            $dyn_value = $dyn_list[1];
                        }
                        $addLink = $dynamic_1_type."=".$dyn_value;
                    }
                    
                    
                    
                }
                
                if ($addLink) $addArticle = $addLink."&";
                else $addArticle = "";
                $addArticle .= "article=".$id;
                
                // echo ("Go To $link ? $addArticle <br>");
                $articleList[$i][goPage] = $link."?".$addArticle;
                
            }
        }
        
        
        return $articleList;
    }
    

    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"] = "Liste";
        $res["table"] = "Tabelle";
        $res["slider"] = "Artikel Slider";
        $res["article"] = "Artikel";
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

        $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
        // $filterList[specialView]["sort"] = "name";
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;


        $filterList[dateRange] = array();
        $filterList[dateRange]["name"] = "Zeitraum";
        $filterList[dateRange]["type"] = "dateRange";
        $filterList[dateRange]["dataName"] = "dateRange";
        $filterList[dateRange]["showData"] = array("submit"=>1,"empty"=>"Zeitraum nicht einschränken");
        $filterList[dateRange]["filter"] = array("mainCat"=>1,"show"=>1);
        $filterList[dateRange]["sort"] = "";
        $filterList[dateRange][customFilter] = 1;

        $filterList[produkt] = 0;
        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Alle Kategorien zeigen");
        $filterList[category]["filter"] = array("mainCat"=>144,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Alle Region zeigen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        return $filterList;
    }



    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        
        
        $res = array();
        $mainTab = "article";
        
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$mainTab][] = $viewModeList[$i];
            }
        }

        $showList = $this->articleShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
        $addToTab = "articleShow";
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
            if ($clickAction == "showProject" OR $clickAction == "showCategory") {

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
            if ($clickAction == "showArticles" OR $clickAction == "showCategory") {

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
    
    function dataShow_List($contentData) {
        return $this->articleShow_List();        
    }
    
    
    function articleShow_List() {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[customer] = array("name"=>"Kunde","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
    }

//    function filter_select($filterType,$code,$dataName,$showData) {
//        $filter = $showData[filter];
//        $sort =   $showData[sort];
//        $selectList = $this->filter_select_getList($filterType,$filter,$sort);
//        $str = "";
//        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
//        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' ";
//        if ($showData[submit]) $str.= "onChange='submit()' ";
//        $str .= "value='$code' >";
//
//        $str.= "<option value='0'";
//        if (!$code) $str.= " selected='1' ";
//
//        $str.= ">Keine Filter</option>";
//
//        foreach ($selectList as $key => $value) {
//            $str.= "<option value='$key'";
//            if ($key == $code)  $str.= " selected='1' ";
//            $str.= ">$value</option>";
//        }
//        $str.= "</select>";
//        return $str;
//    }


   function filter_select_getList_own($filterType,$filter,$sort) {
       // echo ("filter_select_getList_own($filterType,$filter,$sort)");
        switch ($filterType) {
            case "viewMode" :
                $res = $this->viewMode_filter_select_getList($filter,$sort);
                return $res;

        }
        echo ("function filter_select_getList_own($filterType,$filter,$sort) <br />");

    }
//    function viewMode_filter_select_getList($filter,$sort) {
//        $res = array();
//        $res["list"] = "Liste";
//        $res["table"] = "Tabelle";
//
//        $ownList = $this->viewMode_filter_select_getOwnList($filter,$sort);
//        foreach ($ownList as $key => $value) {
//            $res[$key] = $value;
//        }
//        return $res;
//    }




    function articles_filter_select_getList($filter,$sort) {
        $res = array();
        $res["all"] = "Alle Artikel";
        $res["new"] = "Neue Artikel";
        $res["highlight"] = "Highlight Artikel";

        $ownList = $this->articles_filter_select_getOwnList($filter,$sort);
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function articles_filter_select_getOwnList($filter,$sort) {
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
        $res["showArticles"] = "Artikel zeigen";
        
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

        $res["showArticles"] = "ArtikelInfo zeigen";
        
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

function cmsType_article_class() {
    if ($GLOBALS[cmsTypes]["cmsType_article.php"] == "own") $articleClass = new cmsType_article();
    else $articleClass = new cmsType_article_base();
    return $articleClass;
}

function cmsType_article($contentData,$frameWidth) {
    $articleClass = cmsType_article_class();
    return $articleClass->show($contentData,$frameWidth);
}



function cmsType_article_editContent($editContent,$frameWidth) {
    $articleClass = cmsType_article_class();
    return $articleClass->article_editContent($editContent,$frameWidth);
}

function cmsType_article_getName() {
    $articleClass = cmsType_article_class();
    $name = $articleClass->getName();
    return $name;
}


?>
