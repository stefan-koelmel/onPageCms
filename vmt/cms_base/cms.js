
$(".cmsFrameLink").mouseenter(function(){
    $(this).css("cursor","pointer");//("cmsContentHeadOver");
    //contentId = $(this).attr("contentId");
    //$(".cmsContentFrame_"+contentId).addClass("cmsContentFrameOver"); //("background-color","#e8e8e8");

})




$(".cmsFrameLink").mouseleave(function(){
    $(this).css("cursor","default");
    //("cmsContentHeadOver");
    //contentId = $(this).attr("contentId");
    //$(".cmsContentFrame_"+contentId).removeClass("cmsContentFrameOver");//css("background-color","#fff");
})


$(".cmsFrameLink").click(function(){
    url = $(this).children(".hiddenData").children(".hiddenLink").attr("href");
    //url = $(this).attr("link");
    // alert("url" + url);

    window.location = url; //"index.php";
})


$(".naviItemDiv").click(function(){
    url = $(this).children(".hiddenLink").attr("href");
    // alert("clickNaviItem "+url);
    window.location = url;
})

$(".historyButton").click(function(){
    hidden = $(".history_box").hasClass("history_box_hidden");
    
     // hide History
    //  $(".history_box").addClass("history_box_hidden");
    
    // hide Favoriten
    $(".bookmark_frame").addClass("bookmark_frame_hidden");
    
    // hide All BreadCrumb DropDown
    $(".breadCrumbDropdown_List").addClass("breadCrumbDropdown_List_hidden");
    
    if (hidden) {
         $(".history_box").removeClass("history_box_hidden");
//         $('#history_box').animate({
//             height:'toggle',
//             opacity: 1.00
//             //removeClass("history_box_hidden")
//         }, 5000, function() {
//             
//         });
//opacity: 0.25,
//left: '+=50',
//height: 'toggle'
//}, 5000, function() {
//        
//         $(".history_box").removeClass("history_box_hidden");
    } else {
//        $('#history_box').animate({
//             height:'toggle',
//             opacity: 0.5
//             //removeClass("history_box_hidden")
//         }, 5000, function() {
//             
//         });
        $(".history_box").addClass("history_box_hidden");
       
    }
    
    
})


$(".bookmarkDropdown").click(function(){


    // hide History
    $(".history_box").addClass("history_box_hidden");
    
    // hide Favoriten
    // $(".bookmark_frame").addClass("bookmark_frame_hidden");
    
    // hide All BreadCrumb DropDown
    $(".breadCrumbDropdown_List").addClass("breadCrumbDropdown_List_hidden");


    hidden = $(".bookmark_frame").hasClass("bookmark_frame_hidden");
    if (hidden) {
         $(".bookmark_frame").removeClass("bookmark_frame_hidden");
//         $('#history_box').animate({
//             height:'toggle',
//             opacity: 1.00
//             //removeClass("history_box_hidden")
//         }, 5000, function() {
//             
//         });
//opacity: 0.25,
//left: '+=50',
//height: 'toggle'
//}, 5000, function() {
//        
//         $(".history_box").removeClass("history_box_hidden");
    } else {
//        $('#history_box').animate({
//             height:'toggle',
//             opacity: 0.5
//             //removeClass("history_box_hidden")
//         }, 5000, function() {
//             
//         });
        $(".bookmark_frame").addClass("bookmark_frame_hidden");
       
    }
    
    
})


$(".historyLine").click(function(){
    url = $(this).children(".historyTitle").children(".historyLink").attr("href");
    window.location = url;
    //alert ("Click BookmarkList"+url);

})




$(".bookmarkButton").click(function(){
    active = $(".bookmarkButton").hasClass("bookmarkActiveButton");
    
    userId = $(".hiddenBookmark").attr("id");
    link = $(".hiddenBookmark").attr("href");
    state = $(".hiddenBookmark").attr("name");
    breadCrumb = $(".hiddenBookmark").attr("title");
    
    mode = "toggle";
    callUrl = "/cms_"+cmsVersion+"/getData/userData.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl += "&out=setBookmark&userId="+userId+"&mode="+mode+"&url="+link+"&breadCrumb="+breadCrumb;
    //alert (callUrl);
    $.get(callUrl,function(newState){
        lang = newState.length;
        if (lang == 2) {
            newState = newState.substr(1, 1);
            lang = newState.length;
        }
        if (newState == "0") {
            $(".bookmarkButton").removeClass("bookmarkActiveButton");
        } else {
            if (newState == "1") {
                $(".bookmarkButton").addClass("bookmarkActiveButton");
            } else {
               alert ("unkown State = '"+newState+"' lang="+lang ); 
            }
        }


        // get BookmarkList
        callUrl = "/cms_"+cmsVersion+"/getData/userData.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        callUrl += "&out=bookmarkList&userId="+userId;
    //alert (callUrl);
        $.get(callUrl,function(bookmarkList){
            $(".bookmark_frame").html(bookmarkList);
            // alert(bookmarkList);
        })
    })
})

$(".bookmarkItem").click(function(){
    url = $(this).children(".hiddenData").children(".hiddenLink").attr("href");
    // alert ("Click BookmarkList"+url);
    window.location = url;
   
    
})

$(".bookmarkLine").live("click",function(){
    url = $(this).children(".bookmarkTitle").children(".bookmarkLink").attr("href");
    $(".bookmark_frame").addClass("bookmark_frame_hidden");
   //  alert ("Click BookmarkList "+url); 
    window.location = url;
    //   
})


$(".breadCrumbDropdown_button").click(function(){
    id = $(this).attr("id");   
    hasClass = $("#"+id+"_list").hasClass("breadCrumbDropdown_List_hidden");
    
    // hide History
    $(".history_box").addClass("history_box_hidden");
    
    // hide Favoriten
    $(".bookmark_frame").addClass("bookmark_frame_hidden");
    
    // hide All BreadCrumb DropDown
    $(".breadCrumbDropdown_List").addClass("breadCrumbDropdown_List_hidden");
    
    if (hasClass) {
        $("#"+id+"_list").removeClass("breadCrumbDropdown_List_hidden");
    } else {
        $("#"+id+"_list").addClass("breadCrumbDropdown_List_hidden");
    }
})


$(document).ready(function(){
    
//    $("#slider-bxSlider").bxSlider({
//        auto: true,
//        pager: false,
//        mode: 'vertical',
//        captions : true,
//        controls : false
//    });
    
})

    

//$('#slider-slides').slides({
//        //preload: true,
//        // preloadImage: 'img/loading.gif',
//        generatePagination: false,
//        pagination: false,
//        play: 5000,
//        pause: 2500,
//        hoverPause: true,
//        animationStart: function(current){
//                $('.caption').animate({
//                        bottom:-35
//                },100);
//                if (window.console && console.log) {
//                        // example return of current slide number
//                        console.log('animationStart on slide: ', current);
//                };
//        },
//        animationComplete: function(current){
//                $('.caption').animate({
//                        bottom:0
//                },200);
//                if (window.console && console.log) {
//                        // example return of current slide number
//                        console.log('animationComplete on slide: ', current);
//                };
//        },
//        slidesLoaded: function() {
//                $('.caption').animate({
//                        bottom:0
//                },200);
//        }
//});

// $('#slider-id').codaSlider({
////    autoSlide:true,
////    autoHeight:true,
////            autoSlideInterval:4000,
////            autoSlideStopWhenClicked:false,
//     //                  dynamicArrows: true,
////            dynamicTabsAlign: "right",
////            dynamicTabsPosition: "bottom",
////            slideEaseDuration: 1000,
//   // continuous:false
//  });


function cmsData() {
    paramStr = $(".LayoutFrame").children(".hiddenData").text();
    param = getParamList(paramStr);
    return param;
}

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
}

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
}

$(document).ready(function() {
//    posLeft = $(".cmsContentFrame_51").position();
//    heightLeft = $(".cmsContentFrame_51").outerHeight();
//    bottomLeft = $(".cmsContentFrame_51").outerHeight() + posLeft.top;
//
//    posRight = $(".cmsContentFrame_56").position();
//    heightRight = $(".cmsContentFrame_56").outerHeight();
//    bottomRight = heightRight + posRight.top;
//
//
//    // alert ("Links = "+bottomLeft+" Rechts = "+bottomRight);
////    bottomRight = $("cmsContentFrame_56").outerHeight();
//    if (bottomLeft) {
//        if (bottomRight>0) {
//            if (bottomLeft > bottomRight) {
//                diff = bottomLeft - bottomRight;
//                //  $(".cmsContentFrame_56").height(heightRight + diff);
//
//            }
//            // alert ("gefunden team left:"+bottomLeft+" right:"+bottomRight);
//        }
//    }
//
//    leftTop = $(".cmsContentFrame_57").position().top;
//    rightTop = $(".cmsContentFrame_26").position().top;
//    if (leftTop != rightTop) {
//        if (leftTop > rightTop) {
//            $(".cmsContentFrame_26").offset({top:leftTop});
//        } else {
//            $(".cmsContentFrame_57").offset({top:rightTop});
//        }
//    }

    
    //$.ColorPicker.init();
    
});
