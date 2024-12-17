function myokfunc(){
    // alert("This is my custom function which is launched after setting the color");
}

var activeEditTab = "";
var activeEditId = "";

var a1;
var aLoc;
var aReg;

jQuery(function() {
    // serviceUrl =  $('#queryCat').attr('url'), //'cms_base/getData/category.php',
   // alert ("hier "+serviceUrl);
    categoryUrl = $("#queryCatGetUrl").text();
    
    
    if (categoryUrl) {
        var optionsCategory = {
        //serviceUrl: $('#queryCat').attr('url'), //'cms_base/getData/category.php',
        serviceUrl: $("#queryCatGetUrl").text(),
        width: 300,
        delimiter: /(,|;)\s*/,
        deferRequestBy: 0, //miliseconds
        params: {country: 'Yes'},
        noCache: true //set to true, to disable caching
        };
        
        a1 = $('#queryCat').autocomplete(optionsCategory);
    }
    
    regionUrl = $("#queryRegionGetUrl").text();
    if (regionUrl) {
        // alert ("Region Url = "+regionUrl);
    
    
    // regionUrl =  $('#queryRegion').attr('url'), //'cms_base/getData/category.php',
    //alert ("Region "+regionUrl);
        var optionsRegion = {
          // serviceUrl: $('#queryRegion').attr('url'), //'cms_base/getData/category.php',
          serviceUrl: $("#queryRegionGetUrl").text(),
          width: 300,
          delimiter: /(,|;)\s*/,
          deferRequestBy: 0, //miliseconds
          params: {country: 'Yes'},
          noCache: false //set to true, to disable caching
        };
        aReg = $('#queryRegion').autocomplete(optionsRegion);
    }

    locationUrl = $("#locationGetUrl").text();
   // alert ("LocationUrl "+locationUrl);
    // locationUrl =  $('#queryLocation').attr('url'), //'cms_base/getData/category.php',    
    if (locationUrl) {
        // alert ("LocationUrl "+locationUrl);
    

        var optionsLocation = {
            serviceUrl: $("#locationGetUrl").text(), //'cms_base/getData/category.php',
            width: 300,
            delimiter: /(,|;)\s*/,
            deferRequestBy: 0, //miliseconds
            params: {country: 'Yes'},
            noCache: true //set to true, to disable caching
         };

      aLoc = $('#queryLocation').autocomplete(optionsLocation);
    }
});


function liveDropAble() {
    $(".dragColor").draggable({
        start: function( event, ui ) {
           cursor: "pointer"
        },
        revert: "invalid",
        cursor: "move",
        helper: function( event ) {
            bgColor   = $(this).css("background-color");
            setColor  = $(this).attr("id");
            if (setColor.substr(0,5) == "trans") {
                return $( "<div class='dragColorHelper dragColorHelperTransparent' id='"+setColor+"' style='background-color:"+bgColor+";' ></div>" );
            }
            if (setColor.substr(0,4) == "none") {
                return $( "<div class='dragColorHelper dragColorHelperNone' id='"+setColor+"' style='background-color:"+bgColor+";' ></div>" );
            }
           
            return $( "<div class='dragColorHelper' id='"+setColor+"' style='background-color:"+bgColor+";' >&nbsp;</div>" );
        }, // "<div class='trulla'>HELPER</div>",
        connectToSortable: ".cmsEditSelectColor",
        distance: 30,
       //containment: "cmsContentFramePlus",
        zIndex: 10000,
        
        cursorAt: { top: 8, left: 8}
    });

    $(".cmsEditSelectColor").droppable({
        activeClass:"cmsEditSelectColor_active",
        hoverClass: "cmsEditSelectColor_hover",
        drop: function( event, ui ) {
            bgColor   = ui.draggable.css("background-color");
            setColor  = ui.draggable.attr("id");
            type = "color";
            myId = $(this).attr("id");
           
            if (setColor.substr(0,5) == "trans") {
                type = "transparent";      
                $(this).addClass("colorTransparent");
                $(this).removeClass("colorNone");
                $(this).css("background-color","inherit");
            }
            if (setColor.substr(0,4) == "none") {
                $(this).removeClass("colorTransparent");
                $(this).css("background-color","inherit");
                $(this).addClass("colorNone");
                type = "none";
            }
            

            if (type == "color") {
                $(this).css("background-color",bgColor);
                $(this).removeClass("colorTransparent");
                $(this).removeClass("colorNone");
            }
            
           

            setId = myId.substr(5);
            start = myId.substr(0,5);
            colVal = setColor.split("|");
            
            colorId = colVal[0];
            colorValue = colVal[1];
            colorBlend = colVal[2];
            colorSaturation = colVal[3];

             $(this).html(colorSaturation);
             $("#"+setId).val(colorValue);
             $("#"+setId+"_colorId").val(colorId);
             $("#"+setId+"_colorBlend").val(colorBlend);
             $("#"+setId+"_colorSaturation").val(colorSaturation);
             // alert ("setId ="+setId+" myId = "+myId+" start="+start);
             if (start == "text_") return;

             
             layoutColor(setId);
        }
    })
       



    $(".dragNewModul").draggable({
        revert: "invalid",
        cursor: "move",
        // connectToSortable: ".cmsImageSortList",
        distance: 10,
       //containment: "cmsContentFramePlus",
        zIndex: 1000,
        scroll: true,
        scrollSensitivity: 100,
        scrollSpeed: 100,
        opcity: 1.0,
        helper: "clone",
        //handle: '.spacerDrop',
        // cursorAt: { top: 25, left: 25},
        //cursorAt: { top: 10, left: 10},
        start: function(event,ui){
            $(this).css("background-color","#333")
        },        
        stop: function(event,ui){
            $(this).css("background-color","#fff")
        }
        
    });
    
    
    $(".dragBox").draggable({
        revert: "invalid",
        cursor: "move",
        handle: '.dragButton',
        zIndex: 10000,
        helper: function( event ) {
            htmlInhalt = $(this).html();
            htmlInhalt = "Verschiebe mich";
//                help = "HELPER "+$(this).attr("class");
            return $( "<div class='ui-widget-header'>"+htmlInhalt+"</div>" );
        }, // "<div class='trulla'>HELPER</div>",
        cursorAt: { top: 0, left: 0},
        start: function(event,ui){
            $(this).css("opacity","0.2");
        },        
        stop: function(event,ui){
            $(this).css("opacity","1.0");
        }
    });
    
    $(".dragBoxNew").draggable({
        revert: "invalid",
        cursor: "move",
        // handle: '.dragButton',
        zIndex: 10000,
        helper: function( event ) {
            htmlInhalt = $(this).html();
            // htmlInhalt = "Verschiebe mich Neu";
//                help = "HELPER "+$(this).attr("class");
            return $( "<div class='ui-widget-header'>"+htmlInhalt+"</div>" );
        }, // "<div class='trulla'>HELPER</div>",
        cursorAt: { top: 0, left: 0}
//        start: function(event,ui){
//            $(this).css("opacity","0.2");
//        },        
//        stop: function(event,ui){
//            $(this).css("opacity","1.0");
//        }
    });
    
   
        // handle: '.dragButton'
        // connectWith: ".dragFaq"
//        revert: "invalid",
//        cursor: "move",
        
//        zIndex: 10000,
//        helper: function( event ) {
//                    return $( "<div class='ui-widget-header'>I'm a custom helper</div>" );
//             }, // "<div class='trulla'>HELPER</div>",
//        cursorAt: { top: 10, left: 30},
//        start: function(event,ui){
//            $(this).css("opacity","0.2");
//        },        
//        stop: function(event,ui){
//            $(this).css("opacity","1.0");
//        }
   // });
    
    
    $(".layerDrop").droppable({
        activeClass:"layerDrop_active",
        hoverClass:"layerDrop_hover",
        
        drop: function( event, ui ) {
            var setHtml = ui.draggable.html();
            var setText = ui.draggable.text();
            var setId   = ui.draggable.attr("id");
            if (setId.substr(0,12) == "cmsDragModul") {
                
                type = setId.substr(13);
                myId = $(this).attr("id");
                // alert ("Drag NEW MODUL ui= "+type + " myId = "+myId  );
                $(this).text(setText);
                // alert("#"+myId+"_type");
                $("#"+myId+"_type").val(type);
            } 
            
            // $("#"+setId).text("Hover");
        }
    })
     
     $(".spacerDrop").droppable({
        activeClass:"spacerDrop_active",
        hoverClass:"spacerDrop_hover",
        //accept:".dragNewModul",
        drop: function( event, ui ) {
            var setHtml = ui.draggable.html();
            var setId   = ui.draggable.attr("id");

            found = 0;
            
            if (setId.substr(0,15) == "newcmsDragModul") {
                found = "newModulDrag";
                var setClass  = ui.draggable.attr("class");
                
                ui.draggable.next().remove();
                ui.draggable.remove();
                
                
                
                // alert( "Drage new Modul next");
//                
//                
//                var setClass  = ui.draggable.attr("class");
//                
//                var contentId = setId.substr(12);
//                // remove OLD Class
//                $("#"+setId).remove();
//                $("#spacerId_"+contentId).remove();
//                // alert ("REMOVE #"+setId+ "ContentID = "+contentId);
//                
//                
//                var addSpacer = "<div id='spacerId_"+contentId+"' class='spacer spacerContentType spacerContentType_new spacerDrop ui-droppable'>&nbsp;</div>";
//                $(addSpacer).insertAfter(this); //.droppable('activeClass:"spacerDrop_active",hoverClass:"spacerDrop_hover"');
//                

                var addSpacer = "<div class='spacer spacerContentType spacerContentType_new spacerDrop ui-droppable'>&nbsp;</div>";
                $(addSpacer).insertAfter(this); //.droppable('activeClass:"spacerDrop_active",hoverClass:"spacerDrop_hover"');
                
                

                var addModul = "";
                addModul += "<div class='"+setClass+"' ";
                
                addModul += " id='"+setId+"'>";
                addModul += setHtml;
                addModul += "</div>";
               
                // Add Spacer after Modul
                $(addModul).insertAfter(this);
                
                
                
             
                
                liveDropAble();
            }
            
            // DRAG NEW MODUL
            if (setId.substr(0,12) == "cmsDragModul") {
                found = "newModul";
                // alert ("Drag NEW MODUL");
                
                var addSpacer = "<div class='spacer spacerContentType spacerContentType_new spacerDrop ui-droppable'>&nbsp;</div>";
                $(addSpacer).insertAfter(this); //.droppable('activeClass:"spacerDrop_active",hoverClass:"spacerDrop_hover"');
                
                var frame = 0;
                if (setId.substr(0,18) == "cmsDragModul_frame") frame =1;
                
                
                
                var addModul = "";
                addModul += "<div class='dragFrameAdded ";
                // if (frame) addModul += "dragFrame";
                // else addModul += "dragBox";
                
                addModul += "dragBox";
                addModul += "' id='new"+setId+"'>";
                addModul += setHtml;
                //addModul += "<br />"+ setId.substr(0,18);
                addModul += "</div>";
                if (frame) {
                    
                    var frameCount = setId.substr(18);
                    var width = $(this).width();
                    var frameAbs = 10;
                    var frameWidth = (width - ((frameCount-1)*frameAbs)) / frameCount; 
                    
                    
                    addModul += "<div class='cmsContentFrame_Dummy cmsEditToggle' style='width:"+width+"px;'>";
                    
                    for (i=1;i<=frameCount;i++) {
                        if (i==frameCount) frameAbs = 0;
                        addModul += "<div class='cmsContentFrame_DummyFrame cmsContentFrame_DummyFrame_"+i+"' style='margin-right:"+frameAbs+"px;width:"+(frameWidth-4)+"px;'>";
                        addModul += "Spalte "+i;
                        addModul += "</div>";
                    }
//                    addModul += "<div style='clear:both;'></div>";
                    addModul += "</div>";
                    
                    
                    var frameAbs = 10;
                    
                    
                    addModul  += "<div class='addNewModul' >";
                   
                    
                    
                    // addModul += "ANZAHL RAHMEN :"+frameCount+" Breite:"+width+" frameWidth:"+frameWidth+"<br >";
                    
                    for (i=1;i<=frameCount;i++) {
                        if (i==frameCount) frameAbs = 0;
                        
                        addModul += "<div style='float:left;margin-right:"+frameAbs+"px;width:"+frameWidth+"px;' class='dragFrame' id='newFrame_"+i+"' >";
                        // addModul += "Rahmen "+i+"<br />";
                        addModul += "<div class='spacer spacerContentType spacerContentType_new spacerDrop ui-droppable'>&nbsp;</div>";
                        addModul += "</div>";                                                
                    }
                    
                    addModul += "<div style='clear:both;'></div>";                    
                    addModul += "</div>";                    
                }

                $(addModul).insertAfter(this);
                $(".dragFrameAdded").children(".cmsModulSmallImage").css("display","inline-block");
                // Add Spacer after Modul
                
                liveDropAble();
                
            } 
            
            // DRAG EXISTING MODUL
            if (setId.substr(0,12) == "dragContent_") {
                found = "oldModul";
                var setClass  = ui.draggable.attr("class");
                
                var contentId = setId.substr(12);
                // remove OLD Class
                $("#"+setId).remove();
                $("#spacerId_"+contentId).remove();
                // alert ("REMOVE #"+setId+ "ContentID = "+contentId);
                
                
                var addSpacer = "<div id='spacerId_"+contentId+"' class='spacer spacerContentType spacerContentType_new spacerDrop ui-droppable'>&nbsp;</div>";
                $(addSpacer).insertAfter(this); //.droppable('activeClass:"spacerDrop_active",hoverClass:"spacerDrop_hover"');
                
                var addModul = "";
                addModul += "<div class='"+setClass+"' ";
                
                addModul += " id='"+setId+"'>";
                addModul += setHtml;
                addModul += "</div>";
               
                // Add Spacer after Modul
                $(addModul).insertAfter(this);
                
                liveDropAble();
            }    
            
            if (found == 0) {
                alert("NOT Found "+found+" / "+setId);
            }
            
            var sortorder="";
            var newFrameNr = 0;
            var newFrame = 0;
            var out = "";
            var hidden = 1;
            $('.dragFrame').each(function(){
                var idStr = $(this).attr("id");
                var classStr = $(this).attr("class");
                var newframe = 0;
                if (idStr.substr(0,8) == "newFrame") newframe =1 ;
                
                if (newframe) {
                    idStr = "inFrame_"+newFrameNr+"_"+idStr;
                  
                    
                } else {
                    //idStr += "_##"+idStr.substr(0,8)
                }
                
                sortorder += "id="+idStr+" \n";  
                var subOut = "";
                $(this).children(".dragBox").each(function(){
                    var subIdStr = $(this).attr("id");
                    newFrame = 0;
                    if (subIdStr.substr(0,18) == "cmsDragModul_frame") newFrame =1 ;
                    
                    if (newFrame) {
                        newFrameNr = newFrameNr + 1; // ++;
                        subIdStr += "_inFrame_"+newFrameNr;
                    }
                    
                    if (subOut != "") subOut += ",";
                    subOut += subIdStr;
                    sortorder += " - - sub id="+idStr+" \n";  
                })
                
                if (hidden) out += "<input type='hidden' style='width:90%' name='layoutData["+idStr+"]' value='"+subOut+"' />";
                else out += idStr + " : " + "<br /><input type='text' style='width:90%' name='layoutData["+idStr+"]' value='"+subOut+"' /><br />\n";
                
                
                
            })
            //  alert (out);
            
            if (out) {
                var outPut = "";
                outPut += "<form method='post' >";
                if (!hidden) outPut += "Sortierung speichern<br>";
                outPut += out;
                outPut += "<input type='submit' class='cmsInputButton' name='saveLayout' value='Layout speichern' />"
                outPut += "<input type='submit' class='cmsInputButton cmsSecond' name='cancelSaveLayout' value='Layout verwerfen' />"
                               
                outPut += "</form>";
                
                // Show Add Page Start
                $(".cmsContentStart").removeClass("cmsContentStart_hidden");
                $(".cmsContentStart").html(outPut);
                
                // Show Add Page End
                //$(".cmsContentEnd").removeClass("cmsContentEnd_hidden");
                // $(".cmsContentEnd").html(outPut);
                
               
            }
            
            
            
            
        }
     });
}



$(".cmsAddUser").click(function(){
    userId = $(".cmsSelectUser").val();
    toList = $(this).parent().attr("class");
    actInhalt = $("."+toList+"_text").val();
    add = 1;
    actList = actInhalt.split("|");
    for (var i=1;i<actList.length-1;i++) { //
        if (actList[i] == userId) {
            add = 0;
        }
    }

    if (userId && add) {
        if (actInhalt.length == 0) actInhalt += "|";
        actInhalt += userId+"|"

        $("."+toList+"_text").val(actInhalt);


        userName = $(".cmsSelectUser").find("[value='"+userId+"']").text();
        $(this).parent().children(".cmsUserList").append($('<option>', {
            value: userId,
            text : userName
        }));
    }
})

function getEditContent(callUrl,id) {
    var jqxhr = $.get(callUrl, function(data) {
        // alert("success"+data);
        return data;
    })
    
    .done(function(data) { 
        //alert ("get Content for id"+id+"\n"+data)
        $("#editContent_"+id).html(data);
        $("#editContent_"+id).removeClass("cmsContentEditFrameContent_hidden");
        $("#editContent_"+id).parent().removeClass("cmsEditBox");
//        ("cmsContentEditFrameContent_hidden");
//        $(".cmsContentEditFrameContent").html(data);
//        $(".cmsContentEditFrameContent").removeClass("cmsContentEditFrameContent_hidden");
//        
        return data;
    })
    return 0;
}

function setEditCountent(data,id) {
    alert ("set Content for id"+id+"\n"+data);
    $("#editContent_"+id).html(data);
    $("#editContent_"+id).removeClass("cmsContentEditFrameContent_hidden");
}

$(".cmsContentFrame_editJavaButton").live("click",function(){
    idStr = $(this).attr("id");
    id = idStr.substr(13);
    //alert("open "+id);

    out = "Hier Inhalt edit von <br>Content Id ="+id;

    callUrl = "/cms_"+cmsVersion+"/getData/content.php?view=editContent&id="+id;
    callUrl += "&cmsVersion="+cmsVersion+"&cmsName="+cmsName;

    width = $(this).parent().children(".cmsContentEditFrameContent").css("width");
    if (width) callUrl += "&frameWidth="+width;

    // target = $(this).parent().children(".cmsContentEditFrameContent");
    out += "<br>url= "+callUrl;
//    $(this).parent().children(".cmsContentEditFrameContent").html(out);
//    $(this).parent().children(".cmsContentEditFrameContent").removeClass("cmsContentEditFrameContent_hidden");
//    $(this).parent().removeClass("cmsEditBox");
    
    
    data = getEditContent(callUrl,id);
//    
//
//    done = 0;
//    $.get(callUrl,function(data){
//        // alert ("get")
//    })
//    .done(function(data) { 
//        //alert ("done");
//        out = data;
//        done = 1;
//        
//    })
//    while (done == 0) {
//        
//    }
//    $(this).parent().children(".cmsContentEditFrameContent").html(out);
//    $(this).parent().children(".cmsContentEditFrameContent").removeClass("cmsContentEditFrameContent_hidden");
//    $(this).parent().removeClass("cmsEditBox");
    
    // alert ("end of Script");
    
    // text = getUrlText(callUrl);
  //  out = "<br>Inhalt erhalten<br>"+text;
    // alert(out);
//    
//    
//    if (text) {
        
//        //alert ("Inhalt ist "+out);
//        $(this).parent().children(".cmsContentEditFrameContent").html(out);
//        $(this).parent().children(".cmsContentEditFrameContent").removeClass("cmsContentEditFrameContent_hidden");
//        $(this).parent().removeClass("cmsEditBox");
    //}

     

//    $(this).parent().children(".cmsContentEditFrameContent").html(out);
//    $(this).parent().children(".cmsContentEditFrameContent").removeClass("cmsContentEditFrameContent_hidden");
//    $(this).parent().removeClass("cmsEditBox");


})

$(".cmsRemoveUser").click(function(){
    userId = $(this).parent().children(".cmsUserList").val();
    toList = $(this).parent().attr("class");

    actInhalt = $("."+toList+"_text").val();

    if (userId) {
        inList = 0;
        actList = actInhalt.split("|");
        newList = "|";
        for (var i=1;i<actList.length-1;i++) { //
            if (actList[i] == userId) {
                // alert("Is in List");
                inList = 1;
            } else {
                //if (newList.length>0) newList += ",";
                newList += actList[i]+"|";
            }
        }
        if (inList) {
            if (newList.length == 1) newList = "";
           $("."+toList+"_text").val(newList);
           $(this).parent().children(".cmsUserList").find("[value='"+userId+"']").remove();
        }      
    }
})


$(".editWireframe").live("change",function(){
    isOn = $(this).attr("checked");
    if (isOn) {
        //$(".editWireframe_option").attr("disabled",false);
        $(".editWireframe_option").attr("readOnly",false);
        //$(".editWireframe_option").attr("checked","checked");
    } else {
         //$(".editWireframe_option").attr("disabled","disabled");
        $(".editWireframe_option").attr("readOnly","readOnly");
        // $(".editWireframe_option").attr("checked",false);
    }
    // alert ("Wireframe is "+isOn);
})


$(".demo").mouseenter(function() {
    $(this).css("cursor","pointer");

    // alert("Click Here");

    cmsName    = $(".LayoutFrame").attr("cmsName");
    cmsVersion = $(".LayoutFrame").attr("cmsVersion");
        // +"&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl = "/search.php"; // cms_"+cmsVersion+"/cms_sitemap_edit.php?pageId="+siteMapId+"&edit=deletePage&cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl = "/cms_base/getData/category.php";
       // $(".siteMapAdd_"+siteMapId).css("margin-left","0px");
    $.get(callUrl,function(text){
        $(".cmsEditLocation_info").attr("value",text);
            //$(".siteMapAdd_"+siteMapId).html(text);
            //$(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
    });


    
   
})


$(".cmsModulEditState").live("click",function(){
    editState = $(this).hasClass("cmsEditOn");

    url = "/cms_"+cmsVersion+"/getData/setSession.php";


    if (editState) {
        $(this).removeClass("cmsEditOn");
        $(this).addClass("cmsEditOff");
        
        $(this).children(".cmsEditStateImage").attr("src","/cms_base/cmsImages/cmsEditOff.png");
        $(this).parent().removeClass("cmsSiteBarBox_active");
        $(".cmsEditToggle").addClass("cmsEditHidden");
        $(".spacerEdit").removeClass("spacerDrop");
        url += "?edit=0";
        
    } else {
        $(this).addClass("cmsEditOn");
        $(this).removeClass("cmsEditOff");
        
        $(this).children(".cmsEditStateImage").attr("src","/cms_base/cmsImages/cmsEditOn.png");
        $(this).parent().addClass("cmsSiteBarBox_active");
        $(".cmsEditToggle").removeClass("cmsEditHidden");
        $(".spacerEdit").addClass("spacerDrop");
        url += "?edit=1";
         liveDropAble();
    }
    $.get(url,function(res){
        // alert(res);
    })
    // alert(url);
    
})


$(".cmsSelectEditMode").live("click",function(){
    newMode = $(this).attr("id");
    newMode = newMode.substr(12);
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";

   
    if (newMode) {
        $("#setEditMode_Simple").removeClass("cmsSelectEditModeSelected");
        $("#setEditMode_More").removeClass("cmsSelectEditModeSelected");
        $("#setEditMode_Admin").removeClass("cmsSelectEditModeSelected");
        
        $("#setEditMode_"+newMode).addClass("cmsSelectEditModeSelected");

        if (newMode == "Simple") {
            $(".editMode_More").addClass("editMode_hidden");
            $(".editMode_Admin").addClass("editMode_hidden")
        }

        if (newMode == "More") {
            $(".editMode_More").removeClass("editMode_hidden");
            $(".editMode_Admin").addClass("editMode_hidden")
        }


        if (newMode == "Admin") {
            $(".editMode_More").removeClass("editMode_hidden");
            $(".editMode_Admin").removeClass("editMode_hidden")
        }

        
        url += "?editMode="+newMode;
        $.get(url,function(res){
        })
    }
    
})

$(".cmsSelectTarget").live("click",function(){
    setTarget = $(this).attr("id");
    setTarget = setTarget.substr(10);
    if ($(this).hasClass("cmsSelectTargetSelected")) {
        // alert("Target "+setTarget+"is allready active");
        return 0;
    }
    
    $(".cmsSelectTarget").removeClass("cmsSelectTargetSelected");
    $("#setTarget_"+setTarget).addClass("cmsSelectTargetSelected");
    // cmsDirectionFrame cmsDirection_Pc cmsDirection_Mobil
    if (setTarget == "Mobil") {
        $(".cmsDirectionFrame").removeClass("cmsDirection_Pc");
        $(".cmsDirectionFrame").addClass("cmsDirection_Mobil");
    } else {
        $(".cmsDirectionFrame").removeClass("cmsDirection_Mobil");
        $(".cmsDirectionFrame").addClass("cmsDirection_Pc");
    }
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?target_target="+setTarget;
    
    $.get(url,function(res){
        // window.location.reload();
    })
    
    
    // alert("setTarget to "+setTarget);
})

$(".cmsSelectDirection").live("click",function(){
    setDirection = $(this).attr("id");
    setDirection = setDirection.substr(13);
   
//    
    if ($(this).hasClass("cmsSelectDirectionSelected")) {
        // alert("Target "+setTarget+"is allready active");
        return 0;
    } 
    
    $(".cmsSelectDirection").removeClass("cmsSelectDirectionSelected");
    $("#setDirection_"+setDirection).addClass("cmsSelectDirectionSelected");
    // cmsDirectionFrame cmsDirection_Pc cmsDirection_Mobil
   
    if (setDirection == "Landscape") {
        setWidth = 480;
        setHeight = 320;
    } else {
        setHeight = 480;
        setWidth = 320;
    }
   
   
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?target_orientation="+setDirection;
    url += "&target_width="+setWidth;
    url += "&target_height="+setHeight;
    
    $.get(url,function(res){
        
        
        setFrameSize(setWidth,setHeight);
        
         // window.location.reload();
    })
    
    
    // alert("setTarget to "+setTarget);
})


$(".cmsModulUserEditMode").live("click",function(){
    newMode = 0;
    actMode = 0;
    url = "/cms_"+cmsVersion+"/getData/setSession.php";

    if ($(this).hasClass("cmsEditMode_Simple")) {
        actMode = "Simple"; newMode="More";
    }
    if ($(this).hasClass("cmsEditMode_More")) {
        actMode = "More"; newMode="Admin";
    }
    if ($(this).hasClass("cmsEditMode_Admin")) {
        actMode = "Admin"; newMode="Simple";
    }
    if (newMode) {
        $(this).removeClass("cmsEditMode_"+actMode);
        $(this).addClass("cmsEditMode_"+newMode);


        $(this).children(".cmsEditModeImage").attr("src","/cms_base/cmsImages/cmsUser"+newMode+".png");


        if (newMode == "Simple") {
            $(".editMode_More").addClass("editMode_hidden");
            $(".editMode_Admin").addClass("editMode_hidden")
        }

        if (newMode == "More") {
            $(".editMode_More").removeClass("editMode_hidden");
            $(".editMode_Admin").addClass("editMode_hidden")
        }


        if (newMode == "Admin") {
            $(".editMode_More").removeClass("editMode_hidden");
            $(".editMode_Admin").removeClass("editMode_hidden")
        }



        url += "?editMode="+newMode;
        $.get(url,function(res){
        })
    }
})

$("#setUserShowLevel").live("change",function(){
    alert(cmsVersion);
    val = $(this).val();
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?showLevel="+val+"&pageList=0";
    $.get(url,function(res){
        window.location.reload();
    })
    
    
   // alert ("SET SHOW LEVEL TO "+val);
    
})

$(".cmsModulAdd").live("click",function(){
    cmsModul_toggleModul();

})


function cmsModul_showImage(folder) {
    $(".cmsModulImageFrame").removeClass("cmsModul_hidden");
  
    $(".cmsModulContentFrame").addClass("cmsModul_hidden");
    $(".cmsModulColorFrame").addClass("cmsModul_hidden");
    $(".cmsModulSettingsFrame").addClass("cmsModul_hidden");
    $(".cmsModulSitemapFrame").addClass("cmsModul_hidden");

    $(".cmsModulImageScroll").html("");
    $(".cmsModulImageScroll").addClass("cmsLoading");
    
    $(".cmsImageAdd").addClass("cmsModulSelect");
    $(".cmsModulAdd").removeClass("cmsModulSelect");
    $(".cmsColorAdd").removeClass("cmsModulSelect");
    $(".cmsModulSettings").removeClass("cmsModulSelect");
    $(".cmsModulSitemap").removeClass("cmsModulSelect");
    
    callUrl = "/cms_"+cmsVersion+"/cms_imageSelect_get.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    callUrl = "/cms_"+cmsVersion+"/getData/imageSelect.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    
    if (folder) {
        callUrl += "&folder="+folder;  
        $(".cmsModulFolder").text(folder);
    }

    $.get(callUrl,function(text){
        //$(".cmsImageScroll").html(text);
        $(".cmsModulImageScroll").removeClass("cmsLoading");
        $(".cmsModulImageScroll").html(text);

    });
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?showModul=image";
    $.get(url,function(res){})
}


function cmsModul_showColor(folder) {
    $(".cmsModulImageFrame").addClass("cmsModul_hidden");
    $(".cmsModulContentFrame").addClass("cmsModul_hidden");
    $(".cmsModulSettingsFrame").addClass("cmsModul_hidden");
    $(".cmsModulSitemapFrame").addClass("cmsModul_hidden");

    $(".cmsModulColorFrame").removeClass("cmsModul_hidden");
    
    $(".cmsImageAdd").removeClass("cmsModulSelect");
    $(".cmsModulAdd").removeClass("cmsModulSelect");
    $(".cmsColorAdd").addClass("cmsModulSelect");
    $(".cmsModulSettings").removeClass("cmsModulSelect");
    $(".cmsModulSitemap").removeClass("cmsModulSelect");
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?showModul=color";
    $.get(url,function(res){})

}


function cmsModul_showSettings() {
    $(".cmsModulImageFrame").addClass("cmsModul_hidden");
    $(".cmsModulContentFrame").addClass("cmsModul_hidden");
    $(".cmsModulColorFrame").addClass("cmsModul_hidden");
    
    $(".cmsModulSitemapFrame").addClass("cmsModul_hidden");
    
    
    $(".cmsModulSettingsFrame").removeClass("cmsModul_hidden");
    
    $(".cmsImageAdd").removeClass("cmsModulSelect");
    $(".cmsModulAdd").removeClass("cmsModulSelect");
    $(".cmsColorAdd").removeClass("cmsModulSelect");
    $(".cmsModulSettings").addClass("cmsModulSelect");
    $(".cmsModulSitemap").removeClass("cmsModulSelect");
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?showModul=settings";
    $.get(url,function(res){})
    
}

function cmsModul_showSitemap() {
    $(".cmsModulImageFrame").addClass("cmsModul_hidden");
    $(".cmsModulContentFrame").addClass("cmsModul_hidden");
    $(".cmsModulColorFrame").addClass("cmsModul_hidden");
    $(".cmsModulSitemapFrame").removeClass("cmsModul_hidden");
    $(".cmsModulSettingsFrame").addClass("cmsModul_hidden");
    
    $(".cmsImageAdd").removeClass("cmsModulSelect");
    $(".cmsModulAdd").removeClass("cmsModulSelect");
    $(".cmsColorAdd").removeClass("cmsModulSelect");
    $(".cmsModulSettings").removeClass("cmsModulSelect");
    $(".cmsModulSitemap").addClass("cmsModulSelect");
    
    
    posision = $(".cmsModulContentFrame").offset();
    oben = posision.top;
    oben = 90;

    winHeght = $(window).height();

    actHeight = $(".cmsModulContentFrame").height();


    setHeight = winHeght-oben-60;
    if (setHeight < actHeight) {
        $(".cmsModulSitemapFrame").height(setHeight);
        $(".cmsModulSitemapScroll").height(setHeight-35);
    } else {
        $(".cmsModulSitemapFrame").height("auto");
        $(".cmsModulSitemapScroll").height("auto");
    }
    
    
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?showModul=sitemap";
    $.get(url,function(res){})
}

$(".cmsSitemap_reset").live("click",function(){
    url = "/cms_"+cmsVersion+"/getData/setSession.php";
    url += "?pageList=0";
    $.get(url,function(res){
        window.location.reload();    
    })
    
})

$(".cmsModulSiteMap_toggleHidden").live("click",function(){
    hidden = $(this).parent().children(".cmsModulSiteMap_list").hasClass("cmsModulSiteMap_listHidden");
    
    if (hidden)  {
         $(this).parent().children(".cmsModulSiteMap_list").removeClass("cmsModulSiteMap_listHidden");
         $(this).html("-");
    } else {
        $(this).parent().children(".cmsModulSiteMap_list").addClass("cmsModulSiteMap_listHidden");
        $(this).html("+");
    }
    
})


$(".cmsEditSelectColor").click(function(){
    // alert("click");
    cmsModul_showColor();
})

$(".cmsContentNoData").click(function(){
    cmsModul_toggleModul();
})

$(window).resize(function(){
    w = $(window).width();
    h = $(window).height();
//    alert ("Jange "+w+" x "+h);
})

function cmsModul_toggleModul() {
    if ($(".cmsModulContentFrame").hasClass("cmsModul_hidden")) {
        $(".cmsModulContentFrame").removeClass("cmsModul_hidden");
        
        $(".cmsModulAdd").addClass("cmsModulSelect");
        $(".cmsImageAdd").removeClass("cmsModulSelect");
        $(".cmsColorAdd").removeClass("cmsModulSelect");
        $(".cmsModulSettings").removeClass("cmsModulSelect");
        $(".cmsModulSitemap").removeClass("cmsModulSelect");
        
        
        posision = $(".cmsModulContentFrame").offset();
        oben = posision.top;
        oben = 90;
        
        winHeght = $(window).height();
        
        actHeight = $(".cmsModulContentFrame").height();
        
        
        setHeight = winHeght-oben-60;
        if (setHeight < actHeight) {
            $(".cmsModulContentFrame").height(setHeight);
            $(".cmsModulList").height(setHeight-30);
        } else {
            $(".cmsModulContentFrame").height("auto");
            $(".cmsModulList").height("auto");
        }
        
        
        // hide Image
        $(".cmsModulImageFrame").addClass("cmsModul_hidden");
        $(".cmsModulColorFrame").addClass("cmsModul_hidden");
        $(".cmsModulSettingsFrame").addClass("cmsModul_hidden");
        $(".cmsModulSitemapFrame").addClass("cmsModul_hidden");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=modul";
        $.get(url,function(res){})

    } else {
        $(".cmsModulContentFrame").addClass("cmsModul_hidden");
        $(".cmsModulAdd").removeClass("cmsModulSelect");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=no";
        $.get(url,function(res){})
    }
}


function cmsModul_showModul() {
    $(".cmsModulContentFrame").removeClass("cmsModul_hidden");
    $(".cmsModulImageFrame").addClass("cmsModul_hidden");    
}




$(".cmsImageAdd").live("click",function(){
    if ($(".cmsModulImageFrame").hasClass("cmsModul_hidden")) {
        cmsModul_showImage();
    } else {
        $(".cmsModulImageFrame").addClass("cmsModul_hidden");    
        $(".cmsImageAdd").removeClass("cmsModulSelect");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=";
        $.get(url,function(res){})
    }
})


$(".cmsColorAdd").live("click",function(){
    if ($(".cmsModulColorFrame").hasClass("cmsModul_hidden")) {
        cmsModul_showColor();


    } else {
        $(".cmsModulColorFrame").addClass("cmsModul_hidden");     
        $(".cmsColorAdd").removeClass("cmsModulSelect");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=";
        $.get(url,function(res){})
    }
})

$(".cmsModulSettings").live("click",function(){
    if ($(".cmsModulSettingsFrame").hasClass("cmsModul_hidden")) {
        cmsModul_showSettings();
    } else {
        $(".cmsModulSettingsFrame").addClass("cmsModul_hidden");     
        $(".cmsModulSettings").removeClass("cmsModulSelect");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=";
        $.get(url,function(res){})
    }
})

$(".cmsModulSitemap").live("click",function(){
    if ($(".cmsModulSitemapFrame").hasClass("cmsModul_hidden")) {
        cmsModul_showSitemap();
    } else {
        $(".cmsModulSitemapFrame").addClass("cmsModul_hidden");     
        $(".cmsModulSitemap").removeClass("cmsModulSelect");
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?showModul=";
        $.get(url,function(res){})
    }
})



$(".cmsModulContentCategory").live("click",function(){
    id = $(this).attr("id");
    // alert ("KLICJ id="+id);
     if ($("."+id).hasClass("cmsModulCategoryFrameHidden")) {
        $("."+id).removeClass("cmsModulCategoryFrameHidden");

    } else {
        $("."+id).addClass("cmsModulCategoryFrameHidden");

    }

})


$(".cmsContentFrame_editButton_showFrame").live("click",function(){
    id = $(this).attr("id");
    id = id.substring(13);
    

    hasClass = $(".cmsEditFrame_"+id).hasClass("cmsEditFrame_hidden");

    if (hasClass) {
        // alert ("remove class form cmsEditFrame_"+id);
        $(".cmsEditFrame_"+id).removeClass("cmsEditFrame_hidden");
        $(".cmsContentFrame_"+id).addClass("cmsContentFrame_hidden");
    } else {
        // alert ("add class form cmsEditFrame_"+id);
        $(".cmsEditFrame_"+id).addClass("cmsEditFrame_hidden");
        $(".cmsContentFrame_"+id).removeClass("cmsContentFrame_hidden");
    }

   

   /*  callUrl = "/cms_base/getData/editBox.php?button=editContent&editId="+id;
     $.get(callUrl,function(text){
       //alert (text);
       textlength = text.length;
       if (textlength > 30) {
           $(".cmsEditFrame_Content").html(text);
           return 1;
       }

            //$(".siteMapAdd_"+siteMapId).html(text);
            //$(".siteMapAdd_"+siteMapId).addClass("cmsSitemapAddPageShow");
    });*/
})

$(".cmsContentFrame_deleteButton").live("click",function(){
    id = $(this).attr("id");
    if (id) {
        if (id.substr(0,13) == "deleteContent") {
            hasClass = $("."+id).hasClass("cmsContentFrame_deleteAction_hidden");
            if (hasClass) {
                $("."+id).removeClass("cmsContentFrame_deleteAction_hidden");
            } else {
                $("."+id).addClass("cmsContentFrame_deleteAction_hidden");
            }
        } else {
            alert("Action Delete with id="+id+"--"+id.substr(0,14));
        }




    } else {
        
    }
})


$(".layerDrop").click(function(){
    //cmsModul_showModul();
    cmsModul_toggleModul();
})

/* cmsModulContentCategory {
    cursor:pointer;
    font-size: 12px;
    margin:3px 3px 3px 5px;
    border-bottom:1px solid #666;
    color:#666;
}


.cmsModulCategoryFrame {
    background-color:fuchsia;
}

.cmsModulCategoryFrame .cmsModulCategoryFrameHidden {
*/

$(".cmsContentHead").mouseenter(function(){
    if ($(this).hasClass("cmsContentHeadFrame")) {
        $(this).addClass("cmsContentHeadFrameOver");
        contentId = $(this).attr("contentId");
        $(".cmsContentFrame_"+contentId).addClass("cmsContentHeadFrameOver"); //("background-color","#e8e8e8");
    } else {
        $(this).addClass("cmsContentHeadOver");
        contentId = $(this).attr("contentId");
        $(".cmsContentFrame_"+contentId).addClass("cmsContentFrameOver"); //("background-color","#e8e8e8");
    }

})



$(".cmsContentHead").mouseleave(function(){
    if ($(this).hasClass("cmsContentHeadFrame")) {
        $(this).removeClass("cmsContentHeadFrameOver");
        contentId = $(this).attr("contentId");
        $(".cmsContentFrame_"+contentId).removeClass("cmsContentHeadFrameOver");
    } else {
        $(this).removeClass("cmsContentHeadOver");
        contentId = $(this).attr("contentId");
        $(".cmsContentFrame_"+contentId).removeClass("cmsContentFrameOver");
    }
})



$(".cmsEditTab").live("click",function(){
    
    id = $(this).attr("id");
    if (id) {
        tabName =  id.substr(11);
        
        if (!activeEditTab) {
            activeTab = $(this).parent().children(".cmsEditTab_selected").attr("id");
            activeEditTab = activeTab.substr(11);
            // alert ("ActiveTab Id = "+activeEditTab);
        }
        
        
        
        if (tabName != activeEditTab) {
            // Unselect Tab
            
            if (activeEditTab) {
                $("#cmsEditTab_"+activeEditTab).removeClass("cmsEditTab_selected");
                $("#cmsEditTabFrame_"+activeEditTab).addClass("cmsEditFrameHidden");
            } 
            
           
           // Select Tab
            $("#cmsEditTab_"+tabName).addClass("cmsEditTab_selected");
            $("#cmsEditTabFrame_"+tabName).removeClass("cmsEditFrameHidden")
            activeEditTab = tabName;
            
            $(this).parent().children(".cmsEditTabName").val(tabName);
        }
        
        
        return 0;
    }
    
    
    
    
    paramStr = $(this).children(".hiddenData").text();
    param = getParamList(paramStr);
    tabName = param["editName"];
   //  alert ("tabName = "+tabName);
    if (activeEditTab == "") {
        paramStr = $(".cmsEditTabLine").children(".hiddenData").text();
        param = getParamList(paramStr);
        activeEditTab = param["selectTab"];        
    }

    if (tabName != activeEditTab) {
        // remove Active Tab Selected and show
        modName = activeEditTab.substr(0,8);
        if (modName == "content_" ) {
            $(".cmsEditTab_"+activeEditTab).removeClass("cmsEditTabModification_selected");
            //  $(".cmsEditFrame_"+tabName).removeClass("cmsEditFrameModification");
        } else {
            $(".cmsEditTab_"+activeEditTab).removeClass("cmsEditTab_selected");
        }
        
        $(".cmsEditFrame_"+activeEditTab).addClass("cmsEditFrameHidden");



        $(".cmsEditTabName").val(tabName);

        activeEditTab = tabName;
        modName = tabName.substr(0,8);
        
       //  $(".cmsEditTabLine").children(".hiddenTab").text("selectTab:"+tabName);

        if (modName == "content_" ) {
            $(this).addClass("cmsEditTabModification_selected");
            $(".cmsEditFrame_"+tabName).addClass("cmsEditFrameModification");
        } else {
            $(this).addClass("cmsEditTab_selected");
            
        }
        
        $(".cmsEditFrame_"+tabName).removeClass("cmsEditFrameHidden");

   }
   // alert("click "+tabName);
   

})


var selectSelectButton = 0;


$(".cmsEditSelectButton").mouseenter(function(){
    $(this).css("cursor","pointer");
})

$(".cmsEditSelectButton").mouseleave(function(){
    $(this).css("cursor","default");
})

$(".cmsEditSelectButton").click(function(){
    $(".cmsEditSelectButton").removeClass("selectButtonSelected");
    $(this).addClass("selectButtonSelected");
    
    paramStr = $(this).children(".hiddenData").text();
    param = getParamList(paramStr);
    // alert (paramStr);
    
    valueName = param["valueName"];
    value =  param["value"];
   // alert ("valueName "+valueName+" value="+value);
    $("."+valueName).val(value);
})



$(".toggleTextArea").click(function(){
    id = $(this).attr("id");
    // alert ("Klick "+id);

    if ($(".id_"+id).hasClass("hiddenTextArea")) {
        $(".id_"+id).removeClass("hiddenTextArea");
        $(this).text("Eingabe Ausblenden");
        $(this).addClass("cmsSecond");
    } else {
        $(".id_"+id).addClass("hiddenTextArea");
        $(this).text("Eingabe Einblenden");
        $(this).removeClass("cmsSecond");
    }

    
    
})

$(".cmsToggleItem").live("click",function(){
    paramStr = $(this).children(".hiddenData").text();
    param = getParamList(paramStr);
    toggleName = param["toggleName"];// $(this).attr("toggleName");
    toggleId   = param["toggleId"]; //$(this).attr("toggleId");
    toggleClass = param["toggleClass"]; //$(this).attr("toggleClass");

    // alert("hier");
    paramStrClass = $("."+toggleClass).children(".hiddenData").text();
    paramClass = getParamList(paramStrClass);

    toggleMode = paramClass["toggleMode"]; //$("."+toggleClass).attr("toggleMode");
    // alert(toggleClass+"/"+toggleMode)
    if (toggleMode == "single") {
        actSelected = $("#"+toggleClass).attr("value");
        isSelected = $(this).hasClass("cmsToggleSelected");
        if (isSelected == 1) {
           if (actSelected == toggleId) {
                //alert ("is selected and Activ "+toggleName+"/"+toggleId+" act="+actSelected);
           } else {

           }
           
        } else {
            if (actSelected == toggleId) {
                // alert ("is NOT selected and NOT Activ "+toggleName+"/"+toggleId+" act="+actSelected);
            } else {
               //  alert ("Toggle = ")
                // remove Old Selected
                $("."+toggleClass+"_"+actSelected).removeClass("cmsToggleSelected");

                // set SelectedClass
                $("."+toggleClass+"_"+toggleId).addClass("cmsToggleSelected");

                // setValue to input
                $("#"+toggleClass).attr("value",toggleId);

                isMainCategory = toggleClass.indexOf("_Category");
                if (isMainCategory>0) {
                    mainName =  toggleClass.substr(0,isMainCategory);
                    // alert("MainNAme of="+isMainCategory+" name="+mainName);
                    subCatExist = $("."+mainName+"_subCategory").html();
                    if (subCatExist) {
                        paramStr = $("."+mainName+"_subCategory").children(".hiddenData").text();
                        // alert (paramStr);
                        param = getParamList(paramStr);
                        activMainCat = param["mainCat"];
                        count = param["count"];
                        dataName = param["dataName"]
                        url = param["url"];
                        if (!url) {
                            url = "/cms_"+cmsVersion+"/getData/category.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion+"&type=toggle&mode=simple";
                            // alert(url);
                        }
                        // alert(url);
                        //  url = $("."+mainName+"_subCategory").attr("url");
                        url += "&mainCat="+toggleId;
                        url += "&width="+$("."+mainName+"_subCategory").width();
                        url += "&class="+mainName+"_subCategory";
                        url += "&count="+count;
                       // url += "&dataName="+dataName;


                        // alert(url);


                        /*activMainCat = $("."+mainName+"_subCategory").attr("mainCat");
                        count =
                        url = $("."+mainName+"_subCategory").attr("url");
                        url += "&mainCat="+toggleId;
                        url += "&width="+$("."+mainName+"_subCategory").width();
                        url += "&class="+mainName+"_subCategory";
                        url += "&count="+$("."+mainName+"_subCategory").attr("count");
                        url += "&dataName="+$("."+mainName+"_subCategory").attr("dataName");*/
                       //  url += "&dataName="+$("."+mainName+"_subCategory").attr("dataName");
                        if (url) {
                            $.get(url,function(text){
                                param["mainCat"] = toggleId;
                                paramStr = getParamClass(param);
                                
                                textlength = text.length;
                                // alert (text+"\n\r"+textlength);
                                
                                if (textlength> 30) {
                                   
                                    
                                    $("."+mainName+"_subCategory").html(paramStr+text); //+"\n"+url);
                                    $("#"+mainName+"_subCategory").val("");
                                } else {
                                    // text = "<b>Keine Unter-Rubrik vorhanden  "+textlength+" </b>";
                                    $("."+mainName+"_subCategory").html(paramStr+text); //+"\n"+url);
                                    $("#"+mainName+"_subCategory").val("-");
                                }
                            })
                        }
                        if (activMainCat != toggleId) {
                            // $("."+mainName+"_subCategory").attr("mainCat",toggleId);
                            
                        }
                    } else {
                        // alert("NO SubCategory exist");
                    }
                }

            }
        }
    }

    if (toggleMode == "multi") {
        isSelected = $(this).hasClass("cmsToggleSelected");
        removeId = "";
        addId = "";
        if (isSelected == 1) {
            // alert("Selected");
            $(this).removeClass("cmsToggleSelected");
            removeId = toggleId;
        } else {
            // alert("NOT Selected");
            $(this).addClass("cmsToggleSelected");
            addId = toggleId;
        }

        actSelected = $("#"+toggleClass).attr("value");
        selectedList = actSelected.split("|");
        
        out = "|";
        for (var i=0;i<selectedList.length;i++) { //
            id = selectedList[i];
            // alert ("id ='"+id+"' addId="+addId+" removeId="+removeId);
            if (id.length>0) {
                if (id == removeId) {
                    // alert ("removeId from list "+id+"/"+removeId);
                } else {
                    out += id+"|";
                }
            }
        }
        if (addId) {
            out += addId+"|";
        }
        if (out.length <= 1) out = "";
       
        $("#"+toggleClass).attr("value",out);


    }

     // alert ("Name = "+toggleName+" ID = "+toggleId + " Class= " + toggleClass + " MODE = "+toggleMode);

})

$(".cmsInputLineTip").mouseenter(function(e){
    xOffSet = 30;
    yOffSet = -10;
    id = $(this).attr("id");
    $(this).children(".cmsInputLineTipBox").css("display","block");
    $(this).children(".cmsInputLineTipBox").css("top",yOffSet + "px");
    $(this).children(".cmsInputLineTipBox").css("left",xOffSet + "px");
    
})
$(".cmsInputLineTip").mouseleave(function(){
    id = $(this).attr("id");
    $(this).children(".cmsInputLineTipBox").css("display","none");
    
    
    // alert (id);
    
    //$(this).css("cursor","pointer");    
})

$(".cmsInput_languageSelect_edit").click(function(){
    
})

$(".cmsInput_selectLanguage").click(function(){
    editMode = 0;
    if ($(this).parent().hasClass("cmsInput_languageSelect_Edit")) editMode = "Edit";
    if ($(this).parent().hasClass("cmsInput_languageSelect_Show")) editMode = "Show";
    
    // toogle
    if ($(this).hasClass("cmsInput_selectLanguage_selected")) {
        $(this).removeClass("cmsInput_selectLanguage_selected");

    } else {
        $(this).addClass("cmsInput_selectLanguage_selected");       
    }
    
    
    
    if (editMode) {
        str = "";
        $(".cmsInput_languageSelect_"+editMode).children(".cmsInput_selectLanguage").each(function(){
            lg = $(this).text();
            sel = $(this).hasClass("cmsInput_selectLanguage_selected");
            if (sel) {
                if (str.length>0)str += "|";
                str += lg;
            }
            
            if (editMode == "Edit") {
                if (sel) {
                    $(".cmsInput_editLanguage_"+lg).removeClass("cmsInput_editLanguage_hidden");
                } else {
                    $(".cmsInput_editLanguage_"+lg).addClass("cmsInput_editLanguage_hidden");
                }
            }
            
            if (editMode == "Show") {
                if (sel) {
                    $(".cmsInput_showLanguage_"+lg).removeClass("cmsInput_showLanguage_hidden");
                } else {
                    $(".cmsInput_showLanguage_"+lg).addClass("cmsInput_showLanguage_hidden");
                }
            }
            
        })
        
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?lg"+editMode+"="+str;


        $.get(url,function(res){
            // alert(res);
        })
    // alert(url);
        
        // alert ("Click EditMode "+editMode+" ==> "+str);
        
    }
    
//    lg = $(this).text();
//    
//    cla = $(this).parent().parent().parent().attr("class");
//    // alert (lg+" - "+cla);
//    if ($(this).hasClass("cmsInput_selectLanguage_selected")) {
//        $(this).removeClass("cmsInput_selectLanguage_selected");
//        $(".cmsInput_editLanguage_"+lg).addClass("cmsInput_editLanguage_hidden");
//    } else {
//        $(this).addClass("cmsInput_selectLanguage_selected");
//        $(".cmsInput_editLanguage_"+lg).removeClass("cmsInput_editLanguage_hidden");
//    }
}) 
    


$(".cmsTextPos_select").click(function(){
    $(this).children(".cmsTextPos_selectFrame").css("display","inline-block");
    // alert ("click");
})

$(".cmsTextPos_select").mouseleave(function(){
     $(this).children(".cmsTextPos_selectFrame").css("display","none");
})

$(".cmsTextPos_selectPos").click(function(){
    old = $(this).parent().parent().children(".cmsTextPosInput").val();
    
    myClass = $(this).attr("class");
    classList = myClass.split(" ");
    
    myClass = classList[1];
    myClass = myClass.substr(11);
    
    $(this).parent().children(".cmsTextPos_"+old).removeClass("cmsTextPos_selectPos_selected");
    
    
    $(this).parent().parent().children(".cmsTextPosInput").val(myClass);    
    $(this).addClass("cmsTextPos_selectPos_selected");
    
    $(this).parent().parent().children(".cmsTextPos_selectedImage").attr("src","/cms_"+cmsVersion+"/cmsImages/textImagePos_"+myClass+".png");
    
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

$(".cmsPosSelect").click(function(){
    
    old_h = $(this).parent().children(".cmsPosInput_posH").val();
    old_v = $(this).parent().children(".cmsPosInput_posV").val();
   //  if (old_h AND old_v) {
         $(this).parent().children("#pos_"+old_v+"_"+old_h).removeClass("cmsPosSelected");
    //}
    
    id = $(this).attr("id");
    
    res = id.split("_");
    
    hor = res[2];
    ver = res[1];
    
    $(this).parent().children("#pos_"+ver+"_"+hor).addClass("cmsPosSelected"); 
    
    $(this).parent().children(".cmsPosInput_posH").val(hor);
    $(this).parent().children(".cmsPosInput_posV").val(ver);
    
    // alert ("Hor = "+hor+" Ver = "+ver);
    
})

$(".cmsDataBox_positionSelect").click(function(){
    
    setPos = 0;
    if ($(this).hasClass("cmsDataBox_positionTop")) setPos = "top";
    if ($(this).hasClass("cmsDataBox_positionLeft")) setPos = "left";
    if ($(this).hasClass("cmsDataBox_positionCenter")) setPos = "center";
    if ($(this).hasClass("cmsDataBox_positionRight")) setPos = "right";
    if ($(this).hasClass("cmsDataBox_positionBottom")) setPos = "bottom";
    
    
    if (setPos) {
        
        oldPos = $(this).parent().children(".cmsDataBox_positionInput").val();
        
        if (oldPos != setPos) {
            $(this).parent().children(".cmsDataBox_positionInput").val(setPos);
            
            oldPos = oldPos.charAt(0).toUpperCase() + oldPos.slice(1);
            setPos = setPos.charAt(0).toUpperCase() + setPos.slice(1);
            
            $(this).parent().children(".cmsDataBox_position"+oldPos).removeClass("cmsDataBox_positionSelected");
            $(this).parent().children(".cmsDataBox_position"+setPos).addClass("cmsDataBox_positionSelected");
            
        }
//        alert ("set Pos to = "+setPos+ "oldVal = "+oldPos);
        
    }
    
    // <div style="" class="cmsDataBox_positionSelect cmsDataBox_positionBottom cmsDataBox_positionSelected"></div>
//    old_h = $(this).parent().children(".cmsPosInput_posH").val();
//    old_v = $(this).parent().children(".cmsPosInput_posV").val();
//   //  if (old_h AND old_v) {
//         $(this).parent().children("#pos_"+old_v+"_"+old_h).removeClass("cmsPosSelected");
//    //}
//    
//    id = $(this).attr("id");
//    
//    res = id.split("_");
//    
//    hor = res[2];
//    ver = res[1];
//    
//    $(this).parent().children("#pos_"+ver+"_"+hor).addClass("cmsPosSelected"); 
//    
//    $(this).parent().children(".cmsPosInput_posH").val(hor);
//    $(this).parent().children(".cmsPosInput_posV").val(ver);
    
    // alert ("Hor = "+hor+" Ver = "+ver);
    
})



$(".cmsEditCheckBox").click(function(){
    id = $(this).attr("id");
    useId = id.substr(9);
    if ($(this).hasClass("cmsEditCheckBox_active")) {
        $(this).removeClass("cmsEditCheckBox_active");
        $("#"+useId).val("0");
    } else {
        $(this).addClass("cmsEditCheckBox_active");
        $("#"+useId).val("1");
    }
    
    // alert ("id="+id+" useId="+useId);
    
})

$(".cmsShowCheckBox").change(function(){
    id  = $(this).attr("id");
    val = $(this).attr("checked");
    
    id = id.substr(9); 
    // alert ("id - '"+id+"'");
    if (val) {
        $("#cmsEditType_"+id).removeClass("cmsShowEdit_hidden");
    } else {
        $("#cmsEditType_"+id).addClass("cmsShowEdit_hidden");
    }
})

$(".cmsCropCheckBox").change(function(){
    id  = $(this).attr("id");
    val = $(this).attr("checked");
    
//    id = id.substr(9); 
//    alert ("id - '"+id+"'");
    if (val) {
        $("#cmsEditType_crop").removeClass("cmsShowEdit_hidden");
        $("#cmsEditType_dontCrop").addClass("cmsShowEdit_hidden");
        
    } else {
        $("#cmsEditType_dontCrop").removeClass("cmsShowEdit_hidden");
        $("#cmsEditType_crop").addClass("cmsShowEdit_hidden");
    }
    
    
//    if (val) {
//        $("#cmsEditType_"+id).removeClass("cmsShowEdit_hidden");
//    } else {
//        $("#cmsEditType_"+id).addClass("cmsShowEdit_hidden");
//    }
})



$(document).ready(function() {
    $("#firstFocus").focus();
    liveDropAble();
});
