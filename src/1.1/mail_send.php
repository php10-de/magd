<?php

$modul="cron";
$mailsPerRun = 2;
require("inc/req.php");
define('MAIL_DEBUG_MODE', true);
define('DU', false);

if (MAIL_DEBUG_MODE) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

if ((date('H') > 22) OR (date('H') < 8)) {
    //die('');
}

// For Administrators only
//if (!CRONRUN AND !isset($_REQUEST[CRONTOKEN])) GRGR(1);
//Form Hook After Group
/*** Validation ***/

$sql = "SELECT * FROM mail_group g
LEFT JOIN mail_letter l ON l.mail_letter_id=g.mail_letter_id
LEFT JOIN mail_sender s ON s.mail_sender_id=g.sender
WHERE is_active=1";

$groupRes = mysqli_query($con, $sql) or die(mysqli_error($con));

while ($groupRow = mysqli_fetch_array($groupRes)) {
    try {
        if (MAIL_DEBUG_MODE) {
            echo 'Group ' . $groupRow['mail_group_id'] . '<br><br>';
        }
        /*$sql = "
SELECT m.*
FROM mail m
LEFT JOIN mail_group g ON m.group_id=g.mail_group_id
WHERE 
  UPPER(m.lang)='" . strtoupper($groupRow['lang']) . "'
  AND m.group_id = " . $groupRow['mail_group_id'] . "
  AND (m.dsent IS NULL)
";
        if ($groupRow['is_test']) {
            $sql .= " AND is_tester=1";
        }
        $sql .= " LIMIT 0," . $mailsPerRun;*/
$sql = "SELECT user.email as recipient, user.firstname as recipient_firstname, user.lastname as recipient_lastname, user.lang as lang, parent.mann as sex
FROM parent 
LEFT JOIN user ON user.user_id=parent.user
WHERE (austritt IS NULL OR austritt='')";

	if (!isset($_REQUEST['no_test'])) {
		$sql .= " AND parent_id IN(189)";//21, 30
	}

        if (MAIL_DEBUG_MODE) {
            echo $sql.'<br>';
        }

        $res = mysqli_query($con, $sql) or die(mysqli_error($con));

        while ($row = mysqli_fetch_array($res)) {
            try {
                echo 'Recipient ' . $row['recipient'] . '<br>';
                $optSql = "SELECT `inout` FROM mail_opt WHERE mail='" . mysqli_real_escape_string($con, $row['recipient']) . "' ORDER BY opt_date DESC LIMIT 0,1";

                if (MAIL_DEBUG_MODE) {
                    echo $optSql.'<br>';
                }
                $optRes = mysqli_query($con, $optSql);
                $optRow = mysqli_fetch_row($optRes);
                if (isset($optRow[0]) AND 'out' == $optRow[0]) {
                    continue;
                }

                $mailTemplate = $groupRow['html'];
                if ('DE' == strtoupper($row['lang'])) {
                    if (DU === true && $row['recipient_firstname']) {
                        $salutation = 'Hallo ' . $row['recipient_firstname'];
                        $salutationShort = $row['recipient_firstname'];
                        if ($row['sex'])  {
                            $dear = 'liebe' . (($row['sex']=='m' || $row['sex']=='1')?'r':'');
                            $salutationShort = $dear . ' ' . $salutationShort;
                        }
                        $besprichBesprechen = 'besprich';
                        $deinIhr = 'Dein';
                        $deineIhre = 'Deine';
                        $deinerIhrer = 'Deiner';
                        $deinenIhren = 'Deinen';
                        $dirIhnen = 'Dir';
                        $duSie = 'Du';
                        $entscheidestEntscheiden = 'entscheidest';
                        $findestFinden = 'findest';
                        $hastHaben = 'hast';
                        $infizierstInfizieren = 'infizierst';
                        $LassLassen = 'Lass';
                        $dichSie = 'Dich';
                        $dichSich = 'Dich';
                        $solltestDuSolltenSie = 'Solltest Du';
                        $willstWollen = 'willst';
                        $sendeSendenSie = 'sende';
                        $bestaetigeDeinBestaetigenSieIhr = 'best&auml;tige Dein';
                        $koenntestKoennten = 'könntest';
                        $warstWaren = 'warst';
                        $wirstDuWerdenSie = 'wirst Du';
                        $schreibeUnsSchreibenSieUns = 'schreibe uns';
                        $bleibstDuBleibenSie = 'bleibst Du';
                        $bleibstBleiben = 'bleibst';
                        $duWirstSieWerden = 'du wirst';
                    } else if ($row['recipient_lastname'] AND isset($row['sex'])) {
                        $salutation = 'Liebe';
                        $salutation .= ($row['sex']=='m' || $row['sex']=='1')?'r ':' ';
                        $salutationShort = ($row['sex']=='m' || $row['sex']=='1')?'Herr ':'Frau ';
                        $salutationShort .= $row['recipient_lastname'];
                        $salutation .= $salutationShort;
                        $besprichBesprechen = 'besprechen';
                        $deinIhr = 'Ihr';
                        $deineIhre = 'Ihre';
                        $deinerIhrer = 'Ihrer';
                        $deinenIhren = 'Ihren';
                        $dirIhnen = 'Ihnen';
                        $duSie = 'Sie';
                        $entscheidestEntscheiden = 'entscheiden';
                        $findestFinden = 'finden';
                        $hastHaben = 'haben';
                        $infizierstInfizieren = 'infizieren';
                        $LassLassen = 'Lassen Sie';
                        $dichSie = 'Sie';
                        $dichSich = 'sich';
                        $solltestDuSolltenSie = 'Sollten Sie';
                        $willstWollen = 'wollen';
                        $sendeSendenSie = 'senden Sie';
                        $bestaetigeDeinBestaetigenSieIhr = 'best&auml;tigen Sie Ihr';
                        $koenntestKoennten = 'könnten';
                        $warstWaren = 'waren';
                        $wirstDuWerdenSie = 'werden Sie';
                        $schreibeUnsSchreibenSieUns = 'schreiben Sie uns';
                        $bleibstDuBleibenSie = 'bleiben Sie';
                        $bleibstBleiben = 'bleiben';
                        $duWirstSieWerden = 'Sie werden';
                    } else {
                        $salutation = 'Hallo ' . $row['recipient_name'];
                        $salutationShort = $row['recipient_name'];
                        $besprichBesprechen = 'besprechen';
                        $deinIhr = 'Ihr';
                        $deineIhre = 'Ihre';
                        $deinerIhrer = 'Ihrer';
                        $deinenIhren = 'Ihren';
                        $dirIhnen = 'Ihnen';
                        $duSie = 'Sie';
                        $entscheidestEntscheiden = 'entscheiden';
                        $findestFinden = 'finden';
                        $hastHaben = 'haben';
                        $infizierstInfizieren = 'infzieren';
                        $LassLassen = 'Lassen Sie';
                        $dichSie = 'Sie';
                        $dichSich = 'sich';
                        $solltestDuSolltenSie = 'Sollten Sie';
                        $willstWollen = 'wollen';
                        $sendeSendenSie = 'senden Sie';
                        $bestaetigeDeinBestaetigenSieIhr = 'best&auml;tigen Sie Ihr';
                        $koenntestKoennten = 'könnten';
                        $warstWaren = 'waren';
                        $wirstDuWerdenSie = 'werden Sie';
                        $schreibeUnsSchreibenSieUns = 'schreiben Sie uns';
                        $bleibstDuBleibenSie = 'bleiben Sie';
                        $bleibstBleiben = 'bleiben';
                        $duWirstSieWerden = 'Sie werden';
                    }
                } else {
                    $besprichBesprechen = '';
                    $deinIhr = '';
                    $deineIhre = '';
                    $deinerIhrer = '';
                    $deinenIhren = '';
                    $dichSie = '';
                    $dichSich = '';
                    $duSie = '';
                    $entscheidestEntscheiden = '';
                    $findestFinden = '';
                    $hastHaben = 'haben';
                    $infizierstInfizieren = '';
                    $LassLassen = '';
                    $solltestDuSolltenSie = '';
                    $bestaetigeDeinBestaetigenSieIhr = '';
                    $wirstDuWerdenSie = '';
                    $bleibstDuBleibenSie = '';
                    $bleibstBleiben = '';
                    $duWirstSieWerden = '';
                    $schreibeUnsSchreibenSieUns = '';
                    $koenntestKoennten = '';
                    $warstWaren = '';
                    $willstWollen = '';
                    $sendeSendenSie = '';
                    $dirIhnen = '';
                    if ($row['recipient_firstname']) {
                        $salutation = 'Dear ' . $row['recipient_firstname'];
                        $salutationShort = $row['recipient_firstname'];
                    } else if ($row['recipient_lastname'] AND $row['sex']) {
                        $salutation = 'Dear ';
                        $salutationShort = ($row['sex']=='m')?'Mr. ':'Ms. ';
                        $salutationShort .= $row['recipient_lastname'];
                        $salutation .= $salutationShort;
                    } else {
                        $salutation = 'Dear ' . $row['recipient_firstname'];
                        $salutationShort = $row['recipient_firstname'];
                    }

                }
                $mailEnc = urlencode(encrypt($row['recipient'], 'THE_IH_$NEW_FOREST')) . '&amp;g='.$groupRow['mail_group_id'];
                $optInURL = 'http://' . $_SERVER['HTTP_HOST'] . '/a/mail_opt.php?m=' . $mailEnc . '&amp;inout=in';
                $optOutURL = 'http://' . $_SERVER['HTTP_HOST'] . '/a/mail_opt.php?m=' . $mailEnc . '&amp;inout=out';
                $replaceFrom = array('%Salutation%',
                                     '%SalutationShort%',
                                     '%OptInURL%',
                                     '%OptOutURL%',
                                     '%encMail1%',
                                     '%encMail2%',
                                     '%BesprichBesprechen%',
                                     '%DeinIhr%',
                                     '%DeineIhre%',
                                     '%DeinerIhrer%',
                                     '%DeinenIhren%',
                                     '%DichSie%',
                                     '%DichSich%',
                                     '%DuSie%',
                                     '%EntscheidestEntscheiden%',
                                     '%FindestFinden%',
                                     '%hastHaben%',
                                     '%InfizierstInfizieren%',
                                     '%LassLassen%',
                                     '%SolltestDuSolltenSie%',
                                     '%BestaetigeDeinBestaetigenSieIhr%',
                                     '%WirstDuWerdenSie%',
                                     '%BleibstDuBleibenSie%',
                                     '%BleibstBleiben%',
                                     '%DuWirstSieWerden%',
                                     '%SchreibeUnsSchreibenSieUns%',
                                     '%koenntestKoennten%',
                                     '%warstWaren%',
                                     '%WillstWollen%',
                                     '%SendeSendenSie%',
                                     '%DirIhnen%');
                $replaceTo = array($salutation,
                                   $salutationShort,
                                   $optInURL,
                                   $optOutURL,
                                   $mailEnc,
                                   $mailEnc,
                                   $besprichBesprechen,
                                   $deinIhr,
                                   $deineIhre,
                                   $deinerIhrer,
                                   $deinenIhren,
                                   $dichSie,
                                   $dichSich,
                                   $duSie,
                                   $entscheidestEntscheiden,
                                   $findestFinden,
                                   $hastHaben,
                                   $infizierstInfizieren,
                                   $LassLassen,
                                   $solltestDuSolltenSie,
                                   $bestaetigeDeinBestaetigenSieIhr,
                                   $wirstDuWerdenSie,
                                   $bleibstDuBleibenSie,
                                   $bleibstBleiben,
                                   $duWirstSieWerden,
                                   $schreibeUnsSchreibenSieUns,
                                   $koenntestKoennten,
                                   $warstWaren,
                                   $willstWollen,
                                   $sendeSendenSie,
                                   $dirIhnen);
                $mailContent = str_replace($replaceFrom, $replaceTo, $mailTemplate);
                $succ = send_mail($mailContent,
                                  $groupRow['title'],
                                  $row['recipient'],
                                  $row['recipient_name'], false,
                                  $groupRow['from_mail'], $groupRow['from_name'],
                                  $groupRow['response_mail'], $groupRow['respond_name']);

                if(!$succ) {
                    $err.="Mailer Error: " . $mail->ErrorInfo;
                    error_log($err);
                    echo $err;
                    echo '<hr>';
                } else {
                    $err.="Nachricht wurde versandt!";
                }
                flush();
                sleep(rand(1,10));

                if ($succ) {
                    if (MAIL_DEBUG_MODE) {
                        echo 'Mail sent to ' . $row['recipient'] . '<br>';
                    }
                    $sql = "UPDATE mail set dsent=now() WHERE mail_id = " . (int) $row['mail_id'];
                    $data = mysqli_query($con, $sql);
                    $_GET['ok'] = 'done';
                    if (CRONRUN) {
                        echo 'done';
                        exit;
                    }
                } else {
                    $sql = "UPDATE mail set dsent='0000-00-00' WHERE mail_id = " . (int) $row['mail_id'];
                    $data = mysqli_query($con, $sql);
                    echo 'Fehler beim Versand';
                }
            } catch (Exception $e) {
                if (CRONRUN) {
                    echo $e->getMessage();
                    exit;
                }
                $error[] = $e->getMessage();
            }
        }
    } catch (Exception $e) {
        if (CRONRUN) {
            echo $e->getMessage();
            exit;
        }
        $error[] = $e->getMessage();
    }
}

/*
$res = mysqli_query($con, $sql);

while ($row = mysqli_fetch_array($res)) {
    try {

        $mailTemplate = file_get_contents('inc/mail_template/infinite_hooks_news_'.$lang.'.html');
        if ($row['recipient_lastname'] AND $row['sex']) {
            $salutation = 'Sehr geehrte';
            $salutation .= ($row['sex']=='m')?'r ':' ';
            $salutationShort = ($row['sex']=='m')?'Herr ':'Frau ';
            $salutationShort .= $row['recipient_lastname'];
            $salutation .= $salutationShort;
        } else {
            $salutation = 'Hallo ' . $row['recipient_name'];
            $salutationShort = $row['recipient_name'];
        }
        $mailEnc = encrypt($row['recipient'], 'THE_IH_$NEW_FOREST');
        $replaceFrom = array('%Salutation%','%SalutationShort%', '%encMail1%', '%encMail2%');
        $replaceTo = array($salutation, $salutationShort, $mailEnc, $mailEnc);
        $mailContent = str_replace($replaceFrom, $replaceTo, $mailTemplate);
        $succ = send_mail($mailContent,
            'PHP10 und DSGVO',
            $row['recipient'],
            $row['recipient_name'], false, true, false, 'datenschutz@php10.de');

        if(!$succ) {
            $err.="Mailer Error: " . $mail->ErrorInfo;
            error_log($err);
        } else {
            $err.="Nachricht wurde versandt!";
        }
        sleep(rand(1,10));

        if ($succ) {
            $sql = "UPDATE mail set dsent=now() WHERE mail_id = " . (int) $row['mail_id'];
            $data = mysqli_query($con, $sql);
            $_GET['ok'] = 'done';
            if (CRONRUN) {
                echo 'done';
                exit;
            }
        }
    } catch (Exception $e) {
        if (CRONRUN) {
            echo $e->getMessage();
            exit;
        }
        $error[] = $e->getMessage();
    }
}


require("inc/header.inc.php");

if ($error) {
    $headerError = implode('<br>', $error);
}
?>
    <a href="javascript:void(0)" onClick="window.location.href = 'inbox.php'"><img alt="<?php sss('Back to List')?>" title="<?php sss('Back to List')?>" src="css/icon/align_just_icon&16.png" class="listmenuicon"></a><br><br>

    <div class="contentheadline"><?php echo ss('Mail')?></div>
    <br>
    <div class="contenttext">

    </div>
<?php
*/
exit();
die();
?>
