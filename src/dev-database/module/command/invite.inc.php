<?php
global $_VALID;

$commandOptions = [];
validate('confirmed', 'enum', ['true','false']);
$commandOptions['confirmed'] = $_VALID['confirmed'] ? $_VALID['confirmed'] : null;

$commandOptions['entity'] = $_SESSION['entity'];
$commandOptions['user'] = mb_strtolower($param[0]);

if (!isset($param[0])) {
    throw new Exception('User not found');
}

if($commandOptions['confirmed'] !== 'true'){
    $groups = [21,1001];
    $grSQL = "SELECT gr_id FROM gr WHERE LOWER(shortname)='" . mysqli_real_escape_string($con, $commandOptions['entity']) . "'";
    $grRES = mysqli_query($con, $grSQL);
    if (mysqli_num_rows($grRES) === 1) {
        $row = mysqli_fetch_row($grRES);
        $groups[] = $row[0];
    }
    $userSQL = "SELECT * FROM user WHERE LOWER(email) = '" . mysqli_real_escape_string($con, $commandOptions['user']) . "';";
    $userRES = mysqli_query($con, $userSQL);
    $email = '';
    $firstname = '';
    if (mysqli_num_rows($userRES) !== 1) {
        if (is_valid_email($commandOptions['user'])) {
            $email = $commandOptions['user'];
            //throw new Exception($commandOptions['user'] . ' ' . ss('is not a valid email'));
        } else {
            $email = $commandOptions['user'] . '';
            $firstname =  $commandOptions['user'] . '@magd.tools';
        }
        $api_key     = bin2hex(random_bytes(16));
        $loginLink   =  '?k=' . htmlspecialchars($api_key);
        $newpassword = ae_gen_password(2, false);
        $newpassword = sha1($newpassword . SALT);
        $sql         = "INSERT INTO user(email, password, firstname, lastname, is_active, login_link, lang) VALUES(
	'" . mysqli_real_escape_string($con, $email) . "',
	'" . $newpassword . "',
	'" . mysqli_real_escape_string($con, $firstname) . "',
	'',
	1,
	'" . $loginLink . "',
	'DE') ";
        if (!mysqli_query($con, $sql)) {
            throw new Exception('DB Insert Error');
        }
        $newUserId = mysqli_insert_id($con);
        foreach ($groups as $group) {
            $sqlPerm = "INSERT INTO user2gr(user_id,gr_id) VALUES(" . $newUserId . "," . $group . ")";
            if (!mysqli_query($con, $sqlPerm)) {
                throw new Exception('Group Permission Insert Error');
            }
        }
        $nextPage    = "__CONFIRMATION__";
        $showMessage = sprintf(ss('Use the link below to give %s access to %s'), htmlspecialchars($commandOptions['user']), htmlspecialchars($commandOptions['entity'])) . ':<br><br>'
            . HTTP_HOST . $commandOptions['entity'] . '.php' . $loginLink . '<br><br>&nbsp;';
    } else {
        $this->errorLevel = 'notice';
        throw new Exception('User has already access');
    }
} else {
    $showMessage = ss('User permission granted');
}