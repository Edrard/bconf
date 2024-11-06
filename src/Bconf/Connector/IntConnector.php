<?php

namespace edrard\Bconf\Connector;

interface IntConnector
{
    public function connect();
    public function enablePTY();
    public function setTimeouts();
    public function login();
    public function enable();
    public function runPreCommand();
    public function runAfterCommand();
    public function configExport();
    public function getDeviceConfig();
}
