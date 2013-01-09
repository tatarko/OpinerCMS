<?php

/*--- Ošetrenie vstupných dát ---*/
function adjust ($string, $int = false) {
	if ($int === true) return intval ($string);
	else if (!is_numeric ($string)) {
		if (@function_exists ('mysql_real_escape_string')) return mysql_real_escape_string ($string);
		else if (@function_exists ('mysql_escape_string')) return mysql_escape_string ($string);
		else return addslashes ($string);
	} else return $string;
};

?>