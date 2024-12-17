//var lastRoll = "";
//var timeOutList = {};
var selectedFlip = 1;


$(".cmsFlipFrameSelector").click(function(){
    layerNr = $(this).attr("layerNr");
    name    = $(this).attr("name");
    frameWidth   = $(this).attr("frameWidth");


    
    if (selectedFlip != layerNr) {
        
        alert ("KLCIK "+layerNr+" name = "+name);
        selectedFlip = layerNr;
    } else {
        alert ("KLICK on selected Layer "+layerNr);
    }
//
//    }

   // $(".cmsFlipFrameSelector").removeClass("cmsFlipFrameSelectorSelected");


    //$(this).addClass("cmsFlipFrameSelectorSelected");

    // $.get("cms/cms_contentGet.php?name="+name+"_"+layerNr+"&layerNr="+layerNr+"&frameWidth="+frameWidth,function(text){
    //    $("."+name).html(text);
    //});

    //$(".cmsFlipFrameSelector").each(function(index, value) {
    //    console.log('div' + index + ':' + $(this).attr('id'));
        // alert("Hier");
    //    otherFilpName = $(this).attr("name");
        //otherFlipNr   = $(this).attr("layerNr");
       // alert("Hier "+index);
       /* if (otherFlipName == name) {
            alert("Check "+name+" nr="+otherFlipNr+" aktNr="+layerNr);
            if (otherFlipNr != layerNr) {
                $(this).removeClass("cmsFlipFrameSelectorSelected");
            }
        }*/
    //});


})


$("[class^=cmsFlipFrameContent]").mouseenter(function(){
    /*name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    cl = $(this).attr("class");

    if (flipType == "click") {
        $(this).css("cursor","pointer");
    }

    if (flipType == "roll") {
        if (lastRoll != name) {
            $.get("cms/cms_contentGet.php?name="+lastRoll+"_1",function(text){
                $("."+lastRoll).html(text+"<br>"+lastRoll);
            });
        }
    }


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

// $(".cmsFlipFrameContent")
$("[class^=cmsFlipFrameContent]").mouseleave(function(){
    /*name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    cl = $(this).attr("class");

    if (flipType == "click") {
        $(this).css("cursor","default");
    }

    if (cl == "cmsFlipFrameContent "+name) {
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



$("[class^=cmsFlipFrameContent]").click(function(){
    /*name = $(this).attr("name");
    flipType = $(this).attr("flipType");
    active = $(this).attr("active");
    cl = $(this).attr("class");

    if (flipType == "click") {
        layerCount = parseInt($(this).attr("layerCount"));
        layerNr = parseInt($(this).attr("layerNr"));

        layerNr = layerNr + 1;
        if (layerNr > layerCount) layerNr = 1; 
        
        inh = "class:"+cl+"<br>name="+name+"<br>FlipType:"+flipType+"<br>layerCount="+layerCount+"<br>layerNr="+layerNr;
        $.get("cms/cms_contentGet.php?name="+name+"_"+layerNr,function(text){
            $("."+name).html(text+"<br>"+inh);
        });
        $(this).attr("layerNr",layerNr);

    }*/

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




function changeFlipTime(name) {
    /*
    alert("changeFlipTime " + name);
    layerCount = parseInt($("."+name).attr("layerCount"));
    layerNr = parseInt($("."+name).attr("layerNr"));

    layerNr = layerNr + 1;
    if (layerNr > layerCount) layerNr = 1; 
    // $("."+name).html("timer<br>"+layerNr+" / "+layerCount);
    $("."+name).attr("layerNr",layerNr);

    $.get("cms/cms_contentGet.php?name="+name+"_"+layerNr,function(text){
        $("."+name).html(text);
    });


    flipMs = parseInt($("."+name).attr("flipMs"));
    if (flipMS < 50) flipMs = 1000;
    // timeOuts[name] = setTimeout(changeFlipTime(name),flipMs);
    //window.setTimeout(changeFlipTime("flip_66"), 5000);*/
}


 /*var changeFlipTime = function (name){
    layerCount = parseInt($("."+name).attr("layerCount"));
    layerNr = parseInt($("."+name).attr("layerNr"));

    layerNr = layerNr + 1;
    if (layerNr > layerCount) layerNr = 1;
   //  $("."+name).html("timer<br>"+layerNr+" / "+layerCount);

    // window.setTimeout(changeFlipTime("flip_66"), 5000);
  }
*/


function startFlipTime(){
    // alert("start");
    // var deinTimer = window.setInterval(changeFlipTime("flip_66"), 5000);


    // window.setTimeout(changeFlipTime("flip_66"), 5000);

   // var deinTimer = window.setTimeout(changeFlipTime("flip_66"), 5000);
    //alert("start");
   
   
  /* $(".cmsFlipFrameContent").each(function() {
        
        flipType = $(this).attr("flipType");
        active = $(this).attr("active");
        if (active == "1") {
            if (flipType == "time") {
                name     = $(this).attr("name");
                 //setTimeout(function(){
    //                    changeFlipTime(name);
        
      //          },3000);

                flipMs = parseInt($(this).attr("flipMs"));
                if (flipMs < 50) flipMs = 1000;
                flipMs = 10000;
                // setTimeout(function(){changeFlipTime(name);},flipMs);
               // timeOutList = {a:1, b:2, c:3};
                //timeOutList['klaus'] = "Hallo";
                // timeOutList['kobald'] = "Test";
                timeOutList[name] = setInterval(function(){changeFlipTime(name);},flipMs);
                
                inh = "Array hat "+timeOutList.length+" Inhalte";
                for(var prop in timeOutList) {
                    inh = inh + "<br>" + prop + " / " + timeOutList[prop];
                }

                    //flipType = $(this).attr("flipType");
                 $(this).html(inh);
                //},3000);


               //  $("."+name).html("Warte");
            }
        }*/
        /*
             window.setTimeout(changeFlipTime("flip_66"), 10000)
             // $(this).html(flipType);
        }*/
   //})
}


$(document).ready(function(){
    // startFlipTime();
})
