$(".cmsEditFrame_SiteBar").mouseenter(function() {
    $(this).css("cursor","pointer");
})

$(".cmsEditFrame_SiteBar").mouseleave(function() {
    $(this).css("cursor","normal");
})

$(".cmsEditFrame_SiteBar").click(function(){
    hasClass = $(".cmsEditFrame_Content").hasClass("cmsEditFrame_content_hidden");
    
    if (hasClass) {
        $(".cmsEditFrame_Content").removeClass("cmsEditFrame_content_hidden");
        $(".cmsEditFrameBox").removeClass("cmsEditFrameBox_hidden");
    }
    else {
        $(".cmsEditFrame_Content").addClass("cmsEditFrame_content_hidden");
        $(".cmsEditFrameBox").addClass("cmsEditFrameBox_hidden");
    }
    
    
    // alert ("click");
})

$(".cmsContentFrame_editButton").live("click",function(){
    id = $(this).attr("id");
    //alert("click "+id);
    hasClass = $(".cmsEditFrame_Content").hasClass("cmsEditFrame_content_hidden");
    
    if (hasClass) {
        $(".cmsEditFrame_Content").removeClass("cmsEditFrame_content_hidden");
        $(".cmsEditFrameBox").removeClass("cmsEditFrameBox_hidden");
    }
    
     callUrl = "/cms_base/getData/editBox.php?button=editContent&editId="+id;
     $.get(callUrl,function(text){
       //alert (text);
       textlength = text.length;
       if (textlength > 30) {
           $(".cmsEditFrame_Content").html(text);
           return 1;
       }
       
            //$(".siteMapAdd_"+siteMapId).html(text);
            //$(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
    });
})



$(".cmsEditFrame_myCms").live("click",function(){
    out = "";
    
    callUrl = "/cms_base/getData/editBox.php?mainPage=1";
    $.get(callUrl,function(text){
       //alert (text);
       textlength = text.length;
       if (textlength > 30) {
           $(".cmsEditFrame_Content").html(text);
           return 1;
       }
       
            //$(".siteMapAdd_"+siteMapId).html(text);
            //$(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
    });
        
})

$(".cmsEditFrame_button").live("click",function(){
    id = $(this).attr("id");
    
    callUrl = "/cms_base/getData/editBox.php?button="+id;
    
    $.get(callUrl,function(text){
       //alert (text);
       textlength = text.length;
       if (textlength > 30) {
           $(".cmsEditFrame_Content").html(text);
       }
    });
    
    // alert ("click Button Id = "+id+"\n"+url+"\n"+out);
    
    
    
})


$(".cmsEditFrame_headLine").live("click",function(){
    id = $(this).attr("id");

    callUrl = "/cms_base/getData/editBox.php?button="+id;

    $.get(callUrl,function(text){
       //alert (text);
       textlength = text.length;
       if (textlength > 30) {
           $(".cmsEditFrame_Content").html(text);
       }
    });

    // alert ("click Button Id = "+id+"\n"+url+"\n"+out);



})

/*
$("#editCMS").live("click",function(){
    out = "";
    out += "<div class='cmsEditFrame_myCms'>myCMS</div>\n";
    out += "<div class='cmsEditFrame_button'>Einstellungen</div>\n";
    
    $(".cmsEditFrame_Content").html(out);
    
        
})

$("#editdata").live("click",function(){
    out = "";
    out += "<div class='cmsEditFrame_myCms'>myCMS</div>\n";
    out += "<div class='cmsEditFrame_headLine' id='editdata'>Daten</div>\n";
    
    out += "<div class='cmsEditFrame_button' id='editDataUser' >Benutzer</div>\n";
    out += "<div class='cmsEditFrame_button' id='editDataCategory' >Kategorien</div>\n";
   
    out += "<div class='cmsEditFrame_button' id='editContent' >Produkte</div>\n";
    out += "<div class='cmsEditFrame_button' id='editSitemap'>Hersteller</div>\n";
    out += "<div class='cmsEditFrame_button' id='editImages'>Projekte</div>\n";
    out += "<div class='cmsEditFrame_button' id='editdata'>Artikel</div>\n";
    out += "<div class='cmsEditFrame_button' id='editdata'>Termine</div>\n";
    out += "<div class='cmsEditFrame_button' id='editdata'>Location</div>\n";
    
    $(".cmsEditFrame_Content").html(out);
    
        
})

$("#editContent").live("click",function(){
    out = "";
    out += "<div class='cmsEditFrame_myCms'>myCMS</div>\n";
    out += "<div class='cmsEditFrame_headLine' id='editContent'>Inhalte</div>\n";
    
     $(".cmsEditFrame_Content").html(out);
})


$("#editPage").live("click",function(){
    out = "";
    out += "<div class='cmsEditFrame_myCms'>myCMS</div>\n";
    out += "<div class='cmsEditFrame_headLine' id='editPage'>Seite</div>\n";
    
    $(".cmsEditFrame_Content").html(out);
})


$("#editDataUser").live("click",function(){
    out = "";
    out += "<div class='cmsEditFrame_myCms'>my-CMS</div>\n";
    out += "<div class='cmsEditFrame_headLine'  id='editdata'>Daten</div>\n";    
    out += "<div class='cmsEditFrame_headLine2'>Benutzer</div>\n";
    
       
    $(".cmsEditFrame_Content").html(out);
    
        
})*/

