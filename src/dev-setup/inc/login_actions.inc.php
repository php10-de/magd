<?php

$_SESSION['logedin']=true;
// user groups
$grSql = "
SELECT user2gr.gr_id,gr.shortname 
FROM user2gr LEFT JOIN gr ON gr.gr_id=user2gr.gr_id 
WHERE user2gr.user_id=" . $user_id;
$grRes = mysqli_query($con, $grSql);
if ($grRes) {
    while ($grRow = mysqli_fetch_row($grRes)) {
        $_SESSION['GROUP'][$grRow[0]] = $grRow[0];
        $_SESSION['GROUP_NAME'][$grRow[1]] = $grRow[1];
    }
}

// group rights
$grrSql = "SELECT right_id, yn as gr_yn FROM right2gr WHERE right2gr.gr_id IN (SELECT gr_id FROM user2gr WHERE user2gr.user_id=" . $user_id . ")";
$grrRes = mysqli_query($con, $grrSql);
if ($grrRes) {
    while ($urRow = mysqli_fetch_row($grrRes)) {
        $_SESSION['RIGHTS'][$urRow[0]] = $urRow[1];
    }
}

// user rights
$urSql = "(SELECT right_id, yn as u_yn FROM right2user WHERE right2user.user_id=" . $user_id . ")";
$urRes = mysqli_query($con, $urSql);
if ($urRes) {
    while ($urRow = mysqli_fetch_row($urRes)) {
        $_SESSION['RIGHTS'][$urRow[0]] = $urRow[1];
    }
}

$_SESSION['logedin'] = true;
$_SESSION['login_time'] = time();
$_SESSION['user_id'] = $user_id;
$_SESSION['lang'] = $lang;

setcookie('logedin', $_SESSION['user_id'], time() + (86400 * 30 * 30), "/");
setcookie('login_time', $_SESSION['login_time'], time() + (86400 * 30 * 30), "/");
setcookie('lang', $_SESSION['lang'], time() + (86400 * 30 * 30), "/");
/*
  if ($_VALID['stay']) {
  // path for cookies - valid for all paths in domain
  $cookie_path = "/";

  // timeout value for the cookie
  $cookie_timeout = 60 * 60 * 25; // timeout value for the garbage collector
  $garbage_timeout = $cookie_timeout + (60 * 10); //cookie + 10 minutes

  session_name();  // dynamically set - beyond question scope
  session_id(); // dynamically set - beyond question scope

  session_set_cookie_params($cookie_timeout, $cookie_path);

  // set the garbage collector to clean the session files
  ini_set('session.gc_maxlifetime', $garbage_timeout);

  // set new session directory to ensurer unique garbage collection
  $sessdir = ini_get('session.save_path').DIRECTORY_SEPARATOR."visitor";
  if (!is_dir($sessdir)) { mkdir($sessdir, 0777); }
  ini_set('session.save_path', $sessdir);
  session_start();
  } */