var startLocation ="";

function show_dateOutput(category,ort,region) {
    // alert ("cat="+category+"ort="+ort+"region="+region);

    paramStrLink = $(".articleLink_box").children(".hiddenData").text();
    paramLink = getParamList(paramStrLink);
   

    if (category !="-") { }
    if (region !="-") { }
    if (ort!="-") {
        locationId = eval(ort);
        if (locationId>=0) {
            paramLink["locationId"] = locationId;
            paramStrLink = getParamStr(paramLink);
            $(".articleLink_box").children(".hiddenData").text(paramStrLink);


            // $(".articleLink_box").attr("locationId",locationId);
        }
        
    }

    day = $(".adminDates_Day").attr("value");
    month = $(".adminDates_Month").attr("value");
    year = $(".adminDates_Year").attr("value");
    // alert (year+"-"+month+"-"+day);
    if (month!="") {
        month = eval(month);
        if (month > 0 && month < 13) {
            if (month>=10) monthStr = ""+month;
            else monthStr = "0"+month;
            if (year !="") {
                year = eval(year);
                if (year<100) year = year + 2000;
                
                folderName = "images/dates/"+year+"-"+monthStr+"/";

                paramStr = $(".cmsImageSelector").children(".hiddenData").text();
                param = getParamList(paramStr);
                param["folderName"] = folderName;
                paramStr = getParamStr(param);
                $(".cmsImageSelector").children(".hiddenData").text(paramStr);
                // $(".cmsImageSelector").attr("foldername",folderName);

                // set Folder to FolderSelector
                $(".cmsImagePathSelector").attr("value",folderName);
           //    alert ("MONTH "+month+ "YEAR = "+year);

                
                day = eval(day);
                if (day > 0 && day < 32) {
                    if (day>10) dayStr = ""+day;
                    else dayStr = "0"+day;

                    dateStr = year+"-"+monthStr+"-"+dayStr;
                    paramLink["date"] = dateStr;
                    paramStrLink = getParamStr(paramLink);
                    $(".articleLink_box").children(".hiddenData").text(paramStrLink);
                    // $(".articleLink_box").attr("date",dateStr);
                }
                
                
            }
        }

    }
//
//
//
//    info = "Datum = "+day+"."+month+"."+year+"<br>";
//
//    if (category == "-") {
//        category = $("#categoryId").attr("value");
//    }
//    info = info + "Kategorie = " + category+ "<br>";
//
//
//
//
//    if (ort == "-") {
//        ort = $("#locationId").attr("value");
//    }
//
//    info = info + "Ort = " + ort + "<br>";
//
//    if (region == "-") {
//        region = $("#locationRegionId").attr("value");
//    }
//    info = info + "Region = " + region + "<br>";
//
//    $(".adminDates_rightFrame").html(info)
//
//    url = $(".adminDatesFrame").attr("urlDates");
//    // info = info + "<br>" + "URL = "+url;
//    url += "&day="+day;
//    url += "&mon="+month;
//    url += "&yea="+year;
//    url += "&cat="+category;
//    url += "&loc="+ort;
//    url += "&_reg="+region
//    url += "&out=html";
//
//    $.get(url,function(text){
//        $(".adminDatesOutputFrame").html(text);
//    });
    


    
}

function show_Category(categoryId) {
    paramStrDates = $(".adminDatesFrame").children(".hiddenData").text();
    paramDates = getParamList(paramStrDates);
    url = paramDates["urlCategory"];
    // url = $(".adminDatesFrame").attr("urlCategory");
    // url = $("#queryCatGetUrl").text();
    //alert (url);
    //alert(categoryUrl);
    if (categoryId) {
        url += "&categoryId="+categoryId;
        // alert(url);
    } else {
        categoryName = $(".adminDates_Category").attr("value");
        // alert ("Get from content "+categoryName);
        url += "&category="+categoryName;
    }
    url += "&out=categoryData"
    // $(".adminDates_rightFrame").html(url);
    // alert("Category "+url);
    $.get(url,function(text) {
       
        mainList = text.split("|");
        if (mainList.length >= 2) { // Found
            out = "";
            get_categoryId = "";
            get_categoryName = "";
            for (var i=0;i<mainList.length;i++) { //
               items = mainList[i].split("#");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   // alert ("key = '"+key + "' value ='"+value+"'")
                   switch (key) {
                        case "id"     :get_categoryId = value;break;
                        case "name"   :get_categoryName = value;break;

                        default :
                            out += "#"+key+"'" + " = '" + value + "'<br>";
                    }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }
            }            
            $(".adminDates_categoryId").attr("value",get_categoryId);

            $(".adminDates_Category").attr("value",get_categoryName);
            // $(".adminDates_Region").attr("disabled",regionName);
            show_dateOutput(get_categoryId,"-","-");

            //show Output
            // $(".adminDates_rightFrame").html(out);
        } else { // not Found Location
            //$("#categorId").removeAttr("readonly");
            //$("#categorId").attr("value","dsklfj");
            $(".adminDates_CategoryId").attr("value","");
            // $(".adminDates_CategoryId").removeAttr("readonly");

            $(".adminDates_Category").attr("value","");
            //



           //show Output
           $(".adminDates_rightFrame").html("not Found with 'categoryName'");
           show_dateOutput(0,"-","-");
        }

    })
    
}

function show_Region(regionId) {
    paramStrDates = $       (".adminDatesFrame").children(".hiddenData").text();
    paramDates = getParamList(paramStrDates);
    url = paramDates["urlRegion"];
    //url = $("#queryRegionGetUrl").text();
    
   // url = $(".adminDatesFrame").attr("urlRegion");
   
    if (regionId) {
        url += "&_regId="+regionId;
        send = 0;
        // alert(url);
    } else {
        regionName = $(".adminDates_Region").attr("value");
        // alert ("Get from content "+regionName);
        url += "&_regName="+regionName;
        send = 1;
    }
    url += "&out=regionData"
    // alert ("url"+url);
    $.get(url,function(text) {
        mainList = text.split("|");
        if (mainList.length >= 2) { // Found
            out = "";
            get_regionId = "";
            get_regionName = "";
            for (var i=0;i<mainList.length;i++) { //
               items = mainList[i].split("#");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   // alert ("key = '"+key + "' value ='"+value+"'")
                   switch (key) {
                        case "id"     : get_regionId = value;break;
                        case "name"   : get_regionName = value;break;

                        default :
                            out += "#"+key+"'" + " = '" + value + "'<br>";
                    }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }
            }

            $("#regionId").attr("value",get_regionId);
            $("#regionId").attr("readonly","true");
            $(".adminDates_Region").attr("value",get_regionName);
            // $(".adminDates_Region").attr("disabled",regionName);

            //show Output
            //$(".adminDates_rightFrame").html(out);
             if (send) show_dateOutput("-","-",get_regionId);
        } else { // not Found Location
            $("#regionId").attr("value","");
            // $("#locationRegionId").removeAttr("readonly");
            $(".adminDates_Region").removeAttr("disabled");


            //show Output
            //$(".adminDates_rightFrame").html(text);
            if (send) show_dateOutput("-","-",0);
        }

    })
    
}


function show_Location() {
    
 //   url = $("#locationGetUrl").text();
//    alert ("New Url="+url);
//        
//    
    paramStrDates = $(".adminDatesFrame").children(".hiddenData").text();
    paramDates = getParamList(paramStrDates);
    url = paramDates["urlLocation"];
    // alert ("Locatation "+url);
    
    ort = $(".adminDates_Location").val();
    // url = $(".adminDatesFrame").attr("urlLocation");
    url = url+"&loc="+ort;
    url = url+"&out=locData";

    // alert ("url \n"+url);
    $.get(url,function(text){
        // alert(text);
        mainList = text.split("|");
        if (mainList.length >= 2) { // Found
            out = "";
            get_locationStreet = "";
            get_locationStreetNr = "";
            get_locationPlz = "";
            get_locationCity = "";
            get_locationUrl = "";
            get_locationTicketUrl = "";
            get_locationRegion = "";
            get_locationId = "";
            get_locationName = "";
            // out += "Anzahl = " + anz + " <br>";
            for (var i=0;i<mainList.length;i++) { //
            //for (i in mainlist) { //    //
               // out += mainList[i]+"<br>";
               items = mainList[i].split("=");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   if (value.length > 0) {
                      // value =value.replace("&#180;","?");

                       
                       switch (key) {
                            case "id"        :get_locationId = value;break;
                            case "url"       :get_locationUrl = value;break;
                            case "ticketUrl" :get_locationTicketUrl = value;break;
                            case "street"    :get_locationStreet = value;break;
                            case "streetNr"  :get_locationStreetNr = value;break;
                            case "plz"       :get_locationPlz = value;break;
                            case "city"      :get_locationCity = value;break;
                            case "region"    :get_locationRegion = value;break;
                            case "name"      :get_locationName = value;break;

                            default :
                                // alert ("not  "+key+" = "+value);
                                out += "#"+key+"'" + " = '" + value + "'<br>";
                        }
                   }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }
            }

            anz = mainList.length;
            // alert (anz);
    

            $("#locationId").attr("value",get_locationId);
            $("#locationId").attr("readonly","true");

            $(".adminDates_Location").attr("value",get_locationName);

            $("#regionId").attr("value",get_locationRegion);
            $("#regionId").attr("readonly","true");
            $(".adminDates_Region").attr("disabled","true");
            // alert ("showRegion"+get_locationId);
            show_Region(get_locationRegion);


            $("#locationStreet").attr("value",get_locationStreet);
            $("#locationStreet").attr("disabled","true");
            
            $("#locationStreetNr").attr("value",get_locationStreetNr);
            $("#locationStreetNr").attr("disabled","true");
            
//            if (get_locationAdress == "") {
//                $("#locationAdress").attr("value","");
//                $("#locationAdress").removeAttr("disabled");
//            } else {
//                $("#locationAdress").attr("value",get_locationAdress);
//                $("#locationAdress").attr("disabled","true");
//            }
            
            $("#locationPlz").attr("value",get_locationPlz);
            $("#locationPlz").attr("disabled","true");
            
            $("#locationCity").attr("value",get_locationCity);
            $("#locationCity").attr("disabled","true");

            if (get_locationUrl == "") {
                $("#locationUrl").attr("value","");
                $("#locationUrl").removeAttr("disabled");
            } else {
                $("#locationUrl").attr("value",get_locationUrl);
                $("#locationUrl").attr("disabled","true");
            }

//            if (get_locationTicketUrl == "") {
//                $("#locationTicketUrl").attr("value","");
            $("#locationTicketUrl").removeAttr("disabled");
//            } else {
//                $("#locationTicketUrl").attr("value",get_locationTicketUrl);
//                $("#locationTicketUrl").attr("disabled","true");
//            }

            // alert ("showOutput");

            show_dateOutput("-",get_locationId,get_locationRegion);

            paramStrLink = $(".articleLink_box").children(".hiddenData").text();
            paramLink = getParamList(paramStrLink);
            paramLink["locationId"] = locationId;
            paramStrLink = getParamStr(paramLink);
            $(".articleLink_box").children(".hiddenData").text(paramStrLink);



            // $(".articleLink_box").attr("locationId",locationId);



            // alert ("set LocationId<br>");
        } else { // not Found Location
            clearData = 1;
            $("#locationId").attr("value","");
            // $("#locationId").removeAttr("readonly");

            $("#regionId").attr("value","");
            // $("#locationRegionId").removeAttr("readonly");
            $(".adminDates_Region").removeAttr("disabled");
            $(".adminDates_Region").attr("value","");

            if (clearData) $("#locationStreet").attr("value","");
            $("#locationStreet").removeAttr("disabled");

            if (clearData) $("#locationStreetNr").attr("value","");
            $("#locationStreetNr").removeAttr("disabled");


            if (clearData) $("#locationPlz").attr("value","");
            $("#locationPlz").removeAttr("disabled");

            if (clearData) $("#locationCity").attr("value","");
            $("#locationCity").removeAttr("disabled");

            if (clearData) $("#locationUrl").attr("value","");
            $("#locationUrl").removeAttr("disabled");
            
            
            if (clearData) $("#locationTicketUrl").attr("value","");
            $("#locationTicketUrl").removeAttr("disabled");
            
            $(".adminDates_rightFrame").html(text);
            
            
            $(".adminDates_Region").focus();
            show_dateOutput("-",0,0);
        }
    });    
}

$(".articleSelector").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".articleSelector").mouseleave(function(){
    $(this).css("cursor","default");
})

$(".articleSelector").click(function(){
    paramStr =  $(".articleLink_box").children(".hiddenData").text();
    param = getParamList(paramStr);
    
    locationId = param["locationId"]; //$(".articleLink_box").attr("locationId");
    date       = param["date"]; //$(".articleLink_box").attr("date");
    url        = param["articlesUrl"]; //$(".articleLink_box").attr("articlesUrl");
    articleId  = param["articleId"]; //$(".articleLink_box").attr("articleId");

    locationId = $(".adminDates_locationId").attr("value");

    if (articleId) url += "&code="+articleId;

    if (date) url += "&date="+date;

    if (locationId) url += "&location="+locationId;
   
    $.get(url,function(text) {
        $(".articleDropdownFrame").html(text);
    })

})




$(".adminDates_Day").live("change",function(){show_dateOutput("-","-","-");})

$(".adminDates_Month").live("change",function(){show_dateOutput("-","-","-");})

$(".adminDates_Year").live("change",function(){show_dateOutput("-","-","-");})

$(".adminDates_Category").live("change",function() {
    // show_Category();    
})

$(".adminDates_Category").live("focusout",function() {
    show_Category();    
})

//
//$(".adminDates_Location").live("change",function(){
//     //alert ("Change Location");
//     // show_Location();
//})

$(".adminDates_Location").live("focus",function(){
    startLocation = $(this).val();    
    // alert ("Statrt "+startLocation);
})

$(".adminDates_Location").live("focusout",function(){
   //  newLocation = $(this).val();
   setTimeout("locationLeft();",1000);
//    alert("we are done");
//    if (newLocation != startLocation) {
//        // alert ("Change Location\n war="+startLocation+"\n jetz="+newLocation);
//        show_Location();
//    }    
});

function locationLeft() {
    newLocation = $(".adminDates_Location").val();
   
    if (newLocation != startLocation) {
        // alert ("Change Location\n war="+startLocation+"\n jetz="+newLocation);
        show_Location();
    }    
}

$(".adminDates_Location").live("change",function(){
    // setTimeout("locationLeft();",100);
//    newLocation = $(this).val();
//    // alert("Change "+newLocation); 
//    if (newLocation != startLocation) {
//        //alert ("Change Location\n"+startLocation+"\n"+newLocation);
//        // show_Location();
//    }    
})


$(".adminDates_Region").live("change",function(){
    // show_Region();
})

$(".adminDates_Region").live("focusout",function(){
    show_Region();
})






//$(".adminDates_filterLocation").live("change",function(){
//     value = $(this).attr("value");
//     $("#searchForm").submit();
//     alert("Change to "+value);
//})

$(document).ready(function(){
    $("#firstFocus").focus();
    show_dateOutput("-","-","-");
   // $('#dateInput :input:visible:enabled:first').focus();
})

