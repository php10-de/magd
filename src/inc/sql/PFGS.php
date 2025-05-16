<?php
$hroseVersion = '1.0.5';

$sql[] = "UPDATE bank SET process_id=1 
WHERE parent_id IS NOT NULL 
AND process_id IS NULL 
AND bank.betrag > 0
AND parent_id NOT IN (SELECT parent_id FROM parent WHERE user IN (SELECT user_id FROM user2gr WHERE gr_id='1004'))
AND bank.buchungstag BETWEEN '" . $_SESSION['bankyear'] . "-01-01' AND '" . $_SESSION['bankyear'] . "-12-31'";


$sql[] = "UPDATE bank SET bank.beleg=(SELECT p.beleg FROM datev_process p WHERE p.datev_process_id=bank.process_id) WHERE bank.process_id IS NOT NULL
AND bank.buchungstag BETWEEN '" . $_SESSION['bankyear'] . "-01-01' AND '" . $_SESSION['bankyear'] . "-12-31'";


$sql[] = "UPDATE bank SET bank.belegt=1 WHERE bank.process_id IS NOT NULL
AND bank.beleg=1 AND bank.beleg_media IS NOT NULL AND bank.beleg_media!='' 
AND bank.buchungstag BETWEEN '" . $_SESSION['bankyear'] . "-01-01' AND '" . $_SESSION['bankyear'] . "-12-31'";
