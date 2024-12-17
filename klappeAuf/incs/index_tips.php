<?php // charset:UTF-8

function index_tipList($showdate) {
    
    $setDate = date("Y-m-d");
    if ($showdate) $setDate = $showdate;
    $startDay = $setDate;
    
    // $startDay = "2012-10-10";
    
    list($year,$month,$day) = explode("-", $startDay);
    $today = mktime(12,0,0,$month,$day,$year);
    // Anzahl der Tagestipps
    $countDays = 5;

    // Datum mit Wochentag
    $showWeekDay = 1;
    $onlyOneTipPerDay = 0;

    // Take Image
    $takeImage = "first"; // random | first | last

    // Data for ShowImage
    $imageSize = 400;
    $ratio = 4/3;
    $showData = array();
    $showData[frameWidth] = $imageSize;
    $showData[frameHeight] = $imagSize / $ratio;
    $showData[hAlign] = "left";
    $showData[vAlign] = "top";
    $showData[ratio] = $ratio;
    $showData[out] = "url"; // return only imageUrl

    // Result
    $res = array();


    for ($i=0;$i<$countDays;$i++) {
        $day = $today + ($i*24*60*60);
        // echo ("Today = ".date("d.m.Y",$day)."<br />");
        $filter = array();
        $filter[date] = date("Y-m-d",$day); // nur Termine vom Tag
        $filter[show] = 1;                  // nur aktive Termine
        // $filter[image] = "!=''";            // nur Termine mit Bilder
        $filter["highlight"] = 1;                 // nur Tagestipp Termine
        // $filter["image"] = "!=''";
        $sort = "name";
        $out = "out__"; // only for debug

        // get List of Dates with this Filter;
        $dateList = cmsDates_getList($filter, $sort,$out);

        if (is_array($dateList) and count($dateList)) {
            
            
            if ($onlyOneTipPerDay) {
                // nur ein Tip pro Tag
                $anz = count($dateList);
                $randomNr = rand(0,$anz-1);
                $dateList  = array($dateList[$randomNr]);
                
            }
            
            for ($d=0;$d<count($dateList);$d++) {
                $date = $dateList[$d];
            

                // echo ("ANzahl Termine am ".date("d.m.Y",$day)." = $anz<br />");


                // get Image
                $image = $date[image];
                $imgStr = "";
                $imageId = 0;
                
                
//                $link = $date[link];
//                if (substr($link,0,9) == "dateMain:") {
//                    $linkId = substr($link,9);
//                    if ($_SESSION[userLevel]>=9) {
//                        
//                        echo ("Tagesstip is linkedDate $linkId <br>");
//                    }
//                    
//                }
                
                
                
                if (intval($image)>0) $image = "|$image|";
               
                $imageList = explode("|",$image);
                $anz = count($imageList);
                if ($anz == 1) {
                    if ($date[link]) {
                        $linkList = explode("|",$date[link]);
                        for ($lnr=0;$lnr<count($linkList);$lnr++) {
                            list($linkType,$linkIds) = explode (":",$linkList[$lnr]);
                            if ($linkType == "article" AND intval($linkIds)) {
                                // echo ("Take Image form Article $linkIds <br>");
                                $articleData = cmsArticles_getById($linkIds);
                                if (is_array($articleData)) {
                                    $image = $articleData[image];
                                    if (intval($image)>0) $image = "|$image|";
                                    $imageList = explode("|",$image);
                                    $anz = count($imageList);
                                }
                            }
                        }
                    }                                       
                }
                
                
                if ($anz>2) {
                    switch ($takeImage) {
                        case "random" :
                            $randomNr = rand(1,$anz-2);
                            $imageId = intval($imageList[$randomNr]);
                            break;
                        case "first" :
                            $imageId = intval($imageList[1]);
                            break;
                        case "last" :
                            $takeNr = count($imageList) - 2;
                            $imageId = intval($imageList[$takeNr]);
                            break;
                    }
                }
                
                //if (!$imageId) $imageId = rand(1,100);

                if ($imageId>0) {
                    $imageData = cmsImage_getData_by_Id($imageId);
                    if (is_array($imageData)) {
                        $imgStr = cmsImage_showImage($imageData, $imageSize,$showData);
                        
                        $addData = array();
                        $addData[name] = $date[name];
                        $addData[subName] = $date[subName];

                        $addData[id] = $date[id];
                        $addData[date] = cmsDate_getDayString($date[date], $showWeekDay);
                        $addData[time] = cmsDate_getTimeString($date[time],2);
                        $addData[image] = $imgStr;
                        // echo ("$image $imageId $imgStr $anz $randomNr <br>");
                        //show_array($addData);
                        // Add Data to Result
                        $res[] = $addData;
                        
                        
                    }
                } else {
                    // echo "no ImageId $date[id]<br>";
                }
            }
        }
    }
    return ($res);
}


function tip_show($showdate) {
    $tipList = index_tipList($showdate);
    if (count($tipList)>0) {
        echo ("<div class='coda-slider' id='slider-id'> ");
        
        $nr = 0;
        for ($i=0;$i<count($tipList);$i++) {
            $tipData = $tipList[$i];
            
            // image 
            $tipId = $tipData[id];
            $tipName = $tipData[name];
            $tipSubName = $tipData[subName];
            $tipDate = $tipData[date];
            $tipTime = $tipData[time];
            $tipImage = $tipData[image];
            
            if ($tipImage) {
                
                
            } else {
                $nr++;
                if ($nr>3) $nr = 1;
                $tipImage = "dummys/tt-dummy".$nr.".jpg";                
            }
           
            echo ("<div class='slider_content' style='background:url($tipImage);'>");
            echo ("<div class='slider_info boxlink'>");
            if ($tipDate) {
                echo ("$tipDate");
                if ($tipTime) echo(" - $tipTime");
                echo ("<br />");
            }
            echo ("<b>$tipName</b>");
            if ($tipSubName) echo ("<br />$tipSubName");
            echo ("<div class='hidden_url'><a href='kalender.php?dateId=$tipId'>Link zum Artikel</a></div>");
            echo ("</div>");
            echo ("</div>");
            
            
            
            
        }
         echo ("</div>");
    }

   
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
