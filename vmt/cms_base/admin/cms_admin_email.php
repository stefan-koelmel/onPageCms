<?php // charset:UTF-8

class cmsAdmin_email_base extends cmsAdmin_editClass_base {

    function show($frameWidth) {
        $view = $_GET[view];
        switch ($view) {
            case "new" :
                $this->show_edit(0,$frameWidth);
                break;

             case "edit" :
                $emailId = $_GET[id];
                $this->show_edit($emailId,$frameWidth);
                break;

            case "list" :
                $this->show_list($frameWidth);
                break;
            default :
                $this->show_list($frameWidth);
        }
    }

    function show_list($frameWidth) {
        global $pageInfo;


        echo ("<h1>eMail Vorlagen</h1>");

        $sort = "name";
        $filter = array("show"=>1);

        $this->admin_showFilter($frameWidth);
        $_data = array();
        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;


        foreach ($_data as $key => $value) {
            switch ($key) {
                case "page" : break;
                case "sort" : $sort = $value; break;
                case "category" : if ($value) $filter[$key] = $value; break;
                case "filter_category" : if ($value) $filter["category"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_region"   : if ($value) $filter["region"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_email"   :
                    if (intval($value)) $filter["id"] = $value;
                    else {
                        $filter["name"] = "%".$value."%";
                        //echo ("FilterEmail = $value <br />");
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
                                echo "append $key = $value => $filter[$key] to filter <br />";
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

        $this->admin_show_filterSort($filter,$sort);
        $emailList = cmsEmail_getList($filter,$sort);

        $this->checkList($emailList,$_data[filter_specialView]);

      

        $showList = $this->email_showList();

        $showData = array();
        $showData[pageing] = array();
        $showData[pageing][count] = 20;
        $showData[pageing][showTop] = 1;
        $showData[pageing][showBottom] = 1;
        $showData[pageing][viewMode] = "small"; // small | all
        $showData[titleLine] = 1; // small | all

        $this->showList_List($emailList,$showList,$showData,$frameWidth);

        echo ("&nbsp;<br />");
        echo ("<a href='admin_cmsMail.php?view=new' class='cmsLinkButton' >neue neuen eMail anlegen</a>");
    }


    function email_showList() {
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
        return $specialList;
    }


    function admin_get_filterList_own() {
        $filterList = array();
        return $filterList;
    }

    

    function show_edit($emailId,$frameWidth) {
        if ($_POST[saveData][id]>0) $emailId = $_POST[saveData][id];
        if ($emailId > 0) {
            $mode = "edit";
            $headLine = "eMail bearbeiten";
            $saveData = cmsEmail_getById($emailId);
            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }

        } else {
            $mode = "new";
            $headLine = "eMail anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) $saveData = $_POST[saveData];
        }
        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        global $pageInfo;
        $specialData = array();

        $tableName = "email";
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
                    case "emailId" : $saveData[email] = $value; break;
                    case "emailName" : $emailName = $value; break;
                    case "fromDate" : $saveData[fromDate] = $value; break;
                    case "toDate" : $saveData[toDate] = $value; break;

                    case "imageListStr" :
                        // echo ("<h1> set ImageStr '$value'</h1>");
                        $saveData[image] = $value;
                        
                        break;

                    case "imageId" :
                        echo ("<h1>ImageId get $value</h1>");

                        $oldImage = $saveData[image];
                        if ($oldImage) {
                            if (intval($oldImage)) $imageStr = "|".$oldImage."|";
                            else {
                                $imageStr = $oldImage;
                            }
                            $imageStr .= $value."|";
                            echo ("Set newImage to $imageStr was ($oldImage)<br />");
                            $saveData[image] = $imageStr;
                            $reloadPage = 0;
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
        global $cmsName,$cmsVersion;
        if ($_POST[editSave]) {
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
                // show_array($saveData);
                // GET QUERY AND SAVEDATAID FORM saveData
                $queryData = $this->query_queryData($saveData,$editShow);
                $query = $queryData[query];
                $saveDataId = $queryData[saveDataId];
               

                if ($mode == "new") {
                    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_email` SET $query  ";
                }
                if ($mode == "edit") {
                    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_email` SET $query WHERE `id` = $saveDataId ";
                }
                echo ("$query<br />");
                $result = mysql_query($query);
                if ($result) {
                    // $goPage = $pageInfo[page];
                    if ($mode == "new") cms_infoBox("eMail angelegt");
                    else cms_infoBox("eMail gespeichert");
                    if ($lastUrl) $goPage = $lastUrl;
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei eMail angelegen";
                    else $outPut = "Fehler bei eMail gespeichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br />Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        if ($_POST[cancelSave]) { // abbrechen
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "eMail angelegen abgebrochen";
            else $outPut = "eMail speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
        }


        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;


        echo ("<form method='post' enctype='multipart/form-data' >");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
  

//        if ($mode == "new") {
//            echo ("<input type='submit' class='cmsInputButton' name='editSave' value='eMail anlegen'>");
//        } else {
//            echo ("<input type='submit' class='cmsInputButton' name='editSave' value='eMail speichern'>");
//        }
//
//        $dataId = $saveData[id];
//        if ($dataId) {
//            echo ("<input type='submit' class='cmsInputButton cmsSecond' name='deleteData' value='löchen'>");
//        }
//        echo ("<input type='submit' class='cmsInputButton cmsSecond' name='cancelSave' value='abbrechen'>");
        echo ("</form>");
    }

}



function cms_admin_email($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_email_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_email();

    } else {
        $class = new cmsAdmin_email_base();
        // echo ("File $ownPhpFile not found <br />");
    }
    $class->show($frameWidth);
}



?>

