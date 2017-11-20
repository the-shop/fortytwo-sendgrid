<?php

namespace Framework\SendGrid;

use Framework\Base\Module\BaseModule;

class Module extends BaseModule
{
    public function bootstrap()
    {
        // Let's read all files from module config folder and set to Configuration
        $configDirPath = realpath(dirname(__DIR__)) . '/config/';
        $this->setModuleConfiguration($configDirPath);
    }
}
