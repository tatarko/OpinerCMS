<?php


$hours = date("H", time());

if ($hours>=00) { $greeting="Vítám Vás do nového dňa!"; }
if ($hours>=4) { $greeting="Dobré ráno"; }
if ($hours>=10) { $greeting="Krásne dopoludnie"; }
if ($hours>=12) { $greeting="Je čas obeda, teda... Bon apetit!";
}
if ($hours>=13) { $greeting="Dobré popoludnie"; }
if ($hours>=17) { $greeting="Príjemný podvečer"; }
if ($hours>=20) { $greeting="Dobrý večer"; }
if ($hours>=22) { $greeting="Je čas spať"; }



$out = $greeting;
?>
