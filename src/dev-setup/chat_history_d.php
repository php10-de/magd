<?php 

$modul="chat_history";

require("inc/req.php");

// Generally for people with the right to edit chat_history
$groupID = 1001;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'chat_history/chat_history.php')) {
    require MODULE_ROOT.'chat_history/chat_history.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Chat_history_id
validate('chat_history_id', 'int nullable' );

// User_id
validate('user_id', 'int' );

// Ai_id
validate('ai_id', 'int' );

// Session_id
validate('session_id', 'string' );

// Human
validate('human', 'string' );

// Ai
validate('ai', 'string' );

// Action
validate('action', 'string nullable' );

// Cdate
validate('cdate', 'datetime' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM `chat_history` WHERE chat_history_id = " . (int) $_REQUEST['chat_history_id'];
	/*** Before Delete ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'chat_history/chat_history.delete.php')) {
        require MODULE_ROOT.'chat_history/chat_history.delete.php';
    }
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	exit;
}

if (isset($_REQUEST['list_update'])) {
    foreach ($_VALIDDB as $key => $value) {
        if ($_REQUEST['field'] == $key && isset($_VALIDDB[$key]) && $_REQUEST['chat_history_id']) {
            $listUpdateSql = "UPDATE `chat_history` SET `" . $key . "`=" . $value . " WHERE chat_history_id = " . (int) $_REQUEST['chat_history_id'];
            mysqli_query($con, $listUpdateSql) or die('DB List Update Error');
            echo 1;
            exit;
        }
    }
    exit;
}


if (isset($_REQUEST['submitted']) && !$error) {
    $checkSql = "SELECT 1 FROM `chat_history` WHERE chat_history_id = " . (int) $_REQUEST['chat_history_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE `chat_history` SET user_id = "
    .$_VALIDDB['user_id']
     . ",ai_id = " . $_VALIDDB['ai_id']
     . ",session_id = " . $_VALIDDB['session_id']
     . ",human = " . $_VALIDDB['human']
     . ",ai = " . $_VALIDDB['ai']
     . ",action = " . $_VALIDDB['action']
     . ",cdate = " . $_VALIDDB['cdate']
    . " WHERE chat_history_id = " . (int) $_REQUEST['chat_history_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after chat_history update ***/
    
    } else {
        /*** before chat_history insert ***/
	    $sql = "INSERT INTO `chat_history`(chat_history_id, user_id, ai_id, session_id, human, ai, action, cdate) VALUES("
    .$_VALIDDB['chat_history_id']
    . ",
	" . $_VALIDDB['user_id']
    . ",
	" . $_VALIDDB['ai_id']
    . ",
	" . $_VALIDDB['session_id']
    . ",
	" . $_VALIDDB['human']
    . ",
	" . $_VALIDDB['ai']
    . ",
	" . $_VALIDDB['action']
    . ",
	" . $_VALIDDB['cdate']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['chat_history_id'] = mysqli_insert_id($con);
        /*** after chat_history insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'chat_history_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'chat_history_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'chat_history_id' => $_VALID['next_id']];
    } else {
        $loc = 'chat_history.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['chat_history_id']) {
	$sql = "SELECT * FROM `chat_history` WHERE chat_history_id = " . (int) $_REQUEST['chat_history_id'];
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
$n4a['chat_history.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Chat_history')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formchat_history" name="formchat_history" method="post" class="formLayout" >
<?php if($_REQUEST['chat_history_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'chat_history.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['chat_history_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;chat_history_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;chat_history_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('Chat_history')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="user_id-form-group" class="form-group <?php echo (isset($error['user_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="user_id">
	<?php echo ss('User_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="user_id" id="user_id" required="required" /><?php echo basicConvert("user", $_VALID['user_id'], 1, "email", null, $groupID)?> </select />
    </div>
<?php if (isset($error['user_id'])){ echo '<span class="help-block text-danger">'; echo $error['user_id'] . ''; echo '</span>';}?>
</div>
        
<div id="ai_id-form-group" class="form-group <?php echo (isset($error['ai_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="ai_id">
	<?php echo ss('Ai_id')?>
	</label>
    <div class="col-md-6">

        <select class="form-control mb-md" name="ai_id" id="ai_id" required="required" /><?php echo basicConvert("ai", $_VALID['ai_id'], 1, "name", null, $groupID)?> </select />
    </div>
<?php if (isset($error['ai_id'])){ echo '<span class="help-block text-danger">'; echo $error['ai_id'] . ''; echo '</span>';}?>
</div>
        
<div id="session_id-form-group" class="form-group <?php echo (isset($error['session_id']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="session_id">
	<?php echo ss('Session_id')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="session_id" id="session_id" value="<?php echo ss($_VALID['session_id'])?>" required="required" />
    </div>
<?php if (isset($error['session_id'])){ echo '<span class="help-block text-danger">'; echo $error['session_id'] . ''; echo '</span>';}?>
</div>
        
<div id="human-form-group" class="form-group <?php echo (isset($error['human']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="human">
	<?php echo ss('Human')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="human" id="human" value="<?php echo ss($_VALID['human'])?>" required="required" />
    </div>
<?php if (isset($error['human'])){ echo '<span class="help-block text-danger">'; echo $error['human'] . ''; echo '</span>';}?>
</div>
        
<div id="ai-form-group" class="form-group <?php echo (isset($error['ai']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="ai">
	<?php echo ss('Ai')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="ai" id="ai" value="<?php echo ss($_VALID['ai'])?>" required="required" />
    </div>
<?php if (isset($error['ai'])){ echo '<span class="help-block text-danger">'; echo $error['ai'] . ''; echo '</span>';}?>
</div>
        
<div id="action-form-group" class="form-group <?php echo (isset($error['action']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="action">
	<?php echo ss('Action')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="action" id="action" value="<?php echo ss($_VALID['action'])?>" />
    </div>
<?php if (isset($error['action'])){ echo '<span class="help-block text-danger">'; echo $error['action'] . ''; echo '</span>';}?>
</div>
        
<div id="cdate-form-group" class="form-group <?php echo (isset($error['cdate']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="cdate">
	<?php echo ss('Cdate')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="cdate" id="cdate" value="<?php echo ss($_VALID['cdate'])?>" required="required" />
    </div>
<?php if (isset($error['cdate'])){ echo '<span class="help-block text-danger">'; echo $error['cdate'] . ''; echo '</span>';}?>
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
<!-- after chat_history detail form -->
<?php
require("inc/footer.inc.php");