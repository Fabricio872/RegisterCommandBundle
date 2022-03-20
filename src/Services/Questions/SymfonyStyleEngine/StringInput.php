<?php

namespace Fabricio872\RegisterCommand\Services\Questions\SymfonyStyleEngine;

use Fabricio872\RegisterCommand\Annotations\AbstractEditor;
use Fabricio872\RegisterCommand\Annotations\StringEditor;
use Fabricio872\RegisterCommand\Services\Engines\EngineInterface;
use Fabricio872\RegisterCommand\Services\Engines\SymfonyStyleEngine;
use Fabricio872\RegisterCommand\Services\Questions\QuestionInterface;

class StringInput implements QuestionInterface
{
    /** @var StringEditor */
    private AbstractEditor $editor;
    /** @var SymfonyStyleEngine */
    private EngineInterface $engine;
    private string $field;

    public function setEditor(AbstractEditor $editor)
    {
        $this->editor = $editor;
    }

    public function setEngine(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function setField(string $fieldName)
    {
        $this->field = $fieldName;
    }

    public function getAnswer(): mixed
    {
        $symfonyStyle = $this->engine->getSymfonyStyle();
        $defaultQuestion = sprintf('Set value for field %s', $this->field);
        return $symfonyStyle->ask($this->editor->question ?? $defaultQuestion, $this->editor->default);
    }
}