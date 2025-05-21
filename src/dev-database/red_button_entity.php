<?php 
    $modul="red_button_entity";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view red_button_entity
$groupID = 29;
GRGR($groupID);

$_SESSION['entity'] = 'red_button_entity';


// include module if exists
if (file_exists(MODULE_ROOT.'red_button_entity/red_button_entity.php')) {
    require MODULE_ROOT.'red_button_entity/red_button_entity.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'red_button_entity_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['red_button_entity.php?rebuild'] = ss('Rebuild');
$n4a['red_button_entity_d.php'] = '' . ss('Add Red_button_entity') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Entity_name
validate('entity_name', 'string' );
$_SESSION[$modul]['entity_name'] = $_VALID['entity_name'];

// Data_name
validate('data_name', 'string nullable' );
$_SESSION[$modul]['data_name'] = $_VALID['data_name'];

// Data_type
validate('data_type', 'enum nullable', array('int','numeric','string','ckb','date','blob','media') );
$_SESSION[$modul]['data_type'] = $_VALID['data_type'];

// Is_nullable
validate('is_nullable', 'ckb nullable' );
$_SESSION[$modul]['is_nullable'] = $_VALID['is_nullable'];

// After
validate('after', 'string nullable' );
$_SESSION[$modul]['after'] = $_VALID['after'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM red_button_entity WHERE red_button_entity_id = " . (int) $_REQUEST['red_button_entity_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
}

// where condition
if ($_VALID['red_button_entity_id']) {
	$where[] = "red_button_entity.red_button_entity_id =  " . $_VALIDDB['red_button_entity_id'];
}
$_SESSION[$modul]['red_button_entity_id'] = $_VALID['red_button_entity_id'];
if ($_VALID['entity_name']) {
	$where[] = "red_button_entity.entity_name LIKE '%" . mysqli_real_escape_string($con, $_VALID['entity_name']) . "%'";
}
$_SESSION[$modul]['entity_name'] = $_VALID['entity_name'];
if ($_VALID['data_name']) {
	$where[] = "red_button_entity.data_name LIKE '%" . mysqli_real_escape_string($con, $_VALID['data_name']) . "%'";
}
$_SESSION[$modul]['data_name'] = $_VALID['data_name'];
if ($_VALID['data_type']) {
	$where[] = "red_button_entity.data_type =  '" . mysqli_real_escape_string($con, $_VALID['data_type']) . "'";
}
$_SESSION[$modul]['data_type'] = $_VALID['data_type'];
if (isset($_VALID['is_nullable'])) {
	$where[] = "red_button_entity.is_nullable = " . (($_VALIDDB['is_nullable'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['is_nullable'] = $_VALID['is_nullable'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT red_button_entity_id,
red_button_entity.entity_name,
red_button_entity.data_name,
red_button_entity.data_type,
red_button_entity.is_nullable,
red_button_entity.after
FROM red_button_entity
WHERE " . $where . "
ORDER BY " . $orderBy . "
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

    if (!$headless) {
    ?>
    <header class="page-header">
        <!-- <h2><?php echo ss('Red_button_entity'); ?></h2> -->
        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span><?php echo ss('Red_button_entity'); ?></span></li>
            </ol>
            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title"><?php echo ss('Red_button_entity'); ?></h2>
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
        echo '<tr class="head">';echo '<th class="text-center">' . ss('Action') . '</th>';echo '<th class="hasmenu grey" id="th_red_button_entity_id" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_red_button_entity_id" onClick="changeSort(\'red_button_entity_id\')">' . ss('Red_button_entity_id') . '</a></th>';
echo '<th class="hasmenu grey" id="th_entity_name" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_entity_name" onClick="changeSort(\'entity_name\')">' . ss('Entity_name') . '</a></th>';
echo '<th class="hasmenu grey" id="th_data_name" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_data_name" onClick="changeSort(\'data_name\')">' . ss('Data_name') . '</a></th>';
echo '<th class="hasmenu grey" id="th_data_type" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_data_type" onClick="changeSort(\'data_type\')">' . ss('Data_type') . '</a></th>';
echo '<th class="hasmenu grey" id="th_is_nullable" nowrap="nowrap"><a href="javascript:void(0)" id="hlink_is_nullable" onClick="changeSort(\'is_nullable\')">' . ss('Is_nullable') . '</a></th>';
echo '</tr>';echo '<tr class="head">';echo '<th class="text-center" style="vertical-align:middle;font-size:20px;"><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;"title="' . ss('Reset Filter') . '"><i class="fa fa-filter"/></span></th>';echo '<th>
               <input class="form-control search" name="red_button_entity_id" id="red_button_entity_id" value="'.$_SESSION[$modul]['red_button_entity_id'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="entity_name" id="entity_name" value="'.$_SESSION[$modul]['entity_name'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="data_name" id="data_name" value="'.$_SESSION[$modul]['data_name'].'" autocomplete="off"></th>';echo '<th>
               <input class="form-control search" name="data_type" id="data_type" value="'.$_SESSION[$modul]['data_type'].'" autocomplete="off"></th>';echo '<th>
                <input type="radio" name="is_nullable" id="is_nullable" class="search" value="_filter_0_"'.(($_SESSION[$modul]['is_nullable'] == '_filter_0_')?' checked="checked"':'').'>
                <input type="radio" name="is_nullable" id="is_nullable" class="search" value="_filter_1_"'.(($_SESSION[$modul]['is_nullable'] == '_filter_1_')?' checked="checked"':'').'></th>';echo '</tr></thead><tbody class="list-body" id="list_tbody">';}
    
    if (!$listResult) {
        echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
    } else {
        $i = 0;
        while($row = mysqli_fetch_array($listResult)) {
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['red_button_entity_id'].'">';
            echo '<td nowrap class="text-center actions-hover actions-fade"><a href="red_button_entity_d.php?i=&amp;" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil" ></i></a>';
        // people with right to delete see the delete button
            if (R(3)){
                    echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="if (confirm(\'' . ss('Do you really want to delete the Red_button_entity?') . '\')) delRow(\''.$row['red_button_entity_id'].'\');" data-toggle="tooltip" data-placement="top">
                    <i class="fa fa-trash-o" style="color:red;font-size:20px;"/></a>';}
                    echo '</td>';
			echo '<td onClick="location.href=\'red_button_entity_d.php?i='.($i-1).'&amp;red_button_entity_id='.$row['red_button_entity_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['red_button_entity_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['entity_name'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['data_name'])) . '</td>';
			echo '<td nowrap>' . $row['data_type'] . '</td>';
			echo '<td nowrap>' . (($row['is_nullable'] == '1') ? ss('yes') : ss('no')) . '</td>';}
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=red_button_entity&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=red_button_entity&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=red_button_entity&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=red_button_entity&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
      url: 'a/red_button_entity_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'red_button_entity_id';
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

// hook red_button_entity javascript
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