<?php // charset:UTF-8

function cmsDates_getList($filter,$sort="",$out="") {
     
//    switch ($sort) {
//        case "time" : $sortQuery = "ORDER BY `time` ASC "; break;
//        case "date" : $sortQuery = "ORDER BY `date` ASC "; break;
//        case "id"   : $sortQuery = "ORDER BY `id` ASC "; break;
//        default:
//            $sortQuery = "ORDER BY `date` ASC ";
//    }

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

    $showLinked = 1;
    if ($filter) {
        if (is_array($filter)) {
            $filterQuery = "";
            foreach($filter as $key => $value) {
                switch ($key) {
                    case "special" :
                        switch ($value) {
                            case "art" :
                                if ($filterQuery != "") $filterQuery .= " AND ";
                                $filterQuery .= "(`category`=327 OR `category`=330) ";
                                break;
                                
                            case "date" :
                               
                        }
                        break;
                    
                    case "active" :    
                        $actDate = date("Y-m-d");
                        if ($filterQuery != "") $filterQuery .= " AND ";
                        $filterQuery .= "(`date` <= '$actDate' AND `toDate` >= '$actDate')";
                        break;

                    case "search" :
                        if ($value) {
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= " (`name` LIKE '%$value%' OR `info` LIKE  '%$value%') ";
                        }
                        break;
                    
                    default : 
                         //  echo "$filter = $key <br>";
                        $add = 1;
                        if ($value[0]=="!") {
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "`$key`$value ";
                            $add = 0;
                        }

                        if ($value[0]==">" OR $value[0]=="<") {
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "`$key`$value ";
                            $add = 0;
                        }

                         if ($value[0]=="%") {
                            if ($filterQuery != "") $filterQuery .= " AND ";
                            $filterQuery .= "`$key` LIKE '$value' ";
                            $add = 0;
                        }

                        if ($add == 1) {
                            // echo ("$key $value<br>");
                            switch ($key) {
                                case "hideLinked" : $showLinked = 0; break;
                                case "showLinked" : $showLinked = $value; break;
                                case "fromDate" :
                                    if ($filterQuery != "") $filterQuery .= " AND ";
                                    $filterQuery .= "`date` >= '$value' ";
                                    break;
                                case "toDate" :
                                    if ($filterQuery != "") $filterQuery .= " AND ";
                                    $filterQuery .= "`date` <= '$value' ";
                                    break;
                                case "date" :
                                    if ($filterQuery != "") $filterQuery .= " AND ";
                                    $filterQuery .= "`date` = '$value' ";
                                    break;
                                default :
                                    $mode = "string";
                                    switch ($key) {
                                        case "region" : $mode = "integer"; break;
                                        case "location" : $mode = "integer"; break;
                                    }
                                    if ($filterQuery != "") $filterQuery .= " AND ";
                                    switch ($mode) {
                                        case "string" : $filterQuery .= "`$key` = '$value' "; break;
                                        case "integer" : $filterQuery .= "`$key`=$value "; break;
                                    }

                            }
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


    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_dates` ".$filterQuery." ".$sortQuery;
    //
    if ($out=="out") echo ("Query $query <br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    if ($out=="out" AND $anz > 0) echo ("Anzahl = $anz <br>");
    
    
    if ($out == "query") {
        $query2 =  "SELECT `id` FROM `".$GLOBALS[cmsName]."_cms_dates` ".$filterQuery." ".$sortQuery;
        $result2 = mysql_query($query2);
        $anz = mysql_num_rows($result2);
        // echo ("OUTPOT = $query anz = '$anz' <br>");
        mysql_free_result($result);
        return array("query"=>$query,"count"=>$anz);
    }
    

    $res = array();
    $linkDates = array();
    while ($dateData = mysql_fetch_assoc($result)) {
        $dateData = php_clearQuery($dateData);
        $addDate = 1;
        if (strlen($dateData[link])) {
            $link = $dateData[link];
            $name = $dateData[name];
            $id   = $dateData[id];            
            // echo ("Link '$link' <br>");
            if (substr($link,0,9)=="dateMain:") { // Linked Date
                if ($showLinked) {
                    $mainId = intval(substr($link,9));

                    // echo "MainLink is $link $mainId <br>";
                    if (is_array($linkDates["$mainId"])) {
                        $mainDateData = $linkDates["$mainId"];
                        // echo ("Found LinkedDate <br>");
                    } else {
                        // echo ("Dont Found LinkedDate with id =$mainId <br>");
                        $mainDateData = cmsDates_get(array("id"=>$mainId));
                    }
                    if (is_array($mainDateData)) {
                        $mainDateData[mainId] = $mainDateData[id];
                        $mainDateData[id] = $dateData[id];
                        $mainDateData[date] = $dateData[date];
                        $mainDateData[time] = $dateData[time];
                        $mainDateData[subName] = $dateData[subName];
                        if ($dateData[info]) $mainDateData[info] = $dateData[info];
                        if ($dateData[longInfo]) $mainDateData[longInfo] = $dateData[longInfo];
                        $mainDateData[cancel] = $dateData[cancel];
                        $mainDateData[changeLog] = $dateData[changeLog];
                        $mainDateData[highlight] = $dateData[highlight];
                        
                        $mainDateData[lastMod] = $dateData[lastMod];
                        $dateData = $mainDateData;
                    }
                } else {
                    // Date is linked Date
                    $addDate = 0;
                }
            } else {
                $ofset = strpos($link,"date:");
                if (is_integer($ofset)) { // Main Date
                    $linkDates["$id"] = $dateData;
                    // echo ("Date with link $link ofset= $ofset<br>");
                }
            }
        }
        if ($addDate) {
            $res[] = $dateData;
            // echo ("add $dateData<br>");
        }
    }
    // show_array($linkDates);
   // echo ("a=".count($res)."<br>");
    mysql_free_result($result);
    return $res;
}

function cmsDates_selectDates($code,$dataName,$showData,$filter,$sort) {
    $dateList = cmsDates_getList($filter,$sort);

    $str = "";
    $emptyStr = "Termin wählen";
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


    for($i=0;$i<count($dateList);$i++) {
        $dateId = $dateList[$i][id];
        $outValue = "name";
        if ($showData[out]) $outValue = $showData[out];
        $dateName = $dateList[$i][$outValue];
        $str.= "<option value='$dateId'";
        if ($code == $dateId)  $str.= " selected='1' ";
        $str.= ">";
        if (is_array($showData[outList])) {
            for ($o=0;$o<count($showData[outList]);$o++) {
                $outName = $showData[outList][$o];
                if ($dateList[$i][$outName]) {
                    if ($outName == "date") {
                        $str.= cmsDate_getDayString($dateList[$i][$outName],0)." ";
                    } else {
                        $str.= $dateList[$i][$outName]." ";
                    }
                }
            }
        } else {
            $str.= $dateName;
        }
        $str.= "</option>";
    }
    $str.= "</select>";
    return $str;

}

function cmsDates_getById($dateId) {
    $res = cmsDates_get(array("id"=>$dateId));
    if (is_array($res)) return $res;
    if ($res == 0) {
        $out = "Termin nicht gefunden";
        if ($_SESSION[userLevel]>=9) $out .= "<br />Date with id $dateId";
        cms_errorBox($out);
        return 0;
    }
    if ($res > 1) {
        $out = "Mehrere Termine gefunden";
        if ($_SESSION[userLevel]>=9) $out .= "<br />Date with id $dateId";
        cms_errorBox($out);
        return 0;
    }
    return $res;
}




function cmsDates_getByName($datesName,$mainDates) {
    $res = cmsDates_get(array("name"=>$datesName));
    if ($res == 0) {
        cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($res > 1) {
        cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return 0;
    }
    return $res;
}

function cmsDates_get($filter) {
    $filterQuery = "";
    $addLinkedData = 1;
    if (is_array($filter)) {
        foreach ($filter as $key => $value) {
            if ($key == "dontLinkDate") {
                $addLinkedData = 0;
                // echo ("<h1>DONT LINK DATE </h1>");
            } else {

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
                    $add = "";
                    if (substr($value,0,2)==">=") $add = "`$key` $value";
                    if (substr($value,0,2)=="<=") $add = "`$key` $value";
                    //echo ("Query ".substr($value,0,2)."<br>");
                    // if ($filterQuery != "") $filterQuery .= " AND ";
                    if ($add) $filterQuery .= $add;
                    else $filterQuery .= "`$key` = '$value'";



                    // $filterQuery .= "`$key` = '$value' ";
                }
            }
        }
    }
    
    $sortQuery = "";
    // echo("$filterQuery <br>");

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_dates` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);

    // echo("$query -> $result $anz <br>");
    if ($anz == 0) {
        // cms_errorBox("Kategorie nicht gefunden <br>$query");
        return 0;
    }
    if ($anz > 1) {
        // cms_errorBox("Mehrere Kategorien gefunden (Anzahl=$anz)<br>$query");
        return $anz;
    }
    $datesData = mysql_fetch_assoc($result);
    $datesData = php_clearQuery($datesData);
    
    
    if (strlen($datesData[link])) {
        $link = $datesData[link];
        if (substr($link,0,9)=="dateMain:") {
            $mainId = intval(substr($link,9));

            // echo "MainLink is $link $mainId <br>";
          
                // echo ("Dont Found LinkedDate with id =$mainId <br>");
            $mainDateData = cmsDates_get(array("id"=>$mainId));
            if (is_array($mainDateData)) {
                if ($addLinkedData) {
                    $mainDateData[mainId] = $mainDateData[id];
                    $mainDateData[id] = $datesData[id];
                    $mainDateData[date] = $datesData[date];
                    $mainDateData[time] = $datesData[time];
                    $mainDateData[subName] = $datesData[subName];
                    $mainDateData[changeLog] = $datesData[changeLog];
                    $mainDateData[lastMod] = $datesData[lastMod];
                    $mainDateData[cancel] = $datesData[cancel];
                    $mainDateData[highlight] = $datesData[highlight];
                    
                    $datesData = $mainDateData;
                } else {
                    // 
                    $datesData[mainId] = $mainDateData[id];
                }
            }
        } else {
            $ofset = strpos($link,"date:");
            if (is_integer($ofset)) {
                $linkDates["$id"] = $dateData;
                // echo ("Date with link $link ofset= $ofset<br>");
            }
        }
    }
    return $datesData;
}


function cmsDates_existName($datesName,$category) {
    echo ("cmsDates_existName($datesName,$category)<br>");
    $filterQuery = "WHERE `name` LIKE '$datesName'";
    if ($category) $filterQuery .= " AND `category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_dates` ".$filterQuery." ".$sortQuery;
    echo ($query."<br>");
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}


function cmsDates_existID($datesId,$category) {

    $filterQuery = "WHERE `id` = $datesId";
    if ($category) $filterQuery .= " AND `$category` = $category";
    $sortQuery = "";

    $query = "SELECT * FROM `".$GLOBALS[cmsName]."_cms_dates` ".$filterQuery." ".$sortQuery;
    $result = mysql_query($query);
    $anz = mysql_num_rows($result);
    return $anz;

}

function cmsDates_save($data) {
    //echo ("Save Date with ID = $data[id] <br>");
    if (is_array($data[data])) {
        $data[data] = array2Str($data[data]);
    }

    $id = $data[id];
    if ($id) {
        $existData = cmsDates_get(array("id"=>$id));
        if (is_array($existData)) {
            $data[id] = $existData[id];
            return cmsDates_update($data,$existData);
        }
    }

    $name = $data[name];
    $date = $data[date];
    $time = $data[time];
    $category = $data[category];
//    $existData = cmsDates_get(array("name"=>$name,"date"=>$date,"time"=>$time,"category"=>$category));
//    if (is_array($existData)) {
//        $data[id] = $existData[id];
//        //echo ("<h1>");
//        //show_array($data);
//        //echo ("</h1>");
//        // show_array($existData);
//        return cmsDates_update($data,$existData);
//    }
//    if ($existData > 0 ) {
//        $data[id] = $existData[id];
//        
//        return cmsDates_update($data,$existData);
//    }


    $query = "";
    foreach ($data as $key => $value ) {
        if ($value) {
            $value = php_clearStr($value);
            if ($query != "" ) $query.= ", ";
            $query.= "`$key`='$value'";           
        }
    }
    // echo ("insert <br>");
    $query = "INSERT INTO `".$GLOBALS[cmsName]."_cms_dates` SET ".$query;
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

function cmsDates_update($data,$existData) {
    $query = "";
    $id = $data[id];
    foreach ($data as $key => $value ) {
        if ($value AND $key != "id") {
             if ($value == $existData[$key]) {
                // same Data
            } else {
                if (is_string($value) AND strlen($value)) {
                    $value = str_replace(array("'",'"'),array("&#039;","&#034;"),$value);
                }
                if ($query != "" ) $query.= ", ";
                $query.= "`$key`='$value'";
            }
        }
    }
    if ($query == "") {
        echo ("No Change in Data <br>");
        return 1;
    }
            
    $query = "UPDATE `".$GLOBALS[cmsName]."_cms_dates` SET ".$query." WHERE `id` = $id ";
    $result = mysql_query($query);
    if (!$result) {
        echo "Error in $query <br>";
        return 0;
    }
    return 1;
}

function cmsDates_selectMonth($code,$dataName,$showData) {
    $str = "";

    $actMonth = intval(date("m"));
    $actYear = intval(date("Y"));

    $backMonth = 6;
    if ($showData[back]) $backMonth = $showData[back];
    $forMonth = 6;
    if ($showData["for"]) $backMonth = $showData["for"];
    $anzMonth = $backMonth + 1 + $forMonth;

    $startMonth = $actMonth-$backMonth;
    $startYear = $actYear;
    if ($startMonth < 1) {
        $startYear = $startYear - 1;
        $startMonth = $startMonth + 12;
    }


    $str.= "<select name='$dataName' class='cmsSelectMonth' ";
    if ($showData[submit]) $str.= "onChange='submit()' ";
    $str.= "style='min-width:200px;' value='$code' >";

    $str.= "<option value='0'";
    if (!$code) $str.= " selected='1' ";
    $str.= ">Monat wählen</option>";

     for ($i=0;$i<$anzMonth;$i++) {
        if ($startMonth < 10) $startMonth = "0".$startMonth;
        else $startMonth = "".$startMonth;

        $str .= "<option ";
        if ($code == $startYear."-".$startMonth) $str .= "selected='1' ";
        $str .= "value='".$startYear."-".$startMonth."'>".$startMonth." / ".$startYear."</option>";
        $startMonth++;
        if ($startMonth>12) {
            $startMonth=1;
            $startYear++;
        }
    }

    $str.= "</select>";
    return $str;
}


function cmsDates_dayCode($date) {
    list($year,$month,$day) = explode("-",$date);
    $date = mktime(12,0,0,$month,$day,$year);

    $dayCode = intval(date("w",$date));
    return $dayCode;        
}


function cmsDates_dayStr($dayCode) {
    if (is_string($dayCode)) $dayCode = intval($dayCode);
    switch($dayCode) {
        case 1 : $dayStr = "Montag"; break;
        case 2 : $dayStr = "Dienstag"; break;
        case 3 : $dayStr = "Mittwoch"; break;
        case 4 : $dayStr = "Donnerstag"; break;
        case 5 : $dayStr = "Freitag"; break;
        case 6 : $dayStr = "Samstag"; break;
        case 0 : $dayStr = "Sonntag"; break;
        case 7 : $dayStr = "Sonntag"; break;
        default :
            echo "Day not found in $this cmsDates_dayStr($dayCode)<br> ";
            $dayStr = $dayCode;
    }
    return $dayStr;
}

function cmsDates_monthStr($month) {
    if (is_string($month)) $month = intval($month);
    switch($month) {
        case 1 : $monthStr = "Januar"; break;
        case 2 : $monthStr = "Februar"; break;
        case 3 : $monthStr = "März"; break;
        case 4 : $monthStr = "April"; break;
        case 5 : $monthStr = "Mai"; break;
        case 6 : $monthStr = "Juni"; break;
        case 7 : $monthStr = "Juli"; break;
        case 8 : $monthStr = "August"; break;
        case 9 : $monthStr = "September"; break;
        case 10 : $monthStr = "Oktober"; break;
        case 11 : $monthStr = "November"; break;
        case 12 : $monthStr = "Dezember"; break;
        default :
            echo "Month not found in $this dateList_monthStr($month)<br> ";

    }
    return $monthStr;
}

function cmsDate_getDayString($datum,$showWeekDay=0) {
    $day = substr($datum,8,2);
    $month = substr($datum,5,2);
    $year = substr($datum,0,4);

    $date = mktime(12,0,0,$month,$day,$year);



    $dateStr = date("d.m.Y",$date);

    if ($showWeekDay) {
        $dayCode = intval(date("w",$date));
        $dayCodeStr = cmsDates_dayStr($dayCode);

        switch ($showWeekDay) {
            case "only" :
                return $dayCodeStr;
            case "short" :
                $dateStr = substr($dayCodeStr,0,2).", ".$dateStr;
                break;
            default :
                $dateStr = $dayCodeStr.", den ".$dateStr;
        }
    }

    return $dateStr;
}



function cmsDate_getTimeString($time,$anz=2) {
    $hour = substr($time,0,2);
    $min  = subStr($time,3,2);
    $sec  = subStr($time,6,2);
    $str = "";
    if ($hour[0] == "0") $hour = $hour[1];
    if ($anz >= 1) $str .= $hour;
    if ($anz >= 2) $str .= ":".$min;
    if ($anz >= 3) $str .= ":".$sec;
    return $str;
}

function cmsDates_dateRange_getList() {
    $specialList = array();

    $date = date("Y-m-d w");
    $day = substr($date,8,2);
    $month = substr($date,5,2);
    $dayCode = intval(subStr($date,11));
    $year = substr($date,0,4);

    $today = mktime(12,0,0,$month,$day,$year);
    //echo ("Datum $date <br>");

     // TODAY
    $startDate = $year."-".$month."-".$day;
    // echo ("Aktueller Tag => $startDate  <br>");

    $specialList[today] = array("id"=>"active","name"=>"Heute");
    $specialList[today][filter] = array("show"=>1,"date"=>$startDate);
    $specialList[today][sort] = "date";

    // TOMORROW

    $tomorrow = $today + (24 * 60 * 60);
    $dateNew = date("Y-m-d",$tomorrow);
    $startDate = $dateNew;
    // echo ("Morgen Tag => $startDate  <br>");
    $specialList[tomorrow] = array("id"=>"active","name"=>"Morgen");
    $specialList[tomorrow][filter] = array("show"=>1,"date"=>$startDate);
    $specialList[tomorrow][sort] = "date";


    // YESTERDAY
    $yesterday = $today - (24 * 60 * 60);
    $dateNew = date("Y-m-d",$yesterday);
    $startDate = $dateNew;
    //echo ("Gestern Tag => $startDate  <br>");
    $specialList[yesterday] = array("id"=>"active","name"=>"Gestern");
    $specialList[yesterday][filter] = array("show"=>1,"date"=>$startDate);
    $specialList[yesterday][sort] = "date";


    // This Week
    switch ($dayCode) {
        case 1 : $subDays = 0; break;
        case 0 : $subDays = 6; break;
        default :
            $subDays = $dayCode - 1;
    }
    $monday = $today - ($subDays *(24 * 60 * 60));
    $startDate = date("Y-m-d",$monday);
    //echo ("Montag ($subDays abgezogen) $startDate <br>");
    $sunday = $monday + (6 * 24 * 60 * 60);
    $endDate = date("Y-m-d w",$sunday);
    //echo ("Sonntag (7 abgezogen) $endDate <br>");
    $specialList[week] = array("id"=>"active","name"=>"Aktuelle Woche");
    $specialList[week][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[week][sort] = "date";

    // NextWeek Week
    $week = 7 * 24 * 60 * 60;
    $startDate = date("Y-m-d",$monday+$week);
    // echo ("Montag (1 Woche drauf) $startDate <br>");
    $endDate = date("Y-m-d w",$sunday+$week);
    // echo ("Sonntag (Sonntag + 1 Woche) $endDate <br>");
    $specialList[nextWeek] = array("id"=>"active","name"=>"Nächste Woche");
    $specialList[nextWeek][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[nextWeek][sort] = "date";

    // LastWeek Week
    $startDate = date("Y-m-d",$monday-$week);
    //echo ("Montag (1 Woche weg) $startDate <br>");
    $endDate = date("Y-m-d w",$sunday-$week);
    //echo ("Sonntag (Sonntag - 1 Woche) $endDate <br>");
    $specialList[lastWeek] = array("id"=>"active","name"=>"Letzte Woche");
    $specialList[lastWeek][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[lastWeek][sort] = "date";


    // This Month
    $startDate = $year."-".$month."-01";
    $endDate = $year."-".$month."-31";
    // echo ("Aktueller Monat => $startDate -> $endDate <br>");

    $specialList[month] = array("id"=>"active","name"=>"Aktueller Monat");
    $specialList[month][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[month][sort] = "date";

    // NEXT MONTH
    // $month = "12";
    $nextMonth = intval($month)+1;
    $nextYear  = intval($year);
    if ($nextMonth > 12) {
        $nextMonth = 1;
        $nextYear = $nextYear + 1;
    }
    if ($nextMonth < 10) $nextMonth = "0".$nextMonth;

    $startDate = "".$nextYear."-".$nextMonth."-01";
    $endDate = "".$nextYear."-".$nextMonth."-31";
    // echo ("NextMonth => $startDate -> $endDate <br>");
    $specialList[nextMonth] = array("id"=>"active","name"=>"Nächster Monat");
    $specialList[nextMonth][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[nextMonth][sort] = "date";


    // LastMONTH
    // $month = "1";
    $lastMonth = intval($month)-1;
    $lastYear  = intval($year);
    if ($lastMonth < 1) {
        $lastMonth = 12;
        $lastYear = $lastYear - 1;
    }
    if ($lastMonth < 10) $lastMonth = "0".$lastMonth;

    $startDate = "".$lastYear."-".$lastMonth."-01";
    $endDate = "".$lastYear."-".$lastMonth."-31";
    //echo ("Last Month => $startDate -> $endDate <br>");
    $specialList[lastMonth] = array("id"=>"active","name"=>"Vergangener Monat");
    $specialList[lastMonth][filter] = array("show"=>1,"fromDate"=>$startDate,"toDate"=>$endDate);
    $specialList[lastMonth][sort] = "date";
   
    return $specialList;
}

?>
