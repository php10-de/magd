<?php 
    $modul="cron";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view cron
$groupID = 1001;
GRGR($groupID);

$_SESSION['entity'] = 'cron';


// include module if exists
if (file_exists(MODULE_ROOT.'cron/cron.php')) {
    require MODULE_ROOT.'cron/cron.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'cron_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['cron_d.php'] = '' . ss('Add Cron') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Cron_id
validate('cron_id', 'int nullable' );
$_SESSION[$modul]['cron_id'] = $_VALID['cron_id'];

// Task
validate('task', 'string' );
$_SESSION[$modul]['task'] = $_VALID['task'];

// Active
validate('active', 'ckb' );
$_SESSION[$modul]['active'] = $_VALID['active'];

// Mhdmd
validate('mhdmd', 'string' );
$_SESSION[$modul]['mhdmd'] = $_VALID['mhdmd'];

// File
validate('file', 'string' );
$_SESSION[$modul]['file'] = $_VALID['file'];

// Parameters
validate('parameters', 'string nullable' );
$_SESSION[$modul]['parameters'] = $_VALID['parameters'];

// Ran_at
validate('ran_at', 'int nullable' );
$_SESSION[$modul]['ran_at'] = $_VALID['ran_at'];

// End_time
validate('end_time', 'int nullable' );
$_SESSION[$modul]['end_time'] = $_VALID['end_time'];

// Ok
validate('ok', 'ckb' );
$_SESSION[$modul]['ok'] = $_VALID['ok'];

// Log_level
validate('log_level', 'ckb nullable' );
$_SESSION[$modul]['log_level'] = $_VALID['log_level'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM cron WHERE cron_id = " . (int) $_REQUEST['cron_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
}

// where condition
if ($_VALID['cron_id']) {
	$where[] = "cron.cron_id =  " . $_VALIDDB['cron_id'];
}
$_SESSION[$modul]['cron_id'] = $_VALID['cron_id'];
if ($_VALID['task']) {
	$where[] = "cron.task LIKE '%" . mysqli_real_escape_string($con, $_VALID['task']) . "%'";
}
$_SESSION[$modul]['task'] = $_VALID['task'];
if (isset($_VALID['active'])) {
	$where[] = "cron.active = " . (($_VALIDDB['active'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['active'] = $_VALID['active'];
if ($_VALID['mhdmd']) {
	$where[] = "cron.mhdmd LIKE '%" . mysqli_real_escape_string($con, $_VALID['mhdmd']) . "%'";
}
$_SESSION[$modul]['mhdmd'] = $_VALID['mhdmd'];
if ($_VALID['file']) {
	$where[] = "cron.file LIKE '%" . mysqli_real_escape_string($con, $_VALID['file']) . "%'";
}
$_SESSION[$modul]['file'] = $_VALID['file'];
if ($_VALID['parameters']) {
	$where[] = "cron.parameters LIKE '%" . mysqli_real_escape_string($con, $_VALID['parameters']) . "%'";
}
$_SESSION[$modul]['parameters'] = $_VALID['parameters'];
if ($_VALID['ran_at']) {
	$where[] = "cron.ran_at =  " . $_VALIDDB['ran_at'];
}
$_SESSION[$modul]['ran_at'] = $_VALID['ran_at'];
if ($_VALID['end_time']) {
	$where[] = "cron.end_time =  " . $_VALIDDB['end_time'];
}
$_SESSION[$modul]['end_time'] = $_VALID['end_time'];
if (isset($_VALID['ok'])) {
	$where[] = "cron.ok = " . (($_VALIDDB['ok'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['ok'] = $_VALID['ok'];
if (isset($_VALID['log_level'])) {
	$where[] = "cron.log_level = " . (($_VALIDDB['log_level'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['log_level'] = $_VALID['log_level'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT cron_id,
cron.task,
cron.active,
cron.mhdmd,
cron.file,
cron.parameters,
cron.ran_at,
cron.end_time,
cron.ok,
cron.log_level
FROM cron
WHERE " . $where . "
ORDER BY " . $orderBy . "
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

    if (!$headless) {
    ?>
    <header class="page-header">
        <!-- <h2><?php echo ss('Cron'); ?></h2> -->
        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span><?php echo ss('Cron'); ?></span></li>
            </ol>
            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title"><?php echo ss('Cron'); ?></h2>
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
        echo '<tr class="head">';echo '<th class="text-center">' . ss('Action') . '</th>';echo '<th class="hasmenu grey" id="th_cron_id" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_cron_id" onClick="changeSort(\'cron_id\')">' . ss('Cron_id') . '</a></th>';
echo '<th class="hasmenu grey" id="th_task" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_task" onClick="changeSort(\'task\')">' . ss('Task') . '</a></th>';
echo '<th class="hasmenu grey" id="th_active" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_active" onClick="changeSort(\'active\')">' . ss('Active') . '</a></th>';
echo '<th class="hasmenu grey" id="th_mhdmd" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_mhdmd" onClick="changeSort(\'mhdmd\')">' . ss('Mhdmd') . '</a></th>';
echo '<th class="hasmenu grey" id="th_file" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_file" onClick="changeSort(\'file\')">' . ss('File') . '</a></th>';
echo '<th class="hasmenu grey" id="th_parameters" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_parameters" onClick="changeSort(\'parameters\')">' . ss('Parameters') . '</a></th>';
echo '<th class="hasmenu grey" id="th_ran_at" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_ran_at" onClick="changeSort(\'ran_at\')">' . ss('Ran_at') . '</a></th>';
echo '<th class="hasmenu grey" id="th_end_time" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_end_time" onClick="changeSort(\'end_time\')">' . ss('End_time') . '</a></th>';
echo '<th class="hasmenu grey" id="th_ok" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_ok" onClick="changeSort(\'ok\')">' . ss('Ok') . '</a></th>';
echo '<th class="hasmenu grey" id="th_log_level" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_log_level" onClick="changeSort(\'log_level\')">' . ss('Log_level') . '</a></th>';
echo '</tr>';echo '<tr class="head">';echo '<th class="text-center" style="vertical-align:middle;font-size:20px;"><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;"title="' . ss('Reset Filter') . '"><i class="fa fa-filter"/></span></th>';echo '<th>
               <input class="form-control search" name="cron_id" id="cron_id" value="'.$_SESSION[$modul]['cron_id'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="task" id="task" value="'.$_SESSION[$modul]['task'].'" autocomplete="off"></th>';echo '<th>
                <input type="radio" name="active" id="active" class="search" value="_filter_0_"'.(($_SESSION[$modul]['active'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="active" id="active" class="search" value="_filter_1_"'.(($_SESSION[$modul]['active'] == '_filter_1_')?' checked="checked"':'').'></th>';echo '<th>
               <input class="form-control search" name="mhdmd" id="mhdmd" value="'.$_SESSION[$modul]['mhdmd'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="file" id="file" value="'.$_SESSION[$modul]['file'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="parameters" id="parameters" value="'.$_SESSION[$modul]['parameters'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="ran_at" id="ran_at" value="'.$_SESSION[$modul]['ran_at'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="end_time" id="end_time" value="'.$_SESSION[$modul]['end_time'].'" autocomplete="off"></th>';echo '<th>
                <input type="radio" name="ok" id="ok" class="search" value="_filter_0_"'.(($_SESSION[$modul]['ok'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="ok" id="ok" class="search" value="_filter_1_"'.(($_SESSION[$modul]['ok'] == '_filter_1_')?' checked="checked"':'').'></th>';echo '<th>
                <input type="radio" name="log_level" id="log_level" class="search" value="_filter_0_"'.(($_SESSION[$modul]['log_level'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="log_level" id="log_level" class="search" value="_filter_1_"'.(($_SESSION[$modul]['log_level'] == '_filter_1_')?' checked="checked"':'').'></th>';echo '</tr></thead><tbody class="list-body" id="list_tbody">';}
    
    if (!$listResult) {
        echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
    } else {
        $i = 0;
        while($row = mysqli_fetch_array($listResult)) {
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['cron_id'].'">';
            echo '<td nowrap class="text-center actions-hover actions-fade"><a href="cron_d.php?i=&amp;" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil" ></i></a>';
        // people with right to delete see the delete button
            if (R(3)){
                    echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="if (confirm(\'' . ss('Do you really want to delete the Cron?') . '\')) delRow(\''.$row['cron_id'].'\');" data-toggle="tooltip" data-placement="top">
                    <i class="fa fa-trash-o" style="color:red;font-size:20px;"/></a>';}
                    echo '</td>';
			echo '<td onClick="location.href=\'cron_d.php?i='.($i-1).'&amp;cron_id='.$row['cron_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['cron_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['task'])) . '</td>';
			echo '<td nowrap>' . (($row['active'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['mhdmd'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['file'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['parameters'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['ran_at'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['end_time'])) . '</td>';
			echo '<td nowrap>' . (($row['ok'] == '1') ? ss('yes') : ss('no')) . '</td>';
			echo '<td nowrap>' . (($row['log_level'] == '1') ? ss('yes') : ss('no')) . '</td>';}
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=cron&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=cron&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=cron&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=cron&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
      url: 'a/cron_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'cron_id';
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

// hook cron javascript
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