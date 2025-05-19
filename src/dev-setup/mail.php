<?php 
$modul="mail";

require("inc/req.php");

/*** Rights ***/
// Generally for people with the right to view mail
$groupID = 1024;
GRGR($groupID);

$_SESSION['entity'] = 'mail';


// include module if exists
if (file_exists(MODULE_ROOT.'mail/mail.php')) {
    require MODULE_ROOT.'mail/mail.php';
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

$orderBy = ($_VALID['sortcol'])?$_VALID['sortcol'] .(($_VALID['sortdir'])?' ' . $_VALID['sortdir']:''):'mail_id';
$orderBy = mysqli_real_escape_string ( $con , $orderBy );
    
$headless = isset($_REQUEST['headless']);
$n4a['mail_d.php'] = '' . ss('Add Mail') . '';
$n4a['mail.php?send'] = '' . ss('Send') . '';
/*** After Navigation Entries ***/
if (!$headless) require("inc/header.inc.php");

$autocomplete['mail_group']['dbTable'] = 'mail_group';
$autocomplete['mail_group']['firstVarchar'] = 'name';
                
// search in connected entities
if (isset($_REQUEST['autocomplete'])) {
    $searchField = isset($autocomplete[$_REQUEST['field']]['firstVarchar'])?$autocomplete[$_REQUEST['field']]['firstVarchar']:'name';
    echo basicConvert($autocomplete[$_REQUEST['field']]['dbTable'], null, 'autocomplete', $searchField, null, $groupID
    , $searchField . " LIKE '%" . mysqli_real_escape_string($con, $_REQUEST[ "term" ]) . "%'");
    exit;
}

/*** Validation ***/

// Mail_id
validate('mail_id', 'int nullable' );
$_SESSION[$modul]['mail_id'] = $_VALID['mail_id'];

// Mail_group
validate('mail_group', 'string' );
$_SESSION[$modul]['mail_group'] = $_VALID['mail_group'];

// Recipient
validate('recipient', 'string' );
$_SESSION[$modul]['recipient'] = $_VALID['recipient'];

// Recipient_name
validate('recipient_name', 'string nullable' );
$_SESSION[$modul]['recipient_name'] = $_VALID['recipient_name'];

// Subject
validate('subject', 'string' );
$_SESSION[$modul]['subject'] = $_VALID['subject'];

// Content
validate('content', 'string' );
$_SESSION[$modul]['content'] = $_VALID['content'];

// Dsent
validate('dsent', 'datetime nullable' );
$_SESSION[$modul]['dsent'] = $_VALID['dsent'];

// Attachment_media
validate('attachment_media', 'media nullable' );
$_SESSION[$modul]['attachment_media'] = $_VALID['attachment_media'];
    
/***** Mandatory Fields ****/
if (isset($_REQUEST['submitted']) && is_array($_MISSING) && count($_MISSING)) {
	$error[] = ss('missing fields');
}
// where condition
if ($_VALID['mail_group']) {
	$where[] = "mail_group.name LIKE '%" . mysqli_real_escape_string($con, $_VALID['mail_group']) . "%'";
}
$_SESSION[$modul]['mail_group'] = $_VALID['mail_group'];
if ($_VALID['recipient']) {
	$where[] = "mail.recipient LIKE '%" . mysqli_real_escape_string($con, $_VALID['recipient']) . "%'";
}
$_SESSION[$modul]['recipient'] = $_VALID['recipient'];
if ($_VALID['recipient_name']) {
	$where[] = "mail.recipient_name LIKE '%" . mysqli_real_escape_string($con, $_VALID['recipient_name']) . "%'";
}
$_SESSION[$modul]['recipient_name'] = $_VALID['recipient_name'];
if ($_VALID['subject']) {
	$where[] = "mail.subject LIKE '%" . mysqli_real_escape_string($con, $_VALID['subject']) . "%'";
}
$_SESSION[$modul]['subject'] = $_VALID['subject'];
if ($_VALID['dsent']) {
    if (isset($_VALID['dsent_INTERVAL_FROM']) && $_VALID['dsent_INTERVAL_FROM'] 
    && isset($_VALID['dsent_INTERVAL_TO']) && $_VALID['dsent_INTERVAL_TO']) {
        $dsent = mysqli_real_escape_string($con, $_VALID['dsent']);
        $dateIntervalFrom = isset($_VALID['dsent_INTERVAL_FROM'])?$_VALID['dsent_INTERVAL_FROM']:' INTERVAL -10 DAY';
        $dateIntervalTo = isset($_VALID['dsent_INTERVAL_TO'])?$_VALID['dsent_INTERVAL_TO']:' INTERVAL +10 DAY';
        $where[] = "mail.dsent between date_add('" . $dsent . "', " . $dateIntervalFrom . ") AND date_add('" . $dsent . "', " . $dateIntervalTo . ")";
    } else {
        $where[] = "mail.dsent LIKE '" . mysqli_real_escape_string($con, $_VALID['dsent']) . "%'";
    }
}
$_SESSION[$modul]['dsent'] = $_VALID['dsent_ORG'];
if ($_VALID['attachment_media']) {
	$where[] = "mail.attachment_media = '" . mysqli_real_escape_string($con, $_VALID['attachment_media']) . "'";
}
$_SESSION[$modul]['attachment_media'] = $_VALID['attachment_media'];
$where = ($where) ? implode(" AND ", $where) : "1=1";
//List Hook After Where
$sql = "SELECT mail.mail_id,
mail_group.name as mail_group,
mail.recipient,
mail.recipient_name,
mail.subject,
mail.content,
mail.dsent,
mail.attachment_media
FROM `mail`
 LEFT JOIN `mail_group` AS mail_group
 ON mail.mail_group=mail_group.mail_group_id
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
    <!-- <h2><?php echo ss('Mail'); ?></h2> -->
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span><?php echo ss('Mail'); ?></span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('Mail'); ?></h2>
    </header>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="mb-md text-left">
                    <a class="btn btn-success" href="mail_d.php" title=" <?php echo ss('Add new'); ?>"><i class="fa fa-plus"></i> <?php echo ss('Add new'); ?></a>
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
        
    // title mail_group
    echo '<th class="hasmenu grey" id="th_mail_group" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_mail_group" onClick="changeSort(\'mail_group\')">' . ss('Mail_group') . '</a>
    </th>';
        
    // title recipient
    echo '<th class="hasmenu grey" id="th_recipient" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_recipient" onClick="changeSort(\'recipient\')">' . ss('Recipient') . '</a>
    </th>';
        
    // title recipient_name
    echo '<th class="hasmenu grey" id="th_recipient_name" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_recipient_name" onClick="changeSort(\'recipient_name\')">' . ss('Recipient_name') . '</a>
    </th>';
        
    // title subject
    echo '<th class="hasmenu grey" id="th_subject" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_subject" onClick="changeSort(\'subject\')">' . ss('Subject') . '</a>
    </th>';
        
    // title dsent
    echo '<th class="hasmenu grey" id="th_dsent" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_dsent" onClick="changeSort(\'dsent\')">' . ss('Dsent') . '</a>
    </th>';
        
    // title attachment_media
    echo '<th class="hasmenu grey" id="th_attachment_media" nowrap="nowrap">
        <a href="javascript:void(0)" id="hlink_attachment_media" onClick="changeSort(\'attachment_media\')">' . ss('Attachment_media') . '</a>
    </th>';

    echo '</tr>';

    echo '<tr class="head">';
    echo '<th class="text-center" style="vertical-align:middle;font-size:20px;">
            <i class="fa fa-filter" onclick="resetFilter();" style="cursor: pointer; cursor: hand;color:red;" title="' . ss('Reset Filter') . '"/>
           </th>';

    // filter mail_group
    echo '<th nowrap>
    <input class="form-control search" name="mail_group" id="mail_group" value="'.$_SESSION[$modul]['mail_group'].'" autocomplete="off"></th>';

    // filter recipient
    echo '<th nowrap>
    <input class="form-control search" name="recipient" id="recipient" value="'.$_SESSION[$modul]['recipient'].'" autocomplete="off"></th>';

    // filter recipient_name
    echo '<th nowrap>
    <input class="form-control search" name="recipient_name" id="recipient_name" value="'.$_SESSION[$modul]['recipient_name'].'" autocomplete="off"></th>';

    // filter subject
    echo '<th nowrap>
    <input class="form-control search" name="subject" id="subject" value="'.$_SESSION[$modul]['subject'].'" autocomplete="off"></th>';

    // filter dsent
    echo '<th nowrap>
    <input class="form-control search" name="dsent" id="dsent" value="'.$_SESSION[$modul]['dsent'].'" autocomplete="off"></th>';

    // filter attachment_media
    echo '<th nowrap>
    <input class="form-control search" name="attachment_media" id="attachment_media" value="'.$_SESSION[$modul]['attachment_media'].'" autocomplete="off"></th>';

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
        echo '<tr class="dotted ' .  ((($i++ % 2)==0) ? "tr_even":"tr_odd") . '" id="tr_'.$row['mail_id'].'">';
        echo '<td nowrap class="text-center actions-hover actions-fade">
        <a href="mail_d.php?i=' . $i . '&amp;mail_id=' . (int) $row['mail_id'] . '" title="' . ss('Edit') . '" data-toggle="tooltip" data-placement="top">
        <i style="font-size:20px" class="fa fa-pencil" ></i>
        </a>';
        // people with right to delete see the delete button
        if (R(3)){
            echo '&nbsp;&nbsp;<a href="#" title="' . ss('Delete') . '" onclick="delRow(\''.$row['mail_id'].'\');" data-toggle="tooltip" data-placement="top">
            <i class="fa fa-trash-o" style="color:red;font-size:20px;"></i></a>';
        }
        echo '</td>';

        // mail_group
        echo '<td onClick="location.href=\'mail_d.php?i='.($i-1).'&amp;mail_id='.$row['mail_id'].'\'" nowrap>
        ' . str_limit(htmlspecialchars($row['mail_group'])) . '
        </td>';

        // recipient
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['recipient'])) . '
        </td>';

        // recipient_name
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['recipient_name'])) . '
        </td>';

        // subject
        echo '<td nowrap>
        ' . str_limit(htmlspecialchars($row['subject'])) . '
        </td>';

        // dsent
        echo '<td nowrap>
        ' . (($row['dsent'] && ($row['dsent'] != '0000-00-00 00:00:00')) ? date('d.m.Y H:i:s', strtotime($row['dsent'])) : '') . '
        </td>';

        // attachment_media
            $media = '&nbsp;';
            $mediaUrl = HTTP_HOST . 'mail_d.php?show_media&filename=' . $row['attachment_media'];
            if (strpos($row['attachment_media'], '.pdf') !== false) {
                $media = '<a href="' . $mediaUrl . '" target="_blank"><i class="fa fa-file-pdf-o"></i></a>';
            } elseif($row['attachment_media']) {
                $media = '<a href="' . $mediaUrl . '" target="_blank"><i class="fa fa-file-o"></i></a>';
            }
        echo '<td nowrap>
        ' . $media . '
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
                        <p><?php echo ss('Do you really want to delete the Mail?'); ?></p>
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
                    window.location.href = 'red_button_entity_d.php?return_script=<?php echo urlencode($_SERVER['PHP_SELF'].'?BRB')?>&entity_name=mail&after=' + $target.uniqueId().attr('id').substring(3);
                    break
                case "delete":
                    $.ajax({
                        url: 'a/red_button_entity_del.php?searchid&entity_name=mail&data_name='+$target.uniqueId().attr('id').substring(3)
                    }).done( function( data ) {
                        $.ajax({
                            url: 'bigredbutton.php?array=mail&http_method=_REQUEST&is_code_type=0&type=form&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
                        }).done( function( data ) {
                            $.ajax({
                                url: 'bigredbutton.php?array=mail&http_method=_REQUEST&is_code_type=0&type=list&is_file=0&hrose_write_file=on&hrose_overwrite_file=on&hrose_overwrite_changed_file=on&is_options=0&charset=UTF8&trans=on&mysql_typecast=on&mysql_full=on&validate_store_session=on&type_filter=on&custom_html_tags&form_full=on&list_search=on&list_sort=on&list_limit=on&list_delete=on&list_ajax=on&dynamic_list_header=on&list_odd_row=on&submitted=yes'
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
            fetch('mail_d.php?delete=1&mail_id='+pk)
            .then(function(response){
                $('#tr_'+pk).hide();
                new PNotify({
                    title: 'Success!',
                    text: '<?php echo ss('The specific mail deleted!'); ?>',
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

var sortcol = 'mail_id';
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
    url: 'mail_d.php?list_update',
    type: 'post',
    data: { field:field_name, [field_name]:value, mail_id:edit_id },
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
            url: "mail.php?headless=1&autocomplete",
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

// hook mail javascript
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