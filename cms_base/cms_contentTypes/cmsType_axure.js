
$(".axure").ready(function(){
    targetUrl = $(this).attr("src");
    // alert ("Laden "+targetUrl);
    // $(".axure_url").html(targetUrl);
})

$(".axure").load(function(){
    targetUrl = $(this).contents().get(0).location.href;

    splitList = targetUrl.split("/");

    data = splitList[splitList.length-1];
    out = "";
    if (data) {
        splitList = data.split("#");
        axurePage = splitList[0];

        $("#axurePage").val(axurePage);

        data = splitList[1];

        if (data) {
            splitList = data.split("&");
            for (i=0;i<splitList.length;i++) {
                vars = splitList[i].split("=");
                varName = vars[0];
                varCont = vars[1];

                out += varName+" = "+varCont+"<br>";
                $("#axure_"+varName).val(varCont);


                out += splitList[i]+"<br>";
            }
        }



    }


    // alert ("Laden "+targetUrl);
    // $(".axure_url").html(targetUrl+"<br>"+axurePage);
})


$(".axure_open").click(function(){
    display = $(".axure_vars").css("display");
    // alert(display);
    if (display == "none") {
        $(".axure_vars").css("display","block");
    } else {
        $(".axure_vars").css("display","none");
    }
})
  
function cmsAxureLink(link) {
    alert("AXURE LINK "+link+" GO!!!");
}
