# Opiner CMS
#### 1.7 Lorriene

## Obsah

- O projekte
- Požiadavky na server
- Inštalácia
- Často kladené otázky - FAQ
- Licencia
- Odkazy

## O projekte

Open Source redakèný systém pre tvorbu webových stránok pre každého. Pomocou tohto projektu, ktorý je vyvíjaný mladým tímom vytvoríte modernú webovú stránku rýchlo, jednoducho, no najmä zadarmo. Opiner ako slovo samotné znamená "prednies svoj názor, mienku", tak nechajte svoje názory preniknú do sveta dnešného internetu pomocou redakèného systému Opiner CMS.
Viac informácií, veci na stiahnutie, návody a mnoho iného nájdeš na oficiálnych stránkach tohto projektu na adrese: http://opiner.tatarko.sk

## Požiadavky na server

- PHP verzie 5.3 a vyššej
- MySQL verzie 5.0 a vyššej
- pri používaní galérie je potrebná knižnica GD2

## Inštalácia

- Pomocou FTP klienta nakopírujte všetky súbory z tohto priečinku na Váš server.
- Na súbor _config.php nastavte prístupové práva (chmod) na 777.
- Pomocou prehliadača prejdite na adresu Vašej stránky.
- Postupujte podľa krokov inštalácie.

## Často kladené orázky - faq

Otázka: Nejde mi prida obrázky do galérie, súbory na download, nefungujú gravatari...

Odpoveď:

- Pomocou FTP klienta sa prihláste na Váš server a prejdite na priečinok /store
- Na každý priečinok, ktorý sa tu nachádza nastavte prístupové práva (chmod) na 777
- To isté urobte aj na prieèinok /admin/remote/schemas.
- Ak aj napriek tomuto kroku nefungujú, napíšte nám na: opiner@tatarko.sk

Otázka: Chcem zapnú Mod Re-Write, no v administracií to nejde

Odpoveď:

- Najprv si overte, že Váš server tento mód podporuje
- Pomocou FTP klienta sa prihláste na Vašu stránku do jej domovského priečinku
- Súbor default.htaccess premenujte na .htaccess
- Prejdite do administrácie Vašej stránky do časti Nastavenia > Funkcie
- Zaškrtnite políčko Mod Re-Write a uložte nastavenia
- Systém už začne generovať odkazy pomocou spomínaného Mod-u Re-Write

## Licenia

Tento projekt – redakčný systém Opiner CMS a všetky jeho oficiálne súčasti sú šírené myšlienkou Open Source, teda slobodného software a to pod verejnou licenciou GNU/GPL. V praxi to znamená, že tento produkt môžete slobodne používať, rozšírovať medzi ľudmi, na internete a dokonca aj upraviť zdrojové kódy a upravenú verziu šíriť na internete ako svoje dielo – avšak je potrebné uviesť pôvodného autora / dielo (Ovalio / Opiner CMS).

## Odkazy

- Oficiálne stránky systému: http://opiner.tatarko.sk
- Komunita okolo Opiner CMS - Opiner Friends: http://friends.tatarko.sk
- Zákulisné informácie o vývoji: http://tatarko.sk