$(document).ready(function(){ 
    $("a.zoomimage").fancybox({
        'transitionIn'	: 'elastic',
        'transitionOut'	: 'elastic',
        'overlayOpacity': 0.8,
        'overlayColor'	: '#333',
        'titleShow'	: false,
        'centerOnScroll': true,
        'scrolling'     : 'no',
        'cyclic' 	: true
//        'onStart' : function(){
//            alert("start");
//        }
    }); 
    
     $("a.zoomDiv").fancybox({
        'transitionIn'	: 'elastic',
        'transitionOut'	: 'elastic',
        'overlayOpacity': 0.8,
        'overlayColor'	: '#333',
        'titleShow'	: false,
        'cyclic' 	: true,
        'onStart' : function(){
            //alert("startDiv");
        },
        'onError' : function(){
            //alert("errorDiv");
        }
    }); 

});
               