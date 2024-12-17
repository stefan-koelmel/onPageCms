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
    $dynamic_1_Value = $_GET[$dynamic_1_type];

    if ($dynamic_1_Value) {
         // echo ("Dynamic 1 = '$dynamic1' = $dynamic1Set <br>");
         $newPage .= "1";

         if ($dynamic_2) {
            $dynamic_2_type = $dynamicData[dataSource2];
            $dynamic_2_value = $_GET[$dynamic_2_type];
            //echo ("Dynamic 2 = '$dynamic2' = $dynamic2Set <br>");

            if ($dynamic_2_value) $newPage.= "-1";
            else $newPage.= "-0";
         }
    } else {
        $newPage .= "0";
    }

    if ($_SESSION[edit]) {
        div_start("dynamicTitleFrame");
        echo ("DynamicPage '<b>$newPage</b><br />");

        $url = $pageData[name].".php";
        $urlAdd = "";

        if ($dynamic_1) {
            echo ("Dynamische Ebene 1: type='$dynamic_1_type'");
            
//            if ($dynamic_1_Value) {
//                $dynamic_1_Name = cms_dynamicPage_getInfo($dynamicData,1);
//                echo (" => $dynamic_1_Name ");
//                
////                if ($urlAdd) $urlAdd .= "&";
////                else $urlAdd .= "?";
////                $urlAdd = $dynamic_1_type."=".$dynamic_1_Value
//                
//            } else {
//                echo (" - not Set ");
//            }
            
            switch ($dynamic_1_type) {
                case "category" :
                    $selectCatId = $dynamic_1_Value;
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
                        $urlAdd .= $dynamic_1_type."=".$dynamic_1_Value;
                        // echo ("<h2>SET urlAdd to '$urlAdd</h2>");
                    } 
                    break;

                case "project" :
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
                        
            }
        }

        div_end("dynamicTitleFrame","before");
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
                    $list = cms_dynamicPage_getList_category($dynamicData,$dynamic_2_value,$showData);
                    break;
                case "project"  :
                    // echo ("<h1>ADD PROJECT TO LEVEL 2 $dynamic_2_type,$dynamic_2_value,</h1>");
                    $list = cms_dynamicPage_getList_project($dynamicData,$dynamic_2_value,$showData);
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
    }

    switch ($dynamic_type) {
        case "category" :
            if (is_array($addArray)) {
                $categoryId = $addArray[category];
                // echo ("GET catDATA BY #addArray $categoryId</br>");
            }
            else $categoryId = $_GET[category];
            $categoryData = cmsCategory_get(array("id"=>$categoryId));
            if (is_array($categoryData)) $res = $categoryData;
            // echo ("CategoryData<br>");
            // show_array($categoryData);
            break;

        case "project" :
            if (is_array($addArray)) {
                $projectId = $addArray[project];
                // echo ("GET projDATA BY #addArray $projectId</br>");
            }
            else $projectId = $_GET[project];
            $projectData = cmsProject_get(array("id"=>$projectId));            
            if (is_array($projectData)) $res = $projectData;
            break;
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
            $subPageData[subId] = $pageData[id]."_".$i;
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
                        // echo ("add subData $subName to $name $subSubData[url]<br>");
                        $subSubPageName = "$pageData[title]_$subName";

                        $subSubPageData = array();
                        $subSubPageData[id] = $pageData[id];
                        $subSubPageData[subId] = $pageData[id]."_".$i;
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
    $res[project] = "Projekte";        
    
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

function cms_dynamicPage_editSource($editPage,$sourceType,$level) {
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
            $formName = "editPage[data][$dataName]";
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
