// JavaScript Document
$(document).ready(function() {
    $('.firstLevel').hover(function() {
	    $(this).addClass('hovered');
		$(this).next().removeClass('display_none');   
	},function() {
	    $(this).removeClass('hovered');
		$(this).next().addClass('display_none'); 
	});
	
	$('.submenu').hover(function() {
        $(this).prev().addClass('hovered'); 
        $(this).removeClass('display_none');
    },function() {
        $(this).prev().removeClass('hovered');
        $(this).addClass('display_none');
    });

    $('.tbTink tr').hover(function() {
        $(this).addClass('hoveredTr');      
    },function() {
        $(this).removeClass('hoveredTr');
    });
});