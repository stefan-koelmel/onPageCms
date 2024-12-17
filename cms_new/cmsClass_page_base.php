<?php
class cmsClass_page_base extends cmsClass_site_base {
    
    function init_page() {
        
        global $pageData,$pageInfo,$cmsVersion,$cmsName;
        
        $this->cmsVersion = $cmsVersion;
        $this->cmsName    = $cmsName;
        
        $this->init_site();
        
        
        
      
        $this->userLevel = $this->session_get(userLevel); //  $_SESSION[userLevel];
        // echo ("USER = ".$this->site_session_get(userLevel)."<br>");
        if (!$this->userLevel) {
            $this->userLevel = 0;
            $this->session_set(userLevel,$this->userLevel);
        }
        $this->showLevel = $this->session_get(showLevel); // $_SESSION[showLevel];
        if (!$this->showLevel) {
            $this->showLevel = 0;
            $this->session_set("showLevel",$this->showLevel);
        }
        
        if (is_array($pageData)) {
            
            $this->pageData = $pageData;
            $this->pageInfo = $pageInfo;
            $this->pageId   = $this->pageData[id];
            $this->page_post_actions();
            $this->pageCode = $this->page_getPageCode();
            $this->contentList = $this->content_contentList(); //  page_contentList();
        }

        // SET AND GET cmsSettings
        $this->page_cmsSettings();

        
        // TARGET DATA
        $this->mobileEnabled = $this->cmsSettings[mobilPages];
        $this->targetData = array();
        
        // echo ("MOBIL ENABLED = $this->mobileEnabled <br>");
        if ($this->mobileEnabled) {
            $this->targetData[enabled] = $this->mobileEnabled;
            $this->targetData[target] = $_SESSION[target_target];
            $this->targetData[orientation] = $_SESSION[target_orientation];
            $this->targetData[width] = $_SESSION[target_width];
            $this->targetData[height] = $_SESSION[target_height];           
        } else {
            $this->targetData[enabled] = 0;
            $this->targetData[target] = "pc";
        }
        if (!$this->targetData[target]) $this->targetData[target] = "pc";
        // foreach ($this->targetData as $key => $value) echo ("Target $key => $value <br>");
        
        
        global $pageEditAble; 
        $this->pageEditAble = $pageEditAble; // $GLOBAL[pageEditAble];
        $this->editMode = $this->cmsSettings[editMode];

        // $this->editable = $_SESSION[editable];
        $this->edit = $this->session_get(edit); // $_SESSION[edit];

        // echo ("edit=$this->edit editAble = $this->editAble pageEditAble = $this->pageEditAble $editMode=$this->editMode <br>");


        $this->pageWidth = $this->cmsSettings[width];
        if (!$this->pageWidth) $this->pageWidth = 800;

        $this->useType=array();

        $this->editLayout = $_GET[editLayout];

        
       
        // echo ("PAGA MOBILE <br>");
        $this->page_mobilePage();
        // echo ("PAGA LANGUAGE <br>");
        // $this->page_language_init();
        // echo ("PAGE WIREFRAME <br>");
        // wireframe
        $this->page_wireframe_init();
       
        
        // foreach ($this->cmsSettings as $key => $value) echo ("page $key => $value <br />");
        
    }
    
    function page_cmsSettings() {
        $this->cmsSettings = $this->session_get(cmsSettings);
        if (is_array($this->cmsSettings)) return 1;


        if (!function_exists("cms_settings_get")) {
            $settingsFile = $_SERVER['DOCUMENT_ROOT']."/cms_".$this->cmsVersion."/cms_settings.php";
            if (file_exists($settingsFile)) {
                include($settingsFile);
            } else {
                echo ("NOT EXIST settingsFile $settingsFile <br>");
                die();
            }
        }


        $getSettings = cms_settings_get();
        $this->session_set(cmsSettings,$getSettings);
        // echo ("No CMS SETTINGS!! $getSettings in pageClass init()<br>");
        $this->cmsSettings = $getSettings;
        return 1;
    }
    
    function page_setValue($key,$value) {
        
       
        switch ($key) {
            case "pageData" :
                if (is_array($this->pageData)) {
                    echo ("allready SET PAGEDATA in page_base setValue <br>");
                } else {
                    // echo ("page_setValue $value <br>");
                    $this->pageData = $value;
                    
                    session::set(pageData,$value);
                    $this->pageId   = $this->pageData[id];
                    $this->pageCode = $this->page_getPageCode();
                    $this->contentList = $this->content_contentList();

                    $this->init_site_page();
                    
                }

                break;
            
            case "404" :
                
                echo ("HIER kommt die 404 Seite PageData <br>");
                $pageData = array();
                $pageData[id] = "#404";
                $pageData[name] = 404;
                $pageData[title] = "Missing Page";
//                    $cmsSettings = cms::cmsSettings_get();
//                    foreach ($cmsSettings as $key => $value) {
//                        echo ("cms $key => $value <br>");
//                    }


                $this->pageData = $pageData;
                $this->pageId   = "#404";
                $this->pageCode = "page_404";
                $this->contentList = array();

                break;
                    
                                
            
            case "pageInfo" :
                // echo ("page_setValue PAGEINFO <br>");
                $this->pageInfo = $value;
                break;
            case "pageEditAble" :
                $this->pageEditAble = $value;
                break;
            
            case "pageShow" :
                $this->pageShow = $value;
                break;
            
            case "initLanguage" :
                $this->page_language_init();
                break;
                
            default :
                echo ("unkown key($key) in page_base setValue '$value' <br>");
        }
    }
    

    function page_setHistory() {
        cmsHistory_set($this->pageData,$this);
    }
   
    
    
    function page_post_actions() {
        if (!is_array($_POST)) return 0;
        
        if ($_POST[filter]) $reloadData = $this->page_post_filter();
        if ($_POST[editContentCancel]) {
            $reloadData = array("reload"=>1,"addLink"=>array());
            $reloadData[addLink][editMode] = "none";
            $reloadData[addLink][editContentData] = "none";
            $reloadData[addLink][editId]="none";
            $reloadData[addLink][selectedTab] = "none";
            $reloadData[addLink][ok] = "none";
            if ($_GET[editId]) $reloadData[addHash]= "#inh_".$_GET[editId];            
        }
//        foreach ($_POST as $key => $value) {
//            echo ("POST $$key = $value <br>");
//        }
//        
        if (is_array($reloadData)) {
            $reload = $reloadData[reload];
            $addLink = $reloadData[addLink];
            $addHash = $reloadData[addHash];
            if (!is_array($addLink)) $addLink = array();
            
//            echo ("Reload = $reload <br>");
//            echo ("addLink = ");
//            foreach ($addLink as $key => $value ) {
//                echo ("$key=$value ");
//            }
//            echo ("<br>");
            
            $goPage = $_SERVER["SCRIPT_NAME"];
            $query = $_SERVER["QUERY_STRING"];
            $queryList = array();
            if ($query) {
                $query = explode("&",$query);
                foreach ($query as $queryNr => $queryItem) {
                    list($queryKey,$queryValue) = explode("=",$queryItem);
                    $queryList[$queryKey] = $queryValue;
                }
            }
             
            $change = 0;
            foreach ($addLink as $queryKey => $queryValue) {
                // echo ("ADDLINK $queryKey => $queryValue <br>");
                if ($queryValue == "none") {
                    // echo ("dontAdd $queryKey ".$queryList[$queryKey]." <br>");
                     if (!is_null($queryList[$queryKey])) { // allready in Query
                         // echo ("Unset $queryKey<br>");
                         unset($queryList[$queryKey]);
                         $change++;
                     } else {
                         // not Add
                     }
                } else { // has Value
                    if ($queryList[$queryKey]) { 
                            // allready in Query


                        if ($queryList[$queryKey] == $queryValue) {
                            // no Change                        
                        } else {
                            // Change for Query;
                            $change++;
                            $queryList[$queryKey] = $queryValue;
                        }
                    } else {
                        // not in List
                        $change++;
                        $queryList[$queryKey] = $queryValue;
                    }
                }
            }
            if ($change) {
                
                //echo ("CHANGE in query $change <br>");
                $newQuery = "";
                $queryAdd = $addHash;
                foreach ($queryList as $key => $value ) {
                    if ($newQuery) $newQuery.= "&";
                    else $newQuery .= "?";
                    $newQuery .= $key."=".$value;
                    
                    switch ($key) {
                        case "editId" : $queryAdd = "#editFrame_".$value;
                    }
                }
                $newQuery .= $queryAdd;
//                echo ("$newQuery <br>");
//                echo ($goPage );
//                echo ("<a href='".$goPage.$newQuery."' target='test' >Test</a>");
                if ($reload) {
                    reloadPage($goPage.$newQuery,0);
                    die();
                }
            } else {
                echo ("noChange <br>");
            }            
        }

    }

    function page_post_filter() {
        $addLink = array();
        $reloadData = array();
        foreach ($_POST as $key => $value) {
            if (substr($key,0,7) == "filter_") {
                $filterKey = substr($key,7);
                // if ($value != "none") {
                $addLink["filter_".$filterKey] = $value;
                // }
                // echo ("FILTER $key => $value <br>");
            }
        }
        if ($addLink) {
            $reloadData[reload] = 1;
            $reloadData[addLink] = $addLink;
        }
        return $reloadData;
    }
    
    function page_show() {
        
        if (!is_array($this->pageData)) {
            global $pageInfo;
            $pageName = $pageInfo[pageName];
            switch ($pageName) {
                case "404" :
                    echo ("<h1>SEITE NICHT GEFUNDEN </h1>");
                    break;
                
                default : 
                    echo ("<h1>SET PAGE DATA in page_show </h1>");
                    global $pageData,$pageInfo;
                    $this->pageData = $pageData;
                    $this->pageInfo = $pageInfo;
                    $this->pageId   = $this->pageData[id];
                    $this->pageCode = $this->page_getPageCode();
                    $this->contentList = $this->content_contentList();
            }
            
            // foreach ($pageInfo as $key => $value) echo ("pageInfo $key => $value <br>");
            
            
            
        }
        
       
        $this->page_post_actions();
        
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
        // page_start() called in layout_frame_start()
        // $this->page_start();
        
        $this->session_show();
        
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
            $this->page_edit();
            
        }        
    }

    
    function page_edit() {
        switch ($this->editMode) {
            case "onPage"  : $mode = "old"; break;
            case "onPage2" : $mode = "own"; break;
            case "siteBar" : $mode = "new"; break;
            default:
                echo("unkown $cmsEditMode <br>");
        } 
        
        if ($mode != "own") {
            cms_Layout_showEditPageData($this->editMode,$this->pageWidth);
            return 0;
        }
    
        $editMode = $_GET[editMode];

        $addEditClass = "cmsEditToggle";
        if (!$this->edit) $addEditClass .= " cmsEditHidden";
        
        if ($editMode != "pageData") { // not in Edit
           echo ("<div class='cmsEditBox $addEditClass' >");
           $goPage = $this->pageData[page]."?editMode=pageData";
           
           echo ("<div class='cmsContentFrame_editPageButton' >");
           echo ("<a href='$goPage'>");
           echo ("<img src='/cms_".$this->cmsVersion."/cmsImages/cmsEditPage.png' border='0px'>");
           echo ("</a>");
           echo ("</div>");
           echo ("Seite Editieren");
           echo ("</div>");           
        } else {
           echo ("<div class='cmsContentEditFrame cmsContentEditPage $addEditClass'>");
           cms_page_editData($this->pageWidth,$this->targetData);
           
           // $this->page_edit_showForm();
           
           
           
           echo ("</div>");
       }          
    }
    
    function page_end() {
       // echo ("</div>");
        // foreach($this->useType as $key => $count) echo ("Used Type $key = $count <br />");
    }


    function page_pageState() {
        $state = $this->session_get("pageState"); // $_SESSION[pageState];
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
       //  echo ("PAGECODE = $pageCode <br>");
       return $pageCode;
    }
    
    function page_getDynamicPageCode() {
        $dynamicData = $this->pageData[data];
        if (!is_array($dynamicData)) $dynamicData = array();
          
        $newPage = "dynamic_".$this->pageId."-";

        $dynamic_1 = $this->pageData[dynamic];
        $dynamic_2 = $dynamicData[dynamic2];
        $dynamic_1_type = $dynamicData[dataSource];
        $dynamic_1_value = $_GET[$dynamic_1_type];

        // echo ("DYNAMIC dyOn = $dynamic_1 dyType = $dynamic_1_type dyVal = $dynamic_1_value <br>");
        
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
            $sizeList[iPhone] = array("portrait"=>320,"landscape"=>480);
            $sizeList[iPad]   = array("portrait"=>1024,"landscape"=>768);

            $target_target = $this->session_get(target_target);

            if ($target_target == "mobil") {
                $defaultTarget = "iPhone";
                // echo (" Target is $target_target defined by SiteBar $defaultTarget <br>");
                $target_target = $defaultTarget;
            }

            if ($target_target != "pc") {
                $target_orientation = $this->session_get(target_orientation);
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

function pageClass_class() {
    global $pageClass;
    if (is_object($pageClass)) return $pageClass;
    
    $pageClass = new cmsClass_page_content;
    $pageClass->init_page();
    return $pageClass;
}

function pageClass_setValue($key,$value=null) {
    $pageClass = pageClass_class();
    $pageClass->page_setValue($key,$value);
}

?>