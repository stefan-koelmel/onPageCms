
 <?php 
    function sidebar_show($date=null) {

        echo ("<a href='kalender.php' class='go_calender'><span class='quo'>&rsaquo;</span> Zum Veranstaltungskalender</a>");
             echo ("<div class='content_box'>");
                echo ("<div class='content_box_inner'>");
                    include("incs/calendar-left.php");
                    mainCalendar_show();
                echo ("</div>");
             echo ("</div>");
          
             echo ("<p></p>");
              
                   
                //  Show Abgesagte Termine 
            cancelDates($date);

            // Zeige prTexte
            prText($date);

            // Zeige Werbung
            advertise($date);
            
            
           
            
            if ($_SESSION[userLevel] > 5) {
                if (strpos($_SERVER[SCRIPT_NAME],"index.php")) {
                    echo ("<div class='content_box' style='background-color:#cc0;'>");
                    echo ("<div class='content_box_inner'>");
                    if ($date) $dateStr = cmsDate_getDayString($date,0);
                    echo ("<h3>Startseite $dateStr</h3>");
                    mainCalendar_showAdmin($date);
                    echo ("</div>");
                    echo ("</div>");

                    echo ("<p></p>");
                }
            }


    }                
                   
    function cancelDates($date=null) {
        // echo ("<h1>CancelDates</h1>");
        $cancelDatesList = cmsDates_getList(array("cancel"=>1,"show"=>1,"fromDate"=>date("Y-m-d")),"date");
        // show_array($cancelDatesList);

        $outDates = "";
        if (is_array($cancelDatesList)) {
            for ($i=0;$i<count($cancelDatesList);$i++) {
                $dateData = $cancelDatesList[$i];
                $outDates .= "<strong>Termin abgesagt</strong><br />";
                $outDates .= cmsDate_getDayString($dateData[date],1)."<br />";
                if ($dateData[name]) $outDates .= $dateData[name]."<br />";
                if ($dateData[subName]) $outDates .= $dateData[subName]."<br />";
                if ($dateData[info]) $outDates .= $dateData[info]."<br />";
            }
        }


        $out = $outDates;

        if ($out) {
            echo ("<div class='content_box'>  ");
            echo ("<div class='content_box_inner f_thirteen'>");
                
            echo ("<span class='sidebar_header'>Ver&auml;nderungen</span>");

            echo ($outDates);
            echo ("</div>");
            echo ("</div>");


            echo ("<p></p>");
        }

    }
    
    
    function prText($date=null) {
        $sort = "highlight_up";
        $out = "out__";
        if (!$date) $date = date("Y-m-d");
        $filter = array("category"=>332,"show"=>1,"date"=>$date);
        $articleList = cmsArticles_getList($filter, $sort, $out);
        if (!is_array($articleList)) return "";
        if (count($articleList)==0) return "";
        
       
        
        echo ("<div class='content_box'>\n"); 
        echo ("       <div class='content_box_inner f_thirteen'>\n"); 
        echo ("         <span class='sidebar_header'>Meldungen</span>\n"); 
         
         
        $imageSize = 70;
        $showData = array();
        $showData[frameWidth] = $imageSize;
        $showData[vAlign] = "none";
        $showData[hAlign] = "left";
         
        $maxChars = 250; 
         
        for ($i=0;$i<count($articleList);$i++) {
            $chars = $maxChars;
            $article = $articleList[$i];
            $articleId = $article[id];
            $articleName = $article[name];
            $articleText = $article[info];

            $imageStr = "";
            $image = $article[image];
            if (intval($image)) $image = "|$image|";
            $imageList = explode("|",$image);
            if (count($imageList)>=3) {
                $imageId = $imageList[1];
                $imageData = cmsImage_getData_by_Id($imageId);
                if (is_array($imageData)) {
                    // $imageData[ratio] 
                    $imageStr = cmsImage_showImage($imageData, $imageSize, $showData);
                }
            }
                
            $className = "content_meldungen boxlink";
            if ($i==count($articleList)-1) $className .= " meldung_last";
            echo ("           <div class='$className'>\n"); 
            if ($imageStr) {
                echo ($imageStr);
                $chars = $chars - 50;
            }
            if ($articleName) {
                echo ("             <strong>$articleName</strong><br />\n"); 
                $chars = $chars - floor(strlen($articleName)*1.2);
            }
            if ($chars < 50) $chars = 50;
            
            if (strlen($articleText) <= $chars) {
                echo ($articleText);                
            } else {
                $offSet = strpos($articleText," ",$chars);
                echo (substr($articleText,0,$offSet)." ...");
            }
            
            
            // echo ("             $articleText"); 
            echo ("             <div class='hidden_url'><a href='ausgabe.php?articleId=$articleId'>Link zum Artikel</a></div>\n"); 
            echo ("           <div class='clearleft'></div>\n"); 
            echo ("           </div>\n"); 
                
                
         }
         
       
         echo ("       </div>\n"); 
         echo ("     </div>\n"); 
    }
    
    function advertise($date=null) {
        return 0;
        echo ("<p></p>\n"); 
        echo ("      <div class='content_box'>\n"); 
        echo ("        <div class='content_box_inner'>\n"); 
        echo ("          WERBUNG ?\n"); 
        echo ("        </div>\n"); 
        echo ("      </div>\n"); 
    }