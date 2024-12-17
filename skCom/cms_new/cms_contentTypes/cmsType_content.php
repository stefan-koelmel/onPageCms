<?php // charset:UTF-8

class cmsType_content_base extends cmsClass_content_show {
    function getName (){
        return "Inhalt";        
    }
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;

        if ($this->layoutEdit) {
            echo ("<div style='height:400px;background-color:#f00;color:#fff;padding:20px;'>");
            echo ("<h1> Hier ist der Inhalt </h1>");
            echo ("</div>");
            return 0;
        }
        // echo ("<h1>CONTENT SHOW </h1>");
        $this->pageClass->show_contentFrame($this->contentData,$this->innerWidth);
        
        // $this->contentShow_showContent();


        // cms_layout_showContent($contentData,$frameWidth,$showContent,$editable); 
    }

    function contentShow_showContent() {
        echo ("<h1>contentShow_showContent()</h1>");
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        global $pageEditAble;
        $data = $this->contentData[data];
        if (!is_array($data)) $data = array();

        $absLeft = 0;
        $absRight = 0;
        $absTop = 0;
        $absBottom = 0;
        $minHeight = 0;
        // show_array($data);

        if ($data[minHeight]) $minHeight = $data[minHeight];
        if ($data[absLeft]) $absLeft = $data[absLeft];
        if ($data[absRight]) $absRight = $data[absRight];
        if ($data[absTop]) $absTop = $data[absTop];
        if ($data[absBottom]) $absBottom = $data[absBottom];

        $this->innerWidth = $this->frameWidth - $absLeft - $absRight;

        $pageData = $GLOBALS[pageData];
        $pageInfo = $GLOBALS[pageInfo];
        //show_array($pageData);
        // echo ("HIER INHALT OF $pageInfo[pageName]<br />");
        $divData = array();
    
    // $targetWidth = $_SESSION[target_width];
    // $widthProz = floor($frameWidth / $targetWidth * 100.0);
    
    // echo ("SHow Content fw=$frameWidth tw=$targetWidth proz=$widthProz <br>");
    
    
    
    
    // $divData[style] = "width:".$frameWidth."px;";
    // $divData[style] = "width:".$widthProz."%;";
    
        if ($absLeft) $divData[style].= "margin-left:".$absLeft."px;";
        if ($absRight) $divData[style].= "margin-right:".$absRight."px;";
        if ($absTop) $divData[style].= "margin-top:".$absTop."px;";
        if ($absBottom) $divData[style].= "margin-bottom:".$absBottom."px;";
        if ($minHeight) $divData[style].= "min-height:".$minHeight."px;";
    
    
        if ($this->edit) {
            $divData["class"]= "dragFrame";
            $divData[id]="dragFrame_0";
        }
        
        
        
        // GET PAGE TYPE
        $pageType = $this->contentType_getPageType();
    
        switch ($pageType) {
            case "noRight" :
                $this->content_show_noRight($divData);
                break;
            
            case "page" :
                $this->content_show_content($divData);
                break;
            
            case "admin" :
                $this->content_show_admin($divData);
                break;

            case "sitemap" :
                $this->content_show_sitemap($divData);
                break;

        }
    }
    
    function content_show_titleLine() {
        if ($this->contentViewMode == "layout" AND $this->layoutEdit ) return 0;
        $showWidth = $this->frameWidth - 20;
        global $pageData;
        
        div_start("titleLineFrame");
        cms_titleLine($pageData,$this->innerWidth);
        div_end("titleLineFrame");
    }
    
    function content_show_admin($divData) {
        div_start("adminFrame",$divData);
        // SHOW TITLE LINE
        $this->content_show_titleLine();
        
        // SHOW ADMIN
        cms_admin_show($this->adminView,$this->frameWidth);
        div_end("adminFrame");
    }
    
    function content_show_sitemap($divData) {
        global $cmsName,$cmsVersion;
        div_start("siteMapFrame",$divData); // ,array("cmsName"=>$cmsName));
        // SHOW TITLE LINE
        $this->content_show_titleLine();
        
        include($_SERVER['DOCUMENT_ROOT']."/cms_".$cmsVersion."/cms_sitemap.php");

        cms_sitemap_show($this->frameWidth);
        div_end("siteMapFrame");
    }
    
    function content_show_noRight($divData) {
        div_start("content",$divData); //"width:".$frameWidth."px;");
        // SHOW TITLE LINE
        $this->content_show_titleLine();
        
        div_start("pageNotAllowed");
        echo ("SIE HABEN KEINE BERECHTIGUNG FÜR DIESE SEITE");
        div_end("pageNotAllowed");
        div_end("content","before");        
    }
    
    function content_allowedPage() {
        global $pageShow;
        return $pageShow;        
    }
   
    
    function content_show_content($divData) {
        // Dynamic Header
        global $pageData;
        
        $pageId = "page_$pageData[id]";
        // echo ("PAGE Is PAGE <br>");
        $dynamicPageId = cms_dynamicPage_showTitleBar($pageData);
        if ($dynamicPageId) {
            $pageId = $dynamicPageId;
        }
        $GLOBALS[pageContentId] = $pageId;

        // Div ContentStart
        echo ("<div class='cmsContentStart cmsContentStart_hidden'>");
        echo ("&nbsp;");
        echo ("</div>");

        div_start("content",$divData); //"width:".$frameWidth."px;");
        
        // SHOW TITLE LINE
        $this->content_show_titleLine();
        //


        if ($_SESSION[userLevel]>6) $editAble = 1;

        if ($this->pageEditAble) $editAble = 1;

        if ($this->pageEditAble) {
            $spacerClass = "spacer spacerEdit";
            if ($this->edit) $spacerClass .= " spacerDrop";
            echo ("<div id='spacerId_$contentId' class='$spacerClass'>");
        }
        echo ("<div class='spacer spacerContentType spacerContentStart'>&nbsp;</div>");
        if ($this->pageEditAble) {
            echo ("</div>"); //  id='spacerId_$contentId' class='spacerDrop'>");
        }
        // echo ("<h1> SHOW CONTENT FOR $pageId fw=$this->frameWidht iw=$this->innerWidth $divData[style] </h1>");
        // show Content
        cms_content_show($pageId,$this->innerWidth);

        div_end("content","before");
        
        echo ("<div class='cmsContentEnd cmsContentEnd_hidden'>");
        echo ("&nbsp;");
        echo ("</div>");
    }

    function contentType_getPageType() {
        $pageAllowed = $this->content_allowedPage();
        if (!$pageAllowed) {
            return "noRight";
        }
        
        global $pageInfo;
        $pageType = "page";
        
        switch ($pageInfo[pageName]) {
            case "sitemap" : $pageType = "sitemap"; break;
            case "admin" : $pageType = "admin"; $adminView=""; break;
            default :
                if (substr($pageInfo[pageName],0,6) == "admin_") {
                    $this->adminView = substr($pageInfo[pageName],6);  
                    $pageType = "admin";
                }            
        }
        return $pageType;
    }
   
    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth = $this->frameWidth;

        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();
        $res["content"][showName] = $this->lga("content","contentTab");
        $res["content"][showTab] = "More";
 
        $addData = array();
        $addData["text"] = $this->lga("contentType_content","minHeight"); //"Abstand Oben";
        $addData["input"] = "<input type='text' name='editContent[data][minHeight]' value='$data[minHeight]' >";
        $addData[mode] = "Simple";
        $res["content"][] = $addData;
        
        // MainData
        $addData = array();
        $addData["text"] =  $this->lga("contentType_content","distLeft"); //"Abstand Links";
        $addData["input"] = "<input type='text' name='editContent[data][absLeft]' value='$data[absLeft]' >";
        $addData[mode] = "More";
        $res["content"][] = $addData;
        
        $addData = array();
        $addData["text"] = $this->lga("contentType_content","distRight"); //"Abstand Rechts";
        $addData["input"] = "<input type='text' name='editContent[data][absRight]' value='$data[absRight]' >";
        $addData[mode] = "More";
        $res["content"][] = $addData;
        
        $addData = array();
        $addData["text"] = $this->lga("contentType_content","distTop"); //"Abstand Oben";
        $addData["input"] = "<input type='text' name='editContent[data][absTop]' value='$data[absTop]' >";
        $addData[mode] = "More";
        $res["content"][] = $addData;

        $addData = array();
        $addData["text"] = $this->lga("contentType_content","distBottom"); //"Abstand Oben";
        $addData["input"] = "<input type='text' name='editContent[data][absBottom]' value='$data[absBottom]' >";
        $addData[mode] = "More";
        $res["content"][] = $addData;
        
        //$res[frame] = "hideTab";
        $res[wireframe] = "hideTab";
        //$res[frameText] = "hideTab";
        //$res[settings]  = "hideTab";
        
        return $res;
    }    
    
}

function cmsType_content_class() {
    if ($GLOBALS[cmsTypes]["cmsType_content.php"] == "own") $contentClass = new cmsType_content();
    else $contentClass = new cmsType_content_base();
    return $contentClass;
}

function cmsType_content($contentData,$frameWidth) {
    $contentClass = cmsType_content_class();
    $contentClass->show($contentData,$frameWidth);
}



function cmsType_content_editContent($editContent) {
    $contentClass = cmsType_content_class();
    $res = $contentClass->contentType_editContent();
    return $res;
}
    


?>
