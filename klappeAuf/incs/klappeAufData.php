<?php
    function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    function timer_start() {
        $GLOBALS[startTime] = microtime_float();
        $GLOBALS[startTimeList] = array();
    }
    
    function timer_add($name) {
        $GLOBALS[startTimeList][$name] = microtime_float(); // - $GLOBALS[startTime];
    }
    
    function timer_show() {
        $startTime = $GLOBALS[startTime];
        $lastTime = $startTime;
        foreach ($GLOBALS[startTimeList] as $name => $timeStamp) {
            echo ("$name insgesamt=".number_format($timeStamp-$startTime,4,",",".")." diff=".  number_format($timeStamp-$lastTime,4,".",".")."<br>");
            $lastTime = $timeStamp;
        }
    }
    
    function externalLinkTarget($link="") {
        // return "_blank";        
        return "klappeAuf_extern";
    }
   
    function external_link_get($url) {
        //echo ("external_link_get($url)<br>");
        $url = str_replace(array("/r"," "),"",$url);
        $target = externalLinkTarget();
        $res = array();
        $linkList = explode("*",$url);
        for ($i=0;$i<count($linkList);$i++) {
            $linkStr = $linkList[$i];
            
            if ($linkStr) {
                $linkData = explode("#",$linkStr);
                
                
                $url = php_checkUrl($linkData[0]);
                // echo ("get Url='$url'<br>");
                if ($url) {
                    $linkName = $linkData[1];
                    $linkTarget = $linkData[2];
                    if (!$linkTarget) $linkTarget = $target;
                    $res[] = array("url"=>$url,"name"=>$linkName,"target"=>$linkTarget);
                }
            }
                
        }
        return $res;
        
    }
    
    
    function mysql_status() {
        echo ("<h1>OPEN TABLES</h1>");

        $res = mysql_query("SHOW OPEN TABLES");
        if ($res) {
            while ($data =  mysql_fetch_assoc($res)) {#
                echo ("$data[Database] - $data[Table] - $data[In_use] - $data[Name_locked] <br>");
            }
        }

        echo ("<h1>Status</h1>");
        $status = explode('  ', mysql_stat());

        for($i=0;$i<count($status);$i++) {
            echo ($status[$i]."<br>");
        }
    }


    function categoryGetList($name) {
        $catList = $_SESSION[$name];
        if (is_array($catList)) {
            return $catList;
        }
        
        switch ($name) {
            case "RubrikList" :
                $catList = cmsCategory_getList(array("mainCat"=>144,"show"=>1),"id","assoIdList");
                $catList = setShortName($catList);
                $_SESSION[$name] = $catList;
                break;
            
            case "AdressList" :
                $catList = cmsCategory_getList(array("mainCat"=>8,"show"=>1),"name","assoIdList");
                $catList = setShortName($catList);
                $_SESSION[$name] = $catList;
                break;
            
            case "RegionList" :
                $catList = cmsCategory_getList(array("mainCat"=>180,"show"=>1),"id","assoIdList");
                $catList = setShortName($catList);
                $_SESSION[$name] = $catList;
                break;
            
            case "TerminCategoryList" :
                $catList = cmsCategory_getList(array("mainCat"=>1,"show"=>1),"id","assoIdList");
                $catList = setShortName($catList);
                $_SESSION[$name] = $catList;
                break;
    
            
            default :
                if (substr($name,0,14) == "RubrikSubList_") {
                    $catId = substr($name,14);
                    $catList = cmsCategory_getList(array("mainCat"=>$catId,"show"=>1),"id","assoIdList");
                    $catList = setShortName($catList);
                    $_SESSION[$name] = $catList;
                    break;
                    
                } else {
                    echo("unkown ShortName $name in categoryGetList <br>");
                }
        }
        return $catList;
    }
        
      
        
    function setShortName($catList) {
        if (is_array($catList) AND count($catList)) {
            foreach ($catList as $catId => $catValue) {
                $catName = $catValue[name];
                $shortName = getShortName($catName);
                $catList[$catId][shortName] = $shortName;
            }
        }
        return $catList;
    }
        

    function getShortName($catName) {
        $shortName = str_replace(array(","," ","/","-","_"),"_", $catName);
        $shortEnd = strpos($shortName,"_");
        if ($shortEnd) $shortName = subStr($catName,0,$shortEnd);
        $shortName = strtolower($shortName);
        return $shortName;
    }
  
?>
