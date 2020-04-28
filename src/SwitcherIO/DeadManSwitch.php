<?php

namespace SwitcherIO;

class DeadManSwitch
{
    const STATUS_TEST_ERROR = 'test-error';

    const STATUS_OK = 'ok';

    const STATUS_ERROR_CURL = 'curl-error';
    const STATUS_ERROR_BILL_UNPAID = 'bill-unpaid';
    const STATUS_ERROR_SWITCH_PAUSED = 'switch-paused';
    const STATUS_ERROR_START_PING_WITHOUT_RUNTIME = 'start-ping-without-runtime';
    const STATUS_ERROR_START_BEFORE_COMPLETE = 'start-before-complete';
    const STATUS_ERROR_COMPLETE_BEFORE_START = 'complete-before-start';
    const STATUS_ERROR_404 = 'not-found';
    const STATUS_ERROR_500 = 'server-error';

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
        //handle test urls
        switch ($this->urlId) {
            case 'test':
                return;
            
            case 'test-error':
                throw new SwitcherException('Test error', self::STATUS_TEST_ERROR);
        }

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
            } else if ($httpStatus >= 500) {
                sleep(pow(2, $i));
            }
        }

        if (!$response) {
            throw new SwitcherException('Curl error: '.curl_error($c), self::STATUS_ERROR_CURL);
        }

        curl_close($c);

        if ($httpStatus >= 500) {
            throw new SwitcherException('There was a server error on switcher.io.', self::STATUS_ERROR_500);
        }

        $responseDecoded = json_decode($response, true);

        if ($responseDecoded['status-code'] !== self::STATUS_OK) {
            throw new SwitcherException($responseDecoded['message'], $responseDecoded['status-code']);
        }
    }
}