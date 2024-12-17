<?php // charset:UTF-8

class cmsType_empty_base extends cmsType_contentTypes_base {
    function getName (){
        return "Leer";        
    }
    
    function show($contentData,$frameWidth) {
        echo ("Leer<br />");
    }
    
    function empty_editContent($editContent,$frameWidth) {
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

function cmsType_empty_class() {
    if ($GLOBALS[cmsTypes]["cmsType_empty.php"] == "own") $emptyClass = new cmsType_empty();
    else $emptyClass = new cmsType_empty_base();
    return $emptyClass;
}

function cmsType_empty($contentData,$frameWidth) {
    $emptyClass = cmsType_empty_class();
    $emptyClass->show($contentData,$frameWidth);
}



function cmsType_empty_editContent($editContent) {
    $emptyClass = cmsType_empty_class();
    $res = $emptyClass->empty_editContent($editContent, $frameWidth);
    return $res;
}
    


?>
