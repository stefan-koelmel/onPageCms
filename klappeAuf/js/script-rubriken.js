$(document).ready(function(){

//------------------------------------------------- BOX-LINK


$('.select-container').hide();

 $(".change-rubrik").click(function () {
		$('.select-the-rubrik').hide();
    $('.select-mainrubrik').slideToggle();
		return false;
        });

 $(".select-filter-link").click(function () {
		$('.select-the-rubrik').hide();
		var mainrubrik = $(this).attr('id');
    $('.select-'+mainrubrik).slideToggle();
		return false;
        });

$(".close_this_container").click(function () {
    $(this).parent().parent().slideToggle();
		return false;
        });



 $(".change-sub").click(function () {
  	$('.select-mainrubrik').hide();

		var subrubrik = $(this).attr('id').slice(4);
    $('.select-the-rubrik').not('.select-'+subrubrik).each(function(){ $(this).hide(); }); 
    $('.select-'+subrubrik).slideToggle();                                           
		return false;
        });

}); // ende docready



