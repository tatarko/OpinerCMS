/**********************************
 * Opiner Text
 * Text-Files Editor
 * Powered by Ovalio
 * Verzia 1.0
 *********************************/



/*--- Uložiť Ako (Získaj Názov Súboru) ---*/

function GetNewFileName () {
	var nazov = prompt (lang.fname, lang.nfile + '.txt');
	if (nazov != null && nazov != '') {
		element = document.createElement('input');
		element.name = 'new-file-name';
		element.type = 'hidden';
		element.value = nazov;
		document.forms.OpinerTextForm.appendChild(element);
		return true;
	} else return false;
}



/*--- Náhľad súboru ---*/

function GoToFile (file) {
	var sirka = screen.availWidth - 200;
	var vyska = screen.availHeight - 200;
	oknoview = window.open (file, "oknoview", "width=" + sirka + ", height=" + vyska + ", scrollbars=yes");
	oknoview.moveTo(100,75);
	oknoview.focus();
	return false;
}



/*--- Okno Opiner Text-u ---*/

function OpenOpinerWindow (subsite) {
	var sirka = screen.availWidth - 640;
	opinerwindow = window.open (addrBase + 'subsite.php?site=' + subsite, "opinerwindow", "width=640, height=480");
	opinerwindow.moveTo(sirka,0);
	opinerwindow.focus();
	return false;
}



/*--- Pozatvárame otvorené okná ---*/

function OpinerExitForm () {
	oknoview.close();
	opinerwindow.close();
	return true;
}