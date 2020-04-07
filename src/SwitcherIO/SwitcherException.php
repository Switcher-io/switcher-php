<?php

namespace SwitcherIO;

class SwitcherException extends \Exception
{
    /**
     * status code from switcher.io - these will never change
     *
     * @var string
     */
    private $statusCode;

    /**
     * Undocumented function
     *
     * @param string $message
     * @param string $statusCode
     * @param integer $code
     */
    public function __construct($message, $statusCode, $code = 0)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code);
    }

    /**
     * Returns switch status code. These codes will never change, so use them instead of parsing exception messages
     * Will match one of the status constants of \SwitcherIO\DeadManSwitch
     *
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}