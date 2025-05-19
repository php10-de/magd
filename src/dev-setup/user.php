<?php 
$modul="user";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view user
$groupID = 6;
GRGR($groupID);

$_SESSION['entity'] = 'user';


// include module if exists
if (file_exists(MODULE_ROOT.'user/user.php')) {
    require MODULE_ROOT.'user/user.php';
}

/*** General Table variables **/
// fill parameters from session
if (!isset($_REQUEST['headless']) && !isset($_REQUEST['requestfilter']) && isset($_SESSION[$modul])) {
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'user_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = isset($_REQUEST['headless']);
$n4a['user_d.php'] = '' . ss('Add User') . '';
if (!$headless) require("inc/header.inc.php");

/*** Validation ***/

// User_id
validate('user_id', 'int nullable' );
// Admin group for Administrators only
if ($_VALID['user_id'] == 1) {
    GRGR(1);
}
$_SESSION[$modul]['user_id'] = $_VALID['user_id'];

// Email
validate('email', 'string' );
$_SESSION[$modul]['email'] = $_VALID['email'];

// Password
validate('password', 'string' );
$_SESSION[$modul]['password'] = $_VALID['password'];

// Firstname
validate('firstname', 'string' );
$_SESSION[$modul]['firstname'] = $_VALID['firstname'];

// Lastname
validate('lastname', 'string' );
$_SESSION[$modul]['lastname'] = $_VALID['lastname'];

// Is_active
validate('is_active', 'ckb' );
$_SESSION[$modul]['is_active'] = $_VALID['is_active'];

// Lang
validate('lang', 'string' );
$_SESSION[$modul]['lang'] = $_VALID['lang'];
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}
// where condition
if ($_VALID['email']) {
	$where[] = "user.email LIKE '%" . mysqli_real_escape_string($con, $_VALID['email']) . "%'";
}
$_SESSION[$modul]['email'] = $_VALID['email'];
if ($_VALID['firstname']) {
	$where[] = "user.firstname LIKE '%" . mysqli_real_escape_string($con, $_VALID['firstname']) . "%'";
}
$_SESSION[$modul]['firstname'] = $_VALID['firstname'];
if ($_VALID['lastname']) {
	$where[] = "user.lastname LIKE '%" . mysqli_real_escape_string($con, $_VALID['lastname']) . "%'";
}
$_SESSION[$modul]['lastname'] = $_VALID['lastname'];
if (isset($_VALID['is_active'])) {
	$where[] = "user.is_active = " . (($_VALIDDB['is_active'] == 1) ? 1 : 0);
}
$_SESSION[$modul]['is_active'] = $_VALID['is_active'];
// Admin group for Administrators only
if (!GR(1)) {
    $where[] = "user.user_id != 1";
}
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT user.user_id,
user.email,
user.password,
user.firstname,
user.lastname, GROUP_CONCAT(shortname SEPARATOR ', ') as gr_names,
user.is_active,
user.dbinsert,
user.lang,
user.dbupdate
FROM user
LEFT JOIN user2gr ON user2gr.user_id=user.user_id
LEFT JOIN gr ON gr.gr_id=user2gr.gr_id
WHERE " . $where . "
GROUP BY user.user_id, user.firstname, user.lastname
ORDER BY " . $orderBy . "
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<header class="page-header">
    <!-- <h2><?php echo ss('User'); ?></h2> -->
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('User'); ?></span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('User'); ?></h2>
    </header>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-left">
                    <a class="btn btn-success" href="user_d.php" title=" <?php echo ss('Add new'); ?>"><i class="fa fa-plus"></i> <?php echo ss('Add new'); ?></a>
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
    echo '<tr class="head">';
    echo '<th class="text-center">' . ss('Action') . '</th>';
        
    // title email
    echo '<th class="hasmenu grey" id="th_email" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_email" onClick="changeSort(\'email\')">' . ss('Email') . '</a>
    </th>';
        
    // title firstname
    echo '<th class="hasmenu grey" id="th_firstname" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_firstname" onClick="changeSort(\'firstname\')">' . ss('Firstname') . '</a>
    </th>';
        
    // title lastname
    echo '<th class="hasmenu grey" id="th_lastname" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_lastname" onClick="changeSort(\'lastname\')">' . ss('Lastname') . '</a>
    </th>';
        
    // title is_active
    echo '<th class="hasmenu grey" id="th_is_active" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_is_active" onClick="changeSort(\'is_active\')">' . ss('Is_active') . '</a>
    </th>';

    echo '</tr>';

    echo '<tr class="head">';
    echo '<th class="text-center" style="vertical-align:middle;font-size:20px;">
            <span onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;"title="' . ss('Reset Filter') . '">
                <i class="fa fa-filter"/>
            </span>
           </th>';

    // filter email
    echo '<th>
    <input class="form-control search" name="email" id="email" value="'.$_SESSION[$modul]['email'].'" autocomplete="off"></th>';

    // filter firstname
    echo '<th>
    <input class="form-control search" name="firstname" id="firstname" value="'.$_SESSION[$modul]['firstname'].'" autocomplete="off"></th>';

    // filter lastname
    echo '<th>
    <input class="form-control search" name="lastname" id="lastname" value="'.$_SESSION[$modul]['lastname'].'" autocomplete="off"></th>';

    // filter is_active
    echo '<th>
    <input type="radio" name="is_active" id="is_active" class="search" value="_filter_0_"'.(($_SESSION[$modul]['is_active'] == '_filter_0_')?' checked="checked"':'').'>
    <input type="radio" name="is_active" id="is_active" class="search" value="_filter_1_"'.(($_SESSION[$modul]['is_active'] == '_filter_1_')?' checked="checked"':'').'></th>';

    echo '</tr>
    </thead>
    <tbody class="list-body" id="list_tbody">';
}
    
if (!$listResult) {
    echo '<tr>
    <td colspan="3">
    '.ss('No entries found').'
    </td>
    </tr>';
} else {
    $i = 0;
    while($row = mysqli_fetch_array($listResult)) {
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['user_id'].'">';
        echo '<td nowrap class="text-center actions-hover actions-fade">
        <a href="user_d.php?i=$index&amp;user_id=' . (int) $row['user_id'] . '" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top">
        <i style="font-size:20px" class="fa fa-pencil" ></i>
        </a>';
        // people with right to delete see the delete button
        if (R(3)){
            echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="delRow(\''.$row['user_id'].'\');" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-trash-o" style="color:red;font-size:20px;"></i></a>';
        }

            if (R(1)){
                    echo '&nbsp;&nbsp;<a href="#" title="' . ss('Login') . '" onclick="if (confirm(\'' . ss('Use the profile of this user?') . '\')) location.href=\'login.php?rn&id='.$row['user_id'].'\'" data-toggle="tooltip" data-placement="top">
                    <i class="fa fa-bell" style="color:blue;font-size:20px;"></i></a>';}
        echo '</td>';

        // email
        echo '<td onClick="location.href=\'user_d.php?i='.($i-1).'&amp;user_id='.$row['user_id'].'\'" nowrap>
        ' . str_limit(htmlspecialchars($row['email'])) . '
        </td>';

        // firstname
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['firstname'])) . '
        </td>';

        // lastname
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['lastname'])) . '
        </td>';

        // is_active
        echo '<td nowrap>
        ' . (($row['is_active'] == '1') ? ss('yes') : ss('no')) . '
        </td>';
    }
}

if (!$headless) { ?>
        </tbody>
        </table>
    </div>
</section>
    <div id="modalDelete" class="modal-block modal-header-color modal-block-danger mfp-hide">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><?php echo ss('Delete confirmation'); ?></h2>
            </header>
            <div class="panel-body">
                <div class="modal-wrapper">
                    <div class="modal-icon">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="modal-text">
                        <p><?php echo ss('Do you really want to delete the User?'); ?></p>
                    </div>
                </div>
            </div>
            <footer class="panel-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-danger modal-confirm"><?php echo ss('Delete'); ?></button>
                        <button class="btn btn-default modal-dismiss"><?php echo ss('Cancel'); ?></button>
                    </div>
                </div>
            </footer>
        </section>
    </div>

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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=user&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=user&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=user&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=user&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
        $.magnificPopup.open({
            items: {
                src: '#modalDelete'
            },
            type: 'inline',

            fixedContentPos: false,
            fixedBgPos: true,

            overflowY: 'auto',

            closeBtnInside: true,
            preloader: false,

            midClick: true,
            removalDelay: 300,
            mainClass: 'my-mfp-zoom-in',
            modal: true
        });
        
        $(document).on('click', '#modalDelete .modal-dismiss', function (e) {
            e.preventDefault();
            $.magnificPopup.close();
            $(document).off('click','#modalDelete .modal-dismiss');
            $(document).off('click','#modalDelete .modal-confirm');
        });
        
        $(document).on('click', '#modalDelete .modal-confirm', function (e) {
            e.preventDefault();
            fetch('user_d.php?delete=1&user_id='+pk)
            .then(function(response){
                $('#tr_'+pk).hide();
                new PNotify({
                    title: 'Success!',
                    text: '<?php echo ss('The specific user deleted!'); ?>',
                    type: 'success'
                });
                $(document).off('click','#modalDelete .modal-dismiss');
                $(document).off('click','#modalDelete .modal-confirm');
                $.magnificPopup.close();
            })
            .catch(function(error){
                new PNotify({
                    title: 'Error!',
                    text: '<?php echo ss('Some Problem occured in delete'); ?>',
                    type: 'error'
                });
                console.error(errror);
            });

        });
    }

var sortcol = 'user_id';
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
    var verticalScrollPos = $(".search").closest(".table-responsive").scrollLeft();
    $.get(url, function(data) {
        $('#list_tbody').html(data);

        // also add the filterparam to the xls export
        $('#xlsbutton').attr('href',$('#xlsbutton').attr('href') + filterparams);
        $($(".search").closest(".table-responsive")).animate({
            scrollLeft: verticalScrollPos
        }, 0);
        
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
        var tableVertScroll = $(this).closest(".table-responsive").scrollLeft();
        if(tr.hasClass("selected")) {
            tr.removeClass("selected");
        } else {
            localStorage.setItem("<?Php echo $modul; ?>_highlight", JSON.stringify({id: tr.attr('id'), vscroll: tableVertScroll}));
            tr.addClass("selected");
        }
    });
    var localStorageHighlight = localStorage.getItem("<?Php echo $modul; ?>_highlight");
    if(localStorageHighlight && localStorageHighlight !=""){
        var objhigh = JSON.parse(localStorageHighlight);
        $("#"+objhigh.id).addClass("selected");
        if( $("#"+objhigh.id).length > 0 ){
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#"+objhigh.id).offset().top-200
            }, 0);
            $($("#"+objhigh.id).closest(".table-responsive")).animate({
                scrollLeft: objhigh.vscroll
            }, 0);
            localStorage.removeItem("<?Php echo $modul; ?>_highlight");
        }
    }

});

// hook user javascript
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