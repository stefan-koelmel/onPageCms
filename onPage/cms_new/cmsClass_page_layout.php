<?php

class cmsClass_page_layout extends cmsClass_page_base {
    
    function layout_show() {
        $this->layoutList = $this->layout_getList();
        $layoutCount = count($this->layoutList);

        $this->layout_frame_start();

        $leftPos = 0;

        foreach ($this->layoutList as $layoutNr => $layoutContent) {
            $type = $layoutContent[type];
            $specialView = 0;
            if ($layoutNr == 0) $specialView = "first";
            if ($layoutNr == $layoutCount-1) $specialView = "last";

            if ($specialView) $layoutContent[specialView] = $specialView;
            $layoutContent[viewContent] = "layout";
            $layoutContent[leftPos] = $leftPos;

            
            // SHOW LAYOUT CONTENT
            $res = $this->page_content_show($layoutContent,$this->pageWidth);
            if (is_object($res)) {
                $this->layoutList[$layoutNr] = $res;
            } else {
                echo ("<h1>No Object - $res </h1> ");
            }            
        }

        $this->layout_frame_end();
        
        
    }
    
    function layout_getName() {
        $layoutName = $this->pageData["layout"];
        if ($this->editLayout) $layoutName = $this->editLayout;
        if (!$layoutName) $layoutName = $this->cmsSettings[layout];
        if (!$layoutName) $layoutName = "layout_standard";
        return $layoutName;        
    }
    
    function layout_getList() {
        $this->layoutName = $this->layout_getName();
        // echo ("layoutName : $this->layoutName <br>");
        $list = cms_content_getList($this->layoutName);
        return $list;        
    }


    function layout_frame_start() {
        $divData = array();
       
        $divData[style] = "width:".$this->pageWidth."px;";

        global $pageData,$pageInfo,$pageShow,$pageEditAble;
        $pageId = $pageData[id];

        $myLevel = $_SESSION[showLevel];
        if (!$myLevel) $myLevel = 0;
        $myId = $_SESSION[userId];



        $showLevel = $pageData[showLevel];
        $subData = $pageData[data];
        $showContent = 1;


        $showEdit = $_SESSION[edit];
        if ($showEdit) {
            $showAddContent = 1;
            if ($myLevel >= 7) $editAble = 1;

        } else {
            $pageName = $pageData[name];
            if (substr($pageName,0,6) == "admin_") {
                if ($myLevel>7) $showEdit = 1;
                // echo ("<h1>Show Edit because Admin $pageName</h1>");
            }

        }




        // $cmsEditMode = $GLOBALS[cmsSettings][editMode];
        if ($this->pageEditAble ) {
            switch ($this->editMode) {
                case "siteBar" :
                    echo ("<div class='cmsEditMainFrame' style='width:".(400+20+$pageWidth)."px;' >");
                    $res = cmsEditBox_show();
                    echo ("$res");
                    $divData[style] .= "float:left;";
                    break;
                case "onPage2" :
                    // echo ("<div class='layoutEditFrame'>");
                    echo ("<div class='layoutEditFrame' style='min-width:".($this->pageWidth+10+280)."px;' >");
                    $divData[style] .= "float:left;";
                    break;
            }
        }

        div_start("layoutFrame layoutCenter",$divData);

        // echo ("<h2>layoutFrameStart</h2>");
        $this->page_start();


        if ($pageEditAble) {
            $showAddContent = 1;
        }

        if ($this->editLayout) {
            $this->layout_editLayout_start();
        } else {
            $this->layout_saveLayout();           
        }
    }


    function layout_frame_end() {
        if ($this->editLayout) {
            $this->layout_editLayout_send();
        }
        // div_end("dragFrame");

        div_end("layoutFrame layoutCenter","before");

        if ($this->pageEditAble) {
            switch ($this->editMode) {
                case "sideBar" :
                    echo ("<div style='clear:both;'></div>");
                    echo ("</div>");
                    break;

                case "onPage2" :
                    cmsModul_show();
                    echo ("<div style='clear:both;'></div>");
                    echo ("</div>");
                    break;
            }
        }
    }
    
    
    function layout_editLayout_start() {
        div_start("cmsEditLayout");
        echo ("Edit Layout <b>$this->layoutName</b> <a href='".$this->pageInfo[page]."' class='cmsLinkButton'>Layout schließen</a><br />");

        div_start("cmsContentStart cmsContentStart_hidden");
        echo ("&nbsp;");
        div_end("cmsContentStart cmsContentStart_hidden");

        $this->layout_saveLayout($this->layoutName);

        div_end("cmsEditLayout","before");
//        } else {
//            $contentList = cms_content_getList($layoutName);
//        }
//
//
//            $sortCheck = 0;
        div_start("dragFrame",array("id"=>"dragFrame_layout"));
    }

    function layout_editLayout_send() {
        div_end("dragFrame");
    }

    function layout_saveLayout () {
        if (!$_POST) return 0;
        // foreach ($_POST[layoutData] AS $key => $value ) echo ("POST $key = $value <br>");
        
        if ($this->editLayout) {
            // echo ("SAVE LAYOUT $this->editLayout <br>");
            $savePage = $this->editLayout;
        } else {
            $savePage = $this->pageCode; // "page_".$this->pageData[id];
            
           echo ("SAVE CONTENT ".$this->pageData[id]." -   $this->pageCode<br>");
        }
        if (!$savePage) return 0;
        cms_content_savePost($savePage);
    }

}

?>
