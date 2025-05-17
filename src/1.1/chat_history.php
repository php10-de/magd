<?php 
$modul="chat_history";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view chat_history
$groupID = 1001;
GRGR($groupID);

$_SESSION['entity'] = 'chat_history';


// include module if exists
if (file_exists(MODULE_ROOT.'chat_history/chat_history.php')) {
    require MODULE_ROOT.'chat_history/chat_history.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'chat_history_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = isset($_REQUEST['headless']);
$n4a['chat_history_d.php'] = '' . ss('Add Chat_history') . '';
if (!$headless) require("inc/header.inc.php");

$autocomplete['user_id']['dbTable'] = 'user';
$autocomplete['user_id']['firstVarchar'] = 'email';
                
$autocomplete['ai_id']['dbTable'] = 'ai';
$autocomplete['ai_id']['firstVarchar'] = 'name';
                
// search in connected entities
if (isset($_REQUEST['autocomplete'])) {
    $searchField = isset($autocomplete[$_REQUEST['field']]['firstVarchar'])?$autocomplete[$_REQUEST['field']]['firstVarchar']:'name';
    echo basicConvert($autocomplete[$_REQUEST['field']]['dbTable'], null, 'autocomplete', $searchField, null, $groupID
    , $searchField . " LIKE '%" . mysqli_real_escape_string($con, $_REQUEST[ "term" ]) . "%'");
    exit;
}

/*** Validation ***/

// Chat_history_id
validate('chat_history_id', 'int nullable' );
$_SESSION[$modul]['chat_history_id'] = $_VALID['chat_history_id'];

// User_id
validate('user_id', 'string' );
$_SESSION[$modul]['user_id'] = $_VALID['user_id'];

// Ai_id
validate('ai_id', 'string' );
$_SESSION[$modul]['ai_id'] = $_VALID['ai_id'];

// Session_id
validate('session_id', 'string' );
$_SESSION[$modul]['session_id'] = $_VALID['session_id'];

// Human
validate('human', 'string' );
$_SESSION[$modul]['human'] = $_VALID['human'];

// Ai
validate('ai', 'string' );
$_SESSION[$modul]['ai'] = $_VALID['ai'];

// Action
validate('action', 'string nullable' );
$_SESSION[$modul]['action'] = $_VALID['action'];

// Cdate
validate('cdate', 'datetime' );
$_SESSION[$modul]['cdate'] = $_VALID['cdate'];
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}
// where condition
if ($_VALID['user_id']) {
	$where[] = "user.email LIKE '%" . mysqli_real_escape_string($con, $_VALID['user_id']) . "%'";
}
$_SESSION[$modul]['user_id'] = $_VALID['user_id'];
if ($_VALID['ai_id']) {
	$where[] = "ai.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['ai_id']) . "%'";
}
$_SESSION[$modul]['ai_id'] = $_VALID['ai_id'];
if ($_VALID['session_id']) {
	$where[] = "chat_history.session_id LIKE '%" . mysqli_real_escape_string($con, $_VALID['session_id']) . "%'";
}
$_SESSION[$modul]['session_id'] = $_VALID['session_id'];
if ($_VALID['human']) {
	$where[] = "chat_history.human LIKE '%" . mysqli_real_escape_string($con, $_VALID['human']) . "%'";
}
$_SESSION[$modul]['human'] = $_VALID['human'];
if ($_VALID['ai']) {
	$where[] = "chat_history.ai LIKE '%" . mysqli_real_escape_string($con, $_VALID['ai']) . "%'";
}
$_SESSION[$modul]['ai'] = $_VALID['ai'];
if ($_VALID['action']) {
	$where[] = "chat_history.action LIKE '%" . mysqli_real_escape_string($con, $_VALID['action']) . "%'";
}
$_SESSION[$modul]['action'] = $_VALID['action'];
if ($_VALID['cdate']) {
    if (isset($_VALID['cdate_INTERVAL_FROM']) && $_VALID['cdate_INTERVAL_FROM'] 
    && isset($_VALID['cdate_INTERVAL_TO']) && $_VALID['cdate_INTERVAL_TO']) {
        $cdate = mysqli_real_escape_string($con, $_VALID['cdate']);
        $dateIntervalFrom = isset($_VALID['cdate_INTERVAL_FROM'])?$_VALID['cdate_INTERVAL_FROM']:' INTERVAL -10 DAY';
        $dateIntervalTo = isset($_VALID['cdate_INTERVAL_TO'])?$_VALID['cdate_INTERVAL_TO']:' INTERVAL +10 DAY';
        $where[] = "chat_history.cdate between date_add('" . $cdate . "', " . $dateIntervalFrom . ") AND date_add('" . $cdate . "', " . $dateIntervalTo . ")";
    } else {
        $where[] = "chat_history.cdate LIKE '" . mysqli_real_escape_string($con, $_VALID['cdate']) . "%'";
    }
}
$_SESSION[$modul]['cdate'] = $_VALID['cdate_ORG'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT chat_history.chat_history_id,
user.email as user_id,
ai.name as ai_id,
chat_history.session_id,
chat_history.human,
chat_history.ai,
chat_history.action,
chat_history.cdate
FROM `chat_history`
 LEFT JOIN `ai` AS ai
 ON chat_history.ai_id=ai.ai_id
 LEFT JOIN `user` AS user
 ON chat_history.user_id=user.user_id
WHERE " . $where . "
ORDER BY " . $orderBy . "
" . (($_VALID['limit']) ? "
LIMIT 0, " . $_VALID['limit'] : "");
/*** After list SQL ***/
$_SESSION[$modul]['sql'] = $sql;
$listResult = mysqli_query($con, $sql);

if (!$headless) {
?>
<header class="page-header">
    <!-- <h2><?php echo ss('Chat_history'); ?></h2> -->
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('Chat_history'); ?></span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('Chat_history'); ?></h2>
    </header>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-left">
                    <a class="btn btn-success" href="chat_history_d.php" title=" <?php echo ss('Add new'); ?>"><i class="fa fa-plus"></i> <?php echo ss('Add new'); ?></a>
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
        
    // title user_id
    echo '<th class="hasmenu grey" id="th_user_id" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_user_id" onClick="changeSort(\'user_id\')">' . ss('User_id') . '</a>
    </th>';
        
    // title ai_id
    echo '<th class="hasmenu grey" id="th_ai_id" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_ai_id" onClick="changeSort(\'ai_id\')">' . ss('Ai_id') . '</a>
    </th>';
        
    // title session_id
    echo '<th class="hasmenu grey" id="th_session_id" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_session_id" onClick="changeSort(\'session_id\')">' . ss('Session_id') . '</a>
    </th>';
        
    // title human
    echo '<th class="hasmenu grey" id="th_human" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_human" onClick="changeSort(\'human\')">' . ss('Human') . '</a>
    </th>';
        
    // title ai
    echo '<th class="hasmenu grey" id="th_ai" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_ai" onClick="changeSort(\'ai\')">' . ss('Ai') . '</a>
    </th>';
        
    // title action
    echo '<th class="hasmenu grey" id="th_action" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_action" onClick="changeSort(\'action\')">' . ss('Action') . '</a>
    </th>';
        
    // title cdate
    echo '<th class="hasmenu grey" id="th_cdate" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_cdate" onClick="changeSort(\'cdate\')">' . ss('Cdate') . '</a>
    </th>';

    echo '</tr>';

    echo '<tr class="head">';
    echo '<th class="text-center" style="vertical-align:middle;font-size:20px;">
            <i class="fa fa-filter" onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;" title="' . ss('Reset Filter') . '"/>
           </th>';

    // filter user_id
    echo '<th nowrap>
    <input class="form-control search" name="user_id" id="user_id" value="'.$_SESSION[$modul]['user_id'].'" autocomplete="off"></th>';

    // filter ai_id
    echo '<th nowrap>
    <input class="form-control search" name="ai_id" id="ai_id" value="'.$_SESSION[$modul]['ai_id'].'" autocomplete="off"></th>';

    // filter session_id
    echo '<th nowrap>
    <input class="form-control search" name="session_id" id="session_id" value="'.$_SESSION[$modul]['session_id'].'" autocomplete="off"></th>';

    // filter human
    echo '<th nowrap>
    <input class="form-control search" name="human" id="human" value="'.$_SESSION[$modul]['human'].'" autocomplete="off"></th>';

    // filter ai
    echo '<th nowrap>
    <input class="form-control search" name="ai" id="ai" value="'.$_SESSION[$modul]['ai'].'" autocomplete="off"></th>';

    // filter action
    echo '<th nowrap>
    <input class="form-control search" name="action" id="action" value="'.$_SESSION[$modul]['action'].'" autocomplete="off"></th>';

    // filter cdate
    echo '<th nowrap>
    <input class="form-control search" name="cdate" id="cdate" value="'.$_SESSION[$modul]['cdate'].'" autocomplete="off"></th>';

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
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['chat_history_id'].'">';
        echo '<td nowrap class="text-center actions-hover actions-fade">
        <a href="chat_history_d.php?i=' . $i . '&amp;chat_history_id=' . (int) $row['chat_history_id'] . '" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top">
        <i style="font-size:20px" class="fa fa-pencil" ></i>
        </a>';
        // people with right to delete see the delete button
        if (R(3)){
            echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="delRow(\''.$row['chat_history_id'].'\');" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-trash-o" style="color:red;font-size:20px;"></i></a>';
        }
        echo '</td>';

        // user_id
        echo '<td onClick="location.href=\'chat_history_d.php?i='.($i-1).'&amp;chat_history_id='.$row['chat_history_id'].'\'" nowrap>
        ' . str_limit(htmlspecialchars($row['user_id'])) . '
        </td>';

        // ai_id
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['ai_id'])) . '
        </td>';

        // session_id
        echo '<td nowrap>
        <a href="/magd.php?ai_id=' . htmlspecialchars(urlencode($row['ai_id'])) . '&amp;chatid=' . htmlspecialchars(urlencode($row['session_id'])) . '" target="_blank">' . str_limit(htmlspecialchars(urlencode($row['session_id']))) . '</a>
        </td>';

        // human
        echo '<td nowrap><a href="tel:+491752637673">0175263</a>
        ' . str_limit(htmlspecialchars($row['human'])) . '
        </td>';

        // ai
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['ai'])) . '
        </td>';

        // action
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['action'])) . '
        </td>';

        // cdate
        echo '<td nowrap>
        ' . (($row['cdate'] && ($row['cdate'] != '0000-00-00 00:00:00')) ? date('d.m.Y H:i:s', strtotime($row['cdate'])) : '') . '
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
                        <p><?php echo ss('Do you really want to delete the Chat_history?'); ?></p>
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=chat_history&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=chat_history&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=chat_history&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=chat_history&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
            fetch('chat_history_d.php?delete=1&chat_history_id='+pk)
            .then(function(response){
                $('#tr_'+pk).hide();
                new PNotify({
                    title: 'Success!',
                    text: '<?php echo ss('The specific chat_history deleted!'); ?>',
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

var sortcol = 'chat_history_id';
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
        val = $('.search:input:radio[name=' + obj.name + ']:checked').val();
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
        initialise();
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
        $('.search:input:radio[name=' + obj.name + ']:checked').prop('checked', false);
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

function autocompleteUpdate(elem) {
    var id = elem.id;
    var split_id = id.split("__");
    var field_name = split_id[0];
    var edit_id = split_id[1];
    var value = $(elem).val();
    if ('checkbox' == $(elem).attr('type')) {
        if ($(elem).prop("checked")) {
            value = 1;
        } else {
            value = 0;
        }
    }

    // Hide Input element
    $(elem).hide();

    // Hide and Change Text of the container with input element
    $(elem).prev('.edit').show();
    $(elem).prev('.edit').text(value);

    // Sending AJAX request
    $.ajax({
    url: 'chat_history_d.php?list_update',
    type: 'post',
    data: { field:field_name, [field_name]:value, chat_history_id:edit_id },
    success:function(response){
      if(response == 1){
         console.log('Save successfully');
      }else{
         console.log('Not saved.');
      }
    }
    });
}

function initialise() {
    $("#loading-image")
    .bind("ajaxSend", function(){
    $(this).show();
    })
    .bind("ajaxComplete", function(){
    $(this).hide();
    });
    jQuery('#loading-image').hide();
  //  updateList();
    //  updateList();
  $(".dotted td").on("click", function() {
        $(".dotted").removeClass("selected");
        var tr = $(this).parent();
        // ?? supposed to leftScroll?? var tableVertScroll = $(this).closest(".table-responsive").scrollLeft();
        if(tr.hasClass("selected")) {
            tr.removeClass("selected");
        } else {
            localStorage.setItem("<?Php echo $modul; ?>_highlight", tr.attr('id'));
            // ??? localStorage.setItem("<?Php echo $modul; ?>_highlight", JSON.stringify({id: tr.attr('id'), vscroll: tableVertScroll}));
            tr.addClass("selected");
        }
    });

    if(localStorage.getItem("<?Php echo $modul; ?>_highlight")!=""){
        $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).addClass("selected");
        if (typeof $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).offset() != 'undefined') {
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#"+localStorage.getItem("<?Php echo $modul; ?>_highlight")).offset().top-200
            }, 0);
        }
    }
    // Show Input element
    $('.edit').click(function(){
        $('.txtedit').hide();
        $('.autocomplete').hide();
        $(this).next('.txtedit').show();
        $(this).next('.txtedit').focus();
        $(this).next('.autocomplete').val('');
        $(this).next('.autocomplete').show().focus();
        $(this).hide();
    });

    // Save data
    $(".txtedit").change(function(){
        autocompleteUpdate(this);
    });

    $( ".autocomplete" ).autocomplete({
       source: function( request , response ) {
        var id = ($(this.element).prop("id"));
        var split_id = id.split("__");
        var field_name = split_id[0];
        var param = { field: field_name, term: request.term } ;
        $.ajax({
            url: "chat_history.php?headless=1&autocomplete",
            data : param,
            dataType: "json",
            type: "GET",
            success: function (data) {
                response($.map(data, function( item ) {
                    return item;
                }));
            }
        });
       },
       minLength: 2,
       select: function( event, ui ) {
          $(this).val( ui.item.value );
          autocompleteUpdate(this);
          $(this).prev('.edit').text(ui.item.label);
          $(this).val( ui.item.label );
          return false;
       },
    });
}

jQuery(document).ready(function(){ 
    initialise();

});

// hook chat_history javascript
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