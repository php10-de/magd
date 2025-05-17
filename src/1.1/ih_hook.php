<?php 
    $modul="ih_hook";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view ih_hook
$groupID = 29;
GRGR($groupID);

// include module if exists
if (file_exists(MODULE_ROOT.'ih/hook.php')) {
    require MODULE_ROOT.'ih/hook.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'ih_hook_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = (isset($_REQUEST['headless']))?true:false;
$n4a['ih_hook_d.php'] = '' . ss('Add Ih_hook') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// Ih_hook_id
validate('ih_hook_id', 'int nullable' );
$_SESSION[$modul]['ih_hook_id'] = $_VALID['ih_hook_id'];

// Ih_repo_id
validate('ih_repo_id', 'int' );
$_SESSION[$modul]['ih_repo_id'] = $_VALID['ih_repo_id'];

// Ih_branch_id
validate('ih_branch_id', 'string' );
$_SESSION[$modul]['ih_branch_id'] = $_VALID['ih_branch_id'];
if (isset($_REQUEST['submitted']) AND is_array($_MISSING) AND count($_MISSING)) {
	$error[] = ss('missing fields');
}
// delete
if (isset($_REQUEST['delete'])) {
	$sql = "DELETE FROM ih_hook WHERE ih_hook_id = " . (int) $_REQUEST['ih_hook_id'];
	mysqli_query($con, $sql) or error_log(mysqli_error());
}

// where condition
if ($_VALID['ih_hook_id']) {
	$where[] = "ih_hook.ih_hook_id =  " . $_VALIDDB['ih_hook_id'];
}
$_SESSION[$modul]['ih_hook_id'] = $_VALID['ih_hook_id'];
if ($_VALID['ih_repo_id']) {
	$where[] = "ih_hook.ih_repo_id =  " . $_VALIDDB['ih_repo_id'];
}
$_SESSION[$modul]['ih_repo_id'] = $_VALID['ih_repo_id'];
if ($_VALID['ih_branch_id']) {
	$where[] = "ih_hook.ih_branch_id LIKE '%" . mysqli_real_escape_string($con, $_VALID['ih_branch_id']) . "%'";
}
$_SESSION[$modul]['ih_branch_id'] = $_VALID['ih_branch_id'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT ih_hook_id,
ih_hook.ih_repo_id,
ih_hook.ih_branch_id
FROM ih_hook
WHERE " . $where . "
ORDER BY " . $orderBy . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
require MODULE_ROOT . 'ih/hook_list_result.inc.php';

if (!$headless) {
?>
<div class="contentheadline"><?php echo ss('Ih_hook')?></div>
<br>
<div class="contenttext">
    <span class="limit" onclick="setLimit(50);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 50)?'bold':'normal')?>">50</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(200);$(this).css({'font-weight':'bold'});" style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 200)?'bold':'normal')?>">200</span>&nbsp;&nbsp;
    <span class="limit" onclick="setLimit(9999);$(this).css({'font-weight':'bold'});"style="cursor: pointer; cursor: hand; font-weight: <?php echo (($_VALID['limit'] == 9999)?'bold':'normal')?>">∞</span>&nbsp;&nbsp;
    <input type="hidden" class="search" name="limit" id="limit" value="<?php echo $_VALID['limit']?>"><br><br>
<!-- hook vor ih_hook liste -->
<table cellspacing="0" cellpadding="0" class="bw">
<?php
}
if (!$headless) {
    echo '<tr class="head">';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'ih_hook_id\')">' . ss('Ih_hook_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="ih_hook_id" id="ih_hook_id" value="'.$_SESSION[$modul]['ih_hook_id'].'" autocomplete="off">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'ih_repo_id\')">' . ss('Ih_repo_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="ih_repo_id" id="ih_repo_id" value="'.$_SESSION[$modul]['ih_repo_id'].'" autocomplete="off">
           </th>';
    echo '<th class="grey" nowrap="nowrap"><a href="javascript:void(0)" onClick="changeSort(\'ih_branch_id\')">' . ss('Ih_branch_id') . '</a>&nbsp;&nbsp;<br>
           <input class="search" name="ih_branch_id" id="ih_branch_id" value="'.$_SESSION[$modul]['ih_branch_id'].'" autocomplete="off">
           </th>';
    echo '<th><span onclick="resetFilter();" style="cursor: pointer; cursor: hand;"><i class="fa fa-trash-o" title="'. ss('Reset Filter').'"></i></span></th>';
    echo '</tr><tbody id="list_tbody">';
}
  if (!$listResult) {
    echo '<tr><td colspan="3">'.ss('No entries found').'</td></tr>';
  } else {
    $i = 0;
    while($row = mysqli_fetch_array($listResult)) {
      echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['ih_hook_id'].'">';
			echo '<td '.$mouseover.' onClick="location.href=\'ih_hook_d.php?i='.($i-1).'&amp;ih_hook_id='.$row['ih_hook_id'].'\'" nowrap>' . str_limit(htmlspecialchars($row['ih_hook_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['ih_repo_id'])) . '</td>';
			echo '<td nowrap>' . str_limit(htmlspecialchars($row['ih_branch_id'])) . '</td>';
            echo '<td nowrap><a href="ih_hook_d.php?i=&amp;ih_hook_id=' . (int) $row['ih_hook_id'] . '"><i class="fa fa-pencil" title="' . ss('Edit') . '"></i></a>';
// people with right to delete see the delete button
    if (R(3))
            echo '&nbsp;&nbsp;<a href="#" onclick="if (confirm(\'' . ss('Do you really want to delete the Ih_hook?') . '\')) delRow('.$row['ih_hook_id'].');">
            <i class="fa fa-trash-o" title="'.ss('Reset Filter').'"></i></a>';
            echo '</td>';
		echo '</tr>';
	}
}

if (!$headless) { ?>
</table>
</div>

<script type="text/javascript">
function delRow(pk) {
    $.ajax({
      url: 'a/ih_hook_del.php?id='+pk
    });
    $('#tr_'+pk).hide();
}

var sortcol = 'ih_hook_id';
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
    }

});

// hook ih_hook javascript
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