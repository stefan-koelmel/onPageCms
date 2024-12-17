<?php // charset:UTF-8

class cmsAdmin_location_base extends cmsAdmin_editClass_base {

    function admin_dataSource() {
        return "location";;
    }
    
    function admin_listSettings() {
        $res = $this->location_showList();
        return $res;
    }
    
    function show($frameWidth) {        
        if (!function_exists("cmsLocation_get")) {
            echo ("Orte sind deaktiviert !<br>");
            return 0;
        }
        $view = $_GET[view];
        $this->tableName = $this->admin_dataSource();
        switch ($view) {
             case "editShow" :
                $this->edit_editShow();
                break;
            
            case "editList" :
                $this->edit_editList();
                break;
            
            case "new" :
                $this->show_edit(0,$frameWidth);
                break;

             case "edit" :
                $locationId = $_GET[id];
                $this->show_edit($locationId,$frameWidth);
                break;

            case "list" :
                $this->show_list($frameWidth);
                break;
            
           
            
            default :
                $this->show_list($frameWidth);
        }
    }

    function show_list() {
        $frameWidth = $this->frameWidth;
        global $pageInfo;


       //  echo ("<h1>Orte</h1>");

        $sort = "name";
        

        $_data = $this->admin_showFilter($frameWidth);
        // show_array($_data);
        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;
        $filter = $this->emptyListFilter();

        foreach ($_data as $key => $value) {
            switch ($key) {
                case "page" : break;
                case "sort" : $sort = $value; break;
                case "category" : if ($value) $filter[$key] = $value; break;
                case "filter_category" : if ($value) $filter["category"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_region"   : if ($value) $filter["region"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_location"   :
                    if (intval($value)) $filter["id"] = $value;
                    else {
                        $filter["name"] = "%".$value."%";
                        //echo ("FilterLocation = $value <br />");
                    }
                    break;

                case "filter_dateRange"   : if ($value) $filter["dateRange"] = $value; break;
                case "dateRange"   : if ($value) $filter[$key] = $value; break;

                case "filter_specialView"   :
                    $specialFilterList = $this->admin_get_specialFilterList_own();
                    if (is_array($specialFilterList[$value])) {
                        $specialFilter = $specialFilterList[$value];
                        // show_array($specialFilter);

                        // APPEND Filter to Filter
                        if (is_array($specialFilter[filter])) {
                            foreach($specialFilter[filter] as $key => $value ) {
                                $filter[$key] = $value;
                                // echo "append $key = $value => $filter[$key] to filter <br />";
                            }
                        }
                        if ($specialFilter[sort]) {
                            if (is_string($specialFilter[sort]))$sort = $specialFilter[sort];
                        }

                    } else {
                        echo ("Filter SpecialView $key = $value <br />");
                    }
                    break;
                    //if ($value) $filter["region"] = $value; break;
                default :
                    echo ("Unkown $key in get/post_data = '$value' <br />");

            }
        }

       //  $this->admin_show_filterSort($filter,$sort);
        // show_array($filter);
        $locationList = cmsLocation_getList($filter,$sort,"query");

        $this->checkList($locationList,"");

        $this->admin_show_list($locationList);
        return 1;
        
        $showList = $this->location_showList();

        $showData = array();
        $showData[pageing] = array();
        $showData[pageing][count] = 20;
        $showData[pageing][showTop] = 1;
        $showData[pageing][showBottom] = 1;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 1; // small | all

        $this->showList_List($locationList,$showList,$showData,$frameWidth);

        echo ("&nbsp;<br />");
        echo ("<a href='admin_cmsLocation.php?view=new' class='cmsLinkButton' >neue neuen Ort anlegen</a>");
    }


    function location_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>300);
        $showList["category"] = array("name"=>"Kategorie","width"=>180);
        $showList["region"] = array("name"=>"Region","width"=>150);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }

    function admin_get_specialFilterList_own() {
        $specialList = array();
        $specialList[hidden] = array("id"=>"hidden","name"=>"Unsichtbare Orte");
        $specialList[hidden][filter] = array("show"=>0);
        $specialList[hidden][sort] = "name";

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
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Kategorie wählen");
        $filterList[category]["filter"] = array("mainCat"=>8,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Region wählen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        return $filterList;
    }

    

    function show_edit() {
        $locationId = $_GET[id];
        $frameWidth = $this->frameWidth;
        if ($locationId > 0) {
            $mode = "edit";
            $headLine = "Ort bearbeiten";
            $saveData = cmsLocation_getById($locationId);
             if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
                $doSave = 1;
            }
            // if ($_POST[saveData]) $saveData = $_POST[saveData];

        } else {
            $mode = "new";
            $headLine = "Ort anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) {
                $saveData = $_POST[saveData];
                $doSave = 1;
            }
        }
        echo ("<h1>$headLine</h1>");

        $lastUrl = $this->backLink();
        // echo ("Last Url $lastUrl <br>");
        
        
        global $pageInfo;
        $specialData = array();
        $specialData[saveData] = $saveData;

        $tableName = "location";
        $editShow = $this->edit_show($tableName,$specialData);

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
                switch ($key) {
                    case "category" : $saveData[$key] = $value; break;
                    case "locationId" : $saveData[location] = $value; break;
                    case "locationName" : $locationName = $value; break;
                    case "fromDate" : $saveData[fromDate] = $value; break;
                    case "toDate" : $saveData[toDate] = $value; break;

                   case "imageListStr" :
                        $saveData[image] = $value;
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
                            //echo ("Set newImage to $imageStr was ($oldImage)<br />");
                            $saveData[image] = $imageStr;
                            $reloadPage = 1;
                            cms_infoBox("Bild hochgeladen ");
                        } else {

                            $saveData[image] = $value;
                            $reloadPage = 0;
                            cms_infoBox("Bild hochgeladen ");
                        }
                        break;

                    default :
                        echo "<h1>unkown SpecialResult #$key = '$value' </h1>";

                }

            }
        }

       // show_array($saveData);
         if ($_POST[cancelSave]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Ort angelegen abgebrochen";
            else $outPut = "Ort speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
            $doSave = 0;
        }

        global $cmsName,$cmsVersion;
        if ($_POST[editSave]) {
            // CheckData and Convert
            $saveData = $this->checkData($saveData);

            // Check Error
            $error = $this->checkError($saveData,$editShow);

            if (count($error)>0) {
                $errorStr = "";
                foreach ($error as $key => $value) {
                    if ($errorStr != "") $errorStr .= "<br />";
                    $errorStr .= $value;
                }
                cms_errorBox($errorStr);
            }
            
            if (count($error) == 0 AND is_array($saveData)) {

                
                // if ($_SESSION[showLevel] == 9) $reloadPage = 0;
                // show_array($saveData);
               // echo ("<h1>editShow</h1>");
                //show_array($editShow);
                 // GET QUERY AND SAVEDATAID FORM saveData
                $queryData = $this->query_queryData($saveData,$editShow);
                $query = $queryData[query];
                $saveDataId = $queryData[saveDataId];

                if ($mode == "new") {
                    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_location` SET $query  ";
                }
                if ($mode == "edit") {
                    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_location` SET $query WHERE `id` = $saveDataId ";
                }
                $result = mysql_query($query);
                if ($result) {
                    // $goPage = $pageInfo[page];
                    if ($mode == "new") cms_infoBox("Ort angelegt");
                    else {
                        cms_infoBox("Ort gespeichert");
                        $this->cacheRefresh($saveDataId,$saveDate,"save");
                        // $reloadPage = 0;
                    }
                    if ($lastUrl) $goPage = $lastUrl;
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Ort angelegen";
                    else $outPut = "Fehler bei Ort gespeichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br />Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
       


        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;


        echo ("<form method='post' enctype='multipart/form-data' >");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
   
        echo ("</form>");
    }

    function checkData($saveData) {
        return $saveData;
    }

}



function cms_admin_location($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_location_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_location();

    } else {
        $class = new cmsAdmin_location_base();
        // echo ("File $ownPhpFile not found <br />");
    }
    $class->show($frameWidth);
}



?>

