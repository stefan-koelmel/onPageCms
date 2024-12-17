<?php

function cmsSlider($type,$name,$contList=array(),$showData=array(),$width="",$height="",$outPut=1) {
    if (!$type) $type = "bxSlider";
    if (count($contList) == 0) return "";
    
    $out = "";
    $out .= cmsSlider_start($type,$name,$contList,$showData,$width,$height,0);
    $out .= cmsSlider_content($contList, $showData, $sliderFrameId, $sliderFrameClass, $sliderFrameStyle, 0);
    $out .= cmsSlider_end($type,$name,$contList,$showData,$width,$height,0);
    
    if ($outPut) echo ($out);
    return $out;
    
}


function cmsSlider_start($type,$name,$contList=array(),$showData=array(),$width="",$height="",$outPut=1) {
   
    if (!$type) $type = "bxSlider";
    $sliderShowCaption = 1;
            
    $innerContainer = 1;
    switch ($type) {
        case "bxSlider" :
            // http://bxslider.com
            $mainId = $name."_bxSlider";
            $mainClass = $name."_bxSlider";
            $mainStyle = "overflow:hidden;";
           // if ($width) $mainStyle .= "width:".$width."px;";
           // if ($height) $mainStyle .= "height:".$height."px;";
            
            // $sliderFrameStyle ="float:left;";
            $innerContainer = 0;
            break;
                
        case "slides" :
            // http://www.slidesjs.com/
            $mainId = "slider-slides";
            $mainClass = "slider-slides";    
            $mainStyle = "margin-top:20px;";

            $containerId = "";
            $containerClass = "slides_container";

            $slideFrameId = "";
            $slideFrameClass = "slide";
            $slideFrameStyle = "width:".$width."px;height:".$height."px;";

            $sliderCaptionClass = "caption";
            $sliderCaptionId = "";
            $sliderCaptionStyle = "bottom:0px;";
            $sliderCaptionStyle .= "z-index:500;position:absolute;bottom:-35px;left:0;height:30px;padding:5px 20px 0 0;background:#fff;background:rgba(1.0,1.0,1.0,.5);width:100%;font-size:1.3em;line-height:1.33;color:#333;border-top:1px solid #000;text-shadow:none;";
            break;
        
        case "coda" :
            $mainClass = "coda-slider";
            $mainId = "slider-id";
            break;
        default :
            $out .= "Unkown Slider '$type' <br />";

    }

    
    $out .= "<div class='$mainClass' id='$mainId' style='";
    if ($width) $out .= "width:".$width."px;";
    if ($height) $out .= "height:".$height."px;";
    if ($mainStyle) $out .= "$mainStyle";
    $out .= "' >\n ";


            
    // Innser Container
    if ($innerContainer) {
        $out .= "<div ";
        if ($containerId) $out .= "id='$containerId' ";
        if ($containerClass) $out .= "class='$containerClass' ";
        $out .= ">\n";
    }
    
    if ($outPut) echo ($out);
    return $out;
}

function cmsSlider_content($contList, $showData, $sliderFrameId,$sliderFrameClass,$sliderFrameStyle,$outPut=1) {
    $outPut = "";
    $i = 0;
    $i = 0;
    foreach($contList as $key => $content) {
        // Create Slide Container
        $out .= "<div ";
        if ($slideFrameId) $out .= "id='$slideFrameId' ";
        if ($slideFrameClass) $out .= "class='$slideFrameClass' ";
        if ($sliderFrameStyle) $out .= "style='$slideFrameStyle' ";
        $out .= ">\n";
        
        $zoomImage = 0;
        if (is_array($showData[zoomImage])) {
            $zoomImage = $showData[zoomImage][$i];
            $i++;
        }
            
        if ($zoomImage) {
            $out.= "<a href='$zoomImage' class='zoomimage'>";
        }
        
        
        $out .= $content;
        if ($zoomImage) {
            $out.= "</a>";
        }
        $out .= "</div>\n";
    }
    if ($outPut) echo ($out);
    return $out;
}

function cmsSlider_end($type,$name,$contList=array(),$showData=array(),$width="",$height="",$outPut=1) {
    $sliderShowCaption = 1;
    if (!$type) $type = "bxSlider";
    
    $innerContainer = 1;
    switch ($type) {
        case "bxSlider" :
            // http://bxslider.com
            $mainId = $name."_bxSlider";
            $mainClass = $name."_bxSlider";
            $mainStyle = "overflow:hidden;";
           // if ($width) $mainStyle .= "width:".$width."px;";
           // if ($height) $mainStyle .= "height:".$height."px;";
            
            // $sliderFrameStyle ="float:left;";
            $innerContainer = 0;
            break;
                
        case "slides" :
            // http://www.slidesjs.com/
            $mainId = "slider-slides";
            $mainClass = "slider-slides";    
            $mainStyle = "margin-top:20px;";

            $containerId = "";
            $containerClass = "slides_container";

            $slideFrameId = "";
            $slideFrameClass = "slide";
            $slideFrameStyle = "width:".$width."px;height:".$height."px;";

            $sliderCaptionClass = "caption";
            $sliderCaptionId = "";
            $sliderCaptionStyle = "bottom:0px;";
            $sliderCaptionStyle .= "z-index:500;position:absolute;bottom:-35px;left:0;height:30px;padding:5px 20px 0 0;background:#fff;background:rgba(1.0,1.0,1.0,.5);width:100%;font-size:1.3em;line-height:1.33;color:#333;border-top:1px solid #000;text-shadow:none;";
            break;
        
        case "coda" :
            $mainClass = "coda-slider";
            $mainId = "slider-id";
            break;
        default :
            $out .= "Unkown Slider '$type' <br />";

    }
        
    if ($innerContainer) {
        $out .= "</div>\n";
    }

    // end of Slider Frame
    // div_end($mainClass);
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
    if ($showData[loop]) $out .= "    auto: true,\n";
    else $out .= "     auto: false,\n";
    
    //loopAll
    if ($showData[notloop]) $out .= "    infiniteLoop: false,\n";
    else $out .= "    infiniteLoop: true,\n";
    
    
    switch ($showData[direction]) {
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
    $out .= "    captions: false,\n";
    
    // Transition Speed
    $speed = 1000;
    if ($showData[speed]) $speed = $showData[speed];
    $out .= "    speed: $speed,\n";
    
    // Div Stay Time
    $pause = 5000;
    if ($showData[pause]) $pause = $showData[pause];
    $out .= "    pause: $pause,\n";
    
    // Start Frame
    if ($showData[startFrame]) $out .= "    startingSlide: $showData[startFrame],\n";

    $out .= "autoControls:true,";
    

    // Navigation 
    if ($showData[navigate]) $out .= "    controls : true,\n";
    else $out .= "    controls : false,\n";
    
    // Pager 
    if ($showData[pager]) $out .= "   pager: true\n";
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
    
    if ($outPut) echo ($out);
    
    return $out;
}

?>
