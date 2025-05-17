<?php
$modul="gr";

require("inc/req.php");

validate("shortname","string");
validate("longname","string nullable");
validate("id","int");
validate('i', 'int');
$id = $_VALID['id'];

/*** Rights ***/
// Generally for people with right do manage groups
RR(2);
// Admin group for Administrators only
if ($id == 1) {
    GRGR(1);
}

if (!$id) {
    if(isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "INSERT INTO gr(shortname, longname)
                    VALUES (".$_VALIDDB['shortname'].",".$_VALIDDB['longname'].")";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);
            if ($id) {
                foreach ($_VALID as $key => $value) {
                    $data[$key] = $value;
                }
            }
            $_SESSION[$modul]['rl'] = true;
            header('Location: gr.php?ok=Done');
            exit;
        }
    }
} else {
    if (isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "UPDATE gr SET shortname=".$_VALIDDB['shortname'].",
                longname = ".$_VALIDDB['longname']." WHERE gr_id=".$id;
            mysqli_query($con, $sql);
            $_SESSION[$modul]['rl'] = true;
            header('Location: gr.php?ok=Done');
            exit;
        }

    }
    $sql = "SELECT * FROM gr WHERE gr_id=".$id . " LIMIT 0,1";
    $res = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($res);
    
}

if ($id) {
    // Rechte Ergebnis aufbauen ------- //
    $sql="SELECT r.right_id, r.shortname, r.longname, (
            SELECT yn
            FROM right2gr
            WHERE right2gr.right_id = r.right_id
            AND gr_id = ".$id."
          ) AS yn FROM r
          WHERE 1
          ORDER BY shortname";
    $listResult=mysqli_query($con, $sql);
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_VALID['submitted'])) {
    foreach ($_VALID as $key => $value) {
        if (isset($data[$key])) $data[$key] = $value;
    }
}

$n4a['gr.php'] = ss('Back to group list');
require("inc/header.inc.php");
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
                <?php ss($data['shortname']) ?>
            </li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal form-bordered formLayout" name="form<?php echo $modul?>">
            <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = '<?php echo $modul?>.php'"></a>
                        <?php
                        if ($id) {
                            /*** Pagination ***/
                            if ($_REQUEST['user_id']) {
                                $pageResult = memcacheArray($_SESSION[$modul]['sql']);
                                $prevEntry = $pageResult[$_VALID['i']-1];
                                if ($prevEntry) {
                                    echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;user_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
                                } else {
                                    echo '';
                                }

                                $nextEntry = $pageResult[$_VALID['i']+1];
                                if ($nextEntry) {
                                    echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;user_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
                                }
                            }
                        }?>
                        <a href="#" class="fa fa-caret-down"></a>
                    </div>

                    <h2 class="panel-title"><?php echo sss($data['shortname']); ?></h2>
                </header>
                <div class="panel-body">
                    <?php if($_VALID['id']) {
                        echo '<input type="hidden" name="id" value="'.$_VALID['id'].'">';
                    }?>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="shortname"><?php echo ss('Shortname')?></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="shortname" id="shortname" value="<?php echo sss($data['shortname'])?>" required="required" />
                        </div>
                        <?php if (isset($error['shortname'])){ echo '<span class="help-block text-danger">'; echo $error['shortname'] . ''; echo '</span>';}?>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label" for="longname"><?php echo ss('Description')?></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="longname" id="longname" value="<?php echo sss($data['longname'])?>" />
                        </div>
                        <?php if (isset($error['longname'])){ echo '<span class="help-block text-danger">'; echo $error['longname'] . ''; echo '</span>';}?>
                    </div>
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">
                            <input type="hidden" name="submitted" value="submitted">
                            <input type="submit" class="btn btn-primary" id="submit" value="<?php echo ss('Save')?>">
                        </div>
                    </div>
                </footer>
            </section>
        </form>
    </div>
</div>
<?php 
if ($listResult && $listResult->num_rows > 0) {
    ?>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo sss('Group Rights'); ?></h2>
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
        while($row=mysqli_fetch_array($listResult)) {
            echo '<tr class="dotted" id="'.$row['right_id'].'">';
            echo '<td align="right">'. (($row['yn'])?'<i  style="font-size:20px;" class="fa fa-check-square-o tick"></i>':'<i style="font-size:20px;" class="fa fa-square-o tick"></i>') .'</td>';
            echo '<td width="470">' . $row['shortname'].(($row['longname'])?' (' . $row['longname'].')':'').'</td>';
            echo '</tr>';
        }?>
                </tbody>
            </table>
        </div>
    </section>
    <script>
    $(document).ready(function(){

        $(".tick").click(function(){
            if($(this).hasClass("fa-square-o")) {
                $(this).removeClass("fa-square-o");
                $(this).addClass("fa-check-square-o");
                right = 1;
            }
            else{
                $(this).addClass("fa-square-o");
                $(this).removeClass("fa-check-square-o");
                right = 0;
            }
            $.post("a/right2gr_edit.php?action=right&yn="+right, { right_id: $(this).parent().parent().attr("id"), id: <?php echo $id ?> });
            });
    });
    </script>
<?php } ?>
</div>
<?php
require("inc/footer.inc.php");
?>