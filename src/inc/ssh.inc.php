<?php

function getDigitalSignature($data) {
    global $con;
    if(LOG) {
        error_log('create signature');
    }
    $sql = "SELECT value FROM setting WHERE id='PRIVATE_KEY'";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($res);
    if (!$row[0]) {
        if(LOG) {
            error_log('no PRIVATE_KEY found');
        }
        return false;
    }

    $sig_private = $row[0];
    $sPKeyID = openssl_get_privatekey($sig_private);
    // calculate the signature
    openssl_sign($data, $sSignature, $sPKeyID, "sha1WithRSAEncryption");
    // remove key from memory
    openssl_free_key($sPKeyID);
    return getHEXConvertedSignature($sSignature);
}

function getHEXConvertedSignature($sSignature){

    $sFinalSingature = '';
    for($i=0,$ii=strlen($sSignature); $i<$ii; $i++){
        $sChar = $sSignature[$i];
        $sFinalSingature .= str_pad(dechex(ord($sChar)), 2, 0, STR_PAD_LEFT);
    }
    return $sFinalSingature;
}

function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

function generateKeys() {
    global $con;
    if(LOG) {
        error_log('generate keys');
    }
    $new_key_pair = openssl_pkey_new(array("private_key_bits" => 2048,"private_key_type" => OPENSSL_KEYTYPE_RSA,));
    openssl_pkey_export($new_key_pair, $private_key_pem);
    $details = openssl_pkey_get_details($new_key_pair);
    $public_key_pem = $details['key'];
    if (!$public_key_pem || !$private_key_pem) {
        return false;
    }
    if(LOG) {
        error_log('keys generated OK');
    }
    $sig_private = $private_key_pem;
    $sig_public = $public_key_pem;

    if(LOG) {
        error_log('save keys to setting table in database');
    }
    $sql = "REPLACE INTO setting VALUES('PUBLIC_KEY', '".addslashes($sig_public) . "', 1)";
    mysqli_query($con, $sql) or error_log(mysqli_error($con));
    $sql = "REPLACE INTO setting VALUES('PRIVATE_KEY', '".addslashes($sig_private) . "', 1)";
    mysqli_query($con, $sql) or error_log(mysqli_error($con));
    return true;
}

function ssl_verify($string, $signature, $pub_key) {
    if(LOG) {
        error_log('check ssl signature');
    }
    $signature = hexToStr($signature);
    $pub_key = openssl_pkey_get_public($pub_key);

    $ok = openssl_verify($string, $signature, $pub_key);

    openssl_free_key($pub_key);

    if(LOG) {
        error_log('result: ' . $ok);
    }

    return $ok;

    /*
    if ($ok == 1) {
        error_log("gut");
    } elseif ($ok == 0) {
        error_log("schlecht");
    } else {
        error_lol("2 Mist, Fehler beim überprüfen der Signatur");
    }*/
}