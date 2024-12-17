<?php

class cmsClass_site_session {
    
    function init_site_session() {
        $this->sessionType = "cmsName"; // simple / cmsName;
        $_SESSION[sessionType] = $this->sessionType;
    }
    
    
   
    
    function session_show() {
//        $ar = array("red"=>"rot","blue"=>"blau");
//        $this->session_set(test,$ar);
//
//        $blue = $this->session_get("[test][blue]");
//        echo ("Blue is $blue <br>");
//
//        $green = $this->session_get("[test][green]");
//        echo ("Green is $green <br>");
//
//        $this->session_set("[test][green]","grün");
//        $this->session_set("test,notShow",array());
//       
//        $this->session_set("test,farben,rosa","rosa");
//        $this->session_set("test,farben,brown","braun");
//        $this->session_set("test,farben,other,pink","Grell Rosa");
//        
//        $res = $this->session_get("test");
//        if (is_array($res)) foreach ($res as $key => $value) echo ("result -> $key = $value <br>");        
//        foreach ($res[farben] as $key => $value) echo ("farben $key = $value <br>");
        
        

        $showLevel = user::showLevel();
        return 0;
//        foreach ($_SESSION[$this->cmsName."_session"][pageHiddenId] as $key => $value) echo ("Hidden $key = $value <br>");
//        foreach (page::hiddenIdList_get() as $key => $value) echo ("Hidden $key = $value<br>");
//        
        
        if ($showLevel < 9 ) return 0;
        $not = array();
        foreach ($_SESSION as $key => $value) {
            switch ($key) {
                case "sessionType" : break;
                case $this->cmsName."_session" : break;
                case "defaultText"   : break;
                case "adminText"     : break;
            
                default :
                    $not[$key] = $value;
            }
        }
        
        
         if ($this->sessionType == "cmsName" AND $this->cmsName) {
            if (is_array($_SESSION[$this->cmsName."_session"])) {
//        
//            // if (!is_array($_SESSION[$this->cmsName."_session"])) $_SESSION[$this->cmsName."_session"] = array();
                foreach ($_SESSION[$this->cmsName."_session"] as $key => $value) {
                    echo ("session $this->cmsName => $key => $value <br>");
                    
                    //unset($not[$key]);
                }
//            } else {
//                echo ("<h2>NO ARRAY for ".$_SESSION[$this->cmsName."_session"]."  - </h2>");
           }
        }
        
        foreach ($not as $key => $value ) {
            echo ("normal Session = $key = $value <br>");
            if ($key == "style") {
                show_array($value); //  foreach ($value as $k=>$v) echo("stlye $k = $v <br>");
            }
        }
        
    }
    
    
    function site_session_get($key) {   
        echo ("not Use site_session_get  $key!!<br>");
        return $this->session_get($key);
    }
       
    function session_get($key) {    
        return session::get($key);        
    }
    
    function site_session__set($key,$value) {
        echo ("not Use site_session_set $key !!<br>");
        return $this->session_set($key, $value);
    }
    
    function session_set($key,$value) {
        session::set($key,$value);                     
    }
    
  
}



?>
