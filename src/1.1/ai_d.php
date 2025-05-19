<?php 

$modul="ai";

require("inc/req.php");

// Generally for people with the right to edit ai
$groupID = 6;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'ai/ai.php')) {
    require MODULE_ROOT.'ai/ai.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Name
validate('name', 'string' );

// Subtitle
validate('subtitle', 'string nullable' );

// Init_cmd
validate('init_cmd', 'string nullable' );

// Briefing
validate('briefing', 'string nullable' );

// Greeting
validate('greeting', 'string nullable' );

// Active
validate('active', 'ckb' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `ai` WHERE ai_id = " . (int) $_REQUEST['ai_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'ai/ai.delete.php')) {
        require MODULE_ROOT.'ai/ai.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['ai_id']) {
            $listUpdateSql = "UPDATE `ai` SET `" . $key . "`=" . $value . " WHERE ai_id = " . (int) $_REQUEST['ai_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `ai` WHERE ai_id = " . (int) $_REQUEST['ai_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `ai` SET name = "
    .$_VALIDDB['name']
     . ",subtitle = " . $_VALIDDB['subtitle']
     . ",init_cmd = " . $_VALIDDB['init_cmd']
     . ",briefing = " . $_VALIDDB['briefing']
     . ",greeting = " . $_VALIDDB['greeting']
     . ",active = " . $_VALIDDB['active']
    . " WHERE ai_id = " . (int) $_REQUEST['ai_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after ai update ***/
    
    } else {
        /*** before ai insert ***/
	    $sql = "INSERT INTO `ai`(name, subtitle, init_cmd, briefing, greeting, active) VALUES("
    .$_VALIDDB['name']
    . ",
	" . $_VALIDDB['subtitle']
    . ",
	" . $_VALIDDB['init_cmd']
    . ",
	" . $_VALIDDB['briefing']
    . ",
	" . $_VALIDDB['greeting']
    . ",
	" . $_VALIDDB['active']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['ai_id'] = mysqli_insert_id($con);
        /*** after ai insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'ai_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'ai_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'ai_id' => $_VALID['next_id']];
    } else {
        $loc = 'ai.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['ai_id']) {
	$sql = "SELECT * FROM `ai` WHERE ai_id = " . (int) $_REQUEST['ai_id'];
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
$n4a['ai.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Ai')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formai" name="formai" method="post" class="formLayout" >
<?php if($_REQUEST['ai_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'ai.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['ai_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;ai_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;ai_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Ai')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="name-form-group" class="form-group <?php echo (isset($error['name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="name">
	<?php echo ss('Name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="name" id="name" value="<?php echo ss($_VALID['name'])?>" required="required" />
    </div>
<?php if (isset($error['name'])){ echo '<span class="help-block text-danger">'; echo $error['name'] . ''; echo '</span>';}?>
</div>
        
<div id="subtitle-form-group" class="form-group <?php echo (isset($error['subtitle']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="subtitle">
	<?php echo ss('Subtitle')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="subtitle" id="subtitle" value="<?php echo ss($_VALID['subtitle'])?>" />
    </div>
<?php if (isset($error['subtitle'])){ echo '<span class="help-block text-danger">'; echo $error['subtitle'] . ''; echo '</span>';}?>
</div>
        
<div id="init_cmd-form-group" class="form-group <?php echo (isset($error['init_cmd']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="init_cmd">
	<?php echo ss('Init_cmd')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="init_cmd" id="init_cmd" rows="15"><?php echo ss($_VALID['init_cmd'])?></textarea>
    </div>
<?php if (isset($error['init_cmd'])){ echo '<span class="help-block text-danger">'; echo $error['init_cmd'] . ''; echo '</span>';}?>
</div>
        
<div id="briefing-form-group" class="form-group <?php echo (isset($error['briefing']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="briefing">
	<?php echo ss('Briefing')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="briefing" id="briefing" rows="15"><?php echo ss($_VALID['briefing'])?></textarea>
    </div>
<?php if (isset($error['briefing'])){ echo '<span class="help-block text-danger">'; echo $error['briefing'] . ''; echo '</span>';}?>
</div>
        
<div id="greeting-form-group" class="form-group <?php echo (isset($error['greeting']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="greeting">
	<?php echo ss('Greeting')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="greeting" id="greeting" value="<?php echo ss($_VALID['greeting'])?>" />
    </div>
<?php if (isset($error['greeting'])){ echo '<span class="help-block text-danger">'; echo $error['greeting'] . ''; echo '</span>';}?>
</div>
        
<div id="active-form-group" class="form-group <?php echo (isset($error['active']) ? 'has-error' : ' '); ?>"<?php if (!GR(3)) echo 'style="visibility:hidden"';?>>
	<label class="col-md-3 control-label" for="active">
	<?php echo ss('Active')?>
	</label>
    <div class="col-md-6">

        <div class="checkbox-custom checkbox-default">
            <input type="checkbox" name="active" id="active" value="1" <?php echo ($_VALID['active'])?'checked="checked"':''?> />
            <label for="active"></label>
        </div>
    </div>
<?php if (isset($error['active'])){ echo '<span class="help-block text-danger">'; echo $error['active'] . ''; echo '</span>';}?>
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
<!-- after ai detail form -->
<?php
require("inc/footer.inc.php");