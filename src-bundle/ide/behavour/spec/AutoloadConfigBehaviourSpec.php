<?php

namespace ide\behavour\spec;

use behaviour\custom\AutoloadConfigBehaviour;
use ide\behaviour\AbstractBehaviourSpec;
use ide\formats\form\elements\FormFormElement;

class AutoloadConfigBehaviourSpec extends AbstractBehaviourSpec
{

    public function getName()
    {
        return 'Сохранение \ загрузка конфига';
    }

    public function getGroup()
    {
        return AbstractBehaviourSpec::GROUP_LOGIC;
    }

    public function getIcon()
    {
        return "icons/fire16.png";
    }


    public function getDescription()
    {
        return "Позволяет автоматически загружать и сохранять данные для конфига при запуске приложения";
    }

    public function getType()
    {
        return AutoloadConfigBehaviour::class;
    }

    public function isAllowedFor($target)
    {
        return $target instanceof FormFormElement;
    }
}