<?php // charset:UTF-8

class cmsType_ownPhp_base extends cmsClass_content_show {
    
    function getName (){
        return "eigenes Php";
    }

     function contentType_show() {
        $contentData = $this->contentData;
        $frameWidth = $this->frameWidth;

        div_start("cmsOwnPhp");
        $data = $contentData[data];
        if (!is_array($data))  $data = array();
            
        $scriptName = $data[scriptName];
        if (!$scriptName) {
            echo ("<h1>Kein Script ausgewählt </h1>");
            div_end("cmsOwnPhp");
            return 0;
        }
        
        
        $parameter = array();
        $paraList = $this->ownPhp_getParaList($scriptName);
        foreach ($paraList as $key => $value) {
            $setVal = $value[standard];
            if ($value[type] == "checkbox") {
                $setVal = 0;
            }
            $parameter[$key] = $setVal;
        }
        
        
        
        foreach ($data as $key => $value) {
            if (substr($key,0,7) == "ownVar_") {
                $key = substr($key,7);
                // echo ("Found $key => $value <br>");
                $parameter[$key] = $value;
            }
        }
        
        $parameterList = $data[parameter];
        if (is_array($parameterList)) {
        // echo ("scriptName = $scriptName <br />");
            
            foreach ($parameterList as $key => $value) {
                $parameter[$value[name]] = $value[value];
            }
        }
        switch ($scriptName) {
            case "demoScript" :
                break;
            default :
                $res = $this->ownPhp_show($scriptName,$parameter,$frameWidth);
                if ($res!=1) {
                    echo ("Result of $scriptName is '$res' <br />");
                    
                }
        }
     
        // cms_call_ownPhp($scriptName,$parameter);
        
        
        div_end("cmsOwnPhp");


    }
    
    function ownPhp_show($scriptName,$parameter,$frameWidth){}
    
    function ownPhp_getScriptList() {
        $res = array();
        $ownScript = $this->ownPhp_getScriptList_own();
        if (is_array($ownScript)) {
            foreach($ownScript as $key => $value) {
                $res[$key] = $value;
            }                
        }
        return $res;
    }
    
    function ownPhp_getScriptList_own() {
        $res = array();
        return $res;
    }
    
    
    function ownPhp_getParaList($scriptName) {
        $res = array();
        
        $ownVars = $this->edit_getVarList($scriptName);
        if (is_array($ownVars)) {
            foreach ($ownVars as $key => $value) {
                $res[$key] = $value;
            }
        }        
        return $res;
    }

    function edit_getVarList($scriptName) {}


    function contentType_editContent() {
        $editContent = $this->editContent;
        $frameWidth  = $this->frameWidth;
        $data = $this->editContent[data];
        if (!is_array($data)) $data = array();

        $res = array();

        // MainData
        
        $scriptList = $this->ownPhp_getScriptList();
        $input = "<select name='editContent[data][scriptName]' value='$data[scriptName]'>";
        
        $scriptName = $data[scriptName];
        foreach ($scriptList as $key => $value) {
            $select = "";
            if ($key == $scriptName) $select= "selected='1'";
            $input .= "<option value='$key' $select>$value</option>";            
        }
        $input .= "</select>";
        
        
        $addData = array();
        $addData["text"] = "ScriptName";
        $addData["input"] = $input; // "<input type='text' name='editContent[data][scriptName]' value='$data[scriptName]' >";
        $addData["mode"] = "Simple";
        $res[] = $addData;
        
        
        $paraList = $this->ownPhp_getParaList($scriptName);
        foreach ($paraList as $key => $value) {
            // echo ("own Variablen $key = $value <br>");
            $name = $value[name];
            $standard = $value[standard];
            $type = $value[type];
            
            $mode = "More";
            if ($value[mode]) $mode = $value[mode];
            
            // echo (" --> $name $type $standard <br />");
            
            $val = $data["ownVar_".$key];
            if (is_null($val)) $val = $standard;
            
            $input = "";
            switch ($type) {
                case "text" :
                    $input = "<input type='text' name='editContent[data][ownVar_$key]' value='$val' />";
                    break;
                case "checkbox" :
                    $val = $data["ownVar_".$key];
                    
                    if ($val) $checked = "checked='1'";
                    else $checked = "";
                    $input = "<input type='checkbox' $checked name='editContent[data][ownVar_$key]' value='1' />";
                    $input .= $checked;
                    break;
                    
                case "select" :
                    $list = $value["list"];
                    if (!is_array($list)) {
                        $input = "Kein Inhalt für Liste definiert ";
                        continue;
                    }
                    $input .= "<select name='editContent[data][ownVar_$key]' value='$val' >";
                    foreach ($list as $k => $v) {
                        $checked = "";
                        if ($k == $val) $checked = "checked='1'";
                        $input .= "<option value='$k' $checked>$v</option>";
                    }
                    $input .= "</select>";
                    break;    
                    
                    
                default :
                    
                    
                    
            }
            
            
            if ($input) {
                $addData = array();
                $addData["text"] = "$name";
                $addData["input"] = $input;
                $addData["mode"] = $mode;
                $res[] = $addData;
            }
                
            
            
        }
        

        $parameterList = $data[parameter];
        if (!is_array($parameterList)) $parameterList = array();
        $nr = 1;
        foreach($parameterList as $key => $paraData) {
            $paraName = $paraData[name];
            $paraValue = $paraData[value];
            if (strlen($paraName)>1) {
                $addData = array();
                $addData["text"] = "Parameter $nr";
                $input = "<input type='text' name='editContent[data][parameter][para$nr][name]' value='$paraName' >";
                $input .= "<input type='text' name='editContent[data][parameter][para$nr][value]' value='$paraValue' >";
                $addData["input"] = $input;
                $addData["mode"] = "Admin";
                $res[] = $addData;
                $nr++;
            }
        }


        $addData["text"] = "Parameter $nr";
        $input = "<input type='text' name='editContent[data][parameter][para$nr][name]' value='' >";
        $input .= "<input type='text' name='editContent[data][parameter][para$nr][value]' value='' >";
        $addData["input"] = $input;
        $addData["mode"] = "Admin";
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
    return $ownPhpClass->show($contentData,$frameWidth);
}



function cmsType_ownPhp_editContent($editContent,$frameWidth) {
    $ownPhpClass = cmsType_ownPhp_class();
    return $ownPhpClass->ownPhp_editContent($editContent,$frameWidth);
}



?>
