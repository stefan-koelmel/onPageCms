$(".cmsEditLayoutValue").click(function(){
})


function layoutColor(setId) {
    if (setId) {
        if (setId.substr(0,7)== "button_") {
            buttonStr = setId.substr(7);
            button_color(buttonStr);
        }        
        if (setId.substr(0,5)== "text_") {
            textStr = setId.substr(5);
            text_color(textStr);
        }
        if (setId.substr(0,7)== "spacer_") {
            spacerStr = setId.substr(7);
            spacer_color(spacerStr);
            return 0;
        }
        
        
    }
    
   
    
    
    
    backgroundColor = $("#backgroundColor").val();
    if (backgroundColor) $(".cmsLayout_FrameSettings").css("background-color","#"+backgroundColor);

    borderColor = $("#borderColor").val();
    if (borderColor) $(".cmsLayout_FrameSettings").css("border-color","#"+borderColor);

    color = $("#color").val();
    if (color)  $(".cmsLayout_FrameSettings").css("color","#"+color);
    
    
    
    h1Color = $("#h1Color").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h1").children("h1").css("color","#"+h1Color);
    
    h1FrameColor = $("#h1FrameColor").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h1").css("border-color","#"+h1FrameColor);
    
    h2Color = $("#h2Color").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h2").children("h2").css("color","#"+h2Color);
    h2FrameColor = $("#h2FrameColor").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h2").css("border-color","#"+h2FrameColor);
    
    h3Color = $("#h3Color").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h3").children("h3").css("color","#"+h3Color);
    
    h4Color = $("#h4Color").val();
    $(".cmsLayout_FrameSettings").children(".textHeadline_h4").children("h4").css("color","#"+h4Color);
    

}

function layoutMargin() {
    margin = $("#margin").val();
    
    $(".cmsLayout_FrameSettings").css("margin",margin+"px");
    
    marginTop = $("#marginTop").val();
    marginRight = $("#marginRight").val();
    marginBottom = $("#marginBottom").val();
    marginLeft = $("#marginLeft").val();
    // alert ("Margin "+margin+" "+marginTop+" "+marginRight+" "+marginBottom+" "+marginLeft);
    if (marginTop) $(".cmsLayout_FrameSettings").css("margin-top",marginTop+"px");
    else $(".cmsLayout_FrameSettings").css("margin-top",margin+"px");
    $(".cmsLayout_FrameSettings").css("margin-right",marginRight+"px");
    $(".cmsLayout_FrameSettings").css("margin-bottom",marginBottom+"px");
    $(".cmsLayout_FrameSettings").css("margin-left",marginLeft+"px");
    
    
}



function layoutBorder() {
    border = $("#border").val();
    
    $(".cmsLayout_FrameSettings").css("border-width",border+"px");
    
    borderTop = $("#borderTop").val();
    borderRight = $("#borderRight").val();
    borderBottom = $("#borderBottom").val();
    borderLeft = $("#borderLeft").val();
    // alert ("Margin "+border+" "+borderTop+" "+borderRight+" "+borderBottom+" "+borderLeft);
    if (borderTop) $(".cmsLayout_FrameSettings").css("border-top-width",borderTop+"px");
    else $(".cmsLayout_FrameSettings").css("border-top-width",border+"px");
    $(".cmsLayout_FrameSettings").css("border-right-width",borderRight+"px");
    $(".cmsLayout_FrameSettings").css("border-bottom-width",borderBottom+"px");
    $(".cmsLayout_FrameSettings").css("border-left-width",borderLeft+"px");
    
    
    borderColor = $("#borderColor").val();
    $(".cmsLayout_FrameSettings").css("border-color","#"+borderColor);
    
    borderStyle = $("#borderStyle").val();
    // borderStyle = "dotted";
    $(".cmsLayout_FrameSettings").css("border-style",borderStyle);
}


function layoutRadius() {
    radius = $("#radius").val();
    if (radius) radius = radius+"px";
    else radius = "0";
    
    // $(".cmsLayout_FrameSettings").css("border-radius",radius+"px");
    
    radiusTop = $("#radiusTop").val();
    radiusRight = $("#radiusRight").val();
    radiusBottom = $("#radiusBottom").val();
    radiusLeft = $("#radiusLeft").val();
    
    radiusStr = "";
    if (radiusTop) radiusStr += radiusTop+"px ";
    else radiusStr += radius+" ";
    
    if (radiusRight) radiusStr += radiusRight+"px ";
    else radiusStr += radius+" ";
    
    if (radiusBottom) radiusStr += radiusBottom+"px ";
    else radiusStr += radius+" ";
    
    if (radiusLeft) radiusStr += radiusLeft+"px";
    else radiusStr += radius;
    
    $(".cmsLayout_FrameSettings").css("border-radius",radiusStr);
    
//    // alert ("Margin "+border-radius+" "+border-radiusTop+" "+border-radiusRight+" "+border-radiusBottom+" "+border-radiusLeft);
//    if (radiusTop) $(".cmsLayout_FrameSettings").css("border-radius-top",radiusTop+"px");
//    else $(".cmsLayout_FrameSettings").css("border-radius-top",border-radius+"px");
//    $(".cmsLayout_FrameSettings").css("border-radius-right",border-radiusRight+"px");
//    $(".cmsLayout_FrameSettings").css("border-radius-bottom",border-radiusBottom+"px");
//    $(".cmsLayout_FrameSettings").css("border-radius-left",border-radiusLeft+"px");
    
    
}

function layoutPadding() {
    padding = $("#padding").val();
    
    $(".cmsLayout_FrameSettings").css("padding",padding+"px");
    
    paddingTop = $("#paddingTop").val();
    paddingRight = $("#paddingRight").val();
    paddingBottom = $("#paddingBottom").val();
    paddingLeft = $("#paddingLeft").val();
    // alert ("Padding "+padding+" "+paddingTop+" "+paddingRight+" "+paddingBottom+" "+paddingLeft);
    if (paddingTop) $(".cmsLayout_FrameSettings").css("padding-top",paddingTop+"px");
    else $(".cmsLayout_FrameSettings").css("padding-top",padding+"px");
    $(".cmsLayout_FrameSettings").css("padding-right",paddingRight+"px");
    $(".cmsLayout_FrameSettings").css("padding-bottom",paddingBottom+"px");
    $(".cmsLayout_FrameSettings").css("padding-left",paddingLeft+"px");
    
    
}

function spacerChange(spacerStr) {
    
    height = $("#spacer_height").val();
    lineHeight = $("#spacer_lineHeight").val();
    
    
    backColor = $("#spacer_background-color").val();
    
    
    marginHeight = (height - lineHeight) / 2;
    
    lineColor = $("#spacer_lineColor").val();
    lineStyle = "solid";
    
    $(".cmsLayout_FrameSettings").css("margin",marginHeight+"px 0");
    $(".cmsLayout_FrameSettings").css("border-top-width",lineHeight+"px");
    $(".cmsLayout_FrameSettings").css("border-top-color","#"+lineColor);
    $(".cmsLayout_FrameSettings").css("border-top-style",lineStyle);
    $(".cmsLayout_FrameSettings").css("display","block");
    $(".cmsLayout_FrameSettings").css("height",0);
    
    
    
    
    
    
    // alert ("apacerChange height = "+height+" lineHeight="+lineHeight);
    
}

function spacer_color(spacerStr) {
    backColor = $("#spacer_backgroundColor").val();
    backColor = getColorStr(backColor);
   //  alert ("BackColro "+backColor);
    rollColor  = $("#spacer_backgroundRollColor").val();
    rollColor = getColorStr(rollColor);
    
    lineColor = $("#spacer_lineColor").val();
    lineColor = getColorStr(lineColor);
    $(".cmsLayout_FrameSettings").css("border-top-color",lineColor);
    lineStyle = "solid";
    $(".cmsLayout_FrameSettings").css("border-top-style",lineStyle);
    $(".cmsLayout_FrameSettings").css("display","block");
    
    $(".cmsLayout_FrameSettings").parent().css("background-color",backColor);
}


$(".cmsEditLayoutValue").live("change",function(){
    name = $(this).attr("id");
    value = $(this).val();
    
    if (name.substr(0,6) == "margin") layoutMargin();
    if (name.substr(0,7) == "padding") layoutPadding();
    if (name.substr(0,6) == "border") layoutBorder();
    if (name.substr(0,6) == "radius") layoutRadius();
    
    if (name.substr(0,8) == "h1Border") headLineBorder("h1");
    if (name.substr(0,8) == "h2Border") headLineBorder("h2");
    if (name.substr(0,8) == "h3Border") headLineBorder("h3");
    if (name.substr(0,8) == "h4Border") headLineBorder("h4");
    
    if (name.substr(0,7) == "button_") {
        buttonChangeStr = name.substr(7);
        buttonChange(buttonChangeStr);
    }
    
    if (name.substr(0,7) == "spacer_") {
        spacerStr = name.substr(7);
        spacerChange(spacerStr);
    }
    
    if (name.substr(0,5) == "text_") {
        textStr = name.substr(5);
        textChange(textStr);
    }
    // alert ("Change "+name.substr(0,5)+" value='"+value+"'")
})


function text_color(textStr) {
   
    splitList = textStr.split("_");
    type = splitList[0];
    startStr = "text_"+type+"_";
    
//    backColor = $("#"+startStr+"backColor").val();
//    backColor =  getColorStr(backColor);
//    $("#button_"+type).css("background-color",backColor);
//    
//    
//    borderColor = $("#"+startStr+"borderColor").val();
//    borderColor =  getColorStr(borderColor);
//    $("#button_"+type).css("border-color",borderColor);
    
    fontColor = $("#"+startStr+"fontColor").val();
    fontColor =  getColorStr(fontColor);
    $("#text_"+type).css("color",fontColor);
    
    // Shadow
    shadowColor = $("#"+startStr+"shadowColor").val();
    shadowColor =  getColorStr(shadowColor);
    shadowLeft   = $("#"+startStr+"shadowLeft").val();
    shadowRight  = $("#"+startStr+"shadowRight").val();
    maxShadow = shadowLeft;
    if (shadowRight > maxShadow) maxShadow = shadowRight;
   
    
    $("#text_"+type).css("text-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px "+shadowColor);    
//    
    
}

function text_font(textStr) {
    splitList = textStr.split("_");
    type = splitList[0];
    startStr = "text_"+type+"_";
    
    bold      = $("#"+startStr+"font_bold").val();
    kursiv    = $("#"+startStr+"font_kursiv").val();
    underline = $("#"+startStr+"font_underline").val();
    
    
    if (bold == "1") {
        $("#text_"+type).css("font-weight","bold");
    } else {
        $("#text_"+type).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $("#text_"+type).css("font-style","italic");
    } else {
        $("#text_"+type).css("font-style","normal");
    }
    
    if (underline == "1") {
        $("#text_"+type).css("text-decoration","underline");
    } else {
        $("#text_"+type).css("text-decoration","none");
    }
}

function textChange(textStr) {
    textChangeStr = textStr;
    splitList = textChangeStr.split("_");
    type = splitList[0];
    
    startStr = "text_"+type+"_";
    
    // borderRadius = $("#"+startStr+"borderRadius").val();
    shadowLeft   = $("#"+startStr+"shadowLeft").val();
    shadowRight  = $("#"+startStr+"shadowRight").val();
    
    fontSize     = $("#"+startStr+"font_size").val();
    
    maxShadow = shadowLeft;
    if (shadowRight > maxShadow) maxShadow = shadowRight;
    shadowColor = $("#"+startStr+"shadowColor").val();
    // maxShadow = maxShadow * 2;
    
    // $("#text_"+type).css("border-radius",borderRadius+"px");
    
    $("#text_"+type).css("text-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px #"+shadowColor);
    $("#text_"+type).css("font-size",fontSize+"px");
    
  
}

$(".layoutText").mouseenter(function(){
    textStr = $(this).attr("id");
    splitList = textStr.split("_");
    main = splitList[0];
    type = splitList[1];
    startStr = main+"_"+type+"_";
    
//    backColor = $("#"+startStr+"backRollColor").val();
//    backColor =  getColorStr(backColor);
//    $("#text_"+type).css("background-color",backColor);
//    
//    borderColor = $("#"+startStr+"borderRollColor").val();
//    borderColor =  getColorStr(borderColor);
//    $("#text_"+type).css("border-color",borderColor);
    
    fontColor = $("#"+startStr+"fontRollColor").val();
    fontColor =  getColorStr(fontColor);
    $("#text_"+type).css("color",fontColor);
    
    
    bold      = $("#"+startStr+"fontRoll_bold").val();
    kursiv    = $("#"+startStr+"fontRoll_kursiv").val();
    underline = $("#"+startStr+"fontRoll_underline").val();
    
    if (bold == "1") {
        $("#text_"+type).css("font-weight","bold");
    } else {
        $("#text_"+type).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $("#text_"+type).css("font-style","italic");
    } else {
        $("#text_"+type).css("font-style","normal");
    }
    
    if (underline == "1") {
        $("#text_"+type).css("text-decoration","underline");
    } else {
        $("#text_"+type).css("text-decoration","none");
    }
    
     
})


$(".layoutText").mouseleave(function(){
    textStr = $(this).attr("id");
    splitList = textStr.split("_");
    main = splitList[0];
    type = splitList[1];
    startStr = main+"_"+type+"_";
    
//    backColor = $("#"+startStr+"backColor").val();
//    backColor =  getColorStr(backColor);
//    $("#text_"+type).css("background-color",backColor);
//    
//    borderColor = $("#"+startStr+"borderColor").val();
//    borderColor =  getColorStr(borderColor);
//    $("#text_"+type).css("border-color",borderColor);

    bold      = $("#"+startStr+"font_bold").val();
    kursiv    = $("#"+startStr+"font_kursiv").val();
    underline = $("#"+startStr+"font_underline").val();
    
    if (bold == "1") {
        $("#text_"+type).css("font-weight","bold");
    } else {
        $("#text_"+type).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $("#text_"+type).css("font-style","italic");
    } else {
        $("#text_"+type).css("font-style","normal");
    }
    
    if (underline == "1") {
        $("#text_"+type).css("text-decoration","underline");
    } else {
        $("#text_"+type).css("text-decoration","none");
    }

    
    fontColor = $("#"+startStr+"fontColor").val();
    fontColor =  getColorStr(fontColor);
    $("#text_"+type).css("color",fontColor);
    
     
})


function buttonChange(buttonStr) {
    buttonChangeStr = buttonStr;
    splitList = buttonChangeStr.split("_");
    type = splitList[0];
    
    startStr = "button_"+type+"_";
    
    borderRadius = $("#"+startStr+"borderRadius").val();
    shadowLeft   = $("#"+startStr+"shadowLeft").val();
    shadowRight  = $("#"+startStr+"shadowRight").val();
    
    fontSize     = $("#"+startStr+"font_size").val();
    
    maxShadow = shadowLeft;
    if (shadowRight > maxShadow) maxShadow = shadowRight;
    shadowColor = $("#"+startStr+"shadowColor").val();
    // maxShadow = maxShadow * 2;
    
    $("#button_"+type).css("border-radius",borderRadius+"px");
    
    $("#button_"+type).css("box-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px #"+shadowColor);
    $("#button_"+type).css("font-size",fontSize+"px");
    
    
   //  $(".cmsLayout_FrameSettings").css("padding",padding+"px");
    
//    paddingTop = $("#paddingTop").val();
//    paddingRight = $("#paddingRight").val();
//    paddingBottom = $("#paddingBottom").val();
//    paddingLeft = $("#paddingLeft").val();
    // alert ("Button "+startStr+" rad="+borderRadius+" sh-left="+shadowLeft+" sh-right"+shadowRight);
    
    
    
    // alert ("ButtonChange "+type);
}

function button_font(buttonStr) {
    splitList = buttonStr.split("_");
    type = splitList[0];
    startStr = "button_"+type+"_";
    
    bold      = $("#"+startStr+"font_bold").val();
    kursiv    = $("#"+startStr+"font_kursiv").val();
    underline = $("#"+startStr+"font_underline").val();
    
    
    if (bold == "1") {
        $("#button_"+type).css("font-weight","bold");
    } else {
        $("#button_"+type).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $("#button_"+type).css("font-style","italic");
    } else {
        $("#button_"+type).css("font-style","normal");
    }
    
    if (underline == "1") {
        $("#button_"+type).css("text-decoration","underline");
    } else {
        $("#button_"+type).css("text-decoration","none");
    }
    
    
    // alert ("button_font type="+type+" hgjh");
}

function button_color(buttonStr) {
   
    splitList = buttonStr.split("_");
    type = splitList[0];
    startStr = "button_"+type+"_";
    
    backColor = $("#"+startStr+"backColor").val();
    backColor =  getColorStr(backColor);
    $("#button_"+type).css("background-color",backColor);
    
    
    borderColor = $("#"+startStr+"borderColor").val();
    borderColor =  getColorStr(borderColor);
    $("#button_"+type).css("border-color",borderColor);
    
    fontColor = $("#"+startStr+"fontColor").val();
     fontColor =  getColorStr(fontColor);
    $("#button_"+type).css("color",fontColor);
    
    // Shadow
    shadowColor = $("#"+startStr+"shadowColor").val();
    shadowColor =  getColorStr(shadowColor);
    shadowLeft   = $("#"+startStr+"shadowLeft").val();
    shadowRight  = $("#"+startStr+"shadowRight").val();
    maxShadow = shadowLeft;
    if (shadowRight > maxShadow) maxShadow = shadowRight;
   
    
    $("#button_"+type).css("box-shadow",shadowLeft+"px "+shadowRight+"px "+maxShadow+"px "+shadowColor);    
    
    
}


function getColorStr(color) {
    // alert ("Color ="+color);
    if (color == "none") return "none";
    if (color == "trans") return "inherit";
    return "#"+color;
}

$(".layoutButton").mouseenter(function(){
    buttonStr = $(this).attr("id");
    splitList = buttonStr.split("_");
    main = splitList[0];
    type = splitList[1];
    startStr = main+"_"+type+"_";
    
    backColor = $("#"+startStr+"backRollColor").val();
    backColor =  getColorStr(backColor);
    $("#button_"+type).css("background-color",backColor);
    
    borderColor = $("#"+startStr+"borderRollColor").val();
    borderColor =  getColorStr(borderColor);
    $("#button_"+type).css("border-color",borderColor);
    
    fontColor = $("#"+startStr+"fontRollColor").val();
    fontColor =  getColorStr(fontColor);
    $("#button_"+type).css("color",fontColor);
    
    
    bold      = $("#"+startStr+"fontRoll_bold").val();
    kursiv    = $("#"+startStr+"fontRoll_kursiv").val();
    underline = $("#"+startStr+"fontRoll_underline").val();
    
    if (bold == "1") {
        $("#button_"+type).css("font-weight","bold");
    } else {
        $("#button_"+type).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $("#button_"+type).css("font-style","italic");
    } else {
        $("#button_"+type).css("font-style","normal");
    }
    
    if (underline == "1") {
        $("#button_"+type).css("text-decoration","underline");
    } else {
        $("#button_"+type).css("text-decoration","none");
    }
    
     
})


$(".layoutButton").mouseleave(function(){
    buttonStr = $(this).attr("id");
    splitList = buttonStr.split("_");
    main = splitList[0];
    type = splitList[1];
    startStr = main+"_"+type+"_";
    
    backColor = $("#"+startStr+"backColor").val();
    backColor =  getColorStr(backColor);
    $("#button_"+type).css("background-color",backColor);
    
    borderColor = $("#"+startStr+"borderColor").val();
    borderColor =  getColorStr(borderColor);
    $("#button_"+type).css("border-color",borderColor);
    
    fontColor = $("#"+startStr+"fontColor").val();
    fontColor =  getColorStr(fontColor);
    $("#button_"+type).css("color",fontColor);
    
     
})

$(".cmsLayout_FrameSettings").mouseenter(function(){
    backgroundRollColor = $("#backgroundRollColor").val();
    if (backgroundRollColor) {
        $(".cmsLayout_FrameSettings").css("background-color","#"+backgroundRollColor);
    }
    borderRollColor = $("#borderRollColor").val();
    if (borderRollColor) {
        $(".cmsLayout_FrameSettings").css("border-color","#"+borderRollColor);
    }

    rollColor = $("#rollColor").val();
    if (rollColor)  $(".cmsLayout_FrameSettings").css("color","#"+rollColor);
    
    h1Color = $("#h1RollColor").val();
    if (h1Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h1").children("h1").css("color","#"+h1Color);
    h1FrameColor = $("#h1BorderRollColor").val();
    if (h1FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h1").css("border-color","#"+h1FrameColor);
    
    h2Color = $("#h2RollColor").val();
    if (h2Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h2").children("h2").css("color","#"+h2Color);
    h2FrameColor = $("#h2BorderRollColor").val();
    if (h2FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h2").css("border-color","#"+h2FrameColor);
    
    h3Color = $("#h3RollColor").val();    
    if (h3Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h3").children("h3").css("color","#"+h3Color);
    h3FrameColor = $("#h3BorderRollColor").val();
    if (h3FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h3").css("border-color","#"+h3FrameColor);
    
    h4Color = $("#h4RollColor").val();
    if (h4Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h4").children("h4").css("color","#"+h4Color);
    h4FrameColor = $("#h4BorderRollColor").val();
    if (h4FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h4").css("border-color","#"+h4FrameColor);
    
    
})


$(".cmsLayout_FrameSettings").mouseleave(function(){
    backgroundColor = $("#backgroundColor").val();
    if (backgroundColor) {
        $(".cmsLayout_FrameSettings").css("background-color","#"+backgroundColor);
    }
    borderColor = $("#borderColor").val();
    if (borderColor) {
        $(".cmsLayout_FrameSettings").css("border-color","#"+borderColor);
    }

    color = $("#color").val();
    if (color)  $(".cmsLayout_FrameSettings").css("color","#"+color);
    
    h1Color = $("#h1Color").val();
    if (h1Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h1").children("h1").css("color","#"+h1Color);
    h1FrameColor = $("#h1BorderColor").val();
    if (h1FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h1").css("border-color","#"+h1FrameColor);
    
    h2Color = $("#h2Color").val();
    if (h2Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h2").children("h2").css("color","#"+h2Color);
    h2FrameColor = $("#h2BorderColor").val();
    if (h2FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h2").css("border-color","#"+h2FrameColor);
    
    h3Color = $("#h3Color").val();
    if (h3Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h3").children("h3").css("color","#"+h3Color);
    h3FrameColor = $("#h3BorderColor").val();
    if (h3FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h3").css("border-color","#"+h3FrameColor);
    
    h4Color = $("#h4Color").val();
    if (h4Color) $(".cmsLayout_FrameSettings").children(".textHeadline_h4").children("h4").css("color","#"+h4Color);
    h4FrameColor = $("#h4BorderColor").val();
    if (h4FrameColor) $(".cmsLayout_FrameSettings").children(".textHeadline_h4").css("border-color","#"+h4FrameColor);
    
    
})

function headLine(headKey) {
    
    bold      = $("#"+headKey+"Style_bold").val();
    kursiv    = $("#"+headKey+"Style_kursiv").val();
    underline = $("#"+headKey+"Style_underline").val();
    size      = $("#"+headKey+"Sytle_size").val();
    
    if (size) {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("font-size",size+"px");
    }
    
    if (bold == "1") {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("font-weight","bold");
    } else {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("font-weight","normal");
    }
    
    
    if (kursiv == "1") {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("font-style","italic");
    } else {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("font-style","normal");
    }
    
    if (underline == "1") {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("text-decoration","underline");
    } else {
        $(".cmsLayout_FrameSettings").children(".textHeadline").children(headKey).css("text-decoration","none");
    }
    // alert("HEADLINE "+headKey+ "bold = "+bold+" kursiv = "+kursiv+" underline = "+underline);
    
}

function  headLineBorder(headKey) {
 //    alert ("headline Border "+headKey);
    border = $("#"+headKey+"Border").val();
    
    $(".cmsLayout_FrameSettings").children(".textHeadline_"+headKey).css("border-width",border+"px");
    
    borderTop = $("#"+headKey+"BorderTop").val();
    borderRight = $("#"+headKey+"BorderRight").val();
    borderBottom = $("#"+headKey+"BorderBottom").val();
    borderLeft = $("#"+headKey+"BorderLeft").val();
    
    borderColor = $("#"+headKey+"BorderColor").val();
    borderStyle = $("#"+headKey+"BorderStyle").val();
    style = "solid";
    color = "#f00";
    
    $(".cmsLayout_FrameSettings").children(".textHeadline_"+headKey).css("border-top",borderTop+"px "+borderStyle+" #"+borderColor);
    $(".cmsLayout_FrameSettings").children(".textHeadline_"+headKey).css("border-right",borderRight+"px "+borderStyle+" #"+borderColor);
    $(".cmsLayout_FrameSettings").children(".textHeadline_"+headKey).css("border-bottom",borderBottom+"px "+borderStyle+" #"+borderColor);
    $(".cmsLayout_FrameSettings").children(".textHeadline_"+headKey).css("border-left",borderLeft+"px "+borderStyle+" #"+borderColor);
   
}


$(".cmsEditHeadLine ").click(function(){
    id = $(this).attr("id");
    useId = id.substr(9);
    headKey = useId.substr(0,2);
    // alert ("id='"+id+"' useId='"+useId+"' headKey='"+headKey+"'");
    if (headKey == "h1" ) headLine(headKey);
    if (headKey == "h2" ) headLine(headKey);
    if (headKey == "h3" ) headLine(headKey);
    if (headKey == "h4" ) headLine(headKey);
    
   
    if (useId.substr(0,7) == "button_") {
        buttonStr = useId.substr(7); 
        button_font(buttonStr);
    }
    if (useId.substr(0,5) == "text_") {
        textStr = useId.substr(5); 
        text_font(textStr);
    }
    
    
})



$(document).ready(function(){
   // alert ("cms_admin_layout.js");
})


