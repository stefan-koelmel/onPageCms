<?php // charset:UTF-8

function kalender_getLink($link,$set,$to=0) {
    if (!is_array($set)) {
        $setList[$set] = array("value"=>$to,"found"=>0);
    } else {
        $setList = array();
        foreach ($set as $key=> $value) {
            $setList[$key] = array("value"=>$value,"found"=>0);
        }
    }

    $goPage = "";
    
    if ($setList[region][value]) $goPage="?region=".$setList[region][value];
    
    $found = 0;
    foreach ($_GET as $key => $value) {
        // replace setValue
        $add = 1;
        if (is_array($setList[$key])) {
            $add=0;
            $setList[$key][found] = 1;
        }
        if ($add AND $value) {
            if ($goPage == "") $goPage.="?";
            else $goPage.="&";
            $goPage .= $key."=".$value;
        }
    }
    // echo ($goPage."<br />");

   


    foreach($setList as $key => $data) {
        $value = $data[value];
        $found = $data[found];
        // NOT IN GETLIST and $to has value
        if ($value AND $key != "region") {
            if ($goPage == "") $goPage.="?";
            else $goPage.="&";
            $goPage .= $key."=".$value;
        }
    }

    // add LINK
    $goPage = php_clearLink($link.$goPage);
    return $goPage;
}


function kalender_showFilter($selectDate,$selectRegion,$selectCategory,$link) {
    // echo ("kalender_showFilter($selectDate,$selectRegion,$selectCategory,$link)<br />");

    $showBlock = 1;
    $useX = 1;
    $class = "current-info-container clearfloat";
    if ($showBlock) $class .= " filterBlock";
    
    echo ("<div class='$class'>");
    if ($showBlock) echo ("<h4>Anzeige filtern nach:</h4>");
    
    list($year,$month,$day) = explode("-",date("Y-m-d"));
    $today = mktime(12,0,0,$month,$day,$year);
// the-date date-info-link
    if ($selectDate == date("Y-m-d",$today)) $selectDate = "today";
    if ($selectDate == date("Y-m-d",$today-24 *60 *60)) $selectDate = "yesterday";
    if ($selectDate == date("Y-m-d",$today+24 *60 *60)) $selectDate = "tomorrow";
    
//    echo ("$selectDate == ".date("Y-m-d",$today)." -> today<br />");
//    echo ("$selectDate == ".date("Y-m-d",$today-24 *60 *60)." => yesterday<br />");
//    echo ("$selectDate == ".date("Y-m-d",$today+24 *60 *60)." => tomorrow<br />");
    
    
    if (!$selectDate) $selectDate = "today";
    

    
    
    
    $dateList = array();
    $dateList["today"] = "Heute";
    $dateList["tomorrow"] = "Morgen";
    $dateList["yesterday"] = "Gestern";
    $dateList["thisWeek"] = "Diese Woche";
    $dateList["nextWeek"] = "Nächste Woche";
    $dateList["future"] = "Nächste 10 Tage";

    
    echo ("<div class='adress-filterline'>");

    if ($showBlock) {
        echo ("<span class='filterType'>Zeitraum:</span>");
    }
    
    if ($dateList[$selectDate]) {
        if ($showBlock) {
            echo ("<a href='#' class='change-date' title='Filtern nach Zeitraum' >$dateList[$selectDate]</a>");
           
        } else {
            echo ($dateList[$selectDate]);
        } 
    } else {
        if (strlen($selectDate) == 10) { // Datum
            $dateStr = cmsDate_getDayString($selectDate, 1);
            if ($showBlock) {
                echo ("<a href='#' class='change-date' title='Filtern nach Zeitraum'>$dateStr</a>");           
            } else {
                echo ($dateStr);
            }             
        } else {
            echo ("unkown Date $selectDate");
        }
    }
    if ($showBlock) {
         echo ("<br />");
         //kalender_dateSelect($selectDate,$link);
    } else {
        echo (" <a href='#' class='change-current change-date'>(&auml;ndern)</a>");
        echo ("<span class='quo'>&rsaquo;</span> ");
    }

    //// KATEGOIE FILTER
    $dateCatList = $_SESSION["TerminCategoryList"];
    if ($showBlock) {
        echo ("<span class='filterType' >Kategorie:</span>");
    }
    if ($selectCategory) {
        $catName = $dateCatList[$selectCategory][name];
        if ($showBlock) {
            echo ("<a href='#' class='change-categorie' title='Filltern nach Kategorie' >$catName</a>");
            $goLink = kalender_getLink($link,"cat",0);
            $removeClass = "change-current";
            $removeStr = "Filter entfernen";
            if ($useX) {
                $removeClass .= " remove-current";
                $removeStr = "x";
            } else {
                echo (" <span class='quo'>&rsaquo;</span> ");
            }            
            echo (" <a href='$goLink' class='$removeClass' title='Filter entfernen - Alle Kategorien zeigen'>$removeStr</a>");
            // echo (" <a href='$goLink' class='change-current'>enfernen</a>");
        } else {
            echo ("$catName ");

            echo ("<a href='#' class='change-current change-categorie'>(&auml;ndern)</a>");
            $goLink = kalender_getLink($link,"cat",0);
            echo ("<a href='$goLink' class='change-current'>(l&ouml;schen)</a>");
        }
    } else {
        if ($showBlock) {
            echo ("<a href='#' class='change-categorie' title='Filtern nach Kategorie'>Alle Kategorien</a>");
        } else {
            echo ("Alle Kategorien <a href='#' class='change-current change-categorie' >(&auml;ndern)</a>");
        }
    }
    if ($showBlock) {
        echo ("<br />");
        //kalender_showCategory($selectCategory,$link);
        // echo ("<br />");
    } else echo ("<span class='quo'>&rsaquo;</span> ");
    
    
    //// REGION FILTER
    $regionList = categoryGetList("RegionList");
    if ($showBlock) {
        echo ("<span class='filterType'>Region:</span>");
    }
    if ($selectRegion) {
        $regionName = $regionList[$selectRegion][name];
        
        if ($showBlock) {
            echo ("<a href='#' class='change-region' title='Filtern nach Region' >$regionName</a>");
            $goLink = kalender_getLink($link,"region",0);
            $removeClass = "change-current";
            $removeStr = "Filter entfernen";
            if ($useX) {
                $removeClass .= " remove-current";
                $removeStr = "x";
            } else {
                echo (" <span class='quo'>&rsaquo;</span> ");
            }            
            echo (" <a href='$goLink' class='$removeClass' title='Filter entfernen - Alle Regionen zeigen'>$removeStr</a>");
            
            
            // echo (" <span class='quo'>&rsaquo;</span> <a href='$goLink' class='change-current'>enfernen</a>");
        } else {
            echo ("$regionName ");

            echo ("<a href='#' class='change-current change-region'>(&auml;ndern)</a>");
            $goLink = kalender_getLink($link,"region",0);
            echo ("<a href='$goLink' class='change-current'>(l&ouml;schen)</a>");
        }
        
        
        
        
        
    } else {
        if ($showBlock) {
            echo ("<a href='#' class='change-region' title='Filtern nach Region' >Alle Regionen</a>");
            echo ("<br />");
            //kalender_showRegion($selectRegion,$link);
        } else {
            echo ("Alle Regionen <a href='#' class='change-current change-region'>(&auml;ndern)</a>");
        }
    }


  
    echo ("</div>");
    
    

    
    if ($_SESSION[showLevel] >= 9) $search = 1;
    if ($search) {
        $kalenderSearch = $_POST[kalenderSearchText];
        if ($_GET[search]) $kalenderSearch = $_GET[search];

        echo ("<div class='adressSearch'  >");
        echo ("<form class='adressSearchForm' method='post' action='$link' >");
        echo ("<input type='text' class='adressSearchInput' name='kalenderSearchText' value='$kalenderSearch' />");
        echo ("<input type='submit' class='adressSearchButton' name='adressSearchButton' value='suchen' />");

        echo ("</form>");
        echo ("</div>");
    }


      
    
    echo ("</div>");

    

    //if (!$showBlock) {
    kalender_dateSelect($selectDate,$dateList,$link);

    kalender_showCategory($selectCategory,$link);

    kalender_showRegion($selectRegion,$link);
    //}
    echo("<div class='slidespacer'>&nbsp;</div>");
   

}


function kalender_dateSelect($selectDate,$dateList,$link) {
    
    if (!$link) $link = "kalender.php";


    echo("<div class='select-container select-date clearfloat'>");
    echo ("<div class='select-title'>Zeitraum<a href='#' class='close_this_container'>&#215;</a></div>");
    $nr = 0;
    $nrPerLine = 3;
    $i=0;
    foreach ($dateList as $dateId => $dateName) {


        $nr++;

        $className = "select-link";
        if ($nr >= $nrPerLine) $className .= " select-link-last";

        // Category Selected
        if ($selectDate == $dateId) {
            $className .= " select-selected";
            $goLink = kalender_getLink($link,"date",0);
        } else {
            $goLink = kalender_getLink($link,"date",$dateId);
        }

        if ($goLink) echo ("<a href='$goLink' class='$className' >");
        // echo ("  <div class='$divName'>\n");
        echo ("$dateName");
        //echo ("  </div>");
        if ($goLink) echo ("</a>");

        if ($nr >= $nrPerLine) {
            $nr = 0;
        }
    }

    echo ("</div>");

}



function kalender_showCategory($selectedCatId=0,$link="") {
    if (!$link) $link = "kalender.php";
    
   
    $dateCatList = categoryGetList("TerminCategoryList");
    
    if (is_array($dateCatList) AND count($dateCatList)) {

        echo("<div class='select-container select-categorie clearfloat'>");
        echo ("<div class='select-title'>Kategorie<a href='#' class='close_this_container'>&#215;</a></div>");
        $nr = 0;
        $nrPerLine = 3;

        foreach ($dateCatList as $catId => $catValue) {    
            $catName = $catValue[name];
            $shortName = $catValue[shortName];
            $show = 1;
            switch ($catId) {
               // case 327 : $show =0;break; // Ausstellungen 
                case 330 : $show =0;break; // Kunst 
            }
            if ($show) {
                $nr++;

                $className = "select-link";
                if ($nr >= $nrPerLine) $className .= " select-link-last";

                // Category Selected
                if ($selectedCatId == $catId) {
                    $className .= " select-selected";
                    $goLink = kalender_getLink($link,"cat",0);
                } else {
                    $goLink = kalender_getLink($link,"cat",$shortName);
                }

                if ($goLink) echo ("<a href='$goLink' class='$className' >");
                echo ("$catName");
                if ($goLink) echo ("</a>");
                if ($nr >= $nrPerLine) {
                    $nr = 0;
                }
            }
        }
        echo ("</div>");
    }
}

function kalender_showRegion($selectedRegionId,$link="") {
    if (!$link) $link = "kalender.php";
    
    $regionList = categoryGetList("RegionList");
    
    if (is_array($regionList) AND count($regionList)) {
        echo("<div class='select-container select-region clearfloat'>");
        echo ("<div class='select-title'>Regionen<a href='#' class='close_this_container'>&#215;</a></div>");

        $nr = 0;
        $nrPerLine = 3;
        foreach ($regionList as $catId => $catValue) {
            $catName = $catValue[name];
            $shortName = $catValue[shortName];
          
            $nr++;
            $className = "select-link";
            if ($nr >= $nrPerLine) $className .= " select-link-last";

            // Category Selected
            if ($selectedRegionId == $catId) {
                $className .= " select-selected";
                $goLink = kalender_getLink($link,"region",0);
            } else {
                $goLink = kalender_getLink($link,"region",$shortName);
            }

            if ($goLink) echo ("<a href='$goLink' class='$className' >");
            echo ("$catName");
            if ($goLink) echo ("</a>");
            if ($nr >= $nrPerLine) {
                $nr = 0;
            }
        }
        echo ("</div>");

    }

}


function kalender_showInfo($dateData,$dontShow=array()) {
    // show_array($dateData);
    $out = date_showSmall_str($dateData,$dontShow);
    echo ("$out");
}



function date_showSmall_str($dateData,$ownDontShow=array(),$delimiter="-") {
    $out = "";
    if ($_SESSION[userLevel] > 5) $editLink = "admin_cmsDates.php?view=edit&id=";

    if (!is_array($dateData)) return 0;
    $id   = $dateData[id];
    $name = php_clearOutPut($dateData[name]);
    $subName = php_clearOutPut($dateData[subName]);
    $info = php_clearOutPut($dateData[info]);
    $categoryId = $dateData[category];
    $regionId = $dateData[region];
    $locationId = $dateData[location];
    
    $dontShow = array();
    $dontShow[maxDate] = date("Y-m-d");
    $dontShow[dateRange] = 1;
    $dontShow[weekDay] = 1;
    $dontShow[cancel] = 1;
    
    foreach($ownDontShow as $key => $value) {
        $dontShow[$key] = $value;
    }
    
    $showWeekDay = $dontShow[weekDay];
    
    
    // Maximales Datum
    $maxDate = $dontShow[maxDate];
    if ($maxDate == "none") $maxDate = "1900-01-01";
    // $out .= "MaxDate = $maxDate";
    // $maxDate = "2012-10-13";

    $date = $dateData[date];
    $toDate = $dateData[toDate];
    
   
    $delimiter = $delimiter." ";
    
    
    
    $cancel = $dateData[cancel];
    
    $showDate = 1;
    if ($date < $maxDate) $showDate = 0;
    if ($cancel AND $dontShow[cancel]) $showDate = 0;
    //if ($date >= $maxDate AND !$cancel) {
    if ($showDate) {
        if ($dontShow[weekDay]) $showWeekDay = 0;
        $date_Str = cmsDate_getDayString($date,$showWeekDay);

        $toDate_Str = cmsDate_getDayString($toDate,$showWeekDay);
        
        $time = $dateData[time];
        $timeStr = cmsDate_getTimeString($time,2);

        // $delimiter = "|";



        if (!$dontShow[link]) $out .= "<a href='kalender.php?dateId=$id' >";

        $dateStr = "";

        if (!$dontShow[date] AND $date) $dateStr .= "$date_Str ".$delimiter;
        if (!$dontShow[toDate] AND $toDate) $dateStr .= "$toDate_Str ".$delimiter;
        
        if (!$dontShow[dateRange]) {
            if ($date AND $toDate) {
                $dateStr .= "$date_Str bis $toDate_Str ".$delimiter;
            }
        }
        
        
        if (!$dontShow[time] AND $time != "00:00:00") $dateStr .= "$timeStr ".$delimiter;
        if (!$dontShow[name] AND $name) $dateStr .= "<b>$name</b> ".$delimiter;
        // Termin Zusatz 
        if (!$dontShow[subName] AND $subName ) $dateStr .= "$subName ".$delimiter;
        // kurzer Text
        if (!$dontShow[info] AND $info) $dateStr .= "$info ".$delimiter;
        
      
        
        if (!$dontShow[location]) {
            $locationData = cmsLocation_getById($locationId);
            if (is_array($locationData)) {
                $locationStr = $locationData[name];
            } else {
                $locationStr = $dateData[locationStr];
            }
            if ($locationStr) $dateStr .= "$locationStr ".$delimiter;
        }

        if (!$dontShow[category] AND $categoryId) {
            $categoryList = categoryGetList("TerminCategoryList");
            $categoryStr = $categoryList[$categoryId][name];
            // $categoryStr = cmsCategory_getName_byId($categoryId);
            $dateStr .= "$categoryStr ".$delimiter;
        }
        if (!$dontShow[region]) {
            $regionList = categoryGetList("RegionList");
            $regionStr = $regionList[$regionId][name];
            // $regionStr = cmsCategory_getName_byId($regionId);
            $dateStr .= "$regionStr ".$delimiter;
        }
        if ($regionId == 187) { // Umland
            $city = "";
            if ($locationId) {
                if (!is_array($locationData)) $locationStr = $locationData[name];
                $city = $locationData[city];
            } else {
                $city = $dateData[data][city];
            }
            if ($city) $dateStr .= "($city) ".$delimiter;

        }
        // $out .= $regionId." - ";


        $out .= substr($dateStr,0,(strlen($dateStr)-1-strlen($delimiter)));
        if (!$dontShow[link]) $out .= "</a>";
        $out .= "<br />";
        if (!$dontShow[editLink] and $editLink) $out .= "<a href='".$editLink.$dateData[id]."'>Termin bearbeiten</a><br />";
    }

    
    
    
    
   
    

    if (!$dontShow[linkDate]) {
       //  echo ("linkDates $dontShow[linkDates] linkDate $dontShow[linkDate]<br />");
        $linkString = $dateData[link];
        if ($linkString) {
            // echo ("LinkListStroing = $linkString <br />");
            $linkList = explode("|",$limkString);
            $dateLinkList = cmsDates_getList(array("link"=>"dateMain:$dateData[id]","date"=>">=".date("Y-m-d")),"date");
            if (is_array($dateLinkList) AND count($dateLinkList)) {
                //show_array($dateLinkList);
                for ($i=0;$i<count($dateLinkList);$i++) {
                    $linkDate = $dateLinkList[$i];
                    $link_Date = $linkDate[date];
                    $link_Cancel = $linkDate[cancel];
                    if ($link_Date >= $maxDate AND !$link_Cancel) {
                    
                        $showLinkDate = 1;
                        if ($dontShow[maxDate] AND $dontShow[maxDate] < $link_Date ) $showLinkDate = 0;
    //                    else $out .= $dontShow[maxDate]." - $link_Date<br />";
                        if ($showLinkDate) {
                            $linkTime = $linkDate[time];
                            $linkName = $linkDate[name];
                            $linkSubName = $linkDate[subName];
                            if (!$dontShow[link]) $out .= "<a href='kalender.php?dateId=$linkDate[id]' >";

                            $dateStr = "";

                            $showWeekDay = 1;
                            if ($dontShow[weekDay]) $showWeekDay = 0;
                            $linkDateStr = cmsDate_getDayString($link_Date,$showWeekDay);

                            $linkTimeStr = cmsDate_getTimeString($linkTime,2);


                            if (!$dontShow[date] AND $linkDateStr) $dateStr .= "$linkDateStr ".$delimiter;
                            if (!$dontShow[time] AND $linkTimeStr) $dateStr .= "$linkTimeStr ".$delimiter;
                            if (!$dontShow[name] AND $linkName) $dateStr .= "<b>$linkName</b> ".$delimiter;
                            if (!$dontShow[subName] AND $linkSubName ) $dateStr .= "$linkSubName ".$delimiter;

                            if (!$dontShow[category]) $dateStr .= "$categoryStr ".$delimiter;

                            if (!$dontShow[region]) $dateStr .= "$regionStr ".$delimiter;



                            $out .= substr($dateStr,0,(strlen($dateStr)-2));

                            if (!$dontShow[link]) $out .= "</a>";
                            // $out .= "<br />";
                            if (!$dontShow[editLink] AND $editLink) $out .= "<a href='".$editLink.$linkDate[id]."' class='editLink' >Termin bearbeiten</a><br />";

                        }
                    }
                }
            }
        }


    }
    
    return $out;
}


function date_showSmall($dateData,$dontShow=array(),$delimiter="-") {
    echo (date_showSmall_str($dateData, $delimiter));
}



function dates_getSortetList($dateList,$regionList,$catList) {

    // Create Empty Day Array
    $emptyDate = array();

    // Create empty Region Array
    $emptyRegion = array();
    foreach ($regionList as $regionId => $catValue) {
        
        // echo ("Region $regionId = $regionName <br />");
        $emptyRegion[$regionId] = array();
    }

    // create empty Category Array
    foreach($catList as $catId => $catValue) {
        // echo ("Category $catId = $catName <br />");
        $emptyDate[$catId] = $emptyRegion;
        
    }
    
    $res = array();
    for ($i=0;$i<count($dateList);$i++) {
        $date = $dateList[$i][date];
        $category = $dateList[$i][category];
        $show = 1;
        switch ($category) {
            case 330 : $show=0;break; // Kunst 
            case 327 : $show=0;break; // Austellungen 
        }
        
        if ($_SESSION[userLevel] > 5) $debug = 1;
        
        if ($show) {


            $region  = $dateList[$i][region];
            if ($region == 192) $region = 187; // Region -> Umland Karlsruhe

            if (!is_array($res[$date])) {
                $res[$date] = $emptyDate;
            }
            if (is_array($res[$date])) {
                if (is_array($res[$date][$category])) {
                    if (is_array($res[$date][$category][$region])) {
                        $res[$date][$category][$region][] = $dateList[$i];
                    } else {
                        if ($debug) {
                            echo ("not exist Region ($region)<br />");
                            echo ($dateList[$i][name]." - ".$dateList[$i][id]." --> <a href='admin_cmsDates.php?view=edit&id=".$dateList[$i][id]."' >Termin bearbeiten</a><br />");
                        }
                    }
                } else {
                    if ($debug) {
                        echo "Not exist Category ($category) in date <br />";
                        echo ($dateList[$i][name]." - ".$dateList[$i][id]." --> <a href='admin_cmsDates.php?view=edit&id=".$dateList[$i][id]."' >Termin bearbeiten</a><br />");
                        // show_array($dateList[$i]);
                    }
                }
            } else {
                if ($debug) {
                    echo "Not exist Date in Res <br />";
                }
            }
        }
    }
    ksort($res);
    return $res;
}

function kalender_showList($selectedDate,$selectedRegionId,$selectedCatId,$highlightDateId,$link) {
    $filter = array();
    list($year,$month,$day) = explode("-",date("Y-m-d"));
    $today = mktime(12,0,0,$month,$day,$year);
    switch ($selectedDate) {
        case "future" :
            $date = date("Y-m-d");
            $anzDays = 10;            
            
            $endDate = $today + $anzDays * (24 * 60 * 60);
            
            $filter[fromDate] = $date;
            
            $endDateStr = date("Y-m-d",$endDate);
            $filter[toDate] = $endDateStr;
           
            break;

        case "today" :
            $date = date("Y-m-d");
            $filter[date] = $date;
            break;
        case "tomorrow" :
            $day = $today + 24 *60 *60;
            $date = date("Y-m-d",$day);
            $filter[date] = $date;
            break;
        case "yesterday" :
            $day = $today - 24 *60 *60;
            $date = date("Y-m-d",$day);
            $filter[date] = $date;
            break;

        case "thisWeek" :
            $startDate = $today;
            while (date(w,$startDate) != 1) {
                $startDate = $startDate - 24 *60 *60;
            }
            
            $startDateStr = date("Y-m-d",$startDate);
            $filter[fromDate] = $startDateStr;
            $endDate = $startDate + 6 * 24 * 60 * 60;
            $endDateStr = date("Y-m-d",$endDate);
            $filter[toDate] = $endDateStr;
            // echo ("Diese Woche = $startDateStr - $endDateStr <br />");

            break;

        case "nextWeek" :
            $startDate = $today;
            while (date(w,$startDate) != 1) {
                $startDate = $startDate + 24 *60 *60;
            }

            $startDateStr = date("Y-m-d",$startDate);
            $filter[fromDate] = $startDateStr;
            $endDate = $startDate + 6 * 24 * 60 * 60;
            $endDateStr = date("Y-m-d",$endDate);
            $filter[toDate] = $endDateStr;
            // echo ("Nächste Woche = $startDateStr - $endDateStr <br />");

            break;


        default :
            $setDate = 0;
            if (strlen($selectedDate)==10) { // maybeday
                list($year,$month,$day) = explode("-",$selectedDate);
                if ($year>0 AND $month>0 AND $day>0) {
                    $filter[date] = $selectedDate;
                    $setDate = 1;
                    //echo ("Seteze DTage rto $selectedDate");
                }
            }

            if (strlen($selectedDate)==7) { // maybeday
                list($year,$month) = explode("-",$selectedDate);
                if ($year>0 AND $month>0 ) {
                    $startDateStr = $year."-".$month."-01";
                    $filter[fromDate] = $startDateStr;
                    $endDateStr = $year."-".$month."-31";
                    $filter[toDate] = $endDateStr;
                    $setDate = 1;
                    // echo ("Zeige Monat = $startDateStr - $endDateStr <br />");
                }
            }


            if ($setDate == 0) {
                echo "unkown DateSelect $selectedDate <br />";
                return 0;
            }

    }


    if ($_POST[kalenderSearchText]) {
        $kalenderSearchText = $_POST[kalenderSearchText];
       //  echo ("<h1>Serach Adresse $dateSearchText</h1>");
        $goLink = kalender_getLink($link, array("search"=>$kalenderSearchText,"date"=>"future"));
        // $goLink = php_clearLink($goLink);
        // echo ($goLink."<br />");
        $goLink = $link."?search=$kalenderSearchText&date=future";
        // if ($_GET[cat])
        reloadPage($goLink,0);
        return "";
    }

    $searchText = $_GET[search];
    if ($searchText) {
        // echo ("<h1>$searchText</h1>");
        $filter["search"] = $searchText;
    }

    ////////////// KUNST UND AUSSTELLUNGEN /////////////////////////////////////
    if ($selectedCatId == "327") {
        kalender_show_art($selectedDate,$selectedRegionId,$selectedCatId,1,$link);
        echo ("</div>");
        return 0;
    }
     ////////////// ENDE VON KUNST UND AUSSTELLUNGEN ///////////////////////////
   
    if ($selectedCatId) $filter[category] = $selectedCatId;
    if ($selectedRegionId) $filter[region] = $selectedRegionId;

    // No FILTER SET ??????
    if (count($filter) == 0) {
        return 0;
    }

    // Set Default Link, if not set
    if (!$link) $link = 'kalender.php';


    // show Only Active Articles
    $filter[show] = 1;

    // Definiert ob der Tagestipp auch in der Liste erscheint
    $showDayTipInList = 1;
    
    $colorizeCategory = 0;

    $useCache = cmsCache_state();
    $useSingleCache = 1;
    if ($useCache) {
        if ($useSingleCache) {
            $replaceSave = cmsCache_replaceStr_save();
            $replaceGet = cmsCache_replaceStr_get();
            $cachePath = cmsCache_getPath($link);
        } else {
            if ($searchText) $useCache = 0;
            else {
                $cachedText = cmsCache_get($link, $filter, $sort);
                if ($cachedText) {
                    echo ($cachedText);
                    return 0;
                }
            }
        }
    }
    
    $dateList = cmsdates_getList($filter,"time","out_");

    if (is_array($dateList) AND count($dateList)==0) {
        echo ("<div class='noData' >");
        echo ("Leider keine Termine für diese Auswahl vorhanden.");        
        echo ("</div>");
        echo ("</div>");
        return "";
    }


    $outText = "";
    if (is_array($dateList) AND count($dateList)) {
        $regionList = categoryGetList("RegionList");
        
        $catList = categoryGetList("TerminCategoryList"); 
        //$regionList = cmsCategory_getList(array("mainCat"=>180,"show"=>1),"id","assoIdList");
        //  $catList = cmsCategory_getList(array("mainCat"=>1,"show"=>1),"id","assoId");

        $catClassList = array();
        foreach($catList as $catId => $catValue) {
            $catName = $catValue[name];
            $shortName = $catValue[shortName];
            $catClassList[$catId] = "tb_".$shortName;
        }


        $sortetList = dates_getSortetList($dateList,$regionList,$catList);


        $timeDiv = 1;
        
        $dontShow = array("info"=>1);
        $dontShow[category]=1;
        $dontShow[region]=1;
        $dontShow[date]=1;
        $dontShow[toDate] = 1;
        $dontShow[link] = 1;
        $dontShow[editLink] = 1;
        $dontShow[linkDate] = 1;
        $dontShow[subName] = 0;
        $dontShow[info] = 0;
        $dontShow[maxDate] = "none";
        $dontShow[cancel] = 0;


        $detailDontShow = array();
        $detailDontShow[name] = 1;
        $detailDontShow[subName] = 1;
        $detailDontShow[info] = 1;
        $detailDontShow[date] = 1;
        $detailDontShow[time] = 1;
        $detailDontShow[image] = 0;
        $detailDontShow[editLink] = 1;

        
        if ($timeDiv) $dontShow[time] = 1;
        $delimiter = "&#149;"; 

        $lastCategory = "";
        $lastRegion = "";
        
       

        foreach ($sortetList as $date => $dateValue) {
            $dateStr = cmsDate_getDayString($date,0);
            $weekDay = cmsDates_dayCode($date);
            $weekDayStr = cmsDates_dayStr($weekDay);

            $className = "datebubble";
            switch ($weekDay) {
                case 6 : $className .= " datebubbleSaturday"; break;
                case 7 : $className .= " datebubbleSunday"; break;
                case 0 : $className .= " datebubbleSunday"; break;
            }

            //echo ("<div class='$className'><span class='cal-day'>$weekDayStr</span><br />$dateStr</div>");
            $outText .= "<div class='$className'><span class='cal-day'>$weekDayStr</span><br />$dateStr</div>";

            // TagesTipp
            $tippIdList = array();
            foreach ($dateValue as $categoryId => $categoryData) {
                foreach ($categoryData as $regionId => $regionData) {
                    for ($i=0;$i<count($regionData);$i++) {
                        $date = $regionData[$i];
                        $dateId = $date[id];
                        $dateName = $date[name];
                        $dateDate = $date[date];
                        $highLight = $date[highlight];
                        // echo ("$dateId $dateName $highLight <br />");
                        if ($highLight) { // Termin ist Tagestipp
                            $tippIdList[$dateId]=1;
                            $tipText = kalender_showTip($date);
                            $outText .= $tipText;
                        }
                        
                    }
                }
            }

            
            $catClass = "";
            
            
            foreach ($dateValue as $categoryId => $categoryData) {

                $categoryName = $catList[$categoryId][name];
                if ($colorizeCategory) {
                    $catClass = $catClassList[$categoryId];
                }
                
                $categoryStr = "<h2 class='calender-categorie-hl $catClass'>$categoryName</h2>";
                if ($selectedCatId == $categoryId) $categoryStr = "";
        
                foreach ($categoryData as $regionId => $regionData) {
                    $regionName = $regionList[$regionId][name];
                    
                    $regionStr = "<h3 class='calender-region-hl'>$regionName</h3>";
                    if ($selectedRegionId == $regionId) $regionStr = "";

                    if (is_array($regionData) AND count($regionData)) {
                        // Termine vorhanden für diese Date/Category/Region
                        $out = "";
                        for ($i=0;$i<count($regionData);$i++) {
                            $date = $regionData[$i];
                            $dateId = $date[id];

                            $showDate = 1;
                            if ($tippIdList[$dateId]) {
                                // Termin ist TagesTipp
                                $showDate = $showDayTipInList;
                            }

                            if ($showDate) {
                                //$goPage = kalender_getLink($link,array("dateId"=>$dateId));

                                $dateClass = "the-date date-info-link";
                                $dateInfoClass = "date-info-link";
                                // show_array($date);
                                if ($date[cancel]) {
                                    $dateClass .= " date-cancel";
                                    $dateInfoClass .= " date-cancel";
                                }
                                
                                $outDate = "";
                               
                                if ($useCache AND $useSingleCache) {
                                   
                                    $cacheFile = cmsCache_getFileName($link,$dateId,"short");
                                    if (file_exists($cachePath.$cacheFile)) {
                                        $outDate = loadText($cachePath.$cacheFile);
                                        
                                        if (is_array($replaceGet)) {
                                            // echo ("Replace GET $replace[0] -> $replace[1] <br />");
                                            $outDate = str_replace($replaceGet[0],$replaceGet[1],$outDate);
                                        }                                        
                                    } 
                                }
                                if (!$outDate) {
                                    $outDate .= "<div class='$dateClass' id='sub_$dateId'>";
                                    
                                    if ($timeDiv) {
                                        $outDate .= "<div style='width:5%;float:left;text-align:right;margin-right:5px;'>";
                                        //$out .= "<span style='text-align:right;width:35px;margin-right:5px;display:inline-block;'>";
                                        $outDate .= cmsDate_getTimeString($date[time],2);
                                        // $out .= "</span>";
                                        $outDate .= "</div><div style='float:left;width:94%'>";

                                    }
                                    if ($date[cancel]) $outDate .= "Abgesagt: ";

                                    //$out .= "<a href='#' class='$dateInfoClass'>";
                                    $outDate .= date_showSmall_str($date,$dontShow, $delimiter );
                                    //$out .= "</a>";

                                    if ($timeDiv) {
                                        $outDate .= "</div>";
                                        $outDate .= "<div class='clearboth'></div>";
                                        // $out .= "</div>";
                                    }

                                    //$out .= "</a>";
                                    $outDate .= "</div>\n";


                                    // Versteckter Text
                                    $outDate .= "<div class='the-dates-detail' id='main_$dateId'>";
                                    $outDate .= kalender_showDateData($date,$detailDontShow);
                                    $outDate .= "</div>\n";
                                    
                                    if ($useCache AND $useSingleCache) {
                                        if (is_array($replaceSave)) {
                                            $outDate = str_replace($replaceSave[0],$replaceSave[1],$outDate);
                                        }   
                                        saveText($outDate,$cachePath.$cacheFile);
                                        if ($_SESSION[userLevel] >= 9) $out.= "<span style='color:#f00;'>Cache File $cacheFile created </span><br />";
                                    }
                                    
                                }

                                if ($_SESSION[userLevel]>5) {
                                    $pos = strlen($outDate) - 7;
                                    $editLink = "admin_cmsDates.php?view=edit&id=$dateId";

                                    $addLink = "<a href='".$editLink.$dateData[id]."'>Termin bearbeiten</a>";
                                    $outDate = substr($outDate,0,$pos).$addLink.substr($outDate,$pos);
                                }
                                $out .= $outDate;
                                
                                // ende Versteckter Text
                            }
                            
                        }
                        if ($out) {
                            if ($categoryStr) {
                                if ($categoryStr != $lastCategory) {
                                    $outText .= $categoryStr;
                                    $lastCategory = $categoryStr;
                                }
                            }
                            if ($regionStr) $outText .= $regionStr;
                            $outText .= $out;
                        }
                    }
                }
            }
        }
      
        $outText .= "</div>\n";
    }


    echo ($outText);
    if ($useCache AND !$useSingleCache) {
        cmsCache_save($link, $filter, $sort, $outText);
    }


}

function kalender_showTip($dateData) {
    $out = "";
    $out .= "<div class='calender-ttipp clearfloat'>";
    $out .= "<h2 class='calender-ttipp-hl'>Tagestipp</h2>";
    
    $regionList = categoryGetList("RegionList");
    $categoryList = categoryGetList("TerminCategoryList");
    
   
    // echo ("<h1>H1</h1><h2>H2</h2><h3>H3</h3><h4>H4</h4>");
    
    $date = $dateData[date];
    $dateId = $dateData[id];
    $showWeekDay = 1;
    if ($dontShow[weekDay]) $showWeekDay = 0;
    $dateStr = cmsDate_getDayString($date,$showWeekDay);

    $toDate = $dateData[toDate]; // = '0000-00-00'
    $toDateStr = cmsDate_getDayString($toDate,$showWeekDay);

    $name = $dateData[name];// = 'Stefan singt'
    $subName = $dateData[subName]; // = 'Premiere'
    $info = $dateData[info]; // = 'Wer will seine Ohren bluten lassen... Wer nicht singen kann sollte es bleiben lassen, finde ich!'
    $longInfo = $dateData[longInfo];
    
    $category = $dateData[category]; // = '2'
    $categoryStr = $categoryList[$category][name];
    
    
    $region = $dateData[region];// = '181'
    // $regionStr = cmsCategory_getName_byId($region);
    $regionStr = $regionList[$region][name];
    
    $location = $dateData[location];// = '349'
    //locationStr = ''
    $time = $dateData[time];// = '20:15:02'
    $timeStr = cmsDate_getTimeString($time,2);

    
    $imageStr = ""; //dummys/tt-dummy.jpg";
    $image = $dateData[image];// = '|7101|7099|7100|158|7102|157|'
    if (intval($image)) $image = "|$image|";
    if ($image) {
       
        $imageList = explode("|",$image);
        if (count($imageList)>2) {
            
            $imageId = $imageList[rand(1,count($imageList)-2)];
            // $out .= "Image = $imageId <br />";
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $showData = array();
                    $showData[frameWidth] = 200;
                    $showData[out] = "url";
                    
                    $imageStr = cmsImage_showImage($imageData, 200, $showData);
                    // $imageStr .= "$image $imageId <br />";
                }
            }
            
        }
    }
    
    if ($imageStr) $out .= "<img src='$imageStr' alt='' />";
    
    
    
    if ($name) $out.="<h3>$name</h3>";
    if ($subName) $out .= "<h4>$subName</h4>";
    
    
    
    if ($dateStr) $out .= $dateStr;
    if ($toDate != "0000-00-00") $out .= " bis ".$toDateStr;
    if ($timeStr) $out .= " - ".$timeStr." Uhr";
    $out .= "<br />";

    // Standard Text
    if ($longInfo) {
        $out .= $longInfo."<br />";       
    } else {
        if ($info) {
        //     $out .= "<div class='calendar_detail_infoBox' >";
            $out .= $info."<br />";
          //  $out .= "</div>";
        }
    }
    
    $out .= "<a href='kalender.php?dateId=$dateId'>Termin zeigen</a>";
    
    
    
    if ($_SESSION[userLevel] > 5) {
        $editLink = "admin_cmsDates.php?view=edit&id=";
        $out .= "<a href='".$editLink.$dateData[id]."'>Termin bearbeiten</a>";
    }
    
    
    // echo ("Lorem ipsum dolor sit amet, consectetuer adipi");
    
    $out .= "</div>";
    return $out;
}


function kalender_showDateData($dateData,$ownDontShow=array()) {
    $outputNew = 1;
   // if ($_SESSION[userLevel] >= 9) $outputNew = 0;
    
    $categoryList = categoryGetList("TerminCategoryList");
    $regionList   = categoryGetList("RegionList");
    

    $dateId = $dateData[id];
    
    $dontShow = array();
    //$dontShow[image] = 1;
    $dontShow[dateRange] = 1;
    $dontShow[weekDay] = 1;
    // $dontShow[weekDay] = 1;
    
    foreach($ownDontShow as $key => $value) {
        $dontShow[$key] = $value;
    }
    
    
   
    $showWeekDay = $dontShow[weekDay];
    
    $date = $dateData[date];
    $dateStr = cmsDate_getDayString($date,$showWeekDay);
    
    $toDate = $dateData[toDate]; // = '0000-00-00'
    $toDateStr = cmsDate_getDayString($toDate,$showWeekDay);

    $name = $dateData[name];// = 'Stefan singt'
    $subName = $dateData[subName]; // = 'Premiere'
    $info = $dateData[info]; // = 'Wer will seine Ohren bluten lassen... Wer nicht singen kann sollte es bleiben lassen, finde ich!'
    $info = str_replace("\r","&nbsp;<br />",$info);
    $image = $dateData[image];
    
    $category = $dateData[category]; // = '2'
    $categoryStr = $categoryList[$category][name];
   
    $region = $dateData[region];// = '181'
    $regionStr = $regionList[$region][name];
    
    $location = $dateData[location];// = '349'
    //locationStr = ''
    $time = $dateData[time];// = '20:15:02'
    $timeStr = cmsDate_getTimeString($time,2);

    $image = $dateData[image];// = '|7101|7099|7100|158|7102|157|'
    
    $locationId = $dateData[location];
    if ($locationId) {
        $locationData = cmsLocation_getById($locationId);
        if (is_array($locationData)) $locationStr = $locationData[name];
    }

    $link = $dateData[link]; // = 'date:1129,1130,1132,1133|article:9650'
    $list = explode("|",$link);
    $linkList = array();
    for ($i=0;$i<count($list);$i++) {
        if ($list[$i]) {
            list($key,$dataStr) = explode(":",$list[$i]);
            $linkList[$key] = $dataStr;
            // echo ("Link #$key = $dataStr<br />");
        }
    }
    
    $imageSize = 100;
    if (intval($dontShow[imageSize])) $imageSize = $dontShow[imageSize];
    
    
    $out = "";

    /// LOCATION
    if (!$dontShow[location]) {
        $locationOut = "";
        if (!$locationData) {
            $locationStr = $dateData[locationStr];
            if ($locationStr) {
                $locationData = array();
                $locationData[name] = $locationStr;
                $locationData[street] = $dateData[data][street];
                $locationData[streetNr] = $dateData[data][streetNr];
                $locationData[plz] = $dateData[data][plz];
                $locationData[city] = $dateData[data][city];
                $locationData[url] = $dateData[data][url];
                $locationData[ticketUrl] = $dateData[data][ticketUrl];
                $locationData[show] = 0;

            }
        }

        if (is_array($locationData)) {
             include_once("incs/adressen.php");
             $dontShowAdress = array();
             $dontShowAdress[subName] = 1;
             $dontShowAdress[phone] = 1;
             //$dontShowAdress[link] = 1;
             //$dontShowAdress[info] = 1;
             $dontShowAdress[showLink] = 0;
             $dontShowAdress[editLink] = 1;
             
             $mode = "div";
             if ($outputNew) $mode = "subDiv";
             $locationOut = adressen_showInfo_str($locationData,$dontShowAdress,$mode);

             
             
             
        }
    }


    if ($locationOut) {
        $out .= $locationOut;
    }
    
   
    
    /// ARTIKEL UND TEMIN LINK
    $linkDateStr = "";
    if ($linkList[date]) {
        
        $maxDate = date("Y-m-d");
        // $maxDate = "2012-10-01";
        $furtherDateList = array();
        // $out .= "Weiter Termine $linkList[date] <br />";
        // Date is not MainDate -> is Linked Date!!!
        if ($dateData[mainId]) {
            $mainDateData = cmsDates_getById($dateData[mainId]);
            if (is_array($mainDateData)) {
                $mainDate = $mainDateData[date];
                // out .= "MainDate $mainDate <br />";
                if ($mainDate >= $maxDate) {
                    $furtherDateList[$mainDate] = $mainDateData;
                }
            }
        }
       
        
        $dateIdList = explode(",",$linkList[date]);
        
        for ($i=0;$i<count($dateIdList);$i++) {
            
            $furtherDateId = $dateIdList[$i];
            // $out .= "$furtherDateId <br />";
            
            if ($dateId == $furtherDateId) {
                // $out .= "Dont Show Date because is the aktDate $dateId <br />";
            } else {
                $furtherDateData = cmsDates_getById($furtherDateId);

                if (is_array($furtherDateData)) {
                    $furtherDate = $furtherDateData[date];
                    $cancel = $furtherDateData[cancel];
                    // $out .= "$furtherDate $cancel <br />";
                    
                    if ($furtherDate >= $maxDate AND $cancel==0 ) {
                        $furtherDateList[$furtherDate] = $furtherDateData;
                    }
                }
            }
        }
        ksort($furtherDateList);

        if (count($furtherDateList)) {

            if (!$outputNew) $linkDateStr .= "	<div class='dates-open-more-dates'>";
            
            $linkDateStr .= '    <h3>Weitere Termine</h3>';
            
            foreach ($furtherDateList as $furtherDate => $furtherDateData) {
                $furtherDateStr = cmsDate_getDayString($furtherDate,$showWeekDay);
                $furtherTimeStr = cmsDate_getTimeString($furtherDateData[time],2);
                $furtherSubName = $furtherDateData[subName];
                $furtherCancel  = $furtherDateData[cancel];
                $dateItemClass = "dates-open-more-date-item";
                if ($furtherCancel) $dateItemClass.= " dates-open-more-date-item-cancel";
                
                $linkDateStr .= "<div class='$dateItemClass'>";
                if ($furtherCancel)  $linkDateStr .= "Abgesagt: ";
                $linkDateStr .= "$furtherDateStr";
                if ($furtherTimeStr) $linkDateStr .= " &#149; $furtherTimeStr";
                if ($furtherSubName) $linkDateStr .= " &#149; <b>$furtherSubName</b>";
                $linkDateStr .= "</div>";               
            }
             if (!$outputNew) $linkDateStr .= "</div>";
        }
    }
    
   
    
    
    $articleStr = "";
    if ($linkList[article]) {
        $articleIdList = explode(",",$linkList[article]);
        $articleDontShow = array();
        $articleDontShow[imageSize] = 75;
        $articleDontShow[subName] = 1;
        $articleDontShow[editLink] = 1;
        $articleDontShow[showLink] = 0;
        $articleDontShow[showImageLink] = 0;

        include_once("incs/articles.php");
        for ($i=0;$i<count($articleIdList);$i++) {
            $articleId = $articleIdList[$i];
            $articleData = cmsArticles_get(array("id"=>$articleId));
            if (is_array($articleData)) {                
                if (!$outputNew) $articleStr .= "	<div class='dates-open-more-dates'>";
                $articleStr .= '    <h3>Artikel zu diesem Termin</h3>';
                $articleImage = $articleData[image];
                if (!intval($articleImage)) {
                    $spiltList = explode("|",$articleImage);
                    $articleImage = $spiltList[1];
                }
                // $articleStr .= "imageId = $articleImage <br />";´                
                $articleStr .= article_showInfo_str($articleData,$articleDontShow);
                if (!$outputNew) $articleStr .= "</div>";
            }
        }
    }
    
    
    
    

    $dateOut = "";
    
    
    if (!$dontShow[name] AND $name ) $dateOut .= "<h3>$name</h3>";
    
    if (!$dontShow[image] AND $image) {
        
        if (intval($image)>0) $image = "|".$image."|";
        $imageList = explode("|",$image);
        if (count($imageList)>=3) {
            $imageOut = "";
            for ($i=1;$i<count($imageList)-1;$i++) {
                $imageId = intval($imageList[$i]);
                // $imageOut .= "$imageId .. $articleImage <br>";
                if ($imageId AND $imageId != $articleImage) {
                    
                    $imageData = cmsImage_getData_by_Id($imageId);
                    
                    if (is_array($imageData)) {
                        
                        $showData = array();
                        $showData[frameWidth] = $imageSize;
                        $imageHeight = floor($imageSize / $imageData[ratio]);
                        $showData[frameHeight] = $imageHeight;
                        $showData[vAlign] = "left";
                        $showData[hAlign] = "top";
                        
                        $imageStr = cmsImage_showImage($imageData,$imageSize, $showData);
                        if ($imageStr) {
                            $imgStr .= "<div class='noborder' style='display:inline-block;width:".$imgSize."px;margin:0 5px 5px 0;' >";
                            $imgStr .= $imageStr;
                            $imgStr .= "</div>";
                        }
                        $imageOut .= "$imgStr";
                      
                    }
                }
            }
            
            if ($imageOut) {
                $dateOut .= "<div class='dates_detail_imageFrame' >";
                $dateOut .= $imageOut;
                $dateOut .= "</div>";
            }
           
        }    
    }
    
    if (!$dontShow[subName] AND $subName) $out .= "<h4>$subName</h4>";

    if (!$dontShow[date] AND $dateStr) $dateOut .= $dateStr."<br />";
    if (!$dontShow[toDate] AND $toDate != "0000-00-00") $dateOut .= $toDateStr."<br />";
    if (!$dontShow[dateRange] AND $date!="0000-00-00" AND $toDate != "0000-00-00") {
        $dateOut .= $dateStr." bis ".$toDateStr."<br />";
    }
    
    if (!$dontShow[time] AND $timeStr) $dateOut .= "Uhrzeit: ".$timeStr."<br />";
    // Standard Text
    if (!$dontShow[info] AND $info) $dateOut .= $info."<br />";        
    
    $dateUrl = $dateData[data][url];
    if (!$dontShow[urlLink] AND $dateUrl AND $dateUrl != $locationData[url]) {
        $linkListUrl = external_link_get($dateUrl);
        //echo ("$linkList ".count($linkList)."<br />");
        for($i=0;$i<count($linkListUrl);$i++) {
            $linkUrl = $linkListUrl[$i][url];
            $linkName = $linkListUrl[$i][name];
            $linkTarget = $linkListUrl[$i][target];
            if (!$linkName) $linkName = "weitere Infos";
           // echo ($linkUrl."<br />");
            $dateOut .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a>";
        }
    }
    
    
    
    
    $dateTicketUrl = $dateData[data][ticketUrl];
    //$dateOut .= "TicketURL = $dateTicketUrl<br />";
    if (!$dontShow[ticketLink] AND $dateTicketUrl AND $dateTicketUrl != $locationData[ticketUrl]) {
        $linkListUrl = external_link_get($dateTicketUrl);
        // $dateOut .= "$linkList ".count($linkList)."<br />";
        for($i=0;$i<count($linkListUrl);$i++) {
            $linkUrl = $linkListUrl[$i][url];
            $linkName = $linkListUrl[$i][name];
            $linkTarget = $linkListUrl[$i][target];
            
            if (!$linkName) $linkName = "Ticket-Webseite";
            $dateOut .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a>";
        }
    }
    
    
    
//    $dateUrl = php_checkUrl($dateData[data][url]);
//    $dateTicketUrl = php_checkUrl($dateData[data][ticketUrl]);
//    if ($dateUrl) $dateOut .= "<a href='$dateUrl' target=''>$dateUrl</a><br />";
//    if ($dateTicketUrl) $dateOut .= "<a href='$dateTicketUrl' target='$linkTarget'>$linkName</a><br />";
//
    if ($dateOut) {
        $out .= "<div class='dates-open-article' >";
        $out .= $dateOut;
        $out .= "</div>";
    }

     if ($linkDateStr) {
        $out .= $linkDateStr;
    }
    if ($articleStr) {
        $out .= $articleStr;
    }

    

  //  if ($_SESSION[userLevel] > 5) $editLink = "admin_cmsDates.php?view=edit&id=";

    // if (!$dontShow[editLink] AND $editLink) $out .= "<a href='".$editLink.$dateData[id]."'>Termin bearbeiten</a>";

    
    if (!$outputNew) return $out;
    
    // return $out;
    
    $out = "";
    // Ausgabe Termin Data
    
    // $dateOut = "Termin Info";
    // $locationOut = "LocationInfo";
    //  $linkDateStr = ""; //wietere Termine Info";
   //  $articleStr = "Artikel Info";
    
    if ($dateOut) {
        if ($out) $out.= "<div class='dates-open-more-dates'>";
        else $out .= "<div class='dates-open-location-info'>";
        $out .= $dateOut;
        $out .= "</div>";
    }
    
    // Ausgabe Weitere Termine
    if ($linkDateStr) {
        if ($out) $out.= "<div class='dates-open-more-dates'>";
        else $out .= "<div class='dates-open-location-info'>";
        $out .= $linkDateStr;
        $out .= "</div>";
    }
    
    // Ausgabe ARTIKEL Data
    if ($articleStr) {
        if ($out) $out.= "<div class='dates-open-more-dates'>";
        else $out .= "<div class='dates-open-location-info'>";
        $out .= $articleStr;
        $out .= "</div>";
    }
    
    // Ausgabe Location  Data
     if ($locationOut) {
        if ($out) $out.= "<div class='dates-open-location-info dates-open-more-dates'>";
        else $out .= "<div class='dates-open-location-info'>";
        // $out .=  "<div class='dates-open-location-info'>\n";
        
        $out .= $locationOut;
        $out .= "</div>";
    }
    
    

    // show_array($linkList);
    return $out;

}


function kalender_showDetail($selectedDate,$selectedRegion,$selectedCategory,$selectedDateId,$link) {
    
    $dateData = cmsDates_getById($selectedDateId);

    if (!is_array($dateData)) {
        echo "Termin nicht gefunden !";
        return 0;
    }
    
    $regionList = categoryGetList("RegionList");
    $categoryList = categoryGetList("TerminCategoryList");
    
    $imagePos = "left"; // "bottom" "left"
    
    kalender_showDetail_Filter($dateData);

    echo ("<div class='dates_detail_box'>");

    $dateId = $dateData[id];

    $date = $dateData[date];
    $showWeekDay = 1;
    if ($dontShow[weekDay]) $showWeekDay = 0;
    $dateStr = cmsDate_getDayString($date,$showWeekDay);

    $toDate = $dateData[toDate]; // = '0000-00-00'
    $toDateStr = cmsDate_getDayString($toDate,$showWeekDay);

    $name = php_clearOutput($dateData[name]);// = 'Stefan singt'
    $subName = php_clearOutput($dateData[subName]); // = 'Premiere'
    $info = php_clearOutput($dateData[info]); // = 'Wer will seine Ohren bluten lassen... Wer nicht singen kann sollte es bleiben lassen, finde ich!'
    $longInfo = php_clearOutput($dateData[longInfo]);
    
    $category = $dateData[category]; // = '2'
    $categoryStr = $categoryList[$category][name];
    // $categoryStr = cmsCategory_getName_byId($category);
    
    $region = $dateData[region];// = '181'
    $regionStr = $regionList[$region][name];
    // $regionStr = cmsCategory_getName_byId($region);
    
    $location = $dateData[location];// = '349'
    //locationStr = ''
    $time = $dateData[time];// = '20:15:02'
    $timeStr = cmsDate_getTimeString($time,2);

    $image = $dateData[image];// = '|7101|7099|7100|158|7102|157|'

    $locationId = $dateData[location];
    if ($locationId) {
        $locationData = cmsLocation_getById($locationId);
        if (is_array($locationData)) $locationStr = $locationData[name];
    }

    $link = $dateData[link]; // = 'date:1129,1130,1132,1133|article:9650'
    $list = explode("|",$link);
    $linkList = array();
    for ($i=0;$i<count($list);$i++) {
        if ($list[$i]) {
            list($key,$dataStr) = explode(":",$list[$i]);
            $linkList[$key] = $dataStr;
            // echo ("Link #$key = $dataStr<br />");
        }
    }
    
    
    if ($name) echo("<h1>$name</h1> ");
    if ($subName) echo ("<h2>$subName</h2>");
    
    
    $leftStr = "";
        
    echo("<p />");
    echo("<div class='clearfloat'>");

    
    // Temine
    if ($toDate != "0000-00-00") {
        $leftStr .= "$dateStr bis $toDateStr <br />";
    } else {
        $leftStr .= "$dateStr <br />";
    }
    
    // Uhrzeit
    if ($time != "00:00:00") {
        $leftStr .= "Uhrzeit: $timeStr <br />";
    }
    
    // Standard Text / Langer Text
    if ($longInfo) {
        $leftStr .= $longInfo."<br />";
    } else {
       if ($info) {
            $leftStr .= $info."<br />";
            // echo ($info);            
        }
    }
    
    /// IMAGE 
     if (intval($image)>0) $image = "|".$image."|";
    // $image .= "1000|2000|";
    
    $imageList = explode("|",$image);
    // echo ("ImageList Anzahl =".count($imageList)." '$image' <br />");
    if (count($imageList)>2) {
        $imageStr = "";
        $imageStr .= "<div class='article-adress-images-gallery clearfloat'>";
        // echo ("Image List Count = ".count($imageList)." '$image' <br />");
        $showData_Small = array();
        //  $showData_Small[frameWidth] = 200;
        $showData_Small[frameHeight] = 120;
       //  $showData_Small[ratio] = 4/3;
        $showData_Small[vAlign] = "top";
        $showData_Small[hAlign] = "left";
        $showData_Small[out] = "url";


        $showData_Big = array();
        $showData_Big[frameWidth] = 800;
        $showData_Big[frameHeight] = 800;
        $showData_Big[vAlign] = "top";
        $showData_Big[hAlign] = "left";
        $showData_Big[out] = "url";
        for ($i=1;$i<count($imageList)-1;$i++) {
            $imageId = intval($imageList[$i]);
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    $imgStr_small = cmsImage_showImage($imageData,0, $showData_Small);
                    $imgStr_big = cmsImage_showImage($imageData,800, $showData_Big);
                    $imageStr .= "<div class='article-adress-images'>";
                    $imageStr .= "<a href='$imgStr_big' class='zoomimage' title='vergr&ouml;&szlig;ern'>";
                    $imageStr .= "<img src='$imgStr_small' class='noborder' alt='' />";
                    $imageStr .= "</a>";
                    $imageStr .= "</div>";                    
                }
            }
        }
        $imageStr .= "</div>";      
    }
    
    if ($imagePos == "left" AND $imageStr) {
        $leftStr .= $imageStr;
    }
    
    

    $linkString = "";
    $dateUrl = $dateData[data][url];
    if (!$dontShow[urlLink] AND $dateUrl AND $dateUrl != $locationData[url]) {
        $linkListUrl = external_link_get($dateUrl);
        for($i=0;$i<count($linkListUrl);$i++) {
            $linkUrl = $linkListUrl[$i][url];
            $linkName = $linkListUrl[$i][name];
            $linkTarget = $linkListUrl[$i][target];
            if (!$linkName) $linkName = "weitere Infos";
           // echo ($linkUrl."<br />");
            $linkString .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a>";
        }
    }

    $dateTicketUrl = $dateData[data][ticketUrl];
    if (!$dontShow[ticketLink] AND $dateTicketUrl AND $dateTicketUrl != $locationData[ticketUrl]) {
        $linkListUrl = external_link_get($dateTicketUrl);
       //  echo ("$linkListUrl ".count($linkListUrl)."<br />");
        for($i=0;$i<count($linkListUrl);$i++) {
            $linkUrl = $linkListUrl[$i][url];
            $linkName = $linkListUrl[$i][name];
            $linkTarget = $linkListUrl[$i][target];

            if (!$linkName) $linkName = "weitere Infos";
            $linkString .= "<a href='$linkUrl' class='externalTextLink' target='$linkTarget'>$linkName</a>";
        }
    }
    
    if ($linkString) $leftStr .= "&nbsp;<br />".$linkString;

    if ($leftStr) {
        // linkes DIV
        echo("<div class='article-content-left'>");
        echo ($leftStr);
        echo("</div>");

        // rechtes DIV
        echo("<div class='article-content-right'>");
        $rightClass = "article-content-right-infos";
    } else {
        $rightClass = "article-content-all-infos";
    }
    
    // Rechtes Div oder gesamte Breite
   
   
    
    if (!is_array($locationData)) {
//        //echo ("<div class='dates_detail_location' >");
//        include_once("incs/adressen.php");
//        // echo ("<h2>Veranstaltungsort</h2>");
//        adressen_showInfo($locationData);
//        //echo "Info über Location $locationData[name] <br />";
//       
//    } else {
        $locationStr = $dateData[locationStr];
        if ($locationStr) {
            $locationData = array();
            $locationData[name] = $locationStr;
            $locationData[street] = $dateData[data][street];
            $locationData[streetNr] = $dateData[data][streetNr];
            $locationData[plz] = $dateData[data][plz];
            $locationData[url] = $dateData[data][url];
            $locationData[ticketUrl] = $dateData[data][ticketUrl];
            
        }
    }
    
    if (is_array($locationData)) {
        
        $locationDontShow = array();
        $locationDontShow[showLink] = 1;
        $locationDontShow[editLink] = 1;
        $locationDontShow[subName] = 1;
        $locationDontShow[infoGoLink] = 0;
         // Adresse
        echo("<div class='$rightClass right-info-link-box'>");
        echo("<div class='$rightClass-inner'>");
        
        include_once("incs/adressen.php");
        $res = adressen_showInfo_str($locationData,$locationDontShow);   
        echo ($res);
        echo("</div>");
        echo("</div>");
    }
    
   
    
    
    if ($linkList[date]) {
        // get Sortet FurtherDate list, $date after maxDate
        $maxDate = date("Y-m-d");
        $dateIdList = explode(",",$linkList[date]);
        $furtherDateList = array();
        
        if ($dateData[mainId]) { // selected Date is LinkedDate
            $mainDateData = cmsDates_getById($dateData[mainId]);
           
            if (is_array($mainDateData)) {
                $mainDateId = $mainDateData[$i];
                $mainDateDate = $mainDateData[date];
                if ($mainDateDate >= $maxDate AND $mainDateId != $dateId) {
                    $furtherDateList[$mainDateDate] = $mainDateData;
                }
            }
            
        }
        
        
        for ($i=0;$i<count($dateIdList);$i++) {
            $furtherDateId = $dateIdList[$i];
            $furtherDateData = cmsDates_getById($furtherDateId);
            if (is_array($furtherDateData)) {
                $furtherDate = $furtherDateData[date];
                if ($furtherDate >= $maxDate AND $furtherDateId != $dateId) {
                    $furtherDateList[$furtherDate] = $furtherDateData;
                }
            }
        }
        
        ksort($furtherDateList);

        if (count($furtherDateList)) {
            echo("<div class='$rightClass'>");
            echo("<div class='$rightClass-inner'>");
                       
            echo ("<h3>Weitere Termine</h3>");
            foreach ($furtherDateList as $furtherDate => $furtherDateData) {
                $furtherDateStr = cmsDate_getDayString($furtherDate,$showWeekDay);
                $furtherTimeStr = cmsDate_getTimeString($furtherDateData[time],2);
                $furtherSubName = php_clearOutPut($furtherDateData[subName]);
                
                // show_array($furtherDateData);
                $furtherInfo = php_clearOutPut($furtherDateData[info]);
                echo ("$furtherDateStr<br/>");
                echo ("$furtherTimeStr");
                
                
                if ($furtherSubName) echo ("  &#149; ".$furtherSubName);
                if ($furtherInfo) echo ("  &#149; ".$furtherInfo);
                echo ("<p />");
            }
            echo ("</div>");
            echo ("</div>");
        }
    }

    if ($linkList[article]) {
        $articleIdList = explode(",",$linkList[article]);
       
        if (count($articleIdList)) {
            
            $dontShow = array();
            $dontShow[image] = 0;
            $dontShow[showLink] = 0;
            $dontShow[showImageLink] = 0;
            
            
            include_once("incs/articles.php");
            echo("<div class='$rightClass'>");
            echo("<div class='$rightClass-inner'>");
            for ($i=0;$i<count($articleIdList);$i++) {
                $articleId = $articleIdList[$i];
                $articleData = cmsArticles_get(array("id"=>$articleId));
                if (is_array($articleData)) {
                    $articleStr = article_showInfo_str($articleData,$dontShow);
                    if ($articleStr) {
                        echo ("<div class='dates_detail_articles' >");
                        echo ("<h3>Artikel zu diesem Termin </h3>");
                        echo ($articleStr);
                        echo ("</div>");
                    }
                }
            }
            echo ("</div>");
            echo ("</div>");
        }
    }
    
    
    
    
    if ($leftStr) { // rechtes DIV zu
        echo ("</div>");        
    }

    echo ("</div>");

    
    // echo ("ImagePos = $imagePos <br />");
    if ($imagePos == "bottom" AND $imageStr ) {
        echo ($imageStr);
    }
    
   



    if ($_SESSION[showLevel]>=8) {
        echo ("<a href='admin_cmsDates.php?view=edit&id=$dateId'>Termin bearbeiten</a><br />");
    }
   
     echo("</div>");
    //show_array($dateData);

}

function kalender_showDetail_Filter($dateData) {
    echo ("<div class='current-info-container'>");
    
    echo ("<a href='kalender.php'>Kalender</a> ");
   
    $category = $dateData[category];
    switch ($category) {
        case 327 : $mode = "art"; break;
        case 330 : $mode = "art"; break;        
        default : 
            $mode = "date";
    }
    
    $regionList = categoryGetList("RegionList");
    $categoryList = categoryGetList("TerminCategoryList");
    
    if ($mode == "art") {
        $categoryStr = $categoryList[327][name];
        $shortName   = $categoryList[327][shortName];
        if ($category) {
            $categoryStr = "Kunst und Ausstellungen";
            $categoryStr = $categoryList[327][name];
            $shortName   = $categoryList[327][shortName];
            // $categoryStr = cmsCategory_getName_byId($category);
            $goPage = php_clearLink("kalender.php?cat=$shortName");
            // if ($date) $goPage .= "&date=$date";
           // $goPage .= "&cat=$shortName";
            echo ("<span class='quo'>›</span> ");
            echo (" <a href='$goPage'>$categoryStr</a> ");        
        }
        
        $region = $dateData[region];
        if ($region) {
            // $regionStr = cmsCategory_getName_byId($region);
            $regionStr = $regionList[$region][name];
            $shortRegionName = $regionList[$region][shortName];
            echo ("<span class='quo'>›</span> ");
            $goPage = "kalender.php?region=$shortRegionName";
            $goPage .= "&cat=$shortName";
            //            if ($date) $goPage .= "&date=$date";
            $goPage = php_clearLink($goPage);
            echo (" <a href='$goPage'>$regionStr</a> ");                
        }
        
        
        
        
    } else {


        

        $date = $dateData[date];
        if ($date) {
            $dateStr = cmsDate_getDayString($date,0);
            echo ("<span class='quo'>›</span> ");
            echo (" <a href='kalender.php?date=$date'>$dateStr</a> ");
        }

        $region = $dateData[region];
        if ($region) {
            // $regionStr = cmsCategory_getName_byId($region);
            $regionStr = $regionList[$region][name];
            $shortRegionName = $regionList[$region][shortName];
            echo ("<span class='quo'>›</span> ");
            $goPage = "kalender.php?region=$shortRegionName";
            if ($date) $goPage .= "&date=$date";
            $goPage = php_clearLink($goPage);
            echo (" <a href='$goPage'>$regionStr</a> ");                
        }

        $category = $dateData[category];
        if ($category) {
            $categoryStr = $categoryList[$category][name];
            $shortName   = $categoryList[$category][shortName];
            // $categoryStr = cmsCategory_getName_byId($category);
            $goPage = "kalender.php?region=$shortRegionName";
            if ($date) $goPage .= "&date=$date";
            $goPage .= "&cat=$shortName";
            $goPage = php_clearLink($goPage);
            echo ("<span class='quo'>›</span> ");
            echo (" <a href='$goPage'>$categoryStr</a> ");        
        }
    }
    
    echo ("</div>");
    echo ("<div class='slidespacer'> </div>");
}

function kalender_show_art($selectedDate,$selectedRegionId,$selectedCatId,$showHeadline,$link) {
    // echo ("articles_show_art($selectedSubCatId,$link)<br />");

   
    $mode = "Location"; // Holt Location und sucht dann nach Terminen
    $mode = "Dates"; // Holt Kunst/Ausstellungen und sucht dann nach Location
    
    
    $link = "kalender.php";
    
    $useCache = cmsCache_state();
    $useSingleCache = 1;
    if ($useCache) {
        if ($useSingleCache) {
            $replaceSave = cmsCache_replaceStr_save();
            $replaceGet = cmsCache_replaceStr_get();
            $cachePath = cmsCache_getPath($link);
        } else {
            if ($searchText) $useCache = 0;
            else {
                $cachedText = cmsCache_get($link, $filter, $sort);
                if ($cachedText) {
                    echo ($cachedText);
                    return 0;
                }
            }
        }
    }
    
    
    
    $dontShow = array();
    $dontShow[category]=1;
    $dontShow[region]=1;
    $dontShow[location]=1;
    $dontShow[date]=1;
    $dontShow[toDate]=1;
    $dontShow[time]=1;
    $dontShow[dateRange]=0;
    $dontShow[weekDay]=0;
    $dontShow[link] = 1;
    $dontShow[editLink] = 1;
    $dontShow[linkDate] = 1;
    $dontShow[subName] = 0;
    $dontShow[info] = 1;
    $dontShow[maxDate] = "none";
    
    // Detail SHOW
    $dontShowDetail = array();
    $dontShowDetail[category]=1;
    $dontShowDetail[region]=1;
    $dontShowDetail[location]=1;
    $dontShowDetail[date]=1;    
    $dontShowDetail[toDate]=1;
    $dontShowDetail[time]=1;
    $dontShowDetail[dateRange]=0;
    $dontShowDetail[weekDay]=0;
    $dontShowDetail[link] = 0;
    $dontShowDetail[editLink] = 1;
    $dontShowDetail[linkDate] = 0;
    $dontShowDetail[subName] = 0;
    $dontShowDetail[maxDate] = "none";
    
    if ($mode == "Dates") {
        $dontShowDetail[location] = 0;
    }
    
    
    $filter = array();
    $filter["show"] =1;
    if ($selectedRegionId) $filter[region] = $selectedRegionId;
    
    list ($year,$month,$day) = explode("-",date("Y-m-d"));
    $today = mktime(12,0,0,$month,$day,$year);
    switch ($selectedDate) {
        case "today" :
            $fromDate = date("Y-m-d");
            $toDate   = date("Y-m-d");            
            break;
        
        case "tomorrow" :
            $day = $today + 24 *60 *60;
            $fromDate = date("Y-m-d",$day);
            $toDate   = date("Y-m-d",$day);   
            break;
        
        case "yesterday" :
            $day = $today - 24 *60 *60;
            $fromDate = date("Y-m-d",$day);
            $toDate   = date("Y-m-d",$day);   
            break;

        case "thisWeek" :
            $startDate = $today;
            while (date(w,$startDate) != 1) {
                $startDate = $startDate - 24 *60 *60;
            }
            $endDate = $startDate + 6 * 24 * 60 * 60;
            
            $fromDate = date("Y-m-d",$startDate);
            $toDate   = date("Y-m-d",$endDate);
            break;

        case "nextWeek" :
            $startDate = $today;
            while (date(w,$startDate) != 1) {
                $startDate = $startDate + 24 *60 *60;
            }
            $endDate = $startDate + 6 * 24 * 60 * 60;
            
            $fromDate = date("Y-m-d",$startDate);
            $toDate   = date("Y-m-d",$endDate);
            break;
            
        case "future" :
            $fromDate = date("Y-m-d");
            break;
        
        default :
            if (strlen($selectedDate) == 10) { // DATUM
                $fromDate = $selectedDate;
                $toDate   = $selectedDate;
            } else {
                 echo ("unkown $selectedDate <br />");
            }
    }
    
    if ($fromDate AND $toDate) {
        $filter[date] = "<='$fromDate'";
        $filter[toDate] = ">='$toDate'";
    } else {
        $filter[toDate] = ">='$fromDate'";
    }
    // echo "Zeige Austellungen von $fromDate -> $toDate <br />";
    
    
        
    
    
    
    // if ($timeDiv) $dontShow[time] = 1;
    $delimiter = "&#149;"; 
    
    
    $showDateUntil = date("Y-m-d");
    
    switch ($mode) {
        
            
            
        case "Dates" :
             // noch nicht vorbei (auch kommende
            // $filter[active] = "date";  // laufen

            switch ($selectedCatId) {
                case 309 : // Auststellungen
                    $filter[category] = 327;
                    break;
                case 308 : // Kunst
                    $filter[category] = 330;
                    break;
                default :
                    $filter[special] = "art";
                   
            }
            $sort = "date";
            // echo ("Selected Sub Cat = $selectedSubCatId $selectedCatId <br />");
            
            $dateList = cmsDates_getList($filter,$sort,"out__");
            
            
            // Create LocationList with Dates
            // $locationList = array();
            for ($d=0;$d<count($dateList);$d++) {
                $locationId  = $dateList[$d][location];
                $locationStr = $dateList[$d][locationStr];
                
                if (intval($locationId)) {
                    $locationData = cmsLocation_getById($locationId);
                    if (is_array($locationData)) {
                        $locationStr = $locationData[name];
                    }
                }
                if ($locationStr) {
                    if (!is_array($locationList[$locationStr])) {
                        $locationList[$locationStr] = array("name"=>$locationStr,"id"=>$locationId,"dates"=>array());                        
                    }
                    $locationList[$locationStr][dates][] = $dateList[$d];
                }
            }
            
            ksort($locationList);
            
            $showWeekDay = 0;
            $out = "";
            $dateDiv = 1;
            if ($dateDiv) $dontShow[dateRange] = 1;
            foreach ($locationList as $locationName => $data) {
                $locationStr = "";
                
                for($i=0;$i<count($data[dates]);$i++) {
                    $date = $data[dates][$i];
                    $dateId = $data[dates][$i][id];
                    $dateName = $data[dates][$i][name];
                    $dateFrom = $data[dates][$i][date];
                    $dateTo   = $data[dates][$i][toDate];
                    $category  = $data[dates][$i][category];
                    
                    
                    $outDate = "";
                    $outAdd = "";      
                    if ($useCache AND $useSingleCache) {
                        $cacheFile = cmsCache_getFileName($link,$dateId,"art");
                        if (file_exists($cachePath.$cacheFile)) {
                            $outDate = loadText($cachePath.$cacheFile);

                            if (is_array($replaceGet)) {
                                // echo ("Replace GET $replace[0] -> $replace[1] <br />");
                                $outDate = str_replace($replaceGet[0],$replaceGet[1],$outDate);
                            }                                        
                        } 
                        // $outAdd.= "vom Cache $cachePath $cacheFile<br />";
                    }
                   
                    if (!$outDate) {
                        
                        $dateClass = "the-date date-info-link ";
                        $dateInfoClass = "date-info-link";
                        // if ($dateDiv) $dateClass .= " clearfloat";
                        $outDate .= "<div class='$dateClass' id='sub_$dateId'>";
                        if ($dateDiv) {
                            $dateRangeStr = cmsDate_getDayString($dateFrom)." bis ".cmsDate_getDayString($dateTo);
                            $outDate .= "<div class=artDateRange style='float:left;width:24%;'>";
                            $outDate .= $dateRangeStr;
                            $outDate .= "</div><div style='float:left;width:76%;' >";
                        }

                        $outDate .= date_showSmall_str($date,$dontShow, $delimiter );
                        if ($dateDiv) {
                            $outDate .= "</div>";
                            $outDate .= "<div class='clearboth'></div>";
                        }
                        $outDate .= "</div>\n";


                        // Versteckter Text
                        $outDate .= "<div class='the-dates-detail' id='main_$dateId'>";
                        $outDate .= kalender_showDateData($date,$dontShowDetail);
                        $outDate .= "</div>\n";      
                        
                        if ($useCache AND $useSingleCache) {
                            if (is_array($replaceSave)) {
                                $outDate = str_replace($replaceSave[0],$replaceSave[1],$outDate);
                            }   
                            saveText($outDate,$cachePath.$cacheFile);
                            if ($_SESSION[userLevel] >= 9) $outAdd = "<span style='color:#f00;'>Cache File $cacheFile created </span><br />";
                        }

                        
                        
//                        Cache File ausgabe_4547_art.cache created
//Cache File ausgabe_4550_art.cache created 
                        
                        
                    }
                    
                    
                    // Add $editLink to OutPut from Date
                    if ($_SESSION[userLevel]>5) {
                            $pos = strlen($outDate) - 7;
                            $editLink = "admin_cmsDates.php?view=edit&id=$dateId";

                            $addLink = "<a href='".$editLink.$dateData[id]."'>Termin bearbeiten</a>";
                            $outDate = substr($outDate,0,$pos).$addLink.substr($outDate,$pos);
                        }
                    
                    $locationStr .= $outDate;
                    if ($outAdd) $locationStr .= $outAdd;
                    
                }
                if ($locationStr) {
                    if ($out != "") $out.= "<p>";
                    $out .= "<h4>$locationName</h4>";
                    $out .= $locationStr;
                }                
            }
            break;
            
        case "Location" :
            $catList = array("Museen"=>25,"Galerien"=>28);
            $out = "";
            // ************************* GET Locations for CatList ********************** ///
            foreach ($catList as $catName => $catId) {
                $locationList = cmsLocation_getList(array("category"=>$catId,"show"=>1),"name");
                $catStr = "";
                if (is_array($locationList) and count($locationList)) {

                    for ($i=0;$i<count($locationList);$i++) {
                        $locName = $locationList[$i][name];
                        $locId   = $locationList[$i][id];
                        $locUrl  = $locationList[$i][url];


                        // ************* SEARCH FOR DATES for Location ****************** ///
                        
                        // showDateUntil = "2012-09-01";

                        $filter = array();
                        $filter["show"] =1;
                        $filter["location"] = $locId;
                        $filter["toDate"] = ">='$showDateUntil'";

                        switch ($selectedSubCatId) {
                             case 309 : // Auststellungen
                                 $filter[category] = 327;
                                 break;
                             case 308 : // Kunst
                                 $filter[category] = 330;
                                 break;
                        }
                        $sort = "date";
                        
                        $dateList = cmsDates_getList($filter,$sort,"out__");
                        // echo ("DateList = '$dateList' <br />");
                        if (is_array($dateList) AND count($dateList)) {
                            // ********* TERMINE GEFUNDEN FÜR Location ****************** ///
                            // add Location To OutPut
                            $locationStr = "";
                            

                            for ($d=0;$d<count($dateList);$d++) {
                                $date = $dateList[$d];
                                $dateName = $dateList[$d][name];
                                $dateFrom = $dateList[$d][date];
                                $dateTo   = $dateList[$d][toDate];
                                $category  = $dateList[$d][category];
                                $showWeekDay = 0;
                                if ($dateFrom) $dateFromStr = cmsDate_getDayString($dateFrom,$showWeekDay);
                                else $dateFromStr = "";
                                if ($dateTo) $dateToStr = cmsDate_getDayString($dateTo,$showWeekDay);
                                else $dateToStr = "";


                                $locationStr .= " -> $dateName ($dateFromStr bis $dateToStr) $category <br />";
        
                            }
                            if ($locationStr) {
                                $catStr .= "<a href='adressen.php?location=$locId' >";
                                $catStr .= "<h3>$locName </h3>\n";
                                $catStr .= "</a>";
                                $catStr .= $locationStr;
                            }
                        }

                    }
                    if ($catStr) {
                        $out .= "<h2>$catName</h2>";
                        $out .= $catStr;
                    }
                }
            }    
            break;            
            
        
        default :
            echo ("UNKOWN MODE IN Show Kunst und Ausstellungen ");
    }
    
    if ($out) {
        switch ($selectedCatId) {
            case 309 : $headline = "Ausstellungen"; break;// Auststellungen
            case 308 : $headline = "Kunst"; break;// Kunst
            default :
                $headline = "Kunst und Ausstellungen";
        }        
        
       // echo ("<div class='content_box box-shadow current_mag'>");
        // echo ("<h1 class='hl_art'>$headline</h1>");
        if ($showHeadline) echo ("<h2 >$headline</h2>");
        echo ("<div class='current_mag_content'>");
        
//        echo ("<div class='articles_art_frame'>\n");
//        echo ("<div class='articles_art_headline' >");
     
       
        echo ($out);
         echo ("</div>\n");
       // echo ("</div>");
    }    
}



?>
