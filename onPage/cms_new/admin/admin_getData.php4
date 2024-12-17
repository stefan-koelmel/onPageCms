<?php

function admin_getData($url,$data) {

    if (is_array($data)) {
        $add="";
        foreach($data as $key => $value) {
            if ($add=="") $add .= "?";
            else $add.="&";
            $add .= $key."=".$value;
        }
        $url .= $add;
        // echo ("phpVersion = ".phpversion()." url=".$url."<br>");
    }

    if (is_string($data)) {
        $url .= $data;
        //echo ($url."<br>");
    }
    echo ($url."<br>");
    // echo ("php Version ".phpversion()."<br>");
    $data = file_get_contents($url, FILE_USE_INCLUDE_PATH);
    while (substr($data,0,2) != "a:") $data = substr($data,1);
    return $data;
}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
