<?php // charset:UTF-8

    function mainCalendar_show($date=0) {
        $calenderWidth = 250;
        $padding = 8;
        $border = 1;
        $frameWidth = $calenderWidth - (2+$padding) - (2*$border);



        $contentData = array();
        $contentData["id"] = "sidebar";
        $contentData[data] = array();
        $contentData[data][clickAction] = "showDate";
        $contentData[data][clickTarget] = "page";
        $contentData[data][clickUrl] = "kalender.php";
        $contentData[data][checkExistDate] = 0;

        
        if ($date) {
            list($year,$month,$day) = explode("-",$date);
            if (intval($year) AND intval($month)) {
                $contentData[selectDate] = $year."-".$month;
            }                
        } else {
            if ($_GET[date]) {            
                list($year,$month,$day) = explode("-",$_GET[date]);

                if (intval($year) AND intval($month)) {
                    // echo ("$year $month $day <br>");
                }
                $contentData[selectDate] = $year."-".$month;
            }
        }


        // echo ("<script type='text/javascript' src='/cms_$GLOBALS[cmsVersion]/cms_contentTypes/cmsType_dateList.js'>\n");

        $dateClass = cmsType_dateList_class();

        // $dateClass->dateList_showMonth($contentData,$frameWidth);

         if (method_exists($dateClass,"dateList_showMonthSlider")) {
             $contentData[data][monthBack] = 2;
             $contentData[data][monthFor] = 2;
             $contentData[data][height] = 160;
             $dateClass->dateList_showMonthSlider($contentData,$frameWidth);
         } else {
             $dateClass->dateList_showMonth($contentData,$frameWidth);
         }
    }

    function mainCalendar_showAdmin($showdate) {
        $calenderWidth = 250;
        $padding = 8;
        $border = 1;
        $frameWidth = $calenderWidth - (2+$padding) - (2*$border);



        $contentData = array();
        $contentData["id"] = "sidebarAdmin";
        $contentData[data] = array();
        $contentData[data][clickAction] = "showDate";
        $contentData[data][clickTarget] = "page";
        $contentData[data][clickUrl] = "index.php";
        $contentData[data][clickParameter] = "showdate";
        $contentData[data][checkExistDate] = 0;


        if ($showdate) {
            list($year,$month,$day) = explode("-",$showdate);
            // echo ("$year $month $day $showdate <br>");
            if (intval($year) AND intval($month)) {
                $contentData[data][selectDate] = $year."-".$month;
                // echo ("$contentData[selectDate] <br>");
            }
        } else {
            if ($_GET[date]) {
                list($year,$month,$day) = explode("-",$_GET[date]);

                if (intval($year) AND intval($month)) {
                    // echo ("$year $month $day <br>");
                }
                $contentData[selectDate] = $year."-".$month;
            }
        }


        // echo ("<script type='text/javascript' src='/cms_$GLOBALS[cmsVersion]/cms_contentTypes/cmsType_dateList.js'>\n");

        $dateClass = cmsType_dateList_class();

        // $dateClass->dateList_showMonth($contentData,$frameWidth);

         if (method_exists($dateClass,"dateList_showMonthSlider")) {
             $contentData[data][monthBack] = 2;
             $contentData[data][monthFor] = 2;
             $contentData[data][height] = 160;
             $dateClass->dateList_showMonthSlider($contentData,$frameWidth);
         } else {
             $dateClass->dateList_showMonth($contentData,$frameWidth);
         }

    }





 ?>
