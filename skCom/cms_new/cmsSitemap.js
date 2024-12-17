var actSiteMapId = 0;

//function liveSort() {
//    // cmsSitemapSortList
//    // cmsSitemapSortItem
//    $(".cmsSitemapSortList_1").sortable( {
//        placeholder: 'sitemapPlaceholder',
//        forcePlaceholderSize: true,
//        cursor : 'move',
//
//        connectWith: '.cmsSitemapSortList_1',
//	items: '.cmsSitemapSortItem_1'
////		cursor: 'move',
////		placeholder: 'dragPlaceholder',
////		forcePlaceholderSize: false,
//		// opacity: 0.4
//
//    });
//
//     $(".cmsSitemapSortList_2").sortable( {
//        placeholder: 'sitemapPlaceholder',
//        forcePlaceholderSize: true,
//        cursor : 'move',
//
//        //connectWith: '.cmsSitemapSortList',
//	items: '.cmsSitemapSortItem_2'
////		cursor: 'move',
////		placeholder: 'dragPlaceholder',
////		forcePlaceholderSize: false,
//		// opacity: 0.4
//
//    });
//
//
//
////    $(".cmsSitemapSortList").disableSelection();
//
//}

function sitemap_sort_stop(){
    out = "";
    type = "hidden";

    out += "<form method='post' action=''>";
    $(".siteMap_Level1").each(function(){
        // out += "hauptzeile <br>";
        sortMain = 0;
        mainPage = 0;
        $(this).children(".sortSiteMap").children(".siteMap_content").each(function(){
            
            id = $(this).children(".siteMap_item").attr("id");
            if (id) {
                id = id.substr(8);
                name = $(this).children(".siteMap_item").children(".siteMap_showName").text();
                sortMain++;
                if (type != "hidden")  out += "subzeile "+id+" name = "+name+" sort = "+sortMain;
                out += " <input type='"+type+"' name='siteMapList["+id+"][mainPage]' value='"+mainPage+"' />";
                out += " <input type='"+type+"' name='siteMapList["+id+"][sort]' value='"+sortMain+"' />";                
                if (type != "hidden") out += " <br />";

                sortSub = 0;
                subMainPage = id;
                $("#siteMap_Level_"+id).children(".sortSiteMap").children(".siteMap_content").each(function(){
                    id = $(this).children(".siteMap_item").attr("id");
                    id = id.substr(8);
                    name = $(this).children(".siteMap_item").children(".siteMap_showName").text();
                    sortSub++;
                    if (type != "hidden") out += "subSubzeile "+id+" name = "+name+" sort = "+sortSub+" ";

                    out += " <input type='"+type+"' name='siteMapList["+id+"][mainPage]' value='"+subMainPage+"' />";
                    out += " <input type='"+type+"' name='siteMapList["+id+"][sort]' value='"+sortSub+"' />";
                    if (type != "hidden")  out += " <br />";


                    sortSubSub = 0;
                    subSubMainPage = id;
                    $("#siteMap_Level_"+id).children(".sortSiteMap").children(".siteMap_content").each(function(){
                        id = $(this).children(".siteMap_item").attr("id");
                        id = id.substr(8);
                        name = $(this).children(".siteMap_item").children(".siteMap_showName").text();
                        sortSubSub++;
                        if (type != "hidden") out += "subSubzeile "+id+" name = "+name+" sort = "+sortSubSub+" ";
                        out += " <input type='"+type+"' name='siteMapList["+id+"][mainPage]' value='"+subSubMainPage+"' />";
                        out += " <input type='"+type+"' name='siteMapList["+id+"][sort]' value='"+sortSubSub+"' />";
                        if (type != "hidden")  out += " <br />";

                    })


                })
            }
        })
    })
    out += "<input type='submit' name='siteMapSave' value='Sitemap speichern' class='cmsInputButton' />";
    out += "<input type='submit' name='siteMapCancel' value='abbrechen' class='cmsInputButton cmsSecond' />";
    out += "</form>";
    $(".siteMap_output").html(out);
    $(".siteMap_output").removeClass("siteMap_output_hidden");    
}

$(function (){
    $(".sortSiteMap_1, .sortSiteMap_2, .sortSiteMap_3").sortable({
    //$(".sortSiteMap").sortable({        
        connectWith:".sortSiteMap",
        // axis:"y",
        dropOnEmpty: true,
        curorAt: {left:0, top:0},
        cursor:"move",
        scroll:true,
        scrollSensitivity:20,
        forceHelperSize:false,
        forcePlaceholderSize: false,
        distance:20,
        // tolerance:"pointer",
        placeholder: "sitemap_placeholder",
        handle: '.sitemap_move_button',
        helper: function( event,ui ) {
            name = ui.children(".siteMap_item").html();
            width = ui.children(".siteMap_item").width();
            height = ui.children(".siteMap_item").height();
            // children(".siteMap_showName").text();; // .siteMap_showName").text();
            return $( "<div class='siteMap_helper' style='width:"+width+"px;height:"+height+"px;' >"+name+"</div>" );
        },
        
        start:function(event,ui) {
            help = ui.item.attr("class"); //jkhjk";
            // alert("event start "+help);
            //cl = ui.children(".siteMap_Level").attr("id");
            //alert("ui"+cl);
            ui.item.children(".siteMap_Level").addClass("siteMap_sort_hidden");
           // ui.children(".siteMap_Level").addClass("siteMap_Level_hidden");
        },
            
        //}
        stop: function( event, ui ) {
            ui.item.children(".siteMap_Level").removeClass("siteMap_sort_hidden");
            sitemap_sort_stop();
        }
    });
    $(".sortSiteMap").disableSelection();   
});


$(".siteMap_close").click(function(){
    id = $(this).parent().parent().attr("id");
    id = id.substr(8);

    // siteMap_sub_27" class="siteMap_sub siteMap_sub_hidden
    hasClas = $("#siteMap_Level_"+id).hasClass("siteMap_Level_hidden");
    if (hasClas) {
        $("#siteMap_Level_"+id).removeClass("siteMap_Level_hidden");
        $(this).html("Unterseiten ausblenden");
    } else {

        $("#siteMap_Level_"+id).addClass("siteMap_Level_hidden");
        $(this).html("Unterseiten zeigen");
    }
   
})


$(".siteMap_editButton").live("click",function(){
    siteMapId = $(this).parent().parent().attr("id");
    siteMapId = siteMapId.substr(8);
        
    target = $(this).parent().parent().children(".siteMap_editFrame");
    
     if (siteMapId != actSiteMapId) {
        actSiteMapId = siteMapId;
        callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=editPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        
        $.get(callUrl,function(text){
            target.html(text);
        })
        target.removeClass("siteMap_editFrame_hidden");
     } else {
        actSiteMapId = 0;
        target.addClass("siteMap_editFrame_hidden");
     }
    
})

$(".siteMap_deleteButton").live("click",function(){
    siteMapId = $(this).parent().parent().attr("id");
    siteMapId = siteMapId.substr(8);
        
    target = $(this).parent().parent().children(".siteMap_editFrame");
    
     if (siteMapId != actSiteMapId) {
        actSiteMapId = siteMapId;
        callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=deletePage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        
        $.get(callUrl,function(text){
            target.html(text);
        })
        target.removeClass("siteMap_editFrame_hidden");
     } else {
        actSiteMapId = 0;
        target.addClass("siteMap_editFrame_hidden");
     }
    
})



$(".siteMap_newButton").live("click",function(){
    siteMapId = $(this).attr("id");
    siteMapId = siteMapId.substr(18);
    
    target = $(this).parent().children(".siteMap_newEdit");
    maxSort = parseInt(target.text());
    removeClass = "siteMap_newEdit_hidden";
    
    if ($(this).hasClass("siteMape_newLine")) {
        target = $(this).parent().parent().children(".siteMap_editFrame");
        removeClass = "siteMap_editFrame_hidden";
//        target.css("background-color","#ff0000");
//        maxSort = 1;
        // alert("target "+target+" id = "+siteMapId);
    }
    
   
    
    
    if (siteMapId != actSiteMapId) {
        actSiteMapId = siteMapId;

        callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=addPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        
        $(".siteMap_newEdit").addClass("siteMap_newEdit_hidden");
        
        $.get(callUrl,function(text){
            //$(this).parent().children(".siteMap_newEdit").html("text");
            target.html(text);
            target.removeClass(removeClass);
            // alert ("text"+text);
            $(".newPage_sort").val(maxSort);
            
            //$(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
        });
                
        
    } else {
        
         target.addClass("siteMap_newEdit_hidden");
         target.html("");
         // $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
         actSiteMapId = 0;
         
         //$(".siteMapAdd_"+siteMapId).html("");

    }
    
    

    //siteMapId = param["pageId"];
    // siteMapId = $(this).attr("pageId");    
})


$(".siteMapNew").click(function(){
    return 0;
    //paramStr = $(this).children(".hiddenData").text();
    // param = getParamList(paramStr);

    siteMapId = $(this).parent().attr("id");
    siteMapId = siteMapId.substr(8);

    //siteMapId = param["pageId"];
    // siteMapId = $(this).attr("pageId");
    alert("siteMapId = "+ siteMapId);

    if (siteMapId != actSiteMapId) {
        $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
        actSiteMapId = siteMapId;

//        cmsName    = $(".LayoutFrame").attr("cmsName");
//        cmsVersion = $(".LayoutFrame").attr("cmsVersion");


//        paramStr = $(".LayoutFrame").children(".hiddenData").text();
//        param = getParamList(paramStr);
//        cmsName = param["cmsName"];
//        cmsVersion = param["cmsVersion"];
  


        callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=addPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
        $.get(callUrl,function(text){
            $(".siteMapAdd_"+siteMapId).html(text);
            $(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
        });
    } else {
         $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
         actSiteMapId = 0;
         $(".siteMapAdd_"+siteMapId).html("");

    }
})


$(".siteMapEdit").click(function(){
//    paramStr = $(this).children(".hiddenData").text();
//    param = getParamList(paramStr);
//    siteMapId = param["pageId"];


    var idStr = $(this).attr("id");
    
    if (idStr.substr(0,15) == "dynamicContent_") {
    
        var siteMapId  = idStr.substr(0,15);
        alert("siteMapId"+siteMapId+" -- "+dynamic+" \n Page="+cmsName+" version="+cmsVersion);
        if (siteMapId != actSiteMapId) {
            $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
            actSiteMapId = siteMapId;
            // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
            callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=editPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
            alert ("SitmapId = "+siteMapId+" \r ULR = "+callUrl);
            $.get(callUrl,function(text){
                $(".siteMapAdd_"+siteMapId).html(text);
                $(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
            });

        } else {
            $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
            actSiteMapId = 0;
            $(".siteMapAdd_"+siteMapId).html("");
        }
        
        
        
    } else {
        if (idStr.substr(0,18) == "cmsSitemap_editId_") {
            var siteMapId = idStr.substr(18);

            if (siteMapId != actSiteMapId) {
                $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
                actSiteMapId = siteMapId;
                // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
                callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=editPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
                // alert ("SitmapId = "+siteMapId+" \r ULR = "+callUrl);
                $.get(callUrl,function(text){
                    $(".siteMapAdd_"+siteMapId).html(text);
                    $(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
                });

            } else {
                $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
                actSiteMapId = 0;
                $(".siteMapAdd_"+siteMapId).html("");
            }


        } else {
            alert ("Edit = "+idStr+ " \r edit = "+siteMapStr);
        }
    }
        

   
    


   
})

$(".siteMapDelete").click(function(){
    var idStr = $(this).attr("id");
    if (idStr.substr(0,15) == "dynamicContent_") {
    
        var siteMapId  = idStr.substr(0,15);
        alert("siteMapId"+siteMapId+" -- "+dynamic+" \n Page="+cmsName+" version="+cmsVersion);
        if (siteMapId != actSiteMapId) {
            $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
            actSiteMapId = siteMapId;
            // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
            callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=editPage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
            alert ("SitmapId = "+siteMapId+" \r ULR = "+callUrl);
            $.get(callUrl,function(text){
                $(".siteMapAdd_"+siteMapId).html(text);
                $(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
            });

        } else {
            $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
            actSiteMapId = 0;
            $(".siteMapAdd_"+siteMapId).html("");
        }
        
        
        
    } else {
        if (idStr.substr(0,20) == "cmsSitemap_deleteId_") {
            siteMapId = idStr.substr(20);
        }
    }
    
    if (siteMapId) {
        if (siteMapId != actSiteMapId) {
            $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
            actSiteMapId = siteMapId;
            // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
            callUrl = "/cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=deletePage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;

           // $(".siteMapAdd_"+siteMapId).css("margin-left","0px");
            $.get(callUrl,function(text){
                $(".siteMapAdd_"+siteMapId).html(text);
                $(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
            });
        } else {
             $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
             actSiteMapId = 0;
             $(".siteMapAdd_"+siteMapId).html("");
        }
    }
})



$(".siteMapClick").mouseleave(function(){
    $(this).css("cursor","default");
    //$(this).removeClass("cmsContentHeadOver");
    //contentId = $(this).attr("contentId");
    //$(".cmsContentFrame_"+contentId).removeClass("cmsContentFrameOver");//css("background-color","#fff");
})

$(document).ready(function() {
   // liveSort();
   
    
});


