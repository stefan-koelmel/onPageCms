<?php // charset:UTF-8
class cmsType_dynamicContent_base extends cmsType_contentTypes_base {

    function getName (){
        return "Dynamischer Inhalt";
    }

    function dynamicContent_show($contentData,$frameWidth) {
        echo ("Dynamischer Inhalt");
    }

    function dynamicContent_editContent($editContent,$frameWidth) {
        $res = array();
        return $res;
    }
}

function cmsType_dynamicContent_class() {
    if ($GLOBALS[cmsTypes]["cmsType_dynamicContent.php"] == "own") $dynamicContentClass = new cmsType_dynamicContent();
    else $dynamicContentClass = new cmsType_dynamicContent_base();
    return $dynamicContentClass;
}

function cmsType_dynamicContent($contentData,$frameWidth) {
    $dynamicContentClass = cmsType_dynamicContent_class();
    $dynamicContentClass->dynamicContent_show($contentData,$frameWidth);
}


function cmsType_dynamicContent_editContent($editContent,$frameWidth) {
    $dynamicContentClass = cmsType_dynamicContent_class();
    return $dynamicContentClass->dynamicContent_editContent($editContent,$frameWidth);
}

function cmsType_dynamicContent_getName() {
    $dynamicContentClass = cmsType_dynamicContent_class();
    $name = $dynamicContentClass->getName();
    return $name;
}


?>
