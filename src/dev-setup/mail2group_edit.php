<?php
$modul="ajax";

require("../inc/req.php");
validate('mail_id','int');
validate('gr_id', 'int');
validate('yn', 'boolean');

/*** Rights ***/
// Generally for people with right do manage newsletters
GRGR(24);

if (!isset($_MISSING)) {
    if ($_VALID['yn'] == 1) {
        $sql = "REPLACE INTO mail2group(mail_id,group_id) VALUES(".$_VALIDDB['mail_id'].",".$_VALIDDB['gr_id'].")";
    } else {
        $sql = "DELETE FROM mail2group WHERE mail_id = ".$_VALIDDB['mail_id']." AND group_id = ".$_VALIDDB['gr_id'];
    }
    mysqli_query($con, $sql) or print(mysqli_error());
}
?>
