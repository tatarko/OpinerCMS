var hasstarted;

$(document).ready(function() {
	$('.addeditor').click(function(){
		var hid = $(this).attr('href');
		if($(hid).css('display') == 'none')
		$(hid).slideDown('fast');
		else $(hid).slideUp('fast');
		return false;
	});
});