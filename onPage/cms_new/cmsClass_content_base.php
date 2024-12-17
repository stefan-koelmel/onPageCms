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
        $this->cmsVersion = $this->pageClass->cmsVersion;
        $this->pageName = $this->pageClass->pageData[name];
        $this->pageFile = $this->pageName.".php";
        $this->wireframeState = $this->pageClass->wireframeState;
        
        
        global $pageEditAble;
        $this->editable = $_SESSION[editable];
        $this->pageEditAble = $GLOBALS[pageEditAble];
        // foreach ($_GLOBAL as $key => $value) echo ("$key => $value <br>");
        $this->edit = $_SESSION[edit];

        

        
        $this->contentData = $contentData;
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
