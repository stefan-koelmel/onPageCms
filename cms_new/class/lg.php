<?php

class lg {
    
    public static $lgData;
    
    public static $show_lg;
    public static $admin_lg;
    
    private static $defaultLg = "dt";
    
    public static function showLg($setLg=null) {
        if ($setLg) {
            // echo ("SET showLg = $setLg <br>");
            self::$show_lg = $setLg;
            session::set("lg",$setLg);            
        }
        if (self::$show_lg) return self::$show_lg;
        self::$show_lg = session::get("lg");
        return self::$show_lg;        
    }
    
    public static function adminLg($setLg=null) {
        if ($setLg) {
            // echo ("SET adminLg = $setLg <br>");
            self::$admin_lg = $setLg;
            session::set("adminLanguage",$setLg);            
        }
        if (self::$admin_lg) return self::$admin_lg;
        self::$admin_lg = session::get("adminLanguage");
        return self::$admin_lg;        
    }
    
    
    public static function lgStr($str,$showMissing=1,$useDefault=1) {
        
        
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
            }
        } else {
            if (substr($str,0,3)=="lg|") {
                $lgList = array();
                $help = explode("|",$str);           
                for($i=1;$i<count($help);$i++) {
                    list($lgCode,$lgStr) = explode(":",$help[$i]);
                    if ($lgCode) $lgList[$lgCode] = $lgStr;                              
                }
            }
        }
   
        if (!is_array($lgList)) return $str;
       
        $str = $lgList[self::$show_lg];
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
    
        $str = $lgList["lg_".self::$show_lg];
        if (is_string($str)) return $str;
    
        // echo ("<b> NOT found in lgList $this->showLg </b><br>");
        
        if ($useDefault) {
            $showLevel = session::get(showLevel);
            if ($lgList[self::$defaultLg]) {
                $str = "";
                if ($showMissing AND $showLevel>3) {
                    $str.= self::$defaultLg.":".$lgList[self::$defaultLg];
                    $str = self::lgClear($str);                    
                } else {
                    $str .= $lgList[self::$defaultLg];
                }
                return $str;
            }
            
            
            foreach($lgList as $lgCode => $lgStr) {
                if ($lgStr) {
                    if ($showMissing AND $showLevel>3) {
                        $str = $lgCode.":".$lgStr;
                        $str = self::lgClear($str);
                    } else {
                        $str = $lgStr;
                    }
                    return $str;                    
                }
            }
        }
        return $lgList[self::$show_lg];      
    }
    
    private function lgClear($str) {
        
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
            $lgMissing = session::get("lgMissing");
            $class = "cmsLanguageFlag cmsLanguage_$lgCode";
            if (!$lgMissing) $class.= " cmsLanguageFlag_hidden";
            
            $lgStr = "<span class='$class'>$lgCode</span>";
            $str = $lgStr.substr($str,3);
        }
            
            
        return $str;        
    }
    
    
    public static function init() {
        self::data_get();
        
        if (!is_array(self::$lgData)) {
            echo ("<h1>NO DATA ARRAY ".self::$lgData."</h1>");
            die();
        }
        
        $adminLg = self::adminLg();
        $showLg  = self::showLg();
        
        if ($adminLg AND $showLg) return 0;
        
        foreach(self::$lgData as $lg => $lgData) {
            $show = $lgData[show];
            $admin = $lgData[admin];
            // echo ("GET $lg show='$show' admin='$admin' <br>");
            if ($show AND !self::$show_lg) {
                self::showLg($lg);
            }
            
            if ($admin AND !self::$admin_lg) {
                self::adminLg($lg);                    
            }            
        }
        $adminLg = self::adminLg();
        $showLg  = self::showLg();
        
    }
    
    
    private function data_get() {
        // self::$lgData = session::get(lgData);
        if (is_array(self::$lgData)) return self::$lgData;
        
       
        $languageSettings = session::get("cmsSettings,language");
        
        // echo ("languageStr = $languageSettings <br>");
        $lgList = explode("|",$languageSettings);
        
        $lgData = array();
        foreach ($lgList as $nr => $lgCode) {
            list($lg,$select,$edit,$show,$admin) = explode(":",$lgCode);
            // echo ("LanguageDATa get for  is $lg select = $select edit=$edit,    show=$show admin=$adminLg <br> ");
            
            if ($select + $edit + $show == 0) {
                // echo ("Dont use $lg -- nothing Set <br>");
                continue;
            }
            
            $add = array("edit"=>$edit,"selectAble"=>$select,"show"=>$show,"admin"=>$admin);
            $lgData[$lg] = $add;
        }
        self::$lgData = $lgData;
        session::set("lgData",$lgData);        
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // ADMIN TEXT                                                             //
    
    public static function lga($type=0,$code=0,$add="",$setDefault=null) {
        $adminLg = self::adminLg();
        
        if (!$type) return "no Type";
        if (!$code) {
            $offSet = strpos($type,"_");
            if (!$offSet) return "no Code";
            $code = substr($type,$offSet+1);
            $type = substr($type,0,$offSet);
        }
       
        $adminText = session::get("adminText");
      
        
        global $edit_adminText;
        if (!is_array($edit_adminText)) $edit_adminText = array();
        if (!is_array($edit_adminText[$type])) $edit_adminText[$type] = array();
        if (!is_array($edit_adminText[$type][$code])) $edit_adminText[$type][$code] = array();
        
        $typeList = session::get("adminText,$type");
        // echo ("TypeList $typeList von $type,$code <br>");
        if (!is_array($typeList)) {
            return self::admin_notFound($type,$code,$setDefault);
        }
        
     
        $textData = $typeList[$code];
        // echo ("TypeList $textData von $type,$code <br>");
        if (!is_array($textData)) {
            
            return self::admin_notFound($type,$code,$setDefault);
        }
        $edit_adminText[$type][$code] = $textData;
      

        $str = $textData[$adminLg];
        if (!$str) return self::admin_notFound($type, $code,$textData);

        if ($add) $str .= $add;
        return ($str);
    }

    private function admin_notFound($type,$code,$textData=null) {
        
        $str = $type."_".$code;
        if (user::userLevel()<7) return $str;
        
       //  if (is_string($textData)) echo ("STr in admin_notFound $textData <br>");
        global $adminText_notFound;
        if (!is_array($adminText_notFound)) $adminText_notFound = array();
        if (!is_array($adminText_notFound[$type])) $adminText_notFound[$type] = array();

        $setData = 1;
        if (is_array($textData)) $setData = $textData;
        if (is_string($textData)) $setData = array("dt"=>$textData);
        if (!$adminText_notFound[$type][$code]) $adminText_notFound[$type][$code] = $setData;
        // echo ("Add to adminText not Found $type $code - $textData <br>");
       
        return $str;
    }
    
    
    //put your code here
}

?>
