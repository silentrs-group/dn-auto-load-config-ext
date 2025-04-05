<?php

namespace behaviour\custom;


use Error;
use ide\Logger;
use php\gui\framework\behaviour\custom\AbstractBehaviour;
use php\io\FileStream;
use php\io\MemoryStream;
use php\lib\fs;
use php\lib\str;

class AutoloadConfigBehaviour extends AbstractBehaviour
{

    public $configPath = './config.cfg';

    public $classes = '';

    /**
     * @var \behaviour\custom\DataModify[]
     */
    private $instances = [];

    protected function applyImpl($target)
    {
        $this->_target = $target;

        foreach (explode("|", $this->classes) as $class) {
            if (!$this->instances[$class]) {
                $this->instances[$class] = new $class();

                if (!($this->instances[$class] instanceof DataModify)) {
                    unset($this->instances[$class]);
                    throw new \Exception("Class {$class} does not implement " . DataModify::class);
                }
            }
        }

        $this->loadConfig();

        $this->_target->bind("close", function () {
            $this->saveConfig();
        });
    }

    private function loadConfig()
    {
        if (fs::exists($this->preparedConfigPath())) {
            // $this->_target->getConfig()->load($this->preparedConfigPath());

            $stream = new FileStream($this->preparedConfigPath());

            foreach ($this->instances as $modify) {
                $stream = $modify->onRead($stream);
            }

            $this->_target->getConfig()->load($stream);
        }
    }

    private function saveConfig()
    {
        // $this->_target->getConfig()->save($this->preparedConfigPath());

        $memoryStream = new MemoryStream();
        $this->_target->getConfig()->save($memoryStream);

        foreach ($this->instances as $modify) {
            $memoryStream = $modify->onWrite($memoryStream);
        }

        $outputStream = new FileStream($this->preparedConfigPath(), "w");
        $outputStream->write($memoryStream);
    }

    private function preparedConfigPath(): string
    {
        $path = str::replace($this->configPath, '%userhome%', $_ENV["USERPROFILE"]);
        $path = str::replace($path, './', fs::abs('./') . '/');
        $path = fs::normalize($path);

        return $path;

    }

    public function getCode()
    {
        return "autoloadConfig";
    }
}