/* Opiner CMS 1.4
   Správa galérie
   Skripty na spoluprácu s jQuery knižnicou
   za účelom vytvorenia vlastného APIčka
   pre správu galérie */



/* Vykreslenie formuláru na pridávanie
   médií do galérie */

function addMedium () {
	$("#addMedium").hide().html($("#hideForm").html()).fadeIn("slow");
	return false;	
}



/* Funkcia na dokreslenie formuláru
   pre pridávanie médií - pridanie
   ďalších políčok pre ďalší
   obrázok / hudba / video */

function addFMp () {
	if (typeof incr == 'undefined') incr = 1;
	$("#addMedium #addFM").append('<div id="incr' + incr + '"></div>');
	$('#incr' + incr).hide().append('<input type="text" name="title[]" /><textarea name="description[]"></textarea><input type="file" name="file[]" />').fadeIn('slow');
	incr = incr + 1;
	return false;
}



/* Vykreslenia formulárov na editáciu
   informácií o médiu ako napríklad
   názov a popis. */

function editImage (id, tid, cat, page, nadpis, popis) {
	if (typeof lastEGI != 'undefined') {
		$('#EGI' + id).remove();
		$("#editGallery .em:eq(" + lastEGI + ")").fadeOut('medium').html('').fadeIn('fast');
	};
	$("#editGallery .em:eq("+id+")").append('<div id="EGI' + id + '"></div>');
	$('#EGI' + id).hide().append('<form action="?what=gallery&mod=manage&id='+cat+'&edit='+tid+'&page='+page+'" method="post"><input type="text" name="title" value="'+nadpis+'" /><textarea name="description">'+popis+'</textarea><input type="submit" value="' + lang.save + '" /></form>').fadeIn('medium');
	lastEGI = id;
	return false;
}