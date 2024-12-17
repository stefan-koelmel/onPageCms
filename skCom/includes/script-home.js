$(window).load(function() {
//--------------------------------------------------------------------------- TagesTipp-Slider
          $('#slider-id').codaSlider({
            autoSlide:true,
            autoHeight:false,
            autoSlideInterval:4000,
            autoSlideStopWhenClicked:false,
            dynamicArrows: false,
            dynamicTabsAlign: "right",
            dynamicTabsPosition: "bottom",
            slideEaseDuration: 1000,
	    continuous:false            
          });

//--------------------------------------------------------------------------- AutoHeight-THEME-BOXEN
function equalHeight(container) {
  var maxheight = 0;
  container.each(function() {
    var height = $(this).height();
    if(height > maxheight) {
      maxheight = height;
    }
  });
  container.height(maxheight);
}


function tbmequalHeight(tbmcontainer) {
  var maxheight = 0;
  tbmcontainer.each(function() {
    var height = $(this).height();
    if(height > maxheight) {
      maxheight = height;
    }
  });
  tbmcontainer.height(maxheight-29);
}


        $('.themes_container').each (function () {
          equalHeight($('#'+$(this).attr('id')+' .autoheight'));
        });
        
        $('.themes_container').each (function () {
          tbmequalHeight($('#'+$(this).attr('id')+' .tb_show_more'));
        });    

}); // ende winLoad



$(document).ready(function(){


//--------------------------------------------------------------------------- Schatten für Slider und Tipps   


//$('.shadow').shadow();
//$('.shadowraised').shadow('raised');







//------------------------------------------------- BOX-LINK

 $(".boxlink").click(function () {
                var url = $(this).find("a").attr('href');

		if(url!=undefined)
		{
                window.location = url;
		}
        });



//------------------------------------------------- NEW_WINDOW - Target-Blank-Ersatz

$('.newwindow').click(function(){window.open(this.href,'_blank');return false;});



//------------------------------------------------- ScrollTop
$('.gotop').click(function(){$('html, body').animate({scrollTop: '0px'}, 500);return false;});




//-------------------------------------------------OPA
$(".opa").css("opacity","0.8");
	// ON MOUSE OVER
	$(".opa").hover(function () {									  
	$(this).stop().animate({opacity: 1.0}, 300);
	},		
	// ON MOUSE OUT
	function () {		
		$(this).stop().animate({opacity: 0.8}, 300);
	});	




//------------------------------------------------- Slider_Info
$(".slider_info").css("opacity","0.7");
	$(".slider_info").hover(function () {									  
	$(this).stop().animate({opacity: 1.0}, 300);
	},		
	// ON MOUSE OUT
	function () {		
		$(this).stop().animate({opacity: 0.7}, 300);
	});




//------------------------------------------------- MELDUNGEN


$(".content_meldungen").css("opacity","0.7");
	// ON MOUSE OVER
	$(".content_meldungen").hover(function () {									  
	$(this).stop().animate({opacity: 1.0}, 300);
	},		
	// ON MOUSE OUT
	function () {		
		$(this).stop().animate({opacity: 0.7}, 300);
	});	
    
  





//------------------------------------------------- THEME-MORE
$('.tb_more').live("click", function(){
	$(this).addClass('hide_more').parent().parent().children('.tb_show_more').fadeIn();
	return false;
	});
$('.hide_more').live("click", function(){
	$(this).removeClass('hide_more').parent().parent().children('.tb_show_more').fadeOut();
	return false;
	});


}); // ende docready



