$(".searchInput").focus(function(){
    searchString = $(this).val();
    id = $(this).attr("id");
    id = id.substr(7);
    hasClass = $("#searchResult_"+id).hasClass("searchResult_window");
    if (hasClass) {
        
        searchShowResult(id,searchString)
    }    
    
})

$(".searchInput").focusout(function(){        
//    id = $(this).attr("id");
//    id = id.substr(7);
//    
//    hasClass = $("#searchResult_"+id).hasClass("searchResult_window");
//    if (hasClass) {
//        searchString = "";
//        searchShowResult(id,searchString)
//    }
})


$(".searchInput").keyup(function(){
    searchString = $(this).val();
    mylength = searchString.length;
    id = $(this).attr("id");
    id = id.substr(7);
    searchShowResult(id,searchString);
    return 0;
    
    // alert("searchString"+searchString);
    if (mylength >= 3) {
        $("#searchValue_"+id).html("Suche nach'<b>"+searchString+"</b>'");

        callUrl = "/cms_"+cmsVersion+"/getData/search.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion+"&searchString="+searchString+"&contentId="+id;
        // $("#searchResult_"+id).html(callUrl);
        $.get(callUrl,function(text){
            if (text.length>0) {
                $("#searchResult_"+id).html(text);
                $("#searchResult_"+id).removeClass("searchResult_hidden");
                $("#searchBack_"+id).removeClass("searchBack_hidden");
            }
            
        })
    } else {
        $("#searchValue_"+id).html("");
        $("#searchResult_"+id).html("");
        $("#searchResult_"+id).addClass("searchResult_hidden");
        $("#searchBack_"+id).addClass("searchBack_hidden");
    }
})

function searchShowResult(id,searchString) {
    mylength = searchString.length;  
    if (mylength >= 3) {
        $("#searchValue_"+id).html("Suche nach'<b>"+searchString+"</b>'");

        callUrl = "/cms_"+cmsVersion+"/getData/search.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion+"&searchString="+searchString+"&contentId="+id;
        // $("#searchResult_"+id).html(callUrl);
        $.get(callUrl,function(text){
            if (text.length>0) {
                $("#searchResult_"+id).html(text);
                $("#searchResult_"+id).removeClass("searchResult_hidden");
                $("#searchBack_"+id).removeClass("searchBack_hidden");
            }
            
        })
    } else {
        $("#searchValue_"+id).html("");
        $("#searchResult_"+id).html("");
        $("#searchResult_"+id).addClass("searchResult_hidden");
        $("#searchBack_"+id).addClass("searchBack_hidden");
    }
}

$(".searchBack").mouseover(function(){
//    id = $(this).attr("id");
//    id = id.substr(11);   
//    over = $("#searchResult_"+id).mouseover();
//    // alert("searchBack hover "+id+" over "+over);
//    if (over) {
//        $(this).html("ÜBER SEARCH");
//        
//    } else {
//        $(this).html("Mach Zu");
//    }    
})

$(".searchResult").mouseenter(function(){
    id = $(this).attr("id");
    id = id.substr(13);   
    hasClass = $(this).hasClass("searchResult_window");
    // alert("hover "+id+" / "+hasClass);
    if (hasClass) {
        $("#searchBack_"+id).addClass("searchBack_dontClose");
    }
})

$(".searchResult").mouseleave(function(){
    id = $(this).attr("id");
    id = id.substr(13);   
    hasClass = $(this).hasClass("searchResult_window");
    if (hasClass) {
        $("#searchBack_"+id).removeClass("searchBack_dontClose");
    }
})

$(".searchBack").click(function(){
    id = $(this).attr("id");
    id = id.substr(11);   
    hasClass = $("#searchBack_"+id).hasClass("searchBack_dontClose");
    if (hasClass) {
        alert("DONT CLOSE "+id);
        return 0;
    }
  
    
    $("#searchValue_"+id).html("");
    $("#searchResult_"+id).html("");
    $("#searchResult_"+id).addClass("searchResult_hidden");
    $("#searchBack_"+id).addClass("searchBack_hidden");
}) 


$(".searchCancelButton").click(function(){
    id = $(this).parent().attr("id");
    id = id.substr(12);    
    $("#searchResult_"+id).html("");
    $("#searchResult_"+id).addClass("searchResult_hidden");
    $("#search_"+id).val("");
    $(".searchValue").html("");
    
})

$(".searchHitHeadline").live("click",function(){
    id = $(this).attr("id");
    id = id.substr(18);
    hidden =  $(this).parent().children(".searchHitResults").hasClass("searchHitResults_hidden"); //+id).hasClass("searchHitResults_hidden");
    if (hidden) {
        $(this).parent().children(".searchHitResults").removeClass("searchHitResults_hidden");
        $(this).parent().children(".searchHit_showMore").css("display","block");
        $(this).parent().children(".searchHit_showLess").css("display","block");
    } else {
        $(this).parent().children(".searchHitResults").addClass("searchHitResults_hidden");
        $(this).parent().children(".searchHit_showMore").css("display","none"); 
        $(this).parent().children(".searchHit_showLess").css("display","none"); 
    }
})

$(".searchHit_showMore").live("click",function(){
    $(this).parent().children(".searchHitResults").children(".searchHit").each(function(){ //     
        $(this).removeClass("searchHit_hidden");        
    })
    
    $(this).removeClass("searchHit_showMore");
    $(this).addClass("searchHit_showLess");
    $(this).text("Zeige weniger Treffer");
    

    
})


$(".searchHit_showLess").live("click",function(){
    
    id = $(this).parent().parent().attr("id");
    id = id.substr(13);
    
    anz = parseInt($("#searchHitCount_"+id).val());
    // alert("id = "+id+" anzahl = "+anz);
    
    
    // anz = 5;
    nr = 0;
    $(this).parent().children(".searchHitResults").children(".searchHit").each(function(){ // .children(".searchHitResults")
//        inh = $(this).text();
       nr++;
       if (nr > anz) $(this).addClass("searchHit_hidden");        
    })
    
    $(this).removeClass("searchHit_showLess");
    $(this).addClass("searchHit_showMore");
    $(this).text("Zeige mehr Treffer");
    

    
})