<?php

    function search($searchStr) {
        
        $foundLocations = search_locations($searchStr);
        
        search_dates($searchStr);
        search_articles($searchStr);
        
        
        
    }
    
    function search_dates($searchStr) {
        echo ("<h1>Suche in Terminen </h1>");
        $filter = array("search"=>$searchStr,"show"=>1);
        $sort = "date_up";
        $dateList = cmsDates_getList($filter,$sort);
        if (is_array($dateList) AND count($dateList)) {
            include("incs/kalender.php");
            $dontShow = array();
            $dontShow["maxDate"] = "none";
            $dontShow[category] = 1;
            $dontShow[toDate] = 1;
            $dontShow[dateRange] = 1;
            $dontShow[editLink] = 1;
            
            for ($i=0;$i<count($dateList);$i++) {
                $date = $dateList[$i];
                $dateId = $date[id];
                $dateName = $date[name];
                $dateDate = $date[date];
                
                $dateCategory = $date[category];
                if ($dateCategory == 327 OR $dateCategory == 330) {
                    $dontShow[date] = 1;
                    $dontShow[dateRange] = 0;
                } else {
                    $dontShow[date] = 0;
                    $dontShow[dateRange] = 1;
                }
                    
                
                echo ("Termin $dateId $dateDate $dateName $date <br>");
                $out =  date_showSmall_str($date, $dontShow);
                echo ($out);
                
            }
        }                        
    }
    
    
    function search_articles($searchStr) {
        echo ("<h1>Suche in Artikeln </h1>");
        $filter = array("search"=>$searchStr,"show"=>1);
        $sort = "dateRange_up";
        $articleList = cmsArticles_getList($filter,$sort);
        if (is_array($articleList) AND count($articleList)) {
            include("incs/articles.php");
            for ($i=0;$i<count($articleList);$i++) {
                $article = $articleList[$i];
                $articleId = $article[id];
                $articleName = $article[name];
                $articleDateRange = $article[dateRange];
                $outStr = article_showInfo_str($article);
               
                echo ("Artikel $articleId $articleDateRange $articleName <br>");
                echo ($outStr);
            }
        }          
    }

    function search_locations($searchStr) {
        echo ("<h1>Suche in Adressen</h1>");
        $filter = array("search"=>$searchStr,"show"=>1);
        $sort = "name";
        $locationList = cmsLocation_getList($filter,$sort);
        if (is_array($locationList) AND count($locationList)) {
            for ($i=0;$i<count($locationList);$i++) {
                $article = $locationList[$i];
                $articleId = $article[id];
                $articleName = $article[name];
                $articleDateRange = $article[dateRange];
                echo ("Termin $article $articleDateRange $articleName <br>");
            }
        }          
    }
    
    
?>
