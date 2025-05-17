<?php

$modul="chat";

require("../inc/req.php");

define('DEBUG_CHAT_EVENT_STREAM', true);
define('DEBUG_CHAT_EVENT_STREAM_EXTENDED', false);
if (!isset($_GET['ai'])) {
    die('ai not found');
}
if (!isset($_GET['id'])) {
    die('session id not found');
}

$allowedFunc = ['remember_something', 'fasse_zusammen', 'get_current_weather'];

$sql = "SELECT name, init_cmd, briefing, dupdate FROM ai WHERE ai_id=" . (int) $_REQUEST['ai'];
$res = mysqli_query($con, $sql) or die(mysqli_error($con));
$aiRow = mysqli_fetch_array($res);
if (!isset($aiRow['name'])) {
    die('ai ' . (int) $_REQUEST['ai'] . ' not found in chat-event-stream.');
}
$aiId = (int) $_REQUEST['ai'];
$aiStartMsg = $aiRow['init_cmd'];
$aiFunctions[] = $aiRow['functions'];
if (DEBUG_CHAT_EVENT_STREAM) {
    error_log('Using AI ' . $aiRow['name']);
}
if (file_exists(MODULE_ROOT . 'ai/functions/ai.' . $aiId . '.inc.php')) {
    if (DEBUG_CHAT_EVENT_STREAM) {
        error_log('Functions file found.');
    }
    include(MODULE_ROOT . 'ai/functions/ai.' . $aiId . '.inc.php');
}

// For Admins only
GRGR([1001,1003]);
$_ENV['OPENAI_API_KEY'] = OPENAI_API_KEY;
putenv('OPENAI_API_KEY='.OPENAI_API_KEY);

const ROLE = "role";
const CONTENT = "content";
const NAME = "name";
const USER = "user";
const SYS = "system";
const FUNC = "tool";
const ASSISTANT = "assistant";

//$client = \ArdaGnsrn\Ollama\Ollama::client('http://172.16.1.89:11434');

$chat_history_id = $_GET['chat_history_id'];
$id = $_GET['id'];

$dynBlocks['VERSION'] = date('Y.m.d.H.i', strtotime($aiRow['dupdate'])).'-'.date('Y.m.d.H.i') . '-' . $_SESSION['user_id'];
if ($aiRow['briefing']) $dynBlocks['BRIEFING'] = $aiRow['briefing'];

if (file_exists(MODULE_ROOT . 'ai/dyn_blocks/ai.' . $aiId . '.inc')) {
    if (DEBUG_CHAT_EVENT_STREAM) {
        error_log('Dyn Blocks file found.');
    }
    include(MODULE_ROOT . 'ai/dyn_blocks/ai.' . $aiId . '.inc');
}
if (isset($dynBlocks)) {
    foreach ($dynBlocks as $dynBlockKey => $dynBlock) {
        $aiStartMsg = str_replace('DYN_BLOCK_' . $dynBlockKey, $dynBlock, $aiStartMsg);
    }
}

if (DEBUG_CHAT_EVENT_STREAM) {
    error_log('aiStartMsg: ' . $aiStartMsg);
}
//die('stopi');
// Retrieve the data in ascending order by the id column
$hisSelect = 'SELECT * FROM chat_history WHERE session_id=\'' . mysqli_real_escape_string($con, $_REQUEST['id']) . '\' ORDER BY chat_history_id ASC';
if (DEBUG_CHAT_EVENT_STREAM) {
    error_log($hisSelect);
}

$res = mysqli_query($con, $hisSelect) or die(mysqli_error($con));
//$history[] = [ROLE => SYS, CONTENT => $aiStartMsg];
while($row=mysqli_fetch_array($res)) {
    if ($row['human']) $history[] = [ROLE => USER, CONTENT => $row['human']];
    if ($row['ai']) $history[] = [ROLE => ASSISTANT, CONTENT => $row['ai']];
}
// Prepare a SELECT statement to retrieve the 'human' field of the row with ID = $chat_history_id
/* doppelt?
$stmt = 'SELECT human FROM chat_history WHERE chat_history_id = ' . (int) $chat_history_id;

if (DEBUG_CHAT_EVENT_STREAM) {
    error_log($stmt);
}
// Execute the SELECT statement and retrieve the 'human' field
$res = mysqli_query($con, $stmt) or die(mysqli_error($con));
$row = mysqli_fetch_array($res);
$msg = $row['human'];

$history[] = [ROLE => USER, CONTENT => $msg];
*/

$fun2 = json_decode('[
        {
            "name": "merke_dir",
            "description": "Merke dir eine Information",
            "parameters": {
                "type": "object",
                "properties": {
                    "info": {
                        "type": "string",
                        "description": "Eine beliebige Information als Satz"
                    },
                    "unit": {"type": "string"}
                },
                "required": ["info"]
            }
        }
    ]');

$func['name'] = 'merke_dir';
$func['description'] = 'Merke dir eine Information';
$func['parameters']['type'] = 'object';
$func['parameters']['properties']['info']['type'] = 'string';
$func['parameters']['properties']['info']['description'] = 'Eine beliebige Information als Satz';
$func['parameters']['properties']['unit']['type'] = 'string';
$func['parameters']['required'] = ['info'];

$testFunctions[] = $func;

$tools =[];

$rememberTool = [
    'type' => 'function',
    'function' => [
        'name' => 'remember_something',
        'description' => 'Remember an information',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'info' => [
                    'type' => 'string',
                    'description' => 'A piece of information to remember',
                ]
            ],
            'required' => ['info'],
        ],
    ],
];

$fSql = "SELECT * FROM function WHERE ai=" . (int) $_REQUEST['ai'];
$fRes = mysqli_query($con, $fSql) or die(mysqli_error($con));
while ($fRow = mysqli_fetch_array($fRes)) {
    $func = [];
    $func['type'] = 'function';
    $func['function']['name'] = $fRow['name'];
    $func['function']['description'] = $fRow['description'];
    $func['function']['parameters']['type'] = 'object';
    $fpSql = "SELECT * FROM function_param WHERE function=" . $fRow['function_id'];
    $fpRes = mysqli_query($con, $fpSql) or die(mysqli_error($con));
    while ($fpRow = mysqli_fetch_array($fpRes)) {
        $func['function']['parameters']['type'] = 'object';
        $func['function']['parameters']['properties'][$fpRow['name']][$fpRow['property_key']] = ('enum' == $fpRow['property_key']) ? explode(',', $fpRow['property_value']) : $fpRow['property_value'];
    }
    $func['function']['parameters']['required'] = explode(',', $fRow['required']);
    if (DEBUG_CHAT_EVENT_STREAM_EXTENDED)  {
        error_log($fRow['name'] . ':' . print_r($func,true));
    }
    $tools[] = $func;
}

if (DEBUG_CHAT_EVENT_STREAM) {
    //error_log(print_r($tools,true));
}

/*
$func2['name'] = 'fasse_zusammen';
$func2['description'] = 'Fasse eine Konversation zusammen';
$func2['parameters']['type'] = 'object';
$func2['parameters']['properties']['scope']['enum'] = ["customer_name", "contact_lastname", "contact fullname", "project_name", "email"];
$func2['parameters']['properties']['scope']['description'] = "Für wen oder was die Konversation zusammengefasst werden soll: Ein Kundenname, ein Kontakt-Nachname, ein Kontakt-Name, ein Projektname, ein Kontaktname oder eine E-Mail-Adresse";
$func2['parameters']['properties']['name']['type'] = 'string';
$func2['parameters']['properties']['name']['description'] = 'Der Kundenname/Kontakt-Nachname/Kontakt-Name/Projektname/E-Mail-Adresse';
$func2['parameters']['properties']['since']['type'] = 'integer';
$func2['parameters']['properties']['how']['description'] = 'Wie die Konversation zusammengefasst werden soll, z.B. kurz oder ausführlich';
$func2['parameters']['properties']['how']['type'] = 'string';
$func2['parameters']['properties']['since']['description'] = 'Die Anzahl Tage seit wann die Konversation zusammengefasst werden soll';
$func2['parameters']['required'] = ['scope','name'];

$testFunctions[] = $func2;
*/

/*
$response = $client->chat()->create([
    'model' => 'mistral',
    'messages' => $history,
    'tools' => [
        [
            'type' => 'function',
            'function' => [
                'name' => 'get_current_weather',
                'description' => 'Get the current weather',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'location' => [
                            'type' => 'string',
                            'description' => 'The location to get the weather for, e.g. San Francisco, CA',
                        ],
                        'format' => [
                            'type' => 'string',
                            'description' => 'The location to get the weather for, e.g. San Francisco, CA',
                            'enum' => ['celsius', 'fahrenheit']
                        ],
                    ],
                    'required' => ['location', 'format'],
                ],
            ],
        ]
    ]
]);

error_log('hier');
$toolCall = $response->message->toolCalls[0];

error_log($toolCall->function->name); // 'get_current_weather'
error_log($toolCall->function->arguments); // ['location' => 'Paris', 'format' => 'celsius']

$resp = $response->toArray();

error_log(print_r($resp,1));
exit;
*/

$opts = [
    'model' => 'gpt-4',
    //'model' => 'gpt-3.5-turbo',
    'messages' => $messages,
    'functions' => $testFunctions,
    'temperature' => 1,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
    'stream' => true
];

if ($aiFunctions) {
    //$opts['functions'] = $aiFunctions;
}

//error_log(print_r($opts,true));

header('Content-type: text/event-stream');
header('Cache-Control: no-cache');
$txt = "";
unset($_SESSION['func']);
unset($_SESSION['aichat']);

// noot used:
function checkValidData($data) {
    $valid = false;
    if (strpos($data, '{') !== false) {
        $valid = true;
    }
    return $valid;
}

function isJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/*
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

foreach ($responses as $response) {
    $responseContent = $response->message->content;
    $data = '{"id":"chatcmpl-BAMUsxFNYTzQNwmom9Whja4n0kNTp","object":"chat.completion.chunk","created":1741809458,"model":"mistral","service_tier":"default","system_fingerprint":null,"choices":[{"index":0,"delta":{"content":"' . $responseContent . '"},"logprobs":null,"finish_reason":null}]}';
    error_log('sending: ' . $data);
    echo "data: " . $data . "\n\n";
    ob_flush();
    flush();
}
echo "data: [DONE]\n\n";
ob_flush();
flush();
exit;
*/




header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

$history2 = [
    ['role' => 'user', 'content' => 'What is the weather today in Paris?'],
];

$weatherTool = [
    'type' => 'function',
    'function' => [
        'name' => 'get_current_weather',
        'description' => 'Get the current weather',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'location' => [
                    'type' => 'string',
                    'description' => 'The location to get the weather for, e.g. San Francisco, CA',
                ],
                'format' => [
                    'type' => 'string',
                    'description' => 'The location to get the weather for, e.g. San Francisco, CA',
                    'enum' => ['celsius', 'fahrenheit']
                ],
            ],
            'required' => ['location', 'format'],
        ],
    ],
];

$rememberTool = [
    'type' => 'function',
    'function' => [
        'name' => 'remember_something',
        'description' => 'Remember an information',
        'parameters' => [
            'type' => 'object',
            'properties' => [
                'info' => [
                    'type' => 'string',
                    'description' => 'A piece of information to remember',
                ]
            ],
            'required' => ['info'],
        ],
    ],
];

//$tools = [];
//$tools[] = $weatherTool;

$opts = [
    'model' => 'mistral',
    'messages' => $history,
    'tools' => $tools,
    "stream" => true
];

if (DEBUG_CHAT_EVENT_STREAM) {
    error_log(print_r($opts,true));
}

$ch = curl_init("http://172.16.1.89:11434/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($opts));
/*
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
    error_log($data);
    echo "data: " . trim($data) . "\n\n";
    flush();
    return strlen($data);
});*/


curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$txt, &$history, $allowedFunc) {
    try {
        $func = '';
        $dataStart = false;
        if ($obj = json_decode($data) and ($obj->error->message != "" || $obj->error != "")) {
            $theError = $obj->error->message ?: $obj->error;
            $txt .= "data: {\"error\":\"" . $theError . "\"}\n\n";
            error_log('txt line ' . __LINE__ . ': ' . $txt);
            echo $txt;
        } else {
            if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                error_log('incoming: ' . $data);
            }

            if (strpos($data, 'data: ') !== 0) { /// WTFFFF
                $dataStart = true;
                if (DEBUG_CHAT_EVENT_STREAM) {
                    error_log('data start recognized');
                }
                //unset($_SESSION['aichat']['uncompleteChunk']);
                //unset($_SESSION['aichat']['uncompleteData']);
            }
            if (isset($_SESSION['aichat']['uncompleteData']) && $_SESSION['aichat']['uncompleteData']) {
                if (!$dataStart) {
                    $completedData = $_SESSION['aichat']['uncompleteData'] . $data;
                    //if (DEBUG_CHAT_EVENT_STREAM) {
                    error_log('data after prepending uncomplete last data: ' . $completedData);
                    //}
                    unset($_SESSION['aichat']['uncompleteData']);
                } else {
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log('would prepend, but next data seems okay: ' . $data);
                        //unset($_SESSION['aichat']['uncompleteData']);
                    }
                }
            }
            if ($completedData) {
                // skip
                //$data = $completedData;
            }
            $clean = str_replace("data: ", "", $data);
            if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                error_log('clean data: ' . $clean);
            }
            $split = explode("\n", $clean);
            foreach ($split as $dataChunk) {
                if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                    error_log('dataChunk: ' . $dataChunk);
                }
                if ($dataChunk) {
                    if (isset($_SESSION['aichat']['uncompleteChunk']) && $_SESSION['aichat']['uncompleteChunk']) {
                        if (strpos($dataChunk, '{') !== 0) {
                            $dataChunkCompleted = $_SESSION['aichat']['uncompleteChunk'] . $dataChunk;
                            //if (DEBUG_CHAT_EVENT_STREAM) {
                            error_log('data after prepending uncomplete last data chunk: ' . $dataChunkCompleted);
                            //}
                            $correctedData = "data: " . $dataChunkCompleted . "\n\n";
                        } else {
                            if (DEBUG_CHAT_EVENT_STREAM) {
                                error_log('would prepend, but next chunk seem okay: ' . $dataChunk);
                                //unset($_SESSION['aichat']['uncompleteChunk']);
                            }
                        }
                    }
                    if ($dataChunkCompleted) {
                        $dataChunk = $dataChunkCompleted;
                    }
                    if (strpos($dataChunk, '[DONE]') === false) {
                        $arr = json_decode($dataChunk, true);
                        if (!$arr) {
                            if (DEBUG_CHAT_EVENT_STREAM) {
                                error_log('data/chunk uncomplete: ' . $dataChunk);
                            }
                            $_SESSION['aichat']['uncompleteChunk'] = $dataChunk;
                            $_SESSION['aichat']['uncompleteData'] = $data;
                            continue;
                        }
                    }
                    unset($_SESSION['aichat']['uncompleteChunk']);
                    unset($_SESSION['aichat']['uncompleteData']);
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log('$arr: ' . print_r($arr,true));
                    }
                    $arr = json_decode($dataChunk, true);
                    if ($data != "data: [DONE]\n\n" and isset($arr["choices"][0]["delta"]["content"])) {
                        $txt .= $arr["choices"][0]["delta"]["content"];
                        error_log('txt line ' . __LINE__.': '. $txt);
                    }
                    if (isset($arr["choices"][0]["delta"]["tool_calls"][0])) {
                        if (DEBUG_CHAT_EVENT_STREAM) {
                            error_log('Function name found: ' . $arr["choices"][0]["delta"]["tool_calls"][0]["function"]["name"]);
                        }
                        $_SESSION['func']['name'] = $arr["choices"][0]["delta"]["tool_calls"][0]["function"]["name"];
                        //error_log($func);
                    }
                    if (isset($arr["choices"][0]["delta"]["tool_calls"][0]["function"]["arguments"])) {
                        if (DEBUG_CHAT_EVENT_STREAM) {
                            error_log('Function arguments found: ' . $arr["choices"][0]["delta"]["tool_calls"][0]["function"]["arguments"]);
                        }
                        $_SESSION['func']['arguments'] = $arr["choices"][0]["delta"]["tool_calls"][0]["function"]["arguments"];
                        //error_log($func);
                    }
                }
            }
            if (strpos($data, "data: [DONE]") !== false) {
                if ($_SESSION['func']) {
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log('Function: ' . $_SESSION['func']['name']);
                        error_log('Function: ' . $_SESSION['func']['arguments']);
                    }
                    if (isJson($_SESSION['func']['arguments'])) {
                        $funcArgs = json_decode($_SESSION['func']['arguments'], true);
                    } else {
                        $eInfo = explode(":", $_SESSION['func']['arguments']);
                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('$eInfo[0]:' . $eInfo[0]);
                            error_log('$eInfo[1]:' . $eInfo[1]);
                        }
                        $key = trim($eInfo[0], '{');
                        $value = trim($eInfo[1], '}');
                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('$eInfo[0]:' . $key);
                            error_log('$eInfo[1]:' . $value);
                        }
                        $key = trim($key);
                        $value = trim($value);
                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('$eInfo[0]:' . $key);
                            error_log('$eInfo[1]:' . $value);
                        }
                        $key = trim($key, '{');
                        $value = trim($value, '}');
                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('$eInfo[0]:' . $key);
                            error_log('$eInfo[1]:' . $value);
                        }
                        $key = trim($key, '"');
                        $value = trim($value, '"');
                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('$eInfo[0]:' . $key);
                            error_log('$eInfo[1]:' . $value);
                        }
                        $funcArgs[$key] = $value;
                    }
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log(print_r($funcArgs, true));
                    }
                    if (in_array($_SESSION['func']['name'], $allowedFunc) && function_exists($_SESSION['func']['name'])) {
                        $func = $_SESSION['func']['name'];
                        if (!$func) {
                            $funcResult = 'Func name missing';
                        } else {
                            $funcResult = $func($funcArgs);
                        }
                    } else {
                        $funcResult = 'Sorry, I don\'t know how to do that yet.';
                    }
                    $history[] = [ROLE => FUNC, NAME => $_SESSION['func']['name'], CONTENT => $funcResult];

                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log(print_r($history, true));
                    }
                    $opts['messages'] = $history;
                    unset($opts['functions']);
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log(print_r($opts, true));
                    }
                    $ch2 = curl_init("http://172.16.1.89:11434/v1/chat/completions");
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                    curl_setopt($ch2, CURLOPT_POST, true);
                    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([
                        "model" => "mistral",
                        "messages" => $history,
                        "stream" => true
                    ]));
                    curl_setopt($ch2, CURLOPT_WRITEFUNCTION, function ($ch2, $data2) use (&$txt) {

                        if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                            error_log('func incoming ' . $data2);
                        }
                        if ($obj = json_decode($data2) and ($obj->error->message != "" || $obj->error != "")) {
                            error_log('Fehler in function loop');
                            $theError = $obj->error->message ?: $obj->error;
                            $txt .= "data: {\"error\":\"" . $theError . "\"}\n\n";
                            if (DEBUG_CHAT_EVENT_STREAM) {
                                error_log('txt line ' . __LINE__ . ': ' . $txt);
                            }
                            echo $txt;
                        } else {
                            $clean2 = str_replace("data: ", "", $data2);
                            $split2 = explode("\n", $clean2);
                            $correctedData2 = false;
                            foreach ($split2 as $dataChunk2) {
                                if ($dataChunk2) {
                                    if (DEBUG_CHAT_EVENT_STREAM) {
                                        error_log('dataChunk2: ' . $dataChunk2);
                                    }
                                    if (isset($_SESSION['aichat']['uncompleteChunk2']) && $_SESSION['aichat']['uncompleteChunk2']) {
                                        if (strpos($dataChunk2, '{') !== 0) {
                                            $dataChunkCompleted2 = $_SESSION['aichat']['uncompleteChunk2'] . $dataChunk2;
                                            //if (DEBUG_CHAT_EVENT_STREAM) {
                                            error_log('data after prepending uncomplete last data2 chunk: ' . $dataChunkCompleted2);
                                            //}
                                            $correctedData2 = "data: " . $dataChunkCompleted2 . "\n\n";
                                        } else {
                                            if (DEBUG_CHAT_EVENT_STREAM) {
                                                error_log('would prepend, but next chunk2 seem okay: ' . $dataChunk2);
                                                //unset($_SESSION['aichat']['uncompleteChunk2']);
                                            }
                                        }
                                    }
                                    if ($dataChunkCompleted2) {
                                        $dataChunk2 = $dataChunkCompleted2;
                                    }
                                    if ($data2 != "data: [DONE]\n\n") {
                                        $arr2 = json_decode($dataChunk2, true);
                                        if (!$arr2) {
                                            if (DEBUG_CHAT_EVENT_STREAM) {
                                                error_log('data/chunk2 uncomplete: ' . $dataChunk2);
                                            }
                                            $_SESSION['aichat']['uncompleteChunk2'] = $dataChunk2;
                                            continue;
                                        }
                                        unset($_SESSION['aichat']['uncompleteChunk2']);
                                        if (isset($arr2["choices"][0]["delta"]["content"])) {
                                            $txt .= $arr2["choices"][0]["delta"]["content"];
                                            if (DEBUG_CHAT_EVENT_STREAM) {
                                                error_log('txt line ' . __LINE__.': '. $txt);
                                            }
                                        }
                                    }
                                }
                            }
                            if ($_SESSION['aichat']['uncompleteChunk2']) {
                                if (DEBUG_CHAT_EVENT_STREAM) {
                                    error_log('not sending uncomplete chunk2: ' . $_SESSION['aichat']['uncompleteChunk2']);
                                    if ($GLOBALS['firstPrepend2']) {
                                        $dataPos = strpos($data2, 'data: ');
                                        // cut off last data chunk
                                        $data2 = substr($data2, 0, $dataPos);
                                    }
                                }
                            }
                            if ($correctedData2) {
                                if ($GLOBALS['firstPrepend2']) {
                                    error_log('sending corrected data2');
                                    $data2 = $correctedData2 . $data2;
                                    unset ($_SESSION['aichat']['prependData2']);
                                } else {
                                    $GLOBALS['firstPrepend2'] = 1;
                                }
                            }
                            if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                                error_log('sending data2: ' . $data2);
                            }
                            echo $data2;
                            echo PHP_EOL;
                            ob_flush();
                            flush();
                            return strlen($data2);
                        }
                    });

                    curl_exec($ch2);
                    curl_close($ch2);
                } else {
                    if (DEBUG_CHAT_EVENT_STREAM) {
                        error_log('sending data when done: ' . $data);
                    }
                    echo $data;
                }
            } else if (!$_SESSION['func']) {
                //error_log('sending data when not done and no functions');
                echo $data;
                if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                    error_log('sending: ' . $data);
                }
                /* let's skip these corrections, are causing interruption

                if ($_SESSION['aichat']['uncompleteData']) {
                    error_log('not sending uncomplete chunk');
                    error_log($_SESSION['aichat']['uncompleteData']);
                    if ($GLOBALS['firstPrepend']) {
                        $dataPos = strpos($data, 'data: ');
                        // cut off last data chunk
                        $data = substr($data, 0, $dataPos);
                    }
                }

                if ($correctedData) {
                    if ($GLOBALS['firstPrepend']) {
                        error_log('sending corrected data');
                        $data2 = $correctedData . $data;
                        unset ($_SESSION['aichat']['prependData']);
                    } else {
                        $GLOBALS['firstPrepend'] = 1;
                    }
                }
                if (DEBUG_CHAT_EVENT_STREAM_EXTENDED) {
                    error_log('sending: ' . $data);
                }
                echo $data;
                */
            }
        }
        echo PHP_EOL;
        ob_flush();
        flush();
        return strlen($data);
    } catch (Exception $e) {
        error_log('Exception catched '  . $e->getMessage());
    }
});
curl_exec($ch);
curl_close($ch);

$isAction = strpos($txt, '[URL]');
if ($isAction) {
    error_log('Action found: '.$txt);
    $action = substr($txt, ($isAction+6));
    $txt = substr($txt, 0, $isAction);
    error_log('txt line ' . __LINE__.': '. $txt);
}

// Prepare the UPDATE statement
$stmt ="UPDATE chat_history SET ai = '" . mysqli_real_escape_string($con, $txt) . "', action='" . mysqli_real_escape_string($con, $action) . "' WHERE chat_history_id = " . (int) $chat_history_id;
mysqli_query($con, $stmt) or die(mysqli_error($con));
