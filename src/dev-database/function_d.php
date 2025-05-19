<?php 

$modul="function";

require("inc/req.php");

// Generally for people with the right to edit function
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'function/function.php')) {
    require MODULE_ROOT.'function/function.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Ai
validate('ai', 'int' );

// Name
validate('name', 'string' );

// Description
validate('description', 'string' );

// Required
validate('required', 'string nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `function` WHERE function_id = " . (int) $_REQUEST['function_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'function/function.delete.php')) {
        require MODULE_ROOT.'function/function.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['function_id']) {
            $listUpdateSql = "UPDATE `function` SET `" . $key . "`=" . $value . " WHERE function_id = " . (int) $_REQUEST['function_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `function` WHERE function_id = " . (int) $_REQUEST['function_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `function` SET ai = "
    .$_VALIDDB['ai']
     . ",name = " . $_VALIDDB['name']
     . ",description = " . $_VALIDDB['description']
     . ",required = " . $_VALIDDB['required']
    . " WHERE function_id = " . (int) $_REQUEST['function_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after function update ***/
    
    } else {
        /*** before function insert ***/
	    $sql = "INSERT INTO `function`(ai, name, description, required) VALUES("
    .$_VALIDDB['ai']
    . ",
	" . $_VALIDDB['name']
    . ",
	" . $_VALIDDB['description']
    . ",
	" . $_VALIDDB['required']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['function_id'] = mysqli_insert_id($con);
        /*** after function insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'function_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'function_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'function_id' => $_VALID['next_id']];
    } else {
        $loc = 'function.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['function_id']) {
	$sql = "SELECT * FROM `function` WHERE function_id = " . (int) $_REQUEST['function_id'];
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
$n4a['function.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Function')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formfunction" name="formfunction" method="post" class="formLayout" >
<?php if($_REQUEST['function_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'function.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['function_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;function_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;function_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Function')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="ai-form-group" class="form-group <?php echo (isset($error['ai']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="ai">
	<?php echo ss('Ai')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="ai" id="ai" required="required" /><?php echo basicConvert("ai", $_VALID['ai'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['ai'])){ echo '<span class="help-block text-danger">'; echo $error['ai'] . ''; echo '</span>';}?>
</div>
        
<div id="name-form-group" class="form-group <?php echo (isset($error['name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="name">
	<?php echo ss('Name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="name" id="name" value="<?php echo ss($_VALID['name'])?>" required="required" />
    </div>
<?php if (isset($error['name'])){ echo '<span class="help-block text-danger">'; echo $error['name'] . ''; echo '</span>';}?>
</div>
        
<div id="description-form-group" class="form-group <?php echo (isset($error['description']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="description">
	<?php echo ss('Description')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="description" id="description" value="<?php echo ss($_VALID['description'])?>" required="required" />
    </div>
<?php if (isset($error['description'])){ echo '<span class="help-block text-danger">'; echo $error['description'] . ''; echo '</span>';}?>
</div>
        
<div id="required-form-group" class="form-group <?php echo (isset($error['required']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="required">
	<?php echo ss('Required')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="required" id="required" value="<?php echo ss($_VALID['required'])?>" />
    </div>
<?php if (isset($error['required'])){ echo '<span class="help-block text-danger">'; echo $error['required'] . ''; echo '</span>';}?>
</div>

                <input type="hidden" name="submitted" value="submitted">
                <input type="hidden" name="i" value="<?php echo $_VALID['i']+1?>">
                <input type="hidden" name="next_id" value="<?php echo $nextEntry[$modul.'_id']?>">
                </div>
                <footer class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9 col-sm-offset-3">                        
                            <button type="submit" class="btn btn-success"  id="submit" value="<?php echo ss('Save')?>"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo ss('Save')?></button>
                            <button type="submit" class="btn btn-info" id="submit_new" name="submit_new" value="<?php echo ss('Save & New')?>"><i class="fa fa-plus"></i>&nbsp;<?php echo ss('Save & New')?></button>
                            <button type="submit" class="btn btn-primary" id="submit_next" name="submit_next" value="<?php echo ss('Save & Next')?>" ><i class="fa fa-arrow-right"></i>&nbsp;<?php echo ss('Save & Next')?></button>
                            <!-- after submit buttons -->
                        </div>
                    </div>
                </footer>
            </form>
            
        </section>
    </div>
</div>
<!-- after function detail form -->
<?php
require("inc/footer.inc.php");