<?php 
    
$modul="red_button_entity";

require("inc/req.php");

// Generally for people with the right to edit red_button_entity
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'red_button_entity/red_button_entity.php')) {
    require MODULE_ROOT.'red_button_entity/red_button_entity.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Entity_name
validate('entity_name', 'string' );

// Data_name
validate('data_name', 'string nullable' );

// Data_type
validate('data_type', 'enum nullable', array('int','numeric','string','ckb','date','blob','media') );

// Is_nullable
validate('is_nullable', 'ckb nullable' );

// After
validate('after', 'string nullable' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM red_button_entity WHERE red_button_entity_id = " . (int) $_REQUEST['red_button_entity_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE red_button_entity SET entity_name = "
    .$_VALIDDB['entity_name']
     . ",data_name = " . $_VALIDDB['data_name']
     . ",data_type = " . $_VALIDDB['data_type']
     . ",is_nullable = " . $_VALIDDB['is_nullable']
     . ",after = " . $_VALIDDB['after']
    . " WHERE red_button_entity_id = " . (int) $_REQUEST['red_button_entity_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after red_button_entity update ***/
    
    } else {
    
	    $sql = "INSERT INTO red_button_entity(entity_name, data_name, data_type, is_nullable, after) VALUES("
    .$_VALIDDB['entity_name']
    . ",
	" . $_VALIDDB['data_name']
    . ",
	" . $_VALIDDB['data_type']
    . ",
	" . $_VALIDDB['is_nullable']
    . ",
	" . $_VALIDDB['after']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
    require MODULE_ROOT.'red_button_entity/red_button_entity_insert.php';
        $_VALID['red_button_entity_id'] = mysqli_insert_id($con);
        /*** after red_button_entity insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'red_button_entity_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'red_button_entity_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'red_button_entity_id' => $_VALID['next_id']];
    } else {
        $loc = 'red_button_entity.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['red_button_entity_id']) {
	$sql = "SELECT * FROM red_button_entity WHERE red_button_entity_id = " . (int) $_REQUEST['red_button_entity_id'];
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
$n4a['red_button_entity.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Red_button_entity')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left hidden"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formred_button_entity" name="formred_button_entity" method="post" class="formLayout" >
<?php if($_REQUEST['red_button_entity_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
    echo '<input type="hidden" name="return_script" id="return_script" value="'. $_REQUEST['return_script'] . '" />';
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'red_button_entity.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['red_button_entity_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;red_button_entity_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;red_button_entity_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Red_button_entity')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['entity_name']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="entity_name"><?php echo ss('Entity_name')?></label><div class="col-md-6">
<input class="form-control" type="text" name="entity_name" id="entity_name" value="<?php echo ss($_VALID['entity_name'])?>" required="required" /></div>
<?php if (isset($error['entity_name'])){ echo '<span class="help-block text-danger">'; echo $error['entity_name'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['data_name']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="data_name"><?php echo ss('Data_name')?></label><div class="col-md-6">
<input class="form-control" type="text" name="data_name" id="data_name" value="<?php echo ss($_VALID['data_name'])?>" /></div>
<?php if (isset($error['data_name'])){ echo '<span class="help-block text-danger">'; echo $error['data_name'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['data_type']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="data_type"><?php echo ss('Data_type')?></label><div class="col-md-6"><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_int" value="int" <?php echo ($_VALID['data_type'] == 'int')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('int')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_numeric" value="numeric" <?php echo ($_VALID['data_type'] == 'numeric')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('numeric')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_string" value="string" <?php echo ($_VALID['data_type'] == 'string')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('string')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_ckb" value="ckb" <?php echo ($_VALID['data_type'] == 'ckb')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('ckb')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_date" value="date" <?php echo ($_VALID['data_type'] == 'date')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('date')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_blob" value="blob" <?php echo ($_VALID['data_type'] == 'blob')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('blob')?></label></div><div class="radio-custom">
                            <input type="radio"  name="data_type" id="data_type_media" value="media" <?php echo ($_VALID['data_type'] == 'media')?'checked="checked"':''?> /><label name="data_type" > <?php echo ss('media')?></label></div></div>
<?php if (isset($error['data_type'])){ echo '<span class="help-block text-danger">'; echo $error['data_type'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['is_nullable']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="is_nullable"><?php echo ss('Is_nullable')?></label><div class="col-md-6"><div class="checkbox-custom checkbox-default">
                            <input type="checkbox" name="is_nullable" id="is_nullable" value="1" <?php echo ($_VALID['is_nullable'])?'checked="checked"':''?> /><label for="is_nullable"></label></div></div>
<?php if (isset($error['is_nullable'])){ echo '<span class="help-block text-danger">'; echo $error['is_nullable'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['after']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="after"><?php echo ss('After')?></label><div class="col-md-6">
<input class="form-control" type="text" name="after" id="after" value="<?php echo ss($_VALID['after'])?>" /></div>
<?php if (isset($error['after'])){ echo '<span class="help-block text-danger">'; echo $error['after'] . ''; echo '</span>';}?></div>
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
<!-- after red_button_entity detail form -->
<?php
require("inc/footer.inc.php"); 
    ?>