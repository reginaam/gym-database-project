$(document).ready(function() {
	$('.view').hide();
	$('.view.home').show();
	
	$('.tab').click(function(){
		var id = $(this).attr('id');
		$('.tab').removeClass('selected');
		$(this).addClass('selected');
		$('.view').hide();
		$('.view#'+id).show();
	});
});