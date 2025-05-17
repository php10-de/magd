<?php

function getLangArr() {
    global $DE, $EN, $GR, $RO;
    switch (strtolower(CURRENT_LANG)) {
        case 'de':
            return $DE;
            break;
        case 'en':
            return $EN;
            break;
        case 'ro':
            return $RO;
            break;
        case 'gr':
            return $GR;
            break;
    }
}

function ss($s, $v1 = null, $v2 = null, $v3 = null) {
    if (!$s) {
        return '';
    }
    $currentLangArr = getLangArr();
    if (isset($currentLangArr[$s]) && $currentLangArr[$s]) {
        $t = $currentLangArr[$s];
        if (!$v1) {
            // standard
            $sTranslated = $t;
        } elseif ($v3) {
            $sTranslated = sprintf($t, $v1, $v2, $v3);
        } elseif ($v2) {
            $sTranslated = sprintf($t, $v1, $v2);
        } else {
            $sTranslated = sprintf($t, $v1);
        }
    } else {
        $sTranslated = $s;
    }
    return html($sTranslated);
}


function sss($s, $v1 = null, $v2 = null, $v3 = null) {
    if (!$s) {
        return '';
    }
    $currentLangArr = getLangArr();
    if (isset($currentLangArr[$s])) {
        $t = $currentLangArr[$s];
        if (!$v1) {
            // standard
            $sTranslated = $t;
        } elseif ($v3) {
            $sTranslated = sprintf($t, $v1, $v2, $v3);
        } elseif ($v2) {
            $sTranslated = sprintf($t, $v1, $v2);
        } else {
            $sTranslated = sprintf($t, $v1);
        }
    } else {
        $sTranslated = $s;
    }
    echo html($sTranslated);
}


?>