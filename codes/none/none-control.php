<?php
if (!defined ('in')) exit ();



/* Informácie o editori */

$WysiwygInfo = array (
	'name' => 'Žiaden',
	'version' => '',
	'built-for' => '0012008092501',
);



/* Pracovanie s editorom */

if (isset ($this)) {
$return = '<textarea name="' . $this->name . '" rows="15" style="width: 100%;">' . $this->text . '</textarea>';
};