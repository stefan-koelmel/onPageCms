<?php // charset:UTF-8

function cmsCategory_getList($filter,$sort="",$out="") {
    if ($sort) {
        $upPos = strpos($sort, "_up");
        $sortQuery = "";
        if ($upPos) {
            $sortValue = substr($sort,0,$upPos);
            $sortQuery = "ORDER BY `$sortValue` DESC ";

        }
        if ($sortQuery=="") {
           $sortQuery = "ORDER BY `$sort` ASC ";
        }
    } else {
        $sortQuery = "ORDER BY `name` ASC ";
    }

    if ($filter) {
        if (is_array($filter)) {
            // show_array($filter);
            $filterQuery = "";
            foreach($filter as $key => $value) {
                if ($filterQuery != "") $filterQuery .= " AND ";
                $filterQuery .= "`$key` = '$value'";
            }
            $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }

       
    } else {
        $filterQuery = "WHERE `show` = 1";
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    if ($out == "out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($category = mysql_fetch_assoc($result)) {
        if (subStr($out,0,4) == "asso") {
            $catId = $category[id];
            $catName = $category[name];
            switch ($out) {
                case "assoName" : $res[$catName] = $catId; break;
                case "assoId" :   $res[$catId]   = $catName; break;
                case "assoIdList" : $res[$catId]   = $category; break;
                default:
                    $res[$catId] = $category;
            }
        } else {
            $res[] = $category;
        }
    }
    return $res;
}

function cmsCategory_selectCategory($code,$dataName,$showData,$filter,$sort) {
    
    // echo ("function cmsCategory_selectCategory($code,$dataName,$showData,$filter,$sort) {<br>");
    $categoryList = cmsCategory_getList($filter,$sort,"__out");

    $str = "";
    $emptyStr = "Kategorie wählen";
    if ($showData["empty"]) {
        $emptyStr = $showData["empty"];
    }

    $editStyle = "min-width:200px;";
    if ($showData["style"]) $editStyle .= $showData[style];

    $editClass = "cmsSelectType";
    if ($showData["class"]) $editClass.= " ".$showData["class"];

    $disabled = "";
    if ($showData[disabled]) $disabled = "disabled='disabled'";
    $readonly = "";
    if ($showData[readonly]) $readonly = "readonly='readonly'";
    $notIds = array();
    if (is_array($showData[notIds])) $notIds = $showData[notIds];
    
        
    // echo ("EditStyle ='$editStyle'<br>");
    // echo ("EditClass ='$editClass'<br>");

    $str.= "<select name='$dataName' class='$editClass'  style='$editStyle' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $str .= "value='$code' $disabled $readonly >";

    if ($emptyStr) {
        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";
        $str.= ">$emptyStr</option>";
    }
    

    for($i=0;$i<count($categoryList);$i++) {
        
        $idValue = "id";
        if ($showData[idValue]) $idValue = $showData[idValue];

        $categoryId = $categoryList[$i][$idValue];
        if (!$notIds[$categoryId]) {
            $outValue = "name";
            if ($showData[out]) $outValue = $showData[out];
            $categoryName = $categoryList[$i][$outValue];
            $str.= "<option value='$categoryId'";
            if ($code == $categoryId)  $str.= " selected='1' ";
            $str.= ">$categoryName</option>";
        }
    }
    $str.= "</select>";
    return $str;


}

function cmsCategory_selectCategory_toogle($code,$dataName,$showData,$filter,$sort) {
    // show_array($showData);
    $width = 300;
    $count = 3;
    $mode = "single";
    $class = "cmsToggle_Category";
    $outValue = "name";
    $mainFrame = 1;
    $dontShow = array();
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "width" : $width = $value; break;
            case "count" : if ($value) $count = $value; break;
            case "mode"  : $mode = $value; break;
            case "class" : $class = $value; break;
            case "out"   : $outValue = $value; break;
            case "url"   : $url = $value; break;
            case "dontMainFrame" : $mainFrame = 0; break;
            case "empty" : break;
            case "sort" : $sort = $value; break;
            case "dontShow" : if (is_array($value)) $dontShow = $value; break;

            default :
                echo ("unkown Mode in cmsCategory_selectCategory_toogle #$key=$value<br>");
        }
    }
    
    if ("$filter[mainCat]"=="-") {
        // show_array($showData);
        // echo ("<h1>cmsCategory_selectCategory_toogle($code,$dataName,$showData,$filter,$sort)<br></h1>");
        // show_array($filter);
        $categoryList = array();
        $noMainCat = 1;
        
    } else {

        $categoryList = cmsCategory_getList($filter,$sort);
        // show_array($categoryList);
    }
    
   
    $border = 1;
    $padding = 3;
    $width = $width-2*$border;
    $divName = "cmsToggleSelect";
    //$str .= div_start_str($class."_contentFrame","display:inline-block;");


    if ($class) $divName .= " ".$class;


    $divData = array();
    $divData[style] = "width:".$width."px;";
    $divData[toggleMode] = $mode;
    $divData[mainCat] = $filter[mainCat];
    $divData[count] = $count;
    $divData[dataName] = $dataName;
    if ($url) $divData[url] = $url;

    if ($mainFrame) $str.=div_start_str($divName,$divData);
    $widthItem = floor(($width - ($count*$border) -($count*$padding)- $border) / $count);
    if ($noMainCat == 1) {
        $str .= "<strong>Keine Hauptrubrik gewählt</strong>";
    } else {
        switch ($mode) {
            case "multi" :
                $out = "|";
                $exList = explode("|",$code);
                $codeList = array();
                for ($i=0;$i<count($exList);$i++) {
                    $id = $exList[$i];
                    if ($id) {
                        // echo ("id $id in $code<br>");
                        $codeList[$id] = 1;
                        // $out .= $id."|";
                    }
                }
                // show_array($codeList);
                break;
            default :
                $out = "";
        }
        $columnNr = 0;
        $lineNr = 0;
        if (count($categoryList)) {
            for($i=0;$i<count($categoryList);$i++) {
                $categoryId = $categoryList[$i][id];
                $categoryName = $categoryList[$i][$outValue];
                
                if ($dontShow[$categoryId]) {
                    // echo ("Dont SHow $categoryName<br>");
                } else {


                    // echo ("Category $categoryName = $categoryId <br>");
                    $divNameItem = "cmsToggleItem";
                    switch ($mode) {
                         case "multi" :
                             //echo ("Suche $categoryId in codeList $codeList[$categoryId] <br>");
                             if ($codeList[$categoryId]) {
                                 $out .= $categoryId."|";
                                 $divNameItem .= " cmsToggleSelected";
                                 //  echo ("Found $id<br>");
                             }
                             break;

                        case "single" :
                            if ($code == $categoryId) {
                                $out = $categoryId;
                                $divNameItem .= " cmsToggleSelected";
                            }
                            break;

                        default :
                    }


                    $divNameItem .= " ".$class."_".$categoryId;
                    $columnNr++;
                    $divDataItem = array();
                    $divDataItem[style] = "width:".$widthItem."px;";
                    $divDataItem[toggleName] = $categoryName;
                    $divDataItem[toggleId] = $categoryId;
                    $divDataItem[toggleClass] = $class;

                    if ($lineNr) $divDataItem[style] .= "border-top-width:0px;";
                    if ($columnNr == $count) {
                        $divDataItem[style] .= "border-right-width:1px;";
                        $columnNr = 0;
                        $lineNr++;
                    }
                     if ($i == count($categoryList)-1) {
                        $divDataItem[style] .= "border-right-width:1px;";
                        $columnNr = 0;
                    }



                    $str .= div_start_str($divNameItem,$divDataItem);
                    $str .= $categoryName;
                    $str .= div_end_str($divNameItem);
                }  
            }
        } else {
            $str .= "<b>Keine Daten vorhanden</b>";
            $out = "-";
        }
    }
    if ($mainFrame) $str.= div_end_str($divName,"before");
    // $str.= div_end_str($class."_contentFrame");
    if ($mainFrame) $str .= "<input type='hidden' id='$class' name='$dataName' readonly='readonly' value='$out' >";
    return $str;
}


function cmsCategory_selectCategory_auto($code,$dataName,$showData,$filter,$sort) {
    global $cmsName,$cmsVersion;

    $showName = "";

    if ($code>0) {
        $categoryData = cmsCategory_get(array("id"=>$code));
        // if (is_array($categoryData)) $showName = $categoryData[name];
    }

    $url = "/cms_".$cmsVersion."/getData/category.php";
    $url .= "?cmsVersion=$cmsVersion&cmsName=$cmsName";
    
    $disabled = "";
    $readOnly = "";
    $class = "cmsEditCategoryAuto";
    $id    = "queryCat";
    if (is_array($showData)) {
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "show"    : if ($value!=1) $url .= "&show=0"; break;
                case "style"   : $style = $value; break;
                case "class"   : $class .= " ".$value; break;
                case "showName" : $showName = $value; break;
                case "disabled"  : $disabled = $value;
                case "readonly" : $readonly = $value; break;
                case "id" : $id = $value; break;
                default :
                    echo ("showData category_autoComplete $key = $value <br>");
            }
        }
    }

     if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "mainCat" : $url.= "&mainCat=$value"; break;
                case "show" : $url.= "&show=$value"; break;
                default :
                    echo ("filter category_autoComplete $key = $value <br>");
            }
        }
    }

    $str = "";
    $str .= "<div id='".$id."GetUrl' style='display:none;'>$url</div>";
    $str .= "<input type='text' class='$class' style='$style' name='$dataName' id='$id' value='$showName' $readonly $disabled />";

    return $str;
}

function cmsCategory_selectRegion_auto($code,$dataName,$showData,$filter,$sort) {
    global $cmsName,$cmsVersion;

    $showName = "";

    if ($code>0) {
        $categoryData = cmsCategory_get(array("id"=>$code));
        if (is_array($categoryData)) $code = $categoryData[name];
    }

    $url = "/cms_".$cmsVersion."/getData/category.php";
    $url .= "?cmsVersion=$cmsVersion&cmsName=$cmsName";

    $class = "cmsEditCategoryAuto";
    $disabled = "";
    if (is_array($showData)) {
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "show"     : if ($value!=1) $url .= "&show=0"; break;
                case "style"    : $style = $value; break;
                case "class"    : $class .= " ".$value; break;
                case "disabled" : if ($value == 1) $disabled = " disabled='disabled' "; break;

                default :
                    echo ("$key = $value <br>");
            }
        }
    }

    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "mainCat" : $url.= "&mainCat=$value"; break;
                
                default :
                    echo ("$key = $value <br>");
            }
        }
    }

    $str = "";
    $str .= "<input type='text' class='$class' $disabled style='$style' name='$dataName' id='queryRegion' url='$url' value='$code' />";

    return $str;
}

function cmsCategory_getById($categoryId) {
    $categoryData = cmsCategory_get(array("id"=>$categoryId));
    if (is_array($categoryData)) return $categoryData;
    $filterQuery = "WHERE `id` = $categoryId";
    $sortQuery = "";


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    $categoryData =  mysql_fetch_assoc($result);

    return $categoryData;
}


function cmsCategory_getName_byId($categoryId) {
    // echo ("cmsCategory_getName_byId($categoryId)<br>");
    $catData = cmsCategory_get(array("id"=>$categoryId));
    if (is_array($catData)) return $catData[name];
    return "";

}

function cmsCategory_getByName($categoryName,$mainCategory) {
    echo ("cmsCategory_getByName($categoryName,$mainCategory)<br>");
    $filterQuery = "WHERE `name` LIKE '$categoryName'";
    if ($mainCategory) $filterQuery .= " AND `mainCat` = $mainCategory";
    $sortQuery = "";

    // SELECT * FROM `klappeAuf_cms_category` WHERE `name` LIKE 'Lesung' AND `mainCat` = 1
    //SELECT * FROM `klappeAuf_cms_category` WHERE `name` LIKE `Lesung` AND `mainCat` = 1


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    $categoryData =  mysql_fetch_assoc($result);

    return $categoryData;
}

function cmsCategory_get($filter) {
    $filterQuery = "";
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            if ($filterQuery == "") $filterQuery .= "WHERE ";
            else $filterQuery .= "AND ";
            switch ($key) {
                case "name" ; $type = "text"; break;
                case "info" ; $type = "text"; break;
                default:
                    $type = "normal";
            }
            if ($type == "text") {
                $filterQuery .= "`$key` LIKE '$value' ";
            } else {
                $filterQuery .= "`$key` = '$value' ";
            }
        }
    }
    
    $sortQuery = "";
    // echo("$filterQuery <br>");

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
   //  echo("$query -> $result <br>");
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        // cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        // cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return $anz;
    }
    $categoryData =  mysql_fetch_assoc($result);
    $categoryData = php_clearQuery($categoryData);
    return $categoryData;
}

function cmsCategory_getListFromString($categoryString,$output) {
    // echo (" cmsCategory_getListFromString($categoryString,$output)<br>");
    $res = array();
    $splitList = explode("|",$categoryString);
    if (count($splitList)>1) {
        for($i=1;$i<count($splitList)-1;$i++) {
           $id = $splitList[$i];
            // echo ("CategoryId = $id<br>");
            $res[$id] = $id;

        }
    } else {
        if (intval($categoryString)) {
            $id = intval($categoryString);
            $res[$id] = $id;
        }
    }
    switch ($output) {
        case "text" :
            foreach($res as $id => $category) {
                $catName = "";
                $catData = cmsCategory_getById($id);
                if (is_array($catData)) {
                    $catName = $catData[name];
                    $res[$id] = $catName;
                }
                
                // echo ("Get CategoryName for $id = '$catName' <br>");
            }
            break;

    }

    return $res;
    

}



function cmsCategory_existName($categoryName,$mainCategory) {

    $filterQuery = "WHERE `name` LIKE '$categoryName'";
    if ($mainCategory) $filterQuery .= " AND `mainCat` = $mainCategory";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}


function cmsCategory_existID($categoryId,$mainCategory=null) {

    $filterQuery = "WHERE `id` = $categoryId";
    if ($mainCategory) $filterQuery .= " AND `mainCat` = $mainCategory";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_category` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
   //  echo ("Query $query <br>");
    $anz = mysql_num_rows($result);
    // echo ("Anzahl = $anz <br>");
    return $anz;

}

function cmsCategory_save($data) {
    $id = $data[id];
    
    if ($id) {
        $existData = cmsCategory_get(array("id"=>$id));
        
        if (is_array($existData)) {
            $data[id] = $existData[id];
            return cmsCategory_update($data,$existData);
        }
    }

    $name = $data[name];
    $mainCat = $data[mainCat];
    $existData = cmsCategory_existName($name, $mainCat);
    if (is_array($existData)) {
        $data[id] = $existData[id];
        return cmsCategory_update($data,$existData);
    }


    $query = "";
  //   unset($data[lastMod]);
    foreach ($data as $key => $value ) {        
        if ($value) {
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_category` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        echo (mysql_errno()."<br>");
        return 0;
    }
    $insertId = mysql_insert_id();
    return $insertId;
}

function cmsCategory_update($data,$oldData=array()) {
    $query = "";
    $id = $data[id];
    foreach ($data as $key => $value ) {
        if ($value AND $key != "id") {
            if ($value == $oldData[$key]) {
                // same Data
            } else {
                if ($query != "" ) $query.= ", ";
                $query.= "`$key`='$value'";
            }
        }
    }
    if ($query == "") {
       // echo ("No Change in Data <br>");
        return 1;
    }
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_category` SET ".$query." WHERE `id` = $id ";
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

?>
