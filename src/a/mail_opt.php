<?php
$modul="mailopt";

require("../inc/req.php");

validate('g','int nullable');
validate('vor','string nullable');
validate('nach','string nullable');
validate('inout', 'enum', array('in','out') );

if ($_VALID['g']) {

    $sql = "SELECT * FROM mail_group WHERE mail_group_id=" . $_VALIDDB['g'];
    $gres = mysqli_query($con, $sql) or die(mysqli_error($con));

    $group = mysqli_fetch_array($gres);

    if (!count($group)) {
        die('--');
    }

    $sql = "SELECT response_mail FROM mail_sender WHERE mail_sender_id = " . $group['sender'] . " LIMIT 0,1";
    $gres = mysqli_query($con, $sql) or die(mysqli_error($con));

    $grow = mysqli_fetch_array($gres);

    $actionRecipient = $grow[0];
}

// Vom Wanderreiten Newsletter-Anmelden Formular
if (isset($_POST['v134231a18923124v'])) {

    validate('m','email');
    $mail = $_VALID['m'];
    if ($mail) {
        if ($_VALID['inout'] == 'in') {
            $sql = "INSERT INTO mail_opt (mail, mail_group_id, `inout`)
      VALUES (" . $_VALIDDB['m'] . ", " . $_VALIDDB['g'] . ", 'in')";
            mysqli_query($con, $sql);

            $sql = "INSERT INTO mail (recipient, group_id, recipient_firstname, recipient_lastname, lang)
  VALUES (" . $_VALIDDB['m'] . ", " . $_VALIDDB['g'] . ", " . $_VALIDDB['vor'] . ", " . $_VALIDDB['nach'] . ", 'de')";
            mysqli_query($con, $sql);
        }
        mail($actionRecipient, 'Newsletter Anmeldung',
            'Mail: ' . $mail . ', 
Gruppe: ' . $group['name']);
    }
    header('Location: ' . $group['optin_url']);
    exit;

}

// Vom Newsletter Abmelden Link:
validate('m','string');
$mail = decrypt($_VALID['m'], 'THE_IH_$NEW_FOREST');
if ($mail) {
    if ($_VALID['inout'] == 'in') {
        $sql = "INSERT INTO mail_opt (mail, mail_group_id, `inout`)
      VALUES ('" . $mail . "', " . $_VALIDDB['g'] . ", 'in')";
        mysqli_query($con, $sql);

        $sql = "UPDATE mail SET group_id=8 WHERE group_id = " . $_VALIDDB['g'] . " 
            AND recipient = '" . $mail . "'";
        mysqli_query($con, $sql);
        mail($actionRecipient, 'Newsletter Anmeldung',
            'Mail: ' . $mail . ', 
Gruppe: ' . $group['name']);
        header('Location: ' . $group['optin_url']);
    } if ($_VALID['inout'] == 'out') {
        mail($actionRecipient, 'Newsletter Abmeldung',
            'Mail: ' . $mail . ', 
Gruppe: ' . $group['name']);
        $sql = "INSERT INTO mail_opt (mail, mail_group_id, `inout`)
      VALUES ('" . $mail . "', " . $_VALIDDB['g'] . ", 'out')";
        mysqli_query($con, $sql);

        $sql = "DELETE FROM mail WHERE group_id = " . $_VALIDDB['g'] . " 
            AND recipient = '" . $mail . "'";
        mysqli_query($con, $sql);
        header('Location: ' . $group['optout_url']);
    }
}
exit;

?>
