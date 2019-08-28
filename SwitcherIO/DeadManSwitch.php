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
     * @param string $urlId
     * @param string $key
     */
    public function __construct($urlId, $key)
    {
        $this->urlId = $urlId;
        $this->key = $key;
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
        $c = curl_init('https://app.switcher.io/public-api/dead-man-switch/'.$this->urlId.'/'.$action);

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, ['key' => $this->key]);

        $response = curl_exec($c);

        if (!$response) {
            throw new SwitcherException('Curl error: '.curl_error($c));
        }

        curl_close($c);

        if ($response !== 'ok') {
            throw new SwitcherException($response);
        }
    }
}