function changeStatusForm (ofhn, appname, appurl) {
	status = $('#status').html();
	$('#status').hide().html('<form action="http://friends.tatarko.sk/cofr/post/' + ofhn + '" method="post" id="newstatus"></form>');
	$('#newstatus')
		.append('<input type="hidden" name="app" value="' + appname + '" />')
		.append('<input type="hidden" name="appurl" value="' + appurl + '" />')
		.append('<textarea name="status" onclick="this.value=\'\';"></textarea>')
		.append('<input type="submit" value="Uložiť" onclick="return postStatus();" />')
		.append('<input type="submit" value="Zatvoriť" onclick="return statusBack();" />');
	$('#status').slideDown('medium');
	return false;	
}

function statusBack () {
	$('#status').hide().html(status).slideDown('medium');
	return false;
}

function readMessage (id, hash) {
	alert (id);
	alert ("http://friends.tatarko.sk/cofr.php?message=" + hash)
	$.ajax({
		type: "GET",
		url: "http://friends.tatarko.sk/cofr.php",
		data: "message=" + hash,
		success: function(data, status, xhr) {
			alert("Load was performed." + status);
			$("#readMessage").html(data);
		},
		complete: function(xhr, status) {
			alert("Load was completed." + status);
		},
		error: function(xhr, status) {
			alert("Load was errored." + status);
		},
	});
	return false;
}