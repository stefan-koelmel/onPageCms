<?php // charset:UTF-8

class cmsAdmin_user_base extends cmsAdmin_editClass_base {

    function show($frameWidth){
        $view = $_GET[view];

        switch ($view) {
            case "new" :
                $this->userEdit(0,$frameWidth);
                break;

             case "edit" :
                $userId = $_GET[id];
                $this->userEdit($userId,$frameWidth);
                break;

            case "list" :
                $this->userList($frameWidth);
                break;
            default :
                $this->userlist($frameWidth);

        }
    }


    function userList($frameWidth) {
        echo ("<h1>Benutzer</h1>");
        global $pageInfo;

        $sort = $_GET[sort];
        $filter = array();
        $filter[userLevel] = "<=".$_SESSION[userLevel];


        $this->admin_showFilter($frameWidth);

        // show_array($contentData);

        foreach ($_GET as $key => $value) {
            switch ($key) {
                case "category" : if ($value) $filter[$key] = $value; break;
                case "filter_category" : if ($value) $filter["category"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_region"   : if ($value) $filter["region"] = $value; break;
                case "userLevel"   : $filter[$key] = $value; break;
                case "filter_userLevel"   : $filter["userLevel"] = $value; break;
            }
        }


        // show_array($filter);
        $userList = cmsuser_getList($filter,$sort);
 
        $showList = array();
        $showList["salut"] = array("name"=>"Anrede","width"=>100);
        $showList["vName"] = array("name"=>"Vorname","width"=>100);
        $showList["nName"] = array("name"=>"Nachname","width"=>100);
        $showList["userName"] = array("name"=>"Benutzername","width"=>100);
        $showList["userLevel"] = array("name"=>"Ebene","width"=>100);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);




        $this->showList_List($userList,$showList,$showData,$frameWidth);

        echo ("<a href='".$pageInfo[page]."?view=new' class='cmsLinkButton' >neuen Benutzer anlegen</a>");
    }


    function admin_get_filterList_own() {
        // echo ("<h1>HIER</h1>");
        $filterList = array();
        $filterList[produkt] = 0;


        $filterList[userLevel]   = array();
        $filterList[userLevel]["name"] = "Benutzerebene";
        $filterList[userLevel]["type"] = "userLevel";
        $filterList[userLevel]["showData"] = array("maxLevel"=>$_SESSION[userLevel],"submit"=>1,"empty"=>"Alle Benutzerebenen");
        $filterList[userLevel]["filter"] = array();
        $filterList[userLevel]["sort"] = "id";
        $filterList[userLevel]["dataName"] = "userLevel";
        $filterList[userLevel][customFilter] = 1;

        return $filterList;
    }
 

    function userEdit($userId,$frameWidth) {
        if ($userId > 0) {
            $mode = "edit";
            $headLine = "Benutzer bearbeiten";
            $saveData = cmsUser_getById($userId);
            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }

        } else {
            $mode = "new";
            $headLine = "Benutzer anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) $saveData = $_POST[saveData];
        }
        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        global $pageInfo;

        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST);
            if (is_array($specialPostList) AND count($specialPostList)) {
                foreach ($specialList as $key => $value ) {
                    switch ($key) {
                        case "category" : $saveData[$key] = $$value; break;

                        default :
                            echo "unkown SpecialResult #$key = '$value' <br />";
                    }
                }

            }
        }

        $tableName = "user";
        $editShow = $this->edit_show($tableName);

        // SAVE
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
               
                 // GET QUERY AND SAVEDATAID FORM saveData
                $queryData = $this->query_queryData($saveData,$editShow);
                $query = $queryData[query];
                $saveDataId = $queryData[saveDataId];


                if ($mode == "new") {
                    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query  ";
                }
                if ($mode == "edit") {
                    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query WHERE `id` = $saveDataId ";
                }
                $result = mysql_query($query);
                if ($result) {
                    $goPage = $pageInfo[page];
                    if ($mode == "new") cms_infoBox("Benutzer angelegt");
                    else cms_infoBox("Benutzer gespeichert");
                    if ($lastUrl) $goPage = $lastUrl;
                    reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Benutzer angelegen";
                    else $outPut = "Fehler bei Benutzer speichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br />Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Benutzer angelegen abgebrochen";
            else $outPut = "Benutzer speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
        }


       

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $rightWidth = 300;
        $standardHeight = 100;

        echo ("<form method='post'>");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
        echo ("</form>");
    }

 
}

function cms_admin_user($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_user_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_user();

    } else {
        $class = new cmsAdmin_user_base();
        // echo ("File $ownPhpFile not found <br />");
    }
    $class->show($frameWidth);
}



?>
