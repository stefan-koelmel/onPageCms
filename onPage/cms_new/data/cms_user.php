<?php // charset:UTF-8
function cmsUser_getList($filter,$sort,$out=null) {

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
    if ($out == "out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($user = mysql_fetch_assoc($result)) {
        switch ($out) {
            case "assoId" :
                $userId = $user[id];
                $res[$userId] = php_clearQuery($user);
                break;
            default :
                $res[] = php_clearQuery($user);
        }
        

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


function cmsUser_find($nameOrEmail) {
    // by eMail 
    $userData = cmsUser_get(array("email"=>$nameOrEmail));
    if (is_array($userData)) {
        return $userData;
    }
    
    // by userName
    $userData = cmsUser_get(array("userName"=>$nameOrEmail));
    if (is_array($userData)) {
        return $userData;
    }
    return 0;
    
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
    if (!$result) {
        echo "Error in cmsUser_save $query <br>";
        return 0;
    }
    $insertId = mysql_insert_id();
    return $insertId;
}

function cmsUser_update($data) {
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
    
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_user` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}


 function cmsUser_lastMod() {
    $lastMod = date("Y-m-d H:i:s");
    return $lastMod;      
 }

function cmsUser_changeLog($mode,$old_changeLog=0) {
    if (!is_string($old_changeLog)) $old_changeLog = $_POST[saveData][changeLog];

    if ($old_changeLog) {
        // echo ("<h1>Alter ChangeLog $old_changeLog</h1>");
        $changeLog = date("Y-m-d H:i:s").",".$_SESSION[userId].",".$mode;

        $changeList = explode("|",$old_changeLog);
        for ($i=0;$i<count($changeList);$i++) {
            if ($i<5) {
                $changeLog .= "|".$changeList[$i];
               
            }
        }
    } else {
        $changeLog = date("Y-m-d H:i:s").",".$_SESSION[userId].",".$mode;
    }
    return $changeLog;
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
    $userLevel = $loginData[userLevel];

    if (strlen($loginUser)<6) return "shortUser";
    if (strlen($loginPass)<4) return "shortPass";
    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `userName`='$loginUser' AND `password`='$loginPass'";
    if ($userLevel) $query .= " AND `userLevel` >= $userLevel";
    $result = mysql_query($query);
    if (!$result) {
        echo("Error in Query $query <br>");
    } else {
        $anz = mysql_num_rows($result);
        if ($anz == 1) return (cms_user_doLogin($result));
        if ($anz == 0) {
            // Try eMail
            $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_user` WHERE `email`='$loginUser' AND `password`='$loginPass'";
            if ($userLevel) $query .= " AND `userLevel` >= $userLevel";
            $result = mysql_query($query);
            $anz = mysql_num_rows($result);
            if ($anz == 1) return (cms_user_doLogin($result));
            // echo ("Gefunden mit eMail $anz <br>");

        } else {
            // echo ("Gefunden $anz <br>");
        }
    }


    // echo ("Try to Login User with $loginUser / $loginPass <br>");


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
    cms_page_destroy_session();
    if ($userData[userLevel] > 6) {
        $_SESSION[editable] = 1;
    //    $_SESSION[edit] = 0;
    }
    else $_SESSION[editable] = 0;
    $_SESSION[edit] = 0;

    return 1;
}


function cmsUser_doLogin($userData) {
    // echo ("cmsUser_doLogin($userData)<br>");
    $userId = $userData[id];

    // SET lastLogin
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_user` SET `lastLogin`='".date("y-m-d h:i:s")."'";
    // 0000-00-00 00:00:00'
    $firstLogin = $userData[first_log];
    if ($firstLogin == "0000-00-00 00:00:00") $query .= " , `first_log`=now()";
    $query .= " WHERE `id`= $userId";
    
    // echo ("Query $query <br>");
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

function cmsUser_checkMailAdress($email) {
    $noMailStr = "Kein gültiges eMail Format";

    if (strlen($email)<9) return $noMailStr."(length)";
    
    $atPos = strpos($email,"@");
    if ($atPos<2) return $noMailStr."(at))";
    
    $dotPos = strpos($email,".",$atPos+4);
    
    if (!$dotPos) return $noMailStr."(.)";
    
    // echo ("dotPos ".($dotPos+2)." -> ".strlen($email)." <br>");
    if (($dotPos+2) >= strlen($email)) return $noMailStr."(de)";
    return 0;
}
    
        
    

function cms_user_logout($userId = 0) {
    if (!$userId) $userId = $_SESSION[userId];
    $_SESSION[userLevel] = 0;
    $_SESSION[showLevel] = 0;
    $_SESSION[userId]    = 0;
    $_SESSION[editable] = 0;
    $_SESSION[edit] = 0;
    $_SESSION[lastPages] = array();
    //unset($_SESSION[pageState]);
    cms_page_destroy_session();
    // show_array($_SESSION[history]);

    return 1;
}

function cms_user_getLevels() {
    $levelList = array();
    $levelList[0]=array("name"=>"normaler Besucher");
    $levelList[1]=array("name"=>"angemelderter Benutzer");
    $levelList[2]=array("name"=>"bestätigter Besucher");
    $levelList[3]=array("name"=>"Benutzerauswahl");
    $levelList[7]=array("name"=>"Admin - Text");
    $levelList[8]=array("name"=>"Admin - CMS");
    $levelList[9]=array("name"=>"Super Admin");
    $levelList[3]=array("name"=>"Benutzerauswahl");
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
    
    $id = "";
    if ($showData[id]) $id = "id='$showData[id]'";
    $emptyStr = "Userlevel wählen";
    if ($showData["empty"]) $emptyStr = $showData["empty"];
    
    $style = "min-width:200px;";
    if ($showData[width]) $style = "width:".$showData[width]."px;";
    // echo ("Show Empt $emptyStr <br>");
    
    $str.= "<select $id name='$dataName' class='cmsSelectType' $submit style='$style' value='$code' >";

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


function cmsUser_selectSalut($code,$dataName,$showData,$showFilter=0,$showSort=0) {
    if (!is_array($showData)) $showData = array();
    $salutList = cms_user_salut();
    if ($showData[showList]) $salutList = $showData[showList];
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

function cmsUser_countryList() {
    $countryList = array();
    $countryList["D"]=array("name"=>"Deutschland");
    $countryList["CH"]=array("name"=>"Schweiz");
    $countryList["AT"]=array("name"=>"Österreich");
    $countryList["FR"]=array("name"=>"Frankreich");
    $countryList["NL"]=array("name"=>"Niederlande");
    $countryList["BE"]=array("name"=>"Belgien");
    $countryList["DE"]=array("name"=>"Dänemark");
  
    return $countryList;
}

function cmsUser_getCountryName($countryCode) {
    $contryList = cmsUser_countryList();
    if ($contryList[$countryCode]) {
        if (is_string($contryList[$countryCode])) return $contryList[$countryCode];
        if (is_array($contryList[$countryCode])) {
            return $contryList[$countryCode][name];
        }
    }
    return ""; //unkown County $countryCode";
}


function cmsUser_selectCounty($code,$dataName,$showData,$showFilter,$showSort) {
    $countryList = cmsUser_countryList();
    $str = "";
    //$str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$code' >";

    $empty ="Land wählen";
    if ($showData["empty"]) $empty = $showData["empty"];

    $class = "cmsSelectType cmsSelectCountry";
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

    foreach ($countryList as $countryCode => $countryData) {
        $str.= "<option value='$countryCode'";
        if ($code == $countryCode)  $str.= " selected='1' ";
        $str.= ">$countryData[name]</option>";
    }
    $str.= "</select>";
    return $str;
}

function cms_user_selectlevel($level,$maxLevel,$dataName,$addData=array()) {
    $levelList = cms_user_getLevels();
    $str = ""; //max=$maxLevel lev=$level ";
    $str.= "<select name='$dataName' class='cmsSelectType' style='min-width:200px;' value='$level' ";
    foreach($addData as $key => $value) {
        $str.= "$key ='$value' ";
    }
    $str .= " >";
    
    $showMaxLevel = $maxLevel;
    
    $showMaxLevel = 9;
    
    
    

    foreach ($levelList as $levelCode => $levelData) {
        if ($levelCode <= $showMaxLevel) {
            $str.= "<option value='$levelCode'";
            if ($level == $levelCode)  $str.= " selected='1'";
            if ($levelCode > $maxLevel) $str.= " disabled='disabled'";
            $str.= ">$levelData[name]</option>";
        }
    }
    $str.= "</select>";
    return $str;
}

function cms_user_SelectUser($user,$maxLevel,$dataName,$addData=array()) {
    $filter = array("userLevel"=>">=1");
    $sort   = "nName";
    $userList = cmsUser_getList($filter,$sort,"assoId");
//    foreach ($userList as $userId => $userData) {
//        echo ("$userId = $userData <br>");
//        echo (" --> $userData[nName] $userData[vName]<br>");
//
//    }
    $str = "";
    $str.= "<select name='$dataName' class='cmsSelectUser' style='min-width:200px;' value='$type' >";
    foreach($addData as $key => $value) {
        $str.= "$key ='$value' ";

    }

    $str .= " >";



    foreach ($userList as $userId => $userData) {
        // if ($levelCode <= $maxLevel) {
            $str.= "<option value='$userId'";
            if ($user == $userId)  $str.= " selected='1' ";
            $str .= ">";
            $str .= "$userData[nName], $userData[vName], userName:$userData[userName] id:$userId";
            $str.= "</option>";
        // }
    }
    $str.= "</select>";
    return $str;
}

function cmsUser_selectUserList($data,$maxUserLevel,$dataName) {

    $filter = array("userLevel"=>"<=".$maxUserLevel);
    $sort   = "nName";

    $userList = cmsUser_getList($filter,$sort,"assoId");
    
    // foreach ($data as $key => $value ) echo ("key $key = $value <br>");
    if (!is_array($data)) $data = array();
    
    $allowed = $data[allowedUser]; //"4";
    $forbidden = $data[forbiddenUser]; //"1,2";

    //  echo ("allowed = $allowed / forbidden = $forbidden <br />");
    $forbiddenList = explode("|",$forbidden);
    $allowedList = explode("|",$allowed);


    $str = div_start_str("cmsSelectUserList");

    
    $str .= div_start_str("cmsSelectUser_allowed","float:left;width:200px;");
    $str .= "Erlaubte Benutzer<br />";
    $str .= "<input type='hidden____' class='cmsSelectUser_allowed_text' value='$allowed' name='".$dataName."[allowedUser]' /><br>";
    $str .= "<select class='cmsUserList' name='helper_allowed' style='width:200px;' multiple='multiple'>";
    for ($i=1;$i<count($allowedList)-1;$i++) {
        $userId = $allowedList[$i];
        if (is_array($userList[$userId])) {
            $str .= "<option value='$userId' >";
            $str .= $userList[$userId][vName]." ".$userList[$userId][nName]." (id:$userId)";
            $str .= "</option>";
        }
    }

//
//    $str .= "<option value='1' >User1</option>";
//    $str .= "<option value='2' >User2</option>";
//    $str .= "<option value='3' selected='selected' value='3' >User3</option>";
//    $str .= "<option value='4' >User4</option>";
    $str .= "</select><br/>";
    $str .= "<div class='cmsJavaButton cmsSecond cmsRemoveUser'>entfernen</div>";
    $str .= "<div class='cmsJavaButton cmsSecond cmsAddUser'>hinzufügen</div>";
    $str .= div_end_str("cmsSelectUser_allowed");

    $str .= div_start_str("cmsSelectUser_forbidden","float:left");
    $str .= "Ausgeschlossene Benutzer<br />";
    $str .= "<input type='hidden____' class='cmsSelectUser_forbidden_text' value='$forbidden' name='".$dataName."[forbiddenUser]' /><br>";
    $str .= "<select class='cmsUserList' name='helper_forbidden' style='width:200px;' multiple='multiple'>";
    for ($i=1;$i<count($forbiddenList)-1;$i++) {
        $userId = $forbiddenList[$i];
        if (is_array($userList[$userId])) {
            $str .= "<option value='$userId' >";
            $str .= $userList[$userId][vName]." ".$userList[$userId][nName]." (id:$userId)";
            $str .= "</option>";
        }
    }
    $str .= "</select><br/>";
    $str .= "<div class='cmsJavaButton cmsSecond cmsRemoveUser'>entfernen</div>";
    $str .= "<div class='cmsJavaButton cmsSecond cmsAddUser'>hinzufügen</div>";

    $str .= div_end_str("cmsSelectUser_forbidden");

    $str .= div_end_str("cmsSelectUserList","before");
    
    
//    foreach ($userList as $userId => $userData) {
//        echo ("$userId = $userData <br>");
//        echo (" --> $userData[nName] $userData[vName]<br>");
//
// 
    $str.= "<select name='helper' class='cmsSelectUser' size='5' style='width:400px;' value='$type' >";
    foreach ($userList as $userId => $userData) {
        // if ($levelCode <= $maxLevel) {
            $str.= "<option value='$userId'";
            if ($user == $userId)  $str.= " selected='1' ";
            $str .= ">";
            $str .= "$userData[vName] $userData[nName] (id:$userId)";
            $str.= "</option>";
        // }
    }
    $str.= "</select>";
   





    return $str;


}

function cmsUser_getConfirmCode($name_laenge=20) {
    $zeichen = "abcedfghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRTSUVWXYZ0123456789";
    $name_neu = "";

    mt_srand ((double) microtime() * 1000000);
    for ($zi = 0; $zi < $name_laenge; $zi++ ) {
        $name_neu .= $zeichen{mt_rand (0,strlen($zeichen))};
    }
    return $name_neu;
}

function cmsUser_checkData($saveData,$showList=array(),$mode="edit") {
    $error = array();

    foreach ($showList as $key => $value) {
        $show = 1;
        $need = $value[need];
        $view = $value[view];
        $name = $value[name];
        $length = 0;

        $value = $saveData[$key];
        if ($need) {
            if (!$value) {
                $ok = 0;
                switch ($key) {
                    case "name" :
                        $vName = $saveData[vName];
                        $nName = $saveData[nName];
                        if ($vName AND $nName) {
                            // echo ("CHECK $nName $vName <br>");
                            $ok = 1;
                        }
                        break;
                }
                if ($ok) {

                } else {
                // empty but need
                    $error[$key] = "Hier ist eine Eingabe erforderlich";
                }
                // echo ($error[$key]);
            } else {
                switch ($key) {
                    case "userName" :
                        $length = 6;
                        break;
                    case "password" :
                        $length = 6;
                        break;


                }

                if ($length) {
                    if (strlen($value) < $length) {
                        $error[$key] .= "Die Eingabe muss mindestens $length Zeichen haben";
                    }
                }

                if (!$error[$key]) {
                    switch ($key) {
                        case "userName" :
                            if ($mode == "new") {
                                $findUser = cmsUser_find($value);
                                if (is_array($findUser)) {
                                    $error[$key] .= "Der Benutzername '$value' ist bereits vergeben";
                                }
                            }
                            break;

                        case "email" :
                            $res = cmsUser_checkMailAdress($value);
                            if ($res) $error[$key] .= $res;
                            if ($mode == "new") {
                                $findUser = cmsUser_find($value);
                                if (is_array($findUser)) {
                                    $error[$key] .= "Die eMail-Adresse '$value' ist bereits vergeben";
                                }
                            }
                            break;
                    }
                }


            }
        }

    }

    if (count($error)) return $error;
    return 0;
}

function cmsUser_convert($saveData,$showList=array()) {
    foreach ($saveData as $key => $value) {
        if (is_array($value)) {
            // echo ("ARRAY in $key <br>");
            // show_array($value);
            // echo ("showData<br>");
            //show_array($showList[$key]);

            switch ($key) {
                case "phone" :
                    $newStr = "";
                    foreach ($value as $key2 => $value2) {
                        if ($newStr) $newStr.= "|";
                        $newStr .= $value2;
                    }
                    // echo ("NEW STRING for $key = '$newStr' <br>");
                    $saveData[$key] = $newStr;
                    break;
                case "fax" :
                    $newStr = "";
                    foreach ($value as $key2 => $value2) {
                        if ($newStr) $newStr.= "|";
                        $newStr .= $value2;
                    }
                    // echo ("NEW STRING for $key = '$newStr' <br>");
                    $saveData[$key] = $newStr;
                    break;
                case "mobil" :
                    $newStr = "";
                    foreach ($value as $key2 => $value2) {
                        if ($newStr) $newStr.= "|";
                        $newStr .= $value2;
                    }
                    // echo ("NEW STRING for $key = '$newStr' <br>");
                    $saveData[$key] = $newStr;
                    break;

            }

        }
        switch ($key) {
            case "name" :
                $posLeer = strpos($value," ");
                if ($posLeer) {
                    $vName = substr($value,0,$posLeer);
                    $nName = substr($value,$posLeer+1);
                } else {
                    $vName = "";
                    $nName = $value;
                }
                //echo ("SPliT NAME '$value' => '$vName' '$nName' <br> ");
                $saveData[vName] = $vName;
                $saveData[nName] = $nName;
                unset($saveData[$key]);
                break;

            case "streetSingle" :
                $posLeer = strpos($value," ");
                if ($posLeer) {
                    $street = substr($value,0,$posLeer);
                    $streetNr = substr($value,$posLeer+1);
                } else {
                    $street = $value;
                    $streetNr = "";
                }
                // echo ("SPliT Street '$value' => '$street' '$streetNr' <br> ");
                $saveData[street] = $street;
                $saveData[streetNr] = $streetNr;
                unset($saveData[$key]);
                break;

            case "citySingle" :
                $posLeer = strpos($value," ");
                if ($posLeer) {
                    $plz = substr($value,0,$posLeer);
                    $city = substr($value,$posLeer+1);
                } else {
                    $city = $value;
                    $plz = "";
                }
                // echo ("SPliT CITY '$value' => '$plz' '$city' <br> ");
                $saveData[plz] = $plz;
                $saveData[city] = $city;
                unset($saveData[$key]);
                break;


        }
        // echo ("convert $key => $value <br>");
    }

    return $saveData;
}


function cmsUser_showInput($key,$value,$dataName,$styleList,$showList=array(),$errorList=array()) {
    switch ($key) {
        case "userName" :
            $res = cmsUser_showInput_userName($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "password" :
            $res = cmsUser_showInput_password($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "email" :
            $res = cmsUser_showInput_email($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "salut" :
            $res = cmsUser_showInput_salut($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "name" :
            $res = cmsUser_showInput_name($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "company":
            $res = cmsUser_showInput_company($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "street" :
            $res = cmsUser_showInput_street($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "city" :
            $res = cmsUser_showInput_city($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
            
        case "country" :
            $res = cmsUser_showInput_country($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "phone" : 
            $res = cmsUser_showInput_phone($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        case "fax" : 
            $res = cmsUser_showInput_phone($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        case "mobil" : 
            $res = cmsUser_showInput_phone($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        case "url" :
            $res = cmsUser_showInput_url($key, $value, $dataName, $styleList, $showList, $errorList);
            break;
        
        default :
            $res = "unkown $key in cmsUser_showInput <br />";
    }
    return $res;
    
}

function cmsUser_showInput_userName($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
   //      $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[name]' style='width:".$dataWidth."px;' value='$name' /><br />";
           
    $str .= "<input type='text'  class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[$key]' style='width:".$dataWidth."px;' value='$value' />";
    $str .= "<br />";
    
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}

function cmsUser_showInput_email($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
   //      $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[name]' style='width:".$dataWidth."px;' value='$name' /><br />";
           
    $str .= "<input type='text'  class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[$key]' style='width:".$dataWidth."px;' value='$value' />";
    $str .= "<br />";
    
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


function cmsUser_showInput_password($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
   //      $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[name]' style='width:".$dataWidth."px;' value='$name' /><br />";
           
    $str .= "<input type='password'  class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[$key]' style='width:".$dataWidth."px;' value='$value' />";
    $str .= "<br />";
    
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}

function cmsUser_showInput_salut($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
    if (!$view) $view = "select";
    switch ($view) {
        case "select" :
            $w1 = floor($dataWidth *0.5);
            if ($w1 < 70) $w1 = 70;
            if ($w1 > 200) $w1 = 200;
            $showData[style]="width:".$w1."px;";
            $str .= cmsUser_selectSalut($value, $dataName."[salut]", $showData, $showFilter, $showSort)."<br />";
            break;
    }
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


function cmsUser_showInput_name($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    // $str .= span_text_str("$name:",$spanStyle);
    if (!$view) $view = "double";
    switch ($view) {
        case "single" :
            $str .= span_text_str("Name:",$spanStyle);
            $name = $value[vName];
            if ($value[nName]) {
                if ($name) $name .= " ";
                $name .= $value[nName];
            }
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[name]' style='width:".$dataWidth."px;' value='$name' /><br />";
            // $str .= "<input type='text' name='saveData[nName]' style='width:100px;' value='$saveData[vName]' /><br />";
            break;
        case "double" :
            $w1 = floor(($dataWidth-$inputAbs) *0.5);
            $w2 = $dataWidth - $w1 - $inputAbs;
            $str .= span_text_str("Vorname / Nachname:",$spanStyle);
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_v".$key."' name='".$dataName."[vName]' style='width:".$w1."px;' value='$value[vName]' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_n".$key."' name='".$dataName."[nName]' style='width:".$w2."px;' value='$value[nName]' /><br />";
            break;
    }       
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


function cmsUser_showInput_company($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;

    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;

    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }

   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
   //      $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[name]' style='width:".$dataWidth."px;' value='$name' /><br />";

    $str .= "<input type='text'  class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[company]' style='width:".$dataWidth."px;' value='$value' />";
    $str .= "<br />";

    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}
           
function cmsUser_showInput_street($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
    if (!$view) $view = "double";
    
    switch ($view) {
        case "single" :
            $str .= span_text_str("Straße:",$spanStyle);
            $name = $value[street];
            if ($value[streetNr]) {
                if ($name) $name .= " ";
                $name .= $value[streetNr];
            }
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[streetSingle]' style='width:".$dataWidth."px;' value='$name' /><br />";
            break;
        case "double" :
            $w2 = floor($dataWidth*0.1);
            if ($w2 < 30) $w2 = 30;
            $w1 = $dataWidth - $w2 - $inputAbs;
            $str .= span_text_str("Straße / Hausnummer:",$spanStyle);
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[street]' style='width:".$w1."px;' value='$value[street]' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Nr' name='".$dataName."[streetNr]' style='width:".$w2."px;' value='$value[streetNr]' /><br />";
            break;
    }       
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}

function cmsUser_showInput_city($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
    if (!$view) $view = "double";
    switch ($view) {
        case "single" :
            $str .= span_text_str("Ort:",$spanStyle);
            $name = $value[plz];
            if ($value[city]) {
                if ($name) $name .= " ";
                $name .= $value[city];
            }
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[citySingle]' style='width:".$dataWidth."px;' value='$name' /><br />";
            break;
        case "double" :
            $w1 = floor($dataWidth*0.1);
            if ($w1 < 40) $w1 = 40;
            $w2 = $dataWidth - $w1 - $inputAbs;
            $str .= span_text_str("PLZ / Ort:",$spanStyle);
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[plz]' style='width:".$w1."px;' value='$value[plz]' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Nr' name='".$dataName."[city]' style='width:".$w2."px;' value='$value[city]' /><br />";
            break;
    }       
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


function cmsUser_showInput_country($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
    if (!$view) $view = "text";
    $str .= span_text_str("$name:",$spanStyle);
    switch ($view) {
        case "text" :
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_countryText' name='".$dataName."[country]' style='width:".$dataWidth."px;' value='$value' />";
            // $str .= "<input type='text' name='saveData[nName]' style='width:100px;' value='$saveData[vName]' /><br />";
            break;
        case "select" :
            $showData[style]="width:".($dataWidth+4)."px;";
            $showData["class"] = "cmsUserInput cmsUserInput_countrySelect";
            $str .= cmsUser_selectCounty($value, "".$dataName."[country]", $showData, $showFilter, $showSort)."<br />";
            break;
    }
    
   
    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


function cmsUser_showInput_url($key, $value, $dataName, $styleList, $showList, $errorList) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    // $str .= span_text_str("$name:",$spanStyle);
    $phoneSplit = explode("|",$value);
    if (!$view) $view = "single";
    // $str .= "VIEW = $view  / $name <br>";
    switch ($view) {
        case "single" :
            $str .= span_text_str("Webseite:",$spanStyle);
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."' name='".$dataName."[url]' style='width:".$dataWidth."px;' value='$value' /><br />";            
            break;
        case "double" :
            $phone1 =$phoneSplit[0];
            $phone2 = "";
            for($i=1;$i<count($phoneSplit);$i++) {
                if ($i>1) $phone2 .= " ";
                $phone2 .= $phoneSplit[$i];
            }
            $w1 = floor($dataWidth * 0.3);
            $w2 = $dataWidth - $w1 - $inputAbs;
            
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Pre' name='".$dataName."[mobil][pre]' style='width:".$w1."px;' value='$phone1' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Tel' name='".$dataName."[mobil][tel]' style='width:".$w2."px;' value='$phone2' /><br />";
            break;
       

        default :
           
           //  $str .= "Unkown ViewMode in 'show_editeContact_input $view<br>";
    }

    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}
/*
 $show = 0;
        if (is_array($showList[$key])) {
            $show = 1;
            $need = $showList[$key][need];
            $view = $showList[$key][view];
            // show_array($showList[$key]);
        }

        if ($show) {
            $spanStyle = $this->get_infoStyle();
            $error = $errorList[$key];
            if ($need) {
                $spanStyle["class"] .= " cmsUser_needSpan";
            }

            if ($error) {
                $str .= div_start_str("cmsUserError");
                $str .= span_text_str($error,$errorStyle);
            }
            switch ($view) {
                case "single" :
                    $str .= span_text_str("Webseite:",$spanStyle);
                    $str .= "<input type='text' name='saveData[url]' style='width:200px;' value='$saveData[url]' /><br />";
                    // $str .= "<input type='text' name='saveData[nName]' style='width:100px;' value='$saveData[vName]' /><br />";
                    break;
                case "double" :
                    $str .= span_text_str("Webseite / URL:",$spanStyle);
                    $str .= "<input type='text' name='saveData[urlTitle]' style='width:80px;' value='$saveData[urlTitle]' /><br />";
                    $str .= "<input type='text' name='saveData[url]' style='width:120px;' value='$saveData[url]' /><br />";
                    break;
                default :
                    $str .= span_text_str("Webseite:",$spanStyle);
                    $str .= "<input type='text' name='saveData[url]' style='width:200px;' value='$saveData[url]' /><br />";

            }

            if ($error) {
                $str .= div_end_str("cmsUserError");
            }
        }
 */

function cmsUser_showInput_phone($key, $value, $dataName, $styleList=array(), $showList=array(), $errorList=array()) {
    $show = 0;
    if (is_array($showList[$key])) {
        $show = 1;
        $need = $showList[$key][need];
        $view = $showList[$key][view];
        $name = $showList[$key][name];
        // show_array($showList[$key]);
    } else {
        $name = "'$key'";
    }
    $str = "";
    if (!$show) return $str;
   
    $spanStyle = $styleList[infoStyle]; //$this->get_infoStyle();
    $dataStyle = $styleList[dataStyle];
    $errorStyle = $styleList[errorStyle];
    $dataWidth = $styleList[dataWidth];
    if (!$dataWidth) $dataWidth = 200;
    $inputAbs = $styleList[inputAbs];
    if (!$inputAbs) $inputAbs = 10;
    
    $error = $errorList[$key];
    if ($need) {
        $spanStyle["class"] .= " cmsUser_needSpan";
    }

    if ($error) {
        $str .= div_start_str("cmsUserError");
        $str .= span_text_str($error,$errorStyle);
    }
    
   //  show_array($showList);
    $str .= span_text_str("$name:",$spanStyle);
    $phoneSplit = explode("|",$value);
    if (!$view) $view = "double";
    switch ($view) {
        case "single" :
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Pre' name='".$dataName."[$key]' style='width:".$dataWidth."px;' value='$value' /><br />";
            break;
        case "double" :
            $phone1 =$phoneSplit[0];
            $phone2 = "";
            for($i=1;$i<count($phoneSplit);$i++) {
                if ($i>1) $phone2 .= " ";
                $phone2 .= $phoneSplit[$i];
            }
            $w1 = floor($dataWidth * 0.3);
            $w2 = $dataWidth - $w1 - $inputAbs;
            
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Pre' name='".$dataName."[$key][pre]' style='width:".$w1."px;' value='$phone1' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Tel' name='".$dataName."[$key][tel]' style='width:".$w2."px;' value='$phone2' /><br />";
            break;
        case "all" :
            $phone1 = $phoneSplit[0];
            $phone2 = $phoneSplit[1];
            $phone3 = "";
            for($i=2;$i<count($phoneSplit);$i++) {
                if ($i>2) $phone3 .= " ";
                $phone3 .= $phoneSplit[$i];
            }
            
            $w1 = floor($dataWidth * 0.1);
            $w2 = floor($dataWidth * 0.3);
            $w3 = $dataWidth - $w1 - $w2 - (2*$inputAbs);
            
            // echo ("PHONE '$phone1' '$phone2' '$phone3' <br>");
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Cou' name='".$dataName."[$key][cou]' style='width:".$w1."px;' value='$phone1' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Pre' name='".$dataName."[$key][pre]' style='width:".$w2."px;' value='$phone2' />";
            $str .= "<input type='text' class='cmsUserInput cmsUserInput_".$key."Tel' name='".$dataName."[$key][tel]' style='width:".$w3."px;' value='$phone3' /><br />";
            break;

        default :
           $str .= "Unkown ViewMode in 'show_editeContact_input $view<br>";
    }

    if ($error) {
        $str .= div_end_str("cmsUserError");
    }
    return $str;
}


?>
