<?php // charset:UTF-8

class cmsType_content_base extends cmsType_contentTypes_base {
    function getName (){
        return "Inhalt";        
    }
    
    function show($contentData,$frameWidth) {
        echo ("CONTENT<br />");
    }
    
    function content_editContent($editContent,$frameWidth) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();

        // MainData
        $addData = array();
        $addData["text"] = "Rahmen Stärke";
        $addData["input"] = "<input type='text' name='editContent[data][mainBorder]' value='$data[mainBorder]' >";
        //$res[] = $addData;

        return $res;
    }    
    
}

function cmsType_content_class() {
    if ($GLOBALS[cmsTypes]["cmsType_content.php"] == "own") $contentClass = new cmsType_content();
    else $contentClass = new cmsType_content_base();
    return $contentClass;
}

function cmsType_content($contentData,$frameWidth) {
    $contentClass = cmsType_content_class();
    $contentClass->show($contentData,$frameWidth);
}



function cmsType_content_editContent($editContent) {
    $contentClass = cmsType_content_class();
    $res = $contentClass->content_editContent($editContent, $frameWidth);
    return $res;
}
    


?>
