<?php // charset:UTF-8
class cmsType_vmt_base extends cmsType_contentTypes_base {

    function getName (){
        return "vmt";
    }

    function vmt_show($contentData,$frameWidth) {
    }

    function vmt_editContent($editContent,$frameWidth) {
        $res = array();
        return $res;
    }
}

function cmsType_vmt_class() {
    if ($GLOBALS[cmsTypes]["cmsType_vmt.php"] == "own") $vmtClass = new cmsType_vmt();
    else $vmtClass = new cmsType_vmt_base();
    return $vmtClass;
}

function cmsType_vmt($contentData,$frameWidth) {
    $vmtClass = cmsType_vmt_class();
    $vmtClass->vmt_show($contentData,$frameWidth);
}


function cmsType_vmt_editContent($editContent,$frameWidth) {
    $vmtClass = cmsType_vmt_class();
    return $vmtClass->vmt_editContent($editContent,$frameWidth);
}

function cmsType_vmt_getName() {
    $vmtClass = cmsType_vmt_class();
    $name = $vmtClass->getName();
    return $name;
}


?>
