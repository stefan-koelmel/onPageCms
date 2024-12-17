$(window).load(function() {

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

        $('.themes_container').each (function () {
          equalHeight($('#'+$(this).attr('id')+' .autoheight'));
        });


});





$(document).ready(function(){

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




//-------------------------------------------------OPACITY - ALLG
$(".opa").css("opacity","0.8");
	// ON MOUSE OVER
	$(".opa").hover(function () {									  
	$(this).stop().animate({opacity: 1.0}, 300);
	},		
	// ON MOUSE OUT
	function () {		
		$(this).stop().animate({opacity: 0.8}, 300);
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
    
  

//------------------------------------------------- Show/Hide -->Next (KALENDER-INFOS)

 $(".date-info-link").click(function(){
	$('.the-dates-detail').hide();

		if(!$(this).next().hasClass('slideopen'))
		{
			$('.the-dates-detail').removeClass('slideopen');
			$('.date-info-link').removeClass('datescurrentdetail');
			$(this).addClass('datescurrentdetail');
			$(this).next().addClass('slideopen').show();
		}
		else
		{
			$(this).removeClass('datescurrentdetail');
			$(this).next().removeClass('slideopen').hide();
		}
	

		return false;
        });




}); // ende docready



