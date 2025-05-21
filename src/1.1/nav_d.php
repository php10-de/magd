<?php 

$modul="nav";

require("inc/req.php");
require_once(DOC_ROOT."db/SQLHistory.php");

// Generally for people with the right to edit nav
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'nav/nav.php')) {
    require MODULE_ROOT.'nav/nav.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Nav_id
validate('nav_id', 'int nullable' );

// To_nav_id
validate('to_nav_id', 'int nullable' );

// Gr_id
validate('gr_id', 'int nullable' );

// Level
validate('level', 'int' );

// Name
validate('name', 'string' );

// Link
validate('link', 'string' );

// Icon
validate('icon', 'string nullable' );

// Icon_color
validate('icon_color', 'string nullable' );
    
if (!$_VALID['level']) {
    unset($_MISSING['level']);
    if (!$_VALID['to_nav_id']) {
        $_VALID['level'] = 1;
        $_VALIDDB['level'] = 1;
    } else {
        $sql               = "SELECT level FROM nav WHERE nav_id=" . $_VALIDDB['to_nav_id'];
        $res               = mysqli_query($con, $sql);
        $row               = mysqli_fetch_row($res);
        $_VALID['level']   = $row[0] + 1;
        $_VALIDDB['level'] = $row[0] + 1;
    }
}
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `nav` WHERE nav_id = " . (int) $_REQUEST['nav_id'];
	if (!GR(1)) {
        // Non-Administrators don't delete Admin-Navigation entries
        $sql .= " AND gr_id != 1";
    }
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'nav/nav.delete.php')) {
        require MODULE_ROOT.'nav/nav.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['nav_id']) {
            $listUpdateSql = "UPDATE `nav` SET `" . $key . "`=" . $value . " WHERE nav_id = " . (int) $_REQUEST['nav_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `nav` WHERE nav_id = " . (int) $_REQUEST['nav_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `nav` SET to_nav_id = "
    .$_VALIDDB['to_nav_id']
     . ",gr_id = " . $_VALIDDB['gr_id']
     . ",level = " . $_VALIDDB['level']
     . ",name = " . $_VALIDDB['name']
     . ",link = " . $_VALIDDB['link']
     . ",icon = " . $_VALIDDB['icon']
     . ",icon_color = " . $_VALIDDB['icon_color']
    . " WHERE nav_id = " . (int) $_REQUEST['nav_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after nav update ***/
    
    } else {
        // CREATE TABLE
        if ($_REQUEST['create_entity'] == 1) {
            $tableName = mysqli_real_escape_string($con, mb_strtolower($_VALID['name']));
            $sql = 'CREATE TABLE ' . $tableName . '(`' . $tableName . '_id` INT UNSIGNED NOT NULL AUTO_INCREMENT';
            $sql .= ' , PRIMARY KEY (`' . $tableName . '_id`))';
            $sql .= ' ENGINE = InnoDB';
            if (!mysqli_query($con, $sql)) {
                throw new Exception('create: ' . mysqli_error($con));
            }
            //$SQLHistory = new SQLHistory();
            //$SQLHistory->writeHistory($sql);
        }
        /*** before nav insert ***/
	    $sql = "INSERT INTO `nav`(nav_id, to_nav_id, gr_id, level, name, link, icon, icon_color) VALUES("
    .$_VALIDDB['nav_id']
    . ",
	" . $_VALIDDB['to_nav_id']
    . ",
	" . $_VALIDDB['gr_id']
    . ",
	" . $_VALIDDB['level']
    . ",
	" . $_VALIDDB['name']
    . ",
	" . $_VALIDDB['link']
    . ",
	" . $_VALIDDB['icon']
    . ",
	" . $_VALIDDB['icon_color']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['nav_id'] = mysqli_insert_id($con);
        /*** after nav insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'nav_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'nav_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'nav_id' => $_VALID['next_id']];
    } else {
        $loc = 'nav.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['nav_id']) {
	$sql = "SELECT * FROM `nav` WHERE nav_id = " . (int) $_REQUEST['nav_id'];
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
$n4a['nav.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Nav')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formnav" name="formnav" method="post" class="formLayout" >
<?php if($_REQUEST['nav_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'nav.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['nav_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;nav_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;nav_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Nav')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="nav_id-form-group" class="form-group <?php echo (isset($error['nav_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="nav_id">
	<?php echo ss('Nav_id')?>
	</label>
    <div class="col-md-6">

        <div >
        <input type="text" class="form-control" name="nav_id" id="nav_id" value="<?php echo $_VALID['nav_id']?>" required="required" />
            <label for="nav_id"></label>

        </div>
    </div>
<?php if (isset($error['nav_id'])){ echo '<span class="help-block text-danger">'; echo $error['nav_id'] . ''; echo '</span>';}?>
</div>
        
<div id="to_nav_id-form-group" class="form-group <?php echo (isset($error['to_nav_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="to_nav_id">
	<?php echo ss('Parent')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="to_nav_id" id="to_nav_id" /><?php echo navConvert($_VALID['to_nav_id'], true)?> </select />
    </div>
<?php if (isset($error['to_nav_id'])){ echo '<span class="help-block text-danger">'; echo $error['to_nav_id'] . ''; echo '</span>';}?>
</div>
        
<div id="gr_id-form-group" class="form-group <?php echo (isset($error['gr_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="gr_id">
	<?php echo ss('Group')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="gr_id" id="gr_id" /><?php echo basicConvert("gr", $_VALID['gr_id'], 1, "shortname", null, $groupID)?> </select />
    </div>
<?php if (isset($error['gr_id'])){ echo '<span class="help-block text-danger">'; echo $error['gr_id'] . ''; echo '</span>';}?>
</div>
        
<div id="level-form-group" class="form-group <?php echo (isset($error['level']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="level">
	<?php echo ss('Level')?>
	</label>
    <div class="col-md-6">

        <div >
        <input type="text" class="form-control" name="level" id="level" disabled="disabled" value="<?php echo $_VALID['level']?>" required="required" />
            <label for="level"></label>

        </div>
    </div>
<?php if (isset($error['level'])){ echo '<span class="help-block text-danger">'; echo $error['level'] . ''; echo '</span>';}?>
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
        
<div id="link-form-group" class="form-group <?php echo (isset($error['link']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="link">
	<?php echo ss('Link')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="link" id="link" value="<?php echo ss($_VALID['link'])?>" required="required" />
    </div>
<?php if (isset($error['link'])){ echo '<span class="help-block text-danger">'; echo $error['link'] . ''; echo '</span>';}?>
</div>
        
<div id="icon-form-group" class="form-group <?php echo (isset($error['icon']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="icon">
	<?php echo ss('Icon')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="icon" id="icon" value="<?php echo ss($_VALID['icon'])?>" />
    </div>
<?php if (isset($error['icon'])){ echo '<span class="help-block text-danger">'; echo $error['icon'] . ''; echo '</span>';}?>
</div>
        
<div id="icon_color-form-group" class="form-group <?php echo (isset($error['icon_color']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="icon_color">
	<?php echo ss('Icon_color')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="icon_color" id="icon_color" value="<?php echo ss($_VALID['icon_color'])?>" />
    </div>
<?php if (isset($error['icon_color'])){ echo '<span class="help-block text-danger">'; echo $error['icon_color'] . ''; echo '</span>';}?>
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
<!-- after nav detail form -->
<?php
require("inc/footer.inc.php");