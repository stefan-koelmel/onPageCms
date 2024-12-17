<?php // charset:UTF-8

class cmsType_dateList_base extends cmsType_contentTypes_base {


    function getName (){
        return "Termin List";
    }


    function dateList_show($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $padding = 0;
        $innerWidth = $frameWidth - 2 * $padding;

       
        //div_start("dateList","background-color:#ccf;width:".$innerWidth."px;margin:5px 0 5px 0;padding:".$padding."px;");
        div_start("dateList");
        echo ("<h1>TERMINE </h1>"); 
        //show_array($data);
        switch ($data[viewMode]) {
            case "list" :
                $dateId = intval($_GET[dateId]);
                if ($dateId>0) {
                    $res = $this->showDate($dateId,$contentData,$innerWidth);                 
                } else {
                    $this->showList($contentData,$innerWidth);
                }
                break;
            case "month" :
                $this->dateList_showMonth($contentData,$innerWidth,$filter,$sort);
                // $this->dateList_showMonthSlider($contentData,$innerWidth,$filter,$sort);
                break;
            
            case "monthSlider" :
                $this->dateList_showMonthSlider($contentData,$innerWidth,$filter,$sort);
                break;
            
            
            case "week" :
                $this->dateList_showWeek($contentData,$innerWidth,$filter,$sort);
                break;
            default :
                echo ("unkown dateList ViewMode '$data[viewMode]<br />");

        }
        div_end("dateList","before");





    }

    function showList($contentData,$innerWidth) {
        $filter = array();
        $sort = "date";

        $_data = array();
        $this->showList_customFilter($contentData,$frameWidth);
        foreach ($_GET  as $key => $value) $_data[$key] = $value;
        foreach ($_POST as $key => $value) $_data[$key] = $value;

        foreach ($_data as $key => $value) {

            switch ($key) {
                case "sort" : break;
                case "page" : break;

                case "date" :
                    $day = substr($value,8,2);
                    $month = substr($value,5,2);
                    $year = substr($value,0,4);
                    $selectDay = mktime(12,0,0,$month,$day,$year);
                    $dayCode = intval(date("w",$selectDay));
                    $dayStr = cmsDates_dayStr($dayCode);
                    echo ("Datum = $dayStr, den ".date("d.m.Y",$selectDay)."<br />");
                    $filter[date] = $value;
                    break;

                case "category" : if ($value) $filter[$key] = $value; break;
                case "filter_category" : if ($value) $filter["category"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;
                case "filter_region"   : if ($value) $filter["region"] = $value; break;
                case "region"   : if ($value) $filter[$key] = $value; break;

                case "filter_dateRange"   :
                    // echo ("<h1> FILTER DATERANGE $value</h1>");
                    $dateRangeList = $this->dateRange_filter_select_getList();
                    //show_array($dateRangeList);
                    $dateRange = $dateRangeList[$value];
                    if (is_array($dateRange)) {
                        if ($dateRange[filter]) {
                            foreach ($dateRange[filter] as $filterKey => $filterValue) $filter[$filterKey] = $filterValue;
                        }
                        if ($dateRange[sort]) $sort = $dateRange[sort];
                    }
                    break;

                case "dateRange"   : if ($value) $filter[$key] = $value; break;

                case "filter_specialView"   :
                    $specialFilterList = $this->customFilter_specialView_getList();
                    if (is_array($specialFilterList[$value])) {
                        $specialFilter = $specialFilterList[$value];
                        // APPEND Filter to Filter
                        if (is_array($specialFilter[filter])) {
                            foreach($specialFilter[filter] as $key => $value ) {
                                $filter[$key] = $value;
                                // echo "append $key = $value to filter <br />";
                            }
                        }
                        if ($specialFilter[sort]) {
                            if (is_string($specialFilter[sort]))$sort = $specialFilter[sort];
                        }

                    } else {
                        echo ("Filter SpecialView $key = $value <br />");
                    }
                    break;

                default :
                    //echo ("Unkown $key in get/post_data = $value <br />");

            }
        }
        $this->dateList_showList($contentData,$innerWidth,$filter,$sort);
    }

    function showDate($dateId,$contentData,$innerWidth) {
        // go Back Page
        foreach ($_GET as $key => $value) {
            if ($key != "dateId") {
                if ($goPage == "") $goPage .= "?";
                else $goPage.="&";
                $goPage .= "$key=$value";
            }
        }
        $goPage = $GLOBALS[pageInfo][page].$goPage;
       
        $dates = cmsDates_get(array("id"=>$dateId));
        if (!is_array($dates)) {
            echo ("Termin nicht gefunden <br />");
            echo ("<a href='".$pageInfo[page].$goPage."' class='cmsLinkButton' >zurück</a><br />");
            return 0;
        }


        foreach ($dates as $key => $value ) {
            switch ($key) {
                case "id"          : $dateId      = $dates[$key]; break;
                case "name"        : $name        = $dates[$key]; break;
                case "subName"     : $subName     = $dates[$key]; break;
                case "info"        : $info        = $dates[$key]; break;
                case "location"    : $location    = $dates[$key]; break;
                case "locationStr" : $locationStr = $dates[$key]; break;
                case "date"        :
                    $date        = $dates[$key];
                    $dateStr = cmsDate_getDayString($date,1);
                    break;
                case "time"        : 
                    $time        = $dates[$key];
                    $timeStr = cmsDate_getTimeString($time,3);
                    break;
                case "region"      : $region      = $dates[$key]; break;
                case "category"    : $category    = $dates[$key]; break;
                case "link"        : $link        = $dates[$key]; break;
                case "image"       : 
                    $imageList = array();
                    $image       = $dates[$key];
                    if ($image) {
                        if (intval($image)>0) $imageList[] = image;
                        else { // not simpleImage
                            $ofSet = strpos($image,"|");
                            if (is_integer($ofSet)) {
                                $imgList = explode("|",$image);
                                for ($i=0;$i<count($imgList);$i++) {
                                    if (intval($imgList[$i])>0) $imageList[] = $imgList[$i];
                                    // echo ("Image $i = '$imgList[$i]'<br />");
                                }
                            }

                        }
                    }
                    break;

                case "data"        : $data        = $dates[$key]; break;
                case "mainId"      : $mainId      = $dates[$key]; break;
                case "show"        : break;
                case "lastMod"     : break;
                case "changeLog"   : break;
                
                default :
                    echo ("$key = $value <br />");
            }            
        }

        echo ("<h3>$dateStr $timeStr Uhr</h3>");

        echo ("<h1>$name</h1>");
        echo ("<h2>$subName</h2>");
        echo ("$info <br />");
        echo ("&nbsp;<br />");

        if ($category) echo ("Kategorie : ".cmsCategory_getName_byId($category)."<br />");
        if ($region)   echo ("Region : ".cmsCategory_getName_byId($region)."<br />");
        
        echo ("<h3>Ort </h3>");
        if ($location>0) {
            $locationData = cmsLocation_getById($location);
            echo ("<b>$locationData[name]</b><br />");
            if ($locationData[subName]) echo ("$locationData[name]<br />");
            echo ("$locationData[street] $locationData[streetNr]<br />");
            echo ("$locationData[plz] $locationData[city]<br />");

            if ($locationData[phonePhone]) echo ("Telefon: $locationData[phoneRegion] $locationData[phonePhone]<br />");
            if ($locationData[phoneFax]) echo ("Telefax: $locationData[phoneRegion] $locationData[phoneFax]<br />");

            if ($locationData[url]) echo ("$locationData[url]<br />");
            if ($locationData[ticketUrl]) echo ("$locationData[ticketUrl]<br />");



            // show_array($locationData);
        }


        for($i=0;$i<count($imageList);$i++) {
            $imageId = $imageList[$i];
            if ($imageId) {
                $imageData = cmsImage_getData_by_Id($imageId);
                $showData = array();
                $showData[frameWidth] = 200;
                $showData[frameHeight] = 150;
                $showData[hAlign] = "center";
                $showData[vAlign] = "middle";
                div_start("imageBox","display:inline-block;width:200px;height:150px;");
                $imageStr = cmsImage_showImage($imageData,200,$showData);
                echo ("$imageStr");
                div_end("imageBox");
            }
        }
        echo ("<br />");

        if ($mainId AND $mainId != $dateId) {
            // echo ("Not MAIN DATE $mainId<br />");
            $mainData = cmsDates_getById($mainId);
            if (is_array($mainData)) {
                $link = $mainData[link];
            }
        }
        $linkData = array();
        if ($link) {
            // echo ("Link = $link<br />");
            $linkList = explode("|",$link);
            for ($i=0;$i<count($linkList);$i++) {
                $ofSet = strpos($linkList[$i],":");
                $first = substr($linkList[$i],0,$ofSet);
                $second = substr($linkList[$i],$ofSet+1);
                $linkData[$first] = explode(",",$second);
                // echo ("First = $first Second = $second <br />");
            }
        }
        foreach ($linkData as $key => $value) {
            switch ($key) {
                case "article" :
                    for ($i=0;$i<count($value);$i++) {
                        $articleId = $value[$i];
                        echo ("Article = $articleId <br />");
                        $articleData = cmsArticles_getById($articleId);
                        if (is_array($articleData)) {
                            // show_array($articleData);
                        }
                    }

                    break;
                case "date" :
                    
                    if ($mainId) $value[] = $mainId;
                    $furtherDateList = array();
                    for ($i=0;$i<count($value);$i++) {
                        $furtherId = $value[$i];
                        if ($furtherId != $dateId) {
                            
                            $furtherData = cmsDates_getById($furtherId);
                            //echo ("Termin mit Id $furtherId $furtherData<br />");
                            if (is_array($furtherData)) {
                                $further_id      = $furtherData[id];
                                $further_Date    = $furtherData[date];
                                $further_Time    = $furtherData[time];
                                $further_SubName = $furtherData[subName];
                                // echo ("append $further_Date<br />");
                                $furtherDateList[$further_Date] = array("id"=>$further_id,"date"=>$further_Date,"time"=>$further_Time,"subName"=>$further_SubName);
                            }

                        }
                    }

                    ksort($furtherDateList);
                    // show_array($furtherDateList);

                    echo ("<h3>Weitere Termine : </h3>");
                    foreach ($furtherDateList as $key => $furtherData) {                        
                        $further_Date    = cmsDate_getDayString($furtherData[date],1);
                        $further_Time    = cmsDate_getTimeString($furtherData[time],2);
                        $further_SubName = $furtherData[subName];
                        echo ("$further_Date $further_Time $further_SubName <br />");
                    }
                    break;


                default : 
                    echo ("Unkown $key in showDate <br />");
            }
        }

        echo ("<a href='".$pageInfo[page].$goPage."' class='cmsLinkButton' >zurück</a>");
        if ($_SESSION[showLevel]>=8) {
            echo ("<a href='admin_cmsDates.php?view=edit&id=$dateId' class='cmsLinkButton cmsSecond' >editieren</a>");
        }
        echo ("<br />");
        return 1;
        
    }

    function dateList_showList($data,$frameWidth) {
        $maxDates = $data[maxDates];

        $borderWidth = 1;
        $innerWidth = $frameWidth - (2 * $borderWidth);

        for($i=0;$i<$maxDates;$i++) {
            $divData = array("style"=>"width:".$innerWidth."px;height:30px;border:1px solid #eee; margin-bottom:2px;");
            div_start("dateListDate",$divData);
            echo ($i);
            div_end("dateListDate");

        }


    }

    function dateList_monthStr($month) {
        return cmsDates_monthStr($month);
    }

    function dateList_dayCode($dayCode,$width=100) {
        if (!$width) $width = 100;
        $dayStr = cmsDates_dayStr($dayCode);
        if ($width < 80) {
            $dayStr = substr($dayStr,0,2);
            if ($width < 20) {
                $dayStr = substr($dayStr,0,1);
            }
        }
        return $dayStr;
    }

    function dateList_showMonth($contentData,$frameWidth) {
        $data = $contentData[data];

        $days = 7;
        $borderWidth=1;
        $borderColor="#ccc";
        


        $date = date("Y-m-d");
        list($year,$month,$day) = explode("-",$date);
         // $date = "2012-12-05";
        if ($data[$date]) $date = $data[date];
        
        if ($_GET[date]) {
            
            list($getYear,$getMonth,$getDay) = explode("-",$_GET[date]);
            
            
            if (intval($getYear) AND intval($getMonth)) {
                $getDate = $_GET[date];
                $date = $_GET[date];
            } else {
                $totayTC = mkTime(12,0,0,$month,$day,$year);
                
                switch ($getYear) {
                    case "yesterday" : 
                        $newTC = $totayTC - 24*60*60;
                        // echo ("gestern ".date("Y-m-d",$newTC)."<br />");
                        break;
                    case "tommorow" : 
                        $newTC = $totayTC + 24*60*60;
                        // echo ("morgen ".date("Y-m-d",$newTC)."<br />");
                        break;
                        
                        
                }
            }
        }
        if ($_POST[date]) $date = $_POST[date];
            
        // echo ("DATUM = $date <br />");
        
//        $month = subStr($date,5,2);
//        $day = subStr($date,8,2);
//        $year = subStr($date,0,4);
        $monthStr = $this->dateList_monthStr($month);
        // echo ("DATUM = $day $month '$monthStr' $year <br />");


        //echo ("FrameWidth before $frameWidth<br />");
       //  $frameWidth = (($days+1)*$borderWidth) + ($days * $dayWidth);
        global $pageInfo;
        
        // Check if Dates exist 
        $checkExistDate = 0;
        if (!is_null($data[checkExistDate])) $checkExistDate = $data[checkExistDate];
        
        // Kompletter Rahmen für Monatsansicht
        $divData = array();
        if ($data[clickAction]) {
            $divData[clickAction] = $data[clickAction];
            $divData[clickTarget] = $data[clickTarget];
            if ($data[clickUrl]) {
                $goPage = "";
                if ($_GET[region]) $goPage.="?region=".$_GET[region];
                foreach ($_GET as $key => $value) {
                    switch ($key) {
                        case "date"   : break;
                        case "region" : break;
                        case "filter_dateRange" : break;
                        default :
                            if ($goPage == "") $goPage.="?";
                            else $goPage .="&";
                            $goPage .= $key."=".$value;
                    }
                }               
                $goPage = $data[clickUrl].$goPage;
                $divData[clickUrl] = $goPage;
            } else {
                $divData[clickTarget] = $data[clickTarget];
                if ($data[clickTarget] == "page") {
                    $pageId = $data[clickPage];
                    $pageName = $pageInfo[page];
                    //show_array($pageInfo);
                    // echo ("PageName = $pageName ".substr($pageName,0,5)."<br />");
                    if ($pageName == "admin_cmsDates.php" or substr($pageName,0,5) == "admin") {
                        $divData[clickUrl] = "admin_cmsDates.php";
                    } else {

                        global $pageInfo,$pageData;
                        $goPage = "";
                        foreach ($_GET as $key => $value) {
                            if ($key != "date") {
                                if ($goPage == "") $goPage.="?";
                                else $goPage .="&";
                                $goPage .= $key."=".$value;
                            }
                        }
                        $goPage = $pageInfo[page].$goPage;
                        $divData[clickUrl] = $goPage;
                        // show_array($pageInfo);
                       //  echo ($goPage);
                        // $pageClickData = cms_page_getData(intval($pageId));
                        if (is_array($pageClickData)) $divData[clickUrl] = $pageClickData[name].".php";
                    }
                }
            }
        }
        $divData[navigateUrl] = "/cms_$GLOBALS[cmsVersion]/getData/datesData.php?cmsVersion=$GLOBALS[cmsVersion]&cmsName=$GLOBALS[cmsName]&out=calendar";
        $divData[actMonth] = $year."-".$month;
        div_start("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id],$divData);

        //echo ("DayWidth = $dayWidth / $frameWidth <br />");
        $monthStr = $year."-".$month;
        $this->dateList_showMonth_Content($contentData[id],$monthStr,$checkExistDate, $frameWidth);
        div_end("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id]);
    }
    
    function dateList_showMonthSlider($contentData,$frameWidth) {
        $data = $contentData[data];

        $height = $data[height];
        if (!$height) $height = 400;
        
        
        $date = date("Y-m-d");
       
         // $date = "2012-12-05";
        if ($data[date]) $date = $data[date];
        if ($data[selectDate]) $date = $data[selectDate];
        if ($_GET[date]) {
            
            list($getYear,$getMonth,$getDay) = explode("-",$_GET[date]);
            
            
            if (intval($getYear) AND intval($getMonth)) {
                $getDate = $_GET[date];
                $date = $_GET[date];
            } else {
                $totayTC = mkTime(12,0,0,$month,$day,$year);
                
                switch ($getYear) {
                    case "yesterday" : 
                        $newTC = $totayTC - 24*60*60;
                        // echo ("gestern ".date("Y-m-d",$newTC)."<br />");
                        break;
                    case "tommorow" : 
                        $newTC = $totayTC + 24*60*60;
                        // echo ("morgen ".date("Y-m-d",$newTC)."<br />");
                        break;
                }
            }
        }
        if ($_POST[date]) $date = $_POST[date];
          
        // SelectDate
        list($year,$month,$day) = explode("-",$date);
        // echo ("Selected Date = $day $month $year <br />");
        
        // show Range
        $today = date("Y-m-d");
        list($todayYear,$todayMonth,$todayDay) = explode("-",$today);
        
        $monthBack = $data[monthBack];
        $monthFor  = $data[monthFor];
        
        $startMonth = $todayMonth - $monthBack;
        $startYear = $todayYear;
        if ($startMonth < 1) {
            $startMonth = $startMonth + 12;
            $startYear = $startYear-1;
        }
        
        
        $contentList = array();
        
        $checkExistDate = 1;
        if (!is_null($data[checkExistDate])) $checkExistDate = $data[checkExistDate];
        
        // Kompletter Rahmen für Monatsansicht
        $divData = array();
        if ($data[clickAction]) {
            // show_array($data);
            $divData[clickAction] = $data[clickAction];
            $divData[clickTarget] = $data[clickTarget];
            if ($data[clickParameter]) $divData[clickParameter] = "$data[clickParameter]";
            if ($data[clickUrl]) {
                $goPage = "";
                if ($_GET[region]) $goPage.="?region=".$_GET[region];
                foreach ($_GET as $key => $value) {
                    switch ($key) {
                        case "date"   : break;
                        case "region" : break;
                        case "dateId" : break;
                        default :
                            if ($key != $divData[clickParameter]) {
                            
                                if ($goPage == "") $goPage.="?";
                                else $goPage .="&";
                                $goPage .= $key."=".$value;
                            }
                    }
                }               
                $goPage = $data[clickUrl].$goPage;
                $divData[clickUrl] = $goPage;
            } else {
                $divData[clickTarget] = $data[clickTarget];
                if ($data[clickTarget] == "page") {
                    $pageId = $data[clickPage];
                    $pageName = $pageInfo[page];
                    //show_array($pageInfo);
                    
                    // echo ("PageName = $pageName ".substr($pageName,0,5)."<br />");
                    if ($pageName == "admin_cmsDates.php" or substr($pageName,0,5) == "admin") {
                        $divData[clickUrl] = "admin_cmsDates.php";
                    } else {

                        global $pageInfo,$pageData;
                        $goPage = "";
                        foreach ($_GET as $key => $value) {
                            if ($key != "date") {
                                if ($goPage == "") $goPage.="?";
                                else $goPage .="&";
                                $goPage .= $key."=".$value;
                            }
                        }
                        $goPage = $pageInfo[page].$goPage;
                        $divData[clickUrl] = $goPage;
                        // show_array($pageInfo);
                       //  echo ($goPage);
                        // $pageClickData = cms_page_getData(intval($pageId));
                        if (is_array($pageClickData)) $divData[clickUrl] = $pageClickData[name].".php";
                    }
                }
            }
        }
        $divData[navigateUrl] = "/cms_$GLOBALS[cmsVersion]/getData/datesData.php?cmsVersion=$GLOBALS[cmsVersion]&cmsName=$GLOBALS[cmsName]&out=calendar";
        $divData[actMonth] = $year."-".$month;
        
        
        // $divData = array();
        $style = "";
        $style = "width:".$frameWidth."px;float:left;";
        if ($height) $style .= "height:".$height."px;";
        $divData[style] = $style;
                
        $startFrame = 0;
        
        for ($i=0;$i<=($monthBack+$monthFor);$i++) {
            // echo ("$i , $startYear - $startYear <br />");
            if ($month==$startMonth AND $year==$startYear) $startFrame = $i;

            $str = div_start_str("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id],$divData);

            $monthStr = $startYear."-".$startMonth;

            // erster Monat
            if ($i==0) $contentData[data][showBackActive] = 0;
            else $contentData[data][showBackActive] = 1;
            
            if ($i == $monthBack+$monthFor) $contentData[data][showNextActive] = 0;
            else $contentData[data][showNextActive] = 1;
            
            $str .= $this->dateList_showMonth_Content_Str($contentData[id],$monthStr,$contentData, $frameWidth);
            
            $str .= div_end_str("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id]);
            $contentList[$monthStr] = $str;
            
            $startMonth++;
            if ($startMonth >12) {
                $startMonth = 1;
                $startYear++;
            }
            
        }
        // echo ("StartDiv = $startFrame<br />");
        
        if (count($contentList)) {
            $showData = array();
            $showData[mainDiv] = $divData;
            $showData[startFrame] = $startFrame;
            
            
            $showData[direction] = "horozontal";
            $showData[speed] = 500;
            $showData[loop] = 0;
            
            
            
            $width = $frameWidth;
            $name = "calendarSlider_".$contentData[id];
           
            cmsSlider("bxSlider", $name, $contentList, $showData, $width, $height);
        }
     }


    function dateList_showMonth_Content_Str($frameId,$monthStr,$contentData,$frameWidth) {
        $days = 7;
        $borderWidth=1;
        
        $checkExistDate = 0;

        $out = "";
        
        
        $dayWidth = "".(($frameWidth - (($days+1)*$borderWidth)) / $days);
        if (strpos($dayWidth,".")) {
            $dayWidth = intval(substr($dayWidth,0,strpos($dayWidth,".")));
        } else {
            $dayWidth = intval($dayWidth);
        }
        

        list($year,$month) = explode("-",$monthStr);

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";


        $out .= div_start_str("dateNavigate",$divData);
        // MONAT ZURÜCK
        $goMonthBack = intVal($month) -1 ;
        if ($goMonthBack < 1) {
            $goMonthBack = "12";
            $infoMonthBack = $this->dateList_monthStr($goMonthBack)." ".($year-1);
            $goMonthBack = ($year-1)."-".$goMonthBack."-01";
        } else {
            if ($goMonthBack>9) $goMonthBack = "".$goMonthBack;
            else $goMonthBack = "0".$goMonthBack;
            $infoMonthBack = $this->dateList_monthStr($goMonthBack)." ".($year);
            $goMonthBack = $year."-".$goMonthBack."-01";
        }

        // MONAT VOR
        $goMonthFor = intVal($month) + 1 ;
        if ($goMonthFor >12 ) {
            $goMonthFor = "01";
            $infoMonthFor = $this->dateList_monthStr($goMonthFor)." ".($year+1);
            $goMonthFor = ($year+1)."-".$goMonthFor."-01";
        } else {
            if ($goMonthFor>9) $goMonthFor = "".$goMonthFor;
            else $goMonthFor = "0".$goMonthFor;
            $infoMonthFor = $this->dateList_monthStr($goMonthFor)." ".($year);
            $goMonthFor = $year."-".$goMonthFor."-01";
        }


        foreach($_GET as $key => $value) {
            if ($key != "date") {
                if ($goPage == "") $goPage .= "?";
                else $goPage.="&";
                $goPage .= $key."=".$value;
            }
        }



        // $out .= "GO MONAT back = $goMonthBack GO MONAT Vor = $goMonthFor");

        // GO BACK
        $showBack = 1;
        $showBackActive = 1;
        if (is_integer($contentData[data][showBack])) $showBack = $contentData[data][showBack];
        if (is_integer($contentData[data][showBackActive])) $showBackActive = $contentData[data][showBackActive];
        
        $showNext = 1;           
        $showNextActive = 1;
        if (is_integer($contentData[data][showNext])) $showNext = $contentData[data][showNext];
        if (is_integer($contentData[data][showNextActive])) $showNextActive = $contentData[data][showNextActive];
            
        if ($showBack) {
            $divData = array();
            $divData[style] = "width:20px;float:left;text-align:center;";
            if ($showBackActive) {
                $divName = "dateNavigateBack calendarSlider_".$frameId."_back";
            } else {
                $divName = "dateNavigateBack dateNavigateDisabled";
            }
            $out .= div_start_str($divName,$divData);
            $out .= "&#060;&#060;";
            $out .= div_end_str($divName);
        }

        $monthStrOut = $this->dateList_monthStr($month);
        $out .= div_start_str("dateNavigateMiddle","width:".($frameWidth-2*35)."px;float:left;text-align:center;");
        //$out .= div_start_str("dateNavigateMiddle","float:left;text-align:center;");
        $out .= $monthStrOut." ".$year;
        $out .= div_end_str("dateNavigateMiddle");

        
        if ($showNext) {
            $divData = array();
            $divData[style] = "width:20px;float:left;text-align:center;";
            if ($showNextActive) {
                $divName = "dateNavigateNext calendarSlider_".$frameId."_next";
            } else {
                $divName = "dateNavigateNext dateNavigateDisabled";
            }
            $out .= div_start_str($divName,$divData);
            $out .= "&#062;&#062;";
            $out .= div_end_str($divName);
        }
        
        
        $out .= div_end_str("dateNavigate","before");

        $startDayofMonth = 1; // 1=Montag 0=Sonntag

        $startDay = mktime(12,0,0,$month,1,$year);

        // get StartDate for StartDayofMonth
        $weekDay = date("w",$startDay);
        while ($weekDay != $startDayofMonth) {
            $startDay = $startDay - (24 * 60 * 60);
            $weekDay = date("w",$startDay);
        }
        $startDate = date("d.m.Y w",$startDay);
        // $out .= "StartDay = $startDay $startDate $weekDay ".$this->dateList_dayCode($weekDay)." <br /> ");

        // $out .= "Start HeadLine<br />");

        // Show Table HeadLine
        $out .= div_start_str("dateMonthTitleLine","width:".$frameWidth."px;");
        $weekDay = $startDayofMonth;
        for ($i=1;$i<=7;$i++) {
            if ($i<=$days) {
                $style = "width:".$dayWidth."px;float:left;text-align:center;border-top:".$borderWidth."px solid $borderColor;border-right:".$borderWidth."px solid $borderColor;border-bottom:".$borderWidth."px solid $borderColor;";
                if ($i==1) $style .= "border-left:".$borderWidth."px solid $borderColor";
                $divData = array("style"=>$style);
                $divName = "dateMonthDayTitle";
                $divName .= " dateMonthTitle_".substr($this->dateList_dayCode($weekDay),0,2);
                $out .= div_start_str($divName,$divData);
                $out .= $this->dateList_dayCode($weekDay,$dayWidth);
                $out .= div_end_str($divName);
            }
            $weekDay++;
        }
        $out .= div_end_str("dateMonthTitleLine","before");
        
        $actYear = intval(date("Y"));
        $actMonth = intval(date("m"));
        $actDay = intval(date("d"));
        $showMonth = intval($month);
        
        
        if ($_GET[date]) {
            $selectDate = $_GET[date];
            list($selYear,$selMonth,$selDay) = explode("-",$selectDate);
        }

        $day = $startDay;
        $ready = 0;
        // $out .= "showMonth $showMonth <br />");
        while (!$ready) {
            $out .= div_start_str("dateMonthWeek","width:".$frameWidth."px;");
            for ($i=1;$i<=7;$i++) {
                if ($i<=$days) {
                    $weekDay = date("Y-m-d",$startDay);
                    $monthDay = intval(subStr($weekDay,5,2));
                    $dayDay = intval(subStr($weekDay,8,2));
                    $yearDay = subStr($weekDay,0,4);
                    $weekDayCode = intVal(date("w",$startDay));


                    $divData = array();
                    $divName = "dateMonthDay";
                    $style = "width:".$dayWidth."px;float:left;";
                    if ($i==1) $style .= "border-left-width:".$borderWidth."px;";
                    $divData = array("style"=>$style);
                    $divData["id"] = $frameId."_".$weekDay."_".$monthStr;
                   // $divData["date"] = $weekDay;
                    
                    if ($checkExistDate) {
                        // has Date
                        $dateList = cmsDates_getList(array("date"=>$weekDay,"show"=>1));
                        if (is_array($dateList)) {
                            if (count($dateList)>0) $divName .= " dateExist";
                        }
                    }



                    // letzer Monat
                    //if ($monthDay < $showMonth) $divName .= " dateLastMonth";

                    if ($yearDay == $actYear AND $monthDay == $actMonth AND $dayDay == $actDay) $divName .= " dateToday";

                    if ($yearDay == $selYear AND $monthDay == $selMonth AND $dayDay == $selDay) $divName .= " dateSelect";
                    
                    if ($monthDay != $showMonth) {
                        $doReady = 0;
                        // $out .= "READY show=$monthDay zeigeMonat=$showMonth");
                        if ($monthDay > $showMonth) $doReady = 1;
                        if ($showMonth == 1) {
                            if ($monthDay == 12) {
                                $divName .= " dateLastMonth";
                                $doReady = 0;
                            }
                        }
                        if ($showMonth == 12) {
                            if ($monthDay != 1) $doReady = 0;
                            else $doReady = 1;
                            // $out .= "READY show=$monthDay zeigeMonat=$showMonth");
                            // $doReady = 0;
                        }
                        if ($doReady) {
                            $ready = 1;
                            $divName .= " dateNextMonth";                            
                        } else {
                            if ($showMonth != 1 AND $monthDay != 12) {
                                $divName .= " dateLastMonth";
                            }
                        }


                       

                    }

                    $out .= div_start_str($divName,$divData);
                    $out .= $dayDay;
                    $out .= div_end_str($divName);
                }

                $startDay = $startDay + (24 * 60 * 60);
            }
            $out .= div_end_str("dateMonthWeek","before");
        }   
        return $out;
    }

    
    


    function dateList_showMonth_Content($frameId,$monthStr,$checkExistDate,$frameWidth) {
        $days = 7;
        $borderWidth=1;

        $dayWidth = "".(($frameWidth - (($days+1)*$borderWidth)) / $days);
        if (strpos($dayWidth,".")) {
            $dayWidth = intval(substr($dayWidth,0,strpos($dayWidth,".")));
        } else {
            $dayWidth = intval($dayWidth);
        }
        

        list($year,$month) = explode("-",$monthStr);

        $divData = array();
        $divData[style] = "width:".$frameWidth."px;";


        div_start("dateNavigate",$divData);
        // MONAT ZURÜCK
        $goMonthBack = intVal($month) -1 ;
        if ($goMonthBack < 1) {
            $goMonthBack = "12";
            $infoMonthBack = $this->dateList_monthStr($goMonthBack)." ".($year-1);
            $goMonthBack = ($year-1)."-".$goMonthBack."-01";
        } else {
            if ($goMonthBack>9) $goMonthBack = "".$goMonthBack;
            else $goMonthBack = "0".$goMonthBack;
            $infoMonthBack = $this->dateList_monthStr($goMonthBack)." ".($year);
            $goMonthBack = $year."-".$goMonthBack."-01";
        }

        // MONAT VOR
        $goMonthFor = intVal($month) + 1 ;
        if ($goMonthFor >12 ) {
            $goMonthFor = "01";
            $infoMonthFor = $this->dateList_monthStr($goMonthFor)." ".($year+1);
            $goMonthFor = ($year+1)."-".$goMonthFor."-01";
        } else {
            if ($goMonthFor>9) $goMonthFor = "".$goMonthFor;
            else $goMonthFor = "0".$goMonthFor;
            $infoMonthFor = $this->dateList_monthStr($goMonthFor)." ".($year);
            $goMonthFor = $year."-".$goMonthFor."-01";
        }


        foreach($_GET as $key => $value) {
            if ($key != "date") {
                if ($goPage == "") $goPage .= "?";
                else $goPage.="&";
                $goPage .= $key."=".$value;
            }
        }



        // echo ("GO MONAT back = $goMonthBack GO MONAT Vor = $goMonthFor");

        $divData = array();
        $divData[style] = "width:20px;float:left;text-align:center;";
        $divData["id"] = $frameId."_back";
        // array("style"=>"width:20px;float:left;text-align:center;","goMonthBack"=>$goMonthBack));
        div_start("dateNavigateBack",$divData);
//        if ($goPage == "") $goBackPage = $goPage.="?";
//        else $goBackPage = $goPage."&";
//        $goBackPage .= "date=$goMonthBack";
        $helper = '"'.$frameId.'"';
        // echo ("<a href='javascript:goCalenderBack($helper)'>");
        //echo ("<a href='$pageInfo[page].$goBackPage' title='$infoMonthBack' class='dateNavigateButton'> << </a>");
        echo ("&#060;&#060;");
        //echo ("</a>");
        //echo ("b");
        div_end("dateNavigateBack");

        $monthStr = $this->dateList_monthStr($month);
        div_start("dateNavigateMiddle","width:".($frameWidth-2*35)."px;float:left;text-align:center;");
        //div_start("dateNavigateMiddle","float:left;text-align:center;");
        echo ($monthStr." ".$year);
        div_end("dateNavigateMiddle");

        $divData = array();
        $divData[style] = "width:20px;text-align:center;";
        $divData["id"] = $frameId."_for";
        div_start("dateNavigateFor",$divData);
//        if ($goPage == "") $goForPage = $goPage.="?";
//        else $goForPage = $goPage."&";
//        $goForPage .= "date=$goMonthFor";
        //echo ("<a href='$pageInfo[page].$goForPage' title='$infoMonthFor'class='dateNavigateButton'> >> </a>");
        echo ("&#062;&#062;");
        //echo ("f");
        div_end("dateNavigateFor");
        div_end("dateNavigate","before");

        $startDayofMonth = 1; // 1=Montag 0=Sonntag

        $startDay = mktime(12,0,0,$month,1,$year);

        // get StartDate for StartDayofMonth
        $weekDay = date("w",$startDay);
        while ($weekDay != $startDayofMonth) {
            $startDay = $startDay - (24 * 60 * 60);
            $weekDay = date("w",$startDay);
        }
        $startDate = date("d.m.Y w",$startDay);
        // echo ("StartDay = $startDay $startDate $weekDay ".$this->dateList_dayCode($weekDay)." <br /> ");

        // echo ("Start HeadLine<br />");

        // Show Table HeadLine
        div_start("dateMonthTitleLine","width:".$frameWidth."px;");
        $weekDay = $startDayofMonth;
        for ($i=1;$i<=7;$i++) {
            if ($i<=$days) {
                $style = "width:".$dayWidth."px;float:left;text-align:center;border-top:".$borderWidth."px solid $borderColor;border-right:".$borderWidth."px solid $borderColor;border-bottom:".$borderWidth."px solid $borderColor;";
                if ($i==1) $style .= "border-left:".$borderWidth."px solid $borderColor";
                $divData = array("style"=>$style);
                $divName = "dateMonthDayTitle";
                $divName .= " dateMonthTitle_".substr($this->dateList_dayCode($weekDay),0,2);
                div_start($divName,$divData);
                echo ($this->dateList_dayCode($weekDay,$dayWidth));
                div_end($divName);
            }
            $weekDay++;
        }
        div_end("dateMonthTitleLine","before");
        
        $actYear = intval(date("Y"));
        $actMonth = intval(date("m"));
        $actDay = intval(date("d"));
        $showMonth = intval($month);
        
        
        if ($_GET[date]) {
            $selectDate = $_GET[date];
            list($selYear,$selMonth,$selDay) = explode("-",$selectDate);
        }

        $day = $startDay;
        $ready = 0;
        // echo ("showMonth $showMonth <br />");
        while (!$ready) {
            div_start("dateMonthWeek","width:".$frameWidth."px;");
            for ($i=1;$i<=7;$i++) {
                if ($i<=$days) {
                    $weekDay = date("Y-m-d",$startDay);
                    $monthDay = intval(subStr($weekDay,5,2));
                    $dayDay = intval(subStr($weekDay,8,2));
                    $yearDay = subStr($weekDay,0,4);
                    $weekDayCode = intVal(date("w",$startDay));


                    $divData = array();
                    $divName = "dateMonthDay";
                    $style = "width:".$dayWidth."px;float:left;";
                    if ($i==1) $style .= "border-left-width:".$borderWidth."px;";
                    $divData = array("style"=>$style);
                    $divData["id"] = $frameId."_".$weekDay;
                   // $divData["date"] = $weekDay;
                    
                    if ($checkExistDate) {
                        // has Date
                        $dateList = cmsDates_getList(array("date"=>$weekDay,"show"=>1));
                        if (is_array($dateList)) {
                            if (count($dateList)>0) $divName .= " dateExist";
                        }
                    }



                    // letzer Monat
                    //if ($monthDay < $showMonth) $divName .= " dateLastMonth";

                    if ($yearDay == $actYear AND $monthDay == $actMonth AND $dayDay == $actDay) $divName .= " dateToday";

                     if ($yearDay == $selYear AND $monthDay == $selMonth AND $dayDay == $selDay) $divName .= " dateSelect";
                    
                    if ($monthDay != $showMonth) {
                        $doReady = 0;
                        // echo ("READY show=$monthDay zeigeMonat=$showMonth");
                        if ($monthDay > $showMonth) $doReady = 1;
                        if ($showMonth == 1) {
                            if ($monthDay == 12) {
                                $divName .= " dateLastMonth";
                                $doReady = 0;
                            }
                        }
                        if ($showMonth == 12) {
                            if ($monthDay != 1) $doReady = 0;
                            else $doReady = 1;
                            // echo ("READY show=$monthDay zeigeMonat=$showMonth");
                            // $doReady = 0;
                        }
                        if ($doReady) {
                            $ready = 1;
                            $divName .= " dateNextMonth";                            
                        } else {
                            if ($showMonth != 1 AND $monthDay != 12) {
                                $divName .= " dateLastMonth";
                            }
                        }


                        // echo ("Größer $montDay <br />");

                    }

                    // $divName .= " dateMonth_".substr($this->dateList_dayCode($weekDayCode),0,2);

//                    if (is_array($dateList)) {
//                        if (count($dateList)>0) {
//                            for ($d=0;$d<count($dateList);$d++) {
//                                echo ($dateList[$i][name]);
//                            }
//                        }
//                    }

                    div_start($divName,$divData);
                    echo ($dayDay);
                    div_end($divName);
                }

                $startDay = $startDay + (24 * 60 * 60);
            }
            div_end("dateMonthWeek","before");
        }       

    }

    function dateList_showWeek($data,$frameWidth) {
        $days = 5;
        $borderWidth=1;
        $borderColor="#ccc";
        $innerWidth = $frameWidth - 2 * $borderWidth;
        div_start("dateNavigate","width:".$frameWidth."px;");
        div_start("dateNavigateBack","width:20px;float:left;text-align:center;");
        echo ("<a href='#' class='dateNavigateButton'> << </a>");
        div_end("dateNavigateBack");

        div_start("dateNavigateMiddle","width:".($frameWidth-2*20)."px;float:left;text-align:center;");
        echo ("KW 30");
        div_end("dateNavigateMiddle");

        div_start("dateNavigateFor","width:20px;float:left;text-align:center;");
        echo ("<a href='#' class='dateNavigateButton'> >> </a>");
        div_end("dateNavigateFor");
        div_end("dateNavigate","before");

        for($i=1;$i<=$days;$i++) {
            $style = "border:".$borderWidth."px solid $borderColor;width:".$innerWidth."px;";
            if ($i > 1) $style.= "border-top-width:0px;";
            div_start("dateWeekDay",$style);
            echo ($this->dateList_dayCode($i,$innerWidth).", $i.Mai<br />");
            echo ("Termin 1 <br />");
            echo ("Termin 2 <br />");
            div_end("dateWeekDay");
        }

    }


    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"]  = "Liste";
        $res["table"] = "Tabelle";
        $res["list"]  = array("name"=>"Liste mit Terminen");
        $res["month"] = array("name"=>"Monatsansicht");
        $res["monthSlider"] = array("name"=>"Monats-Slider");
        $res["week"]  = array("name"=>"Wochenansicht");
        return $res;
    }


    function customFilter_specialView_getList_own() {
        $specialList = array();
     

        $specialList[noneLocation] = array("id"=>"gone","name"=>"Termine ohne Ort");
        $specialList[noneLocation][filter] = array("show"=>1,"location"=>0);
        $specialList[noneLocation][sort] = "locationStr";


        $specialList[noneRegion] = array("id"=>"gone","name"=>"Termine ohne Region");
        $specialList[noneRegion][filter] = array("show"=>1,"region"=>0);
        $specialList[noneRegion][sort] = "date";
        return $specialList;
    }

    function editContent_filter_getList_own() {
        $filterList = array();
        $filterList[produkt] = 0;

        $filterList[specialView]   = array();
        $filterList[specialView]["name"] = "Spezielle Ansichten";
        $filterList[specialView]["type"] = "specialView";
        $filterList[specialView]["showData"] = array("submit"=>1,"empty"=>"normale Ansicht");
        //$filterList[specialView]["filter"] = array("mainCat"=>180,"show"=>1);
        // $filterList[specialView]["sort"] = "name";
        $filterList[specialView]["dataName"] = "specialView";
        $filterList[specialView][customFilter] = 1;


        $filterList[dateRange] = array();
        $filterList[dateRange]["name"] = "Zeitraum";
        $filterList[dateRange]["type"] = "dateRange";
        $filterList[dateRange]["dataName"] = "dateRange";
        $filterList[dateRange]["showData"] = array("submit"=>1,"empty"=>"Zeitraum nicht einschränken");
        $filterList[dateRange]["filter"] = array("mainCat"=>1,"show"=>1);
        $filterList[dateRange]["sort"] = "";
        $filterList[dateRange][customFilter] = 1;



        $filterList[category] = array();
        $filterList[category]["name"] = "Kategorie";
        $filterList[category]["type"] = "category";
        $filterList[category]["dataName"] = "category";
        $filterList[category]["showData"] = array("submit"=>1,"empty"=>"Alle Kategorie zeigen");
        $filterList[category]["filter"] = array("mainCat"=>1,"show"=>1);
        $filterList[category]["sort"] = "name";
        $filterList[category][customFilter] = 1;

        $filterList[region]   = array();
        $filterList[region]["name"] = "Region";
        $filterList[region]["type"] = "category";
        $filterList[region]["showData"] = array("submit"=>1,"empty"=>"Alle Regionen zeigen");
        $filterList[region]["filter"] = array("mainCat"=>180,"show"=>1);
        $filterList[region]["sort"] = "name";
        $filterList[region]["dataName"] = "region";
        $filterList[region][customFilter] = 1;

        return $filterList;
    }



    function dateList_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();

        $mainTab = "dateList";
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }

        // Add ViewMode
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // MainData
        //    $viewMode = $editContent[data][viewMode];
        //    if ($_POST[editContent][data][viewMode]) $viewMode = $_POST[editContent][data][viewMode];
        //    else {
        //        if ($_POST[editContent][data]) $viewMode = $_POST[editContent][data][viewMode];
        //    }
        //    $addData = array();
        //    $addData["text"] = "Anzeige-Art";
        //    $addData["input"] = $this->viewMode_selectType($viewMode,"editContent[data][viewMode]",array("onChange"=>"submit()"));
        //    $res[] = $addData;

        
        
       
        
       
        // Mouse ACTION
        $mouseAction = $editContent[data][mouseAction];
        if ($_POST[editContent][data][mouseAction]) $mouseAction = $_POST[editContent][data][mouseAction];
        else {
            if ($_POST[editContent][data]) $mouseAction = $_POST[editContent][data][mouseAction];
        }
        $addData = array();
        $addData["text"] = "Aktion bei Maus über";
        $input  = $this->mouseAction_select($mouseAction,"editContent[data][mouseAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;


        // KLICK ACTION
        $clickAction = $editContent[data][clickAction];
        if ($_POST[editContent][data][clickAction]) $clickAction = $_POST[editContent][data][clickAction];
        else {
            if ($_POST[editContent][data]) $clickAction = $_POST[editContent][data][clickAction];
        }
        $addData = array();
        $addData["text"] = "Aktion bei Klick";
        $input  = $this->clickAction_select($clickAction,"editContent[data][clickAction]",array("submit"=>1));
        $addData["input"] = $input;
        $res[action][] = $addData;

        if ($clickAction) {
            $clickTarget = $editContent[data][clickTarget];
            if ($_POST[editContent][data][clickTarget]) $clickTarget = $_POST[editContent][data][clickTarget];
            else {
                if ($_POST[editContent][data]) $clickTarget = $_POST[editContent][data][clickTarget];
            }
            $addData = array();
            $addData["text"] = "Zeigen in";
            $addData["input"] = $this->target_select($clickTarget,"editContent[data][clickTarget]",array("submit"=>1));
            $res[action][] = $addData;


            switch ($clickTarget) {
                case "page" :

                    $clickPage = $editContent[data][clickTarget];
                    if ($_POST[editContent][data][clickPage]) $clickPage = $_POST[editContent][data][clickPage];
                    else if ($_POST[editContent][data]) $clickPage = $_POST[editContent][data][clickPage];

                    $addData = array();
                    $addData["text"] = "Seite auswählen";
                    $addData["input"] = $this->page_select($clickPage,"editContent[data][clickPage]",array("submit"=>1));
                    $res[action][] = $addData;

                    break;
                case "frame" :

                    break;
                case "popup" :
                    $addData = array();
                    $addData["text"] = "Breite PopUp Fenster";
                    $addData["input"] = "<input name='editContent[data][popUpWidth]' style='width:100px;' value='".$editContent[data][popUpWidth]."'>";
                    $res[action][] = $addData;

                    $addData = array();
                    $addData["text"] = "Höhe PopUp Fenster";
                    $addData["input"] = "<input name='editContent[data][popUpHeight]' style='width:100px;' value='".$editContent[data][popUpHeight]."'>";

                    $res[action][] = $addData;
            }
        }




        return $res;
    }
    
    function editContent_filter($editContent,$frameWidth) {

        $showList = $this->editContent_filter_showList();

        $res = array();

        if ($showList[product] ) if ($showList[product][show]) {
            // FILTER PRODUKT
            $filterProduct = $editContent[data][filterProduct];
            if ($_POST[editContent][data][filterProduct]) $filterProduct = $_POST[editContent][data][filterProduct];
            else if ($_POST[editContent][data]) $filterProduct = $_POST[editContent][data][filterProduct];
            $addData = array();
            $addData["text"] = "Produkte Filtern";
            $addData["input"] = $this->filter_select("product",$filterProduct,"editContent[data][filterProduct]",array("submit"=>1));
            $res[] = $addData;
        }

        if ($showList[company] ) if ($showList[company][show]) {
            // FILTER Hersteller
            $filterCompany = $editContent[data][filterCompany];
            if ($_POST[editContent][data][filterCompany]) $filterCompany = $_POST[editContent][data][filterCompany];
            else if ($_POST[editContent][data]) $filterCompany = $_POST[editContent][data][filterCompany];
            $addData = array();
            $addData["text"] = "nach Herstellern";
            $addData["input"] = $this->filter_select("company",$filterCompany,"editContent[data][filterCompany]",array("submit"=>1));
            $res[] = $addData;
        }

        if ($showList[date] ) if ($showList[date][show]) {
            // FILTER Category
            $filterDate = $editContent[data][filterDate];
            if ($_POST[editContent][data][filterDate]) $filterDate = $_POST[editContent][data][filterDate];
            else if ($_POST[editContent][data]) $filterDate = $_POST[editContent][data][filterDate];
            $addData = array();
            $addData["text"] = "nach Datum";
            $addData["input"] = $this->filter_select("date",$filterDate,"editContent[data][filterDate]",array("submit"=>1));
            $res[] = $addData;
        }

         if ($showList[category] ) if ($showList[category][show]) {
            // FILTER Category
            $filterCategory = $editContent[data][filterCategory];
            $filter = $showList[category][filter];
            $sort = $showList[category][sort];
            if ($_POST[editContent][data][filterCategory]) $filterCategory = $_POST[editContent][data][filterCategory];
            else if ($_POST[editContent][data]) $filterCategory = $_POST[editContent][data][filterCategory];
            $addData = array();
            $addData["text"] = "nach Kategorie";
            $addData["input"] = $this->filter_select("category",$filterCategory,"editContent[data][filterCategory]",array("submit"=>1,"filter"=>$filter,"sort"=>$sort));
            $res[] = $addData;
        }

        if ($showList[region] ) if ($showList[region][show]) {
            // FILTER Category
            $filterCategory = $editContent[data][filterRegion];
            if ($_POST[editContent][data][filterRegion]) $filterRegion = $_POST[editContent][data][filterRegion];
            else if ($_POST[editContent][data]) $filterRegion = $_POST[editContent][data][filterCategory];
            $addData = array();
            $addData["text"] = "nach Regionen";
            $addData["input"] = $this->filter_select("region",$filterRegion,"editContent[data][filterRegion]",array("submit"=>1));
            $res[] = $addData;
        }


        if ($showList[location] ) if ($showList[location][show]) {
            // FILTER Location
            $filterCategory = $editContent[data][filterCategory];
            if ($_POST[editContent][data][filterLocation]) $filterCategory = $_POST[editContent][data][filterLocation];
            else if ($_POST[editContent][data]) $filterLocation = $_POST[editContent][data][filterLocation];
            $addData = array();
            $addData["text"] = "nach Orten";
            $addData["input"] = $this->filter_select("location",$filterLocation,"editContent[data][filterLocation]",array("submit"=>1));
            $res[] = $addData;
        }

        // ANZAHL PRODUKTE
        $addData = array();
        $addData["text"] = "Maximale Produkte";
        $input  = "<input name='editContent[data][maxCount]' style='width:100px;' value='".$editContent[data][maxCount]."'>";
        $addData["input"] = $input;
        $res[] = $addData;

        // DARSTELLUNG
        $addData = array();
        $addData["text"] = "Anzahl Produkte in Reihe";
        $addData["input"] = "<input name='editContent[data][imgRow]' style='width:100px;' value='".$editContent[data][imgRow]."'>";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Produkte in Reihe";
        $addData["input"] = "<input name='editContent[data][imgRowAbs]' style='width:100px;' value='".$editContent[data][imgRowAbs]."'>";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = "Abstand Zeilen";
        $addData["input"] = "<input name='editContent[data][imgColAbs]' style='width:100px;' value='".$editContent[data][imgColAbs]."'>";
        $res[] = $addData;
        return $res;

    }


    function editContent_filter_showList() {
        $showList = array();
        $showList[date] = array("nmae"=>"Datum","show"=>1);
        $showList[category] = array("name"=>"Rubrik","show"=>1,"filter"=>array("mainCat"=>0),"sort"=>"name" );
        $showList[region] = array("name"=>"Region","show"=>1);
        $showList[location] = array("name"=>"Ort","show"=>1);

        $ownList = $this->editContent_filter_showList_own();
        if (is_array($ownList)) {
            foreach ($ownlist as $key => $value) {
               if (is_array($showList[$key])) {
                    foreach ($value as $filterKey => $filterValue) {
                        $showList[$key][$filterKey] = $filterValue;
                    }
                } else {
                    $showList[$key] = $value;
                }
            }
        }

        return $showList;
    }

    function editContent_filter_showList_own() {
        return 0;
    }


    function clickAction_getList() {
        $res = array();
        $res["showDate"] = "Termine vom Tag zeigen";
        $res["showMonth"] = "Termine vom Monat zeigen";
        $ownList = $this->clickAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function clickAction_getOwnList() {
        // $res["showCategory"] = 0;
        return $res;
    }


    
    function mouseAction_getList() {
        $res = array();

        $res["showDate"] = "Termine zeigen";
        $res["showCategory"] = "Kategorien zeigen";

        $ownList = $this->mouseAction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }

    function mouseAction_getOwnList() {
        $res = array();
        return $res;
    }


    function category_filter_select_getownlist() {
        return array();
    }

    function viewMode_selectType($type,$dataName,$dataAction=array()) {
        $typeList = array();
        $typeList["list"] = array("name"=>"Liste mit Terminen");
        $typeList["month"] = array("name"=>"Monatsansicht");
        $typeList["week"] = array("name"=>"Wochenansicht");

        $str = "";
        $str.= "<select name='$dataName' class='cmsSelectType' value='$type' ";
        foreach ($dataAction as $key => $value) {
            $str .= " $key='$value'";
        }
        $str .= " >";


        foreach ($typeList as $code => $typeData) {
             $str.= "<option value='$code'";
             if ($code == $type)  $str.= " selected='1' ";
             $str.= ">$typeData[name]</option>";
        }
        $str.= "</select>";
        return $str;
    }
}

function cmsType_dateList_class() {
    if ($GLOBALS[cmsTypes]["cmsType_dateList.php"] == "own") $dateListClass = new cmsType_dateList();
    else $dateListClass = new cmsType_dateList_base();
    return $dateListClass;
}

function cmsType_dateList($contentData,$frameWidth) {
    $dateListClass = cmsType_dateList_class();
    $dateListClass->dateList_show($contentData,$frameWidth);
}



function cmsType_dateList_editContent($editContent,$frameWidth) {
    $dateListClass = cmsType_dateList_class();
    return $dateListClass->dateList_editContent($editContent,$frameWidth);
}

function cmsType_dateList_getName() {
    $dateListClass = cmsType_dateList_class();
    $name = $dateListClass->getName();
    return $name;
}



?>
