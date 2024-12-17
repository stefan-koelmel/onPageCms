<?php

class cmsClass_page_content extends cmsClass_page_layout {



    function page_content_show($contentData,$frameWidth) {
        $type = $contentData[type];
        $viewContent = $contentData[viewContent];
        //if (!$viewContent) $viewContent = "content";




        $type = $contentData[type];

        $newContentData = cms_contentType_oldTypes($type,$contentData);
        if (is_array($newContentData)) {
            $contentData = $newContentData;
            $type = $contentData[type];
        }


        switch ($type) {
            case "frame1" : $type = "frame"; break;
            case "frame2" : $type = "frame"; break;
            case "frame3" : $type = "frame"; break;
            case "frame4" : $type = "frame"; break;
        }

        $functionName = "cmsType_".$type."_class";
        if (!function_exists($functionName)) return "class Function not exist";


        $newClass = call_user_func($functionName);
        if (!is_object($newClass)) return "no Class get";

        // echo ("view=$viewContent editLayout = $this->editLayout <br/>");
        switch ($viewContent) {
            case "content" : 
                if (!method_exists ($newClass ,"content_show")) return "Method $callFunction not exist";

                $newClass->init_PageClass($this);
                $newClass->content_show($contentData,$frameWidth);


                break;

            case "layout"  : 
                if (!method_exists ($newClass ,"layout_show")) return "Method $callFunction not exist";
                $newClass-> init_PageClass($this);
                $newClass->layout_show($contentData,$frameWidth);
//                if ($this->pageEditAble) {
//                    $spacerClass = "spacer spacerEdit spacerLayout";
//                    if ($this->edit AND $this->editLayout) $spacerClass .= " spacerDrop";
//                    echo ("<div id='spacerId_".$contentData[id]."' class='$spacerClass'></div>");
//                }
                break;

            default:
                return "no Function defined for '$viewMode' ";
        }

        // add To page Uses Type
        $this->page_userType($type);
        return $newClass;
    }





   
    function show_contentFrame($contentData=array(),$frameWidth=0) {
        $this->contentData = $contentData;
        $this->frameWidth = $frameWidth;
        $this->contentWidth = $frameWidth;
        $this->contentLeft = $contentData[leftPos] + $contentData[data][absLeft];
        $this->contentRight = $contentData[data][absRight];
       
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

        $this->contentWidth = $this->innerWidth;
        
        $pageData = $GLOBALS[pageData];
        $pageInfo = $GLOBALS[pageInfo];
        //show_array($pageData);
        // echo ("HIER INHALT OF $pageInfo[pageName]<br />");
        $divData = array();
    
    
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


        div_start("titleLineFrame");
        cms_titleLine($this->pageData,$this->contentWidth);
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
        
        //$this->pageCode = "page_".$this->pageId; //[id];
        // echo ("PAGE Is PAGE <br>");
        $dynamicPageId = cms_dynamicPage_showTitleBar($this->pageData);
        if ($dynamicPageId) {
            // $this->pageCode = $dynamicPageId;
        }
        // $GLOBALS[pageContentId] = $pageId;

        // Div ContentStart
        echo ("<div class='cmsContentStart cmsContentStart_hidden'>");
        echo ("&nbsp;");
        echo ("</div>");

        div_start("content",$divData); //"width:".$frameWidth."px;");
        
        // SHOW TITLE LINE
        $this->content_show_titleLine();
        

        if ($this->pageEditAble) {
            $spacerClass = "spacer spacerEdit spacerContentStart";
            if ($this->edit) $spacerClass .= " spacerDrop";
            echo ("<div id='spacerId_".$this->pageData[id]."' class='$spacerClass'>");
            echo ("</div>");
        } else {
            echo ("<div class='spacer spacerContentType spacerContentStart'>&nbsp;</div>");
        }
        

        // GET CONTENT LIST
        // $this->content_get_ContentList();
        
        // SHOW CONTENT LIST
        $this->content_show_contentList();

        // echo ("<h1> SHOW CONTENT FOR $pageId fw=$this->frameWidht iw=$this->innerWidth $divData[style] </h1>");
        // show Content




        //  cms_content_show($pageId,$this->innerWidth);

        div_end("content","before");
        
        
        echo ("<div class='cmsContentEnd cmsContentEnd_hidden'>");
        echo ("&nbsp;");
        echo ("</div>");
    }

    function content_show_frameContent($pageid,$frameWidth,$viewContent,$leftPos=0) {
        if ($viewContent == "layout") {
            // echo ("Show Frame from Layout <br>");
            $myContentList = cms_content_getList($pageid);
        } else {
            $myContentList = $this->content_findContent($pageid,null,"frame");
        }

        // SPACER FRAME START
        $spacerClass = "spacer spacerFrameStart";
        if ($this->pageEditAble) {
            $spacerClass .= " spacerEdit";
            if ($this->edit) $spacerClass .= " spacerDrop";

        }
        echo ("<div id='frameId_".$pageId."' class='$spacerClass'>");       
        echo ("</div>"); //  id='spacerId_$contentId' class='spacerDrop'>");



        $contentCount = count($myContentList);

        if ($contentCount == 0) {
            $this->content_show_noContent("frame");
            return 0;
        }

        foreach ($myContentList as $contentNr => $contentData) {

            $type = $contentData[type];
            $specialView = 0;
            if ($contentNr == 0) $specialView = "first";
            if ($contentNr == $contentCount-1) $specialView = "last";

            if ($specialView) $contentData[specialView] = $specialView;
            $contentData[viewContent] = $viewContent;

            $contentData[leftPos] = $leftPos;

            // getContent
            $res = $this->page_content_show($contentData,$frameWidth);
            if (is_object($res)) $myContentList[$contentNr] = $res;
            else {
                echo ("No Content Get - $res <br />");
                echo ("Show Content $type spe = $specialView width $this->contentWidth <br/> ");
            }

        }

        
    }

    function content_show_flipContent($pageid,$frameWidth,$viewContent,$leftPos=0) {
        
        if ($viewContent == "layout") {
            // echo ("Show Frame from Layout <br>");
            $myContentList = cms_content_getList($pageid);
        } else {
            $myContentList = $this->content_findContent($pageid,null,"flip");
        }

        $contentCount = count($myContentList);

        if ($contentCount == 0) {
            $this->content_show_noContent("flip");
            return 0;
        }

        foreach ($myContentList as $contentNr => $contentData) {

            $type = $contentData[type];
            $specialView = 0;
            if ($contentNr == 0) $specialView = "first";
            if ($contentNr == $contentCount-1) $specialView = "last";

            if ($specialView) $contentData[specialView] = $specialView;
            $contentData[viewContent] = $viewContent;

            $contentData[leftPos] = $leftPos;

            // getContent
            $res = $this->page_content_show($contentData,$frameWidth);
            if (is_object($res)) $myContentList[$contentNr] = $res;
            else {
                echo ("No Content Get - $res <br />");
                echo ("Show Content $type spe = $specialView width $this->contentWidth <br/> ");
            }

        }


//
//        $myContentList = cms_content_getList($pageid);
//        if (!is_array($myContentList)) $myContentList = array();
//
//        $res = $this->content_show_specialContentList("Flip",$myContentList,$frameWidth,$viewContent);
//        return $res;

    }

    function content_show_specialContentList($contentType,$myContentList,$frameWidth,$viewContent) {

        $contentCount = count($myContentList);

        if ($contentCount == 0) {
            $this->content_show_noContent($contentType);
            return 0;
        }

        foreach ($myContentList as $contentNr => $contentData) {

            $type = $contentData[type];
            $specialView = 0;
            if ($contentNr == 0) $specialView = "first";
            if ($contentNr == $contentCount-1) $specialView = "last";

            if ($specialView) $contentData[specialView] = $specialView;
            $contentData[viewContent] = $viewContent;

            // getContent
            $res = $this->content_show($contentData,$frameWidth);
            if (is_object($res)) $myContentList[$contentNr] = $res;
            else {
                echo ("No Content Get - $res <br />");
                echo ("Show Content $type spe = $specialView width $this->contentWidth <br/> ");
            }

        }
        return $myContentList;
    }




    function content_get_ContentList() {
       
        $list = $this->content_findContent($this->pageCode);
        // foreach ($list as $key => $value ) echo ("ContentList $key => $value <br>");
        return $list;
    }

    function content_show_contentList() {
        $contentList = $this->content_get_ContentList();
//        foreach ($contentList as $key => $value) {
//            echo ("content fpr $pageId $key $value[type], $value[pageId] <br>");
//        }
        
        if (!is_array($contentList)) $contentList = array();
        $contentCount = count($contentList);

        if ($contentCount == 0) {
            $this->content_show_noContent();
            return 0;
        }

        $leftPos = $this->contentLeft;
        
        foreach ($contentList as $contentNr => $contentData) {

            $type = $contentData[type];
            $specialView = 0;
            if ($contentNr == 0) $specialView = "first";
            if ($contentNr == $contentCount-1) $specialView = "last";

            if ($specialView) $contentData[specialView] = $specialView;
            $contentData[viewContent] = "content";
            $contentData[leftPos] = $leftPos;

            // getContent
            $res = $this->page_content_show($contentData,$this->contentWidth);
            if (is_object($res)) $this->contentObjectList[$contentNr] = $res;
            else {
                echo ("No Content Get - $res <br />");
                echo ("Show Content $type spe = $specialView width $this->contentWidth <br/> ");
            }

        }


    }

    function content_show_noContent() {
        echo ("No Content for this Page <br>");
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
    
    
     function content_contentList() {
        $pageId = "page_".$this->pageId;
        $pageId = $this->pageCode;
        // echo ("INIT CONTENT $pageId <br>"); // $page_id")
        // $list = cms_content_getAllList($pageId,"sort"); // ($pageId,$sort);
        $list = $this->content_getContent_forCode($pageId);
       
        return $list;
    }
    
    function content_findContent($contentCode=all,$mainId=null,$mainType=null) {
        // echo ("Find content_findContent '$contentCode',$mainId,$mainType) <br>");
        
        if ($mainId) {
            if (intval($mainId)) $mainId = "inh_".$mainId;
            // echo ("Test $mainId <br>");
        }
        
        
        $res = array();
        foreach ($this->contentList as $contentId => $contentData ) {
            if (!is_array($contentData)) {
                echo ("NO ARRAY for $contentId <br>");
                continue;
            }
            $contId       = $contentData[id];
            $contCode     = $contentData[pageId];
            $contMainType = $contentData[mainType];
            $contMainId   = $contentData[mainId];
             
//            if ($contentId == "inh_367") {
//                foreach($contentData as $key => $value) echo ("--> $key => $value <br>");
//            }
           
            
            
            if ($contentCode AND $contentCode != "all") {
                $add = 0;
                if ($contCode == $contentCode) {
                    // echo (" add because difrent ContentCode filter='$contentCode' data='$contCode'<br>");
                    $add = 1;
                } else {
                    // echo ("dont add because difrent ContentCode filter='$contentCode' data='$contCode'<br>");
                    $add = 0; // echo ("dont add because difrent ContentCode filter='$contentCode' data='$contCode'<br>");
                }
                
                
            } else {
                $add = 1;
            }
            
            if ($add AND $mainId) {
                $add = 0;
                if ($contMainId == $mainId) $add = 1;
                // echo ("Check MainID $mainId ".$contentData["mainId"]." <br>");
                
                // $contMainId = $contentData[mainId];
                // echo ("CHECK content $contentId after $mainId  $contentData[mainId]<br>");
            } 
            
            if ($add AND $mainType) {
                $add = 0;
                if ($mainType == $contMainType) $add = 1;
                // echo ("compare $mainType width $contMainType <br>");
                
            }
            
            if ($add) $res[$contentId] = $contentData;
            
//            echo ("$contCode <br>");
//            echo ("found $contentId $contentData <br>");
//            $mainId = $contentData[mainId];
//            $mainType = $contentData[mainType];
//            if ($mainId) echo (" main id=$mainId type='$mainType' <br>");
        }
        return $res;
    }
    
    function content_getContent_forCode($pageId,$sort=null) {
        $myScroll = array();
        // echo ("<h1>GET CONTENT FOR $pageId </h1>");
        $list = cms_content_getList($pageId,$sort); // array("pageId"=>$pageName,$sort));
    
        if (!is_array($list)) return $myScroll;
    
        foreach ($list as $i => $contData) { //for ($i=0;$i<count($list);$i++) {
            $contId    = $contData[id];
            $contType  = $contData[type];
            $contTitle = $contData[title];
            $contCode  = $contData[pageId];

            $id = "inh_".$contId;
            $myScroll[$id] = $contData;
            // $myScroll[$id] = array("id"=>$contId,"type"=>$contType,"title"=>$contTitle,"contCode"=>$contCode);

            if (substr($contType,0,5) == "frame") {
                $frameNr = substr($contType,5);
                for ($f=1;$f<=$frameNr;$f++) {
                    $frameName = "frame_".$contId."_".$f;
                    $frameList = $this->content_getContent_forCode($frameName,$sort);
                    foreach ($frameList as $frameId => $frameContData) {
                        if (!$frameContData["mainId"]) $frameContData["mainId"] = $id;
                        if (!$frameContData["mainType"]) $frameContData["mainType"] = "frame";
                        $myScroll[$frameId] = $frameContData;
                    }
                }              
            }

            if ($contType == "flip") {
                $anz = $contData[data][layerCount];
                // echo ("Fount Content width $anz Layer <br>");
                for ($f=1;$f<$anz+1;$f++) {
                    $layerCode = "layer_".$contId."_".$f;
                    // echo ("Get Content for $layerCode <br>");
                    $layerList = $this->content_getContent_forCode($layerCode,$sort);
                    foreach ($layerList as $layerId => $layerData) {
                        if (!$layerData["mainId"])   $layerData["mainId"] = $id;
                        if (!$layerData["mainType"]) $layerData["mainType"] = "flip";
                        // echo ("ADD $layerId Id to scrollList is=".$layerData["mainId"]." typ = '".$layerData["mainType"]."' <br>");
                        $myScroll[$layerId] = $layerData;
                    }
                }              
            }

        }    
        return ($myScroll);
    }
    
    
    
}

function pageClass_class() {
    global $pageClass;
    if (is_object($pageClass)) return $pageClass;
    
    $pageClass = new cmsClass_page_content;
    $pageClass->init_page();
    return $pageClass;
}



?>
