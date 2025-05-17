<?php

define('DEBUG_1_FUNC', false);

function remember_something(array $arguments) {
    global $con;
    try {
        if (DEBUG_1_FUNC) {
            error_log(print_r($arguments, true));
        }
        if (!isset($arguments['info'])) {
            if (DEBUG_1_FUNC) {
                error_log('info argument missing in merke_dir()');
            }
            return 'info argument missing in merke_dir()';
        }
        $dbInfo = '\n' . mysqli_real_escape_string($con, $arguments['info']) . '.' . '\n';
        if (DEBUG_1_FUNC) {
            error_log('dbInfo:' . $dbInfo);
        }
        $msg = '';

        $sql = "UPDATE ai SET briefing = CONCAT(briefing, '" . $dbInfo . "') WHERE ai_id=1";
        if (DEBUG_1_FUNC) {
            error_log($sql);
        }
        $res = mysqli_query($con, $sql);
        if (!$res) {
            error_log(mysqli_error($con));
            return "Speichern in Datenbank fehlgeschlagen\n";
        }

        mail('fraunholz@mac.com', 'MAGD merkt sich ', $arguments['info']);
        $msg = "Die Information wurde gespeichert";
    } catch (Exception $e) {
        error_log('merke_dir fehlgeschlagen: ' . $e->getMessage());
    }
    if ($msg) {
        return $msg;
    } else {
        return "Speichern fehlgeschlagen\n";
    }
}


function fasse_zusammen(array $arguments) {
    global $con;
    try {
        if (DEBUG_1_FUNC) {
            error_log(print_r($arguments, true));
        }
        if (!isset($arguments['scope'])) {
            if (DEBUG_1_FUNC) {
                error_log('scope argument missing in fasse_zusammen()');
            }
            return 'scope argument missing in fasse_zusammen()';
        }
        if (!isset($arguments['name'])) {
            if (DEBUG_1_FUNC) {
                error_log('name argument missing in fasse_zusammen()');
            }
            return 'name argument missing in fasse_zusammen()';
        }

        if ('email' === $arguments['scope']) {
            $sql = "SELECT datum, text_content FROM inbox WHERE from_address = '" . mysqli_real_escape_string($con, $arguments['name']) . "'";
        } else if('company_name' === $arguments['scope']) {
            $sql = "SELECT datum, text_content FROM inbox WHERE company = (SELECT company_id FROM company WHERE name LIKE '%" . mysqli_real_escape_string($con, $arguments['name']) . "%')";
        } else {
            if (DEBUG_1_FUNC) {
                error_log('scope argument not supported in fasse_zusammen()');
            }
            return 'scope argument not supported in fasse_zusammen()';
        }
        if ($arguments['since']) {
            $sql .= " AND datum >= DATE_SUB(NOW(), INTERVAL " . $arguments['since'] . " DAY)";
        }
        if (DEBUG_1_FUNC) {
            error_log($sql);
        }
        $res = mysqli_query($con, $sql);
        if (!$res) {
            error_log(mysqli_error($con));
            return "Keine Konversationen gefunden\n";
        }

        require_once 'openai.inc.php';

        $aiStartMsg = 'Das aktuelle Datum ist ' . date('d.m.Y') . '. Gib eine Zusammenfassung der folgenden Konversationen:';
        $history[] = [ROLE => SYS, CONTENT => $aiStartMsg];
        while($row = mysqli_fetch_array($res)) {
            $history[] = [ROLE => USER, CONTENT => $row['datum'] . ': ' . $row['text_content']];
        }
        $command = "Fasse die Konversationen " . (($arguments['how']) ? $arguments['how'] . " " : "") . "zusammen.";
        $history[] = [ROLE => USER, CONTENT => $command];

        if (DEBUG_1_FUNC)
            error_log(print_r($history, true));

        $summary = openai_chat($history);

        if (DEBUG_1_FUNC) {
            error_log('summary: ' . $summary);
        }

        $msg = $summary;
    } catch (Exception $e) {
        error_log('fasse_zusammen fehlgeschlagen: ' . $e->getMessage());
    }
    if ($msg) {
        return $msg;
    } else {
        return "Zusammenfassung konnte nicht erstellt werden\n";
    }
}

function get_current_weather(array $arguments) {
    global $con;
    try {
        if (DEBUG_1_FUNC) {
            error_log(print_r($arguments, true));
        }
        if (!isset($arguments['location'])) {
            if (DEBUG_1_FUNC) {
                error_log('location argument missing in get_current_weather()');
            }
            return 'location argument missing in get_current_weather()';
        }
        return 'It will be very hot in Miami today';
    } catch (Exception $e) {
        error_log('get_current_weather fehlgeschlagen: ' . $e->getMessage());
    }
    if ($msg) {
        return $msg;
    } else {
        return "Wetter konnte nicht gefunden werden\n";
    }
}

