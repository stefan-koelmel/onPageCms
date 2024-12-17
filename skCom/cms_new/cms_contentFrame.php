<?php // charset:UTF-8



class cmsFrame_base {
    function getSettings($frameStyle) {
        $border = 0;
        $padding = 0;
        $spacing = 0;
       
        $frameData = $this->getFrameData($frameStyle);
        if (is_array($frameData)) {
            $styleData = $this->getFrameStyle_data($frameStyle);
            if (is_array($styleData)) {
                foreach ($styleData[paddingData] as $key => $value) {
                    // echo ("add $key = $value <br />");
                    $frameData[$key] = $value;
                }    
            }
            return $frameData;
        }
       
        $border  = 0;
        $padding = 0;
        $spacing = 0;
        
        $frameSettings = array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
        return $frameSettings;
    }

    function getFrameData($frameStyle) {
        switch ($frameStyle) {
            case "noFrame" :
                $frameData = $this->getFrameData_noFrame();
                break;
            
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
                // show_array($frameData);
                break;
            default :
                $frameData = $this->getFrameData_own($frameStyle);
        }
        return $frameData;
    }
    
    
    function getFrameStyle_data($frameStyle) {
        if (!is_array($_SESSION[style])) $_SESSION[style] = array();
        if (!is_array($_SESSION[style][frame])) $_SESSION[style][frame] = array();
        
        $sessionData = $_SESSION[style][frame][$frameStyle];
        if (is_array($sessionData)) {
            // echo ("<h2>Get Style for $frameStyle from Sessiom </h2>");

        }        
        $out = "css";
        $style = cmsStyle_frameSettings($frameStyle,$out);
        if (!is_array($style)) return 0;
        // show_array($frameStyle);
        
        // MARGIN
        $margin = $style["margin"];
        $marginLeft = $style["margin-left"];
        if (is_null($marginLeft)) $marginLeft = $margin;
        $marginRight = $style["margin-right"];
        if (is_null($marginRight)) $marginRight = $margin;
        $marginTop = $style["margin-top"];
        if (is_null($marginTop)) $marginTop = $margin;
        $marginBottom = $style["margin-bottom"];
        if (is_null($marginBottom)) $marginBottom = $margin;
        $marginData = array("top"=>$marginTop,"right"=>$marginRight,"bottom"=>$marginBottom,"left"=>$marginLeft);
        // echo ("margin $margin ($marginTop / $marginRight / $marginBottom / $marginLeft) <br>");
        
        
        $padding = $style["padding"];
        $paddingLeft = $style["padding-left"];
        if (is_null($paddingLeft)) $paddingLeft = $padding;
        $paddingRight = $style["padding-right"];
        if (is_null($paddingRight)) $paddingRight = $padding;
        $paddingTop = $style["padding-top"];
        if (is_null($paddingTop)) $paddingTop = $padding;
        $paddingBottom = $style["padding-bottom"];
        if (is_null($paddingBottom)) $paddingBottom = $padding;
        $paddingData = array("top"=>$paddingTop,"right"=>$paddingRight,"bottom"=>$paddingBottom,"left"=>$paddingLeft);
        // echo ("padding $padding ($paddingTop / $paddingRight / $paddingBottom / $paddingLeft) <br>");
        
        
        $border = $style["border-width"];
        $borderLeft = $style["border-left-width"];
        if (is_null($borderLeft)) $borderLeft = $border;
        $borderRight = $style["border-right-width"];
        if (is_null($borderRight)) $borderRight = $border;
        $borderTop = $style["border-top-width"];
        if (is_null($borderTop)) $borderTop = $border;
        $borderBottom = $style["border-bottom-width"];
        if (is_null($borderBottom)) $borderBottom = $border;
        $borderData = array("top"=>$borderTop,"right"=>$borderRight,"bottom"=>$borderBottom,"left"=>$borderLeft);
        // echo ("border $border ($borderTop / $borderRight / $borderBottom / $borderLeft) <br>");
        
        
        $frameData = array();
        if (is_array($marginData))  $frameData[marginData] = $marginData;
        if (is_array($borderData))  $frameData[borderData] = $borderData;
        if (is_array($paddingData)) $frameData[paddingData] = $paddingData;
        
        $_SESSION[style][frame][$frameStyle] = $frameData;
        
        
        return $frameData;
        
        
        
    }
    

    function getFrameData_noFrame() {
        $border = 0;
        $padding = 0;
        $spacing = 0;
        $res = array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
        return $res;
    }
    
    
    function getFrameData_rollFrame() {
        $border = 1;
        $padding = 5;
        $spacing = 10;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame1() {
        $border = 1;
        $padding = 10;
        $spacing = 0;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_frame2() {
        $border = 2;
        $padding = 10;
        $spacing = 0;
        return array("border"=>$border, "padding"=>$padding, "spacing"=>$spacing);
    }

    function getFrameData_systemFrame() {
        $border = 1;
        $padding = 5;
        $spacing = 0;       
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
