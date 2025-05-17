<?php 

$modul="function_param";

require("inc/req.php");

// Generally for people with the right to edit function_param
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'function_param/function_param.php')) {
    require MODULE_ROOT.'function_param/function_param.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Function
validate('function', 'int' );

// Name
validate('name', 'string' );

// Property_key
validate('property_key', 'string nullable' );

// Property_value
validate('property_value', 'string nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `function_param` WHERE function_param_id = " . (int) $_REQUEST['function_param_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'function_param/function_param.delete.php')) {
        require MODULE_ROOT.'function_param/function_param.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['function_param_id']) {
            $listUpdateSql = "UPDATE `function_param` SET `" . $key . "`=" . $value . " WHERE function_param_id = " . (int) $_REQUEST['function_param_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `function_param` WHERE function_param_id = " . (int) $_REQUEST['function_param_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `function_param` SET function = "
    .$_VALIDDB['function']
     . ",name = " . $_VALIDDB['name']
     . ",property_key = " . $_VALIDDB['property_key']
     . ",property_value = " . $_VALIDDB['property_value']
    . " WHERE function_param_id = " . (int) $_REQUEST['function_param_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after function_param update ***/
    
    } else {
        /*** before function_param insert ***/
	    $sql = "INSERT INTO `function_param`(function, name, property_key, property_value) VALUES("
    .$_VALIDDB['function']
    . ",
	" . $_VALIDDB['name']
    . ",
	" . $_VALIDDB['property_key']
    . ",
	" . $_VALIDDB['property_value']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['function_param_id'] = mysqli_insert_id($con);
        /*** after function_param insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'function_param_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'function_param_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'function_param_id' => $_VALID['next_id']];
    } else {
        $loc = 'function_param.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['function_param_id']) {
	$sql = "SELECT * FROM `function_param` WHERE function_param_id = " . (int) $_REQUEST['function_param_id'];
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
$n4a['function_param.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Function_param')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formfunction_param" name="formfunction_param" method="post" class="formLayout" >
<?php if($_REQUEST['function_param_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'function_param.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['function_param_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;function_param_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;function_param_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Function_param')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="function-form-group" class="form-group <?php echo (isset($error['function']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="function">
	<?php echo ss('Function')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="function" id="function" required="required" /><?php echo basicConvert("function", $_VALID['function'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['function'])){ echo '<span class="help-block text-danger">'; echo $error['function'] . ''; echo '</span>';}?>
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
        
<div id="property_key-form-group" class="form-group <?php echo (isset($error['property_key']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="property_key">
	<?php echo ss('Property_key')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="property_key" id="property_key" value="<?php echo ss($_VALID['property_key'])?>" />
    </div>
<?php if (isset($error['property_key'])){ echo '<span class="help-block text-danger">'; echo $error['property_key'] . ''; echo '</span>';}?>
</div>
        
<div id="property_value-form-group" class="form-group <?php echo (isset($error['property_value']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="property_value">
	<?php echo ss('Property_value')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="property_value" id="property_value" value="<?php echo ss($_VALID['property_value'])?>" />
    </div>
<?php if (isset($error['property_value'])){ echo '<span class="help-block text-danger">'; echo $error['property_value'] . ''; echo '</span>';}?>
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
<!-- after function_param detail form -->
<?php
require("inc/footer.inc.php");