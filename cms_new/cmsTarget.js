var target_orientation; //  = "unkown";
var target_target; // = "unkown";
var target_widh; // = document.documentElement.clientWidth;
var target_height; // = document.documentElement.clientHeight;
    
    setSession = 0;
    
    

    setWidth = document.documentElement.clientWidth;
    if (setWidth != target_widh) setSession = 1;
    
    setHeight = document.documentElement.clientHeight;
    if (setHeight != target_height) setSession = 1;
        
//    if (setWidth > setHeight) setOrientation = "Landscape";    
//    else setOrientation = "Portrait";   
//    if (setOrientation != target_orientation) setSession = 1;
    
    
    setTarget = detect_target();
    if (setTarget) {
        if (setTarget != target_target) setSession = 1;
    }
    
   
    
    //alert ("setTarget = "+setTarget+" target_target = "+target_target);
        
    if (setSession) {
        url = "/cms_"+cmsVersion+"/getData/setSession.php";
        url += "?target_width="+setWidth;
        url += "&target_height="+setHeight;
        url += "&cmsName="+cmsName;
        // url += "&target_orientation="+setOrientation;
        if (setTarget) url += "&target_target="+setTarget;
        // url += "?showTarget="+setTarget;
        res = httpGet(url);
        //  alert ("res "+ res + "url = "+url);
        //$.get(url,function(res){
            //window.location.reload();
        //})
        // alert ("set "+url);
    }
    
function detect_target() {
    target = 0;
    if (navigator.platform.indexOf("iPhone") !=-1) target = "iPhone";
    if (navigator.platform.indexOf("iPod") !=-1) target = "iPod";
    if (navigator.platform.indexOf("iPad") !=-1) target = "iPad";
    
    return target;
        
   
    
    
}
    
function httpGet(theUrl) {
    var xmlHttp = null;

    xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false );
    xmlHttp.send( null );
    return xmlHttp.responseText;
}

