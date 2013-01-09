<?php
if (!defined ('in')) exit ();



/* Informácie o editori */

$WysiwygInfo = array (
	'name' => 'Texyla',
	'version' => '0.4.3.4',
	'built-for' => '0012008092501',
);



/* Pracovanie s editorom */

if (isset ($this)) {
$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="' . $this->dir . 'texyla.js"></script>'));
$return = '<textarea id="' . $this->name . '" name="' . $this->name . '" rows="25">' . $this->text . '</textarea>
<script type="text/javascript">
	options = Texyla.configurator.admin ("' . $this->name . '");
	options.editorWidth = 800;
	options.toolbar = [
		"h1", "h2", "h3", "h4", null,
		"bold", "italic", "del", null,
		"center", ["left", "right", "justify"], null,
		"ul", "ol", "blockquote", ["sub", "sup", "acronym", "symbol"], null,
		"link", "img", "emoticon", "table", null,
		"notexy", "html", "code", ["code_html", "code_css", "code_js", "code_php", "code_sql"]
	];
	options.symbols = [["&", "&amp;"], ["©", "&copy;"], ["<", "&lt;"], [">", "&gt"]];
	options.submitButton = false;
	new Texyla (options);
</script>';
};