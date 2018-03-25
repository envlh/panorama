<?php

function is_id($value) {
	return preg_match('/^[1-9][0-9]*$/', $value);
}

function nf_dec($value, $decimals = 2) {
	return number_format($value, $decimals, ',', ' ');
}

?>