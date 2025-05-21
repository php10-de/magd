<?php 
    $modul="profile";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view profile
if (!R(2)) {
    if ('/profile.php' == $_SERVER['PHP_SELF']) {
        header('location: profile_d.php');
        exit();
    } else {
        $_REQUEST['profile_id'] = $_SESSION['user_id'];
    }
}

$_SESSION['entity'] = 'profile';


// include module if exists
if (file_exists(MODULE_ROOT.'profile/profile.php')) {
    require MODULE_ROOT.'profile/profile.php';
}

/*** General Table variables **/
// fill parameters from session
if (!isset($_REQUEST['headless']) && isset($_SESSION[$modul])) {
    foreach ($_SESSION[$modul] as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
	
validate('sortcol', 'string');
validate('sortdir', 'enum', array('ASC','DESC'));

if (isset($_REQUEST['sortcol'])) {
   $_SESSION[$modul]['sortcol'] = $_REQUEST['sortcol'];
}
if (isset($_REQUEST['sortdir'])) {
   $_SESSION[$modul]['sortdir'] = $_REQUEST['sortdir'];
}
if (isset($_REQUEST['limit'])) {
   $_SESSION[$modul]['limit'] = $_REQUEST['limit'];
} else {
    $_REQUEST['limit'] = 50;
    $_SESSION[$modul]['limit'] = 50;
}
validate("limit","int");

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'profile_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['profile_d.php'] = '' . ss('Add Profile') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Profile_id
validate('profile_id', 'int nullable' );
$_SESSION[$modul]['profile_id'] = $_VALID['profile_id'];

// Profile_name
validate('profile_name', 'string' );
$_SESSION[$modul]['profile_name'] = $_VALID['profile_name'];

// Street
validate('street', 'string' );
$_SESSION[$modul]['street'] = $_VALID['street'];

// City
validate('city', 'string' );
$_SESSION[$modul]['city'] = $_VALID['city'];

// Country
validate('country', 'string' );
$_SESSION[$modul]['country'] = $_VALID['country'];

// Labels
validate('labels', 'string' );
$_SESSION[$modul]['labels'] = $_VALID['labels'];

// Api_key
validate('api_key', 'string nullable' );
$_SESSION[$modul]['api_key'] = $_VALID['api_key'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM profile WHERE profile_id = " . (int) $_REQUEST['profile_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
}

// where condition
if ($_VALID['profile_id']) {
	$where[] = "profile.profile_id =  " . $_VALIDDB['profile_id'];
}
$_SESSION[$modul]['profile_id'] = $_VALID['profile_id'];
if ($_VALID['profile_name']) {
	$where[] = "profile.profile_name LIKE '%" . mysqli_real_escape_string($con, $_VALID['profile_name']) . "%'";
}
$_SESSION[$modul]['profile_name'] = $_VALID['profile_name'];
if ($_VALID['street']) {
	$where[] = "profile.street LIKE '%" . mysqli_real_escape_string($con, $_VALID['street']) . "%'";
}
$_SESSION[$modul]['street'] = $_VALID['street'];
if ($_VALID['city']) {
	$where[] = "profile.city LIKE '%" . mysqli_real_escape_string($con, $_VALID['city']) . "%'";
}
$_SESSION[$modul]['city'] = $_VALID['city'];
if ($_VALID['country']) {
	$where[] = "profile.country LIKE '%" . mysqli_real_escape_string($con, $_VALID['country']) . "%'";
}
$_SESSION[$modul]['country'] = $_VALID['country'];
if ($_VALID['labels']) {
	$where[] = "profile.labels LIKE '%" . mysqli_real_escape_string($con, $_VALID['labels']) . "%'";
}
$_SESSION[$modul]['labels'] = $_VALID['labels'];
if ($_VALID['api_key']) {
	$where[] = "profile.api_key LIKE '%" . mysqli_real_escape_string($con, $_VALID['api_key']) . "%'";
}
$_SESSION[$modul]['api_key'] = $_VALID['api_key'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT profile_id,
profile.profile_name,
profile.street,
profile.city,
profile.country,
profile.labels,
profile.api_key
FROM profile
WHERE " . $where . "
ORDER BY " . $orderBy . "
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

    if (!$headless) {
    ?>
    <header class="page-header">
        <!-- <h2><?php echo ss('Profile'); ?></h2> -->
        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span><?php echo ss('Profile'); ?></span></li>
            </ol>
            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title"><?php echo ss('Profile'); ?></h2>
        </header>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-left">
                    <!-- Here goes nothing -->
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-right">
                                <span class="limit" onclick="setLimit(50);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 50)?'bold':'normal');?>">50</span>&nbsp;&nbsp;
                                <span class="limit" onclick="setLimit(200);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 200)?'bold':'normal');?>">200</span>&nbsp;&nbsp;
                                <span class="limit" onclick="setLimit(9999);$(this).css({'font-weight':'bold'});"style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 9999)?'bold':'normal');?>">∞</span>&nbsp;&nbsp;
                                <input type="hidden" class="search" name="limit" id="limit" value="<?php echo $_VALID['limit'];?>"><br><br>
                            </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-none table-hover" id="datatable-master">
    <?php
    }
    if (!$headless) {
        echo '<thead>';
        echo '<tr class="head">';echo '<th class="text-center">' . ss('Action') . '</th>';echo '<th class="hasmenu grey" id="th_profile_id" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_profile_id" onClick="changeSort(\'profile_id\')">' . ss('Profile_id') . '</a></th>';
echo '<th class="hasmenu grey" id="th_profile_name" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_profile_name" onClick="changeSort(\'profile_name\')">' . ss('Profile_name') . '</a></th>';
echo '<th class="hasmenu grey" id="th_street" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_street" onClick="changeSort(\'street\')">' . ss('Street') . '</a></th>';
echo '<th class="hasmenu grey" id="th_city" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_city" onClick="changeSort(\'city\')">' . ss('City') . '</a></th>';
echo '<th class="hasmenu grey" id="th_country" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_country" onClick="changeSort(\'country\')">' . ss('Country') . '</a></th>';
echo '<th class="hasmenu grey" id="th_labels" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_labels" onClick="changeSort(\'labels\')">' . ss('Labels') . '</a></th>';
echo '<th class="hasmenu grey" id="th_api_key" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_api_key" onClick="changeSort(\'api_key\')">' . ss('Api_key') . '</a></th>';
echo '</tr>';echo '<tr class="head">';echo '<th class="text-center" style="vertical-align:middle;font-size:20px;"><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;"title="' . ss('Reset Filter') . '"><i class="fa fa-filter"/></span></th>';echo '<th>
               <input class="form-control search" name="profile_id" id="profile_id" value="'.$_SESSION[$modul]['profile_id'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="profile_name" id="profile_name" value="'.$_SESSION[$modul]['profile_name'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="street" id="street" value="'.$_SESSION[$modul]['street'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="city" id="city" value="'.$_SESSION[$modul]['city'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="country" id="country" value="'.$_SESSION[$modul]['country'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="labels" id="labels" value="'.$_SESSION[$modul]['labels'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="api_key" id="api_key" value="'.$_SESSION[$modul]['api_key'].'" autocomplete="off"></th>';echo '</tr></thead><tbody class="list-body" id="list_tbody">';}
    
    if (!$listResult) {
        echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
    } else {
        $i = 0;
        while($row = mysqli_fetch_array($listResult)) {
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['profile_id'].'">';
            echo '<td nowrap class="text-center actions-hover actions-fade"><a href="profile_d.php?i=&amp;" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil" ></i></a>';
        // people with right to delete see the delete button
            if (R(3)){
                    echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="if (confirm(\'' . ss('Do you really want to delete the Profile?') . '\')) delRow(\''.$row['profile_id'].'\');" data-toggle="tooltip" data-placement="top">
                    <i class="fa fa-trash-o" style="color:red;font-size:20px;"/></a>';}
                    echo '</td>';
			echo '<td onClick="location.href=\'profile_d.php?i='.($i-1).'&amp;profile_id='.$row['profile_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['profile_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['profile_name'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['street'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['city'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['country'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['labels'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['api_key'])) . '</td>';}
}

if (!$headless) { ?>
            </tbody>
            </table>
        </div>
    </section>

<script type="text/javascript">
$(function(){
    $(document).contextmenu({
        delegate: ".hasmenu",
        autoFocus: true,
        preventContextMenuForPopup: true,
        preventSelect: true,
        taphold: true,
        addClass: "list-group list-group-hover",
        menu: [
            {title: "Add Column", cmd: "add", addClass: "list-group-item"},
            {title: "Delete Column", cmd: "delete", addClass: "list-group-item"},
            //{title: "Hide Column", cmd: "hide"},
        ],
        // Handle menu selection
        select: function(event, ui) {
            var $target = ui.target;
            switch(ui.cmd){
                case "add":
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=profile&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=profile&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=profile&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=profile&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                            }).done( function( data ) {
                                location.reload();
                                return false;
                            });
                        });
                    });
                    break
            }
        },
        beforeOpen: function(event, ui) {
            var $menu = ui.menu,
                $target = ui.target,
                extraData = ui.extraData; // passed when menu was opened by call to open()
        }
    });
    
    $("#triggerPopup").click(function(){
        // Trigger popup menu on the first target element
        $(document).contextmenu("open", $(".hasmenu:first"), {foo: "bar"});
        setTimeout(function(){
            $(document).contextmenu("close");
        }, 2000);
    });
});

function delRow(pk) {
    $.ajax({
      url: 'a/profile_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'profile_id';
var sortdir = '';
var del = '';
function updateList() {
    var url = '<?=$_REQUEST['PHP_SELF']?>?headless&sortcol='+sortcol+'&sortdir='+sortdir+'&del='+del;
    var filterparams = '';


    // inputs
    var val = '';
    $('.search:input:not([type=radio])').each(function(index, obj) {
        val = $('#' + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + encodeURIComponent($('#' + obj.name).val());
    });
    
    $('.search:input[type=radio]').each(function(index, obj) {
        val = $('#' + obj.name + ':checked').val();
        if (typeof val !== 'undefined') filterparams += '&' + obj.name + '=' + val;
    });

    $('.bw select').each(function(index, obj) {
        val = $("#" + obj.name).val();
        if (val != '') filterparams += '&' + obj.name + '=' + encodeURIComponent($('#' + obj.name).val());
    });

    url += filterparams;
    $.get(url, function(data) {
        $('#list_tbody').html(data);

        // also add the filterparam to the xls export
        $('#xlsbutton').attr('href',$('#xlsbutton').attr('href') + filterparams);
    });

}
function changeSort(col) {

    if (sortcol == col) {
        sortdir = (sortdir == 'DESC') ? 'ASC' : 'DESC';
    } else {
        sortdir = 'DESC';
    }
    sortcol = col;

    updateList();
}

function resetFilter() {
    $('.search:input').each(function(index, obj) {
        $('#' + obj.name).val('');
    });

    $('.search:input[type=radio]').each(function(index, obj) {
        $('#' + obj.name + ':checked').removeAttr('checked');
    });
    updateList();
}

function setLimit(limit) {
    $('#limit').val(limit);
    $('.limit').css({'font-weight':'normal'});
    updateList();
}

$('.search:input').keyup(function(index) {
    updateList();
});
$('.search:input').change(function(index) {
    updateList();
});
$('.bw select').change(function() {
    updateList();
});

jQuery(document).ready(function(){
    $("#loading-image")
    .bind("ajaxSend", function(){
    $(this).show();
    })
    .bind("ajaxComplete", function(){
    $(this).hide();
    });
    jQuery('#loading-image').hide();
  //  updateList();
  $(".dotted td").on("click", function() {
        $(".dotted").removeClass("selected");
        var tr = $(this).parent();
        if(tr.hasClass("selected")) {
            tr.removeClass("selected");
        } else {
            localStorage.setItem("<?Php echo $modul; ?>_highlight", tr.attr('id'));
            tr.addClass("selected");
        }
    });

    if(localStorage.getItem("<?Php echo $modul; ?>_highlight")!=""){
        $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).addClass("selected");
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).offset().top-200
        }, 0);
    }

});

// hook profile javascript
</script>
<style type="text/css" >
#loading-image {

	width: 65px;
	height: 55px;
	position: fixed;
	right: 160px;
	z-index: 1;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	/*	background-color: #333;
	border-radius: 10px; */
	margin-right:580px;
	top:260px;;
	-khtml-border-radius: 10px;
}
</style>
<?php
require("inc/footer.inc.php");
} 
    ?>