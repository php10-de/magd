<?php
function dateConv($val)
{
	$val1=date('d.m.Y h:i:s', strtotime($val));
	return $val1;
}
?>