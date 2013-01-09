$(document).ready(function()
{
	$('.noadmin strong').css('padding-top', '0');
	if ($('#isadmin').attr('checked'))
	$('.noadmin').slideUp('fast');
	$('#isadmin').change(function ()
	{
		if ($(this).attr('checked'))
		$('.noadmin').slideUp('fast');
		else $('.noadmin').slideDown('fast');
	});
});