
$(".skItem").mouseenter(function(){
    skData = $(this).children(".skData").text();
    
    dataList = skData.split("|");
    out = "";
    categoryList = 0;
    projectList = 0;
    dealerList = 0;
    customerList = 0;
    
    for (var i=0;i<dataList.length;i++) { //
        data = dataList[i].split(":");
        items = data[1].split(",");
        
        if (data[0]=="category") categoryList = items;
        if (data[0]=="project") projectList = items;
        if (data[0]=="customer") customerList = items;
        if (data[0]=="dealer") dealerList = items;
        
        
        out += "ITEM "+data[0]+" =="+data[1]+"<br />";        
        
        
    }
    
    for (var i=0;i<categoryList.length;i++) {
        cat = categoryList[i];
        // out += "HIGHLIGHT Cat "+cat+"<br/>";
        
        $("#skCategory_"+cat).addClass("skCategory_highlight");
    }
    
    for (var i=0;i<projectList.length;i++) {
        proj = projectList[i];
        // out += "HIGHLIGHT Proj "+proj+"<br/>";
        
        $("#skProject_"+proj).addClass("skProject_highlight");
    }
    
     for (var i=0;i<customerList.length;i++) {
        customer = customerList[i];
        // out += "HIGHLIGHT Kunde "+customer+"<br/>";
        
        $("#skCustomer_"+customer).addClass("skCustomer_highlight");
    }
    
    for (var i=0;i<dealerList.length;i++) {
        dealer = dealerList[i];
        // out += "HIGHLIGHT Proj "+dealer+"<br/>";
        
        $("#skDealer_"+dealer).addClass("skDealer_highlight");
    }
    
    $(".skHelp").html(out);
    
})

$(".skItem").mouseleave(function(){
    $(".skCategory").removeClass("skCategory_highlight");
    $(".skProject").removeClass("skProject_highlight");
    $(".skCustomer").removeClass("skCustomer_highlight");
    $(".skDealer").removeClass("skDealer_highlight");
})

$(".skProject").click(function() {
    link = $(this).children(".skLink").attr("href");
    if (link) {
        window.location = link; //"index.php";
    }
    
    
   
})
