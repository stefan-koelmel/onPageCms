


//$(".dateNavigateBack").click(function(){
//    clickId = $(this).attr("id");
//    dateFrameList = clickId.split("_");    
//    dateFrameId = dateFrameList[0];
//    // calendar_go(dateFrameId,"back");
//     alert("click");
//})
//
//$(".dateNavigateFor").click(function(){
//    clickId = $(this).attr("id");
//    dateFrameList = clickId.split("_");    
//    dateFrameId = dateFrameList[0];
//    // calendar_go(dateFrameId,"for");
//    alert("click");
//})


function cmsData() {
    paramStr = $(".LayoutFrame").children(".hiddenData").text();
    param = getParamList(paramStr);
    return param;
};

function getParamList(paramStr) {
    paramList = new Array();
    if (paramStr) {
        if (paramStr.length >= 2) {

            paramMainList = paramStr.split("|");
           //  out = "";
            for (var i=0;i<paramMainList.length;i++) {
                params = paramMainList[i].split(":");
                key = params[0];
                value = params[1];
                // out += "#"+key+"="+value+"\n";
                paramList[key] = value;
            }
        }
    }
    return paramList;
};

function getParamStr(paramList) {
    paramStr = "";
    for (var key in paramList) {
        value = paramList[key];
        if (paramStr != "") paramStr += "|";
        paramStr += key+":"+value;
    }
    
    // alert (paramStr);
    return paramStr;
}

function getParamClass(paramList) {
   paramStr = getParamStr(paramList);
   if (paramStr != "") paramStr = "<div class='hiddenData'>"+paramStr+"</div>";
   return paramStr;
};



//$(".dateMonthDay").live("mouseenter",function(){
//    $(this).css("cursor","pointer");    
//})
//
//
//$(".dateMonthDay").live("mouseleave",function(){
//    $(this).css("cursor","default");
//})




$(".dateMonthDay").click(function(){
    clickId = $(this).attr("id");
    // alert (clickId);
    dateFrameList = clickId.split("_");    
    dateFrameId = dateFrameList[0];
    
    datum = dateFrameList[1];
    
    
    // dateFrameId = $(this).attr("id");
    // paramStr =  $(this).children(".hiddenData").text();
    //param = getParamList(paramStr);
    //datum = param["date"]; //$(this).attr("date");
    
    setDatePara = "date";
    
   
    paramStr = $(".dateMonthList_"+dateFrameId).children(".hiddenData").text();
    param = getParamList(paramStr);
    clickAction = param["clickAction"]; // $(".dateMonthList_"+fId).attr("clickAction");
    clickTarget = param["clickTarget"]; // $(".dateMonthList_"+fId).attr("clickTarget");
    clickParameter = param["clickParameter"];
    if (clickParameter) {
        setDatePara = clickParameter;
    }
    
    // alert ("Action = "+clickAction+"\nTarget = "+clickTarget+"\nDatum = "+datum);
    if (clickAction) {
       
        if (clickTarget == "page") {
           clickUrl = param["clickUrl"]; //  $(".dateMonthList_"+fId).attr("clickUrl");
           if (clickAction == "showDate") {
               hasAsk = clickUrl.indexOf("?");
               if (hasAsk>0) clickUrl += "&";
               else clickUrl += "?";
               
               clickUrl = clickUrl + setDatePara + "=" +datum;
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
    
    

});






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
    //alert ("URL"+url+"\n month"+actMonth);
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
};

function goCalenderBack(dateFrameId) {
   //  dateFrameId = "sidebar";
   //  alert("goback "+dateFrameId);
    calendar_go(dateFrameId,"back");    
};

//$(".dateNavigateBack").live("click",function(){
//    clickId = $(this).attr("id");
//    dateFrameList = clickId.split("_");    
//    dateFrameId = dateFrameList[0];
//    // calendar_go(dateFrameId,"back");
//    alert("click Back");
//});
//
//$(".dateNavigateFor").live("click",function(){
//    clickId = $(this).attr("id");
//    dateFrameList = clickId.split("_");    
//    dateFrameId = dateFrameList[0];
//    calendar_go(dateFrameId,"for");
//    //alert("click For");
//});
//
//
//
//$(".dateNavigateBack").live("click",function(){
//    clickId = $(this).attr("id");
//    dateFrameList = clickId.split("_");    
//    dateFrameId = dateFrameList[0];
//    calendar_go(dateFrameId,"back");
//    //alert("click Back");
//});




