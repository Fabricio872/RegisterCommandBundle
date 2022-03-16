<?php

namespace Fabricio872\RegisterCommand\Annotations\test;

use Attribute;
use Fabricio872\RegisterCommand\Annotations\AbstractEditor;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TestEditor extends AbstractEditor
{

    public function askTTY()
    {
        return "testTTY";
    }

    public function askNoTTY()
    {
        return "testNoTTY";
    }
}