<?php
    global $cmsName,$cmsVersion;
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    // echo ("cmsVersiom = $cmsVersion cmsName=$cmsName <br />");
    include("cms/cms_connect.php");
    $cmsPath = $_SERVER["DOCUMENT_ROOT"]."/cms_".$cmsVersion."/";
    $dataPath = $cmsPath."data/";
    $path = $_SERVER["DOCUMENT_ROOT"]."/cms_".$cmsVersion."/data/";
    
    $files = array();
    $files[article] = array("path"=>$dataPath,"file"=>"cms_articles.php");
    $files[help] = array("path"=>$cmsPath,"file"=>"help.php");
    foreach ($files as $nr => $fileData) {
        $path = $fileData["path"];
        $file = $fileData["file"];
        
        if (file_exists($path.$file)) {
            include($path.$file);
        } else {
            echo ("FILE $path $file not exist<br>");
            die();
        }
    }
  
    $filter = array();
    $filter[show] = 1;
    $filter[date] = date("Y-m-d");
    $sort = "fromDate_up";
    $articles = cmsArticles_getList($filter, $sort);
    
    $rss = "";
    $rss .= "<?xml version='1.0' encoding='UTF-8' ?>\n";
    $rss .= "<rss version='2.0' >\n";
    $rss .= "   <channel >\n";
    $rss .= "       <title>onPage CMS</title>\n";
    $rss .= "       <description>Neuigkeiten vom onPage CMS</description>\n"; 
    $rss .= "       <link>http://cms.stefan-koelmel.com/onPage/rss.php</link>\n";
    $rss .= "       <language>de-de</language>\n";
    $rss .= "       <copyright>Stefan Kölmel</copyright>\n";
    $rss .= "       <image>\n";
    $rss .= "           <url>http://intern.stefan-koelmel.com/logo.png</url>\n";
    // $rss .= "           <link>http://intern.stefan-koelmel.com/index.rss</link>\n";
    $rss .= "       </image>\n";
    
    $show = 0;
    $showImage = 1;
    $actDate = date("r");
    $rss.= "<lastBuildDate>$actDate</lastBuildDate>";
    
    if (is_array($articles)) {
        foreach ($articles as $nr => $article ) {
            
            $name = $article[name];
            $info = $article[info];
            $image = $article[image];
            $id = $article[id];
            if (!$info) $info = $article[longInfo];
            $url = $article[url];
            if (!$url ) $url = "http://cms.stefan-koelmel.de/onPage/news";
            $url = "http://cms.stefan-koelmel.com/onPage/news.php?article=".$id;
            $date = $article[lastMod];
            if ($date) {
                list($datum,$zeit) = explode(" ",$date);
                list($year,$month,$day) = explode("-",$datum);
                list($hour,$minute,$second) = explode(":",$zeit);
                
                // echo ("$datum $zeit <br>");
                //mktime($show, $minute, $second, $month, $day, $year)
                $myDate = mktime($hour,$minute,0,$month,$day,$year);
                $dateStr = date("r",$myDate);
                //echo (date("D, d M Y H:i:s O",$myDate)."<br>");
                // echo (date("r",$myDate)."<br>");
            }
                
            
                
            if ($show) {
                echo ("Name : $name <br>");
                echo ("info : $info <br>");
                echo ("url : $url <br>");
                echo ("image : $image <br>");
                echo ("date = $date '$dateStr'<br>");
            }
            
            $rss .="<item>\n";
            $rss .= "   <title>$name</title>\n";
            $rss .= "   <link>$url</link>\n";
            
            if ($showImage) { 
                $infoAdd = "<![CDATA[<p><img src='http://intern.stefan-koelmel.com/logo.png' style='vertical-align:top;' />";
                $infoAdd .= "kjh kjhld sfkshdlkj hvcxmnv mcvnb mcxvbnx,cmvbn ,cmvnb,mcn vb,mncvx.b,n xc.,vbn xc,vbn xc";
                $infoAdd .= "kjh kjhld sfkshdlkj hvcxmnv mcvnb mcxvbnx,cmvbn ,cmvnb,mcn vb,mncvx.b,n xc.,vbn xc,vbn xc";
                $infoAdd .= "kjh kjhld sfkshdlkj hvcxmnv mcvnb mcxvbnx,cmvbn ,cmvnb,mcn vb,mncvx.b,n xc.,vbn xc,vbn xc";
                $infoAdd .= "kjh kjhld sfkshdlkj hvcxmnv mcvnb mcxvbnx,cmvbn ,cmvnb,mcn vb,mncvx.b,n xc.,vbn xc,vbn xc";
                $infoAdd .= "kjh kjhld sfkshdlkj hvcxmnv mcvnb mcxvbnx,cmvbn ,cmvnb,mcn vb,mncvx.b,n xc.,vbn xc,vbn xc";
                
                //'http://www.designmadeingermany.de/2013/wp-content/uploads/2013/11/5283824a23190.png" class="attachment-thumb wp-post-image" alt="sdfsdf" />"
                $info = $infoAdd."<br />".$info."</p>]]>";
            }
            
            
            
            $rss .= "   <description>".$info."</description>\n";
//            $rss .= "   <image>\n";
//            $rss .= "       <url>http://intern.stefan-koelmel.com/logo.png</url>\n";
//            $rss .= "   </image>\n";
            
            $rss .= "<pubDate>$dateStr</pubDate>\n";
            $rss .= "</item>\n";
            // echo ("ARTIKEL $nr = $article <br>");
            // foreach ($article as $key => $value) echo ("$key = $value <br>");
            
        }
    }
    
    $actDate = date("r");
    
    $rss .= "    <pubDate>".$actDate."</pubDate>\n";
    $rss .= "</channel>\n";
    $rss .= "</rss>\n";
    
    echo ($rss);
    
    
    /*
     * <?xml version='1.0' encoding='UTF-8' ?>
<rss version='2.0' >
    <channel > 
        <link>http://intern.stefan-koelmel.com/index.rss</link>
        <description>My RSS FEED</description>
        <language>de-de</language>
        <copyright>Stefan Kölmel</copyright>
        <image>
            <title>Stefan Kölmel</title>
            <url>http://intern.stefan-koelmel.com/logo.png</url>
            <link>http://intern.stefan-koelmel.com/index.rss</link>
        </image>
        
        <item>
            <title>Eigenen RSS-Feed erstellen</title>
            <link>http://intern.stefan-koelmel.com/index.php?level=1</link>
            <description>So erstellen Sie Ihren eigenen RSS-Feed</description>
        </item>

        <item>
            <title>Stefan Kölmel.de</title>
            <link>http://stefan-koelmel.de</link>
            <description>So sieht meine Webseite aus</description>
        </item>

        <item>
            <title>Stefan Kölmel.com</title>
            <link>http://stefan-koelmel.com</link>
            <description>So sieht meine COM Webseite aus</description>
        </item>
        <pubDate>Mon, 17 Jul 2009 15:21:36 GMT</pubDate>
    </channel>
</rss>

     */
    
    
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
