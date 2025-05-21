<?php
$modul = "login";
$area = "all";
define('DEBUG_AI', true);
require(__DIR__ . "/inc/req.php");

if (isset($_REQUEST['ai_id']) && $_SESSION['user_id']) {
    $ai = (int) $_REQUEST['ai_id'];
} else {
    $ai = 1;
}
$firstname = " ";
if ($_SESSION['user_id']) {
    $firstnameSql = "SELECT firstname FROM user WHERE user_id=" . (int) $_SESSION['user_id'];
    $firstnameRes = mysqli_query($con, $firstnameSql);
    $firnameRow = mysqli_fetch_assoc($firstnameRes);
    $firstname .= $firnameRow['firstname'];
}
$aiName = '';
$aiSubtitle = '';
$aiIcon = '/assets/images/magd-q.png';

$sql = "SELECT ai_id, name, subtitle, greeting FROM ai WHERE ai_id=" . $ai . " OR subtitle = '". mysqli_real_escape_string($con, $_REQUEST['ai_id']) ."' OR name = '". mysqli_real_escape_string($con, $_REQUEST['ai_id']) ."'";
$res = mysqli_query($con, $sql) or die(mysqli_error($con));
$aiRow = mysqli_fetch_array($res);
if (!isset($aiRow['name'])) {
    die('ai not found');
}
$ai = $aiRow['ai_id'];
$aiName = $aiRow['name'];
$aiSubtitle = $aiRow['subtitle'];
if ($aiRow['icon']) {
    $aiIcon = $aiRow['icon'];
}
if ($aiRow['greeting']) {
    $aiGreeting = $aiRow['greeting'];
}
$aiSubtitle = "Hotline: 0175 263 7673";


if (DEBUG_AI) {
    error_log('Loading AI ' . $ai);
}

$n4a['user_d.php'] = ss('Register');
$n4a['forgotpw.php'] = ss('Forgot Password?');
//require("inc/header.inc.php");
?>

<html>
<head>
    <!-- Basic -->
    <meta charset="UTF-8">

    <meta name="keywords" content="<?php html($aiName)?> >" />
    <meta name="description" content="<?php html($aiName)?>">
    <meta name="author" content="<?php html($aiName)?>">

        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo HTTP_SUB ?>assets/images/favicon/apple-touch-icon.png"/>
        <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo HTTP_SUB ?>assets/images/favicon/android-icon-192x192.png"/>
        <link rel="icon" type="image/png" sizes="512x512"  href="<?php echo HTTP_SUB ?>assets/images/favicon/android-icon-512x512.png"/>
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo HTTP_SUB ?>assets/images/favicon/favicon-32x32.png"/>
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo HTTP_SUB ?>assets/images/favicon/favicon-16x16.png"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />


    <link rel="stylesheet" href="<?php echo HTTP_SUB ?>assets/css/chat.css?version=<?php echo $front_version; ?>" />
    <link rel='stylesheet' media='screen and (max-width: 700px)' href='<?php echo HTTP_SUB ?>assets/css/narrow.css?version=<?php echo $front_version; ?>' />

    <!-- /replace:css -->
    <link href="<?php echo HTTP_SUB ?>assets/ajax/chat/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css"/>

    <!-- replace:js -->
    <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/ajax/chat/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/ajax/chat/jquery.mCustomScrollbar.concat.min.js"></script>
</head>
<body>

<script src='https://use.fontawesome.com/releases/v5.0.13/js/all.js'></script>
<input type="text" id="chatid" hidden> <span class="id_session"></span>
<input type="text" id="aiicon" value="<?php echo $aiIcon?>" hidden>
<input type="text" id="ai" value="<?php echo $ai?>" hidden>

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">

            <div class="chat">
                <div class="chat-title">
                    <h1><?php echo html($aiName)?></h1>
                    <h2><?php echo html($aiSubtitle)?></h2>
                    <figure class="avatar">
                        <i class="fa fa-headphones" style="color: #FF4081; width: 2.3em; height: 2em" aria-hidden="true"><i class="fa fa-headphones" style="color: #FF4081; width: 2.3em; height: 2em" aria-hidden="true"></i></i></figure>
                </div>
                <div class="messages">
                    <div class="messages-content"></div>
                </div>
                <div class="message-box">
                    <textarea type="text" class="message-input" placeholder="Nachricht eingeben..." style="width:80%"></textarea>
                    <button type="submit" class="message-submit">Senden</button>
                </div>

            </div>
            <div class="bg"></div>

        </div>
    </div>
    <!-- replace:bottomjs -->
    <script type="text/javascript" src="<?php echo HTTP_SUB ?>assets/js/footer.login.js?version=<?php echo $front_version; ?>"></script>
    <!-- /replace:bottomjs -->

    <script type="text/javascript">
        function uuidv4() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }

        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        <?php
        if ($_REQUEST['chatid'] && GR(3)) {?>
        document.getElementById("chatid").value = "<?php echo $_REQUEST['chatid'] ?>";
        <?php } else {?>
        if (getCookie("chatid") == "") {
            uuid = uuidv4()
            document.cookie = "chatid=" + uuid
            document.getElementById("chatid").value = uuid
        } else {
            document.getElementById("chatid").value = getCookie("chatid");
        }
        <?php }?>
    </script>
    <script src="/assets/js/chat.js"></script>

    <script type="text/javascript">
        var $messages = $('.messages-content'),
            d, h, m,
            i = 0;

        $(window).load(function() {
            $messages.mCustomScrollbar();
            <?php //if (!$_REQUEST['chatid']) {?>
            //if (getCookie("chatid") == "") {
            setTimeout(function() {
                greetMessage();
                //doAction('http://localhost/api/v1/?api=school&action=sick&student_name=Klara-Flederfuechse');
            }, 100);
            //}
            <?php //}?>
        });

        function updateScrollbar() {
            $messages.mCustomScrollbar("update").mCustomScrollbar('scrollTo', 'bottom', {
                scrollInertia: 10,
                timeout: 0
            });
        }

        function setDate(){
            d = new Date()
            if (m != d.getMinutes()) {
                m = d.getMinutes();
                $('<div class="timestamp">' + d.getHours() + ':' + m + '</div>').appendTo($('.message:last'));
            }
        }

        function insertMessage() {
            msg = $('.message-input').val();
            if ($.trim(msg) == '') {
                return false;
            }
            $('<div class="message message-personal">' + msg + '</div>').appendTo($('.mCSB_container')).addClass('new');
        }

        function afterSend() {
            setDate();
            $('.message-input').val(null);
            updateScrollbar();
            /*setTimeout(function() {
                fakeMessage();
            }, 1000 + (Math.random() * 20) * 100);*/
        }

        $('.message-submit').click(function() {
            insertMessage();
            sendMsgT();
            afterSend();
        });

        $(window).on('keydown', function(e) {
            if (e.which == 13) {
                insertMessage();
                sendMsgT();
                afterSend();
                return false;
            }
        })

        var Fake = [
            'Hallo<?php echo $firstname?>, Ich bin <?php echo $aiName?> bei <?php echo TITLE?>. Schön, dass Du mich gefunden hast.',
            'Wie kann ich dir helfen?',
            '<?php echo $aiGreeting?>',
            'Wie geht es Dir?',
            'Ganz gut, Danke!',
            'Was machst Du?',
            'Das ist großartig!',
            'Du bist toll',
            'Du siehst gut aus heute',
            'Wieso?',
            'Erkläre das bitte!',
            'Ich muss jetzt leider gehen',
            'Es hat mich gefreut, mit Dir zu chatten',
            'Bye',
            'Tschüss',
            ':)'
        ]

        function greetMessage() {
            if ($('.message-input').val() != '') {
                return false;
            }
            $('<div class="message loading new"><figure class="avatar"><i class="fa fa-headphones" style="color: #FF4081; width: 2.3em; height: 2em" aria-hidden="true"></i></figure><span></span></div>').appendTo($('.mCSB_container'));
            updateScrollbar();

            setTimeout(function() {
                $('.message.loading').remove();
                if ($('.message').length > 1) {
                    return false;
                }
                $('<div class="message new"><figure class="avatar"><i class="fa fa-headphones" style="color: #FF4081; width: 2.3em; height: 2em" aria-hidden="true"></i></figure>' + Fake[i] + ' ' + Fake[i+<?php echo ($_SESSION['user_id'])?1:2?>] + '</div>').appendTo($('.mCSB_container')).addClass('new');
                setDate();
                updateScrollbar();
                i++;
            }, 1000 + (Math.random() * 20) * 100);

        }
    </script>
</body>
</html>

