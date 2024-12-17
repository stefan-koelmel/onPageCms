<?php

class cmsClass_site_base extends cmsClass_site_session {
    
    function init_site() {
        $this->init_site_session();
        // $this->init_site_page();
    }
    
    function init_site_page() {
        // CheckPage
        page::checkPage();
        
        // aktuelle Seite
        $this->actPage  = page::actPage();
        $this->lastPage = page::lastPage();
        
        
        // echo ("ACTPAGE ='$this->actPage' lastPage='$this->lastPage' <br>");
        
//        // LastPage 
//        $this->lastPage = $this->page_lastPage_get();
//        
        // $this->page_check_List();
        
        // if ($this->cmsName != "game") return 0;
        
        $this->page_history();
        
        
        $this->page_selectPage();
    }
    
    function page_actPage() {
        return page::actPage();        
    }
    
    function page_history() {
        
        $maxHistory = $this->session_get("cmsSettings,history");
        if (!$maxHistory) return 0;
        
        $actPage = $this->actPage;
        
        // echo ("ADD page $actPage to history count = $historyCount <br>");
        
        // $lastPages = $this->session_get("lastPages");
        $lastPages = page::lastPageList_get();
        if (!is_array($lastPages)) $lastPages = array();
        
        // ACT PAGE $actPage is in History
        if ($lastPages[$actPage]) return 0;
        
        // Set ActPage to History
        $lastPages[$actPage] = 1;
        $countHistory = count($lastPages);
        
        if ($maxHistory >= $countHistory) {
            page::lastPageList_set($lastPages);
            // $this->session_set("lastPages",$lastPages);
            return 0;
        }
        
        $removeHistory = $countHistory - $maxHistory;
        // echo ("MOre History as allowed cou = $countHistory / max = $maxHistory rem=$removeHistory <br>");
        $removeList = array();
        foreach ($lastPages as $key => $value) {
            if ($key == $actPage) continue; // Dont Remove ActPage From History
            
            if (count($removeList) < $removeHistory) $removeList[$key] = 1;            
        }
        
        foreach ($removeList as $key => $value) unset($lastPages[$key]);
        
        
        page::lastPageList_set($lastPages);
        // $this->session_set("lastPages",$lastPages);
    }
    
    
    function page_selectPage() {
        
        // echo ("ACT PAGE ='$actPage' LastPage = '$lastPage' <br>");
        if ($this->lastPage == $this->actPage) {
            // echo "NO PAGE CHANGE <br>";
            return 0;
        }
        
        
        // unselect 
        if ($this->lastPage) {
            $backList = $this->page_getBack_byName($this->lastPage);
            // echo ("UNSET $lastPage $backList <br>");
            $backList = explode("|",$backList);
            $keyBase = "pageGroupList";
            $keyAdd = "";
            foreach ($backList as $nr => $backPageName) {
                $keyAdd .= ",".$backPageName;
                $keyStr = $keyBase.$keyAdd.",select";
                $this->session_set($keyStr,null);
                // echo ("unset  KeyStr ='$keyStr' to '0' <br>");
                $keyAdd .= ",subNavi";
            }
        }
        
        // select 
        if ($this->actPage) {
            $backList = $this->page_getBack_byName($this->actPage);
            $backList = explode("|",$backList);
            $backCount = count($backList)-1;

            $keyBase = "pageGroupList";
            $keyAdd = "";

            foreach ($backList as $nr => $backPageName) {
                $setSelect = "subSelect";
                if ($nr == $backCount) $setSelect = "select";            

                $keyAdd .= ",".$backPageName;
                $keyStr = $keyBase.$keyAdd.",select";
                
                $this->session_set($keyStr,$setSelect);
                //$get = $this->session_get($keyBase.$keyAdd.",name");
                //echo ("$get KeyStr ='$keyStr' to '$setSelect' <br>");
                $keyAdd .= ",subNavi";
            }
            // SET ACTPAGE TO LASTPAGE
            $this->page_lastPage_set($this->actPage);       
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // PAGE LISTS                                                             //
    function page_pageList() { return $this->session_get("pageList"); }
    function page_pageIdList() { return $this->session_get("pageIdList"); }
    function page_backList() { return $this->session_get("pageBackList"); }
    function page_groupList() { return $this->session_get("pageGroupList"); }
    ////////////////////////////////////////////////////////////////////////////
    
    
    // PAGE 
    function page_lastPage_get() {
        return page::lastPage();
        return $this->session_get("pageLast");        
    }
    function page_lastPage_set($pageName) {
        return $this->session_set("pageLast",$pageName); 
    }
    
    // PAGE FUNCTIONS                                                         //
    function page_getData_byId($pageId) {
        return page::data_byId($pageId);
        $pageName = $this->page_getName_byId($pageId);
        return $this->page_getData_byName($pageName);
    }
    
    function page_getData_byName($pageName) {
        return page::data_byName($pageName);
        return $this->session_get("pageList,".$pageName);
    }
    
    function page_getName_byId($pageId) {
        return page::data_byId($pageId);
        return $this->session_get("pageIdList,".$pageId);
    }
    
    function page_getBack_byId($pageId) {
        $pageName = $this->page_getName_byId($pageId);
        return $this->page_getBack_byName($pageName);
    }
    
    function page_getBack_byName($pageName) {
        $dynamicPageName = $this->page_dynamic_pageName($pageName);
        if (is_array($dynamicPageName)) {
            $mainName = $dynamicPageName[mainName];
            $addName  = $dynamicPageName[addName];
            
            $backStart = $this->page_getBack_byName($mainName);
            // echo ("MainName = $mainName AddName = '$addName' backStart = '$backStart'  <br>");
            
            $res = $backStart."|".$addName;
            // echo ("Result => '$res' <br>");
            return $res;
            
        }
        return page::backList_getPage($pageName); //$this->session_get("pageBackList,".$pageName);
    }
    
    function page_get_Back($pageId) {
        return $this->session_get("pageBackList,pageId_".$pageId);
    }
    
    function page_get_Name($pageId) {
        return $this->session_get("pageIdList,".$pageId);
    }
    
    
    function page_dynamic_pageName($pageName) {
       
        $delimiterStart = strpos($pageName,"_");
        $sameStart = strpos($pageName,"=");
        if (!$delimiterStart OR !$sameStart) return 0;
       
        
        $start_page = substr($pageName,0,$delimiterStart);
        $end_page = subStr($pageName,$delimiterStart+1); 
        // echo ("is Dynamic start='$start_page' ende = '$end_page' <br>");
        
        $res = array();
        $res[mainName] = $start_page;
        $res[addName] = $pageName;
        return $res;
        
        
    }
    
    function page_getData($idOrName,$useSession=1) {
        return cmsPage_getData($idOrName,$useSession);
    }
    
}

function cmsSite_class() {
    $siteClass = new cmsClass_site_base();
    return $siteClass;
}

function site_session_get($key) {
    global $pageClass;
    if (is_object($pageClass)) {
        // echo ("GET FROM PAGECLASS $key <br>");
        $res = $pageClass->session_get($key);
        return $res;
    }
    return cmsSite_class()->session_get($key);
    
    
}

function site_session_set($key,$value) {
    // echo ("site SESSION set ");
    global $pageClass;
    if (is_object($pageClass)) {
        // echo ("ST FROM PAGECLASS $key <br>");
        $res = $pageClass->session_set($key,$value);
        return $res;
    }
    echo ("GET FROM PAGECLASS $key <br>");
    cmsSite_class()->session_set($key, $value);
}



?>
