<?php
function cms_dynamicPage_showTitleBar($pageData) {
    $dynamic = $pageData[dynamic];
    if (!$dynamic) return 0;
    
    
    $dynamicData = $pageData[data];
    if (!is_array($dynamicData)) $dynamicData = array();
    $pageId = $pageData[id];


    //echo ("cms_content_show($pageorFrame)<br>");

    $newPage = "dynamic_".$pageId."-";

    $dynamic_1 = $pageData[dynamic];
    $dynamic_2 = $dynamicData[dynamic2];
    $dynamic_1_type = $dynamicData[dataSource];
    $dynamic_1_value = $_GET[$dynamic_1_type];

    // show_array($dynamicData);
    if ($dynamic_1_value) {
         // echo ("Dynamic 1 = '$dynamic1' = $dynamic1Set <br>");
         $newPage .= "1";

         if ($dynamic_2) {
            $dynamic_2_type = $dynamicData[dataSource2];
            $dynamic_2_value = $_GET[$dynamic_2_type];
            // echo ("Dynamic 2 = '$dynamic2' = $dynamic2Set <br>");

            if ($dynamic_2_value) $newPage.= "-1";
            else $newPage.= "-0";
         }
    } else {
        $newPage .= "0";
    }

    $showBar = 1;
    if (!$showBar) return $newPage;
    
    
    $edit = $_SESSION[edit];
    $editable = ($_SESSION[userLevel]>6);
    
    if ($editable) {
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";
        
        div_start("dynamicTitleFrame $addEditClass");
        echo ("DynamicPage '<b>$newPage</b><br />");

        $url = $pageData[name].".php";
        $urlAdd = "";

        if ($dynamic_1) {
            echo ("Dynamische Ebene 1: type='$dynamic_1_type'");
            
//            if ($dynamic_1_value) {
//                $dynamic_1_Name = cms_dynamicPage_getInfo($dynamicData,1);
//                echo (" => $dynamic_1_Name ");
//                
////                if ($urlAdd) $urlAdd .= "&";
////                else $urlAdd .= "?";
////                $urlAdd = $dynamic_1_type."=".$dynamic_1_value
//                
//            } else {
//                echo (" - not Set ");
//            }
            
            switch ($dynamic_1_type) {
                case "category" :
                    $selectCatId = $dynamic_1_value;
                    $showData = array();
                    $mainCat = 0;
                    if ($dynamicData[mainCat]) $mainCat = $dynamicData[mainCat];
                    if ($dynamicData[subCat]) $mainCat = $dynamicData[subCat];
                    $showData[mainCat] = $mainCat;
                    $catList = cmsCategory_getList($showData);

                   

                    for ($i=0;$i<count($catList);$i++) {
                        $catData = $catList[$i];
                        $catId = $catData[id];
                        $catName = $catData[name];
                        
                        $class = "cmsDynamicSelect";
                        if ($catId == $selectCatId) $class.= " cmsDynamicSelect_active";
                        
                        echo ("<a href='".$url.$urlAdd."?category=$catId' class='$class' >$catName</a>");                        
                    }
                    
                    if ($selectCatId) {
                        echo ("<a href='".$url.$urlAdd."?category=0' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                        
                        if ($urlAdd) $urlAdd .= "&";
                        else $urlAdd .= "?";
                        $urlAdd .= $dynamic_1_type."=".$dynamic_1_value;
                        // echo ("<h2>SET urlAdd to '$urlAdd</h2>");
                    } 
                    break;

                case "project" :
                    $selectProjectId = $dynamic_1_value;
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    
                    $projectList = cmsProject_getList($showData,"name","out__");
                    for ($i=0;$i<count($projectList);$i++) {
                        $projData = $projectList[$i];
                        $projId = $projData[id];
                        $projName = $projData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&project=$projId";
                        } else {
                            $add = "?project=$projId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($projId == $selectProjectId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$projName</a>");

                    }
                    
                    if ($selectProjectId) {
                        echo ("url = $url add= $urlAdd ");
                        // $add = "";
                        if ($urlAdd) {
                            $add = $urlAdd;
                        } else {
                            $add = "";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    
                    break;
                    
            case "product" :
                    $selectProductId = $dynamic_1_value;
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    
                    $productList = cmsProduct_getList($showData,"name","out__");
                    for ($i=0;$i<count($productList);$i++) {
                        $prodData = $productList[$i];
                        $prodId = $prodData[id];
                        $prodName = $prodData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&product=$prodId";
                        } else {
                            $add = "?product=$prodId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($prodId == $selectProductId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$prodName</a>");

                    }
                    
                    if ($selectProductId) {
                        // echo ("url = $url add= $urlAdd ");
                        // $add = "";
                        if ($urlAdd) {
                            $add = $urlAdd;
                        } else {
                            $add = "";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    
                    break;                    
                    
                case "company" :
                    $selectCompanyId = $dynamic_1_value;
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    
                    $companyList = cmsCompany_getList($showData,"name","out__");
                    for ($i=0;$i<count($companyList);$i++) {
                        $compData = $companyList[$i];
                        $compId = $compData[id];
                        $compName = $compData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&company=$compId";
                        } else {
                            $add = "?company=$compId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($compId == $selectCompanyId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$compName</a>");

                    }
                    
                    if ($selectCompanyId) {
                        // echo ("url = $url add= $urlAdd ");
                        // $add = "";
                        if ($urlAdd) {
                            $add = $urlAdd;
                        } else {
                            $add = "";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    
                    break;        
                case "article" :
                    $selectArticleId = $dynamic_1_value;
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    $articleList = cmsArticles_getList($showData,"name","out__");
                    for ($i=0;$i<count($articleList);$i++) {
                        $articleData = $articleList[$i];
                        $articleId = $articleData[id];
                        $articleName = $articleData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&article=$articleId";
                        } else {
                            $add = "?article=$articleId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($articleId == $selectArticleId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$articleName</a>");
                    }
                    
                    if ($selectArticleId) {
                        if ($urlAdd) {
                            $add = $urlAdd;
                        } else {
                            $add = "";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    break;   
                    
                case "dates" :
                    $selectDateId = $dynamic_1_value;
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    $dateList = cmsDates_getList($showData,"name","out__");
                    for ($i=0;$i<count($dateList);$i++) {
                        $dateData = $dateList[$i];
                        $dateId = $dateData[id];
                        $dateName = $dateData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&dates=$dateId";
                        } else {
                            $add = "?dates=$dateId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($dateId == $selectDateId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$dateName</a>");
                    }
                    
                    if ($selectDateId) {
                        if ($urlAdd) {
                            $add = $urlAdd;
                        } else {
                            $add = "";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    break;                                   
                    

            }
            echo ("<br />");

        }

        if ($dynamic_2) {
            echo ("Dynamische Ebene 2: type='$dynamic_2_type' ");

            switch ($dynamic_2_type) {
                case "project" :
                    $selectProjectId = $dynamic_2_value;
                    // $url = $pageData[name].".php";
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    
                    $projectList = cmsProject_getList($showData,"name","out__");
                    for ($i=0;$i<count($projectList);$i++) {
                        $projData = $projectList[$i];
                        $projId = $projData[id];
                        $projName = $projData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&project=$projId";
                        } else {
                            $add = "?project=$projId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($projId == $selectProjectId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$projName</a>");

                    }
                    
                    if ($selectProjectId) {
                        if ($urlAdd) {
                            $add = $urlAdd."&project=0";
                        } else {
                            $add = "?project=0";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    break;
                case "product" :
                    $selectProductId = $dynamic_2_value;
                    // $url = $pageData[name].".php";
                    $showData = array();
                    $showData[show] = 1;
                    if ($selectCatId) $showData[category] = $selectCatId;
                    
                    $productList = cmsproduct_getList($showData,"name","out__");
                    for ($i=0;$i<count($productList);$i++) {
                        $prodData = $productList[$i];
                        $prodId = $prodData[id];
                        $prodName = $prodData[name];
                        if ($urlAdd) {
                            $add = $urlAdd."&product=$prodId";
                        } else {
                            $add = "?product=$prodId";
                        }
                        
                        $class = "cmsDynamicSelect";
                        if ($prodId == $selectProductId) $class.= " cmsDynamicSelect_active";
                        echo ("<a href='".$url.$add."' class='$class' >$prodName</a>");

                    }
                    
                    if ($selectProductId) {
                        if ($urlAdd) {
                            $add = $urlAdd."&product=0";
                        } else {
                            $add = "?product=0";
                        }
                        echo ("<a href='".$url.$add."' class='cmsDynamicSelect cmsDynamicSelect_clear' >nicht gewählt</a>");  
                    }
                    break;
                    
                default : 
                    echo ("Unkown Type '$dynamic_2_type' in show_dynamicTitleBar ");
                        
            }
        }

        div_end("dynamicTitleFrame $addEditClass","before");
    }


    return $newPage;
}

function cms_dynamicPage_breadCrumb($pageData,$addArray=0) {
    $res = array();
    $dynamicData = $pageData[data];
    if (!is_array($dynamicData)) $dynamicData = array();
    // echo ("<h1> Call cms_dynamicPage_breadCrumb </h1>");
    // if (is_array($addArray)) show_array($addArray);
    $dynamic_1_type = $dynamicData[dataSource];
    if ($dynamic_1_type) {
        if (is_array($addArray)) $dynamic_1_value = $addArray[$dynamic_1_type];
        else $dynamic_1_value = $_GET[$dynamic_1_type];

        
        $dynamicContentData = cms_dynamicPage_getData($dynamicData,1,$addArray);
        $dynamicName = $dynamicContentData[name];
        $icon = $dynamicContentData[image];
        
        
        if ($dynamic_1_value) {
            $dynamic_2 = $dynamicData[dynamic2];
            if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                if (is_array($addArray)) {
                    // echo ("suche hier nach $dynamic_2_type wäre $addArray[project] <br>");
                    $dynamic_2_value = $addArray[$dynamic_2_type];
                }
                else $dynamic_2_value = $_GET[$dynamic_2_type];
               // echo ("<h1>Suche Dyn2 '$dynamic_2_type' '$dynamic_2_value' </h1>");
                if ($dynamic_2_value) {
                    $dynamicContentData = cms_dynamicPage_getData($dynamicData,2,$addArray);
                    $dynamicName2 = $dynamicContentData[name];
                    $icon2 = $dynamicContentData[image];
                    // echo ("found $dynamicName $icon <br>");
                    // echo ("found $dynamicName <br>");
                    $goPage2 = $pageData[name].".php?".$dynamic_1_type."=".$dynamic_1_value."&".$dynamic_2_type."=".$dynamic_2_value;
                    $res[] = array("name"=>$dynamicName2,"url"=>$goPage2,"id"=>"","icon"=>$icon2);
                    // echo ("<h3>ADD $dynamicName </h3>");
                }
            }

            $goPage = $pageData[name].".php?".$dynamic_1_type."=".$dynamic_1_value;
            $res[] = array("name"=>$dynamicName,"url"=>$goPage,"id"=>"","icon"=>$icon);
            // echo ("<h2>ADD $dynamicName </h2>");
            
           
         }
    }
    // show_array($res);
    return $res;
}

function cms_dynamicPage_getList($dynamicData,$dynamicLevel,$addArray=0) {
    $dynamic_1 = 1;
    $dynamic_1_type = $dynamicData[dataSource];
    $dynamic_1_value = $_GET[$dynamic_1_type];
    if (is_array($addArray)) {
        if ($addArray[$dynamic_1_type]) $dynamic_1_value = $addArray[$dynamic_1_type];
    }
    
    $dynamic_2 = $dynamicData[dynamic2];
    if ($dynamic_2) {
        $dynamic_2_type = $dynamicData[dataSource2];
        $dynamic_2_value = $_GET[$dynamic_2_type];
        if (is_array($addArray)) {
            if ($addArray[$dynamic_1_type]) $dynamic_1_value = $addArray[$dynamic_1_type];
            if ($addArray[$dynamic_2_type]) $dynamic_2_value = $addArray[$dynamic_2_type];
            //echo ("<h2>SET dynamic2Value Level 1 $dynamic_1_type $dynamic_1_value </h2>");
            //echo ("<h2>SET dynamic2Value Level 2 $dynamic_2_type $dynamic_2_value </h2>");
        }
    }
    
    switch ($dynamicLevel) {
        case 1:
            $showData = array();
            switch ($dynamic_1_type) {
                case "category" : 
                    $mainCat = $dynamicData[mainCat];
                    $subCat  = $dynamicData[subCat];
                    if ($mainCat) $showData[mainCat] = $mainCat;
                    if ($subCat) $showData[subCat] = $subCat;
                    $list = cms_dynamicPage_getList_category($dynamicData,$dynamic_1_value,$showData);
                    break;
                    
                case "project"  :
                    $list = cms_dynamicPage_getList_project($dynamicData,$dynamic_1_value,$showData);
                    break;

                case "product"  :
                    $list = cms_dynamicPage_getList_product($dynamicData,$dynamic_1_value,$showData);
                    break;
                
                case "company"  :
                    $list = cms_dynamicPage_getList_company($dynamicData,$dynamic_1_value,$showData);
                    break;

                case "article"  :
                    $list = cms_dynamicPage_getList_article($dynamicData,$dynamic_1_value,$showData);
                    break;

                case "dates"  :
                    $list = cms_dynamicPage_getList_dates($dynamicData,$dynamic_1_value,$showData);
                    break;
                
                default:
                    echo ("<h1> UNKOWN $dynamic_1_type </h1>");

                
            }
            break;
        case 2:
           
            $showData = array($dynamic_1_type => $dynamic_1_value);
            switch ($dynamic_2_type) {
                case "category" : 
                    $mainCat = $dynamicData[mainCat2];
                    $subCat  = $dynamicData[subCat2];
                    if ($mainCat) $showData[mainCat] = $mainCat;
                    if ($subCat) $showData[subCat] = $subCat;
                    // $list = cms_dynamicPage_getList_category($dynamicData,$dynamic_2_value,$showData);
                    break;
                case "project"  :
                    // echo ("<h1>ADD PROJECT TO LEVEL 2 $dynamic_2_type,$dynamic_2_value,</h1>");
                    $list = cms_dynamicPage_getList_project($dynamicData,$dynamic_2_value,$showData);
                    // echo ("Found ".count($list)."ITEMS <br>");
                    break;
                 case "product"  :
                    // echo ("<h1>ADD PRODUCT TO LEVEL 2 $dynamic_2_type,$dynamic_2_value,</h1>");
                    $list = cms_dynamicPage_getList_product($dynamicData,$dynamic_2_value,$showData);
                    // echo ("Found ".count($list)."ITEMS <br>");
                    break;
            }
            break;
    }
    return $list;
}

function cms_dynamicPage_getList_category($dynamicData,$selectId,$showData) {
    
    
    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) { 
        switch ($key) {
            case "mainCat" : 
                $filter[$key] = $value; 
//                if ($addUrl) $addUrl .= "&";
//                $addUrl = $key."=".$value;
                break;
            case "project" : break;
            
            default :
                echo ("unkown key $key in cms_dynamicPage_getList_category <br/>");
                
        }
    }
    
    $categoryList = cmsCategory_getList($filter,"name","out__");
    for ($i=0;$i<count($categoryList);$i++) {
        $catData = $categoryList[$i];
        $catId = $catData[id];
        $catName = $catData[name];
        
        $addData = array();
        $addData[id] = $catId;
        $addData[name] = $catName;
        $addData[image] = $catData[image];
        $addData[value] = $catId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "category=$catId";
        $addData[url] = $url;
        if ($catId == $selectId) $addData[active] = 1;
        
        $res[$catId] = $addData;
    }
    return $res;
    
}

function cms_dynamicPage_getList_project($dynamicData,$selectId,$showData) {
    
    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) { 
        switch ($key) {
            case "category" : 
                $filter[$key] = $value; 
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            
            default :
                echo ("unkown key $key in cms_dynamicPage_getList_project <br/>");
                
        }
    }

    $res = array();
    $projectList = cmsProject_getList($filter,"name","out__");
    for ($i=0;$i<count($projectList);$i++) {
        $projData = $projectList[$i];
        $projId = $projData[id];
        $projName = $projData[name];
        
        $addData = array();
        $addData[id] = $projId;
        $addData[name] = $projName;
        $addData[image] = $projData[image];
        $addData[value] = $projId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "project=$projId";
        // echo ("add $projName to $url <br>");
        $addData[url] = $url;
        // $addData[filter] = $filter;
        if ($projId == $selectId) $addData[active] = 1;
        
        $res[$projId] = $addData;
    }
    return $res;
}


function cms_dynamicPage_getList_product($dynamicData,$selectId,$showData) {

    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "category" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;

            default :
                echo ("unkown key $key in cms_dynamicPage_getList_product <br/>");

        }
    }

    $res = array();
    $projectList = cmsProduct_getList($filter,"name","out__");
    for ($i=0;$i<count($projectList);$i++) {
        $projData = $projectList[$i];
        $projId = $projData[id];
        $projName = $projData[name];

        $addData = array();
        $addData[id] = $projId;
        $addData[name] = $projName;
        $addData[image] = $projData[image];
        $addData[value] = $projId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "product=$projId";
        // echo ("add $projName to $url <br>");
        $addData[url] = $url;
        // $addData[filter] = $filter;
        if ($projId == $selectId) $addData[active] = 1;

        $res[$projId] = $addData;
    }
    return $res;
}


function cms_dynamicPage_getList_company($dynamicData,$selectId,$showData) {     
    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "product" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            case "project" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            default :
                echo ("unkown key $key in cms_dynamicPage_getList_company <br/>");

        }
    }

    $res = array();
    $companyList = cmsCompany_getList($filter,"name","out__");
    for ($i=0;$i<count($companyList);$i++) {
        $compData = $companyList[$i];
        $compId = $compData[id];
        $compName = $compData[name];

        $addData = array();
        $addData[id] = $compId;
        $addData[name] = $compName;
        $addData[image] = $compData[image];
        $addData[value] = $compId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "company=$compId";
        // echo ("add $projName to $url <br>");
        $addData[url] = $url;
        // $addData[filter] = $filter;
        if ($compId == $selectId) $addData[active] = 1;

        $res[$compId] = $addData;
    }
    return $res;
}
    
function cms_dynamicPage_getList_article($dynamicData,$selectId,$showData) {   
    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "category" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            case "project" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            default :
                echo ("unkown key $key in cms_dynamicPage_getList_article <br/>");

        }
    }

    $res = array();
    $articleList = cmsArticles_getList($filter,"name","out__");
    for ($i=0;$i<count($articleList);$i++) {
        $articleData = $articleList[$i];
        $articleId = $articleData[id];
        $articleName = $articleData[name];

        $addData = array();
        $addData[id] = $articleId;
        $addData[name] = $articleName;
        $addData[image] = $articleData[image];
        $addData[value] = $articleId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "article=$articleId";
        // echo ("add $projName to $url <br>");
        $addData[url] = $url;
        // $addData[filter] = $filter;
        if ($articleId == $selectId) $addData[active] = 1;

        $res[$articleId] = $addData;
    }
    return $res;    
}
function cms_dynamicPage_getList_dates($dynamicData,$selectId,$showData) {   
    $filter = array();
    $filter[show] = 1;
    $addUrl = "";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "category" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            case "project" :
                $filter[$key] = $value;
                if ($addUrl) $addUrl .= "&";
                $addUrl = $key."=".$value;
                break;
            default :
                echo ("unkown key $key in cms_dynamicPage_getList_dates <br/>");

        }
    }

    $res = array();
    $dateList = cmsDates_getList($filter,"name","out__");
    for ($i=0;$i<count($dateList);$i++) {
        $dateData = $dateList[$i];
        $dateId = $dateData[id];
        $dateName = $dateData[name];

        $addData = array();
        $addData[id] = $dateId;
        $addData[name] = $dateName;
        $addData[image] = $dateData[image];
        $addData[value] = $dateId;
        $url = $addUrl;
        if ($url) $url.= "&";
        $url .= "dates=$dateId";
        // echo ("add $projName to $url <br>");
        $addData[url] = $url;
        // $addData[filter] = $filter;
        if ($dateId == $selectId) $addData[active] = 1;

        $res[$dateId] = $addData;
    }
    return $res; 
}


function cms_dynamicPage_getInfo($dynamicData,$dynamicLevel,$addArray=0) {
    $dynamicContentData = cms_dynamicPage_getData($dynamicData,$dynamicLevel,$addArray);
    if (is_array($dynamicContentData)) {
        $dynamicName = $dynamicContentData[name];
    } 
}

function cms_dynamicPage_getData($dynamicData,$dynamicLevel,$addArray=0) {
    // show_array($addArray);
    switch ($dynamicLevel) {
        case 1:
            $dynamic_type = $dynamicData[dataSource];
            $res = "DYNAMISCH ".$dynamic;
            break;
        case 2:
            $dynamic_type = $dynamicData[dataSource2];
            $res = "DYNAMISCH ".$dynamic;
            break;
        
        default:
            return 0;
    }

    switch ($dynamic_type) {
        case "category" :
            if (is_array($addArray)) $categoryId = $addArray[category];                
            else $categoryId = $_GET[category];
            $categoryData = cmsCategory_get(array("id"=>$categoryId));
            if (is_array($categoryData)) $res = $categoryData;            
            break;

        case "project" :
            if (is_array($addArray)) $projectId = $addArray[project];
            else $projectId = $_GET[project];
            $projectData = cmsProject_get(array("id"=>$projectId));            
            if (is_array($projectData)) $res = $projectData;
            break;

        case "product" :
            if (is_array($addArray)) $productId = $addArray[product];
            else $productId = $_GET[product];
            $productData = cmsProduct_get(array("id"=>$productId));
            if (is_array($productData)) $res = $productData;
            break;
        case "company" :
            if (is_array($addArray)) $companyId = $addArray[company];                
            else $companyId = $_GET[company];
            $companyData = cmsCompany_get(array("id"=>$companyId));
            if (is_array($companyData)) $res = $companyData;
            
            break;  
        case "article" :
            if (is_array($addArray)) $articleId = $addArray[company];                
            else $articleId = $_GET[article];
            $articleData = cmsArticles_get(array("id"=>$articleId));
            if (is_array($articleData)) $res = $articleData;
            break;  
        case "dates" :
            if (is_array($addArray)) $dateId = $addArray[company];                
            else $dateId = $_GET[date];
            $dateData = cmsDates_get(array("id"=>$dateId));
            if (is_array($dateData)) $res = $dateData;
            break;  
            
            
        default:
            // echo ("dynamicData,$dynamicLevel,$addArray<br>");
            // show_array($addArray);
            $res = array("name"=>"unkown dynamic '$dynamic_type'");
    }
    return $res;
}



function cms_dynamicPage_getSortList($pageData) {
   
    $res = array();     
    
    $data = $pageData[data];
    if ($data AND is_string($data)) $data = str2Array ($data);
    if (!is_array($data)) $data=array();
    
    
    $dataSource = $data[dataSource];
    
    if ($data[dynamic2] AND $data[dataSource]) {
        $dataSource2 = $data[dataSource2];
        // echo ("ADD '$dataSource2' <br />");
    }
 
    $list = cms_dynamicPage_getList($data,1);
    // echo ("<h2>list $list ".count($list)."</h2>");
    if (is_array($list) AND count ($list)) {
        foreach ($list as $projId => $subData) { // ($i = 0;$i<count($list);$i++) {
            $name = $subData[name];

            $subPageName = "$pageData[title]_$name";

            $subPageData = array();
            $subPageData[id] = $pageData[id];
            $subPageData[subId] = $pageData[id]."_".$projId;
            $link = $dataSource."=".$projId;
            // echo ("ADD SubID ".$pageData[id]."_$projId link = $link<br>");
            $subPageData[link] = $link;
            $subPageData[name] = $pageData[name];
            $subPageData[addUrl] = $subData[url];
            $subPageData[title] = "$name";
            $subPageData[show] = $show;
            $subPageData[image] = $subData[image];
            $subPageData[navigation] = $pageData[navigation];
            $subPageData[dynamicContent] = 1;


            $dynamic_1_type = $data[dataSource];
            $dynamic_1_value = $subData[value];

            if ($dataSource2) {
                $subNavi = array();
                $addArray = array($dynamic_1_type => $dynamic_1_value);
                $subList = cms_dynamicPage_getList($data,2,$addArray);
                if (is_array($subList) AND count ($subList)) {
                    foreach ($subList as $projId => $subSubData) { // ($i = 0;$i<count($list);$i++) {
                        $subName = $subSubData[name];
                        $addLink = $link."&".$dataSource2."=".$projId;
                        // echo ("add subData $subName to $name $subSubData[url]<br>");
                        // echo ("add LInk 2 = $addlink <br />");
                        $subSubPageName = "$pageData[title]_$subName";

                        $subSubPageData = array();
                        $subSubPageData[id] = $pageData[id];
                        $subSubPageData[subId] = $pageData[id]."_".$i;
                        $subSubPageData[link] = $addLink;
                        $subSubPageData[name] = $pageData[name];
                        $subSubPageData[addUrl] = $subSubData[url];
                        $subSubPageData[title] = $subName;
                        $subSubPageData[show] = $show;
                        $subSubPageData[image] = $subSubData[image];
                        $subSubPageData[navigation] = $pageData[navigation];
                        $subSubPageData[dynamicContent] = 1;

                        $subNavi[$subSubPageName] = $subSubPageData;

                    }
                    $subPageData[subNavi] = $subNavi;
                }

            }

            $res[$subPageName] = $subPageData;


        }
    }
    
    return $res;
}

function cms_dynamicPage_getSortList_data($type,$page,$filterLevel=0,$level=0) {
    // echo ("cms_page_dynamic_getSortList_data($type,$page,$filterLevel,$level)<br>");
    $filter = array();
    $filter[show] = 1;
    $sort = "name";
    
    if (!is_array($page[data])) $page[data] = str2Array($page[data]);

    if ($level) {
        $filterTypeLevel = $page[data][dataSource];
        // echo ("FilterType Level 0 = $filterTypeLevel<br>");
        switch ($filterTypeLevel) {
            case "category" :
                $mainId = $page[data][mainId];
                $subId  = $page[data][subId];
                if ($subId) $filter[category] = $subId;
                else $filter[category] = $mainId;
                break;
                
        }
    }
    
    
    switch ($type) {
        case "category" :
            
            break;
        case "project"  :
            if ($level==0) {
                $res = cmsProject_getList($filter, $sort);
                
            } else {
                if ($filterLevel) {
                    switch ($filterTypeLevel) {
                        case "category" :
                            $filter[category] = $filterLevel;
                            break;
                    }
                }
            
                $res = cmsProject_getList($filter, $sort,"_out");                                
            }
            break;
    }
    return $res;
}


function cms_dynamicPage_addUrl($pageData) {
    $dynamic_1 = $pageData[dynamic];
    
    if (!$dynamic_1) return "";
    
    $data = $pageData[data];
    if (!is_array($data)) $data = array();   
    $out = "";
    
    $dynamic_1_source = $data[dataSource];
    $dynamic_1_get    = $_GET[$dynamic_1_source];
    if ($dynamic_1_get) {
        $out .= $dynamic_1_source."=".$dynamic_1_get;
    }
    
    $dynamic_2 = $data[dynamic2];
    if ($dynamic_2) {
        $dynamic_2_source = $data[dataSource2];
        $dynamic_2_get    = $_GET[$dynamic_2_source];
        if ($dynamic_2_get) {
            if ($out) $out .= "&";
            $out .= $dynamic_2_source."=".$dynamic_2_get;
        }
    }
    return $out;
}


function cms_dynamicPage_Source_getSortList() {
    $res = array();
    $res[category] = "Kategorien";
    $res[project]  = "Projekte";  
    $res[company]  = "Hersteller"; 
    $res[product]  = "Produkte";        
    $res[article]  = "Artikel";  
    $res[location] = "Location";  
    $res[dates]    = "Termine";  
    return $res;
}





function cms_dynamicPage_Source($pageId,$dataName) {
    // echo ("cms_page_SelectMainPage($pageId,$dataName)<br />");
    $pageList = cms_dynamicPage_Source_getSortList();
    // foreach($pageList as $key => $value) echo ("page $key = $value <br />");

    
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$pageId' >";
    
    foreach ($pageList as $pageName => $showName) {
        // $id = $pageData[id];
        $str.= "<option value='$pageName'";

        if ($pageId == $pageName) $str.= " selected='1' ";
        
        $str.= ">$showName</option>";       
    }
    $str.= "</select>";
    return $str;


}

function cms_dynamicPage_editSource($editPage,$sourceType,$level,$formName="") {
     //echo ("editSource $sourceType <br>");
     $data = $editPage[data];
     if (!is_array($data)) $data = array();
     $res = array();
    
     switch ($sourceType) {
        case "category" :

            $dataName = "mainCat";
            if ($level>1) $dataName.= $level;
            $mainCat = $data[$dataName];

 
            $showData = array();
            $filter = array("mainCat"=>"0");
            $sort = "name";
            if ($formName) $formName = $formName.= "[data][$dataName]";
            else $formName = "editPage[data][$dataName]";
            
            $input = cmsCategory_selectCategory($mainCat,$formName, $showData, $filter, $sort);
            // $input .= "<br>FormName = $formName / $mainCat <br>";
            
            $add = array();
            $add[name] = "Kategorie wählen:";
            $add[input] = $input;
            $res[] = $add;
            

            if ($mainCat) {
                $dataName = "subCat";
                if ($level>1) $dataName.=$level;

                // echo ($div1."Unter-Kategorie wählen:");
                $subCat = $editPage[data][$dataName];
                $showData = array();
                $showData["empty"] = "Keine Unterkategorie";
                $filter = array("mainCat"=>$mainCat);
                $sort = "name";
                $formName = "editPage[data][$dataName]";
                $input = cmsCategory_selectCategory($subCat,$formName, $showData, $filter, $sort);
                // $input .= "<br>FormName = $formName ";

                $add = array();
                $add[name] = "Unter-Kategorie wählen:";
                $add[input] = $input;
                $res[] = $add;
                
            }
            break;
     }
     return $res;
}



?>
