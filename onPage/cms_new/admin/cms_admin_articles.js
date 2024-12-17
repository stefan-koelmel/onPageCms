



function articles_show_Category(categoryId) {
   /* url = $(".adminDatesFrame").attr("urlCategory");
    if (categoryId) {
        url += "&categoryId="+categoryId;
        // alert(url);
    } else {
        categoryName = $(".adminDates_Category").attr("value");
        // alert ("Get from content "+categoryName);
        url += "&category="+categoryName;
    }
    url += "&out=categoryData"
    $(".adminDates_rightFrame").html(url);
    $.get(url,function(text) {
        // alert(text);
        mainList = text.split("|");
        if (mainList.length >= 2) { // Found
            out = "";
            categoryId = "";
            categoryName = "";
            for (var i=0;i<mainList.length;i++) { //
               items = mainList[i].split("#");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   // alert ("key = '"+key + "' value ='"+value+"'")
                   switch (key) {
                        case "id"     : categoryId = value; break;
                        case "name"   : categoryName = value; break;

                        default :
                            out += "#"+key+"'" + " = '" + value + "'<br>";
                    }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }
            }

            $(".adminDates_CategoryId").attr("value",categoryId);

            $(".adminDates_Category").attr("value",categoryName);
            // $(".adminDates_Region").attr("disabled",regionName);
            show_dateOutput(categoryId,"-","-");

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

    })*/
    
}

function articles_show_Region(regionId) {
  /*  url = $(".adminDatesFrame").attr("urlRegion");
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
    $.get(url,function(text) {
        mainList = text.split("|");
        if (mainList.length >= 2) { // Found
            out = "";
            regionId = "";
            regionName = "";
            for (var i=0;i<mainList.length;i++) { //
               items = mainList[i].split("#");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   // alert ("key = '"+key + "' value ='"+value+"'")
                   switch (key) {
                        case "id"     : regionId = value; break;
                        case "name"   : regionName = value; break;

                        default :
                            out += "#"+key+"'" + " = '" + value + "'<br>";
                    }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }
            }

            $("#locationRegionId").attr("value",regionId);
            $("#locationRegionId").attr("readonly","true");
            $(".adminDates_Region").attr("value",regionName);
            // $(".adminDates_Region").attr("disabled",regionName);

            //show Output
            //$(".adminDates_rightFrame").html(out);
             if (send) show_dateOutput("-","-",regionId);
        } else { // not Found Location
            $("#locationRegionId").attr("value","");
            // $("#locationRegionId").removeAttr("readonly");
            $(".adminDates_Region").removeAttr("disabled");


            //show Output
            //$(".adminDates_rightFrame").html(text);
            if (send) show_dateOutput("-","-",0);
        }

    }) */
    
}



function articles_show_Location() {

    ort = $(".adminArticles_Location").val();
   
    // alert ("Location change to "+ort);
    paramStr = $(".adminArticlesFrame").children(".hiddenData").text();
    param = getParamList(paramStr);
    url = param["urlLocation"];

    // url = $(".adminArticlesFrame").attr("urlLocation");
    url = url+"&loc="+ort;
    url = url+"&out=locData";
    // alert(url);
    
    $.get(url,function(text){
       
        mainList = text.split("|");
       
        if (mainList.length >= 2) { // Found
            var locationData = new Array();
            out = "";
            get_locationAdress = "";
            get_locationCity = "";
            get_locationUrl = "";
            get_locationRegion = "";
            get_locationId = "";
            get_locationName = "";
            get_locationticketUrl = "";
            // out += "Anzahl = " + anz + " <br>";
            for (var i=0;i<mainList.length;i++) { //
            //for (i in mainlist) { //    //
               // out += mainList[i]+"<br>";
               items = mainList[i].split("=");
               if (items.length > 1)  {
                   key = items[0];
                   value = items[1];
                   if (value.length > 0) {
                       // alert ("key = '"+key + "' value ='"+value+"'")
                       switch (key) {
                            case "id"        : get_locationId = value;break;
                            case "url"       : get_locationUrl = value;break;
                            case "ticketUrl" : get_locationticketUrl = value;break;
                            case "street"    : get_locationAdress += value;break;
                            case "hnr"       : get_locationAdress += " " + value;break;
                            case "plz"       : get_locationCity += value;break;
                            case "city"      : get_locationCity += " " + value;break;
                            case "region"    : get_locationRegion = value;break;
                            case "name"      : get_locationName = value;break;

                            default :
                                out += "#"+key+"'" + " = '" + value + "'<br>";
                        }
                   }
               } else {
                   // alert ("anzahl = "+items.length + "von "+mainList[i]);
               }

              
               //"Hallo";
            }
            anz = mainList.length;
            // alert(out);
            

            $("#locationId").attr("value",get_locationId);
            $("#locationId").attr("readOnly","true");

            $(".adminArticles_Location").attr("value",get_locationName);

            if (get_locationRegion != "") {
                oldRegion = $("#adminArticles_Region").attr("value");
                if (oldRegion) {
                    $(".adminArticles_Region_"+oldRegion).removeClass("cmsToggleSelected");
                }
                $("#adminArticles_Region").attr("value",get_locationRegion);
                $(".adminArticles_Region_"+get_locationRegion).addClass("cmsToggleSelected");               
            }

            if (get_locationUrl != "") {
                // $(".adminArticles_url").attr("value",get_locationUrl);
                // $(".adminArticles_url").attr("disabled","true");
            }

            if (get_locationticketUrl != "") {
                // $(".adminArticles_ticketUrl").attr("value",get_locationticketUrl);
                // $(".adminArticles_ticketUrl").attr("disabled","true");
            }


          /*  $("#locationRegionId").attr("value",locationRegion);
            $("#locationRegionId").attr("readonly","true");
            $(".adminDates_Region").attr("disabled","true");
            show_Region(locationRegion);

            if (locationAdress == "") {
                $("#locationAdress").attr("value","");
                $("#locationAdress").removeAttr("disabled");
            } else {
                $("#locationAdress").attr("value",locationAdress);
                $("#locationAdress").attr("disabled","true");
            }
            $("#locationCity").attr("value",locationCity);
            $("#locationCity").attr("disabled","true");

            $("#locationUrl").attr("value",locationUrl);
            $("#locationUrl").attr("disabled","true");


            $(".adminDates_rightFrame").html(out);
            show_dateOutput("-",locationId,locationRegion); */
        } else { // not Found Location
            $("#locationId").attr("value","");

//            $(".adminArticles_url").val("");
//            $(".adminArticles_url").removeAttr("disabled");
//
//            $(".adminArticles_ticketUrl").val("");
//            $(".adminArticles_ticketUrl").removeAttr("disabled");
            // $("#locationId").removeAttr("readonly");

           /* $("#locationRegionId").attr("value","");
            // $("#locationRegionId").removeAttr("readonly");
            $(".adminDates_Region").removeAttr("disabled");
            $(".adminDates_Region").attr("value","");

            $("#locationAdress").attr("value","");
            $("#locationAdress").removeAttr("disabled");


            $("#locationCity").attr("value","");
            $("#locationCity").removeAttr("disabled");

            $("#locationUrl").attr("value","");
            $("#locationUrl").removeAttr("disabled");
            
            $(".adminDates_rightFrame").html(text);
            
            $(".adminDates_Region").focus();
            show_dateOutput("-",0,0); */
        }
    });    
}






$(".adminArticles_Day").live("change",function(){articles_show_dateOutput("-","-","-");})

$(".adminArticles_Month").live("change",function(){artiles_show_dateOutput("-","-","-");})

$(".adminArticles_Year").live("change",function(){articles_show_dateOutput("-","-","-");})

$(".adminArticles_Category").live("change",function() {
    alert("Hier");   
})


//$("#adminArticles_Category").change(function() {
//    alert("Hier");
//    show_Category();
//})


$(".adminArticles_DateRange").change(function(){
    alert("Hier");
    ausgabe = $(".adminArticles_DateRange").val();
    ausgabe = ausgabe.replace("/","-");
    $(".cmsImagePathSelector").attr("value","images/articles/"+ausgabe+"/");
    $(".cmsImageSelector").attr("folderName","images/articles/"+ausgabe+"/");
})

//$(".adminArticles_Location").live("focusout",function(){
//     articles_show_Location();
//})



$(".adminArticles_Location").live("focus",function(){
    startLocation = $(this).val();    
})

$(".adminArticles_Location").live("focusout",function(){
    newLocation = $(this).val();
    if (newLocation != startLocation) {
        //alert ("Change Location\n"+startLocation+"\n"+newLocation);
       articles_show_Location();
    }
})



$(".adminArticles_Region").live("change",function(){
    show_Region();
})

$(".adminArticles_filterLocation").live("change",function(){
     value = $(this).attr("value");
     $("#searchForm").submit();
     alert("Change to "+value);
})


$(".dateSelector").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".dateSelector").mouseleave(function(){
    $(this).css("cursor","default");
})

$(".dateSelector").click(function(){
    
    frameContent = $(".dateDropdownFrame").text();
    if (frameContent.length < 10) { // show


        paramStr = $(".dateLink_box").children(".hiddenData").text();
        param    = getParamList(paramStr);
        url      = param["dateUrl"]; // url        = $(".dateLink_box").attr("dateUrl");

        dateRange  = $(".adminArticles_DateRange").val();
        if (dateRange) url += "&dateRange="+dateRange;

        dateId     = param["dateId"];
        if (dateId) url += "&code="+dateId;

        locationId  = $(".adminDates_locationId").val();
        if (locationId) url += "&location="+locationId;

        $.get(url,function(text) {
            $(".dateDropdownFrame").html(text);
        })
    } else {
        $(".dateDropdownFrame").html("");
    }

})

$(document).ready(function(){
   // $("#firstFocus").focus();
   // show_dateOutput("-","-","-");
   // $('#dateInput :input:visible:enabled:first').focus();
   // alert ("ready");
})

