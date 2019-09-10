<?php

namespace SwitcherIO;

class DeadManSwitch
{
    /**
     * Key to post to switch url
     *
     * @var string
     */
    private $key;

    /**
     * Switch identifier in switch url, e.g. abc123 in https://dmsr.io/abc123
     *
     * @var string
     */
    private $urlId;

    /**
     * number of times to retry failed api request
     *
     * @var integer
     */
    private $retry;

    /**
     * curl timeouts
     *
     * @var integer
     */
    private $timeout;

    /**
     * @param string $urlId
     * @param string $key
     * @param integer $retry
     * @param integer $timeout
     */
    public function __construct($urlId, $key, $retry = 3, $timeout = 2)
    {
        $this->urlId = $urlId;
        $this->key = $key;
        $this->retry = $retry;
        $this->timeout = $timeout;
    }

    /**
     * Ping the switch's /start endpoint
     * Throws exception on error
     *
     * @throws SwitcherException
     * @return void
     */
    public function start()
    {
        $this->doCall('start');
    }

    /**
     * Ping the switch's /complete endpoint
     * Throws exception on error
     *
     * @throws SwitcherException
     * @return void
     */
    public function complete()
    {
        $this->doCall('complete');
    }

    /**
     * Ping the switch's /pause endpoint
     * Throws exception on error
     *
     * @throws SwitcherException
     * @return void
     */
    public function pause()
    {
        $this->doCall('pause');
    }

    /**
     * @param string $action
     * @throws SwitcherException
     * @return void
     */
    private function doCall($action)
    {
        $c = curl_init('https://dmsr.io/'.$this->urlId.'/'.$action);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->timeout); 
        curl_setopt($c, CURLOPT_TIMEOUT, $this->timeout);

        curl_setopt($c, CURLOPT_POSTFIELDS, ['key' => $this->key]);

        for ($i = 0; $i < $this->retry; $i++) {
            $response = curl_exec($c);
            $httpStatus = curl_getinfo($c, CURLINFO_RESPONSE_CODE);

            //stop retry if curl gets a response and http status is not 5xx
            if ($response && $httpStatus < 500) {
                break;
            }
        }

        if (!$response) {
            throw new SwitcherException('Curl error: '.curl_error($c));
        }

        curl_close($c);

        if ($response !== 'ok') {
            throw new SwitcherException($response);
        }
    }
}