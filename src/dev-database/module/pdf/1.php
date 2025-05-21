<?php

$pdfContent = null;
$anlage = '';
$titel = 'Bestätigung';
$einzelText = '';
$sammelText = '';

$year = $_SESSION['bankyear'];
//ini_set('display_errors', 1);
//require VENDOR_ROOT . 'autoload.php';

$sql = "SELECT bank.betrag, bank.buchungstag, bank.process_id, parent.last_payment, parent.salutation, parent.title, parent.name, parent.firstname, parent.street, parent.zipcode, parent.city FROM bank 
    LEFT JOIN parent ON bank.parent_id=parent.parent_id 
    LEFT JOIN user ON parent.user=user.user_id
   WHERE bank.buchungstag BETWEEN '" . (int) $year . "-01-01' AND '" . (int) $year . "-12-31'
    AND bank.process_id IN (1,2)
    AND user.user_id=" . (int) $recipientUserId . "
    ORDER BY bank.buchungstag";

$payments = [];
$res = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($res)) {
    $payments[] = $row;
    $amountNum += (float) $row['betrag'];
}

if (count($payments) > 1) {
    $titel = 'Sammelbestätigung';
    $sammelText = 'Ob es sich um den Verzicht auf Erstattung von Aufwendungen handelt, ist der Anlage zur Sammelbestätigung zu entnehmen.';
    $anlage = '<div class="page-break" style="page-break-after: always;"></div><<h1 style="font-size: 10pt;"><strong>Anlage zur Sammelbest&auml;tigung</strong></h1>
<table style="border-collapse: collapse; width: 100%; height: 126px;" border="1">
<tbody>
<tr style="height: 54px;">
<td style="width: 25%; height: 54px;"><strong>Datum der Zuwendung</strong></td>
<td style="width: 25%; height: 54px;"><strong>Art der Zuwendung</strong></td>
<td style="width: 25%; height: 54px;"><strong>Verzicht auf die Erstattung von Aufwendungen ja/nein</strong></td>
<td style="width: 25%; height: 54px;"><strong>Betrag</strong></td>
</tr>
';
    foreach ($payments as $payment) {
        $anlage .= '<tr style="height: 18px;">
<td style="width: 25%; height: 18px;">' . date('d.m.Y', strtotime($payment['buchungstag'])) . '</td>
<td style="width: 25%; height: 18px;">' . (($payment['process_id'] == 1)?'Mitgliedsbeitrag':'Spende') . '</td>
<td style="width: 25%; height: 18px;">nein</td>
<td style="width: 25%; height: 18px;">' . number_format($payment['betrag'], 2, ',', '') . ',-&euro;</td>
</tr>';
    }
    $anlage .= '
<tr style="height: 18px;">
<td style="width: 25%; height: 18px;">&nbsp;</td>
<td style="width: 25%; height: 18px;">&nbsp;</td>
<td style="width: 25%; height: 18px;">&nbsp;</td>
<td style="width: 25%; height: 18px;">&nbsp;</td>
</tr><tr style="height: 18px;">
<td style="width: 25%; height: 18px;">Gesamtsumme:</td>
<td style="width: 25%; height: 18px;">&nbsp;</td>
<td style="width: 25%; height: 18px;">&nbsp;</td>
<td style="width: 25%; height: 18px;">' . number_format($amountNum, 2, ',', '') . ',-&euro;</td>
</tr>';
    $anlage .= '</tbody>
</table>';
} else {
    $einzelText = 'Es handelt sich um den Verzicht auf Erstattung von Aufwendungen &nbsp;JA &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;NEIN X';
}


$row = $payments[0];

if ($amountNum <= 0) {
    echo '<div style="margin: auto; width: 32%; padding: 4px 12px; background-color: #ffdddd; border-left: 6px solid #f44336"><p><strong>Achtung!</strong> Die Erstellung einer Spendenbescheinigung für diesen Zeitraum ist nicht möglich</div>';
    exit;
}
$name = '';
if ($row['salutation'] && 'Firma' !== $row['salutation']) {
    $name = $row['salutation'] . ' ';
}
if ($row['title']) {
    $name .= $row['title'] . ' ';
}
if ($row['firstname']) {
    $name .= $row['firstname'] . ' ';
}
$name .= $row['name'];
$street = $row['street'];
$zipcode = $row['zipcode'];
$city = $row['city'];
$last_payment = date('d.m.Y', strtotime($row['last_payment']));
$pdfcontent = '';

try {
    //$pdfcontent = file_get_contents('pdf_template.html');
    $pdfcontent = $attachmentTemplate;
    $amount = '<b><u>' . number_format($amountNum,2,',','') . ' €</u></b><br>(in Buchstaben: ' . htmlentities(num2text($amountNum)) . ')';
    $pdfcontent = str_replace(
        [
            '__AMOUNT__',
            '__TITEL__',
            '__YEAR__',
            '__NAME__',
            '__STREET__',
            '__ZIPCODE__',
            '__CITY__',
            '__LAST_PAYMENT__',
            '__EINZEL_TEXT__',
            '__SAMMELBESTAETIGUNG_TEXT__',
            '__ANLAGE__'
        ],
        [
            $amount,
            $titel,
            $year,
            $name,
            $street,
            $zipcode,
            $city,
            $last_payment,
            $einzelText,
            $sammelText,
            $anlage
        ], $pdfcontent);
    if (!isset($_REQUEST['combine_pdf'])) {
        $options = new Dompdf\Options();
        $options->set('defaultFont', 'Calibri');
        $dompdf = new Dompdf\Dompdf($options);

        $dompdf->loadHtml($pdfcontent);

    // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4');

    // Render the HTML as PDF
        $dompdf->render();

    // Output the generated PDF to Browser
        $pdfContent = $dompdf->output();
    } else {
        if (isset($_REQUEST['combine_pdf']) && $combinedPdfContent) {
            $combinedPdfContent .= '<div class="page-break" style="page-break-after: always;"></div>';
        }
        $combinedPdfContent .= $pdfcontent;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
