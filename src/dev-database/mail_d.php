<?php 

$modul="mail";

require("inc/req.php");

// Generally for people with the right to edit mail
$groupID = 1024;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'mail/mail.php')) {
    require MODULE_ROOT.'mail/mail.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Mail_group
validate('mail_group', 'int' );

// Recipient
validate('recipient', 'string' );

// Recipient_name
validate('recipient_name', 'string nullable' );

// Subject
validate('subject', 'string' );

// Content
validate('content', 'string' );

// Dsent
validate('dsent', 'datetime nullable' );

// Attachment_media
validate('attachment_media', 'media nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
    /*** Delete attachment_media  ***/
    $sql = "SELECT attachment_media FROM `mail` WHERE mail_id = " . (int) $_REQUEST['mail_id'];
    $res = mysqli_query($con,$sql);
    $row = mysqli_fetch_row($res);
    unlink(MEDIA_PRIV_ROOT . $row[0]);

	$sql = "DELETE FROM `mail` WHERE mail_id = " . (int) $_REQUEST['mail_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'mail/mail.delete.php')) {
        require MODULE_ROOT.'mail/mail.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['mail_id']) {
            $listUpdateSql = "UPDATE `mail` SET `" . $key . "`=" . $value . " WHERE mail_id = " . (int) $_REQUEST['mail_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}

    if (isset($_REQUEST['show_media'])) {
        validate('filename', 'path');

        if (strpos($_VALID['filename'],'.pdf') !== false) {
            $contentType = 'application/pdf';
        } else if (strpos($_VALID['filename'],'.jpg')!== false
            || strpos($_VALID['filename'],'.jpeg') !== false) {
            $contentType = 'image/jpeg';
        } else if (strpos($_VALID['filename'],'.png') !== false) {
            $contentType = 'image/png';
        } else {
            $contentType = 'application/octet-stream';
            header("Content-disposition: attachment; filename=" . $_VALID['filename']);
        }
        
        header('Content-Type: ' . $contentType);
        readfile(MEDIA_PRIV_ROOT . $_VALID['filename']);
        exit;
    }
    // start upload attachment_media
    if($_VALID['attachment_media']) {
        $targetRelDir = $modul . '/';
        $targetDir = MEDIA_PRIV_ROOT . $targetRelDir;
        $tmpfname = basename(tempnam($targetDir, "attachment_media_"));
        $path_parts = pathinfo($_FILES['attachment_media']['name']);
        $fileName = $tmpfname . '.' . strtolower($path_parts['extension']);
        $targetFile = $targetDir . $fileName;
    
        if(move_uploaded_file($_VALID['attachment_media'],$targetFile)) {
            $_VALID['attachment_media'] = $targetFile;
            $_VALIDDB['attachment_media'] = "'" . $targetRelDir . $fileName . "'";
            unlink ($targetDir . $tmpfname);
        } else {
            throw new Exception('File not uploaded');
        }
        // end upload attachment_media
    }

if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `mail` WHERE mail_id = " . (int) $_REQUEST['mail_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `mail` SET mail_group = "
    .$_VALIDDB['mail_group']
     . ",recipient = " . $_VALIDDB['recipient']
     . ",recipient_name = " . $_VALIDDB['recipient_name']
     . ",subject = " . $_VALIDDB['subject']
     . ",content = " . $_VALIDDB['content']
     . ",dsent = " . $_VALIDDB['dsent']
     . ",attachment_media = " . $_VALIDDB['attachment_media']
    . " WHERE mail_id = " . (int) $_REQUEST['mail_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after mail update ***/
    
    } else {
        /*** before mail insert ***/
	    $sql = "INSERT INTO `mail`(mail_group, recipient, recipient_name, subject, content, dsent, attachment_media) VALUES("
    .$_VALIDDB['mail_group']
    . ",
	" . $_VALIDDB['recipient']
    . ",
	" . $_VALIDDB['recipient_name']
    . ",
	" . $_VALIDDB['subject']
    . ",
	" . $_VALIDDB['content']
    . ",
	" . $_VALIDDB['dsent']
    . ",
	" . $_VALIDDB['attachment_media']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['mail_id'] = mysqli_insert_id($con);
        /*** after mail insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'mail_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'mail_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'mail_id' => $_VALID['next_id']];
    } else {
        $loc = 'mail.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['mail_id']) {
	$sql = "SELECT * FROM `mail` WHERE mail_id = " . (int) $_REQUEST['mail_id'];
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
$n4a['mail.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Mail')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formmail" name="formmail" method="post" class="formLayout" enctype="multipart/form-data">
<?php if($_REQUEST['mail_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'mail.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['mail_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;mail_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;mail_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Mail')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="mail_group-form-group" class="form-group <?php echo (isset($error['mail_group']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="mail_group">
	<?php echo ss('Mail_group')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="mail_group" id="mail_group" required="required" /><?php echo basicConvert("mail_group", $_VALID['mail_group'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['mail_group'])){ echo '<span class="help-block text-danger">'; echo $error['mail_group'] . ''; echo '</span>';}?>
</div>
        
<div id="recipient-form-group" class="form-group <?php echo (isset($error['recipient']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="recipient">
	<?php echo ss('Recipient')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="recipient" id="recipient" value="<?php echo ss($_VALID['recipient'])?>" required="required" />
    </div>
<?php if (isset($error['recipient'])){ echo '<span class="help-block text-danger">'; echo $error['recipient'] . ''; echo '</span>';}?>
</div>
        
<div id="recipient_name-form-group" class="form-group <?php echo (isset($error['recipient_name']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="recipient_name">
	<?php echo ss('Recipient_name')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="recipient_name" id="recipient_name" value="<?php echo ss($_VALID['recipient_name'])?>" />
    </div>
<?php if (isset($error['recipient_name'])){ echo '<span class="help-block text-danger">'; echo $error['recipient_name'] . ''; echo '</span>';}?>
</div>
        
<div id="subject-form-group" class="form-group <?php echo (isset($error['subject']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="subject">
	<?php echo ss('Subject')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="subject" id="subject" value="<?php echo ss($_VALID['subject'])?>" required="required" />
    </div>
<?php if (isset($error['subject'])){ echo '<span class="help-block text-danger">'; echo $error['subject'] . ''; echo '</span>';}?>
</div>
        
<div id="content-form-group" class="form-group <?php echo (isset($error['content']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="content">
	<?php echo ss('Content')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="content" id="content"><?php echo ss($_VALID['content'])?></textarea>
    </div>
<?php if (isset($error['content'])){ echo '<span class="help-block text-danger">'; echo $error['content'] . ''; echo '</span>';}?>
</div>
        
<div id="dsent-form-group" class="form-group <?php echo (isset($error['dsent']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="dsent">
	<?php echo ss('Dsent')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="dsent" id="dsent" value="<?php echo ss($_VALID['dsent'])?>" />
    </div>
<?php if (isset($error['dsent'])){ echo '<span class="help-block text-danger">'; echo $error['dsent'] . ''; echo '</span>';}?>
</div>
        
<div id="attachment_media-form-group" class="form-group <?php echo (isset($error['attachment_media']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="attachment_media">
	<?php echo ss('Attachment_media')?>
	</label>
    <div class="col-md-6">

        <input type="file" accept="image/*,application/pdf" name="attachment_media" id="attachment_media"  />
<?php if ($_VALID['attachment_media']) { 
    $mediaUrl = HTTP_HOST . $_SERVER['PHP_SELF'] . '?show_media&filename=' . $_VALID['attachment_media'];
    if (strpos($_VALID['attachment_media'], '.pdf') === false) {
        echo '<a href="' . $mediaUrl . '" target="_blank"><img src="' . $mediaUrl . '" width="30"></a>';
    } else {
        echo '<a href="' . $mediaUrl . '" target="_blank"><i class="fa fa-file-pdf-o"></i></a>';
    }
    }?>
        <input type="hidden" name="hidden_attachment_media" id="attachment_media" value="<?php echo ss($_VALID['attachment_media'])?>" />
    </div>
<?php if (isset($error['attachment_media'])){ echo '<span class="help-block text-danger">'; echo $error['attachment_media'] . ''; echo '</span>';}?>
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
<!-- after mail detail form -->
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