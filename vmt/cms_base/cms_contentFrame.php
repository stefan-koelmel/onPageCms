<?php // charset:UTF-8



class cmsFrame_base {
    function getSettings($frameStyle) {
        $border = 0;
        $padding = 0;
        $spacing = 0;

        $frameData = $this->getFrameData($frameStyle);
        if (is_array($frameData)) return $frameData;


        switch ($frameStyle) {
            case "rollFrame" :
                $border = 1;
                $padding = 5;
                $spacing = 10;
                break;
            case "frame1" :
                $border = 1;
                $padding = 5;
                $spacing = 10;
                break;
             case "frame2" :
                $border = 1;
                $padding = 5;
                $spacing = 10;
                break;
            case "systemframe" :
                $border = 1;
                $padding = 5;
                $spacing = 10;
                break;

        }

        $frameSettings = array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
        return $frameSettings;
    }

    function getFrameData($frameStyle) {
        switch ($frameStyle) {
            case "rollFrame" :
                $frameData = $this->getFrameData_rollFrame();
                break;
            case "frame1" :
                $frameData = $this->getFrameData_frame1();
                break;
            case "frame2" :
                $frameData = $this->getFrameData_frame2();
                break;
            
            case "systemFrame" :
                $frameData = $this->getFrameData_systemFrame();
                break;
            default :
                $frameData = $this->getFrameData_own($frameStyle);
        }
        return $frameData;
    }

    function getFrameData_rollFrame() {
        $border = 1;
        $padding = 5;
        $spacing = 10;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame1() {
        $border = 1;
        $padding = 5;
        $spacing = 10;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame2() {
        $border = 1;
        $padding = 5;
        $spacing = 10;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_systemFrame() {
        $border = 1;
        $padding = 5;
        $spacing = 10;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }
    
    function getFrameData_own($frameStyle) {
        switch ($frameStyle) {
            case "unkown" :
                break;

            default :
                $border = 0;
                $padding = 0;
                $spacing = 0;
                return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
        }
        
    }

    function getSpecial_before($frameStyle,$contentData,$frameWidth,$textData=array()) {
        switch ($frameStyle) {
            case "rollFrame" :
                $specialData = $this->getSpecial_rollFrame_before($contentData,$frameWidth);
                break;
            case "frame1" :
                $specialData = $this->getSpecial_frame1_before($contentData,$frameWidth);
                break;
             case "frame2" :
                $specialData = $this->getSpecial_frame2_before($contentData,$frameWidth);
                break;
            
            case "systemFrame" :
                $specialData = $this->getSpecial_systemFrame_before($contentData,$frameWidth,$textData);
                break;
            
            default :
                $specialData = $this->getSpecial_own_before($frameStyle,$contentData,$frameWidth);
        }
        return $specialData;
    }



    function getSpecial_rollFrame_before($contentData) {}

    function getSpecial_frame1_before($contentData) {}

    function getSpecial_frame2_before($contentData){}
    
    function getSpecial_systemFrame_before($contentData,$frameWidth,$textData=array()){
        $res = cmsSystemFrame_frameStart($contentData, $frameWidth,$textData);
        return $res;
    }

    function getSpecial_own_before($frameStyle,$contentData){

    }


    function getSpecial_after($frameStyle,$contentData,$frameWidth,$textData=array()) {
        switch ($frameStyle) {
            case "rollFrame" :
                $specialData = $this->getSpecial_rollFrame_after($contentData,$frameWidth);
                break;
            case "frame1" :
                $specialData = $this->getSpecial_frame1_after($contentData,$frameWidth);
                break;
             case "frame2" :
                $specialData = $this->getSpecial_frame2_after($contentData,$frameWidth);
                break;
             case "systemFrame" :
                $specialData = $this->getSpecial_systemFrame_after($contentData,$frameWidth,$textData);
                break;
            
            default :
                $specialData = $this->getSpecial_own_after($frameStyle,$contentData,$frameWidth);
        }
        return $specialData;
    }

    function getSpecial_rollFrame_after($contentData) {}

    function getSpecial_frame1_after($contentData) {}

    function getSpecial_frame2_after($contentData){}

    function getSpecial_systemFrame_after($contentData,$frameWidth){
        $res = cmsSystemFrame_frameEnd($contentData, $frameWidth);
        return $res;
    }

    
    
    function getSpecial_own_after($frameStyle,$contentData){

    }

    function getSpecial_edit($frameStyle,$editContent,$frameWidth){
    switch ($frameStyle) {
            case "rollFrame" :
                $specialData = $this->getSpecial_rollFrame_edit($editContent,$frameWidth);
                break;
            case "frame1" :
                $specialData = $this->getSpecial_frame1_edit($editContent,$frameWidth);
                break;
             case "frame2" :
                $specialData = $this->getSpecial_frame2_edit($editContent,$frameWidth);
                break;
            default :
                $specialData = $this->getSpecial_own_edit($frameStyle,$editContent,$frameWidth);
        }
        return $specialData;
    }

    function getSpecial_rollFrame_edit($editContent,$frameWidth) {}

    function getSpecial_frame1_edit($editContent,$frameWidth) {}

    function getSpecial_frame2_edit($editContent,$frameWidth){}

    function getSpecial_own_edit($frameStyle,$editContent,$frameWidth){

    }

    function getStyles () {
        $styleList = array ("noFrame"=>"Kein Rahmen","frame1"=>"RahmenStil 1","frame2"=>"Rahmenstil 2","rollFrame"=>"Rahmen mit Rollstatus");
        $styleList["systemFrame"] = "Aktiver Rahmen";

        $addStyles = $this->addStyles();
        foreach ($addStyles as $key => $value) {
            $styleList[$key] = $value;
        }
        return $styleList;
    }

    
    function addStyles () {
        $styleList = array ();
        return $styleList;
    }

}


function cmsFrame_getInstance() {
    global $cmsName;
    $ownPhpFile = "cms/cms_contentFrame_own.php";
    $exist = file_exists($ownPhpFile);
    if ($exist) {
        require_once ($ownPhpFile);
        // echo ("Exist  $ownPhpFile <br />");
        $instance =  new cmsFrame();
    } else {

        $instance =  new cmsFrame_base();
    }
    return $instance;
}



function cmsFrame_getSettings($frameStyle) {
    $frameClass = cmsFrame_getInstance();
    return $frameClass->getSettings($frameStyle);
}


function cmsFrame_getStyles() {
    $frameClass = cmsFrame_getInstance();
    return $frameClass->getStyles($frameStyle);
}

function cmsFrame_getSpecial_edit($frameStyle, $editContent,$frameWidth) {
    $frameClass = cmsFrame_getInstance();
    return $frameClass->getSpecial_edit($frameStyle,$editContent,$frameWidth);
}

function cmsFrame_getSpecial_before($frameStyle,$contentData,$frameWidth,$textData=array()) {
    $frameClass = cmsFrame_getInstance();
    return $frameClass->getSpecial_before($frameStyle,$contentData,$frameWidth,$textData);
}


function cmsFrame_getSpecial_after($frameStyle,$contentData,$frameWidth,$textData=array()) {
    $frameClass = cmsFrame_getInstance();
    return $frameClass->getSpecial_after($frameStyle,$contentData,$frameWidth,$textData);
}



?>
