<?php
if (!defined ('in')) exit();
$out = HeadIfPost ($translate['manual']);

if (!isset ($_GET['old']))
$out .= '<table cellspacing="25px" width="100%"><tr><td valign="top" width="400px">
<h2>' . $translate['manual.video'] . '</h2>
<p>' . $translate['manual.video.title'] . '</p>
<object width="400" height="300"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=7836023&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=7836023&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="400" height="300"></embed></object>
</td></tr></table>
<p><a href="admin.php?what=manual&amp;old">&raquo; ' . $translate['manual.old'] . '</a></p>';

else $out .= '<a name="arts"></a>
<h2>Články</h2>
<p>Články v tomto systéme fungujú bežným spôsobom. Na stránke si vytvoríte nejaké kategórie článkov a do nich budete môcť následne vkladať články.</p>

<a name="arts-add-edit"></a>
<h3>Pridať (Upraviť) článok</h3>
<p>Pri pridávaní článkov sa nám tu objavuje zopár vstupných polí a na čo slúžia? Tak <b>Názov článku</b> je vlastne názov článku, pod akým sa bude zobrazovať na stránke. Nižšie nájdete <b>výber kategórií</b>, ku ktorým bude článok priradený. Každý článok môžete priradiť ako k jednej kategórií, tak aj k dvom, či trom súčasne - záleží len Vás, kde všade bude. <b>Nastavenia</b> článku, tak tam nastavuje, či sa má článok zobrazovať na stránke a či sa majú pri tomto článku zobrazovať / pridávať komentáre. Ak chcete zmazať komentáre pridané k článku, tak to urobíte pomocou editácie článku, kde v nastaveniach zaškrtnente políčko <b>odstániť komentáre napísané pri tomto článku</b>. To isté vykonáte
aj keď chcete vynulovať počet prečítaní článku alebo odstrániť hodnotenie článku.</p>
<p>Ak nechcete článok hneď po dopísaní publikovať, tak si ho môžete načasovať, kedy sa článok sám publikuje pomocou políčka, kde určíte dátum a čas pridania.</p>
<p>K článku môžete pochopiteľne vložiť obrázok a to pomocou políčka URL, kde zadáte danú adresu, kde sa obrázok nachádza. Alebo ho môžete vložiť z disku pomocou súboru.</p>
<p>Písanie samotného článku sa delí na 2 časti a ich vyplnenie závisí od toho, aký druh článku chcete napísať. Ak chcete napísať nejaký krátky článok, tak stačí iba ak napíšete <b>Prvú časť článku</b>. Ale ak chcete napísať bežný článok, tak do <b>Prvej časti článku</b> napíšte len niečo ako úvod (popis) článku a samotný článok napíšte do <b>Druhej časti článku</b>.</p>

<a name="arts-delete"></a>
<h3>Mazanie článkov</h3>
<p>Informovanie Vás, či mazanie článku prebehlo úspešne</p>

<a name="options"></a>
<h2>Nastavenia</h2>
<p>V tejto časti administrácie máte možnosť meniť základné nastavenia systému - to ako sa bude systém správať, aké funkcie bude podporovať....</p>

<a name="options-login"></a>
<h3>Prihlasovanie</h3>
<p>Pri týchto nastaveniach určujete pod akým <b>menom</b> a <b>heslom</b> sa je možné prihlásiť do administrácie. Taktiež môžete zapnúť/vypnúť funkciu <b>automatického prihlasovania</b>, ktorá ak je zapnutá, tak pri každej návšteve stránky budete automaticky prihlásený.</p>

<a name="options-site-info"></a>
<h3>O stránke</h3>
<p>V tejto časti máte možnosť meniť základné informácie o stránke ako jej <b>názov</b>, ktorý sa zobrazuje ako titulok stránky (základná lišta prehliadača) a ako hlavný nadpis stránky (väčšinou priamo v logu stránky), či <b>popis</b>, ktorý sa pri väčšine motívoch zobrazuje hneď pod názvom stránky. Máte tu možnosť taktiež možnosť upraviť <b>kľúčové slová</b>, ktoré Vám pomôžu lepšie optimalizovať Vašu stránku pre vyhľadávače (napríklad Google, Yahoo, Live Search, Zoznam...). Máte tu taktiež možnosť vyplniť informácie o Vás ako <b>autora</b> stránky, či Váš <b>mail</b>, ktorý sa bude zobrazovať pri Vami napísaných komentároch. <b>Separátor</b>, to môže byť nejaký špeciálny znak (či slovo, alebo krátky text), pomocou ktorého budú oddelené Názov Stránky a Názov Aktívnej Časti Stránky pri titulky stránky (základná lišta prehliadača). A posledná možnosť, ktorou je <b>Pätička stránky</b> môžete upraviť text, ktorý sa bude vkládať do najspodnejšej časti Vašej stránky. Ak do nej vložíte text <b><kbd>[stats]</kbd></b>, tak tento text bude nahradený aktuálnymi štatistiky systému. Môžete sem vložiť odkaz na naše stránky ako formu poďakovania za to, čo pre Vás robíme.</p>
<p>Pri tejto časti stránky máte možnosť taktiež aj zapnúť / vypnúť jednotlivé časti stránky ako <b>Galéria</b>, <b>Návštevná kniha</b>, <b>Mapa stránky</b> a zoznam <b>RSS</b> feedov.</p>

<a name="options-functions"></a>
<h3>Funkcie</h3>
<p>Ak si chcete <b>motív</b> (template) Vašej stránky, tak to môžete urobiť práve v tejto časti administrácie. Stačí iba vybrať motív, ktorý myslíte a nastavenia iba uložiť. Rovnako je to aj so zmenou <b>wysiwyg</b> editoru (<a href="#wysiwyg">čo to je</a>), či môžete vybrať <b>sekciu</b>, ktorá bude štandartne načítaná pri navštívení úvodu stránky.</p>
<p>Taktieť v tejto časti môžete globálne na celej stránky zapnúť komentáre, či počítadlo prečítaní článku alebo či sa má pri článkoch zobrazovať Vaše meno ako autora článku. Taktiež môžete zapnúť zaznámenavanie štatistík, či povoliť zobrazovanie odkazu na prihlasovanie sa do administrácie v pätičke.</p>
<p>Pri tejto časti máte možnosť aj nastaviť nastavenia aplikácie Pviewer ako napríklad použitú schému, či povolenie komentárov pri obrázkoch alebo napríklad koľko obrázkov sa má zobrazovať v zoznamoch posledných a náhodných obrázkov. Špeciálne nastavenie je veľkosť obrázkov, kde nastavujete, akú šírku má mať obrázok (v pixeloch). Ak obrázok bude širší ako táto hodnota, tak obrázok bude zmenšený a uložený na server do cache. Ak nechcete, aby boli obrázky zmenšované, tak túto hodnotu nastavte na <b>0</b>.</p>
<p>Nastaviť môžete aj názov, ktorý bude používaný na celej stránke a taktiež aj úvodný text, ktorý bude zobrazený pri zozname bleskových správ (mikroblogu).</p>
<p>Poslednou časťou je zapnutie Mod(u) Re-Write. Ako tento modul zapnúť, to sa dozviete na naších stránkach v článku <a href="http://opiner.tatarko.sk/clanok-18-ako-na-mod-re-write.html">Ako na Mod Re-Write</a>.</p>

<a name="options-admin"></a>
<h3>Administrácia</h3>
<p>Pri nastaveniach administrácie môžete nastavovať ako majú byť zoraďované zoznamy v administrácií a taktiež, koľko položiek má obsahovať jedna strana týchto zoznamov a taktiež osobité nastavenie stránkovanie je aj pre detailný prehľad štatistiík.</p>
<p>Môžete si aj upraviť Domovskú stránku administrácie, kde môžete zapnúť / vypnúť jednotlivé časti ako ikonky, RSS feed naších stránok, či zoznam aktualizácií.</p>
<p>Špecialnou položkou je nastavenia RSS feedov, ktoré bude možné čítať cez administráciu. A ako vložiť sem tieto feedy?</p>
<h4>Syntax zoznamu feedov</h4>
<p>Jeden feed vložíme tak, že vložíme jeho adresu a hneď za ňu vložíme znak |, za ktorý napíšeme názov feedu (ktorý bude v menu).</p>
<p>Nasledne ak chceme vložiť viac feedov, tak pri každom použijeme postup vyššie plus jednotlíve feedy oddelíme znakom #.</p>
<strong>Pluginy</strong>
<p>Môžete nastaviť či sa majú pluginy zobrazovať v administrácii alebo či budú vôbec zapnuté. Ak máte nainštalované nejaké pluginy, tak si ich môžete nastaviť ako widgety a pomocou zaškrtnutia určíte, ktoré to majú byť.</p>

<a name="options-limits"></a>
<h3>Limity</h3>
<p>Tu si nastavujete rôzne limity pre dané veci na Vašej stránke. Podľa toho aký bude mať daná vec limit, tak toľko sa bude zobrazovať na Vašej stránke. Napríklad ak nastavíte limit pre Komentáre 10, tak sa pod každým článkom bude zobrazovať maximálne 10 komentárov na jedno stránkovanie.</p>

<a name="options-favicon"></a>
<h3>Ikonka webu</h3>
<p>V tejto časti máte možnosť nahrať na stránku (prípadne odstrániť) ikonku webu, ktorá sa bude používať pri adrese stránky v prehliadači a pri RSS generátore.</p>

<a name="options-favicon"></a>
<h3>Databáza</h3>
<p>Tu si môžete meniť prístupové údaje, pomocou ktorých sa systém pripája k MySQL serveru.</p>

<a name="menubox"></a>
<h2>Správa Menu boxov</h2>
<p>Tu môžete spravovať všetky Menu boxy, ktoré ste pridali. Môžete ich upravovať alebo vymazať.</p>

<a name="menu-add-edit"></a>
<h3>Pridať (Upraviť) box</h3>
<p>Táto funkcia Vám dovoľuje pridať akýkoľvek Menu box do menu na Vašej stránke. Pridať môžete vlastný obsah alebo generovaný. Generovaný obsah znamená, že si môžete vybrať z dvanásť hcm modulov, ktoré sa budú zobrazovať vo Vašom menu na stránke. Vlastný obsah píšete do wysiwyg editora Texyla.</p>

<a name="menu-delete"></a>
<h3>Zmazať box</h3>
<p>V tejto časti máte možnosť zmazať určitý box menu - pri potvrdzovaní iba volíte, čí má byť box skutočne odstránený.</p>

<a name="links"></a>
<h2>Správa odkazov</h2>
<p>Tu môžete spravovať všetky Odkazy, ktoré sa nachádzajú na Vašej stránke. Môžete ich upravovať alebo vymazať. V hranatej zátvorke máte napísané, či sa daný odkaz po kliknutí naň otvorí v novom okne prehliadača alebo v tom istom.</p>

<a name="links-add-edit"></a>
<h3>Pridať (Upraviť) odkaz</h3>
<p>Táto funkcia Vám dovoľuje pridať na Vašu stránku odkaz na inú stránku. Do kolónky „Text odkazu“ napíšete text, ktorý sa bude zobrazovať každému na stránke. Do kolónky „Popisok“ môžete, ale aj nemusíte napísať popis danej stránky, na ktorú chcete odkazovať.</p> 
<p>Adresu odkazu môžete zadať klasicky URL alebo máte možnosť vybrať z vytvorených sekcií redakčného systému, ak potrebujete náhodou odkazovať na tieto sekcie.</p>
<p>V nastavení zvolíte či sa má odkaz zobrazovať v hlavnom menu alebo vo vedľajšom menu ako generovaný obsah menu. Tretia možnosť je vytvoriť na zápis HCM modulu, ktorý môžete vložiť kdekoľvek. Pozícia umožňuje zvoliť si ľubovoľné poradie odkazu a to pomocou ľubovoľného čísla.</p>
<p>A nakoniec nastavíte, či sa má daný odkaz otvoriť v novom okne prehliadača alebo v tom istom.</p>

<a name="links-delete"></a>
<h3>Zmazať odkaz</h3>
<p>Rozdujete, či má byť odkaz skutočne odstránený.</p>

<a name="artcats"></a>
<h2>Kategórie</h2>
<p>Tu spravujete svoje kategórie článkov. Môžete ich upraviť alebo vymazať.</p>

<a name="artcats-add-edit"></a>
<h3>Pridať (Upraviť) kategóriu</h3>
<p>Tu pridávate kategórie článkov. Stačí napísať názov kategórie a zvoliť, či sa má zobrazovať na stránke alebo nie.</p>

<a name="artcats-delete"></a>
<h3>Zmazať kategóriu</h3>
<p>Rozhodujete, či má byť kategória skutočne odstránená</p>

<a name="artcats-move"></a>
<h3>Presúvanie článkov</h3>
<p>V tejto oblasti máte možnosť presunu článkov do inej kategórie. Stačí zadať, aby sa z kategórie „A“ presunuli články do kategórie „B“. Využíva sa to najmä, keď chcete vymazať „A“ kategóriu, tak aby sa Vám články spolu s ňou nevymazali, tak ich jednoducho presuniete do kategórie „B“.</p>

<a name="gallery"></a>
<h2>Galéria</h2>
<p>Tu môžete spravovať Vaše galérie. Môžete do nich pridávať obrázky po kliknutí na zelenú ikonku „+“ alebo pridané kategórie upravovať a vymazávať.</p>

<a name="gallery-add-edit"></a>
<h3>Pridať (Upraviť) kategóriu</h3>
<p>Táto funkcia Vám dovoľuje pridať ľubovoľnú kategóriu obrázkov vo Vašej galérii. Do kolonky „nadpis kategórie“ stačí napísať nadpis kategórie, ktorú chcete pridať a následne jej môžete pridať aj popis. Ak chcete, aby sa zobrazovala na stránke, tak treba zaškrtnúť políčko „Zobrazovať v zoznamoch“. Kategóriu musíte pridať aspoň jednu, pretože bez nej nemôžete pridávať obrázky alebo fotky do Vašej galérie.</p>

<a name="gallery-delete"></a>
<h3>Mazanie obrázkov</h3>
<p>Rozhodujete, či má byť odstránená galéria spolu s obrázkami a komentármi.</p>

<a name="images"></a>
<h3>Obrázky</h3>
<p>V tejto časti máte možnosť prehliadne spravovať obrázky danej galérie.</p>

<a name="images-add-edit"></a>
<h3>Pridávanie (Úprava) obrázkov</h3>
<p>Po kliknutí na zelenú ikonku „+“ v správe kategórii si môžete do danej kategórie pridať ľubovoľné obrázky. Pridávať môžete naraz po troch obrázkov. Najprv si vyhľadáte obrázok uložený na Vašom pevnom disku. Zadáte názov obrázku a môžete aj popis daného obrázku.</p>

<a name="images-delete"></a>
<h3>Mazanie obrázkov</h3>
<p>Rozhodnutie, či sa majú odstrániť vybrané obrázky spolu s komentármi.</p>

<a name="sections"></a>
<h2>Sekcie</h2>
<p>Tu môžete spravovať všetky sekcie, ktoré si vytvoríte a pridáte na Vašu stránku. V hranatej zátvorke máte napísané, či sa daná sekcia zobrazuje v hlavnom menu alebo je použitá ako štartovacia stránka.</p>
<p>V nastaveniach si môžete zvoliť, ktorý sekcia bude ako štartovacia stránka.</p>
<p>Ako štartovaciu sekciu je najlepšie nastaviť "Posledné články", kedy sa Vám na domovskej stránke budú zobrazovať posledné publikované články. Počet si nastavíte v systémových nastaveniach v záložke Limity.</p>


<a name="sections-add-edit"></a>
<h3>Pridať sekciu</h3>
<p>Pridávanie sekcií sa robí vo Vami zvolenom wysiwyg editore. Zvolíte názov sekcie a umiestnite ju najlepšie do Hlavného menu. Ďalej pozícia slúži na určenie poradia danej sekcie, zvolíte číslom. Ak chcete, aby Vám ku danej sekcii mohli čitatelia pridávať komentáre, tak zaškrtnite políčko „Komentáre“.</p>


<a name="sections-delete"></a>
<h3>Mazanie sekcie</h3>
<p>Rozhodnutie, či má byť sekcia a komentáre skutočne odstránená.</p>

<a name="file-manager"></a>
<a name="file-manager-add"></a>
<a name="file-manager-delete"></a>
<h2>Správca súborov</h2>
<p>V tejto časti máte možnosť na stránku nahrávať súbory, ktoré môžete následne pomocu priloženého HCM zápisu zápisu napríklad do článkov, sekcií...</p>
<p>Pri pridávaní súbor vyberáte súbor uložený na disku a pri jeho mazaní sme informovaní, či mazanie súboru prebehlo úspešne.</p>

<a name="polls"></a>
<h2>Ankety</h2>
<p>Tu máte možnosť spravovať ankety, ktoré sa nachádzajú na stránke.</p>

<a name="polls-add"></a>
<h3>Pridať anketu</h3>
<p>Pri pridávaní ankety vyplňujete samotnú anketnú otázku a pridávate odpoveďe. Taktieť je možné zablokovať hlasovanie v ankete, kedy budú zobrazované výsledky ankety priamo každému.</p>

<a name="polls-edit"></a>
<h3>Upraviť anketu</h3>
<p>Pri upravovaní ankety môťete zmeniť znenie anketnej otázky, či počet hlasov u jednotlivých odpovedí, či možnosť zablokovať (odblokovať) hlasovanie v ankete.</p>

<a name="polls-delete"></a>
<h3>Mazanie ankety</h3>
<p>Informovanie o tom, ako dopadlo vymázavanie ankety.</p>

<a name="updates"></a>
<h2>Aktualizácia</h2>
<p>Zoznam zmien u jednotlivých verzií systému vrátane odkazov na download aktualizačných balíčkov (ak existuje novšia verzia, ako máte nainštalovanú).</p>

<a name="stats"></a>
<h2>Štatistiky</h2>
<p>Štatistiky sú rozdelené do dvoch módov a to Detailný a Súhrnný. V detailnom móde máte rozpísané podrobnosti o návštevníkovi. Všetko je rozdelené na dni, mesiace a roky. Ďalej o kedy a kde sa nachádzal návštevník a z akej IP adresy, operačného systému a prehliadača. V súhrnnom móde máte naopak jednoduché zobrazenie návštevnosti, kde ide o dátum návštevy, počet kliknutí na stránke, počet návštev a unikátnych IP adries.</p>';