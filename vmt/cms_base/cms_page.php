<?php // charset:UTF-8

function cms_page_getData($pageName_pageId) {

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
        $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `id` = $pageName_pageId ";
    } else {
        $query = "SELECT * FROM `".$cmsName."_cms_pages` WHERE `name` = '$pageName_pageId' ";
    }
    $result = mysql_query($query);
    if (!$result) {
        echo "ERror in Query '$query' <br />";
        return 0;
    }

    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        if ($_SESSION[userLevel] >= 9) {
            echo ("$query <br />");
            echo ("NO cms_page Found for '$pageName_pageId'<br />");
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
                echo ("<a href='$pageInfo[page]?createPage=1'>anlegen</a><br />");
            }
            // show_array($GLOBALS[pageInfo]);
            
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
    
    if (strlen($pageData[data])) $pageData[data] = str2Array ($pageData[data]);
    else $pageData[data] = array();
    // cmsHistory_set($pageData);
    return $pageData;
}


function cms_page_get($getData) {
    $query = "";
    foreach($getData as $key => $value) {
        //if ($value) {
            if ($query) $query .= ", ";
            $query .= "`$key` = '$value' ";
        // }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE $query";
    // echo ("Query = '$query' <br> ");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found <br>");
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
    


function cms_page_getList($getData) {
    $query = "";
    foreach($getData as $key => $value) {
        //if ($value) {
            if ($query) $query .= ", ";
            $query .= "`$key` = '$value' ";
        // }
    }
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE $query";
    // echo ("Query = '$query' <br> ");
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query '$query' <br>");
        return 0;
    }
    $anz = mysql_num_rows($result);
    // echo ("Anz = $anz <br>");
    if ($anz == 0) {
        echo ("Not found <br>");
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
    if ($forNavi) {
        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `navigation`= '1' ORDER by `sort` ";
        // echo ("Query for Navigation $query <br />");
    }
    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in query $query <br />");
        return 0;
    }

    $pageListUnsort = array();
    while ($page = mysql_fetch_assoc($result)) {
        $show = 1;
        if (substr($page[name],0,5) == "admin") {
            $show=cms_page_showAdminPage ($page[name]);
            // echo ("Admin Page $page[name] <br />");
        } 

        if ($show) {
            $page[show] = 0;
            $pageListUnsort[] = $page;
        }

    }

    $pageInfo = $GLOBALS[pageInfo];
    // show_array($pageInfo);
    $pageData = $GLOBALS[pageData];
    // show_array($pageData);
    $aktPageId = $pageData[id];

    // Hauptebene
    $pageList = array();
    for ($i=0;$i<count($pageListUnsort);$i++) {
        $page = $pageListUnsort[$i];
        $mainPage = $page[mainPage];
        $pageId = $page[id];

        if ($mainPage == 0) { // Hauptnavi
            $add = 1;
             
            $data = $page[data];
            if ($data AND !is_array($data)) $data = str2Array ($data);
            if ($data) {
                foreach ($data as $key => $value) {
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
            
            
            $page[show] = $add;
            $pageList["".$pageId] = $page;         
            
            if ($pageId == $aktPageId) {               
                $pageList["".$pageId][selectPage] = 1;
            }
            $pageListUnsort[$i][show] = 1;
            
            $dynamic = $page[dynamic];
            if ($dynamic) {
                $dynamicAdd = cms_dynamicPage_getSortList($page);
                if (is_array($dynamicAdd)) {
                    if (!is_array($pageList[$pageId][subNavi])) $pageList[$pageId][subNavi] = array();
                    foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                        if ($dynamicKey == "Projekte_Multimedia") {
                            // echo ("add Level 0 dynamicAdd $dynamicKey $dynamicData[name] $dynamicData[subNavi] <br />");
                            // show_array($dynamicData);
                        }
                        $pageList[$pageId][subNavi][$dynamicKey] = $dynamicData;

                    }                    
                }
            }
            
            
        }
    }

    // 1.Ebene
    for ($i=0;$i<count($pageListUnsort);$i++) {
        $page = $pageListUnsort[$i];
        $show = $page[show];
        
        
        
        if ($show == 0) {
            $pageId = $page[id];
            $mainPage = $page[mainPage];
            $idCode = "".$mainPage;
            

            if (is_array($pageList[$idCode])) {
                // echo ("-> found in ".$pageList[$idCode][title]." <br />");
                if (!is_array($pageList[$idCode][subNavi])) $pageList[$idCode][subNavi] = array();
                $pageList[$idCode][subNavi]["".$pageId] = $page;
                if ($pageId == $aktPageId) {
                    // echo ("ZEIGE aktuelle Seite in Ebene 1<br />");
                    $pageList[$idCode][subNavi]["".$pageId][selectPage] = 1;
                    $pageList[$idCode][subSelect] = 1;
                }


                $dynamic = $page[dynamic];
                if ($dynamic) {
                    $dynamicAdd = cms_dynamicPage_getSortList($page);
                    if (is_array($dynamicAdd)) {
                        if (!is_array($pageList[$pageId][subNavi])) $pageList[$pageId][subNavi] = array();
                        foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                            // echo ("add Level 1 dynamicAdd $dynamicKey $dynamicData[name] <br />");

                            $pageList[$idCode][subNavi]["".$pageId][subNavi][$dynamicKey] = $dynamicData;
                        }
                    }
                }


                $pageListUnsort[$i][show] = 1;
            } else {
                // echo ("not Found in ebene 1 '$page[title]' '$page[name]' $mainPage<br />");
            }
        }
    }

    // echo ("notShowed 2 <br />");

    // 2.Ebene
    for ($i=0;$i<count($pageListUnsort);$i++) {
        $page = $pageListUnsort[$i];
        $show = $page[show];
        if ($show == 0) {
            $pageId = $page[id];
            $mainPage = $page[mainPage];
            //  echo ("not showed2 $page[title] $page[name] $mainPage <br />");
            $idCode = "".$mainPage;
            foreach ($pageList as $mainNaviCode => $mainNaviPage) {
                if (is_array($mainNaviPage[subNavi])) {
                    if (is_array($mainNaviPage[subNavi][$idCode])) {
                       
                       // echo ("found in SUbNavi from $mainNaviPage[title] sub = ".$mainNaviPage[subNavi][$idCode][title]."<br />");
                        if (!is_array($mainNaviPage[subNavi][$idCode][subNavi])) $mainNaviPage[subNavi][$idCode][subNavi] = array();
                        $pageList[$mainNaviCode][subNavi][$idCode][subNavi]["".$pageId] = $page;
                        if ($pageId == $aktPageId) {
                         //   echo ("ZEIGE aktuelle Seite in Ebene 2<br />");
                            $pageList[$mainNaviCode][subNavi][$idCode][subNavi]["".$pageId][selectPage] = 1;
                            $pageList[$mainNaviCode][subNavi][$idCode][subSelect] = 1;
                            $pageList[$mainNaviCode][subSelect] = 1;
                        }

                        $dynamic = $page[dynamic];
                        if ($dynamic) {
                            $dynamicAdd = cms_dynamicPage_getSortList($page);
                            if (is_array($dynamicAdd)) {
                                if (!is_array($pageList[$pageId][subNavi])) $pageList[$pageId][subNavi] = array();
                                foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
                                    $pageList[$mainNaviCode][subNavi][$idCode][subNavi][$dynamicKey] = $dynamicData;
                                }
                            }
                        }


                        $pageListUnsort[$i][show] = 1;
                    }
                }
            }
        }
    }


    // 3.Ebene
    for ($i=0;$i<count($pageListUnsort);$i++) {
        $page = $pageListUnsort[$i];
        $show = $page[show];
        if ($show == 0) {
            $pageId = "".$page[id];
            $mainPage = $page[mainPage];
            // echo ("<h2>not showed2 $page[title] $page[name] $mainPage </h2>");
            $idCode = "".$mainPage;
            foreach ($pageList as $mainNaviCode => $mainNaviPage) {
                if (is_array($mainNaviPage[subNavi])) {
                    foreach ($mainNaviPage[subNavi] as $subKey => $subValue ) {
                        // echo ("Suche in $key $subValue[title] für '$page[title]' nach '$idCode'<br />");
                        if (is_array($subValue[subNavi])) {
                            if ($subValue[subNavi][$idCode]) {
//                                echo "<h2>found in $subValue[title] </h2>";
//                                echo ("Check  ".$pageList[$mainNaviCode][subNavi][$subKey][name]."<br>");
//                                echo ("Check2 ".$pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][name]."<br>");
                                if (!is_array($pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi])) $pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi] = array();
                                $pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi][$pageId] = $page;
                                
                                if ($pageId == $aktPageId) {
                                    echo ("ZEIGE aktuelle Seite in Ebene 2<br />");
                                    $pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi][$pageId][selectPage] = 1;
                                    $pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subSelect] = 1;
                                    $pageList[$mainNaviCode][subNavi][$subKey][subSelect] = 1;
                                    $pageList[$mainNaviCode][subSelect] = 1;
                                    
//                                    echo ("Select 1  ".$pageList[$mainNaviCode][selectPage]." - sub=".$pageList[$mainNaviCode][subSelect]." <br>");
//                                    echo ("Select 2  ".$pageList[$mainNaviCode][subNavi][$subKey][selectPage]." - sub=".$pageList[$mainNaviCode][subNavi][$subKey][subSelect]."<br>");
//                                    echo ("Select 3 ".$pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][selectPage]." - sub=".$pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subSelect]."<br>");
//                                    echo ("Select 3 ".$pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi][$pageId][selectPage]." - sub=".$pageList[$mainNaviCode][subNavi][$subKey][subNavi][$idCode][subNavi][$pageId][subSelect]."<br>");
//                                    
                                }
//
//                                $dynamic = $page[dynamic];
//                                if ($dynamic) {
//                                    $dynamicAdd = cms_dynamicPage_getSortList($page);
//                                    if (is_array($dynamicAdd)) {
//                                        if (!is_array($pageList[$pageId][subNavi])) $pageList[$pageId][subNavi] = array();
//                                        foreach ($dynamicAdd as $dynamicKey => $dynamicData) {
//                                            $pageList[$mainNaviCode][subNavi][$idCode][subNavi][$dynamicKey] = $dynamicData;
//                                        }
//                                    }
//                                }


                                  $pageListUnsort[$i][show] = 1;





                            }
                            
                        }
                    }
                    if (is_array($mainNaviPage[subNavi][subNavi])) {
                        if (is_array($mainNaviPage[subNavi][subNavi][$idCode])) {
                            echo ("found '$idCode' in subSubNavi from $mainNaviPage[title] / ".$mainNaviPage[subNavi][title]." / ".$mainNaviPage[subNavi][subNavi][title]." sub = ".$page[title]."<br />");

                            // if (!is_array($mainNaviPage[subNavi][$idCode][subNavi])) $mainNaviPage[subNavi][$idCode][subNavi] = array();
                           // $pageList[$mainNaviCode][subNavi][$idCode][subNavi]["".$pageId] = $page;
                          //  $pageListUnsort[$i][$show] = 1;
                        }
                    }
                }
            }
        }
    }

//    // 4.Ebene
//    for ($i=0;$i<count($pageListUnsort);$i++) {
//        $page = $pageListUnsort[$i];
//        $show = $page[show];
//        if ($show == 0) {
//            $pageId = $page[id];
//            $mainPage = $page[mainPage];
//            // echo ("not showed2 $page[title] $page[name] $mainPage <br />");
//            $idCode = "".$mainPage;
//            foreach ($pageList as $mainNaviCode => $mainNaviPage) {
//                if (is_array($mainNaviPage[subNavi])) {
//                    foreach ($mainNaviPage[subNavi] as $key => $value ) {
//                        echo ("Suche in $key $value[title] für '$page[title]' nach '$idCode'<br />");
//                    }
//                    if (is_array($mainNaviPage[subNavi][subNavi])) {
//                        if (is_array($mainNaviPage[subNavi][subNavi][$idCode])) {
//                            echo ("found '$idCode' in subSubNavi from $mainNaviPage[title] / ".$mainNaviPage[subNavi][title]." / ".$mainNaviPage[subNavi][subNavi][title]." sub = ".$page[title]."<br />");
//
//                            // if (!is_array($mainNaviPage[subNavi][$idCode][subNavi])) $mainNaviPage[subNavi][$idCode][subNavi] = array();
//                           // $pageList[$mainNaviCode][subNavi][$idCode][subNavi]["".$pageId] = $page;
//                          //  $pageListUnsort[$i][$show] = 1;
//                        }
//                    }
//                }
//            }
//        }
//    }

    return $pageList;

    // Administration
    $foundAdmin = 0;
    $maxId = 0;
    foreach ($pageList as $key => $value) {
        if ($key > $maxId) $maxId = $key;
        //echo ($key." - ".$value[name]."<br />");
        if ($value[name] == "admin") $foundAdmin = $key;
    }

    if ($foundAdmin == 0) {
        $adminData = array();
        $adminData[name] = "admin";
        $adminData[title] = "Administration";
        $adminData[navigation] = 1;
        $adminData[breadcrumb] = 1;
        $adminData[sort] = 200;
        $adminData[showLevel] = 8;
        $adminData[mainPage] = 0;
        $adminData[show] = 1;
        $maxId++;
        $pageList[$maxId] = $adminData;
        $maxId = count($pagelist);
    } else {
        $adminData = $pageList[$foundAdmin];
    }
  //  show_array($adminData);

    $adminSub = array();
    /// CMS
    $cmsData = array("name"=>"admin.php?view=cmsCms", "title"=>"CMS-Verwaltung","navigation"=>1,"breadcrump"=>1,"showLevel"=>8,"mainPage"=>$foundAdmin,"subNavi"=>array() );
    $cmsData[subNavi][] = array("name"=>"admin.php?view=cmsLayout", "title"=>"CMS Layout","navigation"=>1,"breadcrump"=>1,"showLevel"=>8,"mainPage"=>$foundAdmin );
    $adminSub[] = $cmsData;

    // DATEN
    $cmsData = array("name"=>"admin.php?view=cmsData", "title"=>"CMS-Daten","navigation"=>1,"breadcrump"=>1,"showLevel"=>8,"mainPage"=>$foundAdmin,"subNavi"=>array() );
    $cmsData[subNavi][] = array("name"=>"admin.php?view=cmsUser", "title"=>"CMS Benutzer","navigation"=>1,"breadcrump"=>1,"showLevel"=>8,"mainPage"=>$foundAdmin );
    $cmsData[subNavi][] = array("name"=>"admin.php?view=cmsDates", "title"=>"CMS Termind","navigation"=>1,"breadcrump"=>1,"showLevel"=>8,"mainPage"=>$foundAdmin );
    $adminSub[] = $cmsData;

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


        case "admin_company"  : $type = "company"; break;
        case "admin_category"  : $type = "category"; break;
        case "admin_cmsMail"  : $type = "email"; break;
        case "admin_cmsUser"  : $type = "user"; break;
        case "admin_cmsDates" : $type = "dates"; break;
        case "admin_product"  : $type = "product"; break;
        default :
            if (substr($adminPage,0,6) == "admin_") {

                $type = substr($adminPage,6);
                // echo ("unkown Admin Type ".$type."<br />");
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
    if ($anz > 0) return 1;
    return 0;
}

function cms_page_update($pageId,$changeData=array()) {
    $query = "";
    if (is_array($pageId)) {
        $compareData = $pageId;
        $pageId = $compareData[id];
    }
    $diff = 0;
    
    // show_array($changeData);
    
    foreach ($changeData as $key => $value) {
        if (is_array($value)) $value = array2Str ($value);
        $add = 1;
        if (is_array($compareData)) {
            if ($compareData[$key] == $value) {
                
                $add = 0;
            } else {
                echo ("Diffrent old ='$compareData[$key]' new = '$value' <br>");
            }
        }
        if ($add) {
            $diff++;
            if ($query!="") $query .= ", ";
            $query .= "`$key`='$value'";
        }
    }
    
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
    echo ("cms_page_create($newName,$newData)<br>");
    $pageExist = cms_page_exist($newName);
    if ($pageExist != 0) {
        //echo ("cms_page_create - database entry for $newName exist <br />");
    } else {

        if (!is_array($newData)) $newData = array();

        $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_pages` SET `name`='$newName', `sort`=9999 ";
        foreach ($newData as $key => $value) {
            switch ($key) {
                case "name" : break;
                case "addPageTo" ; break;
                default:
                    $query .= ", `$key` = '$value' ";
            }
        }

//    echo ("file '' = ".file_exists("cms/emptyPage.php")." <br />");
//    echo ("file / = ".file_exists("/cms/emptyPage.php")." <br />");
//    echo ("file ../ = ".file_exists("../cms/emptyPage.php")." <br />");
//    echo ("file root = ".file_exists($_SERVER['DOCUMENT_ROOT']."/cms/emptyPage.php")." <br />");
    
        $result = mysql_query($query);
        if (!$result) { 
            echo ("cms_page_create - Error by Create page '$newName' in Database <br />");
            echo ("$query <br />");
            return 0;
        } else {
            echo ("cms_page_create - page '$newName' added to Database <br />");
        }
    }
    
    
    
    echo ("exist /".$GLOBALS[cmsName]."/".$newName.".php <br>");
    global $cmsVersion;
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName]."/".$newName.".php")) {
        return 1;
    }
    
//    if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$newName.".php")) {
//        return 1;
//    }
    // return 0;
    
    $resCopy = copy($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/emptyPage.php",$_SERVER['DOCUMENT_ROOT']."/".$GLOBALS[cmsName]."/".$newName.".php");
    if ($resCopy) echo ("Datei wurde kopiert <br />");
    else {
        echo "copy cms_".$cmsVersion."/emptyPage.php nach $newName.php schlug fehl...\n";
        return 0;
    }

    return 1;
    
}


function cms_page_delete($deleteId) {
    // echo ("Delete Page with id $deleteId <br>");
    
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
    
    
    
    
    
    
    $query = "DELETE FROM `".$GLOBALS[cmsName]."_cms_pages` WHERE `id` = $deleteId";
    $result = mysql_query($query);    
    if (!$result) {
        cms_errorBox("Fehler beim Löschen der Seite<br>".$query);
        return 0;
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

function cms_page_editData() { // $pageData,$pageInfo) {
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
        
        foreach ($savePageData as $key => $value ) {
            
            
//             switch ($key) {
//                  case "data" :
//                      $value = array2Str($value);
//                      echo ("Daten für $key => $value <br>");
//                      break;
//                    if ($value) {
//            }
            
            
            
            if ($pageData[$key] != $value) {
                $changeData[$key] = $value;
                $pageData[$key] = $value;
               
            }
            // echo (" -> $key = $value <br />");
        }

        if (count($changeData) == 0) {
            cms_infoBox("keine Veränderung / nicht gespeichert");

            $goPage = $pageInfo[page];
            if ($saveButton) $goPage .= "?editMode=".$_GET[editMode];
            $seconds=1;
            reloadPage ($goPage, $seconds);
            
        } else {
            $res = cms_page_update($id,$changeData);
            if ($res == 1) {
                cms_infoBox("Daten gespeichert !!");
                if ($saveButton) { $goPage = $pageInfo[page]."?editMode=".$_GET[editMode]; $seconds=3; }
                if ($saveAndClose) { $goPage = $pageInfo[page]; $seconds = 1;}
               
                reloadPage ($goPage, $seconds);
                if ($saveAndClose) return 1;
            }
        }
    }

    // foreach($pageInfo as $key => $value) echo ("PageInfo $key = $value <br />");
    echo ("<h3>Seiten Daten bearbeiten</h3>");
    
    $data = $pageData[data];
    if (!is_array($data)) $data = array();
    
//    foreach ($GLOBALS[cmsSettings][data] as $key => $value) {
//        echo ("Settings $key = $value <br>");
//    }
    // show_array($pageData);
    //foreach($pageData as $key => $value) echo ("PageData $key = $value <br />");
    $div1 = div_start_str("inputLine").div_start_str("inputLeft","width:200px;float:left;padding-top:5px;");
    $div2 = div_end_str("inputLeft").div_start_str("inputRight","float:left;");
    $div3 = div_end_str("inputRight").div_end_str("inputLine","before");
    echo ("<form method='post'>\n");

    echo ("<input type='hidden' name='savePageData[id]' value='$pageData[id]' >");
    echo ($div1."Name:");
    echo ($div2."<input type='text' name='savePageData[name]' value='$pageData[name]' disabled='1' >\n");
    echo ($div3);

    echo ($div1."Name:");
    echo ($div2."<input type='text' name='savePageData[title]' value='$pageData[title]' >\n");
    echo ($div3);


    echo ($div1."Seiten-Titel:");
    echo ($div2."<input type='text' name='savePageData[pageTitle]' style='width:600px;' value='$pageData[pageTitle]' >\n");
    echo ($div3);


    echo ($div1."Seiten-Beschreibung:");
    echo ($div2."<textarea type='text' name='savePageData[description]' style='width:600px;height:80px;' >$pageData[description]</textarea>\n");
    echo ($div3);
    
    echo ($div1."Seiten-Keywords:");
    echo ($div2."<textarea type='text' name='savePageData[keywords]' style='width:600px;height:80px;' >$pageData[keywords]</textarea>\n");
    echo ($div3);
   
    
    echo ($div1."Icon:");
    echo ($div2);
    
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

    $input .= "<input type='$inputType' class='cmsImageId' style='width:30px;' name='savePageData[imageId]' value='$pageData[imageId]' />";
    $input .= "</div>";
    
    echo ($input);
    
//    echo ("<div class='cmsImageFrame'>");
//    echo ("<img src='' class='cmsImageSelectModul' height='90px' width='90px' />");
//    echo ("</div>");
//    echo ("<input class='cmsImageId' style='width:30px;' name='editContent[data][imageId]' value='' type='hidden' />");
    echo ($div3);

    echo ($div1."Dynamische Seite:");
    $dynamic = $pageData[dynamic];
    if ($dynamic) $checked="checked='checked'";
    else $checked = "";
    echo ($div2);
    echo ("<input type='checkbox' value='1' name='savePageData[dynamic]' $checked />");
    echo ($div3);
    
    
    if ($dynamic) {
        // if (is_array($data)) show_array($data);
        $dataSource = $data[dataSource];
        // echo ("Dynamische Seite '$dataSource' <br />");
        echo ($div1."Quelle Dynamische Inhalte:");
        echo ($div2.cms_dynamicPage_Source($dataSource,"savePageData[data][dataSource]"));
        echo ($div3);
        switch ($dataSource) {
            case "category" :
                $mainCat = $data[mainCat];
                echo ($div1."Kategorie wählen:");
                $showData = array();
                $filter = array("mainCat"=>"0");
                $sort = "name";
                echo ($div2.cmsCategory_selectCategory($mainCat,"savePageData[data][mainCat]", $showData, $filter, $sort));
                echo ($div3);
                
                if ($mainCat) {
                    echo ($div1."Unter-Kategorie wählen:");
                    $subCat = $data[subCat];
                    $showData = array();
                    $showData["empty"] = "Keine Unterkategorie";
                    $filter = array("mainCat"=>$mainCat);
                    $sort = "name";
                    echo ($div2.cmsCategory_selectCategory($subCat,"savePageData[data][subCat]", $showData, $filter, $sort));
                    echo ($div3);                    
                }
        }
        
        
        echo ($div1."Dynamische Inhalte");
        echo ($div2."<input type='checkbox' name='savePageData[data][dynamic2]' value='1' ");
        if ($data[dynamic2]) echo "checked='checked'";
        echo (">\n");echo ($div3);
        
        $dataSource2 = $data[dataSource2];
        echo ($div1."Quelle 2. Ebene Dynamische Inhalte:");
        echo ($div2.cms_dynamicPage_Source($dataSource2,"savePageData[data][dataSource2]"));
        echo ($div3);
        
    
    }
    
    echo ($div1."Übergeordnete Seite:");
    echo ($div2.cms_page_SelectMainPage($pageData[mainPage],"savePageData[mainPage]"));
    echo ($div3);

    echo ($div1."Zeigen ab UserLevel");
    echo ($div2.cms_user_SelectLevel($pageData[showLevel],$_SESSION[userLevel],"savePageData[showLevel]"));
    echo ($div3);
    
    
    echo ($div1."Zeigen bis UserLevel");
    echo ($div2.cms_user_SelectLevel($pageData[toLevel],$_SESSION[userLevel],"savePageData[toLevel]"));
    echo ($div3);
    
    
    $contentType_class = cms_contentTypes_class();
    $special_viewFilter = $contentType_class->use_special_viewFilter($pageData,"savePageData");
    if (is_array($special_viewFilter)) {
        foreach($special_viewFilter as $key => $value) {
            echo ($div1.$value[text]);
            echo ($div2.$value[input]);
            //show_array($value);
            echo ($div3);                             
        }
    }

    echo ($div1."Layout:");
    echo ($div2.cms_layout_SelectLayout($pageData[layout],"savePageData[layout]"));
    echo ($div3);



    echo ($div1."Seite in Navigation anzeigen:");
    echo ($div2."<input type='checkbox' name='savePageData[navigation]' value='1' ");
    if ($pageData[navigation]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ($div1."Seiten-Pfad anzeigen:");
    echo ($div2."<input type='checkbox' name='savePageData[breadcrumb]' value='1' ");
    if ($pageData[breadcrumb]) echo "checked='checked'";
    echo (">\n");
    echo ($div3);

    echo ("<input type='submit' class='cmsInputButton' name='saveAndClose' value='speichern und schließen' > &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton' name='saveButton' value='speichern'> &nbsp; ");
    echo ("<input type='submit' class='cmsInputButton cmsSecond'name='saveCancel' value='abbrechen' > &nbsp; ");
    echo ("<form>");
    echo ("<br />\n");
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
    if ($page == "404.php") {
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

    return $pageInfo;
}

function cms_page_getSubPage($pageId,$sort="") {
    
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
        
        $showName = $pageData[title];
        if ($showName=="") $showName = $pageData[name];
        $str.= ">$showName</option>";

        if (is_array($pageData[subNavi])) {
            $level++;
            foreach( $pageData[subNavi] as $subName => $subPageData ) {
                $id = $subPageData[id];
                $str.= "<option value='$id'";

                if ($pageId == $id) $str.= " selected='1' ";
                $str.= ">";
                for ($l=1;$l<$level;$l++) $str .= "&nbsp; ";
                $showName = $subPageData[title];
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
                        $showName = $subSubPageData[title];
                        if ($showName=="") $showName = $subSubPageData[name];
                        $str .= "$showName</option>";

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







function cms_page_save($data) {
   
    $id = $data[id];
    if ($id) {
        $compareData = cms_page_get(array("id"=>$id));
        
        if (is_array($compareData)) {
            $res = cms_page_update($compareData,$data);
            return $res;
        } 
        
    }
    
    show_array($data);
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
    echo ("Insert id = $data[id] -> insertid = $insertId <br>");
    
    
    // show_array($data);
    return 1;
    
    
    
    
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
        $pageData = cms_page_getData($pageName_pageId);
    }
    // echo ("$pageName_pageId<br>");
   
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
        for ($i=count($dynamicBreadCrumb)-1;$i>=0;$i--) {
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
