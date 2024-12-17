<?php

class page {
    //put your code here
    
    
    static public $actPage;
    static public $lastPage;
    static public $trulla;
    
    
    
    static public function pageCode() {
        
    }
    
    static public function checkPage() {
        
        $pageList = self::pageList();
        $pageIdList = self::idList();
        $pageBackList = self::backList();
        $pageGroupList = self::groupList();
        
        $debug = 0;
        // if ($this->cmsName == "game") $debug = 1;
        
        if (!is_array($pageList)) {
            if ($debug) echo ("<h2>Create pageList </h2>");
            $pageList = cmsPage_getAllPages();
            
            // create LayoutList
            $layoutList = array();
            $hiddenList = array();
            foreach ($pageList as $pageName => $pageData) {
                if ($pageData[show] == "hidden") {
                    $hideWhy = $pageData["hideWhy"];
                    // echo ("page $pageName Hidden because $hideWhy <br> ");
                    $hiddenList[$pageName] = $pageData;
                    unset($pageList[$pageName]);
                    continue;
                }
                
                
                if (substr($pageName,0,7) != "layout_") continue;
                
                $layoutList[$pageName] = $pageData;
                unset($pageList[$pageName]);
                // echo ("<b>$pageName</b> is LAYOUT <br>");
            }
            // SET Layout List
            session::set(layouts,$layoutList);
            // echo ("Count Hidden".count($hiddenList)."<br />");
            self::hiddenList_set($hiddenList);
            // set pageList
            self::pageList_set($pageList);
            
            // $this->session_set("pageList",$pageList);
            
            $pageIdList = null;
            $pageBackList = null;
            $pageGroupList = null;            
        }
        
        if (!is_array($pageIdList)) {
            if ($debug) echo ("<h2>Create pageIdList<br> ");
            $pageIdList = array();
            foreach ($pageList as $pageName => $pageData) {
                $pageId = $pageData[id];
                $pageIdList[$pageId] = $pageName; 
                // echo ("Set $pageId = $pageName <br>");
            }
            self::idList_set($pageIdList);
            
            
            // Hidden 
            $hiddenIdList = array();
            foreach ($hiddenList as $pageName => $pageData ) {
                $pageId = $pageData[id];
                $hiddenIdList[$pageId] = $pageName; 
                // echo ("Set $pageId = $pageName to Hidden <br>");
            }
            self::hiddenIdList_set($hiddenIdList);    
            
            // $this->session_set("pageIdList",$pageIdList);
        }
        
        if (!is_array($pageGroupList) OR !is_array($pageBackList)) {
            if ($debug) echo ("<h2>Create pageGroupList AND pageBackList </h2>");
            
            // foreach ($pageList as $key => $value) echo ("pageList $key => $value <br>");
            
            $res = cmsPage_getGoupeList($pageList,$pageIdList);
            foreach ($res as $key => $value ) {
                switch ($key) {
                    case "pageBackList" :
                        $pageBackList = $value;
                        self::backList_set($pageBackList);
                        // $this->session_set("pageBackList", $pageBackList);
                        break;
                    case "pageGroupList" :
                        $pageGroupList = $value;
                        self::groupList_set($pageGroupList);
                        //$this->session_set("pageGroupList",$pageGroupList);
                        break;
                    default : 
                        echo ("unkown key($key) in Create PageGroupList $value <br>");
                }
            }
            self::lastPage(0); // $lastPage = null;
        }
    }        
    
    
    
    
    static public function data_byName($pageCode) {
        $pageData = session::get("pageList,".$pageCode);
        if (is_array($pageData)) return $pageData;
        
        
        $dynamcicPage = self::dynamic_pageCode($pageCode);
        if ($dynamcicPage) {
            return "dynamicPageCode";
        }
        $pageData = session::get("pageList,".$pageCode);
        return $pageData;        
    }
    
    static public function data_byId($pageId) {
        $pageName = page::pageName_byId($pageId);
        if ($pageName) {
            return self::data_byName($pageName);
        }
        return 0;
        echo ("No PageName get for Id $pageId <br>");        
    }
    
    static public function pageName_byId($pageId) {
        return session::get("pageIdList,".$pageId);
    }
            
    
    static public function actPage() {
        $pageData = session::get("pageData");
        if (!is_array($pageData)) return "noPageData";
        
        $pageName = $pageData[name];
        $dynamic = $pageData[dynamic];
        
        if ($dynamic) {
            $pageCode = self::dynamic_actPage($pageData);
        } else {
            $pageCode = $pageName;
        }
        
        self::$actPage = session::get("actual_Page");
        
        
        if ($pageCode != self::$actPage) {
            // echo "Set Last page to '".self::$actPage."' newPage = '$pageCode' <br>";
            self::lastPage(self::$actPage);
            // self::$lastPage == self::$actPage;
            self::$actPage = $pageCode;
            session::set("actual_Page",$pageCode);
        }
        
        return $pageCode;
    }
    
    static public function lastPage($setLast=null) {
        if (!is_null($setLast)) {
            // echo ("SET LAST PAGE to '$setLast' <br>");
            self::$lastPage = $setLast;
            session::set("last_Page",$setLast);
        } else {
            self::$lastPage = session::get("last_Page");
        }
        
        return self::$lastPage;
    }
    
        
    private function dynamic_actPage($pageData) {
        $pageName = $pageData[name];
        $data = $pageData[data];
        $pageNameAdd = "";
        
        // DYNAMIC LEVEL 1 
        $dataSource_1 = $data[dataSource];
        if ($dataSource_1) {
            $dataValue_1 = $_GET[$dataSource_1];
            if ($dataValue_1) {
                if ($pageNameAdd) $pageNameAdd .= "|";
                $pageNameAdd .= $dataSource_1."=".$dataValue_1;
            }        
        }
        
        // DYNAMIC LEVEL 2 
        $dataSource_2 = $data[dataSource2];
        if ($dataSource_2) {
            $dataValue_2 = $_GET[$dataSource_2];
            if ($dataValue_2) {
                if ($pageNameAdd) $pageNameAdd .= "|";
                $pageNameAdd .= $dataSource_2."=".$dataValue_2;
            }
            // echo ("SOURCE 2 => $dataSource_2 = '$dataValue_2' <br>");
        }
        
        if ($pageNameAdd) {
            $pageName .= "_".$pageNameAdd;
            // echo ("DYMAMIC PAGECODE = $pageName <br> ");
        }
        return $pageName;
    }

    
    public static function mainPages($mainId=0) {
        $pageList = self::pageList();
        $mainList = array();
        foreach ($pageList as $pageName => $pageData) {
            $pageMainId = $pageData[mainPage];
            if ($pageMainId != $mainId) continue;            
            $mainList[$pageName] = $pageData;            
        }
        return $mainList;
    }
    
    
    public static function pageList_setPage($pageName,$pageData) {
        $keyStr = "pageList,".$pageName;
        // echo ("set keyStr = '$keyStr' = '$pageData' $pageData[mainId] <br>");
        session::set($keyStr,$pageData);
    }

    
     public static function pageList_setPage_value($pageName,$key,$value) {
        $keyStr = "pageList,".$pageName.",".$key;
        echo ("set keyStr = '$keyStr' = '$value' <br>");
        session::set($keyStr,$value);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // LIST FUNCTIONS                                                         //
    public static function pageList() { return self::pageList_get(); }
    public static function idList() { return self::idList_get(); }
    public static function groupList() { return self::groupList_get(); }
    public static function backList() { return self::backList_get(); }
        
    public static function pageList_get() { return session::get("pageList"); }
    public static function pageList_set($pageList) { session::set("pageList",$pageList); }
    
    public static function hiddenList_get() { return session::get("pageHidden"); }
    public static function hiddenList_set($hiddenList) { session::set("pageHidden",$hiddenList); }
    
    public static function hiddenIdList_get() { return session::get("pageHiddenId"); }
    public static function hiddenIdList_set($hiddenIdList) { session::set("pageHiddenId",$hiddenIdList); }
    
    public static function idList_get() {return session::get("pageIdList"); }
    public static function idList_set($pageIdList) {session::set("pageIdList",$pageIdList);}
    
 
    public static function backList_get()  { return session::get("pageBackList"); }
    public static function backList_set($backList) { return session::set("pageBackList",$backList); }
    
    public static function backList_getPage($pageName) { 
        $backList = session::get("pageBackList,".$pageName);
        if ($backList) return $backList;
        
        $dynamicPage = self::dynamic_pageCode($pageName);
        if ($dynamicPage) {
            $mainName = $dynamicPage[mainName];
            $addName  = $dynamicPage[addName];
            
            $backList = session::get("pageBackList,".$mainName);
            if ($addName) $backList.= "|".$addName;
            
            return $backList;
            
        }
        return session::get("pageBackList,".$pageName); }
    
    
    
    public static function groupList_get() { return session::get("pageGroupList"); }
    public static function groupList_set($groupList) { return session::set("pageGroupList",$groupList); }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // LAST PAGES LIST                                                        //
    public static function lastPageList() { return self::lastPageList_get(); }
    public static function lastPageList_get() { return session::get("lastPages"); }
    public static function lastPageList_set($lastPages) { return session::set("lastPages",$lastPages); }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // HIDDEN PAGES                                                           //
    
    public static function hiddenData_byId($pageId) {
        
        
        $hiddenIdList = self::hiddenIdList_get();
        $pageName = $hiddenIdList[$pageId];
        if ($pageName) {
            return self::hiddenData_byName($pageName);
        }
        $hiddenList = self::hiddenList_get();
        foreach ($hiddenList as $pageName => $pageData) {
            if ($pageId == $pageData[id]) {
                return $pageData;
            }
        }
        return "notFound";
    }
    
    public static function hiddenData_byName($pageName) {
        $hiddenList = self::hiddenList_get();
        $pageData = $hiddenList[$pageName];
        if (is_array($pageData)) return $pageData;
        return "notFound";        
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // PAGE INFO BACK                                                         //
    
    public static function infoBack($code=null) {
        if (is_null($code)) {
            $code = self::$actPage;
        }
        
        if (!is_array($code)) {
            if (is_integer($code)) {
                $pageId   = $code;
                $pageName = self::pageName_byId($pageId);
                $pageData = self::data_byName();
            } else {
                $pageName = $code;
                $pageData = self::data_byName($pageName);
                $pageId   = $pageData[id];
            }
        } else {
            $pageData = $code;
            $pageName = $pageData[name];
            $pageId   = $pageData[id];
        }
        
        if (!is_array($pageData)) {
            // echo ("<h1>No pageData ($pageData) get by code($code) </h1>");
            return array();
        }
        
        $name = $pageData[title];
        $url  = $pageData[name].".php";
        $urlAdd = $pageData[addUrl];
        if ($urlAdd) $url .= "?".$urlAdd;
        
        $breadcrumb = "";
        $breadCrumbList = array();
        $icon = $pageData[image];
        
        
        $backStr = self::backList_getPage($pageName);
        $indexName = self::pageName_byId(1);
        if (substr($backStr,0,strlen($indexName)) != $indexName) {
            // echo ("ADD indexName to backStr <br>");
            $backStr = $indexName."|".$backStr;
        }
        $backList = explode("|",$backStr);
        $backList = array_reverse($backList);
        foreach ($backList as $nr => $myPageName) {
            
            $myPageData = self::data_byName($myPageName);
            $myName = $myPageData[title];
            $myUrl  = $myPageData[name].".php";
            $myUrlAdd = $myPageData[urlAdd];
            if ($myUrlAdd) $myUrl .= "?".$myUrlAdd;
            $myIcon = $myPageData[image];
            
            $add=array();
            $add[name] = $myName;
            $add[url] = $myUrl;
            $add[id] = $myId;
            $add[icon] = $myIcon;
            $breadCrumbList[$myPageName] = $add;
            
            // echo ("ADD breadcrumbList $myPageName $myUrl <br>");
            
            // set Breadcrumb
            $addName = lg::lgStr($myName);            
            if ($breadcrumb) $breadcrumb = $addName." | ".$breadcrumb;                
            else $breadcrumb = $addName;
            
            // SET ICON
            if (!$icon) {
                if ($myIcon) $icon = $myIcon;
            }            
        }
        $res = array();
        $res[name] = $name;
        $res[url]  = $url;
        $res[icon] = $icon;
        $res[breadCrumb] = $breadcrumb;
        $res[breadCrumbList] = $breadCrumbList;
        return $res;        
    }

    private function dynamic_pageCode($pageCode) {
        $delimiterStart = strpos($pageCode,"_");
        $sameStart = strpos($pageCode,"=");
        if (!$delimiterStart OR !$sameStart) return 0;
       
        
        $start_page = substr($pageCode,0,$delimiterStart);
        $end_page = subStr($pageCode,$delimiterStart+1); 
        // echo ("is Dynamic start='$start_page' ende = '$end_page' <br>");
        
        $res = array();
        $res[mainName] = $start_page;
        $res[addName] = $pageCode;
        return $res;
    }
    
}

?>
