function widgetsStartUp () {
	var ii = 0;
	$('.sidebarPlugin').each(function(){
		if (ii > 2) {
			$('.sidebarPlugin:eq(' + ii + ') .inner').hide();
			$('.sidebarPlugin:eq(' + ii + ') h2:eq(0) .icons').append(' <span id="link' + ii + '"><a href="#" onclick="return showWidget(' + ii + ');"><img src="admin/images/icon-add.png" title="' + lang.max + '" alt="" /></a></span>');
		} else $('.sidebarPlugin:eq(' + ii + ') h2:eq(0) .icons').append(' <span id="link' + ii + '"><a href="#" onclick="return hideWidget(' + ii + ');"><img src="admin/images/icon-delete.png" title="' + lang.min + '" alt="" /></a></span>');
		ii = ii + 1;
	});
};

function showWidget(id) {
	$('.sidebarPlugin:eq(' + id + ') .inner').slideDown('fast');
	$('#link' + id).hide().html('<a href="#" onclick="return hideWidget(' + id + ');"><img src="admin/images/icon-delete.png" title="' + lang.min + '" alt="" /></a>').fadeIn('fast');
	return false;
}

function hideWidget(id) {
	$('.sidebarPlugin:eq(' + id + ') .inner').slideUp('fast');
	$('#link' + id).hide().html('<a href="#" onclick="return showWidget(' + id + ');"><img src="admin/images/icon-add.png" title="' + lang.max + '" alt="" /></a>').fadeIn('fast');
	return false;
}