<?php // charset:UTF-8

class cmsAdmin_category_base extends cmsAdmin_editClass_base {

    function show($frameWidth) {
        if (!function_exists("cmsCategory_get")) {
            echo ("Termine sind deaktiviert !<br>");
            return 0;
        }
        $view = $_GET[view];

        $this->tableName = "category";
        switch ($view) {
             case "editShow" :
                $this->edit_editShow();
                break;
            
            case "editList" :
                $this->edit_editList();
                break;
            case "new" :
                $this->category_edit(0,$frameWidth);
                break;

             case "edit" :
                $categoryId = $_GET[id];
                $this->category_edit($categoryId,$frameWidth);
                break;

            case "list" :
                $this->category_list($frameWidth);
                break;
            default :
                $this->category_list($frameWidth);

        }
    }


    function category_List($frameWidth) {

        // $categoryList = cmsCategory_getList($filter,$sort);
        global $pageInfo;

        $mainCat = $_GET[mainCat];

        $headLine = "Kategorien";
        if ($mainCat>0) {
            $mainCatData = cmsCategory_getById($mainCat);
            $mainCatName = $mainCatData[name];
            $topCat = $mainCatData[mainCat];
            $headLine .= " von '$mainCatName'";
        }

        echo ("<h1>$headLine</h1>");

        if ($mainCatName) {
            if ($topCat > 0) {
                $topCatData = cmsCategory_getById($topCat);
                $topCatName = $mainCatData[name];
                echo ("<a href='$pageInfo[page]?mainCat=$topCat' class='cmsLinkButton cmsSecond'> gehe zur Kategorie $topCatName </a> ");
            } else {
                echo ("<a href='$pageInfo[page]' class='cmsLinkButton cmsSecond'> gehe zur Kategorie-Übersicht</a> ");
            }
        }

         if ($mainCatName) {
            echo ("<a href='$pageInfo[page]?view=new&mainCat=$mainCat' class='cmsLinkButton' >neue Kategorie in '$mainCatName' anlegen</a>");
        } else {
            echo ("<a href='$pageInfo[page]?view=new' class='cmsLinkButton' >neue Haupt-Kategorie anlegen</a>");
        }

        // echo("MainCat = $mainCat // Top Catekory = $topCat<br />");

        if ($mainCat >= 0) {
            $sort = "name";
            if ($mainCat == 140) $sort = "name_up";
            $categoryList = cmsCategory_getList(array("mainCat"=>$mainCat,"show"=>1),$sort);


        } else {
            $categoryList = cmsCategory_getList(array("show"=>1,"mainCat"=>0),"SORT BY `name` ASC");
            $mainCat = 0;
        }

        for ($i=0;$i<count($categoryList);$i++) {
            $category = $categoryList[$i];
            $id = $category[id];
            $subCategory = cmsCategory_getList(array("show"=>1,"mainCat"=>$id), $sort);
            //echo ("Unter Kategorien Anzahl = ".count($subCategory)."<br />");
            if (is_array($subCategory) AND count($subCategory)>0 ) {
                $categoryList[$i][subCat] = 1;
                // echo ("SUB <br />");
            } else {
                $categoryList[$i][subCat] = 0;
            }
        }


        $showList = $this->category_showList();

        $this->showList_List($categoryList,$showList,$showData,$frameWidth);


        
    }


    function category_showList() {
        $showList = array();
        $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40);
        $showList["name"] = array("name"=>"Name","width"=>400);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center");
        $showList["subCat"] = array("name"=>"Unterkategorien","width"=>120,"align"=>"center");
        return $showList;
    }


    function category_getLine($category,$frameWidth) {

        global $pageInfo;
        // echo ("cmsCategory_getLine($category,$frameWidth,$topCat)<br />");
        $showList = array();
        $showList["image"] = array("name"=>"Bild","width"=>80,"height"=>40);
        $showList["name"] = array("name"=>"Name","width"=>400);
        $showList["show"] = array("name"=>"Zeigen","width"=>50,"align"=>"center");
        $showList["subCat"] = array("name"=>"Unterkategorien","width"=>120,"align"=>"center");

        if (!is_array($category) ) { // showTitleLine
            $str = "";
            $divName = "cmsCategoryTitleLine";
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
                $str.= div_start_str("cmsCategoryTitleLine_$key",$style);
                $str.= $name;
                $str.= div_end_str("cmsCategoryTitleLine_$key");
            }
            $str.= div_end_str($divName,"before");
            return $str;
        }

        $str = "";

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
        if ($goPage == "") $goPage.= "?";
        else $goPage .= "&";
        $goPage .= "view=edit&id=$category[id]";

        $str.= "<a href='$goPage'>";
        $divName = "cmsCategoryLine";
        $divData = array();
        $divData[style] = "width:".$frameWidth."px;margin-top:3px;";
        $height = $showList["image"]["height"];
        if ($height) $divData[style] .= "height:".$height."px;line-height:".$height."px;";
        $divData[id] = $category[id];
        $str.= div_start_str($divName,$divData);

        foreach ($showList as $key => $value) {
            $width = $value[width];
            $height = $value[height];
            $name = $value[name];

            $cont = "key=$key";
            switch ($key) {
                case "image" :
                    $imageId = intval($category[image]);
                    $cont = "kein Bild";
                    if ($imageId > 0) {
                        $imageData = cmsImage_getData_by_Id($imageId);
                        if (is_array($imageData)) {
                            // $cont = cmsImage_showImage($imageData,$width,array("frameHeight"=>$height,"frameWidth"=>$width,"vAlign"=>"middle","hAlign"=>"center"));
                        }
                    }
                    break;
                case "name" :
                    $cont = $category[$key];
                    if (!$cont) $cont = "kein Name";
                    break;

                case "show" :
                    if ($category[$key]) $cont = "1";
                    else $cont = 0;
                    break;
                case "subCat":
                    if ($category[$key] == 1 ) $cont="<a href='$pageInfo[page]?mainCat=$category[id]'>zeigen</a>";
                    else $cont="<a href='$pageInfo[page]?view=new&mainCat=$category[id]'>anlegen</a>";



            }

            $align = $value[align];

            $style = "float:left;width:".$width."px;";
            if ($align) $style .= "text-align:$align;";

            $str.= div_start_str("cmsCategoryLine_$key",$style);
            $str.= $cont;
            $str.= div_end_str("cmsCategoryLine_$key");
        }
        $str.= div_end_str($divName,"before");
        $str.= "</a>";
        return $str;
    }



    function category_Edit($categoryId,$frameWidth) {
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

         if ($categoryId > 0) {
            $mode = "edit";
            $headLine = "Kategory bearbeiten";
            $saveData = cmsCategory_getById($categoryId);
            if ($_POST[saveData]) {
                $saveData = php_clearPost($_POST[saveData]);
            }

        } else {
            $mode = "new";
            $headLine = "Kategorie anlegen";
            $saveData = array();
            $saveData[show] = 1;
            if ($_POST[saveData]) $saveData = $_POST[saveData];
        }

        $mainCat = $_GET[mainCat];

        if ($mainCat>0) {
            $mainCatData = cmsCategory_getById($mainCat);
            $mainCatName = $mainCatData[name];
            $topCat = $mainCatData[mainCat];
            $headLine .= " in '$mainCatName'";
            $saveData[mainCat] = $mainCat;
        }


        echo ("<h1>$headLine</h1>");
        $lastUrl = $this->backLink();
        global $pageInfo;
        
        $tableName = "category";

        $reloadPage = 1;
        if (is_array($_POST)) {
            $specialPostList = $this->specialPostList($_POST, $tableName);
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
                        echo "unkown SpecialResult #$key = '$value' <br />";
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
                    // $goPage = $pageInfo[page];
                    if ($mode == "new") cms_infoBox("Kategorie angelegt");
                    else cms_infoBox("Kategorie gespeichert");
                    if ($lastUrl) $goPage = $lastUrl;
                    if ($reloadPage) reloadPage($goPage,1);
                } else {
                    if ($mode == "new") $outPut = "Fehler bei Kategorie angelegen";
                    else $outPut = "Fehler bei Kategorie speichern";
                    if ($_SESSION[showLevel]==9) $outPut .= "<br />Query = '$query'";
                    cms_errorBox($outPut);
                }
            }
        }
        // CANCEL
        if ($_POST[cancelSave]) { // abbrechen
            $reloadPage = 1;
            // $goPage = $pageInfo[page];
            if ($mode == "new") $outPut = "Kategorie angelegen abgebrochen";
            else $outPut = "Kategorie speichern abgebrochen";
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
//


    }
}


function cms_admin_category($frameWidth,$ownAdminPath=""){
    $ownPhpFile = $ownAdminPath."/cms_admin_category_own.php";
    if (file_exists($ownPhpFile)) {
        require_once($ownPhpFile);
        $class = new cmsAdmin_category();

    } else {
        $class = new cmsAdmin_category_base();
        // echo ("File $ownPhpFile not found <br />");
    }
    $class->show($frameWidth);
}





?>
