  



$(document).ready(function(){


$('textarea').autosize();  
    
  
//------------------------------------------------- FORM_VALUE
$('.forminput').each(
		function(){
			
			var titleVal = $(this).attr('title');			
			
			$(this)
			
			/* Remove the title attribute to stop IE using it as a tooltip */
			.attr('title', '')
			
			/* Use the title of the element we got earlier to set it's default value */
			.val(titleVal)
			
			/* Add the default value to the data object to get later */
			.data('defaultValues', { title: titleVal })
			
			/* If user is focusing on element with default text, remove it to allow them to enter data on focus */
			.focus(
				function(){
					if( $(this).val() == $(this).data('defaultValues').title ){
						$(this).val('');
					}
				}
			)
			
			/* If user has losst focus on the element without entering content, replace with default text */
			.blur(
				function(){
					if( $(this).val() == '' ){
						$(this).val($(this).data('defaultValues').title);
					}
				}
			);
		}
	); 





//------------------------------------------------- CONTACT - FORM-POST
var formoptions={
	target:"#formoutput",
	url:"contact-post.php",
	resetForm:false
	};

$("#contactForm").submit(function(){
	$("#contactForm").ajaxSubmit(formoptions);
	return false
});










}); // ende docready




