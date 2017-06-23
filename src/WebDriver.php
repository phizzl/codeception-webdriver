<?php

namespace Phizzl\Codeception\Modules;


use Codeception\TestInterface;
use Phizzl\Browserstack\Api\AutomateApiClient;

class WebDriver extends \Codeception\Module\WebDriver
{
    /**
     * @var bool
     */
    public static $failed = false;

    /**
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        parent::_failed($test, $fail);

        if($this->useBrowserStackHub()){
            static::$failed = true;
        }
    }

    /**
     * @inheritdoc
     */
    public function _afterSuite()
    {
        parent::_afterSuite();
        if($this->useBrowserStackHub()
            && static::$failed === false){
            $this->getBrowserstackApi()->markSessionFailed($this->webDriver->getSessionID());
        }
    }

    /**
     * @return bool
     */
    private function useBrowserStackHub()
    {
        return isset($this->capabilities['browserstack.user']);
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