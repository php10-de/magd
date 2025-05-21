<?php
use Orhanerday\OpenAi\OpenAi;

define('DEBUG_OPENAI', false);

$_ENV['OPENAI_API_KEY'] = OPENAI_API_KEY;
putenv('OPENAI_API_KEY='.OPENAI_API_KEY);

const ROLE = "role";
const CONTENT = "content";
const NAME = "name";
const USER = "user";
const SYS = "system";
const FUNC = "function";
const ASSISTANT = "assistant";


function openai_chat($history) {
    $open_ai_key = getenv('OPENAI_API_KEY');
    $open_ai = new OpenAi($open_ai_key);

    $opts = [
        'model' => 'gpt-4',
        //'model' => 'gpt-3.5-turbo',
        'messages' => $history,
        'temperature' => 0.78,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
        'stream' => false
    ];

    $aiResponse = json_decode($open_ai->chat($opts));
    if (DEBUG_1_FUNC) {
        error_log(print_r($aiResponse, true));
    }

    if (isset($aiResponse->error)) {
        if (DEBUG_1_FUNC) {
            error_log('error in aiResponse: ' . $aiResponse->error);
        }
        return 'error in aiResponse: ' . $aiResponse->error;
    }

    if (isset($aiResponse->error->message)) {
        if (DEBUG_1_FUNC) {
            error_log('error in aiResponse: ' . $aiResponse->error->message);
        }
        return 'error in aiResponse: ' . $aiResponse->error->message;
    }

    if (!isset($aiResponse->choices)) {
        if (DEBUG_1_FUNC) {
            error_log('choices o missing in aiResponse');
        }
        return 'choices missing in aiResponse';
    }
    if (!isset($aiResponse->choices[0])) {
        if (DEBUG_1_FUNC) {
            error_log('message a missing in aiResponse');
        }
        return 'message missing in aiResponse';
    }
    if (!isset($aiResponse->choices[0]->message)) {
        if (DEBUG_1_FUNC) {
            error_log('message missing in aiResponse');
        }
        return 'message missing in aiResponse';
    }
    $responseContent = $aiResponse->choices[0]->message->content;
    return $responseContent;
}