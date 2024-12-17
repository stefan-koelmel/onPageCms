<?php // charset:UTF-8

class cmsType_ownPhp_base extends cmsType_contentTypes_base {
    
    function getName (){
        return "eigenes Php";
    }

    function show($contentData,$frameWidth) {

        div_start("cmsOwnPhp");
        $data = $contentData[data];
        if (is_array($data)) {
            $scriptName = $data[scriptName];
            $parameterList = $data[parameter];
            // echo ("scriptName = $scriptName <br />");
            $parameter = array();
            foreach ($parameterList as $key => $value) {
                $parameter[$value[name]] = $value[value];
            }
            // show_array($parameterList);
            // show_array($parameter);
            cms_call_ownPhp($scriptName,$parameter);
        }
        // echo ("ownPhp $data");



        div_end("cmsOwnPhp");


    }



    function ownPhp_editContent($editContent) {
        $data = $editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();


        // MainData
        $addData = array();
        $addData["text"] = "ScriptName";
        $addData["input"] = "<input type='text' name='editContent[data][scriptName]' value='$data[scriptName]' >";
        $res[] = $addData;

        $parameterList = $data[parameter];
        if (!is_array($parameterList)) $parameterList = array();
        $nr = 1;
        foreach($parameterList as $key => $paraData) {
            $paraName = $paraData[name];
            $paraValue = $paraData[value];
            if (strlen($paraName)>1) {
                $addData["text"] = "Parameter $nr";
                $input = "<input type='text' name='editContent[data][parameter][para$nr][name]' value='$paraName' >";
                $input .= "<input type='text' name='editContent[data][parameter][para$nr][value]' value='$paraValue' >";
                $addData["input"] = $input;
                $res[] = $addData;
                $nr++;
            }
        }


        $addData["text"] = "Parameter $nr";
        $input = "<input type='text' name='editContent[data][parameter][para$nr][name]' value='' >";
        $input .= "<input type='text' name='editContent[data][parameter][para$nr][value]' value='' >";
        $addData["input"] = $input;
        $res[] = $addData;
        //$addData["text"] = "Hintergrundfarbe";
        //$addData["input"] = "<input type='text' name='editContent[data][mainBackColor]' value='$data[mainBackColor]' >";
        //$res[] = $addData;

        return $res;
    }
}


function cmsType_ownPhp_class() {
    if ($GLOBALS[cmsTypes]["cmsType_ownPhp.php"] == "own") $ownPhpClass = new cmsType_ownPhp();
    else $ownPhpClass = new cmsType_ownPhp_base();
    return $ownPhpClass;
}


function cmsType_ownPhp($contentData,$frameWidth) {
    $ownPhpClass = cmsType_ownPhp_class();
    $ownPhpClass->show($contentData,$frameWidth);
}



function cmsType_ownPhp_editContent($editContent,$frameWidth) {
    $ownPhpClass = cmsType_ownPhp_class();
    return $ownPhpClass->ownPhp_editContent($editContent,$frameWidth);
}



?>
