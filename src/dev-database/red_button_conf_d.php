<?php 

$modul="red_button_conf";

require("inc/req.php");

// Generally for people with the right to edit red_button_conf
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'red_button_conf/red_button_conf.php')) {
    require MODULE_ROOT.'red_button_conf/red_button_conf.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Red_button_conf_id
validate('red_button_conf_id', 'int nullable' );

// Red_button_entity_name
validate('red_button_entity_name', 'string' );

// Red_button_data_name
validate('red_button_data_name', 'string nullable' );

// Param
validate('param', 'string' );

// Value
validate('value', 'string nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `red_button_conf` WHERE red_button_conf_id = " . (int) $_REQUEST['red_button_conf_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'red_button_conf/red_button_conf.delete.php')) {
        require MODULE_ROOT.'red_button_conf/red_button_conf.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['red_button_conf_id']) {
            $listUpdateSql = "UPDATE `red_button_conf` SET `" . $key . "`=" . $value . " WHERE red_button_conf_id = " . (int) $_REQUEST['red_button_conf_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `red_button_conf` WHERE red_button_conf_id = " . (int) $_REQUEST['red_button_conf_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `red_button_conf` SET red_button_entity_name = "
    .$_VALIDDB['red_button_entity_name']
     . ",red_button_data_name = " . $_VALIDDB['red_button_data_name']
     . ",param = " . $_VALIDDB['param']
     . ",value = " . $_VALIDDB['value']
    . " WHERE red_button_conf_id = " . (int) $_REQUEST['red_button_conf_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after red_button_conf update ***/
    
    } else {
        /*** before red_button_conf insert ***/
	    $sql = "INSERT INTO `red_button_conf`(red_button_conf_id, red_button_entity_name, red_button_data_name, param, value) VALUES("
    .$_VALIDDB['red_button_conf_id']
    . ",
	" . $_VALIDDB['red_button_entity_name']
    . ",
	" . $_VALIDDB['red_button_data_name']
    . ",
	" . $_VALIDDB['param']
    . ",
	" . $_VALIDDB['value']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['red_button_conf_id'] = mysqli_insert_id($con);
        /*** after red_button_conf insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'red_button_conf_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'red_button_conf_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'red_button_conf_id' => $_VALID['next_id']];
    } else {
        $loc = 'red_button_conf.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['red_button_conf_id']) {
	$sql = "SELECT * FROM `red_button_conf` WHERE red_button_conf_id = " . (int) $_REQUEST['red_button_conf_id'];
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
$n4a['red_button_conf.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Red_button_conf')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formred_button_conf" name="formred_button_conf" method="post" class="formLayout" >
<?php if($_REQUEST['red_button_conf_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'red_button_conf.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['red_button_conf_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;red_button_conf_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;red_button_conf_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Red_button_conf')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="red_button_entity_name-form-group" class="form-group <?php echo (isset($error['red_button_entity_name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="red_button_entity_name">
	<?php echo ss('Red_button_entity_name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="red_button_entity_name" id="red_button_entity_name" value="<?php echo ss($_VALID['red_button_entity_name'])?>" required="required" />
    </div>
<?php if (isset($error['red_button_entity_name'])){ echo '<span class="help-block text-danger">'; echo $error['red_button_entity_name'] . ''; echo '</span>';}?>
</div>
        
<div id="red_button_data_name-form-group" class="form-group <?php echo (isset($error['red_button_data_name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="red_button_data_name">
	<?php echo ss('Red_button_data_name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="red_button_data_name" id="red_button_data_name" value="<?php echo ss($_VALID['red_button_data_name'])?>" />
    </div>
<?php if (isset($error['red_button_data_name'])){ echo '<span class="help-block text-danger">'; echo $error['red_button_data_name'] . ''; echo '</span>';}?>
</div>
        
<div id="param-form-group" class="form-group <?php echo (isset($error['param']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="param">
	<?php echo ss('Param')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="param" id="param" value="<?php echo ss($_VALID['param'])?>" required="required" />
    </div>
<?php if (isset($error['param'])){ echo '<span class="help-block text-danger">'; echo $error['param'] . ''; echo '</span>';}?>
</div>
        
<div id="value-form-group" class="form-group <?php echo (isset($error['value']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="value">
	<?php echo ss('Value')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="value" id="value" value="<?php echo ss($_VALID['value'])?>" />
    </div>
<?php if (isset($error['value'])){ echo '<span class="help-block text-danger">'; echo $error['value'] . ''; echo '</span>';}?>
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
<!-- after red_button_conf detail form -->
<?php
require("inc/footer.inc.php");