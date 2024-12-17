<?php // charset:UTF-8

class cmsAdmin_dates_base extends cmsAdmin_editClass_base {
    
    function admin_dataSource() {
        return "dates";;
    }
    
    
    function admin_listSettings() {
        $res = $this->datesShow_showList();
        return $res;
    }
    
    function show($frameWidth){
        if (!function_exists("cmsDates_get")) {
            echo ("Termine sind deaktiviert !<br>");
            return 0;
        }
            
        
        $view = $_GET[view];
        $dateId = $_GET[id];
        if ($dateId) $view = "edit";

        $this->tableName = $this->admin_dataSource();
        switch ($view) {
             case "editShow" :
                $this->edit_editShow();
                break;
            
            case "editList" :
                $this->edit_editList();
                break;
            case "new" :
                $this->cmsDates_edit(0,$frameWidth);
                break;

             case "edit" :
                $this->cmsDates_edit($dateId,$frameWidth);
                break;

            case "list" :
                $this->show_list($frameWidth);
                break;
            default :
                $this->show_list($frameWidth);

        }
    }
    
    function show_list() {
        $this->cmsDates_list($this->frameWidth);
    }
    
    function show_new() {
        $projectId = 0;
        $this->cmsDates_edit($projectId,$this->frameWidth); 
    }
    
    function show_edit() {
        $projectId = $_GET[id];        
        $this->cmsDates_edit($projectId,$this->frameWidth);        
    }


    function cmsDates_list($frameWidth) {
        global $pageInfo;

        $_data = $this->admin_showFilter($frameWidth);
       
        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;

        if (is_array($_data) AND count($_data)) {
            foreach ($_data as $key => $value) {
                switch ($key) {
                    case "sort" : $sort = $value; break;
                    case "page" : break;

                    case "date" :
                        $day = substr($value,8,2);
                        $month = substr($value,5,2);
                        $year = substr($value,0,4);
                        $selectDay = mktime(12,0,0,$month,$day,$year);
                        $dayCode = intval(date("w",$selectDay));
                        $dayStr = cmsDates_dayStr($dayCode);
                        // echo ("Datum = $dayStr, den ".date("d.m.Y",$selectDay)."<br>");
                        $filter[date] = $value;
                        break;

                    case "category" : if ($value) $filter[$key] = $value; break;
                    case "filter_category" : if ($value) $filter["category"] = $value; break;
                    case "region"   : if ($value) $filter[$key] = $value; break;
                    case "filter_region"   : if ($value) $filter["region"] = $value; break;
                    case "region"   : if ($value) $filter[$key] = $value; break;

                    case "filter_location" :

                        $locationData = cmsLocation_get(array("name"=>$value,"show"=>1));

                        if (is_array($locationData)) {
                            $locationId = $locationData[id];
                            $filter[location] = $locationId;
                        }
                        break;
                    case "filter_content" :
                        if ($value) $filter["search"] = $value; break;

                    case "filter_dateRange"   :
                        //echo ("<h1> FILTER DATERANGE $value</h1>");
                        $dateRangeList = $this->dateRange_filter_select_getList();
                        // show_array($dateRangeList);
                        $dateRange = $dateRangeList[$value];
                        if (is_array($dateRange)) {
                            if ($dateRange[filter]) {
                                foreach ($dateRange[filter] as $filterKey => $filterValue) $filter[$filterKey] = $filterValue;
                            }
                            if ($dateRange[sort]) $sort = $dateRange[sort];
                        }
                        break;


                        if ($value) $filter["dateRange"] = $value; break;
                    // case "dateRange"   :
                        if ($value) $filter[$key] = $value; break;

                    case "filter_specialView"   :
                        $specialFilterList = $this->admin_get_specialFilterList_own();
                        if (is_array($specialFilterList[$value])) {
                            $specialFilter = $specialFilterList[$value];
                            // show_array($specialFilter);

                            // APPEND Filter to Filter
                            if (is_array($specialFilter[filter])) {
                                foreach($specialFilter[filter] as $key => $value ) {
                                    $filter[$key] = $value;
                                    // echo "append $key = $value to filter <br>";
                                }
                            }
                            if ($specialFilter[sort]) {
                                if (is_string($specialFilter[sort])) $sort = $specialFilter[sort];
                            }

                            //echo ("Filter =".$filter);
                            // echo ("Sort = $sort <br>");

                        } else {
                            echo ("Filter SpecialView $key = $value <br>");
                        }
                        break;
                        //if ($value) $filter["region"] = $value; break;
                    default :
                        echo ("Unkown $key in get/post_data = $value <br>");

                }
            }
        }
        $newLink = "";
        foreach ($_GET as $key => $value) {
            if ($value) $newLink.= "&$key=$value";            
        }
        

        // $this->admin_show_filterSort($filter,$sort);

        $datesList = cmsDates_getList($filter,$sort,"query");
        $this->checkList($datesList,"ticketUrl");
        $this->admin_show_list($datesList);
        return 0;
        
        $newLink .= "&view=new";
        $newLink = php_addUrl($pageInfo[page],$newLink);
        
        echo ("<a href='$newLink' class='cmsLinkButton' >neue Termin anlegen</a> <br>");
        
        $showList = $this->datesShow_showList();
        
        
        $showData = array();
        $showData[pageing] = array();
        $showData[pageing][count] = 20;
        $showData[pageing][showTop] = 1;
        $showData[pageing][showBottom] = 1;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 1; // small | all

        $this->showList_List($datesList,$showList,$showData,$frameWidth);


        echo ("<a href='$newLink' class='cmsLinkButton' >neue Termin anlegen</a>");
    }


    function datesShow_showList() {
        $showList = array();
        $showList["image"]     = array("name"=>"Bild","width"=>70,"height"=>40,"sort"=>0);
        $showList["date"]      = array("name"=>"Datum","width"=>60,"sort"=>1);
        $showList["category"]  = array("name"=>"Rubrik","width"=>160,"align"=>"left");
        $showList["location"]  = array("name"=>"Location","width"=>140,"sort"=>1);
        $showList["name"]      = array("name"=>"Titel","width"=>140,"align"=>"left");
        $showList["time"]      = array("name"=>"Uhrzeit","width"=>50);
        $showList["show"]      = array("name"=>"zeigen","width"=>50,"align"=>"center","sort"=>0);
        $showList["highlight"] = array("name"=>"Tip","width"=>50,"align"=>"center","sort"=>1);
        $showList["print"]     = array("name"=>"Print","width"=>50,"align"=>"center","sort"=>1,"type"=>"checkbox");
        // $showList["new"]       = array("name"=>"Neu","width"=>50,"align"=>"center","sort"=>1);
        return $showList;
    }
    
    

    function cmsDates_edit($dateId,$frameWidth) {       
        
        $resolveLinkDate = 0;
        $linkedDate = 0;
        if ($dateId > 0) {
            $mode = "edit";
            $headLine = "Termin bearbeiten";
            $filter = array("id"=>$dateId);
            if ($resolveLinkDate == 0) $filter["dontLinkDate"] = 1;
            $saveData = cmsDates_get($filter);
            if ($saveData[mainId]) {
                $mainId = $saveData[mainId];
                if ($resolveLinkDate) {
                    echo ("Date is link -> MainId = $mainId <br>");
                    $newSaveData = cmsDates_getById($mainId);
                    if (is_array($newSaveData)) {
                        $saveData = $newSaveData;
                        $dateId = $mainId;
                    }
                } else {
                    $headLine = "Weiteren Termin bearbeiten";
                    // echo ("<h2>Weiterer Termin bearbeiten</h2>Date is link -> MainId = $mainId <br>");
                    $linkedDate = 1;
                }
            }

            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);     
                if ($linkedDate) {
                    $saveData[mainId] = $mainId;
                }
            }

        } else {
            $mode = "new";
            $headLine = "Termine anlegen";
            $saveData = $this->emptyData(); // array();
            if ($_POST[saveData]) {
                $saveData = $_POST[saveData];
                $saveDataId = $saveData[id];
                if ($saveDataId) {
                    $mode = "edit";
                    $dateId = $saveDataId;
                }

            }
        }
        echo ("<h1>$headLine</h1>");
        
       // show_array($saveData);
        ///$lastUrl = $this->backLink();
        // foreach ($_SESSION[lastPages] as $key => $value ) echo ("last $key => $value <br>");
        // echo ("<b> Last $lastUrl </b><br>" );
        
       
        global $pageInfo;

        $specialData = array();
        $specialData[id] = $saveData[id];

        $tableName = "dates";
        $editShow = $this->edit_show($tableName,$specialData);

        if (is_array($editShow[link][showData][date])) {
            $editShow[link][showData][date][showName] = $dateId;
        }
        
        
//        if ($saveData[$link]) {
//            echo ("Link : '$saveData[$link] <br>");
//            // $this->linkDate("check",$specialPostList[linkDate],$specialPostList[link],$saveData);
//        }
            
        

        // disable FirstFocus if edit / not new
        if ($mode=="edit") {
            if ($editShow[date][id]=="firstFocus") $editShow[date][id] = "";
        } else {
            if (is_array($editShow[link][showData])) {
                $editShow[link][showData][date][show] = 0;
                $editShow[link][showData][article][show] = 0;
            }            
            // show_array($editShow[link]);
        }
       
        
        $goPage = "";
        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "view" : break;
                case "id" : break;

                default :
                    if ($goPage == "") $goPage.= "?";
                    else $goPage .= "&";
                    $goPage .= "$key=$value";
            }
        }
        $goPage = $pageInfo[page].$goPage;
        $reloadPage = 1;       
        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST,$tableName);
            // show_array($specialPostList);
            
            foreach ($specialPostList as $key => $value ) {
                // echo ("specialPostList $key = $value link=$saveData[link]<br>");
                switch ($key) {
                    case "saveAdress" :
                        echo ("<h1>saveAdress</h1>");
                        $locationStr = $saveData[locationStr];
                        if ($locationStr) {
                            $saveData = $this->checkData($saveData);
                            $locationData = array();
                            $locationData[name] = $locationStr;
                            $locationData[street] = $saveData[data][street];
                            $locationData[streetNr] = $saveData[data][streetNr];
                            $locationData[plz] = $saveData[data][plz];
                            $locationData[city] = $saveData[data][city];
                            $locationData[url] = $saveData[data][url];
                            $locationData[ticketUrl] = $saveData[data][ticketUrl];
                            
                            $locationData[region] = $saveData[region];
                            $locationData[subName] = "";
                            $locationData[category] = "";
                            $locationData[data] = array();
                            
                            $locationData[show] = "0";
                            $locationData[changeLog] = date("Y-m-d H:i:s")." ".time().",".$_SESSION[userId].",Angelegt über Termin";
                             
                           
                            
                           //  show_array($locationData);
                            
                            $newLocationId = cmsLocation_save($locationData);
                            echo ("Save Location $newLocationId <br>");
                            if ($newLocationId >0) {
                                cms_infoBox("Adresse gespeichert<br>");                                
                                $saveData[location] = $newLocationId;
                                $saveData[locationStr] = "";                                                                
                            } else {
                                cms_errorBox("Fehler beim Adresse speichern");
                            }
                            
                            
                            // $saveData[locationStr] = $locationStr;
                        } else {
                            cms_errorBox("Adresse speichern<br>Kein Name eingegeben");
                           // echo ("<h1>No LocationName </h1>");

                           //show_array($_POST,0,1);

                        }

                        break;


                    case "categoryId"   : $saveData["category"] = $value; break;
                    case "categoryName" : $categoryName = $value; break;

                    case "locationId" : $saveData[location] = $value; break;
                    case "locationName" : $locationName = $value; break;

                    case "regionId" : $saveData[region] = $value; break;
                    case "regionName" : $regionName = $value; break;

                    case "date" : $saveData[date] = $value; break;
                    case "time" : $saveData[time] = $value; break;
                    case "toTime" : $saveData[toTime] = $value; break;
                    case "fromDate" : $saveData[fromDate] = $value; break;
                    case "toDate" : $saveData[toDate] = $value; break;
                    case "imageListStr" :
                        $saveData[image] = $value;
                        echo ("Set Image to $value<br>");
                        $reloadPage = 0;
                        break;

                    case "imageId" :
                        $oldImage = $saveData[image];
                        if ($oldImage) {
                            if (intval($oldImage)) $imageStr = "|".$oldImage."|";
                            else {
                                $imageStr = $oldImage;
                            }
                            $imageStr .= $value."|";
                            echo ("Set newImage to $imageStr was ($oldImage)<br>");
                            $saveData[image] = $imageStr;
                            $reloadPage = 0;
                            cms_infoBox("Bild hochgeladen ");
                        } else {

                            $saveData[image] = $value;
                            $reloadPage = 0;
                            cms_infoBox("Bild hochgeladen ");
                        }
                        break;
                    case "dateLinkString" : break;
                    case "linkDate" :
                        if ($linkedDate == 0) {
                            // echo ("<h2>LinkDate</h2>");
                            $res = $this->linkDate("check",$specialPostList[linkDate],$specialPostList[link],$saveData);
                            // echo ("<h2>LinkDate '$res'</h2>");
                            if ($res) {
                                cms_infoBox("weitere Termin aktuallisiert $res ");
                                if ($goPage == $pageInfo[page]) $goPage.= "?";
                                else $goPage .= "&";
                                $goPage .= "view=edit&id=$dateId";
                                $reloadPage = 1;
                                $lastUrl = "";
                                $doSave = 1;

                            } else {
//                                if ($res == 0) {
//                                    echo ("Hier");
//                                }
//                                echo ("Error in LinkAdd $res <br>");
//                                // show_array($specialPostList);
//                                $reloadPage = 0;
//                                $doSave = 0;
                            }
                        }
                        break;

                    case "addSubDate" :
                        echo ("<h2>addSubDate $value</h2>");                        
                        if ($linkedDate == 0) {
                            $res = $this->linkDate("add",$specialPostList[linkDate],$specialPostList[link],$saveData,$value);
                            if (is_string($res)) {
                                $reloadPage = 1;
                                $saveData[link] = $res;
                                if ($goPage == $pageInfo[page]) $goPage.= "?";
                                else $goPage .= "&";
                                $goPage .= "view=edit&id=$dateId";
                                $doSave = 1;
                                $lastUrl = "";
                            } else {
                                echo ("Result of linkDateAdd = $res<br>");
                            }
                        }
                        break;

                    case "delSubDate" :
                        if ($linkedDate == 0) {
                            // echo ("<h2>delSubDate $value</h2>");
                            $res = $this->linkDate("delete",$specialPostList[linkDate],$specialPostList[link],$saveData,$value);
                            if (is_string($res)) {
                                cms_infoBox("weitere Termin gelöscht");
                                $saveData[link] = $res;
                                if ($goPage == $pageInfo[page]) $goPage.= "?";
                                else $goPage .= "&";
                                $goPage .= "view=edit&id=$dateId";
                                $reloadPage = 1;
                                $doSave = 1;
                                $lastUrl = "";
                            } else {
                                echo ("Result of linkDateAdd = $res<br>");
                            }
                        }
                        break;
                    
                    case "articleLinkString" : break;
                    case "link" :
                        // echo ("<h2>LINK</h2>");
                        if (is_array($value)) {
                            $setLink = "";
                        
                            foreach ($value as $linkKey => $linkValue) {
                               //  echo ("LINK $linkKey $linkValue <br>");
                                switch ($linkKey) {
                                    case "article" : 
                                        if ($setLink) $setLink .= "|";
                                        $setLink .= $linkValue;
                                        break;
                                    case "date" :    
                                        if ($setLink) $setLink .= "|";
                                        $setLink .= $linkValue;
                                        break;
                                    default :
                                       echo ("unkown $linkKey = $linkValue <br>");
                                       
                                }
                            }
                            // echo ("New LinkStr = '$setLink'<br>");
                            $saveData[link] = $setLink;
                        }
                        break;
                    case "linkArticle" :
                        // echo ("<h2>Setze ArtikelId to $value</h2>");
                        $res = $this->linkArticle("set",$value,$specialPostList[link],$dateId);
                        // echo ("<h2>Setze ArtikelId to $value -> $res</h2>");
                        if (is_string($res)) {
                            if ($value) cms_infoBox("Artikel verknüpft");
                            else cms_infoBox("Artikel Verknüpfung gelöscht");
                            $saveData[link] = $res;
                            if ($goPage == $pageInfo[page]) $goPage.= "?";
                            else $goPage .= "&";
                            $goPage .= "view=edit&id=$dateId";
                            $reloadPage = 1;                            
                        } else {
                            echo ("Result of linkArtikle = $res<br>");
                        }
                        break;



                    default :
                        echo "<h1>unkown SpecialResult #$key = '$value' in cms_admin_dates</h1>";
                        $reloadPage = 0;

                }
                

            }
            // echo ("LINK = $saveData[link] <br>");
        }
        
        
        
        $categoryId = $saveData[category];
        if ($categoryId) {
            $categoryData = cmsCategory_get(array("id"=>$categoryId));
            if (is_array($categoryData)) {
                $categoryName = $categoryData[name];
                $saveData[category] = $categoryId;
                $editShow[category][disabled] = 0;
                $editShow[category][showData][showName] = $categoryName;
            }
        }


        $regionId = $saveData[region];
        if ($regionId) {
            $regionData = cmsCategory_get(array("id"=>$regionId));
            if (is_array($regionData)) {
                $regionName = $regionData[name];
                $saveData[region] = $regionId;
                $editShow[region][disabled] = 0;
                $editShow[region][showData][showName] = $regionName;
            }
        }


        $locationId = $saveData[location];
        if ($locationId) {
            $locationData = cmsLocation_get(array("id"=>$locationId));
            if (is_array($locationData)) {
                
                $locationName = $locationData[name];
                $editShow[location][showData][showName] = $locationName;

               

                $url = $locationData[url];
                if ($url) {
                    $saveData[data][url] = $url;
                    $editShow[data][showData][url][disabled] = 1;
                }

                $ticketUrl = $locationData[ticketUrl];
                if ($ticketUrl) {
                    $saveData[data][ticketUrl] = $ticketUrl;
                    $editShow[data][showData][ticketUrl][disabled] = 1;
                }

                $regionId = $locationData[region];
                if ($regionId) {
                    $regionData = cmsCategory_get(array("id"=>$regionId),"");
                    if (is_array($regionData)) {
                        $regionName = $regionData[name];                        
                        $saveData[region] = $regionId;
                        $editShow[region][disabled] = 1;
                        $editShow[region][showData][showName] = $regionName;
                    }

                }
                $street = $locationData[street];
                $streetNr = $locationData[streetNr];
                $plz = $locationData[plz];
                $city = $locationData[city];
                // set Data
                $saveData[data][street] = $street;
                $saveData[data][streetNr] = $streetNr;
                $editShow[data][showData][street][disabled] = 1;
                $editShow[data][showData][streetNr][disabled] = 1;
                
                $saveData[data][plz] = $plz;
                $saveData[data][city] = $city;
                $editShow[data][showData][plz][disabled] = 1;
                $editShow[data][showData][city][disabled] = 1;

                // echo ("ShowName = $locationName <br>");
            }
        }

       // show_array($saveData);
        global $cmsName,$cmsVersion;
        if ($_POST[cancelSave]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Ternin angelegen abgebrochen";
            else $outPut = "Ternin speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
            $doSave = 0;
        }
        
        if ($_POST[editSave] OR $doSave) {
            if ($saveData) {
                // echo ("KETZT SPEICHERN LINK = $saveData[link] <br>");
                //$reloadPage = 0;
                $error = $this->checkError($saveData,$editShow);
                if (count($error)>0) {
                    $errorStr = "";
                    foreach ($error as $key => $value) {
                        if ($errorStr != "") $errorStr .= "<br>";
                        $errorStr .= $value;
                    }
                    cms_errorBox($errorStr);
                }
                if (count($error) == 0 AND is_array($saveData)) {
                    // Check Data
                    $saveData = $this->checkData($saveData);
                    if ($saveData[dontReload]) {
                        unset($saveData[dontReload]);
                        $reloadPage = $saveData[dontReload];
                    }
                    
                    if ($saveData[mainId]) {
                        unset($saveData[mainId]);                        
                    }
                    
                    // if ($_SESSION[userLevel] >= 9) $reloadPage = 0;
                    // show_array($saveData);

                    // GET QUERY AND SAVEDATAID FORM saveData
                    $dontAddQuery = 0;
                    if (intval($saveData[location])) {
                        $dontAddQuery = "location";
                    }
                                      
                    $queryData = $this->query_queryData($saveData,$editShow,$dontAddQuery);
                    $query = $queryData[query];
                    $saveDataId = $queryData[saveDataId];
                    // if ($mode == "new" AND $saveDataId )
                    // echo ("Save $query <br>");
                    // $reloadPage = 0;
                    if ($mode == "new") {
                        $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_dates` SET $query  ";
                    }
                    if ($mode == "edit") {
                        $query = "UPDATE `".$GLOBALS[cmsName]."_cms_dates` SET $query WHERE `id` = $saveDataId ";
                    }
                    $result = mysql_query($query);
                  
                    //echo ("$query<br>");
                    if ($result) {
                        // $goPage = $pageInfo[page];
                        if ($mode == "new") {
                            cms_infoBox("Ternin angelegt");
                            $saveDataId = mysql_insert_id();
                            $goPage = php_addUrl($goPage,"?view=edit&id=$saveDataId");
                            $respop = array_pop($_SESSION[lastPages]);
                            reloadPage($goPage,1);
                            return 0;
                        }
                        cms_infoBox("Ternin gespeichert");  
                        $this->cacheRefresh($saveDataId,$saveData,$mainId);
                        if ($_SESSION[userLevel] >= 9) $reloadPage = 0;
//                        if ($_SESSION[userLevel] >= 9) echo ($query);
                        if ($reloadPage) {
                            if ($lastUrl) {
                                $goPage = $lastUrl;
                                $goPage .= "&dontCache=1";
                            }
                            reloadPage($goPage,1);
                        }
                    } else {
                        if ($mode == "new") $outPut = "Fehler bei Ternin angelegen";
                        else $outPut = "Fehler bei Ternin gespeichern";
                        if ($_SESSION[showLevel]==9) $outPut .= "<br>Query = '$query'";
                        cms_errorBox($outPut);
                    }
                    
                    if ($mainId) $saveData[mainId] = $mainId; 
                    
                }
            }
        }
        
        if ($linkedDate) {
            echo ("<h1>Linked Date</h1>");
            if ($_SESSION[userLevel] >= 9) {
                // show_array($editShow[link],1);
                foreach ($saveData as $key => $value) {
                    // echo ("$key = $value <br>");
                }
            }
            // $editShow[mainId][show] = 1;
            $editShow[category][show] = 0;
            $editShow[location][show] = 0;
            $editShow[locationStr][show] = 0;
            $editShow[region][show] = 0;
            $editShow[name][show] = 0;
            $editShow[info][show] = 0;
            
            $editShow[image][show] = 0;
            $editShow[data][show] = 0;
            $editShow[link][show] = 1;
            $editShow[link][showData][date][name] = "Haupttermin";
            $editShow[link][showData][date][tip] = "Dies ist ein Verknüpfter Termin";
            $editShow[link][showData][article][show] = 0;
            // show_array($editShow[link],1);
        }

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;
        $divData = array();
        $divData[urlLocation] = "/cms_$cmsVersion/getData/locationData.php?cmsVersion=$cmsVersion&cmsName=$cmsName";
        $divData[urlRegion] = "/cms_$cmsVersion/getData/categoryData.php?cmsVersion=$cmsVersion&cmsName=$cmsName&mainCat=180";
        $divData[urlCategory] = "/cms_$cmsVersion/getData/categoryData.php?cmsVersion=$cmsVersion&cmsName=$cmsName&mainCat=1";
        $divData[urlDates] = "/cms_$cmsVersion/getData/datesData.php?cmsVersion=$cmsVersion&cmsName=$cmsName";
        div_start("adminDatesFrame",$divData);

        echo ("<form method='post' enctype='multipart/form-data' >");
        
        
        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);

        echo ("</form>");
        div_end("adminDatesFrame");
    }





   

}

 function cmsDates_getLine($dates,$frameWidth) {
    $showList = array();
    $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40);
    $showList["date"] = array("name"=>"Datum","width"=>80);
    $showList["category"] = array("name"=>"Rubrik","width"=>180,"align"=>"left");
    $showList["location"] = array("name"=>"Location","width"=>180);
    $showList["name"] = array("name"=>"Titel","width"=>150,"align"=>"left");
    $showList["time"] = array("name"=>"Uhrzeit","width"=>80);
    $showList["show"] = array("name"=>"zeigen","width"=>50,"align"=>"center");

    if (!is_array($dates) ) { // showTitleLine
        $str = "";
        $divName = "cmsDatesTitleLine";
        $divData = array();
        //$height = $showList["image"]["height"];
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        //if ($height) $divData[style] .= "height:".$height."px;";
        $str.= div_start_str($divName,$divData);
        foreach ($showList as $key => $value) {
            $width = $value[width];
            $height = $value[height];
            $name = $value[name];
            $align = $value[align];
            if (!$name) $name = "key=$name";
            $style = "float:left;width:".$width."px;";
            if ($align) $style .= "text-align:$align;";
            $str.= div_start_str("cmsDatesTitleLine_$key",$style);
            $str.= $name;
            $str.= div_end_str("cmsDatesTitleLine_$key");
        }
        $str.= div_end_str($divName,"before");
        return $str;
    }

    $str = "";

    $str.= "<a href='?view=edit&id=$dates[id]'>";
    $divName = "cmsDatesLine";
    $divData = array();
    $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
    $height = $showList["image"]["height"];
    if ($height) $divData[style] .= "height:".$height."px;line-height:".$height."px;";
    $divData[id] = $dates[id];
    $str.= div_start_str($divName,$divData);

    foreach ($showList as $key => $value) {
        $width = $value[width];
        $height = $value[height];
        $name = $value[name];

        $cont = "key=$key";
        switch ($key) {
            case "image" :
                $imageId = intval($dates[image]);
                $cont = "kein Bild";
                if ($imageId > 0) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if (is_array($imageData)) {
                        // $cont = cmsImage_showImage($imageData,$width,array("frameHeight"=>$height,"frameWidth"=>$width,"vAlign"=>"middle","hAlign"=>"center"));
                    }
                }
                break;
            case "category" :
                $res = cmsCategory_get(array("id"=>$dates[$key]));
                if (is_array($res)) $cont = $res[name];
                else $cont = "keine Rubrik";
                break;

              case "location" :
                $res = cmsLocation_get(array("id"=>$dates[$key]));
                if (is_array($res)) $cont = $res[name];
                else {
                    if ($dates[locationStr]) $cont = "'".$dates[locationStr]."'";
                    else $cont = "keine Ort";
                }
                break;

            case "name" :
                $cont = $dates[$key];
                if (!$cont) $cont = "kein Name";
                break;

            case "show" :
                if ($dates[$key]) $cont = "1";
                else $cont = 0;
                break;

             case "date" :
                 $datum = $dates[$key];
                 if ($datum[4]=="-") {
                     $cont = substr($datum,8,2).".".substr($datum,5,2).".".substr($datum,0,4);
                     $cont = substr($datum,8,2).".".substr($datum,5,2).".".substr($datum,2,2);
                 } else {
                    $cont = $datum;
                 }
                break;


            case "time" :
                if (strlen($dates[$key])==8) $cont= substr($dates[$key],0,5);
                else $cont = $dates[$key];
                break;

            default :
                if ($dates[$key]) $cont = $dates[$key];


        }

        $align = $value[align];

        $style = "float:left;width:".$width."px;";
        if ($align) $style .= "text-align:$align;";

        $str.= div_start_str("cmsDatesLine_$key",$style);
        $str.= $cont;
        $str.= div_end_str("cmsDatesLine_$key");
    }
    $str.= div_end_str($divName,"before");
    $str.= "</a>";
    return $str;
}



function cms_admin_datesClass($ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_dates_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_dates();

    } else {
        $class = new cmsAdmin_dates_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    return $class;
}

function cms_admin_dates($frameWidth,$ownAdminPath=""){
    $class = cms_admin_datesClass($ownAdminPath);
    $class->show($frameWidth);
}










?>
