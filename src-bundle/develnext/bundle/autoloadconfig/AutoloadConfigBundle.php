<?php

namespace develnext\bundle\autoloadconfig;

use ide\behaviour\IdeBehaviourDatabase;
use ide\behavour\spec\AutoloadConfigBehaviourSpec;
use ide\bundle\AbstractBundle;
use ide\bundle\AbstractJarBundle;
use ide\editors\value\ClassPropertyEditor;
use ide\project\Project;

class AutoloadConfigBundle extends AbstractJarBundle
{
    public function onAdd(Project $project, AbstractBundle $owner = null)
    {
        parent::onAdd($project, $owner);

        if ($bDatabase = IdeBehaviourDatabase::get()) {
            $bDatabase->registerBehaviourSpec(AutoloadConfigBehaviourSpec::class);
            ClassPropertyEditor::register(new ClassPropertyEditor());
        }
    }

    public function onRemove(Project $project, AbstractBundle $owner = null)
    {
        parent::onRemove($project, $owner);

        if ($bDatabase = IdeBehaviourDatabase::get()) {
            $bDatabase->unregisterBehaviourSpec(AutoloadConfigBehaviourSpec::class);
        }
    }



}