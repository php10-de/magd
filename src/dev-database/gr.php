<?php
$modul="gr";

require("inc/req.php");
validate('user_id','int');

/*** Rights ***/
// Generally for people with right to manage groups
RR(2);

$n4a['gr_d.php'] = ss('Add group');
$headless = (isset($_REQUEST['headless']))?true:false;
if (!$headless) require("inc/header.inc.php");

// Ergebnis aufbauen ------- //
$sql="SELECT gr.gr_id, gr.shortname, gr.longname";
if ($_VALID['user_id']) {
    $sql .= ", (
            SELECT 1
            FROM user2gr
            WHERE user2gr.user_id = ".$_VALID['user_id']."
                AND user2gr.gr_id = gr.gr_id
          ) AS yn";
}
$sql .= " FROM gr";


/*** Filter ***/
// Admin group for Administrators only
if (!GR(1)) {
    // Admin group for admins only
    $where[] = "gr.gr_id != 1";
}
$sql .= ' where ' . (($where) ? implode(" AND ", $where) : "1=1");

/*** Order By ***/
$sql .= " ORDER BY gr.shortname";
$_SESSION[$modul]['sql'] = $sql;
$listResult = getMemCache($sql);
// refresh memcache after saving (ok) or deleting (rl)
if (!$listResult || isset($_SESSION[$modul]['rl'])) {
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
                <?php ss('Group')?>
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

        <h2 class="panel-title"><?php echo ss('Group'); ?></h2>
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
            foreach ($listResult as $index => $row) {
                echo '<tr class="dotted" id="tr_'.$row['gr_id'].'">';
                  if (!$_VALID['user_id']) {
                echo '<td align="right" class="text-center actions-hover actions-fade" style="width:8%">
                    <span style="padding-left:5px;padding-right:5px;">
                    <a href="gr_d.php?id='.$row['gr_id'].'" title="'.ss('Edit').'" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil" ></i></a>&nbsp;
                    <a href="#" onclick="if (confirm(\''.ss('Do you really want to delete it?').'\')) delRow('.$row['gr_id'].');" title="'.ss('Delete').'" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-trash-o text-danger" ></i></a>
                    </span> 
                    </td>';
              } else {

                echo '<td align="right" style="width:1%;">
                    <span id="grtick_'.$row['gr_id'].'" onClick="ynGr('.$row['gr_id'].')">
                        '. (($row['yn'])?'<i style="font-size:20px" class="fa  fa-check-square-o tick"></i>':'<i style="font-size:20px" class="fa fa-square-o tick"></i>') .'
                    </span>
                     </td>';
              }
                echo '<td width="470" '.$mouseover.' onClick="location.href=\''.$modul.'_d.php?i='.$index.'&amp;id='.$row[$modul.'_id'].'\'">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
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
          url: 'a/gr_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php
require("inc/footer.inc.php");
}
?>