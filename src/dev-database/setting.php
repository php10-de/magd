<?php
$modul="settings";

require("inc/req.php");

/*** Rights ***/
// For Technicians only
GRGR(3);

if (isset($_POST['sent'])) {
    foreach ($_REQUEST['s'] as $key=>$value) {
        $sql = "UPDATE setting SET value = '".my_sql($value) . "' WHERE id='".my_sql($key)."' AND gr_id IN (".implode(',', $_SESSION['GROUP']).")";
        mysqli_query($con, $sql) or die(mysqli_error());
    }
}

// Ergebnis aufbauen und cachen ------- //
$sql="SELECT id, value FROM setting
      WHERE 1
      ORDER BY id DESC";
$listResult=mysqli_query($con, $sql);

// Cache
$s = '<?php ';
while ($row = mysqli_fetch_row($listResult)) {
    $setting[$row[0]] = $row[1];
    $s .= 'define(\''.$row[0].'\',\''.$row[1].'\'); ';
}
$s .= ' ?>';
file_put_contents('inc/settings.inc.php', $s);


// Ergebnis Gruppen-Rechte abhängig aufbauen ------- //
/*$sql="SELECT s.id, s.value FROM setting s
      WHERE 1=1";

/*** Filter ***/

/*** Order By **
$sql .= " ORDER BY id DESC";
$listResult=mysqli_query($con, $sql);
*/
$n4a['setting_d.php'] = ss('Add setting');
require("inc/header.inc.php");
?>
<style>
    .c-pointer{
        cursor: pointer !important;
    }
</style>
<header class="page-header">
    <!--    <h2></h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="start.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
        </ol>

        <a class="sidebar-right-toggle"><i class="fa fa-chevron-left hide"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php echo sss('Settings'); ?></h2>
            </header>
            <div class="panel-body">
                <form class="form-horizontal form-bordered" id="formsettings" method="POST">
                    <?php if($err!="") { ?>
                        <div class="alert alert-danger">
                            <strong><?php echo $err; ?></strong>
                        </div>
                    <?php } ?>
                    <?php foreach ($setting as $key => $value) { ?>
                        <div class="form-group" id="tr_<?php echo html($key); ?>">
                            <label class="col-md-3 control-label"><?php echo html($key); ?></label>
                            <div class="col-md-6">
                                <div class="input-group mb-md">
                                    <input type="text" name="s[<?php echo html($key); ?>]" value="<?php echo htmlentities($value); ?>" class="form-control">
                                    <span  title="<?php echo ss('Delete'); ?>" onclick="if (confirm('<?php echo ss('Do you really want to delete it?'); ?>')) delRow('<?php echo html($key); ?>');" class="input-group-addon btn-danger c-pointer"><i class="fa fa-trash-o"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group" id="tr_<?php echo html($key); ?>">
                        <label class="col-md-3 control-label">&nbsp;</label>
                        <div class="col-md-6">
                            <a href="setting_d.php" class="btn btn-info"><i class="fa fa-plus"></i> <?php echo sss('Add setting');?></a>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button onClick="$('#formsettings').submit();" class="btn btn-success"><i class="fa fa-floppy-o listmenuicon" title="<?php echo sss('Save');?>"></i> <?php echo sss('Save');?></button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </div>
                </div>
            </footer>
        </section>
    </div>
</div>

<script type="text/javascript">
    function delRow(pk) {
        $.ajax({
          url: 'a/setting_del.php?id='+pk
        });
        $('#tr_'+pk).hide();
    }
</script>
<?php require("inc/footer.inc.php"); ?>