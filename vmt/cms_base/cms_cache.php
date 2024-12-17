<?php

function cmsCache_enable() {
    $_SESSION[cache] = 1;
}

function cmsCache_disable() {
    $_SESSION[cache] = 0;
}

function cmsCache_state() {
    // foreach ($_SESSION[cmsSettings] as $key => $value ) echo ("$key => $value <br>");
    return $_SESSION[cache];
}


function cmsCache_root() {
    $cmsRoot = $_SESSION[cmsRoot];
    
    if (is_int($cmsRoot)) return $cmsRoot;
    $cmsName = $GLOBALS[cmsName];
    $root = $_SERVER[DOCUMENT_ROOT];
    if (file_exists($root."/$cmsName/cache")) {
        $cmsRoot = 1;
    } else { 
        $cmsRoot = 0;
    }
    if ($_SESSION[userLevel] >= 9) echo ("Detect CMSCache Root = $cmsRoot <br>");
    
    $_SESSION[cmsRoot] = $cmsRoot;
    return $cmsRoot;
}

function cmsCache_subPathAdd($page) {
    $add = 0;
    switch ($page) {
        case "ausgabe.php" :
            $add = "ausgabe/";
            break;
        case "kalender.php" :
            $add = "kalender/";
            break;
        case "archive.php" :
            $add = "archive/";
            break;
        case "adressen.php" :
            $add = "adressen/";
            break;
        default :
            if (substr($page,0,7) == "archive") {
                $subPath = substr($page,8);
                if (strlen($subPath)==7) {
                    $add = $page."/";
                }
                // echo ("subpath = $subPath<br>");
            }

    }
    return $add;
}

function cmsCache_getPath($page="") {
    $cmsCachePath = $_SESSION[cmsCachePath];
    
    if (is_string($cmsCachePath) AND strlen($cmsCachePath)) {
        $addPath = cmsCache_subPathAdd($page);
        if ($addPath) $cmsCachePath .= $addPath;                
        return $cmsCachePath;
    }
    
    
    $cmsRoot = cmsCache_root();
    $cmsName = $GLOBALS[cmsName];
    $root = $_SERVER[DOCUMENT_ROOT];
    if ($cmsRoot == 1) {
        $cmsCachePath = $root."/$cmsName/cache/";
    } else {
        $cmsCachePath = $root."/cache/";        
    }
    
    if ($_SESSION[userLevel] >= 9) echo ("Detect CMSCache Path = '$cmsCachePath' <br>");
    $_SESSION[cmsCachePath] = $cmsCachePath;
    
    $addPath = cmsCache_subPathAdd($page);
    if ($addPath) $cmsCachePath .= $addPath;  
    return $cmsCachePath;
}

function cmsCache_replaceStr_get() {
    $cmsRoot = cmsCache_root();
    if ($cmsRoot) {
        $replace = array("src='/images/","src='/".$GLOBALS[cmsName]."/images/");
    } else {
        // $replace = array("src='/".$GLOBALS[cmsName]."/images/","src='/images/");
    }
    return $replace;
}


function cmsCache_replaceStr_save() {
    $cmsRoot = cmsCache_root();
    if ($cmsRoot) {
        $replace = array("src='/".$GLOBALS[cmsName]."/images/","src='/images/");
        // $replace = array("src='/images/","src='/".$GLOBALS[cmsName]."/images/");
    } else {
        // $replace = array("src='/".$GLOBALS[cmsName]."/images/","src='/images/");
    }
    return $replace;
}


function cmsCache_get($page,$filter,$sort) {
    $useCache = cmsCache_state();
    if (!$useCache) {
        
        $infoRes = cmsCache_showInfo($page, $filter, $sort,$fn);
        if ($infoRes == "reload") return "<strong> RELOAD </strong>";
        
        return 0;
    }  
    
    $fn = cmsCache_getFileName($page,$filter,$sort);
    $path = cmsCache_getPath();
    
    $infoRes = cmsCache_showInfo($page, $filter, $sort,$fn);
    switch ($infoRes) {
        case "dontUse" : 
            echo ("<strong>GENERATE AND RELOAD! $infoRes</strong><br>");
            return 0;
            break;
        
        case "reload" : 
            // echo ("<strong> RELOAD </strong><br>");
            return "<strong> RELOAD </strong>";
          
        
    }
   
    if (!file_exists($path.$fn)) return 0;
    
    // Cache File Exist
    $out = loadText($path.$fn);
    
    $replace = cmsCache_replaceStr_get();
    if (is_array($replace)) {
        // echo ("Replace GET $replace[0] -> $replace[1] <br>");
        $out = str_replace($replace[0],$replace[1],$out);
    }
    return $out;    
}


function cmsCache_save($page,$filter,$sort,$out) {
    // echo ("cmsCache_save($page,$filter,$sort<br>");
    $useCache = cmsCache_state();
    if (!$useCache) return 0;
    
    
    $fn = cmsCache_getFileName($page,$filter,$sort);
    
    $path = cmsCache_getPath();
       
    // echo ("Save CacheFile $fn nach <br>'$path' <br>");
    
    $replace = cmsCache_replaceStr_save();
    if (is_array($replace)) {
        //  echo ("Replace SAVE $replace[0] -> $replace[1] <br>");
        $out = str_replace($replace[0],$replace[1],$out);
    }
    saveText($out,$path.$fn);       
}

function cmsCache_deleteFile($fn) {
    return "dontUse";
}
function cmsCache_deletePage($page) {
    if (substr($page,strlen($page)-4) == ".php") $page = substr($page,0,strlen($page)-4);
    echo ("cmsCache_deletePage($page)<br>");
    $delStart = $page.".";
    $path = cmsCache_getPath();
    $handle = opendir($path);
    $delList = array();
    while ($file = readdir ($handle)) {
        if(is_dir($folder."/".$file)) {
            if($file != "." && $file != "..") {
                // echo ("CahceFolder Hää?? <br>");
            }
        } else {
            $fileName = $folder.$file;
            $compareName = substr($file,0,strlen($delStart));
            if ($compareName == $delStart) {
                $delList[] = $file;

            }
        }   
    }
    closedir($handle);
    
    $infoText = "Cache wurde geleert";
    
    for ($i=0;$i<count($delList);$i++) {
        
        $fn = $delList[$i];
        if ($_SESSION[userLevel]==9) $infoText .= "<br>$fn";
        
        unlink($path.$fn);
    }
    
    if ($_SESSION[userLevel]==9) $infoText .= "&nbsp;<br>";
   
    $infoText .= "<strong> ".count($delList)." Dateien wurden gelöscht</strong><br/>";
    
    
    echo ("$infoText<br />");
    return "reload";
    
         
}

function cmsCache_deleteId($page,$id,$shortList) {
    $cachePath = cmsCache_getPath($page);
    $cacheFile = cmsCache_getFileName($page, $id,"");
    // echo ("cmsCache_deleteId $page $cachePath $cacheFile <br>");
    if (is_array($shortList)) {
        for ($i=0;$i<count($shortList);$i++) {
            $cacheFile = cmsCache_getFileName($page, $id,$shortList[$i]);
            // echo ("Delete $cachePath $cacheFile <br>");
            if (file_exists($cachePath.$cacheFile)) {
                if ($_SESSION[showLevel] >= 9) echo ("$cacheFile delete <br>");
                unlink($cachePath.$cacheFile);
            }             
        }
    }
    
}

function cmsCache_disableCache() {
    cmsCache_disable();
    return "reload";
}
    
function cmsCache_enableCache() {
    cmsCache_enable();
    return "reload";
}        
     


function cmsCache_showInfo($page,$filter,$sort,$fn) {
    
    if ($_SESSION[userLevel] < 9) return $returnRes;
    
    
    $cmsState = cmsCache_state();
    
    
    div_start("cacheInfo","background-color:#ccc;padding:5px;min-height:60px;border:1px solid #555;width:100%;");
    
   
    if ($cmsState) {
        $path = cmsCache_getPath();
        $exist = file_exists($path.$fn);
    
        if ($exist) echo ("<strong>Cache File exist</strong><br>");
        else echo ("Cache File existiert nicht nicht</strong><br>");
    } else {
        echo ("<strong>Cache ist ausgeschaltet  !</strong><br>");
    }    
    
    
    
    $link = "";
    foreach ($_GET as $key => $value) {
        switch ($key) {
            case "dontCache" : 
                $res = cmsCache_deleteFile();
                echo ("Dont Cache Result = '$res' <br>");
                if ($res == "dontUse") $returnRes = $res;
                break;
                
            case "delCache" : 
                $res = cmsCache_deletePage($value);
                echo ("Dont Cache Result = '$res' <br>");            
                if ($res == "reload") $returnRes = $res;
                   
                break;
            case "disableCache" : 
                $res = cmsCache_disableCache();
                if ($res == "reload") $returnRes = $res;
                break;
                
            case "enableCache" : 
                $res = cmsCache_enableCache();
                if ($res == "reload") $returnRes = $res;
                break;
                
            
            default :
                if ($link) $link .= "&";
                else $link .= "?";
                $link .= $key."=".$value;
        }
        
    }

    if ($link) $addLink = "&";
    else $addLink = "?";
    $link = $page.$link;
    
    if ($returnRes == "reload") {
        echo ("Reload '$link' - '$returnRes'<br>");
        reloadPage($link,2);        
    }
    
    if ($cmsState) {
    
        if ($returnRes != "reload") {
            echo ("<a href='".$link.$addLink."dontCache=1' >Diese Ansicht neu erstellen</a><br />");
            
            echo ("<a href='".$link.$addLink."delCache=$page' >Alle Ansichten für $page neu erstellen</a><br />");
            
            echo ("<a href='".$link.$addLink."disableCache=1' >Cache ausschalten</a><br />");
        }
    } else {
        echo ("<a href='".$link.$addLink."enableCache=1' >Cache einschalten</a><br />");
    }
     div_end("cacheInfo"); 
        
     return $returnRes;  
      
}

function cmsCache_getFileName($page,$filter,$sort) {
    if (substr($page,strlen($page)-4) == ".php") $page = substr($page,0,strlen($page)-4);
    
    $offSlash = strpos($page,"/");
    if ($offSlash) {
        $page = substr($page,0,$offSlash);
        // echo (" /// $page <br>");
    }
    
    // get Filter String
    $filterStr = "";
    if (is_array($filter)) {
        foreach ($filter as $key => $value ) {
            if ($filterStr) $filterStr.= "|";
            $filterStr .= $key."_".$value;
        }
    } else {
        if (strlen($filter)>0) {
            $fn = $page."_".$filter;
            if ($sort) $fn .= "_".$sort;
            $fn .= ".cache";
            return $fn;
        }
    }
    
    // get Sort String
    $sortStr = $sort;
    
    $fn = $page;
    $useMd5 = 1;
    if ($useMd5) {
        $md5Str = $filterStr;
        if ($sortStr) $md5Str .= "#".$sortStr;
        if ($md5Str) {
            $md5Str = md5($md5Str);
            // echo ("$md5Str <br>");
            $fn .= ".".$md5Str;
        } else {
            $fn .= ".all";
        }
        // $fn .= ".".md5($filterStr)
    } else {
        if ($filterStr) $fn .= ".".$filterStr;
        if ($sortStr) $fn .= "#".$sortStr;
    }
    
    
    
    $fn .= ".cache";
    // echo ("<h3>$fn</h3>");
    return $fn;
}
?>
