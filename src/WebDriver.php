<?php

namespace Phizzl\Codeception\Modules;


use Codeception\TestInterface;
use Phizzl\Browserstack\Api\AutomateApiClient;

class WebDriver extends \Codeception\Module\WebDriver
{
    /**
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        parent::_failed($test, $fail);
        if(isset($this->capabilities['browserstack.user'])){
            $this->getBrowserstackApi()->markSessionFailed($this->webDriver->getSessionID());
        }
    }

    /**
     * @return AutomateApiClient
     */
    private function getBrowserstackApi()
    {
        $api = new AutomateApiClient();
        $api->setUsername($this->capabilities['browserstack.user']);
        $api->setKey($this->capabilities['browserstack.key']);
        return $api;
    }
}