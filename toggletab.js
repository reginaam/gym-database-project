$(document).ready(function() {
	$('.view').hide();
	var id = $('.tab.selected').attr('id');
	$('.view#'+id).show();
	
	$('.tab').click(function(){
		var id = $(this).attr('id');
		$('.tab').removeClass('selected');
		$(this).addClass('selected');
		$('.view').hide();
		$('.view#'+id).show();
	});
});