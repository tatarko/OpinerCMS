/* loadAdminApi
 * Načíta všetky potrebné funkcie, aby mohla administrácia začať fungovať ako
 * jedna JS (Ajax) aplikácia - načíta si svoje API */

function loadAdminApi(rw, ajaxadmin)
{
 if (rw == 1) widgetsStartUp();
 if (ajaxadmin == 1)
 $('a.ajax').click(function(){
  var href = $(this).attr('href');
  $('#ajaxloader').load(href + ' #content');
  return false;
 });
 $('#menu > ul > li > a').click(function() { if (this.href == '#') return false; });
 $('#menu > ul > li').hover(function(){
  var pos = $(this).position();
  $(this).find('ul:first').css({position: 'fixed', left: 64, top: (pos.top + 48)}).fadeIn('fast');
 }, function(){
  $(this).find('ul:first').fadeOut('fast');
 });
};





/* showM
 * Táto funkcia spôsobí zakrytie/vysunutie
 * menu pri pravom ovládacom paneli.
*/  

function showM(id) {
	$("#"+id + " > ul").slideDown("fast");
	return false;
}





/* hideM
 * Táto funkcia spôsobí zakrytie/vysunutie
 * menu pri pravom ovládacom paneli.
*/

function hideM(id) {
	$("#" + id + " > ul").slideUp("fast");
	return false;
}