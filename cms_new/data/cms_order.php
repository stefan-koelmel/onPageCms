<?php // charset:UTF-8
function cmsOrder_getList($filter,$sort) {

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
        $sortQuery = "ORDER BY `nName` ASC ";
    }

    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
                 switch ($value[0]) {
                    case ">" :
                        $filterQuery .= "`$key`$value";
                        break;
                    case "<" :
                        $filterQuery .= "`$key`$value";
                        break;
                    default :
                        if ($key == "category") { // multiselect from category
                             $filterQuery .= " (`$key` = '$value' OR `$key` LIKE  '%|$value|%') ";
                        } else {
                            $filterQuery .= "`$key`='$value'";
                        }
                }


//                if ($filterQuery != "") $filterQuery .= " AND ";
//                $filterQuery .= "`$key` = $value";
            }
            $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }


    } else {
        $filterQuery = ""; //  "WHERE `show` = 1";
    }


        $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_order` ".$filterQuery." ".$sortQuery;
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($user = mysql_fetch_assoc($result)) {
        $res[] = php_clearQuery($user);

    }
    return $res;
}


function cmsOrder_get($filter) {
    $filterQuery = "";
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            if ($filterQuery == "") $filterQuery .= "WHERE ";
            else $filterQuery .= "AND ";
            switch ($key) {
                case "name" : $type = "text"; break;
                case "info" : $type = "text"; break;
                case "url" : $type = "text"; break;
                default:
                    $type = "normal";
                    // if (is_string($value)) $type = "text";
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_order` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    //echo("$query -> $result <br>");
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
    $userData =  mysql_fetch_assoc($result);
    $userData = php_clearQuery($userData);
    
    return $userData;
}

function cmsOrder_add($orderData) {
    $res = 0;
    
    
    $payment = $orderData[payment];
    if (!$payment) return "noPayment";
    
    $adress = $orderData[adress];
    if (!is_array($adress)) {
        return "noAdress";
    }
    
    
    echo ("ADD ORDER<br>");
    
    
    return "notReady";    
    
    
    
    
    
    
    
}



function cmsOrder_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cms_user_getById($id);
        if ($existData) {
            return cmsOrder_update($data);
        }
    }
    echo ("Not Found - no Id <br> ");

    $email = php_clearStr($data[email]);
    $existData = cmsOrder_get(array("email"=>$email));
    if (is_array($existData)) {
        $data[id] = $existData[id];
        return cmsOrder_update($data);
    }
    // echo ("not Found with Name / Id<br>");
    $query = "";
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }

        if ($value) {
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    $query = "INSERT `".$GLOBALS[cmsName]."_cms_order` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsOrder_save $query <br>";
        return 0;
    }
    $insertId = mysql_insert_id();
    return $insertId;
}

function cmsOrder_update($data) {
    $query = "";
    $id = $data[id];
    $addChange = 0;
    $addLastMod = 0;
    
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }
        
        if (!is_null($value) AND $key != "id") {
            if ($value == "not") $value = "";
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_order` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}






function cmsOrder_selectSalut($code,$dataName,$showData,$showFilter,$showSort) {
    $salutList = cms_user_salut();
    $str = "";
    //$str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$code' >";

    $empty ="Bitte wählen";
    if ($showData["empty"]) $empty = $showData["empty"];

    
    
    $class = "cmsSelectType cmsSelectSalut";
    if ($showData["class"]) {
        $class .= " ".$showData["class"];
    }
    
    $str.= "<select name='$dataName' class='$class' ";
    
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $valueStr = "";
    if ($code) $valueStr = " value='$code'";
    
   
    $style = "min-width:200px;";
    if ($showData[style]) {
        if (is_null(strpos($showData[style],"width"))) $style .= "$showData[style]";
        else $style = $showData[style];                
    }
    
    $str.= "style='$style' $valueStr >";

    if ($empty) {
        $str.= "<option value='0'";
        if (!$code) $str.= " selected='selected' ";
        $str.= ">$empty</option>";
    }


    foreach ($salutList as $salutCode => $salutData) {
        $str.= "<option value='$salutCode'";
        if ($code == $salutCode)  $str.= " selected='1' ";
        $str.= ">$salutData[name]</option>";
    }
    $str.= "</select>";
    return $str;

}



?>
