<?php 

$modul="user";

require("inc/req.php");

// Generally for people with the right to edit user
$groupID = 6;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'user/user.php')) {
    require MODULE_ROOT.'user/user.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// User_id
validate('user_id', 'int nullable' );
// Admin group for Administrators only
if ($_VALID['user_id'] == 1) {
    GRGR(1);
}

// Email
validate('email', 'string' );

// Password
validate('password', 'string' );

// Firstname
validate('firstname', 'string' );

// Lastname
validate('lastname', 'string' );

// Is_active
validate('is_active', 'ckb' );

// Login_link
validate('login_link', 'string nullable' );

// Lang
validate('lang', 'string' );
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}

/*** Deletion ***/
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM user WHERE user_id = " . (int) $_VALID['user_id'];
	/*** Before Delete ***/
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
	// include delete script if exists
    if (file_exists(MODULE_ROOT.'user/user.delete.php')) {
        require MODULE_ROOT.'user/user.delete.php';
    }
	exit;
}


if (isset($_REQUEST['submitted']) AND !$error) {
    if ($_VALID['password']) {
        $_VALID['password'] = sha1($_VALID['password'].SALT);
        $_VALIDDB['password'] = "'" . my_sql($_VALID['password']) . "'";
    }
    $checkSql = "SELECT 1 FROM user WHERE user_id = " . (int) $_REQUEST['user_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
        //  Admin user edit for Administrators only
        $sql = "SELECT 1 FROM user2gr WHERE gr_id=1 AND user_id=" . $_VALID['user_id'];
        $res = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($res);
        if (isset($row[0])) {
            GRGR(1);
        }
    
	    $sql = "UPDATE user SET email = "
    .$_VALIDDB['email']
     . (($_VALID['password'])?",password = " . $_VALIDDB['password'] : "")
     . ",firstname = " . $_VALIDDB['firstname']
     . ",lastname = " . $_VALIDDB['lastname']
     . ",is_active = " . $_VALIDDB['is_active']
     . ",login_link = " . $_VALIDDB['login_link']
     . ",lang = " . $_VALIDDB['lang']
     . ",dbupdate = NOW()"
    . " WHERE user_id = " . (int) $_REQUEST['user_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after user update ***/
    
    } else {
        /*** before user insert ***/
	    $sql = "INSERT INTO user(user_id, email, password, firstname, lastname, is_active, login_link, lang) VALUES("
    .$_VALIDDB['user_id']
    . ",
	" . $_VALIDDB['email']
    . ",
	" . $_VALIDDB['password']
    . ",
	" . $_VALIDDB['firstname']
    . ",
	" . $_VALIDDB['lastname']
    . ",
	" . $_VALIDDB['is_active']
    . ",
	" . $_VALIDDB['login_link']
    . ",
	" . $_VALIDDB['lang']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['user_id'] = mysqli_insert_id($con);
        /*** after user insert ***/
        require MODULE_ROOT.'user/user.insert.php';
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'user_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'user_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'user_id' => $_VALID['next_id']];
    } else {
        $loc = 'user.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['user_id']) {
	$sql = "SELECT * FROM user WHERE user_id = " . (int) $_REQUEST['user_id'];
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
$n4a['user.php'] = ss('Back to List');
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
            <li><span><?php echo ss('User')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formuser" name="formuser" method="post" class="formLayout" >
<?php if($_REQUEST['user_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
}?>
<header class="panel-heading">
    <div class="panel-actions">
        <a href="#" class="fa fa-caret-down"></a>
        <a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'user.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['user_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;user_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;user_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

    <h2 class="panel-title">
    <?php echo ss('User')?>
    </h2>
</header>
<div class="panel-body">
        
<div id="email-form-group" class="form-group <?php echo (isset($error['email']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="email">
	<?php echo ss('Email')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="email" id="email" value="<?php echo ss($_VALID['email'])?>" required="required" />
    </div>
<?php if (isset($error['email'])){ echo '<span class="help-block text-danger">'; echo $error['email'] . ''; echo '</span>';}?>
</div>
        
<div id="password-form-group" class="form-group <?php echo (isset($error['password']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="password">
	<?php echo ss('Password')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="password" name="password" id="password" value="<?php echo ss($_VALID['password'])?>" required="required" />
    </div>
<?php if (isset($error['password'])){ echo '<span class="help-block text-danger">'; echo $error['password'] . ''; echo '</span>';}?>
</div>
        
<div id="firstname-form-group" class="form-group <?php echo (isset($error['firstname']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="firstname">
	<?php echo ss('Firstname')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="firstname" id="firstname" value="<?php echo ss($_VALID['firstname'])?>" required="required" />
    </div>
<?php if (isset($error['firstname'])){ echo '<span class="help-block text-danger">'; echo $error['firstname'] . ''; echo '</span>';}?>
</div>
        
<div id="lastname-form-group" class="form-group <?php echo (isset($error['lastname']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="lastname">
	<?php echo ss('Lastname')?>
	</label>
    <div class="col-md-6">

        <input class="form-control" type="text" name="lastname" id="lastname" value="<?php echo ss($_VALID['lastname'])?>" required="required" />
    </div>
<?php if (isset($error['lastname'])){ echo '<span class="help-block text-danger">'; echo $error['lastname'] . ''; echo '</span>';}?>
</div>
        
<div id="is_active-form-group" class="form-group <?php echo (isset($error['is_active']) ? 'has-error' : ' '); ?>">
	<label class="col-md-3 control-label" for="is_active">
	<?php echo ss('Is_active')?>
	</label>
    <div class="col-md-6">

        <div class="checkbox-custom checkbox-default">
            <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo ($_VALID['is_active'] OR !$_VALID['submitted'])?'checked="checked"':''?> />
            <label for="is_active"></label>
        </div>
    </div>
<?php if (isset($error['is_active'])){ echo '<span class="help-block text-danger">'; echo $error['is_active'] . ''; echo '</span>';}?>
</div>
        
<input class="form-control" type="hidden" name="login_link" id="login_link" value="<?php echo ss($_VALID['login_link'])?>" />
<input class="form-control" type="hidden" name="lang" id="lang" value="DE" required="required" />

<?php if (isset($error['lang'])){ echo '<span class="help-block text-danger">'; echo $error['lang'] . ''; echo '</span>';}?>


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
<?php
if ($_REQUEST['user_id']) {
    /*** Group ***/
    if (R(2)) { ?>
        <section class="panel" style="height:100%;width:100%;">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-up hide"></a>
                </div>
                <h2 class="panel-title clickable" id="groupsHeader"><?php sss('Groups')?></h2>
            </header>
            <div class="panel-body" style="display:none;">
                <table class="table table-bordered table-striped mb-none table-hover bw" cellspacing="0" cellpadding="0" >
                    <tbody id="grlist_tbody">

                    </tbody>
                </table>
            </div>
        </section>
        <script type="text/javascript">

            var grRefresh = false;
            var groupsLoaded = false;
            $('#groupsHeader').on("click",function(e){
                if(!groupsLoaded){
                    var element = e.target;
                    $(element).removeClass('clickable');
                    var url = 'gr.php?headless&user_id=<?php echo $_REQUEST['user_id']; ?>';
                    if(grRefresh) url += '&rl';
                    $.get(url, function(data) {
                        $('#grlist_tbody').html(data);
                        $(e.target).parent().find('.panel-actions a.fa-caret-up').removeClass('hide').click();
                        groupsLoaded = true;
                    });
                }
            });

            function ynGr(gr_id) {
                if($('#grtick_'+gr_id).find('i.fa').hasClass('fa-square-o')){
                    gright = 1;
                } else if($('#grtick_'+gr_id).find('i.fa').hasClass('fa-check-square-o')){
                    gright = 0;
                }
                $.post("a/user2gr_edit.php?yn="+gright, { gr_id: gr_id, user_id: <?php echo $_REQUEST['user_id'] ? $_REQUEST['user_id'] : "null" ?> },function(data){
                    new PNotify({
                        title: 'Success',
                        text: ( gright === 1 ? 'Group added to user.' : 'Group Removed from user.'),
                        type: 'success',
                        shadow: true
                    });
                    if(gright === 0){
                        $('#grtick_'+gr_id).find('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }else if(gright === 1){
                        $('#grtick_'+gr_id).find('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
                    }
                });
            }

        </script>
    <?php } ?>
    <?php
    /*** Rights ***/
    if (R(4)) { ?>
        <section class="panel" style="height:100%;width:100%;">
            <header class="panel-heading" >
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-up hide"></a>
                </div>
                <h2 class="panel-title clickable" id="rightsHeader"><?php sss('Rights')?></h2>
            </header>
            <div class="panel-body" style="display:none;">
                <table class="table table-bordered table-striped mb-none table-hover bw" cellspacing="0" cellpadding="0">
                    <tbody id="rlist_tbody">

                    </tbody>
                </table>
            </div>
        </section>
        <script type="text/javascript">

            var rRefresh = false;
            var rightsLoaded = false;
            $('#rightsHeader').on("click",function(e){
                if(!rightsLoaded){
                    var element = e.target;
                    $(element).removeClass('clickable');
                    var url = 'r.php?headless&user_id=<?php echo $_REQUEST['user_id']; ?>';
                    if(rRefresh) url += '&rl';
                    $.get(url, function(data) {
                        $('#rlist_tbody').html(data);
                        $(e.target).parent().find('.panel-actions a.fa-caret-up').removeClass('hide').click();
                        rightsLoaded = true;
                    });
                }
            });

            function ynR(r_id, gr_yn) {
                if($('#rtick_'+r_id).find('i.fa').hasClass('fa-square-o')){
                    right = 1;
                } else if($('#rtick_'+r_id).find('i.fa').hasClass('fa-check-square-o')){
                    right = 0;
                }
                $.post("a/user2r.php?yn="+right, { r_id: r_id, gr_yn: gr_yn, user_id: <?php echo $_REQUEST['user_id'] ? $_REQUEST['user_id'] : "null"; ?> },function(data){
                    new PNotify({
                        title: 'Success',
                        text: ( right === 1 ? 'Group added to user.' : 'Group Removed from user.'),
                        type: 'success',
                        shadow: true
                    });
                    if(right === 0){
                        $('#rtick_'+r_id).find('i.fa').removeClass('fa-check-square-o').addClass('fa-square-o');
                    }else if(right === 1){
                        $('#rtick_'+r_id).find('i.fa').removeClass('fa-square-o').addClass('fa-check-square-o');
                    }
                });
            }

        </script>
    <?php } ?>
<?php } ?>

<!-- after user detail form -->
<?php
require("inc/footer.inc.php");