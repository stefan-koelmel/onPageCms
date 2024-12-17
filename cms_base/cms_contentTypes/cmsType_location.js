
$(".adminDates_Loctaion").focus_lost(function() {
    //val = $(this).attr("value");
    //alert("Lost ");
})

$(".adminDates_Loctaion").focus(function() {
    //val = $(this).attr("value");
    //alert("Focus ");
})

$(".adminDates_Loctaion").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".adminDates_Loctaion").change(function(){
    $(this).css("cursor","default");
    //val = $(this).attr("value");
    alert("Change to ");
})




/*function show_rollover(id,type) {
    
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
}

function hide_rollover(id,type) {
   
    if (type == "showCategory") {
        $("#"+id+"_roll").addClass("companyListRollHidden");
        // $(".companyListRoll").addClass("companyListRollHidden");
    }
}


$(".companyListImageBox").mouseenter(function(){
    clickAction = $(".companyList").attr("clickAction");


    
    cursor = 0;
    if (clickAction == "goUrl") cursor = 1;
    if (clickAction == "showProduct") cursor = 1;
    if (clickAction == "showCategory") cursor = 1;
    
    mouseAction = $(".companyList").attr("mouseAction");
    if (mouseAction == "showCategory") {
        cursor = 1;
        id = $(this).attr("id");
        show_rollover(id,mouseAction);
        
    }


    if (cursor == 1) {
        $(this).css("cursor","pointer");
    }
})


$(".companyListImageBox").mouseleave(function(){
     clickAction = $(".companyList").attr("clickAction");

    cursor = 0;
    if (clickAction == "goUrl") cursor = 1;
    if (clickAction == "showProduct") cursor = 1;
    if (clickAction == "showCategory") cursor = 1;

    mouseAction = $(".companyList").attr("mouseAction");
    if (mouseAction == "showCategory") {
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


    if (clickAction == "goUrl") {
        companyUrl = $(this).attr("companyUrl");
        window.open(companyUrl, 'companys');
    }

    if (clickAction == "showProduct") {
//        $(".companyList").animate({
//            opacity: 0.25
//
//            }, 5000, function() {
//                // Animation complete.
//        });
        imageListHeight = $(".companyList").css("height")
        companyId = $(this).attr("companyId");
        cmsName = $(".companyList").attr("cmsName");
        frameWidth = $(".companyList").css("width");
        

        callUrl = "/cms/cms_productSelect_get.php?companyId="+companyId+"&cmsName="+cmsName+"&frameWidth="+frameWidth;
        $.get(callUrl,function(text){
            text = text + "<a class='javascriptButton closeProductList' >Produkte</a><br>";
            $(".productShowFrame").html(text);
        });


        $(".companyList").addClass("companyListHidden");

        $(".productShowFrame").removeClass("productShowFrameHidden");

       
        $(".productShowFrame").css("height",imageListHeight)

        // alert ("Zeige Produkte von Hersteller mit ID = "+companyId);
    }
    if (clickAction == "showCategory") {
        companyId = $(this).attr("companyId");
        alert ("Zeige Kategorien von Hersteller mit ID = "+companyId);

    }
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
}) */
