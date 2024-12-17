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
    
    $(".dragNewModul").draggable({
        revert: "invalid",
        cursor: "move",
        // connectToSortable: ".cmsImageSortList",
        distance: 30,
       //containment: "cmsContentFramePlus",
        zIndex: 10000,
        helper: "clone",
        cursorAt: { top: 10, left: 10}
    });
    
    
    $(".dragBox").draggable({
        revert: "invalid",
        cursor: "move",
        handle: '.dragButton',
        zIndex: 10000,
        helper: function( event ) {
                    return $( "<div class='ui-widget-header'>I'm a custom helper</div>" );
             }, // "<div class='trulla'>HELPER</div>",
        cursorAt: { top: 10, left: 30},
        start: function(event,ui){
            $(this).css("opacity","0.2");
        },        
        stop: function(event,ui){
            $(this).css("opacity","1.0");
        }
    });
    
     
     $(".spacerDrop").droppable({
        activeClass:"spacerDrop_active",
        hoverClass:"spacerDrop_hover",
        drop: function( event, ui ) {
            var setHtml = ui.draggable.html();
            var setId   = ui.draggable.attr("id");


            // DRAG NEW MODUL
            if (setId.substr(0,12) == "cmsDragModul") {
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
                addModul += "' id='"+setId+"'>";
                addModul += setHtml;
                //addModul += "<br />"+ setId.substr(0,18);
                addModul += "</div>";
                if (frame) {
                    addModul  += "<div class='' >";
                    var frameCount = setId.substr(18);
                    var width = $(this).width();
                    var frameAbs = 10;
                    var frameWidth = (width - ((frameCount-1)*frameAbs)) / frameCount; 
                    
                    
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

                // Add Spacer after Modul
                
                liveDropAble();
                
            } 
            
            // DRAG EXISTING MODUL
            if (setId.substr(0,12) == "dragContent_") {
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
            
//            } else {
//                alert ("Unkown ID '"+setId+"' "+setId.substr(0,12));
//            }
            
            var sortorder="";
            var newFrameNr = 0;
            var newFrame = 0;
            var out = "";
            var hidden = 1;
            $('.dragFrame').each(function(){
                var idStr = $(this).attr("id");
                var classStr = $(this).attr("class")
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


$(function(){
//    $(".dragNewModul").draggable({
//        revert: "invalid",
//        cursor: "move",
//        // connectToSortable: ".cmsImageSortList",
//        distance: 30,
//       //containment: "cmsContentFramePlus",
//        zIndex: 10000,
//        helper: "clone",
//        cursorAt: { top: 10, left: 10}
//    });
//    
//    
//    $(".dragBox").draggable({
//        revert: "invalid",
//        cursor: "move",
//        handle: '.dragButton',
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
//    });
    
    
 


   
//
//    $('.dragFrame').sortable({
//		connectWith: '.dragFrame',
//		handle: '.dragButton',
//		cursor: 'move',
//		placeholder: 'dragPlaceholder',
//		forcePlaceholderSize: false,
//		opacity: 0.4,
//        	stop: function(event, ui){
//                    var sortorder='';
//                    $('.dragFrame').each(function(){
//                        var itemorder=$(this).sortable('toArray');
//                        var dragFrameId=$(this).attr('id');
//                        if (dragFrameId.substr(0,11) != "cmsModulCat") {
//                            // sortorder+=dragFrameId+'='+itemorder.toString()+'<br>';
//                            sortorder+=dragFrameId+": <input type='text' name='sortOrder["+dragFrameId+"]' value='"+itemorder+"' /><br>";
////                            for (i=0;i<itemorder.length;i++) {
////                                sortorder+=" - "+itemorder[i]+"<br>";
////                                
////                            }
//                        }                                                
//                    });
//                    
//                    if (sortorder) {
//                        var out = "";
//                        out += "<form method='post' >";
//                        out += "Sortierung speichern<br>";
//                        out += sortorder;
//                        out += "<input type='submit' class='cmsInputButton' name='sortOrderSave' value='Speichern' />"
//                        out += "</form>";
//                        $(".cmsContentEnd").removeClass("cmsContentEnd_hidden");
//                        $(".cmsContentEnd").html(out);
//                        
//                        
//                        
//                    }
//                    
//                    // alert('SortOrder: '+sortorder);
//                    
//                    /*Pass sortorder variable to server using ajax to save state*/
//		}
//	})
//	.disableSelection();
});

   


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


$(".cmsModulAdd").live("click",function(){
    if ($(".cmsModulContentFrame").hasClass("cmsModul_hidden")) {
        $(".cmsModulContentFrame").removeClass("cmsModul_hidden");
        // hide Image
        $(".cmsModulImageFrame").addClass("cmsModul_hidden");

    } else {
        $(".cmsModulContentFrame").addClass("cmsModul_hidden");

    }
})


function cmsModul_showImage(folder) {
    $(".cmsModulImageFrame").removeClass("cmsModul_hidden");
  
    $(".cmsModulContentFrame").addClass("cmsModul_hidden");

    $(".cmsModulImageScroll").html("");
    $(".cmsModulImageScroll").addClass("cmsLoading");
    
    
    callUrl = "/cms_"+cmsVersion+"/cms_imageSelect_get.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
    if (folder) {
        callUrl += "&folder="+folder;
    }

    $.get(callUrl,function(text){
        //$(".cmsImageScroll").html(text);
        $(".cmsModulImageScroll").removeClass("cmsLoading");
        $(".cmsModulImageScroll").html(text);

    });
}



$(".cmsImageAdd").live("click",function(){
    if ($(".cmsModulImageFrame").hasClass("cmsModul_hidden")) {
        cmsModul_showImage();
//        $(".cmsModulImageFrame").removeClass("cmsModul_hidden");
//        // hide Module
//        $(".cmsModulContentFrame").addClass("cmsModul_hidden");
//        
//        $(".cmsModulImageScroll").html("");
//        $(".cmsModulImageScroll").addClass("cmsLoading");
//        
//        
//        callUrl = "/cms_"+cmsVersion+"/cms_imageSelect_get.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
////       // alert("folderName="+callUrl);
////    
////        $(".cmsImageScroll").html("");
////        $("."
//        $.get(callUrl,function(text){
//            //$(".cmsImageScroll").html(text);
//            $(".cmsModulImageScroll").removeClass("cmsLoading");
//            $(".cmsModulImageScroll").html(text);
//            
//        });
        

    } else {
        $(".cmsModulImageFrame").addClass("cmsModul_hidden");       
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
                if (isMainCategory) {
                    mainName =  toggleClass.substr(0,isMainCategory);
                    subCatExist = $("."+mainName+"_subCategory").html();
                    if (subCatExist) {
                        paramStr = $("."+mainName+"_subCategory").children(".hiddenData").text();
                        // alert (paramStr);
                        param = getParamList(paramStr);
                        activMainCat = param["mainCat"];
                        count = param["count"];
                        dataName = param["dataName"]
                        url = param["url"];
                        // alert(url);
                        //  url = $("."+mainName+"_subCategory").attr("url");
                        url += "&mainCat="+toggleId;
                        url += "&width="+$("."+mainName+"_subCategory").width();
                        url += "&class="+mainName+"_subCategory";
                        url += "&count="+count;
                        url += "&dataName="+dataName;


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


$(document).ready(function() {
    $("#firstFocus").focus();
    liveDropAble();
});
