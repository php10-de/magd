<?php

if (!$lastname) {
    $salutation = 'Hallo ' . $row['firstname'];
    $salutationShort = $row['firstname'];
    if ($row['salutation'] && $row['salutation'] !== 'Firma')  {
        $dear = 'liebe' . (($row['salutation']=='Herr')?'r':'');
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
} else if ($lastname AND isset($row['salutation'])) {
    $salutation = 'Liebe';
    $salutation .= ($row['salutation'] == 'Herr')?'r ':' ';
    $salutationShort = $row['salutation'] . ' ';
    if ($row['title'])  {
        $salutationShort .= $row['title'] . ' ';
    }
    $salutationShort .= $lastname;
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

$replaceFrom = array('%Salutation%',
                     '%SalutationShort%',
                     '%OptInURL%',
                     '%OptOutURL%',
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
                   $mailGroupRow['optin_url'],
                   $mailGroupRow['optout_url'],
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