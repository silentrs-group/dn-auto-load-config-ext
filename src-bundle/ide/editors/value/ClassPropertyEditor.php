<?php

namespace ide\editors\value;

use ide\Ide;
use php\gui\framework\EventBinder;
use php\gui\layout\UXAnchorPane;
use php\gui\UXCheckbox;
use php\gui\UXForm;
use php\gui\UXListView;
use php\io\File;
use php\io\Stream;
use php\lib\fs;
use php\lib\str;

class ClassPropertyEditor extends TextPropertyEditor
{
    /**
     * @var UXListView
     */
    private $listView;
    /**
     * @var string
     */
    public $projectRootDir;

    public function __construct(callable $getter = null, callable $setter = null)
    {
        parent::__construct($getter, function ($editor, $value) {
            $this->designProperties->target->{$this->code}->clear();
            $this->designProperties->target->{$this->code}->addAll($value);
        });


        $this->setReadOnly(true);

        $this->projectRootDir = fs::parent(Ide::project()->getMainProjectFile());
    }

    public function getCode()
    {
        return 'classes';
    }

    public function makeUi()
    {
        // text field
        $r = parent::makeUi();


        return $r;
    }

    public function showDialog($x = null, $y = null)
    {
        $dialog = $this->getEditorForm($x, $y);

        $this->clear();

        $list = explode("|", $this->textField->text);

        fs::scan($this->projectRootDir, [
            "extensions"    => ["php"],
            "excludeDirs"   => true,
            "excludeHidden" => true,
            "callback"      => function (File $file) use ($list)
            {
                $data = Stream::getContents($file);

                if (str::contains($data, '<?php') &&
                    str::contains($data, "class") &&
                    str::contains($data, "implements DataModify")
                ) {

                    foreach ($list as $item) {
                        if ($this->prepareText($file->getAbsolutePath()) == $item) {

                            $this->addItem($file, true);
                            return;
                        }
                    }

                    $this->addItem($file);
                }
            }
        ], 20);

        $dialog->show();
    }

    protected function getEditorForm($x = null, $y = null)
    {
        if (!$this->editorForm) {
            $this->editorForm = new UXForm();
            $this->editorForm->style    = "UTILITY";
            $this->editorForm->modality = "APPLICATION_MODAL";

            $binder = new EventBinder($this->editorForm);
            $binder->bind("close", function () {
                $this->save();
            });

            $this->editorForm->add($this->listView = new UXListView());
            UXAnchorPane::setAnchor($this->listView, 0);
        }

        $this->editorForm->width = 210;
        $this->editorForm->height = 360;
        $this->editorForm->x = $x;
        $this->editorForm->y = $y;

        return $this->editorForm;
    }

    private function addItem(File $file, $selected = false)
    {

        $text = $this->prepareText($file->getAbsolutePath());

        $this->listView->items->add($c = new UXCheckbox($text));
        $c->selected = $selected;
    }

    private function clear()
    {
        $this->listView->items->clear();
    }

    private function save ()
    {
        $list = [];

        foreach ($this->listView->items as $item) {
            if ($item->selected) {
                $list[] = $item->text;
            }
        }

        $this->applyValue(implode("|", $list));
    }

    private function prepareText ($text) {
        $text = str::replace($text, "/", "\\");
        $text = str::replace($text, $this->projectRootDir . '\src\\', "");
        return str::replace($text, ".php", "");
    }
}