<?php 
    
$modul="ih_branch";

require("inc/req.php");

// Generally for people with the right to edit ih_branch
$groupID = 1002;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'ih/branch.php')) {
    require MODULE_ROOT.'ih/branch.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Name
validate('name', 'string nullable' );

// Is_active
validate('is_active', 'ckb' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM ih_branch WHERE ih_repo_id = '" . $_REQUEST['ih_repo_id'] . "' AND name = '" . $_REQUEST['name'] . "'";
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE ih_branch SET is_active = "
    .$_VALIDDB['is_active']
    . " WHERE ih_repo_id = '" . $_REQUEST['ih_repo_id'] . "' AND name = '" . $_REQUEST['name'] . "'";
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after ih_branch update ***/
    
    } else {
    
	    $sql = "INSERT INTO ih_branch(name, is_active) VALUES("
    .$_VALIDDB['name']
    . ",
	" . $_VALIDDB['is_active']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['name'] = mysqli_insert_id($con);
        /*** after ih_branch insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'ih_branch_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'ih_branch_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'ih_branch_id' => $_VALID['next_id']];
    } else {
        $loc = 'ih_branch.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['ih_repo_id'] AND $_REQUEST['name']) {
	$sql = "SELECT * FROM ih_branch WHERE ih_repo_id = '" . $_REQUEST['ih_repo_id'] . "' AND name = '" . $_REQUEST['name'] . "'";
	$data = mysqli_fetch_assoc(mysqli_query($con, $sql));
    foreach ($data as $key => $value) {
        $_VALID[$key] = $value;
    }
}
// manuelle Eingabe überschreibt DB-Werte
if (isset($_REQUEST['submitted'])) {
    foreach ($_VALID as $key => $value) {
        $_VALID[$key] = $value;
    }
}
$n4a['ih_branch.php'] = ss('Back to List');
require("inc/header.inc.php");

if ($error) {
	$headerError = implode('<br>', $error);
}
?><header class="page-header">
    <!--    <h2>Title</h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('Ih_branch')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left hidden"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formih_branch" name="formih_branch" method="post" class="formLayout" >
<?php if($_REQUEST['name']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'ih_branch.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['name']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;name='.$prevEntry[$modul.'_id'].'"><i class="fa fa-chevron-left" title="' . ss('Previous') . '"></i></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;name='.$nextEntry[$modul.'_id'].'"><i class="fa fa-chevron-right" title="' . ss('Next') . '"></i></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Ih_branch')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['name']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="name"><?php echo ss('Name')?></label><div class="col-md-6">
<input class="form-control" type="text" name="name" id="name" value="<?php echo ss($_VALID['name'])?>" required="required" /></div>
<?php if (isset($error['name'])){ echo '<span class="help-block text-danger">'; echo $error['name'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['is_active']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="is_active"><?php echo ss('Is_active')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                            <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($_VALID['is_active'] OR !$_VALID['submitted'])?'checked="checked"':''?> /><label for="is_active"></label></div></div>
<?php if (isset($error['is_active'])){ echo '<span class="help-block text-danger">'; echo $error['is_active'] . ''; echo '</span>';}?></div>
                <input type="hidden" name="submitted" value="submitted">
                <input type="hidden" name="i" value="<?php echo $_VALID['i']+1?>">
                <input type="hidden" name="next_id" value="<?php echo $nextEntry[$modul.'_id']?>">
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">                        
                            <input type="submit" class="btn btn-primary"  id="submit" value="<?php echo ss('Submit')?>"/>
                            <input type="submit" class="btn btn-default" id="submit_new" name="submit_new" value="<?php echo ss('Submit & New')?>"/>
                            <input type="submit" class="btn btn-success" id="submit_next" name="submit_next" value="<?php echo ss('Submit & Next')?>" />
                        </div>
                    </div>
                </footer>
            </form>
            
        </section>
    </div>
</div>
<!-- after ih_branch detail form -->
<?php
require("inc/footer.inc.php"); 
    ?>