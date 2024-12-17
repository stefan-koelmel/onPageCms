<?php

class cmsClass_page_base {
    
    function init_page() {
        
        global $pageData,$pageInfo,$cmsVersion;
        
        $this->cmsVersion = $cmsVersion;
        
        $this->pageData = $pageData;
        $this->pageInfo = $pageInfo;
        $this->pageId   = $this->pageData[id];
        
        
        $this->pageCode = $this->page_getPageCode();

        $this->cmsSettings = $_SESSION[cmsSettings];
        if (!is_array($this->cmsSettings)) {
            $getSettings = cms_settings_get();
            echo ("No CMS SETTINGS !! $getSettings <br>");
            $this->cmsSettings = $getSettings;
        }
        global $pageEditAble;
        $this->pageEditAble = $pageEditAble; // $GLOBAL[pageEditAble];
        $this->editMode = $this->cmsSettings[editMode];

        // $this->editable = $_SESSION[editable];
        $this->edit = $_SESSION[edit];

        // echo ("edit=$this->edit editAble = $this->editAble pageEditAble = $this->pageEditAble $editMode=$this->editMode <br>");


        $this->pageWidth = $this->cmsSettings[width];
        if (!$this->pageWidth) $this->pageWidth = 800;

        $this->useType=array();

        $this->editLayout = $_GET[editLayout];

        
        $this->contentList = $this->content_contentList(); //  page_contentList();

        $this->page_mobilePage();

        // wireframe
        $this->wireframeState = cmsWireframe_state();
        
        // foreach ($this->cmsSettings as $key => $value) echo ("page $key => $value <br />");
        
    }


    
    function page_show() {

        $pageState = $this->page_pageState();
        switch ($pageState) {
            case "online" :
                break;
            case "construction" :
                $exit = cms_page_construction();
                if ($exit) return 0;
                break;
            case "inWork" :
                $exit = cms_page_inWork();
                if ($exit) return 0;
                break;
        }
        // echo ("<h2>page_show</h2>");
        // $this->page_start();
        
        
        $this->layout_show();
        
        
        $this->page_end();
        
    }

    function page_userType($type) {
        $this->useType[$type]++;
    }

    function page_start() {
        $standardVersion = $GLOBALS[cmsVersion];
        global $defaultCmsVersion;
        if ($defaultCmsVersion) {
            div_start("testCmsVersion","background-color:#f00;display:block;padding:5px;color:#fff;font-size:14px;");
            $standardVersion = $defaultCmsVersion;
            $testVersion = $_SESSION[cmsVersion];
            echo ("CMS VERSION <b>$testVersion</b> wird getestet - Standard CMS-Version ist <k>$standardVersion</k>");
            div_end("testCmsVersion");
        }


        if ($this->pageEditAble AND !$this->editLayout ) {
            cms_Layout_showEditPageData($his->editMode,$this->pageWidth);
        }        
    }



    function page_end() {
       // echo ("</div>");
        // foreach($this->useType as $key => $count) echo ("Used Type $key = $count <br />");
    }


    function page_pageState() {
        $state = $_SESSION[pageState];
        if ($state) return $state;

        $state = $this->cmsSettings[state];
        if (!$state) $state = "online";
        return $state;
    }

    
   function page_getPageCode() {
       $pageCode = "page_".$this->pageId;
       if ($this->pageData[dynamic]) {
           $pageCode = $this->page_getDynamicPageCode();
       }
       return $pageCode;
    }
    
    function page_getDynamicPageCode() {
        $dynamicData = $this->pageData[data];
        if (!is_array($dynamicData)) $dynamicData = array();
        $pageId = $this->pageData[id];


        //echo ("cms_content_show($pageorFrame)<br>");

        $newPage = "dynamic_".$this->pageId."-";

        $dynamic_1 = $this->pageData[dynamic];
        $dynamic_2 = $dynamicData[dynamic2];
        $dynamic_1_type = $dynamicData[dataSource];
        $dynamic_1_value = $_GET[$dynamic_1_type];

        // show_array($dynamicData);
        if ($dynamic_1_value) {
             // echo ("Dynamic 1 = '$dynamic1' = $dynamic1Set <br>");
             $newPage .= "1";

             if ($dynamic_2) {
                $dynamic_2_type = $dynamicData[dataSource2];
                $dynamic_2_value = $_GET[$dynamic_2_type];
                // echo ("Dynamic 2 = '$dynamic2' = $dynamic2Set <br>");

                if ($dynamic_2_value) $newPage.= "-1";
                else $newPage.= "-0";
             }
        } else {
            $newPage .= "0";
        }
        return $newPage;
    }
    
    

    function page_mobilePage() {
        $mobilPages = $this->cmsSettings[mobilPages];
        if (!$mobilPages) {
            $this->mobilPages = 0;
            return 0;
        }
        if ($mobilPages) {
            $specialWidth = 0;

            $sizeList = array();
            $sizeList[iPhone] = array("Portrait"=>320,"Landscape"=>480);
            $sizeList[iPad]   = array("Portrait"=>1024,"Landscape"=>768);

            $target_target = $_SESSION[target_target];

            if ($target_target == "Mobil") {
                $defaultTarget = "iPhone";
                // echo (" Target is $target_target defined by SiteBar $defaultTarget <br>");
                $target_target = $defaultTarget;
            }

            if ($target_target != "Pc") {
                $target_orientation = $_SESSION[target_orientation];
                // echo ("Target = $target_target Orientation = $target_orientation useWidth = $_SESSION[target_width] <br>");
                if (is_array($sizeList[$target_target])) {
                    $specialWidth = $sizeList[$target_target][$target_orientation];
                    // echo "SizeList Found / useWidth = $specialWidth <br>";
                }
            }



            if ($specialWidth AND $specialWidth < $this->pageWidth ) {
                // echo ("<b> SET WIDTH from $pageWidth to $specialWidth </b><br/>");
                $this->pageWidth = $specialWidth;
            }
        }

    }
    
    //put your code here
}



?>
