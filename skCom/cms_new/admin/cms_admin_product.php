<?php // charset:UTF-8

class cmsAdmin_product_base extends cmsAdmin_editClass_base {

    function show($frameWidth){
        if (!function_exists("cmsProduct_get")) {
            echo ("Produkte sind deaktiviert !<br>");
            return 0;
        }
        $view = $_GET[view];
        $this->tableName = "product";
        switch ($view) {
            case "editShow" :
                $this->edit_editShow();
                break;
            
            case "editList" :
                $this->edit_editList();
                break;
            
            case "new" :
                $this->productEdit(0,$frameWidth);
                break;

             case "edit" :
                $productId = $_GET[id];
                $this->productEdit($productId,$frameWidth);
                break;

            case "list" :
                $this->productList($frameWidth);
                break;
            
            
            
            
            default :
                $this->productlist($frameWidth);

        }
    }


    function productList($frameWidth) {
        echo ("<h1>Produkte</h1>");

        $sort = $_GET[sort];

        $productList = cmsproduct_getList($filter,$sort);
 
        $showList = $this->product_showList();
        
        $this->showList_List($productList,$showList,$showData,$frameWidth);

        echo ("<a href='".$GLOBALS[pageData][name].".php?view=new' class='cmsLinkButton' >neues Produkt anlegen</a>");
    }

    function product_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Produktbild","width"=>80,"height"=>60,"sort"=>0);
        $showList["name"] = array("name"=>"Name","width"=>200);
        $showList["company"] = array("name"=>"Hersteller","width"=>120);
        $showList["category"] = array("name"=>"Kategorie","width"=>120);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center","sort"=>0);
        return $showList;
    }


    function productEdit($productId,$frameWidth) {
        if ($productId > 0) {
            $mode = "edit";
            $headLine = "Produkt bearbeiten";
            $saveData = cmsProduct_getById($productId);
            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }

        } else {
            $mode = "new";
            $headLine = "Produkt anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) $saveData = $_POST[saveData];
        }
        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        global $pageInfo;

        $reloadPage = 1;
        $tableName = "product";
        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST,$tableName);
            foreach ($specialPostList as $key => $value ) {
                switch ($key) {
                    case "category" : $saveData[$key] = $$value; break;
                    case "imageId" :
                        //echo ("<h1>ImageId get $value</h1>");
                        $saveData[image] = $value;
                        $reloadPage = 0;
                        cms_infoBox("Bild hochgeladen ");
                        break;
                    default :
                        echo "unkown SpecialResult #$key = '$value' <br>";
                }

            }
        }

        $specialData = array();
        if ($saveData[category]) $specialData[category] = $saveData[category];
        else $specialData[category] = "-";
        
        $editShow = $this->edit_show($tableName,$specialData);
        

        // SAVE
        global $cmsName,$cmsVersion;
        if ($_POST[editSave]) {
//            $uploadResult = $this->admin_uploadImage();
//            echo ("Upload Result = $uploadResult <br>");
//            if (is_integer($uploadResult)) {
//                echo ("<h1> new Image ID after upload = $uploadResult</h1>");
//                $saveData[image] = $uploadResult;
//                $reloadPage = 0;
//            }

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
                    if ($mode == "new") cms_infoBox("Produkt angelegt");
                    else cms_infoBox("Produkt gespeichert");
                    if ($lastUrl) $goPage = $lastUrl;
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Produkt angelegen";
                    else $outPut = "Fehler bei Produkt speichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br>Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            $reloadPage = 1;
            $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Produkt angelegen abgebrochen";
            else $outPut = "Produkt speichern abgebrochen";
            cms_infoBox($outPut);
            if ($lastUrl) $goPage = $lastUrl;
            if ($reloadPage) reloadPage($goPage,1);
        }


       

        $leftWidth = 200;
        $rightWidth = $frameWidth - $leftWidth - 10;
        $standardHeight = 100;

        echo ("<form method='post' enctype='multipart/form-data' >");

        $this->editShowInput($saveData,$editShow,$error,$leftWidth,$rightWidth,$standardHeight);
        
        echo ("</form>");
    }

 
}

function cms_admin_product($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_product_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_product();

    } else {
        $class = new cmsAdmin_product_base();
        // echo ("File $ownPhpFile not found <br>");
    }
    $class->show($frameWidth);
}



?>
