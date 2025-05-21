<?php

$modul = "command";
if (isset($_REQUEST['cacheit'])) ob_start();
require("inc/req.php");
require MODULE_ROOT . 'command/Command.php';
define('SHOW_COMMAND_ID', false);

// Generally for people with the right to execute a command
$groupID = 29;
GRGR($groupID);

$n4a['commands.php?cacheit'] = '' . ss('Cache it') . '';
$n4a['commands.php?v=100000005'] = '' . ss('Version 100000005') . '';
require("inc/header.inc.php");

function throwExc($msg){
    echo $msg;
    throw new Exception($msg);
}
?>
<header class="page-header">
<!--    <h2>Pricing Tables</h2>-->

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="start.php">
                    <i class="fa fa-home"></i>
                </a>
            </li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
        </div>

        <h2 class="panel-title"><?php echo ss('Commands') ?></h2>
    </header>
    <div class="panel-body">
        <div class="table-responsive">


        <?php
        try{
        $sql = "
SELECT DISTINCT ID 
FROM command 
WHERE (lang = '" . CURRENT_LANG . "' 
    OR lang='" . DEFAULT_LANG . "')
    AND (is_word IS NULL 
        OR is_word = 0)";

        if (isset($_REQUEST['v'])) {
            validate('v', 'int');
            $sql .= ' AND version >= ' . $_VALIDDB['v'];
        }
        $res = mysqli_query($con, $sql) or throwExc($sql);
        while ($row = mysqli_fetch_array($res)) {
            echo '<table class="table table-bordered table-striped table-condensed"><tbody>';
            $first = false;
            //if (SHOW_COMMAND_ID) echo '<br><i>' . html($row['ID']) . '</i>';
            $sqlC = "SELECT command FROM command WHERE ID = '" . my_sql($row['ID']) . "' AND lang='" . CURRENT_LANG . "'";
            $resC = mysqli_query($con, $sqlC) or throwExc($sqlC);
            while ($rowC = mysqli_fetch_array($resC)) {
                $phraseArr = explode(' ', $rowC['command']);
                $phrase    = '';
                $firstWord = true;
                foreach ($phraseArr as $word) {
                    $alias[$word] = [];
                    $sqlW         = "
    SELECT DISTINCT command 
    FROM command 
    WHERE is_word=1 
        AND LOWER(ID) = (SELECT DISTINCT c2.ID FROM command c2 WHERE c2.is_word=1 AND c2.lang='" . CURRENT_LANG . "' AND c2.command='" . my_sql(mb_strtolower($word)) . "') 
        AND lang='" . CURRENT_LANG . "'";
                    $resW         = mysqli_query($con, $sqlW) or throwExc($sqlW);
                    while ($rowW = mysqli_fetch_array($resW)) {
                        if ($rowW['command'] !== $word) {
                            $theAlias = $rowW['command'];
                            if ($firstWord) {
                                $theAlias = ucfirst($theAlias);
                            }
                            $alias[$word][] = html($theAlias);
                        }
                    }
                    $isAlias     = (count($alias[$word]) > 0);
                    $aliasTipTip = implode(', ', $alias[$word]);
                    if ($firstWord) {
                        $word = ucfirst($word);
                    }
                    $firstWord = false;
                    $phrase    .= '<span data-html="true" data-toggle="tooltip" data-placement="top" class="' . (($isAlias) ? 'tip_alias" title="' . $aliasTipTip . '"' : '"') . ">" . html($word) . '</span>&nbsp;';
                }
                echo '<tr><td>' . (($first) ? '' : '<b>') . ucfirst($phrase) . (($first) ? '' : '</b>') . '</td></tr>';
                $first = true;
            }
            if (CURRENT_LANG != DEFAULT_LANG) {
                $sqlC = "
SELECT command 
FROM command 
WHERE ID = '" . my_sql($row['ID']) . "' 
    AND lang='" . DEFAULT_LANG . "'
    AND (is_word IS NULL 
        OR is_word = 0)";
                if (isset($_REQUEST['v'])) {
                    validate('v', 'int');
                    $sqlC .= ' AND version >= ' . $_VALIDDB['v'];
                }
                $resC = mysqli_query($con, $sqlC) or throwExc($sqlC);
                while ($rowC = mysqli_fetch_array($resC)) {
                    $phraseArr = explode(' ', $rowC['command']);
                    $phrase    = '';
                    $firstWord = true;
                    foreach ($phraseArr as $word) {
                        $alias[$word] = [];
                        $sqlW         = "
    SELECT DISTINCT command 
    FROM command 
    WHERE is_word=1 
        AND LOWER(ID) = (SELECT DISTINCT c2.ID FROM command c2 WHERE c2.is_word = 1 AND c2.lang='" . DEFAULT_LANG . "' AND c2.command='" . my_sql(mb_strtolower($word)) . "') 
        AND lang='" . DEFAULT_LANG . "'
    ";
                        $resW         = mysqli_query($con, $sqlW) or throwExc($sqlW);
                        while ($rowW = mysqli_fetch_array($resW)) {
                            if ($rowW['command'] !== $word) {
                                $theAlias = $rowW['command'];
                                if ($firstWord) {
                                    $theAlias = ucfirst($theAlias);
                                }
                                $alias[$word][] = html($theAlias);
                            }
                        }
                        $isAlias     = (count($alias[$word]) > 0);
                        $aliasTipTip = implode(', ', $alias[$word]);
                        if ($firstWord) {
                            $word = ucfirst($word);
                        }
                        $firstWord = false;
                        $phrase    .= '<span data-html="true" data-toggle="tooltip" data-placement="top" class="' . (($isAlias) ? 'tip_alias" title="' . $aliasTipTip . '"' : '"') . ">" . html($word) . '</span>&nbsp;';
                    }
                    echo '<tr><td>' . (($first) ? '' : '<b>') . ucfirst($phrase) . (($first) ? '' : '</b>') . '</td></tr>';
                    $first = true;
                }
            }
            //echo '<br><br>';
            echo '</tbody></table>';
        }

        ?>
        </div>
    </div>
</section>
<?php
} catch (Exception $e){
    
} finally {
    require("inc/footer.inc.php");
    if (isset($_REQUEST['cacheit'])) {
        $commandsContent = ob_get_contents();
        file_put_contents('commands.html', $commandsContent);
        ob_end_flush();
    }
}

