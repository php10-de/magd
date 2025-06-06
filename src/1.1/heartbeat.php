<?php

$modul="cron";
define('LOG_HEARTBEAT', false);

require("inc/req.php");

// For Administrators only
if (!CRONRUN) GRGR(1);
//Form Hook After Group
/*** Validation ***/

//require_once VENDOR_ROOT . 'autoload.php';
//$templates = array('cron_email_despatched');
//define('TPL_DONE', true);

require_once(MODULE_ROOT . 'cron/CronParser.php');

$cron = new CronParser();

$crontasks1 = $con->query("
	SELECT * FROM cron
	WHERE active <> 0
");

while ($crontask = $crontasks1->fetch_array())
{
	if(LOG_HEARTBEAT){
		error_log("***** Processing cron task '$crontask[task]' *****");
	}
	//if one of cron crashed, send alert email
	if ($crontask['ok'] == false){
		mail("A cron task was failed", "'$crontask[task]' crashed when we tried to run it.",ERROR_MAIL_RECIPIENT);
	}

	if ($cron->calcLastRan($crontask['mhdmd'])){
		//0=minute, 1=hour, 2=dayOfMonth, 3=month, 4=week, 5=year
		$lastRan = $cron->getLastRan();
		$my_logs = '';

		//error_log($cron->getDebug());

		if ($cron->getLastRanUnix() > $crontask['ran_at']){
			if(LOG_HEARTBEAT) {
				error_log("'$crontask[task]' \r\ndue to be run at: $lastRan[5]-$lastRan[3]-$lastRan[2] $lastRan[1]:$lastRan[0]\r\nlast ran at: " . date('Y-m-d H:i:s', $crontask['ran_at']) . "\r\nTime now is: " . date('Y-m-d H:i:s'));
				error_log("Begin processing '$crontask[task]'");
			}
			//log the time we start this cron
			$con->query("
				UPDATE cron
				SET ran_at = " . time() . ", ok = false
				WHERE cron_id = $crontask[cron_id]
			");

			run_cron_task($crontask['file'],$crontask['parameters']);

			//completed the cron task
			$con->query("
				UPDATE cron
				SET ok = true, end_time = ".time()."
				WHERE cron_id = $crontask[cron_id]
			");
		}
		else{
			if(LOG_HEARTBEAT) {
				error_log("'$crontask[task]' is not due.\r\nLast due at: $lastRan[5]-$lastRan[3]-$lastRan[2] $lastRan[1]:$lastRan[0]\r\nlast ran at: " . date('Y-m-d H:i:s', $crontask['ran_at']) . "\r\nTime now is: " . date('Y-m-d H:i:s'));
			}
		}

	}
	else{
		error_log("Unable to calculate LastRan for cron id: $crontask[cron_id]");
	}


}

function run_cron_task($file,$parameters){

	global $con, $my_logs;
	$parts = parse_url($parameters);
	parse_str($parts['query'], $params_array);
	foreach ($params_array as $key => $value){
		$_REQUEST[$key] = $value;
	}
	try {
		include $file;
	}catch (Exception $exception){
		error_log( " **** CRON Error **** ".$exception->getMessage());
	}
}

?>
