<?php 
    
$modul="ih_repo";

require("inc/req.php");

// Generally for people with the right to edit ih_repo
$groupID = 1002;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'ih/repo.php')) {
    require MODULE_ROOT.'ih/repo.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Ih_repo_id
validate('ih_repo_id', 'int nullable' );

// Url
validate('url', 'string' );

// Username
validate('username', 'string nullable' );

// Password
validate('password', 'string nullable' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM ih_repo WHERE ih_repo_id = " . (int) $_REQUEST['ih_repo_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE ih_repo SET url = "
    .$_VALIDDB['url']
     . ",username = " . $_VALIDDB['username']
     . ",password = " . $_VALIDDB['password']
    . " WHERE ih_repo_id = " . (int) $_REQUEST['ih_repo_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after ih_repo update ***/
    
    } else {
    
	    $sql = "INSERT INTO ih_repo(ih_repo_id, url, username, password) VALUES("
    .$_VALIDDB['ih_repo_id']
    . ",
	" . $_VALIDDB['url']
    . ",
	" . $_VALIDDB['username']
    . ",
	" . $_VALIDDB['password']
    . ") ";
        require MODULE_ROOT . 'ih/repo_insert.inc.php';
        $_VALID['ih_repo_id'] = mysqli_insert_id($con);
        /*** after ih_repo insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'ih_repo_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'ih_repo_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'ih_repo_id' => $_VALID['next_id']];
    } else {
        $loc = 'ih_repo.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['ih_repo_id']) {
	$sql = "SELECT * FROM ih_repo WHERE ih_repo_id = " . (int) $_REQUEST['ih_repo_id'];
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
$n4a['ih_repo.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Ih_repo')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left hidden"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formih_repo" name="formih_repo" method="post" class="formLayout" >
<?php if($_REQUEST['ih_repo_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'ih_repo.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['ih_repo_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;ih_repo_id='.$prevEntry[$modul.'_id'].'"><i class="fa fa-chevron-left" title="' . ss('Previous') . '"></i></a>';
    } else {
        echo '&nbsp;<span style="margin:8px">&nbsp;</span>';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;ih_repo_id='.$nextEntry[$modul.'_id'].'"><i class="fa fa-chevron-right" title="' . ss('Next') . '"></i></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Ih_repo')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['url']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="url"><?php echo ss('Url')?></label><div class="col-md-6">
<input class="form-control" type="text" name="url" id="url" value="<?php echo ss($_VALID['url'])?>" required="required" /></div>
<?php if (isset($error['url'])){ echo '<span class="help-block text-danger">'; echo $error['url'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['username']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="username"><?php echo ss('Username')?></label><div class="col-md-6">
<input class="form-control" type="text" name="username" id="username" value="<?php echo ss($_VALID['username'])?>" /></div>
<?php if (isset($error['username'])){ echo '<span class="help-block text-danger">'; echo $error['username'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['password']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="password"><?php echo ss('Password')?></label><div class="col-md-6">
<input class="form-control" type="text" name="password" id="password" value="<?php echo ss($_VALID['password'])?>" /></div>
<?php if (isset($error['password'])){ echo '<span class="help-block text-danger">'; echo $error['password'] . ''; echo '</span>';}?></div>
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
<!-- after ih_repo detail form -->
<?php
require("inc/footer.inc.php"); 
    ?>