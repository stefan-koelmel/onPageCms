/*function show_date() {
    day = $(".adminDates_Day").attr("value");
    month = $(".adminDates_Month").attr("value");
    year = $(".adminDates_Year").attr("value");
   

    info = "Datum = "+day+"."+month+"."+year+"<br>";

    category = $(".adminDates_Category").attr("value");
    info = info + "Kategorie = " + category;


    ort = $(".adminDates_Location").attr("value");
    info = info + "Ort = " + ort;

    url = $(".adminDatesFrame").attr("url");
    info = info + "<br>" + "URL = "+url;
    url = url+"&day="+day;
    url = url+"&mon="+month;
    url = url+"&yea="+year;
    url = url+"&cat="+category;
    url = url+"&loc="+ort;
    url = url+"&out=list";

    $.get(url,function(text){
        erg = "";
        var fensterwerte = {
             "breite": 400, "hoehe": 500, "titel": "Neues Fenster"
        }
        for (var elem in fensterwerte) {
            erg += "Index " + elem + ": ";
            erg += fensterwerte[elem] + "<br />";
        }
        $(".adminDates_rightFrame").html(url+"<br>"+erg + text);
    });
    


    
}
function show_Location() {
    show_date();
}

function show_Category() {

    show_date();
}


$(".adminDates_Category").live("focusout",function() {
    show_Category();
})

$(".adminDates_Day").live("change",function(){show_date();})

$(".adminDates_Month").live("change",function(){show_date();})

$(".adminDates_Year").live("change",function(){show_date();})

$(".adminDates_Location").live("focusout",function(){
     show_Location();
})


$(document).ready(function(){
    $("#firstFocus").focus();
   // $('#dateInput :input:visible:enabled:first').focus();
}) */