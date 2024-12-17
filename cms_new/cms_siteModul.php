<?php
class cmsSiteModul {
    
    
    function init($pageClass) {
        $this->pageClass = $pageClass;
        
    }
    
    function session_get($key) {
        return session::get($key);        
    }
    
    function session_set($key,$value) {
        session::set($key,$value);        
    }
    
    
    function show() {
        $out = "";
        echo ("<div class='cmsEditModulFrame'>");
       
        $pageWidth = $GLOBALS[cmsSettings][width];
        $out .= "<div class='cmsModulFrame'>"; //  style='left:".($pageWidth+20)."px;'>";
        // foreach ($GLOBALS[cmsSettings] as $key => $value) $out.= "$key = $value <br>";
        
        
        $edit = $this->session_get(edit); // $_SESSION[edit];
        
        
       // if ($_SESSION["showModul"]) {
        $editState = $this->session_get(showModul); // $_SESSION[showModul];
        // $out .= "ses $editState <br>";
        if (!$editState) $editState = "settings";
        
        
        $mainDiv = "cmsEditBox cmsSiteBarBox";
        if ($edit) $mainDiv .= " cmsSiteBarBox_active";
        // Header
        $out .= "<div class='$mainDiv' style=''>";
        
        
        
        $editClass = "cmsModulEditState";
        if ($edit) {
            $editClass .= " cmsEditOn";
            $title = "Editieren ausschalten";
        }
        else {
            $editClass .= " cmsEditOff";
            $title = "Editieren einschalten";
        }
        $out.= "<div class='$editClass' title='$title' >";
        //if ($edit) $out .= "<img class='cmsEditStateImage' src='/cms_base/cmsImages/cmsEditOn.png' border='0px'>";
        // else $out .= "<img class='cmsEditStateImage' src='/cms_base/cmsImages/cmsEditOff.png' border='0px'>";
        $out .= "</div>";
        
        
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";
        
        // userMode 
        
        $editMode = $this->session_get(editMode); // $_SESSION[editMode];
        if (!$editMode) {
            $editMode = "Simple";
            $this->session_set(editMode,$editMode);
            // $_SESSION[editMode] = $editMode;
        }
        
        $showEditMode = 0;
        if ($showEditMode) {
            $editModulClass = "cmsModulUserEditMode cmsEditMode_$editMode $addEditClass";
            $out.= "<div class='$editModulClass' >";
            $out .= "</div>";
        }
        
        
        
        // Modul ADD
        $divModul = "cmsModulAdd $addEditClass";
        if ($editState == "modul") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Button Image
        $divModul = "cmsImageAdd $addEditClass";
        if ($editState == "image") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        
        $divModul = "cmsColorAdd $addEditClass";
        if ($editState == "color") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Settings
        $divModul = "cmsModulSettings $addEditClass";
        if ($editState == "settings") $divModul .= " cmsModulSelect";
        $out.= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Sitemap 
        $divModul = "cmsModulSitemap $addEditClass";
        if ($editState == "sitemap") $divModul .= " cmsModulSelect";
        $out.= "<div class='$divModul' >";
        $out .= "</div>";
        
        
        
        $putInSide = 1;
        
        if (!$putInSide) $out .= "</div>";

        // GETMODULE
        $dontShow = array("page"=>1,"not"=>1);
        $editLayout = $_GET[editLayout];
        if ($editLayout) $dontShow[page]=0;
        
        $hidden = ($editState != "modul");
        $out .= cms_layout_editModul($dontShow,$hidden,$addEditClass);

        // CLOSE FRAMES

        // Images Frame 
        $dontShow = array("page"=>1,"not"=>1);
        $hidden = 1;
        if ($editState == "image") $hidden = 0;
        $out .= cms_image_editImage($dontShow,$hidden,$addEditClass);
        
        // Color Frame
        $hidden = 1;
        if ($editState == "color") $hidden = 0;
        $out .= cms_layout_editColor($dontShow,$hidden);
        
        // Setting Frame
        $hidden = 1;
        if ($editState == "settings") $hidden = 0;
        $out .= $this->modul_settings($dontShow,$hidden,$addEditClass);
        
        
        // Sitemap Frame
        $hidden = 1;
        if ($editState == "sitemap") $hidden = 0;
        $out .= $this->modul_siteMap($dontShow,$hidden,$addEditClass);
        
        if ($putInSide) $out .= "</div>";
        
        $out .= "</div>";
        
        $out .= "</div>";
     
        echo ($out);      
    }
    
    
    function modul_settings($dontShow,$hidden=1,$addEditClass="") {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";

       
        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulSettingsFrame $hidden $addEditClass","");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Darstellung";
        $out .= div_end_str("cmsModulContentHead");
        
        
        $out .= span_text_str("Editier-Level:",80);
        $editMode = $this->session_get(editMode);
        
        $list = array("Simple"=>"Einfach","More"=>"Erweitert","Admin"=>"Experte");
        
        foreach ($list as $key => $value) {
            $divClass = "cmsSelectEditMode";
            $divId = "setEditMode_$key";
            //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
            if ($key==$editMode) {
                //$style.="background-color:#ff0;";
                $divClass .= " cmsSelectEditModeSelected";
            }
            $out .= "<div class='$divClass' id='$divId' style='$style'>";
            $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsUser".$key.".png'><br/>";
            $out .= "$value";
            $out .= "</div>";
            
        }
        $out .= "<br />";
        
        $showLevel = $this->pageClass->session_get("showLevel"); // $_SESSION[showLevel];
        $userLevel = $this->pageClass->session_get("userLevel"); // $_SESSION[userLevel];
        if ($userLevel > 6) {
            $out .= "sL = $showLevel uL = $userLevel <br>";
            $out .= span_text_str("Anzeigen als:",80);
            
            $showData = array();
            // $showData[submit] = 1;
            $showData[id] = "setUserShowLevel";
            $showData[width] = 156;
            $showData[maxLevel] = $userLevel;
            
            $out .= cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort);

            // echo ("Seite anzeigen als: ".cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
            // echo ("</form>");
            $out .= "<br />";
        }
        
        $editClass = cms_contentTypes_class();
        $languageList = cms_text_getSettings();
        if (count($languageList)) {
            $out.= "Sprache:<br/ >";
            $out .= span_text_str("editieren:",80);
            $out .= $editClass->editContent_languageSelect($languageList,"Edit")."<br />";
            
            $out .= span_text_str("anzeigen:",80);
            $out .= $editClass->editContent_languageSelect($languageList,"Show")."<br />";
            
            
          
            
            
        }
        
        $out .= span_text_str("fehlende :",80);
        $lgMissing = $this->session_get("lgMissing");
        if ($lgMissing) $checked="checked='checked'"; else $checked = "";
        $out .= "<input type='checkbox' $checked value='1' name='cmsLgMissing' class='cmsLgMissing' /><br />";
        $out .= "&nbsp;<br />";
        
        
       
            
        
        
        $mobilPages = $this->session_get("cmsSettings,mobilPages");
        if ($mobilPages) {
            $out .= span_text_str("Ausgabe auf:",80);
        
            $showTarget = $this->session_get(target_target); // $_SESSION[target_target];
            if (!$showTarget) {
                $showTarget = "pc";
                $this->session_set(target_target, $showTarget);
                // $_SESSION[target_target] = "pc";
                // $showTarget = $this->session_get("target_target];
                $out .= "<h1>SET DEFAULTTARGET TO $showTarget </h1>";
            }
            $list = array("pc"=>"Rechner","mobil"=>"Mobil"); //,"MobilLand"=>"quer");
            foreach ($list as $key => $value) {
                $divClass = "cmsSelectTarget";
                $divId = "setTarget_$key";
                //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
                if ($key==$showTarget) {
                    //$style.="background-color:#ff0;";
                    $divClass .= " cmsSelectTargetSelected";
                }
                $out .= "<div class='$divClass' id='$divId' style='$style'>";
                $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsTarget_".$key.".png'><br/>";
                $out .= "$value";
                $out .= "</div>";

            }
            $out .= "<br />";
         
           
            $directionClass = "cmsDirectionFrame cmsDirection_$showTarget";
            //  if ($showTarget != "pc") {
                $out .= "<div class = '$directionClass' >";
                $out .= span_text_str("Ausrichtung:",80);


              
                $showDirection = $this->session_get(target_orientation);
                if (!$showDirection) {
                    $showDirection = "landscape";
                    $this->session_set(target_orientation,$showDirection);
                   
                    // $showDirection = $this->session_get("target_orientation];
                }
           
                // $out .= "MOBILE DIRECTION $showDirection $showTarget<br/>";
                $list = array("portrait"=>"Hoch","landscape"=>"Quer"); //,"MobilLand"=>"quer");
                foreach ($list as $key => $value) {
                    $divClass = "cmsSelectDirection";
                    $divId = "setDirection_$key";
                    //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
                    if ($key==$showDirection) {
                        //$style.="background-color:#ff0;";
                        $divClass .= " cmsSelectDirectionSelected";
                    }
                    $out .= "<div class='$divClass' id='$divId' style='$style'>";
                    $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsDirection_".$key.".png'><br/>";
                    $out .= "$value";
                    $out .= "</div>";

                }
            // }
            // $out .= "<br />";
            $out .= "</div>";
            
            
            
            
        }
        $out .= div_end_str("cmsModulSettingsFrame $hidden $addEditClass");
        return $out;
        
        
    }
    
    function modul_siteMap($dontShow,$hidden=1,$addEditClass="") {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";
       
        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulSitemapFrame $hidden $addEditClass","height:auto;");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Sitemap";
        $out .= div_start_str("cmsSitemap_reset cmsLinkButton","float:right;");
        $out .= "reset";
        $out .= div_end_str("cmsSitemap_reset cmsLinkButton");
        $forNavi=0;
        
        $pageList = page::groupList();
        
        
        $out .= div_end_str("cmsModulContentHead");
        
        $out .= div_start_str("cmsModulSitemapScroll");
        $out .= $this->modul_siteMap_pages($pageList,0);
        $out .= div_end_str("cmsModulSitemapScroll");
        
        $out .= div_end_str("cmsModulSitemapFrame $hidden $addEditClass");
        return $out;
    }
    
    
    function modul_siteMap_pages($pageList,$level) {
        $out = "";
        
        $showOnlyNavi = 1;
        foreach ($pageList as $pageName => $pageData) {
            $pageTitle = cms_text_getLg($pageData[title]);
            if (!$pageTitle) $pageTitle = $pageName;
            
            $show = 1;
            $hidden = 0;
            $navi = $pageData[navigation];
            if ($showOnlyNavi AND !$navi) $show = 0;
            
            
            if ($show) {
                $link = $pageName.".php";
                
                $subNavi = $pageData[subNavi];
                $hasSubNavi = is_array($subNavi);
                
                $select = $pageData[select];
                $subSelect = $pageData[subSelect];
                
                if ($hasSubNavi) {
                    if ($level > 0) $hidden = 1;
                    if ($pageName == "admin") $hidden = 1;
                }
                
                if ($hidden) {
                    if ($select) $hidden = 0;
                    if ($subSelect) $hidden = 0;
                }
                
                
                $out .= "<div class='cmsModulSiteMap_page cmsModulSiteMap_level_$level'>";
                if ($hasSubNavi) {
                    if ($hidden) {
                        $out .= "<div class='cmsModulSiteMap_toggleHidden'>+</div>";
                    } else {
                        $out .= "<div class='cmsModulSiteMap_toggleHidden'>-</div>";
                    }
                }
                $linkClass = "cmsModulSiteMap_link";
                if ($select == "select") $linkClass .= " cmsModulSiteMap_select";
                if ($select == "subSelect") $linkClass .= " cmsModulSiteMap_subSelect";
                // if ($subSelect) $linkClass .= " cmsModulSiteMap_subSelect";
                
                
                $out .= "<a class='$linkClass' href='$link' >$pageTitle</a>";
                //$out .= "</div>";
                
                if ($hasSubNavi) {
                    $listDivName = "cmsModulSiteMap_list";
                    if ($hidden) $listDivName .= " cmsModulSiteMap_listHidden";
                    
                    $out .= "<div class='$listDivName'>";
                    $out .= cms_Sitemap_showPages($subNavi, $level+1);
                    $out .= "</div>";
                }
                $out .= "<div style='clear:both;'></div>";
                $out .= "</div>\n";
                
                
                
                
//                foreach ($pageData as $key => $value ) {
//                    if (is_array($value) ) $out .= " - $key = $value <br />";
//                }
            }
        }
        return $out;
    }
    
    
}

function cmsSiteModul_class() {
    $siteModulClass = new cmsSiteModul();
    return $siteModulClass;
}

    function cmsModul_show($pageClass=0) {
        
        $siteModulClass = cmsSiteModul_class();
        if (is_object($siteModulClass)) {
            if (is_object($pageClass)) {
                $siteModulClass->init($pageClass);
            }
            $siteModulClass->show();
            return 1;
        }
        
        $out = "";
        echo ("<div class='cmsEditModulFrame'>");
       
        $pageWidth = $GLOBALS[cmsSettings][width];
        $out .= "<div class='cmsModulFrame'>"; //  style='left:".($pageWidth+20)."px;'>";
        // foreach ($GLOBALS[cmsSettings] as $key => $value) $out.= "$key = $value <br>";
        
        
        $edit = $_SESSION[edit];
        
        $editState = "_settings";
        if ($_SESSION["showModul"]) {
            $editState = $_SESSION[showModul];
            // $out .= "ses $editState <br>";
        }
        
        
        $mainDiv = "cmsEditBox cmsSiteBarBox";
        if ($edit) $mainDiv .= " cmsSiteBarBox_active";
        // Header
        $out .= "<div class='$mainDiv' style=''>";
        
        
        
        $editClass = "cmsModulEditState";
        if ($edit) {
            $editClass .= " cmsEditOn";
            $title = "Editieren ausschalten";
        }
        else {
            $editClass .= " cmsEditOff";
            $title = "Editieren einschalten";
        }
        $out.= "<div class='$editClass' title='$title' >";
        //if ($edit) $out .= "<img class='cmsEditStateImage' src='/cms_base/cmsImages/cmsEditOn.png' border='0px'>";
        // else $out .= "<img class='cmsEditStateImage' src='/cms_base/cmsImages/cmsEditOff.png' border='0px'>";
        $out .= "</div>";
        
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";
        
        // userMode 
        
        $editMode = $_SESSION[editMode];
        if (!$editMode) {
            $editMode = "Simple";
            $_SESSION[editMode] = $editMode;
        }
        
        $showEditMode = 0;
        if ($showEditMode) {
            $editModulClass = "cmsModulUserEditMode cmsEditMode_$editMode $addEditClass";
            $out.= "<div class='$editModulClass' >";
            $out .= "</div>";
        }
        
        
        
        // Modul ADD
        $divModul = "cmsModulAdd $addEditClass";
        if ($editState == "modul") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Button Image
        $divModul = "cmsImageAdd $addEditClass";
        if ($editState == "image") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        
        $divModul = "cmsColorAdd $addEditClass";
        if ($editState == "color") $divModul .= " cmsModulSelect";
        $out .= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Settings
        $divModul = "cmsModulSettings $addEditClass";
        if ($editState == "settings") $divModul .= " cmsModulSelect";
        $out.= "<div class='$divModul' >";
        $out .= "</div>";
        
        // Sitemap 
        $divModul = "cmsModulSitemap $addEditClass";
        if ($editState == "sitemap") $divModul .= " cmsModulSelect";
        $out.= "<div class='$divModul' >";
        $out .= "</div>";
        
        
        
        $putInSide = 1;
        
        if (!$putInSide) $out .= "</div>";

        // GETMODULE
        $dontShow = array("page"=>1,"not"=>1);
        $editLayout = $_GET[editLayout];
        if ($editLayout) $dontShow[page]=0;
        
        $hidden = ($editState != "modul");
        $out .= cms_layout_editModul($dontShow,$hidden,$addEditClass);

        // CLOSE FRAMES

        // Images Frame 
        $dontShow = array("page"=>1,"not"=>1);
        $hidden = 1;
        if ($editState == "image") $hidden = 0;
        $out .= cms_image_editImage($dontShow,$hidden,$addEditClass);
        
        // Color Frame
        $hidden = 1;
        if ($editState == "color") $hidden = 0;
        $out .= cms_layout_editColor($dontShow,$hidden);
        
        // Setting Frame
        $hidden = 1;
        if ($editState == "settings") $hidden = 0;
        $out .= cms_Settings_showModul($dontShow,$hidden,$addEditClass);
        
        
        // Sitemap Frame
        $hidden = 1;
        if ($editState == "sitemap") $hidden = 0;
        $out .= cms_Sitemap_showModul($dontShow,$hidden,$addEditClass);
        
        
        
        
        
        // COLORS         
//        $out .= "<div class='cmsModulColorFrame cmsModul_hidden' >";
//        $out .= "<div class='cmsModulContentHead'>Farben</div>";
//        // $out .= "<input type='text' value='' name='filterImage' style='width:100%;' /><br/>";
//
//        $out .= "<div class='cmsModulColorScroll ' >"; //dragImageFrame
        
//        $out .= "</div>";
//
//        $out.= "</div>";
//        

        if ($putInSide) $out .= "</div>";
        
        $out .= "</div>";
        
        $out .= "</div>";
        
        
        

        echo ($out);              
    }
    
    
    function cms_image_editImage($dontShow,$hidden=1,$addEditClass="") {
        // IMAGES 
        
        $showImage = 0;
        
        if ($hidden == 0) $showImage = 1;
        $showNewFolder = 0;
        $showUpload = 0; 
        
        $out = ""; // Hidden = $hidden / $showImage <br>";
        
        $res = cmsModul_newFolder();
        if (is_array($res)) {
            $newFolderOut = $res[out];
            if ($res[imageFolder]) $imageFolder = $res[imageFolder];
            if ($res[showImage]) $showImage = $res[showImage];
            $showNewFolder = $res[showNewFolder];
        }    
        
        $res = cmsModul_imageUpload($imageFolder,"cmsMmodul");
        if (is_array($res)) {
            $uploadOut = $res[out];
            if ($res[imageFolder]) $imageFolder = $res[imageFolder];
            if ($res[showImage]) $showImage = $res[showImage];
            $showUpload = $res[showUpload];
        }
        
        $imageDiv = "cmsModulImageFrame";
        if ($showImage) {
            if (!$imageFolder) $imageFolder = "images/";
        } else { 
            $imageDiv .= " cmsModul_hidden";
        }
        $imageDiv .= " $addEditClass";

        $out .= "<div class='$imageDiv ' >";
        
        // Headline
        $out .= "<div class='cmsModulContentHead '>Bilder</div>";
        
        // Aktueller Ordner
        $out .= "Ordner:";
        $out .= "<div class='cmsModulFolder'>images/</div><br />";
    
        // Button New Folder
        $out .= "<div class='cmsModulNewFolder'>new Folder</div>";
    
        // Button Upload Image
        $out .= "<div class='cmsModulUploadImage'>upload Image</div>";
        $out .= "<br>";
        
        
        // new Folder
        $folderDiv = "cmsModulImageFolderInput";
        if (!$showNewFolder) $folderDiv .= " cmsModulHidden";
        $out .= "<div class='$folderDiv' >"; //dragImageFrame
        $out .= $newFolderOut;
        $out .= "</div>";
        
        // upload Image
        $uploadDiv = "cmsModulImageUploadInput";
        if (!$showUpload) $uploadDiv .= " cmsModulHidden";
        $out .= "<div class='$uploadDiv' >"; //dragImageFrame
        $out .= $uploadOut;
        $out .= "</div>";
        
        $out .= "<div class='cmsModulImageScroll ' >"; //dragImageFrame
        if ($imageFolder) {
            $imgList = cmsImage_SelectList_getContent($imageFolder);
            $out .= $imgList;
        }        
        $out .= "</div>";
        
        $out.= "</div>";

        
        return $out;
    }
    
    
    function cmsModul_imageUpload($mainFolder="images/",$target="cmsModul") {
        $out = "";
        
        $showForm = 1;
        $showImage = 0;
        $showUpload = 0;
        
        $uploadFile = $_POST[uploadFile];
        if (is_array($uploadFile)) {
            $showImage = 1;
            $showUpload = 1;
            $mainFolder = $uploadFile[mainFolder];
            $upload = $uploadFile[upload];
            
            if (isset($_FILES) AND $mainFolder AND $upload) {
                $imageFolder = $mainFolder;
                if ($_FILES[uploadFile][name]) {
                    // $out .= "UPLOAD ".$_FILES[uploadFile][name]."<br>";
                    $showInfo = 0;
                    $imageId = cmsImage_upload_File($_FILES[uploadFile],$mainFolder,$showInfo);
                    $imageId = intval($imageId);
                    if ($imageId) {
                        $imageData = cmsImage_getData_by_Id($imageId);
                        $width = 80;
                        $imgStr = cmsImage_showImage($imageData,$width,array("createSmall"=>1));
                       
                        $out .= div_start_str("cmsImageSelectFrame",$divData); //"background-color:#ccc;padding:3px;margin-right:3px;margin-bottom:3px;width:".$width."px;height:".$width."px;float:left;");
                        $out .= "<h3>Bild hochgeladen</h3>";
                        $out .= $imgStr;
                        // $out .="$imageData[fileName]";
                        $out .= div_end_str("cmsImageSelectFrame");
                        $showForm = 0;
                        $goPage = $_SERVER[REDIRECT_URL]."?".$_SERVER[REDIRECT_QUERY_STRING];
                        // $out .= "go '$goPage' <br>";
                        reloadPage($goPage,1);
                        
                    } else {
                        $out .= "Fehler beim Upload $imageId<br/>";
                    }
                }
            } else {
                if (!$mainFolder) $out .= "Kein Ordner ausgewählt<br/>";
            }
        }
        
        if ($showForm) {
            $out .= "<form id='fileupload' action='' method='POST' enctype='multipart/form-data'>";
            $out .= "<input type='text' class='cmsModulImage_mainFolder' readonly='readonly' value='$mainFolder' name='uploadFile[mainFolder]' /><br />";
            $out .= "<input type='text' class='cmsModulImage_target' readonly='readonly' value='$target' name='uploadFile[target]' /><br />";
            $out .= "<div class='cmsModulImage_dragFrame'>";
            $out .= "Drag<br />or<br />Click";
            $out .= "</div>";

            $out .= "<div class='cmsModulImage_loading cmsModulHidden'>";
            $out .= "</div>";
            $out .= "<div class='cmsModulImage_dragButton'>";
            $out .= "<input type='file' name='uploadFile' accept='text/*' class='cmsModulImage_dragInput' size='1'>";
            $out .= "</div>";
            $out .= "<input type='submit' class='cmsModulImage_uploadButton' name='uploadFile[upload]' value='hochladen' />";
            // $out .= "<br />";

            $out .= "<div class='cmsModulImage_dragOutput'></div>";

            $out .= "</form>";     
            
        }
        
        
        $res = array();
        $res[out] = $out;
        $res[imageFolder] = $imageFolder;
        $res[showImage] = $showImage;
        $res[showUpload] = $showUpload;
        return $res;
    }
    
    function cmsModul_newFolder() {
        $out = "";
        
        $showImage = 0;
        $showNewFolder = 0;
        $showForm = 1;
        
        $newFolder = $_POST[newFolder];
        if (is_array($newFolder)) {
            $showImage = 1;
            $showNewFolder = 1;
            $newName = $newFolder[newName];
            $mainFolder =$newFolder[mainFolder];                     
            $imageFolder = $mainFolder;
            if ($newName AND $mainFolder) {
                $res = cmsImage_createFolder($mainFolder,$newName);
                if ($res) {
                    $out .= "<h3>Neuer Ordner angelegt!</h3>";
                    $out .= "<b>$newName</b>";
                    $showForm = 0;
                    $goPage = $_SERVER[REDIRECT_URL]."?".$_SERVER[REDIRECT_QUERY_STRING];
                    reloadPage($goPage,1);
                } else {
                    $out .= "Fehler beim Ordner anlegen<br/>";
                }
            }      
            
        }
        
        if ($showForm) {
            $out .= "Neuer Ordner anlegen in:";
            $out .= "<form method='post' >";
            $out .= "<input type='text' class='newImageFolder_mainFolder' readonly='readonly' value='$mainFolder' name='newFolder[mainFolder]' /><br />";
            $out .= "<input type='text' class='newImageFolder_newName' value='$newName' name='newFolder[newName]' /><br />";
            $out .= "<input type='submit' class='cmsInputButton'  value='anlegen' name='newFolder[create]' />";
            $out .= "<input type='submit' class='cmsJavaButton cmsSecond newImageFolder_cancel' value='abbrechen' name='newFolder[cancel]' />";        
            $out .= "</form>";
        }
        
        $res = array();
        $res[out] = $out;
        $res[imageFolder] = $imageFolder;
        $res[showImage] = $showImage;
        $res[showNewFolder] = $showNewFolder;
        return $res;
        
    }
       
       
    
    
    function cms_layout_editModul($dontShow=array(),$hidden=1,$addEditClass="") {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";

        // $editLayout = $_GET[editLayout];
       
        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulContentFrame $hidden $addEditClass","");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Module";
        $out .= div_end_str("cmsModulContentHead");
        $typeList = cms_contentType_getSortetList(1);
        foreach($dontShow as $key => $value) {
            if ($value == 1) unset($typeList[$key]);
        }
        
        $out .= div_start_str("cmsModulList");
        // if (!$showPage) unset($typeList[page]);
        // unset($typeList[not]);
        foreach ($typeList as $key => $value ) {
            switch ($key) {
                 case ("data2") :
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $frameAdd = "cmsModulCategoryFrameHidden";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCatList_$key' >";
                     foreach ($value as $dataType => $dataValue) {
                         
                        // class="cmsModulContentCategory cmsModulContentCategorySecond"
                         $showTypeList = 0;
                         if ($showTypeList) {
                            $out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                            $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  $frameAdd cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         } else {
                            //$out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                            // $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  $frameAdd cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         }
                         foreach ($dataValue as $type => $typeValue) {
                             
                             switch ($mode) {
                                case "sort" :
                                   $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                                   $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                   break;
                                case "drag" :
                                    $style = "width:48px;height:48px;margin-right:1px;margin-bottom:1px;display:inline-block;";
                                    $out .= "<div class='cmsModulContentButton dragNewModul' id='cmsDragModul_$type' style='$style'>";
                                    $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                    break;

                            }
                        
                             $out .= "&nbsp; ".$typeValue[name]." - $mode";
                             $out .= "</div>";
                         }
                         if ($showTypeList) {
                             $out .= "</div>";
                         }
                         
                     }
                     $out .= "</div>";
                     break;
                 default:
                     $frameAdd = "";
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key' >$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCatList_$key' >";
                     foreach ($value as $type => $typeValue) {
                         if ($typeValue["use"]) {
                            // $style="margin-left:10px";
                            // cmsContentFrameBox dragBox
                            switch ($mode) {
                                case "sort" :
                                   $out .= "<div class='cmsModulContentButton drageBox'  id='cmsDragModul_$type' style='$style'>";
                                   $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                   break;
                               
                                case "drag" :
                                    $style = "";
                                    $style = "width:48px;height:48px;margin:0 2px 2px 0;display:inline-block;z-index:1000;";
                                    
                                    
                                    $typeImage = "";
                                    $exist = 0;
                                    
                                    $fn = "/cms_".$GLOBALS[cmsVersion]."/cmsImages/sitebar/cmsType_".$type.".png";
                                    if (file_exists($_SERVER[DOCUMENT_ROOT].$fn)) {
                                        $typeImage = $fn;
                                    } else {
                                        // search Image in Own
                                        $fn = "/cms/cms_contentTypes/cmsType_$type.png";
                                        // if ($type == "skCom") echo ("fn1 = $_SERVER[DOCUMENT_ROOT] $fn <br>");
                                        if (file_exists($_SERVER[DOCUMENT_ROOT].$fn)) {
                                            // if ($type == "skCom") echo ("EXIST 1 <br>");
                                            $typeImage = $fn;                                           
                                        } else {
                                            // search in base/contentType/
                                            $fn = "/cms_".$GLOBALS[cmsVersion]."/cms_contentTypes/cmsType_".$type.".png";
                                            // if ($type == "skCom") echo ("fn2 - $fn <br>");
                                            if (file_exists($_SERVER[DOCUMENT_ROOT].$fn)) {
                                                $typeImage = $fn;                                                
                                            } else {
                                                $fn = "/".$GLOBALS[cmsName]."/cms/cms_contentTypes/cmsType_$type.png";
                                                //if ($type == "skCom") echo ("fn3 = $_SERVER[DOCUMENT_ROOT] $fn <br>");
                                                if (file_exists($_SERVER[DOCUMENT_ROOT].$fn)) {
                                                    // if ($type == "skCom") echo ("EXIST 3 <br>");
                                                    $typeImage = $fn;    
                                                }
                                            }
                                                
                                        }
                                    }
                                    if ($typeImage) {
                                        $exist = 1;
                                        $style .="font-size:0px;background-image:url($typeImage);";
                                    }
                                    // $styleButton = "display:inline-block;z-index:1000;";
                                    // $styleButton = "display:inline-block;";
                                    
                                    
                                    $out .= "<div class='cmsModulContentButton dragNewModul' title='$typeValue[name]' id='cmsDragModul_$type' style='$style'>";
                                    //$out .= "<div class='dragButton' style='$styleButton'>";
                                    if ($exist ) {
                                        $out .= "<img class='cmsModulSmallImage' src='$fn' width='24px' height='24px' border='0px'>";
                                        
                                    } else { 
                                        $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'>";
                                    }
                                    //$out .= "</div>";
                                    break;

                            }

                            
                            $out .= "&nbsp;".$typeValue[name];
                            
                           
                            $out .= "</div>";
                        }
                     }
                     $out .= "</div>";
             }
        }

        $out .= div_end_str("cmsModulList");
        
        $out .= div_end_str("cmsModulContentFrame $hidden $addEditClass");
        return $out;
    }
    
    function cms_layout_editModul_old($dontShow=array(),$hidden=1) {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";

        $edit = $_SESSION[edit];
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";

        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulContentFrame $hidden $addEditClass","");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Module";
        $out .= div_end_str("cmsModulContentHead");
        $typeList = cms_contentType_getSortetList();
        foreach($dontShow as $key => $value) {
            if ($value == 1) unset($typeList[$key]);
        }
        $out .= div_start_str("cmsModulList");
        // if (!$showPage) unset($typeList[page]);
        // unset($typeList[not]);
        foreach ($typeList as $key => $value ) {
            switch ($key) {
                 case ("data") :
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCatList_$key' >";
                     foreach ($value as $dataType => $dataValue) {
                         
                        // class="cmsModulContentCategory cmsModulContentCategorySecond"
                         $showTypeList = 0;
                         if ($showTypeList) {
                            $out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                            $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  $frameAdd cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         } else {
                            //$out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                            // $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  $frameAdd cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         }
                         foreach ($dataValue as $type => $typeValue) {
                             
                             switch ($mode) {
                                case "sort" :
                                   $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                                   $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                   break;
                                case "drag" :
                                    $out .= "<div class='cmsModulContentButton dragNewModul' id='cmsDragModul_$type' style='$style'>";
                                    $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                    break;

                            }
                        
                             $out .= "&nbsp; ".$typeValue[name]." - $mode";
                             $out .= "</div>";
                         }
                         if ($showTypeList) {
                             $out .= "</div>";
                         }
                         
                     }
                     $out .= "</div>";
                     break;
                 default:
                     $frameAdd = "";
                     if ($mode == "sort") $frameAdd = "dragFrame";
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key' >$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame $frameAdd cmsModulCat_$key' id='cmsModulCatList_$key' >";
                     foreach ($value as $type => $typeValue) {
                         if ($typeValue["use"]) {
                            // $style="margin-left:10px";
                            // cmsContentFrameBox dragBox
                            switch ($mode) {
                                case "sort" :
                                   $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                                   $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";
                                   break;
                                case "drag" :
                                    // $style="z-index:99";
                                    $style = "";
                                    $styleButton = "display:inline-block;";
                                    // $styleButton = "display:inline-block;";
                                    $out .= "<div class='cmsModulContentButton dragNewModul' id='cmsDragModul_$type' style='$style'>";
                                    // $out .= "<div class='dragButton' style='$styleButton'>";
                                    $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'>";
                                    $out .= "</div>";
                                    break;

                            }


                            $out .= "&nbsp;".$typeValue[name];
                            $out .= "</div>";
                        }
                     }
                     $out .= "</div>";
             }
        }

        $out .= div_end_str("cmsModulList");
        
        $out .= div_end_str("cmsModulContentFrame $hidden $addEditClass");
        return $out;
    }
    
//    function cms_Settings_showModul($dontShow,$hidden=1,$addEditClass="") {
//        $out = "";
//        if ($hidden) $hidden = "cmsModul_hidden";
//        else $hidden = "";
//
//       
//        $mode = "drag"; // sort
//
//        $out .= div_start_str("cmsModulSettingsFrame $hidden $addEditClass","");
//        $out .= div_start_str("cmsModulContentHead");
//        $out .= "Darstellung";
//        $out .= div_end_str("cmsModulContentHead");
//        
//        
//        $out .= span_text_str("Editier-Level:",80);
//        $editMode = $_SESSION[editMode];
//        
//        $list = array("Simple"=>"Einfach","More"=>"Erweitert","Admin"=>"Experte");
//        
//        foreach ($list as $key => $value) {
//            $divClass = "cmsSelectEditMode";
//            $divId = "setEditMode_$key";
//            //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
//            if ($key==$editMode) {
//                //$style.="background-color:#ff0;";
//                $divClass .= " cmsSelectEditModeSelected";
//            }
//            $out .= "<div class='$divClass' id='$divId' style='$style'>";
//            $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsUser".$key.".png'><br/>";
//            $out .= "$value";
//            $out .= "</div>";
//            
//        }
//        $out .= "<br />";
//        
//        $showLevel = $_SESSION[showLevel];
//        $userLevel = $_SESSION[userLevel];
//        if ($userLevel > 6) {
//            $out .= span_text_str("Anzeigen als:",80);
//            $showData = array();
//            // $showData[submit] = 1;
//            $showData[id] = "setUserShowLevel";
//            $showData[width] = 156;
//            $out .= cmsUser_selectUserLevel($showLevel,"setShowLevel",$showData,$showFilter,$showSort);
//
//            // echo ("Seite anzeigen als: ".cms_user_selectlevel($showLevel,$userLevel,"setShowLevel",array("onChange"=>"submit()")));
//            // echo ("</form>");
//            $out .= "<br />";
//        }
//        
//        $editClass = cms_contentTypes_class();
//        $languageList = cms_text_getSettings();
//        if (count($languageList)) {
//            $out.= "Sprache:<br/ >";
//            $out .= span_text_str("editieren:",80);
//            $out .= $editClass->editContent_languageSelect($languageList,"Edit")."<br />";
//            
//            $out .= span_text_str("anzeigen:",80);
//            $out .= $editClass->editContent_languageSelect($languageList,"Show")."<br />";
//            
//            
//            foreach ($languageList as $key => $value) {
//                //$out .= "$key = $value <br>";
//            }
//            $out .= "&nbsp;<br />";
//            
//        }
//        
//       
//            
//        
//        $mobilPages = site_session_get(cmsSettings,mobilPages);
//        if ($mobilPages) {
//            $out .= span_text_str("Ausgabe auf:",80);
//        
//            $showTarget = $_SESSION[target_target];
//            if (!$showTarget) {
//                $_SESSION[target_target] = "pc";
//                $showTarget = $_SESSION[target_target];
//                $out .= "<h1>SET DEFAULTTARGET TO $showTarget </h1>";
//            }
//            $list = array("pc"=>"Rechner","mobil"=>"Mobil"); //,"MobilLand"=>"quer");
//            foreach ($list as $key => $value) {
//                $divClass = "cmsSelectTarget";
//                $divId = "setTarget_$key";
//                //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
//                if ($key==$showTarget) {
//                    //$style.="background-color:#ff0;";
//                    $divClass .= " cmsSelectTargetSelected";
//                }
//                $out .= "<div class='$divClass' id='$divId' style='$style'>";
//                $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsTarget_".$key.".png'><br/>";
//                $out .= "$value";
//                $out .= "</div>";
//
//            }
//            $out .= "<br />";
//         
//           
//            $directionClass = "cmsDirectionFrame cmsDirection_$showTarget";
//            //  if ($showTarget != "pc") {
//                $out .= "<div class = '$directionClass' >";
//                $out .= span_text_str("Ausrichtung:",80);
//
//
//                $showDirection = $_SESSION[target_orientation];
//                if (!$showDirection) {
//                    $_SESSION[target_orientation] = "landscape";
//                    $showDirection = $_SESSION[target_orientation];
//                }
//           
//                // $out .= "MOBILE DIRECTION $showDirection $showTarget<br/>";
//                $list = array("portrait"=>"Hoch","landscape"=>"Quer"); //,"MobilLand"=>"quer");
//                foreach ($list as $key => $value) {
//                    $divClass = "cmsSelectDirection";
//                    $divId = "setDirection_$key";
//                    //$style = "display:inline-block;border:1px solid #ccc;text-align:center;width:50px;";
//                    if ($key==$showDirection) {
//                        //$style.="background-color:#ff0;";
//                        $divClass .= " cmsSelectDirectionSelected";
//                    }
//                    $out .= "<div class='$divClass' id='$divId' style='$style'>";
//                    $out .= "<img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsDirection_".$key.".png'><br/>";
//                    $out .= "$value";
//                    $out .= "</div>";
//
//                }
//            // }
//            // $out .= "<br />";
//            $out .= "</div>";
//            
//            
//            
//            
//        }
//        $out .= div_end_str("cmsModulSettingsFrame $hidden $addEditClass");
//        return $out;
//        
//        
//    }
    
    function cms_Sitemap_showModul($dontShow,$hidden=1,$addEditClass="") {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";
       
        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulSitemapFrame $hidden $addEditClass","height:auto;");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Sitemap";
        $out .= div_start_str("cmsSitemap_reset cmsLinkButton","float:right;");
        $out .= "reset";
        $out .= div_end_str("cmsSitemap_reset cmsLinkButton");
        $forNavi=0;
        $pageList = cms_page_getSortList($forNavi);
        $out .= div_end_str("cmsModulContentHead");
        
        $out .= div_start_str("cmsModulSitemapScroll");
        $out .= cms_Sitemap_showPages($pageList,0);
        $out .= div_end_str("cmsModulSitemapScroll");
        
        $out .= div_end_str("cmsModulSitemapFrame $hidden $addEditClass");
        return $out;
    }
    
    function cms_Sitemap_showPages($pageList,$level) {
        $out = "";
        
        $showOnlyNavi = 1;
        foreach ($pageList as $pageId => $pageData) {
            $pageName = $pageData[name];
            $pageTitle = cms_text_getLg($pageData[title]);
            if (!$pageTitle) $pageTitle = $pageName;
            
            $show = 1;
            $hidden = 0;
            $navi = $pageData[navigation];
            if ($showOnlyNavi AND !$navi) $show = 0;
            
            
            if ($show) {
                $link = $pageName.".php";
                
                $subNavi = $pageData[subNavi];
                $hasSubNavi = is_array($subNavi);
                
                $select = $pageData[select];
                $subSelect = $pageData[subSelect];
                
                if ($hasSubNavi) {
                    if ($level > 0) $hidden = 1;
                    if ($pageName == "admin") $hidden = 1;
                }
                
                if ($hidden) {
                    if ($select) $hidden = 0;
                    if ($subSelect) $hidden = 0;
                }
                
                
                $out .= "<div class='cmsModulSiteMap_page cmsModulSiteMap_level_$level'>";
                if ($hasSubNavi) {
                    if ($hidden) {
                        $out .= "<div class='cmsModulSiteMap_toggleHidden'>+</div>";
                    } else {
                        $out .= "<div class='cmsModulSiteMap_toggleHidden'>-</div>";
                    }
                }
                $linkClass = "cmsModulSiteMap_link";
                if ($select == "select") $linkClass .= " cmsModulSiteMap_select";
                if ($select == "subSelect") $linkClass .= " cmsModulSiteMap_subSelect";
                // if ($subSelect) $linkClass .= " cmsModulSiteMap_subSelect";
                
                
                $out .= "<a class='$linkClass' href='$link' >$pageTitle</a>";
                //$out .= "</div>";
                
                if ($hasSubNavi) {
                    $listDivName = "cmsModulSiteMap_list";
                    if ($hidden) $listDivName .= " cmsModulSiteMap_listHidden";
                    
                    $out .= "<div class='$listDivName'>";
                    $out .= cms_Sitemap_showPages($subNavi, $level+1);
                    $out .= "</div>";
                }
                $out .= "<div style='clear:both;'></div>";
                $out .= "</div>\n";
                
                
                
                
//                foreach ($pageData as $key => $value ) {
//                    if (is_array($value) ) $out .= " - $key = $value <br />";
//                }
            }
        }
        return $out;
    }
    
    function cms_layout_editColor($dontShow=array(),$hidden=1) {
        $out = "";
        if ($hidden) $hidden = "cmsModul_hidden";
        else $hidden = "";

        $edit = $_SESSION[edit];
        $addEditClass = "cmsEditToggle";
        if (!$edit) $addEditClass .= " cmsEditHidden";

        $mode = "drag"; // sort

        $out .= div_start_str("cmsModulColorFrame $hidden $addEditClass","");
        $out .= div_start_str("cmsModulContentHead");
        $out .= "Farben";
        $out .= div_end_str("cmsModulContentHead");
        
        $colorList = cmsStyle_getList(array("type"=>"color"),"id","assoId");

        $fak = 0.05;
        
        // Transparent AND None
        $divName = "modulColorList  dragNewModul ui-draggable";
        $out .= div_start_str($divName,"width:100%;padding:2px;");
            
        $out .= "<div class='dragColor dragColorTransparent' title='Transparent' id='trans|trans|100|0' style=''>&nbsp;</div>";
        $out .= "<div class='dragColor dragColorNone' title='Keine Farbe' id='none|none|100|0' style=''>&nbsp;</div>";
        $out .= div_end_str($divName,"before");

        $out .= div_start_str($divName,"width:100%;padding:2px;");
        $out .= "<div class='dragColor' title='Weiß' id='white|ffffff|100|0' style='background-color:#fff;'>&nbsp;</div>";
        $red = 255;
        $green = 255;
        $blue = 255;
        $add = -12;
        $out .= "<div style='float:left;left;display:block;'>";
        $out .= "Weiß<br />";
        for ($i=1;$i<11;$i++) {
            $red_new = floor($red + ($i*$add));
            $green_new = floor($green + ($i*$add));
            $blue_new = floor($blue + ($i*$add));
                
            $newColor = cmsStyle_rgb2rgb($red_new,$green_new,$blue_new);
            $style = "background-color:#$newColor;";
            $average = floor(($red_new+$green_new+$blue_new) / 3);
            if ($average > 127) $style .= "color:#000;";
            else $style .= "color:#fff;";
            
            $out .= "<div class='dragColor dragColorSmall' title='$newColor' id='white|$newColor|100|$i' style='$style'>$i</div>";
            
        }
        $out .= "</div>";
        $out .= div_end_str($divName,"before");

        $out .= div_start_str($divName,"padding:2px;");
        $out .= "<div class='dragColor' title='Schwarz'  id='black|000000|100|0' style='background-color:#000;'>&nbsp;</div>";
        $red = 0;
        $green = 0;
        $blue = 0;
        $add = 12;
        $out .= "<div style='float:left;left;display:block;'>";
        $out .= "Schwarz<br />";
        for ($i=1;$i<11;$i++) {
            $red_new = floor($red + ($i*$add));
            $green_new = floor($green + ($i*$add));
            $blue_new = floor($blue + ($i*$add));
                
            $newColor = cmsStyle_rgb2rgb($red_new,$green_new,$blue_new);
            $style = "background-color:#$newColor;";
            $average = floor(($red_new+$green_new+$blue_new) / 3);
            if ($average > 127) $style .= "color:#000;";
            else $style .= "color:#fff;";
            
            $out .= "<div class='dragColor dragColorSmall' title ='$newColor' id='black|$newColor|100|$i' style='$style'>$i</div>";
            
        }
        $out .= "</div>";
        $out .= div_end_str($divName,"before");
        

        foreach ($colorList as $colorId => $value) {
            $color = cmsStyle_colorCheck($value[color]);

            $divName = "modulColorList  dragNewModul ui-draggable";
            $out .= div_start_str($divName,"padding:2px;");
            $out .= "<div class='dragColor' id='$colorId|$color|100|0' style='background-color:#$color;'>&nbsp;</div>";
            $out .= "<div style='float:left;left;display:block;'>";
            $out .= "Color $colorId $value[name] $value[color] <br />";
            $hexColor = cmsStyle_hex2rgb($color);
            // show_array($hexColor);

            $red = $hexColor[0];
            $green = $hexColor[1];
            $blue = $hexColor[2];
            $red_fak = 255*$fak; //$red*$fak;
            $green_fak = 255*$fak; //$green*$fak;
            $blue_fak = 255*$fak; //$blue*$fak;
            for ($i=-5;$i<=5;$i++) {
                $red_new = floor($red + ($i*$red_fak));
                $green_new = floor($green + ($i*$green_fak));
                $blue_new = floor($blue + ($i*$blue_fak));
                $newColor = cmsStyle_rgb2rgb($red_new,$green_new,$blue_new);
                $style = "background-color:#$newColor;";
                $average = floor(($red_new+$green_new+$blue_new) / 3);
                if ($average > 127) $style .= "color:#000;";
                else $style .= "color:#fff;";
                
                // $out .= " $r=>$red_new $g $b $color <br>";


                if ($i != 0) {
                    $out .= "<div class='dragColor dragColorSmall' id='$colorId|$newColor|100|$i' style='$style'>$i</div>";
                }
            }


            $out .= "</div>";
            $out .= div_end_str($divName,"before");
        }
       
        $out .= div_end_str("cmsModulColorFrame $hidden $addEditClass");
        return $out;
    }


    
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
