var lastRoll = "";
var timeOutList = {};
var timeOutName = {};
var selectedFlip = 0;


$(".cmsFlipMainFrame_click").click(function(){
    // alert("click");
})

function setActivFlip(flip) {
    alert("setActivFlip ="+flip);
}

$(".cmsFlipFrameSelector ").click(function(){
    title = $(this).attr("title");
    data = title.split("|");
    
    contentId = data[0];
    layerNr = data[1];
    
    
    type = $(this).parent().parent().attr("class");
    type = type.substr(31);


    activ = 1;
    // SELECTOR
    if ($(this).hasClass("cmsFlipFrameSelectorSelected")) {
        $(this).removeClass("cmsFlipFrameSelectorSelected");
        activ = 0;
    } else {
        $(".cmsFlipSelector_"+contentId).removeClass("cmsFlipFrameSelectorSelected");
        $(this).addClass("cmsFlipFrameSelectorSelected");

    }

    
    hideLayer = 1;
    if (type == "slider") {
        // stop animation
        slider = eval("cmsFlipSlider_"+contentId+"_Slider");

        if (activ) {
       
            slider.stopAuto();
            slider.goToSlide(layerNr-1);
        } else {
            slider.startAuto();           
        }

        hideLayer = 0;
    }


    if (hideLayer) {
        // CONTENT
        $(".cmsFlipContent_"+contentId).addClass("cmsFlipContent_hidden");
        $(".cmsFlipContent_"+contentId+"_"+layerNr).removeClass("cmsFlipContent_hidden");
    }
    
    // SET TAB
    if (type == "tab") {
        $(".cmsFlipFrame_tabFrame_"+contentId).children(".cmsFlipFrame_tab").removeClass("cmsFlipFrame_tab_selected");
        $(".cmsFlipFrame_tabFrame_"+contentId).children(".cmsFlipFrame_tab_"+layerNr).addClass("cmsFlipFrame_tab_selected");
    }
    
    // SET ACTIV POST VALUE 
    $("#activeFlip_"+contentId).val(layerNr);
  
})

$(".cmsFlipFrame_tab").click(function(){
    type = $(this).parent().parent().parent().attr("class").substr(31);
    contentId = $(this).parent().attr("class").substr(44);
    layerNr = $(this).attr("class").substr(34);    
        
    // SELECTOR in EDIT MODE    
    if ($("#cmsFlipSelect_"+contentId).attr("class")) {
        $("#cmsFlipSelect_"+contentId).children(".cmsFlipFrameSelector").removeClass("cmsFlipFrameSelectorSelected");
        $("#cmsFlipSelect_"+contentId).children(".cmsFlipSelector_"+contentId+"_"+layerNr).addClass("cmsFlipFrameSelectorSelected");        
        // SET ACTIV POST VALUE 
        $("#activeFlip_"+contentId).val(layerNr);
    }    

    // Select Tab
    $(this).parent().children(".cmsFlipFrame_tab").removeClass("cmsFlipFrame_tab_selected");
    $(this).addClass("cmsFlipFrame_tab_selected");
    
    
     // CONTENT
    $(".cmsFlipContent_"+contentId).addClass("cmsFlipContent_hidden");
    $(".cmsFlipContent_"+contentId+"_"+layerNr).removeClass("cmsFlipContent_hidden");

})


$(".cmsFlipMainFrame_roll").live("mouseenter",function(){
    contentId = $(this).parent().children(".cmsFlipSelectFrame").attr("id").substr(14);
    
    
    if (contentId) {
        inEdit = 0;
        if ($(".cmsFlipSelector_"+contentId+"_1").hasClass("cmsFlipFrameSelectorSelected")) inEdit = 1;
        if ($(".cmsFlipSelector_"+contentId+"_2").hasClass("cmsFlipFrameSelectorSelected")) inEdit = 2;
        
        if (inEdit) {
            return 0;
        }
    }
    
    $(this).children(".cmsFlipRollOut").removeClass("cmsFlipContent_hidden");
    $(this).children(".cmsFlipRollIn").addClass("cmsFlipContent_hidden");
    
    
//    if (contentId) {
//        $("#cmsFlipSelect_"+contentId).children(".cmsFlipSelector_"+contentId+"_2").addClass("cmsFlipFrameSelectorSelected");
//    }     
    
})

$(".cmsFlipMainFrame_roll").live("mouseleave",function(){
    contentId = $(this).parent().children(".cmsFlipSelectFrame").attr("id").substr(14);
    
    
    if (contentId) {
        inEdit = 0;
        if ($(".cmsFlipSelector_"+contentId+"_1").hasClass("cmsFlipFrameSelectorSelected")) inEdit = 1;
        if ($(".cmsFlipSelector_"+contentId+"_2").hasClass("cmsFlipFrameSelectorSelected")) inEdit = 2;
        
        if (inEdit) {           
            return 0;
        }
    }
    
    
    $(this).children(".cmsFlipRollOut").addClass("cmsFlipContent_hidden");
    $(this).children(".cmsFlipRollIn").removeClass("cmsFlipContent_hidden");
    
  
//    if (contentId) {
//        $("#cmsFlipSelect_"+contentId).children(".cmsFlipSelector_"+contentId+"_2").removeClass("cmsFlipFrameSelectorSelected");
//    }  
    
})

$(".cmsFlipClick").click(function(){
    layer = $(this).attr("class");
    layerList = layer.split(" ");
    info = layerList[2].split("_");


    contentId = info[1];
    layerNr = parseInt(info[2]);

    newlayerNr = layerNr+1;
    if ($(".cmsFlipContent_"+contentId+"_"+newlayerNr).attr("class")) {       
    } else {
        newlayerNr = 1;
    }


    $(".cmsFlipContent_"+contentId+"_"+layerNr).addClass("cmsFlipContent_hidden");

    $(".cmsFlipContent_"+contentId+"_"+newlayerNr).removeClass("cmsFlipContent_hidden");

    
    
})


$("cmsFlipSlider_64_Slider").bxSlider({
  
  onAfterSlide: function(){
    // do mind-blowing JS stuff here
    alert('A slide has finished transitioning. Bravo. Click OK to continue!');
  }
});



//$(".cmsFlipRollIn").live("mouseenter",function(){
//    className = $(this).attr("class");
//   //  alert ("over + "+className);
//   
//   //cmsFlipContent cmsFlipContent_64 cmsFlipContent_64_1 cmsFlipRollIn
//    
//   
//    $(this).parent().children(".cmsFlipRollOut").removeClass("cmsFlipContent_hidden");
//    $(this).addClass("cmsFlipContent_hidden");
//})
//
//
//$(".cmsFlipRollOut").live("mouseleave",function(){
//    
//    
//    $(this).parent().children(".cmsFlipRollIn").removeClass("cmsFlipContent_hidden");
//    $(this).addClass("cmsFlipContent_hidden");
//    
//})



$(".cmsFlipFrameContent").mouseenter(function(){
    name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    cl = $(this).attr("class");
    flipId  = $(this).attr("flipId");
    frameWidth   = $(this).attr("frameWidth");

    if (flipType == "click") {
        $(this).css("cursor","pointer");
    }

    if (flipType == "roll") {
        if (lastRoll != name) {
            layerNr = $(this).attr("layerNr");
            if (layerNr == "1") {
                layerNr = 2;
                callUrl = "cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth+"&flipId="+flipId;
                $(this).attr("layerNr","roll");
                // $("."+name).html(callUrl);
               // $("."+name).html("text" + name + " / " +layerNr + "<br>"+callUrl);
                $.get(callUrl,function(text){
                    //$("."+name).html("text" + name + " / " +layerNr + " <br>"+callUrl+"<br>"+text);
                    $("."+name).html(text+"<br>layerNr"+layerNr+" name="+name);
                });
                lastRoll = name;
            }
        }



       
    }

    /*
    if (cl == "cmsFlipFrameContent "+name) {
        if (active == "1") {
            if (flipType == "roll") {
                if (lastRoll != "") {
                    $.get("cms/cms_contentGet.php?name="+lastRoll+"_1",function(text){
                        $("."+lastRoll).html(lastRoll);
                    });
                }

                $.get("cms/cms_contentGet.php?name="+name+"_2",function(text){
                    $("."+name).html(name+"<br>"+text);

                });
                lastRoll = name;
            }
        }
    }
  //help = $(this).html();
  */

})


$(".cmsFlipFrameContent").mouseleave(function(){
    name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    cl = $(this).attr("class");

    if (flipType == "click") {
        $(this).css("cursor","default");
    }

    if (flipType == "roll") {
        layerNr = $(this).attr("layerNr");
        if (lastRoll == name) {
            if (layerNr == "roll") {
                // alert("RollOut "+name);
                layerNr = 1;
                callUrl = "cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth+"&flipId="+flipId;
                // $("."+name).html(callUrl);
                $.get(callUrl,function(text){
                    //$("."+name).html("text" + name + " / " +layerNr + " <br>"+callUrl+"<br>"+text);
                    $("."+name).html(text+"<br>rollOut:"+name);
                });
                $(this).attr("layerNr","1");
                lastRoll = "";
            } else {
                alert (layerNr);
            }
        }
        //if (lastRoll != name) {
            //alert ("roll "+name);
    }

    /*if (cl == "cmsFlipFrameContent "+name) {
        if (active == "1") {
            if (flipType == "roll") {
                $.get("cms/cms_contentGet.php?name="+name+"_1",function(text){
                    $("."+name).html(text);
                });
                lastRoll = "";
            }
        }
    }*/

})


$(".cmsFlipFrameContent").click(function(){
    name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    frameWidth = $(this).attr("frameWidth");
    flipId = $(this).attr("flipId");
    cl = $(this).attr("class");

    if (flipType == "click") {
        layerCount = parseInt($(this).attr("layerCount"));
        layerNr = parseInt($(this).attr("layerNr"));
        //alert ("Klick Frame "+ layerNr + " / "+layerCount);
        layerNr = layerNr + 1;
        if (layerNr > layerCount) layerNr = 1;
        // alert ("Show Frame "+ layerNr + " / "+layerCount);
        callUrl = "cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth+"&flipId="+flipId;
        inh = "class:"+cl+"<br>name="+name+"<br>FlipType:"+flipType+"<br>layerCount="+layerCount+"<br>layerNr="+layerNr;
        $.get(callUrl,function(text){
            // $("."+name).html(callUrl+"<br>"+text+"<br>"+inh);
            $("."+name).html(text);
        });
        $(this).attr("layerNr",layerNr);

        
    }

    /*if (cl == "cmsFlipFrameContent "+name) {
        if (active == "1") {
            if (flipType == "roll") {
                $.get("cms/cms_contentGet.php?name="+name+"_1",function(text){
                    $("."+name).html(text);
                });
                lastRoll = "";
            }
        }
    }*/

})

//
//function startFlipTime() {
//    
//    // var deinTimer = window.setInterval(changeFlipTime("flip_66"), 5000);
//
//
//    // window.setTimeout(changeFlipTime("flip_66"), 5000);
//
//   // var deinTimer = window.setTimeout(changeFlipTime("flip_66"), 5000);
//    //alert("start");
//
//    $(".cmsFlipFrameContent").each(function() {
//        flipType = $(this).attr("flipType");
//        flipId   = $(this).attr("flipId");
//        active = $(this).attr("active");
//        nr = 0;
//        if (active == "1") {
//            if (flipType == "slider") {
//                name     = $(this).attr("name");
//                
//                 //setTimeout(function(){
//    //                    changeFlipTime(name);
//
//      //          },3000);
//
//                flipMs = parseInt($(this).attr("flipMs"));
//                if (flipMs < 50) flipMs = 1000;
//                // flipMs = 1000;
//                //
//                // alert("TimeOut for name "+name + " ms = "+flipMs);
//                // setTimeout(function(){changeFlipTime(name);},flipMs);
//               // timeOutList = {a:1, b:2, c:3};
////                timeOutList['klaus'] = "Hallo";
////                 timeOutList['kobald'] = "Test";
//                nr++;
//                timeOutName[name] = "flip_"+flipId;
//                timeOutList[name] = setInterval("changeFlipTime('" + name + "', " + nr + ")",flipMs);
//                // timeOutList[name] = setInterval(function() {"changeFlipTime('" + name + "', " + nr + ")"},flipMs);
//                //timeOutList[name] = setTimeout()
//
//                inh = "Array hat "+timeOutList.length+" Inhalte";
//                for(var prop in timeOutList) {
//                    inh = inh + "<br>" + prop + " / " + timeOutList[prop];
//                }
//                //timeOutList["nam_e"]= setInterval(function() {alert("// Do something "+name+" every 2 seconds");}, 10000);
//
//                    //flipType = $(this).attr("flipType");
//                 // $(this).html(inh);
//                //},3000);
//
//
//               //  $("."+name).html("Warte");
//            }
//           
//        }
//    /*
//         window.setTimeout(changeFlipTime("flip_66"), 10000)
//         // $(this).html(flipType);
//    }*/
//   })
//   anz = timeOutList.length;
//  // alert ("ANZAHL = " + anz);
//  // for (var eigenschaft in timeOutName) {
// //      alert ("hops "+eigenschaft+" inh = " + timeOutName[eigenschaft])
//   // }
//    /*for (var i = 1; i <= count(timeOutList); i++) {
//        alert("timeOutName "+i);
//    }*/
//}
//
//
//function changeFlipTime(name,nr) {
//    
//   // alert("changeFlipTime " + name + "nr " + nr);
//    
//    layerCount = parseInt($("."+name).attr("layerCount"));
//    layerNr = parseInt($("."+name).attr("layerNr"));
//    frameWidth = $("."+name).attr("frameWidth");
//    flipId = $("."+name).attr("flipId");
//
//    layerNr = layerNr + 1;
//    if (layerNr > layerCount) layerNr = 1;
//    //
//
//    callUrl = "cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth+"&flipId="+flipId;
//    // $("."+name).html("timer<br>"+layerNr+" / "+layerCount+ "<br>"+callUrl);
//    $("."+name).attr("layerNr",layerNr);
//
//    //callUrl = "cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth+"&flipId="+flipId;
//        // inh = "class:"+cl+"<br>name="+name+"<br>FlipType:"+flipType+"<br>layerCount="+layerCount+"<br>layerNr="+layerNr;
//    $.get(callUrl,function(text){
//        $("."+name).html(text);
//    });
//    // name = "Huopps";
//
//
//    //flipMs = parseInt($("."+name).attr("flipMs"));
//    //if (flipMS < 50) flipMs = 1000;
//    // timeOuts[name] = setTimeout(changeFlipTime(name),flipMs);
//    //window.setTimeout(changeFlipTime("flip_66"), 5000);*/
//}
//
//$(document).ready(function(){
//    // startFlipTime();
//    // alert("start");
//})
//startFlipTime();
