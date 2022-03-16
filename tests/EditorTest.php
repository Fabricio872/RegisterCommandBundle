<?php

namespace Fabricio872\RegisterCommand\Tests;

use Fabricio872\RegisterCommand\Entity\User;
use Fabricio872\RegisterCommand\Services\Editor;
use PHPUnit\Framework\TestCase;

class EditorTest extends TestCase
{
    public function test_editor_must_respond_with_user_entity()
    {
        $user = new User();
        $editor = new Editor($user);

        $this->assertEquals($user, $editor->getEntity());;
    }

    public function test_editor_calls_annotation_on_each_parameter_in_user_entity()
    {
        $user = new User();
        $editor = new Editor($user);

        $editor->run();

        $this->assertEquals("testNoTTY", $editor->getEntity()->getEmail());

        Editor::$TTY = true;
        $editor->run();
        $this->assertEquals("testTTY", $editor->getEntity()->getEmail());
    }
}
