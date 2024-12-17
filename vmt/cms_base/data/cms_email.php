<?php // charset:UTF-8

function cmsEmail_getList($filter,$sort) {
  
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
                if ($value != "-") {
                    // echo ("Filter Email $key  '$value' <br>");
                    if ($filterQuery != "") $filterQuery .= " AND ";


                    switch ($value[0]) {
                        case ">" :
                            $filterQuery .= "`$key`$value";
                            break;
                        case "%" :
                            $filterQuery .= "`$key`LIKE '$value'";
                            break;

                        case "<" :
                            $filterQuery .= "`$key`$value";
                            break;
                        default :
                            switch ($key) {
                                case "category" :  // multiselect from category
                                    if ($value) {
                                        $filterQuery .= " (`$key` = '$value' OR `$key` LIKE  '%|$value|%') ";
                                    } else {
                                         $filterQuery .= "`$key`LIKE'0'";
                                    }
                                    break;
                                case "show" :
                                    $filterQuery .= "`$key`=$value";
                                    break;
                                default :
                                    $filterQuery .= "`$key`='$value'";

                            }
                    }

                }
                
            }
            if ($filterQuery) $filterQuery = "WHERE ".$filterQuery;
        }

        switch ($filter) {
            case "new" :
                $filterQuery = "WHERE `new` = 1";
        }

       
    } else {
        $filterQuery = "WHERE `show` = 1";
    }


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
    // echo ("Query $query <br>");
    $result = mysql_query($query);
    $res = array();
    while ($email = mysql_fetch_assoc($result)) {
        if ($email[data]) {
            $data = str2Array($email[data]);
            $email[data] = array();
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $email[data][$key] = php_unclearStr($value);
                }
            }
        }
        $res[] = $email;

    }
    return $res;
}

function cmsEmail_selectEmail_auto($code,$dataName,$showData,$filter,$sort) {
    global $cmsName,$cmsVersion;
    $emailName = $code;
    $showName = "";

    if ($code>0) {
        $categoryData = cmsEmail_get(array("id"=>$code));
        if (is_array($categoryData)) $showName = $categoryData[name];
    }

    $url = "/cms_".$cmsVersion."/getData/email.php";
    $url .= "?cmsVersion=$cmsVersion&cmsName=$cmsName";

    $class = "cmsEditEmailAuto";
    if (is_array($showData)) {
        foreach ($showData as $key => $value) {
            switch ($key) {
                case "style"   : $style = $value; break;
                case "class"   : $class .= " ".$value; break;
                case "submit"  : break;
                case "empty" : break;
                case "content" : $emailName = $value; break;
                default :
                    echo ("$key = $value <br>");
            }
        }
    }
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "mainCat" : $url.= "&mainCat=$value"; break;
                case "show"    : if ($value!=1) $url .= "&show=0"; break;
                
                default :
                    echo ("$key = $value <br>");
            }
        }
    }


    $str = "";
    $submit = "";
    if ($showData[submit]) $submit .= "onChange='submit()'";
    // echo ("input type='text' class='$class' $submit style='$style' name='$dataName' id='queryEmail' url='$url' value='$code'<br>");
    $str .= "<input type='text' class='$class' $submit style='$style' name='$dataName' id='queryEmail' url='$url' value='$emailName' />";

    return $str;

}

function cmsEmail_selectEmail($code,$dataName,$showData,$filter,$sort) {
    $companyList = cmsEmail_getList($filter,$sort);

    $str = "";

    $empty ="Bitte wählen";
    if ($showData["empty"]) $empty = $showData["empty"];

    $str.= "<select name='$dataName' class='cmsSelectType' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $str.= "style='min-width:200px;' value='$code' >";

    if ($empty) {
        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";
        $str.= ">$empty</option>";
    }

    for($i=0;$i<count($companyList);$i++) {
        $companyId = $companyList[$i][id];
        $companyName = $companyList[$i][name];
         $str.= "<option value='$companyId'";
         if ($code == $companyId)  $str.= " selected='1' ";
         $str.= ">$companyName</option>";
    }
    $str.= "</select>";
    return $str;


}

function cmsEmail_getById($companyId) {
    $filterQuery = "WHERE `id` = $companyId";
    $sortQuery = "";


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
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
    $emailData =  mysql_fetch_assoc($result);
    //echo ("EmailData = $emailData[data]<br>");
    // while ($str[0] != "a") $str = substr($str,1);
    //echo ("1.Zeichen ".$emailData[data][0]."<br>");
    $emailData[data] = str2Array($emailData[data]);
    //echo ("EmailData = $emailData[data]<br>");
    return $emailData;
}




function cmsEmail_getByName($emailName,$mainEmail) {

    $filterQuery = "WHERE `name` LIKE '$emailName'";
    if ($mainEmail) $filterQuery .= " AND `mainCat` = $mainEmail";
    $sortQuery = "";

    // SELECT * FROM `klappeAuf_cms_email` WHERE `name` LIKE 'Lesung' AND `mainCat` = 1
    //SELECT * FROM `klappeAuf_cms_email` WHERE `name` LIKE `Lesung` AND `mainCat` = 1


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
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
    $emailData =  mysql_fetch_assoc($result);
    $emailData[data] = str2Array($emailData[data]);

    return $emailData;
}

function cmsEmail_get($filter) {
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

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
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
    $emailData =  mysql_fetch_assoc($result);
    $emailData = php_clearQuery($emailData);
    
    return $emailData;
}


function cmsEmail_existName($emailName,$category) {
    echo ("cmsEmail_existName($emailName,$category)<br>");
    $filterQuery = "WHERE `name` LIKE '$emailName'";
    if ($category) $filterQuery .= " AND `category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
    echo ($query."<br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}


function cmsEmail_existID($emailId,$category) {

    $filterQuery = "WHERE `id` = $emailId";
    if ($category) $filterQuery .= " AND `$category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_email` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}

function cmsEmail_save($data) {
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        // echo ("id exist $id <br>");
        $existData = cmsEmail_existID($id);
        if ($existData) {
            return cmsEmail_update($data);
        }
    }
    // echo ("Not Found - no Id <br> ");
   
    $name = php_clearStr($data[name]);
    $category = $data[category];
    $existData = cmsEmail_get(array("name"=>$name,"category"=>$data[category]));
    if (is_array($existData)) {
        // echo ("Found with Name <br>");
        $data[id] = $existData[id];
        return cmsEmail_update($data);
    }

    // a:1:{s:9:"imageFile";s:16:"AlterBrauhof.jpg";}

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
    $query = "INSERT `".$GLOBALS[cmsName]."_cms_email` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in cmsEmail_save $query <br>";
        return 0;
    }
    return 1;
}

function cmsEmail_update($data) {
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
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_email` SET ".$query." WHERE `id` = $id ";
    // echo ("Query $query<br>");
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}


function cmsEmail_convertText($text,$data) {
    foreach ($data as $key => $value) {
        switch ($key) {
            case "salut" :
                $value = cmsUser_getSalut($value);
                break;
            case "message" :
                $key = "message";
                // $value = "SALUTATION = $value";
                break;
        }
        $searchStr = "#".$key."#";
        $text = str_replace($searchStr, $value, $text);
    }

    return $text;
}

function cmsEmail_sendMail($email,$subject,$text) {
    if (!$subject) $subject = "Nachricht von '".$GLOBALS[cmsName]."'";
    // echo ("cmsEmail_sendMail($email,$subject,$text)<br>");

    $header = 'From: Schaufenster Karlsruhe' . "\r\n" .
    'Reply-To: info@schaufenster-ka.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


    $res = mail($email,$subject,$text,$header);
    return $res;
}


?>
