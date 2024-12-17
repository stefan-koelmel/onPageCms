<?php // charset:UTF-8
function cmsUser_getList($filter,$sort) {

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


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` ".$filterQuery." ".$sortQuery;
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($user = mysql_fetch_assoc($result)) {
        $res[] = php_clearQuery($user);

    }
    return $res;
}


function cmsUser_get($filter) {
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` ".$filterQuery." ".$sortQuery;
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


function cmsUser_getById($userId) {
    $userData = cmsUser_get(array("id"=>$userId));
    if (is_array($userData)) return $userData;
    $filterQuery = "WHERE `id` = $userId";
    $sortQuery = "";


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($anz == 0) {
        cms_errorBox("Benutzer nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        cms_errorBox("Mehrere Benutzer gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    $userData =  mysql_fetch_assoc($result);
    $userData = php_clearQuery($userData);
    return $userData;
}

function cms_user_getById($userId) {
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `id`=$userId";
    $result = mysql_query($query);
    if (!$result) {
        echo("Error in Query $query <br>");
        return 0;
    }

    $anz = mysql_num_rows($result);
    if ($anz==1) {
        $userData = mysql_fetch_assoc($result);
        return $userData;
    }
    if ($anz == 0) return "norFound";

    return "moreFound";
}

function cmsUser_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cms_user_getById($id);
        if ($existData) {
            return cmsUser_update($data);
        }
    }
    echo ("Not Found - no Id <br> ");

    $email = php_clearStr($data[email]);
    $existData = cmsUser_get(array("email"=>$email));
    if (is_array($existData)) {
        $data[id] = $existData[id];
        return cmsUser_update($data);
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
    $query = "INSERT `".$GLOBALS[cmsName]."_cms_user` SET ".$query;
    $result = mysql_query($query);
    echo ($query."<br>");
    if (!$result) {
        echo "Error in cmsUser_save $query <br>";
        return 0;
    }
    return 1;
}

function cmsUser_update($data) {
    $query = "";
    $id = $data[id];
    foreach ($data as $key => $value ) {
        switch ($key) {
            case "data" : break;
            default :
                $value = php_clearStr($value);
        }

        if ($value AND $key != "id") {
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";
        }
    }
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_user` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}



function cms_user_getInfo($userId,$showData) {
    $userData = cms_user_getById($userId);
    if (!is_array($userData)) {
        cms_errorBox("benutzer Daten nicht gefunden - $userData");
        return 0;
    }
    return $userData;

}


function cms_user_login($loginData) {
    if (!is_array($loginData)) return "noData";

    $loginUser = $loginData[userName];
    $loginPass = $loginData[password];

    if (strlen($loginUser)<6) return "shortUser";
    if (strlen($loginPass)<4) return "shortPass";
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `userName`='$loginUser' AND `password`='$loginPass'";
    $result = mysql_query($query);
    if (!$result) {
        echo("Error in Query $query <br>");
    } else {
        $anz = mysql_num_rows($result);
        if ($anz == 1) return (cms_user_doLogin($result));
        if ($anz == 0) {
            // Try eMail
            $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `email`='$loginUser' AND `password`='$loginPass'";
            $result = mysql_query($query);
            $anz = mysql_num_rows($result);
            if ($anz == 1) return (cms_user_doLogin($result));
            echo ("Gefunden mit eMail $anz <br>");

        } else {
            echo ("Gefunden $anz <br>");
        }
    }


    echo ("Try to Login User with $loginUser / $loginPass <br>");


    return 0;
}

function cms_user_doLogin($result) {
    $userData = mysql_fetch_assoc($result);
    
    $userId = $userData[id];

    // SET lastLogin
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_user` SET `lastLogin`='".date("y-m-d h:i:s")."'";
    // 0000-00-00 00:00:00'
    $firstLogin =$userData[first_log];
    if ($firstLogin == "0000-00-00 00:00:00") $query .= " , `first_log`=now()";
    $query .= " WHERE `id`= $userId";

    $result = mysql_query($query);
    if (!$result) {
        echo ("Error in Query $query <br>");
        return 0;
    }

    $_SESSION[userLevel] = $userData[userLevel];
    $_SESSION[showLevel] = $userData[userLevel];
    $_SESSION[userId]    = $userData[id];
    if ($userData[userLevel] > 6) {
        $_SESSION[editable] = 1;
    //    $_SESSION[edit] = 0;
    }
    else $_SESSION[editable] = 0;
    $_SESSION[edit] = 0;

    return 1;
}

function cms_user_logout($userId = 0) {
    if (!$userId) $userId = $_SESSION[userId];
    $_SESSION[userLevel] = 0;
    $_SESSION[showLevel] = 0;
    $_SESSION[userId]    = 0;
    $_SESSION[editable] = 0;
    $_SESSION[edit] = 0;
    $_SESSION[lastPages] = array();
    // show_array($_SESSION[history]);

    return 1;
}

function cms_user_getLevels() {
    $levelList = array();
    $levelList[0]=array("name"=>"normaler Besucher");
    $levelList[1]=array("name"=>"angemelderter Benutzer");
    $levelList[2]=array("name"=>"bestätigter Besucher");
    $levelList[7]=array("name"=>"Admin - Text");
    $levelList[8]=array("name"=>"Admin - CMS");
    $levelList[9]=array("name"=>"Super Admin");
    return $levelList;
}

function cmsUser_getUserLevel($level) {
     $res = "unbekannt";
     $levelList = cms_user_getLevels();
     if (is_array($levelList[$level])) {
         $res = $levelList[$level][name];
     }
     return $res;
}


function cmsUser_selectUserLevel($code,$dataName,$showData,$showFilter,$showSort) {
    $levelList = cms_user_getLevels();
    $str = "";
    $submit = "";
    if ($showData[submit]) $submit = "onChange='submit()'";
    $emptyStr = "Userlevel wählen";
    if ($showData["empty"]) $emptyStr = $showData["empty"];
    // echo ("Show Empt $emptyStr <br>");
    
    $str.= "<select name='$dataName' class='cmsSelectType' $submit style='min-width:200px;' value='$code' >";

    if ($emptyStr) {
        $selected = "";
        if (is_null($code)) {
            //  echo "is null $code <br>";
            $selected = "selected='1'";
        } 
        if ($code == "notSelected") {
            $selected = "selected='1'";#
            //echo ("code = notSelected<br>");
        }

        $str.= "<option value='notSelected' $selected >$emptyStr</option>";

    }


    $maxLevel = $showData[maxLevel];
    if (!$maxLevel) $maxLevel = $_SESSION[userLevel];

    foreach ($levelList as $levelCode => $levelData) {
        if ($levelCode <= $maxLevel) {
            $selected = "";
            if (!is_null($code) and $code!="notSelected") {
                // echo ("Compare $code with $levelCode <br>");
                if ($code == $levelCode) $selected = "selected='1' ";
            }
            $str.= "<option value='$levelCode' $selected >$levelData[name]</option>";
        }
    }
    $str.= "</select>";
    return $str;

}


function cms_user_salut() {
    $salutList = array();
    $salutList[1]=array("name"=>"Herr");
    $salutList[2]=array("name"=>"Frau");
    $salutList[3]=array("name"=>"Firma");
  
    return $salutList;
}

function cmsUser_getSalut($salut) {
     $res = "unbekannt";
     $salutList = cms_user_salut();
     if (is_array($salutList[$salut])) {
         $res = $salutList[$salut][name];
     }
     return $res;
}


function cmsUser_selectSalut($code,$dataName,$showData,$showFilter,$showSort) {
    $salutList = cms_user_salut();
    $str = "";
    //$str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$code' >";

    $empty ="Bitte wählen";
    if ($showData["empty"]) $empty = $showData["empty"];

    $str.= "<select name='$dataName' class='cmsSelectType' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $valueStr = "";
    if ($code) $valueStr = " value='$code'";
    $str.= "style='min-width:200px;' $valueStr >";

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

function cms_user_selectlevel($level,$maxLevel,$dataName,$addData=array()) {
    $levelList = cms_user_getLevels();
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$type' >";
    foreach($addData as $key => $value) {
        $str.= "$key ='$value' ";
    }
    $str .= " >";
    


    foreach ($levelList as $levelCode => $levelData) {
        if ($levelCode <= $maxLevel) {
            $str.= "<option value='$levelCode'";
            if ($level == $levelCode)  $str.= " selected='1' ";
            $str.= ">$levelData[name]</option>";
        }
    }
    $str.= "</select>";
    return $str;
}

?>
