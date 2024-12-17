var actSiteMapId = 0;

function liveSort() {
    // cmsSitemapSortList
    // cmsSitemapSortItem
    $(".cmsSitemapSortList_1").sortable( {
        placeholder: 'sitemapPlaceholder',
        forcePlaceholderSize: true,
        cursor : 'move',

        connectWith: '.cmsSitemapSortList_1',
	items: '.cmsSitemapSortItem_1'
//		cursor: 'move',
//		placeholder: 'dragPlaceholder',
//		forcePlaceholderSize: false,
		// opacity: 0.4

    });

     $(".cmsSitemapSortList_2").sortable( {
        placeholder: 'sitemapPlaceholder',
        forcePlaceholderSize: true,
        cursor : 'move',

        //connectWith: '.cmsSitemapSortList',
	items: '.cmsSitemapSortItem_2'
//		cursor: 'move',
//		placeholder: 'dragPlaceholder',
//		forcePlaceholderSize: false,
		// opacity: 0.4

    });



//    $(".cmsSitemapSortList").disableSelection();

}








$(".siteMapClick").mouseenter(function(){
    $(this).css("cursor","pointer");
   
    
//    $(this).addClass("cmsContentHeadOver");
//    contentId = $(this).attr("contentId");
//    $(".cmsContentFrame_"+contentId).addClass("cmsContentFrameOver"); //("background-color","#e8e8e8");

})




$(".siteMapNew").click(function(){
    paramStr = $(this).children(".hiddenData").text();
    param = getParamList(paramStr);
    siteMapId = param["pageId"];
    // siteMapId = $(this).attr("pageId");
    // alert("siteMapId = "+ siteMapId);

    if (siteMapId != actSiteMapId) {
        $(".siteMapAdd_"+actSiteMapId).removeClass("cmsSitemapAddPageShow");
        actSiteMapId = siteMapId;

        cmsName    = $(".LayoutFrame").attr("cmsName");
        cmsVersion = $(".LayoutFrame").attr("cmsVersion");


        paramStr = $(".LayoutFrame").children(".hiddenData").text();
        param = getParamList(paramStr);
        cmsName = param["cmsName"];
        cmsVersion = param["cmsVersion"];
  


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
   liveSort();
   
    
});


