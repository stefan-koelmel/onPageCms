<?php // charset:UTF-8

function cms_infoBox($text) {
    cms_showBox("info",$text);
}

function cms_errorBox($text) {
    cms_showBox("error", $text);
}


function cms_showBox($type,$text) {
    switch ($type) {
        case "info" :
            div_start("cmsBox cmsBoxInfo");
            echo ($text);
            div_end("cmsBox cmsBoxInfo");
            break;
        case "error" :
            div_start("cmsBox cmsBoxError");
            echo ($text);
            div_end("cmsBox cmsBoxError");
            break;
    }
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
