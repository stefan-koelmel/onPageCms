$(".dateMonthDay").live("mouseenter",function(){
    $(this).css("cursor","pointer");    
})


$(".dateMonthDay").live("mouseleave",function(){
    $(this).css("cursor","default");
})


$(".dateMonthDay").live("click",function(){
    
    clickId = $(this).attr("id");
    // alert (clickId);
    dateFrameList = clickId.split("_");    
    dateFrameId = dateFrameList[0];
    
    datum = dateFrameList[1];
    
   
   
    paramStr = $(".dateMonthList_"+dateFrameId).children(".hiddenData").text();
    param = getParamList(paramStr);
    clickAction = param["clickAction"]; // $(".dateMonthList_"+fId).attr("clickAction");
    clickTarget = param["clickTarget"]; // $(".dateMonthList_"+fId).attr("clickTarget");
    //alert ("Action = "+clickAction+"\nTarget = "+clickTarget);
    if (clickAction) {
       
        if (clickTarget == "page") {
            clickUrl = param["clickUrl"]; //  $(".dateMonthList_"+fId).attr("clickUrl");
           if (clickAction == "showDate") {
               hasAsk = clickUrl.indexOf("?");
               if (hasAsk>0) clickUrl += "&";
               else clickUrl += "?";
               
                clickUrl = clickUrl + "date="+datum;
                location = clickUrl;
                //alert ("go URL "+clickUrl);
           }
           if (clickAction == "showMonth") {
                clickUrl = clickUrl + "?month="+datum;
                location = clickUrl;
                alert ("go URL "+clickUrl);
           }
        }
    } else {
        //  alert ("No ClickAction ("+fId+") "+clickAction+" / "+clickTarget+" / "+clickUrl+" for Datum "+datum);
    }
    
    

})


//$(".dateNavigateBack").live("mouseenter",function(){
//    $(this).css("cursor","pointer");
//})
//
//$(".dateNavigateFor").live("mouseleave",function(){
//    $(this).css("cursor","default");
//})

function calendar_go(dateFrameId,direction) {
    paramStr = $(".calendarFrame_"+dateFrameId).children('.hiddenData').text();
    // alert(paramStr+" / "+dateFrameId);
    param = getParamList(paramStr);
    // dateFrameId = param["dateFrame_id"];
    url = param["navigateUrl"];
    actMonth = param["actMonth"];

//    paramStr = $(".calendarFrame_"+dateFrameId).attr("name");
//    param = getParamList(paramStr);
//
//    url = param["navigateUrl"];
//    actMonth = param["actMonth"];
    



    //url = $(".calendarFrame_"+dateFrameId).attr("navigateUrl");
    //actMonth = $(".calendarFrame_"+dateFrameId).attr("actMonth");
    url += "&actMonth="+actMonth;
    url += "&direction="+direction;
    url += "&width="+$(".calendarFrame_"+dateFrameId).width();
    url += "&frameId="+dateFrameId;
    // alert ("URL"+url+"\n month"+actMonth);
    $.get(url,function(text){
        // alert(text);
        mainList = text.split("|");
        param["actMonth"] = mainList[1];
        paramClass = getParamClass(param);
        // alert(mainList[2]);
        // $(".calendarFrame_"+dateFrameId).attr("name",paramStr);
        // $(".calendarFrame_"+dateFrameId).attr("actMonth",mainList[1]);
        $(".calendarFrame_"+dateFrameId).html(paramClass+"\n"+mainList[2]);
        // alert(mainList[0])
    })
}

$(".dateNavigateBack").live("click",function(){
    clickId = $(this).attr("id");
    dateFrameList = clickId.split("_");    
    dateFrameId = dateFrameList[0];
    // alert(dateFrameId);
    calendar_go(dateFrameId,"back");
})

$(".dateNavigateFor").live("click",function(){
    clickId = $(this).attr("id");
    dateFrameList = clickId.split("_");    
    dateFrameId = dateFrameList[0];
    calendar_go(dateFrameId,"for");
})

//$('#calendarSlider_24_back').live("click",function(){
//    alert('click');
//})
