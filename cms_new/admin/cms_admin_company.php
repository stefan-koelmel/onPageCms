<?php // charset:UTF-8

class cmsAdmin_company_base extends cmsAdmin_editClass_base {

    function admin_dataSource() {
        return "company";;
    }
   
    function admin_listSettings() {
        $res = $this->company_showList();
        return $res;
    }
    
    function show($frameWidth){
        if (!function_exists("cmsCompany_get")) {
            echo ("Hersteller sind deaktiviert !<br>");
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
                $this->CompanyEdit(0,$frameWidth);
                break;

             case "edit" :
                $companyId = $_GET[id];
                $this->companyEdit($companyId,$frameWidth);
                break;

            case "list" :
                $this->companyList($frameWidth);
                break;
            default :
                $this->companylist($frameWidth);

        }
    }
    
    function show_list() {
        $this->companyList($this->frameWidth);
    }
    
    function show_new() {
        $projectId = 0;
        $this->CompanyEdit($projectId,$this->frameWidth); 
    }
    
    function show_edit() {
        $projectId = $_GET[id];        
        $this->CompanyEdit($projectId,$this->frameWidth);        
    }


    function companyList($frameWidth) {
        // echo ("<h1>Hersteller</h1>");

        $sort = $_GET[sort];

        $companyList = cmsCompany_getList($filter,$sort);

        $this->admin_show_list($companyList);
        return 0;
        echo ("<a href='".$GLOBALS[pageData][name].".php?view=new' class='cmsLinkButton' >neuen Hersteller anlegen</a><br />");

        $showList = $this->company_showList();
        $showData = array();
        $showData[titleLine] = 1;
        $this->showList_List($companyList,$showList,$showData,$frameWidth);

        echo ("<a href='".$GLOBALS[pageData][name].".php?view=new' class='cmsLinkButton' >neuen Hersteller anlegen</a>");
    }

    function company_showList() {
        $showList["image"] = array("name"=>"Logo","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["category"] = 0;
        $showList["show"] = 0; //array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }
   
    

    function companyEdit($companyId,$frameWidth) {
        if (is_array($_POST[saveData])) {
            $companyId = $_POST[saveData][id];
        }
        if ($companyId > 0) {
            $mode = "edit";
            $headLine = "Hersteller bearbeiten";
            $saveData = cmsCompany_getById($companyId);
            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }
        } else {
            $mode = "new";
            $headLine = "Hersteller anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) $saveData = $_POST[saveData];
        }
        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        global $pageInfo;


        $tableName = "company";
        $reloadPage = 1;
        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST,$tableName);
            foreach ($specialPostList as $key => $value ) {
                // echo ("Special POST $key = $value <br>");
                switch ($key) {
                    case "category" : $saveData[$key] = $value; break;
                    case "imageId" :
                        //echo ("<h1>ImageId get $value</h1>");
                        cms_infoBox("Bild hochgeladen ");
                        // $reloadPage = 0;
                        $saveData[image] = $value;
                        // $reloadPage = 0;
                        break;

                    default :
                        echo "unkown SpecialResult #$key = '$value' <br>";
                }

            }
        }

       
        $editShow = $this->edit_show($tableName);

        // SAVE
        global $cmsName,$cmsVersion;
        if ($_POST[editSave]) {

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

                if ($mode == "new") {
                    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query  ";
                }
                if ($mode == "edit") {
                    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_".$tableName."` SET $query WHERE `id` = $saveDataId ";
                }
                $result = mysql_query($query);
                if ($result) {
                    $goPage = $pageInfo[page];
                    if ($mode == "new") cms_infoBox("Hersteller angelegt");
                    else cms_infoBox("Hersteller gespeichert");
                    if ($lastUrl) $goPage = $lastUrl;
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Hersteller angelegen";
                    else $outPut = "Fehler bei Herstller speichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br>Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Hersteller angelegen abgebrochen";
            else $outPut = "Hersteller speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            reloadPage($goPage,1);
        }

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;

        echo ("<form method='post' enctype='multipart/form-data' >");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
        


        echo ("</form>");
    }

 
}

function cms_admin_companyClass($ownAdminPath="") {
    $ownPhpFile = $ownAdminPath."/cms_admin_company_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_company();

    } else {
        $class = new cmsAdmin_company_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    return $class;
}

function cms_admin_company($frameWidth,$ownAdminPath=""){
    $class = cms_admin_companyClass($ownAdminPath);
    $class->show($frameWidth);
}



?>
