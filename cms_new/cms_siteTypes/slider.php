<?php
class slider {
    
    private static $lgType = "sliderInput";
    private static $sliderType = "bxSlider";
    
    public static function show($name,$contentList,$contentData,$showData=array(),$type="") {
        if ($type) self::$sliderType = $type;
        
        $sliderData = self::sliderData($contentData,$showData);
        
        $out = "";
        switch (self::$sliderType) {
            case "bxSlider" : $out .= self::bxStart($name,$sliderData); break;
            default :
                $out .= "unkown SLiderType ".self::$sliderType;
        }
        
        foreach ($contentList as $key => $value) {
            $out .= $value;
        }
        
        switch (self::$sliderType) {
            case "bxSlider" : $out .= self::bxEnd($name,$sliderData); break;
            default :
                $out .= "unkown SLiderType ".self::$sliderType;
        }
        
        return $out;
        
        
    }
    
    
    public static function start($name,$contentData,$showData=array(),$type="") {
        if ($type) self::$sliderType = $type;
        
        
        
        $sliderData = self::sliderData($contentData,$showData);
        
        $out = "";
        switch (self::$sliderType) {
            case "bxSlider" : $out .= self::bxStart($name,$sliderData); break;
            default :
                $out .= "unkown SLiderType ".self::$sliderType;
        }
        
        return $out;
    }
    
    public static function end($name,$contentData,$showData=array(),$type=0){
        if ($type) self::$sliderType = $type;
        
        $sliderData = self::sliderData($contentData,$showData);
        $out = "";
        switch (self::$sliderType) {
            case "bxSlider" : $out .= self::bxEnd($name,$sliderData); break;
            default :
                $out .= "unkown SLiderType ".self::$sliderType;
        }
        
        return $out;
    }

    public static function styleData($wireframe,$type=0) {
        if ($type) self::$sliderType = $type;

        switch (self::$sliderType) {
            case "bxSlider" : $res = self::bxStyleData($wireframe); break;
            default :
                $res = "unkown SLiderType ".self::$sliderType;
        }

        return $res;
    }

    public static function input($editContent,$editType="data") {
        
        $lgType = "sliderInput";

        switch ($editType) {
            case "data" : $editStart = "slider"; break;
            case "image" : $editStart = "imageSlider"; break;

        }
        
        
        $res = array();
        $addData = array();
        $addData["text"] = lg::lga($lgType,"change","","Wechsel");
        $direction = $editContent[data][$editStart."Direction"];
        $input  = self::slider_direction_select($direction,"editContent[data][".$editStart."Direction]",array());
        $addData["input"] = $input;
        $addData[mode] = "Simple";
        $res[] = $addData;


        $addData = array();
        $addData["text"] = lg::lga($lgType,"loop","","Auto Loop");
        $loop = $editContent[data][$editStart."Loop"];
        $checked = "";
        if ($loop) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][".$editStart."Loop]' $checked >";
        $addData[mode] = "Simple";
        $res[] = $addData;

        $addData = array();
        $timeAdd = lg::lga($lgType,"timeAdd","","in Milli-Sekunden");
        $addData["text"] = lg::lga($lgType,"pause","","Zeit für Bild in ms");
        $addData["input"] = "<input name='editContent[data][".$editStart."Pause]' style='width:100px;' value='".$editContent[data][$editStart."Pause"]."'/>".$timeAdd;
        $addData[mode] = "More";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = lg::lga($lgType,"speed","","Zeit für Wechsel in ms");
        
        $addData["input"] = "<input name='editContent[data][".$editStart."Speed]' style='width:100px;' value='".$editContent[data][$editStart."Speed"]."'/>".$timeAdd;
       
        $addData[mode] = "More";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = lg::lga($lgType,"selector","","Navigation");
        $navigate = $editContent[data][$editStart."Navigate"];
        $checked = "";
        if ($navigate) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][".$editStart."Navigate]' $checked />";
        $addData[mode] = "Admin";
        $res[] = $addData;

        $addData = array();
        $addData["text"] = lg::lga($lgType,"pager","","Einzelauswahl");
        $pager = $editContent[data][$editStart."Pager"];
        $checked = "";
        if ($pager) $checked = " checked='checked'";
        $addData["input"] = "<input type='checkbox' name='editContent[data][".$editStart."Pager]' $checked />";
        $addData[mode] = "Admin";
        $res[] = $addData;
        return $res;
    }
    
    private function bxStart($name,$sliderData) {
        $out = ""; // SLIDER START $name <br>";
        
        //foreach ($sliderData as $key => $value) echo ("sliderData $key => $value <br>");
        
        $sliderShowCaption = 1;
        
        $width = $sliderData[width];
        $height = $sliderData[height];
      
        $mainId = $name."_bxSlider";
        $mainClass = $name."_bxSlider";
        $mainStyle = "overflow:hidden;";
      
        $out .= "<div class='$mainClass' id='$mainId' style='";
        if ($width) $out .= "width:".$width."px;";
        if ($height) $out .= "height:".$height."px;";
        if ($mainStyle) $out .= "$mainStyle";
        $out .= "' >\n ";
        return $out;
    }
    
    private function bxEnd($name,$sliderData) {
        $out = "";
        
     
        // http://bxslider.com
   
        $out .= "</div>\n";      

        // $out .= "<h1>Javascrippt</h1>");
        // add JAVASCRIPT


        $out .= "<script type='text/javascript'>\n";
        $out .= "var ".$name."_Slider = 'not' ;\n\n";
        $out .= "$(document).ready(function() {\n";

        $out .= "$(function(){\n";
       //  $out .= "alert(".$name."_Slider);";
        $out .= " ".$name."_Slider = $('#".$name."_bxSlider').bxSlider({\n";


        //loop
        if ($sliderData[loop]) $out .= "    auto: true,\n";
        else $out .= "     auto: false,\n";

        //loopAll
        if ($sliderData[notloop]) $out .= "    infiniteLoop: false,\n";
        else $out .= "    infiniteLoop: true,\n";


        switch ($sliderData[direction]) {
            case "vertical" :
                $out .= "    mode: 'vertical',\n";
                break;
            case "horizontal" :
                $out .= "    mode: 'horizontal',\n";
                break;
            case "fade" :
                $out .= "     mode: 'fade',\n"; 
                break;
            default :
               $out .= "     mode: 'horizontal',\n";  
        }
        // horizontal fade vertical
        // $out .= '    mode: "vertical",');
        $out .= "    captions: true,\n";

        // Transition Speed
        $speed = 1000;
        if ($sliderData[speed]) $speed = $sliderData[speed];
        $out .= "    speed: $speed,\n";
        
        // Div Stay Time
        $pause = 5000;
        if ($sliderData[pause]) $pause = $sliderData[pause];
        $out .= "    pause: $pause,\n";

        // Start Frame
        if ($sliderData[startFrame]) $out .= "    startingSlide: $sliderData[startFrame],\n";

        // $out .= "autoControls:true,";


        // Navigation 
        if ($sliderData[navigate]) $out .= "    controls : true,\n";
        else $out .= "    controls : false,\n";

        // Pager 
        if ($sliderData[pager]) $out .= "   pager: true\n";
        else $out .= "   pager: false\n";



        $out .= "});\n\n";
        //  calenderSlider_24_back

        //calendarSlider_24_back
        // calendarSlider_24_back
    //    $out .= "$('.".$name."_back').click(function(){\n";
    //    // $out .= " alert('click');\n");
    //    $out .= " ".$name."_Slider.goToPreviousSlide();\n";
    //    $out .= "})\n\n";
    //
    //    $out .= "$('.".$name."_next').click(function(){\n";
    //    // $out .= " alert('click');\n");
    //    $out .= " ".$name."_Slider.goToNextSlide();\n";
    //    $out .= "})\n\n";
    //
    //    $out .= "function slider_go(nr) {\n";
    //    $out .= "  alert('go'+nr');\n";
    //    $out .= "}\n\n";


        $out .= "});\n";
        $out .= "})\n";

        $out .= "</script>\n";
    
    
        return $out;
    }

    private function bxStyleData($wireframe) {
        $res = array();
        if ($wireframe) {
            $res[padding] = 0;
        } else {
            $res[padding] = 5;
        }
        return $res;

    }
    
    private function sliderData($contentData,$showData=array()) {
        
        $sliderData = array();
        $sliderData[contentId] = $contentData[id];

        $showType = $showData[showType];
        if (!$showType) $showType = "data";
    
        $data = $contentData[data];
        if (!is_array($data)) $data = array();
        
        
        foreach ($data as $key => $value) {
            $add = 1;
            switch ($showType) {
                case "data" :
                    if (substr($key,0,6) != "slider") $add = 0;
                    else $newKey = substr($key,6);
                    break;
                case "image" : 
                    if (substr($key,0,11) != "imageSlider") $add = 0;
                    else $newKey = substr($key,11); break;
                    
                    $add = 0;
                    break;
                default :
                    echo ("unkown $showType in slider::sliderData() <br>");
            }
            if (!$add) continue;
           //  echo ("$newKey => $value <br>");
           
            $newKey = lcfirst($newKey);
            $sliderData[$newKey] = $value;            
        }
        
        foreach ($showData as $key => $value) $sliderData[$key]=$value;
        return $sliderData;
    }
    
    
    
    private function slider_direction_getList() {
        $res = array();
        $res["horizontal"] = "Horizontal";
        $res["vertical"] = "Vertikal";
        $res["fade"] = "Überblendung";

        $ownList = self::slider_direction_getOwnList();
        foreach ($ownList as $key => $value) {
            $res[$key] = $value;
        }
        $lgType = "sliderInput";
        
        foreach ($res as $key => $value) {
            $lg = lg::lga($lgType,"change_".$key,"",$value);
            $res[$key] = $lg;
        }
        
        return $res;
    }

    private function slider_direction_getOwnList() {
        return array();
    }

    private function slider_direction_select($code,$dataName,$showData=array()) {
        $selectList = self::slider_direction_getList();
        $str = "";
        //$str.= "function categoryList_clickAction_select($code,$dataName,$showData)<br />";
        $str.= "<select name='$dataName' class='cmsSelectType'  style='min-width:80px;' ";
        if ($showData[submit]) $str.= "onChange='submit()' ";
        $str .= "value='$code' >";

        $str.= "<option value='0'";
        if (!$code) $str.= " selected='1' ";

        $noChange = lg::lga(self::$lgType,"change_default");
        
        $str.= ">$noChange</option>";

        foreach ($selectList as $key => $value) {
            if (is_string($value)) {
                $str.= "<option value='$key'";
                if ($key == $code)  $str.= " selected='1' ";
                $str.= ">$value</option>";
            }
        }
        $str.= "</select>";
        return $str;
    }
}

?>
