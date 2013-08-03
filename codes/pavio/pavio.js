/*
 * Pavio 1.0
 *
 * by Tomáš Tatarko
 * http://tatarko.info/
 * http://tatarko.sk/
 *
 * Created for: Opiner CMS
 * http://opiner.tatarko.sk/
 *
 */


// Definovanie globalných premenných
var __pavio_src,          		// Cesta k obrázku
    __pavio_title = '(bez popisu)',	// Popisok obrázku
    __pavio_count = 0,			// Súčet obrázkov v galérií
    __pavio_actual = 0,			// Ktorý zo sady obrázkov je vybraný?
    __pavio_collection = new Array(),	// Pole so zoznamom obrázkov
    __pavio_timer;                      // Časovač na auto-načítanie SideNotes



// Po načítaní stránky
$(document).ready(function(){



 // Uzatvorí okienko
 function __pavio_close () {
  $('#pavio').slideUp('medium', function(){
   $(this).remove();
  });
  $(document).unbind('keydown', __pavio_bind);
 }



 // Funkcia na bindovanie
 function __pavio_bind (chr) {
  if(chr.which == 27) __pavio_close();
  else if(chr.which == 39) __pavio_next();
  else if(chr.which == 37) __pavio_prev();
  // return false;
 }



 // Ideme na nasledujúci obrázok
 function __pavio_next () {
  if(__pavio_actual == __pavio_count)
  __pavio_actual = 1;
  else __pavio_actual += 1;
  __pavio_src = __pavio_collection[__pavio_actual][0];
  __pavio_title = __pavio_collection[__pavio_actual][1];
  $('#pavio .image .headImage').fadeOut('medium', function(){
   $(this).attr('src', __pavio_src).attr('alt', __pavio_title).fadeIn('medium');
  });
  $('#pavio .image .description').html(__pavio_collection[__pavio_actual][1]);
  $('#actual').html(__pavio_actual);
  __pavio_comments();
 }



 // Ideme na predchadzajúci obrázok
 function __pavio_prev () {
  if(__pavio_actual == 1)
  __pavio_actual = __pavio_count;
  else __pavio_actual -= 1;
  __pavio_src = __pavio_collection[__pavio_actual][0];
  __pavio_title = __pavio_collection[__pavio_actual][1];
  $('#pavio .image .headImage').fadeOut('fast', function(){
   $(this).attr('src', __pavio_src).attr('alt', __pavio_title).fadeIn('medium');
  });
  $('#pavio .image .description').html(__pavio_collection[__pavio_actual][1]);
  $('#actual').html(__pavio_actual);
  __pavio_comments();
 }



 // Načítanie komentárov
 function __pavio_comments(){
  $('.pavio .comments_box .comment').fadeOut('fast').remove();
  var id = __pavio_src.split('/');
  id = id[2].split('.');
  id = id[0];
  $.getJSON('codes/pavio/pavio.php', {
   comments: id
  }, function(data){
   if(data.code == '100') {
    for (var i = 1; i <= data.count; ++i){
     var arr = data.comment[i];
     var comment = $('<div>').attr('id', 'comment' + arr.id).addClass('comment').css('display', 'none');
     $('<p>').addClass('entry').html(arr.entry).appendTo(comment);
     $('<p>').addClass('metainfo').html(arr.dateformat).appendTo(comment);
     $(comment).appendTo('#pavio .comments_box').fadeIn('slow');
    };
    $('.pavio .input').attr('value', '');
   } else alert(data.message);
  });
  clearTimeout(__pavio_timer);
  __pavio_timer = setTimeout('__pavio_comments', 5000);
 }


 // Nasadí event na všetky naše odkazy
 $('a[rel*="pavio"]').click(function(){



  // Zapamätá si informácie o obrázku
  __pavio_src = $(this).attr('href');
  __pavio_title = $(this).attr('title');


  
  // Ak ide o sadu obrázkov, prednačíta si informácie
  var array = $(this).attr('rel').split('/');
  if (typeof array[1] != 'undefined'){
   __pavio_count = 0;
   __pavio_actual = 0;
   var gallery = array[1];
   $('a[rel="pavio/' + gallery + '"]').each(function(){
    __pavio_collection[++__pavio_count] = [$(this).attr('href'), $(this).attr('title')];
    if ($(this).attr('href') == __pavio_src)
    __pavio_actual = __pavio_count;
   });
  };



  // Načítame Pavio okienko
  $('<div>').attr('id', 'pavio').addClass('pavio').css('display', 'none').appendTo('body').slideDown('slow', function(){



   // Vľavo > Obrázok
   var pattern = $('<div>').addClass('pattern');
   var image = $('<div>').addClass('image');
   $('<img>').attr('alt', __pavio_title).attr('src', __pavio_src).addClass('headImage').css('display', 'none').fadeIn('slow').appendTo(image);
   $('<p>').addClass('description').html(__pavio_title).appendTo(image);
   $('<p>').addClass('meta').html('Obrázok <span id="actual">' + __pavio_actual + '</span> z <span id="count">' + __pavio_count + '</span>').appendTo(image);
   $(image).appendTo(pattern);



   // Vpravo > Komentáre
   var comments = $('<div>').addClass('comments');
   $('<h1>').html('SideNotes').appendTo(comments);
   $('<form>').html('<textarea name="comment" class="input"></textarea><input type="submit" value="Pridať" class="submit" />').submit(function(){
    var id = __pavio_src.split('/');
    id = id[2].split('.');
    id = id[0];
    $.getJSON('codes/pavio/pavio.php', {
     id: id,
     comment: $('.pavio .input').attr('value')
    }, function(data){
     if(data.code == '100') {
      var comment = $('<div>').attr('id', 'comment' + data.comment.id).addClass('comment').css('display', 'none');
      $('<p>').addClass('entry').html(data.comment.entry).appendTo(comment);
      $('<p>').addClass('metainfo').html(data.comment.dateformat).appendTo(comment);
      $(comment).prependTo('#pavio .comments_box').slideDown('slow');
      $('.pavio .input').attr('value', '');
     } else alert(data.message);
    });
    return false;
   }).appendTo(comments);
   $('<div>').addClass('comments_box').appendTo(comments);


   // Výstup
   $(pattern).appendTo(this);
   $(comments).appendTo(this);
   __pavio_comments();
  });
  
  
  // Bindujeme
  $(document).bind('keydown', __pavio_bind);
  
  // Končíme, nejdeme na obrázok
  return false;
 });
});