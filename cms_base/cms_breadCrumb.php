<?php




class cms_breadCrumb_base extends cmsType_contentTypes_base {

    function breadCrumb($pageData,$frameWidth) {
        $delimiter = " | ";
        
        div_start("breadCrumb"); //,"width:".$frameWidth."px;");
        $breadCrumbList = $this->breadCrumb_getList($pageData);
        $anz = count($breadCrumbList);
        
        
        if ($_GET[setSession]) {
            list($sessionKey,$sessionValue) = explode("|",$_GET[setSession]);
            if ($sessionKey AND $sessionValue) {
                if ($_SESSION[$sessionKey] != $sessionValue) {
                    $_SESSION[$sessionKey] = $sessionValue;
                    reloadPage($pageData[name].".php",0);
                }
            }                        
        }
        
        
        $withLink =1;
        for ($i=$anz-1;$i>=0;$i--) {
            $url  = $breadCrumbList[$i][url];
            $name = $breadCrumbList[$i][name];
            $name = cms_text_getLg($name);
            // echo ("NAME = $name ");
            // show_array($breadCrumbList[$i]);
            if ($_SESSION[userLevel]) {
                $dropList = $breadCrumbList[$i][dropList];
                $dropId   = $breadCrumbList[$i][dropId];
            } else {
                $dropList = 0;
            }
            $class = "breadCrumbLink";
            if (is_array($dropList)) {
                $class .= " breadCrumbDropdown";
            }
            
            
            if ($i>0) {
                if (is_array($dropList)) {
                    echo ("<div class='breadCrumbDropdown_frame'>");
                }
                echo ("<a href='$url' class='$class'>$name</a>");
                if (is_array($dropList)) {
                    echo ("<div class='breadCrumbDropdown_button' id='breadCrumbButton_".$dropId."' >&nbsp;</div>");
                    
                    echo ("<div class='breadCrumbDropdown_List breadCrumbDropdown_List_hidden' id='breadCrumbButton_".$dropId."_list' >");
                    $goPage = $pageData[name].".php?";
                    
                    for ($d=0;$d<count($dropList);$d++) {
                        $name = $dropList[$d][name];
                        $name = cms_text_getLg($name);
                        $url  = $dropList[$d][url];
                        if (substr($url,0,8)=="session:") {
                            $url= $goPage."setSession=".substr($url,8);
                        }
                        
                        
                        echo ("<a href='".$url."' class='breadCrumbDropdown_link' >$name</a><br/>");
                    }                    
                    echo ("</div>");
                }
                if (is_array($dropList)) {
                    echo ("</div>");
                }
                
                echo ("$delimiter");
            } else {
                if ($withLink) echo ("<a href='$url' class='breadCrumbActive'>$name</a>");
                else echo ("<span class='breadCrumpActive'>$name - 0 </span>");
            }
        }
        
        div_end("breadCrumb");
    }

    function breadCrumb_getList($pageData) {
        $activePage = $pageData[name];
        $pageInfo = cms_page_getInfoBack($activePage);

        $breadCrumbList = $pageInfo[breadCrumbList ];
        return $breadCrumbList;
    }
}



function cms_breadCrumb_Class() {
    $cmsName = $GLOBALS[cmsName];
    
    
    switch ($_SERVER[HTTP_HOST]) {
        case "cms.stefan-koelmel.com" :
            $ownFn = $_SERVER[DOCUMENT_ROOT]."/$cmsName/cms/cms_breadCrumb_own.php";
            break;
        default :
            $ownFn = $_SERVER[DOCUMENT_ROOT]."/cms/cms_breadCrumb_own.php";            
    }
    
    // echo ("OnwFile = $ownFn <br>");
    if (file_exists($ownFn)) {
        include($ownFn);
        // echo ("<h1>EXIST</h1>");
        $breadCrumbClass = new cms_breadCrumb_own();
    } else {
        $breadCrumbClass = new cms_breadCrumb_base();
    }
    return $breadCrumbClass;
}

function cms_page_breadcrumb($pageData,$frameWidth) {
    $breadCrumbClass = cms_breadCrumb_Class();
    // echo ("<h1>$breadCrumbClass</h1>");
    $breadCrumbClass->breadCrumb($pageData,$frameWidth);   
}

?>
