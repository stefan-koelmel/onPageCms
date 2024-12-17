<?php

class cmsClass_content_base {

    function init_PageClass($pageClass) {
        if (is_object($pageClass)) $this->pageClass = $pageClass;
    }

    function init_content($contentData,$frameWidth) {
        // echo ("<h1> INIT $contentData,$frameWidth </h1>");
        if (!is_array($contentData)) $contentData = array();
        if (!is_array($contentData[data])) $contentData[data] = array();


        
        
        if (!is_object( $this->pageClass)) {
            $this->pageClass = pageClass_class();
        } 
        
        $this->pageId = $this->pageClass->pageId;
        $this->pageCode = $this->pageClass->pageCode;
        $this->cmsVersion = $this->pageClass->cmsVersion;
        $this->pageData = $this->pageClass->pageData;
        $this->pageName = $this->pageClass->pageData[name];
        $this->pageFile = $this->pageName.".php";
        $this->wireframeState = $this->pageClass->wireframeState;
        
        $this->mobileEnabled = $this->pageClass->mobileEnabled;
        $this->targetData = $this->pageClass->targetData;
        
        
        $this->userLevel = $this->pageClass->userLevel;
        $this->showLevel = $this->pageClass->showLevel;
        
        global $pageEditAble;
        $this->editable = $this->session_get(editable); //$_SESSION[editable];
        $this->pageEditAble = $this->pageClass->pageEditAble; // $this->session_get(pageEditAble); // $GLOBALS[pageEditAble];
        // foreach ($_GLOBAL as $key => $value) echo ("$key => $value <br>");
        $this->edit = $this->session_get(edit); //$_SESSION[edit];

        

        
        $this->contentData = $contentData;
        
        $title = $this->contentData[title];
        if (is_string($title)) {
            $help = str2Array($title);
            if (is_array($help)) $this->contentData[title] = $help;
        }
        
        
        $this->frameWidth = $frameWidth;
        $this->innerWidth = $frameWidth;

        $this->contentId = $contentData[id];
        $this->contentType = $contentData[type];

        $this->init_language();
        
        $this->contentType_init();
        $this->contentName = $this->getName();

        if ($this->pageEditAble) {
            $this->init_edit();
        }
        $this->contentViewMode = "content";

        $this->layoutEdit = 0;

        // echo ("Init $this->contentId $this->contentName <br>");
    }
    
    function session_get($key) {
        if (!is_object($this->pageClass)) {
            echo ("No Object Class for $key <br>");
        
            kjhkjh();
        }
        return $this->pageClass->session_get($key);
    }

    function session_set($key,$value) {
        if (!is_object($this->pageClass)) kjhkjh();
        return $this->pageClass->session_set($key,$value);        
    }
    
    function setMainClass($mainClass=0) {
        if (is_object($mainClass)) {
            $this->mainClass = $mainClass;
            // echo ("<h1>Set Main Class in ".$this->getName()."</h1>");
        }
    }
    
    function page_getData($idOrName,$useSession=1) {
        return $this->pageClass->page_getData($idOrName,$useSession);
    }
    
    function contentType_init() { }

    function init_layout($contentData,$frameWidth) {
        $this->init_content($contentData, $frameWidth);
        // echo ("<h1>CONTENT $this->contentName for Layout </h1>");
        $this->contentViewMode = "layout";
        if ($_GET[editLayout]) $this->layoutEdit = $_GET[editLayout];
        
    }
    
    function page_getContent($contentCode,$mainId=null,$mainType=null) {
        $res = $this->pageClass->content_findContent($contentCode,$mainId,$mainType);
        return $res;
    }
}

?>
