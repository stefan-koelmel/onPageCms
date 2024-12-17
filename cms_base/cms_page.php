<?php // charset:UTF-8

function cms_page_state() {
    $state = $_SESSION[pageState];
    if ($state) return $state;
    
    global $cmsSettings;
    $state = $cmsSettings[state];
    
    if (!$state) $state = "online";
    
    return $state;
    
    
}
    

function cms_page_getData($pageName_pageId,$useSession=1) {
    // $useSession = 0;
    $cmsName = $GLOBALS[cmsName];
    if ($GLOBALS[debug]) echo ("cmsName in cms_page_getData $cmsName <br />");
    if (!$cmsName) {
        echo ("cmsName = $GLOBALS[cmsName] <br />");
        if ($_SESSION[cmsName]) {
            $cmsName = $_SESSION[cmsName];
            echo ("get cmsName fro session => $cmsName <br />");
        }

    }
    
    if (is_integer($pageName_pageId)) {
        if ($useSession) $pageData = cms_page_get_Session($pageName_pageId);
        if (is_array($pageData)) return $pageData;
        
        $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `id` = $pageName_pageId ";
    } else {
        if ($useSession) $pageData = cms_page_get_Session($pageName_pageId);
        if (is_array($pageData)) return $pageData;
        
        $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `name` = '$pageName_pageId' ";
    }
    $result = mysql_query($query);
    if (!$result) {
        echo "ERror in Query '$query' <br />";
        return 0;
    }

    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        if ($_SESSION[showLevel] >= 9) {
//            echo ("$query <br />");
//            echo ("NO cms_page Found for '$pageName_pageId'<br />");
            $pageInfo = $GLOBALS[pageInfo];
            $createPage = $_GET[createPage];
            if ($createPage) {
                echo("CREATE PAGE<br>");
                $newName = $pageInfo[pageName];
                $newData = array();
                $newData[title] = $newName;
                $newData[navigation] = 0;
                $newData[breadcrumb] = 1;
                $newData[showLevel] = 0;
                $newData[mainPage] = 0;
                $res = cms_page_create($newName,$newData);
                if ($res) {
                    reloadPage($newName.".php",3);
                }
            } else {
                // echo ("<a href='$pageInfo[page]?createPage=1'>anlegen</a><br />");
            }
        }
        return 0;
    }

    if ($anz > 1) {
        $pageList = array();
        while ($pageData = mysql_fetch_assoc($result)) {
            $pageList[] = $pageData;
        }
        return $pageList;
    }

    $pageData = mysql_fetch_assoc($result);
    if (is_string($pageData[data])) $pageData[data] = str2Array ($pageData[data]);
    
    
    
    
    //if (strlen($pageData[data])) $pageData[data] = str2Array ($pageData[data]);
    // else $pageData[data] = array();
    if (!is_array($pageData[data])) $pageData[data] = array(); 
    return $pageData;
}


function cms_page_get($getData) {
    $query = "";
    if (is_array($getData)) {
        foreach($getData as $key => $value) {
            // echo ("$key = $value <br>");
            //if ($value) {
                if ($query) $query .= ", ";
                $query .= "`$key` = '$value' ";
            // }
        }
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE $query";
    } else {
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` ";
    }
    
    // echo ("Query = '$query' <br> ");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found for page $getData<br>");
        return 0;
    }
    if ($anz > 1) {
        echo ("More found in page_get<br>");
        return 0;
    }   
    $data = mysql_fetch_assoc($result);
    
    if ($data[data]) {
        if (is_string($data[data]) AND strlen($data[data])) {
            $data[data] = str2Array($data[data]);
        } else {
            $data[data] = array();
        }
        
    } else {
        $data[data] = array();
    }
    
    return $data;
}
    


function cms_page_getList($getData,$sort=null,$out=null) {
    $filterQuery = "";
    foreach($getData as $key => $value) {
        switch ($key) {
            case "search" : 
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "`title` LIKE '%$value%'" ;
                break;

            case "searchText" : 
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "(`title` LIKE '%$value%' OR `description` LIKE '%$value%')" ;
                break;
            default :
                if ($filterQuery) $filterQuery .= "AND ";
                $filterQuery .= "`$key` = '$value' ";
        }
        // }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE $filterQuery";
    // echo ("Query = '$query' <br> ");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found contentList<br>");
        return 0;
    }
    if ($anz > 1) {
        echo ("More found <br>");
        return 0;
    }   
    $data = mysql_fetch_assoc($result);
    
    if ($data[data]) {
        if (is_string($data[data]) AND strlen($data[data])) {
            $data[data] = str2Array($data[data]);
        } else {
            $data[data] = array();
        }
        
    } else {
        $data[data] = array();
    }
    
    return $data;
}
    

function cmsPage_getList($getData,$sort=null,$out=null) {
    $filterQuery = "";
    foreach($getData as $key => $value) {
        switch ($key) {
            case "search" : 
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "`title` LIKE '%$value%'" ;
                break;

            case "searchText" : 
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "(`title` LIKE '%$value%' OR `description` LIKE '%$value%')" ;
                break;
            default :
                if ($filterQuery) $filterQuery .= "AND ";
                $filterQuery .= "`$key` = '$value' ";
        }
        // }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE $filterQuery";
    if ($out == "out") echo ("Query = $query <br>");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $res = array();
    while ($page = mysql_fetch_assoc($result)) {
        
        if ($page[data]) {
            if (is_string($page[data]) AND strlen($page[data])) {
                $page[data] = str2Array($page[data]);
            } else {
                $page[data] = array();
            }
       } else {
            $page[data] = array();
       }     
       $res[] = $page;
    }
    return $res;
}

function cmsPage_search($searchString,$searchText) {
    $filter = array();
    if ($searchText) $filter[searchText] = $searchString;
    else $filter["search"] = $searchString;
    $out = "";
    $sort ="";
    
    $res = cmsPage_getList($filter,$sort,$out);
    return $res;

}



function cms_page_getNavi() {
    return cms_page_getSortList();
    $navi_List = array();
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` ORDER BY `sort` ASC "; //  WHERE `navigation` > 0;

    $result = mysql_query($query);
    if (!$result) return $naviList;

    while($pageData = mysql_fetch_assoc($result)) {
        $code = "".$pageData[id];
        $mainPage = $pageData[mainPage];
        if ($mainPage == "0") { // hauptNavi
            if (!is_array($naviList[$code])) $naviList[$code] = array();
            $naviList[$code] = $pageData;
        } else {
           // echo ("Main Page is not 0");

        }

    }

    return $naviList;
}

function cms_page_getSortList($forNavi=0) {
    $navi_List = array();
   
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` ORDER by `sort` ";
//    if ($forNavi) {
//        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `navigation`= '1' ORDER by `sort` ";
//        // echo ("Query for Navigation $query <br />");
//    }
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br />");
        return 0;
    }
    
    
    $lg = cms_text_getLanguage();
    $userLevel = $_SESSION[userLevel];
    $userId    = $_SESSION[userId];
    
    $pageListUnsort = $_SESSION[pageList];
    if (!is_array($pageListUnsort)) {
        $pageListUnsort = array();
        while ($page = mysql_fetch_assoc($result)) {
            $show = 1;
            if (substr($page[name],0,5) == "admin") {
                $show=cms_page_showAdminPage($page[name]);
                // echo ("Admin Page $page[name] <br />");
            } 
            
            $pageLevel = $page[showLevel];
            $toLevel   = $page[toLevel];
            
            $pageName  = $page[name];
             
            if ($show) {
                $pageData = str2Array($page[data]);
                if (is_array($pageData)) {
                    $page[data] = $pageData;
                }
                if ($userLevel <= $pageLevel) {
                    $show = 0;
                    // echo ("Dont show $pageName because $pageLevel > $userLevel <br> ");
                    if ($pageLevel == 3) {
                        $allowedUser = $pageData[allowedUser];
                        if ($allowedUser) {
                            $use = strpos("--".$allowedUser,"|".$userId."|");
                            if ($use) {
                                $show = 1;
                            }
                        }
                    }    
                } else {
                    if ($pageLevel == 3) {
                        $allowedUser = $pageData[allowedUser];
                        if ($allowedUser) {
                            $use = strpos("--".$allowedUser,"|".$userId."|");
                            if ($use) {
                                $show = 0;
                            }
                        }
                        
                    }
                    $forbiddenUser = $pageData[forbiddenUser];
                }
            }
            

            if ($show) {
                $page[show] = 0;
               
                if ($page[data]) {
                    if (is_string($page[data])) $page[data] = str2Array ($page[data]);
                }
                if (!is_array($page[data])) $page[data] = array();
                
                $doLg = array("title"=>1,"keywords"=>1,"description"=>1);                
                foreach ($doLg as $key => $value) {
                    $page[$key] = cms_text_createArray($page[$key],$lg);
                }
                
                
                
                // add 
                $pageListUnsort[$pageName] = $page;
                // echo ("ADD PAGE $pageName <br>");
            }

        }
        $_SESSION[pageList] = $pageListUnsort;
    }

    $oldSelect = 1;
    
    $pageInfo = $GLOBALS[pageInfo];
    // show_array($pageInfo);
    $pageData = $GLOBALS[pageData];
    // show_array($pageData);
    $aktPageId = $pageData[id];

    // Hauptebene
    $pageList = array();
    // for ($i=0;$i<count($pageListUnsort);$i++) {
    foreach ($pageListUnsort as $pageName => $page) {
        // $page = $pageListUnsort[$i];
        $mainPage = $page[mainPage];
        $pageId = $page[id];

        if ($mainPage == 0) { // Hauptnavi
            $add = 1;
             
            if (!is_array($page[data])) {
                if (is_string($page[data])) $page[data]=str2Array ($page[data]);                
            }
            
            if (is_array($page[data])) {
                foreach ($page[data] as $key => $value) {
                    $setPos = strpos($key,"Set");
                    if ($setPos) {
                        $checkName = substr($key,0,$setPos);
                        if ($value == "1") {
                            if (!$_SESSION[$checkName]) {
                                $add = 0;
                            }
                        }
                    }
                }
            }
           
            
            
            // $page[show] = $add;
            $pageList[$pageId] = $page;         
            
            if ($pageId == $aktPageId) {               
                if ($oldSelect) {
                    $pageList[$pageId][selectPage] = 1;
                }
                $pageList[$pageId][select] = "select";
            }
            $pageListUnsort[$pageName][show] = 1;
            
            $dynamic = $page[dynamic];
            if ($dynamic) {
                $dynamicAdd = cms_dynamicPage_getSortList($page);
                if (is_array($dynamicAdd)) {
                    if (!is_array($pageList[$pageId][subNavi])) $pageList[$pageId][subNavi] = array();
                    foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                        $pageList[$pageId][subNavi][$dynamicKey] = $dynamicData;
                    }                    
                }
            }
        }
    }

    // 1.Ebene
    foreach ($pageListUnsort as $pageName => $page) {
   // for ($i=0;$i<count($pageListUnsort);$i++) {
        //$page = $pageListUnsort[$i];
        $show = $page[show];
        
        if ($show) continue; // breits gezeigt
        
       
        $mainPage = $page[mainPage];
        if (is_array($pageList[$mainPage])) {
            // $idCode = "".$mainPage;
            $pageId = $page[id];

            // echo (" add $page[title] to ".$pageList[$mainPage][title]." <br>");


            if ($pageId == $aktPageId) {
             //   echo ("ZEIGE aktuelle Seite in Ebene 2<br />");
                if ($oldSelect) {
                    $pageList[$mainPage][subSelect] = 1;
                    $page[selectPage] = 1;  
                }

                $page[select] = "select";
                $pageList[$mainPage][select] = "subSelect";
            } else {
                if ($pageList[$mainPage][select]== "select") {
                    $page[select] = "beforeSelect";
                    if ($oldSelect) {
                        $page[beforeSelect] = 1;
                    }
                }
            }

            $dynamic = $page[dynamic];
            $dynamic_show = $page[data][dynamicNavigation];
            if ($dynamic AND $dynamic_show) {

                $dynamicAdd = cms_dynamicPage_getSortList($page);
                if (is_array($dynamicAdd)) {
                    if (!is_array($page[subNavi])) $page[subNavi] = array();
                    foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                        // echo ("add Level 1 dynamicAdd $dynamicKey $dynamicData[name] <br />");

                        $page[subNavi][$dynamicKey] = $dynamicData;
                    }
                }
            }
            /// ADD PAGE TO MAIN PAGE
            if (!is_array($pageList[$mainPage][subNavi])) $pageList[$mainPage][subNavi] = array();
            $pageList[$mainPage][subNavi][$pageId] = $page;

            // SET PAGE TO SHOW
            $pageListUnsort[$pageName][show] = 1;

        }
            

    }
    
    
    
   

    // 2.Ebene
    // for ($i=0;$i<count($pageListUnsort);$i++) {
    foreach ($pageListUnsort as $pageName => $page) {        
        // $page = $pageListUnsort[$i];
        $show = $page[show];
        unset($page[show]);
        if ($show) {
            continue ;
            echo ("DONT SHOW -- ");
             // breits gezeigt
            
        }
            
        $mainPage = $page[mainPage];

        foreach ($pageList as $mainId => $main_Page) {
            if (!is_array($main_Page[subNavi])) continue;
            
            if (is_array($main_Page[subNavi][$mainPage])) {
                // echo ("Add $page[title] to $main_Page[title] <br>");
                
                $pageId = $page[id];
                
                if ($pageId == $aktPageId) { // aktuelle Seite
                    if ($oldSelect) {
                        $page[selectPage] = 1;
                        $pageList[$mainId][subSelect] = 1;
                        $pageList[$mainId][subNavi][$mainPage][subSelect] = 1;
                    }
                    
                    $page[select] = "select";
                    $pageList[$mainId][select] = "subSelect";
                    $pageList[$mainId][subNavi][$mainPage][select] = "subSelect";
                    
                } 
                // check if before Page is Select
                if ($pageList[$mainId][subNavi][$mainPage][select] == "select") {
                    $page[select] = "beforeSelect";
                    if ($oldSelect) {
                        $page[beforeSelect] = 1;
                    }
                }
                       
                $dynamic = $page[dynamic];
                $dynamic_show = $page[data][dynamicNavigation];
                
                if ($dynamic AND $dynamic_show) {
                    $dynamicAdd = cms_dynamicPage_getSortList($page);
                    if (is_array($dynamicAdd)) {
                        if (!is_array($page[subNavi])) $page[subNavi] = array();
                        foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                            $page[subNavi][$dynamicKey] = $dynamicData;
                        }
                    }
                }
                
                
                // add Page to SubNavi
                if (!is_array($pageList[$mainId][subNavi][$mainPage][subNavi][$pageId])) $pageList[$mainId][subNavi][$mainPage][subNavi][$pageId] = array();
                $pageList[$mainId][subNavi][$mainPage][subNavi][$pageId] = $page;
                
                 // SET PAGE TO SHOW
                $pageListUnsort[$pageName][show] = 1;
                
            }
        }
    }
    
    
    // 3.Ebene
    // for ($i=0;$i<count($pageListUnsort);$i++) {
    foreach ($pageListUnsort as $pageName => $page) {
        // $page = $pageListUnsort[$i];
        $show = $page[show];
        unset($page[show]);
        if ($show) {
            continue ;
            echo ("DONT SHOW -- ");
             // breits gezeigt
            
        }
            
        $mainPage = $page[mainPage];
        $pageId   = $page[id];
        
        foreach ($pageList as $mainId => $main_Page) {
            if (!is_array($main_Page[subNavi])) continue;
            foreach ($main_Page[subNavi] as $subId => $subPage) {
                if (!is_array($subPage[subNavi])) continue;
                
                if (is_array($subPage[subNavi][$mainPage])) {
                   
                    if ($pageId == $aktPageId) { // Aktuelle Seite
                        if ($oldSelect) {
                            $page[selectPage] = 1;
                            $pageList[$mainId][subSelect] = 1;
                            $pageList[$mainId][subNavi][$subId][subSelect] = 1;
                            $pageList[$mainId][subNavi][$subId][subNavi][$mainPage][subSelect] = 1;
                        }
                    
                        $page[select] = "select";
                        $pageList[$mainId][select] = "subSelect";
                        $pageList[$mainId][subNavi][$subId][select] = "subSelect";
                        $pageList[$mainId][subNavi][$subId][subNavi][$mainPage][select] = "subSelect";                        
                    } 
                    if ($pageList[$mainId][subNavi][$subId][subNavi][$mainPage][select] == "select") {
                        if ($oldSelect) {
                            $page[beforeSelect] = 1;                            
                        } 
                        $page[select] = "beforeSelect";
                    }
                    
                    
                    
                    
                    // add Page to SubSubNavi
                    if (!is_array($pageList[$mainId][subNavi][$subId][subNavi][$mainPage][subNavi][$pageId])) $pageList[$mainId][subNavi][$subId][subNavi][$mainPage][subNavi][$pageId] = array();
                    $pageList[$mainId][subNavi][$subId][subNavi][$mainPage][subNavi][$pageId] = $page;

                     // SET PAGE TO SHOW
                    $pageListUnsort[$pageName][show] = 1;
                    
                    
                }
            }
        }
    }
    
    return $pageList;

}


function cms_page_goPage($add="") {
    $pageData = $GLOBALS[pageData];
    // show_array($pageData);
    $goPage= $pageData[name].".php";
    
    $addUrl = "";
    
    $dynAdd = cms_dynamicPage_addUrl($pageData);
    if ($dynAdd) {
        if ($addUrl) $addUrl .= "&".$dynAdd;
        else $addUrl .= "?".$dynAdd;
    }
    
    if ($add) {
        if ($addUrl) $addUrl .= "&".$add;
        else $addUrl .= "?".$add;
    }
    
    return $goPage.$addUrl;
}


 


function cms_page_showAdminPage ($adminPage) {

    // echo ("Show cms_page_showAdminPage $adminPage <br />") ;
    $show = 0;
    global $cmsSettings;
    if (is_string($cmsSettings[specialData])) $cmsSettings[specialData] = str2Array ($cmsSettings[specialData]);

    switch ($adminPage) {
        case "admin" : $show=1; break;
        case "admin_cmsCms" : $show=1; break;
        case "admin_data" : $show=1; break;

        case "admin_cmsLayout" : $show=1; break;

        case "admin_cmsSettings" : $show=1; break;
        case "admin_cmsImages" : $show=1; break;

//    
//    unkown Admin Type images
//unkown Admin Type importExport
//unkown Admin Type articles
//unkown Admin Type location
//unkown Admin Type projects
//    

        case "admin_company"  : $type = "company"; break;
        case "admin_cmsCompany"  : $type = "company"; break;
        case "admin_category"  : $type = "category"; break;
        case "admin_cmsCategory"  : $type = "category"; break;
        case "admin_cmsMail"  : $type = "email"; break;
        case "admin_mail"  : $type = "email"; break;
        
        case "admin_cmsUser"  : $type = "user"; break;
        case "admin_cmsDates" : $type = "dates"; break;
        case "admin_product"  : $type = "product"; break;
        case "admin_cmsProduct"  : $type = "product"; break;
        case "admin_cmsProject"  : $type = "project"; break;
    
        case "admin_cmsImportExport"  : $type = "importExport"; break;   
        case "admin_cmsUserData"  : $type = "userData"; break;
        case "admin_cmsEmail"  : $type = "email"; break;   
        case "admin_cmsLocation"  : $type = "location"; break;
        case "admin_cmsArticles"  : $type = "articles"; break;   
        case "admin_cmsOrder"  : $type = "order"; break;
        
        case "admin_importExport"  : $type = "importExport"; break;
        case "admin_images"  : $type = "images"; break;   
        case "admin_articles"  : $type = "articles"; break;
        case "admin_location"  : $type = "locations"; break;   
        case "admin_projects"  : $type = "projects"; break;
        
  
        default :
            if (substr($adminPage,0,6) == "admin_") {

                $type = substr($adminPage,6);
                echo ("unkown Admin Type ".$type."  ($adminPage)<br />");
            } else {
                echo ("unkownType in cms_page_showAdminPage '$adminPage' ".substr($adminPage,0,6)."<br />");
                $type = "unkown";
            }
    }

    $use = $cmsSettings[specialData][$type];
    if ($use) {
        $show = 1;
    }

    return $show;



}



function cms_page_exist($pageName) {
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `name` = '$pageName' ";
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in function cms_page_exist Query '$query' <br />";
        return 0;
    }

    $anz = mysql_num_rows($result);
    if ($anz == 0) return 0;
    if ($anz > 1) return "more";
    $pageData = mysql_fetch_assoc($result);
    $pageId = $pageData[id];
    return $pageId;
}

function cms_page_update($pageId,$changeData=array()) {
    $query = "";
    if (is_array($pageId)) {
        $compareData = $pageId;
        $pageId = $compareData[id];
    }
    $diff = 0;
    
    
    
//    if (!$changeData[dynamic]) $changeData[dynamic] = "0";
//    if (!$changeData[navigation]) $changeData[navigation] = "0";
//    if (!$changeData[breadcrumb]) $changeData[breadcrumb] = "0";
//    if (!$changeData[showLevel]) $changeData[showLevel] = "0";
    // echo ("cms_page_update($pageId,$changeData)<br />");
    // show_array($changeData);
    
    foreach ($changeData as $key => $value) {
        if (is_array($value)) $value = array2Str ($value);
        $add = 1;
        if (is_array($compareData)) {
            if ($compareData[$key] == $value) {
                // echo ("ist gleich $key = $value <br>");
                $add = 0;
            } else {
                // echo ("Diffrent old ='$compareData[$key]' new = '$value' <br>");
            }
        }
        if ($key == "pageTitle") $add = 0;
        if ($add) {
            $diff++;
            if ($query!="") $query .= ", ";
            $query .= "`$key`='$value'";
        }
    }
    // echo ("QUery = $query <br />");
    cms_page_update_Session($changeData);
    
    if ($diff) {
    
        if (strlen($query)>0) {
            $query = "UPDATE `".$GLOBALS[cmsName]."_cms_pages` SET $query WHERE `id` = '$pageId' ";
            
            $result = mysql_query($query);
            if ($result) return 1;

            echo ("Error in Query '$query' <br />");
            return 0;
        }
    } else {
        // echo "No Change for $pageId<br>";
        return 1;
    }
}

function cms_page_create($newName,$newData=array()) {
    // echo ("cms_page_create($newName,$newData)<br>");
    $pageExist = cms_page_exist($newName);
    if ($pageExist) {
        $createId = $pageExist;
    } else {
        if (!$newData[name]) $newData[name] = $newName;
        $createId = cms_page_save($newData);
    }
    
    
    $targetPath = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName]."/";
    if (!file_exists($targetPath)) {
        $targetPath = $_SERVER['DOCUMENT_ROOT']."/";
    }
    if ($newData[targetPath]) {
        $targetPath = $newData[targetPath];
        unset($newData[targetPath]);
    }
    
    
    
    
    //echo ("exist /".$GLOBALS[cmsName]."/".$newName.".php <br>");
    // global $cmsVersion;
    if (file_exists($targetPath."/".$newName.".php")) {
        // FILE EXIST
        return $createId;
    }
    
//    if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$newName.".php")) {
//        return 1;
//    }
    // return 0;
    $orgPath = $_SERVER['DOCUMENT_ROOT']."/cms_".$GLOBALS[cmsVersion]."/emptyPage.php";
    
    $resCopy = copy($orgPath,$targetPath."/".$newName.".php");
    if ($resCopy) {
       // echo ("Datei wurde kopiert <br />");
        
    }
    else {
        echo "copy cms_".$GLOBALS[cmsVersion]."/emptyPage.php nach $newName.php schlug fehl...<br />\n";
        echo ("ORGPATH = $orgPath<br />");
        echo ("TargetP = $targetPath<br />");
        return 0;
    }
    
    cms_page_update_Session($newData);
    // cms_page_destroy_session();
    return $createId;
    
}


function cms_page_delete($deleteId) {
    $deleteId = intval($deleteId);
   
    $pageData = cms_page_getData($deleteId);
    $pageName = $pageData[name];
    
    
   //  echo ("Found $pageData $pageData[name] $pageData[id] <br> ");
    
    $pageId = "page_".$deleteId;
    // echo ("Find Content for '$pageId' <br />");
    
    $contentList = cms_content_getList($pageId);
    // echo ("ContentList = $contentList <br>");
    if (is_array($contentList) AND count($contentList)) {
        for ($i=0;$i<count($contentList);$i++) {
            $content = $contentList[$i];
            $contentId = $content[id];
            $contentType = $content[type];
            // echo ("Find Content $contentId $contentType <br>");
            
            if (substr($contentType,0,5)=="frame") {
                $frameAnz = substr($contentType,5);
                // echo ("Frame ANZ = $frameAnz <br>");
                for ($f=1;$f<=$frameAnz;$f++) {
                   
                    $frameContentStr = "frame_".$contentId."_".$f;
                    // echo ("DELETE CONTENT FOR $contentId Frame $f -> '$frameContentStr'<br>");
                    $frameContentList =  cms_content_getList($frameContentStr);
                    if (is_array($frameContentList) AND count($frameContentList)) {
                        for ($fc=0;$fc<count($frameContentList);$fc++) {
                            $frameContent = $frameContentList[$fc];
                            $frameContentId = $frameContent[id];
                            $frameContentType = $frameContent[type];
                            // echo ("Find Content for frame <b>$frameContentStr</b> $frameContentId $frameContentType <br>");
                            $res = cms_content_delete($frameContentId,1);
                        }
                    }
                    
                    
                    
                    // 
                }
                
            }
            $res = cms_content_delete($contentId,1);
        }
    }
    
    // Remove from PageList
    cms_page_destroy_session($pageName);
    
    // Delete in Database
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `id` = $deleteId";
    $result = mysql_query($query);    
    if (!$result) {
        cms_errorBox("Fehler beim Löschen der Seite<br>".$query);
        return 0;
    }
    
    // Delete File on Server
    $fn = $pageName.".php";
    if (file_exists($fn)) {
        unlink($fn);
    } 
    return 1;
}

function cms_page_changeSort($id,$sort) {
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_pages` SET `sort`=$sort WHERE `id`=$id";
    $result = mysql_query($query);
    if ($result) return 1;

    echo ("Error in Query $query <br />");
    return 0;


}

function cms_page_editData($pageWidth=500) { // $pageData,$pageInfo) {
    global $pageData,$pageInfo;
    
    $savePageData = $_POST[savePageData];
   
    $saveButton = $_POST[saveButton];
    $saveAndClose = $_POST[saveAndClose];
   
    if ($_POST[saveCancel]) {
        $goPage = $pageInfo[page];
        reloadPage ($goPage, 0);
        return 0;
    }

    if ($saveButton OR $saveAndClose) $save = 1;
    if ($savePageData AND $save) {
        $id = $savePageData[id];
        $changeData = array();

        // correcturen für checkBox
        if (!$savePageData[dynamic]) $savePageData[dynamic] = "0";
        if (!$savePageData[navigation]) $savePageData[navigation] = "0";
        if (!$savePageData[breadcrumb]) $savePageData[breadcrumb] = "0";
        if (!$savePageData[showLevel]) $savePageData[showLevel] = "0";
        if (!$savePageData[data]) $savePageData[data] = array();
        // echo ("Navi is $savePageData[navigation] BreadCrumb is $savePageData[breadcrumb]<br />");
        
        $change_inData = 0;
        foreach ($savePageData as $key => $value ) {
            switch ($key) {
                case "data" :
                    $newData = $pageData["data"];
                    if (!is_array($newData)) $newData = array();
                    if (is_array($value)) {
                        foreach ($value as $dataKey => $dataValue) {
                            if ($dataValue != $newData[$dataKey]) {
                                // echo ("CHANGE Data $dataKey from $newData[$key] -> $dataValue <br />");                                
                                $newData[$dataKey] = $dataValue;
                                $change_inData++;
                            } else {
                                // echo ("No change for $dataKey $dataValue <br>");
                            }
                        }
                    }
                    if ($change_inData) {
                        // echo ("<h1>Change in Data </h1>");
                        $changeData["data"] = $newData;  
                        $savePageData["data"] = $newData;
                    }
//                    $oldDataStr = array2Str($pageData["data"]);
//                    $newDataStr = array2Str($value);
//                    if ($newDataStr != $oldDataStr) {
//                        $changeData["key"] = $value;
//                        
//                    }
                    break;
                
                    
                default :
                    if (is_array($value)) {
                        $newData = array2str($value);
                        $oldData = array2Str($pageData[$key]);
                        if ($oldData != $newData ) {
                            $changeData[$key] = $value;
                        }
//                        $str = "lg";
//                        foreach ($value as $lgCode => $lgStr) {
//                            // echo ("SAVE LG  for $key $lgCode,$lgStr <br>");
//                            $str .= "|".$lgCode.":".$lgStr;
//                        }
//                                  
//                        $value = $str;    
//                        $savePageData[$key] = $value;
//                        echo ("lg STR for $key = '$value' <br> ");  
                        
                    }
                    if ($pageData[$key] != $value) {
                        $changeData[$key] = $value;
                        $pageData[$key] = $value;
                        echo ("Change $key to $value <br>");
                    }
            }
        }

        if (count($changeData) == 0) {
            cms_infoBox("keine Veränderung / nicht gespeichert");

            $goPage = $pageInfo[page];
            if ($saveButton) $goPage .= "?editMode=".$_GET[editMode];
            $seconds=1;
            reloadPage ($goPage, $seconds);
            
        } else {
            // $pageData = $savePageData[name];
            
            $res = cms_page_update($pageData,$savePageData);
            if ($res == 1) {
                cms_infoBox("Daten gespeichert !!");
                if ($saveButton) { $goPage = $pageInfo[page]."?editMode=".$_GET[editMode]; $seconds=3; }
                if ($saveAndClose) { $goPage = $pageInfo[page]; $seconds = 1;}
                reloadPage ($goPage, $seconds);
                if ($saveAndClose) return 1;
            }
        }
    }
    
    if (!is_array($savePageData)) $savePageData = $pageData;

    // foreach($pageInfo as $key => $value) echo ("PageInfo $key = $value <br />");
    echo ("<h3>Seiten Daten bearbeiten</h3>");
     
   // $data = $savePageData[data];
   // if (!is_array($savePageData[data])) $savePageData[data] = array();
    
    
   // foreach ($savePageData[data] as $key => $value) echo ("DATA IN PAGE $key = $value <br />");
//    foreach ($GLOBALS[cmsSettings][data] as $key => $value) {
//        echo ("Settings $key = $value <br>");
//    }
    // show_array($pageData);
    $pageWidth = $pageWidth-20-2;
    $leftWidth = 170;
    $inputWidth = $pageWidth - $leftWidth;
    
    // echo ("PageWidth = $pageWidth $leftWidth / $inputWidth <br> ");
    //foreach($pageData as $key => $value) echo ("PageData $key = $value <br />");
    echo ("<form method='post'>\n");

    $hideList = array();
    $showEdit = cms_page_editContent($savePageData,"savePageData",$hideList,$inputWidth);
    
    $editMode = $_SESSION[editMode];
    
    for ($i=0;$i<count($showEdit);$i++) {
        
        $text = $showEdit[$i][text];
        $input = $showEdit[$i][input];
        $mode  = $showEdit[$i][mode];
        
        $level = $showEdit[$i][level];
                

        $lineDivName = "inputLine";
        if ($level>0  AND $level > $_SESSION[userLevel] ) {
             $lineDivName .= " editMode_hidden";
             // continue;
        } else {
            if ($mode) {
                $lineDivName .= " editMode_".$mode;
                switch ($mode) {
                    case "Simple" : break;
                    case "More" :
                        if ($editMode == "Simple") {
                            $lineDivName .= " editMode_hidden";
                        }
                        break;
                    case "Admin" :
                        if ($editMode == "Simple" OR $editMode == "More") {
                            $lineDivName .= " editMode_hidden";
                        }
                        break;

                    case "Hidden" :
                        $lineDivName .= " editMode_hidden";
                        break;


                }
            } else {
                if ($_SESSION[userLevel] == 9) {
                    $lineDivName .= " editMode_unkown";
                }
            }
        }
        
        if ($level>0  AND $level > $_SESSION[userLevel] ) {
             $lineDivName .= " editMode_hidden";
             // continue;
        }
        
        $out .= div_start_str($lineDivName) ;
        $out .= div_start_str("inputLeft","width:".$leftWidth."px;");
        $out .= $text.":";
        $out .= div_end_str("inputLeft");
        $out .= div_start_str("inputRight");
        $out .= $input;
        //if ($secondLine) $out.= $seccondLine;
        $out .= div_end_str("inputRight");
        $out .= div_end_str($lineDivName,"before");
      
        
        
    }
    
    echo ($out);


    echo ("<input type='submit' class='cmsInputButton' name='saveAndClose' value='speichern und schließen' > &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton' name='saveButton' value='speichern'> &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton cmsSecond'name='saveCancel' value='abbrechen' > &nbsp; ");
    echo ("<form>");
    echo ("<br />\n");
}


function cms_page_editContent($pageData,$formName="savePageData",$hideList=array(),$inputWidth=200) {
    // echo ("cms_page_editContent($pageData,$formName,$hideList,$inputWidth) <br>");
    $data = $pageData[data];
    if (!is_array($data)) $data = array();
    $res = array();
    
    echo ("<input type='hidden' name='".$formName."[id]' value='$pageData[id]' >");
    echo ("<input type='hidden' name='".$formName."[name]' value='$pageData[name]' >");
    $addData = array();
    $addData[text] = "Datei-Name";
    $addData[input] = "<input type='text' name='showTemp[name]' value='$pageData[name]' disabled='1' >\n";
    $addData[mode] = "More";
    $res[] = $addData;


//    $title = $pageData[title];
//    $pageTitle = $pageData[pageTitle];
//    $pageDescription = $pageData[description];
//    $pageKeywords = $pageData[keywords];





//    echo ("Title = $title <br>");
//    echo ("pageTitle = $pageTitle <br>");
//    echo ("pageDescription $pageDescription <br>");
//    echo ("pageKeywords = $pageKeywords <br>");



    $contentCode = "page_".$pageData[id];
    if (!is_array($editText)) {
        // echo ("Get Text from Database<br>");
        $editText = cms_text_getForContent($contentCode,1);
        foreach ($editText as $key => $value ) {
            echo ("$key : <br>");
            foreach ($value as $k2 => $v2) {
                echo ("$k2 = $v2 | ");
            }
            echo ("<br />");
        }
    } else {
       // echo ("get Text form POST<br>");
       //
    }

    $dbContent = $pageData[title];
    
    
    $editClass = cms_contentTypes_class();
//    $showData = array();
//    $showData[defaultText] = $pageData[title];
//    $showData[textData]    = $editText[title];
//    $showData[view] = "data";
//    $showData[color] = 1;
//    $showData[css] = 1;
//    $showData[width] = 200;
//    $showData[name] = "Rahmen-Überschrift";
//    $showData[lgSelect] = 1;
//    $showData[mode] = "Simple";
//    $showData[formName] = $formName;
    
    
    
    $showData = array();
    $showData[formName] = $formName;
    $showData[dataSource] = "page";
    $showData[editMode] = "SimpleLg"; // array("simple","language","textDb")[0];
    
    
    $showData[title] = "Name";
    $showData[dataName] = "title";
    $showData[text] = $pageData[title];
    $showData[width] = $inputWidth/2-4;
    $showData[mode] = "Simple";
    $addData = $editClass->edit_text($showData);
    $res[] = $addData;
   
    $addData = array();
    $addData[text] = "Name";
    $addData[input] = "<input type='text' name='".$formName."[title]' style='width:".($inputWidth/2-4)."px' value='$pageData[title]' >\n";
    $addData[mode] = "Simple";
    // $res[] = $addData;

    
//    $showData[title] = "Seiten_Titel";
//    $showData[dataName] = "pageTitle";
//    $showData[text] = $pageData[pageTitle];
//    $showData[width] = $inputWidth-8;
//    $showData[editMode] = "SimpleLg";
//    $addData = $editClass->edit_text($showData);
//    if ($hideList["pageTitle"]) $addData[mode] = "Hidden";
//    $res[] = $addData;
//    
//    
//    $addData = array();
//    $addData[text] = "Seiten-Titel";
//    $addData[input] = "<input type='text' name='".$formName."[pageTitle]' style='width:".($inputWidth-8)."px;' value='$pageData[pageTitle]' >\n";
//    $addData[editMode] = "More" ;
//    if ($hideList["pageTitle"]) $addData[mode] = "Hidden";
    //$res[] = $addData;

    $addData = array();
    $addData[text] = "Seiten-Beschreibung";
    $addData[input] = "<textarea type='text' name='".$formName."[description]' style='width:".($inputWidth-4)."px;height:80px;' >$pageData[description]</textarea>\n";
    $addData[mode] = "More";
    if ($hideList["description"]) $addData[mode] = "Hidden";
    $res[] = $addData;

    // Keywords
    $showData[title] = "Seiten-Keywords";
    $showData[dataName] = "keywords";
    $showData[text] = $pageData[keywords];
    $showData[width] = $inputWidth-8;
    $showData[mode] = "More";
    $showData[viewMode] = "textarea";
    $showData[height] = 32;
    $addData = $editClass->edit_text($showData);
    if ($hideList["keywords"]) $addData[mode] = "Hidden";
    $res[] = $addData;
    
    $addData = array();
    $addData[text] = "Seiten-Keywords";
    $addData[input] = "<textarea type='text' name='".$formName."[keywords]' style='width:".($inputWidth-4)."px;height:80px;' >$pageData[keywords]</textarea>\n";
    $addData[mode] = "More";
    if ($hideList["keywords"]) $addData[mode] = "Hidden";
    // $res[] = $addData;

    
    
    $addData = array();
    $img = "<img src='/cms_".$GLOBALS[cmsVersion]."/images/image.gif' width='90px' height='90px' class='$imageClickClass'> ";
    $imageId = intval($pageData[imageId]);  
    if ($imageId > 0) {
        $imageData = cmsImage_getData_by_Id($imageId);
        if (is_array($imageData)) {
            $imagePath = $imageData[orgpath];
            $idStr = "id:$imageId|path:$imagePath";
            $img = cmsImage_showImage($imageData,100,array("class"=>$imageClickClass,"id"=>$idStr));
        }
    }
    
    $input = "";
    $input .= "<div class='cmsImageDropFrame cmsDropSingle' >";
    $input .= "<div class='cmsImageFrame' >";
    $input .= $img;
    $input .= "</div>";
    $inputType = "hidden";

    $input .= "<input type='$inputType' class='cmsImageId' style='width:30px;' name='".$formName."[imageId]' value='$pageData[imageId]' />";
    $input .= "</div>";
    $addData[text] = "Seiten-Icon";
    $addData[input] = $input;
    $addData[mode] = "More";
    if ($hideList["image"]) $addData[mode] = "Hidden";
    $res[] = $addData;
    
    $addData = array();
    $dynamic = $pageData[dynamic];
    if ($dynamic) $checked="checked='checked'";
    $addData[text] = "Dynamische Seite";
    $addData[input] = "<input type='checkbox' value='1' name='".$formName."[dynamic]' $checked />";
    $addData[mode] = "Admin";
    $res[] = $addData;
    
    if ($dynamic) {
        // if (is_array($data)) show_array($data);
        $dataSource = $data[dataSource];
        // echo ("Dynamische Seite '$dataSource' <br />");
        $addData = array();
        $dynamic = $pageData[dynamic];
        if ($dynamic) $checked="checked='checked'";
        $addData[text] = "Quelle Dynamische Inhalte";
        $addData[input] = cms_dynamicPage_Source($dataSource,$formName."[data][dataSource]");
        $addData[mode] = "Admin";
        $res[] = $addData;
       
        switch ($dataSource) {
            case "category" :
                $mainCat = $data[mainCat];
                $showData = array();
                $filter = array("mainCat"=>"0");
                $sort = "name";
               
                $addData = array();
                $addData[text] = "Kategorie wählen";
                $addData[input] = cmsCategory_selectCategory($mainCat,$formName."[data][mainCat]", $showData, $filter, $sort);
                $addData[mode] = "Admin";
                $res[] = $addData;
                
                if ($mainCat) {
                    echo ($div1."Unter-Kategorie wählen:");
                    $subCat = $data[subCat];
                    $showData = array();
                    $showData["empty"] = "Keine Unterkategorie";
                    $filter = array("mainCat"=>$mainCat);
                    $sort = "name";
                    echo ($div2.cmsCategory_selectCategory($subCat,$formName."[data][subCat]", $showData, $filter, $sort));
                    echo ($div3);   
                    $addData = array();
                    $addData[text] = "Unter-Kategorie wählen";
                    $addData[input] = cmsCategory_selectCategory($subCat,$formName."[data][subCat]", $showData, $filter, $sort);
                    $addData[mode] = "Admin";
                    $res[] = $addData;
                }
        }
        
        $addData = array();
        $dynamic2 = $data[dynamic2];
        if ($dynamic2) $checked = "checked='checked'";
        else $checked = "";
        $addData[text] = "Dynamische Inhalte";
        $addData[input] = "<input type='checkbox' name='".$formName."[data][dynamic2]' value='1' $checked \>";
        $addData[mode] = "Admin";
        $res[] = $addData;
        
        $addData = array();
        $dynamicNavigation = $data[dynamicNavigation];
        if ($dynamicNavigation) $checked = "checked='checked'";
        else $checked = "";
        $addData[text] = "Dynamische Inhalte in Navigation";
        $addData[input] = "<input type='checkbox' name='".$formName."[data][dynamicNavigation]' value='1' $checked \>";
        $addData[mode] = "Admin";
        $res[] = $addData;
       
        
        
        
        if ($dynamic2) {
            $addData = array();
            $dataSource2 = $data[dataSource2];
            $addData[text] = "Quelle 2. Ebene Dynamische Inhalte";
            $addData[input] = cms_dynamicPage_Source($dataSource2,$formName."[data][dataSource2]");
            $addData[mode] = "Admin";
            $res[] = $addData;
            
            $addData = array();
    
            $dynamicNavigation2 = $data[dynamicNavigation2];
            if ($dynamicNavigation2) $checked = "checked='checked'";
            else $checked = "";
            $addData[text] = "2. Dynamische Inhalte in Navigation";
            $addData[input] = "<input type='checkbox' name='".$formName."[data][dynamicNavigation2]' value='1' $checked \>";
            $addData[mode] = "Admin";
            $res[] = $addData;
       
        }
    }
    
    $addData = array();
    $addData[text] = "Übergeordnete Seite";
    $addData[input] = cms_page_SelectMainPage($pageData[mainPage],$formName."[mainPage]");
    $addData[mode] = "Simple";
    $res[] = $addData;
    
   
    $maxLevel = $_SESSION[userLevel];
    if ($maxLevel == 1) {
        if (is_string($pageData[data][allowedUser])) {
            $pos = strpos( $pageData[data][allowedUser],"|".$_SESSION[userId]."|");
            if (!is_null($pos)) {
                $maxLevel = 3;
            }
        }
    }
    if ($pageData[showLevel] == 3 AND $maxLevel < 3) $maxLevel = 3;
    $addData = array();
    $addData[text] = "Zeigen ab UserLevel";
    $showData = array();
    $input = "";
    if ($_SESSION[userLevel] < 8) {
        $showData[disabled] = "disabled";         
        $input .= "<input type='hidden' name='".$formName."[showLevel]' value='$pageData[showLevel]' />";
    }
    $input .= cms_user_SelectLevel($pageData[showLevel],$maxLevel,"help[showLevel]",$showData);
    $addData[input] = $input;
    $addData[mode] = "More";
    // $addData[level] = 8;
    $res[] = $addData;
    
    // USER AUSWAHL
    if ($pageData[showLevel] == 3) {
       $addData = array();
       $addData[text] = "Benutzer hinzufügen / sperren";
       $addData[input] = cmsUser_selectUserList($data,$maxLevel,$formName."[data]");
       $addData[mode] = "More";
       $addData[level] = 8;
       $res[] = $addData;       
    }


    $addData = array();
    $addData[text] = "Zeigen bis UserLevel";
    $showData = array();
    
    $input = "";
    if ($_SESSION[userLevel] < 8) {
        $showData[disabled] = "disabled";         
        $input .= "<input type='hidden' name='".$formName."[toLevel]' value='$pageData[toLevel]' />";
    }
    $input .= cms_user_SelectLevel($pageData[toLevel],$maxLevel,"help[toLevel]",$showData);
    $addData[input] = $input; 
    $addData[mode] = "More";
    $addData[level] = 0;
    $res[] = $addData;
    
    $contentType_class = cms_contentTypes_class();
    $special_viewFilter = $contentType_class->use_special_viewFilter($pageData,$formName);
    if (is_array($special_viewFilter)) {
        foreach($special_viewFilter as $key => $value) {
            $addData = array();
            $addData[text] = $value[text];
            $addData[input] = $value[input]; 
            $mode = $value[mode];
            if (!$mode) $mode = "More";
            $addData[mode] = $mode;
            $res[] = $addData;            
        }
    }

    $addData = array();
    $addData[text] = "Layout";
    $addData[input] = cms_layout_SelectLayout($pageData[layout],$formName."[layout]");
    $addData[mode] = "Simple";
    $res[] = $addData;
    

    $addData = array();
    if ($pageData[navigation]) $checked = "checked='checked'";
    else $checked = "";
    $addData[text] = "Seite in Navigation anzeigen";
    $addData[input] = "<input type='checkbox' name='".$formName."[navigation]' value='1' $checked \>";
    $addData[mode] = "Simple";
    $res[] = $addData;
    
    $addData = array();
    if ($pageData[breadcrumb]) $checked = "checked='checked'";
    else $checked = "";
    $addData[text] = "Seiten-Pfad anzeigen";
    $addData[input] = "<input type='checkbox' name='".$formName."[breadcrumb]' value='1' $checked \>"; 
    $addData[mode] = "Simple";
    $res[] = $addData;
    
    return $res;
}

function cms_getCmsName() {
    $page = $_SERVER[PHP_SELF];
    if ($page[0]=="/") $page = substr($page,1);

    $folderList = explode("/",$page);
    global $cmsName;
    if (count($folderList)>1) {
        $cmsName = $folderList[0];
    } else {
        $cmsName = "empty";
    }
    return $cmsName;
}

function cms_page_getInfo() {
    global $pageInfo;
    $lg = cms_text_getLanguage();
    if (is_array($pageInfo)) {
        $lg = $pageInfo[lg];
    } else {
        $lg = "dt";
    }

    // show_array($_SERVER);
    $pageInfo = array();
    
    // HOST - zb stefa-koelmel.de
    $host = $_SERVER[HTTP_HOST];
    $pageInfo["host"] = $host;

    // PAGE - zb. index.php
    $page = $_SERVER[PHP_SELF];
    if ($page[0]=="/") $page = substr($page,1);

    $folderList = explode("/",$page);
    global $cmsName;
    //  echo ("cmsName in cms_page_getInfo $cmsName <br />");
    if (count($folderList)>1) {
        $page = $folderList[count($folderList)-1];
        $path = "";
        for($i=0;$i<count($folderList)-1;$i++) {
            $path .= $folderList[$i]."/";
        }

        $cmsName = $folderList[0];
        //echo ("path = '$path' & name=$pageName <br />");
    } else {
        
        $path = "";
        if (!$cmsName) $cmsName = "empty";
    }
    $pageInfo["cmsName"] = $cmsName;

    if ($GLOBALS[debug]) echo ("Get CMSName = $cmsName <br />");

    if ($_SESSION[cmsName] != $cmsName) $_SESSION[cmsName] = $cmsName;


    $pageInfo["page"] = $page;

    
    // REQUEST URL
    if ($page == "404___.php") {
        $requestPage = $_SERVER[REQUEST_URI];
        if ($requestPage[0]=="/") $requestPage = substr($requestPage,1);
        $folderList = explode("/",$requestPage);

        $path = "";
        if (count($folderList)>1) {
            $requestPage = $folderList[count($folderList)-1];

            for($i=0;$i<count($folderList)-1;$i++) {
                $path .= $folderList[$i]."/";
            }

            $cmsName = $folderList[0];
        } else {
            $cmsName = "empty";
        }
        $pageInfo["cmsName"] = $cmsName;

        if ($GLOBALS[cmsName] != $cmsName) {
            echo ("cmsName Change from $GLOBALS[cmsName] to $cmsName <br />");
            $GLOBALS[cmsName] = $cmsName;
        }

        if ($_SESSION[cmsName] != $cmsName) {
            $_SESSION[cmsName] = $cmsName;
        }

        $offSetName = strpos($requestPage,".");
        if ($offSetName > 0) {
            $requestPageName = substr($requestPage,0,$offSetName);
            $pageInfo["requestPageName"] = $requestPageName;

            $rest = substr($requestPage,$offSetName+1);
            $off  = strpos($rest,"?");
            if ($off>0) {
                $requestPageType = subStr($rest,0,$off);
                $requestPageParameter = substr($rest,$off+1);
                $pageInfo["requestPageType"] = $requestPageType;
                $pageInfo["requestPageParameter"] = $requestPageParameter;
            } else {
                $requestPageType = $rest;
                $pageInfo["requestPageType"] = $requestPageType;
            }
        }
        $pageInfo["requestPagePath"] = $path;
    }
   
    $folderList = explode("/",$page);
    
    if (count($folderList)>1) {
        $pageName = $folderList[count($folderList)-1];
        $path = "";
        for($i=0;$i<count($folderList)-1;$i++) {
            $path .= $folderList[$i]."/";
        }
        //echo ("path = '$path' & name=$pageName <br />");
    } else {
        $pageName = $page;
        $path = "";
    }



    // PAGENAME / PAGETYPE - zb. index / php
    $offSetName = strpos($pageName,".");
    if ($offSetName > 0) {
        $pageName = substr($pageName,0,$offSetName);
        $pageInfo["pageName"] = $pageName;
        $pageType = subStr($pageName,$offSetName+1);
        $pageInfo["pageType"] = $pageType;
    }
    $pageInfo["path"] = $path;


    $queryString = $_SERVER[QUERY_STRING];
    $queryList = explode("&",$dataString);
    $paraList = array();
    for ($i=0;$i<count($queryList);$i++) {
        $data = explode("=",$queryList[$i]);
        if (count($data)==2) {
            $paraList[$data[0]] = $data[1];
        }
    }
    $pageInfo["parameter"] = $paraList;

    
    if (!$lg) $lg = "dt";
    $pageInfo["lg"] = $lg;
   //  $_SESSION[lg] = $lg;

    return $pageInfo;
}

function cms_page_getSubPage($pageId,$sort="") {
    
    $pageData = cms_page_getSubpage_Session($pageId);
    if (is_array($pageData)) return $pageData;
    
    
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `mainPage` = $pageId ";
    
    if ($sort) {
        $upPos = strpos($sort, "_up");
        $sortQuery = "";
        if ($upPos) {
            $sortValue = substr($sort,0,$upPos);
            $sortQuery = "ORDER BY `$sortValue` DESC ";
                // echo ("Sort down '$sortValue' -> $sortQuery <br>");
        }
        
        if ($sortQuery=="") {
            $sortQuery = "ORDER BY `$sort` ASC ";
        }
        $query .= $sortQuery;
    }
        
    $result = mysql_query($query);
    // echo ("SubPage for $pageId $query <br>");
    $res = array();
    while ($subPage = mysql_fetch_assoc($result)) {
        $res[] = $subPage;

    }
    return $res;
}


function cms_page_getParallelPage($pageData,$sort="sort",$hideOwn=1) {
    if (!is_array($pageData)) {
        if (intval($pageData)) {
            $pageId = intval($pageData);
            $pageData = cms_page_getData($pageId);
        }        
    }
    
    if (!is_array($pageData)) {
        return "no PageData";
    }
    
    $pageId = $pageData[id];
    $mainPage = $pageData[mainPage];
    
    // from Session PageList
    if ($hideOwn ) $hidePage = $pageId;
    else $hidePage = 0;
    $res = cms_page_getParallelPage_Session($mainPage,$hidePage);
    if (is_array($res)) return $res;
    
    // echo ("Get Pages width mainPage = $mainPage AND id != $pageId <br>");
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `mainPage` = $mainPage ";
    if ($hideOwn) $query .= "AND `id` != $pageId ";
      if ($sort) {
        $upPos = strpos($sort, "_up");
        $sortQuery = "";
        if ($upPos) {
            $sortValue = substr($sort,0,$upPos);
            $sortQuery = "ORDER BY `$sortValue` DESC ";
                // echo ("Sort down '$sortValue' -> $sortQuery <br>");
        }        
        if ($sortQuery=="") {
            $sortQuery = "ORDER BY `$sort` ASC ";
        }
        $query .= $sortQuery;
    }
        
    $result = mysql_query($query);
    $res = array();
    while ($subPage = mysql_fetch_assoc($result)) {
        $id = $subPage[id];
        $res[] = $subPage;

    }
    return $res;
    
}

function cms_page_SelectMainPage($pageId,$dataName) {
    // echo ("cms_page_SelectMainPage($pageId,$dataName)<br />");
    $pageList = cms_page_getSortList();
    // foreach($pageList as $key => $value) echo ("page $key = $value <br />");

    
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$pageId' >";
    $level = 1;

    // noLink
    $str.= "<option value='noLink'";
    if (!$pageId OR $pageId=="noLink") $str.= " selected='1' ";
    $str.= ">Kein Link</option>";

    // Hauptebene
    $str.= "<option value='0'";
    if ($pageId == "0") $str.= " selected='1' ";
    $str.= ">Hauptebene</option>";

        

    foreach ($pageList as $pageName => $pageData) {
        $id = $pageData[id];
        $str.= "<option value='$id'";

        if ($pageId == $id) $str.= " selected='1' ";
        
        $showName = cms_text_getLg($pageData[title]);
        if ($showName=="") $showName = $pageData[name];
        $str.= ">$showName</option>";

        if (is_array($pageData[subNavi])) {
            $level++;
            foreach($pageData[subNavi] as $subName => $subPageData ) {
                $id = $subPageData[id];
                $str.= "<option value='$id'";

                if ($pageId == $id) $str.= " selected='1' ";
                $str.= ">";
                for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                $showName = cms_text_getLg($subPageData[title]);
                if ($showName=="") $showName = $subPageData[name];
                $str .= "$showName</option>";

                // 2. Ebene
                if (is_array($subPageData[subNavi])) {
                    $level++;
                    foreach( $subPageData[subNavi] as $subName => $subSubPageData ) {
                        $id = $subSubPageData[id];
                        $str.= "<option value='$id'";

                        if ($pageId == $id) $str.= " selected='1' ";
                        $str.= ">";
                        for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                        $showName = cms_text_getLg($subSubPageData[title]);
                        if ($showName=="") $showName = $subSubPageData[name];
                        $str .= "$showName</option>";
                        
                        if (is_array($subSubPageData[subNavi])) {
                            $level++;
                            foreach( $subSubPageData[subNavi] as $subName => $subSubSubPageData ) {
                                $id = $subSubSubPageData[id];
                                $str.= "<option value='$id'";

                                if ($pageId == $id) $str.= " selected='1' ";
                                $str.= ">";
                                for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                                $showName = cms_text_getLg($subSubSubPageData[title]);
                                if ($showName=="") $showName = $subSubSubPageData[name];
                                $str .= "$showName</option>";

                            }
                            $level--;
                        }
                        

                    }
                    $level--;
                }



            }
            $level--;
        }

    }
    $str.= "</select>";
    return $str;


}

function cms_page_get_Session($idOrName) {
    // echo ("cms_page_get_Session($idOrName) <br>");
    if (!is_array($_SESSION[pageList])) return 0;
    if (is_int($idOrName)) {
        foreach ($_SESSION[pageList] as $pageName => $pageData) {
            if ($pageData[id] == $idOrName) {
                // echo ("Found PageData with id = $pageName -> name = $pageName <br/>");
                if (is_string($pageData[data])) $pageData[data] = str2Array ($pageData[data]);
                return $pageData;
            }
        }
    }
    
    
    if (!is_string($idOrName)) return 0;
    if (!strlen($idOrName)) return 0;
    
    $pageData = $_SESSION[pageList][$idOrName];
    if (is_array($pageData)) {
        $pageId = $pageData[id];
        if (!$pageId) {
            echo ("<h1> NO PAGE ID for $idOrName </h1>");
            $newData = cms_page_getData($idOrName,0);
            if (is_array($newData)) {
                $_SESSION[pageList][$idOrName] = $newData;
                echo ("newData =$newData $newData[id]<br>");
                return $newData;
            }
            
        }
        if (is_string($pageData[data])) $pageData[data] = str2Array ($pageData[data]);
        return $pageData;                
    }
}

function cms_page_getSubpage_Session($idOrName) {
    // echo ("cms_page_getSubpage_Session($idOrName)<br>");
    
    if (is_string($idOrName)) {
        $help = intval($idOrName);
        if ($help > 0) $idOrName = $help;
    }
    
    
    if (!is_array($_SESSION[pageList])) return 0;
    
    if (is_string($idOrName)) {
        $pageData = cms_page_get_Session($idOrName);
        echo ("DATA is $pageData ");
        if (is_array($pageData)) {
            $mainPage = $pageData[id];
        }
    } else {
        $mainPage = $idOrName;
    } 
    if (!is_integer($mainPage)) return 0;
    
   
    $res = array();
    foreach ($_SESSION[pageList] as $pageName => $pageData) {
        $pageMainPage = $pageData[mainPage];
        if ($mainPage != $pageMainPage) continue;
        
        $res[$pageName] = $pageData;
    }
    return $res;    
}

function cms_page_getParallelPage_Session($mainPage,$hidePage) {
    // echo ("cms_page_getParallelPage_Session($mainPage,$hidePage)<br>");
    
    if (!is_array($_SESSION[pageList])) return 0;
    
    $res = array();
    foreach ($_SESSION[pageList] as $pageName => $pageData) {
        $pageMainPage = $pageData[mainPage];
        if ($mainPage != $pageMainPage) continue;
        if ($hidePage == $pageData[id]) continue;
        
        $res[$pageName] = $pageData;
    }
    return $res;    
}

function cms_page_destroy_session($idOrName=null) {
    if (is_int($idOrName)) {
        $pageData = cms_page_getData($idOrName);
        $pageName = $pageData[name];
    } 
    if (is_string($idOrName)) {
        $pageName = $idOrName;        
    }
    
    if (is_string($pageName)) {
        //echo "remove from Sesseion $pageName -> ".$_SESSION[pageList][$pageName]."<br />";
        if (is_array($_SESSION[pageList][$pageName])) {
            // echo "Found in Sesssion an Remove <br />";
            unset($_SESSION[pageList][$pageName]);
            // echo ("After remove = ".$_SESSION[pageList][$pageName]."<br />");
            return 1;
        }
    }
    unset($_SESSION[pageList]);
    // echo ("<h1>UNSET SESSION PAGELIST</h1>");
}

function cms_page_update_Session($pageData) {
    if (!is_array($pageData)) return 0;
    if (!is_array($_SESSION[pageList])) return 0;
    $pageName = $pageData[name];
    if (!$pageName) {
        echo ("<h1>no PageName in updatePageSession </h1>");
        foreach ($pageData as $key => $value ) echo ("update $key = $value <br />");
        return 0;
    }
    if (is_array($_SESSION[pageList][$pageName])) {
        foreach ($pageData as $key => $value ) {
            switch ($key) {
                case "data" :
                    $oldDataStr = array2Str($_SESSION[pageList][$pageName][$key]);
                    $newStr     = array2Str($value);
                    if ($oldDataStr != $newDataStr) {
                        // echo ("Change Data STR <br/>");
                        $_SESSION[pageList][$pageName][$key] = $value;
                    }
                    break;
                default :
                    if ($_SESSION[pageList][$pageName][$key] == $value) {
                        // echo ("No Change for $key ('$value')<br />");
                    } else {
                        // echo ("Change for $key old ='".$_SESSION[pageList][$pageName][$key]."' new='$value' <br/>");
                        $_SESSION[pageList][$pageName][$key] = $value;
                    }
                    
            }
        }
    } else {
        // echo ("Update hole pageData $pageName $pageData <br />");
        $_SESSION[pageList][$pageName] = $pageData;
    }
    
    
    // cms_infoBox("UPDATE PageList for $pageName ");
    
        
}


function cms_page_save($data,$compareData=null) {
   
    $id = $data[id];
    if ($id) {
        if (!is_array($compareData)) {
            $compareName = $data[name];
            if ($compareName) $compareData = cms_page_getData($compareName);
            else $compareData = cms_page_getData(intval($id)); //$compareName);cms_page_get(array("id"=>$id));
        }
        
        if (is_array($compareData)) {
            $res = cms_page_update($compareData,$data);
            return $res;
        } 
        
    }
    
    $out = 0;
    $targetPath = $_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName]."/";
    if ($data[targetPath]) {
        $targetPath = $data[targetPath];
        unset($data[targetPath]);
    }
    
    if ($data[out]) {
        $out = $data[out];
        unset($data[out]);
    }
    
   // show_array($data);
    $query = "";
    foreach ($data as $key => $value) {    
        if ($query) $query .= ", ";
        $query .= "`$key`='$value'";    
    }
    
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_pages` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error by Insert $query <br>");
        return 0;
    }
    $insertId = mysql_insert_id();
    if ($out) echo ("Insert id = $data[id] -> insertid = $insertId <br>");
    
    
    
    // show_array($data);
    return $insertId;
    
    
    
    
}
    
function cms_page_getInfoBack($pageName_pageId,$addArray=0) {
    if (is_array($pageName_pageId)) {
        $pageData = $pageName_pageId;
    } else {
        if (is_string($pageName_pageId)) {
            $pos = strpos($pageName_pageId,"?");
            if ($pos) $pageName_pageId = substr($pageName_pageId,0,$pos);
            

            $pos = strpos($pageName_pageId,".php");
            if ($pos) $pageName_pageId = substr($pageName_pageId,0,$pos);
        }
        // echo ("<h1>$pageName_pageId</h1>");
        $pageData = cms_page_getData($pageName_pageId);
    }
    
    
    if (!is_array($pageData)) return 0;
    // echo ("$pageName_pageId $pageData<br>");
   
    $icon = 0;
    $breadCrump = "";
    $showName = "";
    $breadCrumpList = array();
    
    
    
    $delimiter = " | ";
  
    if ($pageData[imageId]) $icon = $pageData[imageId]; 
    
    
    $title = $pageData[title];
    $name  = $pageData[name];
    if (!$title) $title = $name;
    
    $showName = $title; 
    $breadCrumb = $title;
    
    $mainPage = $pageData[mainPage];
    
    $dynamicBreadCrumbStr = "";
    $dynamicBreadCrumb = cms_dynamicPage_breadCrumb($pageData,$addArray);
    if (is_array($dynamicBreadCrumb)) {
        //for ($i=count($dynamicBreadCrumb)-1;$i>=0;$i--) {
        for ($i=0;$i<count($dynamicBreadCrumb);$i++) {
            $breadCrumpList[] = $dynamicBreadCrumb[$i];
            $dynamicName = $dynamicBreadCrumb[$i][name];
            // echo ("<h3>found $dynamicName </h3>");
            //if ($dynamicBreadCrumbStr) $dynamicBreadCrumbStr .= $delimiter;
            $dynamicBreadCrumbStr .= $delimiter.$dynamicName;
            $dynamicLastName = $dynamicName;
            $iconGet = $dynamicBreadCrumb[$i][icon];
            if ($iconGet) $dynamicIcon = $iconGet;
        }
    }
    
    
    $breadCrumpList[] = array("name"=>$title,"url"=>$name.".php","id"=>$id,"icon"=>$pageData[imageId]);
    
    
    if ($mainPage) {
        // echo ("getMainPage $mainPage<br>");
        while ($mainPage > 0) {
            $subPage = cms_page_getData(intval($mainPage));
            $lastId = $subPage[id];
            $title = $subPage[title];
            $title = cms_text_getLg($title);
            $name = $subPage[name];
            if (!$title) $title = $name;
            $breadCrumb = $title.$delimiter.$breadCrumb;
            
            if ($subPage[imageId] AND !$icon) $icon = $subPage[imageId];
            
            $breadCrumpList[] = array("name"=>$title,"url"=>$name.".php","id"=>$id,"icon"=>$subPage[imageId]);
            
            
            $mainPage = $subPage[mainPage];
            // $mainPage = 0;
        }
        
       
    } else {
        $lastId = $pageData[id];
    }
    
    // echo ("Last Id = $lastId <br>");
    if ($lastId != 1) { // not Index
        $indexData = cms_page_getData(1);

        $title = $indexData[title];
        $title = cms_text_getLg($title);
        $name  = $indexData[name];
        
        if (!$title) $title = $name;
        $breadCrumb = $title.$delimiter.$breadCrumb;

        if ($indexData[imageId] AND !$icon) $icon = $indexData[icon];
        
        $breadCrumpList[] = array("name"=>$title,"url"=>$name.".php","id"=>$id,"icon"=>$subPage[imageId]);
    }
    
    if ($dynamicBreadCrumbStr) {
        $breadCrumb .= $dynamicBreadCrumbStr;
    }
    if ($dynamicLastName) {
        $showName = $dynamicLastName;
    }
    if ($dynamicIcon) {
        $icon = $dynamicIcon;
    }
    
    $res = array();
    $res[name] = $showName;
    $res[breadCrumb] = $breadCrumb ;
    $res[icon] = $icon;
    $res[breadCrumbList] = $breadCrumpList;
    // show_array($res);
    return $res;
    
    
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
