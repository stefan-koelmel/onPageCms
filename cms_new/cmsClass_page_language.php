<?php
class cmsClass_page_language extends cmsClass_page_base {
    
    function page_language_init() {
        $this->lgList  = cms_text_getSettings();
        // echo ("Disabled Langenue settings in page_language init()<br>");
        $this->showLg  = lg::showLg(); //$this->get_show_lg(); //cms_text_getLanguage();
        $this->adminLg = lg::adminLg(); // $this->get_admin_lg(); // cms_text_adminLg();
       
        $this->defaultLg = "dt";
        
        // echo ("<h1>SHOW LG = $this->showLg ADMIN $this->adminLg </h1>");
    }
    
    
    
    function get_show_lg() {
        $lg = lg::showLg();
        if ($lg) return $lg;
        return cms_text_getLanguage();
    }
    
    function get_admin_lg() {
        $adminLg = lg::adminLg(); // $this->session_get(adminLanguage);
        if ($adminLg) return $adminLg;
        return cms_text_adminLg();
    }
    
    
    
    function lgStr($str,$showMissing=1,$useDefault=1) {
        // return lg::lgStr($str, $showMissing, $useDefault);
        
        if (is_array($str)) {
            if ($str[id]) {
                $lgList = array();
                foreach ($str as $key => $value ) {
                    if (substr($key,0,3) != "lg_") continue;
                    $lgCode = substr($key,3);
                    // echo "Found LG $lgCode with $value <br />";
                    $lgList[$lgCode] = $value;
                }
            } else {
                $lgList = $str;
            
                // foreach ($lgList as $key => $value) echo ("LG $key = $value <br>");
            }
        } else {
            if (substr($str,0,3)=="lg|") {
                $lgList = array();
                $help = explode("|",$str);           
                for($i=1;$i<count($help);$i++) {
                    list($lgCode,$lgStr) = explode(":",$help[$i]);
                    if ($lgCode) $lgList[$lgCode] = $lgStr;           
                    // echo ("add $lgCode $lgStr <br>");
                }
            }
        }
   
        if (!is_array($lgList)) return $str;
        
       
        
    
        $str = $lgList[$this->showLg];
        // CHeck for Defined Languages
        if ($str) {
            switch($str) {
                case "#dt" : $getLg = "dt"; break;
                case "#en" : $getLg = "en"; break;
                case "#fr" : $getLg = "fr"; break;
            }
            if ($getLg) {
                $str = $lgList[$getLg];
            }
        }
        if ($str) return $str;
    
        $str = $lgList["lg_".$this->showLg];
        if (is_string($str)) return $str;
    
        // echo ("<b> NOT found in lgList $this->showLg </b><br>");
        
        if ($useDefault) {
            if ($lgList[$this->defaultLg]) {
                $str = "";
                if ($showMissing AND $this->showLevel>3) {
                    $str.= $this->defaultLg.":".$lgList[$this->defaultLg];
                    $str = $this->lgClear($str);                    
                } else {
                    $str .= $lgList[$this->defaultLg];
                }
                return $str;
            }
            
            
            foreach($lgList as $lgCode => $lgStr) {
                if ($lgStr) {
                    if ($showMissing AND $this->showLevel>3) {
                        $str = $lgCode.":".$lgStr;
                        $str = $this->lgClear($str);                    
                    } else {
                        $str = $lgStr;
                    }
                    return $str;                    
                }
            }
        }
        return $lgList[$this->showLg];
        
        // $str = cms_text_getLg($str, $defaultLg);        
    }
    
    function lgClear($str) {
        
        if ($str[2] != ":") return $str;
        
        $lgCode = substr($str,0,2);
      
        $replace = 0;
        switch ($lgCode) {
            case "dt" : $replace = 1; break;
            case "en" : $replace = 1; break;
            case "fr" : $replace = 1; break;
            default :
                $str = "LG ($lgCode)?? ".$str;
        }
        
        if ($replace) {
            $lgMissing = $this->session_get("lgMissing");
            $class = "cmsLanguageFlag cmsLanguage_$lgCode";
            if (!$lgMissing) $class.= " cmsLanguageFlag_hidden";
            
            $lgStr = "<span class='$class'>$lgCode</span>";
            $str = $lgStr.substr($str,3);
        }
            
            
        return $str;        
    }
    
    
     function page_wireframe_init() {
        $wireEnabled = $this->cmsSettings[wireframe];
        if (!$wireEnabled) {
            $this->wireframeEnabled = 0;
            $this->wireframeState = 0;
            $this->wireframeData = array();
            return 0;
        }
        
        $this->wireframeEnabled = 1;
        $state = $this->session_get(wireframe);
        if (is_null($state)) {
            
            $setState = $this->cmsSettings[wireframeOn];
              
            $this->session_set("wireframe",$setState);
            $state = $setState;
        }       
        $this->wireframeState = $state;
         
         
        $lineColor = 0; // "#33ff33;
        $backColor = "#777777";

        $this->wireframeData = array();
        $this->wireframeData[backColor] = $lineColor;
        $this->wireframeData[lineColor] = $backColor;

        $crossPath = $_SERVER[DOCUMENT_ROOT]."/cms_".$this->cmsVersion."/images/wireframe/";
        // get CrossFile
        if ($lineColor[0] == "#") $lineColor = substr($lineColor,1);
         
        if (file_exists($crossPath."cross-".$lineColor.".png")) {
            $crossFile = "cross-".$lineColor.".png";
        } else {
            if (file_exists($crossPath."cross.png")) {
                $crossFile = "cross.png";
            }
        }        
        $this->wireframeData[crossPath] = $crossPath;
        if ($crossFile) $this->wireframeData[crossFile] = $crossFile;
         
        // targetPath
        switch ($_SERVER[HTTP_HOST]) {
            case "cms.stefan-koelmel.com" :
                $targetPath = $_SERVER[DOCUMENT_ROOT]."/".$this->cmsName."/wireframe/";
                break;
            default :
                $targetPath = $_SERVER[DOCUMENT_ROOT]."/wireframe/";                
        }
        $this->wireframeData[targetPath] = $targetPath;        
        
        
        $str = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br />";
        $str .= "<br />";
        $str .= "Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br />";
        $str .= "<br />";
        $str .= "Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.<br />";
        $str .= "<br />";
        $str .= "At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, At accusam aliquyam diam diam dolore dolores duo eirmod eos erat, et nonumy sed tempor et et invidunt justo labore Stet clita ea et gubergren, kasd magna no rebum. sanctus sea sed takimata ut vero voluptua. est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.<br />";
        $str .= "<br />";
        $str .= "Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus.<br />";
        $str .= "<br />";
        $str .= "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br />";
        $str .= "<br />";
        $str .= "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br />";
        $str .= "<br />";
        $str .= "Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br />";
        $str .= "<br />";
        $str .= "Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud ex<br />";
        
        $this->wireframeData[lorem] = $str;
    }
}

?>
