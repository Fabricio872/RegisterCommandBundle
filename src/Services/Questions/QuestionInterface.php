<?php

namespace Fabricio872\RegisterCommand\Services\Questions;

use Fabricio872\RegisterCommand\Annotations\AbstractEditor;
use Fabricio872\RegisterCommand\Services\Engines\EngineInterface;

interface QuestionInterface
{
    public function setEditor(AbstractEditor $editor);

    public function setEngine(EngineInterface $engine);

    public function setField(string $fieldName);

    public function getAnswer();
}
