<?php // charset:UTF-8

class cmsAdmin_articles_base extends cmsAdmin_editClass_base {

    function show($frameWidth){
        $view = $_GET[view];
        $articlesId = $_GET[id];
        if ($articlesId) $view="edit";

        switch ($view) {
            case "new" :
                $this->articlesEdit(0,$frameWidth);
                break;

             case "edit" :
                $articlesId = $_GET[id];
                $this->articlesEdit($articlesId,$frameWidth);
                break;

            case "list" :
                $this->articlesList($frameWidth);
                break;
            default :
                $this->articleslist($frameWidth);

        }
    }



    function articlesList($frameWidth) {
        $sort = "name";
        
        echo ("<h1>Artikel</h1>");

        $filter = $this->emptyListFilter();
        $sort = "fromDate";


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
                    echo ("Unkown $key in get/post_data = $value articles<br>");

            }
        }

        $out = "query";
        $articlesList = cmsArticles_getList($filter,$sort,$out);
        //  echo ("ArticleList $articlesList <br>");
        // show_array($articlesList);
        // $this->checkList($articlesList,$_data[filter_specialView]);
        $this->checkList($articlesList,"linkedDates");
        
        $newLink = "";
        foreach ($_GET as $key => $value) {
            if ($value) $newLink.= "&$key=$value";            
        }
        $newLink .= "&view=new";
        $newLink = php_addUrl($pageInfo[page],$newLink);
        
        
        // Insert Button Before
        echo ("<a href='$newLink' class='cmsLinkButton' >neues Artikel anlegen</a><br>");
        
        $this->admin_show_filterSort($filter,$sort);
        $showList = $this->location_showList();
       
        $showData = array();
        $showData[pageing] = array();
        $showData[pageing][count] = 20;
        $showData[pageing][showTop] = 1;
        $showData[pageing][showBottom] = 1;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 1; // small | all

        $this->showList_List($articlesList,$showList,$showData,$frameWidth);

        echo ("<a href='$newLink' class='cmsLinkButton' >neues Artikel anlegen</a>");
    }


    


    function location_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Artikelbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["fromDate"] = array("name"=>"Von","width"=>40);
        $showList["toDate"] = array("name"=>"Bis","width"=>40);
        $showList["name"] = array("name"=>"Name","width"=>200);

        $showList["category"] = array("name"=>"Rubrik","width"=>160,"align"=>"left");
        $showList["location"] = array("name"=>"Ort","width"=>160,"align"=>"left");
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }


    function admin_get_specialFilterList_own() {
        $specialList = array();
        $specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Artikel");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "fromDate";

        $specialList[active] = array("id"=>"active","name"=>"Aktive Artikel");
        $specialList[active][filter] = array("show"=>1,"fromDate"=>"<='".date("Y-m-d")."'","toDate"=>">='".date("Y-m-d")."'");
        $specialList[active][sort] = "fromDate";

        $specialList[comeing] = array("id"=>"coming","name"=>"Kommende Artikel");
        $specialList[comeing][filter] = array("show"=>1,"fromDate"=>">='".date("Y-m-d")."'");
        $specialList[comeing][sort] = "fromDate";

        $specialList[gone] = array("id"=>"gone","name"=>"Vergangen Artikel");
        $specialList[gone][filter] = array("show"=>1,"toDate"=>"<='".date("Y-m-d")."'");
        $specialList[gone][sort] = "toDate";
        return $specialList;
    }


    function admin_get_filterList_own() {
        $filterList = array();

        $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
        // $filterList[specialView]["sort"] = "name";
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;


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

   


    

    function articlesEdit($articlesId,$frameWidth) {
//        $saveData = $this->emptyData();
//        if ($_POST[saveData]) {
//            $saveData = php_clearPost($_POST[saveData]);
//            if (is_array($saveData)) {
//                if ($saveData[id]) $articlesId = $saveData[id];
//            }
//        }
        
        if ($articlesId > 0) {
            $mode = "edit";
            $headLine = "Artikel bearbeiten";
            $saveData = cmsArticles_getById($articlesId);

            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }
        } else {
            $mode = "new";
            $headLine = "Artikel anlegen";
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
        $tableName = "articles";

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
                        $res = $this->linkDate("set",$value,$specialPostList[link],$articlesId);
                        if (is_string($res)) {
                            if ($value) cms_infoBox("Termin verknüpft");
                            else cms_infoBox("Termin Verknüpfung gelöscht");
                            $saveData[link] = $res;
                            if ($goPage == $pageInfo[page]) $goPage.= "?";
                            else $goPage .= "&";
                            $goPage .= "view=edit&id=$articlesId";
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
                            cms_infoBox("Artikel angelegt");
                            $articleId = mysql_insert_id();
                            $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
                            $respop = array_pop($_SESSION[lastPages]);
                            reloadPage($goPage,0);
                            return 0;
                            break;
                           
                        case "edit"      : 
                            cms_infoBox("Artikel gespeichert");
                            $this->cacheRefresh($saveDataId,$saveData,"save");
                            // if ($_SESSION[showLevel]==9) $reloadPage = 0;
                            if ($lastUrl) $goPage = $lastUrl;
                            if ($reloadPage) reloadPage($goPage,1);
                            break;
                        case "duplicate" : 
                            cms_infoBox("Artikel dupliziert");
                            $articleId = mysql_insert_id();
                            $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
                            $respop = array_pop($_SESSION[lastPages]);
                            reloadPage($goPage,0);
                            return 0;
                            break;
                    }
                            
                    
//                    if ($mode == "new") {
//                        cms_infoBox("Artikel angelegt");
//                        $articleId = mysql_insert_id();
//                        $goPage = php_addUrl($goPage,"?view=edit&id=$articleId");
//                        $respop = array_pop($_SESSION[lastPages]);
//                        reloadPage($goPage,0);
//                        return 0;
//                    }
//                    cms_infoBox("Artikel gespeichert");
//                    $this->cacheRefresh($saveDataId,$saveData,"save");
//                    // if ($_SESSION[showLevel]==9) $reloadPage = 0;
//                    if ($lastUrl) $goPage = $lastUrl;
//                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    switch ($mode) {
                        case "new"       : $outPut = "Fehler bei Artikel angelegen"; break;
                        case "edit"      : $outPut = "Fehler bei Artikel speichern"; break;
                        case "duplicate" : $outPut = "Fehler bei Artikel duplizieren"; break;                            
                    }
                    if ($_SESSION[showLevel]==9) $outPut .= "<br>Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Artikel angelegen abgebrochen";
            else $outPut = "Artikel speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
        }

        // Löschen
        if ($_POST[deleteData]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Artikel angelegen abgebrochen";
            else $outPut = "Artikel speichern abgebrochen";

            $deleteId = $saveData[id];
            if ($deleteId) {
                echo ("Wollen Sie diesen Artikel wirklich löschen?<br>");
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
        div_start("adminArticlesFrame",$divData);



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
        

        // Zeige Buttons
//        $buttonList = $this->editButtons($saveData);
//        foreach($buttonList as $key => $value) {
//            $disabled = "";
//            switch ($value[type]) {
//                case "submit" :
//                    if ($value[disabled]) $disabled = "disabled=disabled";
//
//                    echo("<input type='submit' $disabled class='".$value["class"]."' name='$value[value]' value='$value[name]'>");
//                    break;
//                case "link" :
//                    echo ("<a href='$value[link]' class='".$value["class"]."' >$value[name]</a>");
//                    break;
//
//                default :
//                    echo ("unkown Type in $key = '$value[type] <br>");
//            }
//        }
        
        
//        if ($mode == "new") {
//            echo ("<input type='submit' class='cmsInputButton' name='editSave' value='Artikel anlegen'>");
//        } else {
//            echo ("<input type='submit' class='cmsInputButton' name='editSave' value='Artikel speichern'>");
//        }
//        echo ("<input type='submit' class='cmsInputButton cmsSecond' name='cancelSave' value='abbrechen'>");
//
//        $dataId = $saveData[id];
//        if ($dataId) {
//            echo ("<input type='submit' class='cmsInputButton cmsSecond' name='deleteData' value='löchen'>");
//        }



        echo ("</form>");
        div_end("adminArticlesFrame");
    }

 
}

function cms_admin_articles($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_articles_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_articles();

    } else {
        $class = new cmsAdmin_articles_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);
}



?>
