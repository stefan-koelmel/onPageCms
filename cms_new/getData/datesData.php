<?php // charset:UTF-8
    header('Content-Type: text/html; charset=UTF-8');
    setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu', 'ge');

    global $cmsName,$cmsVersion;
    $cmsName = $_GET[cmsName];
    $cmsVersion = $_GET[cmsVersion];
    $mainCat = $_GET[mainCat];

    $out = $_GET[out];

     if (file_exists($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php")) {        
        include($_SERVER['DOCUMENT_ROOT']."/cms/cms_connect.php");
    } else {
        include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
    }

    
    switch ($out) {
        
        case "selectCalendar" :
            $date = $_GET[date];
            $width = $_GET[width];
            $frameId = explode("_",$_GET[frameId]);
            $frameId = $frameId[0];
            
            
            
            
//            echo ("date : $date <br>");
//            echo ("width : $width <br>");
//            echo ("frameId : $frameId <br>");
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/help.php");
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/pageStyles.php");
            
            
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/data/cms_dates.php");
            $out =  cmsDate_getMonthView($date,$frameId,$width); 
            echo ($out);
            
            break;
            

        case "calendar" :
            //echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>\n");
            $direction = $_GET[direction];
            $actMonth  = $_GET[actMonth];
            $width     = $_GET[width];
            $frameId   = $_GET[frameId];
//            echo ("DIRECTION = $direction<br>");
//            echo ("ACTMONTH  = $actMonth<br>");
            list($year,$month) = explode("-",$actMonth);
            switch ($direction) {
                case "for" :
                    $month = $month +1;
                    if ($month>12) {$month =1;$year=$year+1;}
                    break;
                case "back" :
                    $month = $month -1;
                    if ($month<1) {$month =12;$year=$year-1;}
                    break;
                default :
                    echo ("unkown Direction $direction");
                    die();
            }

            if ($month<10) $month = "0".$month;
            else $month = "$month";

            $newMonth = $year."-".$month;
            echo ("|".$newMonth."|");
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php");

            $dateClass = cmsType_dateList_class();
            //echo ($dateClass);
            $dateClass->dateList_showMonth_Content($frameId,$newMonth,0,$width);
            break;


        case "dropdown" :
            include($_SERVER['DOCUMENT_ROOT']."/cms_$cmsVersion/cms.php");

            $dataName = $_GET[dataName];
            if (!$dataName) {
                echo ("NoDataName");
                die();
            }

            $code = $_GET[code];

            //  $location = 202;

            $filter = array("showLinked"=>0);
            if ($_GET[location]) {
                $location = $_GET[location];
                $filter[location] = $location;
                echo ("Zeige Termine von Ort: $location<br>");
            }



            // $date = "2012-09-01";
            if ($_GET[dateRange]) {
                $date = $_GET[dateRange];
                list($y,$m) = explode('-',$date);
                $d = "01";
                $time = mktime(0, 0, 0, $m, $d, $y);
                $startDate = $time - (60 * 24 * 60 * 60);
                $endDate   = $time + (60 * 24 * 60 * 60);

                echo ("Zeige Termine im Zeitraum von  ".date("d.m.Y",$startDate)." bis ".date("d.m.Y",$endDate)."<br>");
                $startDate = date("Y-m-d",$startDate);
                $endDate   = date("Y-m-d",$endDate);

                $filter[fromDate] = $startDate;
                $filter[toDate]   = $endDate;

            }

            $showData = array();
            $showData[style]= "width:100%";
            $showData[submit] =1;
            $showData[outList] = array ("date","name");
           // $showData[out] = "id";
            $str = cmsDates_selectDates($code, $dataName, $showData, $filter, $sort);
            echo ($str);
            return $str;
            break;




        case "html" :
            foreach ($_GET as $key => $value ) {
                // echo ("#$key = $value <br>");
            }

            $day = $_GET[day];
            if (strlen($day) < 2) $day = "0".$day;
            $month = $_GET[mon];
            if (strlen($month) < 2) $month = "0".$month;
            $year = $_GET[yea];
            if (strlen($year) == 2) {
                if ($year < 100) $year = "20".$year;
            }

            $datum = $day.".".$month.".".$year;
            $date = $year."-".$month."-".$day;

            $locationId = intval($_GET[loc]);
            $regionId = intval($_GET[_reg]);
            $categoryId = intval($_GET[cat]);

            $getQuery = "SELECT * FROM `".$cmsName."_cms_dates` WHERE `show` = 1 ";
            if ($date)        $getQuery .= "AND `date`='$date' ";
            if ($categoryId)  $getQuery .= "AND `category`=$categoryId ";
            if ($locationId)  $getQuery .= "AND `location`=$locationId ";
            if ($regionId)    $getQuery .= "AND `region`=$regionId ";


            // echo ("$getQuery <br>");
            $result = mysql_query($getQuery);
            if ($result) {
                $dateList = array();
                while ($dates = mysql_fetch_assoc($result)){
                    $dateList[] = $dates;
                }
                
                global $cmsName,$cmsVersion;
                if ($_GET[cmsName]) $cmsName = $_GET[cmsName];
                if ($_GET[cmsVersion]) $cmsVersion = $_GET[cmsVersion];

                $cmsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php";
                // echo ("cnsFile = $cmsFile <br>");

                include($cmsFile);
                
                $dateClass = cmsType_dateList_class();
                // $dateClass->dateList_showList($dateList,$frameWidth);
                // echo ("DateClass = $dateClass <br>");

                $sortList = $dateClass->dates_convert($dateList);

                $filter = array();
                $filter[date] = $date;
                $filter[category] = $date;
                $filter[region] = $date;
                $dateClass->dates_show_sortList($sortList,$filter);



                for ($i=0; $i<$dateList.count;$i++) {
                    $dates = $dateList[$i];
                    echo ("$dates[id] - $dates[name] $dates[time] <br>");
                }
                
                
                

            } else {
                echo ("No Result <br>");
                echo ($getQuery."<br>");
                echo (mysql_error());
            }

    

            break;


        case "lkjlk" :

            $regionName = $_GET[_regName];
            $regionId = $_GET[_regId];

            $mainCatStr = "";
            if ($mainCat) $mainCatStr = " AND `mainCat` = $mainCat ";
            $getQuery .= "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 $mainCatStr AND ";
            if ($regionName) $getQuery .= "`name` like '$regionName' ";
            if ($regionId) $getQuery .= "`id`= $regionId ";
            $result = mysql_query($getQuery);
            echo ("Query = $getQuery<br>");
            if ($result) {
                $str = "region";
                $anz = mysql_num_rows($result);
                if ($anz == 0 AND $regionName) {
                    $getQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `show` = 1 $mainCatStr AND ";
                    $getQuery .= "`name` like '".$regionName."%' ";
                    // echo ("Try with % $getQuery <br>");
                    $result = mysql_query($getQuery);
                    $anz = mysql_num_rows($result);
                    //echo ("Anz = '$anz' <br>");
                    if ($anz==0) {
                      /*  $getQuery = "Select * FROM `".$cmsName."_cms_category` WHERE `mainCat` = $mainCat ";
                        $result = mysql_query($getQuery);
                        while ($cat = mysql_fetch_assoc($result)) {
                            echo ("$cat[id] - $cat[name] <br>");
                        } */
                    }
                }

                if ($anz == 1) {
                    $regionData =  mysql_fetch_assoc($result);
                    foreach ($regionData as $key => $value ) {
                        if ($str != "") $str.="|";
                        $str .= "$key#$value";
                   }
                } else {
                    $str = "notFournd";
                    $str.= "<br>".$getQuery;
                }
            }
            echo ($str);
            die();
            break;
        default :
            echo ("Output = $out <br>");
    }


    
    
?>
