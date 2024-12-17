<?php
    session_start();
//    echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n");
//    echo ("<html xmlns='http://www.w3.org/1999/xhtml' lang='de' xml:lang='de' >\n");
//    
//   
//    
    
   
    global $cmsName,$cmsVersion;
    if (file_exists("cmsSettings.php")) include("cmsSettings.php");
    include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms.php");
   
    $out = "";  
    $request = $_SERVER["REQUEST_URI"];
    $request = urldecode($request);
    // $request = str_replace("_"," ", $request);
    $out .= "Request = $request <br />";
    $split = explode("/",$request);
    
    
    $path = "";
    if (count($split)>2) {
        for($i=1;$i<count($split)-1;$i++) {
            $path.=$split[$i]."/";
        }
        $searchAfter = $split[count($split)];
    } else {
        $searchAfter = $split[count($split)];
    
    }
    
    if ($path) {
        $out .= "<h1>Path '$path' </h1>";
    }
    
    
    for($i=1;$i<count($split);$i++) {
        // echo ("$i = '$split[$i]'<br />");    
        $searchAfter = $split[$i];
    }
    
    
    $queryString = $_SERVER[QUERY_STRING];
    $queryString = urldecode($queryString);

    $queryPos = strpos($searchAfter,"?");
    if ($queryPos) {
        $queryString .= "&".substr($searchAfter,$queryPos+1);
        $searchAfter = substr($searchAfter,0,$queryPos);
        $out .= "query = $searchAfter quer = $queryString <br />";
        
    }
    
    $queryPos = strpos($searchAfter,".php");
    if ($queryPos) {
        // $queryString .= "&".substr($searchAfter,$queryPos+1);
        $searchAfter = substr($searchAfter,0,$queryPos);
        $out .= "query = $searchAfter quer = $queryString <br />";
        
    }
    
   
    // make ParameterList
    $queryString = str_replace("?","&",$queryString);
    $queryList = explode("&",$dataString);
    $parameter = array();
    for ($i=0;$i<count($queryList);$i++) {
        $data = explode("=",$queryList[$i]);
        if (count($data)==2) {
            $parameter[$data[0]] = $data[1];
            $out .= "add Query $data[0] = $data[1] <br />";
        }
    }
   //  echo ($out);
    
    
    $found = array();
    $found["name"] = array();
    $found["title"] = array();
    $out .= "Search After $searchAfter <br />";
    $pageList = $_SESSION[pageList];
    if (!is_array($pageList)) {
        $out .= "no PageList exist <br />";   
        $pageList = cms_page_getSortList();
    } 
    
    
    
    $root = $_SERVER["DOCUMENT_ROOT"]."/";
    
    if (is_array($pageList)) {
        
        if ($pageList[$searchAfter]) {
            
            $pageName = $searchAfter;
            $data = $pageList[$pageName];
            $fn = $pageName.".php";
            if ($path) {
                $fn = "http://".$_SERVER[HTTP_HOST]."/".$fn;
                reloadPage($fn,0);
                die();
            }
            
            showPage($pageName,$data,$root,$fn,$parameter,$path);
            die();         
        }
        
        
        $out .= "PageList exist <br />";
        
        foreach ($_SESSION[pageList] as $pageName => $pageData) {
            $pageTitle = $pageData[title];
            $pageName  = $pageData[name];
            $pageid    = $pageData[id];
            if ($searchAfter == $pageTitle ) {
                $found["title"][] = $pageName;
            }            
        }
    } 
       
    
    
    
    
    foreach ($found as $foundIn => $foundList) {
        $anz = count($foundList);
        if (count($foundList)) {
            for ($i=0;$i<count($foundList);$i++) {
                $pageName = $foundList[$i];
                $out .= "Gefunden in $foundIn / $i = $foundList[$i] <br>";
                
                $fn = $pageName.".php";
                $out .= "Gefunden in $foundIn / $i = $foundList[$i] --> $root $fn <br>";
                if (file_exists($root.$fn)) {
                    $data = $pageList[$pageName];
                    $fn = $pageName.".php";
                    if ($path) {
                        $fn = "http://".$_SERVER[HTTP_HOST]."/".$fn;
                        reloadPage($fn,0);
                        die();
                    }
                    
                    showPage($pageName,$data,$root,$fn,$parameter,$path);
                    die();         
                }
            }
        }
    }
    echo ($out);
    
    function showPage($pageName,$data,$root,$fn,$parameter=array(),$path="") {
       global $cmsName,$cmsVersion;
//        if (file_exists($root."cmsSettings.php")) include($root."cmsSettings.php");
        // include($root."cms_".$cmsVersion."/cms.php");

        
        global $pageInfo;
        $pageInfo = array();
        $pageInfo[host] = $_SERVER["HTTP_HOST"];
        $pageInfo[cmsName] = $cmsName;
        $pageInfo[page] = $fn;
        $pageInfo[pageName] = $pageName;
//                    pageInfo = pageType =>
        $pageInfo["path"] = $path;
        $pageInfo["parameter"] = $parameter;
        $pageInfo["lg"] = $_SESSION[lg];
                    
    

        global $pageData;
        $pageData = $data;
        $pageName = $pageName;

        
        // show Header
        cms_header_show($pageData,$pageInfo);
        echo ("<body>\n");
        cms_page_show();
        echo ("</body>");
        echo ("</html>");
    }
?>
