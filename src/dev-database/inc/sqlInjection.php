<?php
function sql_injection($val)
{
	$val=mysqli_real_escape_string($con, stripslashes($val));
	//echo "<br>".$val."<br>";
	return $val;
}
?>