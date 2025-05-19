<?php

namespace app\hrose;

use app\hrose\API;
use Augmented;

class APIHrose extends API
{
    protected $Hrose;

    public function __construct($request, $origin)
    {
        parent::__construct($request);
        // Abstracted out for example
        $Hrose = new Augmented();

        if (!array_key_exists('serial', $this->request)) {
            throw new Exception('No serial provided');
        } else if (!$HroseId = $Hrose->checkSerial($this->request['serial'])) {
            throw new Exception('Invalid API Key');
        }

        $Hrose->load();
        $this->Hrose = $Hrose;
    }

    /**
     * Deploy Endpoint
     */
    public function deploySql($_VALID)
    {
        $string = $_VALID['serial'] . $_VALID['filename'];
        if (!$this->Hrose->checkSignature($string, $_VALID['signature'])) {
            throw new Exception('Invalid Signature');
        } else {
            return $this->Hrose->deploy($_VALID);
        }
    }
}