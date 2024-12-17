function show_rollover(id,type) {    
    width = $("#"+id).css("width");
    height = $("#"+id).css("height");
    left = $("#"+id).css("left");
    right = $("#"+id).css("right");
    if (type == "showCategory") {
        $("#"+id+"_roll").removeClass("companyListRollHidden");
        $("#"+id+"_roll").css("width",width);
        $("#"+id+"_roll").css("height",height);

//        $(".companyListRoll").removeClass("companyListRollHidden");
//        $(".companyListRoll").css("width",width);
//        $(".companyListRoll").css("height",height);
       //  $(".companyListRoll").css("left",left);
    }

    if (type == "showProduct") {
        isHidden = $("#"+id+"_roll").hasClass("companyListRollHidden");
        if (isHidden == 1) {
            // alert ("Hidden");
            
//            $("#"+id+"_roll").css("width",width);
//            $("#"+id+"_roll").css("height",height);

//            cmsParam = cmsData();
//            cmsVersion = cmsParam["cmsVersion"];
//            cmsName = cmsParam["cmsName"];
//
//            paramStr = $("#"+id).children(".hiddenData").text();
//            param = getParamList(paramStr);
//            companyId = param["companyId"];
//            url = "/cms_"+cmsVersion+"/getData/productData.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
//            url += "&companyId="+companyId;
//            url += "&out=productImage";
//            url += "&width="+width;
//            url += "&height="+height;

            cont = $("#"+id+"_roll").html();
            if (cont) {
                // alert ("Inhalt = "+cont);
          
            // alert(url);
            // $.get(url,function(text){
                //dontShow = text.indexOf("notExist");
                //if (dontShow > 0) {
                 //    alert("nicht"+dontShow);
                //    return 0;
                //} else {
                    //  $("#"+id+"_roll").css("cursor","pointer");
                    //$("#"+id+"_roll").html(text);
                    $("#"+id+"_roll").css("text-align","left");
    //                $("#"+id+"_roll").css("height",height);
    //                $("#"+id+"_roll").css("visibility","visible");
    //                $("#"+id+"_roll").css("display","inline-block");
    //
                    $("#"+id+"_roll").removeClass("companyListRollHidden");
                    $("#"+id+"_roll").css("opacity","0.0");
                    $("#"+id+"_roll").animate({
                        opacity: 1.0
                        // height: height
                    }, 300, function() {

                     // Animation complete.
                    
                    });
                //}
           // });
                return 1;
            }
            
        }
        

        
    }
   

}

function hide_rollover(id,type) {
   
    if (type == "showCategory") {
        $("#"+id+"_roll").addClass("companyListRollHidden");
        // $(".companyListRoll").addClass("companyListRollHidden");
    }

    if (type == "showProduct") {
        cont = $("#"+id+"_roll").html();
        if (cont) {
            $("#"+id+"_roll").animate({
                opacity: 0.0
                // height: 0
            }, 200, function() {
                 // Animation complete.
                 $("#"+id+"_roll").addClass("companyListRollHidden");
            });
        }

    }
}


$(".companyListImageBox").mouseenter(function(){
    paramStr = $(this).parent().parent().children('.hiddenData').html();
    param = getParamList(paramStr);
    clickAction = param["clickAction"]; //$(".companyList").attr("clickAction");



    cursor = 0;
    if (clickAction == "goUrl") cursor = 1;
    if (clickAction == "showProduct") cursor = 1;
    if (clickAction == "showCategory") cursor = 1;
    if (clickAction == "showCompany") cursor = 1;

    mouseAction = param["mouseAction"];
    if (mouseAction == "showCategory") {
        cursor = 1;
        id = $(this).attr("id");
        show_rollover(id,mouseAction);
    }

    if (mouseAction == "showProduct") {
        cursor = 1;
        id = $(this).attr("id");
        // alert("showProduct from id = "+id);
        cursor = show_rollover(id,mouseAction);       
    }


    if (cursor == 1) {
        $(this).css("cursor","pointer");
    }
})


$(".companyListImageBox").mouseleave(function(){
    paramStr = $(this).parent().parent().children('.hiddenData').html();
    param = getParamList(paramStr);
    clickAction = param["clickAction"]; //$(".companyList").attr("clickAction");
    mouseAction = param["mouseAction"];
    cursor = 0;
    if (clickAction == "goUrl") cursor = 1;
    if (clickAction == "showProduct") cursor = 1;
    if (clickAction == "showCategory") cursor = 1;
    if (clickAction == "showCompany") cursor = 1;

    
    if (mouseAction == "showCategory") {
        cursor = 1;
        id = $(this).attr("id");
        hide_rollover(id,mouseAction);        
    }
    if (mouseAction == "showProduct") {
        cursor = 1;
        id = $(this).attr("id");
        hide_rollover(id,mouseAction);
    }




    if (cursor == 1) {
        $(this).css("cursor","default");
    }
})


$(".companyListImageBox").click(function(){
    clickAction = $(".companyList").attr("clickAction");
    cmsName = $(".LayoutFrame").attr("cmsName");
    cmsVersion = $(".LayoutFrame").attr("cmsVersion");

    if (clickAction == "goUrl") {
        companyUrl = $(this).attr("companyUrl");
        window.open(companyUrl, 'companys');
    }


    if (clickAction == "contentName") {
        imageListHeight = $(".companyList").css("height");
        companyId = $(this).attr("companyId");
        contentNameId = $(this).attr("contentNameId");
        frameWidth = $(".companyList").css("width");
        callUrl = "/cms_"+cmsVersion+"/cms_contentGet.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        callUrl += "&company="+companyId;
        callUrl += "&frameWidth="+frameWidth; 
        callUrl += "&contentNameId="+contentNameId;
        
       // callUrl = "/cms/cms_productSelect_get.php?companyId="+companyId+"&cmsName="+cmsName+"&frameWidth="+frameWidth;
        $.get(callUrl,function(text){
            // text = callUrl + "<br>" + text;
            $(".contentNameFrame").css("height",imageListHeight);

            text = text + "<a class='javascriptButton closeContentNameFrame' >zur�ck</a><br>";
            $(".contentNameFrame").html(text);


            $(".companyList").addClass("companyListHidden");

            $(".contentNameFrame").removeClass("contentNameFrameHidden");




        });
       
        

       //  alert ("Zeige Produkte von Hersteller mit ID = "+companyId);
    }



    if (clickAction == "showProduct") {
        /*$(".companyList").animate({
            opacity: 0.25
           
            }, 5000, function() {
                // Animation complete.
        });*/
        imageListHeight = $(".companyList").css("height");
        companyId = $(this).attr("companyId");
        cmsParam = cmsData();
        cmsVersion = cmsParam["cmsVersion"];
        cmsName = cmsParam["cmsName"];
       
        frameWidth = $(".companyList").css("width");
        

        callUrl = "/cms/cms_productSelect_get.php?companyId="+companyId+"&cmsName="+cmsName+"&frameWidth="+frameWidth;
        $.get(callUrl,function(text){
            text = text + "<a class='javascriptButton closeProductList' >Produkte</a><br>";
            $(".productShowFrame").html(text);
        });
        text  = "Zeige Produkte von Hersteller "+companyId+"<br>";
        text += "<a class='javascriptButton closeProductList' >Hersteller</a><br>";
        $(".productShowFrame").html(text);


        $(".companyList").addClass("companyListHidden");

        $(".productShowFrame").removeClass("productShowFrameHidden");

       
        $(".productShowFrame").css("height",imageListHeight)

        // alert ("Zeige Produkte von Hersteller mit ID = "+companyId);
    }
    if (clickAction == "showCategory") {
        companyId = $(this).attr("companyId");
        alert ("Zeige Kategorien von Hersteller mit ID = "+companyId);

    }

    if (clickAction == "showCompany") {
        companyId = $(this).attr("companyId");
        clickTarget = $(".companyList").attr("clickTarget");
        if (clickTarget == "frame") {
            imageListHeight = $(".companyList").css("height");
            $(".companyList").addClass("companyListHidden");
            $(".companyShowFrame").removeClass("productShowFrameHidden");
            $(".companyShowFrame").css("height",imageListHeight);
        }
        if (clickTarget == "page") {
            clickPage = $(".companyList").attr("clickPage");
            alert ("Zeige Produkte von Hersteller mit ID = "+companyId+"Target = "+clickTarget+" ClickPage = "+clickPage);
        }
        

    }
})

$(".companyShowFrame").click(function(){
    $(".companyList").removeClass("companyListHidden");
    $(".companyShowFrame").addClass("productShowFrameHidden");
    $(".companyShowFrame").css("height",0);

})

$(".productShowFrame").click(function(){
    $(".companyList").removeClass("companyListHidden");
    $(".productShowFrame").addClass("productShowFrameHidden");
    $(".productShowFrame").css("height",0);

})

$(".closeProductList").live("mouseenter",function(){
    $(this).css("cursor","pointer");
})

$(".closeProductList").live("mouseleave",function(){
    $(this).css("cursor","default");
})


$(".closeProductList").live("click",function(){
   $(".companyList").removeClass("companyListHidden");

   $(".productShowFrame").addClass("productShowFrameHidden");
})

$(".closeContentNameFrame").live("click",function(){
   $(".companyList").removeClass("companyListHidden");

   $(".contentNameFrame").addClass("contentNameFrameHidden");
   $(".contentNameFrame").html("");
})
