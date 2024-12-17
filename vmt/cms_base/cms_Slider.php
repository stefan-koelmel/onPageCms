<?php

function cmsSlider($type,$name,$contList=array(),$showData=array(),$width="",$height="") {
    if (!$type) $type = "bxSlider";
    
    
        
        
    /// SLIDER
    if (count($contList) == 0) return "";
    
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
            echo ("Unkown Slider '$type' <br />");

    }

    
    echo ("<div class='$mainClass' id='$mainId' style='");
    if ($width) echo("width:".$width."px;");
    if ($height) echo ("height:".$height."px;");
    if ($mainStyle) echo ("$mainStyle");
    echo ("' >\n ");


            
    // Innser Container
    if ($innerContainer) {
        echo ("<div ");
        if ($containerId) echo ("id='$containerId' ");
        if ($containerClass) echo ("class='$containerClass' ");
        echo (">\n");
    }
    
    foreach($contList as $key => $content) {
        // Create Slide Container
        echo ("<div ");
        if ($slideFrameId) echo ("id='$slideFrameId' ");
        if ($slideFrameClass) echo ("class='$slideFrameClass' ");
        if ($sliderFrameStyle) echo ("style='$slideFrameStyle' ");
        echo (">\n");
        
        echo($content);
        

       

        echo ("</div>\n");




    }
            
    if ($innerContainer) {
        echo ("</div>\n");
    }

    // end of Slider Frame
    // div_end($mainClass);
    echo ("</div>\n");      
    
    // echo ("<h1>Javascrippt</h1>");
    // add JAVASCRIPT
    
    
    echo ("<script type='text/javascript'>\n");
    echo( "$(document).ready(function() {\n");
    echo ("$(function(){\n");
    echo (" var ".$name."_Slider = $('#".$name."_bxSlider').bxSlider({\n");
    
    //loop
    if ($showData[loop]) echo ("    auto: true,\n");
    else echo ("     auto: false,\n");
  
    switch ($showData[direction]) {
        case "vertical" :
            echo ("    mode: 'vertical',\n");
            break;
        case "horizontal" :
            echo ("    mode: 'horizontal',\n");
            break;
        case "fade" :
            echo ("     mode: 'fade',\n"); 
            break;
        default :
           echo ("     mode: 'horizontal',\n");  
    }
    // horizontal fade vertical
    // echo ('    mode: "vertical",');
    echo ("    captions: false,\n");
    
    // Transition Speed
    $speed = 1000;
    if ($showData[speed]) $speed = $showData[speed];
    echo ("    speed: $speed,\n");
    
    // Div Stay Time
    $pause = 5000;
    if ($showData[pause]) $pause = $showData[pause];
    echo ("    pause: $pause,\n");
    
    // Start Frame
    if ($showData[startFrame]) echo ("    startingSlide: $showData[startFrame],\n");
    
    // Navigation 
    if ($showData[navigate]) echo ("    controls : true,\n");
    else echo ("    controls : false,\n");
    
    // Pager 
    if ($showData[pager]) echo ("   pager: true\n");
    else echo ("   pager: false\n");
    
    echo ("});\n\n");
    //  calenderSlider_24_back
    
    //calendarSlider_24_back
    // calendarSlider_24_back
    echo ("$('.".$name."_back').click(function(){\n");
    // echo (" alert('click');\n");
    echo (" ".$name."_Slider.goToPreviousSlide();\n");
    echo ("})\n\n");
    
    echo ("$('.".$name."_next').click(function(){\n");
    // echo (" alert('click');\n");
    echo (" ".$name."_Slider.goToNextSlide();\n");
    echo ("})\n\n");
    
    echo ("});\n");
    echo ("})\n");
    
    
    echo ("</script>\n");
    
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
