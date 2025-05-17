<?php
$modul="sql";

require("inc/req.php");

if (isset($_REQUEST['cacheit'])) ob_start();
$n4a['sql.php?autoexec'] = ss('Autoexec');
require("inc/header.inc.php");
define('LOG', false);
define('ENABLE_DEPLOY_SYNC', false);

/*** Rights ***/
// For Technicians only
RR(2);

if (isset($_REQUEST['autoexec'])) {
    $DB_VERSION = file_get_contents(SQL_ROOT . 'version.txt');
}

?>
<script type="text/javascript" src="<?php echo HTTP_SUB?>assets/vendor/ace/src-min/ace.js"></script>
<script type="text/javascript" src="<?php echo HTTP_SUB?>assets/vendor/ace/src-min/ext-language_tools.js"></script>
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
<div class="row">
    <div class="col-lg-12 col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                </div>

                <h2 class="panel-title"><?php sss('Features')?></h2>
            </header>
            <div class="panel-body">
                <div class="toggle" data-plugin-toggle data-plugin-options='{ "isAccordion": true }'>
<?php

include_once INC_ROOT . 'ssh.inc.php';
$path = "inc/sql/";
$handle=opendir ($path);

if (!file_exists(INC_ROOT . 'serial.txt')) {
    if(LOG) {
        error_log('serial does not exist');
    }
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    $serial = $randomString;
    if(LOG) {
        error_log('setze ' . INC_ROOT . 'serial.txt');
    }
    file_put_contents(INC_ROOT . 'serial.txt', $serial);
} else {
    if(LOG) {
        error_log('read serial');
    }
    $serial = file_get_contents(INC_ROOT . 'serial.txt');
}

$sql = "SELECT 1 FROM setting WHERE id='PRIVATE_KEY'";
$res = mysqli_query($con, $sql);

if ($res AND !mysqli_num_rows($res)) {
    if(LOG) {
        error_log('private key not found');
    }
    generateKeys();
}

while ($datei = readdir ($handle)) {
    if ($datei != '.'
        AND $datei != '..'
        AND $datei != '.svn'
        AND $datei != 'readme.txt'
        AND $datei != 'serials.txt'
        AND $datei != 'version.txt'
        AND $datei != 'hroses'
        AND (strpos($datei, '.sql') === false)
    ) {
        $j++;
        if(LOG) {
            error_log('***   ' . $datei . '   ***');
        }
        unset($sql);
        $autoExec = false;
        $hrose = false;
        $hroseRestriction = false;
        $date = false;
        $doneHrose = array();
        $alreadyDone = false;
        $executed = false;
        $single = false;
        $hroseVersion = false;
        include($path . $datei);
        if ($hroseVersion) {
            $autoExec = true;
        }


        $apiError = false;
        if ($hrose) {
            if(LOG) {
                error_log('hroses restriction given');
            }
            if (!in_array($serial, $hrose)) {
                if(LOG) {
                    error_log('this system is not concerned');
                }
                $hroseRestriction = true;
            } else {

                $autoExec = true;
                if(LOG) {
                    error_log('loading deploy status file...');
                }
                $hroseFile = $path . 'hroses/' . $datei . '.hrose';
                if (file_exists($hroseFile)) {
                    $doneHrose = unserialize(file_get_contents($hroseFile));
                    if ($doneHrose AND in_array($serial, $doneHrose)) {
                        if(LOG) {
                            error_log('sql already executed');
                        }
                        $alreadyDone = true;
                    } else {
                        if(LOG) {
                            error_log('not yet executed');
                        }
                    }
                } else {
                    if(LOG) {
                        error_log('file not found. not yet executed');
                    }
                }
            }
        }

        if($autoExec AND LOG) {
            error_log('auto execution');
        }

        if (!$hroseRestriction) {
            if(LOG) {
                error_log('not restricted for this client');
            }

            $status = '';
            if($hrose AND !$alreadyDone) {
                $status = ' (waiting for you)';
            } else if ($hrose) {
                $status = ' (waiting for others)';
            }
            if($status AND LOG) {
                error_log($status);
            }
            
            ?>
                <!-- onclick="$('tr .dotted').hide(); $('.file<?php echo $j ?>').show()" -->
                
                    <section class="toggle">
                        <label><u><?php echo ucfirst(substr($datei, 0, -4)) ?></u>  <?php echo $status?></label>
                        <div class="toggle-content">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-none bw">
                                        <tbody>

                

            <?php
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($date);
            $interval = $datetime1->diff($datetime2);
            $days = (int)$interval->format('%a');
            $skip = false;
            $error = array();
            if ($date AND ($days > SQL_AUTOEXEC_DAYS)) {
                if(LOG) {
                    error_log('too old');
                }
                $error[] = 'too old';
            }

            if ($single) {
                if(LOG) {
                    error_log('single execution');
                }
                if (count($sql) > 1) {
                    if(LOG) {
                        error_log('multiple statements for single execution found');
                    }
                    $error[] = 'multiple statements for single execution found';
                }
                if (!$hrose) {
                    if(LOG) {
                        error_log('no $hrose option set');
                    }
                    $error[] = 'no $hrose option set';

                }
                if ($alreadyDone) {
                    if(LOG) {
                        error_log('skipping because already done');
                    }
                    $skip = true;
                }
            }

            if ($error OR $skip) {
                if($error AND LOG) {
                    error_log('errors: ' . implode('. ', $error));
                }
                echo '<tr><td><span class="text-danger">' . implode('<br>', $error) . '</span></td></tr>';
            } else {
                if (!is_array($sql) || !count($sql)) {
                    if(LOG) {
                        error_log('skipping, empty file');
                    }
                    continue;
                }
                foreach ($sql as $sqlKey => $s) {
                    $i++;
                    echo '<tr class="dotted file' . $j . '" >';
                    echo '
                <td width="1%"><div class="btn btn-primary" id="' . $j . '_' . $i . '_r"><i style="cursor:pointer" class="fa fa-play" onclick="$(\'#' . $j . '_' . $i . '_r\').load(\'' . HTTP_HOST . 'a/exec_sql.php?f=' . html($datei) . '&amp;i=' . $sqlKey . '\');"></i>
                </div></td>'.'<td><div class="code-editor" style="width:inherit !important;min-height:34px;">' . $s . '</div></td></tr>';

                    if (isset($_REQUEST['autoexec']) AND $autoExec) {
                        if(LOG) {
                            error_log('auto execution');
                        }
                    }
                    if (isset($_REQUEST['autoexec']) AND $autoExec AND (!$hroseVersion || $DB_VERSION < $hroseVersion)) {
                        if(LOG) {
                            error_log('execute');
                        }
                        $executed = true;
                        if (strpos($s, 'file:') === 0) {
                            $fileName = DATA_ROOT . trim(ltrim($s, 'file:'));
                            if(LOG) {
                                error_log('reading SQL file ' . $fileName);
                            }
                            $s = file_get_contents($fileName);
                        }
                        if (!mysqli_multi_query($con, $s)) {
                            $errorMsg = mysqli_error($con);
                            if(LOG) {
                                error_log($errorMsg);
                            }
                            if (strpos($errorMsg, 'Duplicate') !== false OR
                                strpos($errorMsg, 'already exists') !== false) {
                                $executed = false;
                                echo '<tr><td><span class="text-warning">Duplicate</span></td></tr>';
                            } elseif ($alreadyDone) {
                                echo '<tr><td><span class="text-warning">OK again</span></td></tr>';
                            } else {
                                $executed = false;
                                echo '<tr><td><span class="text-danger">' . mysqli_error($con) . '</span></td></tr>';
                            }
                        } elseif ($alreadyDone) {
                            echo '<tr><td><span class="text-warning">OK again</span></td></tr>';
                        } else {
                            echo '<tr><td><span class="text-success">OK</span></td></tr>';
                        }
                    } else {
                        if(LOG) {
                            //error_log($hroseVersion);
                            //error_log($DB_VERSION);
                        }
                    }
                }
                if(!$executed) {
                    if(LOG) {
                        error_log('error(s) or not required');
                    }
                }
            }


            if($hrose AND ($executed OR !$single)) {
                if(LOG) {
                    error_log('calling home');
                }
                // CURL
                $signature = getDigitalSignature($serial . $datei);
                sort($hrose);
                $postdata = array(
                    "serial" => $serial,
                    "hroses" => json_encode($hrose),
                    "filename" => $datei,
                    "signature" => $signature
                );
                $ch = curl_init(MASTER_HROSE . '/a/hrose_sql_deploy.php');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $decoded = json_decode($result);
                $decodedArr = json_decode($result,true);
                curl_close($ch);

                if (isset($decoded->error)) {
                    if(LOG) {
                        error_log('api error '. $decoded->error);
                    }
                    $apiError = $decoded->error;
                    echo '<span class="text-danger">API error: '.$apiError.'</span>';
                } else if ($result->isError) {
                    echo '<span class="text-danger">';
                    print_r($result);
                    echo '</span>';
                } else {
                    $all = ($decodedArr['data'] == $hrose);

                    if ($all) {
                        if(LOG) {
                            error_log('hroses complete');
                        }
                        unlink($path . $datei);
                        unlink($path . 'hroses/' . $datei . '.hrose');
                    } else {
                        if(LOG) {
                            error_log('hroses incomplete');
                        }
                        file_put_contents($hroseFile, serialize($decodedArr['data']));
                    }
                }
            } else {
                if(ENABLE_DEPLOY_SYNC && LOG) {
                    error_log('not calling home or no hroses');
                }
            }
                ?>
                    </tbody>
                </table>
            </div>
                </div>
        </section>
        

        <?php }
    }
}
if (isset($_REQUEST['autoexec'])) {
    file_put_contents(SQL_ROOT . 'version.txt', HROSE_VERSION);
}

closedir($handle);
?>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12">
        <div id="form2" class="form-horizontal">
            <section class="panel">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="fa fa-caret-down"></a>
                    </div>

                    <h2 class="panel-title"><?php sss('SQL Execution')?></h2>

                    <p class="panel-subtitle">
                        <?php sss('Type the sql query and press execute button to run it.')?>.
                    </p>
                </header>
                <div class="panel-body">
                    <div class=" row form-group">
                        <div class="col-lg-12">
                            <label for="arbitary" class="col-lh-12 control-label"><?php sss('SQL query')?>:</label>
                        </div>
                    </div>
                    <div class="row form-group" id="arbitrary">
                        <div class="col-lg-12">
                            <textarea name="arbitrary" class="form-control" id=arbitrary_sql></textarea>
                        </div>
                    </div>
                </div>
                <footer class="panel-footer">
                    <button class="btn btn-primary" onclick="$('#arbitrary').load('<?=HTTP_HOST?>/a/exec_sql.php?arbitrary',{'sql': $('#arbitrary_sql').val()});">
                        <i class="fa fa-play"></i> <?php sss('Execute')?>
                    </button> 
                </footer>
            </section>
        </div>
    </div>
</div>
<script type="text/javascript">
    var elem_editors = $('.code-editor');
    var editors = [];
    elem_editors.map(function(index, item){
        $(item).attr("id","editor_"+index);
        var buildDom = require("ace/lib/dom").buildDom;
        var editor = ace.edit("editor_"+index);
        editor.setOptions({
            maxLines: Infinity
        });
        editor.setReadOnly(true);
        editor.setTheme("ace/theme/xcode");
        editor.session.setMode("ace/mode/sql");
        editors.push(editor);
        return item;
    });
    

</script>
<?php //exec_sql(\''.html($datei).'\','.$j.','.$i.')
require("inc/footer.inc.php");
if (isset($_REQUEST['cacheit'])) {
    $commandsContent = ob_get_contents();
    file_put_contents('chat.html', $commandsContent);
    ob_end_flush();
}
?>
<script type="text/javascript">
function exec_sql(file,fileCnt,i) {
    var url = '<?php echo HTTP_HOST?>a/exec_sql.php?f='+file+'&i='+i;
    $.get(url, function(data) {
        $('#'+fileCnt+'_'+i+'_r').html(data);
    });
}
</script>