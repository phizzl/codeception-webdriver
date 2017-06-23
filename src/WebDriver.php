<?php

namespace Phizzl\Codeception\Modules;

use Codeception\TestInterface;
use Phizzl\Browserstack\Api\AutomateApiClient;

class WebDriver extends \Codeception\Module\WebDriver
{
    /**
     * @var array
     */
    public static $sessionId = [];

    /**
     * @param TestInterface $test
     * @param \Exception $fail
     */
    public function _failed(TestInterface $test, $fail)
    {
        parent::_failed($test, $fail);
        static::$sessionId[$this->webDriver->getSessionID()] = $this->webDriver->getSessionID();
    }

    /**
     * @inheritdoc
     */
    public function _afterSuite()
    {
        parent::_afterSuite();

        if($this->useBrowserStackHub()
            && count(static::$sessionId) > 0){
            $api = $this->getBrowserstackApi();
            foreach(static::$sessionId as $id) {
                $this->debug("Mark Browserstack session as failed: {$id}");
                $api->markSessionFailed($id);
            }
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