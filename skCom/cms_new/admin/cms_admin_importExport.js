 function copyFile(name,percent,target){
    $("."+target).children(".cmsupdate_name").html(name);
    $("."+target).children(".cmsupdate_percent").css("width",percent);              
}


function copyReady() {
    $(".cmsupdate_frame").css("display","none");
    $(".cmsupdate_ready").css("display","block");    
}
function next() {
    
    callUrl = "/cms_"+cmsVersion+"/admin/copy.php";
    
    $.get(callUrl,function(res){  
         if (res == "ready") {
            copyFile("Fertig",0,"copy_1");
            copyReady();
//            url = "setup.php?view=install";
//            
//            
//            url = "/admin_cmsImportExport.php?view=updateCMS";
//            if (url) {
//                window.location = url; //"index.php";
//            }
        } else {
            if (res == "0") {
            } else {
                sp = res.split("|");
                percent = sp[1];
                inh = sp[0];
                //alert ("res "+res+" inh "+inh+" per "+percent);
                copyFile(inh,percent,"copy_1");
                next();
            }
             //$(".copy_name").html("Res = '"+res+"'");
        }
    })      
}