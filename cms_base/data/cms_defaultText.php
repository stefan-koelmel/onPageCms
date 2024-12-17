<?php
    function load_defaultText() {
        // echo ("<h1>NO DEFAULT TEXT FOUND</h1>");
        
        $query = "SELECT * FROM `cms_defaultText` ";
        $result = mysql_query($query);
        if (!$result ) {
            echo ("Error in '$query' <br />");
            return 0;
        }
        
        $res = array();
        while ($text = mysql_fetch_assoc($result)) {

            $type = 0;
            $code = 0;
            $add = array();
            foreach ($text as $key => $value) {

                switch ($key) {
                    case "type" : $type = $value; break;
                    case "code" : $code = $value; break;
                    case "id"   : $add[id] = $value; break;
                    default: 
                        if (substr($key,0,3) == "lg_") {
                            $lgCode = substr($key,3);
                            $add[$lgCode] = $value;
                        } else {
                            echo ("unkown $key in lg $value <br>");
                        }
                }

            }
            if (!$type) continue;
            if (!$code) continue;
            if (!is_array($res[$type])) $res[$type] = array();

            $res[$type][$code] = $add;
        }
        $_SESSION[defaultText] = $res;
        
        
        
    }
    
    
    function load_adminText() {
        echo ("<h1>LOAF ADMIN TEXT ");
        $query = "SELECT * FROM `cms_adminText` ";
        $result = mysql_query($query);
        if (!$result ) {
            echo ("Error in '$query' <br />");
            return 0;
        }
        
        $res = array();
        while ($text = mysql_fetch_assoc($result)) {

            $type = 0;
            $code = 0;
            $add = array();
            foreach ($text as $key => $value) {

                switch ($key) {
                    case "type" : $type = $value; break;
                    case "code" : $code = $value; break;
                    case "id"   : $add[id] = $value; break;
                    default: 
                        if (substr($key,0,3) == "lg_") {
                            $lgCode = substr($key,3);
                            $add[$lgCode] = $value;
                        } else {
                            echo ("unkown $key in lg $value <br>");
                        }
                }

            }
            if (!$type) continue;
            if (!$code) continue;
            if (!is_array($res[$type])) $res[$type] = array();

            $res[$type][$code] = $add;
        }
        $_SESSION[adminText] = $res;
        echo ("found = ".count($res)."</h1>");
    }

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
