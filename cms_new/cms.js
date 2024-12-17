var flipFrameSelect;

/* FOR NAVIGATION */
$(".mobileNavigation_titleLine").click(function(){
    if ($(this).hasClass("mobileNavigation_titleLine_dontOpen")) return 0;
    id = $(this).attr("id");
    id = id.substr(10);
    
    if ($("#mobileNav_List_"+id).hasClass("mobileHidden")) {
        $("#mobileNav_List_"+id).removeClass("mobileHidden");
    } else {
        $("#mobileNav_List_"+id).addClass("mobileHidden");
    }
})

$(".mobileNavigationShow").click(function(){
    if ($(".mobileNavigation_pageList").hasClass("mobileHidden")) {
        $(".mobileNavigation_pageList").removeClass("mobileHidden");
    } else {
        $(".mobileNavigation_pageList").addClass("mobileHidden");
    }
    
}) 

$(".mobileNavigation_titleLine_item").click(function(){
    id = $(this).attr("id");
    if (!id) return 0;
    
    mainObj =  $(this).parent().parent().children(".mobileNavigation_pageList");
    if (id=="mainMenu") {
        $(".mobileNavigation_subList").addClass("mobileHidden");
        if (mainObj.hasClass("mobileHidden")) {
            mainObj.removeClass("mobileHidden");
        } else {
            mainObj.addClass("mobileHidden");
        }
        // $(this).parent().parent().children(".mobileNavigation_pageList").removeClass("mobileHidden");
        return 0;
    }
    
    id = id.substr(7);
    
    if ($("#subList_"+id).hasClass("mobileHidden")) {
        mainObj.addClass("mobileHidden");
        $(".mobileNavigation_subList").addClass("mobileHidden");
        $("#subList_"+id).removeClass("mobileHidden");    
    } else {
        $("#subList_"+id).addClass("mobileHidden"); 
    }
})


$(".mobileNavigation_sub").click(function(){
    className = $(this).parent().parent().attr("class");
    if ($(this).parent().parent().children(".mobileNavigation_itemList").hasClass("mobileHidden")) {
          $(this).parent().parent().children(".mobileNavigation_itemList").removeClass("mobileHidden");
          $(this).addClass("mobileNavigation_subOpen");
          $(this).removeClass("mobileNavigation_subClose");
    } else {
        $(this).parent().parent().children(".mobileNavigation_itemList").addClass("mobileHidden");
        
        $(this).addClass("mobileNavigation_subClose");
        $(this).removeClass("mobileNavigation_subOpen");
    }
    
})


$(".mobileNavigation_item").click(function(){
    url = $(this).children(".mobileNavigationLink").attr("href");
    // alert ("url "+url);
})
//wh = $(window).width();
//
//alert ("Orienation = "+target_orientation+" wh="+wh+" ");



 function detectOrientation (orientation) {
    set_orientation = "";
    if ( orientation == 0 ) {
        set_orientation = "portrait";
    }
    else if ( orientation == 90 ) {
        set_orientation = "landscape";
    }
    else if ( orientation == -90 ) {
     
        set_orientation = "landscape";
    }
    else if ( orientation == 180 ) {
        set_orientation = "portrait";     
    }
    return set_orientation;
 }    


$(window).bind('orientationchange', function(event) {
  // alert('new orientation:' + orientation);
    
    if (mobilPage) {
        setSession = 0;

        setWidth = document.documentElement.clientWidth;
        if (setWidth != target_widh) setSession = 1;

        setHeight = document.documentElement.clientHeight;
        if (setHeight != target_height) setSession = 1;

        if (setWidth > setHeight) setOrientation = "landscape";    
        else setOrientation = "portrait";   
        if (setOrientation != target_orientation) setSession = 1;


        if (setOrientation == "landscape") {
            $(".hidePortrait").removeClass("orientationHidden");
            $(".hideLandscape").addClass("orientationHidden");
            
//            $(".hidePortrait").removeClass("orientationHidden");
//            $(".hideLandscape").addClass("orientationHidden");       
        } 
        if (setOrientation == "portrait" ) {
            $(".hidePortrait").addClass("orientationHidden");
            $(".hideLandscape").removeClass("orientationHidden");
            
            //$(".hideLandscape").removeClass("orientationHidden");
            // $(".hidePortrait").addClass("orientationHidden");      
        }
        
        // alert("Set Oriantation "+setOrientation);


        setTarget = detect_target();
        if (setTarget != target_target) setSession = 1;


        if (setSession) {
            url = "/cms_"+cmsVersion+"/getData/setSession.php";
            url += "?target_width="+setWidth;
            url += "&target_height="+setHeight;
            url += "&target_orientation="+setOrientation;
            url += "&target_target="+setTarget;
            // url += "?showTarget="+setTarget;
            url += "&cmsName="+cmsName;
            

            $.get(url,function(res){
                
                // window.location.reload();
                setFrameSize(setWidth,setHeight);
            })
        }
    }
  
});

function setFrameSize(setWidth,setHeight) {
    // alert ("Set Frame Size to "+setWidth+"x"+setHeight);
    $(".layoutFrame").css("width",setWidth+"px");
}

function removeFrameSize() {
    $(".layoutFrame").css("width","");
}

$(".cmsText_readMore_button").click(function(){
    $(this).parent().children(".cmsText_readMore_short").addClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readMore_button").addClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readMore_all").removeClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readLess_button").removeClass("cmsText_readMore_hidden");
    
    
    str = "";
    obj = $(this).parent();
    while (!obj.hasClass("layoutFrame")) {
        if (obj.hasClass("tableListLine")) equalHeight(obj.children('.tableItem'));
        
       //  if (obj.hasClass("cmsTypeFrame")) equalHeight(obj.children('.cmsTypeSubframe'));
       if (obj.hasClass("cmsSameHeight")) equalHeight(obj.children(".cmsSameHeightItem"));
        
        str += obj.attr("class")+"\n";
        obj = obj.parent();
    }

})

$(".cmsText_readLess_button").click(function(){
    $(this).parent().children(".cmsText_readMore_short").removeClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readMore_button").removeClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readMore_all").addClass("cmsText_readMore_hidden");
    $(this).parent().children(".cmsText_readLess_button").addClass("cmsText_readMore_hidden");
    
    obj = $(this).parent();
    while (!obj.hasClass("layoutFrame")) {
        if (obj.hasClass("tableListLine")) equalHeight(obj.children('.tableItem'));
        // if (obj.hasClass("cmsTypeFrame")) equalHeight(obj.children('.cmsTypeSubframe'));
        if (obj.hasClass("cmsSameHeight")) equalHeight(obj.children(".cmsSameHeightItem"));
        obj = obj.parent();
    }

    
})


$(".cmsLanguageSelectList").change(function(){
    setLg = $(this).val();
    
    if ($(this).hasClass("cmsLanguge_"+setLg)) return 0;
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?lg="+setLg;
    url += "&cmsName="+cmsName;
    $.get(url,function(res){
        window.location.reload();
    })
})

$(".cmsLanguageSelect").click(function(){
    setLg = $(this).attr("id");
    setLg = setLg.substr(12);
    
    if ($(this).hasClass("cmsLanguage_selected")) return 0;
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?lg="+setLg;
    url += "&cmsName="+cmsName;
    $.get(url,function(res){
        window.location.reload();
    })
    
    //alert("Set LG TO "+setLg);
})

$(".cmsWireframe_off").live("click",function(){
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?wireframe=1";
    url += "&cmsName="+cmsName;
    // $_SESSION[wireframe]
    
    // alert ("Click Wireframe "+url);
    $.get(url,function(res){
        window.location.reload();
    })
})  

$(".cmsWireframeSwitch").change(function(){
    // state = $(this).val();
    // alert ("Change state ="+state);
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?wireframe=0";
    url += "&cmsName="+cmsName;
    // $_SESSION[wireframe]
    
    // alert ("Click Wireframe "+url);
    $.get(url,function(res){
        window.location.reload();
    })
})


$(".cmsFrameLink").live("click",function(){
    url = $(this).children(".hiddenData").children(".hiddenLink").attr("href");
    if (!url) {
        url = $(this).children(".hiddenLink").attr("href");
    }
    
    
    if (url) {
        window.location = url; //"index.php";
    }
})

$(".cmsScroll").click(function(){
    id = $(this).attr("id");
    scrollY = 0;
    if (id) {
        first = id.substr(0,6);
        if (first == "anker_") {
            nr = "inh_"+id.substr(6);
            // var pos = $("#"+nr).position();
            pos = $("#"+nr).offset();
            myClass = $("#"+nr).attr("class");
            goY = pos.top;
            goX = pos.left;
            if (goY) {
                scrollY = goY;
            }
            
            
            
//            alert ("NR is "+pos+" Y = "+goY+ " X = "+goX+"\n Class="+myClass);
//            return 0;
        }
//        alert (first);
//        return 1;
    }
    

    //alert ("Scroll "+id);
    $("html, body").animate({
        scrollTop: scrollY
    }, 600);
    // $("html,body").scrollTop();
    /*
     [href*=#]').bind("click", function(event) {
		event.preventDefault();
		var ziel = $(this).attr("href");

		$('html,body').animate({
			scrollTop: $(ziel).offset().top
		}, 2000 , function (){location.hash = ziel;});
});
     */

})

$(".cmsContentFrame_open").live("click",function(){
    id = $(this).attr("id");

    hasClass = $(".cmsContentFrame_"+id).hasClass("cmsContentFrame_hidden");

   
        $(".cmsContentFrame_"+id).addClass("cmsContentFrame_hidden");

        $(this).addClass("cmsContentFrame_close");
        $(this).removeClass("cmsContentFrame_open");

        $(this).children(".cmsContentFrame_open_text").addClass("cmsContentFrame_close_text");
        $(this).children(".cmsContentFrame_open_text").removeClass("cmsContentFrame_open_text");


        $(this).children(".cmsContentFrame_open_button").addClass("cmsContentFrame_close_button");
        $(this).children(".cmsContentFrame_open_button").removeClass("cmsContentFrame_open_button");

})

$(".cmsContentFrame_close").live("click",function(){
    id = $(this).attr("id");

    hasClass = $(".cmsContentFrame_"+id).hasClass("cmsContentFrame_hidden");

   //  if (hasClass) {
        $(".cmsContentFrame_"+id).removeClass("cmsContentFrame_hidden");


        $(this).addClass("cmsContentFrame_open");
        $(this).removeClass("cmsContentFrame_close");

        $(this).children(".cmsContentFrame_close_text").addClass("cmsContentFrame_open_text");
        $(this).children(".cmsContentFrame_close_text").removeClass("cmsContentFrame_close_text");


        $(this).children(".cmsContentFrame_close_button").addClass("cmsContentFrame_open_button");
        $(this).children(".cmsContentFrame_close_button").removeClass("cmsContentFrame_close_button");

    


})



var noClick;
$(".tableItemNoClick").mouseover(function(){
    noClick = 1;
    // alert("over");
})

$(".tableItemNoClick").mouseout(function(){
    noClick = 0;
   // alert("out");
})

$(".tableItemClick").click(function(){
    url = $(this).children(".hiddenLink").attr("href");
    hasNoClick = $(this).hasClass("noClick");
    if (hasNoClick) {
        // alert("noClick");
        return 0;
    }
    if (noClick) {
        // alert("NOCLICK SET");
        return 1;
    }
    window.location = url; //"index.php";
})

var noClick;
$(".listItemNoClick").mouseover(function(){
    noClick = 1;
    // alert("over");
})

$(".listItemNoClick").mouseout(function(){
    noClick = 0;
   // alert("out");
})

$(".listItemClick").click(function(){
    url = $(this).children(".hiddenLink").attr("href");
    hasNoClick = $(this).hasClass("noClick");
    if (hasNoClick) {
        // alert("noClick");
        return 0;
    }
    if (noClick) {
        // alert("NOCLICK SET");
        return 1;
    }
    window.location = url; //"index.php";
})



$(".sliderItemClick").click(function(){
    url = $(this).children(".hiddenLink").attr("href");
    //url = $(this).attr("link");
    //alert("url " + url);

    window.location = url; //"index.php";
})


$(".naviItemDiv").click(function(){
    url = $(this).children(".hiddenLink").attr("href");
    // alert("clickNaviItem "+url);
    window.location = url;
})


$(".titleLineBack").click(function(){
    hasClass =$(this).hasClass("titleLineBack_close");
    if (hasClass) {
        return 0;
    }
    
    // Hide History if open
    hidden = $(".history_box").hasClass("history_box_hidden");
    if (!hidden) {        
        $(".history_box").addClass("history_box_hidden");       
    }

        
    // Hide Bookmark if open   
    hidden = $(".bookmark_frame").hasClass("bookmark_frame_hidden");
    if (!hidden) {
        $(".bookmark_frame").addClass("bookmark_frame_hidden");   
    }
    
    // hide titleLineBack
    $(".titleLineBack").addClass("titleLineBack_close");    
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
         $(".titleLineBack").removeClass("titleLineBack_close");
    } else {
        $(".history_box").addClass("history_box_hidden");
        $(".titleLineBack").addClass("titleLineBack_close");
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
         $(".titleLineBack").removeClass("titleLineBack_close");

    } else {
        $(".bookmark_frame").addClass("bookmark_frame_hidden");
        $(".titleLineBack").addClass("titleLineBack_close");
       
    }
    
    
})


$(".historyLine").click(function(){
    url = $(this).children(".historyTitle").children(".historyLink").attr("href");
    window.location = url;
    //alert ("Click BookmarkList"+url);

})




$(".bookmarkButton").click(function(){
    active = $(".bookmarkButton").hasClass("bookmarkActiveButton");
    
    pageName = $(this).parent().children(".hiddenBookmark").text();
    
    // alert("BookmarkButton PageName = "+pageName+" Active = "+active);
    
    mode = "toggle";
    callUrl = "/cms_"+cmsVersion+"/getData/userData.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl += "&out=setBookmark&mode="+mode+"&pageName="+pageName;
   
//    
//    
//    userId = $(".hiddenBookmark").attr("id");
//    link = $(".hiddenBookmark").attr("href");
//    state = $(".hiddenBookmark").attr("name");
//    breadCrumb = $(".hiddenBookmark").attr("title");
//    
//    mode = "toggle";
//    callUrl = "/cms_"+cmsVersion+"/getData/userData.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
//    callUrl += "&out=setBookmark&userId="+userId+"&mode="+mode+"&url="+link+"&breadCrumb="+breadCrumb;
    // alert (callUrl);
    $.get(callUrl,function(newState){
        lang = newState.length;
        // alert( "lang = "+lang+" stat = "+newState);
        if (lang == 2) {
            newState = newState.substr(1, 1);
            lang = newState.length;
        }
        if (lang == 3) {
            newState = newState.substr(2, 1);
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
        callUrl += "&out=bookmarkList"; //&userId="+userId;
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


$(".cmsFlipMainFrame_roll").mouseover(function(){
    // var flipFrameSelect;
    frameSelect = parseInt(flipFrameSelect);
    // alert ("flip ="+flipFrameSelect+" sel="+frameSelect);
    if (frameSelect > 0) {
        // alert ("FlipSelect is active"+FlipFrameSelect);
        return 0;
    }
    flipId = $(this).attr("id");
    
    hideName = ".cmsFlipContent_"+flipId+"_1";
    showName = ".cmsFlipContent_"+flipId+"_2";
    
    $(hideName).addClass("cmsFlipContent_hidden");
    $(showName).removeClass("cmsFlipContent_hidden");
    
})

$(".cmsFlipMainFrame_roll").mouseout(function(){
    flipId = $(this).attr("id");
    
    // var flipFrameSelect;
    frameSelect = parseInt(flipFrameSelect);
    if (frameSelect > 0) {
        // alert ("FlipSelect is active"+frameSelect);
        return 0;
    }
    
    
    hideName = ".cmsFlipContent_"+flipId+"_1";
    showName = ".cmsFlipContent_"+flipId+"_2";
    
    $(hideName).removeClass("cmsFlipContent_hidden");
    $(showName).addClass("cmsFlipContent_hidden");
})


$(".cmsFlipMainFrame_click").click(function(){
    flipId = $(this).attr("id");
    
    frameSelect = parseInt(flipFrameSelect);
    if (frameSelect > 0) {
        // alert ("FlipSelect is active"+frameSelect);
        return 0;
    }
    
    anz = $(this).children(".cmsFlipMainFrame_"+flipId+"_count").text();
    active = $(this).children(".cmsFlipMainFrame_"+flipId+"_active").text();
    
    anz = parseInt(anz);
    active = parseInt(active);
    
    
    showNr = active + 1;
    if (showNr > anz) showNr = 1;
    
    $(this).children(".cmsFlipMainFrame_"+flipId+"_active").text(showNr);
    
    
    
    hideName = ".cmsFlipContent_"+flipId+"_"+active;
    showName = ".cmsFlipContent_"+flipId+"_"+showNr;
//    
    $(hideName).addClass("cmsFlipContent_hidden");
    $(showName).removeClass("cmsFlipContent_hidden");
    
})

// ************************************************************************** //
// ** CMS TEXT EDIT                                                        ** //
// ************************************************************************** //
$(".cmsEditText_areaSelect").live("click",function(){
    id = $(this).attr("id");
    area = id.substr(12);
    hideOther = 1;
    setActive = 1;
    
    if (setActive) {
        isActive = $(this).hasClass("cmsEditText_area_selectActive");
        $(".cmsEditText_areaSelect").removeClass("cmsEditText_area_selectActive");
        if (!isActive) {
            
        // } else {
            $(this).addClass("cmsEditText_area_selectActive");
        }
    }
    
    
    
    mainClass = ".cmsEditText_areaList_"+area;
    isHidden = $(mainClass).hasClass("cmsEditText_hidden");
    
    if (hideOther) {
        $(".cmsEditText_areaList").addClass("cmsEditText_hidden");        
    }
    
    text = $(mainClass).text();
    if (text == "") {
        newHtml = $(".cmsEditText_help_"+area).html();
        if (newHtml) {
            $(mainClass).html(newHtml);
            $(".cmsEditText_help_"+area).html("<h1>WEG!</h1>");
        }
//        alert("Main Class has no text ");
//        newHtml = $(".cmsEditText_help_"+area).html();
//        alert ("NEW "+newHtml);
//        $(mainClass).html("<h1>HELLO WORLD</h1>");
        
    }
    
    
    if (isHidden) {
        // alert ("show");
        $(mainClass).removeClass("cmsEditText_hidden");         
    } else {
        // alert ("hide");
        $(mainClass).addClass("cmsEditText_hidden");
    }
    
})

$(".cmsEditText_typeSelect").live("click",function(){
    id = $(this).attr("id");
    type = id.substr(17);
    hideOther = 1;
    setActive = 1;
    
    if (setActive) {
        isActive = $(this).hasClass("cmsEditText_typeSelect_selectActive");
        $(".cmsEditText_typeSelect ").removeClass("cmsEditText_typeSelect_selectActive");
        if (!isActive) $(this).addClass("cmsEditText_typeSelect_selectActive");        
    }
    
    mainClass = ".cmsEditText_codeList_"+type;
    isHidden = $(mainClass).hasClass("cmsEditText_hidden");
    
    if (hideOther) {
        $(".cmsEditText_codeList").addClass("cmsEditText_hidden");        
    }
    
    if (isHidden) {
        // alert ("show");
        $(mainClass).removeClass("cmsEditText_hidden");         
    } else {
        // alert ("hide");
        $(mainClass).addClass("cmsEditText_hidden");
    }
    
    
})


$(".cmsEditText_codeSelect").live("click",function(){
    id = $(this).attr("id");
    code = id.substr(17);
    hideOther = 1;
    setActive = 1;
    // cmsEditText_codeSelect cmsEditText_codeList_tabSettings
    
    if (setActive) {
        isActive = $(this).hasClass("cmsEditText_codeSelect_selectActive");
        // $(".cmsEditText_codeSelect").removeClass("cmsEditText_codeSelect_selectActive");
        if (isActive) {
            $(this).removeClass("cmsEditText_codeSelect_selectActive");        
        } else {
            $(this).addClass("cmsEditText_codeSelect_selectActive");        
        }
    }
    
    // parentClass = $(this).parent().attr("class");
    parentId    = $(this).parent().attr("id");
    
    list = parentId.split("**");
    area = list[0];
    type = list[1];
    
    
    textId = "#cmsEditText_"+area+"_"+type+"_"+code;
    
    // alert ("textId = '"+textId+"' area="+area+" type="+type+" code = "+code+" id="+parentId);
    
    if ($(textId).hasClass("cmsEditText_hidden")) {
        $(textId).removeClass("cmsEditText_hidden");
    } else {
        $(textId).addClass("cmsEditText_hidden");
    }
})


$(".cmsEditText_typeSelect").click(function(){
    //alert ("cmsType SELECT ");
})

$(".cmsEditText_codeSelect").click(function(){
    // alert ("cmsCode SELECT ");
})


$(".cmsFilterButton").click(function(){
     close = $(this).parent().children(".cmsFilterList").hasClass("cmsFilterHidden");
     if (close) {
         $(this).parent().children(".cmsFilterList").removeClass("cmsFilterHidden");
         // $(this).parent().children(".cmsFilterList").height()
        
     } else {
         $(this).parent().children(".cmsFilterList").addClass("cmsFilterHidden");        
     }
})

$(".cmsFilterOutputButton").click(function(){
     close = $(this).parent().children(".cmsFilterOutputList ").hasClass("cmsFilterHidden");
     if (close) {
         $(this).parent().children(".cmsFilterOutputList ").removeClass("cmsFilterHidden");
         $(this).css("margin-top","0px");
         // $(this).parent().children(".cmsFilterList").height()
        
     } else {
         $(this).parent().children(".cmsFilterOutputList ").addClass("cmsFilterHidden");        
         $(this).css("margin-top","");
     }
})

$(".cmsCalendarSelect_back").live("click",function(){
    go = $(this).children(".hiddenData").text();
    go = go.substr(7);
    calendarObj = $(this).parent().parent().parent();
    
    width = calendarObj.children(".cmsCalendarSelectFrame").width();
    frameId = $(this).attr("id");
    
    // alert ("click Day " +go + "class = "+calendarObj.attr("class"));
   
    url = "/cms_"+cmsVersion+"/getData/datesData.php";
    
    
    url += "?out=selectCalendar&date="+go;
    url += "&frameId="+frameId;
    url += "&width="+width;
    url += "&cmsVersion="+cmsVersion;
    // calendarObj.children(".cmsCalendarSelectFrame").html(url);
    
    
     $.get(url,function(res){
         calendarObj.children(".cmsCalendarSelectFrame").html(res);
         
     })
})

$(".cmsCalendarSelect_for").live("click",function(){
    go = $(this).children(".hiddenData").text();
    go = go.substr(7);
    calendarObj = $(this).parent().parent().parent();
    
    width = calendarObj.children(".cmsCalendarSelectFrame").width();
    frameId = $(this).attr("id");
    
    // alert ("click Day " +go + "class = "+calendarObj.attr("class"));
   
    url = "/cms_"+cmsVersion+"/getData/datesData.php";
    
    
    url += "?out=selectCalendar&date="+go;
    url += "&frameId="+frameId;
    url += "&width="+width;
    url += "&cmsVersion="+cmsVersion;
    // calendarObj.children(".cmsCalendarSelectFrame").html(url);
    
    
     $.get(url,function(res){
         calendarObj.children(".cmsCalendarSelectFrame").html(res);
         
     })
    
    
})

$(".cmsCalendarSelect_day").live("click",function(){
    id = $(this).attr("id");
    date = id.substr(18);
    calendarObj = $(this).parent().parent().parent();
    // alert ("click Day " +id + "class = "+calendarObj.attr("class"));
    
    calendarObj.children(".cmsCalendarSelectFrame").addClass("cmsCalendarSelectFrame_hidden");
    
    calendarObj.children(".cmsCalendarValue").val(date);
    
    dateList = date.split("-");
    
    dateStr = dateList[2]+"."+dateList[1]+"."+dateList[0];
    calendarObj.children(".cmsCalendarInput").children(".cmsCalendarSelect").val(dateStr);
})

$(".cmsCalendarSelect").click(function(){
    val = $(this).parent().parent().children(".cmsCalendarValue").val();
    calendarObj = $(this).parent().parent();
    
    hidden = calendarObj.children(".cmsCalendarSelectFrame").hasClass("cmsCalendarSelectFrame_hidden");
    if (hidden) {
        $(".cmsCalendarSelectFrame").addClass("cmsCalendarSelectFrame_hidden");
        calendarObj.children(".cmsCalendarSelectFrame").removeClass("cmsCalendarSelectFrame_hidden");
    } else {
        calendarObj.children(".cmsCalendarSelectFrame").addClass("cmsCalendarSelectFrame_hidden");
    }
    
})


//$(".cmsCalendarInput").click(function(){
//    calendarObj = $(this).parent();
//    
//    alert("calendar "+calendarObj.attr("class"));
//    
//})

$(".socialMediaButton").click(function(){
    link = $(this).children(".hiddenData").children(".hiddenLink").attr("href");
    if (link) {
        window.open(link);
    }
    
    
})

$(".socialMediaWindow_button").click(function(){
    
    visible = $(this).parent().children(".socialMediaWindow ").hasClass("socialMediaWindow_show");
    if (visible) {
        $(this).parent().parent().parent().children(".socialMediaWindowBack ").removeClass("socialMediaWindowBack_show");
        $(this).parent().children(".socialMediaWindow").removeClass("socialMediaWindow_show");
        $(this).removeClass("socialMediaWindow_buttonActive");
        $(".socialMediaFrame").removeClass("socialMediaFrame_active");
       
    } else {
        $(this).parent().parent().parent().children(".socialMediaWindowBack ").addClass("socialMediaWindowBack_show");
        $(this).parent().children(".socialMediaWindow ").addClass("socialMediaWindow_show");
        $(this).addClass("socialMediaWindow_buttonActive");
        $(this).parent().addClass("socialMediaFrame_active");
    }
    
})

$(".socialWindowClose").click(function(){
    $(".socialMediaWindowBack").removeClass("socialMediaWindowBack_show");
    $(".socialMediaWindow").removeClass("socialMediaWindow_show");    
    $(".socialMediaWindow_button").removeClass("socialMediaWindow_buttonActive");
    $(".socialMediaFrame").removeClass("socialMediaFrame_active");
//    $(this).parent().parent().parent().children(".socialMediaWindowBack").removeClass("socialMediaWindowBack_show");
//    $(this).parent().parent().removeClass("socialMediaWindow_show");
//    $(this).parent().parent().addClass("TRULLA");
    
})

$(".socialMediaWindowBack").click(function(){
    $(this).removeClass("socialMediaWindowBack_show");
    $(".socialMediaWindow").removeClass("socialMediaWindow_show");
    $(".socialMediaWindow_button").removeClass("socialMediaWindow_buttonActive");
    $(".socialMediaFrame").removeClass("socialMediaFrame_active");
})

$(".cmsBoxReload_frame").click(function(){
    // alert ("Start");
    
    
    cmsBoxReload($(this));
//    maxWidth = $(this).width();
//    wait =  parseInt($(this).children(".cmsBoxReload_wait").text());
//    url = $(this).children(".cmsBoxReload_url").attr("href");
//    
//    // alert ("WAIT = "+wait+" url="+url);
//    
//    
//    $(this).children(".cmsBoxReload_process").width("0");
//    $(this).children(".cmsBoxReload_process").css("opacity",0.5);
//    $(this).children(".cmsBoxReload_process").animate({
//          width: maxWidth+"px"
//          
//    }, wait, function() {
//        if (url) {
//            window.location = url;
//        } else  {
//            window.location.reload();
//        }        
//    })  
    
    
})

$(".cmsBoxReload_cancel").click(function(){
    $(this).parent().children(".cmsBoxReload_frame").children(".cmsBoxReload_process").stop();
    
})

function cmsBoxReload(obj) {
    if (!obj) {
        alert ("NO OBJEXT FIND ");
        return 0;
    }
    
    maxWidth = obj.width();
    wait =  parseInt(obj.children(".cmsBoxReload_wait").text());
    url = obj.children(".cmsBoxReload_url").attr("href");
    
    // alert ("WAIT = "+wait+" url="+url);
    
    
    obj.children(".cmsBoxReload_process").width("0");
    obj.children(".cmsBoxReload_process").css("opacity",0.5);
    obj.children(".cmsBoxReload_process").animate({
          width: maxWidth+"px"
          
    }, wait, "linear", function() {
        if (url) {
            window.location = url;
        } else  {
            window.location.reload();
        }        
    })  

}


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



function equalHeight(container,subContainer) {
    var maxheight = 0;
    // alert(" container = "+container.attr("class"));
    
    container.each(function() {
        
//        if (subContainer) {
//           var height = $(this).children(subContainer).height();
//           var id = $(this).children(subContainer).attr("id");
//        } else {
//        else

        // if ($(this).css("height")) {
            $(this).height("auto");
//            alert ($(this).attr("class")+" has Height "+$(this).css("height"));
//        }
            var height = $(this).height();
            if (height == 0) {
                // alert ("height is 0");
                display = $(this).css("display");
                if (display == "none") {
                    $(this).css("display","block");
                    height = $(this).height();
                    $(this).css("display","");
                }
            }
       // }
        
        
        // alert(" height = "+height+" container = "+container.attr("class"));
        if(height > maxheight) {
            maxheight = height;
        }
    });
    if (maxheight > 0) {
         // maxheight = 200;
        //alert("same Height "+maxheight);
        // container.height(maxheight);
        if (subContainer) container.children(subContainer).height(maxheight);
        else container.height(maxheight);
    }
}


function equalHeightSub(container,subContainer) {
    var maxheight = 0;
    container.each(function() {
        var height = $(this).children(subContainer).height();
        var id = $(this).children(subContainer).attr("id");
       
        if (height == 0) {
            $(this).css("display","block");
            height = $(this).height();
            $(this).css("display","");
        }
        
        
        // alert(" height = "+height+" id="+id);
        if(height > maxheight) {
            maxheight = height;
        }
    });
    // maxheight = 400;
    if (maxheight > 0) {
         // maxheight = 200;
        //alert("same Height "+maxheight);
        // container.height(maxheight);
        container.children(subContainer).height(maxheight);
        
    }
}


$(document).load(function(){
//    var width = $(window).width();
//    var height = $(window).height();
//    
//    alert("showTarget "+showTarget+" dim = "+width+"x"+height);
    
    
    
})


$(document).ready(function() {
    // alert("showTarget "+showTarget+" dim = "+width+"x"+height);
    
    $(".cmsBoxReload_frame").each (function () {
        cmsBoxReload($(this));
    })
    
    
    $(".cmsSameHeight").each (function () {
        equalHeight($(this).children(".cmsSameHeightItem")); //+$(this).attr('id')+' .autoheight'));
    });
    
    $('.tableListLine').each (function () {
        equalHeight($(this).children('.tableItem')); //+$(this).attr('id')+' .autoheight'));
    });
     
   
    $('.cmsFlipMainFrame_sameHeight').each (function () {
      //   alert("FlipContainer");
       equalHeight($(this).children('.cmsFlipContent')); //+$(this).attr('id')+' .autoheight'));
    });
     
 
  
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

$(document).ready(function() {
   // window.scroll(0,10);   
});
