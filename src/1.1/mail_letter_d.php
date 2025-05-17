<?php 

$modul="mail_letter";

require("inc/req.php");

// Generally for people with the right to edit mail_letter
$groupID = 1024;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'mail_letter/mail_letter.php')) {
    require MODULE_ROOT.'mail_letter/mail_letter.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Name
validate('name', 'string' );

// Title
validate('title', 'string' );

// Html
validate('html', 'string' );

// Pdf
validate('pdf', 'int nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `mail_letter` WHERE mail_letter_id = " . (int) $_REQUEST['mail_letter_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'mail_letter/mail_letter.delete.php')) {
        require MODULE_ROOT.'mail_letter/mail_letter.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['mail_letter_id']) {
            $listUpdateSql = "UPDATE `mail_letter` SET `" . $key . "`=" . $value . " WHERE mail_letter_id = " . (int) $_REQUEST['mail_letter_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `mail_letter` WHERE mail_letter_id = " . (int) $_REQUEST['mail_letter_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `mail_letter` SET name = "
    .$_VALIDDB['name']
     . ",title = " . $_VALIDDB['title']
     . ",html = " . $_VALIDDB['html']
     . ",pdf = " . $_VALIDDB['pdf']
    . " WHERE mail_letter_id = " . (int) $_REQUEST['mail_letter_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after mail_letter update ***/
    
    } else {
        /*** before mail_letter insert ***/
	    $sql = "INSERT INTO `mail_letter`(name, title, html, pdf) VALUES("
    .$_VALIDDB['name']
    . ",
	" . $_VALIDDB['title']
    . ",
	" . $_VALIDDB['html']
    . ",
	" . $_VALIDDB['pdf']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['mail_letter_id'] = mysqli_insert_id($con);
        /*** after mail_letter insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'mail_letter_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'mail_letter_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'mail_letter_id' => $_VALID['next_id']];
    } else {
        $loc = 'mail_letter.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['mail_letter_id']) {
	$sql = "SELECT * FROM `mail_letter` WHERE mail_letter_id = " . (int) $_REQUEST['mail_letter_id'];
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
$n4a['mail_letter.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Mail_letter')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formmail_letter" name="formmail_letter" method="post" class="formLayout" >
<?php if($_REQUEST['mail_letter_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'mail_letter.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['mail_letter_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;mail_letter_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;mail_letter_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Mail_letter')?>
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
        
<div id="title-form-group" class="form-group <?php echo (isset($error['title']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="title">
	<?php echo ss('Title')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="title" id="title" value="<?php echo ss($_VALID['title'])?>" required="required" />
    </div>
<?php if (isset($error['title'])){ echo '<span class="help-block text-danger">'; echo $error['title'] . ''; echo '</span>';}?>
</div>
        
<div id="html-form-group" class="form-group <?php echo (isset($error['html']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="html">
	<?php echo ss('Html')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="html" id="html"><?php echo ss($_VALID['html'])?></textarea>
    </div>
<?php if (isset($error['html'])){ echo '<span class="help-block text-danger">'; echo $error['html'] . ''; echo '</span>';}?>
</div>
        
<div id="pdf-form-group" class="form-group <?php echo (isset($error['pdf']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="pdf">
	<?php echo ss('Pdf')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="pdf" id="pdf" /><?php echo basicConvert("pdf", $_VALID['pdf'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['pdf'])){ echo '<span class="help-block text-danger">'; echo $error['pdf'] . ''; echo '</span>';}?>
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
<!-- after mail_letter detail form -->
    <script src='<?php echo HTTP_SUB ?>assets/vendor/jquery-tinymce/tinymce.min.js'></script>

    <script>tinymce.init({ selector:'textarea',
            width:800,
            height:600,
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'save table contextmenu directionality emoticons template paste textcolor'
            ]});
    </script>
<?php
require("inc/footer.inc.php");