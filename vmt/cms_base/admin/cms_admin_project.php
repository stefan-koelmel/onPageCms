<?php // charset:UTF-8

class cmsAdmin_project_base extends cmsAdmin_editClass_base {

    function show($frameWidth){
        $view = $_GET[view];
        $projectId = $_GET[id];
        if ($projectId) $view="edit";

        switch ($view) {
            case "new" :
                $this->projectEdit(0,$frameWidth);
                break;

             case "edit" :
                $projectId = $_GET[id];
                $this->projectEdit($projectId,$frameWidth);
                break;

            case "list" :
                $this->projectList($frameWidth);
                break;
            default :
                $this->projectlist($frameWidth);

        }
    }



    function projectList($frameWidth) {
        $sort = "name";
        
        echo ("<h1>Projekte</h1>");

        $filter = $this->emptyListFilter();
        $sort = "id";


        $_data = $this->admin_showFilter($frameWidth);
      

        // $_data = array();

        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;

        foreach ($_data as $key => $value) {
            switch ($key) {
                case "sort" : $sort = $value; break;
                case "page" : break;
                case "category" : if ($value) $filter[$key] = intval($value); break;
                case "filter_category" : if ($value) $filter["category"] = intval($value); break;
                case "region"   : if ($value) $filter[$key] = intval($value); break;
                case "filter_region"   : if ($value) $filter["region"] = intval($value); break;
                case "region"   : if ($value) $filter[$key] = intval($value); break;

                case "filter_dateRange"   : if ($value) $filter["dateRange"] = $value; break;
                case "dateRange"   : if ($value) $filter[$key] = $value; break;

                case "filter_location" :

                    $locationData = cmsLocation_get(array("name"=>$value,"show"=>1));
                    if (is_array($locationData)) {
                        $locationId = $locationData[id];
                        // echo ("Ort = $value / $locationId<br>");
                        $filter[location] = $locationId;
                    }
                    break;
                case "filter_content" :
                    if ($value) $filter["content"] = $value; break;

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
                            if (is_string($specialFilter[sort]))$sort = $specialFilter[sort];
                        }
                        
                    } else {
                        echo ("Filter SpecialView $key = $value <br>");
                    }
                    break;
                    //if ($value) $filter["region"] = $value; break;
                default :
                    echo ("Unkown $key in get/post_data = $value projects<br>");

            }
        }

        $out = "out";
        $projectList = cmsProject_getList($filter,$sort,$out);
        //  echo ("ArticleList $projectsList <br>");
        // show_array($projectsList);
        // $this->checkList($projectsList,$_data[filter_specialView]);
        $this->checkList($projectsList,"linkedDates");
        
        $newLink = "";
        foreach ($_GET as $key => $value) {
            if ($value) $newLink.= "&$key=$value";            
        }
        $newLink .= "&view=new";
        $newLink = php_addUrl($pageInfo[page],$newLink);
        
        
        // Insert Button Before
        echo ("<a href='$newLink' class='cmsLinkButton' >neues Projekt anlegen</a><br>");
        
        $this->admin_show_filterSort($filter,$sort);
        $showList = $this->project_showList();
       
        $showData = array();
        $showData[pageing] = array();
        $showData[pageing][count] = 20;
        $showData[pageing][showTop] = 1;
        $showData[pageing][showBottom] = 1;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 1; // small | all

        $this->showList_List($projectList,$showList,$showData,$frameWidth);

        echo ("<a href='$newLink' class='cmsLinkButton' >neues Projekt anlegen</a>");
    }


    


    function project_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Projektbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);

        $showList["category"] = array("name"=>"Kategorie","width"=>160,"align"=>"left");
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }


    function admin_get_specialFilterList_own() {
        $specialList = array();
        $specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Projekt");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "id";
//
//        $specialList[active] = array("id"=>"active","name"=>"Aktive Projekt");
//        $specialList[active][filter] = array("show"=>1,"fromDate"=>"<='".date("Y-m-d")."'","toDate"=>">='".date("Y-m-d")."'");
//        $specialList[active][sort] = "fromDate";
//
//        $specialList[comeing] = array("id"=>"coming","name"=>"Kommende Projekt");
//        $specialList[comeing][filter] = array("show"=>1,"fromDate"=>">='".date("Y-m-d")."'");
//        $specialList[comeing][sort] = "fromDate";
//
//        $specialList[gone] = array("id"=>"gone","name"=>"Vergangen Projekt");
//        $specialList[gone][filter] = array("show"=>1,"toDate"=>"<='".date("Y-m-d")."'");
//        $specialList[gone][sort] = "toDate";
        return $specialList;
    }


    function admin_get_filterList_own() {
        $filterList = array();

//        $filterList[specialView]   = array();
//        $filterList[specialView]["name"] = "Spezielle Ansichten";
//        $filterList[specialView]["type"] = "specialView";
//        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
//        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
//        // $filterList[specialView]["sort"] = "name";
//        $filterList[specialView]["dataName"] = "specialView";
//        $filterList[specialView][customFilter] = 1;
//
//
//        $filterList[produkt] = 0;
//        $filterList[category] = array();
//        $filterList[category]["name"] = "Kategorie";
//        $filterList[category]["type"] = "category";
//        $filterList[category]["dataName"] = "category";
//        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Alle Kategorien zeigen");
//        $filterList[category]["filter"] = array("mainCat"=>144,"show"=>1);
//        $filterList[category]["sort"] = "name";
//        $filterList[category][customFilter] = 1;
//
//        $filterList[region]   = array();
//        $filterList[region]["name"] = "Region";
//        $filterList[region]["type"] = "category";
//        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Alle Region zeigen");
//        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
//        $filterList[region]["sort"] = "name";
//        $filterList[region]["dataName"] = "region";
//        $filterList[region][customFilter] = 1;




        return $filterList;
    }

    
    

    function projectEdit($projectId,$frameWidth) {
//        $saveData = $this->emptyData();
//        if ($_POST[saveData]) {
//            $saveData = php_clearPost($_POST[saveData]);
//            if (is_array($saveData)) {
//                if ($saveData[id]) $projectId = $saveData[id];
//            }
//        }
        
        if ($projectId > 0) {
            $mode = "edit";
            $headLine = "Projekt bearbeiten";
            $saveData = cmsProject_getById($projectId);

            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }
        } else {
            $mode = "new";
            $headLine = "Projekt anlegen";
            $saveData = $this->emptyData();
            if ($_POST[saveData]) {
                foreach ($_POST[saveData] as $key => $value) $saveData[$key] = $value;
                $saveDataId = $saveData[id];
                if ($saveDataId) {
                    $mode = "edit";
                    $dateId = $saveDataId;
                }
            }
        }


        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        

        $reloadPage = 1;
        $tableName = "project";

        global $pageInfo;
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


        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST,$tableName);
            // show_array($specialPostList);
            foreach ($specialPostList as $key => $value ) {
                switch ($key) {
                    case "category" : $saveData[$key] = $value; break;
                    case "locationId" : $saveData[location] = $value; break;
                    case "locationName" : $locationName = $value; break;
                    case "fromDate" : $saveData[fromDate] = $value; break;
                    case "toDate" : $saveData[toDate] = $value; break;

                    case "link" :
                        // echo ("<h1> LINK $value </h1>");
                        if (is_array($value)) {
                            $setLink = "";
                        
                            foreach ($value as $linkKey => $linkValue) {
                                // echo ("LINK $linkKey '$linkValue' <br>");
                                switch ($linkKey) {
                                    case "article" : 
                                        if ($setLink) $setLink .= "|";
                                        $setLink .= $linkValue;
                                        break;
                                    case "date" :  
                                        if ($linkValue) {
                                            if ($setLink) $setLink .= "|";
                                            $setLink .= $linkValue;
                                        }
                                        break;
                                    default :
                                       echo ("unkown $linkKey = $linkValue <br>");
                                       
                                }
                            }
                            // echo ("New LinkStr = '$setLink'<br>");
                            $saveData[link] = $setLink;
                        }
                        break;
                        
                        foreach ($value as $linkType => $linkValue) {
                            
                        }
                        show_array($value);
                        break;
                    case "linkDate" :
                        // echo ("<h2>Setze DateId to $value</h2>");
                        $res = $this->linkDate("set",$value,$specialPostList[link],$projectId);
                        if (is_string($res)) {
                            if ($value) cms_infoBox("Termin verknüpft");
                            else cms_infoBox("Termin Verknüpfung gelöscht");
                            $saveData[link] = $res;
                            if ($goPage == $pageInfo[page]) $goPage.= "?";
                            else $goPage .= "&";
                            $goPage .= "view=edit&id=$projectId";
                            $reloadPage = 1;
                            $doSave = 1;
                        } else {
                            echo ("Result of linkArtikle = $res<br>");
                        }
                        break;


                    case "imageListStr" :
                        $saveData[image] = $value;
                        $reloadPage = 0;
                        break;

                    case "imageId" :
                        if (intval($value)) {
                            $oldImage = $saveData[image];
                            if ($oldImage) {
                                if (intval($oldImage)) $imageStr = "|".$oldImage."|";
                                else {
                                    $imageStr = $oldImage;
                                }
                                $imageStr .= $value."|";
                                //echo ("Set newImage to $imageStr was ($oldImage)<br>");
                                $saveData[image] = $imageStr;
                                $reloadPage = 0;
                                cms_infoBox("Bild hochgeladen ");
                            } else {
                                
                                $saveData[image] = $value;
                                $reloadPage = 0;
                                cms_infoBox("Bild hochgeladen ");   
                            }                        
                        } else {
                            cms_infoBox("Fehler beim Bildhochladen<br>$value");
                            $reloadPage = 0;
                        }
                        break;

                    default :
                        echo "<h1>unkown SpecialResult #$key = '$value' </h1>";

                }

            }
        }
        $specialData = array();


        
        if (intval($saveData[location])>0) {
            $locationId = $saveData[location];
            $locationData = cmsLocation_get(array("id"=>$locationId));            
            if (is_array($locationData)) {
                $specialData[locationName] = $locationData[name];
            }
            else $specialData[locationName] = "";
        }
       
        if ($saveData[category]) $specialData[category] = $saveData[category];
        else $specialData[category] = "-";
        if ($saveData[id]) $specialData[id] = $saveData[id];
        
        
        
        $editShow = $this->edit_show($tableName,$specialData);

  
        if ($saveData[dateRange]) {
            //echo ("<h1>$saveData[dateRange]</h1>");
            if (is_array($editShow[image])) $editShow[image][imageFolder].= $saveData[dateRange]."/";
            // show_array($editShow[image]);
        }
        
        if ($_POST[duplicate]) {
            // echo ("Duplicate $doSave <br>");
            $doSave = 1;
            $mode = "duplicate";
        }

        // SAVE
        global $cmsName,$cmsVersion;
        if ($_POST[editSave] OR $doSave) {

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
                
                // GET QUERY AND SAVEDATAID FORM saveData
                $queryData = $this->query_queryData($saveData,$editShow);
                $query = $queryData[query];
                $saveDataId = $queryData[saveDataId];
               
                switch ($mode) {
                    case "new" :
                        $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query  ";
                        break;
                    case "edit" :
                        $query = "UPDATE `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query WHERE `id` = $saveDataId ";
                        break;
                    case "duplicate" :
                        $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query  "; 
                        break;
                }
                
                //echo ("Query = $query<br>");
                $result = mysql_query($query);
                if ($result) {
                    // $goPage = $pageInfo[page];
                    switch ($mode) {
                        case "new"       : 
                            cms_infoBox("Projekt angelegt");
                            $articleId = mysql_insert_id();
                            $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
                            $respop = array_pop($_SESSION[lastPages]);
                            reloadPage($goPage,0);
                            return 0;
                            break;
                           
                        case "edit"      : 
                            cms_infoBox("Projekt gespeichert");
                            $this->cacheRefresh($saveDataId,$saveData,"save");
                            // if ($_SESSION[showLevel]==9) $reloadPage = 0;
                            if ($lastUrl) $goPage = $lastUrl;
                            if ($reloadPage) reloadPage($goPage,1);
                            break;
                        case "duplicate" : 
                            cms_infoBox("Projekt dupliziert");
                            $articleId = mysql_insert_id();
                            $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
                            $respop = array_pop($_SESSION[lastPages]);
                            reloadPage($goPage,0);
                            return 0;
                            break;
                    }
                            
                    
//                    if ($mode == "new") {
//                        cms_infoBox("Projekt angelegt");
//                        $articleId = mysql_insert_id();
//                        $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
//                        $respop = array_pop($_SESSION[lastPages]);
//                        reloadPage($goPage,0);
//                        return 0;
//                    }
//                    cms_infoBox("Projekt gespeichert");
//                    $this->cacheRefresh($saveDataId,$saveData,"save");
//                    // if ($_SESSION[showLevel]==9) $reloadPage = 0;
//                    if ($lastUrl) $goPage = $lastUrl;
//                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    switch ($mode) {
                        case "new"       : $outPut = "Fehler bei Projekt angelegen"; break;
                        case "edit"      : $outPut = "Fehler bei Projekt speichern"; break;
                        case "duplicate" : $outPut = "Fehler bei Projekt duplizieren"; break;                            
                    }
                    if ($_SESSION[showLevel]==9) $outPut .= "<br>Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Projekt angelegen abgebrochen";
            else $outPut = "Projekt speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
        }

        // Löschen
        if ($_POST[deleteData]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Projekt angelegen abgebrochen";
            else $outPut = "Projekt speichern abgebrochen";

            $deleteId = $saveData[id];
            if ($deleteId) {
                echo ("Wollen Sie diesen Projekt wirklich löschen?<br>");
                echo ("<a href='$goPage?del=$deleteId' > JA </a> ");
                echo ("<a href='$goPage?view=edit&id=$deleteId' > NEIN </a> ");
            }

            // cms_infoBox($outPut);
           //  reloadPage($goPage,1);
        }


       

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;

        global $cmsName,$cmsVersion;

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $divData = array();
        $divData[urlLocation] = "/cms_$cmsVersion/getData/locationData.php?cmsVersion=$cmsVersion&cmsName=$cmsName";
        $divData[urlRegion] = "/cms_$cmsVersion/getData/categoryData.php?cmsVersion=$cmsVersion&cmsName=$cmsName&mainCat=180";
        $divData[urlCategory] = "/cms_$cmsVersion/getData/categoryData.php?cmsVersion=$cmsVersion&cmsName=$cmsName&mainCat=1";
        $divData[urlDates] = "/cms_$cmsVersion/getData/datesData.php?cmsVersion=$cmsVersion&cmsName=$cmsName";
        div_start("adminProjectsFrame",$divData);



        $goPageList = array();
        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "edit" : break;
                case "id" : break;

                default :
                    $goPageList[$key]=$value;
            }
        }
        foreach ($goPageList as $key => $value) {
            if ($goPage == "") $goPage.= "?";
            else $goPage .= "&";
            $goPage .= "$key=$value";
        }
        // show_array($saveData);
        echo ("<form method='post' enctype='multipart/form-data' >");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
        

        echo ("</form>");
        div_end("adminProjectsFrame");
    }

 
}

function cms_admin_project($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_project_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_project();

    } else {
        $class = new cmsAdmin_project_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);
}



?>
