<?php // charset:UTF-8
class cmsType_footer_base extends cmsClass_content_show {

    function getName() {
        return "Fußzeile";
    }
    
    function contentType_init() {
        $str = "";
        
        $this->socialClass = cmsType_social_Class();
        if (!is_object($this->socialClass)) $str .= " NO TEXT Class ";
        else $this->socialClass->setMainClass($this);
    }
    
    
    function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;
        
        $pageInfo = $GLOBALS[pageInfo];

        div_start("footer","width:auto;");
        
        $showEdit = 0;
        if ($this->userLevel>6) $showEdit = 1;
        
        if ($showEdit == 0) {
            $thisPageData = $GLOBALS[pageData];
            $pageShowLevel = $thisPageData[showLevel];
            if ($pageShowLevel == 3) { // Special User Mode
                $myId = $this->session_get(userId);
                $allowedUser = $thisPageData[data][allowedUser];
                if ($allowedUser AND $myId) {
                    // echo ("ALLOWED USER ='$allowedUser' $myId <br>");
                    $userPos = strpos($allowedUser,"|$myId|");
                    if (is_int($userPos)) {
                        $showEdit = 1;
                    }
                }
            }                
        }
        
        
        
       // echo ("<h1>FOOTER</h1>");
     
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        $myShowLevel = $this->showLevel;
        if (!$myShowLevel) $myShowLevel = 0;
        // echo ("myShowLevel $myShowLevel<br />" );
        // foreach ($data as $key => $value) echo ("$key = $value <br />");
        $actPage = page::actPage();
        if ($data[kontakt]) {
            $pageName = "kontakt";
            $pageData = page::data_byName($pageName);
            $showName = lg::lgStr($pageData[title]);
            if (!$pageData[hidden]) {
                $class = "footerLink";
                if ($actPage == $pageName) $class .= " footerActive";
                echo ("<a href='".$pageData[name].".php' class='$class' >$showName</a> ");                  
            }          
        }

        if ($data[sitemap]) {
            $pageName = "sitemap";
            $pageData = page::data_byName($pageName);
            $showName = lg::lgStr($pageData[title]);
            if (!$pageData[hidden]) {
                $class = "footerLink";
                if ($actPage == $pageName) $class .= " footerActive";
                echo ("<a href='".$pageData[name].".php' class='$class' >$showName</a> ");            
            }           
        }

        if ($data[impressum]) {
            $pageName = "impressum";
            $pageData = page::data_byName($pageName);
            $showName = lg::lgStr($pageData[title]);
            if (!$pageData[hidden]) {
                $class = "footerLink";
                if ($actPage == $pageName) $class .= " footerActive";
                echo ("<a href='".$pageData[name].".php' class='$class' >$showName</a> ");            
            }
        }

       
        if ($this->pageEditAble) {
            switch ($this->editMode) {
                case "onPage" :
                    if ($this->edit==1) {
                        $goPage = cms_page_goPage("edit=0");
                        echo (" | <a href='$goPage'>editiern stoppen!</a>");
                    } else {
                        $goPage = cms_page_goPage("edit=1");
                        if ($_GET[editLayout]) $goPage.="&editLayout=".$_GET[editLayout];
                        echo (" | <a href='$goPage'>editiern</a> ");
                    }

                    $showLevel = $this->showLevel;
                    $userLevel = $this->userLevel;

                    if ($userLevel > 6) {    
                        //echo ("showLevel = $showLevel / UserLevel = $userLevel ");
                        echo ("<form method='post' style='display:inline-block;' >");

                        echo (" anzeigen:");
                        $showData = array();
                        $showData[submit] = 1;
                        echo (cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort));

                        // echo ("Seite anzeigen als: ".cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
                        echo ("</form>");
                    }
                    break;
            }
        }

        $social = $data[social];
        if ($social) $this->show_social($contentData,$frameWidth);
        if ($this->showLevel>8) {
            $class = "footerLink";
            // if ($pageInfo[pageName] == "impressum") $class .= " footerActive";
            // echo (" uL=$this->userLevel sL=$this->showLevel");
            echo ("<a href='../index.php' class='$class' >CMS-Page</a>");
        }
        div_end("footer","before");
    }

    function show_social($contentData,$frameWidth) {
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        $advice = $data[advise];
        $advice_icon = $data[adviseIcon];
        
        $facebook = $data[facebook];
        $facebook_icon = $data[facebookIcon];
        
        $twitter = $data[twitter];
        $twitter_icon = $data[twitterIcon];
        
        $google = $data[googlePlus];
        $googleIcon = $data[googlePlusIcon];
        
        $rss = $data[rss];
        $rssIcon = $data[rssIcon];
        
        $youtube = $data[youtube];
        $youtubeIcon = $data[youtubeIcon];
        
        div_start("socialMedia");
        // show_array($data);
       //  echo ("Social");
        $wireframeState = $this->wireframeState;
        
        $this->socialClass->contentType_show();
        
//        // echo ("SocialClass =$socialClass <br />");
//        $this->socialClass->showSocial("advise", $contentData, $width);
//        $this->socialClass->showSocial("facebook", $contentData, $width);
//        $this->socialClass->showSocial("twitter", $contentData, $width);
//        $this->socialClass->showSocial("googlePlus", $contentData, $width);
//        $this->socialClass->showSocial("youtube", $contentData, $width);
//        $this->socialClass->showSocial("rss", $contentData, $width);
        
        div_end("socialMedia","before");
    }    
        
        
        
    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
       //  foreach ($editContent[data] as $key => $value ) echo ("editCont $key = $value <br />");
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();
        $res = array();
        $res[footer][showName] = $this->lga("content","footerTab");
        $res[footer][showTab] = "Simple";
        $textType = "contentType_".$this->contentType;
        // MainData
        $addData = array();
        $addData["text"] = $this->lga($textType,"showContact"); //"Kontakt";
        $input = "<input type='checkbox' name='editContent[data][kontakt]' value='1' ";
        if ($editContent[data][kontakt]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[footer][] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = $this->lga($textType,"showSitemap"); //"Sitemap";
        $input = "<input type='checkbox' name='editContent[data][sitemap]' value='1' ";
        if ($editContent[data][sitemap]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[footer][] = $addData;

        // MainData
        $addData = array();
        $addData["text"] = $this->lga($textType,"showImpressum"); //"Impressum";
        $input = "<input type='checkbox' name='editContent[data][impressum]' value='1' ";
        if ($editContent[data][impressum]) $input .= "checked='checked'";
        $input .= " />\n";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[footer][] = $addData;
        
        // Social Media
        $addData = array();
        $addData["text"] = $this->lga($textType,"showSocial"); //"Social Media";
        $social = $data[social];
        if ($social) $checked = "checked='schecked'";
        else $checked = "";
        $input = "<input type='checkbox' name='editContent[data][social]' value='1' $checked />";
        $addData["input"] = $input;
        $addData["mode"] = "Simple";
        $res[footer][] = $addData;
        
        $res[social] = $this->edit_social($editContent,$frameWidth);
        
        
        return $res;
    }
    
    
    
    function edit_social($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();
        
       // $this->socialClass = cmsType_social_class();
        if (is_object($this->socialClass)) {
            $this->socialClass->setMainClass($this);
            $res = $this->socialClass->contentType_editContent();        
        }
        // $res = $socialClass->editContent($editContent);
        return $res;
        
    }
}

function cmsType_footer_class() {
    if ($GLOBALS[cmsTypes]["cmsType_footer.php"] == "own") $footerClass = new cmsType_footer();
    else $footerClass = new cmsType_footer_base();

    return $footerClass;
}


function cmsType_footer($contentData,$frameWidth) {
    $footerClass = cmsType_footer_class();
    $footerClass->init_content($contentData,$frameWidth);// cmsType_footer_class();

    return $footerClass->contentType_show();
}





function cmsType_footer_editContent($editContent,$frameWidth) {
    $footerClass = cmsType_footer_class();
    $footerClass->init_content($contentData,$frameWidth);

    return $footerClass->contentType_editContent();
}


?>
