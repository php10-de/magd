<?php

namespace app\hrose;

use app\hrose\API;
use Augmented;
use Calendar;
use MAGD;
use School;
use Test;

class APIv1 extends API
{
    protected $Hrose;

    protected $allowedAPI = [
        'augmented',
        'magd',
        'calendar',
        'test',
        'school',
        'newsletter'];

    public function __construct($request, $origin)
    {
        define('API_CALL', true);
        parent::__construct($request);
        echo parent::processAPI();
    }

    public function magd()
    {
        require("../../app/hrose/MAGD.php");
        $API = new MAGD();

        if (!isset($_SESSION['logedin']) && !array_key_exists('api_key', $this->request)) {
            return ['meta' => ['status' => 'error', 'error_message' => 'No API key provided']];
        } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
            return ['meta' => ['status' => 'error', 'error_message' => 'Invalid API Key']];
        }

        try {
            $response = $API->response(1);
            $return = ['meta' => ['status' => 'ok']];
            if (is_array($response)) {
                foreach ($response as $key => $value) {
                    if ('pagination' === $key || 'data' === $key) {
                        $return[$key] = $value;
                    } else {
                        $return['meta'][$key] = $value;
                    }
                }
            }
            return $return;
        } catch (Exception $e) {
            $status = $API->errorLevel ?: 'error';
            if ('notice' == $status) {
                return ['meta' => ['status' => $status, 'message' => $e->getMessage()]];
            } else {
                return ['meta' => ['status' => $status, 'error_message' => $e->getMessage()]];
            }
        }
    }

    public function test()
    {
        require("../../app/hrose/Test.php");
        $API = new Test();

        try {
            if (!array_key_exists('api_key', $this->request)) {
                throw new Exception('No API key provided');
            } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
                throw new Exception('Invalid API Key');
            }
            $response = $API->response(1);
            return ['status' => 'ok', 'response' => $response];
        } catch (Exception $e) {
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }

    public function augmented()
    {
        require("../../app/hrose/Augmented.php");
        $API = new Augmented();

        try {
            if (!array_key_exists('api_key', $this->request)) {
                throw new Exception('No API key provided');
            } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
                throw new Exception('Invalid API Key');
            }
            $response = $API->response();
            return ['status' => 'ok', 'response' => $response];
        } catch (Exception $e) {
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }

    public function calendar()
    {
        require("../../app/hrose/Calendar.php");
        $API = new Calendar();

        /*if (!array_key_exists('api_key', $this->request)) {
            throw new Exception('No API key provided');
        } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
            throw new Exception('Invalid API Key');
        }*/

        try {
            $response = $API->response(1);
            return ['status' => 'ok', 'response' => $response];
        } catch (Exception $e) {
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }

    public function newsletter()
    {
        require("../../app/hrose/Newsletter.php");
        $API = new Calendar();

        /*if (!array_key_exists('api_key', $this->request)) {
            throw new Exception('No API key provided');
        } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
            throw new Exception('Invalid API Key');
        }*/

        try {
            $response = $API->response(1);
            return ['status' => 'ok', 'response' => $response];
        } catch (Exception $e) {
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }

    public function school()
    {
        require("../../app/hrose/School.php");
        $API = new School();

        /*if (!array_key_exists('api_key', $this->request)) {
            throw new Exception('No API key provided');
        } else if (!$UserId = $API->checkSerial($this->request['api_key'])) {
            throw new Exception('Invalid API Key');
        }*/

        try {
            $response = $API->response(1);
            return ['status' => 'ok', 'response' => $response];
        } catch (Exception $e) {
            return ['status' => 'error', 'response' => $e->getMessage()];
        }
    }
}