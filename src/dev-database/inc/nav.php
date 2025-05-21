<?php
$modul="nav";

require("inc/req.php");

/*** Rights ***/
// Generally for people in the management group
GRGR(6);

$n4a['nav_d.php'] = ss('Add menu entry');
require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT * FROM nav WHERE 1=1";

/*** Filter ***/

/*** Order By ***/
$sql .= " ORDER BY name";
$listResult=mysqli_query($con, $sql);
$num_rows = mysqli_num_rows($listResult);

?>
<div class="contentheadline"><?php sss('Group')?></div>
<br>
<div class="contenttext">
<table cellspacing="0" cellpadding="0" class="bw">
<?php
while($row=mysqli_fetch_array($listResult)) {
    echo '<tr class="dotted" id="tr_'.$row['gr_id'].'">';
    echo '<td width="470">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
    echo '<td align="right"><a href="gr_d.php?id='.$row['gr_id'].'"><i class="fa fa-pencil" title="' . ss('Edit') . '"></i></a>&nbsp;<a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow('.$row['gr_id'].');"><i class="fa fa-trash-o" title="'. ss('Delete').'"></i></a></td>';
    echo '</tr>';
}?>

</table>
</div>
<?php
require("inc/footer.inc.php");

?>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/gr_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>