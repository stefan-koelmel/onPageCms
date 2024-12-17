<?php

function articleTip_show($showDate) {
    $filter = array();
    $filter[show] = 1; // Artikel ist sichtbar
    $filter[date] = date("Y-m-d"); // Sichtbarer Zeitraum
    if ($showDate) $filter[date] = $showDate;
    $filter[image] = "!=''"; // Artikel hat Bild
    $filter[highlight] = ">='5'"; // Priorität ist sehr hoch
    $filter["new"] = 1; // Startseite ausgewählt
    $filter[category] = "!=319";  // nicht Meldungen
    
    $sort = "fromDate";
    
    $imageSize = 200;
    
    
    $articleList = cmsArticles_getList($filter, $sort,"out_");
    if (count($articleList)) {
        $anz = count($articleList);
        if ($anz > 1) { //mehr als ein Artikel
            $articleNr = rand(0,count($articleList)-1);
        } else {
            $articleNr = 0;
        }
        $article = $articleList[$articleNr];
        
        $articleId = $article[id];
        $name    = $article[name];
        $subName = $article[subName];
        $info    = str_replace("\r", "<br />", $article[info]);    
        $image   = $article[image];
        
        
        if (intval($image)) $image = "|".$image."|";
        $imageIdList = explode("|",$image);
        $imageNr = rand(1,count($imageIdList)-2);
        $imageId = $imageIdList[$imageNr];
        
        //echo ("$imageId <br>");
        $imageData = cmsImage_getData_by_Id($imageId);
        
        
        $goLink = "ausgabe.php?articleId=$articleId";
        $goLink = php_clearLink($goLink);
        
        echo ("<div class='home_highlight'>");
        $maxLength = 200;
    
        if (is_array($imageData)) {
            $showData = array();
            $showData[frameWidth] = $imageSize;
            $showData[frameHeight] = $imageSize;
            $showData[vAlign] = "none";
            $showData[hAlign] ="none";
            $imageStr = cmsImage_showImage($imageData, $imageSize,$showData);
            
            echo ("<div class='home_highlight_img hh_img_wide'>");
            
            echo ("<a href='$goLink'>");
            echo ("$imageStr");
            echo ("</a>");
            
            echo ("</div>");
            
        }
        
        echo ("<div class='home_highlight_content'>");
        if ($name) {
            echo ("<strong>$name</strong><br />");
            $maxLength = $maxLength - floor(strlen($name) * 1.2);
        }
        if ($subName) {
            echo ("$subName <br />");
            $maxLength = $maxLength - strlen($name);
        }
        // echo ($maxLength."-".strlen($info)."<br>");
        if (strlen($info)>$maxLength) {
            // echo ("jhe<br>");
            $lastChar = strpos($info," ",$maxLength);
            // echo ("$lastChar / $maxLength");
            echo (substr($info,0,$lastChar+1)."...");
        } else {
            echo ($info);
        }
        echo ("</div>");
        
        echo("<div class='hh_readmore'><a href='$goLink' class='hh_readmore_link'><span class='quo'>&rsaquo;</span> weiter lesen</a></div>");
        echo ("</div>");     
        
    
    }
    
    
    
 
                                     
}
                    
?>
