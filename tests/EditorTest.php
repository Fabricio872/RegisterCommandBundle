<?php

namespace Fabricio872\RegisterCommand\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Fabricio872\RegisterCommand\Entity\User;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSetException;
use Fabricio872\RegisterCommand\Exceptions\EngineNotSupported;
use Fabricio872\RegisterCommand\Services\Editor;
use Fabricio872\RegisterCommand\Services\Engines\SymfonyStyleEngine;
use Fabricio872\RegisterCommand\Services\Engines\TestEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class EditorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        Editor::$ENGINE = null;
    }


    public function test_editor_must_respond_with_user_entity()
    {
        $user = new User();
        $editor = new Editor($this->createMock(Container::class));
        $editor->setUser($user);

        $this->assertEquals($user, $editor->getUser());;
    }

    public function test_editor_calls_annotation_on_each_parameter_in_user_entity()
    {
        $user = new User();
        $editor = new Editor($this->createMock(Container::class));
        $editor->setUser($user);

        Editor::$ENGINE = new TestEngine();
        $editor->run();

        $this->assertEquals("test", $editor->getUser()->getEmail());
    }

    public function test_engine_is_set_exception()
    {
        $user = new User();
        $editor = new Editor($this->createMock(Container::class));
        $editor->setUser($user);

        $this->expectException(EngineNotSetException::class);
        $editor->run();
    }

    public function test_engine_not_supported_exception()
    {
        $user = new User();
        $editor = new Editor($this->createMock(Container::class));
        $editor->setUser($user);

        Editor::$ENGINE = new SymfonyStyleEngine(
            $this->createMock(Editor::class),
            $this->createMock(EntityManagerInterface::class)
        );

        $this->expectException(EngineNotSupported::class);
        $editor->run();
    }
}
