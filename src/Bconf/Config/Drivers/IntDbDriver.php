<?php

namespace edrard\Bconf\Config\Drivers;

interface IntDbDriver
{
    public function getDevices($ammount);
    public function getGroups();
}
