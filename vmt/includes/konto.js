var activeBookingNr = "";

$("[id^=orderLine]").click(function(){
    nr = $(this).attr("bookingNr");
    id = $(this).attr("bookingId");
    if (activeBookingNr != nr) {
        close_BookingLine(activeBookingNr)


        activeBookingNr = nr;
        $("#orderInfo"+nr).removeClass("orderInfoClose");
        //$("#orderInfo"+nr).addClass("orderSelect");
        $("[bookingNr^="+nr+"]").addClass("orderSelect");//.css("background-color","#0f0");
        //$("[bookingNr^="+nr+"]").css("border-color","#F19023");
        // $("[bookingNr^="+nr+"]").css("height","100px");
       // $("[id^=orderLine]").css("height","110px");

    } else { // close if open
        if (activeBookingNr != "") {
            $("#orderInfo"+nr).addClass("orderInfoClose");
            $("[bookingNr^="+nr+"]").removeClass("orderSelect");
            activeBookingNr = "";
        }
    }
})

function close_BookingLine(nr) {
    if (nr != "") {
        $("#orderInfo"+nr).addClass("orderInfoClose");
        $("[bookingNr^="+nr+"]").removeClass("orderSelect");
        // $("[bookingNr^="+nr+"]").css("border-color","#e8e8e8");

//        $("[bookingNr^="+nr+"]").css("height","60px");
//        $("[bookingNr^="+nr+"]").css("height","20px");
//        $("[bookingNr^="+nr+"]").css("overflow","hidden");
//        $("[bookingNr^="+nr+"]").css("line-height","20px;");
    }
    
//    $("[bookingNr^="+nr+"]").css("height","100px");
//    $("[id^=orderLine]").css("height","30px");
//    $("bookingNr="+nr).css("background-color","#f00");
}



function showUser(filter){
    $.get("includes/kontoSites/findUser.php?filter="+filter,function(text){
        $("#output").html(text);
    });
}

$(".searchUser").keyup(function(){
    var filter = $(this).val();
    showUser(filter);
})

function start_allUser(){
    var filter = $(".searchUser").val();
    showUser(filter);
}



// DEALER //////////////////////////////////////////////////////////////////////
function showDealer(filter){
    $.get("includes/kontoSites/findDealer.php?filter="+filter,function(text){
        $("#output").html(text);
    });   
}

$(".searchDealer").keyup(function(){
    var filter = $(this).val();
    showDealer(filter);
})

function start_allDealer(){
    var filter = $(".searchDealer").val();
    showDealer(filter);
}

// GARAGE //////////////////////////////////////////////////////////////////////
function showGarage(filter){
    $.get("includes/kontoSites/findGarage.php?filter="+filter,function(text){
        $("#output").html(text);
    });
}

$(".searchGarage").keyup(function(){
    var filter = $(this).val();
    showGarage(filter);
})


function start_allGarage(){
    var filter = $(".searchGarage").val();
    showGarage(filter);
}


// TERMINE  //////////////////////////////////////////////////////////////////////
var selectCode = "";

$(".MonthActive").click(function(){
    dayCode = $(this).attr("day");
    year    = $(this).attr("year");
    if ($(this).hasClass("MonthClose")) {
         $(this).removeClass("MonthClose");
         $(this).html(dayCode);
    } else {
        $(this).addClass("MonthClose");
        $(this).html(dayCode+"<input type='hidden' style='width:30;' value='1' name='closeData["+dayCode+year+"]'> ");
    }
})



$(".TimeLineColumn").click(function(){
    if (viewMode == "open") {
        if ($(this).hasClass("TimeOpen")) {
            $(this).removeClass("TimeOpen");
            $(this).removeClass("TimeBooking");
            $(this).html("&nbsp;");
           // $(this).text("0" + viewMode);
        } else {
            $(this).addClass("TimeOpen");
            $(this).addClass("TimeBooking");
            $(this).html("&nbsp;<input type='hidden' style='width:30;' value='1' name='openData["+dayCode+"]["+timeCode+"]'> ");

           // $(this).text("1" + viewMode);
        }
        $(this).removeClass("TimeBookRoll");
    }

    if (viewMode == "area") {
        if ($(this).hasClass("TimeOpen")) {
            if ($(this).hasClass("TimeBooking")) {
                $(this).removeClass("TimeBooking");
                $(this).html("&nbsp;");
            // $(this).text("0" + viewMode);
            } else {
                $(this).addClass("TimeBooking");
                $(this).html("&nbsp;<input type='hidden' style='width:30;' value='1' name='areaData["+dayCode+"]["+timeCode+"]'> ");
            }
            // $(this).text("1" + viewMode);
            $(this).removeClass("TimeBookRoll");
        }
       
    }


})

$(".TimeAsked").click(function(){
    bookId = $(this).attr("dateId")
    $.get("includes/zertDates/zertAction.php?zertDateId="+bookId,function(data){
        $(".bookingArea").html(data);
    });
})

$(".TimeBooked").click(function(){
    bookId = $(this).attr("dateId")
    $.get("includes/zertDates/zertAction.php?zertDateId="+bookId,function(text){
        $(".bookingArea").html(text);
    });
})



$(".TimeBooking").click(function(){
    
    if (viewMode == "dates") {

        if ($(this).hasClass("timeBookFree")) {
            $(this).removeClass("timeBookFree");
        } else {
            $(this).addClass("timeBookFree");
        }

        startTime = $(this).attr("startstr");
        endTime   = $(this).attr("endStr");
        timeCode  = $(this).attr("timeCode");
        dayCode   = $(this).attr("dayCode");
        weekday   = $(this).attr("weekday");
        datum     = $(this).attr("datum");

        if (selectCode.length > 0) {
            $("."+selectCode).removeClass("TimeBookSelect");
        }

        selectCode = "TC_" +dayCode+"_"+timeCode;

        $("."+selectCode).addClass("TimeBookSelect");
        $(this).removeClass("TimeBookRoll");

       /* $(".bookingArea").css("height","60px");
        $(".bookingArea").css("margin","5px 0 5px 0");
        // $(".bookingArea").css("overflow","visible");
        dayString = weekday + ", den " +datum + "<input type='hidden' name='day' value='"+datum+"' >";
        $(".bookingDay").html(dayString);
        timeString = startTime + " - " + endTime + " Uhr <input type='hidden' name='time' value='"+timeCode+"' >";
        $(".bookingTime").html(timeString); */
    }

    //alert("Buchung am " + weekday + ", den " +datum+ "\r\nUhrzeit:" + startTime + " - " + endTime + " Code "+ timeCode);
})

$(".TimeLineColumn").mouseenter(function(){
    timeCode  = $(this).attr("timeCode");
    dayCode = $(this).attr("dayCode");

    rollCode = "TC_" +dayCode+"_"+timeCode;
    // alert(rollCode);
    if (viewMode == "dates") {
        if (rollCode != selectCode) $(this).addClass("TimeBookRoll");
    }
    if (viewMode == "open") {
        // if (rollCode != selectCode) $(this).addClass("TimeBookRoll");
    }

    $(".timecode"+timeCode).css("background-color","#eeee88");
    $(".daycode"+dayCode).css("background-color","#eeee88");


})

$(".TimeLineColumn").mouseleave(function(){
    timeCode  = $(this).attr("timecode");
    dayCode = $(this).attr("dayCode");
    //$(this).css("background-color","#fff");
    $(this).removeClass("TimeBookRoll");
    $(".timecode"+timeCode).css("background-color","#ddd");
    $(".daycode"+dayCode).css("background-color","#ddd");
})

/*
function filterList(header, list) {
    var form = $("<form>").attr({"class":"filterform","action":"#"}),
        input = $("<input>").attr({"class":"filterinput","type":"text"});
    $(form).append(input).appendTo(header);

    $(input)
      .change( function () {
        var filter = $(this).val();
        if(filter) {

          $matches = $(list).find('a:Contains(' + filter + ')').parent();
          $('li', list).not($matches).slideUp();
          $matches.slideDown();

          showUser(filter);
          out = "Filter:";
          $.get("includes/kontoSites/findUser.php?filter="+filter,function(text){
                $("#output").html(text);
          });

          var myArray = new Array("id6","id1");

          //$(user).find("div").slideUp();
         for( var i=0; i <myArray.length; i++) {
              id = myArray[i];
              $("#"+id).slideUp();
          }
          //$("#id20").slideDown();

         
          
         // $matchUser = $(user).find('email').attr("email","Contains("+filter+")");
          $matchUser = $(user).find("email=sk@stefan-koelmel.de");
          $('div', user).not($matchUser).slideUp();
          $matchUser.slideDown();
          
        
        } else {
            showUser("");
          $(list).find("li").slideDown();
          $(user).find("div").slideDown();
          //$(output).text("empty");
        }
        return false;
      })
    .keyup( function () {
        var filter = $(this).val();
        showUser(filter);

        // $(this).change();
    });
  }




(function ($) {
  jQuery.expr[':'].Contains = function(a,i,m){
      return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
  };

 

  $(function () {
    filterList($("#form"), $("#list"));
  });
}(jQuery));

*/