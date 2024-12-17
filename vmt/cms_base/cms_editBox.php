<?php
class editBox {
    function show() {
        $out = "";
        $out .= "<div class='cmsEditFrameBox' style=''>";
        
        $out .= "&nbsp;";
        
        // Site Bar
        $out .= "<div class='cmsEditFrame_SiteBar'>";
        $out .= ">";        
        $out .= "</div>";
        
        
        // Content
        $mainShow = "cms";
        $editMode = $_GET[editMode];
        switch ($editMode) {
            case "pageData" :
                $mainShow = "pageData";
                break;
            case "editContentData" :
                $mainShow = "contentData";
                break;
        }
        $out .= $this->show_content($mainShow);
        
        $out .= "<div style='clear:both'></div>";
        
        $out .= "</div>";
        
        return $out;
        
    }
    
    
    function show_content($mainShow) {
        $out = "";
        
        $class = "cmsEditFrame_Content";
        $open = 1;
        if (!$open) $class .= " cmsEditFrame_content_hidden";
        
        $out .= "<div class='$class'>";
        
        
        $out .= "<div class='cmsEditFrame_myCms'>myCMS</div>";
        
        switch ($mainShow) {
            case "pageData" :
                $out .= $this->content_pageData();
                break;
            
            case "cms" :
                $out .= $this->content_cms();
                break;
            
            case "contentData" :
                $out .= $this->content_contentData();
                break;


            default: 
                $out .= "Unkown ShowMode $mainShow";
                
        }
        
        $out .= "</div>";
        return $out;
        
        $out .= "<span class='cmsEditFrameTabButton'>Einstellungen</span>";
        $out .= "<span class='cmsEditFrameTabButton'>Seite</span>";
        $out .= "</div>";
    }
    
    
    
    function content_cms() {
        $out = "";
        $out .= "<div class='cmsEditFrame_headLine' id='editCMS'>Einstellungen</div>\n";
       
        
        return $out;
    }
    
    function content_mainPage() {
        $out = "";
        $out .= "<div class='cmsEditFrame_myCms'>myCMS</div>";
        
        
        $out .=  "<div class='cmsEditFrame_button' id='editCMS' >Einstellungen</div>\n";
        $out .=  "<div class='cmsEditFrame_button' id='editPage' >Seite</div>\n";

        $out .=  "<div class='cmsEditFrame_button' id='editContent' >Inhalte</div>\n";
        $out .=  "<div class='cmsEditFrame_button' id='editSitemap' >Sitemap</div>\n";
        $out .=  "<div class='cmsEditFrame_button' id='editImages' >Bilder</div>\n";
        $out .=  "<div class='cmsEditFrame_button' id='editData' >Daten</div>\n";

        return $out;
    }
    
    
    function content_pageData() {
        $out = "";     
        $out .= "<div class='cmsEditFrame_headLine' id='editPage'>Seiten-Daten</div>\n"; 
        return $out;        
    }
    
    function content_contentData() {
        $out = "";
        //$out .= "<div class='cmsEditFrame_headLine' id='editSitemap'>Sitemap</div>\n";
        $out .= "<div class='cmsEditFrame_headLine' id='editContent'>Inhalte</div>\n";

        $out .=  "<div class='cmsEditFrame_button' id='editContent_New' >Neue Inhalte</div>\n";





        $editId = $_GET[editId];
        if (!intval($editId)) {
            if (substr($editId,0,13) == "editButtonID_") {
                $editId = substr($editId,13);
            } else {
                $out .= "Unkown Edit Id $editId<br>" ;
            }
        }
        if (intval($editId)) {
            $contentData = cms_content_getId($editId);
            $frameWidth = 270;
            $res = cms_content_edit($contentData,$frameWidth);
        // foreach ($res as $key => $value) $out .= "$key <br>";
        
            $out .= $res[outPut];
        }
        
        
       
        return $out;
        
    }

    function content_newContent() {

        $out = "";
        $out .= "<div class='cmsEditFrame_headLine' id='editContent'>Inhalte</div>\n";

        $dontShow = array("page"=>1,"not"=>1);
        $hidden = 1;
      
        $out .= div_start_str("cmsModulContentFrame $hidden","");
        $typeList = cms_contentType_getSortetList();
        foreach($dontShow as $key => $value) {
            if ($value == 1) unset($typeList[$key]);
        }
        foreach ($typeList as $key => $value ) {
            switch ($key) {
                 case ("data") :
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame dragFrame cmsModulCat_$key' id='cmsModulCat_$key' >";
                     foreach ($value as $dataType => $dataValue) {

                        // class="cmsModulContentCategory cmsModulContentCategorySecond"
                         $out .= "<div class='cmsModulContentCategory cmsModulContentCategorySecond' id='cmsModulCat_$key_$dataType' >$dataType</div>";
                         $out .= "<div class='cmsModulCategoryFrame cmsModulCategoryFrameSecond  dragFrame cmsModulCategoryFrameHidden cmsModulCat_$key_$dataType' id='cmsModulCat_$key_$dataType'  >";
                         foreach ($dataValue as $type => $typeValue) {
                             $out .= "<div class='cmsModulContentButton cmsModulContentButtonSecond dragBox' id='cmsDragModul_data_$type'>";
                             $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";

                             $out .= "&nbsp; ".$typeValue[name];
                             $out .= "</div>";
                         }
                         $out .= "</div>";

                     }
                     $out .= "</div>";
                     break;
                 default:
                     $out .= "<div class='cmsModulContentCategory' id='cmsModulCat_$key'>$key</div>";
                     $out .= "<div class='cmsModulCategoryFrame dragFrame cmsModulCat_$key' id='cmsModulCat_$key' >";
                     foreach ($value as $type => $typeValue) {
                         $out .= "<div class='cmsModulContentButton drageBox' id='cmsDragModul_$type' style='$style'>";
                         $out .= "<div class='dragButton' style='display:inline-block;'><img src='/cms_".$GLOBALS[cmsVersion]."/cmsImages/cmsMove.png' border='0px'></div>";

                         $out .= "&nbsp;".$typeValue[name];
                         $out .= "</div>";
                     }
                     $out .= "</div>";
             }
        }

        $out .= div_end_str("cmsModulContentFrame $hidden");
        


        return $out;
        
    }
    
    function content_siteMap() {
        $out = "";
        $out .= "<div class='cmsEditFrame_headLine' id='editSitemap'>Sitemap</div>\n";


        $pageList = cms_page_getSortList();
        $mainSort = 0;
        $mainNr = 0;

        foreach($pageList as $idCode => $page) {
            $name = $page[name];
            $showName = $page[title];

            $show = 1;
//            switch ($name) {
//                case "sitemap" : $show = $page[navigation]; break;
//                case "admin" :
//                    // echo ("Admin $_SESSION[showLevel] <br>");
//                    if ($_SESSION[showLevel]<9) $show =9;
//                    break;
//
//                default :
//                    if (substr($name,0,7)== "layout_") $show = 0;
//
//            }
            $sort = $page[sort];
//                if ($sort != $mainSort) {
//                echo ("Change Sort in MainLevel from $sort to $mainSort <br>");
//                $res = cms_page_changeSort($page[id],$mainSort);
//            }
//
           
            $out .= "$showName $pageLevel <br />";

            $pageLevel = $page[showLevel];
            foreach ($page[subNavi] as $key2 => $page2) {
                $name = $page2[name];
                $showName = $page2[title];
                $sort = $page2[sort];
                $pageLevel = $page2[showLevel];
                $out .= " -- $showName $sort $pageLevel <br />";

            }


        }
        
        return $out;
        
    }
    
    
    function content_images() {
        $out = "";
        $out .= "<div class='cmsEditFrame_headLine' id='editImages'>Bilder</div>\n";
        
        return $out;
        
    }
    
    function content_data() {
        $out = "";
        $out .= "<div class='cmsEditFrame_headLine' id='editData'>Daten</div>\n";

        $out .=  "<div class='cmsEditFrame_button' id='editLayout' >Layout</div>\n";
//        foreach ($_SESSION[cmsSettings] as $key => $value) {
//            $out .= " $key = $value <br>";
//        }
      
        $specialData = cmsAdmin_activeData("name");
        foreach ($specialData as $key => $value) {
            $name = $value[name];
            $out .=  "<div class='cmsEditFrame_button' id='editData$key' >$name</div>\n";
        }

      



        
        return $out;
        
    }
    
    
    function getContent($getData) {
        
        
        $mainPage = $getData[mainPage];
        if ($mainPage) {
            $out = $this->content_mainPage();
            
        }
        
        
        $button = $getData[button];
        
        if ($button) {
            $out .= "<div class='cmsEditFrame_myCms'>myCMS</div>";
        
            switch ($button) {
                case "editSitemap" :
                    $out .= $this->content_sitemap(); 
                    break;
                
                case "editCMS" :
                    $out .= $this->content_cms(); 
                    break;
                
                case "editPage" :
                    $out .= $this->content_pageData(); 
                    break;
                
                case "editContent" :
                    $out .= $this->content_contentData(); 
                    break;
                
                case "editImages" :
                    $out .= $this->content_images(); 
                    break;
                
                case "editData" :
                    $out .= $this->content_data(); 
                    break;

                case "editContent_New" :
                    $out .= $this->content_newContent();
                    $out .= "Hallo";
                    break;
                
                default : 
                    $out .= "<b>unkown Button in editBox $button </b>";
            }
        }

        return $out;
    }
    
}

function cmsEditBox_class() {
    // $class =  $ownPhpFile = $ownAdminPath."/cms_admin_articles_own.php";
//    if (file_exists($ownPhpFile)) {
//        require_once($ownPhpFile);
//        $class = new cmsAdmin_articles();
//
//    } else {
        $class = new editBox();
    // }
    return $class;
}



function cmsEditBox_show() {
    $class = cmsEditBox_class();
//   
        // echo ("File $ownPhpFile not found <br>");
    
    $res = $class->show();
    return $res;
}



function cmsEditBox_getContent($getData) {
    $class = cmsEditBox_class();
    $res = $class->getContent($getData);
    return $res;
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
