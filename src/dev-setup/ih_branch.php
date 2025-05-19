<?php 
    $modul="ih_branch";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view ih_branch
$groupID = 29;
GRGR($groupID);

$_SESSION['entity'] = 'ih_branch';


// include module if exists
if (file_exists(MODULE_ROOT.'ih/branch.php')) {
    require MODULE_ROOT.'ih/branch.php';
}

    
$headless = (isset($_REQUEST['headless']))?true:false;
//$n4a['ih_branch_d.php'] = '' . ss('Add Ih_branch') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Name
validate('name', 'string nullable' );
$_SESSION[$modul]['name'] = $_VALID['name'];

// Is_active
validate('is_active', 'ckb' );
$_SESSION[$modul]['is_active'] = $_VALID['is_active'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM ih_branch WHERE ih_repo_id = '" . $_VALIDDB['ih_repo_id'] . "' AND name = '" . $_VALIDDB['name'] . "'";
	mysqli_query($con, $sql) or error_log(mysqli_error($con));
	/*** After Delete Query ***/
}

// where condition
if ($_VALID['name']) {
	$where[] = "ih_branch.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['name']) . "%'";
}
$_SESSION[$modul]['name'] = $_VALID['name'];
if (isset($_VALID['is_active'])) {
	$where[] = "ih_branch.is_active = " . (($_VALIDDB['is_active'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['is_active'] = $_VALID['is_active'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT ih_repo_id,
ih_branch.name,
ih_branch.is_active
FROM ih_branch
WHERE 1=1
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
require MODULE_ROOT . 'ih/branch_list_result.inc.php';

    if (!$headless) {
    ?>
    <header class="page-header">
        <!-- <h2><?php echo ss('Ih_branch'); ?></h2> -->
        <div class="right-wrapper pull-right">
            <ol class="breadcrumbs">
                <li>
                    <a href="index.php">
                        <i class="fa fa-home"></i>
                    </a>
                </li>
                <li><span><?php echo ss('Ih_branch'); ?></span></li>
            </ol>
            <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
        </div>
    </header>
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title"><?php echo ss('Ih_branch'); ?></h2>
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
        echo '<tr class="head">';echo '<th class="text-center">' . ss('Action') . '</th>';echo '<th class="hasmenu grey" id="th_name" nowrap="nowrap">' . ss('Name') . '</th>';
echo '<th class="hasmenu grey" id="th_is_active" nowrap="nowrap">' . ss('Is_active') . '</th>';
echo '</tr>';echo '<tr class="head">';echo '<th class="text-center" style="vertical-align:middle;font-size:20px;"><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;"title="' . ss('Reset Filter') . '"><i class="fa fa-filter"/></span></th>';echo '<th></th>';echo '<th></th>';echo '</tr></thead><tbody class="list-body" id="list_tbody">';}
    
    if (!$listResult) {
        echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
    } else {
        $cnt = count($listResult);
        for ($i = 0; $i < $cnt; $i++) {
        $row = $listResult[$i];
        echo '<tr class="dotted ' .  ((($i % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['name'].'">';
            echo '<td nowrap class="text-center actions-hover actions-fade"><a href="ih_branch_d.php?i=&amp;" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top"><i style="font-size:20px" class="fa fa-pencil" ></i></a>';
        // people with right to delete see the delete button
            if (R(3)){
                    echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="if (confirm(\'' . ss('Do you really want to delete the Ih_branch?') . '\')) delRow(\''.$row['ih_branch_id'].'\');" data-toggle="tooltip" data-placement="top">
                    <i class="fa fa-trash-o" style="color:red;font-size:20px;"/></a>';}
                    echo '</td>';
			echo '<td onClick="location.href=\'ih_hook_tree.php?i='.($i-1).'&amp;headless=1&ih_branch_id='.$row['ih_branch_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['name'])) . '</td>';
			echo '<td nowrap>' . (($row['is_active'] == '1') ? ss('yes') : ss('no')) . '</td>';}
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=ih_branch&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=ih_branch&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=ih_branch&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=ih_branch&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
      url: 'a/ih_branch_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

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