<?php 

$modul="cache";

require("inc/req.php");

// Generally for people with the right to edit cache
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'cache/cache.php')) {
    require MODULE_ROOT.'cache/cache.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Cache_id
validate('cache_id', 'int nullable' );

// Url
validate('url', 'string' );

// Updated
validate('updated', 'string nullable' );

// Active
validate('active', 'int nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM cache WHERE cache_id = " . (int) $_VALID['cache_id'];
	/*** Before Delete ***/
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM cache WHERE cache_id = " . (int) $_REQUEST['cache_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE cache SET url = "
    .$_VALIDDB['url']
     . ",updated = " . $_VALIDDB['updated']
     . ",active = " . $_VALIDDB['active']
    . " WHERE cache_id = " . (int) $_REQUEST['cache_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after cache update ***/
    
    } else {
        /*** before cache insert ***/
	    $sql = "INSERT INTO cache(cache_id, url, updated, active) VALUES("
    .$_VALIDDB['cache_id']
    . ",
	" . $_VALIDDB['url']
    . ",
	" . $_VALIDDB['updated']
    . ",
	" . $_VALIDDB['active']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['cache_id'] = mysqli_insert_id($con);
        /*** after cache insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'cache_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'cache_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'cache_id' => $_VALID['next_id']];
    } else {
        $loc = 'cache.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['cache_id']) {
	$sql = "SELECT * FROM cache WHERE cache_id = " . (int) $_REQUEST['cache_id'];
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
$n4a['cache.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Cache')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formcache" name="formcache" method="post" class="formLayout" >
<?php if($_REQUEST['cache_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'cache.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['cache_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;cache_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;cache_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Cache')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['url']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="url"><?php echo ss('Url')?></label><div class="col-md-6">
<input class="form-control" type="text" name="url" id="url" value="<?php echo ss($_VALID['url'])?>" required="required" /></div>
<?php if (isset($error['url'])){ echo '<span class="help-block text-danger">'; echo $error['url'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['updated']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="updated"><?php echo ss('Updated')?></label><div class="col-md-6">
<input class="form-control" type="text" name="updated" id="updated" value="<?php echo ss($_VALID['updated'])?>" /></div>
<?php if (isset($error['updated'])){ echo '<span class="help-block text-danger">'; echo $error['updated'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['active']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="active"><?php echo ss('Active')?></label><div class="col-md-6"><div >
                    <input type="text" class="form-control" name="active" id="active" value="<?php echo $_VALID['active']?>" /><label for="active"></label></div></div>
<?php if (isset($error['active'])){ echo '<span class="help-block text-danger">'; echo $error['active'] . ''; echo '</span>';}?></div>
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
<!-- after cache detail form -->
<?php
require("inc/footer.inc.php");