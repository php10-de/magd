<?php
$modul="r";

require("inc/req.php");

validate("shortname","string");
validate("longname","string nullable");
validate("id","int");
$id = $_VALID['id'];

/*** Rights ***/
// Generally for people with right do change rights
RR(4);

if (!$id) {
    if(isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "INSERT INTO r(shortname, longname)
                    VALUES (".$_VALIDDB['shortname'].",".$_VALIDDB['longname'].")";
            $res = mysqli_query($con, $sql);
            $id = mysqli_insert_id($con);
            if ($id) {
                foreach ($_VALID as $key => $value) {
                    $data[$key] = $value;
                }
            }
            header('Location: r.php?ok=Done');
        }
    }
} else {
    if (isset($_REQUEST['submitted'])) {
        if (!$_VALID['shortname']) {
            $headerError = ss('Some mandatory fields are missing');
        } else {
            $sql = "UPDATE r SET shortname=".$_VALIDDB['shortname'].",
                longname = ".$_VALIDDB['longname']." WHERE right_id=".$id;
            mysqli_query($con, $sql);
            header('Location: r.php?ok=Done');
        }

    }
    $sql = "SELECT * FROM r WHERE right_id=".$id . " LIMIT 0,1";
    $res = mysqli_query($con, $sql);
    $data = mysqli_fetch_array($res);
    
}

// manuelle Eingabe überschreibt DB-Werte
if (isset($_VALID['submitted'])) {
    foreach ($_VALID as $key => $value) {
        if (isset($data[$key])) $data[$key] = $value;
    }
}

$n4a['r.php'] = ss('Back to rights list');
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
                <?php sss('Right')?>
            </li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal form-bordered" name="form<?php echo $modul?>" class="formLayout">
            <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
                        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'r.php'"></a>
                        <?php
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
                        }?>
                    </div>

                    <h2 class="panel-title"><?php sss('Right')?></h2>
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
<?php require("inc/footer.inc.php"); ?>