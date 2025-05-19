<?php 
    
$modul="profile";

require("inc/req.php");

// Generally for people with the right to edit profile
if (!R(2)) {
    if ('/profile.php' == $_SERVER['PHP_SELF']) {
        header('location: profile_d.php');
        exit();
    } else {
        $_REQUEST['profile_id'] = $_SESSION['user_id'];
    }
}

// include module if exists
if (file_exists(MODULE_ROOT.'profile/profile.php')) {
    require MODULE_ROOT.'profile/profile.php';
}
//Form Hook After Group

validate('i', 'int nullable');
validate('next_id', 'int nullable');

/*** Validation ***/

// Profile_id
validate('profile_id', 'int nullable' );

// Profile_name
validate('profile_name', 'string' );

// Street
validate('street', 'string' );

// City
validate('city', 'string' );

// Country
validate('country', 'string' );

// Labels
validate('labels', 'string' );

// Api_key
validate('api_key', 'string nullable' );
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}

if (isset($_REQUEST['submitted']) AND !$error) {
    $checkSql = "SELECT 1 FROM profile WHERE profile_id = " . (int) $_REQUEST['profile_id'];
    $checkRes = mysqli_query($con, $checkSql);
    $exists = mysqli_fetch_row($checkRes);

    if ($exists[0]) {
    
	    $sql = "UPDATE profile SET profile_name = "
    .$_VALIDDB['profile_name']
     . ",street = " . $_VALIDDB['street']
     . ",city = " . $_VALIDDB['city']
     . ",country = " . $_VALIDDB['country']
     . ",labels = " . $_VALIDDB['labels']
     . ",api_key = " . $_VALIDDB['api_key']
    . " WHERE profile_id = " . (int) $_REQUEST['profile_id'];
        mysqli_query($con, $sql) or die('DB Update Error');
        /*** after profile update ***/
    
    } else {
    
	    $sql = "INSERT INTO profile(profile_id, profile_name, street, city, country, labels, api_key) VALUES("
    .$_VALIDDB['profile_id']
    . ",
	" . $_VALIDDB['profile_name']
    . ",
	" . $_VALIDDB['street']
    . ",
	" . $_VALIDDB['city']
    . ",
	" . $_VALIDDB['country']
    . ",
	" . $_VALIDDB['labels']
    . ",
	" . $_VALIDDB['api_key']
    . ") ";
        mysqli_query($con, $sql) or die('DB Insert Error');
        $_VALID['profile_id'] = mysqli_insert_id($con);
        /*** after profile insert ***/
    }
    if (isset($_REQUEST['submit_new'])) {
        $loc = 'profile_d.php';
        $nextParam = ['ok' => 'Done'];
    } else if (isset($_REQUEST['submit_next'])) {
        $loc = 'profile_d.php';
        $nextParam = ['ok' => 'Done', 'i' => $_VALID['i'], 'profile_id' => $_VALID['next_id']];
    } else {
        $loc = 'profile.php';
        $nextParam = ['ok' => 'Done'];
    }
    nextHeader($loc, $nextParam);
}

if ($_REQUEST['profile_id']) {
	$sql = "SELECT * FROM profile WHERE profile_id = " . (int) $_REQUEST['profile_id'];
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
$n4a['profile.php'] = ss('Back to List');
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
            <li><span><?php echo ss('Profile')?></span></li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left hidden"></i></a>
    </div>
</header>
<div class="row">
    <div class="col-lg-12">
        
        <section class="panel">
<form class="form-horizontal form-bordered" id="formprofile" name="formprofile" method="post" class="formLayout" >
<?php if($_REQUEST['profile_id']) {
  /** HTML Update-Form **/
} else {
  /** HTML Insert-Form **/
    $_VALID['api_key'] = bin2hex(random_bytes( 32));
}?>
<header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
<a class="fa  fa-list" title="<?php sss('Back to List')?>" href="javascript:void(0)" onClick="window.location.href = 'profile.php'"></a>
<?php
/*** Pagination ***/
if ($_REQUEST['profile_id']) {
    $pageResult = memcacheArray($_SESSION[$modul]['sql']);
    $prevEntry = $pageResult[$_VALID['i']-1];
    if ($prevEntry) {
        echo '<a href="'.$modul.'_d.php?i='.($_VALID['i']-1).'&amp;profile_id='.$prevEntry[$modul.'_id'].'" class="fa fa-chevron-left" title="' . ss('Previous') . '"></a>';
    } else {
        echo '';
    }

    $nextEntry = $pageResult[$_VALID['i']+1];
    if ($nextEntry) {
        echo '&nbsp;&nbsp;<a href="'.$modul.'_d.php?i='.($_VALID['i']+1).'&amp;profile_id='.$nextEntry[$modul.'_id'].'" class="fa fa-chevron-right" title="' . ss('Next') . '"></a>';
    }
}?></div>

                    <h2 class="panel-title"><?php echo ss('Profile')?></h2>
                </header><div class="panel-body"><div class="form-group <?php echo (isset($error['profile_name']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="profile_name"><?php echo ss('Profile_name')?></label><div class="col-md-6">
<input class="form-control" type="text" name="profile_name" id="profile_name" value="<?php echo ss($_VALID['profile_name'])?>" required="required" /></div>
<?php if (isset($error['profile_name'])){ echo '<span class="help-block text-danger">'; echo $error['profile_name'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['street']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="street"><?php echo ss('Street')?></label><div class="col-md-6">
<input class="form-control" type="text" name="street" id="street" value="<?php echo ss($_VALID['street'])?>" required="required" /></div>
<?php if (isset($error['street'])){ echo '<span class="help-block text-danger">'; echo $error['street'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['city']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="city"><?php echo ss('City')?></label><div class="col-md-6">
<input class="form-control" type="text" name="city" id="city" value="<?php echo ss($_VALID['city'])?>" required="required" /></div>
<?php if (isset($error['city'])){ echo '<span class="help-block text-danger">'; echo $error['city'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['country']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="country"><?php echo ss('Country')?></label><div class="col-md-6">
<input class="form-control" type="text" name="country" id="country" value="<?php echo ss($_VALID['country'])?>" required="required" /></div>
<?php if (isset($error['country'])){ echo '<span class="help-block text-danger">'; echo $error['country'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['labels']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="labels"><?php echo ss('Labels')?></label><div class="col-md-6">
<input class="form-control" type="text" name="labels" id="labels" value="<?php echo ss($_VALID['labels'])?>" required="required" /></div>
<?php if (isset($error['labels'])){ echo '<span class="help-block text-danger">'; echo $error['labels'] . ''; echo '</span>';}?></div><div class="form-group <?php echo (isset($error['api_key']) ? 'has-error' : ' '); ?>"><label class="col-md-3 control-label" for="api_key"><?php echo ss('Api_key')?></label><div class="col-md-6">
<input class="form-control" type="text" name="api_key" id="api_key" value="<?php echo ss($_VALID['api_key'])?>" /></div>
<?php if (isset($error['api_key'])){ echo '<span class="help-block text-danger">'; echo $error['api_key'] . ''; echo '</span>';}?></div>
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
<!-- after profile detail form -->
<?php
require("inc/footer.inc.php"); 
    ?>