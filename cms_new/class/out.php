<?php
class out {
    
    static public $wireframe;
    
    
    public static function init() {
        self::$wireframe = session::get(wireframe);
        // echo "Init out wire = ".self::$wireframe." <br>";
        
    }
    
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // DIV FUNCTIONS                                                          //
    public static function errorBox($msg,$reload=array()) {
        self::__errorBox($msg,$reload);
    }
    
    public static function infobox($msg,$reload=array()) {
        self::__infoBox($msg,$reload);
    }
    
    private function __infoBox($msg,$reload) {
        echo (self::__infoBox_str($msg,$reload));
    }
    
    private function __infoBox_str($msg,$reload) {
        $divName = "cmsBox cmsBoxInfo";
        $str = div::start_str($divName);
        $str .= $msg;
        $str .= self::__reloadStr($reload);
        $str .= div::end_str($divName);
        return $str;
    }
    
    private function __errorBox($msg,$reload) {
        echo (self::__errorBox_str($msg,$reload));
    }
    
    private function __errorBox_str($msg,$reload) {
        $divName = "cmsBox cmsBoxError";
        $str = div::start_str($divName);
        $str .= $msg;
        $str .= self::__reloadStr($reload);
        $str .= div::end_str($divName);
        return $str;
    }
    
    private function __reloadStr($reload) {
        $url = $reload[url];
        $wait = $reload[wait];
        $cancel = $reload[cancel];
        
        if (!$url AND !$wait) return "";
        $str .= "<br />";
        $divName = "cmsBoxReload";
        $str .= div::start_str($divName);
        if ($wait) {
            
            if ($wait < 100) $wait = $wait * 1000;
            
            
            $str .= "Reload in $wait ms ";
            $str .= "<div class='".$divName."_frame' >";
            if ($url) $str .= "<a href='$url' class='".$divName."_url'>link</a>";
            $str .= "<div class='".$divName."_wait' >$wait</div>";
            
            
            $str .= "<div class='".$divName."_process' >&nbsp;</div>";
            $str .= "</div>";  
            
            $str .= "<div class='".$divName."_cancel' >x</div>";
            
//            $str .= "<script type='text/javascript'>";
//            $str .= "cmsBoxReload();";
//            $str .= "</script>";
            
        } else {
            $str .= "RELOAD !!! ";
        }
        
        $str .= div::end_str($divName);
        return $str;
    }
}


class div {
    
    public static function start($divName,$divData=array()) { 
        self::__div_start($divName,$divData);         
    }        
    public static function end($divName,$divClose) { 
        self::__div_end($divName,$divClose); 
    }    
    public static function dive($divName,$divData=array(),$divContent="noContent",$divClose=0) {
        self::__div_div($divName,$divData,$divContent,$divClose);
    }    
    public static function start_str($divName,$divData=array()) { 
        return self::__div_start_str($divName,$divData); 
    }
    public static function end_str($divName,$divClose=0) { 
        return self::__div_end_str($divName,$divClose); 
    }
    
    public static function div_str($divName,$divData=array(),$divContent="noContent",$divClose=0) {
        return self::__div_div_str($divName,$divData,$divContent,$divClose);
    }
    
    
    private static function __div_start($divName,$divData) {
        echo (self::__div_start_str($divName, $divData));
    }
    
    private static function __div_end($divName,$divClose) {
        echo (self::__div_end_str($divName, $divClose));
    }
    
    private static function __div_div($divName,$divData,$divContent,$divClose) {
        echo (self::__div_div_str($divName,$divData,$divContent,$divClose));
    }
    
    
    private static function __div_start_str($divName,$divData) {
        $str = div_start_str($divName,$divData);
        return $str;
    }
    
    private static function __div_end_str($divName,$divClose) {
        $str = div_end_str($divName,$divClose);
        return $str;    
    }
    
    private static function __div_div_str($divName,$divData,$divContent,$divClose) {
        $str = "";
        $str .= self::__div_start_str($divName, $divData);
        $str .= $divContent;
        $str .= self::__div_end_str($divName, $divClose);
        return $str;
    }
    
    
    
    
}

?>
