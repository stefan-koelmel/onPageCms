<?php // charset:UTF-8

function cms_infoBox($text) {
    $res = cms_infoBox_str($text);
    echo ($res);
}

function cms_infoBox_str($text) {
    $res = cms_showBox("info",$text);
    return $res;
}
function cms_errorBox($text) {
    $res = cms_errorBox_str($text);
    echo ($res);
}

function cms_errorBox_str($text) {
    $res = cms_showBox("error", $text);
    return $res;
}


function cms_showBox($type,$text) {
    switch ($type) {
        case "info" :
            $str = div_start_str("cmsBox cmsBoxInfo");
            $str .= $text;
            $str .= div_end_str("cmsBox cmsBoxInfo");
            break;
        case "error" :
            $str .= div_start_str("cmsBox cmsBoxError");
            $str .= $text;
            $str .= div_end_str("cmsBox cmsBoxError");
            break;
    }
    return $str;
}

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
