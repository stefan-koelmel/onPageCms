function basket_refreshInfo() {
    // alert ("REFRESH BasketInfo");

   callUrl = "/cms_"+cmsVersion+"/getData/basket.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
   callUrl += "&mode=showInfo";
   $.get(callUrl,function(res){
        $(".basketInfo").html(res);
   })

    
   // html = "REFRESH BASKETINFO";
   
}

function basket_itemRefresh(basketId,anz,maxAdd,name,value,shipping) {

   callUrl = "/cms_"+cmsVersion+"/getData/basket.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion;
   callUrl += "&mode=showBasketItem&basketId="+basketId+"&anz="+anz;
   callUrl += "$maxAdd="+maxAdd+"&name="+name+"&value="+value+"&shipping="+shipping;
   $.get(callUrl,function(res){
        $("."+basketId).html(res);
        // $(".basketInfo").html(res);
   })
}


function basket_showInfo(text) {
    alert (text);
}


$(".cmsAddBasketButton").live("click",function(){
    classes = $(this).parent(".basket").attr("class");
    classSplit = classes.split(" ");
    addItem = classSplit[1];
    
    anz =  $("."+addItem).children(".cmsBasketAddCount").val();
    
    maxAdd = $("."+addItem).children(".cmsBasketMaxCount").html();
    
    anz    = parseInt(anz);
    maxAdd = parseInt(maxAdd);
    
    
    name     = $("."+addItem).children(".cmsBasketAddName").html();
    value    = $("."+addItem).children(".cmsBasketAddValue").html();
    shipping = $("."+addItem).children(".cmsBasketAddShipping").html();
    
   
    if (anz > maxAdd) {
        basket_showInfo("Sie haben "+anz+" Artikel ausgewählt, es sind aber nur "+maxAdd+" verfügbar!");
        return 0;
    }
    
    callUrl = "/cms_"+cmsVersion+"/getData/basket.php?cmsName="+cmsName+"&cmsVersion="+cmsVersion+"&mode=add";
    callUrl += "&basketId="+addItem+"&count="+anz+"&name="+name+"&value="+value+"&shipping="+shipping;
    $.get(callUrl,function(res){
        if (res == "1") {
            basket_showInfo("Erfogreich in den Warenkorb gelegt");
            basket_itemRefresh(addItem,anz,maxAdd,name,value,shipping);
        } else {
            basket_showInfo("Fehler beim in den Warenkorb legen'"+res+"' !");
        }
         
    })
  
  
  // alert ("add Item "+addItem+" Anzahl="+anz+" MAX = "+maxAdd);
    // alert ("addToBasket");
    
    basket_refreshInfo();
})

$(".basketpayment").change(function(){
    // alert("Anders");
    sel = $(this).val(); //"val");
    classen = $(this).attr("class");
    // alert ("VAL = "+sel + " Class = "+classen);
    $(".basketPayment_info").removeClass("basketPayment_info_selected");
    $(".basketPayment_info_"+sel).addClass("basketPayment_info_selected");
    
})

$(".cmsBasketUseDelivery").change(function(){
    sel = $(this).attr("checked"); //"val");
    // alert ("VAL = "+sel);
    if (sel) {
        $(".cmsBasket_delivery").removeClass("cmsBasket_delivery_hidden")
        
    } else {
        $(".cmsBasket_delivery").addClass("cmsBasket_delivery_hidden");
    }    
})

$(document).ready(function() {
   // liveSort();
   // alert("Hier");
    
});


