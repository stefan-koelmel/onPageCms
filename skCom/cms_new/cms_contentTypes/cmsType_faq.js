$(".faq_main").click(function(){
    id = $(this).attr("id");
    id = id.substr(9);
    
    hasClass = $("#faq_main_content_"+id).hasClass("faq_main_content_hidden");
    if (hasClass) {
        $("#faq_main_content_"+id).removeClass("faq_main_content_hidden");      
        $(this).removeClass("faq_main_close");
    } else {
        $("#faq_main_content_"+id).addClass("faq_main_content_hidden");   
        $(this).addClass("faq_main_close");
    }
})

$(".faq_headline_single").click(function(){
     id = $(this).attr("id");
    id = id.substr(4);
    
    hasClass = $("#faqText_"+id).hasClass("faq_text_hidden")
    
    $(".faq_text").addClass("faq_text_hidden");   
    $(".faq_headline_single").removeClass("faq_headline_active");   
    
//    id = $(this).attr("id");
//    id = id.substr(4);
//    
//    hasClass = $("#faqText_"+id).hasClass("faq_text_hidden");
    if (hasClass) {
//        $("#faqText_"+id).removeClass("faq_text_hidden");      
//        $(this).addClass("faq_headline_active");
   } else {
//       $("#faqText_"+id).addClass("faq_text_hidden");   
//       $(this).removeClass("faq_headline_active");
        $("#faqText_"+id).removeClass("faq_text_hidden");      
        $(this).addClass("faq_headline_active");

   }
    
    
})


$(".faq_headline").click(function(){
    id = $(this).attr("id");
    id = id.substr(4);
    
    hasClass = $("#faqText_"+id).hasClass("faq_text_hidden");
    if (hasClass) {
        $("#faqText_"+id).removeClass("faq_text_hidden");      
        $(this).addClass("faq_headline_active");
    } else {
        $("#faqText_"+id).addClass("faq_text_hidden");   
        $(this).removeClass("faq_headline_active");
    }
    
})

$(".faq_edit_button").click(function(){
    id = $(this).attr("id");
    id = id.substr(9);   
    hasClass = $("#faq_edit_form_"+id).hasClass("faq_edit_input_hidden");
    if (hasClass) {
        $(".faq_edit_input").addClass("faq_edit_input_hidden");
        $("#faq_edit_form_"+id).removeClass("faq_edit_input_hidden");      
        $(this).addClass("faq_edit_button_active");
    } else {
        $("#faq_edit_form_"+id).addClass("faq_edit_input_hidden");   
        $(this).removeClass("faq_edit_button_active");
    }
    
    
    
})

$(".faq_delete_button").click(function(){
    id = $(this).attr("id");
    id = id.substr(11);
   
    hasClass = $("#faq_deleteFrame_"+id).hasClass("faq_delete_action_hidden");
    if (hasClass) {
        $("#faq_deleteFrame_"+id).removeClass("faq_delete_action_hidden");
    } else {
        $("#faq_deleteFrame_"+id).addClass("faq_delete_action_hidden");
    }       
})


$(".faq_new_button").click(function(){
    id = $(this).attr("id");
    id = id.substr(8);
    // alert(id);
    hasClass = $("#faq_new_form_"+id).hasClass("faq_new_input_hidden");
    if (hasClass) {
        $("#faq_new_form_"+id).removeClass("faq_new_input_hidden");      
        $(this).addClass("faq_new_button_active");
    } else {
        $("#faq_new_form_"+id).addClass("faq_new_input_hidden");   
        $(this).removeClass("faq_new_button_active");
    }
    
    
    
})

function faq_sort_stop() {
    
    out = "<h2>Reihenfolge speichern</h2>";
    showType = "hidden";
    $(".faq_sort").each(function() {
        id = $(this).attr("id");
        catId =id.substr(17);
        // out += "Category Frame "+id+" / catId = "+catId+"<br>";
        sort = 0;
        
        $(this).children(".faq_item").each(function(){
            id = $(this).attr("id");
            faqId = id.substr(9);
            // out += "Childen "+faqId+"<br>";
            out += "<input type='"+showType+"' name='faqSort["+faqId+"][sort]' value='"+sort+"' />";
            out += "<input type='"+showType+"' name='faqSort["+faqId+"][catId]' value='"+catId+"' />";
            sort++;
        })
        
    })
    out += "<input type='submit' value='Reihenfolge speichern' name='faq_sort_save' class='cmsInputButton' />";
    out += "<input type='submit' value='abbrechen' name='faq_sort_cancel' class='cmsInputButton cmsSecond' />";
    $(".faq_output").html(out);
    $(".faq_output").removeClass("faq_output_hidden");
}

$(function (){
    $(".faq_sort").sortable({
        connectWith:".faq_sort",
        axis:"y",
        
        handle: '.faq_move_button',
        stop: function( event, ui ) {
            faq_sort_stop();
        }
    });
    $(".faq_sort").disableSelection();  
});