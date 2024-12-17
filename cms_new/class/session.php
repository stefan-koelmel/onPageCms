<?php

class session {
    //put your code here
    
    static public $cmsName;
    static public $sessionType;
    
    static public function init($setCmsName,$setType="cmsName") {
        self::$cmsName = $setCmsName;
        
        self::$sessionType = $setType;
        $_SESSION[sessionType] = self::$sessionType;
    }
    
    

    static public function get($key) {
        $keyList = self::session_keyList($key);
        if (is_array($keyList)) $key = $keyList[0];
        
        $value = "not found $key ";
        $sessionType = self::specialType($key);
        
        if (is_array($keyList)) {
            $sessionData = null;
            foreach ($keyList as $nr => $keyCode) {
                if ($nr == 0) {
                    switch ($sessionType) {
                        case "simple" : $sessionData = $_SESSION[$keyCode];  break;
                        case "cmsName" : $sessionData = $_SESSION[self::$cmsName."_session"][$keyCode]; break;
                    }                  
                } else {
                    $sessionData = $sessionData[$keyCode];                  
                }
                
            }
            return $sessionData;
        } else {
            switch ($sessionType) {
                case "simple" : $value = $_SESSION[$key];  break;
                case "cmsName" : 
                    switch ($key) {
                        case "cmsName" : $value = self::$cmsName; break;
                           
                        default :
                            $value = $_SESSION[self::$cmsName."_session"][$key];
                            // if (is_null($value)) echo ("No Value for $key <br>");
                            // if ($key == "pageHiddenId") echo ("HIer $value <br>");
                    }                    
            }            
        }       
        return $value;
        
    }
    
    
    static public function set($key,$value) {
        if (!$key) echo ("<h1>site_session_set($key,$value) - no Key</h1>");
        if (!self::$cmsName) {
            echo ("<h1>site_session_set($key,$value) - no cmsName='self::$cmsName'</h1>");
            global $cmsName;
            self::$cmsName = $cmsName;
        }
        
        
        $keyList = self::session_keyList($key);
        if (is_array($keyList)) $key = $keyList[0];
            
        $sessionType = self::specialType($key);
        
        if (is_array($keyList)) {
            switch (count($keyList)) {
                case 1 :
                    switch ($sessionType) {
                        case "simple"                             : $_SESSION[$keyList[0]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"][$keyList[0]] = $value; break;
                    }
                    break;
                case 2 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] = $value; break;
                    }
                    break;
                case 3 :
                    // echo ("3 - $sessionType $keyList[0], $keyList[1], $keyList[2] $value<br>");
                    switch ($sessionType) {                        
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] = $value; break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] = $value; break;
                    }
                    break;
                case 4 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] = $value; break;
                    }
                    break;
                case 5 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] = $value; break;
                    }
                    break;
                
                case 6 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] = $value; break;
                    }
                    break;
                
                case 7 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] = $value; break;
                    }
                    break;
                
               case 8 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] = $value; break;
                    }
                    break;
                
               case 9 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] [$keyList[8]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] [$keyList[8]]  = $value; break;
                    }
                    break;
                
               case 10 :
                    switch ($sessionType) {
                        case "simple"                              : $_SESSION[$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] [$keyList[8]] [$keyList[9]] = $value;  break;
                        case "cmsName" : $_SESSION[self::$cmsName."_session"] [$keyList[0]] [$keyList[1]] [$keyList[2]] [$keyList[3]] [$keyList[4]] [$keyList[5]] [$keyList[6]] [$keyList[7]] [$keyList[8]] [$keyList[9]]  = $value; break;
                    }
                    break;
                
                
                
                default:
                    echo "<h1>unkown Count ".count($keyList)."MaxCount defined is 10 </h1>";
            }         
        } else {
            switch ($sessionType) {
                case "simple" :
                    if (is_null($value)) unset($_SESSION[$key]);
                    else $_SESSION[$key] = $value;  
                    break;
                case "cmsName" : 
                    if (is_null($value)) unset($_SESSION[self::$cmsName."_session"][$key] );
                    else $_SESSION[self::$cmsName."_session"][$key] = $value;
                    break;
            }            
        }        
        return $value;                
    }
    
    
    
    private function session_keyList($key) {
        $keyList = 0;
        if (strpos($key,",")) {
            $keyList = explode(",",$key);
        }
        if (strpos($key,"]")) {
            $keyStr = str_replace(array("][","[","]"),array(",","",""),$key);
            $keyList = explode(",",$keyStr);            
        }
        return $keyList;  
    }
    
    private function specialType($key=0) {
        $sessionType = self::$sessionType;
        switch ($key) {
            case "adminText"     : $sessionType = "simple"; break;
        }
//            case "pageList_ "      : $sessionType = "simple"; break;
//            case "pageGroupList_ " : $sessionType = "simple"; break;
//        }
        return $sessionType;
    }
}

?>
