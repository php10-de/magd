<?php
$modul="r";

require("inc/req.php");
validate('user_id','int');

/*** Rights ***/
// Generally for people with right to manage groups
RR(2);

$n4a['r_d.php'] = ss('Add right');
$headless = (isset($_REQUEST['headless']))?true:false;
if (!$headless) require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT r.right_id, r.shortname, r.longname";
if ($_VALID['user_id']) {
    $sql .= ", (
            SELECT yn
            FROM right2user
            WHERE right2user.user_id = ".$_VALID['user_id']."
                AND right2user.right_id = r.right_id
          ) AS yn, (
            SELECT max(yn)
            FROM right2gr
            WHERE right2gr.gr_id IN (
                SELECT gr_id FROM user2gr 
                    WHERE user2gr.user_id = ".$_VALID['user_id']."
                )
                AND right2gr.right_id = r.right_id
                GROUP BY right2gr.right_id
          ) AS gr_yn";
}
$sql .= " FROM r";



/*** Filter ***/

/*** Order By ***/
$sql .= " ORDER BY r.shortname";
$listResult = getMemCache($sql);
// refresh memcache after saving (ok) or deleting (rl)
$rl = isset($_SESSION[$modul]['rl']) || isset($_GET['ok']);
if (!$listResult || $rl) {
    $r = mysqli_query($con, $sql) or die(mysqli_error());
    unset($listResult);
    while($row=mysqli_fetch_array($r))
        $listResult[]=$row;
    if ($memcache) {
        setMemCache($sql, $listResult);
    }
    unset($_SESSION[$modul]['rl']);
}

if (!$headless) {
?>
<header class="page-header">
<!--    <h2>Pricing Tables</h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="start.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li>
                <?php sss('Rights')?>
            </li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('Rights'); ?></h2>
    </header>
<div class="panel-body">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="mb-md text-left">
                <!-- Here goes nothing -->
            </div>
        </div>
        <div class="col-sm-12 col-md-6">

        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-none table-hover bw" id="datatable-master">
            <tbody>
<?php
}
foreach ($listResult as $row) {
    echo '<tr class="dotted" id="tr_' . $row['right_id'] . '">';
    if (!$_VALID['user_id']) {
        echo '<td  class="text-center actions-hover actions-fade" style="width:8%">
            <span style="padding-left:5px;padding-right:5px;">
        <a href="r_d.php?id=' . $row['right_id'] . '"  title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil"></i></a>&nbsp;
        <a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete it?') . '\')) delRow(' . $row['right_id'] . ');" data-toggle="tooltip" data-placement="top" title="' . ss('Delete') . '"><i style="font-size:20px" class="fa fa-trash-o text-danger"></i></a>
         </span></td>';
    } else {
        $yn = (isset($row['yn'])) ? $row['yn'] : $row['gr_yn'];
        echo '<td align="right" style="width:1%;">
        <span style="display:' . ((isset($row['yn']) AND ( $row['gr_yn'] != $row['yn'])) ? 'inline' : 'none') . ';" class="' . (($yn) ? 'green' : 'red') . '">!&nbsp;</span>
        <span id="rtick_' . $row['right_id'] . '" onClick="ynR(' . $row['right_id'] . ', ' . (int) $row['gr_yn'] . ')" style="padding-left:5px;padding-right:5px;">
            ' . (($yn) ? '<i style="font-size:20px"  class="fa  fa-check-square-o tick"></i>' : '<i style="font-size:20px" class="fa fa-square-o tick"></i>') . '
        </span>
         </td>';
    }
    echo '<td width="470">' . $row['shortname'] . (($row['longname']) ? ' (' . $row['longname'] . ')' : '') . '</td>';
    echo '</tr>';
}

if (!$headless) {?>
            </tbody>
            </table>
        </div>
    </section>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/r_del.php?rn&id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php
require("inc/footer.inc.php");
}
?>