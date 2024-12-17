<?php // charset:UTF-8

class cmsType_date_base extends cmsClass_content_data_show {


    function getName (){
        return "Termine";
    }
    
   
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth  = $this->innerWidth;
        $data = $contentData[data];
        if (!is_array($data)) $data = array();

        $padding = 0;
        $innerWidth = $frameWidth - 2 * $padding;

       
        //div_start("date","background-color:#ccf;width:".$innerWidth."px;margin:5px 0 5px 0;padding:".$padding."px;");
        div_start("date");
        // echo ("<h1>TERMINE </h1>"); 
        //show_array($data);
        switch ($data[viewMode]) {
            case "list" :
                $dateId = intval($_GET[dateId]);
                $res = $this->show_showList($contentData,$innerWidth);                 
                break;
            
            case "dateList" :
                $res = $this->show_dateList($contentData,$frameWidth);                 
                break;
            
            case "month" :
                $this->date_showMonth($contentData,$innerWidth,$filter,$sort);
                break;
            
            case "table" :
                $this->show_showTable($contentData,$innerWidth);
                break;
            
            case "monthSlider" :
                $this->date_showMonthSlider($contentData,$innerWidth,$filter,$sort);
                break;
            
            case "week" :
                $this->date_showWeek($contentData,$innerWidth,$filter,$sort);
                break;

            default :
                echo ("unkown date ViewMode '$data[viewMode]<br />");

        }
        div_end("date","before");





    }
    
    function show_showList($contentData,$innerWidth) {
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
        
        $dateList = $this->date_getList($filter);
        $this->data_showList("date",$dateList);
        return 1;
        
        
        $this->date_showList($contentData,$innerWidth,$filter,$sort);
    }
    
    function show_dateList($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        div_start("dateDateListFrame","width:".$frameWidth."px;");
        // echo ("<h1>DateList</h1>");
        // show_array($data);
        
        $todayDate = date("Y-m-d");
        list($year,$month,$day) = explode("-",date("Y-m-d"));
        // echo ("TODAY $day $month $year <br>");
        $todayTS = mktime(12,0,0,$month,$day,$year);
        
        
        
        $dayBack = $data[dayBack];
        if ($dayBack>0) {
            // echo ("GO $dayBack Days back from Today <br>");
            $startTS = $todayTS - ($dayBack * (24*60*60));
            $startDate = date("Y-m-d",$startTS);
            // echo ("START TAG = $startDate <br>");            
        } else {
            $startDate = $todayDate;
            // echo ("START TAG IST HEUTE = $startDate <br>"); 
        }
        
        
        
        $dayForward = $data[dayForward];
        if ($dayForward>0) {
            $endTS = $todayTS + ($dayForward * (24*60*60));
            $endDate = date("Y-m-d",$endTS);
            // echo ("GO $dayForward Days for from Today $endDate <br>");
            
        }
        
//        dayBack = '2'
//dayForward = '21'
//maxDates = '2'
//dateRange = '1'
//dateGroupe = '1'
        // show_array($data);
        $maxDates = $data[maxDates];
        $showDateRange = $data[dateRange];
        $groupeDates = $data[dateGroupe];
        
        $showWeekDay = 1;
        if ($showDateRange) {
            div_start("dateDateListTitle");
            if ($startDate AND $endDate) {
                echo ("Zeitraum vom ".cmsDate_getDayString($startDate, $showWeekDay,0)." bis ".cmsDate_getDayString($endDate, $showWeekDay,0));                
            } else {
                echo ("Termin ab ".cmsDate_getDayString($startDate, $showWeekDay,0));
            }
            div_end("dateDateListTitle");
        }
        
        // $endDate = "2013-05-01";

        $sort = "date";
        $filter[fromDate] = $startDate;
        if ($endDate) $filter[toDate] = $endDate;
        
        
        if ($groupeDates) {
            $dateList = $this->date_getList($filter,$sort,"assoDate");
            $myShowList = $this->dataShow_List($contentData);
            unset($myShowList[date]);
            unset($myShowList[toDate]);
            foreach ($dateList as $date => $value) {
                div_start("dateDateListDay");
                div_start("dateListDayTitle");
                echo (cmsDate_getDayString($date, $showWeekDay,1));
                div_end("dateListDayTitle");

                for ($i=0;$i<count($value);$i++) {
                    $date = $value[$i];
                    $res = $this->dataBox_show("dateDateList",$date,$contentData,$frameWidth,$myShowList);
                    echo ($res);                              
                }
                div_end("dateDateListDay");
            }
        } else {
            $dateList = $this->date_getList($filter,$sort);
            for ($i=0;$i<count($dateList);$i++) {
                $date = $dateList[$i];
                $res = $this->dataBox_show("dateDateList",$date,$contentData,$frameWidth);
                echo ($res);
            }
        }

        div_end("dateDateListFrame");
    }
    
    
    function show_showTable($contentData,$frameWidth) {
        div_start("dateList","width:".$frameWidth."px;");


        $filter = array();
        $sort = "date";
        $dateList = $this->date_getList($filter,$sort);
        // show_array($dateList[1]);
        
        $this->data_showTable("date",$dateList,$contentData,$frameWidth);

        div_end("dateList","before");       
    }

    
    function date_showMonthSlider($contentData,$frameWidth) {
        $data = $contentData[data];

        $height = $data[height];
        
        $date = date("Y-m-d");
       
//         // $date = "2012-12-05";
//        if ($data[date]) $date = $data[date];
//        if ($data[selectDate]) $date = $data[selectDate];
//        if ($_GET[date]) {
//            
//            list($getYear,$getMonth,$getDay) = explode("-",$_GET[date]);
//            
//            
//            if (intval($getYear) AND intval($getMonth)) {
//                $getDate = $_GET[date];
//                $date = $_GET[date];
//            } else {
//                $totayTC = mkTime(12,0,0,$month,$day,$year);
//                
//                switch ($getYear) {
//                    case "yesterday" : 
//                        $newTC = $totayTC - 24*60*60;
//                        // echo ("gestern ".date("Y-m-d",$newTC)."<br />");
//                        break;
//                    case "tommorow" : 
//                        $newTC = $totayTC + 24*60*60;
//                        // echo ("morgen ".date("Y-m-d",$newTC)."<br />");
//                        break;
//                }
//            }
//        }
//        if ($_POST[date]) $date = $_POST[date];
          
        // SelectDate
        list($year,$month,$day) = explode("-",$date);
        // echo ("Selected Date = $day $month $year <br />");
        
        // show Range
        $today = date("Y-m-d");
        list($todayYear,$todayMonth,$todayDay) = explode("-",$today);
        
        $monthBack = $data[monthBack];
        $monthFor  = $data[monthForward];
        
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
//        if ($data[clickAction]) {
//            // show_array($data);
//            $divData[clickAction] = $data[clickAction];
//            $divData[clickTarget] = $data[clickTarget];
//            if ($data[clickParameter]) $divData[clickParameter] = "$data[clickParameter]";
//            if ($data[clickUrl]) {
//                $goPage = "";
//                if ($_GET[region]) $goPage.="?region=".$_GET[region];
//                foreach ($_GET as $key => $value) {
//                    switch ($key) {
//                        case "date"   : break;
//                        case "region" : break;
//                        case "dateId" : break;
//                        default :
//                            if ($key != $divData[clickParameter]) {
//                            
//                                if ($goPage == "") $goPage.="?";
//                                else $goPage .="&";
//                                $goPage .= $key."=".$value;
//                            }
//                    }
//                }               
//                $goPage = $data[clickUrl].$goPage;
//                $divData[clickUrl] = $goPage;
//            } else {
//                $divData[clickTarget] = $data[clickTarget];
//                if ($data[clickTarget] == "page") {
//                    $pageId = $data[clickPage];
//                    $pageName = $pageInfo[page];
//                    //show_array($pageInfo);
//                    
//                    // echo ("PageName = $pageName ".substr($pageName,0,5)."<br />");
//                    if ($pageName == "admin_cmsDates.php" or substr($pageName,0,5) == "admin") {
//                        $divData[clickUrl] = "admin_cmsDates.php";
//                    } else {
//
//                        global $pageInfo,$pageData;
//                        $goPage = "";
//                        foreach ($_GET as $key => $value) {
//                            if ($key != "date") {
//                                if ($goPage == "") $goPage.="?";
//                                else $goPage .="&";
//                                $goPage .= $key."=".$value;
//                            }
//                        }
//                        $goPage = $pageInfo[page].$goPage;
//                        $divData[clickUrl] = $goPage;
//                        // show_array($pageInfo);
//                       //  echo ($goPage);
//                        // $pageClickData = cms_page_getData(intval($pageId));
//                        if (is_array($pageClickData)) $divData[clickUrl] = $pageClickData[name].".php";
//                    }
//                }
//            }
//        }
        // $divData[navigateUrl] = "/cms_$GLOBALS[cmsVersion]/getData/datesData.php?cmsVersion=$GLOBALS[cmsVersion]&cmsName=$GLOBALS[cmsName]&out=calendar";
        // $divData[actMonth] = $year."-".$month;
        
        
        // $divData = array();
        $style = "";
        $style = "width:".$frameWidth."px;float:left;";
        if ($height) $style .= "height:".$height."px;";
        $divData[style] = $style;
                
        $startFrame = 0;
        
        $sliderName = "dateMonthSlider";
        
        for ($i=0;$i<=($monthBack+$monthFor);$i++) {
            // echo ("$i , $startMonth - $startYear <br />");
            if ($month==$startMonth AND $year==$startYear) $startFrame = $i;

            $str = div_start_str("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id],$divData);

            $monthStr = $startYear."-".$startMonth;

            $showData = array();
            // erster Monat
            if ($i==0) $showData[showBackActive] = 0;
            else $showData[showBackActive] = 1;
            
            // letzter Monat
            if ($i == $monthBack+$monthFor) $showData[showNextActive] = 0;
            else $showData[showNextActive] = 1;
            
//            // erster Monat
//            if ($i==0) $contentData[data][showBackActive] = 0;
//            else $contentData[data][showBackActive] = 1;
//            
//            if ($i == $monthBack+$monthFor) $contentData[data][showNextActive] = 0;
//            else $contentData[data][showNextActive] = 1;
            
            //  $str .= $this->date_showMonth_Content_Str($contentData[id],$monthStr,$showData, $frameWidth);
            $str .= $this->date_showMonth_Month($sliderName,$monthStr,$showData, $frameWidth);
            
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
            div_start("dateMonthListFrame","width:".$frameWidth."px;");
            $showData = array();
            // $showData[mainDiv] = $divData;
            $showData[startFrame] = $startFrame;
            
            
            $showData[direction] = "horozontal";
            $showData[speed] = 500;
            $showData[loop] = 0;
            $showData[notloop] = 1;
            
            
            
            $width = $frameWidth;
            $name = "$sliderName"; //_".$contentData[id];
           
            cmsSlider("bxSlider", $name, $contentList, $showData, $width, $height);
            div_end("dateMonthListFrame");
        }
    }
    
    function date_showMonth_Month($sliderName,$monthStr,$showData,$frameWidth) {



        $out .= $this->show_showMonth_title($sliderName,$monthStr,$showData,$frameWidth);
        
        
        
        $days = $showData[dayCount];
        if (!$days) $days = 7;
        
        
        $borderWidth=1;
        
        $checkExistDate = 1;

        
        
        $dayWidth = "".(($frameWidth - (($days+1)*$borderWidth)) / $days);
        if (strpos($dayWidth,".")) {
            $dayWidth = intval(substr($dayWidth,0,strpos($dayWidth,".")));
        } else {
            $dayWidth = intval($dayWidth);
        }
        

        list($year,$month) = explode("-",$monthStr);

       

        
       
        $startDayofMonth = 1; // 1=Montag 0=Sonntag

        $startDay = mktime(12,0,0,$month,1,$year);

        // get StartDate for StartDayofMonth
        $weekDay = date("w",$startDay);
        while ($weekDay != $startDayofMonth) {
            $startDay = $startDay - (24 * 60 * 60);
            $weekDay = date("w",$startDay);
        }
        $startDate = date("d.m.Y w",$startDay);
        // $out .= "StartDay = $startDay $startDate $weekDay ".$this->date_dayCode($weekDay)." <br /> ");

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
                $divName .= " dateMonthTitle_".substr($this->date_dayCode($weekDay),0,2);
                $out .= div_start_str($divName,$divData);
                $out .= $this->date_dayCode($weekDay,$dayWidth);
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
                        $date = cmsDates_getList(array("date"=>$weekDay,"show"=>1));
                        if (is_array($date)) {
                            if (count($date)>0) $divName .= " dateExist";
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
    
    function show_showMonth_title($sliderName,$monthStr,$showData,$frameWidth) {
         
        list($year,$month) = explode("-",$monthStr);

        $out = "";
        // dateMonthListTitle
        $out .= div_start_str("dateMonthListTitle","width:".$frameWidth."px;");
        // $out .= "GO MONAT back = $goMonthBack GO MONAT Vor = $goMonthFor");

        // GO BACK
        $showBack = 1;
        $showBackActive = 1;
        if (is_integer($showData[showBack])) $showBack = $showData[showBack];
        if (is_integer($showData[showBackActive])) $showBackActive = $showData[showBackActive];
        
        $showNext = 1;           
        $showNextActive = 1;
        if (is_integer($showData[showNext])) $showNext = $showData[showNext];
        if (is_integer($showData[showNextActive])) $showNextActive = $showData[showNextActive];
            
        if ($showBack) {
            $divData = array();
            $divData[style] = "float:left;";
            //  $divName = "dateNavigateBack calendarSlider_".$frameId."_back";
            $divName = "sliderBackButton ".$sliderName."_back";
            if (!$showBackActive) {
                // $divName .= " dateNavigateDisabled";
                $divName =  "sliderBackButton sliderBackButton_disabled ".$sliderName."_back_disabled";
            }
            $out .= div_start_str($divName,$divData);
            $out .= "&#060;&#060;";
            $out .= div_end_str($divName);
        }

        $monthStrOut = $this->date_monthStr($month);
        $out .= div_start_str("dateNavigateMiddle","width:".($frameWidth-2*35)."px;float:left;text-align:center;");
        //$out .= div_start_str("dateNavigateMiddle","float:left;text-align:center;");
        $out .= $monthStrOut." ".$year;
        $out .= div_end_str("dateNavigateMiddle");

        
        if ($showNext) {
            $divData = array();
             $divData[style] = "float:right;";
            if ($showNextActive) {
                // $divName = "dateNavigateNext calendarSlider_".$frameId."_next";
                $divName = "sliderNextButton ".$sliderName."_next";
            } else {
                // $divName = "dateNavigateNext dateNavigateDisabled";
                $divName = "sliderNextButton sliderNextButton_disabled ".$sliderName."_next_disabled";
            }
            $out .= div_start_str($divName,$divData);
            $out .= "&#062;&#062;";
            $out .= div_end_str($divName);
        }
        
        
        $out .= div_end_str("dateMonthListTitle","before");
        return $out;
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
        if ($this->showLevel>=8) {
            echo ("<a href='admin_cmsDates.php?view=edit&id=$dateId' class='cmsLinkButton cmsSecond' >editieren</a>");
        }
        echo ("<br />");
        return 1;
        
    }

    function date_showList($data,$frameWidth) {
        $maxDates = $data[maxDates];

        $borderWidth = 1;
        $innerWidth = $frameWidth - (2 * $borderWidth);

        for($i=0;$i<$maxDates;$i++) {
            $divData = array("style"=>"width:".$innerWidth."px;height:30px;border:1px solid #eee; margin-bottom:2px;");
            div_start("dateDate",$divData);
            echo ($i);
            div_end("dateDate");

        }


    }

    
    function date_getList($filter=array(),$sort="date",$out="") {
       
        $dateList = cmsDates_getList($filter, $sort);
        
        switch ($out) {
            case "assoDate" :
                $newList = array();
                for ($i=0;$i<count($dateList);$i++) {
                    $date = $dateList[$i];
                    
                    $fromDate = $date[date];
                    
                    if (!is_array($newList[fromDate])) $newList[$fromDate] = array();
                    $newList[$fromDate][] = $date ;                    
                }
                return $newList;
        }
        
        return $dateList;
    }
    
    function date_monthStr($month) {
        return cmsDates_monthStr($month);
    }

    function date_dayCode($dayCode,$width=100) {
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

    function date_showMonth($contentData,$frameWidth) {
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
        $monthStr = $this->date_monthStr($month);
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
        $this->date_showMonth_Content($contentData[id],$monthStr,$checkExistDate, $frameWidth);
        div_end("calendarFrame calendarFrame_$contentData[id] dateMonthList_".$contentData[id]);
    }
    
    


    function date_showMonth_Content_Str($frameId,$monthStr,$contentData,$frameWidth) {
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
            $infoMonthBack = $this->date_monthStr($goMonthBack)." ".($year-1);
            $goMonthBack = ($year-1)."-".$goMonthBack."-01";
        } else {
            if ($goMonthBack>9) $goMonthBack = "".$goMonthBack;
            else $goMonthBack = "0".$goMonthBack;
            $infoMonthBack = $this->date_monthStr($goMonthBack)." ".($year);
            $goMonthBack = $year."-".$goMonthBack."-01";
        }

        // MONAT VOR
        $goMonthFor = intVal($month) + 1 ;
        if ($goMonthFor >12 ) {
            $goMonthFor = "01";
            $infoMonthFor = $this->date_monthStr($goMonthFor)." ".($year+1);
            $goMonthFor = ($year+1)."-".$goMonthFor."-01";
        } else {
            if ($goMonthFor>9) $goMonthFor = "".$goMonthFor;
            else $goMonthFor = "0".$goMonthFor;
            $infoMonthFor = $this->date_monthStr($goMonthFor)." ".($year);
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

        $monthStrOut = $this->date_monthStr($month);
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
        // $out .= "StartDay = $startDay $startDate $weekDay ".$this->date_dayCode($weekDay)." <br /> ");

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
                $divName .= " dateMonthTitle_".substr($this->date_dayCode($weekDay),0,2);
                $out .= div_start_str($divName,$divData);
                $out .= $this->date_dayCode($weekDay,$dayWidth);
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
                        $date = cmsDates_getList(array("date"=>$weekDay,"show"=>1));
                        if (is_array($date)) {
                            if (count($date)>0) $divName .= " dateExist";
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

    
    


    function date_showMonth_Content($frameId,$monthStr,$checkExistDate,$frameWidth) {
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
            $infoMonthBack = $this->date_monthStr($goMonthBack)." ".($year-1);
            $goMonthBack = ($year-1)."-".$goMonthBack."-01";
        } else {
            if ($goMonthBack>9) $goMonthBack = "".$goMonthBack;
            else $goMonthBack = "0".$goMonthBack;
            $infoMonthBack = $this->date_monthStr($goMonthBack)." ".($year);
            $goMonthBack = $year."-".$goMonthBack."-01";
        }

        // MONAT VOR
        $goMonthFor = intVal($month) + 1 ;
        if ($goMonthFor >12 ) {
            $goMonthFor = "01";
            $infoMonthFor = $this->date_monthStr($goMonthFor)." ".($year+1);
            $goMonthFor = ($year+1)."-".$goMonthFor."-01";
        } else {
            if ($goMonthFor>9) $goMonthFor = "".$goMonthFor;
            else $goMonthFor = "0".$goMonthFor;
            $infoMonthFor = $this->date_monthStr($goMonthFor)." ".($year);
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

        $monthStr = $this->date_monthStr($month);
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
        // echo ("StartDay = $startDay $startDate $weekDay ".$this->date_dayCode($weekDay)." <br /> ");

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
                $divName .= " dateMonthTitle_".substr($this->date_dayCode($weekDay),0,2);
                div_start($divName,$divData);
                echo ($this->date_dayCode($weekDay,$dayWidth));
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
                        $date = cmsDates_getList(array("date"=>$weekDay,"show"=>1));
                        if (is_array($date)) {
                            if (count($date)>0) $divName .= " dateExist";
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

                    // $divName .= " dateMonth_".substr($this->date_dayCode($weekDayCode),0,2);

//                    if (is_array($date)) {
//                        if (count($date)>0) {
//                            for ($d=0;$d<count($date);$d++) {
//                                echo ($date[$i][name]);
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

    function date_showWeek($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $date = date("Y-m-d");
        // echo ("Date is $date <br>");
        $weekDay = cmsDates_dayCode($date);

        list($year,$month,$day) = explode("-",$date);
        $today = mktime(12,0,0,$month,$day,$year);

        if ($weekDay) {
            $startDay = $today - ($weekDay * (24*60*60));
            $startDayStr = date("Y-m-d",$startDay);

            // echo ("StartDay = $startDay $startDayStr <br>");

        } else {
            $startDay = $today;
        }
        $endDate = $startDay + (6 * (24*60*60));
        $endDateStr = date("Y-m-d",$endDate);

        
       //  show_array($data);
        
        $anzBack = $data[weeksBack];
        $anzForward = $data[weeksForward];
        $showDayStr = 1;
        // $showEmptyDates = 1;
        
        $showEmptyDates = $data[showEmpty];

        $show_date = $data[date_show];
        $show_date_view = $data[date_view];
      
        $showWeek = array();

        $firstFrame = "thisWeek";
        $lastFrame  = "thisWeek";

        // VERGANGEN HEIT
        if ($anzBack>0) {
            $firstFrame = "back_".$anzBack;
            for ($i=$anzBack;$i>0;$i--) {
                $startWeek = $startDay - ($i*7*(24*60*60));
                $startWeekStr = date("Y-m-d",$startWeek);

                $endWeek =  $startWeek + (6 * (24*60*60));
                $endWeekStr = date("Y-m-d",$endWeek);

                // echo ("goBack $i $startWeekStr - $endWeekStr <br>");
                $showWeek["back_".$i] = array("startCode"=>$startWeek,"start"=>$startWeekStr,"end"=>$endWeekStr);
            }
        }

        // AKTUELL
        $showWeek["thisWeek"] = array("startCode"=>$startDay,"start"=>$startDayStr,"end"=>$endDateStr);

        // ZUKUNFT
        if ($anzForward>0) {
            $lastFrame = "future_".$anzForward;
            for ($i=1;$i<=$anzForward;$i++) {
                $startWeek = $startDay + ($i*7*(24*60*60));
                $startWeekStr = date("Y-m-d",$startWeek);

                $endWeek =  $startWeek + (6 * (24*60*60));
                $endWeekStr = date("Y-m-d",$endWeek);

                // echo ("goFuture $i $startWeekStr - $endWeekStr <br>");
                $showWeek["future_".$i] = array("startCode"=>$startWeek,"start"=>$startWeekStr,"end"=>$endWeekStr);
            }
        }


        // START FRAME
        div_start("dateWeek","width:".$frameWidth."px;");

        $sliderName ="dateWeekSlider";
        
        $days = 5;
        $borderWidth=1;
        $borderColor="#ccc";
        $innerWidth = $frameWidth - 2 * $borderWidth;

        // SHOWDATA
        $myShowList = $this->dateShow_List($contentData);
        // show_array($myShowList);
        unset($myShowList[date]);
        unset($myShowList[toDate]);
        
        
        
        foreach ($showWeek as $key => $value) {

            $str = div_start_str("dateWeekFrame dateWeekFrame_$key");
            // $str .= "START WEEK $key <br>";
            
            $startDate = $value[start];
            $endDate = $value[end];

            $dateList = cmsDates_getList(array("fromDate"=>$startDate,"toDate"=>$endDate),"date","out__");
            for ($d=0;$d<count($dateList);$d++) {
                $date = $dateList[$d];
                // show_array($date);
            }


            $str.= div_start_str("weekSliderTitle weekSliderTitle_$key","width:".$frameWidth."px;");
            $navWidth = 40;

            // backWards
            $activeBack = 1;
            if ($key ==  $firstFrame) $activeBack = 0;
            if ($activeBack) $divName = "sliderBackButton ".$sliderName."_back";
            else $divName = "sliderBackButton sliderBackButton_disabled ".$sliderName."_back_disabled";
       
            $str.= "<div class='$divName' style='float:left;' > << </div>";

            if ($showDayStr) {
                $str.= div_start_str("weekSliderTitleText","float:left;width:".($frameWidth-(2*$navWidth))."px;text-align:center;");
                $startStr = cmsDate_getDayString($startDate,$showWeekDay);
                $endStr = cmsDate_getDayString($endDate,$showWeekDay);
                $str .= "Zeitraum: $startStr - $endStr";
                $str.= div_end_str("weekSliderTitleText");
            }
            
            // forwardWards
            $activeNext = 1;
            if ($key ==  $lastFrame) $activeNext = 0;
            if ($activeNext) $divName = "sliderNextButton ".$sliderName."_next";
            else $divName = "sliderNextButton sliderNextButton_disabled ".$sliderName."_next_disabled";

            $str .= "<div class='$divName' style='float:right;' > >> </div>";
            $str.= div_end_str("weekSliderTitle weekSliderTitle_$key","before");
            
            $startCode = $value[startCode];
            
            
           
            
            
            $foundInWeek = 0;
            for ($i=0;$i<7;$i++) {

                $dayCode = $startCode + ($i * (24*60*60));
                $day = date("Y-m-d",$dayCode);

                $found = array();
                for ($d=0;$d<count($dateList);$d++) {
                    $date = $dateList[$d];
                    if ($date[date] == $day) {
                        $found[] = $dateList[$d];
                    }

                // show_array($date);
                }

                $show = 0;
                if (count($found)) {
                    $foundInWeek = $foundInWeek + count($found);
                    $show = 1;
                } else {
                    if ($showEmptyDates) $show = 1;
                }

                if ($show) {
                    $str .= div_start_str("dateWeekDay");
                    $str .= div_start_str("dateWeekDayTitle");
                    switch ($show_date_view) {
                        case "short" : $str .= cmsDate_getDayString($day,0,0)."<br />";break;
                        case "long"  : $str .= cmsDate_getDayString($day,0,1)."<br />"; break;
                        case "weekDay"  : $str .= cmsDate_getDayString($day,1,1)."<br />"; break;
                        case "noYear"  : $str .= cmsDate_getDayString($day,0,"no")."<br />"; break;
                        default :
                            $str .= "View = $show_date_view ".cmsDate_getDayString($day,$showWeekDay)."<br />";
                            
                    }
                    $str .= div_end_str("dateWeekDayTitle");
                    // $str .= cmsDate_getDayString($day,$showWeekDay)."<br>";
                    for ($d=0;$d<count($found);$d++) {
                        $date = $found[$d];
                        
                        $res = $this->dataBox_show("dateWeekDate", $date, $contentData, $frameWidth-2*$borderWidth,$myShowList);
                        $str .= $res;

//                        $dateTime = $date[time];
//                        
//                        $dateName = $date[name];
//                        $dateInfo = $date[info];
//                        $str .= "$dateTime<br>";
//                        $str .= "<h3>$dateName</h3>";
//                        $str.= "$dateInfo";
                        // foreach ($date as $dateKey => $dateValue) str.= "$dateKey = $dateValue <br>";
                    }


                    $str .= div_end_str("dateWeekDay");
                }
                



            }
            if ($foundInWeek === 0 AND !$showEmptyDates ) {
                $str .= div_start_str("dateWeek_noDates");
                $str .= "KEINE TERMIN IN DIESER WOCHE";
                $str .= div_end_str("dateWeek_noDates");
            }


            
            $str .= div_end_str("dateWeekFrame dateWeekFrame_$key");
            $content[$key] = $str;
            // echo ($str);
            
            // echo ("START WEEK $key <br>");
            
            
            
        }


        // show_array($showWeek);

        div_end("dateWeek");

        
        if (count($content)) {
            $type = null;
            $name = $sliderName;
            $showData = array();
            $width = $frameWidth;

            $direction = $data[direction];
            if (!$direction) $direction = "horizontal";
            $loop      = $data[loop];
            if (!$loop) $loop = 0;
            $pause = $data[pause];
            if (!$pause) $pause = 5000;
            $speed = $data[speed];
            if (!$speed) $speed = 500;
            $navigate = $data[navigate];
            $pager     = $data[pager];

            $showData[loop] = 0;
            $showData[notloop] = 1;
            $directionList = array("vertical","horizontal","fade");
            $showData[direction] = "horizontal"; // $directionList[0];
            $showData[mainDiv] = $divData;
            $showData[startFrame] = $anzBack;
            $showData[speed] = 500;

            $showData[pause] = 5000;
            $showData[navigate] = 0;
            $showData[page] = 0;
            cmsSlider("bxSlider", $name, $content, $showData, $width, $height);
        }
    }


    function viewMode_filter_select_getOwnList($filter,$sort) {
        $res = array();
        $res["list"]  = array("name"=>"Liste");
        // $res["table"] = "Tabelle";
        $res["dateList"]  = array("name"=>"Liste mit Terminen");
        
        $res["slider"] = array("name"=>"Termin Slider");

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



    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $this->tableName = "date";
        $res = array();

//        $mainTab = "date";
//        // Add ViewMode
//        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
//        if (is_array($viewModeList)) {
//            $addToTab = $mainTab;
//            for ($i=0;$i<count($viewModeList);$i++) {
//                // echo ("Add to $addToTab $viewModeList[$i]<br />");
//                $res[$addToTab][] = $viewModeList[$i];
//            }
//        }

        
        $mainTab = "date";
        $viewMode = $data[viewMode];
        
        // Add ViewMode
        $viewModeList = $this->editContent_ViewMode($editContent,$frameWidth);
        if (is_array($viewModeList)) {
            $addToTab = $mainTab;
            $res[$addToTab][showName] = $this->lga("content",$this->tableName."Tab");
            $res[$addToTab][showTab] = "Simple";
            for ($i=0;$i<count($viewModeList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $viewModeList[$i];
            }
        }
        
//        $viewMode = $data[viewMode];
//        $addList = array();
//        switch ($viewMode) {
//            case "week" : $addList = $this->date_editContent_week($editContent,$frameWidth);
//        }
//        for ($i=0;$i<count($addList);$i++) {             
//            $res[$addToTab][] = $addList[$i];
//        }

        
        // ShowList
        $showList = $this->dateShow_List();
        $addList = $this->dataBox_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "dateShow";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        $res[$addToTab][showName] = $this->lga("content",$this->tableName."ViewTab");
            $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        
        // Add FILTER
        $filterList = $this->editContent_filterView($editContent,$frameWidth);
        if (is_array($filterList)) {
            $addToTab = "filter";
            $res[$addToTab][showName] = $this->lga("content",$this->tableName."ViewTab");
            $res[$addToTab][showTab] = "Simple";
            for ($i=0;$i<count($filterList);$i++) {
                // echo ("Add to $addToTab $viewModeList[$i]<br />");
                $res[$addToTab][] = $filterList[$i];
            }
        }

        // ACTION 
        $addList = $this->action_editContent($data,$showList);
       // show_array($addList);
        $addToTab = "action";
        if (!is_array($res[$addToTab])) $res[$addToTab] = array();
        $res[$addToTab][showName] = $this->lga("content",$this->tableName."ActionTab");
        $res[$addToTab][showTab] = "Simple";
        for ($i=0;$i<count($addList);$i++) {
            // echo ("ADD $i $addList[$i] <br>");
            $res[$addToTab][] = $addList[$i];
        }
        
        return $res;
    }
    
    function editContent_ViewMode_ownViewMode($viewMode,$editContent,$frameWidth) {
//        $data = $editContent[data];
//        if (!is_array($data)) $data = array();
        
        // $viewMode = $data[viewMode];
        switch ($viewMode) {
            case "week" :
                $res = $this->date_editContent_week($editContent, $frameWidth);
                break;
            
            case "monthSlider" :
                $res = $this->date_editContent_monthSlider($editContent, $frameWidth);
                break;
            case "month": break;
            case "dateList" :
                $res = $this->date_editContent_dateList($editContent, $frameWidth);
                break;
            
            default : 
                echo("<h1> UNKONW $viewMode in editContent_ViewMode_ownViewMode</h1>");
            
        }
        return $res;
        echo (" function editContent_ViewMode_ownViewMode($viewMode,$editContent,$frameWidth)<br>");

    

    }
    
    
    
    function date_editContent_week($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        // weeks Back
        $add=array();
        $add[text] = "Wochen zurück";
        $add[input] = "<input type='text' style='width:30px;' value='$data[weeksBack]' name='editContent[data][weeksBack]' />";
        $res[] = $add;
        
        // weeks Forward
        $add=array();
        $add[text] = "Wochen in Zukunft";
        $add[input] = "<input type='text' style='width:30px;' value='$data[weeksForward]' name='editContent[data][weeksForward]' />";
        $res[] = $add;

        // Show Empty Date
        $add=array();
        $add[text] = "Tage ohne Termin zeigen";
        if ($data[showEmpty]) $checked= "checked='checked'";
        else $checked = "";
        $add[input] = "<input type='checkbox' $checked value='1' name='editContent[data][showEmpty]' />";
        $res[] = $add;

        return $res;        
    }
    
    function date_editContent_monthSlider($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        // weeks Back
        $add=array();
        $add[text] = "Monate zurück";
        $add[input] = "<input type='text' style='width:30px;' value='$data[monthBack]' name='editContent[data][monthBack]' />";
        $res[] = $add;
        
        // weeks Forward
        $add=array();
        $add[text] = "Monate in Zukunft";
        $add[input] = "<input type='text' style='width:30px;' value='$data[monthForward]' name='editContent[data][monthForward]' />";
        $res[] = $add;

        // Show Empty Date
        $add=array();
        $add[text] = "Highlight Termine";
        if ($data[highlightDates]) $checked= "checked='checked'";
        else $checked = "";
        $add[input] = "<input type='checkbox' $checked value='1' name='editContent[data][highlightDates]' />";
        $res[] = $add;
        
        
        return $res;
        
    }

    function date_editContent_dateList($editContent, $frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        
        // weeks Back
        $add=array();
        $add[text] = "Tage zurück";
        $add[input] = "<input type='text' style='width:30px;' value='$data[dayBack]' name='editContent[data][dayBack]' />";
        $res[] = $add;
        
        // weeks Forward
        $add=array();
        $add[text] = "Tage in Zukunft";
        $add[input] = "<input type='text' style='width:30px;' value='$data[dayForward]' name='editContent[data][dayForward]' />";
        $res[] = $add;

        // maxDates
        $add=array();
        $add[text] = "Maximale Terminanzahl";
        $add[input] = "<input type='text' style='width:30px;' value='$data[maxDates]' name='editContent[data][maxDates]' />";
        $res[] = $add;
        
        
        // Show Daterange
        $add=array();
        $add[text] = "Zeitraum zeigen";
        if ($data[dateRange]) $checked= "checked='checked'";
        else $checked = "";
        $add[input] = "<input type='checkbox' $checked value='1' name='editContent[data][dateRange]' />";
        $res[] = $add;
        
        
        // Groupe Days
        $add=array();
        $add[text] = "Termine gruppiert nach Datum";
        if ($data[dateGroupe]) $checked= "checked='checked'";
        else $checked = "";
        $add[input] = "<input type='checkbox' $checked value='1' name='editContent[data][dateGroupe]' />";
        $res[] = $add;
        
        
        return $res;
    }
    
    
    function dataShow_List($contentData=array()) {
        return $this->dateShow_List();
    }
    
    function dateShow_List($contentData=array()) {
        $show = array();
        $show[name] = array("name"=>"Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[info] = array("name"=>"2. Überschrift","style"=>array("h1"=>"Überschrift 1","h2"=>"Überschrift 2","h3"=>"Überschrift 3","h4"=>"Überschrift 4"),"position"=>1);
        $show[longInfo] = array("name"=>"Text","style"=>array("left"=>"Linksbündig","center"=>"Zentriert","right"=>"Rechtsbündig"),"position"=>1);
        $show[category] = array("name"=>"Kategorie","description"=>"Bezeichnung zeigen","position"=>1);
        $show[image] = array("name"=>"Bilder","view"=>array("slider"=>"Bild Slider","first"=>"erstes Bild","random"=>"Zufallsbild","gallery"=>"Bildgalery"),"position"=>1);
        
        
        $show[date] =  array("name"=>"Datum","position"=>1,"description"=>"Bezeichnung","type"=>"date","view"=>array("short"=>"Kurzes Datum","long"=>"langes Datum","weekDay"=>"Mit Wochentag","noYear"=>"Ohne Jahr"));
        $show[toDate] = array("name"=>"bis Datum","type"=>"date");
        $show[time] = array("name"=>"Uhrzeit","position"=>1,"description"=>"Bezeichnung","type"=>"time");
        $show[toTime] = array("name"=>"bis Uhrzeit","type"=>"time");
        
        $show[region] = array("name"=>"Region","position"=>1,"description"=>"Bezeichnung");
        $show[location] = array("name"=>"Ort","position"=>1,"description"=>"Bezeichnung");
        $show[url] = array("name"=>"Link","position"=>1,"description"=>"Bezeichnung");
        
        $show[basket] = array("name"=>"Warenkorb","description"=>"Bezeichnung zeigen","position"=>1);
        // $show[url] = array("name"=>"Webseite","description"=>"Bezeichnung zeigen","position"=>1);
        return $show;
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
        $res = array();
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

function cmsType_date_class() {
    if ($GLOBALS[cmsTypes]["cmsType_date.php"] == "own") $dateClass = new cmsType_date();
    else $dateClass = new cmsType_date_base();
    return $dateClass;
}

function cmsType_date($contentData,$frameWidth) {
    $dateClass = cmsType_date_class();
    return $dateClass->show($contentData,$frameWidth);
}



function cmsType_date_editContent($editContent,$frameWidth) {
    $dateClass = cmsType_date_class();
    return $dateClass->date_editContent($editContent,$frameWidth);
}

function cmsType_date_getName() {
    $dateClass = cmsType_date_class();
    $name = $dateClass->getName();
    return $name;
}



?>
