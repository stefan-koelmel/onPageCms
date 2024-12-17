<?php // charset:UTF-8

function cmsProduct_getList($filter,$sort,$out=null) {
  
    if ($sort) {
        $upPos = strpos($sort, "_up");
        $sortQuery = "";
        if ($upPos) {
            $sortValue = substr($sort,0,$upPos);
            $sortQuery = "ORDER BY `$sortValue` DESC ";
            // echo ("Sort down '$sortValue' -> $sortQuery <br>");
        }

        if ($sortQuery=="") {
           $sortQuery = "ORDER BY `$sort` ASC ";
        }
    } else {
        $sortQuery = "ORDER BY `name` ASC ";
    }

    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
                if ($filterQuery != "") $filterQuery .= " AND ";


                switch ($value[0]) {
                    case ">" :
                        $filterQuery .= "`$key`$value";
                        break;
                    case "<" :
                        $filterQuery .= "`$key`$value";
                        break;
                    default :
                        switch ($key) {
                            case "category" :
                                $filterQuery .= "(`$key` = '$value' OR `$key` LIKE  '%|$value|%')";
                                break;


                             case "search" :
                                $filterQuery .= "(`name` LIKE '%$value%' OR `subName` LIKE '%$value%' )";
                                break;

                            case "searchText" :
                                $filterQuery .= "(`name` LIKE '%$value%' OR `subName` LIKE '%$value%' OR `info` LIKE '%$value%')";
                                break;

                            default :
                                $filterQuery .= "`$key`='$value'";
                        }
                }



              
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_product` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    if (!$result) {
        echo ("Fehler in $query <br>");
        return 0;
    }
    if ($out == "out") echo ("$query <br>");
    
    $res = array();
    while ($product = mysql_fetch_assoc($result)) {
        switch ($out) {
            case "assoId" :
                $id = $product[id];
                $res[$id] = $product;
                break;
            case "assoName" :
                $name = $product[name];
                $res[name] = $product;
                break;
            default :
                $res[] = $product;
        }
        
    }
    return $res;
}


function cmsProduct_search($searchString,$searchText,$filter) {
    if (!is_array($filter)) $filter = array();
    if ($searchText) $filter["searchText"] = $searchString;
    else $filter["search"] = $searchString;
    $out = "";
    $sort ="";
    $res = cmsProduct_getList($filter,$sort,$out);
    return $res;
}

function cmsProduct_getById($productId) {
    $productData = cmsProduct_get(array("id"=>$productId));
    if (is_array($productData)) return $productData;
    $filterQuery = "WHERE `id` = $productId";
    $sortQuery = "";


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_product` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        cms_errorBox("Hersteller nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        cms_errorBox("Mehrere Hersteller gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    $productData =  mysql_fetch_assoc($result);

    return $productData;
}

function cmsProduct_get($filter) {

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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_product` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    // echo("$query -> $result <br>");
    $anz = mysql_num_rows($result);

   //  echo("$query -> $result $anz <br>");
    if ($anz == 0) {
        // cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        // cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return $anz;
    }
    $productData = mysql_fetch_assoc($result);
    $productData = php_clearQuery($productData);
    return $productData;
}


function cmsProduct_selectProduct($code,$dataName,$showData,$filter,$sort) {
    $productList = cmsProduct_getList($filter,$sort);

    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:200px;' value='$code' >";

    $str.= "<option value='0'";
    if (!$code) $str.= " selected='1' ";
    $str.= ">Bitte wählen</option>";

    for($i=0;$i<count($productList);$i++) {
        $productId = $productList[$i][id];
        $productName = $productList[$i][name];
         $str.= "<option value='$productId'";
         if ($code == $productId)  $str.= " selected='1' ";
         $str.= ">$productName</option>";
    }
    $str.= "</select>";
    return $str;


}

function cmsProduct_selectProduct_toggle($code,$dataName,$showData,$filter,$sort) {
    $productList = cmsProduct_getList($filter,$sort);

    $width = 300;
    $count = 3;
    $mode = "single";
    $class = "";
    foreach ($showData as $key => $value) {
        switch ($key) {
            case "width" : $width = $value; break;
            case "count" : $count = $value; break;
            case "mode"  : $mode = $value; break;
            case "class" : $class = $value; break;

            default :
                echo ("unkown Mode in cmsCategory_selectCategory_toogle #$key=$value<br>");
        }
    }
    $width = 400;

    $border = 1;
    $padding = 3;
    $width = $width-2*$border;
    $divName = "cmsToggleSelect";
    if ($class) $divName .= " ".$class;
    $divData = array();
    $divData[style] = "width:".$width."px;";
    $divData[toggleMode] = $mode;
    $str.=div_start_str($divName,$divData);
    $widthItem = ($width - ($count*$border) -($count*$padding)- $border) / $count;
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
    for($i=0;$i<count($productList);$i++) {
        $categoryId = $productList[$i][id];
        $categoryName = $productList[$i][name];
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

        if ($columnNr == $count) {
            $divDataItem[style] .= "border-right-width:1px;";
            $columnNr = 0;
        }


        $str .= div_start_str($divNameItem,$divDataItem);
        $str .= $categoryName;
        $str .= div_end_str($divNameItem);
    }
    $str.= div_end_str($divName,"before");
    $str .= "<input type='hidden' id='$class' name='$dataName' readonly='readonly' value='$out' >";
    return $str;
}

function cmsProduct_selectProduct_auto($code,$dataName,$showData,$filter,$sort) {
    global $cmsName,$cmsVersion;

    $showName = "";

    if ($code>0) {
        $productData = cmsProduct_get(array("id"=>$code));
        if (is_array($productData)) $showName = $productData[name];
    }

    $url = "/cms_".$cmsVersion."/getData/product.php";
    $url .= "?cmsVersion=$cmsVersion&cmsName=$cmsName";

    $class = "cmsEditProductAuto";

    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "mainCat" : $url.= "&mainCat=$value"; break;
                case "show"    : if ($value!=1) $url .= "&show=0"; break;
                case "style"   : $style = $value; break;
                case "class"   : $class .= " ".$value; break;

                default :
                    echo ("$key = $value <br>");
            }
        }
    }

    $str = "";
    $str .= "<input type='text' class='$class' style='$style' name='$dataName' id='queryCat' url='$url' value='$showName' />";

    return $str;
}


?>
