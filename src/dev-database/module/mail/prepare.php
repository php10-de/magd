<?php
//ini_set('display_errors', 'on');
if (file_exists(INC_ROOT . "num2text.inc.php")) {
    include INC_ROOT . "num2text.inc.php";
}
ini_set('display_errors', 'on');
function deleteFilesInDirectory($directory) {
    // Check if the directory exists
    if (!is_dir($directory)) {
        echo "Directory does not exist: $directory";
        return false;
    }

    // Open the directory and read its contents
    $items = scandir($directory);

    foreach ($items as $item) {
        // Skip the special entries '.' and '..'
        if ($item === '.' || $item === '..') {
            continue;
        }

        // Full path of the item
        $filePath = $directory . DIRECTORY_SEPARATOR . $item;

        if (is_dir($filePath)) {
            // Recursively delete subdirectories
            deleteFilesInDirectory($filePath);

            // Remove the empty directory
            rmdir($filePath);
        } else {
            // Delete the file
            unlink($filePath);
        }
    }

    return true;
}
$mailDelSql = "TRUNCATE TABLE mail";
mysqli_query($con, $mailDelSql) or die(mysqli_error($con));

$attachmentDir = MEDIA_PRIV_ROOT . 'mail';
if (!deleteFilesInDirectory($attachmentDir)) {
    echo "Failed to delete attachments.";
}

if (isset($_REQUEST['mail_group_id'])) {
    $mailGroupId = (int) $_REQUEST['mail_group_id'];
    $mailGroupSql = "SELECt * FROM mail_group WHERE mail_group_id = " . $mailGroupId;
    $mailGroupRes = mysqli_query($con, $mailGroupSql) or die(mysqli_error($con));
    $mailGroupRow = mysqli_fetch_assoc($mailGroupRes);
    $sqlFilter = $mailGroupRow['sql_filter'];
    if ((strpos($sqlFilter, '%YEAR') !== false) && !$_SESSION['bankyear']) {
        header('Location: accounting.php');
        exit;
    }

    if (file_exists(__DIR__ . '/mail.group.' . $mailGroupId . '.php')) {
        require __DIR__ . '/mail.group.' . $mailGroupId . '.php';
    } else {
        $templateSql = "SELECT mail_letter.name, mail_letter.title, mail_letter.html, mail_letter.pdf, pdf.html_template, pdf.static_file_media FROM mail_letter 
LEFT JOIN pdf ON mail_letter.pdf=pdf.pdf_id 
WHERE mail_letter_id = " . (int) $mailGroupRow['mail_letter_id'];
        $templateRes = mysqli_query($con, $templateSql) or die(mysqli_error($con));
        $templateRow = mysqli_fetch_assoc($templateRes);
        $subject = $templateRow['title'];;
        $mailTemplate = $templateRow['html'];
        $attachmentTemplateId = $templateRow['pdf'];
        $attachmentTemplate = $templateRow['html_template'];
        $_VALIDDB['attachment'] = 'NULL';
        $staticAttachment = false;
        $combinedPdfContent = '';
        if ($templateRow['pdf']) {
            if ($templateRow['static_file_media'] && file_exists(MEDIA_PRIV_ROOT . $templateRow['static_file_media'])) {
                $_VALIDDB['attachment'] = "'" . $templateRow['static_file_media'] . "'";
                $staticAttachment = true;
            }
        }
        $parentSql = "SELECT user.user_id, user.email, parent.salutation, parent.title, parent.firstname, parent.name FROM parent 
LEFT JOIN user ON parent.user=user.user_id ";
        if (!$sqlFilter) {
            $sqlFilter = 'WHERE 1=1';
        }
        $sqlFilter = str_replace('%YEAR%', $_SESSION['bankyear'], $sqlFilter);
        $parentSql .= $sqlFilter;
        $parentRes = mysqli_query($con, $parentSql) or die(mysqli_error($con));
        while ($row = mysqli_fetch_assoc($parentRes)) {
            $recipientUserId = $row['user_id'];
            $recipient = $row['email'];
            $salutation = $row['salutation'];
            $title = $row['title'];
            $firstname = $row['firstname'];
            $lastname = $row['name'];
            $name = $title . ' ' . $firstname . ' ' . $lastname;
            require __DIR__ . '/placeholder.default.php';

            // PDF
            if (!$staticAttachment && $mailTemplate) {
                if (file_exists(MODULE_ROOT . 'pdf/' . $attachmentTemplateId . '.php')) {
                    require MODULE_ROOT . 'pdf/' . $attachmentTemplateId . '.php';
                    if ($pdfContent) {
                        $targetDir = MEDIA_PRIV_ROOT . 'mail';
                        $tmpfname = basename(tempnam($targetDir, "attachment_media_"));
                        $fileName = $tmpfname . '.pdf';
                        $targetFile = $targetDir . '/' . $fileName;
                        if (!file_put_contents($targetFile, $pdfContent)) {
                            echo "Failed to save PDF.";
                        }
                        $_VALIDDB['attachment'] = "'mail/" . $fileName . "'";
                    }
                }
            }
            $mailInsertSql = "INSERT INTO mail(mail_group, recipient, recipient_name, subject, content, attachment_media) VALUES($mailGroupId, " .
                "'" . mysqli_real_escape_string($con, $recipient) . "'," .
                "'" . mysqli_real_escape_string($con, $name) . "'," .
                "'" . mysqli_real_escape_string($con, $subject) . "'," .
                "'" . mysqli_real_escape_string($con, $mailContent) . "'," .
                $_VALIDDB['attachment'] . ")";
            mysqli_query($con, $mailInsertSql) or die(mysqli_error($con));
        }
        if (isset($_REQUEST['combine_pdf'])) {
            $finalHtml = '<!DOCTYPE html>
<html>
<head>
    <style>
        /* Force a page break before an element */
        .page-break {
            page-break-before: always;
        }

        /* Optionally ensure no content appears on the same page */
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>' . $combinedPdfContent . '</body>
</html>';
            //echo $combinedPdfContent;exit;

            //ob_end_clean(); // Clear output buffer
            //header('Content-Type: application/pdf');
            $options = new Dompdf\Options();
            $options->set('defaultFont', 'Calibri');
            $dompdf = new Dompdf\Dompdf($options);

            $dompdf->loadHtml($combinedPdfContent);

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4');

            // Render the HTML as PDF
            $dompdf->render();

            ob_end_clean();

            $dompdf->stream('Kombiniert.pdf');
            //file_put_contents(MEDIA_PRIV_ROOT . 'mail/kombiniert.pdf', $dompdf->output());
            exit;
        }
    }
}
