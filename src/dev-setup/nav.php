<?php 
$modul="nav";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view nav
$groupID = 29;
GRGR($groupID);

$_SESSION['entity'] = 'nav';


// include module if exists
if (file_exists(MODULE_ROOT.'nav/nav.php')) {
    require MODULE_ROOT.'nav/nav.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'name';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = isset($_REQUEST['headless']);
$n4a['nav_d.php'] = '' . ss('Add Nav') . '';
/*** After Navigation Entries ***/
if (!$headless) require("inc/header.inc.php");

$autocomplete['to_nav_id']['dbTable'] = 'nav_1';
$autocomplete['to_nav_id']['firstVarchar'] = 'name';
                
$autocomplete['gr_id']['dbTable'] = 'gr';
$autocomplete['gr_id']['firstVarchar'] = 'shortname';
                
// search in connected entities
if (isset($_REQUEST['autocomplete'])) {
    $searchField = isset($autocomplete[$_REQUEST['field']]['firstVarchar'])?$autocomplete[$_REQUEST['field']]['firstVarchar']:'name';
    echo basicConvert($autocomplete[$_REQUEST['field']]['dbTable'], null, 'autocomplete', $searchField, null, $groupID
    , $searchField . " LIKE '%" . mysqli_real_escape_string($con, $_REQUEST[ "term" ]) . "%'");
    exit;
}

/*** Validation ***/

// Nav_id
validate('nav_id', 'int nullable' );
$_SESSION[$modul]['nav_id'] = $_VALID['nav_id'];

// To_nav_id
validate('to_nav_id', 'string nullable' );
$_SESSION[$modul]['to_nav_id'] = $_VALID['to_nav_id'];

// Gr_id
validate('gr_id', 'string nullable' );
$_SESSION[$modul]['gr_id'] = $_VALID['gr_id'];

// Level
validate('level', 'int' );
$_SESSION[$modul]['level'] = $_VALID['level'];

// Name
validate('name', 'string' );
$_SESSION[$modul]['name'] = $_VALID['name'];

// Link
validate('link', 'string' );
$_SESSION[$modul]['link'] = $_VALID['link'];

// Params
validate('params', 'string nullable' );
$_SESSION[$modul]['params'] = $_VALID['params'];

// Icon
validate('icon', 'string nullable' );
$_SESSION[$modul]['icon'] = $_VALID['icon'];

// Icon_color
validate('icon_color', 'string nullable' );
$_SESSION[$modul]['icon_color'] = $_VALID['icon_color'];
    
if (!$_VALID['level']) {
    unset($_MISSING['level']);
    if (!$_VALID['to_nav_id']) {
        $_VALID['level'] = 1;
        $_VALIDDB['level'] = 1;
    } else {
        $sql               = "SELECT level FROM nav WHERE nav_id=" . $_VALIDDB['to_nav_id'];
        $res               = mysqli_query($con, $sql);
        $row               = mysqli_fetch_row($res);
        $_VALID['level']   = $row[0] + 1;
        $_VALIDDB['level'] = $row[0] + 1;
    }
}
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}
// where condition
if ($_VALID['to_nav_id']) {
	$where[] = "nav_1.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['to_nav_id']) . "%'";
}
$_SESSION[$modul]['to_nav_id'] = $_VALID['to_nav_id'];
if ($_VALID['gr_id']) {
	$where[] = "gr.shortname LIKE '%" . mysqli_real_escape_string($con, $_VALID['gr_id']) . "%'";
}
$_SESSION[$modul]['gr_id'] = $_VALID['gr_id'];
if ($_VALID['name']) {
	$where[] = "nav.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['name']) . "%'";
}
$_SESSION[$modul]['name'] = $_VALID['name'];
if ($_VALID['icon']) {
	$where[] = "nav.icon LIKE '%" . mysqli_real_escape_string($con, $_VALID['icon']) . "%'";
}
$_SESSION[$modul]['icon'] = $_VALID['icon'];
if ($_VALID['icon_color']) {
	$where[] = "nav.icon_color LIKE '%" . mysqli_real_escape_string($con, $_VALID['icon_color']) . "%'";
}
$_SESSION[$modul]['icon_color'] = $_VALID['icon_color'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT nav.nav_id,
nav_1.name as to_nav_id,
gr.shortname as gr_id,
nav.level,
nav.name,
nav.link,
nav.params,
nav.icon,
nav.icon_color
FROM `nav`
 LEFT JOIN `gr` AS gr
 ON nav.gr_id=gr.gr_id
 LEFT JOIN `nav` AS nav_1
 ON nav.to_nav_id=nav_1.nav_id
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
    <!-- <h2><?php echo ss('Nav'); ?></h2> -->
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('Nav'); ?></span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('Nav'); ?></h2>
    </header>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-left">
                    <a class="btn btn-success" href="nav_d.php" title=" <?php echo ss('Add new'); ?>"><i class="fa fa-plus"></i> <?php echo ss('Add new'); ?></a>
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
        
    // title to_nav_id
    echo '<th class="hasmenu grey" id="th_to_nav_id" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_to_nav_id" onClick="changeSort(\'to_nav_id\')">' . ss('Parent') . '</a>
    </th>';
        
    // title gr_id
    echo '<th class="hasmenu grey" id="th_gr_id" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_gr_id" onClick="changeSort(\'gr_id\')">' . ss('Group') . '</a>
    </th>';
        
    // title name
    echo '<th class="hasmenu grey" id="th_name" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_name" onClick="changeSort(\'name\')">' . ss('Name') . '</a>
    </th>';
        
    // title icon
    echo '<th class="hasmenu grey" id="th_icon" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_icon" onClick="changeSort(\'icon\')">' . ss('Icon') . '</a>
    </th>';
        
    // title icon_color
    echo '<th class="hasmenu grey" id="th_icon_color" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_icon_color" onClick="changeSort(\'icon_color\')">' . ss('Icon_color') . '</a>
    </th>';

    echo '</tr>';

    echo '<tr class="head">';
    echo '<th class="text-center" style="vertical-align:middle;font-size:20px;">
            <i class="fa fa-filter" onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;" title="' . ss('Reset Filter') . '"/>
           </th>';

    // filter to_nav_id
    echo '<th nowrap>
    <input class="form-control search" name="to_nav_id" id="to_nav_id" value="'.$_SESSION[$modul]['to_nav_id'].'" autocomplete="off"></th>';

    // filter gr_id
    echo '<th nowrap>
    <input class="form-control search" name="gr_id" id="gr_id" value="'.$_SESSION[$modul]['gr_id'].'" autocomplete="off"></th>';

    // filter name
    echo '<th nowrap>
    <input class="form-control search" name="name" id="name" value="'.$_SESSION[$modul]['name'].'" autocomplete="off"></th>';

    // filter icon
    echo '<th nowrap>
    <input class="form-control search" name="icon" id="icon" value="'.$_SESSION[$modul]['icon'].'" autocomplete="off"></th>';

    // filter icon_color
    echo '<th nowrap>
    <input class="form-control search" name="icon_color" id="icon_color" value="'.$_SESSION[$modul]['icon_color'].'" autocomplete="off"></th>';

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
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['nav_id'].'">';
        echo '<td nowrap class="text-center actions-hover actions-fade">
        <a href="nav_d.php?i=' . $i . '&amp;nav_id=' . (int) $row['nav_id'] . '" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top">
        <i style="font-size:20px" class="fa fa-pencil" ></i>
        </a>';
        // people with right to delete see the delete button
        if (R(3)){
            echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="delRow(\''.$row['nav_id'].'\');" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-trash-o" style="color:red;font-size:20px;"></i></a>';
        }
        echo '</td>';

        // to_nav_id
        echo '<td onClick="location.href=\'nav_d.php?i='.($i-1).'&amp;nav_id='.$row['nav_id'].'\'" nowrap>
        ' . str_limit(htmlspecialchars($row['to_nav_id'])) . '
        </td>';

        // gr_id
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['gr_id'])) . '
        </td>';

        // name
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['name'])) . '
        </td>';

        // icon
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['icon'])) . '
        </td>';

        // icon_color
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['icon_color'])) . '
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
                        <p><?php echo ss('Do you really want to delete the Nav?'); ?></p>
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=nav&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=nav&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=nav&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=nav&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
            fetch('nav_d.php?delete=1&nav_id='+pk)
            .then(function(response){
                $('#tr_'+pk).hide();
                new PNotify({
                    title: 'Success!',
                    text: '<?php echo ss('The specific nav deleted!'); ?>',
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

var sortcol = 'name';
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
    url: 'nav_d.php?list_update',
    type: 'post',
    data: { field:field_name, [field_name]:value, nav_id:edit_id },
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
            url: "nav.php?headless=1&autocomplete",
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

// hook nav javascript
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