var selectImageId = 0;
var selectSrc = "...";

$(function() {
    $(".dragImage").draggable({
        revert: "invalid",
        cursor: "move",
        connectToSortable: ".cmsImageSortList",
        distance: 30,
        containment: "cmsContentFramePlus",
        zIndex: 10000,
        helper: "clone",
        cursorAt: { top: 10, left: 10}
        
    });
    
    
    $(".cmsImageDropFrame").droppable({
        activeClass:"cmsImageDropFrame_active",
        hoverClass:"cmsImageDropFrame_hover",
        
        drop: function( event, ui ) {
            var imageStr = ui.draggable.html();
            //  imageId  = ui.draggable.attr("id");
            
            var idStr = ui.draggable.attr("id");
            if (idStr) {
                var param = getParamList(idStr);
                var imageId = param["id"];                                       
            }

            if ($(this).hasClass("cmsDropSingle")) {
                $(this).children(".cmsImageFrame").html(imageStr);
                $(this).children(".cmsImageId").val(imageId);
            }

            
            // alert(imageId);
        }

    });
    
    
    $('.cmsImageSortList').sortable({
//        connectWith: '.cmsImageSortList',
        items: ".cmsImageSortItem",
        revert: true,
        // handle: '.cmsImageSortItem',
        cursor: 'move',
        // placeholder: 'dragPlaceholder',
        forcePlaceholderSize: true,
        opacity: 0.4,
        stop: function(event, ui){
            var sortorder='';
            var delimter = "|";
            var newIdStr = "";
            newIdStr += delimter;
            $('.cmsImageSortList').each(function(){
                var insertTo = ""
                if ($(this).hasClass("cmsImage_listList")) insertTo = "ListView";
                if ($(this).hasClass("cmsImage_listBlock")) insertTo = "ListBlock";
               
                // sortorder += "order = " + $(this).attr("class") + " \n";
               
                $('.cmsImageSortList').children("div").each(function(){
                    if ($(this).hasClass("dragImage")) {
                        
                        
                        
                        // var imageId = $(this).attr("id");
                        $(this).removeClass("dragImage");
                        $(this).addClass("cmsImageSortItem");
                        
                        if (insertTo == "ListView") {
                            $(this).addClass("imageListLine");
                        } 
                        if (insertTo == "ListBlock") {
                            $(this).addClass("cmsImageListItem");                                     
                        }
                    }
                    var idStr = $(this).attr("id");
                    if (idStr) {
                        var param = getParamList(idStr);
                        var imageId = param["id"];                       
                        newIdStr += imageId + delimter;
                    }
                    
                    sortorder += "children = " + $(this).attr("id") + " \n";
                    $("#cmsImage_list_imageStr").val(newIdStr);
                })
                //var itemorder=$(this).sortable('toArray');
                // var dragFrameId=$(this).attr('id');
                        
                            // sortorder+=dragFrameId+'='+itemorder.toString()+'<br>';
                // sortorder+="order :" + itemorder;
                
            })
            sortorder += "idStr = " + newIdStr;
            // alert (sortorder);
        }
    });
     $(".cmsImageSortList").disableSelection();

});


//
//$(function() {
//        $( "#draggable" ).draggable({ revert: "valid" });
//        $( "#draggable2" ).draggable({ revert: "invalid" });
//
//        $( "#droppable" ).droppable({
//            activeClass: "ui-state-hover",
//            hoverClass: "ui-state-active",
//            drop: function( event, ui ) {
//                $( this )
//                    .addClass( "ui-state-highlight" )
//                    .find( "p" )
//                        .html( "Dropped!" );
//            }
//        });
//    });




$(".dragImageFrame").sortable({
    handle: '.dragImage',
    cursor: 'move',

    placeholder: 'dragPlaceholder',
    forcePlaceholderSize: false,
    opacity: 0.4
})
.disableSelection();



$(".cmsImageSelect").mouseenter(function(){
    //$(this).cursor("pointer");
    $(this).css("cursor","pointer");
})
$(".cmsImageSelect").mouseleave(function(){
    //$(this).cursor("normal");
    $(this).css("cursor","default");
})


$(".cmsEditHorButton").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".cmsEditHorButton").mouseleave(function(){
    $(this).css("cursor","default");
})

$(".cmsEditHorButton").click(function(){
    $(".cmsEditHorButton").removeClass("selectedHAlign");
    $(this).addClass("selectedHAlign");
    valueName = $(this).attr("title");
    value = $(this).attr("id");
    $("."+valueName).val(value);
})


$(".cmsEditVerButton").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".cmsEditVerButton").mouseleave(function(){
    $(this).css("cursor","default");
})

$(".cmsEditVerButton").click(function(){
    $(".cmsEditVerButton").removeClass("selectedVAlign");
    $(this).addClass("selectedVAlign");
    valueName = $(this).attr("title");
    value = $(this).attr("id");
    $("."+valueName).val(value);
})


$(".cmsImageSelectModul").click(function(){
    
    var idStr = $(this).attr("id");
    if (idStr) {
        var param = getParamList(idStr);
        
        var id = param["id"];
        var folder = param["path"];
    }
    cmsModul_showImage(folder);
})




$(".cmsImageSelect").click(function(){
    $(".cmsImageSelector").css("height","260px");
    $(".cmsImageSelector").css("visible","1");

    $(".cmsImageSelector").css("overflow","auto");
    
    paramStr = $(".cmsImageSelector").children(".hiddenData").text();

    param = getParamList(paramStr);
    folderName = param["folderName"]; //$(".cmsImageSelector").attr("folderName");
    cms_Data = cmsData();
    cmsName = cms_Data["cmsName"];
    cmsVersion = cms_Data["cmsVersion"];
    // cmsName    = $(".LayoutFrame").attr("cmsName");
    // cmsVersion = $(".LayoutFrame").attr("cmsVersion");
   
    callUrl = "/cms_"+cmsVersion+"/cms_imageSelect_get.php?folder="+folderName+"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    // alert("folderName="+callUrl);
    
    $(".cmsImageScroll").html("");
    $.get(callUrl,function(text){
         $(".cmsImageScroll").html(text);
    });

})


$(".cmsImageSelectFrame").click(function(){
    paramStr = $(this).children(".hiddenData").text();
    param = getParamList(paramStr);


    selectImageId = param["imageId"]; //$(this).attr("imageId");
    selectSrc     = param["imageSrc"]; //  $(this).attr("imageSrc");
    $(".cmsImageSelectFrame").css("background-color","#ccc");
    $(this).css("background-color","#eee");

    //$(".cmsImageSelector").css("height","0px");
    //$(".cmsImageSelector").css("visible","none");

    //$(".cmsImageSelector").css("overflow","hidden");
    //$(".cmsImageId").val(imageId);
})

$(".cmsImageSelectCancel").click(function(){
    $(".cmsImageSelector").css("height","0px");
    $(".cmsImageSelector").css("visible","none");

    $(".cmsImageSelector").css("overflow","hidden");
})

$(".cmsImageSelectSelect").click(function(){
    if (selectImageId > 0) {
        $(".cmsImageSelector").css("height","0px");
        $(".cmsImageSelector").css("visible","none");

        $(".cmsImageSelector").css("overflow","hidden");
        $(".cmsImageId").val(selectImageId);


        actionChange = $(".cmsImageId").attr("action");
        if (actionChange == "submit") {
            actionFormName = $(".cmsImageId").attr("formName");
            if (actionFormName.length < 1) {
                actionFormName = "cmsEditContentForm";
            }
            // alert ("action = "+actionChange + "in Form "+actionFormName);
            $("."+actionFormName).submit();
        }

        if (actionChange == "focus") {
            $(".cmsImageId").focus();
        }
        $(".cmsImageId").focus();


        $(".cmsImageSelect").attr("src",selectSrc);
    }
})

$(".cmsFolderSelectFrame").mouseenter(function(){
    //$(this).cursor("pointer");
    $(this).css("cursor","pointer");
})

$(".cmsFolderSelectFrame").mouseleave(function(){
    //$(this).cursor("normal");
    $(this).css("cursor","default");
})

$(".cmsFolderSelectFrame").click(function() {
    
    id = $(this).attr("id");
    if (id) {
        beforeFrame = $(this).parent().attr("class");
//        afterFrame = $(this).children().attr("class");
//        alert("Before = "+beforeFrame);
        
        folderName = id;
    } else {
        paramStr = $(this).children(".hiddenData").text();
        param = getParamList(paramStr);
        folderName = param["folderName"]; //$(".cmsImageSelector").attr("folderName");


        // folderName = $(this).attr("folderName");
        cms_Data = cmsData();
        cmsName = cms_Data["cmsName"];
        cmsVersion = cms_Data["cmsVersion"];
        
        beforeFrame = "cmsImageScroll";
    }
    
    
    loading = "<img src='/cms_base/cmsImages/loading_big.gif' >";
    
    $("."+beforeFrame).html("");
    $("."+beforeFrame).addClass("cmsLoading");
    
   
        // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;

    callUrl = "/cms_"+cmsVersion+"/cms_imageSelect_get.php?folder="+folderName+"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;

    $.get(callUrl,function(text){    
         $("."+beforeFrame).removeClass("cmsLoading");
         $("."+beforeFrame).html(text);
         

    });  
    
})


var dontClose = 0;

$(".imgListImageBox").mouseenter(function(){
    //$(this).cursor("pointer");
    $(this).css("cursor","pointer");
})

$(".imgListImageBox").mouseleave(function(){
    //$(this).cursor("normal");
    $(this).css("cursor","default");
})

$(".imgListImageBox").click(function(){
    paramStr = $(this).parent().parent().children('.hiddenData').text();
    param = getParamList(paramStr);
    clickAction = param["clickAction"]; //$(".companyList").attr("clickAction");
    imgList     = param["imageList"];

    paramStr = $(this).children('.hiddenData').text();
    param = getParamList(paramStr);

    imageId = param["imageId"]; //  $(this).attr("imageId");

    // alert("clickAction = "+clickAction+" \n imgList = "+imgList+ " \n imageId = "+imageId);

    if (clickAction == "fullPreview") {
        previewImage_page(imgList,imageId);
    }

    if (clickAction == "framePreview") {
        previewImage_frame(imgList,imageId);
    }

})

function previewImage_page(imgList,imageId) {
    cms_Data = cmsData();
    cmsName = cms_Data["cmsName"];
    cmsVersion = cms_Data["cmsVersion"];

    callUrl = "/cms_"+cmsVersion+"/cms_imageShow.php?imageId="+imageId+"&mode=preview&imageList="+imgList+"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    // alert(callUrl);
    //  $(".imagePreviewContent").html(callUrl);
    addtext = "\n<div class='imagePreviewClose'>X</div>";
    $.get(callUrl,function(text){
        $(".imagePreviewContent").html(addtext+text);
        $(".imagePreviewWindow").addClass("imagePreviewWindowOpen");
        $(".imagePreviewContent").addClass("imagePreviewContentOpen");
        dontClose = 0;
    });

    // alert("PreviewImage\n imgList = "+imgList+"\n imageId = "+imageId);
}


function previewImage_frame(imgList,imageId) {
    cms_Data = cmsData();
    cmsName = cms_Data["cmsName"];
    cmsVersion = cms_Data["cmsVersion"];

    width = $(".imageList").css("width");
    height = $(".imageList").css("height");
    callUrl = "/cms_"+cmsVersion+"/cms_imageShow.php?imageId="+imageId+"&mode=preview&imageList="+imgList+"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl = callUrl + "&width="+width+"&height="+height;

    //  $(".imagePreviewContent").html(callUrl);
    addtext = "\n<div class='imagePreviewClose'>X</div>";
    
    $.get(callUrl,function(text){
        $(".imagePreviewFrame").html(addtext+text);
        $(".imagePreviewFrame").css("addtext+text);")
        $(".imagePreviewFrame").addClass("imagePreviewFrameShow");
        $(".imagePreviewFrame").css("width", $(".imageList").css("width"));
        $(".imagePreviewFrame").css("height", $(".imageList").css("height"));
       // $(".imagePreviewFrame").css("background-color", $(".imageList").css("background-color"));
        $(".imagePreviewFrame").css("background-color", "#f00");
        $(".imageList").css("display","none");
        $(".imageList").css("overflow","hidden");


        width = parseInt(width) + 20;
        newWidth = ""+width+"px";
        //newWidth = "650px";
        
        $(".imagePreviewClose").css("margin-top","-25px");
        $(".imagePreviewClose").css("margin-left",newWidth);
        
     

    });
    // alert("PreviewImage\n imgList = "+imgList+"\n imageId = "+imageId);
}

$(".imagePreviewContent").live("mouseenter",function(){
    dontClose = 1;
})

$(".imagePreviewContent").live("mouseleave",function(){
    dontClose = 0;
})

$(".imagePreviewClose").live("mouseenter",function(){
    $(this).css("cursor","pointer");
    dontClose = 1;
})

$(".imagePreviewClose").live("mouseleave",function(){
    $(this).css("cursor","default");
    dontClose = 0;
})

$(".imagePreviewClose").live("click",function(){
    paramStr = $(".imageList").children(".hiddenData").text();
    param = getParamList(paramStr);
    clickAction = param["clickAction"];    
   
    if (clickAction == "fullPreview") {
        $(".imagePreviewWindow").removeClass("imagePreviewWindowOpen");
        $(".imagePreviewContent").removeClass("imagePreviewContentOpen");
    }

    if (clickAction == "framePreview") {
        $(".imageList").css("display","block");
        $(".imageList").css("overflow","visible");
        $(".imagePreviewFrame").removeClass("imagePreviewFrameShow");        
    }

   
})

$(".imagePreviewContent").live("click", function(){
    cmsParam = cmsData();
    cmsVersion = cmsParam["cmsVersion"];
    cmsName = cmsParam["cmsName"];

    paramStr = $(".imageList").children(".hiddenData").text();
    param = getParamList(paramStr);
    imgList = param["imageList"];

    


    // imgList = $(".imageList").attr("imageList");
    nextImage = $(".imagePreviewImage").attr("nextImage");
    // alert("PreviewImage\n imgList = "+imgList+"\n imageId = "+nextImage);
    addtext = "<div class='imagePreviewClose'>X</div>\n";

    callUrl = "/cms_"+cmsVersion+"/cms_imageShow.php?imageId="+nextImage+"&mode=preview&imageList="+imgList+"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    $.get(callUrl,function(text){
        $(".imagePreviewContent").html(addtext+text);
    });

    dontClose = 1;
})


$(".imagePreviewFrame").live("mouseenter",function(){
    $(this).css("cursor","pointer");

})

$(".imagePreviewFrame").live("mouseleave",function(){
    $(this).css("cursor","default");
  
})

$(".imagePreviewFrame").live("click", function(){
    imgList = $(".imageList").attr("imageList");
    nextImage = $(".imagePreviewImage").attr("nextImage");
    
    cmsParam = cmsData();
    cmsVersion = cmsParam["cmsVersion"];
    cmsName = cmsParam["cmsName"];
   
    // alert("PreviewImage\n imgList = "+imgList+"\n imageId = "+nextImage);
    addtext = "<div class='imagePreviewClose'>X</div>\n";

    callUrl = "/cms/cms_imageShow.php?imageId="+nextImage+"&mode=preview&imageList="+imgList+"&cmsName="+cmsName;
    $.get(callUrl,function(text){
        $(".imagePreviewContent").html(addtext+text);
    });

    dontClose = 1;
})


$(".imagePreviewWindow").live("click",function(){
    if (dontClose == 0) {
        $(".imagePreviewWindow").removeClass("imagePreviewWindowOpen");
        $(".imagePreviewContent").removeClass("imagePreviewContentOpen");
    } else {
        //alert ("CCLOSSEEE !!");
    }
})




$(".cmsFolderSelectFrame").click(function() {
  
})



$(document).ready(function(){


    //alert(" startFlipTime();");
})