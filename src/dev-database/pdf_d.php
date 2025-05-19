<?php 

$modul="pdf";

require("inc/req.php");

// Generally for people with the right to edit pdf
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'pdf/pdf.php')) {
    require MODULE_ROOT.'pdf/pdf.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Name
validate('name', 'string' );

// Html_template
validate('html_template', 'string nullable' );

// Static_file_media
validate('static_file_media', 'media nullable' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
    /*** Delete static_file_media  ***/
    $sql = "SELECT static_file_media FROM docs WHERE docs_id = " . (int) $_VALID['docs_id'];
    $res = mysqli_query($con,$sql);
    $row = mysqli_fetch_row($res);
    unlink(MEDIA_PRIV_ROOT . $row[0]);

	$sql = "DELETE FROM `pdf` WHERE pdf_id = " . (int) $_REQUEST['pdf_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'pdf/pdf.delete.php')) {
        require MODULE_ROOT.'pdf/pdf.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['pdf_id']) {
            $listUpdateSql = "UPDATE `pdf` SET `" . $key . "`=" . $value . " WHERE pdf_id = " . (int) $_REQUEST['pdf_id'];
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
    // start upload static_file_media
    if($_VALID['static_file_media']) {
        $targetRelDir = $modul . '/';
        $targetDir = MEDIA_PRIV_ROOT . $targetRelDir;
        $tmpfname = basename(tempnam($targetDir, "static_file_media_"));
        $path_parts = pathinfo($_FILES['static_file_media']['name']);
        $fileName = $tmpfname . '.' . strtolower($path_parts['extension']);
        $targetFile = $targetDir . $fileName;
    
        if(move_uploaded_file($_VALID['static_file_media'],$targetFile)) {
            $_VALID['static_file_media'] = $targetFile;
            $_VALIDDB['static_file_media'] = "'" . $targetRelDir . $fileName . "'";
            unlink ($targetDir . $tmpfname);
        } else {
            throw new Exception('File not uploaded');
        }
        // end upload static_file_media
    }

if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `pdf` WHERE pdf_id = " . (int) $_REQUEST['pdf_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `pdf` SET name = "
    .$_VALIDDB['name']
     . ",html_template = " . $_VALIDDB['html_template']
     . ",static_file_media = " . $_VALIDDB['static_file_media']
    . " WHERE pdf_id = " . (int) $_REQUEST['pdf_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after pdf update ***/
    
    } else {
        /*** before pdf insert ***/
	    $sql = "INSERT INTO `pdf`(name, html_template, static_file_media) VALUES("
    .$_VALIDDB['name']
    . ",
	" . $_VALIDDB['html_template']
    . ",
	" . $_VALIDDB['static_file_media']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['pdf_id'] = mysqli_insert_id($con);
        /*** after pdf insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'pdf_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'pdf_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'pdf_id' => $_VALID['next_id']];
    } else {
        $loc = 'pdf.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['pdf_id']) {
	$sql = "SELECT * FROM `pdf` WHERE pdf_id = " . (int) $_REQUEST['pdf_id'];
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
$n4a['pdf.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Pdf')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formpdf" name="formpdf" method="post" class="formLayout" enctype="multipart/form-data">
<?php if($_REQUEST['pdf_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'pdf.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['pdf_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;pdf_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;pdf_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Pdf')?>
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
        
<div id="html_template-form-group" class="form-group <?php echo (isset($error['html_template']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="html_template">
	<?php echo ss('Html_template')?>
	</label>
    <div class="col-md-6">

        <textarea class="form-control" name="html_template" id="html_template"><?php echo ss($_VALID['html_template'])?></textarea>
    </div>
<?php if (isset($error['html_template'])){ echo '<span class="help-block text-danger">'; echo $error['html_template'] . ''; echo '</span>';}?>
</div>
        
<div id="static_file_media-form-group" class="form-group <?php echo (isset($error['static_file_media']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="static_file_media">
	<?php echo ss('Static_file_media')?>
	</label>
    <div class="col-md-6">

        <input type="file" accept="image/*,application/pdf" name="static_file_media" id="static_file_media"  />
<?php if ($_VALID['static_file_media']) { 
    $mediaUrl = HTTP_HOST . $_SERVER['PHP_SELF'] . '?show_media&filename=' . $_VALID['static_file_media'];
    if (strpos($_VALID['static_file_media'], '.pdf') === false) {
        echo '<a href="' . $mediaUrl . '" target="_blank"><img src="' . $mediaUrl . '" width="30"></a>';
    } else {
        echo '<a href="' . $mediaUrl . '" target="_blank"><i class="fa fa-file-pdf-o"></i></a>';
    }
    }?>
        <input type="hidden" name="hidden_static_file_media" id="static_file_media" value="<?php echo ss($_VALID['static_file_media'])?>" />
    </div>
<?php if (isset($error['static_file_media'])){ echo '<span class="help-block text-danger">'; echo $error['static_file_media'] . ''; echo '</span>';}?>
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
<!-- after pdf detail form -->
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